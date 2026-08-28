<?php
require_once '../backend/includes/init.php';

require_login('admin');

// Alias page for admin menu management.
header('Location: manage-foods.php');
exit;
?>