<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $employeeId = $this->route('employee');

        return [
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::unique('employees', 'user_id')->ignore($employeeId),
            ],
            'id_department' => 'nullable|integer|exists:departments,id',
            'id_position' => 'nullable|integer|exists:positions,id',
            'nik' => 'nullable|string|max:30',
            'join_date' => 'nullable|date',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'employment_status' => 'required|in:Tetap,Kontrak,Probation,Resign,Perlu Verifikasi',
            'can_online_attendance' => 'nullable|boolean',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'resign_date' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'User yang dipilih tidak ditemukan.',
            'user_id.unique' => 'User ini sudah punya data karyawan.',
            'id_department.exists' => 'Departemen tidak ditemukan.',
            'id_position.exists' => 'Posisi tidak ditemukan.',
            'employment_status.required' => 'Status kepegawaian wajib diisi.',
            'employment_status.in' => 'Status kepegawaian tidak valid.',
            'contract_end_date.after_or_equal' => 'Tanggal berakhir kontrak tidak boleh sebelum tanggal mulai kontrak.',
        ];
    }
}
