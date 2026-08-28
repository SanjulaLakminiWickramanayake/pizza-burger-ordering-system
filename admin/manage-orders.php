<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Manage Orders';
$order_obj = new Order($conn);
$status_filter = $_GET['status'] ?? 'all';
$search = sanitize($_GET['search'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'] ?? 'pending';
    $result = $order_obj->updateStatus($order_id, $status);
    if ($result['success']) {
        header('Location: manage-orders.php');
        exit;
    }
}

if ($search) {
    $orders_result = $order_obj->search($search, 100, 0);
} elseif ($status_filter === 'all') {
    $orders_result = $order_obj->getAll(100, 0);
} else {
    $orders_result = $order_obj->getByStatus($status_filter, 100, 0);
}

$orders = $orders_result['data'] ?? [];
$status_options = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'];
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
            <i class="fas fa-pizza-slice"></i> PizzaBurger
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
            <li><a href="menu.php"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="manage-orders.php" class="active"><i class="fas fa-shopping-bag"></i> Manage Orders</a></li>
            <li><a href="manage-customers.php"><i class="fas fa-users"></i> Manage Customers</a></li>
            <li><a href="manage-delivery.php"><i class="fas fa-truck"></i> Delivery Staff</a></li>
            <li><a href="manage-inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>Manage Orders</h1>
        </div>

        <div style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
            <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <select name="status" style="padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="all"<?php echo $status_filter === 'all' ? ' selected' : ''; ?>>All Statuses</option>
                    <?php foreach ($status_options as $status): ?>
                        <option value="<?php echo $status; ?>"<?php echo $status_filter === $status ? ' selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $status)); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="search" placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                <button type="submit" class="btn-secondary">Filter</button>
            </form>
        </div>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Placed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['order_number']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                                <td>PKR <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo get_status_label($order['status']); ?></td>
                                <td><?php echo ucfirst($order['payment_status']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" style="padding: 6px 8px; border-radius: 4px; border: 1px solid #ddd;">
                                            <?php foreach ($status_options as $status): ?>
                                                <option value="<?php echo $status; ?>"<?php echo $order['status'] === $status ? ' selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $status)); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-primary" style="padding: 8px 10px;">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">No orders found.</td>
                        </tr>
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
