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

    public function getNamaRekAttribute()
    {
        return $this->atas_nama;
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

    public function adjustments()
    {
        return $this->hasMany('App\Models\BankAdjustment', 'id_bank');
    }

    /**
     * Check how many transactions are linked to this bank account across all financial modules.
     */
    public function getTransactionCount(): int
    {
        $arCount = Payment::where('id_bank', $this->id)->count();
        $apCount = PurchasePayment::where('id_bank', $this->id)->count();
        $expenseCount = Expense::where('id_bank', $this->id)->count();
        $transferCount = BankTransfer::where('id_from_bank', $this->id)->orWhere('id_to_bank', $this->id)->count();
        $feeCount = UnitQuotation::where('id_source_bank', $this->id)->where('fee_payment_status', 'paid')->count()
                  + ManualManagementFee::where('id_source_bank', $this->id)->where('fee_payment_status', 'paid')->count();
        $pettyCount = PettyCashTransaction::where('id_bank', $this->id)->orWhere('id_source_bank', $this->id)->count();
        $adjustmentCount = BankAdjustment::where('id_bank', $this->id)->count();

        return (int) ($arCount + $apCount + $expenseCount + $transferCount + $feeCount + $pettyCount + $adjustmentCount);
    }

    /**
     * Determine whether this bank account has any transaction history.
     */
    public function hasTransactions(): bool
    {
        return $this->getTransactionCount() > 0;
    }
}

