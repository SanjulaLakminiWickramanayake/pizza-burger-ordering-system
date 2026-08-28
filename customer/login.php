<?php
require_once '../backend/includes/init.php';

// Redirect if already logged in
if (is_logged_in('customer')) {
    redirect(SITE_URL . 'customer/dashboard.php');
}

$page_title = 'Customer Login';

require 'header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $customer = new Customer($conn);
        $result = $customer->login($email, $password);
        
        if ($result['success']) {
            $_SESSION['customer_id'] = $result['customer']['id'];
            $_SESSION['customer_name'] = $result['customer']['name'];
            $_SESSION['customer_email'] = $result['customer']['email'];
            $_SESSION['last_activity'] = time();
            
            redirect(SITE_URL . 'customer/dashboard.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="container" style="max-width: 500px; margin: 60px auto;">
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 30px; color: #FF6B35;">Customer Login</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" style="margin-top: 20px;">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-secondary" style="width: 100%; padding: 12px;">Login</button>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <p>Don't have an account? <a href="register.php" style="color: #FF6B35; font-weight: bold;">Register here</a></p>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
