<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $minAmount = config('xendit.withdrawal.min_amount', 20000);
        $maxAmount = config('xendit.withdrawal.max_amount', 50000000);

        return [
            'amount' => [
                'required',
                'integer',
                'min:' . $minAmount,
                'max:' . $maxAmount,
            ],
            'payment_account_id' => ['required', 'integer', 'exists:payment_accounts,id'],
            'bank_code' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $minAmount = config('xendit.withdrawal.min_amount', 20000);
        $maxAmount = config('xendit.withdrawal.max_amount', 50000000);

        return [
            'amount.required' => 'Jumlah penarikan harus diisi.',
            'amount.integer' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Jumlah penarikan minimum adalah Rp ' . number_format($minAmount, 0, ',', '.'),
            'amount.max' => 'Jumlah penarikan maksimum adalah Rp ' . number_format($maxAmount, 0, ',', '.'),
            'payment_account_id.required' => 'Pilih rekening tujuan terlebih dahulu.',
            'payment_account_id.exists' => 'Rekening tujuan tidak valid.',
            'bank_name.string' => 'Nama bank harus berupa teks.',
            'account_name.string' => 'Nama akun harus berupa teks.',
            'account_number.string' => 'Nomor akun harus berupa teks.',
            'notes.string' => 'Catatan harus berupa teks.',
        ];
    }

    /**
     * Get the validated data as an array.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ensure amount is integer
        if (isset($validated['amount'])) {
            $validated['amount'] = (int) $validated['amount'];
        }

        return $validated;
    }
}
