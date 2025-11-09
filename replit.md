# StyleHub - E-Commerce Clothing Store

## Overview
StyleHub is a full-featured e-commerce website for selling clothing and accessories. Built with HTML, CSS, JavaScript, PHP, and PostgreSQL, it provides a complete shopping experience with an admin management panel.

**Project Type:** E-Commerce Website  
**Tech Stack:** HTML5, CSS3, JavaScript, PHP 8.2, PostgreSQL  
**Created:** November 9, 2025  
**Status:** Production Ready

## Features

### Customer Features
- **Product Catalog**: Browse clothing items across multiple categories (Men, Women, Kids, Accessories)
- **Product Filtering**: Filter products by category
- **Shopping Cart**: Add items to cart, adjust quantities, remove items
- **Checkout System**: Submit orders with customer information
- **Responsive Design**: Mobile-friendly interface

### Admin Features
- **Secure Login**: Admin authentication system
- **Dashboard**: Overview of products, orders, and revenue
- **Product Management**: Add, edit, and delete products
- **Order Management**: View orders, update order status, view order details
- **Inventory Management**: Track stock levels

## Project Structure

```
.
├── index.php                 # Home page
├── products.php              # Product catalog page
├── cart.php                  # Shopping cart page
├── config/
│   └── database.php          # Database connection
├── api/
│   ├── products.php          # Products API (CRUD operations)
│   └── orders.php            # Orders API (CRUD operations)
├── admin/
│   ├── login.php             # Admin login page
│   ├── dashboard.php         # Admin dashboard
│   ├── manage-products.php   # Product management interface
│   ├── manage-orders.php     # Order management interface
│   └── logout.php            # Admin logout
├── css/
│   └── style.css             # Main stylesheet
└── js/
    ├── main.js               # Main JavaScript (product display, cart)
    └── cart.js               # Shopping cart functionality
```

## Database Schema

### Products Table
- `id`: Serial primary key
- `name`: Product name
- `description`: Product description
- `price`: Decimal price
- `image`: Emoji or icon representing product
- `category`: men/women/kids/accessories
- `sizes`: Available sizes (comma-separated)
- `stock`: Inventory quantity
- `created_at`: Timestamp

### Orders Table
- `id`: Serial primary key
- `customer_name`: Customer's name
- `email`: Customer email
- `phone`: Customer phone number
- `address`: Shipping address
- `items`: JSON array of ordered items
- `total`: Order total amount
- `status`: Order status (pending/processing/shipped/delivered/cancelled)
- `order_date`: Timestamp

### Admin Users Table
- `id`: Serial primary key
- `username`: Admin username
- `password`: Hashed password
- `created_at`: Timestamp

## Admin Access

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

**Admin URL:** `http://[your-domain]/admin/login.php`

## Environment Variables

The following environment variables are automatically configured:
- `DATABASE_URL`: PostgreSQL connection string
- `PGHOST`: PostgreSQL host
- `PGPORT`: PostgreSQL port
- `PGDATABASE`: Database name
- `PGUSER`: Database user
- `PGPASSWORD`: Database password
- `SESSION_SECRET`: Session encryption key

## Development Workflow

### Starting the Server
The PHP development server runs automatically on port 5000:
```bash
php -S 0.0.0.0:5000
```

### Accessing the Site
- **Customer Store:** `http://[your-domain]/`
- **Admin Panel:** `http://[your-domain]/admin/login.php`

## Recent Changes

**November 9, 2025:**
- Initial project setup with complete e-commerce functionality
- Implemented product catalog with 12 sample clothing items
- Created admin panel with full CRUD operations
- Added shopping cart with local storage
- Implemented order management system
- Set up PostgreSQL database with all tables
- Configured PHP 8.2 development server
- Implemented production-ready security:
  - Admin authentication on all mutating endpoints
  - CSRF protection for admin operations
  - Server-side order validation and price calculation
  - Stock verification during checkout

## Key Features Implementation

### Shopping Cart
- Uses browser's localStorage for persistence
- Add/remove items
- Quantity adjustment
- Real-time total calculation
- Order submission with customer details

### Admin Panel
- Secure session-based authentication
- Dashboard with statistics (products, orders, revenue)
- Product management (add, edit, delete)
- Order management (view details, update status)
- Responsive data tables

### Security Features
- **Password Hashing**: Bcrypt hashing for admin passwords
- **Session-Based Authentication**: Secure admin login with session management
- **SQL Injection Protection**: PDO prepared statements throughout
- **CSRF Protection**: Token-based CSRF protection on all admin endpoints
- **Authorization Controls**: Admin-only access to mutating API endpoints
- **Server-Side Validation**: Order totals recalculated server-side from database prices
- **Stock Validation**: Server checks inventory before accepting orders
- **Input Sanitization**: All user inputs validated and sanitized
- **Security Headers**: Cache-Control, X-Content-Type-Options, X-Frame-Options, X-XSS-Protection

## Sample Products

The database includes 12 sample clothing products:
- Men's clothing (t-shirts, jeans, blazers, coats)
- Women's clothing (dresses, yoga pants, skirts)
- Kids' clothing (t-shirts, hoodies)
- Accessories (handbags, sneakers, belts)

## Future Enhancements

Potential improvements for next phase:
- Payment gateway integration (Stripe/PayPal)
- User accounts for customers
- Order tracking system
- Email notifications
- Product reviews and ratings
- Advanced search and filtering
- Product image uploads
- Sales analytics dashboard
- Discount codes and promotions

## Notes

- The website uses PostgreSQL instead of MySQL as it's the built-in database in the Replit environment
- All API endpoints use RESTful design patterns
- The shopping cart data is stored in browser localStorage
- Admin password is hashed using PHP's password_hash() function
- The website is fully responsive and mobile-friendly
