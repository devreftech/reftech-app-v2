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
     * Menghasilkan ekspresi SQL untuk menghitung Week (1-5) berbasis Kalender Kerja (Senin - Minggu).
     * Week 1: Tanggal 1 s/d Minggu pertama bulan tsb.
     * Week 2, 3, dst: Dimulai setiap hari Senin berikutnya.
     * Maksimal bernilai 5.
     *
     * Formula: LEAST(5, CEIL((DAYOFMONTH(col) + WEEKDAY(DATE_SUB(col, INTERVAL (DAYOFMONTH(col)-1) DAY))) / 7))
     *
     * @param string $column
     * @return string
     */
    public function getWeekSqlExpr(string $column): string
    {
        return "LEAST(5, CEIL((DAYOFMONTH({$column}) + WEEKDAY(DATE_SUB({$column}, INTERVAL (DAYOFMONTH({$column}) - 1) DAY))) / 7))";
    }

    /**
     * Rekap KPI sales dipecah per Minggu 1-5 berbasis Kalender Kerja (Senin - Minggu).
     *
     * Catatan asumsi (bisa direvisi bareng sales manager):
     *  - "New leads berhasil introduction" = client dengan id_issues >= 3
     *    (Send Introduction ke atas) yang punya aktivitas Responded di minggu itu.
     *  - Pemisahan New Leads vs CRM pakai client.role ('Customers' = CRM).
     *  - "Data canvasing keseluruhan" dihitung dari registrasi client source Canvasing.
     */
    public function buildWeeklyKpi($sales, $month, $year)
    {
        $actWeekExpr = $this->getWeekSqlExpr('activities.date');
        $poLegacyWeekExpr = $this->getWeekSqlExpr('quotation.po_date');
        $poUnitWeekExpr = $this->getWeekSqlExpr('unit_quotation.po_received');
        $quoteLegacyWeekExpr = $this->getWeekSqlExpr('quotation.estimated_date');
        $quoteUnitWeekExpr = $this->getWeekSqlExpr('unit_quotation.date');
        $clientWeekExpr = $this->getWeekSqlExpr('client.created_at');

        // Aktivitas (Daily Call / Follow Up / CRM / Visit) untuk sales ini di bulan berjalan.
        $act = function (array $names, $status = 'Responded') use ($sales, $month, $year) {
            $q = Activities::join('client', 'client.id', '=', 'activities.id_client')
                ->where('client.id_sales', $sales)
                ->whereIn('activities.name', $names)
                ->whereMonth('activities.date', $month)
                ->whereYear('activities.date', $year);
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
                        $clientWeekExpr
                    )],
                    ['name' => 'New Leads Yang Berhasil Di Introduction', 'data' => $bucket(
                        $act(['Daily Call', 'Follow Up'])->where('client.id_issues', '>=', 3),
                        $actWeekExpr,
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
                        $actWeekExpr
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
                        $actWeekExpr,
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
                        $clientWeekExpr
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

    /**
     * Ambil rincian detail data untuk Rekap KPI Mingguan saat diklik per baris & minggu.
     *
     * @param int|string $sales
     * @param int $month
     * @param int $year
     * @param string|null $section
     * @param string $rowName
     * @param int|string|null $week
     * @return array
     */
    public function getWeeklyKpiDetail($sales, $month, $year, $section, $rowName, $week = null)
    {
        $validWeeks = [1, 2, 3, 4, 5];
        $week = ($week === 'all' || $week === 'total' || empty($week) || (int)$week < 1 || (int)$week > 5) ? null : (int)$week;

        $actWeekExpr = $this->getWeekSqlExpr('activities.date');
        $poLegacyWeekExpr = $this->getWeekSqlExpr('quotation.po_date');
        $poUnitWeekExpr = $this->getWeekSqlExpr('unit_quotation.po_received');
        $quoteLegacyWeekExpr = $this->getWeekSqlExpr('quotation.estimated_date');
        $quoteUnitWeekExpr = $this->getWeekSqlExpr('unit_quotation.date');
        $clientWeekExpr = $this->getWeekSqlExpr('client.created_at');

        $act = function (array $names, $status = 'Responded') use ($sales, $month, $year, $actWeekExpr, $week) {
            $q = Activities::join('client', 'client.id', '=', 'activities.id_client')
                ->where('client.id_sales', $sales)
                ->whereIn('activities.name', $names)
                ->whereMonth('activities.date', $month)
                ->whereYear('activities.date', $year);
            if ($status !== null) {
                $q->where('activities.status', $status);
            }
            if ($week) {
                $q->whereRaw("{$actWeekExpr} = ?", [$week]);
            }
            return $q;
        };

        $legacyQuote = function () use ($sales, $month, $year, $quoteLegacyWeekExpr, $week) {
            $q = Quotation::join('pic', 'quotation.id_pic', '=', 'pic.id')
                ->join('client', 'pic.id_client', '=', 'client.id')
                ->where('quotation.id_sales', $sales)
                ->where('quotation.level', '1')->where('quotation.is_primary', '1')
                ->whereMonth('quotation.estimated_date', $month)
                ->whereYear('quotation.estimated_date', $year);
            if ($week) {
                $q->whereRaw("{$quoteLegacyWeekExpr} = ?", [$week]);
            }
            return $q;
        };

        $uqQuote = function () use ($sales, $month, $year, $quoteUnitWeekExpr, $week) {
            $q = UnitQuotation::join('client', 'unit_quotation.id_client', '=', 'client.id')
                ->where('unit_quotation.id_sales', $sales)
                ->where('unit_quotation.is_latest', 1)
                ->whereMonth('unit_quotation.date', $month)
                ->whereYear('unit_quotation.date', $year);
            if ($week) {
                $q->whereRaw("{$quoteUnitWeekExpr} = ?", [$week]);
            }
            return $q;
        };

        $legacyPo = function () use ($sales, $month, $year, $poLegacyWeekExpr, $week) {
            $q = Quotation::join('pic', 'quotation.id_pic', '=', 'pic.id')
                ->join('client', 'pic.id_client', '=', 'client.id')
                ->where('quotation.id_sales', $sales)
                ->where('quotation.status', '100')
                ->where('quotation.level', '1')->where('quotation.is_primary', '1')
                ->whereMonth('quotation.po_date', $month)
                ->whereYear('quotation.po_date', $year);
            if ($week) {
                $q->whereRaw("{$poLegacyWeekExpr} = ?", [$week]);
            }
            return $q;
        };

        $uqPo = function () use ($sales, $month, $year, $poUnitWeekExpr, $week) {
            $q = UnitQuotation::join('client', 'unit_quotation.id_client', '=', 'client.id')
                ->where('unit_quotation.id_sales', $sales)
                ->where('unit_quotation.is_latest', 1)
                ->where('unit_quotation.status', 'po_received')
                ->whereMonth('unit_quotation.po_received', $month)
                ->whereYear('unit_quotation.po_received', $year);
            if ($week) {
                $q->whereRaw("{$poUnitWeekExpr} = ?", [$week]);
            }
            return $q;
        };

        $mapQuoteRows = function ($legacyQuery, $uqQuery) {
            $legacyItems = $legacyQuery->select([
                'quotation.id',
                'quotation.no_quote',
                'client.company',
                'quotation.estimated_date as date',
                'quotation.nett as nominal',
                'quotation.status',
                'quotation.title'
            ])->orderByDesc('quotation.estimated_date')->get()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'no_quote' => $q->no_quote ?? '-',
                    'company' => $q->company ?? '-',
                    'date' => $q->date ? Carbon::parse($q->date)->format('d/m/Y') : '-',
                    'nominal' => (float) ($q->nominal ?? 0),
                    'status' => ($q->status == '100' ? 'PO' : ($q->status == '0' ? 'Loss' : 'Active')),
                    'title' => $q->title ?? '-',
                    'type' => 'Parts',
                    'is_smart' => false,
                    'url' => route('quotation.show', $q->id),
                ];
            });

            $smartItems = $uqQuery->select([
                'unit_quotation.id',
                'unit_quotation.no_quote',
                'unit_quotation.type',
                'client.company',
                'unit_quotation.date',
                DB::raw('(unit_quotation.total - COALESCE(unit_quotation.tax_amount, 0)) as nominal'),
                'unit_quotation.status',
                'unit_quotation.title'
            ])->orderByDesc('unit_quotation.date')->get()->map(function ($uq) {
                $typeName = !empty($uq->type) ? $uq->type : 'Unit';
                return [
                    'id' => $uq->id,
                    'no_quote' => $uq->no_quote ?? '-',
                    'company' => $uq->company ?? '-',
                    'date' => $uq->date ? Carbon::parse($uq->date)->format('d/m/Y') : '-',
                    'nominal' => (float) ($uq->nominal ?? 0),
                    'status' => ucfirst(str_replace('_', ' ', $uq->status ?? 'Draft')),
                    'title' => $uq->title ?? '-',
                    'type' => $typeName,
                    'is_smart' => true,
                    'url' => route('unit-quotation.show', $uq->id),
                ];
            });

            $all = $legacyItems->concat($smartItems);
            return [
                'category' => 'quotation',
                'items' => $all,
                'total_nominal' => $all->sum('nominal'),
                'total_count' => $all->count(),
            ];
        };

        $mapPoRows = function ($legacyQuery, $uqQuery) {
            $legacyItems = $legacyQuery->select([
                'quotation.id',
                'quotation.no_quote',
                'quotation.no_po',
                'client.company',
                'quotation.po_date as date',
                'quotation.nett as nominal',
                'quotation.status',
                'quotation.title'
            ])->orderByDesc('quotation.po_date')->get()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'no_quote' => $q->no_quote ?? '-',
                    'no_po' => $q->no_po ?? '-',
                    'company' => $q->company ?? '-',
                    'date' => $q->date ? Carbon::parse($q->date)->format('d/m/Y') : '-',
                    'nominal' => (float) ($q->nominal ?? 0),
                    'status' => 'PO Done',
                    'title' => $q->title ?? '-',
                    'type' => 'Parts',
                    'is_smart' => false,
                    'url' => route('quotation.show', $q->id),
                ];
            });

            $smartItems = $uqQuery->select([
                'unit_quotation.id',
                'unit_quotation.no_quote',
                'unit_quotation.no_po',
                'unit_quotation.type',
                'client.company',
                'unit_quotation.po_received as date',
                DB::raw('(unit_quotation.total - COALESCE(unit_quotation.tax_amount, 0)) as nominal'),
                'unit_quotation.status',
                'unit_quotation.title'
            ])->orderByDesc('unit_quotation.po_received')->get()->map(function ($uq) {
                $typeName = !empty($uq->type) ? $uq->type : 'Unit';
                return [
                    'id' => $uq->id,
                    'no_quote' => $uq->no_quote ?? '-',
                    'no_po' => $uq->no_po ?? '-',
                    'company' => $uq->company ?? '-',
                    'date' => $uq->date ? Carbon::parse($uq->date)->format('d/m/Y') : '-',
                    'nominal' => (float) ($uq->nominal ?? 0),
                    'status' => 'PO Received',
                    'title' => $uq->title ?? '-',
                    'type' => $typeName,
                    'is_smart' => true,
                    'url' => route('unit-quotation.show', $uq->id),
                ];
            });

            $all = $legacyItems->concat($smartItems);
            return [
                'category' => 'po',
                'items' => $all,
                'total_nominal' => $all->sum('nominal'),
                'total_count' => $all->count(),
            ];
        };

        // Match based on $rowName
        switch ($rowName) {
            case 'Data Canvasing Keseluruhan Per Minggu':
                $q = Client::where('client.id_sales', $sales)
                    ->where('client.source', 'Canvasing')
                    ->whereMonth('client.created_at', $month)
                    ->whereYear('client.created_at', $year);
                if ($week) {
                    $q->whereRaw("{$clientWeekExpr} = ?", [$week]);
                }
                $data = $q->select('client.*')->orderByDesc('client.created_at')->get()->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'company' => $c->company ?? '-',
                        'address' => $c->address ?? $c->area ?? '-',
                        'phone' => $c->phone ?? '-',
                        'date' => $c->created_at ? $c->created_at->format('d/m/Y') : '-',
                        'source' => $c->source ?? 'Canvasing',
                        'role' => $c->role ?? 'Leads',
                        'url' => route('detail.leads', $c->id),
                    ];
                });
                return [
                    'category' => 'client',
                    'title' => 'Data Canvasing Keseluruhan',
                    'items' => $data,
                    'total_count' => $data->count(),
                ];

            case 'New Leads Yang Berhasil Di Introduction':
                $q = $act(['Daily Call', 'Follow Up'])->where('client.id_issues', '>=', 3);
                $data = $q->select(
                    'activities.*',
                    'client.company as client_company',
                    'client.role as client_role',
                    'client.id as client_id'
                )->orderByDesc('activities.date')->get()->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'company' => $a->client_company ?? '-',
                        'client_id' => $a->client_id,
                        'client_url' => $a->client_role == 'Customers' ? route('detail.customers', $a->client_id) : route('detail.leads', $a->client_id),
                        'name' => $a->name,
                        'date' => $a->date ? Carbon::parse($a->date)->format('d/m/Y') : '-',
                        'status' => $a->status ?? 'Responded',
                        'note' => $a->note ?? $a->note_result ?? '-',
                    ];
                });
                return [
                    'category' => 'activity',
                    'title' => 'New Leads Berhasil Di-Introduction',
                    'items' => $data,
                    'total_count' => $data->count(),
                ];

            case 'Quotation Dari New Leads':
                $legacy = $legacyQuote()->where('client.role', '!=', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('quotation.id_support')
                    ->whereNull('client.id_support');
                $smart = $uqQuote()->where('client.role', '!=', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('unit_quotation.id_support')
                    ->whereNull('client.id_support');
                $res = $mapQuoteRows($legacy, $smart);
                $res['title'] = 'Quotation Dari New Leads';
                return $res;

            case 'PO Dari New Leads Baru':
                $legacy = $legacyPo()->where('client.role', '!=', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('quotation.id_support')
                    ->whereNull('client.id_support');
                $smart = $uqPo()->where('client.role', '!=', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('unit_quotation.id_support')
                    ->whereNull('client.id_support');
                $res = $mapPoRows($legacy, $smart);
                $res['title'] = 'PO Dari New Leads Baru';
                return $res;

            case 'Total CRM Yang Berhasil Di Follow Up':
                $q = $act(['CRM', 'Crm']);
                $data = $q->select(
                    'activities.*',
                    'client.company as client_company',
                    'client.role as client_role',
                    'client.id as client_id'
                )->orderByDesc('activities.date')->get()->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'company' => $a->client_company ?? '-',
                        'client_id' => $a->client_id,
                        'client_url' => $a->client_role == 'Customers' ? route('detail.customers', $a->client_id) : route('detail.leads', $a->client_id),
                        'name' => $a->name,
                        'date' => $a->date ? Carbon::parse($a->date)->format('d/m/Y') : '-',
                        'status' => $a->status ?? 'Responded',
                        'note' => $a->note ?? $a->note_result ?? '-',
                    ];
                });
                return [
                    'category' => 'activity',
                    'title' => 'CRM Yang Berhasil Di Follow Up',
                    'items' => $data,
                    'total_count' => $data->count(),
                ];

            case 'Quotation Dari CRM':
                $legacy = $legacyQuote()->where('client.role', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('quotation.id_support')
                    ->whereNull('client.id_support');
                $smart = $uqQuote()->where('client.role', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('unit_quotation.id_support')
                    ->whereNull('client.id_support');
                $res = $mapQuoteRows($legacy, $smart);
                $res['title'] = 'Quotation Dari CRM';
                return $res;

            case 'PO Dari Customer CRM':
                $legacy = $legacyPo()->where('client.role', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('quotation.id_support')
                    ->whereNull('client.id_support');
                $smart = $uqPo()->where('client.role', 'Customers')
                    ->where('client.source', '!=', 'Indotrading')
                    ->whereNull('unit_quotation.id_support')
                    ->whereNull('client.id_support');
                $res = $mapPoRows($legacy, $smart);
                $res['title'] = 'PO Dari Customer CRM';
                return $res;

            case 'Customer CRM Yang Di Visit Bulan Ini':
                $q = $act(['Visit'])->where('client.role', 'Customers');
                $data = $q->select(
                    'activities.*',
                    'client.company as client_company',
                    'client.role as client_role',
                    'client.id as client_id'
                )->orderByDesc('activities.date')->get()->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'company' => $a->client_company ?? '-',
                        'client_id' => $a->client_id,
                        'client_url' => $a->client_role == 'Customers' ? route('detail.customers', $a->client_id) : route('detail.leads', $a->client_id),
                        'name' => $a->name,
                        'date' => $a->date ? Carbon::parse($a->date)->format('d/m/Y') : '-',
                        'status' => $a->status ?? 'Responded',
                        'note' => $a->note ?? $a->note_result ?? '-',
                    ];
                });
                return [
                    'category' => 'activity',
                    'title' => 'Customer CRM Yang Di Visit',
                    'items' => $data,
                    'total_count' => $data->count(),
                ];

            case 'Total New Leads Dari Marketing':
                $q = Client::where('client.id_sales', $sales)
                    ->where(function ($sq) {
                        $sq->where('client.source', 'Indotrading')
                            ->orWhereNotNull('client.id_support');
                    })
                    ->whereMonth('client.created_at', $month)
                    ->whereYear('client.created_at', $year);
                if ($week) {
                    $q->whereRaw("{$clientWeekExpr} = ?", [$week]);
                }
                $data = $q->select('client.*')->orderByDesc('client.created_at')->get()->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'company' => $c->company ?? '-',
                        'address' => $c->address ?? $c->area ?? '-',
                        'phone' => $c->phone ?? '-',
                        'date' => $c->created_at ? $c->created_at->format('d/m/Y') : '-',
                        'source' => $c->source ?? 'Marketing',
                        'role' => $c->role ?? 'Leads',
                        'url' => route('detail.leads', $c->id),
                    ];
                });
                return [
                    'category' => 'client',
                    'title' => 'New Leads Dari Marketing',
                    'items' => $data,
                    'total_count' => $data->count(),
                ];

            case 'Quotation Dari Marketing':
                $legacy = $legacyQuote()->where(function ($q) {
                    $q->where('client.source', 'Indotrading')
                        ->orWhereNotNull('quotation.id_support')
                        ->orWhereNotNull('client.id_support');
                });
                $smart = $uqQuote()->where(function ($q) {
                    $q->where('client.source', 'Indotrading')
                        ->orWhereNotNull('unit_quotation.id_support')
                        ->orWhereNotNull('client.id_support');
                });
                $res = $mapQuoteRows($legacy, $smart);
                $res['title'] = 'Quotation Dari Marketing';
                return $res;

            case 'PO Dari Marketing':
                $legacy = $legacyPo()->where(function ($q) {
                    $q->where('client.source', 'Indotrading')
                        ->orWhereNotNull('quotation.id_support')
                        ->orWhereNotNull('client.id_support');
                });
                $smart = $uqPo()->where(function ($q) {
                    $q->where('client.source', 'Indotrading')
                        ->orWhereNotNull('unit_quotation.id_support')
                        ->orWhereNotNull('client.id_support');
                });
                $res = $mapPoRows($legacy, $smart);
                $res['title'] = 'PO Dari Marketing';
                return $res;

            default:
                return [
                    'category' => 'empty',
                    'title' => $rowName,
                    'items' => collect([]),
                    'total_count' => 0,
                ];
        }
    }
}
