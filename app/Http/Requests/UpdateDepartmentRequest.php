<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $departmentId = $this->route('department');

        return [
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($departmentId)],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
                Rule::notIn([is_object($departmentId) ? $departmentId->id : $departmentId]),
            ],
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama departemen wajib diisi.',
            'code.unique' => 'Kode departemen sudah dipakai.',
            'parent_id.exists' => 'Departemen induk tidak ditemukan.',
            'parent_id.not_in' => 'Departemen tidak boleh menjadi induk dari dirinya sendiri.',
        ];
    }
}
