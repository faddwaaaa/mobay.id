<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditPayoutService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('xendit.disbursement.api_key') ?? config('xendit.api_key');
        $this->apiUrl = config('xendit.api_base_url');
    }

    /**
     * Create disbursement to bank account
     * 
     * @param array $data
     * @return array
     */
    public function createPayout(array $data)
    {
        try {
            $bankCode = strtoupper($this->getBankCode($data['bank_name']));

            $payload = [
                'external_id' => $data['external_id'] ?? uniqid('DISB-', true),
                'amount' => (int) $data['amount'],
                'bank_account_holder_name' => $data['account_name'],
                'bank_code' => $bankCode,
                'bank_account_number' => $data['account_number'],
                'description' => $data['notes'] ?? 'Disbursement from Payou.id',
                'email_to' => $data['email'] ?? '',
            ];

            $response = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/payouts', $payload);

            $result = $response->json();

            Log::info('Xendit Disbursement Created:', [
                'external_id' => $payload['external_id'],
                'amount' => $data['amount'],
                'bank' => $bankCode,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $result,
                'status_code' => $response->status(),
                'reference_no' => $result['id'] ?? null,
                'status' => $result['status'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Xendit Disbursement Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Get disbursement status
     * 
     * @param string $payoutId
     * @return array
     */
    public function getPayoutStatus(string $payoutId)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->apiUrl . '/payouts/' . $payoutId);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Get Disbursement Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * List available banks
     * 
     * @return array
     */
    public function getSupportedBanks()
    {
        return config('xendit.banks', []);
    }

    /**
     * Validate bank account
     * 
     * @param string $bank
     * @param string $account
     * @return array
     */
    public function validateBankAccount(string $bank, string $account)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->apiUrl . '/account_validation', [
                    'bank_code' => strtoupper($this->getBankCode($bank)),
                    'bank_account_number' => $account,
                ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error('Validate Bank Account Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Convert bank name to Xendit bank code
     * 
     * @param string $bankName
     * @return string
     */
    protected function getBankCode(string $bankName): string
    {
        $bankCodes = [
            'BCA' => 'BCA',
            'BNI' => 'BNI',
            'BRI' => 'BRI',
            'MANDIRI' => 'MANDIRI',
            'CIMB' => 'CIMB',
            'PERMATA' => 'PERMATA',
            'BNI SYARIAH' => 'BNI_SYARIAH',
            'BSI' => 'BSI',
            'DANAMON' => 'DANAMON',
            'MEGA' => 'MEGA',
            'PANIN' => 'PANIN_BANK_PANIN',
            'MUAMALAT' => 'BANK_MUAMALAT',
            'OCBC' => 'OCBC',
            'MAYBANK' => 'MAYBANK',
            'SINARMAS' => 'SINARMAS',
        ];

        $bankNameUpper = strtoupper(trim($bankName));
        return $bankCodes[$bankNameUpper] ?? strtoupper(str_replace(' ', '_', $bankName));
    }
}
