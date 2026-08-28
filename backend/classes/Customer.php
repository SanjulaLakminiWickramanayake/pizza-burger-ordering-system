<?php
require_once 'Database.php';

class Customer {
    private $db;
    
    public function __construct($connection) {
        $this->db = new Database($connection);
    }
    
    /**
     * Register customer
     */
    public function register($data) {
        // Validate input
        if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!is_valid_email($data['email'])) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (!is_valid_phone($data['phone'])) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }
        
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Check if email already exists
        $check = $this->db->selectOne("SELECT id FROM customers WHERE email = ?", [$data['email']]);
        if ($check['success']) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Insert customer
        $insert_data = [
            'name' => sanitize($data['name']),
            'email' => sanitize($data['email']),
            'phone' => sanitize($data['phone']),
            'password' => hash_password($data['password']),
            'address' => sanitize($data['address'] ?? ''),
            'city' => sanitize($data['city'] ?? ''),
            'postal_code' => sanitize($data['postal_code'] ?? ''),
            'status' => 'active'
        ];
        
        $result = $this->db->insert('customers', $insert_data);
        
        if ($result['success']) {
            return ['success' => true, 'message' => 'Registration successful', 'customer_id' => $result['id']];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    /**
     * Login customer
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        $result = $this->db->selectOne("SELECT * FROM customers WHERE email = ? AND status = 'active'", [$email]);
        
        if (!$result['success']) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        $customer = $result['data'];
        
        if (!verify_password($password, $customer['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        return ['success' => true, 'customer' => $customer];
    }
    
    /**
     * Get customer by ID
     */
    public function getById($id) {
        $result = $this->db->selectOne("SELECT * FROM customers WHERE id = ?", [$id]);
        return $result;
    }
    
    /**
     * Update customer profile
     */
    public function updateProfile($customer_id, $data) {
        if (empty($customer_id)) {
            return ['success' => false, 'message' => 'Customer ID is required'];
        }
        
        $update_data = [];
        
        if (!empty($data['name'])) {
            $update_data['name'] = sanitize($data['name']);
        }
        
        if (!empty($data['phone'])) {
            if (!is_valid_phone($data['phone'])) {
                return ['success' => false, 'message' => 'Invalid phone number'];
            }
            $update_data['phone'] = sanitize($data['phone']);
        }
        
        if (!empty($data['address'])) {
            $update_data['address'] = sanitize($data['address']);
        }
        
        if (!empty($data['city'])) {
            $update_data['city'] = sanitize($data['city']);
        }
        
        if (!empty($data['postal_code'])) {
            $update_data['postal_code'] = sanitize($data['postal_code']);
        }
        
        if (empty($update_data)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $result = $this->db->update('customers', $update_data, 'id = ?', [$customer_id]);
        
        if ($result['success']) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Profile update failed'];
    }
    
    /**
     * Change password
     */
    public function changePassword($customer_id, $old_password, $new_password, $confirm_password) {
        if (empty($customer_id) || empty($old_password) || empty($new_password) || empty($confirm_password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if ($new_password !== $confirm_password) {
            return ['success' => false, 'message' => 'New passwords do not match'];
        }
        
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Get customer
        $result = $this->getById($customer_id);
        if (!$result['success']) {
            return ['success' => false, 'message' => 'Customer not found'];
        }
        
        $customer = $result['data'];
        
        // Verify old password
        if (!verify_password($old_password, $customer['password'])) {
            return ['success' => false, 'message' => 'Old password is incorrect'];
        }
        
        // Update password
        $update_result = $this->db->update('customers', ['password' => hash_password($new_password)], 'id = ?', [$customer_id]);
        
        if ($update_result['success']) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        
        return ['success' => false, 'message' => 'Password change failed'];
    }
    
    /**
     * Get customer orders
     */
    public function getOrders($customer_id, $limit = 10, $offset = 0) {
        $result = $this->db->select(
            "SELECT o.*, 
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
             FROM orders o 
             WHERE o.customer_id = ? 
             ORDER BY o.created_at DESC 
             LIMIT ? OFFSET ?",
            [$customer_id, $limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Get all customers
     */
    public function getAll($limit = 10, $offset = 0) {
        $result = $this->db->select(
            "SELECT * FROM customers ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Block customer
     */
    public function blockCustomer($customer_id) {
        $result = $this->db->update('customers', ['status' => 'blocked'], 'id = ?', [$customer_id]);
        return $result;
    }
    
    /**
     * Unblock customer
     */
    public function unblockCustomer($customer_id) {
        $result = $this->db->update('customers', ['status' => 'active'], 'id = ?', [$customer_id]);
        return $result;
    }
}
?>
