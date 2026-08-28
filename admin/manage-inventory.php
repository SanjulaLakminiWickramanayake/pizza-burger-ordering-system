<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Inventory Management';
$db = new Database($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $item_data = [
        'item_name' => sanitize($_POST['item_name'] ?? ''),
        'quantity' => floatval($_POST['quantity'] ?? 0),
        'unit' => sanitize($_POST['unit'] ?? ''),
        'reorder_level' => floatval($_POST['reorder_level'] ?? 0),
        'supplier' => sanitize($_POST['supplier'] ?? '')
    ];

    $result = $db->insert('inventory', $item_data);
    if ($result['success']) {
        $success = 'Inventory item added successfully.';
    } else {
        $error = $result['message'];
    }
}

if (isset($_GET['delete'])) {
    $inventory_id = intval($_GET['delete']);
    $db->delete('inventory', 'id = ?', [$inventory_id]);
    header('Location: manage-inventory.php');
    exit;
}

$inventory_result = $db->select('SELECT * FROM inventory ORDER BY last_updated DESC');
$inventory_items = $inventory_result['data'] ?? [];
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
            <li><a href="manage-inventory.php" class="active"><i class="fas fa-boxes"></i> Inventory</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>Inventory Management</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row cols-3" style="gap: 30px; margin-bottom: 40px;">
            <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px; color: #FF6B35;">Add Inventory Item</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="item_name" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" name="unit" required>
                    </div>
                    <div class="form-group">
                        <label>Reorder Level</label>
                        <input type="number" name="reorder_level" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <input type="text" name="supplier">
                    </div>
                    <button type="submit" name="add_item" class="btn-secondary" style="width: 100%;">Add Item</button>
                </form>
            </div>

            <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); grid-column: span 2; overflow-x: auto;">
                <h3 style="margin-bottom: 20px;">Inventory Items</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Reorder Level</th>
                            <th>Supplier</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inventory_items)): ?>
                            <?php foreach ($inventory_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                    <td><?php echo $item['reorder_level']; ?></td>
                                    <td><?php echo htmlspecialchars($item['supplier']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($item['last_updated'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete this item?');" style="color: #dc3545;">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px; color: #666;">No inventory items found.</td>
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
