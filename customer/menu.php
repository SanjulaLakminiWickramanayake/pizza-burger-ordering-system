<?php
require_once '../backend/includes/init.php';

$page_title = 'Menu';

require 'header.php';

$food = new Food($conn);
$food_obj = new Food($conn);

// Get categories
$categories = $food_obj->getCategories();

// Filter by category if specified
$category_id = $_GET['category'] ?? 0;
$search = $_GET['search'] ?? '';

if ($search) {
    $foods_result = $food_obj->search($search);
} elseif ($category_id) {
    $foods_result = $food_obj->getByCategory($category_id);
} else {
    $foods_result = $food_obj->getAll();
}
?>

<div class="container">
    <h1 style="margin-top: 30px; margin-bottom: 30px; font-size: 32px;">Our Menu</h1>
    
    <div style="background: white; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
        <form method="GET" style="display: flex; gap: 15px;">
            <input type="text" name="search" placeholder="Search food items..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 10px;">
            <button type="submit" class="btn-secondary">Search</button>
        </form>
    </div>
    
    <div style="margin-bottom: 30px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="menu.php" class="btn-secondary" style="padding: 8px 16px; text-decoration: none; color: white;">All Categories</a>
            <?php if ($categories['success'] && !empty($categories['data'])): ?>
                <?php foreach ($categories['data'] as $cat): ?>
                    <a href="?category=<?php echo $cat['id']; ?>" class="btn-secondary" style="padding: 8px 16px; text-decoration: none; color: white; background: <?php echo ($category_id == $cat['id']) ? '#F7931E' : '#FF6B35'; ?>;">
                        <?php echo $cat['name']; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($foods_result['success'] && !empty($foods_result['data'])): ?>
        <div class="row cols-4">
            <?php foreach ($foods_result['data'] as $item): ?>
                <div class="food-card">
                    <div class="food-image">
                        <img src="<?php echo food_image_url($item['image_path']); ?>" alt="<?php echo $item['name']; ?>" onerror="this.src='<?php echo SITE_URL; ?>images/placeholder.png'">
                        <?php if ($item['discount_percentage'] > 0): ?>
                            <div class="food-badge">-<?php echo $item['discount_percentage']; ?>%</div>
                        <?php endif; ?>
                    </div>
                    <div class="food-info">
                        <div class="food-name"><?php echo $item['name']; ?></div>
                        <div class="food-category"><?php echo $item['category_name']; ?></div>
                        <div class="food-description"><?php echo substr($item['description'], 0, 50); ?>...</div>
                        <div class="food-rating">
                            <i class="fas fa-star"></i> <?php echo $item['rating']; ?>/5
                        </div>
                        <div style="margin-bottom: 10px; font-size: 12px; color: #666;">
                            <?php echo $item['stock'] > 0 ? 'In Stock' : '<span style="color: #dc3545;">Out of Stock</span>'; ?>
                        </div>
                        <div class="food-footer">
                            <div class="food-price">
                                <?php if ($item['discount_percentage'] > 0): ?>
                                    <span class="food-price-original">PKR <?php echo $item['price']; ?></span>
                                <?php endif; ?>
                                PKR <?php echo $item['final_price']; ?>
                            </div>
                            <?php if ($item['stock'] > 0): ?>
                                <button class="btn-add-cart" onclick="Cart.addItem(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>', <?php echo $item['final_price']; ?>, '<?php echo $item['image_path']; ?>')">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
                                    Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 40px; text-align: center; border-radius: 5px;">
            <h3>No items found</h3>
            <p>Try searching for different items or browse our categories.</p>
        </div>
    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>
