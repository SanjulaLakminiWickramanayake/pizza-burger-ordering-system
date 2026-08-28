<?php
require_once '../backend/includes/init.php';

require_login('customer');

$page_title = 'Shopping Cart';

require 'header.php';

$customer_id = $_SESSION['customer_id'];
$cart = new Cart($conn);

$cart_data = $cart->getCart($customer_id);
$items_result = $cart->getItems($customer_id);

$cart_items = [];
if ($items_result['success']) {
    $cart_items = $items_result['data'];
}
?>

<div class="container" style="margin-top: 30px; margin-bottom: 60px;">
    <h1 style="margin-bottom: 30px;">Shopping Cart</h1>
    
    <?php if (!empty($cart_items)): ?>
        <div class="row cols-2" style="gap: 30px;">
            <div>
                <div class="cart-container">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="<?php echo food_image_url($item['image_path']); ?>" alt="<?php echo $item['name']; ?>" onerror="this.src='<?php echo SITE_URL; ?>images/placeholder.png'">
                            </div>
                            <div class="cart-item-info">
                                <div class="cart-item-name"><?php echo $item['name']; ?></div>
                                <div class="cart-item-price">PKR <?php echo number_format($item['unit_price'], 2); ?></div>
                            </div>
                            <div class="quantity-control">
                                <button class="qty-btn" onclick="Cart.updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                                <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                                <button class="qty-btn" onclick="Cart.updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                                <button class="qty-btn" style="background: #dc3545; color: white;" onclick="Cart.removeItem(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div style="text-align: right; min-width: 100px;">
                                <strong>PKR <?php echo number_format($item['total_price'], 2); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div>
                <div class="cart-total" style="background: white; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 20px;">Order Summary</h3>
                    
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>PKR <?php echo number_format($cart_data['data']['subtotal'], 2); ?></span>
                    </div>
                    
                    <div class="total-row">
                        <span>Tax (5%):</span>
                        <span>PKR <?php echo number_format($cart_data['data']['tax'], 2); ?></span>
                    </div>
                    
                    <div class="total-row">
                        <span>Delivery Charge:</span>
                        <span>
                            <?php if ($cart_data['data']['delivery_charge'] == 0): ?>
                                <span style="color: var(--success-color);">FREE</span>
                            <?php else: ?>
                                PKR <?php echo number_format($cart_data['data']['delivery_charge'], 2); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="total-row">
                        <span>Total:</span>
                        <span style="color: var(--primary-color);">PKR <?php echo number_format($cart_data['data']['total'], 2); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn-secondary" style="width: 100%; text-align: center; padding: 12px; margin-top: 20px; text-decoration: none; display: block;">
                        Proceed to Checkout
                    </a>
                    
                    <a href="menu.php" class="btn-primary" style="width: 100%; text-align: center; padding: 12px; margin-top: 10px; text-decoration: none; display: block;">
                        Continue Shopping
                    </a>
                    
                    <button onclick="Cart.clearCart()" class="btn-primary" style="width: 100%; padding: 12px; margin-top: 10px; background: #dc3545;">
                        Clear Cart
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 60px; text-align: center; border-radius: 5px;">
            <h3 style="font-size: 24px; margin-bottom: 20px;">Your cart is empty</h3>
            <p style="margin-bottom: 30px;">Start by adding some delicious items from our menu!</p>
            <a href="menu.php" class="btn-secondary" style="padding: 12px 30px; text-decoration: none; color: white;">Browse Menu</a>
        </div>
    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>
