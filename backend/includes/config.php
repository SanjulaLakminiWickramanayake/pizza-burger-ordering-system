<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_delivery_system');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Website Configuration
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? 'https://' : 'http://';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : '';
$projectRoot = str_replace('\\', '/', dirname(__FILE__, 3));
$documentRoot = rtrim($documentRoot, '/');
$projectRoot = rtrim($projectRoot, '/');

if ($documentRoot !== '' && strpos($projectRoot, $documentRoot) === 0) {
    $relativePath = substr($projectRoot, strlen($documentRoot));
    $basePath = '/' . ltrim($relativePath, '/');
} else {
    $basePath = '';
}

define('SITE_URL', rtrim($protocol . $host . $basePath, '/') . '/');
define('SITE_NAME', 'Pizza & Burger Hub');
define('UPLOAD_DIR', dirname(__FILE__, 3) . '/uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 10]);

// Settings
define('ITEMS_PER_PAGE', 12);
define('TAX_RATE', 0.05); // 5%
define('DELIVERY_CHARGE', 150);
define('FREE_DELIVERY_MIN', 2000);
define('CURRENCY', 'PKR');
?>
