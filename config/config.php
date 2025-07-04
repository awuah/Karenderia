<?php
/**
 * Karenderia Configuration File
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'karenderia');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'Karenderia Multiple Restaurant System');
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@karenderia.com');

// Payment Gateway Configuration
define('PAYPAL_MODE', 'sandbox'); // sandbox or live
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_CLIENT_SECRET', '');

define('STRIPE_PUBLISHABLE_KEY', '');
define('STRIPE_SECRET_KEY', '');

// Paystack Configuration
define('PAYSTACK_MODE', 'test'); // test or live
define('PAYSTACK_TEST_PUBLIC_KEY', 'pk_test_your_test_public_key_here');
define('PAYSTACK_TEST_SECRET_KEY', 'sk_test_your_test_secret_key_here');
define('PAYSTACK_LIVE_PUBLIC_KEY', 'pk_live_your_live_public_key_here');
define('PAYSTACK_LIVE_SECRET_KEY', 'sk_live_your_live_secret_key_here');

// SMS Gateway Configuration
define('TWILIO_SID', '');
define('TWILIO_TOKEN', '');
define('TWILIO_FROM', '');

// Email Configuration
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');

// Google Maps API
define('GOOGLE_MAPS_API_KEY', '');

// Facebook Login
define('FACEBOOK_APP_ID', '');
define('FACEBOOK_APP_SECRET', '');

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', 'uploads/');

// Security Settings
define('ENCRYPTION_KEY', 'your-secret-key-here');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Timezone
date_default_timezone_set('UTC');

// Error Reporting
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>