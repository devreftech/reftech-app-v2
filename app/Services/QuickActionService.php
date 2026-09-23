<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class QuickActionService
{
    /**
     * Maximum number of quick actions allowed per user.
     */
    public const MAX_ITEMS = 4;

    /**
     * Master catalog of all available quick actions across the system.
     *
     * @return array<string, array{
     *     id: string,
     *     title: string,
     *     subtitle: string,
     *     category: string,
     *     icon: string,
     *     icon_bg: string,
     *     route: string|null,
     *     route_params: array|null,
     *     url: string|null,
     *     roles: array<string>
     * }>
     */
    public static function getMasterCatalog(): array
    {
        return [
            // ==================== SALES & MARKETING ====================
            'unit_quotation_create' => [
                'id'           => 'unit_quotation_create',
                'title'        => 'Create Quote',
                'subtitle'     => 'Smart Quote',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-file-document-plus-outline',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'unit-quotation.create',
                'route_params' => [],
                'url'          => '/smart-quote/create',
                'roles'        => ['*'],
            ],
            'leads_index' => [
                'id'           => 'leads_index',
                'title'        => 'Create Leads',
                'subtitle'     => 'Tambah Calon Pelanggan',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-account-plus-outline',
                'icon_bg'      => 'bg-label-success',
                'route'        => 'leads.index',
                'route_params' => [],
                'url'          => '/leads',
                'roles'        => ['*'],
            ],
            'product_stock' => [
                'id'           => 'product_stock',
                'title'        => 'Stock Spare Part',
                'subtitle'     => 'Cek Data Product & Stok',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-package-variant-closed',
                'icon_bg'      => 'bg-label-warning',
                'route'        => 'product.index',
                'route_params' => [],
                'url'          => '/product',
                'roles'        => ['*'],
            ],
            'unit_ready' => [
                'id'           => 'unit_ready',
                'title'        => 'Unit Ready Stock',
                'subtitle'     => 'Cek Unit Siap Ditawarkan',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-air-conditioner',
                'icon_bg'      => 'bg-label-info',
                'route'        => 'unit.index',
                'route_params' => [],
                'url'          => '/unit',
                'roles'        => ['*'],
            ],
            'leads_by_sales' => [
                'id'           => 'leads_by_sales',
                'title'        => 'Leads By Sales',
                'subtitle'     => 'Monitoring Leads Sales',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-account-switch-outline',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'index-sales.leads',
                'route_params' => [],
                'url'          => '/leads-by-sales',
                'roles'        => ['*'],
            ],
            'customers_by_sales' => [
                'id'           => 'customers_by_sales',
                'title'        => 'Customers By Sales',
                'subtitle'     => 'Monitoring Data Pelanggan',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-account-group-outline',
                'icon_bg'      => 'bg-label-info',
                'route'        => 'index-sales.customers',
                'route_params' => [],
                'url'          => '/customer-by-sales',
                'roles'        => ['*'],
            ],
            'quotation_index' => [
                'id'           => 'quotation_index',
                'title'        => 'Daftar Quotation',
                'subtitle'     => 'Kelola Penawaran Sparepart',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-email-outline',
                'icon_bg'      => 'bg-label-warning',
                'route'        => 'quotation.index',
                'route_params' => [],
                'url'          => '/quotation',
                'roles'        => ['*'],
            ],
            'prospect_index' => [
                'id'           => 'prospect_index',
                'title'        => 'Marketing Leads',
                'subtitle'     => 'Pipeline Leads Marketing',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-account-details-outline',
                'icon_bg'      => 'bg-label-danger',
                'route'        => 'prospect.index',
                'route_params' => [],
                'url'          => '/prospect',
                'roles'        => ['*'],
            ],
            'monthly_report' => [
                'id'           => 'monthly_report',
                'title'        => 'Monthly Report',
                'subtitle'     => 'Laporan Sales & Marketing',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-finance',
                'icon_bg'      => 'bg-label-success',
                'route'        => 'report.monthly',
                'route_params' => [],
                'url'          => '/report/monthly',
                'roles'        => ['*'],
            ],
            'sales_mailbox' => [
                'id'           => 'sales_mailbox',
                'title'        => 'Sales Mailbox',
                'subtitle' => 'Email & Inbox Penawaran',
                'category'     => 'Sales & Marketing',
                'icon'         => 'mdi mdi-email-fast-outline',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'sales.mailbox.index',
                'route_params' => [],
                'url'          => '/sales/mailbox',
                'roles'        => ['*'],
            ],

            // ==================== ACCOUNTING & FINANCE ====================
            'invoice_index' => [
                'id'           => 'invoice_index',
                'title'        => 'Cek Invoice',
                'subtitle'     => 'Daftar & Status Tagihan',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-file-document-check-outline',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'invoice.index',
                'route_params' => [],
                'url'          => '/invoice',
                'roles'        => ['*'],
            ],
            'kanban_mon_doc' => [
                'id'           => 'kanban_mon_doc',
                'title'        => 'Mon. Document',
                'subtitle'     => 'Monitoring Dokumen Penagihan',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-view-dashboard-outline',
                'icon_bg'      => 'bg-label-info',
                'route'        => 'kanban.monitoring-document',
                'route_params' => [],
                'url'          => '/accounting/monitoring-document',
                'roles'        => ['*'],
            ],
            'invoice_request' => [
                'id'           => 'invoice_request',
                'title'        => 'Request Invoice',
                'subtitle'     => 'Antrean Permintaan Invoice',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-file-clock-outline',
                'icon_bg'      => 'bg-label-warning',
                'route'        => 'invoice.request',
                'route_params' => [],
                'url'          => '/request/invoice',
                'roles'        => ['*'],
            ],
            'payment_receipt' => [
                'id'           => 'payment_receipt',
                'title'        => 'Payment Receipt',
                'subtitle'     => 'Penerimaan Pembayaran (AR)',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-cash-check',
                'icon_bg'      => 'bg-label-success',
                'route'        => 'payment_index.payment',
                'route_params' => [],
                'url'          => '/payment-index/payment',
                'roles'        => ['*'],
            ],
            'payment_aging' => [
                'id'           => 'payment_aging',
                'title'        => 'Invoice Aging',
                'subtitle'     => 'Piutang Jatuh Tempo (AR)',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-calendar-clock',
                'icon_bg'      => 'bg-label-danger',
                'route'        => 'payment_index.aging',
                'route_params' => [],
                'url'          => '/payment-index/aging',
                'roles'        => ['*'],
            ],
            'purchase_payments' => [
                'id'           => 'purchase_payments',
                'title'        => 'Pembayaran Supplier',
                'subtitle'     => 'Hutang & Pengeluaran AP',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-bank-transfer-out',
                'icon_bg'      => 'bg-label-warning',
                'route'        => 'payable.index_receipt',
                'route_params' => [],
                'url'          => '/payable/receipt',
                'roles'        => ['*'],
            ],
            'petty_cash' => [
                'id'           => 'petty_cash',
                'title'        => 'Kas Kecil / Petty Cash',
                'subtitle'     => 'Transaksi Kas Operasional',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-wallet-outline',
                'icon_bg'      => 'bg-label-info',
                'route'        => 'petty_cash.index',
                'route_params' => [],
                'url'          => '/finance/petty-cash',
                'roles'        => ['*'],
            ],
            'bank_transfers' => [
                'id'           => 'bank_transfers',
                'title'        => 'Rekening Bank & Mutasi',
                'subtitle'     => 'Daftar Kas & Rekening',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-bank',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'bank.index',
                'route_params' => [],
                'url'          => '/finance/bank',
                'roles'        => ['*'],
            ],
            'bank_reconciliation' => [
                'id'           => 'bank_reconciliation',
                'title'        => 'Rekonsiliasi Bank',
                'subtitle'     => 'Pencocokan Rekening Koran',
                'category'     => 'Accounting & Finance',
                'icon'         => 'mdi mdi-swap-horizontal-bold',
                'icon_bg'      => 'bg-label-success',
                'route'        => 'finance.reconciliation.index',
                'route_params' => [],
                'url'          => '/finance/bank-reconciliation',
                'roles'        => ['*'],
            ],

            // ==================== PROJECT & SERVICE ====================
            'kanban_index' => [
                'id'           => 'kanban_index',
                'title'        => 'Papan Kanban',
                'subtitle'     => 'Task & Workflow Kanban',
                'category'     => 'Project & Service',
                'icon'         => 'mdi mdi-view-dashboard',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'kanban.index',
                'route_params' => [],
                'url'          => '/kanban',
                'roles'        => ['*'],
            ],
            'service_reports_project' => [
                'id'           => 'service_reports_project',
                'title'        => 'Project Report',
                'subtitle'     => 'Daily Service & Project Report',
                'category'     => 'Project & Service',
                'icon'         => 'mdi mdi-clipboard-text-clock-outline',
                'icon_bg'      => 'bg-label-info',
                'route'        => 'service-reports.index',
                'route_params' => ['tab' => 'project'],
                'url'          => '/service-reports?tab=project',
                'roles'        => ['*'],
            ],
            'basts_index' => [
                'id'           => 'basts_index',
                'title'        => 'BAST',
                'subtitle'     => 'Berita Acara Serah Terima',
                'category'     => 'Project & Service',
                'icon'         => 'mdi mdi-file-sign',
                'icon_bg'      => 'bg-label-success',
                'route'        => 'bast.index',
                'route_params' => [],
                'url'          => '/bast',
                'roles'        => ['*'],
            ],
            'contracts_index' => [
                'id'           => 'contracts_index',
                'title'        => 'Selling Contract',
                'subtitle'     => 'Daftar Kontrak Customer',
                'category'     => 'Project & Service',
                'icon'         => 'mdi mdi-file-certificate-outline',
                'icon_bg'      => 'bg-label-warning',
                'route'        => 'contract.index',
                'route_params' => [],
                'url'          => '/contract',
                'roles'        => ['*'],
            ],
            'tool_transfers' => [
                'id'           => 'tool_transfers',
                'title'        => 'Data & Audit Tools',
                'subtitle'     => 'Serah Terima Alat & Tool',
                'category'     => 'Project & Service',
                'icon'         => 'mdi mdi-tools',
                'icon_bg'      => 'bg-label-secondary',
                'route'        => 'tool-assignment.index',
                'route_params' => [],
                'url'          => '/tool-assignment',
                'roles'        => ['*'],
            ],
            'hvac_calculator' => [
                'id'           => 'hvac_calculator',
                'title'        => 'HVAC Estimator',
                'subtitle'     => 'Kalkulasi Beban Pendinginan',
                'category'     => 'Project & Service',
                'icon'         => 'mdi mdi-calculator-variant-outline',
                'icon_bg'      => 'bg-label-danger',
                'route'        => 'hvac.quick-calculator',
                'route_params' => [],
                'url'          => '/hvac/quick-calculator',
                'roles'        => ['*'],
            ],

            // ==================== PROCUREMENT & WAREHOUSE ====================
            'purchase_requests' => [
                'id'           => 'purchase_requests',
                'title'        => 'Purchase Request',
                'subtitle'     => 'Pengajuan Pembelian Barang (PR)',
                'category'     => 'Procurement & Warehouse',
                'icon'         => 'mdi mdi-cart-arrow-down',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'purchase-request.index',
                'route_params' => [],
                'url'          => '/purchase-request',
                'roles'        => ['*'],
            ],
            'purchase_orders' => [
                'id'           => 'purchase_orders',
                'title'        => 'Purchase Order',
                'subtitle'     => 'Pesanan Pembelian ke Vendor (PO)',
                'category'     => 'Procurement & Warehouse',
                'icon'         => 'mdi mdi-file-document-edit-outline',
                'icon_bg'      => 'bg-label-warning',
                'route'        => 'purchase.index',
                'route_params' => [],
                'url'          => '/purchase',
                'roles'        => ['*'],
            ],
            'product_in' => [
                'id'           => 'product_in',
                'title'        => 'Penerimaan Barang',
                'subtitle'     => 'Goods Receipt / Product In',
                'category'     => 'Procurement & Warehouse',
                'icon'         => 'mdi mdi-truck-delivery-outline',
                'icon_bg'      => 'bg-label-success',
                'route'        => 'product-in.index',
                'route_params' => [],
                'url'          => '/product-in',
                'roles'        => ['*'],
            ],
            'product_out' => [
                'id'           => 'product_out',
                'title'        => 'Pengeluaran Barang',
                'subtitle'     => 'Delivery Order / Product Out',
                'category'     => 'Procurement & Warehouse',
                'icon'         => 'mdi mdi-package-up',
                'icon_bg'      => 'bg-label-danger',
                'route'        => 'product-out.index',
                'route_params' => [],
                'url'          => '/product-out',
                'roles'        => ['*'],
            ],
            'supplier_index' => [
                'id'           => 'supplier_index',
                'title'        => 'Data Supplier',
                'subtitle'     => 'Daftar Vendor & Kontak PIC',
                'category'     => 'Procurement & Warehouse',
                'icon'         => 'mdi mdi-domain',
                'icon_bg'      => 'bg-label-info',
                'route'        => 'supplier.index',
                'route_params' => [],
                'url'          => '/supplier',
                'roles'        => ['*'],
            ],

            // ==================== HR & GENERAL AFFAIRS ====================
            'hr_portal' => [
                'id'           => 'hr_portal',
                'title'        => 'Portal Karyawan',
                'subtitle'     => 'Presensi, Cuti & Info Saya',
                'category'     => 'HR & General Affairs',
                'icon'         => 'mdi mdi-account-circle-outline',
                'icon_bg'      => 'bg-label-primary',
                'route'        => 'hr.portal.index',
                'route_params' => [],
                'url'          => '/hr/my-portal',
                'roles'        => ['*'],
            ],
            'hr_attendances' => [
                'id'           => 'hr_attendances',
                'title'        => 'Data Presensi',
                'subtitle'     => 'Rekap Kehadiran Staff',
                'category'     => 'HR & General Affairs',
                'icon'         => 'mdi mdi-calendar-check-outline',
                'icon_bg'      => 'bg-label-success',
                'route'        => 'hr.attendances.index',
                'route_params' => [],
                'url'          => '/hr/attendances',
                'roles'        => ['*'],
            ],
            'hr_leaves' => [
                'id'       => 'hr_leaves',
                'title'    => 'Cuti & Izin',
                'subtitle' => 'Pengajuan & Approval Cuti',
                'category' => 'HR & General Affairs',
                'icon'     => 'mdi mdi-calendar-month-outline',
                'icon_bg'  => 'bg-label-warning',
                'route'    => 'hr.leaves.index',
                'route_params' => [],
                'url'      => '/hr/leaves',
                'roles'    => ['*'],
            ],
            'hr_reimbursements' => [
                'id'       => 'hr_reimbursements',
                'title'    => 'Reimbursement',
                'subtitle' => 'Klaim Biaya Karyawan',
                'category' => 'HR & General Affairs',
                'icon'     => 'mdi mdi-receipt-text-outline',
                'icon_bg'  => 'bg-label-info',
                'route'    => 'hr.reimbursements.index',
                'route_params' => [],
                'url'      => '/hr/reimbursements',
                'roles'    => ['*'],
            ],
            'hr_employees' => [
                'id'       => 'hr_employees',
                'title'    => 'Data Karyawan',
                'subtitle' => 'Direktori Pegawai & Jabatan',
                'category' => 'HR & General Affairs',
                'icon'     => 'mdi mdi-card-account-details-outline',
                'icon_bg'  => 'bg-label-secondary',
                'route'    => 'employees.index',
                'route_params' => [],
                'url'      => '/employees',
                'roles'    => ['*'],
            ],

            // ==================== DEVELOPER & MANAGEMENT ====================
            'developer_dashboard' => [
                'id'       => 'developer_dashboard',
                'title'    => 'Dev Dashboard',
                'subtitle' => 'System Telemetry & Health',
                'category' => 'Developer & System',
                'icon'     => 'mdi mdi-monitor-dashboard',
                'icon_bg'  => 'bg-label-danger',
                'route'    => 'developer.dashboard',
                'route_params' => [],
                'url'      => '/developer/dashboard',
                'roles'    => ['Developer', 'Admin'],
            ],
            'developer_maintenance' => [
                'id'       => 'developer_maintenance',
                'title'    => 'Maintenance Mode',
                'subtitle' => 'Kelola Status Maintenance',
                'category' => 'Developer & System',
                'icon'     => 'mdi mdi-alert-octagon-outline',
                'icon_bg'  => 'bg-label-warning',
                'route'    => 'developer.maintenance.index',
                'route_params' => [],
                'url'      => '/developer/maintenance',
                'roles'    => ['Developer', 'Admin'],
            ],
            'activity_logs' => [
                'id'       => 'activity_logs',
                'title'    => 'Activity Logs',
                'subtitle' => 'Audit Log & Jejak Sistem',
                'category' => 'Developer & System',
                'icon'     => 'mdi mdi-history',
                'icon_bg'  => 'bg-label-info',
                'route'    => 'activity-log.index',
                'route_params' => [],
                'url'      => '/activity-log',
                'roles'    => ['Developer', 'Admin'],
            ],
            'helpdesk_tickets' => [
                'id'       => 'helpdesk_tickets',
                'title'    => 'Helpdesk Tickets',
                'subtitle' => 'Kelola Bug & Permintaan',
                'category' => 'Developer & System',
                'icon'     => 'mdi mdi-ticket-confirmation-outline',
                'icon_bg'  => 'bg-label-primary',
                'route'    => 'helpdesk.index',
                'route_params' => [],
                'url'      => '/helpdesk',
                'roles'    => ['*'],
            ],
        ];
    }

    /**
     * Get default action keys for a given user role.
     *
     * @param string|null $role
     * @return array<string>
     */
    public static function getDefaultActionIdsForRole(?string $role): array
    {
        return match ($role) {
            'Accounting', 'Finance Manager', 'Finance' => [
                'invoice_index',
                'kanban_mon_doc',
                'invoice_request',
                'payment_receipt',
            ],
            'Developer' => [
                'developer_dashboard',
                'developer_maintenance',
                'activity_logs',
                'kanban_index',
            ],
            'HR', 'HRD', 'General Affairs' => [
                'hr_attendances',
                'hr_leaves',
                'hr_reimbursements',
                'hr_employees',
            ],
            'Warehouse', 'Purchasing' => [
                'purchase_requests',
                'purchase_orders',
                'product_in',
                'product_out',
            ],
            'Service', 'Technician', 'Project Manager' => [
                'kanban_index',
                'service_reports_project',
                'basts_index',
                'tool_transfers',
            ],
            // Default (Sales, Sales Manager, Admin, etc.)
            default => [
                'unit_quotation_create',
                'leads_index',
                'product_stock',
                'unit_ready',
            ],
        };
    }

    /**
     * Resolve the active Quick Actions for a specific user (max 4 items).
     *
     * @param User|null $user
     * @return array<array{
     *     id: string,
     *     type?: string,
     *     title: string,
     *     subtitle: string,
     *     category: string,
     *     icon: string,
     *     icon_bg: string,
     *     url: string,
     *     raw_url?: string
     * }>
     */
    public static function getUserQuickActions(?User $user): array
    {
        $catalog = static::getMasterCatalog();
        $rawActions = [];

        if ($user && !empty($user->quick_actions) && is_array($user->quick_actions)) {
            $rawActions = array_slice($user->quick_actions, 0, static::MAX_ITEMS);
        }

        // Fallback to role defaults if user hasn't set custom shortcuts
        if (empty($rawActions)) {
            $rawActions = static::getDefaultActionIdsForRole($user?->role);
        }

        $result = [];
        foreach ($rawActions as $entry) {
            if (is_string($entry) && isset($catalog[$entry])) {
                $item = $catalog[$entry];
                $url = static::resolveItemUrl($item);
                $result[] = array_merge($item, [
                    'type'    => 'catalog',
                    'url'     => $url,
                    'raw_url' => $item['url'] ?? $url,
                ]);
            } elseif (is_array($entry) && (($entry['type'] ?? '') === 'custom' || !empty($entry['is_custom']))) {
                $url = $entry['url'] ?? '/';
                $rawUrl = $url;
                if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://') && !str_starts_with($url, '//')) {
                    $url = url($url);
                }
                $result[] = [
                    'id'       => $entry['id'] ?? ('custom_' . md5(($entry['title'] ?? '') . $rawUrl)),
                    'type'     => 'custom',
                    'title'    => $entry['title'] ?? 'Custom Link',
                    'subtitle' => $entry['subtitle'] ?? 'Direct URL',
                    'category' => 'Custom Link',
                    'icon'     => !empty($entry['icon']) ? $entry['icon'] : 'mdi mdi-link-variant',
                    'icon_bg'  => !empty($entry['icon_bg']) ? $entry['icon_bg'] : 'bg-label-primary',
                    'url'      => $url,
                    'raw_url'  => $rawUrl,
                ];
            }
        }

        // If user has no custom setting and somehow less than 4, pad with defaults
        if (empty($user?->quick_actions) && count($result) < static::MAX_ITEMS) {
            foreach (static::getDefaultActionIdsForRole($user?->role) as $fallbackId) {
                if (count($result) >= static::MAX_ITEMS) break;
                if (!collect($result)->contains('id', $fallbackId) && isset($catalog[$fallbackId])) {
                    $item = $catalog[$fallbackId];
                    $result[] = array_merge($item, [
                        'type'    => 'catalog',
                        'url'     => static::resolveItemUrl($item),
                        'raw_url' => $item['url'] ?? '',
                    ]);
                }
            }
        }

        return array_slice($result, 0, static::MAX_ITEMS);
    }

    /**
     * Get all available catalog items categorized for the modal selection,
     * filtered by user permission/role if applicable.
     *
     * @param User|null $user
     * @return array<string, array<int, array>>
     */
    public static function getCategorizedCatalogForUser(?User $user): array
    {
        $catalog = static::getMasterCatalog();
        $userRole = $user?->role ?? 'Sales';
        $isDev = $user?->isDeveloper() ?? false;

        $categorized = [];
        foreach ($catalog as $item) {
            // Role accessibility check
            if (!in_array('*', $item['roles'])) {
                if (!$isDev && !in_array($userRole, $item['roles'])) {
                    continue;
                }
            }

            $cat = $item['category'];
            if (!isset($categorized[$cat])) {
                $categorized[$cat] = [];
            }
            $categorized[$cat][] = array_merge($item, [
                'url' => static::resolveItemUrl($item),
            ]);
        }

        return $categorized;
    }

    /**
     * Resolve actual URL from route or static url.
     */
    public static function resolveItemUrl(array $item): string
    {
        if (!empty($item['route']) && Route::has($item['route'])) {
            try {
                $params = $item['route_params'] ?? [];
                return route($item['route'], $params);
            } catch (\Throwable $e) {
                // Ignore route parameter exception if any and fallback to url
            }
        }

        return url($item['url'] ?? '/');
    }
}
