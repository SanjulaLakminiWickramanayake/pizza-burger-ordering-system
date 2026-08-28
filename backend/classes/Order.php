<?php
require_once 'Database.php';

class Order {
    private $db;
    
    public function __construct($connection) {
        $this->db = new Database($connection);
    }
    
    /**
     * Create order from cart
     */
    public function createOrder($customer_id, $data) {
        if (empty($customer_id) || empty($data['delivery_address']) || empty($data['delivery_phone'])) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }
        
        // Get cart
        $cart_result = $this->db->selectOne("SELECT * FROM carts WHERE customer_id = ?", [$customer_id]);
        if (!$cart_result['success']) {
            return ['success' => false, 'message' => 'Cart not found'];
        }
        
        $cart = $cart_result['data'];
        
        if ($cart['total_items'] == 0) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }
        
        // Get cart items
        $items_result = $this->db->select(
            "SELECT ci.*, f.name as food_name FROM cart_items ci 
             JOIN food_items f ON ci.food_id = f.id 
             WHERE ci.cart_id = ?",
            [$cart['id']]
        );
        
        if (!$items_result['success'] || empty($items_result['data'])) {
            return ['success' => false, 'message' => 'Cart items not found'];
        }
        
        $cart_items = $items_result['data'];
        
        // Calculate totals
        $subtotal = $cart['subtotal'];
        $tax = calculate_tax($subtotal);
        $delivery_charge = get_delivery_charge($subtotal);
        $total = $subtotal + $tax + $delivery_charge;
        
        // Create order
        $order_data = [
            'order_number' => generate_order_number(),
            'customer_id' => $customer_id,
            'delivery_address' => sanitize($data['delivery_address']),
            'delivery_phone' => sanitize($data['delivery_phone']),
            'delivery_notes' => sanitize($data['delivery_notes'] ?? ''),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_charge' => $delivery_charge,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_method' => sanitize($data['payment_method'] ?? 'cash'),
            'payment_status' => 'unpaid'
        ];
        
        $insert_result = $this->db->insert('orders', $order_data);
        if (!$insert_result['success']) {
            return ['success' => false, 'message' => 'Failed to create order'];
        }
        
        $order_id = $insert_result['id'];
        
        // Insert order items
        foreach ($cart_items as $item) {
            $order_item_data = [
                'order_id' => $order_id,
                'food_id' => $item['food_id'],
                'food_name' => $item['food_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price']
            ];
            
            $this->db->insert('order_items', $order_item_data);
        }
        
        // Create payment record
        $payment_data = [
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'payment_method' => $order_data['payment_method'],
            'amount' => $total,
            'status' => 'pending'
        ];
        
        $this->db->insert('payments', $payment_data);
        
        // Clear cart
        $this->db->delete('cart_items', 'cart_id = ?', [$cart['id']]);
        $this->db->update('carts', [
            'total_items' => 0,
            'subtotal' => 0,
            'tax' => 0,
            'delivery_charge' => 0,
            'total' => 0
        ], 'id = ?', [$cart['id']]);
        
        // Create notification
        $notification_data = [
            'customer_id' => $customer_id,
            'order_id' => $order_id,
            'title' => 'Order Confirmed',
            'message' => 'Your order #' . $order_data['order_number'] . ' has been placed successfully',
            'type' => 'order_status'
        ];
        
        $this->db->insert('notifications', $notification_data);
        
        return ['success' => true, 'message' => 'Order created successfully', 'order_id' => $order_id, 'order_number' => $order_data['order_number']];
    }
    
    /**
     * Get order details
     */
    public function getOrder($order_id) {
        $result = $this->db->selectOne("SELECT * FROM orders WHERE id = ?", [$order_id]);
        return $result;
    }
    
    /**
     * Get order items
     */
    public function getOrderItems($order_id) {
        $result = $this->db->select("SELECT * FROM order_items WHERE order_id = ?", [$order_id]);
        return $result;
    }
    
    /**
     * Update order status
     */
    public function updateStatus($order_id, $status) {
        $valid_statuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'];
        
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $result = $this->db->update('orders', ['status' => $status], 'id = ?', [$order_id]);
        
        if ($result['success']) {
            // Create notification
            $order = $this->getOrder($order_id);
            if ($order['success']) {
                $notification_data = [
                    'customer_id' => $order['data']['customer_id'],
                    'order_id' => $order_id,
                    'title' => 'Order Status Updated',
                    'message' => 'Your order status is now: ' . get_status_label($status),
                    'type' => 'order_status'
                ];
                
                $this->db->insert('notifications', $notification_data);
            }
        }
        
        return $result;
    }
    
    /**
     * Get all orders
     */
    public function getAll($limit = 10, $offset = 0) {
        $result = $this->db->select(
            "SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone
             FROM orders o 
             JOIN customers c ON o.customer_id = c.id 
             ORDER BY o.created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Get orders by status
     */
    public function getByStatus($status, $limit = 10, $offset = 0) {
        $result = $this->db->select(
            "SELECT o.*, c.name as customer_name 
             FROM orders o 
             JOIN customers c ON o.customer_id = c.id 
             WHERE o.status = ? 
             ORDER BY o.created_at DESC 
             LIMIT ? OFFSET ?",
            [$status, $limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Search orders
     */
    public function search($search_term, $limit = 10, $offset = 0) {
        $term = '%' . $search_term . '%';
        
        $result = $this->db->select(
            "SELECT o.*, c.name as customer_name 
             FROM orders o 
             JOIN customers c ON o.customer_id = c.id 
             WHERE o.order_number LIKE ? 
                OR c.name LIKE ? 
                OR c.email LIKE ? 
                OR c.phone LIKE ? 
             ORDER BY o.created_at DESC 
             LIMIT ? OFFSET ?",
            [$term, $term, $term, $term, $limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Assign delivery person
     */
    public function assignDeliveryPerson($order_id, $delivery_person_id) {
        // Update order
        $update_result = $this->db->update('orders', 
            ['delivery_person_id' => $delivery_person_id, 'status' => 'ready'], 
            'id = ?', 
            [$order_id]
        );
        
        if ($update_result['success']) {
            // Create delivery assignment
            $assignment_data = [
                'order_id' => $order_id,
                'delivery_person_id' => $delivery_person_id,
                'status' => 'assigned'
            ];
            
            $this->db->insert('delivery_assignments', $assignment_data);
        }
        
        return $update_result;
    }
}
?>
