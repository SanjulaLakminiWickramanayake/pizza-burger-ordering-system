<?php
require_once '../backend/includes/init.php';

require_login('customer');

$page_title = 'Logout';

// Clear session
session_destroy();
$_SESSION = [];

// Redirect to home
redirect(SITE_URL . 'customer/index.php');
?>
