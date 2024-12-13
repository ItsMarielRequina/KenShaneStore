-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 02:34 PM
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
-- Database: `kenshanestore`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`user_id`, `product_id`, `quantity`) VALUES
(1, 1, 6),
(1, 10, 3),
(5, 3, 1),
(5, 4, 1),
(5, 5, 1),
(5, 6, 1),
(5, 8, 1),
(5, 10, 1),
(6, 1, 1),
(6, 2, 1),
(6, 5, 2),
(6, 6, 5),
(7, 3, 2),
(7, 6, 2),
(13, 1, 1),
(14, 1, 1),
(14, 2, 1),
(15, 1, 1),
(16, 1, 2),
(16, 2, 1),
(16, 3, 1),
(17, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `restock_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `quantity`, `restock_date`) VALUES
(1, 1, 99, NULL),
(2, 2, 99, NULL),
(3, 3, 99, NULL),
(4, 4, 100, NULL),
(5, 5, 100, NULL),
(6, 6, 98, NULL),
(7, 7, 99, NULL),
(8, 8, 100, NULL),
(9, 9, 100, NULL),
(10, 10, 100, NULL),
(15, 21, 100, NULL),
(16, 22, 99, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_id`, `total_amount`, `order_date`, `payment_method`, `contact_number`, `address`, `status`, `updated_at`) VALUES
(145, 19, 'order_675ab42c0f8f6', 1287.00, '2024-12-12 10:00:12', '0', '09123456789', 'PH', 'Pending', '2024-12-12 10:00:12'),
(146, 19, 'orderID_675aa94491232', 1287.00, '2024-12-12 10:00:13', 'GCash', '09123456789', 'PH', 'Completed', '2024-12-12 10:00:29'),
(147, 19, 'order_675ab6304273d', 78.00, '2024-12-12 10:08:48', '0', '09123456789', 'PH', 'Pending', '2024-12-12 10:08:48'),
(153, 19, 'order_675ab6481b5b7', 78.00, '2024-12-12 10:09:12', '0', '09123456789', 'PH', 'Pending', '2024-12-12 10:09:12'),
(156, 19, 'order_675ab65a7a32e', 78.00, '2024-12-12 10:09:30', 'GCash', '09123456789', 'PH', 'Completed', '2024-12-12 10:09:52'),
(157, 19, 'order_675ab6846f6d9', 398.00, '2024-12-12 10:10:12', '0', '09123456789', 'PH', 'Pending', '2024-12-12 10:10:12'),
(158, 19, 'order_675ab685aa1cc', 398.00, '2024-12-12 10:10:13', 'COD', '09123456789', 'PH', 'Completed', '2024-12-12 10:14:30'),
(159, 19, 'order_675ab743b347d', 169.00, '2024-12-12 10:13:23', '0', '09123456789', 'PH', 'Pending', '2024-12-12 10:13:23'),
(160, 19, 'order_675ab744c4985', 169.00, '2024-12-12 10:13:24', 'GCash', '09123456789', 'PH', 'Completed', '2024-12-12 10:14:32'),
(161, 19, 'order_675acd430a41f', 176.00, '2024-12-12 11:47:15', '0', '09123456789', 'PH', 'Pending', '2024-12-12 11:47:15'),
(162, 19, 'order_675acd80e5895', 176.00, '2024-12-12 11:48:16', '0', '09123456789', 'PH', 'Pending', '2024-12-12 11:48:16'),
(163, 4, 'order_675ad16a87250', 88.00, '2024-12-12 12:04:58', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 12:04:58'),
(164, 4, 'order_675ad3ee0c77e', 43.00, '2024-12-12 12:15:42', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 12:15:42'),
(165, 4, 'order_675ad3efb1e55', 43.00, '2024-12-12 12:15:43', 'GCash', '09123456789', 'Philippines', 'Completed', '2024-12-12 12:15:52'),
(166, 4, 'order_675ad43756a02', 88.00, '2024-12-12 12:16:55', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 12:16:55'),
(167, 4, 'order_675ad4395e371', 88.00, '2024-12-12 12:16:57', 'COD', '09123456789', 'Philippines', 'Completed', '2024-12-12 12:27:54'),
(168, 4, 'order_675ad6b154394', 43.00, '2024-12-12 12:27:29', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 12:27:29'),
(169, 4, 'order_675ad6b29fe32', 43.00, '2024-12-12 12:27:30', 'COD', '09123456789', 'Philippines', 'Completed', '2024-12-12 12:27:46'),
(170, 4, 'order_675ad79492a9e', 32.00, '2024-12-12 12:31:16', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 12:31:16'),
(171, 4, 'order_675ad7965075f', 32.00, '2024-12-12 12:31:18', 'COD', '09123456789', 'Philippines', 'Completed', '2024-12-12 12:31:34'),
(172, 4, 'order_675ae4dd04355', 64.00, '2024-12-12 13:27:57', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 13:27:57'),
(173, 4, 'order_675ae4de453c4', 64.00, '2024-12-12 13:27:58', 'GCash', '09123456789', 'Philippines', 'Completed', '2024-12-12 13:32:32'),
(174, 4, 'order_675ae5c8c92be', 814.00, '2024-12-12 13:31:52', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 13:31:52'),
(175, 4, 'order_675ae5cad13f5', 814.00, '2024-12-12 13:31:54', 'COD', '09123456789', 'Philippines', 'Completed', '2024-12-12 13:32:24'),
(176, 4, 'order_675ae62d20b57', 88.00, '2024-12-12 13:33:33', '0', '09123456789', 'Philippines', 'Pending', '2024-12-12 13:33:33'),
(177, 4, 'order_675ae62e3ba1d', 88.00, '2024-12-12 13:33:34', 'COD', '09123456789', 'Philippines', 'Pending', '2024-12-12 13:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(64, 145, 9, 13, 99.00),
(65, 147, 7, 2, 39.00),
(66, 153, 7, 2, 39.00),
(67, 157, 3, 2, 199.00),
(68, 159, 10, 1, 169.00),
(71, 163, 17, 1, 88.00),
(72, 164, 18, 1, 43.00),
(73, 166, 19, 1, 88.00),
(74, 168, 18, 1, 43.00),
(75, 170, 21, 1, 32.00),
(76, 172, 21, 2, 32.00),
(77, 174, 1, 1, 149.00),
(78, 174, 2, 1, 139.00),
(79, 174, 3, 1, 199.00),
(80, 174, 6, 2, 144.00),
(81, 174, 7, 1, 39.00),
(82, 176, 22, 1, 88.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image_url`) VALUES
(1, 'Orocan Pail', 'Orocan Pail with Stainless Steel Handle', 149.00, 100, 'image/1.jpg'),
(2, 'Orocan Basin', 'OROCAN Utility Basin / PLANGGANA', 139.00, 100, 'image/2.jpg'),
(3, 'Sanyo Ratan', 'Sanyo box Rattan 201 Stool Chair', 199.00, 100, 'image/3.jpg'),
(4, 'Uniglobal Basket', 'Uniglobal #1155 Filing Knitted Organizer Basket with Cover w/box', 119.00, 100, 'image/4.jpg'),
(5, 'Aesthetic Timba', 'Aesthetic Timba multipurpose laundry Big Basket modern style Pail for bathroom', 169.00, 100, 'image/5.jpg'),
(6, 'Aesthetic Basket Pail', '9390 MULTIPURPOSE AESTHETIC BASKET PAIL WITH MOCHA DIPPER MODERN STYLE LARGE LAUNDRY', 144.00, 100, 'image/6.jpg'),
(7, 'Dust Bin', 'Uniglobal Dust Bin', 39.00, 100, 'image/7.jpg'),
(8, 'Water Dipper', 'Sunnyware #119 Standing Water Dipper', 21.00, 100, 'image/8.jpg'),
(9, 'Laundry Basket', '#9710 Uniglobal Colorful Laundry Basket', 99.00, 100, 'image/9.jpg'),
(10, '3n1 Set', 'UNIGLOBAL Mocha Trio: 3-in-1 Set of Plastic Basin, Pail and Dipper', 169.00, 100, 'image/10.jpg'),
(21, 'Hanger', NULL, 32.00, 99, 'image/image (14).png'),
(22, 'Large Chamber Pot', NULL, 88.00, 0, 'image/image (15).png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_role` varchar(50) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `user_role`, `birthday`, `address`, `contact_number`, `profile_image`, `status`) VALUES
(4, 'o_dazaii', 'admin@gmail.com', '$2y$10$c9vItmJW6a5RHk/ihw/wo.4uhnCTwUTXeGdSZjEfTwJHCH39LdSBa', '2024-10-26 07:51:07', 'admin', '2024-12-09', 'Philippines', '09123456789', 'uploads/i waaaab uu.png', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD UNIQUE KEY `order_id_2` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
