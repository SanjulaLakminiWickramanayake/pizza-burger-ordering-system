<?php
require_once '../backend/includes/init.php';

if (is_logged_in('admin')) {
    redirect(SITE_URL . 'admin/dashboard.php');
}

$page_title = 'Admin Login';

// Don't use header.php for admin - create simple HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Pizza & Burger Hub</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/style.css">
</head>
<body style="background: linear-gradient(135deg, #FF6B35, #F7931E); min-height: 100vh; display: flex; align-items: center;">

<div class="container" style="max-width: 450px;">
    <div style="background: white; padding: 50px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h2 style="text-align: center; margin-bottom: 30px; color: #FF6B35; font-size: 28px;">Admin Panel</h2>
        
        <?php
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Email and password are required';
            } else {
                $admin = new Admin($conn);
                $result = $admin->login($email, $password);
                
                if ($result['success']) {
                    $_SESSION['admin_id'] = $result['admin']['id'];
                    $_SESSION['admin_name'] = $result['admin']['name'];
                    $_SESSION['admin_email'] = $result['admin']['email'];
                    $_SESSION['admin_role'] = $result['admin']['role'];
                    $_SESSION['last_activity'] = time();
                    
                    redirect(SITE_URL . 'admin/dashboard.php');
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        if ($error) {
            echo '<div class="alert alert-danger" style="margin-bottom: 20px;">' . $error . '</div>';
        }
        ?>
        
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
            <p>Default Admin Email: admin@fooddelivery.com</p>
            <p>Default Password: Check database.sql</p>
        </div>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>js/cart.js"></script>
<script src="<?php echo SITE_URL; ?>js/forms.js"></script>
</body>
</html>
