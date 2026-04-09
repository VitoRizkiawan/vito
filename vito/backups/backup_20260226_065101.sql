

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO order_details (id,order_id,product_id,price,quantity,created_at) VALUES ('1','7','6','1000000.00','1','2026-02-26 12:30:50');
INSERT INTO order_details (id,order_id,product_id,price,quantity,created_at) VALUES ('2','8','6','1000000.00','1','2026-02-26 12:31:52');
INSERT INTO order_details (id,order_id,product_id,price,quantity,created_at) VALUES ('3','9','6','1000000.00','1','2026-02-26 12:37:35');
INSERT INTO order_details (id,order_id,product_id,price,quantity,created_at) VALUES ('4','10','6','1000000.00','1','2026-02-26 12:48:24');
INSERT INTO order_details (id,order_id,product_id,price,quantity,created_at) VALUES ('5','11','6','1000000.00','1','2026-02-26 12:49:39');


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `payment_method` enum('Transfer','COD') NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `order_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO orders (id,user_id,address,payment_method,payment_proof,total_price,status,order_date,created_at) VALUES ('7','15','qq','COD','','1000000.00','Pending','2026-02-26 06:30:50','2026-02-26 12:30:50');
INSERT INTO orders (id,user_id,address,payment_method,payment_proof,total_price,status,order_date,created_at) VALUES ('8','15','2qw','Transfer','proofs/699fdac865d60_1772083912.png','1000000.00','Pending','2026-02-26 06:31:52','2026-02-26 12:31:52');
INSERT INTO orders (id,user_id,address,payment_method,payment_proof,total_price,status,order_date,created_at) VALUES ('9','15','aa','COD','','1000000.00','Pending','2026-02-26 06:37:35','2026-02-26 12:37:35');
INSERT INTO orders (id,user_id,address,payment_method,payment_proof,total_price,status,order_date,created_at) VALUES ('10','15','2qw','Transfer','proofs/699fdea8ad0ee_1772084904.png','1000000.00','Pending','2026-02-26 06:48:24','2026-02-26 12:48:24');
INSERT INTO orders (id,user_id,address,payment_method,payment_proof,total_price,status,order_date,created_at) VALUES ('11','15','qq','COD','','1000000.00','Pending','2026-02-26 06:49:39','2026-02-26 12:49:39');


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products (id,name,category,price,description,stock,image) VALUES ('6','Puma','Sepatu','1000000','Puma keren','1','699f16b89077c.jpg');


CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `proof_payment` varchar(255) DEFAULT NULL,
  `receipt` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qty` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users (id,username,email,password,role,created_at) VALUES ('13','admin','admin@gmail.com','$2y$10$ptKX1FjvjAt/rLh1YoErteuD885kVwhi59y6plWaCYZ/HcpwhQxTO','admin','2026-02-25 10:44:51');
INSERT INTO users (id,username,email,password,role,created_at) VALUES ('14','raihan','raihanazka1@gmail.com','$2y$10$5e.WnYW2dEwqCpkaBQwHdulOyobBPj5Up6HoAJ/DFkm.uuSOUwau6','petugas','2026-02-25 10:57:31');
INSERT INTO users (id,username,email,password,role,created_at) VALUES ('15','vito','vito@gmail.com','$2y$10$2fIRl4mmnnFnhCJFJ7us6ew/tLqIppAuxCuz5zj38hRl34Qc/25Ka','','2026-02-25 22:12:24');
