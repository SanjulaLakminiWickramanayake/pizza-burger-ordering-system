<?php
require_once '../backend/includes/init.php';

require_login('delivery');

$page_title = 'My Deliveries';
$delivery_id = $_SESSION['delivery_id'];
$delivery_obj = new DeliveryStaff($conn);

$assigned_orders = $delivery_obj->getAssignedOrders($delivery_id);
$orders = $assigned_orders['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Pizza & Burger Hub</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="admin-container">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-truck"></i> Delivery Portal
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="deliveries.php" class="active"><i class="fas fa-list"></i> My Deliveries</a></li>
            <li><a href="history.php"><i class="fas fa-history"></i> Delivery History</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>My Deliveries</h1>
        </div>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_address']); ?></td>
                                <td><?php echo ucfirst($order['assignment_status']); ?></td>
                                <td><a href="delivery-detail.php?id=<?php echo $order['id']; ?>" style="color: #FF6B35; text-decoration: none;"><i class="fas fa-eye"></i> View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; color:#666; padding: 30px;">No deliveries assigned.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
