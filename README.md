# MarketNest — Multi-Vendor E-Commerce Platform

A modern multi-vendor e-commerce platform built with **Laravel 12**, **Inertia.js**, and **ReactJS**.

## Features

### Customer

- Browse products (guest)
- Add to cart (guest, synced on login)
- Must authenticate before checkout
- Select product variants on checkout
- Place orders
- Leave reviews after ordering

### Vendor

- Email-verified registration
- Upload/manage products with variants
- View own orders & revenue dashboard
- Product images via Cloudinary

### Admin

- Oversee all users, vendors, and products
- Ban products / suspend vendors
- Review & approve vendor applications
- Full system analytics dashboard

---

## Tech Stack

| Layer        | Tech                     |
| ------------ | ------------------------ |
| Backend      | Laravel 12               |
| Frontend     | React (JS, no TS)        |
| Bridge       | Inertia.js v2            |
| Styling      | Tailwind CSS v4          |
| File Storage | Cloudinary               |
| Auth         | Laravel Breeze (Inertia) |
| DB           | MySQL / PostgreSQL       |

---

## Installation

```bash
# 1. Clone & install PHP deps
composer install

# 2. Install JS deps
npm install

# 3. Copy env
cp .env.example .env
php artisan key:generate

# 4. Configure .env
# - DB credentials
# - Cloudinary credentials (CLOUDINARY_URL)
# - Mail credentials (for email verification)

# 5. Migrate & seed
php artisan migrate --seed

# 6. Run dev servers
php artisan serve
npm run dev
```

---

## Directory Structure (Key Files)

```
app/
  Http/Controllers/
    Auth/                    # Auth controllers
    Admin/                   # Admin dashboard controllers
    Vendor/                  # Vendor dashboard controllers
    Shop/                    # Customer-facing controllers
  Models/
    User.php
    Product.php
    ProductVariant.php
    Order.php
    OrderItem.php
    Cart.php
    CartItem.php
    Review.php
    Category.php
    VendorProfile.php

resources/js/
  Pages/
    Auth/                    # Login, Register, Verify Email
    Admin/                   # Admin dashboard pages
    Vendor/                  # Vendor dashboard pages
    Shop/                    # Customer-facing pages
  Components/
    Layout/                  # Navbar, Footer, Sidebar
    Shop/                    # Product cards, cart drawer
    Admin/                   # Admin-specific UI
    Vendor/                  # Vendor-specific UI
```

---

## Default Accounts (after seeding)

| Role     | Email                   | Password |
| -------- | ----------------------- | -------- |
| Admin    | admin@marketnest.com    | password |
| Vendor   | vendor@marketnest.com   | password |
| Customer | customer@marketnest.com | password |

---

## Cloudinary Setup

Add to `.env`:

```
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

Install package: `composer require cloudinary-labs/cloudinary-laravel`
