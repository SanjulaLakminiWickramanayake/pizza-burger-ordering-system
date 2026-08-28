<?php
require_once '../backend/includes/init.php';

require_login('delivery');

$page_title = 'Delivery Dashboard';

$delivery_id = $_SESSION['delivery_id'];
$delivery_obj = new DeliveryStaff($conn);

$assigned_orders = $delivery_obj->getAssignedOrders($delivery_id);
$delivery_history = $delivery_obj->getDeliveryHistory($delivery_id, 10);
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
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="deliveries.php"><i class="fas fa-list"></i> My Deliveries</a></li>
            <li><a href="history.php"><i class="fas fa-history"></i> Delivery History</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="admin-header">
            <h1>Delivery Dashboard</h1>
            <div>
                <span style="margin-right: 20px;">Welcome, <?php echo htmlspecialchars($_SESSION['delivery_name']); ?></span>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-label">Assigned Orders</div>
                <div class="stat-value"><?php echo count($assigned_orders['data'] ?? []); ?></div>
            </div>
            
            <div class="stat-card" style="border-left-color: #F7931E;">
                <div class="stat-label">Delivered Today</div>
                <div class="stat-value">0</div>
            </div>
            
            <div class="stat-card" style="border-left-color: #28a745;">
                <div class="stat-label">Total Deliveries</div>
                <div class="stat-value">0</div>
            </div>
            
            <div class="stat-card" style="border-left-color: #17a2b8;">
                <div class="stat-label">Rating</div>
                <div class="stat-value">4.5 ★</div>
            </div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;">Assigned Orders</h3>
            
            <?php if ($assigned_orders['success'] && !empty($assigned_orders['data'])): ?>
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
                        <?php foreach ($assigned_orders['data'] as $order): ?>
                            <tr>
                                <td><strong><?php echo $order['order_number']; ?></strong></td>
                                <td><?php echo $order['customer_name']; ?></td>
                                <td><?php echo $order['customer_phone']; ?></td>
                                <td><?php echo substr($order['delivery_address'], 0, 30); ?>...</td>
                                <td><span class="badge badge-info"><?php echo get_status_label($order['status']); ?></span></td>
                                <td>
                                    <a href="delivery-detail.php?id=<?php echo $order['id']; ?>" style="color: #FF6B35; text-decoration: none;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #666;">No assigned orders at the moment.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
