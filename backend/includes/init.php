<?php
// Session initialization
session_start();

require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/functions.php';

// Include all classes
require_once dirname(__FILE__) . '/../classes/Database.php';
require_once dirname(__FILE__) . '/../classes/Customer.php';
require_once dirname(__FILE__) . '/../classes/Admin.php';
require_once dirname(__FILE__) . '/../classes/Food.php';
require_once dirname(__FILE__) . '/../classes/Order.php';
require_once dirname(__FILE__) . '/../classes/Cart.php';
require_once dirname(__FILE__) . '/../classes/DeliveryStaff.php';

// Set session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_destroy();
    $_SESSION = [];
}

$_SESSION['last_activity'] = time();

// Initialize global database instance
global $conn;
?>
