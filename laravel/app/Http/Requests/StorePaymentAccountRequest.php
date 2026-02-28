<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'bank_code'      => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9_]+$/'],
            'account_number' => ['required', 'string', 'digits_between:10,16'],
            'account_holder' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[\p{L}\s.\'-]+$/u'],
            'label'          => ['nullable', 'string', 'max:50'],
            'is_default'     => ['nullable', 'boolean'],
            'pin'            => ['required', 'string', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'bank_code.required'      => 'Pilih bank terlebih dahulu.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_number.digits_between' => 'Nomor rekening harus 10–16 digit.',
            'account_holder.required' => 'Nama pemilik rekening wajib diisi.',
            'account_holder.regex'    => 'Nama hanya boleh mengandung huruf dan spasi.',
            'pin.required'            => 'Konfirmasi PIN wajib diisi.',
            'pin.digits'              => 'PIN harus 6 digit angka.',
        ];
    }
}