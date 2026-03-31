<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransPayoutService
{
    protected $serverKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production', false);
        $this->apiUrl = $isProduction 
            ? 'https://dashboard.midtrans.com/disbursement/v1' 
            : 'https://dashboard.sandbox.midtrans.com/disbursement/v1';
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
            $disbursementApiKey = config('midtrans.disbursement_api_key');
            $isProduction = config('midtrans.disbursement_is_production', false);
            $apiUrl = $isProduction 
                ? 'https://dashboard.midtrans.com/disbursement/v1' 
                : 'https://dashboard.sandbox.midtrans.com/disbursement/v1';

            $response = Http::withBasicAuth($disbursementApiKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl . '/disburse', [
                    'id' => uniqid('DISB-', true), // Unique disbursement ID
                    'bank_code' => $this->getBankCode($data['bank_name']),
                    'account_number' => $data['account_number'],
                    'amount' => (int) $data['amount'],
                    'remark' => $data['notes'] ?? 'Disbursement from Payou.id',
                    'beneficiary_name' => $data['account_name'],
                    'beneficiary_email' => $data['email'] ?? '',
                ]);

            $result = $response->json();

            Log::info('Midtrans Disbursement Response:', $result);

            return [
                'success' => $response->successful(),
                'data' => $result,
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Disbursement Error: ' . $e->getMessage());
            
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
     * @param string $referenceNo
     * @return array
     */
    public function getPayoutStatus(string $referenceNo)
    {
        try {
            $disbursementApiKey = config('midtrans.disbursement_api_key');
            $isProduction = config('midtrans.disbursement_is_production', false);
            $apiUrl = $isProduction 
                ? 'https://dashboard.midtrans.com/disbursement/v1' 
                : 'https://dashboard.sandbox.midtrans.com/disbursement/v1';

            $response = Http::withBasicAuth($disbursementApiKey, '')
                ->get($apiUrl . '/disburse/' . $referenceNo);

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
     * Get list of supported banks
     * 
     * @return array
     */
    public function getBeneficiaryBanks()
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->apiUrl . '/beneficiary_banks');

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error('Get Beneficiary Banks Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
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
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->apiUrl . '/account_validation', [
                    'bank' => $this->getBankCode($bank),
                    'account' => $account,
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
     * Get balance
     * 
     * @return array
     */
    public function getBalance()
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->apiUrl . '/balance');

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error('Get Balance Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Convert bank name to Midtrans bank code
     * 
     * @param string $bankName
     * @return string
     */
    protected function getBankCode(string $bankName): string
    {
        $bankCodes = [
            'BCA' => 'bca',
            'BNI' => 'bni',
            'BRI' => 'bri',
            'MANDIRI' => 'mandiri',
            'CIMB' => 'cimb',
            'PERMATA' => 'permata',
            'BNI SYARIAH' => 'bni_syariah',
            'BSI' => 'bsi',
            'DANAMON' => 'danamon',
            'MEGA' => 'mega',
            'PANIN' => 'panin',
            'MUAMALAT' => 'muamalat',
            'OCBC' => 'ocbc',
            'MAYBANK' => 'maybank',
            'BTPN' => 'btpn',
            'JENIUS' => 'jenius',
            'SINARMAS' => 'sinarmas',
        ];

        $bankNameUpper = strtoupper(trim($bankName));
        
        return $bankCodes[$bankNameUpper] ?? strtolower(str_replace(' ', '_', $bankName));
    }
}
