<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Reports;
use App\Models\Unit;
use App\Models\UnitQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    /**
     * Handle global omnibox search across pages and live database entities.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'pages' => [],
                'clients' => [],
                'quotations' => [],
                'invoices' => [],
                'units' => [],
                'service_reports' => [],
                'purchase_orders' => [],
            ]);
        }

        $query = trim($request->input('q', ''));
        $role = $user->role;
        $isSales = in_array($role, ['Sales']);

        // 1. Static Pages & Menu Navigation (Role-Filtered)
        $allPages = $this->getAvailablePages($role);
        $matchedPages = [];

        if (!empty($query)) {
            $qLower = mb_strtolower($query);
            foreach ($allPages as $page) {
                $nameLower = mb_strtolower($page['name']);
                $catLower = mb_strtolower($page['category'] ?? '');
                if (str_contains($nameLower, $qLower) || str_contains($catLower, $qLower)) {
                    $matchedPages[] = $page;
                }
                if (count($matchedPages) >= 5) {
                    break;
                }
            }
        }

        // If query is too short for DB search (< 2 characters), return just matched pages
        if (strlen($query) < 2) {
            return response()->json([
                'pages' => $matchedPages,
                'clients' => [],
                'quotations' => [],
                'invoices' => [],
                'units' => [],
                'service_reports' => [],
                'purchase_orders' => [],
            ]);
        }

        // 2. Clients / Customers
        $clientsQuery = Client::query();
        if ($isSales) {
            $clientsQuery->where('id_sales', $user->id);
        }
        $clientsQuery->where(function ($w) use ($query) {
            $w->where('company', 'LIKE', "%{$query}%")
              ->orWhere('phone', 'LIKE', "%{$query}%")
              ->orWhere('mobile', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        });
        $clients = $clientsQuery->limit(5)->get()->map(function ($c) {
            $isCust = ($c->role === 'Customers');
            $subtitle = $c->area ?: ($c->phone ?: ($c->email ?: '-'));
            return [
                'id' => $c->id,
                'name' => $c->company ?: '-',
                'subtitle' => $subtitle,
                'badge' => $isCust ? 'Customer' : 'Leads',
                'badge_class' => $isCust ? 'bg-label-success' : 'bg-label-primary',
                'icon' => $isCust ? 'mdi-domain' : 'mdi-account-search-outline',
                'url' => $isCust ? route('detail.customers', $c->id) : route('detail.leads', $c->id),
            ];
        });

        // 3. Quotations (Smart & Legacy)
        $smartQuotes = UnitQuotation::query();
        if ($isSales) {
            $smartQuotes->where('id_sales', $user->id);
        }
        $smartQuotes->where('is_latest', 1)
            ->where(function ($w) use ($query) {
                $w->where('no_quote', 'LIKE', "%{$query}%")
                  ->orWhere('title', 'LIKE', "%{$query}%");
            });
        $sqList = $smartQuotes->limit(3)->get()->map(function ($sq) {
            return [
                'id' => $sq->id,
                'name' => $sq->no_quote ?: '-',
                'subtitle' => $sq->title ?: 'Smart Quotation',
                'badge' => 'Smart Quote',
                'badge_class' => 'bg-label-info',
                'icon' => 'mdi-file-document-outline',
                'url' => route('unit-quotation.show', $sq->id),
            ];
        });

        $legacyQuotes = Quotation::query();
        if ($isSales) {
            $legacyQuotes->where('id_sales', $user->id);
        }
        $legacyQuotes->where('level', '1')->where('is_primary', '1')
            ->where(function ($w) use ($query) {
                $w->where('no_quote', 'LIKE', "%{$query}%")
                  ->orWhere('title', 'LIKE', "%{$query}%");
            });
        $lqList = $legacyQuotes->limit(3)->get()->map(function ($lq) {
            return [
                'id' => $lq->id,
                'name' => $lq->no_quote ?: '-',
                'subtitle' => $lq->title ?: 'Parts Quotation',
                'badge' => 'Parts Quote',
                'badge_class' => 'bg-label-warning',
                'icon' => 'mdi-file-document-outline',
                'url' => route('quotation.show', $lq->id),
            ];
        });
        $quotations = $sqList->concat($lqList)->take(5)->values();

        // 4. Invoices (Visible for Admin, Accounting, Sales, Developer)
        $invoices = collect([]);
        if (in_array($role, ['Admin', 'Accounting', 'Developer', 'Sales', 'Sales Manager'])) {
            $invoices = Invoice::leftJoin('quotation as q', 'q.id', '=', 'invoice.id_quotation')
                ->leftJoin('pic as p', 'p.id', '=', 'q.id_pic')
                ->leftJoin('client as cq', 'cq.id', '=', 'p.id_client')
                ->leftJoin('unit_quotation as uq', 'uq.id', '=', 'invoice.id_unit_quotation')
                ->leftJoin('client as cuq', 'cuq.id', '=', 'uq.id_client')
                ->where(function ($w) use ($query) {
                    $w->where('invoice.no_invoice', 'LIKE', "%{$query}%")
                      ->orWhere('invoice.no_po', 'LIKE', "%{$query}%")
                      ->orWhere('cq.company', 'LIKE', "%{$query}%")
                      ->orWhere('cuq.company', 'LIKE', "%{$query}%");
                })
                ->select(
                    'invoice.id',
                    'invoice.id_unit_quotation',
                    'invoice.no_invoice',
                    'invoice.no_po',
                    'cq.company as quote_client',
                    'cuq.company as unit_quote_client'
                )
                ->limit(4)->get()->map(function ($inv) {
                    $client = $inv->unit_quote_client ?: ($inv->quote_client ?: ($inv->no_po ? 'PO: ' . $inv->no_po : 'Invoice Client'));
                    $url = $inv->id_unit_quotation ? route('invoice.show_unit', $inv->id) : route('invoice.show', $inv->id);
                    return [
                        'id' => $inv->id,
                        'name' => $inv->no_invoice ?: ($inv->no_po ? 'PO: ' . $inv->no_po : 'Invoice #' . $inv->id),
                        'subtitle' => $client,
                        'badge' => 'Invoice',
                        'badge_class' => 'bg-label-primary',
                        'icon' => 'mdi-receipt-text-outline',
                        'url' => $url,
                    ];
                });
        }

        // 5. Units (Master Unit Global)
        $units = Unit::query()
            ->where(function ($w) use ($query) {
                $w->where('sku', 'LIKE', "%{$query}%")
                  ->orWhere('brand', 'LIKE', "%{$query}%")
                  ->orWhere('model', 'LIKE', "%{$query}%");
            })
            ->limit(4)->get()->map(function ($u) {
                $title = trim(($u->brand ? $u->brand . ' ' : '') . ($u->model ?: ''));
                return [
                    'id' => $u->id,
                    'name' => $u->sku . ($title ? ' — ' . $title : ''),
                    'subtitle' => $u->unit ?: 'Unit Global',
                    'badge' => 'Unit',
                    'badge_class' => 'bg-label-secondary',
                    'icon' => 'mdi-cog-outline',
                    'url' => route('unit-global.show', $u->id),
                ];
            });

        // 6. Service Reports (Visible for Technician, Manager, Admin, Dev, Sales)
        $serviceReports = collect([]);
        if (in_array($role, ['Admin', 'Developer', 'Technician', 'Service Manager', 'Service Admin', 'Sales', 'Sales Manager'])) {
            $serviceReports = Reports::leftJoin('machine as m', 'm.id', '=', 'reports.id_machine')
                ->leftJoin('client as c', 'c.id', '=', 'm.id_client')
                ->where(function ($w) use ($query) {
                    $w->where('reports.jobdesc', 'LIKE', "%{$query}%")
                      ->orWhere('reports.desc', 'LIKE', "%{$query}%")
                      ->orWhere('reports.type', 'LIKE', "%{$query}%")
                      ->orWhere('c.company', 'LIKE', "%{$query}%")
                      ->orWhere('m.serial', 'LIKE', "%{$query}%")
                      ->orWhere('m.tag', 'LIKE', "%{$query}%")
                      ->orWhere('m.desc', 'LIKE', "%{$query}%");
                })
                ->select(
                    'reports.id',
                    'reports.type',
                    'reports.jobdesc',
                    'c.company as client_company',
                    'm.serial as machine_serial',
                    'm.tag as machine_tag'
                )
                ->limit(4)->get()->map(function ($sr) {
                    $sub = [];
                    if ($sr->client_company) $sub[] = $sr->client_company;
                    if ($sr->machine_serial) $sub[] = 'SN: ' . $sr->machine_serial;
                    if ($sr->jobdesc) $sub[] = $sr->jobdesc;
                    return [
                        'id' => $sr->id,
                        'name' => 'SR #' . $sr->id . ($sr->type ? ' (' . $sr->type . ')' : ''),
                        'subtitle' => !empty($sub) ? implode(' • ', $sub) : ($sr->type ?: 'Service Report'),
                        'badge' => 'Service Report',
                        'badge_class' => 'bg-label-info',
                        'icon' => 'mdi-wrench-outline',
                        'url' => route('service-reports.show', $sr->id),
                    ];
                });
        }

        // 7. Purchase Orders (Visible for Admin, Logistic, Accounting, Developer)
        $purchaseOrders = collect([]);
        if (in_array($role, ['Admin', 'Developer', 'Logistic', 'Accounting', 'Purchasing'])) {
            $purchaseOrders = PurchaseOrder::query()
                ->where(function ($w) use ($query) {
                    $w->where('no_po', 'LIKE', "%{$query}%")
                      ->orWhere('company', 'LIKE', "%{$query}%");
                })
                ->limit(4)->get()->map(function ($po) {
                    return [
                        'id' => $po->id,
                        'name' => $po->no_po ?: '-',
                        'subtitle' => $po->company ?: 'PO Supplier',
                        'badge' => 'Purchase Order',
                        'badge_class' => 'bg-label-warning',
                        'icon' => 'mdi-cart-outline',
                        'url' => route('purchase.show', $po->id),
                    ];
                });
        }

        return response()->json([
            'pages' => $matchedPages,
            'clients' => $clients,
            'quotations' => $quotations,
            'invoices' => $invoices,
            'units' => $units,
            'service_reports' => $serviceReports,
            'purchase_orders' => $purchaseOrders,
        ]);
    }

    /**
     * Get system pages list filtered by user role.
     */
    private function getAvailablePages(string $role): array
    {
        $pages = [];

        // Common
        $pages[] = ['name' => 'Dashboard Overview', 'category' => 'Dashboard', 'icon' => 'mdi-home-outline', 'url' => url('/')];
        $pages[] = ['name' => 'Rekap Performance / Overview', 'category' => 'Dashboard', 'icon' => 'mdi-chart-areaspline', 'url' => route('report.current')];
        $pages[] = ['name' => 'Monthly Report Sales', 'category' => 'Reports', 'icon' => 'mdi-finance', 'url' => route('report.monthly')];
        $pages[] = ['name' => 'Kanban Board', 'category' => 'Task', 'icon' => 'mdi-view-dashboard-outline', 'url' => route('kanban.index')];

        // Sales & Client
        if (in_array($role, ['Admin', 'Developer', 'Sales', 'Sales Manager', 'Project Admin', 'Accounting'])) {
            $pages[] = ['name' => 'Data Leads', 'category' => 'Sales', 'icon' => 'mdi-account-search-outline', 'url' => route('index-sales.leads')];
            $pages[] = ['name' => 'Data Customers (CRM)', 'category' => 'Sales', 'icon' => 'mdi-account-group-outline', 'url' => route('index-sales.customers')];
            $pages[] = ['name' => 'Smart Quotation (Unit)', 'category' => 'Quotation', 'icon' => 'mdi-file-document-outline', 'url' => route('unit-quotation.index')];
            $pages[] = ['name' => 'Parts Quotation', 'category' => 'Quotation', 'icon' => 'mdi-file-document-edit-outline', 'url' => route('quotation.index')];
            $pages[] = ['name' => 'Sales Forecast', 'category' => 'Sales', 'icon' => 'mdi-trending-up', 'url' => route('forecast.index')];
            $pages[] = ['name' => 'Sales Payment Templates', 'category' => 'Sales', 'icon' => 'mdi-credit-card-outline', 'url' => route('sales-payment-templates.index')];
            $pages[] = ['name' => 'Schedule & Visits', 'category' => 'Sales', 'icon' => 'mdi-calendar-clock', 'url' => url('/schedule-visit')];
        }

        // Warehouse & Logistics
        if (in_array($role, ['Admin', 'Developer', 'Logistic', 'Sales', 'Sales Manager'])) {
            $pages[] = ['name' => 'Unit Global (Master Unit)', 'category' => 'Warehouse', 'icon' => 'mdi-cube-outline', 'url' => route('unit-global.index')];
            $pages[] = ['name' => 'Unit Siap Ditawarkan (Bekas & Baru)', 'category' => 'Warehouse', 'icon' => 'mdi-package-variant-closed', 'url' => route('unit.index')];
            $pages[] = ['name' => 'Unit Acquisition (Aset Mesin)', 'category' => 'Warehouse', 'icon' => 'mdi-warehouse', 'url' => route('unit-acquisition.index')];
            $pages[] = ['name' => 'Unit Product In (Goods Receipt)', 'category' => 'Warehouse', 'icon' => 'mdi-truck-delivery-outline', 'url' => route('unit-product-in.index')];
            $pages[] = ['name' => 'Unit Product Out', 'category' => 'Warehouse', 'icon' => 'mdi-dolly', 'url' => route('unit-product-out.index')];
            $pages[] = ['name' => 'Sparepart & Stock Inventory', 'category' => 'Warehouse', 'icon' => 'mdi-database-outline', 'url' => route('stock.index')];
            $pages[] = ['name' => 'Delivery Order', 'category' => 'Warehouse', 'icon' => 'mdi-truck-fast-outline', 'url' => route('delivery.index')];
        }

        // Service & Technical
        if (in_array($role, ['Admin', 'Developer', 'Technician', 'Service Manager', 'Service Admin', 'Sales'])) {
            $pages[] = ['name' => 'Service Reports', 'category' => 'Service', 'icon' => 'mdi-wrench-outline', 'url' => route('service-reports.index')];
            $pages[] = ['name' => 'Master Machine', 'category' => 'Service', 'icon' => 'mdi-engine-outline', 'url' => route('machine.index')];
        }

        // Purchasing
        if (in_array($role, ['Admin', 'Developer', 'Logistic', 'Purchasing', 'Accounting'])) {
            $pages[] = ['name' => 'Purchase Orders (PO Supplier)', 'category' => 'Purchasing', 'icon' => 'mdi-cart-outline', 'url' => route('purchase.index')];
            $pages[] = ['name' => 'Fixed Assets', 'category' => 'Purchasing', 'icon' => 'mdi-office-building-cog-outline', 'url' => route('fixed.index')];
        }

        // Accounting & Finance
        if (in_array($role, ['Admin', 'Developer', 'Accounting'])) {
            $pages[] = ['name' => 'Invoice Management', 'category' => 'Accounting', 'icon' => 'mdi-receipt-text-outline', 'url' => route('invoice.index')];
            $pages[] = ['name' => 'Accounting Reports', 'category' => 'Accounting', 'icon' => 'mdi-finance', 'url' => route('reports.index')];
        }

        // Admin & Developer
        if (in_array($role, ['Admin', 'Developer'])) {
            $pages[] = ['name' => 'User Management', 'category' => 'Settings', 'icon' => 'mdi-account-cog-outline', 'url' => route('users.index')];
            $pages[] = ['name' => 'Maintenance Mode Settings', 'category' => 'Settings', 'icon' => 'mdi-server-security', 'url' => route('developer.maintenance.index')];
        }

        return $pages;
    }
}
