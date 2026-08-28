<?php
require_once '../backend/includes/init.php';

require_login('delivery');

$page_title = 'Delivery Profile';
$delivery_id = $_SESSION['delivery_id'];
$delivery_obj = new DeliveryStaff($conn);
$delivery_info = $delivery_obj->getById($delivery_id);
$delivery = $delivery_info['data'] ?? [];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $vehicle_type = sanitize($_POST['vehicle_type'] ?? '');
    $vehicle_plate = sanitize($_POST['vehicle_plate'] ?? '');

    $result = $delivery_obj->update($delivery_id, [
        'name' => $name,
        'phone' => $phone,
        'address' => $address,
        'vehicle_type' => $vehicle_type,
        'vehicle_plate' => $vehicle_plate,
        'status' => $delivery['status'] ?? 'active'
    ]);
    if ($result['success']) {
        $success = 'Profile updated successfully.';
        $delivery_info = $delivery_obj->getById($delivery_id);
        $delivery = $delivery_info['data'] ?? [];
    } else {
        $error = $result['message'];
    }
}
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
            <li><a href="deliveries.php"><i class="fas fa-list"></i> My Deliveries</a></li>
            <li><a href="history.php"><i class="fas fa-history"></i> Delivery History</a></li>
            <li><a href="profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>My Profile</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 700px;">
            <form method="POST">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($delivery['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($delivery['email'] ?? ''); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($delivery['phone'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" style="min-height: 80px;"><?php echo htmlspecialchars($delivery['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Vehicle Type</label>
                    <input type="text" name="vehicle_type" value="<?php echo htmlspecialchars($delivery['vehicle_type'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Vehicle Plate</label>
                    <input type="text" name="vehicle_plate" value="<?php echo htmlspecialchars($delivery['vehicle_plate'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn-secondary" style="width: 100%;">Update Profile</button>
            </form>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
