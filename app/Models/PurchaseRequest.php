<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;
    protected $table = "purchase_request";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'no_pr',
        'no_gr',
        'id_pending',
        'id_user',
        'status',
        'gr_sent_at',
        'rejected_at',
        'rejected_reason',
        'rejected_by',
        'date',
    ];
    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
    public function details()
    {
        return $this->hasMany('App\Models\PurchaseRequestDetail', 'id_purchase_request');
    }
    public function purchaseOrders()
    {
        return $this->hasMany('App\Models\PurchaseOrder', 'id_purchase_request');
    }

    /**
     * Dapatkan semua PO terkait (baik melalui FK langsung maupun melalui alokasi item detail).
     */
    public function getPurchaseOrdersAttribute()
    {
        if ($this->relationLoaded('purchaseOrders')) {
            $directPos = $this->getRelation('purchaseOrders');
        } else {
            $directPos = $this->purchaseOrders()->get();
        }

        $detailIds = $this->relationLoaded('details')
            ? $this->details->pluck('id')
            : PurchaseRequestDetail::where('id_purchase_request', $this->id)->pluck('id');

        $poIdsFromAlloc = PurchaseRequestDetailAllocation::whereIn('id_purchase_request_detail', $detailIds)
            ->pluck('id_purchase_order')
            ->filter()
            ->unique();

        if ($poIdsFromAlloc->isEmpty()) {
            return $directPos;
        }

        $directIds = $directPos->pluck('id')->toArray();
        $missingPoIds = $poIdsFromAlloc->filter(fn($id) => !in_array($id, $directIds))->values();

        if ($missingPoIds->isEmpty()) {
            return $directPos;
        }

        $extraPos = PurchaseOrder::whereIn('id', $missingPoIds)->get();
        return $directPos->concat($extraPos);
    }

    public function rejector()
    {
        return $this->belongsTo('App\Models\User', 'rejected_by', 'id');
    }
}
