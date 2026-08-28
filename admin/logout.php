<?php
require_once '../backend/includes/init.php';

require_login('admin');

session_destroy();
$_SESSION = [];

redirect(SITE_URL . 'admin/login.php');
?>
