<?php
require_once 'Database.php';

class Food {
    private $db;
    
    public function __construct($connection) {
        $this->db = new Database($connection);
    }
    
    /**
     * Get all food items
     */
    public function getAll($limit = 12, $offset = 0) {
        $result = $this->db->select(
            "SELECT f.*, c.name as category_name 
             FROM food_items f 
             JOIN categories c ON f.category_id = c.id 
             WHERE f.status = 'active' 
             ORDER BY f.created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Get food by category
     */
    public function getByCategory($category_id, $limit = 12, $offset = 0) {
        $result = $this->db->select(
            "SELECT f.*, c.name as category_name 
             FROM food_items f 
             JOIN categories c ON f.category_id = c.id 
             WHERE f.category_id = ? AND f.status = 'active' 
             ORDER BY f.created_at DESC 
             LIMIT ? OFFSET ?",
            [$category_id, $limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Get food by ID
     */
    public function getById($id) {
        $result = $this->db->selectOne(
            "SELECT f.*, c.name as category_name 
             FROM food_items f 
             JOIN categories c ON f.category_id = c.id 
             WHERE f.id = ?",
            [$id]
        );
        
        return $result;
    }
    
    /**
     * Search food items
     */
    public function search($search_term, $limit = 12, $offset = 0) {
        $term = '%' . $search_term . '%';
        
        $result = $this->db->select(
            "SELECT f.*, c.name as category_name 
             FROM food_items f 
             JOIN categories c ON f.category_id = c.id 
             WHERE f.status = 'active' 
             AND (f.name LIKE ? OR f.description LIKE ? OR c.name LIKE ?) 
             ORDER BY f.created_at DESC 
             LIMIT ? OFFSET ?",
            [$term, $term, $term, $limit, $offset]
        );
        
        return $result;
    }
    
    /**
     * Get featured foods (top rated and best selling)
     */
    public function getFeatured($limit = 8) {
        $result = $this->db->select(
            "SELECT f.*, c.name as category_name 
             FROM food_items f 
             JOIN categories c ON f.category_id = c.id 
             WHERE f.status = 'active' 
             ORDER BY f.total_orders DESC, f.rating DESC 
             LIMIT ?",
            [$limit]
        );
        
        return $result;
    }
    
    /**
     * Create food item (Admin)
     */
    public function create($data) {
        if (empty($data['name']) || empty($data['category_id']) || empty($data['price'])) {
            return ['success' => false, 'message' => 'Required fields missing'];
        }
        
        $price = floatval($data['price']);
        $discount = floatval($data['discount_percentage'] ?? 0);
        $final_price = $price - ($price * $discount / 100);
        
        $food_data = [
            'category_id' => intval($data['category_id']),
            'name' => sanitize($data['name']),
            'description' => sanitize($data['description'] ?? ''),
            'price' => $price,
            'discount_percentage' => $discount,
            'final_price' => $final_price,
            'image_path' => sanitize($data['image_path'] ?? ''),
            'status' => 'active',
            'stock' => intval($data['stock'] ?? 0)
        ];
        
        $result = $this->db->insert('food_items', $food_data);
        return $result;
    }
    
    /**
     * Update food item (Admin)
     */
    public function update($food_id, $data) {
        if (empty($food_id)) {
            return ['success' => false, 'message' => 'Food ID is required'];
        }
        
        $update_data = [];
        
        if (!empty($data['name'])) {
            $update_data['name'] = sanitize($data['name']);
        }
        
        if (!empty($data['description'])) {
            $update_data['description'] = sanitize($data['description']);
        }
        
        if (!empty($data['price'])) {
            $price = floatval($data['price']);
            $discount = floatval($data['discount_percentage'] ?? 0);
            $final_price = $price - ($price * $discount / 100);
            
            $update_data['price'] = $price;
            $update_data['discount_percentage'] = $discount;
            $update_data['final_price'] = $final_price;
        }
        
        if (!empty($data['stock'])) {
            $update_data['stock'] = intval($data['stock']);
        }
        
        if (!empty($data['status'])) {
            $update_data['status'] = sanitize($data['status']);
        }
        
        if (empty($update_data)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $result = $this->db->update('food_items', $update_data, 'id = ?', [$food_id]);
        return $result;
    }
    
    /**
     * Delete food item (Admin)
     */
    public function delete($food_id) {
        $result = $this->db->delete('food_items', 'id = ?', [$food_id]);
        return $result;
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        $result = $this->db->select("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
        return $result;
    }
}
?>
