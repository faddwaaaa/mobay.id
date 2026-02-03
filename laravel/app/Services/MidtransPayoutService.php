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
            ? 'https://api.midtrans.com/v2/iris' 
            : 'https://api.sandbox.midtrans.com/v2/iris';
    }

    /**
     * Create payout to bank account
     * 
     * @param array $data
     * @return array
     */
    public function createPayout(array $data)
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/payouts', [
                    'payouts' => [
                        [
                            'beneficiary_name' => $data['account_name'],
                            'beneficiary_account' => $data['account_number'],
                            'beneficiary_bank' => $this->getBankCode($data['bank_name']),
                            'beneficiary_email' => $data['email'] ?? '',
                            'amount' => (int) $data['amount'],
                            'notes' => $data['notes'] ?? 'Withdrawal dari Payou.id',
                        ]
                    ]
                ]);

            $result = $response->json();

            Log::info('Midtrans Payout Response:', $result);

            return [
                'success' => $response->successful(),
                'data' => $result,
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Payout Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Get payout status
     * 
     * @param string $referenceNo
     * @return array
     */
    public function getPayoutStatus(string $referenceNo)
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->apiUrl . '/payouts/' . $referenceNo);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Get Payout Status Error: ' . $e->getMessage());
            
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
