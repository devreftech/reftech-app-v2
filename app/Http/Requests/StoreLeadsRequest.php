<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id_sales' => [
                'required', 'integer'
            ],
            'id_issues' => [
                'required', 'integer'
            ],
            'company' => [
                'required', 'string', 'max:255'
            ],
            'email' => [
                'required', 'string', 'max:255', 'email'
            ],
            'phone' => [
                'required', 'regex:/^([0-9\s\-\=\(\)]*)$/', 'max:100'
            ],
            'web' => [
                'required', 'string', 'max:255'
            ],
            'source' => [
                'required', 'string', 'max:255'
            ],
            'mobile' => [
                'required', 'string'
            ],
            'address' => [
                'required', 'string', 'max:255'
            ],
            'id_detail_compressor' => [
                'required', 'integer'
            ],
            'name_pic' => [
                'required', 'string', 'max:255'
            ],
            'email_pic' => [
                'required', 'string', 'max:255', 'email'
            ],
            'phone_pic' => [
                'required', 'regex:/^([0-9\s\-\=\(\)]*)$/', 'max:100'
            ],
            'position' => [
                'required', 'string', 'max:255'
            ],
        ];
    }
}
