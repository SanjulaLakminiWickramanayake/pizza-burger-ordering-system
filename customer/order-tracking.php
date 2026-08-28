<?php
require_once '../backend/includes/init.php';

require_login('customer');

$page_title = 'Order Tracking';

require 'header.php';

$order_id = $_GET['id'] ?? 0;

if (!$order_id) {
    redirect(SITE_URL . 'customer/dashboard.php');
}

$order_obj = new Order($conn);
$order_result = $order_obj->getOrder($order_id);
$items_result = $order_obj->getOrderItems($order_id);

if (!$order_result['success']) {
    redirect(SITE_URL . 'customer/dashboard.php');
}

$order = $order_result['data'];
$items = $items_result['data'] ?? [];

// Status mapping
$statuses = ['pending' => 0, 'confirmed' => 1, 'preparing' => 2, 'ready' => 3, 'out_for_delivery' => 4, 'delivered' => 5];
$current_step = $statuses[$order['status']] ?? 0;
?>

<div class="container" style="margin-top: 30px; margin-bottom: 60px;">
    <h1 style="margin-bottom: 30px;">Order Tracking</h1>
    
    <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="margin: 0 0 5px 0;">Order #<?php echo $order['order_number']; ?></h2>
                <p style="color: #666; margin: 0;">Placed on <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
            </div>
            <div style="text-align: right;">
                <span class="badge badge-primary" style="padding: 8px 15px; font-size: 14px;"><?php echo get_status_label($order['status']); ?></span>
            </div>
        </div>
    </div>
    
    <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 40px;">
        <h3 style="margin-bottom: 30px;">Order Status</h3>
        
        <div class="order-status">
            <?php
            $status_labels = [
                'pending' => 'Order Received',
                'confirmed' => 'Confirmed',
                'preparing' => 'Preparing',
                'ready' => 'Ready',
                'out_for_delivery' => 'Out for Delivery',
                'delivered' => 'Delivered'
            ];
            
            $all_statuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered'];
            
            foreach ($all_statuses as $index => $status):
            ?>
                <div class="status-step <?php echo $current_step >= $statuses[$status] ? 'completed' : ''; ?> <?php echo $order['status'] === $status ? 'active' : ''; ?>">
                    <div class="status-circle"><?php echo $index + 1; ?></div>
                    <div class="status-label"><?php echo $status_labels[$status]; ?></div>
                    <?php if ($index < count($all_statuses) - 1): ?>
                        <div class="status-line"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="row cols-2" style="gap: 30px; margin-bottom: 40px;">
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;">Order Details</h3>
            
            <div style="margin-bottom: 15px;">
                <strong>Delivery Address:</strong>
                <p style="color: #666; margin: 5px 0 0 0;"><?php echo htmlspecialchars($order['delivery_address']); ?></p>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Delivery Phone:</strong>
                <p style="color: #666; margin: 5px 0 0 0;"><?php echo htmlspecialchars($order['delivery_phone']); ?></p>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Payment Method:</strong>
                <p style="color: #666; margin: 5px 0 0 0;"><?php echo get_payment_method_label($order['payment_method']); ?></p>
            </div>
            
            <div>
                <strong>Payment Status:</strong>
                <p style="margin: 5px 0 0 0;">
                    <span class="badge <?php echo $order['payment_status'] === 'paid' ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo ucfirst($order['payment_status']); ?>
                    </span>
                </p>
            </div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;">Order Summary</h3>
            
            <div class="total-row" style="margin-bottom: 15px;">
                <span>Subtotal:</span>
                <span>PKR <?php echo number_format($order['subtotal'], 2); ?></span>
            </div>
            
            <div class="total-row" style="margin-bottom: 15px;">
                <span>Tax (5%):</span>
                <span>PKR <?php echo number_format($order['tax'], 2); ?></span>
            </div>
            
            <div class="total-row" style="margin-bottom: 15px;">
                <span>Delivery Charge:</span>
                <span><?php echo $order['delivery_charge'] == 0 ? '<span style="color: var(--success-color);">FREE</span>' : 'PKR ' . number_format($order['delivery_charge'], 2); ?></span>
            </div>
            
            <div class="total-row" style="border-top: 2px solid var(--border-color); padding-top: 15px; font-size: 18px; font-weight: bold;">
                <span>Total:</span>
                <span style="color: var(--primary-color);">PKR <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
    </div>
    
    <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">Order Items</h3>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['food_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>PKR <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td>PKR <?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'footer.php'; ?>
