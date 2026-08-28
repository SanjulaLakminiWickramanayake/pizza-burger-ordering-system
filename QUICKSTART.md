# 🚀 Quick Start Guide

## Fastest Way to Get Started (5 Minutes)

### Prerequisites
✅ XAMPP installed and running
✅ Apache and MySQL services started

### Step 1: Import Database (1 minute)
```
1. Open http://localhost/phpmyadmin
2. Create database: food_delivery_system
3. Go to Import tab
4. Select database.sql from project folder
5. Click Go
```

### Step 2: Verify Installation (1 minute)
```
Open in browser:
- http://localhost/food/customer/index.php (Customer portal)
- http://localhost/food/admin/login.php (Admin panel)
- http://localhost/food/delivery/login.php (Delivery portal)
```

### Step 3: Test with Default Accounts

**Admin Login:**
```
Email: admin@fooddelivery.com
Password: The bcrypt hash in database - use admin123
```

**Sample Customer:**
```
Email: ahmed@example.com
Password: Use the hash in database
```

**Browse as Guest:**
- Visit http://localhost/food/
- Browse menu
- Add items to cart (requires login to checkout)

---

## 📖 Understanding the Project Structure

### Customer Journey
```
index.php (Home) 
  → menu.php (Browse/Search Food)
    → login.php (Create Account)
      → cart.php (Add Items)
        → checkout.php (Place Order)
          → order-tracking.php (Track Order)
```

### Admin Functions
```
admin/login.php
  → admin/dashboard.php (Statistics)
    → manage-foods.php (Add/Edit Food)
    → manage-orders.php (Handle Orders)
    → manage-customers.php (Customer Management)
    → manage-delivery.php (Assign Deliveries)
    → reports.php (View Reports)
```

### Delivery Operations
```
delivery/login.php
  → delivery/dashboard.php (View Assigned Orders)
    → deliveries.php (Manage Current Deliveries)
      → delivery-detail.php (Update Status & Complete)
```

---

## 🔑 Database Passwords

All user passwords in sample data are bcrypt hashes. To create new accounts:

### For Testing - Use These Credentials

**Admin:**
- Email: admin@fooddelivery.com
- Password: (Check hash in database or use admin123)

**Customer Test:**
- Name: Test User
- Email: test@example.com
- Password: Test123!

**Delivery Staff Test:**
- Email: delivery@test.com
- Name: Test Rider

---

## 🎯 What's Already Built

✅ Complete Database Schema (17 tables)
✅ Customer Portal (Registration, Menu, Cart, Checkout, Tracking)
✅ Admin Dashboard (Statistics, Food Management)
✅ Delivery Portal (Dashboard, Order Assignment)
✅ Authentication System (Secure login/logout)
✅ Responsive Design (Mobile, Tablet, Desktop)
✅ Shopping Cart with Real-time Calculations
✅ Order Tracking System
✅ Payment Methods (Cash, Bank, Online)
✅ Security (Password hashing, SQL injection prevention)

---

## 📝 What You Can Still Add

### Admin Pages (Templates provided):
- [ ] Manage Foods - CRUD interface
- [ ] Manage Orders - Filter and assign
- [ ] Manage Customers - Search and block
- [ ] Manage Inventory - Stock tracking
- [ ] Generate Reports - PDF exports
- [ ] System Settings - Configuration UI

### Features to Enhance:
- [ ] Email notifications (PHPMailer)
- [ ] Payment gateway (Stripe/PayPal)
- [ ] SMS alerts (Twilio)
- [ ] Google Maps integration
- [ ] Invoice PDF generation
- [ ] Advanced analytics
- [ ] Coupon system

---

## 🔧 Useful Commands

### Reset Admin Password
```
UPDATE admins SET password='$2y$10$...' WHERE email='admin@fooddelivery.com';
```

### Clear All Orders
```
DELETE FROM orders;
DELETE FROM cart_items;
DELETE FROM carts;
```

### Reset Database
```
DROP DATABASE food_delivery_system;
(Then reimport database.sql)
```

---

## 🐛 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| Cannot connect to database | Check MySQL is running, verify credentials |
| Pages show 404 | Verify folder structure, check SITE_URL in config.php |
| CSS/JS not loading | Clear browser cache, check paths |
| Login doesn't work | Check database import was successful |
| Images not showing | Verify uploads folder exists and is writable |

---

## 📞 Common Questions

**Q: How do I add a new food item?**
A: Create the admin/manage-foods.php page using the Food class provided.

**Q: How do I setup email notifications?**
A: Install PHPMailer via Composer and integrate in Order class.

**Q: Can I change the payment methods?**
A: Yes, edit the Payment class and checkout.php form.

**Q: How do I assign orders to delivery staff?**
A: Admin/manage-orders.php has the assignDeliveryPerson() method ready.

---

## 🎓 Learning Resources

1. **Database Design**: See database.sql for schema
2. **Backend Logic**: Check backend/classes/ for all methods
3. **Forms**: Review customer/checkout.php for validation
4. **API Calls**: See customer/cart-api.php for AJAX examples

---

**Ready to Customize?** Start with admin/manage-foods.php - it's the easiest!

Created: 2024
Version: 1.0
