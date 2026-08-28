<?php
require_once '../backend/includes/init.php';

require_login('delivery');

session_destroy();
$_SESSION = [];

redirect(SITE_URL . 'delivery/login.php');
?>
