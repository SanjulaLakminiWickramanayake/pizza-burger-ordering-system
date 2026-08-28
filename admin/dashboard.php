<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Admin Dashboard';

$admin_id = $_SESSION['admin_id'];
$admin_obj = new Admin($conn);

$stats = $admin_obj->getDashboardStats();
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
            <li><a href="dashboard.php" class="active"><i class="fas fa-dashboard"></i> Dashboard</a></li>
            <li><a href="manage-foods.php"><i class="fas fa-utensils"></i> Add Foods</a></li>
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
            <h1>Dashboard</h1>
            <div>
                <span style="margin-right: 20px;">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <a href="manage-foods.php" class="btn-primary" style="padding: 8px 16px; text-decoration: none; color: white; margin-right: 10px;"><i class="fas fa-plus"></i> Add New Food</a>
                <a href="logout.php" class="btn-secondary" style="padding: 8px 16px; text-decoration: none; color: white;">Logout</a>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
            </div>
            
            <div class="stat-card" style="border-left-color: #F7931E;">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
            </div>
            
            <div class="stat-card" style="border-left-color: #28a745;">
                <div class="stat-label">Total Sales</div>
                <div class="stat-value">PKR <?php echo number_format($stats['total_sales'], 0); ?></div>
            </div>
            
            <div class="stat-card" style="border-left-color: #17a2b8;">
                <div class="stat-label">Today's Sales</div>
                <div class="stat-value">PKR <?php echo number_format($stats['today_sales'], 0); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">This Month Sales</div>
                <div class="stat-value">PKR <?php echo number_format($stats['month_sales'], 0); ?></div>
                <div class="stat-change">+12% from last month</div>
            </div>
            
            <div class="stat-card" style="border-left-color: #F7931E;">
                <div class="stat-label">Pending Orders</div>
                <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
            </div>
            
            <div class="stat-card" style="border-left-color: #28a745;">
                <div class="stat-label">Delivered Orders</div>
                <div class="stat-value"><?php echo $stats['delivered_orders']; ?></div>
            </div>
            
            <div class="stat-card" style="border-left-color: #dc3545;">
                <div class="stat-label">Cancelled Orders</div>
                <div class="stat-value"><?php echo $stats['cancelled_orders']; ?></div>
            </div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;">Popular Foods</h3>
            
            <?php if (!empty($stats['popular_foods'])): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Food Name</th>
                            <th>Category</th>
                            <th>Total Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['popular_foods'] as $food): ?>
                            <tr>
                                <td><?php echo $food['name']; ?></td>
                                <td><?php echo $food['category_name']; ?></td>
                                <td><?php echo $food['total_sold']; ?> units</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
