<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIn extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "product_in";
    protected $date = [
        'created_at',
        'updated_at',
        'date_invoice',
        'date'
    ];
    protected $fillable = [
        'no_product_in',
        'created_by',
        'no_do',
        'invoice',
        'id_purchase_order',
        'supplier',
        'note',
        'subtotal',
        'total_no_tax',
        'shipping',
        'tax',
        'total',
        'price',
    ];
    public function supp()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo('App\Models\PurchaseOrder', 'id_purchase_order', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailProductIn', 'id_product_in');
    }
    public function return()
    {
        return $this->hasMany('App\Models\Retur', 'id_product_in');
    }
    public function costs()
    {
        return $this->hasMany('App\Models\ProductInCost', 'id_product_in');
    }
    public function payments()
    {
        return $this->hasMany('App\Models\PurchasePayment', 'id_product_in');
    }

    public function getTotalPaidAttribute()
    {
        $paidFromPayments = (float) $this->payments()->sum('amount');
        if ($paidFromPayments > 0) {
            return $paidFromPayments;
        }
        if ($this->accept == '1') {
            return (float) $this->total;
        }
        return 0;
    }

    public function getRemainingPayableAttribute()
    {
        return max(0, (float) $this->total - $this->total_paid);
    }

    public function getPaymentStatusAttribute()
    {
        if ($this->accept == '1' || $this->remaining_payable <= 0) {
            return 'paid';
        }
        if ($this->total_paid > 0 && $this->remaining_payable > 0) {
            return 'partial';
        }
        return 'unpaid';
    }

    public function getDueDateAttribute()
    {
        if (!empty($this->date_payment)) {
            return \Carbon\Carbon::parse($this->date_payment)->toDateString();
        }
        $topDays = 30;
        if ($this->purchaseOrder && !empty($this->purchaseOrder->top)) {
            $topDays = (int) $this->purchaseOrder->top;
        }
        $baseDate = $this->date_invoice ?: $this->date;
        if ($baseDate) {
            return \Carbon\Carbon::parse($baseDate)->addDays($topDays)->toDateString();
        }
        return null;
    }

    public function getDueStatusAttribute()
    {
        if ($this->accept == '1' || $this->remaining_payable <= 0) {
            return 'paid';
        }
        $dueDate = $this->due_date;
        if (!$dueDate) {
            return 'current';
        }
        $today = \Carbon\Carbon::today();
        $due = \Carbon\Carbon::parse($dueDate)->startOfDay();
        
        if ($today->gt($due)) {
            return 'overdue';
        }
        $daysUntilDue = $today->diffInDays($due, false);
        if ($daysUntilDue <= 7) {
            return 'due_soon';
        }
        return 'current';
    }
}
