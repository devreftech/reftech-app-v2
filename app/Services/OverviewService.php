<?php

namespace App\Services;

use App\Models\Activities;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\SalesReports;
use App\Models\UnitQuotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OverviewService
{
    /**
     * Sales yang tidak menjalankan akuisisi New Leads — section "Record New Leads"
     * disembunyikan dari Rekap KPI Mingguan (mis. Annisa Nurfadilah / id 4 yang
     * fokus CRM & Indotrading saja).
     */
    private const SALES_WITHOUT_NEW_LEADS = [4];

    /**
     * Resolve active SalesReport for the given request parameters or current date.
     *
     * @param string|null $reportId
     * @return array
     */
    public function resolveReportContext($reportId = null)
    {
        if ($reportId && str_starts_with($reportId, 'full_')) {
            $year = (int) str_replace('full_', '', $reportId);
            return [
                'type' => 'full_year',
                'year' => $year,
                'report' => null,
            ];
        }

        if ($reportId) {
            $report = SalesReports::find($reportId);
        } else {
            $year = now()->year;
            $semester = now()->month <= 6 ? 1 : 2;
            $report = SalesReports::where('year', $year)->where('semester', $semester)->first();
            if (!$report) {
                $report = SalesReports::orderBy('year', 'desc')->orderBy('semester', 'desc')->first();
            }
        }

        return [
            'type' => 'semester',
            'year' => $report->year ?? now()->year,
            'report' => $report,
        ];
    }

    /**
     * Fetch active sales users list for overview page.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveSalesList()
    {
        return User::activeSalesAndProjectAdmins();
    }

    /**
     * Calculate support report stats and data arrays for OverviewController.
     *
     * @param int $year
     * @param int $month
     * @param int|null $semester
     * @param string $mode
     * @param int $supportId
     * @return array
     */
    public function getSupportReportData(int $year, int $month, ?int $semester, string $mode, int $supportId)
    {
        if ($mode === 'semester') {
            $firstDay = $semester == 1 ? "{$year}-01-01" : "{$year}-07-01";
            $lastDay  = $semester == 1
                ? date('Y-m-t', strtotime("{$year}-06-01"))
                : date('Y-m-t', strtotime("{$year}-12-01"));
        } else {
            $firstDay = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $lastDay  = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        }

        $yearList = SalesReports::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if (!$yearList->contains($year)) {
            $yearList = $yearList->push($year)->sortDesc()->values();
        }

        return [
            'year' => $year,
            'month' => $month,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'yearList' => $yearList,
            'supportId' => $supportId,
        ];
    }

    /**
     * Rekap KPI sales dipecah per Minggu 1-5 (meniru sheet Excel manual milik
     * sales manager). Sumber minggu:
     *  - Baris aktivitas  -> kolom activities.week (dipilih manual sales saat input)
     *  - Baris quotation  -> kolom unit_quotation.week (smart quotation)
     *  - Baris PO         -> diturunkan CEIL(DAYOFMONTH(po_received)/7) karena
     *                        minggu PO tidak disimpan di record manapun
     *
     * Catatan asumsi (bisa direvisi bareng sales manager):
     *  - "New leads berhasil introduction" = client dengan id_issues >= 3
     *    (Send Introduction ke atas) yang punya aktivitas Responded di minggu itu.
     *  - Pemisahan New Leads vs CRM pakai client.role ('Customers' = CRM).
     *  - "Data canvasing keseluruhan" belum ada definisi metrik -> ditandai on Review.
     */
    public function buildWeeklyKpi($sales, $month, $year)
    {
        $validWeeks = [1, 2, 3, 4, 5];
        $poLegacyWeekExpr = 'CEIL(DAYOFMONTH(quotation.po_date) / 7)';
        $poUnitWeekExpr = 'CEIL(DAYOFMONTH(unit_quotation.po_received) / 7)';
        $quoteLegacyWeekExpr = 'COALESCE(NULLIF(quotation.week, 0), CEIL(DAYOFMONTH(quotation.estimated_date) / 7))';
        $quoteUnitWeekExpr = 'COALESCE(NULLIF(unit_quotation.week, 0), CEIL(DAYOFMONTH(unit_quotation.date) / 7))';

        // Aktivitas (Daily Call / Follow Up / CRM / Visit) untuk sales ini di bulan berjalan.
        $act = function (array $names, $status = 'Responded') use ($sales, $month, $year, $validWeeks) {
            $q = Activities::join('client', 'client.id', '=', 'activities.id_client')
                ->where('client.id_sales', $sales)
                ->whereIn('activities.name', $names)
                ->whereMonth('activities.date', $month)
                ->whereYear('activities.date', $year)
                ->whereIn('activities.week', $validWeeks);
            if ($status !== null) {
                $q->where('activities.status', $status);
            }
            return $q;
        };

        // Legacy quotation dibuat di bulan berjalan
        $legacyQuote = function () use ($sales, $month, $year) {
            return Quotation::join('pic', 'quotation.id_pic', '=', 'pic.id')
                ->join('client', 'pic.id_client', '=', 'client.id')
                ->where('quotation.id_sales', $sales)
                ->where('quotation.level', '1')->where('quotation.is_primary', '1')
                ->whereMonth('quotation.estimated_date', $month)
                ->whereYear('quotation.estimated_date', $year);
        };

        // Smart quotation dibuat di bulan berjalan
        $uqQuote = function () use ($sales, $month, $year) {
            return UnitQuotation::join('client', 'unit_quotation.id_client', '=', 'client.id')
                ->where('unit_quotation.id_sales', $sales)
                ->where('unit_quotation.is_latest', 1)
                ->whereMonth('unit_quotation.date', $month)
                ->whereYear('unit_quotation.date', $year);
        };

        // Legacy quotation yang PO-nya diterima di bulan berjalan
        $legacyPo = function () use ($sales, $month, $year) {
            return Quotation::join('pic', 'quotation.id_pic', '=', 'pic.id')
                ->join('client', 'pic.id_client', '=', 'client.id')
                ->where('quotation.id_sales', $sales)
                ->where('quotation.status', '100')
                ->where('quotation.level', '1')->where('quotation.is_primary', '1')
                ->whereMonth('quotation.po_date', $month)
                ->whereYear('quotation.po_date', $year);
        };

        // Smart quotation yang PO-nya diterima di bulan berjalan
        $uqPo = function () use ($sales, $month, $year) {
            return UnitQuotation::join('client', 'unit_quotation.id_client', '=', 'client.id')
                ->where('unit_quotation.id_sales', $sales)
                ->where('unit_quotation.is_latest', 1)
                ->where('unit_quotation.status', 'po_received')
                ->whereMonth('unit_quotation.po_received', $month)
                ->whereYear('unit_quotation.po_received', $year);
        };

        $bucket = function ($query, $weekExpr, $distinctClient = false) {
            $countExpr = $distinctClient ? 'COUNT(DISTINCT client.id)' : 'COUNT(*)';
            $rows = $query->selectRaw("{$weekExpr} as wk, {$countExpr} as total")
                ->groupBy('wk')
                ->pluck('total', 'wk');

            $map = [];
            foreach ($rows as $k => $v) {
                $wk = (int) $k;
                if ($wk >= 1 && $wk <= 5) {
                    $map[$wk] = (int) $v;
                }
            }

            $out = [];
            $sum = 0;
            foreach ([1, 2, 3, 4, 5] as $i) {
                $out[$i] = $map[$i] ?? 0;
                $sum += $out[$i];
            }
            $out['total'] = $sum;
            return $out;
        };

        $bucketMerged = function (array $queries) {
            $map = [];
            foreach ($queries as $item) {
                $query = $item['query'];
                $weekExpr = $item['week'];
                $rows = $query->selectRaw("{$weekExpr} as wk, COUNT(*) as total")
                    ->groupBy('wk')
                    ->pluck('total', 'wk');
                foreach ($rows as $k => $v) {
                    $wk = (int) $k;
                    if ($wk >= 1 && $wk <= 5) {
                        $map[$wk] = ($map[$wk] ?? 0) + (int) $v;
                    }
                }
            }
            $out = [];
            $sum = 0;
            foreach ([1, 2, 3, 4, 5] as $i) {
                $out[$i] = $map[$i] ?? 0;
                $sum += $out[$i];
            }
            $out['total'] = $sum;
            return $out;
        };

        $sections = [
            'newleads' => [
                'label' => 'Record New Leads',
                'rows'  => [
                    ['name' => 'Data Canvasing Keseluruhan Per Minggu', 'data' => $bucket(
                        Client::where('client.id_sales', $sales)
                            ->where('client.source', 'Canvasing')
                            ->whereMonth('client.created_at', $month)
                            ->whereYear('client.created_at', $year),
                        'CEIL(DAYOFMONTH(client.created_at) / 7)'
                    )],
                    ['name' => 'New Leads Yang Berhasil Di Introduction', 'data' => $bucket(
                        $act(['Daily Call', 'Follow Up'])->where('client.id_issues', '>=', 3),
                        'activities.week',
                        true
                    )],
                    ['name' => 'Quotation Dari New Leads', 'data' => $bucketMerged([
                        [
                            'query' => $legacyQuote()->where('client.role', '!=', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $quoteLegacyWeekExpr,
                        ],
                        [
                            'query' => $uqQuote()->where('client.role', '!=', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('unit_quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $quoteUnitWeekExpr,
                        ],
                    ])],
                    ['name' => 'PO Dari New Leads Baru', 'data' => $bucketMerged([
                        [
                            'query' => $legacyPo()->where('client.role', '!=', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $poLegacyWeekExpr,
                        ],
                        [
                            'query' => $uqPo()->where('client.role', '!=', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('unit_quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $poUnitWeekExpr,
                        ],
                    ])],
                ],
            ],
            'crm' => [
                'label' => 'Record CRM',
                'rows'  => [
                    ['name' => 'Total CRM Yang Berhasil Di Follow Up', 'data' => $bucket(
                        $act(['CRM', 'Crm']),
                        'activities.week'
                    )],
                    ['name' => 'Quotation Dari CRM', 'data' => $bucketMerged([
                        [
                            'query' => $legacyQuote()->where('client.role', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $quoteLegacyWeekExpr,
                        ],
                        [
                            'query' => $uqQuote()->where('client.role', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('unit_quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $quoteUnitWeekExpr,
                        ],
                    ])],
                    ['name' => 'PO Dari Customer CRM', 'data' => $bucketMerged([
                        [
                            'query' => $legacyPo()->where('client.role', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $poLegacyWeekExpr,
                        ],
                        [
                            'query' => $uqPo()->where('client.role', 'Customers')
                                ->where('client.source', '!=', 'Indotrading')
                                ->whereNull('unit_quotation.id_support')
                                ->whereNull('client.id_support'),
                            'week'  => $poUnitWeekExpr,
                        ],
                    ])],
                    ['name' => 'Customer CRM Yang Di Visit Bulan Ini', 'data' => $bucket(
                        $act(['Visit'])->where('client.role', 'Customers'),
                        'activities.week',
                        true
                    )],
                ],
            ],
            'indotrading' => [
                'label' => 'Record Marketing',
                'rows'  => [
                    ['name' => 'Total New Leads Dari Marketing', 'data' => $bucket(
                        Client::where('client.id_sales', $sales)
                            ->where(function ($q) {
                                $q->where('client.source', 'Indotrading')
                                  ->orWhereNotNull('client.id_support');
                            })
                            ->whereMonth('client.created_at', $month)
                            ->whereYear('client.created_at', $year),
                        'CEIL(DAYOFMONTH(client.created_at) / 7)'
                    )],
                    ['name' => 'Quotation Dari Marketing', 'data' => $bucketMerged([
                        [
                            'query' => $legacyQuote()->where(function ($q) {
                                $q->where('client.source', 'Indotrading')
                                  ->orWhereNotNull('quotation.id_support')
                                  ->orWhereNotNull('client.id_support');
                            }),
                            'week'  => $quoteLegacyWeekExpr,
                        ],
                        [
                            'query' => $uqQuote()->where(function ($q) {
                                $q->where('client.source', 'Indotrading')
                                  ->orWhereNotNull('unit_quotation.id_support')
                                  ->orWhereNotNull('client.id_support');
                            }),
                            'week'  => $quoteUnitWeekExpr,
                        ],
                    ])],
                    ['name' => 'PO Dari Marketing', 'data' => $bucketMerged([
                        [
                            'query' => $legacyPo()->where(function ($q) {
                                $q->where('client.source', 'Indotrading')
                                  ->orWhereNotNull('quotation.id_support')
                                  ->orWhereNotNull('client.id_support');
                            }),
                            'week'  => $poLegacyWeekExpr,
                        ],
                        [
                            'query' => $uqPo()->where(function ($q) {
                                $q->where('client.source', 'Indotrading')
                                  ->orWhereNotNull('unit_quotation.id_support')
                                  ->orWhereNotNull('client.id_support');
                            }),
                            'week'  => $poUnitWeekExpr,
                        ],
                    ])],
                ],
            ],
        ];

        if (in_array((int) $sales, self::SALES_WITHOUT_NEW_LEADS, true)) {
            unset($sections['newleads']);
        }

        return $sections;
    }
}
