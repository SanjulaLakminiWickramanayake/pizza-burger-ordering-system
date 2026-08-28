<?php
require_once '../backend/includes/init.php';

require_login('delivery');

$page_title = 'Delivery Details';
$delivery_id = $_SESSION['delivery_id'];
$delivery_obj = new DeliveryStaff($conn);

$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    redirect(SITE_URL . 'delivery/deliveries.php');
}

// Fetch assignment and order info
$result = $conn->prepare(
    "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address, da.status AS assignment_status, da.notes, da.picked_up_at, da.delivered_at
     FROM orders o
     JOIN customers c ON o.customer_id = c.id
     JOIN delivery_assignments da ON o.id = da.order_id
     WHERE o.id = ? AND da.delivery_person_id = ?"
);

if (!$result) {
    redirect(SITE_URL . 'delivery/deliveries.php');
}

$result->bind_param('ii', $order_id, $delivery_id);
$result->execute();
$order_data = $result->get_result()->fetch_assoc();
$result->close();

if (!$order_data) {
    redirect(SITE_URL . 'delivery/deliveries.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';

    $update_result = $delivery_obj->updateAssignmentStatus($order_id, $status);
    if ($update_result['success']) {
        if (!empty($notes)) {
            $delivery_obj->addNotes($order_id, $notes);
        }
        $success = 'Delivery status updated successfully.';

        header('Location: delivery-detail.php?id=' . $order_id);
        exit;
    }
    $error = $update_result['message'] ?? 'Unable to update status.';
}

$order_obj = new Order($conn);
$order_items_result = $order_obj->getOrderItems($order_id);
$order_items = $order_items_result['data'] ?? [];

$status_options = ['assigned' => 'Assigned', 'picked_up' => 'Picked Up', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'];
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
            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>Delivery Detail</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h3>Order Info</h3>
            <div class="row cols-2" style="gap: 20px; margin-top: 15px;">
                <div>
                    <p><strong>Order #:</strong> <?php echo htmlspecialchars($order_data['order_number']); ?></p>
                    <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order_data['delivery_address']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_data['delivery_phone']); ?></p>
                </div>
                <div>
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order_data['customer_name']); ?></p>
                    <p><strong>Assignment Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $order_data['assignment_status'])); ?></p>
                    <p><strong>Payment:</strong> <?php echo ucfirst($order_data['payment_status']); ?></p>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h3>Order Items</h3>
            <table class="table" style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($order_items)): ?>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['food_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>PKR <?php echo number_format($item['unit_price'], 2); ?></td>
                                <td>PKR <?php echo number_format($item['total_price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; color:#666; padding: 20px;">No items found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 700px;">
            <h3>Update Delivery Status</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <?php foreach ($status_options as $value => $label): ?>
                            <option value="<?php echo $value; ?>"<?php echo $order_data['assignment_status'] === $value ? ' selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Delivery Notes</label>
                    <textarea name="notes" style="min-height: 120px;"><?php echo htmlspecialchars($order_data['notes']); ?></textarea>
                </div>
                <button type="submit" class="btn-secondary" style="width: 100%;">Update Status</button>
            </form>
        </div>

        <a href="deliveries.php" class="btn-primary" style="text-decoration: none;">Back to My Deliveries</a>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
