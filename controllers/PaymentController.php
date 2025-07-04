<?php
/**
 * Payment Controller
 */

require_once 'includes/paystack.php';

class PaymentController {
    
    /**
     * Process Paystack payment
     */
    public function paystack() {
        global $db;
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cart');
            return;
        }
        
        if (!isLoggedIn()) {
            redirect('/login');
            return;
        }
        
        $order_id = sanitize($_POST['order_id']);
        $amount = sanitize($_POST['amount']);
        $email = sanitize($_POST['email']);
        
        // Get order details
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$order_id, $_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            redirect('/cart');
            return;
        }
        
        $paystack = new PaystackGateway();
        $reference = 'KAR_' . $order_id . '_' . time();
        
        $result = $paystack->initializePayment(
            $email,
            $amount,
            $reference,
            SITE_URL . '/payment/paystack/callback'
        );
        
        if ($result['success']) {
            // Store payment reference in database
            $stmt = $db->prepare("INSERT INTO payment_transactions (order_id, transaction_id, payment_method, amount, status) VALUES (?, ?, 'paystack', ?, 'pending')");
            $stmt->execute([$order_id, $reference, $amount]);
            
            // Redirect to Paystack payment page
            redirect($result['authorization_url']);
        } else {
            $_SESSION['error'] = $result['message'];
            redirect('/checkout');
        }
    }
    
    /**
     * Handle Paystack callback
     */
    public function paystackCallback() {
        global $db;
        
        $reference = sanitize($_GET['reference']);
        
        if (!$reference) {
            $_SESSION['error'] = 'Invalid payment reference';
            redirect('/cart');
            return;
        }
        
        $paystack = new PaystackGateway();
        $result = $paystack->verifyPayment($reference);
        
        if ($result['success'] && $result['status'] === 'success') {
            // Update payment transaction
            $stmt = $db->prepare("UPDATE payment_transactions SET status = 'completed', gateway_response = ? WHERE transaction_id = ?");
            $stmt->execute([json_encode($result['data']), $reference]);
            
            // Get order ID from transaction
            $stmt = $db->prepare("SELECT order_id FROM payment_transactions WHERE transaction_id = ?");
            $stmt->execute([$reference]);
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($transaction) {
                // Update order status
                $stmt = $db->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed' WHERE id = ?");
                $stmt->execute([$transaction['order_id']]);
                
                $_SESSION['success'] = 'Payment successful! Your order has been confirmed.';
                redirect('/order/success/' . $transaction['order_id']);
            } else {
                $_SESSION['error'] = 'Order not found';
                redirect('/cart');
            }
        } else {
            // Payment failed
            $stmt = $db->prepare("UPDATE payment_transactions SET status = 'failed', gateway_response = ? WHERE transaction_id = ?");
            $stmt->execute([json_encode($result), $reference]);
            
            $_SESSION['error'] = 'Payment failed: ' . ($result['message'] ?? 'Unknown error');
            redirect('/checkout');
        }
    }
    
    /**
     * Handle Paystack webhook
     */
    public function paystackWebhook() {
        global $db;
        
        // Verify webhook signature
        $input = @file_get_contents("php://input");
        $event = json_decode($input, true);
        
        if (!$event) {
            http_response_code(400);
            exit('Invalid JSON');
        }
        
        // Verify the event is from Paystack
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
        $computed_signature = hash_hmac('sha512', $input, PaystackConfig::getSecretKey());
        
        if (!hash_equals($signature, $computed_signature)) {
            http_response_code(400);
            exit('Invalid signature');
        }
        
        // Handle different event types
        switch ($event['event']) {
            case 'charge.success':
                $this->handleChargeSuccess($event['data']);
                break;
            case 'charge.failed':
                $this->handleChargeFailed($event['data']);
                break;
            default:
                // Log unknown event
                error_log('Unknown Paystack event: ' . $event['event']);
        }
        
        http_response_code(200);
        exit('OK');
    }
    
    /**
     * Handle successful charge webhook
     */
    private function handleChargeSuccess($data) {
        global $db;
        
        $reference = $data['reference'];
        
        // Update payment transaction
        $stmt = $db->prepare("UPDATE payment_transactions SET status = 'completed', gateway_response = ? WHERE transaction_id = ?");
        $stmt->execute([json_encode($data), $reference]);
        
        // Get order ID
        $stmt = $db->prepare("SELECT order_id FROM payment_transactions WHERE transaction_id = ?");
        $stmt->execute([$reference]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($transaction) {
            // Update order status
            $stmt = $db->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed' WHERE id = ?");
            $stmt->execute([$transaction['order_id']]);
            
            // Send confirmation email/SMS
            $this->sendOrderConfirmation($transaction['order_id']);
        }
    }
    
    /**
     * Handle failed charge webhook
     */
    private function handleChargeFailed($data) {
        global $db;
        
        $reference = $data['reference'];
        
        // Update payment transaction
        $stmt = $db->prepare("UPDATE payment_transactions SET status = 'failed', gateway_response = ? WHERE transaction_id = ?");
        $stmt->execute([json_encode($data), $reference]);
    }
    
    /**
     * Send order confirmation
     */
    private function sendOrderConfirmation($order_id) {
        // Implementation for sending confirmation email/SMS
        // This would integrate with your existing notification system
    }
    
    /**
     * Load view
     */
    private function loadView($view, $data = array()) {
        extract($data);
        include 'views/' . $view . '.php';
    }
}
?>