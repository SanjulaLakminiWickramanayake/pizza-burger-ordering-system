<?php
require_once 'Database.php';

class DeliveryStaff {
    private $db;
    
    public function __construct($connection) {
        $this->db = new Database($connection);
    }
    
    /**
     * Delivery staff login
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        $result = $this->db->selectOne("SELECT * FROM delivery_staff WHERE email = ? AND status IN ('active', 'on_break')", [$email]);
        
        if (!$result['success']) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        $staff = $result['data'];
        
        if (!verify_password($password, $staff['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        return ['success' => true, 'staff' => $staff];
    }
    
    /**
     * Get delivery staff by ID
     */
    public function getById($id) {
        $result = $this->db->selectOne("SELECT * FROM delivery_staff WHERE id = ?", [$id]);
        return $result;
    }
    
    /**
     * Get assigned orders
     */
    public function getAssignedOrders($delivery_person_id) {
        $result = $this->db->select(
            "SELECT o.*, c.name as customer_name, c.phone as customer_phone, c.address as customer_address,
                    da.status as assignment_status
             FROM orders o 
             JOIN customers c ON o.customer_id = c.id 
             JOIN delivery_assignments da ON o.id = da.order_id 
             WHERE da.delivery_person_id = ? AND da.status IN ('assigned', 'picked_up')
             ORDER BY da.assigned_at DESC",
            [$delivery_person_id]
        );
        
        return $result;
    }
    
    /**
     * Get delivery history
     */
    public function getDeliveryHistory($delivery_person_id, $limit = 10, $offset = 0) {
        $result = $this->db->select(
            "SELECT o.*, c.name as customer_name, da.status as assignment_status, da.delivered_at
             FROM orders o 
             JOIN customers c ON o.customer_id = c.id 
             JOIN delivery_assignments da ON o.id = da.order_id 
             WHERE da.delivery_person_id = ? AND da.status IN ('delivered', 'cancelled')
             ORDER BY da.delivered_at DESC 
             LIMIT ? OFFSET ?",
            [$delivery_person_id, $limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Update delivery assignment status
     */
    public function updateAssignmentStatus($order_id, $status) {
        $valid_statuses = ['assigned', 'picked_up', 'delivered', 'cancelled'];
        
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $update_data = ['status' => $status];
        
        if ($status === 'picked_up') {
            $update_data['picked_up_at'] = date('Y-m-d H:i:s');
            $this->db->update('orders', ['status' => 'out_for_delivery'], 'id = ?', [$order_id]);
        } elseif ($status === 'delivered') {
            $update_data['delivered_at'] = date('Y-m-d H:i:s');
            $this->db->update('orders', ['status' => 'delivered'], 'id = ?', [$order_id]);
        }
        
        $result = $this->db->update('delivery_assignments', $update_data, 'order_id = ?', [$order_id]);
        
        return $result;
    }
    
    /**
     * Add delivery notes
     */
    public function addNotes($order_id, $notes) {
        $result = $this->db->update('delivery_assignments', 
            ['notes' => sanitize($notes)], 
            'order_id = ?', 
            [$order_id]
        );
        
        return $result;
    }
    
    /**
     * Get all delivery staff (Admin)
     */
    public function getAll($limit = 10, $offset = 0) {
        $result = $this->db->select(
            "SELECT * FROM delivery_staff ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Create delivery staff (Admin)
     */
    public function create($data) {
        if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Required fields missing'];
        }
        
        if (!is_valid_email($data['email'])) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (!is_valid_phone($data['phone'])) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }
        
        // Check if email already exists
        $check = $this->db->selectOne("SELECT id FROM delivery_staff WHERE email = ?", [$data['email']]);
        if ($check['success']) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        $staff_data = [
            'name' => sanitize($data['name']),
            'email' => sanitize($data['email']),
            'phone' => sanitize($data['phone']),
            'password' => hash_password($data['password']),
            'address' => sanitize($data['address'] ?? ''),
            'vehicle_type' => sanitize($data['vehicle_type'] ?? 'bike'),
            'vehicle_plate' => sanitize($data['vehicle_plate'] ?? ''),
            'status' => 'active'
        ];
        
        $result = $this->db->insert('delivery_staff', $staff_data);
        
        return $result;
    }
    
    /**
     * Update delivery staff (Admin)
     */
    public function update($staff_id, $data) {
        if (empty($staff_id)) {
            return ['success' => false, 'message' => 'Staff ID is required'];
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
        
        if (!empty($data['vehicle_type'])) {
            $update_data['vehicle_type'] = sanitize($data['vehicle_type']);
        }
        
        if (!empty($data['status'])) {
            $update_data['status'] = sanitize($data['status']);
        }
        
        if (empty($update_data)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $result = $this->db->update('delivery_staff', $update_data, 'id = ?', [$staff_id]);
        
        return $result;
    }
}
?>
