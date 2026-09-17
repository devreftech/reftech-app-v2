<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:departments,code',
            'parent_id' => 'nullable|integer|exists:departments,id',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama departemen wajib diisi.',
            'code.unique' => 'Kode departemen sudah dipakai.',
            'parent_id.exists' => 'Departemen induk tidak ditemukan.',
        ];
    }
}
