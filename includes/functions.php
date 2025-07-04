<?php
/**
 * Common Functions for Karenderia
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Check if user is merchant
 */
function isMerchant() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'merchant';
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = 'USD') {
    return '$' . number_format($amount, 2);
}

/**
 * Calculate distance between two coordinates
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $distance = $earth_radius * $c;

    return $distance;
}

/**
 * Send email notification
 */
function sendEmail($to, $subject, $message) {
    // Email sending logic here
    // You can use PHPMailer or similar library
    return mail($to, $subject, $message);
}

/**
 * Upload file
 */
function uploadFile($file, $destination) {
    $target_dir = UPLOAD_PATH . $destination . '/';
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        return array('success' => false, 'message' => 'File already exists');
    }

    // Check file size
    if ($file["size"] > MAX_FILE_SIZE) {
        return array('success' => false, 'message' => 'File is too large');
    }

    // Allow certain file formats
    $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx");
    if (!in_array($imageFileType, $allowed_types)) {
        return array('success' => false, 'message' => 'File type not allowed');
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('success' => true, 'filename' => basename($file["name"]));
    } else {
        return array('success' => false, 'message' => 'Upload failed');
    }
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
?>