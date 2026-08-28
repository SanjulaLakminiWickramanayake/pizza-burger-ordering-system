<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Settings';
$db = new Database($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $value = sanitize($value);
        $existing = $db->selectOne('SELECT id FROM settings WHERE setting_key = ?', [$key]);
        if ($existing['success']) {
            $db->update('settings', ['setting_value' => $value], 'id = ?', [$existing['data']['id']]);
        } else {
            $db->insert('settings', ['setting_key' => $key, 'setting_value' => $value, 'description' => '']);
        }
    }
    $success = 'Settings saved successfully.';
}

$settings_result = $db->select('SELECT * FROM settings ORDER BY setting_key ASC');
$settings = $settings_result['data'] ?? [];
$settings_map = [];
foreach ($settings as $setting) {
    $settings_map[$setting['setting_key']] = $setting['setting_value'];
}

$defaults = [
    'site_name' => SITE_NAME,
    'support_email' => 'info@pizzaburger.com',
    'support_phone' => '+923001234567',
    'delivery_time' => '00:45:00',
    'currency' => CURRENCY
];
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
            <li><a href="manage-delivery.php"><i class="fas fa-truck"></i> Delivery Staff</a></li>
            <li><a href="manage-inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>Settings</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px;">
            <form method="POST">
                <?php foreach ($defaults as $key => $default_value): ?>
                    <div class="form-group">
                        <label><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
                        <input type="text" name="settings[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($settings_map[$key] ?? $default_value); ?>">
                    </div>
                <?php endforeach; ?>

                <button type="submit" name="save_settings" class="btn-secondary" style="width: 100%;">Save Settings</button>
            </form>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
