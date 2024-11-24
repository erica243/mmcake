-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2024 at 08:44 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(30) NOT NULL,
  `client_ip` varchar(20) NOT NULL,
  `user_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `qty` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `client_ip`, `user_id`, `product_id`, `qty`) VALUES
(11, '', 2, 6, 2),
(16, '', 3, 9, 1),
(17, '', 3, 8, 1),
(28, '', 4, 12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`) VALUES
(7, 'Birthdayd Cake'),
(8, 'Baptismal Cake '),
(10, 'Fathers/Mothers Day Cake'),
(19, 'Wedding Cake');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(30) NOT NULL,
  `order_number` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `name` text NOT NULL,
  `address` text NOT NULL,
  `mobile` text NOT NULL,
  `email` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_method` varchar(100) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `shipping` int(11) NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `order_date`, `name`, `address`, `mobile`, `email`, `status`, `delivery_method`, `transaction_id`, `payment_method`, `created_at`, `shipping`, `pickup_date`, `pickup_time`) VALUES
(1, 5932, '2024-07-24 19:27:07', 'Erica Adlit', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 07:00:35', 0, '0000-00-00', '00:00:00'),
(2, 9001, '2024-07-24 19:32:26', 'Erica Adlit', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 07:00:35', 0, '0000-00-00', '00:00:00'),
(3, 6169, '2024-07-25 06:39:32', 'Erica Adlit', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 07:00:35', 0, '0000-00-00', '00:00:00'),
(4, 3060, '2024-07-25 06:41:46', 'Happy Meal', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '0915825964', 'ducaykristel@gmail.com', 1, 'delivery', 0, 'gcash', '2024-07-25 07:00:35', 0, '0000-00-00', '00:00:00'),
(5, 2054, '2024-07-25 07:02:44', 'Happy Meal', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '0915825964', 'ducaykristel@gmail.com', 1, 'delivery', 0, 'gcash', '2024-07-25 07:03:49', 0, '0000-00-00', '00:00:00'),
(6, 3205, '2024-07-25 07:03:13', 'Happy Meal', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '0915825964', 'ducaykristel@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 07:03:56', 0, '0000-00-00', '00:00:00'),
(7, 3458, '2024-07-25 07:03:39', 'Happy Meal', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '0915825964', 'ducaykristel@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 07:05:04', 0, '0000-00-00', '00:00:00'),
(8, 8019, '2024-07-25 07:37:04', 'Happy Meal', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '0915825964', 'ducaykristel@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 08:16:13', 0, '0000-00-00', '00:00:00'),
(9, 4500, '2024-07-25 08:15:50', 'Happy Meal', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '0915825964', 'ducaykristel@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 08:28:15', 0, '0000-00-00', '00:00:00'),
(10, 1556, '2024-07-25 08:36:42', 'Erica Adlit', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'delivery', 0, 'cash', '2024-07-25 08:37:17', 0, '0000-00-00', '00:00:00'),
(12, 9725, '2024-07-25 11:04:43', 'Salve De Mesa', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'delivery', 0, 'gcash', '2024-07-25 15:30:32', 0, '0000-00-00', '00:00:00'),
(13, 1506, '2024-07-25 13:55:28', 'Salve De Mesa', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'pickup', 0, 'cash', '2024-07-25 21:07:47', 0, '2024-07-31', '21:57:00'),
(14, 1225, '2024-07-25 15:48:45', 'Salve De Mesa', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 0, 'delivery', 0, 'cash', '2024-07-25 21:48:45', 0, '0000-00-00', '00:00:00'),
(15, 5804, '2024-07-25 21:07:27', 'Salve De Mesa', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 0, 'delivery', 0, 'gcash', '2024-07-26 03:07:27', 0, '0000-00-00', '00:00:00'),
(16, 3429, '2024-07-25 21:38:56', 'Salve De Mesa', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'Delivery', 0, 'gcash', '2024-07-26 08:25:55', 0, '0000-00-00', '00:00:00'),
(17, 4696, '2024-07-26 03:46:03', 'erica adlit', 'malbago', '9815825964', 'eica204@chavezgmail.com', 0, 'pickup', 0, 'gcash', '2024-07-26 09:46:03', 0, '2024-07-26', '00:48:00'),
(18, 8866, '2024-07-26 08:27:52', 'Salve De Mesa', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 1, 'delivery', 0, 'gcash', '2024-07-26 08:28:11', 0, '0000-00-00', '00:00:00'),
(19, 4210, '2024-07-26 08:31:53', 'Salve De Mesa', 'tarong madridejos cebu', '0915825964', 'erica204chavez@gmail.com', 0, 'delivery', 0, 'gcash', '2024-07-26 14:31:53', 0, '0000-00-00', '00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int(30) NOT NULL,
  `order_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `qty` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `order_id`, `product_id`, `qty`) VALUES
(54, 0, 12, 1),
(55, 0, 15, 1),
(56, 5, 10, 1),
(57, 6, 11, 1),
(58, 7, 15, 1),
(59, 8, 15, 1),
(60, 9, 12, 1),
(61, 10, 11, 1),
(62, 11, 15, 1),
(63, 12, 19, 1),
(64, 13, 16, 1),
(65, 14, 10, 1),
(66, 15, 10, 1),
(67, 15, 18, 1),
(68, 16, 16, 6),
(69, 17, 16, 1),
(70, 18, 14, 1),
(71, 19, 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `img_path` text NOT NULL,
  `status` varchar(100) NOT NULL,
  `size` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `name`, `description`, `price`, `img_path`, `status`, `size`) VALUES
(11, 7, 'Wedding Cake', 'fsdf', 55543, '1720082160_b8.jpg', 'Unavailable', '0'),
(14, 8, 'sdsd', 'sdsd', 22, '1720083240_b1.jpg', 'Available', '0'),
(15, 8, 'qq', 'as', 1, '1720083240_b6.jpg', 'Unavailable', '0'),
(16, 7, 'Wedding Cakes', 's', 480, '1721756460_Messenger_creation_178d1d8b-412c-4402-a406-9d2893f31320.png', 'Available', '0'),
(18, 8, 'Vitamins salve', 'se', 9, '1721897280_logo.jpg', 'Available', '1'),
(29, 7, 'Wedding Cake', 'z', 123, '', 'Available', '122');

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
(1, 'M&M Cake Ordering System', 'erica204chavez@gmail.com', '+639158259643', '1721754180_bg.jpg', '&lt;h1 style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;span style=&quot;color:rgb(68,68,68);text-align: center; background: transparent; position: relative;&quot;&gt;&lt;h1&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;b style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;ABOUT US&lt;/b&gt;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;h1&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&amp;nbsp;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;/span&gt;&lt;span style=&quot;font-size:20px;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); text-align: center; background: transparent; position: relative; font-size: 20px;&quot;&gt;&lt;h1 style=&quot;font-size: 20px;&quot;&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 20px;&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 20px;&quot;&gt;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;/span&gt;&lt;span style=&quot;font-size: 24px; text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); text-align: center; background: transparent; position: relative; font-size: 24px;&quot;&gt;&lt;h1 style=&quot;font-size: 24px;&quot;&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;b style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;Welcome to the M&amp;amp;M Cake Ordering System, home of beautifully tasty cakes, an unforgettable cake for every one! We at M&amp;amp;M believe that every occasion is of the highest importance: Celebrate with a cake as exceptional and unique as you. Our selection of beautifully crafted cakes is perfect for your special occasions, whether it&rsquo;s celebrating a birthday, wedding, anniversary - you name it.&lt;/b&gt;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;h3 style=&quot;font-size: 24px;&quot;&gt;&lt;b style=&quot;font-size: 24px;&quot;&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp;&amp;nbsp;&lt;br style=&quot;font-size: 24px;&quot;&gt;&lt;/sup&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;/sup&gt;&lt;/sup&gt;&lt;/b&gt;&lt;/h3&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;span style=&quot;font-size: 24px; color: rgb(68, 68, 68);&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); text-align: center; background: transparent; position: relative; font-size: 24px;&quot;&gt;&lt;h3 style=&quot;font-size: 24px;&quot;&gt;&lt;b style=&quot;font-size: 24px;&quot;&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt; The story of M&amp;amp;M started in the 1980s with a love of baking and a dedication to perfection. The name &quot;M&amp;amp;M&quot; stands for &quot;Money and Millions,&quot; symbolizing our commitment to delivering value and abundance in every creation we make.&lt;br style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;/sup&gt;&lt;/sup&gt;&lt;sup style=&quot;font-size: 24px;&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp;&amp;nbsp;&lt;br style=&quot;font-size: 24px;&quot;&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp; We pride ourselves on selecting only the best ingredients, meaning that every cake we make not only looks amazing but tastes delicious too. Our talented bakers and decorators bring some of your favorite classic flavors to new heights, as well as one-of-a-kind creations inspired by your sweetest visions.&lt;/span&gt;&lt;/sup&gt;&lt;/b&gt;&lt;/h3&gt;&lt;/span&gt;&lt;p style=&quot;text-align: center; font-size: 24px;&quot;&gt;&lt;/p&gt;&lt;/span&gt;&lt;p style=&quot;text-align: center; font-size: 24px;&quot;&gt;&lt;/p&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;p style=&quot;text-align: center; font-size: 24px;&quot;&gt;&lt;/p&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 16px;&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;&lt;/p&gt;&lt;/span&gt;&lt;/h1&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `username` text NOT NULL,
  `password` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=admin , 2 = staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`) VALUES
(1, 'Administrator', 'admin', '$2y$10$efDvenHYJ5Fu/xxt1ANbXuRx5/TuzNs/s4k6keUiiFvr2ueE0GmrG', 1),
(3, 'manilyn', 'staff', '$2y$10$/WrVxpafEU4nupXFPnpznOPebS7FgQHM9cQUgSIIRp8G6ZjIvkVdG', 1),
(5, 'Wedding Cake', 'admin', '$2y$10$HCPYY2qxNuuhHX0jQhYiG.l/HtaweFPQ3uFspBy5/FnL2.CTcgtw6', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `user_id` int(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `address` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_id`, `first_name`, `last_name`, `email`, `password`, `mobile`, `address`) VALUES
(1, 'James', 'Smith', 'jsmith@sample.com', '1254737c076cf867dc53d60a0364f38e', '4756463215', 'adasdasd asdadasd'),
(2, 'Claire', 'Blake', 'cblake@mail.com', '$2y$10$QYX8P9KwBKXunMEE4I5hVO/hO9pxUU/aswTlf.v.Uy1CNDEabTafS', '0912365487', 'Sample Address'),
(3, 'erica', 'adlit', 'manilyndemesa87@gmail.com', '$2y$10$lye723KpwQVP76Urjas03OBI/I0AUVxkGJaGtNteHZL.9c000gjmK', '0915829634', 'tarong'),
(4, 'Keneth', 'Ducay', 'kenethducay12@gmail.com', '$2y$10$x9FMoDT/WR2Ungg.8kutnO.o3LtuMq/vimb4uhO8dWnCAY/S2XrUe', '0915829634', 'Atop-Atop, Bantayan, Cebu'),
(5, 'erica', 'adlit', 'mdemesa@gmail.com', '$2y$10$nde4A5aYOfo8tyj2M4eoOOVnbcavB4nusFunQWDFVhBVIEhhTAznK', '0915829634', 'tarong'),
(6, 'erica', 'adlit', 'us1071591@gmail.com', '$2y$10$1XyTnLIuoMvX/Wrlj0rBMu4oQPEDbK61mU3SS9CLV3u92W2rkXTFa', '0915829634', 'w'),
(7, 'Salves', 'De Mesa', 'erica204chavez@gmail.com', '$2y$10$eu05fn9zN6mnYz97w1R0EuNrOrb7ygMGGmeu8MP/wlolsthgb8jYq', '0915825964', 'tarong madridejos cebu'),
(9, 'Erica', 'Adlit', 'eica204@chavezgmail.com', '$2y$10$WpZN5wYpw0dstOT6AUKkBeCgLNJkfgtQhSBJpKMW9XldgPBvaRKX2', '9815825964', 'malbago');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
