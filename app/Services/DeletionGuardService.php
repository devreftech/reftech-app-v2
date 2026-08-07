<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PendingPO;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SerialProduct;

class DeletionGuardService
{
    public function checkQuotationDeletion(Quotation $quotation): array
    {
        $reasons = [];

        if ($quotation->invoice()->exists()) {
            $reasons[] = 'quotation sudah memiliki invoice';
        }

        if ($quotation->payment()->exists()) {
            $reasons[] = 'quotation sudah memiliki payment';
        }

        if ($quotation->suo()->exists()) {
            $reasons[] = 'quotation sudah terkait SUO';
        }

        return [
            'allowed' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    public function checkProductDeletion(Product $product): array
    {
        $reasons = [];

        if ($product->serial()->exists()) {
            $reasons[] = 'produk memiliki equivalent';
        }

        if ($product->detail()->exists()) {
            $reasons[] = 'produk memiliki data replacement/detail';
        }

        return [
            'allowed' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    public function checkEquivalentDeletion(SerialProduct $equivalent): array
    {
        $reasons = [];

        if ($equivalent->detailQuotation()->exists()) {
            $reasons[] = 'equivalent dipakai di detail quotation';
        }

        if ($equivalent->detailPending()->exists()) {
            $reasons[] = 'equivalent dipakai di pending PO';
        }

        if ($equivalent->purchaseRequests()->exists()) {
            $reasons[] = 'equivalent dipakai di purchase request';
        }

        if ($equivalent->detailDelivery()->exists()) {
            $reasons[] = 'equivalent dipakai di detail delivery';
        }

        if ($equivalent->detailReturn()->exists()) {
            $reasons[] = 'equivalent dipakai di return';
        }

        if ($equivalent->spareparts()->exists()) {
            $reasons[] = 'equivalent dipakai di sparepart';
        }

        return [
            'allowed' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    public function checkPendingDeletion(PendingPO $pending): array
    {
        $reasons = [];

        if ($pending->detail()->exists()) {
            $reasons[] = 'pending PO memiliki detail item';
        }

        if ($pending->pr()->exists()) {
            $reasons[] = 'pending PO memiliki purchase request';
        }

        if ($pending->return()->exists()) {
            $reasons[] = 'pending PO memiliki return';
        }

        return [
            'allowed' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    public function checkInvoiceDeletion(Invoice $invoice): array
    {
        $reasons = [];

        if ($invoice->delivery()->exists()) {
            $reasons[] = 'invoice sudah memiliki delivery';
        }

        return [
            'allowed' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    public function checkPaymentDeletion(Payment $payment): array
    {
        $reasons = [];

        if ((int) $payment->level === 1) {
            $reasons[] = 'payment sudah terverifikasi';
        }

        return [
            'allowed' => empty($reasons),
            'reasons' => $reasons,
        ];
    }
}
