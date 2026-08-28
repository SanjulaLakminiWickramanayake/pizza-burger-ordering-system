<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Manage Foods';

$food_obj = new Food($conn);
$categories = $food_obj->getCategories();
$foods_result = $food_obj->getAll(50, 0);
$foods = $foods_result['data'] ?? [];

$error = '';
$success = '';

// Handle Add Food
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_food'])) {
    $data = [
        'category_id' => $_POST['category_id'] ?? 0,
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'price' => $_POST['price'] ?? 0,
        'discount_percentage' => $_POST['discount_percentage'] ?? 0,
        'stock' => $_POST['stock'] ?? 0
    ];
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $upload_result = upload_image($_FILES['image'], UPLOAD_DIR . 'food_images/');
        if ($upload_result['success']) {
            $data['image_path'] = $upload_result['filename'];
        }
    }
    
    $result = $food_obj->create($data);
    if ($result['success']) {
        $success = 'Food item added successfully!';
        header('Refresh: 2');
    } else {
        $error = 'Failed to add food item';
    }
}

// Handle Delete Food
if (isset($_GET['delete'])) {
    $food_id = intval($_GET['delete']);
    $result = $food_obj->delete($food_id);
    if ($result['success']) {
        header('Location: manage-foods.php');
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
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="admin-header">
            <h1>Manage Foods</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="row cols-3" style="margin-bottom: 40px; gap: 30px;">
            <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px; color: #FF6B35;">Add New Food Item</h3>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php if ($categories['success']): ?>
                                <?php foreach ($categories['data'] as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="name">Food Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" style="height: 80px;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (PKR) *</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="discount_percentage">Discount %</label>
                        <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Food Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    
                    <button type="submit" name="add_food" class="btn-secondary" style="width: 100%; padding: 12px;">
                        <i class="fas fa-plus"></i> Add Food Item
                    </button>
                </form>
            </div>
            
            <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); grid-column: span 2;">
                <h3 style="margin-bottom: 20px;">Food Items List</h3>
                
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($foods as $food): ?>
                                <tr>
                                    <td><?php echo $food['name']; ?></td>
                                    <td><?php echo $food['category_name']; ?></td>
                                    <td>PKR <?php echo $food['price']; ?></td>
                                    <td><?php echo $food['discount_percentage']; ?>%</td>
                                    <td><?php echo $food['stock']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $food['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo ucfirst($food['status']); ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 12px;">
                                        <a href="edit-food.php?id=<?php echo $food['id']; ?>" style="color: #FF6B35; margin-right: 10px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $food['id']; ?>" onclick="return confirm('Delete this item?')" style="color: #dc3545;">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
