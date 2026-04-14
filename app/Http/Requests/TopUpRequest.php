<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can request top-up
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $minAmount = config('midtrans.topup.min_amount', 10000);
        $maxAmount = config('midtrans.topup.max_amount', 10000000);

        return [
            'amount' => [
                'required',
                'integer',
                'min:' . $minAmount,
                'max:' . $maxAmount,
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $minAmount = config('midtrans.topup.min_amount', 10000);
        $maxAmount = config('midtrans.topup.max_amount', 10000000);

        return [
            'amount.required' => 'Jumlah top-up harus diisi.',
            'amount.integer' => 'Jumlah top-up harus berupa angka.',
            'amount.min' => 'Jumlah top-up minimum adalah Rp ' . number_format($minAmount, 0, ',', '.'),
            'amount.max' => 'Jumlah top-up maksimum adalah Rp ' . number_format($maxAmount, 0, ',', '.'),
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
