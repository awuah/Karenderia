<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-credit-card"></i> Payment Method</h4>
                    </div>
                    <div class="card-body">
                        <form id="payment-form" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="amount" value="<?php echo $order['total_amount']; ?>">
                            <input type="hidden" name="email" value="<?php echo $user['email']; ?>">
                            
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h5>Select Payment Method</h5>
                                    <div class="payment-methods">
                                        <!-- PayPal Option -->
                                        <div class="form-check payment-option mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                            <label class="form-check-label d-flex align-items-center" for="paypal">
                                                <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal" class="me-3" style="height: 30px;">
                                                <div>
                                                    <strong>PayPal</strong>
                                                    <small class="d-block text-muted">Pay securely with your PayPal account</small>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <!-- Stripe Option -->
                                        <div class="form-check payment-option mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe">
                                            <label class="form-check-label d-flex align-items-center" for="stripe">
                                                <img src="https://stripe.com/img/v3/home/twitter.png" alt="Stripe" class="me-3" style="height: 30px;">
                                                <div>
                                                    <strong>Credit/Debit Card</strong>
                                                    <small class="d-block text-muted">Visa, Mastercard, American Express</small>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <!-- Paystack Option -->
                                        <div class="form-check payment-option mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" id="paystack" value="paystack">
                                            <label class="form-check-label d-flex align-items-center" for="paystack">
                                                <div class="payment-logo me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 30px; background: #00C3F7; color: white; font-weight: bold; border-radius: 4px;">
                                                    Pay
                                                </div>
                                                <div>
                                                    <strong>Paystack</strong>
                                                    <small class="d-block text-muted">Pay with card, bank transfer, or mobile money</small>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <!-- Cash on Delivery Option -->
                                        <div class="form-check payment-option mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                            <label class="form-check-label d-flex align-items-center" for="cod">
                                                <i class="fas fa-money-bill-wave me-3" style="font-size: 30px; color: #28a745;"></i>
                                                <div>
                                                    <strong>Cash on Delivery</strong>
                                                    <small class="d-block text-muted">Pay when your order arrives</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-lock me-2"></i>
                                    Complete Payment - <?php echo formatCurrency($order['total_amount']); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span><?php echo formatCurrency($order['subtotal']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee:</span>
                            <span><?php echo formatCurrency($order['delivery_fee']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span><?php echo formatCurrency($order['tax_amount']); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span><?php echo formatCurrency($order['total_amount']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            
            if (!selectedMethod) {
                alert('Please select a payment method');
                return;
            }
            
            const method = selectedMethod.value;
            const form = this;
            
            // Set form action based on payment method
            switch(method) {
                case 'paystack':
                    form.action = '/payment/paystack';
                    break;
                case 'paypal':
                    form.action = '/payment/paypal';
                    break;
                case 'stripe':
                    form.action = '/payment/stripe';
                    break;
                case 'cod':
                    form.action = '/payment/cod';
                    break;
                default:
                    alert('Invalid payment method');
                    return;
            }
            
            form.submit();
        });
    </script>
</body>
</html>