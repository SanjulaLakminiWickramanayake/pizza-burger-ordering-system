<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Manage Customers';
$customer_obj = new Customer($conn);

if (isset($_GET['block'])) {
    $customer_id = intval($_GET['block']);
    $customer_obj->blockCustomer($customer_id);
    header('Location: manage-customers.php');
    exit;
}

if (isset($_GET['unblock'])) {
    $customer_id = intval($_GET['unblock']);
    $customer_obj->unblockCustomer($customer_id);
    header('Location: manage-customers.php');
    exit;
}

$customers_result = $customer_obj->getAll(100, 0);
$customers = $customers_result['data'] ?? [];
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
            <li><a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Manage Orders</a></li>
            <li><a href="manage-customers.php" class="active"><i class="fas fa-users"></i> Manage Customers</a></li>
            <li><a href="manage-delivery.php"><i class="fas fa-truck"></i> Delivery Staff</a></li>
            <li><a href="manage-inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>Manage Customers</h1>
        </div>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Orders</th>
                        <th>Spent</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td>
                                    <span class="badge <?php echo $customer['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo ucfirst($customer['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo intval($customer['total_orders']); ?></td>
                                <td>PKR <?php echo number_format($customer['total_spent'], 2); ?></td>
                                <td>
                                    <?php if ($customer['status'] === 'active'): ?>
                                        <a href="?block=<?php echo $customer['id']; ?>" class="btn-primary" style="background: #dc3545;">Block</a>
                                    <?php else: ?>
                                        <a href="?unblock=<?php echo $customer['id']; ?>" class="btn-secondary">Unblock</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">No customers found.</td>
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
