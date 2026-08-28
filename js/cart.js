// Cart Management Functions

const Cart = {
    // Add item to cart
    addItem(foodId, foodName, price, image) {
        fetch('cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add',
                food_id: foodId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Item added to cart successfully!');
                updateCartCount();
            } else {
                showAlert('danger', data.message || 'Failed to add item to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred. Please try again.');
        });
    },

    // Update cart item quantity
    updateQuantity(cartItemId, quantity) {
        if (quantity < 1) {
            this.removeItem(cartItemId);
            return;
        }

        fetch('cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update',
                cart_item_id: cartItemId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlert('danger', data.message || 'Failed to update cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred. Please try again.');
        });
    },

    // Remove item from cart
    removeItem(cartItemId) {
        if (confirm('Are you sure you want to remove this item?')) {
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'remove',
                    cart_item_id: cartItemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showAlert('danger', data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
            });
        }
    },

    // Clear cart
    clearCart() {
        if (confirm('Are you sure you want to clear your cart?')) {
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'clear'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showAlert('danger', data.message || 'Failed to clear cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
            });
        }
    }
};

// Update cart count in header
function updateCartCount() {
    fetch('cart-api.php?action=count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.count || 0;
            }
        });
}

// Show alert message
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 16px;">&times;</button>`;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    } else {
        document.body.insertBefore(alertDiv, document.body.firstChild);
    }

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PK', {
        style: 'currency',
        currency: 'PKR'
    }).format(amount);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
