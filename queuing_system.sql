-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 (regenerated to match Laravel domain migrations + anonymous queues)
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
(3, 2, '2026-03-15', 2, NULL, NULL),
(4, 1, '2026-03-17', 20, NULL, NULL),
(5, 2, '2026-03-17', 2, NULL, NULL),
(6, 1, '2026-03-21', 24, NULL, NULL),
(7, 1, '2026-03-25', 6, NULL, NULL),
(8, 1, '2026-03-26', 3, NULL, NULL),
(9, 2, '2026-03-26', 2, NULL, NULL),
(10, 2, '2026-04-07', 5, NULL, NULL),
(11, 1, '2026-04-07', 4, NULL, NULL),
(12, 1, '2026-04-14', 9, NULL, NULL),
(13, 2, '2026-04-14', 6, NULL, NULL);

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
(1, '2024-0001', 'Juan Dela Cruz', NULL, NULL),
(2, '2024-0002', 'Maria Santos', NULL, NULL),
(3, '2024-0003', 'Jose Rizal', NULL, NULL),
(4, '2024-0004', 'Ana Reyes', NULL, NULL),
(5, '2024-0005', 'Pedro Garcia', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue_number` varchar(20) NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `issue_reason` varchar(32) DEFAULT NULL,
  `priority` tinyint(1) DEFAULT 0,
  `status` enum('waiting','serving','done','cancelled','held') DEFAULT 'waiting',
  `queue_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`id`, `queue_number`, `service_id`, `priority`, `status`, `queue_date`, `created_at`, `updated_at`) VALUES
(1, 'C001', 1, 0, 'done', '2026-03-14', NULL, NULL),
(2, 'C002', 1, 0, 'done', '2026-03-14', NULL, NULL),
(3, 'C003', 1, 0, 'done', '2026-03-14', NULL, NULL),
(4, 'C004', 1, 0, 'serving', '2026-03-14', NULL, NULL),
(5, 'C005', 1, 1, 'done', '2026-03-14', NULL, NULL),
(6, 'C006', 1, 0, 'waiting', '2026-03-14', NULL, NULL),
(7, 'C001', 1, 0, 'done', '2026-03-15', NULL, NULL),
(8, 'R001', 2, 0, 'serving', '2026-03-15', NULL, NULL),
(9, 'C002', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(10, 'R002', 2, 0, 'waiting', '2026-03-15', NULL, NULL),
(11, 'C003', 1, 0, 'done', '2026-03-15', NULL, NULL),
(12, 'C004', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(13, 'C005', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(14, 'C006', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(15, 'C007', 1, 0, 'serving', '2026-03-15', NULL, NULL),
(16, 'C008', 1, 0, 'waiting', '2026-03-15', NULL, NULL),
(17, 'C009', 1, 1, 'waiting', '2026-03-15', NULL, NULL),
(18, 'C001', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(19, 'C002', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(20, 'C003', 1, 1, 'serving', '2026-03-17', NULL, NULL),
(21, 'C004', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(22, 'C005', 1, 0, 'done', '2026-03-17', NULL, NULL),
(23, 'C006', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(24, 'C007', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(25, 'C008', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(26, 'C009', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(27, 'C010', 1, 0, 'done', '2026-03-17', NULL, NULL),
(28, 'C011', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(29, 'C012', 1, 1, 'serving', '2026-03-17', NULL, NULL),
(30, 'C013', 1, 0, 'serving', '2026-03-17', NULL, NULL),
(31, 'C014', 1, 0, 'done', '2026-03-17', NULL, NULL),
(32, 'C015', 1, 0, 'done', '2026-03-17', NULL, NULL),
(33, 'C016', 1, 0, 'done', '2026-03-17', NULL, NULL),
(34, 'C017', 1, 0, 'done', '2026-03-17', NULL, NULL),
(35, 'C018', 1, 0, 'done', '2026-03-17', NULL, NULL),
(36, 'R001', 2, 0, 'done', '2026-03-17', NULL, NULL),
(37, 'C019', 1, 0, 'done', '2026-03-17', NULL, NULL),
(38, 'C020', 1, 0, 'done', '2026-03-17', NULL, NULL),
(39, 'R002', 2, 0, 'done', '2026-03-17', NULL, NULL),
(40, 'C001', 1, 0, 'done', '2026-03-21', NULL, NULL),
(41, 'C002', 1, 0, 'done', '2026-03-21', NULL, NULL),
(42, 'C003', 1, 0, 'done', '2026-03-21', NULL, NULL),
(43, 'C004', 1, 0, 'done', '2026-03-21', NULL, NULL),
(44, 'C005', 1, 0, 'done', '2026-03-21', NULL, NULL),
(45, 'C006', 1, 0, 'done', '2026-03-21', NULL, NULL),
(46, 'C007', 1, 0, 'done', '2026-03-21', NULL, NULL),
(47, 'C008', 1, 0, 'done', '2026-03-21', NULL, NULL),
(48, 'C009', 1, 0, 'done', '2026-03-21', NULL, NULL),
(49, 'C010', 1, 0, 'done', '2026-03-21', NULL, NULL),
(50, 'C011', 1, 0, 'done', '2026-03-21', NULL, NULL),
(51, 'C012', 1, 0, 'done', '2026-03-21', NULL, NULL),
(52, 'C013', 1, 0, 'done', '2026-03-21', NULL, NULL),
(53, 'C014', 1, 0, 'done', '2026-03-21', NULL, NULL),
(54, 'C015', 1, 1, 'done', '2026-03-21', NULL, NULL),
(55, 'C016', 1, 0, 'done', '2026-03-21', NULL, NULL),
(56, 'C017', 1, 0, 'done', '2026-03-21', NULL, NULL),
(57, 'C018', 1, 0, 'done', '2026-03-21', NULL, NULL),
(58, 'C019', 1, 0, 'done', '2026-03-21', NULL, NULL),
(59, 'C020', 1, 0, 'done', '2026-03-21', NULL, NULL),
(60, 'C021', 1, 0, 'done', '2026-03-21', NULL, NULL),
(61, 'C022', 1, 0, 'done', '2026-03-21', NULL, NULL),
(62, 'C023', 1, 0, 'done', '2026-03-21', NULL, NULL),
(63, 'C024', 1, 0, 'serving', '2026-03-21', NULL, NULL),
(64, 'C001', 1, 0, 'serving', '2026-03-25', NULL, NULL),
(65, 'C002', 1, 1, 'waiting', '2026-03-25', NULL, NULL),
(66, 'C003', 1, 0, 'waiting', '2026-03-25', NULL, NULL),
(67, 'C004', 1, 1, 'waiting', '2026-03-25', NULL, NULL),
(68, 'C005', 1, 1, 'waiting', '2026-03-25', NULL, NULL),
(69, 'C006', 1, 0, 'waiting', '2026-03-25', NULL, NULL),
(70, 'C001', 1, 0, 'done', '2026-03-26', NULL, NULL),
(71, 'C002', 1, 0, 'done', '2026-03-26', NULL, NULL),
(72, 'C003', 1, 0, 'serving', '2026-03-26', NULL, NULL),
(73, 'R001', 2, 0, 'done', '2026-03-26', NULL, NULL),
(74, 'R002', 2, 1, 'done', '2026-03-26', NULL, NULL),
(75, 'R001', 2, 1, 'waiting', '2026-04-07', NULL, NULL),
(76, 'R002', 2, 1, 'waiting', '2026-04-07', NULL, NULL),
(77, 'C001', 1, 1, 'waiting', '2026-04-07', NULL, NULL),
(78, 'R003', 2, 1, 'waiting', '2026-04-07', NULL, NULL),
(79, 'C002', 1, 0, 'waiting', '2026-04-07', NULL, NULL),
(80, 'C003', 1, 0, 'waiting', '2026-04-07', NULL, NULL),
(81, 'C004', 1, 0, 'waiting', '2026-04-07', NULL, NULL),
(82, 'R004', 2, 0, 'waiting', '2026-04-07', NULL, NULL),
(83, 'R005', 2, 1, 'waiting', '2026-04-07', NULL, NULL),
(84, 'C001', 1, 0, 'done', '2026-04-14', NULL, NULL),
(85, 'C002', 1, 0, 'done', '2026-04-14', NULL, NULL),
(86, 'R001', 2, 0, 'done', '2026-04-14', NULL, NULL),
(87, 'C003', 1, 0, 'done', '2026-04-14', NULL, NULL),
(88, 'C004', 1, 1, 'done', '2026-04-14', NULL, NULL),
(89, 'C005', 1, 0, 'serving', '2026-04-14', NULL, NULL),
(90, 'R002', 2, 0, 'done', '2026-04-14', NULL, NULL),
(91, 'R003', 2, 0, 'done', '2026-04-14', NULL, NULL),
(92, 'R004', 2, 1, 'done', '2026-04-14', NULL, NULL),
(93, 'R005', 2, 0, 'waiting', '2026-04-14', NULL, NULL),
(94, 'R006', 2, 1, 'serving', '2026-04-14', NULL, NULL),
(95, 'C006', 1, 0, 'waiting', '2026-04-14', NULL, NULL),
(96, 'C007', 1, 0, 'waiting', '2026-04-14', NULL, NULL),
(97, 'C008', 1, 0, 'waiting', '2026-04-14', NULL, NULL),
(98, 'C009', 1, 0, 'waiting', '2026-04-14', NULL, NULL);

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
(13, 8, 2, '2026-03-15 06:42:37', NULL, NULL, NULL),
(14, 18, 1, '2026-03-17 04:38:39', NULL, NULL, NULL),
(15, 20, 1, '2026-03-17 04:40:15', NULL, NULL, NULL),
(16, 19, 1, '2026-03-17 04:40:16', NULL, NULL, NULL),
(17, 21, 1, '2026-03-17 04:41:22', NULL, NULL, NULL),
(18, 22, 1, '2026-03-17 04:42:25', '2026-03-17 04:42:40', NULL, NULL),
(19, 23, 1, '2026-03-17 05:13:34', NULL, NULL, NULL),
(20, 24, 1, '2026-03-17 05:14:09', NULL, NULL, NULL),
(21, 25, 1, '2026-03-17 05:14:42', NULL, NULL, NULL),
(22, 26, 1, '2026-03-17 05:20:10', NULL, NULL, NULL),
(23, 27, 1, '2026-03-17 05:20:18', '2026-03-17 05:24:20', NULL, NULL),
(24, 28, 1, '2026-03-17 05:29:20', NULL, NULL, NULL),
(25, 29, 1, '2026-03-17 05:29:55', NULL, NULL, NULL),
(26, 30, 1, '2026-03-17 05:35:50', NULL, NULL, NULL),
(27, 31, 1, '2026-03-17 05:35:55', '2026-03-17 05:40:25', NULL, NULL),
(28, 32, 1, '2026-03-17 05:40:25', '2026-03-17 05:40:33', NULL, NULL),
(29, 33, 1, '2026-03-17 05:40:33', '2026-03-17 05:40:48', NULL, NULL),
(30, 34, 1, '2026-03-17 05:46:38', '2026-03-17 05:46:50', NULL, NULL),
(31, 35, 1, '2026-03-17 05:46:50', '2026-03-17 05:49:16', NULL, NULL),
(32, 37, 1, '2026-03-17 05:50:03', '2026-03-17 05:50:09', NULL, NULL),
(33, 38, 1, '2026-03-17 05:50:16', '2026-03-17 05:50:25', NULL, NULL),
(34, 36, 2, '2026-03-17 05:51:09', '2026-03-17 05:51:23', NULL, NULL),
(35, 39, 2, '2026-03-17 05:51:23', '2026-03-17 05:51:34', NULL, NULL),
(36, 40, 1, '2026-03-21 06:25:25', '2026-03-21 06:27:21', NULL, NULL),
(37, 41, 1, '2026-03-21 06:27:21', '2026-03-21 06:42:54', NULL, NULL),
(38, 42, 1, '2026-03-21 06:42:54', '2026-03-21 06:43:23', NULL, NULL),
(39, 43, 1, '2026-03-21 06:43:23', '2026-03-21 06:45:36', NULL, NULL),
(40, 44, 1, '2026-03-21 06:45:36', '2026-03-21 06:46:39', NULL, NULL),
(41, 45, 1, '2026-03-21 06:48:49', '2026-03-21 06:50:08', NULL, NULL),
(42, 46, 1, '2026-03-21 06:50:08', '2026-03-21 06:52:46', NULL, NULL),
(43, 47, 1, '2026-03-21 06:52:46', '2026-03-21 06:53:36', NULL, NULL),
(44, 48, 1, '2026-03-21 06:53:36', '2026-03-21 06:57:20', NULL, NULL),
(45, 49, 1, '2026-03-21 06:57:20', '2026-03-21 07:00:50', NULL, NULL),
(46, 50, 1, '2026-03-21 07:03:58', '2026-03-21 07:04:07', NULL, NULL),
(47, 51, 1, '2026-03-21 07:04:18', '2026-03-21 07:39:45', NULL, NULL),
(48, 54, 1, '2026-03-21 07:39:45', '2026-03-21 07:43:33', NULL, NULL),
(49, 52, 1, '2026-03-21 07:43:33', '2026-03-21 08:04:15', NULL, NULL),
(50, 53, 1, '2026-03-21 08:04:15', '2026-03-21 08:04:42', NULL, NULL),
(51, 55, 1, '2026-03-21 08:04:42', '2026-03-21 08:04:50', NULL, NULL),
(52, 56, 1, '2026-03-21 08:04:50', '2026-03-21 08:04:56', NULL, NULL),
(53, 57, 1, '2026-03-21 08:04:56', '2026-03-21 08:05:02', NULL, NULL),
(54, 58, 1, '2026-03-21 08:05:02', '2026-03-21 08:05:08', NULL, NULL),
(55, 59, 1, '2026-03-21 08:05:08', '2026-03-21 08:05:12', NULL, NULL),
(56, 60, 1, '2026-03-21 08:05:12', '2026-03-21 08:05:20', NULL, NULL),
(57, 61, 1, '2026-03-21 08:05:20', '2026-03-21 08:05:26', NULL, NULL),
(58, 62, 1, '2026-03-21 08:05:26', '2026-03-21 08:05:32', NULL, NULL),
(59, 63, 1, '2026-03-21 08:05:32', NULL, NULL, NULL),
(60, 64, 1, '2026-03-25 09:15:31', NULL, NULL, NULL),
(61, 70, 1, '2026-03-26 10:49:50', '2026-03-26 10:52:28', NULL, NULL),
(62, 71, 1, '2026-03-26 10:55:36', '2026-03-26 10:55:48', NULL, NULL),
(63, 72, 1, '2026-03-26 10:55:48', NULL, NULL, NULL),
(64, 74, 2, '2026-03-26 10:56:42', '2026-03-26 10:56:55', NULL, NULL),
(65, 73, 2, '2026-03-26 10:56:55', '2026-03-26 10:57:05', NULL, NULL),
(66, 84, 1, '2026-04-14 05:16:13', '2026-04-14 05:16:22', NULL, NULL),
(67, 85, 1, '2026-04-14 05:18:24', '2026-04-14 05:18:29', NULL, NULL),
(68, 87, 1, '2026-04-14 05:18:29', '2026-04-14 05:39:28', NULL, NULL),
(69, 86, 2, '2026-04-14 05:19:12', '2026-04-14 05:34:07', NULL, NULL),
(70, 90, 2, '2026-04-14 05:36:05', '2026-04-14 05:36:19', NULL, NULL),
(71, 91, 2, '2026-04-14 05:36:35', '2026-04-14 05:37:50', NULL, NULL),
(72, 92, 2, '2026-04-14 05:37:51', '2026-04-14 05:40:23', NULL, NULL),
(73, 88, 1, '2026-04-14 05:39:28', '2026-04-14 05:39:36', NULL, NULL),
(74, 89, 1, '2026-04-14 05:39:36', NULL, NULL, NULL),
(75, 94, 2, '2026-04-14 05:40:23', NULL, NULL, NULL);

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
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(64) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'walkin_pin', '1981', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','guard') NOT NULL,
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
(7, 'Guard', 'guard@gmail.com', 'guard', 'guard', NULL, NULL, NULL);

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
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_id_unique` (`student_id`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_queue_date` (`queue_date`),
  ADD KEY `queues_student_id_index` (`student_id`),
  ADD KEY `queues_issued_by_index` (`issued_by`);

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_setting_key_unique` (`setting_key`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `queue_calls`
--
ALTER TABLE `queue_calls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
