<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevQuote extends Model
{
    use HasFactory;
    protected $table = "rev_quote";
    protected $fillable = [
        "id_quotation", "rev_no_quote"
        ];

        public function quotation(){
            return $this->belongsTo('App\Models\Quotation', 'id', 'id_quotation');
        }
}
