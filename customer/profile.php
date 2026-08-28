<?php
require_once '../backend/includes/init.php';

require_login('customer');

$page_title = 'My Profile';

require 'header.php';

$customer_id = $_SESSION['customer_id'];
$customer_obj = new Customer($conn);
$customer_info = $customer_obj->getById($customer_id);
$customer = $customer_info['data'] ?? [];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $data = [
            'name' => $_POST['name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'city' => $_POST['city'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? ''
        ];
        
        $result = $customer_obj->updateProfile($customer_id, $data);
        if ($result['success']) {
            $success = 'Profile updated successfully!';
            $customer = $customer_obj->getById($customer_id)['data'];
        } else {
            $error = $result['message'];
        }
    } elseif (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $result = $customer_obj->changePassword($customer_id, $old_password, $new_password, $confirm_password);
        if ($result['success']) {
            $success = 'Password changed successfully!';
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="container" style="margin-top: 30px; margin-bottom: 60px; max-width: 900px;">
    <h1 style="margin-bottom: 30px;">My Profile</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="row cols-2" style="gap: 30px;">
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-user"></i> Personal Information</h3>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Street Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>">
                </div>
                
                <div class="row cols-2">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($customer['city'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($customer['postal_code'] ?? ''); ?>">
                    </div>
                </div>
                
                <button type="submit" name="update_profile" class="btn-secondary" style="width: 100%; padding: 12px;">Update Profile</button>
            </form>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-lock"></i> Change Password</h3>
            
            <form method="POST">
                <div class="form-group">
                    <label for="old_password">Current Password</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="change_password" class="btn-secondary" style="width: 100%; padding: 12px;">Change Password</button>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <h3 style="margin-bottom: 20px;"><i class="fas fa-sign-out-alt"></i> Account</h3>
            
            <div style="color: #666; margin-bottom: 15px;">
                <p>Member Since: <?php echo date('M d, Y', strtotime($customer['created_at'] ?? '')); ?></p>
                <p>Total Orders: <?php echo $customer['total_orders']; ?></p>
                <p>Total Spent: PKR <?php echo number_format($customer['total_spent'], 2); ?></p>
            </div>
            
            <a href="logout.php" class="btn-primary" style="width: 100%; text-align: center; padding: 12px; text-decoration: none; display: block;">Logout</a>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
