<?php
require_once '../backend/includes/init.php';

$page_title = 'Home';

require 'header.php';

$food = new Food($conn);
$featured_foods = $food->getFeatured(8);
?>

<div class="hero">
    <div class="container">
        <h1>Welcome to Pizza & Burger Hub</h1>
        <p>Order delicious pizzas and burgers online and get them delivered to your doorstep in just 30 minutes!</p>
        <a href="menu.php" class="btn-primary">Order Now</a>
    </div>
</div>

<div class="container" style="margin-top: 60px;">
    <section style="margin-bottom: 60px; text-align: center;">
        <h2 style="font-size: 32px; margin-bottom: 40px;">Why Choose Us?</h2>
        
        <div class="row cols-4">
            <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
                <div style="font-size: 40px; color: #FF6B35; margin-bottom: 15px;">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Fast Delivery</h3>
                <p>Get your order delivered within 30 minutes</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
                <div style="font-size: 40px; color: #FF6B35; margin-bottom: 15px;">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>Fresh Ingredients</h3>
                <p>We use only the freshest and finest ingredients</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
                <div style="font-size: 40px; color: #FF6B35; margin-bottom: 15px;">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Secure Payment</h3>
                <p>Multiple secure payment options available</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
                <div style="font-size: 40px; color: #FF6B35; margin-bottom: 15px;">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Our customer support team is always here to help</p>
            </div>
        </div>
    </section>

    <section style="margin-bottom: 60px;">
        <h2 style="font-size: 32px; margin-bottom: 30px; text-align: center;">Featured Items</h2>
        
        <?php if ($featured_foods['success'] && !empty($featured_foods['data'])): ?>
            <div class="row cols-4">
                <?php foreach ($featured_foods['data'] as $food_item): ?>
                    <div class="food-card">
                        <div class="food-image">
                            <img src="<?php echo food_image_url($food_item['image_path']); ?>" alt="<?php echo $food_item['name']; ?>" onerror="this.src='<?php echo SITE_URL; ?>images/placeholder.png'">
                            <?php if ($food_item['discount_percentage'] > 0): ?>
                                <div class="food-badge">-<?php echo $food_item['discount_percentage']; ?>%</div>
                            <?php endif; ?>
                        </div>
                        <div class="food-info">
                            <div class="food-name"><?php echo $food_item['name']; ?></div>
                            <div class="food-category"><?php echo $food_item['category_name']; ?></div>
                            <div class="food-description"><?php echo substr($food_item['description'], 0, 50); ?>...</div>
                            <div class="food-rating">
                                <i class="fas fa-star"></i> <?php echo $food_item['rating']; ?>/5
                            </div>
                            <div class="food-footer">
                                <div class="food-price">
                                    <?php if ($food_item['discount_percentage'] > 0): ?>
                                        <span class="food-price-original">PKR <?php echo $food_item['price']; ?></span>
                                    <?php endif; ?>
                                    PKR <?php echo $food_item['final_price']; ?>
                                </div>
                                <button class="btn-add-cart" onclick="Cart.addItem(<?php echo $food_item['id']; ?>, '<?php echo addslashes($food_item['name']); ?>', <?php echo $food_item['final_price']; ?>, '<?php echo $food_item['image_path']; ?>')">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section style="background: linear-gradient(135deg, #FF6B35, #F7931E); color: white; padding: 40px; border-radius: 10px; margin-bottom: 60px; text-align: center;">
        <h2>Special Offer</h2>
        <p style="font-size: 18px; margin: 20px 0;">Get 20% OFF on your first order!</p>
        <p>Use code: <strong>WELCOME20</strong></p>
        <a href="menu.php" class="btn-primary" style="margin-top: 20px;">Order Now</a>
    </section>
</div>

<?php require 'footer.php'; ?>
