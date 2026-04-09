-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2026 at 10:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buyzoneviriz`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `shipping_expedition` varchar(50) DEFAULT NULL,
  `shipping_type` varchar(50) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `total_price` int(11) DEFAULT NULL,
  `total_items` int(11) DEFAULT 1,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tracking_status` varchar(50) DEFAULT 'Menunggu Pembayaran'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `order_date`, `address`, `payment_method`, `payment_proof`, `shipping_expedition`, `shipping_type`, `shipping_cost`, `total_price`, `total_items`, `name`, `phone`, `email`, `tracking_status`) VALUES
(1, 13, 'Pending', '2026-03-31 22:20:56', 'hhh', 'Transfer', 'proofs/69cc8f1870cf5_1775013656.jpg', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Menunggu Pembayaran'),
(2, 13, 'Pending', '2026-03-31 22:21:29', 'hhh', 'Transfer', 'proofs/69cc8f3985671_1775013689.jpg', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Menunggu Pembayaran'),
(3, 13, 'Pending', '2026-03-31 22:22:33', 'hhh', 'Transfer', 'proofs/69cc8f79190a0_1775013753.jpg', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Menunggu Pembayaran'),
(4, 13, 'Pending', '2026-03-31 22:23:00', 'hhh', 'Cod', '', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Menunggu Pembayaran'),
(5, 13, 'Pending', '2026-03-31 22:24:31', 'hhh', 'Transfer', 'proofs/69cc8fef99dc1_1775013871.jpg', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Menunggu Pembayaran'),
(6, 13, 'Pending', '2026-03-31 22:28:36', 'ilham sama najo ciumanb', 'Cod', '', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Menunggu Pembayaran'),
(7, 13, 'Pending', '2026-03-31 22:46:39', 'hhh', 'Cod', '', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Menunggu Pembayaran'),
(8, 19, 'Success', '2026-04-06 23:15:32', 'asasa', 'Transfer', 'proofs/69d484e47f556_1775535332.jpg', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Selesai'),
(9, 19, 'Success', '2026-04-07 22:10:31', 'Jalan Raden Sanim', 'Transfer', '', NULL, NULL, 0.00, 3000000, 1, NULL, NULL, NULL, 'Selesai'),
(10, 19, 'Success', '2026-04-07 22:51:32', 'Jalan Raden Sanim', 'Cod', '', NULL, NULL, 0.00, 3700000, 1, NULL, NULL, NULL, 'Dibatalkan'),
(11, 19, 'Success', '2026-04-07 22:52:13', 'Jalan Raden Sanim', 'Cod', '', NULL, NULL, 0.00, 0, 1, NULL, NULL, NULL, 'Selesai'),
(12, 19, 'Success', '2026-04-07 23:13:14', 'Jalan Raden Sanim', 'Transfer', '', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Dikirim'),
(13, 19, 'Success', '2026-04-07 23:17:34', 'Jalan Raden Sanim', 'Transfer', '', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Selesai'),
(14, 19, 'Success', '2026-04-08 10:53:08', 'Jalan Raden Sanim', 'Transfer', '', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Selesai'),
(15, 19, 'Success', '2026-04-08 10:54:08', 'Jalan Raden Sanim', 'Cod', '', NULL, NULL, 0.00, 1000000, 1, NULL, NULL, NULL, 'Dikirim'),
(16, 4, 'Pending', '2026-04-09 01:49:09', 'Jalan Raden Sanim', 'Cod', '', 'JNE', 'Reguler', 9000.00, 1009000, 1, 'ilham', '081384168009', NULL, 'Menunggu Pembayaran'),
(17, 19, 'Pending', '2026-04-09 03:13:22', 'jl raden sanim', 'Transfer', 'proofs/69d75fa2c5aea_1775722402.jpeg', 'J&T', 'Reguler', 8000.00, 708000, 1, 'vito', '4242424242', NULL, 'Menunggu Pembayaran');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 3, 6, 1, 1000000),
(2, 4, 6, 1, 1000000),
(3, 5, 6, 1, 1000000),
(4, 6, 6, 1, 1000000),
(5, 7, 6, 1, 1000000),
(6, 8, 6, 1, 1000000),
(7, 9, 6, 3, 1000000),
(8, 10, 9, 1, 700000),
(9, 10, 11, 1, 3000000),
(10, 12, 7, 1, 1000000),
(11, 13, 7, 1, 1000000),
(12, 14, 6, 1, 1000000),
(13, 16, 7, 1, 1000000),
(14, 17, 9, 1, 700000);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `stock`, `image`, `description`) VALUES
(6, 'Puma Speedcat', 'Sepatu', 1000000, 0, '69d6e90e4d50e.jpg', '.'),
(7, 'Adidas Spezial', 'Sepatu', 1000000, 0, '69d5aa7366504.jpg', '...'),
(9, 'Converse Run Star Trainer', 'Sepatu', 700000, 0, '69d59d5ead60a.jpg', '.'),
(10, 'Adidas Cheongsam', 'Jaket', 3000000, 1, '69d5a7c57fc89.jpg', '.'),
(11, 'Nike Tech', 'Jaket', 3000000, 1, '69d5a8955ba46.jpg', '.');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `success`
--

CREATE TABLE `success` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `success`
--

INSERT INTO `success` (`id`, `order_id`, `customer_name`, `payment_method`, `total_price`, `created_at`) VALUES
(1, 15, 'prabuk', 'Cod', 1000000, '2026-04-09 14:15:13'),
(2, 10, 'prabuk', 'Cod', 3700000, '2026-04-09 14:38:03'),
(3, 12, 'prabuk', 'Transfer', 1000000, '2026-04-09 14:38:09'),
(4, 11, 'prabuk', 'Cod', 0, '2026-04-09 14:38:16'),
(5, 13, 'prabuk', 'Transfer', 1000000, '2026-04-09 14:38:22'),
(6, 14, 'prabuk', 'Transfer', 1000000, '2026-04-09 14:38:28'),
(7, 9, 'prabuk', 'Transfer', 3000000, '2026-04-09 14:38:54'),
(8, 8, 'prabuk', 'Transfer', 1000000, '2026-04-09 14:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `proof_payment` varchar(255) DEFAULT NULL,
  `receipt` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qty` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `name`, `phone`, `address`, `profile_picture`) VALUES
(13, 'admin', 'admin@gmail.com', '$2y$10$ptKX1FjvjAt/rLh1YoErteuD885kVwhi59y6plWaCYZ/HcpwhQxTO', 'admin', '2026-02-25 03:44:51', NULL, NULL, NULL, NULL),
(14, 'petugas', 'raihanazka1@gmail.com', '$2y$10$5e.WnYW2dEwqCpkaBQwHdulOyobBPj5Up6HoAJ/DFkm.uuSOUwau6', 'petugas', '2026-02-25 03:57:31', NULL, NULL, NULL, NULL),
(19, 'prabuk', 'prabuk@gmail.com', '$2y$10$SZDEOZa7cXamNGVD6CL0.On3rosskFotdTSG4Dw4Wv0JwGiIWG9gy', 'user', '2026-04-03 14:40:25', 'vito', '081212341234', 'Jalan Raden Sanim', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `full_name`, `phone`, `address`, `photo`) VALUES
(2, 19, 'vito', '4242424242', 'jl raden sanim', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `success`
--
ALTER TABLE `success`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `success`
--
ALTER TABLE `success`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
