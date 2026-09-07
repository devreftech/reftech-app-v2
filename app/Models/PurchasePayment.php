<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'purchase_payments';

    protected $fillable = [
        'id_product_in',
        'id_project_expense',
        'id_supplier',
        'id_bank',
        'payment_number',
        'date',
        'amount',
        'payment_method',
        'proof_file',
        'note',
        'created_by',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function productIn()
    {
        return $this->belongsTo(ProductIn::class, 'id_product_in');
    }

    public function projectExpense()
    {
        return $this->belongsTo(ProjectExpense::class, 'id_project_expense');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
