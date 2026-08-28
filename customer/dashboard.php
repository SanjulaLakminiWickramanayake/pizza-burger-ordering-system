<?php
require_once '../backend/includes/init.php';

require_login('customer');

$page_title = 'Dashboard';

require 'header.php';

$customer_id = $_SESSION['customer_id'];
$customer_obj = new Customer($conn);
$order_obj = new Order($conn);

$customer_info = $customer_obj->getById($customer_id);
$customer = $customer_info['data'] ?? [];

$orders_result = $customer_obj->getOrders($customer_id, 10, 0);
$orders_list = $orders_result['data'] ?? [];
?>

<div class="container" style="margin-top: 30px; margin-bottom: 60px;">
    <h1 style="margin-bottom: 30px;">My Dashboard</h1>
    
    <div class="row cols-4" style="margin-bottom: 40px;">
        <div style="background: white; padding: 20px; border-radius: 5px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 32px; color: #FF6B35; margin-bottom: 10px;"><?php echo $customer['total_orders']; ?></div>
            <div>Total Orders</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 5px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 32px; color: #FF6B35; margin-bottom: 10px;">PKR <?php echo number_format($customer['total_spent'], 2); ?></div>
            <div>Total Spent</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 5px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 32px; color: #FF6B35; margin-bottom: 10px;"><?php echo count($orders_list); ?></div>
            <div>Recent Orders</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 5px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <a href="profile.php" style="color: #FF6B35; text-decoration: none;">
                <div style="font-size: 24px; margin-bottom: 10px;"><i class="fas fa-user"></i></div>
                <div>Profile</div>
            </a>
        </div>
    </div>
    
    <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Recent Orders</h3>
            <a href="menu.php" class="btn-secondary" style="padding: 8px 16px; text-decoration: none; color: white;">Order More</a>
        </div>
        
        <?php if (!empty($orders_list)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders_list as $order): ?>
                        <tr>
                            <td><strong><?php echo $order['order_number']; ?></strong></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td><?php echo $order['item_count']; ?> items</td>
                            <td>PKR <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><span class="badge badge-info"><?php echo get_status_label($order['status']); ?></span></td>
                            <td>
                                <a href="order-tracking.php?id=<?php echo $order['id']; ?>" style="color: #FF6B35; text-decoration: none;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #666;">No orders yet. <a href="menu.php" style="color: #FF6B35;">Start ordering!</a></p>
        <?php endif; ?>
    </div>
</div>

<?php require 'footer.php'; ?>
