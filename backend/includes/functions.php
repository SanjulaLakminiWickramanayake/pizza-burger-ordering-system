<?php
// Utility Functions

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (10-15 digits)
 */
function is_valid_phone($phone) {
    return preg_match('/^[0-9]{10,15}$/', preg_replace('/\D/', '', $phone));
}

/**
 * Hash password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate unique order number
 */
function generate_order_number() {
    return 'ORD' . date('YmdHis') . rand(1000, 9999);
}

/**
 * Format currency
 */
function format_currency($amount) {
    return CURRENCY . ' ' . number_format($amount, 2);
}

/**
 * Get the public URL for a food image.
 */
function food_image_url($image_path) {
    $filename = basename(trim((string) $image_path));

    if ($filename === '') {
        return SITE_URL . 'images/placeholder.png';
    }

    $uploaded_file = UPLOAD_DIR . 'food_images/' . $filename;
    if (is_file($uploaded_file)) {
        return SITE_URL . 'uploads/food_images/' . rawurlencode($filename);
    }

    $sample_file = dirname(__FILE__, 3) . '/images/' . $filename;
    if (is_file($sample_file)) {
        return SITE_URL . 'images/' . rawurlencode($filename);
    }

    return SITE_URL . 'images/placeholder.png';
}

/**
 * Calculate tax
 */
function calculate_tax($subtotal) {
    return $subtotal * TAX_RATE;
}

/**
 * Calculate delivery charge
 */
function get_delivery_charge($subtotal) {
    return ($subtotal >= FREE_DELIVERY_MIN) ? 0 : DELIVERY_CHARGE;
}

/**
 * Get order status label
 */
function get_status_label($status) {
    $labels = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'preparing' => 'Preparing',
        'ready' => 'Ready for Delivery',
        'out_for_delivery' => 'Out for Delivery',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];
    return isset($labels[$status]) ? $labels[$status] : ucfirst($status);
}

/**
 * Get payment method label
 */
function get_payment_method_label($method) {
    $methods = [
        'cash' => 'Cash on Delivery',
        'bank_transfer' => 'Bank Transfer',
        'online_screenshot' => 'Online Payment'
    ];
    return isset($methods[$method]) ? $methods[$method] : ucfirst($method);
}

/**
 * Redirect
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 */
function is_logged_in($user_type = 'customer') {
    if ($user_type === 'customer') {
        return isset($_SESSION['customer_id']);
    } elseif ($user_type === 'admin') {
        return isset($_SESSION['admin_id']);
    } elseif ($user_type === 'delivery') {
        return isset($_SESSION['delivery_id']);
    }
    return false;
}

/**
 * Require login
 */
function require_login($user_type = 'customer', $redirect_url = null) {
    if (!is_logged_in($user_type)) {
        if ($redirect_url === null) {
            if ($user_type === 'admin') {
                $redirect_url = SITE_URL . 'admin/login.php';
            } elseif ($user_type === 'delivery') {
                $redirect_url = SITE_URL . 'delivery/login.php';
            } else {
                $redirect_url = SITE_URL . 'customer/login.php';
            }
        }
        redirect($redirect_url);
    }
}

/**
 * Validate image upload
 */
function validate_image_upload($file, $max_size = 5242880) { // 5MB
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Image size exceeds maximum limit'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid image format. Only JPEG, PNG, GIF, and WebP are allowed'];
    }
    
    return ['success' => true];
}

/**
 * Upload image
 */
function upload_image($file, $destination) {
    $validation = validate_image_upload($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filepath = $destination . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to upload image'];
}

/**
 * Get database connection from config
 */
function get_db() {
    global $conn;
    return $conn;
}

?>
