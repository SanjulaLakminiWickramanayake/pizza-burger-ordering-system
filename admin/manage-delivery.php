<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Delivery Staff';
$delivery_obj = new DeliveryStaff($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $data = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'password' => $_POST['password'] ?? '',
        'address' => $_POST['address'] ?? '',
        'vehicle_type' => $_POST['vehicle_type'] ?? '',
        'vehicle_plate' => $_POST['vehicle_plate'] ?? ''
    ];

    $result = $delivery_obj->create($data);
    if ($result['success']) {
        $success = 'Delivery staff member added successfully.';
    } else {
        $error = $result['message'];
    }
}

$staff_result = $delivery_obj->getAll(100, 0);
$staff_list = $staff_result['data'] ?? [];
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
            <li><a href="manage-customers.php"><i class="fas fa-users"></i> Manage Customers</a></li>
            <li><a href="manage-delivery.php" class="active"><i class="fas fa-truck"></i> Delivery Staff</a></li>
            <li><a href="manage-inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>Delivery Staff</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row cols-3" style="gap: 30px; margin-bottom: 40px;">
            <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px; color: #FF6B35;">Add Delivery Staff</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle Type</label>
                        <input type="text" name="vehicle_type">
                    </div>
                    <div class="form-group">
                        <label>Vehicle Plate</label>
                        <input type="text" name="vehicle_plate">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" style="min-height: 80px;"></textarea>
                    </div>
                    <button type="submit" name="add_staff" class="btn-secondary" style="width: 100%;">Add Staff</button>
                </form>
            </div>

            <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); grid-column: span 2; overflow-x: auto;">
                <h3 style="margin-bottom: 20px;">Delivery Staff Members</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Vehicles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($staff_list)): ?>
                            <?php foreach ($staff_list as $staff): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $staff['status'] === 'active' ? 'badge-success' : 'badge-warning'; ?>">
                                            <?php echo ucfirst($staff['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($staff['vehicle_type'] . ' / ' . $staff['vehicle_plate']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No delivery staff found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
