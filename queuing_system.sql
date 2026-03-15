-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2026 at 02:30 PM
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
-- Database: `queuing_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_queue_counters`
--

CREATE TABLE `daily_queue_counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `queue_date` date NOT NULL,
  `last_number` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_queue_counters`
--

INSERT INTO `daily_queue_counters` (`id`, `service_id`, `queue_date`, `last_number`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-03-14', 6, NULL, NULL),
(2, 1, '2026-03-15', 9, NULL, NULL),
(3, 2, '2026-03-15', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `queue_number` varchar(20) NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `priority` tinyint(1) DEFAULT 0,
  `status` enum('waiting','serving','done','cancelled') DEFAULT 'waiting',
  `queue_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`id`, `student_name`, `student_id`, `queue_number`, `service_id`, `priority`, `status`, `queue_date`, `created_at`, `updated_at`) VALUES
(1, 'rhem sumodlayon', '20231-00276', 'C001', 1, 0, 'done', '2026-03-14', NULL, NULL),
(2, 'mike bedrona', '20231-00277', 'C002', 1, 0, 'done', '2026-03-14', NULL, NULL),
(3, 'omar bayabao', '20231-00278', 'C003', 1, 0, 'done', '2026-03-14', NULL, NULL),
(4, 'nino malicay', '20231-00279', 'C004', 1, 0, 'serving', '2026-03-14', NULL, NULL),
(5, 'robert encio', '20231-00280', 'C005', 1, 1, 'done', '2026-03-14', NULL, NULL),
(6, 'rhem sumodlayon', '20231-00276', 'C006', 1, 0, 'waiting', '2026-03-14', NULL, NULL),
(7, 'rhem sumodlayon', '20231-00276', 'C001', 1, 0, 'done', '2026-03-15', NULL, NULL),
(8, 'mike bedrona', '20231-00277', 'R001', 2, 0, 'serving', '2026-03-15', NULL, NULL),
(9, 'nino malicay', '20231-00280', 'C002', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(10, 'robert encio', '20231-00281', 'R002', 2, 0, 'waiting', '2026-03-15', NULL, NULL),
(11, 'omar bayabao', '20231-00280', 'C003', 1, 0, 'done', '2026-03-15', NULL, NULL),
(12, 'rhem sumodlayon', '20231-00276', 'C004', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(13, 'omar bayabao', '20231-00277', 'C005', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(14, 'nino malicay', '20231-00281', 'C006', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(15, 'omar bayabao', '20231-00280', 'C007', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(16, 'rhem sumodlayon', '20231-00276', 'C008', 1, 0, 'waiting', '2026-03-15', NULL, NULL),
(17, 'nino malicay', '20231-00281', 'C009', 1, 1, 'waiting', '2026-03-15', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `queue_calls`
--

CREATE TABLE `queue_calls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue_id` bigint(20) UNSIGNED NOT NULL,
  `window_id` bigint(20) UNSIGNED NOT NULL,
  `called_time` datetime DEFAULT NULL,
  `finished_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue_calls`
--

INSERT INTO `queue_calls` (`id`, `queue_id`, `window_id`, `called_time`, `finished_time`, `created_at`, `updated_at`) VALUES
(1, 5, 1, '2026-03-14 18:28:42', '2026-03-14 18:32:23', NULL, NULL),
(2, 1, 1, '2026-03-14 18:32:35', '2026-03-14 18:32:49', NULL, NULL),
(3, 2, 1, '2026-03-14 18:32:59', '2026-03-14 18:35:59', NULL, NULL),
(4, 3, 1, '2026-03-14 18:36:19', '2026-03-14 18:37:59', NULL, NULL),
(5, 4, 1, '2026-03-14 18:38:08', NULL, NULL, NULL),
(6, 7, 1, '2026-03-15 06:22:06', '2026-03-15 06:23:13', NULL, NULL),
(7, 9, 1, '2026-03-15 06:23:22', NULL, NULL, NULL),
(8, 11, 1, '2026-03-15 06:24:49', '2026-03-15 06:30:52', NULL, NULL),
(9, 12, 1, '2026-03-15 06:30:59', NULL, NULL, NULL),
(10, 13, 1, '2026-03-15 06:31:53', NULL, NULL, NULL),
(11, 14, 1, '2026-03-15 06:40:55', NULL, NULL, NULL),
(12, 15, 1, '2026-03-15 06:41:09', NULL, NULL, NULL),
(13, 8, 2, '2026-03-15 06:42:37', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(50) NOT NULL,
  `prefix` varchar(5) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `prefix`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Cashier', 'C', 'Handles payments and financial transactions', NULL, NULL),
(2, 'Registrar', 'R', 'Handles student records and documents', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL,
  `window_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `window_id`, `created_at`, `updated_at`) VALUES
(4, 'Admin', 'admin@gmail.com', 'admin', 'admin', NULL, NULL, NULL),
(5, 'rhem', 'rhem@gmail.com', '$2y$12$Cc1oTHVFSTKCete8xBl3Oux35rcTpo52txfudAfNRCBNBLbo1GxwO', 'staff', 1, '2026-03-14 10:18:06', '2026-03-14 10:18:06'),
(6, 'omar', 'omar@gmail.com', '$2y$12$GJ7.g1kfBq6zecfSOQpyKOylPIIPhkpczhGlNP0SVcQMzdC3Qh2OW', 'staff', 2, '2026-03-14 10:19:43', '2026-03-14 10:20:22');

-- --------------------------------------------------------

--
-- Table structure for table `windows`
--

CREATE TABLE `windows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `window_name` varchar(50) NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `windows`
--

INSERT INTO `windows` (`id`, `window_name`, `service_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Window 1', 1, 'active', NULL, NULL),
(2, 'Window 2', 2, 'active', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_queue_counters`
--
ALTER TABLE `daily_queue_counters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_queue_date` (`queue_date`);

--
-- Indexes for table `queue_calls`
--
ALTER TABLE `queue_calls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `queue_id` (`queue_id`),
  ADD KEY `window_id` (`window_id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `window_id` (`window_id`);

--
-- Indexes for table `windows`
--
ALTER TABLE `windows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_queue_counters`
--
ALTER TABLE `daily_queue_counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `queue_calls`
--
ALTER TABLE `queue_calls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `windows`
--
ALTER TABLE `windows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_queue_counters`
--
ALTER TABLE `daily_queue_counters`
  ADD CONSTRAINT `daily_queue_counters_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `queues`
--
ALTER TABLE `queues`
  ADD CONSTRAINT `queues_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `queue_calls`
--
ALTER TABLE `queue_calls`
  ADD CONSTRAINT `queue_calls_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `queues` (`id`),
  ADD CONSTRAINT `queue_calls_ibfk_2` FOREIGN KEY (`window_id`) REFERENCES `windows` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`window_id`) REFERENCES `windows` (`id`);

--
-- Constraints for table `windows`
--
ALTER TABLE `windows`
  ADD CONSTRAINT `windows_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
