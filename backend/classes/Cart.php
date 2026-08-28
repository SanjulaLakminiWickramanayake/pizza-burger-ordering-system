<?php
require_once 'Database.php';

class Cart {
    private $db;
    
    public function __construct($connection) {
        $this->db = new Database($connection);
    }
    
    /**
     * Get or create cart
     */
    public function getCart($customer_id) {
        $result = $this->db->selectOne("SELECT * FROM carts WHERE customer_id = ?", [$customer_id]);
        
        if (!$result['success']) {
            // Create new cart
            $cart_data = [
                'customer_id' => $customer_id,
                'total_items' => 0,
                'subtotal' => 0,
                'tax' => 0,
                'delivery_charge' => 0,
                'total' => 0
            ];
            
            $insert_result = $this->db->insert('carts', $cart_data);
            if ($insert_result['success']) {
                return $this->getCart($customer_id);
            }
        }
        
        return $result;
    }
    
    /**
     * Add item to cart
     */
    public function addItem($customer_id, $food_id, $quantity) {
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Invalid quantity'];
        }
        
        // Get food item
        $food_result = $this->db->selectOne("SELECT * FROM food_items WHERE id = ?", [$food_id]);
        if (!$food_result['success']) {
            return ['success' => false, 'message' => 'Food item not found'];
        }
        
        $food = $food_result['data'];
        
        // Check stock
        if ($food['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        // Get cart
        $cart_result = $this->getCart($customer_id);
        if (!$cart_result['success']) {
            return ['success' => false, 'message' => 'Cart not found'];
        }
        
        $cart = $cart_result['data'];
        $unit_price = $food['final_price'] ?? $food['price'];
        $total_price = $unit_price * $quantity;
        
        // Check if item already in cart
        $existing_result = $this->db->selectOne(
            "SELECT * FROM cart_items WHERE cart_id = ? AND food_id = ?",
            [$cart['id'], $food_id]
        );
        
        if ($existing_result['success']) {
            // Update quantity
            $existing = $existing_result['data'];
            $new_quantity = $existing['quantity'] + $quantity;
            $new_total = $new_quantity * $unit_price;
            
            $this->db->update('cart_items',
                ['quantity' => $new_quantity, 'total_price' => $new_total],
                'id = ?',
                [$existing['id']]
            );
        } else {
            // Add new item
            $item_data = [
                'cart_id' => $cart['id'],
                'food_id' => $food_id,
                'quantity' => $quantity,
                'unit_price' => $unit_price,
                'total_price' => $total_price
            ];
            
            $this->db->insert('cart_items', $item_data);
        }
        
        // Update cart totals
        $this->updateCartTotals($cart['id']);
        
        return ['success' => true, 'message' => 'Item added to cart'];
    }
    
    /**
     * Update item quantity
     */
    public function updateItem($cart_item_id, $quantity) {
        if ($quantity < 0) {
            return ['success' => false, 'message' => 'Invalid quantity'];
        }
        
        if ($quantity == 0) {
            return $this->removeItem($cart_item_id);
        }
        
        // Get item
        $item_result = $this->db->selectOne("SELECT * FROM cart_items WHERE id = ?", [$cart_item_id]);
        if (!$item_result['success']) {
            return ['success' => false, 'message' => 'Item not found'];
        }
        
        $item = $item_result['data'];
        
        // Check food stock
        $food_result = $this->db->selectOne("SELECT * FROM food_items WHERE id = ?", [$item['food_id']]);
        if (!$food_result['success']) {
            return ['success' => false, 'message' => 'Food item not found'];
        }
        
        $food = $food_result['data'];
        
        if ($food['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        // Update quantity
        $new_total = $quantity * $item['unit_price'];
        $this->db->update('cart_items',
            ['quantity' => $quantity, 'total_price' => $new_total],
            'id = ?',
            [$cart_item_id]
        );
        
        // Update cart totals
        $this->updateCartTotals($item['cart_id']);
        
        return ['success' => true, 'message' => 'Item quantity updated'];
    }
    
    /**
     * Remove item from cart
     */
    public function removeItem($cart_item_id) {
        // Get item
        $item_result = $this->db->selectOne("SELECT * FROM cart_items WHERE id = ?", [$cart_item_id]);
        if (!$item_result['success']) {
            return ['success' => false, 'message' => 'Item not found'];
        }
        
        $item = $item_result['data'];
        
        // Delete item
        $this->db->delete('cart_items', 'id = ?', [$cart_item_id]);
        
        // Update cart totals
        $this->updateCartTotals($item['cart_id']);
        
        return ['success' => true, 'message' => 'Item removed from cart'];
    }
    
    /**
     * Update cart totals
     */
    public function updateCartTotals($cart_id) {
        // Get all items
        $items_result = $this->db->select("SELECT * FROM cart_items WHERE cart_id = ?", [$cart_id]);
        
        $total_items = 0;
        $subtotal = 0;
        
        if ($items_result['success']) {
            foreach ($items_result['data'] as $item) {
                $total_items += $item['quantity'];
                $subtotal += $item['total_price'];
            }
        }
        
        $tax = calculate_tax($subtotal);
        $delivery_charge = get_delivery_charge($subtotal);
        $total = $subtotal + $tax + $delivery_charge;
        
        // Update cart
        $this->db->update('carts',
            [
                'total_items' => $total_items,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'delivery_charge' => $delivery_charge,
                'total' => $total
            ],
            'id = ?',
            [$cart_id]
        );
    }
    
    /**
     * Get cart items
     */
    public function getItems($customer_id) {
        $cart_result = $this->getCart($customer_id);
        if (!$cart_result['success']) {
            return ['success' => false, 'message' => 'Cart not found'];
        }
        
        $cart = $cart_result['data'];
        
        $result = $this->db->select(
            "SELECT ci.*, f.name, f.image_path FROM cart_items ci 
             JOIN food_items f ON ci.food_id = f.id 
             WHERE ci.cart_id = ?",
            [$cart['id']]
        );
        
        return $result;
    }
    
    /**
     * Clear cart
     */
    public function clear($customer_id) {
        $cart_result = $this->getCart($customer_id);
        if (!$cart_result['success']) {
            return ['success' => false, 'message' => 'Cart not found'];
        }
        
        $cart = $cart_result['data'];
        
        $this->db->delete('cart_items', 'cart_id = ?', [$cart['id']]);
        
        $this->db->update('carts',
            [
                'total_items' => 0,
                'subtotal' => 0,
                'tax' => 0,
                'delivery_charge' => 0,
                'total' => 0
            ],
            'id = ?',
            [$cart['id']]
        );
        
        return ['success' => true, 'message' => 'Cart cleared'];
    }
}
?>
