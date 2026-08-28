<?php
require_once '../backend/includes/init.php';

require_login('customer');

$page_title = 'Checkout';

require 'header.php';

$customer_id = $_SESSION['customer_id'];
$cart = new Cart($conn);
$customer_obj = new Customer($conn);

// Get customer info
$customer_info = $customer_obj->getById($customer_id);
$customer = $customer_info['data'] ?? [];

// Get cart info
$cart_data = $cart->getCart($customer_id);
if (!$cart_data['success'] || $cart_data['data']['total_items'] == 0) {
    redirect(SITE_URL . 'customer/cart.php');
}

$cart_info = $cart_data['data'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'delivery_address' => $_POST['delivery_address'] ?? '',
        'delivery_phone' => $_POST['delivery_phone'] ?? '',
        'delivery_notes' => $_POST['delivery_notes'] ?? '',
        'payment_method' => $_POST['payment_method'] ?? 'cash'
    ];
    
    $order = new Order($conn);
    $result = $order->createOrder($customer_id, $data);
    
    if ($result['success']) {
        $_SESSION['order_created'] = true;
        $_SESSION['order_id'] = $result['order_id'];
        redirect(SITE_URL . 'customer/order-tracking.php?id=' . $result['order_id']);
    } else {
        $error = $result['message'];
    }
}
?>

<div class="container" style="margin-top: 30px; margin-bottom: 60px; max-width: 900px;">
    <h1 style="margin-bottom: 30px;">Checkout</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row cols-2" style="gap: 30px;">
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;">Delivery Information</h3>
            
            <form method="POST">
                <div class="form-group">
                    <label for="delivery_address">Delivery Address *</label>
                    <textarea id="delivery_address" name="delivery_address" required><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="delivery_phone">Delivery Phone *</label>
                    <input type="tel" id="delivery_phone" name="delivery_phone" required value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="delivery_notes">Special Instructions (Optional)</label>
                    <textarea id="delivery_notes" name="delivery_notes" placeholder="Any special instructions for delivery?"></textarea>
                </div>
                
                <h3 style="margin: 30px 0 20px;">Payment Method</h3>
                
                <div class="form-group">
                    <input type="radio" id="cash" name="payment_method" value="cash" checked>
                    <label for="cash" style="display: inline; margin-left: 10px;">Cash on Delivery</label>
                </div>
                
                <div class="form-group">
                    <input type="radio" id="bank" name="payment_method" value="bank_transfer">
                    <label for="bank" style="display: inline; margin-left: 10px;">Bank Transfer</label>
                </div>
                
                <div class="form-group">
                    <input type="radio" id="online" name="payment_method" value="online_screenshot">
                    <label for="online" style="display: inline; margin-left: 10px;">Online Payment</label>
                </div>
                
                <button type="submit" class="btn-secondary" style="width: 100%; padding: 12px; margin-top: 20px;">Place Order</button>
            </form>
        </div>
        
        <div>
            <div style="background: #f8f9fa; padding: 25px; border-radius: 5px;">
                <h3 style="margin-bottom: 20px;">Order Summary</h3>
                
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>PKR <?php echo number_format($cart_info['subtotal'], 2); ?></span>
                </div>
                
                <div class="total-row">
                    <span>Tax (5%):</span>
                    <span>PKR <?php echo number_format($cart_info['tax'], 2); ?></span>
                </div>
                
                <div class="total-row">
                    <span>Delivery Charge:</span>
                    <span>
                        <?php if ($cart_info['delivery_charge'] == 0): ?>
                            <span style="color: var(--success-color);">FREE</span>
                        <?php else: ?>
                            PKR <?php echo number_format($cart_info['delivery_charge'], 2); ?>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="total-row" style="border-top: 2px solid var(--border-color); padding-top: 15px; font-size: 18px; font-weight: bold;">
                    <span>Total:</span>
                    <span style="color: var(--primary-color);">PKR <?php echo number_format($cart_info['total'], 2); ?></span>
                </div>
                
                <div style="background: white; padding: 15px; border-radius: 5px; margin-top: 20px; font-size: 13px; color: #666;">
                    <p><strong>Estimated Delivery Time:</strong> 30-45 minutes</p>
                    <p><strong>Free Delivery:</strong> Orders above PKR 2000</p>
                    <p style="margin-top: 10px; color: #999;">By placing your order, you agree to our terms and conditions.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
