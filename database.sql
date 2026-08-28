-- Pizza & Burger Online Food Ordering and Delivery Management System
-- Complete Database Schema

CREATE DATABASE IF NOT EXISTS food_delivery_system;
USE food_delivery_system;

-- ===================================
-- ADMINS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('superadmin', 'manager') DEFAULT 'manager',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- ===================================
-- CUSTOMERS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    city VARCHAR(100),
    postal_code VARCHAR(20),
    status ENUM('active', 'blocked', 'inactive') DEFAULT 'active',
    total_orders INT DEFAULT 0,
    total_spent DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_status (status)
);

-- ===================================
-- CATEGORIES TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_status (status)
);

-- ===================================
-- FOOD ITEMS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS food_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(8, 2) NOT NULL,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    final_price DECIMAL(8, 2),
    image_path VARCHAR(255),
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    stock INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0,
    total_orders INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_price (price)
);

-- ===================================
-- FOOD IMAGES TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS food_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    food_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (food_id) REFERENCES food_items(id) ON DELETE CASCADE,
    INDEX idx_food (food_id)
);

-- ===================================
-- CARTS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    total_items INT DEFAULT 0,
    subtotal DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    delivery_charge DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_customer (customer_id),
    INDEX idx_customer (customer_id)
);

-- ===================================
-- CART ITEMS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(8, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food_items(id) ON DELETE CASCADE,
    INDEX idx_cart (cart_id),
    INDEX idx_food (food_id)
);

-- ===================================
-- DELIVERY STAFF TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS delivery_staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    vehicle_type VARCHAR(50),
    vehicle_plate VARCHAR(50),
    status ENUM('active', 'inactive', 'on_break') DEFAULT 'active',
    total_deliveries INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- ===================================
-- ORDERS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    delivery_address VARCHAR(255) NOT NULL,
    delivery_phone VARCHAR(20) NOT NULL,
    delivery_notes TEXT,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) NOT NULL,
    delivery_charge DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('cash', 'bank_transfer', 'online_screenshot') DEFAULT 'cash',
    payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    delivery_person_id INT,
    estimated_delivery_time TIME,
    actual_delivery_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (delivery_person_id) REFERENCES delivery_staff(id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_status (status),
    INDEX idx_order_number (order_number),
    INDEX idx_created_at (created_at)
);

-- ===================================
-- ORDER ITEMS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    food_id INT,
    food_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(8, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food_items(id) ON DELETE SET NULL,
    INDEX idx_order (order_id),
    INDEX idx_food (food_id)
);

-- ===================================
-- PAYMENTS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'online_screenshot') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    reference VARCHAR(255),
    screenshot_path VARCHAR(255),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_order (order_id)
);

-- ===================================
-- REVIEWS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    food_id INT NOT NULL,
    customer_id INT NOT NULL,
    order_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (food_id) REFERENCES food_items(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_food (food_id),
    INDEX idx_customer (customer_id),
    INDEX idx_status (status)
);

-- ===================================
-- DELIVERY ASSIGNMENTS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS delivery_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL UNIQUE,
    delivery_person_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    picked_up_at DATETIME,
    delivered_at DATETIME,
    status ENUM('assigned', 'picked_up', 'delivered', 'cancelled') DEFAULT 'assigned',
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (delivery_person_id) REFERENCES delivery_staff(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_delivery_person (delivery_person_id),
    INDEX idx_status (status)
);

-- ===================================
-- INVENTORY TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(100) NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    reorder_level DECIMAL(10, 2) DEFAULT 10,
    supplier VARCHAR(100),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_item_name (item_name)
);

-- ===================================
-- INVENTORY TRANSACTIONS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS inventory_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    inventory_id INT NOT NULL,
    transaction_type ENUM('add', 'use', 'adjust') NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    reference VARCHAR(255),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_inventory (inventory_id),
    INDEX idx_created_at (created_at)
);

-- ===================================
-- NOTIFICATIONS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    order_id INT,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order_status', 'delivery', 'system') DEFAULT 'system',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

-- ===================================
-- SETTINGS TABLE
-- ===================================
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===================================
-- INSERT SAMPLE DATA
-- ===================================

-- Sample Admin
INSERT INTO admins (name, email, password, phone, role, status) VALUES
('Super Admin', 'admin@fooddelivery.com', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '03001234567', 'superadmin', 'active'),
('Manager', 'manager@fooddelivery.com', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '03009876543', 'manager', 'active');

-- Sample Categories
INSERT INTO categories (name, description, status) VALUES
('Pizza', 'Delicious pizzas with various toppings', 'active'),
('Burgers', 'Tasty burgers with premium ingredients', 'active'),
('Beverages', 'Cool and refreshing drinks', 'active'),
('French Fries', 'Crispy fried potatoes', 'active'),
('Combo Meals', 'Special combo packages', 'active');

-- Sample Food Items - Pizza
INSERT INTO food_items (category_id, name, description, price, discount_percentage, image_path, status, stock) VALUES
(1, 'Margherita Pizza', 'Classic pizza with tomato, mozzarella, and basil', 800.00, 0, 'margherita.jpg', 'active', 50),
(1, 'Pepperoni Pizza', 'Delicious pizza loaded with pepperoni', 950.00, 5, 'pepperoni.jpg', 'active', 40),
(1, 'Veggie Pizza', 'Fresh vegetables on a crispy crust', 750.00, 0, 'veggie.jpg', 'active', 45),
(1, 'BBQ Chicken Pizza', 'Smoky BBQ chicken with onions', 1100.00, 10, 'bbq_chicken.jpg', 'active', 30);

-- Sample Food Items - Burgers
INSERT INTO food_items (category_id, name, description, price, discount_percentage, image_path, status, stock) VALUES
(2, 'Classic Burger', 'Juicy beef patty with lettuce and tomato', 400.00, 0, 'classic_burger.jpg', 'active', 60),
(2, 'Double Cheese Burger', 'Double beef patties with double cheese', 600.00, 5, 'double_cheese.jpg', 'active', 50),
(2, 'Chicken Burger', 'Crispy chicken breast with special sauce', 500.00, 0, 'chicken_burger.jpg', 'active', 55),
(2, 'Spicy Buffalo Burger', 'Hot and spicy buffalo chicken burger', 550.00, 10, 'buffalo_burger.jpg', 'active', 35);

-- Sample Food Items - Beverages
INSERT INTO food_items (category_id, name, description, price, discount_percentage, image_path, status, stock) VALUES
(3, 'Coca Cola', 'Ice cold Coca Cola 330ml', 150.00, 0, 'coca_cola.jpg', 'active', 100),
(3, 'Fanta Orange', 'Refreshing Fanta Orange 330ml', 150.00, 0, 'fanta.jpg', 'active', 100),
(3, 'Fresh Lemonade', 'Homemade fresh lemonade 500ml', 200.00, 0, 'lemonade.jpg', 'active', 80),
(3, 'Iced Tea', 'Chilled iced tea 500ml', 180.00, 0, 'iced_tea.jpg', 'active', 90);

-- Sample Food Items - French Fries
INSERT INTO food_items (category_id, name, description, price, discount_percentage, image_path, status, stock) VALUES
(4, 'Regular Fries', 'Crispy potato fries', 250.00, 0, 'regular_fries.jpg', 'active', 70),
(4, 'Cheese Fries', 'Fries loaded with melted cheese', 350.00, 5, 'cheese_fries.jpg', 'active', 60),
(4, 'Spicy Fries', 'Fries with special spicy coating', 300.00, 0, 'spicy_fries.jpg', 'active', 50),
(4, 'Garlic Parmesan Fries', 'Fries with garlic and parmesan', 380.00, 0, 'garlic_fries.jpg', 'active', 40);

INSERT INTO food_items (category_id, name, description, price, discount_percentage, image_path, status, stock) VALUES
(5, 'Pizza + Fries Combo', 'One pizza with a side of fries', 1200.00, 10, 'pizza_fries_combo.jpg', 'active', 20),
(5, 'Burger + Beverage Combo', 'One burger with a drink', 600.00, 5, 'burger_drink_combo.jpg', 'active', 25),
(5, 'Family Feast', '2 Pizzas + 2 Burgers + 2 Drinks', 2500.00, 15, 'family_feast.jpg', 'active', 15),
(5, 'Party Pack', '3 Pizzas + 4 Burgers + 4 Drinks', 4000.00, 20, 'party_pack.jpg', 'active', 10);
-- Sample Food Images
INSERT INTO food_images (food_id, image_path, is_primary) VALUES
(1, 'margherit.jpg', TRUE),
(2, 'pepperoni.jpg', TRUE),
(3, 'veggie.jpg', TRUE),
(4, 'bbq_chicken.jpg', TRUE),
(5, 'classic_burger.jpg', TRUE),
(6, 'double_cheese.jpg', TRUE),
(7, 'chicken_burger.jpg', TRUE),
(8, 'buffalo_burger.jpg', TRUE),
(9, 'coca_cola.jpg', TRUE),
(10, 'fanta.jpg', TRUE),
(11, 'lemonade.jpg', TRUE),
(12, 'iced_tea.jpg', TRUE),
(13, 'regular_fries.jpg', TRUE),
(14, 'cheese_fries.jpg', TRUE),
(15, 'spicy_fries.jpg', TRUE),
(16, 'garlic_fries.jpg', TRUE),
(17, 'pizza_fries_combo.jpg', TRUE),
(19, 'family_feast.jpg', TRUE),
(20, 'party_pack.jpg', TRUE);

-- Update final_price based on discount
UPDATE food_items SET final_price = ROUND(price - (price * discount_percentage / 100), 2) WHERE id > 0;

-- Sample Customers
INSERT INTO customers (name, email, phone, password, address, city, postal_code, status) VALUES
('Ahmed Khan', 'ahmed@example.com', '03001234567', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '123 Street, House 1', 'Karachi', '74000', 'active'),
('Fatima Ali', 'fatima@example.com', '03009876543', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '456 Avenue, House 2', 'Lahore', '54000', 'active'),
('Hassan Ahmed', 'hassan@example.com', '03005555555', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '789 Road, House 3', 'Islamabad', '44000', 'active');

-- Sample Delivery Staff
INSERT INTO delivery_staff (name, email, phone, password, address, vehicle_type, vehicle_plate, status) VALUES
('Ali Rider', 'ali.rider@fooddelivery.com', '03111111111', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '100 Delivery Lane', 'Bike', 'KHI-123', 'active'),
('Hassan Courier', 'hassan.courier@fooddelivery.com', '03222222222', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '200 Delivery Road', 'Bike', 'KHI-456', 'active'),
('Mustafa Driver', 'mustafa.driver@fooddelivery.com', '03333333333', '$2y$10$RGbGsOqm3EaKv5jV3DyXBOaMLjBqxRtw0zC1A8hTQv1JTL36o5meW', '300 Transport Way', 'Car', 'KHI-789', 'active');

-- Inventory Items
INSERT INTO inventory (item_name, quantity, unit, reorder_level, supplier) VALUES
('Burger Buns', 500, 'pieces', 100, 'Local Bakery'),
('Pizza Dough', 200, 'kg', 50, 'Flour Mill'),
('Cheese', 150, 'kg', 30, 'Dairy Farm'),
('Chicken Breast', 100, 'kg', 25, 'Meat Supplier'),
('Beef Patties', 80, 'kg', 20, 'Meat Supplier'),
('Vegetables', 200, 'kg', 50, 'Vegetable Market'),
('Sauces', 50, 'liters', 10, 'Condiment Factory'),
('Soft Drinks', 300, 'bottles', 100, 'Beverage Distributor');

-- Settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('restaurant_name', 'Pizza & Burger Hub', 'Restaurant name'),
('restaurant_phone', '+92-300-1234567', 'Main contact number'),
('restaurant_email', 'info@pizzaburger.com', 'Contact email'),
('tax_percentage', '5', 'Tax percentage'),
('delivery_charge', '150', 'Standard delivery charge'),
('free_delivery_minimum', '2000', 'Minimum order for free delivery'),
('currency', 'PKR', 'Currency symbol'),
('restaurant_address', '123 Food Street, Karachi', 'Restaurant address');
