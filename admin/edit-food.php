<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Edit Food Item';
$food_obj = new Food($conn);
$categories = $food_obj->getCategories();
$error = '';
$success = '';

$food_id = intval($_GET['id'] ?? 0);
$food_item = null;

if ($food_id) {
    $result = $food_obj->getById($food_id);
    if ($result['success']) {
        $food_item = $result['data'];
    }
}

if (!$food_item) {
    header('Location: manage-foods.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category_id' => $_POST['category_id'] ?? 0,
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'price' => $_POST['price'] ?? 0,
        'discount_percentage' => $_POST['discount_percentage'] ?? 0,
        'stock' => $_POST['stock'] ?? 0,
        'status' => $_POST['status'] ?? 'active'
    ];

    if (!empty($_FILES['image']['name'])) {
        $upload_result = upload_image($_FILES['image'], UPLOAD_DIR . 'food_images/');
        if ($upload_result['success']) {
            $data['image_path'] = $upload_result['filename'];
        } else {
            $error = $upload_result['message'];
        }
    }

    if (!$error) {
        $result = $food_obj->update($food_id, $data);
        if ($result['success']) {
            $success = 'Food item updated successfully.';
            $food_item = $food_obj->getById($food_id)['data'];
        } else {
            $error = $result['message'];
        }
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
            <i class="fas fa-pizza-slice"></i> PizzaBurger
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
            <li><a href="manage-foods.php" class="active"><i class="fas fa-utensils"></i> Manage Foods</a></li>
            <li><a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Manage Orders</a></li>
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
            <h1>Edit Food Item</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin-bottom: 40px;">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">Select category</option>
                        <?php foreach ($categories['data'] as $category): ?>
                            <option value="<?php echo $category['id']; ?>"<?php echo $food_item['category_id'] == $category['id'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($food_item['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" style="min-height: 100px;"><?php echo htmlspecialchars($food_item['description']); ?></textarea>
                </div>

                <div class="row cols-2">
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($food_item['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Discount %</label>
                        <input type="number" name="discount_percentage" step="0.01" value="<?php echo htmlspecialchars($food_item['discount_percentage']); ?>">
                    </div>
                </div>

                <div class="row cols-2">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?php echo htmlspecialchars($food_item['stock']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="active"<?php echo $food_item['status'] === 'active' ? ' selected' : ''; ?>>Active</option>
                            <option value="inactive"<?php echo $food_item['status'] === 'inactive' ? ' selected' : ''; ?>>Inactive</option>
                            <option value="out_of_stock"<?php echo $food_item['status'] === 'out_of_stock' ? ' selected' : ''; ?>>Out of Stock</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Image</label>
                    <?php if (!empty($food_item['image_path'])): ?>
                                <img src="<?php echo food_image_url($food_item['image_path']); ?>" alt="Image" style="max-width: 150px; display: block; margin-bottom: 10px;">
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn-secondary" style="width: 100%;">Save Changes</button>
            </form>
        </div>

        <a href="manage-foods.php" class="btn-primary" style="text-decoration: none;">Back to Manage Foods</a>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
