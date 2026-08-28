<?php
require_once 'Database.php';

class Admin {
    private $db;
    
    public function __construct($connection) {
        $this->db = new Database($connection);
    }
    
    /**
     * Admin login
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        $result = $this->db->selectOne("SELECT * FROM admins WHERE email = ? AND status = 'active'", [$email]);
        
        if (!$result['success']) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        $admin = $result['data'];
        
        if (!verify_password($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        return ['success' => true, 'admin' => $admin];
    }
    
    /**
     * Get admin by ID
     */
    public function getById($id) {
        $result = $this->db->selectOne("SELECT * FROM admins WHERE id = ?", [$id]);
        return $result;
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total customers
        $customers = $this->db->query("SELECT COUNT(*) as count FROM customers WHERE status = 'active'");
        $stats['total_customers'] = $customers['success'] ? $customers['data'][0]['count'] : 0;
        
        // Total orders
        $orders = $this->db->query("SELECT COUNT(*) as count FROM orders");
        $stats['total_orders'] = $orders['success'] ? $orders['data'][0]['count'] : 0;
        
        // Total sales
        $sales = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'delivered'");
        $stats['total_sales'] = $sales['success'] && $sales['data'][0]['total'] ? $sales['data'][0]['total'] : 0;
        
        // Today's sales
        $today_sales = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = CURDATE() AND status = 'delivered'");
        $stats['today_sales'] = $today_sales['success'] && $today_sales['data'][0]['total'] ? $today_sales['data'][0]['total'] : 0;
        
        // This month sales
        $month_sales = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND status = 'delivered'");
        $stats['month_sales'] = $month_sales['success'] && $month_sales['data'][0]['total'] ? $month_sales['data'][0]['total'] : 0;
        
        // Pending orders
        $pending = $this->db->query("SELECT COUNT(*) as count FROM orders WHERE status IN ('pending', 'confirmed', 'preparing')");
        $stats['pending_orders'] = $pending['success'] ? $pending['data'][0]['count'] : 0;
        
        // Delivered orders
        $delivered = $this->db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'delivered'");
        $stats['delivered_orders'] = $delivered['success'] ? $delivered['data'][0]['count'] : 0;
        
        // Cancelled orders
        $cancelled = $this->db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'cancelled'");
        $stats['cancelled_orders'] = $cancelled['success'] ? $cancelled['data'][0]['count'] : 0;
        
        // Popular foods
        $popular = $this->db->select("SELECT f.*, c.name as category_name, SUM(oi.quantity) as total_sold 
                                      FROM food_items f 
                                      JOIN categories c ON f.category_id = c.id 
                                      JOIN order_items oi ON f.id = oi.food_id 
                                      GROUP BY f.id 
                                      ORDER BY total_sold DESC 
                                      LIMIT 5");
        $stats['popular_foods'] = $popular['success'] ? $popular['data'] : [];
        
        return $stats;
    }
    
    /**
     * Get daily sales
     */
    public function getDailySales($days = 30) {
        $result = $this->db->select(
            "SELECT DATE(created_at) as date, SUM(total_amount) as sales, COUNT(*) as orders 
             FROM orders 
             WHERE status = 'delivered' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
             GROUP BY DATE(created_at) 
             ORDER BY date ASC",
            [$days]
        );
        
        return $result;
    }
    
    /**
     * Get monthly sales
     */
    public function getMonthlySales($months = 12) {
        $result = $this->db->select(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as sales, COUNT(*) as orders 
             FROM orders 
             WHERE status = 'delivered' AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
             GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
             ORDER BY month ASC",
            [$months]
        );
        
        return $result;
    }
    
    /**
     * Get food sales report
     */
    public function getFoodSalesReport() {
        $result = $this->db->select(
            "SELECT f.id, f.name, c.name as category_name, 
                    SUM(oi.quantity) as total_quantity, 
                    SUM(oi.total_price) as total_sales, 
                    COUNT(DISTINCT oi.order_id) as order_count
             FROM food_items f 
             JOIN categories c ON f.category_id = c.id 
             LEFT JOIN order_items oi ON f.id = oi.food_id 
             GROUP BY f.id 
             ORDER BY total_sales DESC"
        );
        
        return $result;
    }
    
    /**
     * Get inventory report
     */
    public function getInventoryReport() {
        $result = $this->db->select(
            "SELECT * FROM inventory ORDER BY quantity ASC"
        );
        
        return $result;
    }
}
?>
