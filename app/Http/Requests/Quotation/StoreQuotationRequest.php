<?php

namespace App\Http\Requests\Quotation;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id_client' => ['required'],
            'id_pic' => ['required'],
            'id_sales' => ['required'],
            'no_quote' => ['required', 'string'],
            'title' => ['required', 'string'],
            'product' => ['required'],
            'detail_product' => ['required'],
            'expired_date' => ['required', 'date'],
            'validity' => ['required'],
            'pricing' => ['required'],
            'delivery_process' => ['required'],
            'payment' => ['required'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'validity.required' => 'Field Validity Wajib Diisi',
            'pricing.required' => 'Field Pricing Wajib Diisi',
            'delivery_process.required' => 'Field Delivery Process Wajib Diisi',
            'payment.required' => 'Field Payment Wajib Diisi',
        ];
    }
}
