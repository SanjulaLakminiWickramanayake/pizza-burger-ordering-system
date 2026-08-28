<?php
require_once '../backend/includes/init.php';

// Handle cart API requests
header('Content-Type: application/json');

if (!is_logged_in('customer')) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$cart = new Cart($conn);

$action = $_GET['action'] ?? ($_POST['action'] ?? 'get');

switch($action) {
    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $cart->addItem($customer_id, $data['food_id'], $data['quantity']);
        echo json_encode($result);
        break;
        
    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $cart->updateItem($data['cart_item_id'], $data['quantity']);
        echo json_encode($result);
        break;
        
    case 'remove':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $cart->removeItem($data['cart_item_id']);
        echo json_encode($result);
        break;
        
    case 'clear':
        $result = $cart->clear($customer_id);
        echo json_encode($result);
        break;
        
    case 'count':
        $cart_data = $cart->getCart($customer_id);
        if ($cart_data['success']) {
            echo json_encode(['success' => true, 'count' => $cart_data['data']['total_items']]);
        } else {
            echo json_encode(['success' => true, 'count' => 0]);
        }
        break;
        
    case 'get':
    default:
        $cart_data = $cart->getCart($customer_id);
        $items = $cart->getItems($customer_id);
        
        echo json_encode([
            'success' => true,
            'cart' => $cart_data['data'] ?? [],
            'items' => $items['data'] ?? []
        ]);
        break;
}
?>
