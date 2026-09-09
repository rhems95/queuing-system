-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2026 at 06:46 PM
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
(14, 1, '2026-08-25', 9, NULL, NULL),
(15, 4, '2026-08-25', 2, NULL, NULL),
(16, 3, '2026-08-25', 2, NULL, NULL),
(17, 1, '2026-09-01', 1, NULL, NULL),
(18, 1, '2026-09-08', 1, NULL, NULL),
(19, 1, '2026-09-09', 20, NULL, NULL),
(20, 4, '2026-09-09', 2, NULL, NULL),
(21, 3, '2026-09-09', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_09_09_000000_add_students_hold_and_queue_student_id', 1),
(2, '2026_09_09_010000_align_students_collation_with_queues', 2);

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue_number` varchar(20) NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `priority` tinyint(1) DEFAULT 0,
  `status` enum('waiting','serving','done','cancelled','held') DEFAULT 'waiting',
  `queue_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`id`, `queue_number`, `service_id`, `student_id`, `priority`, `status`, `queue_date`, `created_at`, `updated_at`) VALUES
(99, 'C001', 1, NULL, 1, 'serving', '2026-08-25', '2026-08-25 04:15:22', '2026-08-25 04:15:22'),
(100, 'C002', 1, NULL, 0, 'waiting', '2026-08-25', '2026-08-25 04:23:27', '2026-08-25 04:23:27'),
(101, 'C003', 1, NULL, 0, 'waiting', '2026-08-25', '2026-08-25 04:25:27', '2026-08-25 04:25:27'),
(102, 'C004', 1, NULL, 1, 'waiting', '2026-08-25', '2026-08-25 07:34:13', '2026-08-25 07:34:13'),
(103, 'R001', 4, NULL, 0, 'waiting', '2026-08-25', '2026-08-25 07:34:48', '2026-08-25 07:34:48'),
(104, 'C005', 1, NULL, 1, 'waiting', '2026-08-25', '2026-08-25 07:36:03', '2026-08-25 07:36:03'),
(105, 'C006', 1, NULL, 0, 'waiting', '2026-08-25', '2026-08-25 07:36:45', '2026-08-25 07:36:45'),
(106, 'C007', 1, NULL, 0, 'waiting', '2026-08-25', '2026-08-25 07:38:59', '2026-08-25 07:38:59'),
(107, 'C008', 1, NULL, 1, 'waiting', '2026-08-25', '2026-08-25 07:41:17', '2026-08-25 07:41:17'),
(108, 'C009', 1, NULL, 1, 'waiting', '2026-08-25', '2026-08-25 07:42:23', '2026-08-25 07:42:23'),
(109, 'D001', 3, NULL, 0, 'waiting', '2026-08-25', '2026-08-25 07:42:39', '2026-08-25 07:42:39'),
(110, 'R002', 4, NULL, 1, 'waiting', '2026-08-25', '2026-08-25 07:42:52', '2026-08-25 07:42:52'),
(111, 'D002', 3, NULL, 0, 'waiting', '2026-08-25', '2026-08-25 07:43:03', '2026-08-25 07:43:03'),
(112, 'C001', 1, NULL, 0, 'waiting', '2026-09-01', '2026-08-31 21:27:02', '2026-08-31 21:27:02'),
(113, 'C001', 1, NULL, 0, 'waiting', '2026-09-08', '2026-09-08 02:27:59', '2026-09-08 02:27:59'),
(114, 'C001', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:22:03', '2026-09-08 19:22:03'),
(115, 'R001', 4, NULL, 0, 'serving', '2026-09-09', '2026-09-08 19:26:42', '2026-09-08 19:26:42'),
(116, 'R002', 4, NULL, 0, 'waiting', '2026-09-09', '2026-09-08 19:26:43', '2026-09-08 19:26:43'),
(117, 'C002', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:26:49', '2026-09-08 19:26:49'),
(118, 'C003', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:27:00', '2026-09-08 19:27:00'),
(119, 'C004', 1, NULL, 1, 'done', '2026-09-09', '2026-09-08 19:27:06', '2026-09-08 19:27:06'),
(120, 'C005', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:29:33', '2026-09-08 19:29:33'),
(121, 'C006', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:29:52', '2026-09-08 19:29:52'),
(122, 'C007', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:34:28', '2026-09-08 19:34:28'),
(123, 'C008', 1, NULL, 1, 'done', '2026-09-09', '2026-09-08 19:34:37', '2026-09-08 19:34:37'),
(124, 'C009', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:38:05', '2026-09-08 19:38:05'),
(125, 'C010', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 19:40:47', '2026-09-08 19:40:47'),
(126, 'C011', 1, NULL, 1, 'done', '2026-09-09', '2026-09-08 19:40:54', '2026-09-08 19:40:54'),
(127, 'C012', 1, NULL, 0, 'done', '2026-09-09', '2026-09-08 23:59:22', '2026-09-08 23:59:22'),
(128, 'C013', 1, NULL, 0, 'done', '2026-09-09', '2026-09-09 00:00:02', '2026-09-09 00:00:02'),
(129, 'C014', 1, '2024-0005', 0, 'cancelled', '2026-09-09', '2026-09-09 07:03:20', '2026-09-09 07:03:20'),
(131, 'C015', 1, '2024-0002', 0, 'done', '2026-09-09', '2026-09-09 08:01:14', '2026-09-09 08:01:14'),
(132, 'C016', 1, '2024-0003', 0, 'done', '2026-09-09', '2026-09-09 08:07:39', '2026-09-09 08:07:39'),
(133, 'C017', 1, '2024-0001', 0, 'serving', '2026-09-09', '2026-09-09 08:30:43', '2026-09-09 08:30:43'),
(134, 'C018', 1, '2024-0004', 1, 'done', '2026-09-09', '2026-09-09 08:31:26', '2026-09-09 08:31:26'),
(135, 'D001', 3, '2024-0003', 1, 'waiting', '2026-09-09', '2026-09-09 08:31:40', '2026-09-09 08:31:40'),
(136, 'C019', 1, '20231-00278', 1, 'done', '2026-09-09', '2026-09-09 08:33:57', '2026-09-09 08:33:57'),
(137, 'C020', 1, '2024-0002', 0, 'done', '2026-09-09', '2026-09-09 08:42:46', '2026-09-09 08:42:46');

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
(76, 99, 1, '2026-08-25 15:45:25', NULL, NULL, NULL),
(77, 119, 1, '2026-09-09 03:28:09', '2026-09-09 03:28:22', NULL, NULL),
(78, 114, 1, '2026-09-09 03:28:22', '2026-09-09 03:28:40', NULL, NULL),
(79, 117, 1, '2026-09-09 03:29:00', '2026-09-09 03:29:12', NULL, NULL),
(80, 118, 1, '2026-09-09 03:29:25', '2026-09-09 03:29:44', NULL, NULL),
(81, 120, 1, '2026-09-09 03:29:44', '2026-09-09 03:29:58', NULL, NULL),
(82, 121, 1, '2026-09-09 03:34:20', '2026-09-09 03:34:48', NULL, NULL),
(83, 123, 1, '2026-09-09 03:35:14', '2026-09-09 03:35:21', NULL, NULL),
(84, 126, 2, '2026-09-09 16:36:20', '2026-09-09 16:36:34', NULL, NULL),
(85, 122, 1, '2026-09-09 07:42:07', '2026-09-09 07:42:09', NULL, NULL),
(86, 115, 6, '2026-09-09 03:45:12', NULL, NULL, NULL),
(87, 124, 1, '2026-09-09 15:46:02', '2026-09-09 15:46:05', NULL, NULL),
(88, 125, 1, '2026-09-09 15:46:05', '2026-09-09 15:46:06', NULL, NULL),
(89, 127, 1, '2026-09-09 15:46:06', '2026-09-09 15:46:07', NULL, NULL),
(90, 128, 1, '2026-09-09 15:46:07', '2026-09-09 15:46:09', NULL, NULL),
(91, 131, 1, '2026-09-09 16:07:51', '2026-09-09 16:08:01', NULL, NULL),
(92, 132, 1, '2026-09-09 16:27:37', '2026-09-09 16:29:43', NULL, NULL),
(93, 131, 1, '2026-09-09 16:32:05', '2026-09-09 16:32:22', NULL, NULL),
(94, 134, 1, '2026-09-09 16:32:22', '2026-09-09 16:34:40', NULL, NULL),
(95, 136, 1, '2026-09-09 16:34:40', '2026-09-09 16:37:09', NULL, NULL),
(96, 133, 2, '2026-09-09 16:42:06', NULL, NULL, NULL),
(97, 137, 1, '2026-09-09 16:42:53', '2026-09-09 16:43:25', NULL, NULL);

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
(1, 'Cashier', 'C', 'Handles payments and cashier transactions', NULL, NULL),
(2, 'Promissory Notes', 'P', 'Handles promissory note processing', NULL, NULL),
(3, 'Data Management Office', 'D', 'Handles data management transactions', NULL, NULL),
(4, 'Registrar', 'R', 'Handles registrar transactions', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `name`, `created_at`, `updated_at`) VALUES
(1, '2024-0001', 'Juan Dela Cruz', '2026-09-09 07:01:09', '2026-09-09 07:01:09'),
(2, '2024-0002', 'Maria Santos', '2026-09-09 07:01:09', '2026-09-09 07:01:09'),
(3, '2024-0003', 'Jose Rizal', '2026-09-09 07:01:09', '2026-09-09 07:01:09'),
(4, '2024-0004', 'Ana Reyes', '2026-09-09 07:01:09', '2026-09-09 07:01:09'),
(5, '2024-0005', 'Pedro Garcia', '2026-09-09 07:01:09', '2026-09-09 07:01:09'),
(11, '20231-00278', 'Rhem Sumodlayon', '2026-09-09 08:33:27', '2026-09-09 08:33:27');

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
(6, 'omar', 'omar@gmail.com', '$2y$12$GJ7.g1kfBq6zecfSOQpyKOylPIIPhkpczhGlNP0SVcQMzdC3Qh2OW', 'staff', 2, '2026-03-14 10:19:43', '2026-03-14 10:20:22'),
(7, 'cahier3', 'cashier3@gmail.com', '$2y$12$VoYe8K5FYvLmXRY7OD/D5ORMXNVXqD/3zoB2sMnBPmJMshBYYAz1W', 'staff', 3, '2026-09-08 19:43:13', '2026-09-08 19:43:13'),
(8, 'dmo sample', 'dmo@gmail.com', '$2y$12$W9upiHMBWaBoWrI.r.4wV.zvkhq90Pr9JzpI5QGbCoUw24wT3nuJ.', 'staff', 5, '2026-09-08 19:43:38', '2026-09-08 19:43:38'),
(9, 'registrar sample', 'registrar@gmail.com', '$2y$12$KA1kIbNBTJ85ChFwXZuz5OeWdHg6vA2EPRXukj8Uqwsj2qhjH3dPm', 'staff', 6, '2026-09-08 19:44:14', '2026-09-08 19:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `windows`
--

CREATE TABLE `windows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `window_name` varchar(50) NOT NULL,
  `group_name` varchar(50) DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `windows`
--

INSERT INTO `windows` (`id`, `window_name`, `group_name`, `service_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Cashier 1', 'Window 1', 1, 'active', NULL, NULL),
(2, 'Cashier 2', 'Window 1', 1, 'active', NULL, NULL),
(3, 'Cashier 3', 'Window 1', 1, 'active', NULL, NULL),
(4, 'Promissory Notes', 'Window 2', 2, 'active', NULL, NULL),
(5, 'DMO', 'Window 3', 3, 'active', NULL, NULL),
(6, 'Registrar', 'Window 4', 4, 'active', NULL, NULL);

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
  ADD KEY `idx_queue_date` (`queue_date`),
  ADD KEY `queues_student_id_index` (`student_id`);

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
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_id_unique` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `users_window_id_unique` (`window_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `queue_calls`
--
ALTER TABLE `queue_calls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `windows`
--
ALTER TABLE `windows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
