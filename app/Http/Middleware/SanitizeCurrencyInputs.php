<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class SanitizeCurrencyInputs extends TransformsRequest
{
    /**
     * Common currency/nominal field name patterns to sanitize.
     */
    protected $currencyFieldPatterns = [
        'amount',
        'price',
        'harga',
        'budget',
        'saldo',
        'fee',
        'nominal',
        'subtotal',
        'total',
        'discount',
        'diskon',
        'biaya',
        'shipping',
        'ongkir',
        'down_payment',
        'dp',
        'initial_balance',
        'plafond',
        'modal',
        'revenue',
        'hpp',
        'gross_fee',
        'selling_price',
    ];

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (!is_string($value)) {
            return $value;
        }

        // Check if string matches strict Indonesian thousand-separated format (e.g. "1.500.000", "50.000")
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
            return str_replace('.', '', $value);
        }

        // Check if key matches known currency field or monthly budget m1..m12
        $lowerKey = strtolower($key);
        $isCurrencyKey = false;

        if (preg_match('/^m([1-9]|1[0-2])$/', $lowerKey)) {
            $isCurrencyKey = true;
        } else {
            foreach ($this->currencyFieldPatterns as $pattern) {
                if (str_contains($lowerKey, $pattern)) {
                    $isCurrencyKey = true;
                    break;
                }
            }
        }

        if ($isCurrencyKey && !empty($value)) {
            // Strip any thousand dots and currency prefix e.g. "Rp 1.000.000" -> "1000000"
            // Keep pure digits
            $cleaned = preg_replace('/[^\d]/', '', $value);
            if ($cleaned !== '' && is_numeric($cleaned)) {
                return $cleaned;
            }
        }

        return $value;
    }
}
