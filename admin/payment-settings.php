<?php
/**
 * Admin Payment Settings Page
 */

// Check if user is admin
if (!isAdmin()) {
    redirect('/login');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'paystack_mode' => sanitize($_POST['paystack_mode']),
        'paystack_test_public_key' => sanitize($_POST['paystack_test_public_key']),
        'paystack_test_secret_key' => sanitize($_POST['paystack_test_secret_key']),
        'paystack_live_public_key' => sanitize($_POST['paystack_live_public_key']),
        'paystack_live_secret_key' => sanitize($_POST['paystack_live_secret_key']),
        'paystack_enabled' => isset($_POST['paystack_enabled']) ? 1 : 0
    ];
    
    // Update settings in database
    foreach ($settings as $key => $value) {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    
    $_SESSION['success'] = 'Payment settings updated successfully!';
    redirect('/admin/payment-settings');
}

// Get current settings
$stmt = $db->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'paystack_%'");
$stmt->execute();
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Settings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 bg-dark text-white p-0">
                <div class="p-3">
                    <h5>Admin Panel</h5>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link text-white" href="/admin">Dashboard</a>
                    <a class="nav-link text-white" href="/admin/restaurants">Restaurants</a>
                    <a class="nav-link text-white" href="/admin/orders">Orders</a>
                    <a class="nav-link text-white active" href="/admin/payment-settings">Payment Settings</a>
                    <a class="nav-link text-white" href="/admin/users">Users</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h2><i class="fas fa-credit-card"></i> Payment Gateway Settings</h2>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <form method="POST">
                                <!-- Paystack Settings -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5><i class="fas fa-credit-card"></i> Paystack Configuration</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="paystack_enabled" id="paystack_enabled" 
                                                   <?php echo ($settings['paystack_enabled'] ?? 0) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="paystack_enabled">
                                                <strong>Enable Paystack</strong>
                                            </label>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="paystack_mode" class="form-label">Mode</label>
                                            <select class="form-select" name="paystack_mode" id="paystack_mode">
                                                <option value="test" <?php echo ($settings['paystack_mode'] ?? 'test') === 'test' ? 'selected' : ''; ?>>Test Mode</option>
                                                <option value="live" <?php echo ($settings['paystack_mode'] ?? 'test') === 'live' ? 'selected' : ''; ?>>Live Mode</option>
                                            </select>
                                        </div>
                                        
                                        <h6>Test Keys</h6>
                                        <div class="mb-3">
                                            <label for="paystack_test_public_key" class="form-label">Test Public Key</label>
                                            <input type="text" class="form-control" name="paystack_test_public_key" 
                                                   value="<?php echo htmlspecialchars($settings['paystack_test_public_key'] ?? ''); ?>"
                                                   placeholder="pk_test_...">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="paystack_test_secret_key" class="form-label">Test Secret Key</label>
                                            <input type="password" class="form-control" name="paystack_test_secret_key" 
                                                   value="<?php echo htmlspecialchars($settings['paystack_test_secret_key'] ?? ''); ?>"
                                                   placeholder="sk_test_...">
                                        </div>
                                        
                                        <h6>Live Keys</h6>
                                        <div class="mb-3">
                                            <label for="paystack_live_public_key" class="form-label">Live Public Key</label>
                                            <input type="text" class="form-control" name="paystack_live_public_key" 
                                                   value="<?php echo htmlspecialchars($settings['paystack_live_public_key'] ?? ''); ?>"
                                                   placeholder="pk_live_...">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="paystack_live_secret_key" class="form-label">Live Secret Key</label>
                                            <input type="password" class="form-control" name="paystack_live_secret_key" 
                                                   value="<?php echo htmlspecialchars($settings['paystack_live_secret_key'] ?? ''); ?>"
                                                   placeholder="sk_live_...">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                            </form>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Setup Instructions</h6>
                                </div>
                                <div class="card-body">
                                    <ol class="small">
                                        <li>Create a Paystack account at <a href="https://paystack.com" target="_blank">paystack.com</a></li>
                                        <li>Go to Settings → API Keys & Webhooks</li>
                                        <li>Copy your Test/Live keys</li>
                                        <li>Set up webhook URL: <code><?php echo SITE_URL; ?>/payment/paystack/webhook</code></li>
                                        <li>Enable the events: <code>charge.success</code>, <code>charge.failed</code></li>
                                    </ol>
                                    
                                    <div class="alert alert-info small mt-3">
                                        <strong>Note:</strong> Always test with test keys before going live.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>