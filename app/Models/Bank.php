<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    
    protected $table = "bank";
    
    protected $fillable = [
        'bank',
        'no_rek',
        'atas_nama',
        'entity',
        'branch',
        'initial_balance',
        'saldo',
        'is_active',
        'description',
        'no_voucher',
        'no_cheque',
        'memo',
        'payee',
        'amount',
        'is_petty_cash',
        'pic_id',
        'plafond',
    ];

    /**
     * Backward-compatibility accessors
     */
    public function getNamaBankAttribute()
    {
        return $this->bank;
    }

    public function getNoRekeningAttribute()
    {
        return $this->no_rek;
    }

    public function pic()
    {
        return $this->belongsTo('App\Models\User', 'pic_id');
    }

    public function pettyCashTransactions()
    {
        return $this->hasMany('App\Models\PettyCashTransaction', 'id_bank');
    }

    /**
     * Relations to financial modules
     */
    public function payable()
    {
        return $this->hasMany('App\Models\Payable', 'id_bank');
    }

    public function arPayments()
    {
        return $this->hasMany('App\Models\Payment', 'id_bank');
    }

    public function apPayments()
    {
        return $this->hasMany('App\Models\PurchasePayment', 'id_bank');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense', 'id_bank');
    }

    public function projectExpenses()
    {
        return $this->hasMany('App\Models\ProjectExpense', 'id_bank');
    }

    public function transfersOut()
    {
        return $this->hasMany('App\Models\BankTransfer', 'id_from_bank');
    }

    public function transfersIn()
    {
        return $this->hasMany('App\Models\BankTransfer', 'id_to_bank');
    }

    public function unitQuotationFees()
    {
        return $this->hasMany('App\Models\UnitQuotation', 'id_source_bank');
    }

    public function manualManagementFees()
    {
        return $this->hasMany('App\Models\ManualManagementFee', 'id_source_bank');
    }
}
