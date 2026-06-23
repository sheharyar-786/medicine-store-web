# HealthCare Store — Online Medicine Store

A PHP + MySQL online pharmacy application built for XAMPP.

## Features

- Customer storefront with search, categories, and product details
- Session-based shopping cart
- User registration and login
- Checkout with prescription upload
- Admin dashboard for products, categories, and orders

## Requirements

- PHP 7.4+
- MySQL / MariaDB
- XAMPP (or Apache + PHP + MySQL)

## Setup

1. Clone this repo into your XAMPP `htdocs` folder:
   ```
   git clone https://github.com/sheharyar-786/medicine-store-web.git medicine-store
   ```

2. Start Apache and MySQL in XAMPP.

3. Import the database:
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Import `sql/databse.sql`

4. Configure database connection in `includes/db_connect.php` if needed (default: `root` with no password).

5. Visit `http://localhost/medicine-store`

## Project Structure

```
├── admin/           Admin panel pages
├── actions/         Form handlers (login, cart, orders)
├── assets/          CSS, JS, uploads
├── includes/        Shared PHP (header, footer, DB)
├── sql/             Database schema
└── *.php            Storefront pages
```

## Tech Stack

- PHP (MySQLi)
- MySQL
- HTML / CSS / JavaScript
- Font Awesome icons
