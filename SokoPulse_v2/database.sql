CREATE DATABASE IF NOT EXISTS sokopulse CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;USE sokopulse;
DROP TABLE IF EXISTS order_items;DROP TABLE IF EXISTS orders;DROP TABLE IF EXISTS products;DROP TABLE IF EXISTS customers;DROP TABLE IF EXISTS admins;
CREATE TABLE admins(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(80) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,active TINYINT(1) NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE customers(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,email VARCHAR(150) NOT NULL UNIQUE,phone VARCHAR(30) NOT NULL,address VARCHAR(255),password_hash VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE products(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,description TEXT,price DECIMAL(12,2) NOT NULL,stock INT NOT NULL DEFAULT 0,image VARCHAR(255) DEFAULT 'assets/placeholder.svg',active TINYINT(1) NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE orders(id INT AUTO_INCREMENT PRIMARY KEY,customer_id INT NULL,customer_name VARCHAR(150) NOT NULL,phone VARCHAR(30) NOT NULL,email VARCHAR(150),delivery_address VARCHAR(255),total DECIMAL(12,2) NOT NULL,payment_status ENUM('Pending','Paid','Failed') NOT NULL DEFAULT 'Pending',order_status ENUM('Pending','Processing','Ready','Shipped','Completed','Cancelled') NOT NULL DEFAULT 'Pending',merchant_request_id VARCHAR(120),checkout_request_id VARCHAR(120),mpesa_receipt VARCHAR(100),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,CONSTRAINT fk_orders_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,INDEX(customer_id),INDEX(checkout_request_id)) ENGINE=InnoDB;
CREATE TABLE order_items(id INT AUTO_INCREMENT PRIMARY KEY,order_id INT NOT NULL,product_id INT NULL,product_name VARCHAR(150) NOT NULL,quantity INT NOT NULL,unit_price DECIMAL(12,2) NOT NULL,subtotal DECIMAL(12,2) NOT NULL,CONSTRAINT fk_items_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,CONSTRAINT fk_items_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL) ENGINE=InnoDB;

INSERT INTO admins(username,password_hash) VALUES('admin','$2y$12$9M4v3SQTKbNwh3dO08u5q.daaTTHSqY2i.yvaCekVrGJEshQ.gUI2');
INSERT INTO products(name,description,price,stock,image) VALUES
('Wireless Headphones','Comfortable wireless headphones.',2500,15,'assets/placeholder.svg'),
('Smart Watch','Affordable smart watch.',3500,10,'assets/placeholder.svg'),
('USB-C Cable','Durable USB-C cable.',800,30,'assets/placeholder.svg'),
('Laptop Backpack','Padded laptop backpack.',2200,12,'assets/placeholder.svg');
