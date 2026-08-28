<?php
require_once '../backend/includes/init.php';

$page_title = 'Customer Registration';

require 'header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? ''
    ];
    
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = 'Passwords do not match';
    } else {
        $customer = new Customer($conn);
        $result = $customer->register($data);
        
        if ($result['success']) {
            $success = 'Registration successful! Redirecting to login...';
            $_SESSION['registration_success'] = true;
            header('Refresh: 2; url=login.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="container" style="max-width: 600px; margin: 40px auto;">
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 30px; color: #FF6B35;">Create Your Account</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row cols-2">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="address">Street Address</label>
                <input type="text" id="address" name="address">
            </div>
            
            <div class="row cols-2">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city">
                </div>
                
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code">
                </div>
            </div>
            
            <div class="row cols-2">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-secondary" style="width: 100%; padding: 12px;">Register</button>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <p>Already have an account? <a href="login.php" style="color: #FF6B35; font-weight: bold;">Login here</a></p>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
