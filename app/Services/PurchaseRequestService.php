<?php

namespace App\Services;

use App\Models\DetailPendingPO;
use App\Models\DetailQuotation;
use App\Models\Payment;
use App\Models\PendingPO;
use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseRequestService
{
    /**
     * Find the draft (status=0) PR header for this pending, or create one.
     * Locked so concurrent submissions for the same pending don't create
     * two headers.
     */
    public function findOrCreateDraftHeader(int $idPending, ?int $idUser): PurchaseRequest
    {
        return DB::transaction(function () use ($idPending, $idUser) {
            $header = PurchaseRequest::where('id_pending', $idPending)
                ->where('status', '0')
                ->lockForUpdate()
                ->first();

            if (!$header) {
                $header = new PurchaseRequest();
                $header->no_pr = $this->generateNoPr();
                $header->id_pending = $idPending;
                $header->id_user = $idUser;
                $header->status = '0';
                $header->date = Carbon::now();
                $header->save();
            }

            return $header;
        });
    }

    /**
     * PR baru boleh lanjut ke On Delivery kalau semua qty di semua item-nya
     * sudah teralokasi ke Purchase Order (bisa lebih dari satu PO/supplier per item).
     */
    public function isFullyAllocated(PurchaseRequest $pr): bool
    {
        return $pr->details->every(fn ($d) => $d->remainingQty <= 0);
    }

    /**
     * PR baru pindah status ke On Delivery(2) setelah SEMUA baris alokasi (di semua PO
     * yang terhubung) sudah diisi info pengirimannya. Dicek per alokasi, bukan per item PR,
     * karena satu item bisa split qty ke beberapa PO dengan cargo/resi/tanggal berbeda-beda.
     */
    public function allDeliveriesSubmitted(PurchaseRequest $pr): bool
    {
        $allocations = $pr->details->flatMap(fn ($d) => $d->allocations);

        return $allocations->isNotEmpty() && $allocations->every(fn ($a) => !is_null($a->purchase_type));
    }

    public function generateNoPr(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "PR/{$year}/{$month}/";

        $last = PurchaseRequest::where('no_pr', 'like', $prefix . '%')
            ->orderByDesc('no_pr')
            ->value('no_pr');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }

    /**
     * PR untuk sparepart baru diproses setelah ada kepastian pembayaran dari
     * customer. "Kepastian" di sini bukan cuma payment type='DP' — ada beberapa
     * skema (DP/BP, CBD, COD) yang semuanya sah asal sudah dikonfirmasi Accounting
     * (date_confirm terisi, atau level=1 — beberapa alur seperti Escrow di
     * QuotationController::add_payment set level=1 tanpa date_confirm).
     * Tempo (bayar belakangan/kredit term) dikecualikan dari syarat ini: barang
     * memang jalan duluan baru dibayar, jadi menunggu Tempo confirmed sebelum
     * memulai pengadaan justru muter balik / tidak masuk akal.
     */
    public function paymentGateSatisfied(PendingPO $pending): bool
    {
        $query = Payment::query();

        if ($pending->id_unit_quotation) {
            $query->where('id_unit_quotation', $pending->id_unit_quotation);
        } else {
            $query->where('id_quotation', $pending->id_quotation);
        }

        if ((clone $query)->where('type', 'Tempo')->exists()) {
            return true;
        }

        return (clone $query)->where(function ($q) {
            $q->whereNotNull('date_confirm')->orWhere('level', 1);
        })->exists();
    }

    /**
     * Buat baris PR untuk item quotation (non-unit) yang statusnya Kurang (3)
     * dan belum pernah diajukan ke PR. Idempoten lewat pr_requested_at.
     * pr_qty_needed disimpan langsung saat alokasi stok terjadi (lihat
     * QuotationController), supaya tidak perlu menghitung ulang dari qty/bdg/bks
     * di sini.
     */
    public function generateShortfallForQuotationPending(PendingPO $pending, ?int $idUser): void
    {
        $items = DetailQuotation::where('id_quotation', $pending->id_quotation)
            ->where('status', 3)
            ->whereNull('pr_requested_at')
            ->get();

        $this->createPrLinesForShortfall($pending, $idUser, $items);
    }

    /**
     * Sama seperti generateShortfallForQuotationPending tapi untuk alur Unit Quotation,
     * yang menyimpan status kekurangan langsung di detail_pending_po.
     */
    public function generateShortfallForUnitPending(PendingPO $pending, ?int $idUser): void
    {
        $items = DetailPendingPO::where('id_pending', $pending->id)
            ->where('status', 3)
            ->whereNull('pr_requested_at')
            ->get();

        $this->createPrLinesForShortfall($pending, $idUser, $items);
    }

    private function createPrLinesForShortfall(PendingPO $pending, ?int $idUser, $items): void
    {
        $header = null;
        foreach ($items as $item) {
            $missingQty = (int) $item->pr_qty_needed;
            if ($missingQty > 0) {
                if (!$header) {
                    $header = $this->findOrCreateDraftHeader($pending->id, $idUser);
                }
                $header->details()->create([
                    'id_equivalent' => $item->id_equivalent,
                    'qty' => $missingQty,
                    'note' => 'Otomatis dibuat oleh sistem karena stok kurang (qty kurang: ' . $missingQty . '), diproses setelah DP payment dikonfirmasi.',
                ]);
            }
            $item->pr_requested_at = Carbon::now();
            $item->save();
        }
    }

    /**
     * Entry point dipanggil setiap kali sebuah payment dibuat ATAU dikonfirmasi
     * (lewat PaymentController::confirm_payment, InvoiceController::confirm_payment_unit,
     * QuotationController::add_payment/confirm_payment, atau UnitQuotationController::addPayment).
     * Menilai sendiri apakah payment ini sudah cukup untuk membuka gate PR
     * (logic sama seperti paymentGateSatisfied) — kalau ya, generate PR untuk semua
     * pending sparepart milik quotation/unit-quotation ini yang masih menunggu.
     */
    public function evaluatePaymentGate(Payment $payment, ?int $idUser): void
    {
        $gateSatisfied = $payment->type === 'Tempo'
            || $payment->date_confirm !== null
            || (int) $payment->level === 1;

        if (!$gateSatisfied) {
            return;
        }

        if ($payment->id_unit_quotation) {
            $pendings = PendingPO::where('id_unit_quotation', $payment->id_unit_quotation)->get();
            foreach ($pendings as $pending) {
                $this->generateShortfallForUnitPending($pending, $idUser);
            }
            return;
        }

        $pendings = PendingPO::where('id_quotation', $payment->id_quotation)->get();
        foreach ($pendings as $pending) {
            $quotation = $pending->quote;
            if ($quotation && $quotation->type === 'Sparepart') {
                $this->generateShortfallForQuotationPending($pending, $idUser);
            }
        }
    }
}
