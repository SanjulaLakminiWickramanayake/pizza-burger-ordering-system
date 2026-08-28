<?php
/**
 * Installation Verification Script
 * Run this to verify your installation is correct
 */

$checks_passed = 0;
$checks_total = 0;

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Installation Verification</title>";
echo "<link rel='stylesheet' href='css/style.css'>";
echo "</head><body>";
echo "<div class='container' style='margin-top: 40px;'>";
echo "<h1 style='color: #FF6B35;'>📋 Installation Verification</h1>";

// Check 1: Database connection
echo "<div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h3><i class='fas fa-database'></i> Database Connection</h3>";

$checks_total++;
try {
    require_once 'backend/includes/config.php';
    $test_conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($test_conn->connect_error) {
        echo "<p style='color: #dc3545;'>❌ Failed: " . $test_conn->connect_error . "</p>";
    } else {
        echo "<p style='color: #28a745;'>✅ Database connected successfully</p>";
        $checks_passed++;
        $test_conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: #dc3545;'>❌ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Check 2: Required folders
echo "<div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h3><i class='fas fa-folder'></i> Required Folders</h3>";

$folders = [
    'customer' => 'Customer Portal',
    'admin' => 'Admin Panel',
    'delivery' => 'Delivery Portal',
    'backend' => 'Backend Logic',
    'css' => 'Stylesheets',
    'js' => 'JavaScript',
    'uploads' => 'Uploads'
];

foreach ($folders as $folder => $name) {
    $checks_total++;
    if (is_dir($folder)) {
        echo "<p style='color: #28a745;'>✅ $name ($folder/) exists</p>";
        $checks_passed++;
    } else {
        echo "<p style='color: #dc3545;'>❌ $name ($folder/) missing</p>";
    }
}

echo "</div>";

// Check 3: Database tables
echo "<div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h3><i class='fas fa-table'></i> Database Tables</h3>";

require_once 'backend/includes/config.php';
$result = $conn->query("SHOW TABLES FROM " . DB_NAME);
$table_count = $result->num_rows;

$checks_total += 2;

if ($table_count >= 15) {
    echo "<p style='color: #28a745;'>✅ All tables created ($table_count tables found)</p>";
    $checks_passed++;
} else {
    echo "<p style='color: #dc3545;'>❌ Tables missing (only $table_count found, need at least 15)</p>";
}

// Check sample data
$admin_check = $conn->query("SELECT COUNT(*) as count FROM admins");
$admin_count = $admin_check->fetch_assoc()['count'];
if ($admin_count > 0) {
    echo "<p style='color: #28a745;'>✅ Admin account exists ($admin_count admins)</p>";
    $checks_passed++;
} else {
    echo "<p style='color: #dc3545;'>❌ No admin account found</p>";
}

echo "</div>";

// Check 4: File permissions
echo "<div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h3><i class='fas fa-lock'></i> File Permissions</h3>";

$checks_total += 2;

if (is_writable('uploads')) {
    echo "<p style='color: #28a745;'>✅ uploads/ folder is writable</p>";
    $checks_passed++;
} else {
    echo "<p style='color: #dc3545;'>❌ uploads/ folder is not writable</p>";
}

if (is_writable('backend/includes/config.php')) {
    echo "<p style='color: #28a745;'>✅ config.php is writable</p>";
    $checks_passed++;
} else {
    echo "<p style='color: #ffc107;'>⚠️ config.php is read-only (this is OK)</p>";
    $checks_passed++;
}

echo "</div>";

// Check 5: Essential files
echo "<div style='background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h3><i class='fas fa-file'></i> Essential Files</h3>";

$files = [
    'backend/classes/Database.php' => 'Database class',
    'backend/classes/Customer.php' => 'Customer class',
    'backend/classes/Admin.php' => 'Admin class',
    'css/style.css' => 'Main stylesheet',
    'js/cart.js' => 'Cart JavaScript',
    'customer/index.php' => 'Customer home',
    'admin/login.php' => 'Admin login',
    'delivery/login.php' => 'Delivery login'
];

foreach ($files as $file => $name) {
    $checks_total++;
    if (file_exists($file)) {
        echo "<p style='color: #28a745;'>✅ $name exists</p>";
        $checks_passed++;
    } else {
        echo "<p style='color: #dc3545;'>❌ $name missing</p>";
    }
}

echo "</div>";

// Summary
echo "<div style='background: linear-gradient(135deg, #FF6B35, #F7931E); color: white; padding: 30px; margin: 30px 0; border-radius: 5px; text-align: center;'>";
echo "<h2 style='margin: 0 0 20px 0;'>Verification Results</h2>";

$percentage = ($checks_passed / $checks_total) * 100;
echo "<h1 style='margin: 0 0 20px 0; font-size: 48px;'>" . round($percentage) . "%</h1>";
echo "<p style='font-size: 18px;'><strong>$checks_passed</strong> of <strong>$checks_total</strong> checks passed</p>";

if ($percentage == 100) {
    echo "<p style='font-size: 20px; color: #d4edda;'>✅ <strong>Installation Complete!</strong></p>";
    echo "<p style='margin-top: 20px;'>";
    echo "<a href='customer/index.php' style='color: white; text-decoration: none; background: rgba(0,0,0,0.2); padding: 10px 20px; border-radius: 5px; display: inline-block; margin: 0 10px;'>Visit Customer Portal</a>";
    echo "<a href='admin/login.php' style='color: white; text-decoration: none; background: rgba(0,0,0,0.2); padding: 10px 20px; border-radius: 5px; display: inline-block; margin: 0 10px;'>Admin Login</a>";
    echo "</p>";
} else {
    echo "<p style='font-size: 16px; color: #fff3cd;'>⚠️ <strong>Please fix the errors above</strong></p>";
    echo "<p style='margin-top: 20px; font-size: 14px;'>Check README.md or QUICKSTART.md for help</p>";
}

echo "</div>";

echo "</div></body></html>";
?>
