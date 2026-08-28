<?php
require_once '../backend/includes/init.php';

if (is_logged_in('delivery')) {
    redirect(SITE_URL . 'delivery/dashboard.php');
}

$page_title = 'Delivery Staff Login';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $delivery = new DeliveryStaff($conn);
        $result = $delivery->login($email, $password);
        
        if ($result['success']) {
            $_SESSION['delivery_id'] = $result['staff']['id'];
            $_SESSION['delivery_name'] = $result['staff']['name'];
            $_SESSION['delivery_email'] = $result['staff']['email'];
            $_SESSION['last_activity'] = time();
            
            redirect(SITE_URL . 'delivery/dashboard.php');
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
    <title>Delivery Staff Login - Pizza & Burger Hub</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/style.css">
</head>
<body style="background: linear-gradient(135deg, #FF6B35, #F7931E); min-height: 100vh; display: flex; align-items: center;">

<div class="container" style="max-width: 450px;">
    <div style="background: white; padding: 50px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h2 style="text-align: center; margin-bottom: 30px; color: #FF6B35; font-size: 28px;">
            <i class="fas fa-truck"></i> Delivery Staff Login
        </h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-secondary" style="width: 100%; padding: 12px; font-size: 16px;">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
            <p>Contact admin for credentials</p>
        </div>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
