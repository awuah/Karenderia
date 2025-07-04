<?php
/**
 * Paystack Payment Gateway Configuration
 */

class PaystackConfig {
    // Paystack API Configuration
    const TEST_PUBLIC_KEY = 'pk_test_your_test_public_key_here';
    const TEST_SECRET_KEY = 'sk_test_your_test_secret_key_here';
    const LIVE_PUBLIC_KEY = 'pk_live_your_live_public_key_here';
    const LIVE_SECRET_KEY = 'sk_live_your_live_secret_key_here';
    
    // API URLs
    const TEST_BASE_URL = 'https://api.paystack.co';
    const LIVE_BASE_URL = 'https://api.paystack.co';
    
    /**
     * Get public key based on mode
     */
    public static function getPublicKey() {
        return (PAYSTACK_MODE === 'live') ? self::LIVE_PUBLIC_KEY : self::TEST_PUBLIC_KEY;
    }
    
    /**
     * Get secret key based on mode
     */
    public static function getSecretKey() {
        return (PAYSTACK_MODE === 'live') ? self::LIVE_SECRET_KEY : self::TEST_SECRET_KEY;
    }
    
    /**
     * Get base URL based on mode
     */
    public static function getBaseUrl() {
        return (PAYSTACK_MODE === 'live') ? self::LIVE_BASE_URL : self::TEST_BASE_URL;
    }
}
?>