# SokoPulse v2

A rebuilt PHP/MySQL e-commerce coursework project for XAMPP.

## Included
- Customer registration/login/logout
- Login required before checkout
- Customer account and order history
- Products and session cart
- Admin login/dashboard
- Admin product add/edit/remove
- Admin order status management
- MySQL schema with customers, admins, products, orders and order_items
- Daraja OAuth + M-PESA Express/STK Push + callback handling

## Install
1. Extract to `C:\xampp\htdocs\SokoPulse`.
2. Start Apache and MySQL.
3. Open `http://localhost/phpmyadmin`.
4. Import `database.sql` into a database named `sokopulse`.
5. Open `http://localhost/SokoPulse/`.

## Admin
Username: `admin`
Password: `Admin@12345`

## M-PESA
Open `config.php` and enter your Daraja Consumer Key, Consumer Secret, Shortcode and STK Push Passkey. The project uses the Daraja sandbox STK Push endpoint by default. The callback is `mpesa_callback.php`. A local `localhost` callback cannot be reached by Safaricom over the public internet, so callback-based status updates require a reachable development URL.

Do not put the Consumer Secret in the Passkey field.
