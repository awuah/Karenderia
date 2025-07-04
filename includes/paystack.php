<?php
/**
 * Paystack Payment Gateway Class
 */

require_once 'config/paystack.php';

class PaystackGateway {
    private $secret_key;
    private $public_key;
    private $base_url;
    
    public function __construct() {
        $this->secret_key = PaystackConfig::getSecretKey();
        $this->public_key = PaystackConfig::getPublicKey();
        $this->base_url = PaystackConfig::getBaseUrl();
    }
    
    /**
     * Initialize payment transaction
     */
    public function initializePayment($email, $amount, $reference = null, $callback_url = null) {
        $url = $this->base_url . '/transaction/initialize';
        
        $fields = [
            'email' => $email,
            'amount' => $amount * 100, // Convert to kobo
            'reference' => $reference ?: $this->generateReference(),
            'callback_url' => $callback_url ?: SITE_URL . '/payment/paystack/callback'
        ];
        
        $response = $this->makeRequest($url, $fields);
        
        if ($response && $response['status']) {
            return [
                'success' => true,
                'data' => $response['data'],
                'authorization_url' => $response['data']['authorization_url'],
                'access_code' => $response['data']['access_code'],
                'reference' => $response['data']['reference']
            ];
        }
        
        return [
            'success' => false,
            'message' => $response['message'] ?? 'Payment initialization failed'
        ];
    }
    
    /**
     * Verify payment transaction
     */
    public function verifyPayment($reference) {
        $url = $this->base_url . '/transaction/verify/' . $reference;
        
        $response = $this->makeRequest($url, null, 'GET');
        
        if ($response && $response['status']) {
            return [
                'success' => true,
                'data' => $response['data'],
                'status' => $response['data']['status'],
                'amount' => $response['data']['amount'] / 100, // Convert from kobo
                'reference' => $response['data']['reference'],
                'gateway_response' => $response['data']['gateway_response']
            ];
        }
        
        return [
            'success' => false,
            'message' => $response['message'] ?? 'Payment verification failed'
        ];
    }
    
    /**
     * Get transaction details
     */
    public function getTransaction($reference) {
        $url = $this->base_url . '/transaction/' . $reference;
        
        $response = $this->makeRequest($url, null, 'GET');
        
        if ($response && $response['status']) {
            return [
                'success' => true,
                'data' => $response['data']
            ];
        }
        
        return [
            'success' => false,
            'message' => $response['message'] ?? 'Transaction not found'
        ];
    }
    
    /**
     * List transactions
     */
    public function listTransactions($perPage = 50, $page = 1) {
        $url = $this->base_url . '/transaction?perPage=' . $perPage . '&page=' . $page;
        
        $response = $this->makeRequest($url, null, 'GET');
        
        if ($response && $response['status']) {
            return [
                'success' => true,
                'data' => $response['data'],
                'meta' => $response['meta']
            ];
        }
        
        return [
            'success' => false,
            'message' => $response['message'] ?? 'Failed to fetch transactions'
        ];
    }
    
    /**
     * Generate unique reference
     */
    private function generateReference() {
        return 'KAR_' . time() . '_' . uniqid();
    }
    
    /**
     * Make HTTP request to Paystack API
     */
    private function makeRequest($url, $data = null, $method = 'POST') {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->secret_key,
                'Content-Type: application/json',
            ],
        ]);
        
        if ($data && $method === 'POST') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            error_log('Paystack API Error: ' . $err);
            return false;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Get public key for frontend
     */
    public function getPublicKey() {
        return $this->public_key;
    }
}
?>