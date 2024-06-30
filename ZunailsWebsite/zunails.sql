-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2024 at 04:01 PM
-- Server version: 8.0.32
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zunails`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(55) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `available_times`
--

CREATE TABLE `available_times` (
  `id` int NOT NULL,
  `available_date` date NOT NULL,
  `available_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `available_times`
--

INSERT INTO `available_times` (`id`, `available_date`, `available_time`) VALUES
(10, '2024-06-30', '11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` enum('booked','cancelled','paid') DEFAULT 'booked',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `services` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `booking_date`, `booking_time`, `status`, `created_at`, `services`) VALUES
(1, 1, '2024-06-20', '09:00:00', 'paid', '2024-06-18 22:20:41', NULL),
(2, 1, '2024-06-19', '08:00:00', 'paid', '2024-06-18 22:22:14', NULL),
(3, 1, '2024-06-22', '15:00:00', 'paid', '2024-06-18 22:36:50', NULL),
(4, 1, '2024-06-22', '11:00:00', 'paid', '2024-06-18 22:44:32', NULL),
(5, 1, '2024-06-26', '10:00:00', 'paid', '2024-06-18 23:28:44', NULL),
(6, 1, '2024-06-21', '08:00:00', 'booked', '2024-06-20 11:07:18', NULL),
(7, 1, '2024-06-20', '14:00:00', 'booked', '2024-06-20 17:44:14', NULL),
(8, 1, '2024-06-29', '13:00:00', 'booked', '2024-06-20 21:30:30', NULL),
(9, 1, '2024-06-20', '09:00:00', 'booked', '2024-06-20 21:31:45', NULL),
(10, 1, '2024-06-23', '10:00:00', 'paid', '2024-06-20 21:34:42', NULL),
(11, 1, '2024-06-22', '10:00:00', 'paid', '2024-06-20 22:46:38', NULL),
(12, 1, '2024-06-24', '12:00:00', 'paid', '2024-06-20 22:48:03', NULL),
(13, 1, '2024-06-22', '10:00:00', 'paid', '2024-06-20 22:55:43', NULL),
(14, 1, '2024-06-24', '11:00:00', 'paid', '2024-06-20 23:02:18', NULL),
(15, 1, '2024-06-22', '08:00:00', 'paid', '2024-06-20 23:07:26', NULL),
(16, 1, '2024-06-22', '08:00:00', 'paid', '2024-06-20 23:15:08', NULL),
(17, 1, '2024-06-24', '13:00:00', 'paid', '2024-06-20 23:20:16', NULL),
(18, 1, '2024-06-24', '10:00:00', 'paid', '2024-06-20 23:22:11', NULL),
(19, 1, '2024-06-27', '10:00:00', 'paid', '2024-06-20 23:23:22', NULL),
(20, 1, '2024-06-27', '12:00:00', 'paid', '2024-06-20 23:26:01', NULL),
(21, 1, '2024-06-27', '08:00:00', 'paid', '2024-06-20 23:30:41', NULL),
(22, 1, '2024-06-27', '10:00:00', 'paid', '2024-06-20 23:33:14', NULL),
(23, 1, '2024-06-27', '16:00:00', 'paid', '2024-06-20 23:37:07', NULL),
(24, 1, '2024-06-27', '09:00:00', 'paid', '2024-06-20 23:46:19', NULL),
(25, 1, '2024-06-27', '11:00:00', 'paid', '2024-06-20 23:59:24', NULL),
(26, 1, '2024-06-28', '09:00:00', 'paid', '2024-06-21 00:00:12', NULL),
(27, 1, '2024-06-28', '08:00:00', 'paid', '2024-06-21 00:02:09', NULL),
(28, 1, '2024-06-28', '10:00:00', 'paid', '2024-06-21 00:05:38', NULL),
(29, 1, '2024-06-24', '14:00:00', 'paid', '2024-06-21 09:10:12', NULL),
(30, 1, '2024-06-24', '14:00:00', 'paid', '2024-06-21 10:21:18', NULL),
(31, 2, '2024-06-24', '10:00:00', 'paid', '2024-06-21 12:31:23', NULL),
(32, 2, '2024-06-24', '14:00:00', 'paid', '2024-06-21 12:34:11', NULL),
(33, 2, '2024-06-25', '11:00:00', 'paid', '2024-06-21 13:47:35', NULL),
(34, 2, '2024-06-26', '12:00:00', 'booked', '2024-06-21 14:11:07', NULL),
(35, 2, '2024-06-22', '12:00:00', 'booked', '2024-06-21 14:13:34', NULL),
(36, 2, '2024-06-26', '12:00:00', 'paid', '2024-06-21 18:27:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_services`
--

CREATE TABLE `booking_services` (
  `booking_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_services`
--

INSERT INTO `booking_services` (`booking_id`, `service_id`) VALUES
(1, 29),
(2, 31),
(3, 29),
(4, 25),
(5, 31),
(6, 29),
(7, 10),
(7, 30),
(7, NULL),
(7, NULL),
(7, NULL),
(8, 13),
(8, 23),
(9, 17),
(9, 30),
(9, NULL),
(9, NULL),
(10, 17),
(10, 30),
(10, NULL),
(10, NULL),
(11, 31),
(12, 29),
(13, 32),
(14, 32),
(15, 32),
(16, 32),
(17, 32),
(18, 32),
(19, 32),
(20, 32),
(21, 32),
(22, 32),
(23, 31),
(24, 32),
(25, 32),
(26, 32),
(27, 32),
(28, 32),
(29, 31),
(30, 31),
(31, 13),
(31, 18),
(31, 25),
(31, 30),
(32, 29),
(33, 31),
(34, 32),
(35, 31),
(36, 31);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `image_name`, `description`, `price`, `image_path`, `created_at`) VALUES
(1, 'Short Acrylic Overlay', 'Detailed Art', 360.00, '6671702710c919.46685750.png', '2024-06-18 11:31:51'),
(2, 'Short Acrylic Overlay', 'French, Simple Art', 430.00, '66717231dda479.69593395.png', '2024-06-18 11:40:33'),
(3, 'Short Acrylic Overlay', 'Simple Art', 320.00, '6671726eb89bc7.98755198.png', '2024-06-18 11:41:34'),
(4, 'Short Acrylic Overlay', 'Simple Art', 320.00, '667172b5bdd561.91117754.png', '2024-06-18 11:42:45');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `category`, `description`, `price`) VALUES
(9, 'Acrylic Overlay', 'Short', 310.00),
(10, 'Acrylic Overlay', 'Medium', 330.00),
(11, 'Acrylic Overlay', 'Long', 370.00),
(12, 'Gel Overlay', 'Short', 300.00),
(13, 'Gel Overlay', 'Medium', 315.00),
(14, 'Gel Overlay', 'Long', 330.00),
(15, 'Acrylic Sculpture', 'Short', 330.00),
(16, 'Acrylic Sculpture', 'Medium', 350.00),
(17, 'Acrylic Sculpture', 'Long', 380.00),
(18, 'Art', 'Ombre p/set', 150.00),
(19, 'Art', 'Ombre p/nail', 15.00),
(20, 'Art', 'French p/set', 120.00),
(21, 'Art', 'French p/nail', 15.00),
(22, 'Art', 'Art (Detailed)', 50.00),
(23, 'Art', 'Art (Simple)', 30.00),
(24, 'Art', 'Cartoon Art p/nail', 60.00),
(25, 'Art', 'Chrome', 12.00),
(26, 'Art', 'Rhinestone', 30.00),
(27, 'Art', 'Glitter p/nail', 8.00),
(28, 'Art', 'Reflective Glitter p/nail', 12.00),
(29, 'Removal', 'Acrylic Soak (with Polish Finish)', 120.00),
(30, 'Removal', 'Acrylic Soak (with New Set)', 70.00),
(31, 'Removal', 'Gel Removal (with Polish Finish)', 80.00),
(32, 'Removal', 'Gel Removal (with New Set)', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `phone_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `phone_number`) VALUES
(2, 'Johan Meyer', 'johan30@icloud.ca.za', '$2y$10$7j2ZUx9GzQNJ6TIP84fIaeVE3ARHjGmq7/cvUURKCxB7RwTxDr2N6', '2024-06-21 14:29:07', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`);

--
-- Indexes for table `available_times`
--
ALTER TABLE `available_times`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `available_times`
--
ALTER TABLE `available_times`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD CONSTRAINT `booking_services_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
