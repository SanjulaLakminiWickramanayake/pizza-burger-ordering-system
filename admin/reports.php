<?php
require_once '../backend/includes/init.php';

require_login('admin');

$page_title = 'Reports';
$admin_obj = new Admin($conn);
$stats = $admin_obj->getDashboardStats();
$daily_sales = $admin_obj->getDailySales(7);
$monthly_sales = $admin_obj->getMonthlySales(6);
$food_report = $admin_obj->getFoodSalesReport();
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
            <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php" style="color: #dc3545 !important;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="admin-header">
            <h1>Reports</h1>
        </div>

        <div class="dashboard-grid" style="margin-bottom: 30px;">
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
                <div class="stat-value">PKR <?php echo number_format($stats['total_sales'], 2); ?></div>
            </div>
            <div class="stat-card" style="border-left-color: #17a2b8;">
                <div class="stat-label">Delivered Orders</div>
                <div class="stat-value"><?php echo $stats['delivered_orders']; ?></div>
            </div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h3>Daily Sales (Last 7 Days)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sales</th>
                        <th>Orders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($daily_sales['data'])): ?>
                        <?php foreach ($daily_sales['data'] as $day): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($day['date']); ?></td>
                                <td>PKR <?php echo number_format($day['sales'], 2); ?></td>
                                <td><?php echo $day['orders']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center; color:#666;">No sales data available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h3>Monthly Sales (Last 6 Months)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Sales</th>
                        <th>Orders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monthly_sales['data'])): ?>
                        <?php foreach ($monthly_sales['data'] as $month): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($month['month']); ?></td>
                                <td>PKR <?php echo number_format($month['sales'], 2); ?></td>
                                <td><?php echo $month['orders']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center; color:#666;">No monthly data available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Top Selling Foods</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Food Name</th>
                        <th>Category</th>
                        <th>Total Quantity</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($food_report['data'])): ?>
                        <?php foreach ($food_report['data'] as $food): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($food['name']); ?></td>
                                <td><?php echo htmlspecialchars($food['category_name']); ?></td>
                                <td><?php echo $food['total_quantity']; ?></td>
                                <td>PKR <?php echo number_format($food['total_sales'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; color:#666;">No food sales data available.</td></tr>
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
