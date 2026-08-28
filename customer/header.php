<?php
// Header Template
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Pizza & Burger Hub - Online Food Delivery</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <div class="header-top">
        <div class="container" style="display: flex; justify-content: space-between;">
            <div>
                <a href="tel:+923001234567"><i class="fas fa-phone"></i> +92-300-1234567</a>
                <a href="mailto:info@pizzaburger.com"><i class="fas fa-envelope"></i> info@pizzaburger.com</a>
            </div>
            <div>
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="<?php echo SITE_URL; ?>customer/profile.php">My Profile</a>
                    <a href="<?php echo SITE_URL; ?>customer/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>customer/login.php">Customer Login</a>
                    <a href="<?php echo SITE_URL; ?>admin/login.php">Admin Panel</a>
                    <a href="<?php echo SITE_URL; ?>delivery/login.php">Delivery Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <nav class="navbar">
        <a href="<?php echo SITE_URL; ?>" class="logo">Pizza<span>Burger</span></a>
        
        <ul class="nav-menu">
            <li><a href="<?php echo SITE_URL; ?>customer/index.php">Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>customer/menu.php">Menu</a></li>
            <?php if (isset($_SESSION['customer_id'])): ?>
                <li><a href="<?php echo SITE_URL; ?>customer/dashboard.php">Dashboard</a></li>
            <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>customer/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="user-menu">
            <a href="<?php echo SITE_URL; ?>customer/cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </a>
            <?php if (!isset($_SESSION['customer_id'])): ?>
                <a href="<?php echo SITE_URL; ?>customer/login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main>
