<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Helper to safely add an index if it doesn't already exist.
     */
    protected function addIndexSafely(string $table, array|string $columns, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $existingIndexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))->pluck('Key_name')->unique()->toArray();
        if (in_array($indexName, $existingIndexes)) {
            return;
        }

        // Verify all columns exist in the table before indexing
        $tableColumns = Schema::getColumnListing($table);
        $cols = (array) $columns;
        foreach ($cols as $col) {
            if (!in_array($col, $tableColumns)) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($cols, $indexName) {
            $tableBlueprint->index($cols, $indexName);
        });
    }

    /**
     * Helper to safely drop an index if it exists.
     */
    protected function dropIndexSafely(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $existingIndexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))->pluck('Key_name')->unique()->toArray();
        if (!in_array($indexName, $existingIndexes)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
            $tableBlueprint->dropIndex($indexName);
        });
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Invoice table
        $this->addIndexSafely('invoice', 'id_quotation', 'idx_invoice_id_quotation');
        $this->addIndexSafely('invoice', 'id_unit_quotation', 'idx_invoice_id_unit_quotation');
        $this->addIndexSafely('invoice', 'no_invoice', 'idx_invoice_no_invoice');
        $this->addIndexSafely('invoice', 'date', 'idx_invoice_date');
        $this->addIndexSafely('invoice', 'type', 'idx_invoice_type');
        $this->addIndexSafely('invoice', 'status_p', 'idx_invoice_status_p');

        // 2. Payment table
        $this->addIndexSafely('payment', 'id_quotation', 'idx_payment_id_quotation');
        $this->addIndexSafely('payment', 'id_unit_quotation', 'idx_payment_id_unit_quotation');
        $this->addIndexSafely('payment', 'id_bank', 'idx_payment_id_bank');
        $this->addIndexSafely('payment', 'method', 'idx_payment_method');
        $this->addIndexSafely('payment', 'date', 'idx_payment_date');
        $this->addIndexSafely('payment', 'due_date', 'idx_payment_due_date');
        $this->addIndexSafely('payment', 'level', 'idx_payment_level');

        // 3. Prospect table
        $this->addIndexSafely('prospect', 'id_sales', 'idx_prospect_id_sales');
        $this->addIndexSafely('prospect', 'id_pic', 'idx_prospect_id_pic');
        $this->addIndexSafely('prospect', 'id_quotation', 'idx_prospect_id_quotation');
        $this->addIndexSafely('prospect', 'level', 'idx_prospect_level');
        $this->addIndexSafely('prospect', 'date', 'idx_prospect_date');
        $this->addIndexSafely('prospect', 'category', 'idx_prospect_category');

        // 4. Detail Quotation table
        $this->addIndexSafely('detail_quotation', 'id_quotation', 'idx_detail_quotation_id_quotation');
        $this->addIndexSafely('detail_quotation', 'id_equivalent', 'idx_detail_quotation_id_equivalent');
        $this->addIndexSafely('detail_quotation', 'status', 'idx_detail_quotation_status');

        // 5. Serial Product table
        $this->addIndexSafely('serial_product', 'pn', 'idx_serial_product_pn');
        $this->addIndexSafely('serial_product', 'brand', 'idx_serial_product_brand');

        // 6. Product table
        $this->addIndexSafely('product', 'go', 'idx_product_go');

        // 7. Contract table
        $this->addIndexSafely('contract', 'id_quotation', 'idx_contract_id_quotation');
        $this->addIndexSafely('contract', 'id_unit_quotation', 'idx_contract_id_unit_quotation');
        $this->addIndexSafely('contract', 'id_user', 'idx_contract_id_user');
        $this->addIndexSafely('contract', 'type', 'idx_contract_type');
        $this->addIndexSafely('contract', 'date', 'idx_contract_date');

        // 8. SUO table
        $this->addIndexSafely('suo', 'id_sales', 'idx_suo_id_sales');
        $this->addIndexSafely('suo', 'id_quotation', 'idx_suo_id_quotation');
        $this->addIndexSafely('suo', 'status', 'idx_suo_status');

        // 9. SUO Detail table
        $this->addIndexSafely('suo_detail', 'id_suo', 'idx_suo_detail_id_suo');
        $this->addIndexSafely('suo_detail', 'stock_status', 'idx_suo_detail_stock_status');

        // 10. Pending PO table
        $this->addIndexSafely('pending_po', 'id_unit_quotation', 'idx_pending_po_id_unit_quotation');
        $this->addIndexSafely('pending_po', 'status', 'idx_pending_po_status');
        $this->addIndexSafely('pending_po', 'type', 'idx_pending_po_type');

        // 11. Delivery table
        $this->addIndexSafely('delivery', 'id_invoice', 'idx_delivery_id_invoice');
        $this->addIndexSafely('delivery', 'id_unit_quotation', 'idx_delivery_id_unit_quotation');
        $this->addIndexSafely('delivery', 'id_suo', 'idx_delivery_id_suo');
        $this->addIndexSafely('delivery', 'date', 'idx_delivery_date');

        // 12. Comment table
        $this->addIndexSafely('comment', 'id_prospect', 'idx_comment_id_prospect');
        $this->addIndexSafely('comment', 'type', 'idx_comment_type');
        $this->addIndexSafely('comment', 'date', 'idx_comment_date');

        // 13. Client table
        $this->addIndexSafely('client', 'role', 'idx_client_role');
        $this->addIndexSafely('client', 'company', 'idx_client_company');

        // 14. Purchase Order table
        $this->addIndexSafely('purchase_order', 'id_supplier', 'idx_purchase_order_id_supplier');
        $this->addIndexSafely('purchase_order', 'receipt_status', 'idx_purchase_order_receipt_status');
        $this->addIndexSafely('purchase_order', 'id_purchase_request', 'idx_purchase_order_id_pr');
        $this->addIndexSafely('purchase_order', 'date', 'idx_purchase_order_date');

        // 15. Purchase Request table
        $this->addIndexSafely('purchase_request', 'id_user', 'idx_purchase_request_id_user');
        $this->addIndexSafely('purchase_request', 'id_pending', 'idx_purchase_request_id_pending');
        $this->addIndexSafely('purchase_request', 'status', 'idx_purchase_request_status');

        // 16. Product In table
        $this->addIndexSafely('product_in', 'id_supplier', 'idx_product_in_id_supplier');
        $this->addIndexSafely('product_in', 'invoice', 'idx_product_in_invoice');
        $this->addIndexSafely('product_in', 'date', 'idx_product_in_date');

        // 17. Product Out table
        $this->addIndexSafely('product_out', 'id_user', 'idx_product_out_id_user');
        $this->addIndexSafely('product_out', 'invoice', 'idx_product_out_invoice');
        $this->addIndexSafely('product_out', 'po', 'idx_product_out_po');
        $this->addIndexSafely('product_out', 'flag', 'idx_product_out_flag');
        $this->addIndexSafely('product_out', 'date', 'idx_product_out_date');

        // 18. Unit Quotation Detail table
        $this->addIndexSafely('unit_quotation_detail', 'id_unit_quotation', 'idx_uq_detail_id_unit_quotation');

        // 19. Unit Inventory table
        $this->addIndexSafely('unit_inventory', 'id_unit', 'idx_unit_inventory_id_unit');
        $this->addIndexSafely('unit_inventory', 'serial_number', 'idx_unit_inventory_serial_number');
        $this->addIndexSafely('unit_inventory', 'id_unit_product_in', 'idx_unit_inventory_id_unit_product_in');
        $this->addIndexSafely('unit_inventory', 'status', 'idx_unit_inventory_status');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Invoice
        $this->dropIndexSafely('invoice', 'idx_invoice_id_quotation');
        $this->dropIndexSafely('invoice', 'idx_invoice_id_unit_quotation');
        $this->dropIndexSafely('invoice', 'idx_invoice_no_invoice');
        $this->dropIndexSafely('invoice', 'idx_invoice_date');
        $this->dropIndexSafely('invoice', 'idx_invoice_type');
        $this->dropIndexSafely('invoice', 'idx_invoice_status_p');

        // 2. Payment
        $this->dropIndexSafely('payment', 'idx_payment_id_quotation');
        $this->dropIndexSafely('payment', 'idx_payment_id_unit_quotation');
        $this->dropIndexSafely('payment', 'idx_payment_id_bank');
        $this->dropIndexSafely('payment', 'idx_payment_method');
        $this->dropIndexSafely('payment', 'idx_payment_date');
        $this->dropIndexSafely('payment', 'idx_payment_due_date');
        $this->dropIndexSafely('payment', 'idx_payment_level');

        // 3. Prospect
        $this->dropIndexSafely('prospect', 'idx_prospect_id_sales');
        $this->dropIndexSafely('prospect', 'idx_prospect_id_pic');
        $this->dropIndexSafely('prospect', 'idx_prospect_id_quotation');
        $this->dropIndexSafely('prospect', 'idx_prospect_level');
        $this->dropIndexSafely('prospect', 'idx_prospect_date');
        $this->dropIndexSafely('prospect', 'idx_prospect_category');

        // 4. Detail Quotation
        $this->dropIndexSafely('detail_quotation', 'idx_detail_quotation_id_quotation');
        $this->dropIndexSafely('detail_quotation', 'idx_detail_quotation_id_equivalent');
        $this->dropIndexSafely('detail_quotation', 'idx_detail_quotation_status');

        // 5. Serial Product
        $this->dropIndexSafely('serial_product', 'idx_serial_product_pn');
        $this->dropIndexSafely('serial_product', 'idx_serial_product_brand');

        // 6. Product
        $this->dropIndexSafely('product', 'idx_product_go');

        // 7. Contract
        $this->dropIndexSafely('contract', 'idx_contract_id_quotation');
        $this->dropIndexSafely('contract', 'idx_contract_id_unit_quotation');
        $this->dropIndexSafely('contract', 'idx_contract_id_user');
        $this->dropIndexSafely('contract', 'idx_contract_type');
        $this->dropIndexSafely('contract', 'idx_contract_date');

        // 8. SUO
        $this->dropIndexSafely('suo', 'idx_suo_id_sales');
        $this->dropIndexSafely('suo', 'idx_suo_id_quotation');
        $this->dropIndexSafely('suo', 'idx_suo_status');

        // 9. SUO Detail
        $this->dropIndexSafely('suo_detail', 'idx_suo_detail_id_suo');
        $this->dropIndexSafely('suo_detail', 'idx_suo_detail_id_product');

        // 10. Pending PO
        $this->dropIndexSafely('pending_po', 'idx_pending_po_id_unit_quotation');
        $this->dropIndexSafely('pending_po', 'idx_pending_po_status');
        $this->dropIndexSafely('pending_po', 'idx_pending_po_type');

        // 11. Delivery
        $this->dropIndexSafely('delivery', 'idx_delivery_id_invoice');
        $this->dropIndexSafely('delivery', 'idx_delivery_id_unit_quotation');
        $this->dropIndexSafely('delivery', 'idx_delivery_id_suo');
        $this->dropIndexSafely('delivery', 'idx_delivery_date');

        // 12. Comment
        $this->dropIndexSafely('comment', 'idx_comment_id_prospect');
        $this->dropIndexSafely('comment', 'idx_comment_type');
        $this->dropIndexSafely('comment', 'idx_comment_date');

        // 13. Client
        $this->dropIndexSafely('client', 'idx_client_role');
        $this->dropIndexSafely('client', 'idx_client_company');

        // 14. Purchase Order
        $this->dropIndexSafely('purchase_order', 'idx_purchase_order_id_supplier');
        $this->dropIndexSafely('purchase_order', 'idx_purchase_order_status');
        $this->dropIndexSafely('purchase_order', 'idx_purchase_order_date');

        // 15. Purchase Request
        $this->dropIndexSafely('purchase_request', 'idx_purchase_request_id_sales');
        $this->dropIndexSafely('purchase_request', 'idx_purchase_request_status');

        // 16. Product In
        $this->dropIndexSafely('product_in', 'idx_product_in_id_supplier');
        $this->dropIndexSafely('product_in', 'idx_product_in_invoice');
        $this->dropIndexSafely('product_in', 'idx_product_in_date');

        // 17. Product Out
        $this->dropIndexSafely('product_out', 'idx_product_out_id_pending_po');
        $this->dropIndexSafely('product_out', 'idx_product_out_date');

        // 18. Unit Quotation Detail
        $this->dropIndexSafely('unit_quotation_detail', 'idx_uq_detail_id_unit_quotation');

        // 19. Unit Inventory
        $this->dropIndexSafely('unit_inventory', 'idx_unit_inventory_id_unit');
        $this->dropIndexSafely('unit_inventory', 'idx_unit_inventory_sn');
        $this->dropIndexSafely('unit_inventory', 'idx_unit_inventory_status');
    }
};
