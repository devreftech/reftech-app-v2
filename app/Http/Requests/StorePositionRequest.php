<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_department' => 'nullable|integer|exists:departments,id',
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:20',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'id_department.exists' => 'Departemen tidak ditemukan.',
            'name.required' => 'Nama posisi wajib diisi.',
            'level.required' => 'Level wajib diisi.',
            'level.min' => 'Level minimal 1.',
        ];
    }
}
