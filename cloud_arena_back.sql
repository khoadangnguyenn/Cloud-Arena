-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 12, 2026 at 09:50 AM
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
-- Database: `cloud_arena`
--

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

CREATE TABLE `about` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT 'Khóa ngoại trỏ về Admin đã cập nhật nội dung',
  `uptime` varchar(50) DEFAULT NULL COMMENT 'Ví dụ: 99.9%',
  `support` varchar(255) DEFAULT NULL COMMENT 'Ví dụ: 24/7',
  `performance` varchar(255) DEFAULT NULL COMMENT 'Thông tin hiệu năng',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `type` enum('ticket','revenue') NOT NULL,
  `source_key` varchar(120) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `type`, `source_key`, `title`, `message`, `url`, `payload`, `created_at`) VALUES
(1, 'ticket', 'ticket_created:8:2', 'Ticket mới từ guest2', 'Test Guest 2 - guest2@gmail.com', '/admincontacts?user_id=8&contact_id=2', '{\"user_id\":8,\"contact_id\":2}', '2026-05-08 14:43:03'),
(2, 'ticket', 'ticket_created:8:1', 'Ticket mới từ guest', 'Test Guest - guest@gmail.com', '/admincontacts?user_id=8&contact_id=1', '{\"user_id\":8,\"contact_id\":1}', '2026-05-08 14:42:38'),
(3, 'ticket', 'ticket_created:2:4', 'Ticket mới từ Test User 1', 'Fix - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=4', '{\"user_id\":2,\"contact_id\":4}', '2026-05-08 14:12:24'),
(4, 'ticket', 'ticket_created:2:3', 'Ticket mới từ Bảo', 'Ý kiến - guest@picoctf.org', '/admincontacts?user_id=2&contact_id=3', '{\"user_id\":2,\"contact_id\":3}', '2026-05-07 11:57:43'),
(5, 'ticket', 'ticket_created:2:2', 'Ticket mới từ Giang', 'Cần tư vấn gói dịch vụ - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=2', '{\"user_id\":2,\"contact_id\":2}', '2026-05-07 10:08:37'),
(6, 'ticket', 'ticket_created:2:1', 'Ticket mới từ test', 'need test - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=1', '{\"user_id\":2,\"contact_id\":1}', '2026-05-06 08:55:12'),
(7, 'revenue', 'order_completed:15', 'Đơn hàng #15 đã hoàn tất', 'Khách #2 thanh toán 1.000.000đ.', '/admin', '{\"order_id\":15,\"user_id\":2,\"total_amount\":1000000}', '2026-05-05 07:30:00'),
(8, 'revenue', 'order_completed:14', 'Đơn hàng #14 đã hoàn tất', 'Khách #2 thanh toán 2.500.000đ.', '/admin', '{\"order_id\":14,\"user_id\":2,\"total_amount\":2500000}', '2026-05-01 03:00:00'),
(9, 'revenue', 'order_completed:13', 'Đơn hàng #13 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":13,\"user_id\":2,\"total_amount\":500000}', '2026-04-19 02:30:00'),
(10, 'revenue', 'order_completed:12', 'Đơn hàng #12 đã hoàn tất', 'Khách #2 thanh toán 2.000.000đ.', '/admin', '{\"order_id\":12,\"user_id\":2,\"total_amount\":2000000}', '2026-04-11 07:00:00'),
(11, 'revenue', 'order_completed:11', 'Đơn hàng #11 đã hoàn tất', 'Khách #2 thanh toán 1.500.000đ.', '/admin', '{\"order_id\":11,\"user_id\":2,\"total_amount\":1500000}', '2026-04-04 03:00:00'),
(12, 'revenue', 'order_completed:10', 'Đơn hàng #10 đã hoàn tất', 'Khách #2 thanh toán 750.000đ.', '/admin', '{\"order_id\":10,\"user_id\":2,\"total_amount\":750000}', '2026-03-28 04:20:00'),
(13, 'revenue', 'order_completed:9', 'Đơn hàng #9 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":9,\"user_id\":2,\"total_amount\":500000}', '2026-03-22 08:30:00'),
(14, 'revenue', 'order_completed:8', 'Đơn hàng #8 đã hoàn tất', 'Khách #2 thanh toán 1.000.000đ.', '/admin', '{\"order_id\":8,\"user_id\":2,\"total_amount\":1000000}', '2026-03-15 06:00:00'),
(15, 'revenue', 'order_completed:7', 'Đơn hàng #7 đã hoàn tất', 'Khách #2 thanh toán 2.000.000đ.', '/admin', '{\"order_id\":7,\"user_id\":2,\"total_amount\":2000000}', '2026-03-07 02:00:00'),
(16, 'revenue', 'order_completed:6', 'Đơn hàng #6 đã hoàn tất', 'Khách #2 thanh toán 750.000đ.', '/admin', '{\"order_id\":6,\"user_id\":2,\"total_amount\":750000}', '2026-02-25 03:45:00'),
(17, 'revenue', 'order_completed:5', 'Đơn hàng #5 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":5,\"user_id\":2,\"total_amount\":500000}', '2026-02-14 09:00:00'),
(18, 'revenue', 'order_completed:4', 'Đơn hàng #4 đã hoàn tất', 'Khách #2 thanh toán 1.000.000đ.', '/admin', '{\"order_id\":4,\"user_id\":2,\"total_amount\":1000000}', '2026-02-03 04:00:00'),
(19, 'revenue', 'order_completed:3', 'Đơn hàng #3 đã hoàn tất', 'Khách #2 thanh toán 250.000đ.', '/admin', '{\"order_id\":3,\"user_id\":2,\"total_amount\":250000}', '2026-01-20 02:15:00'),
(20, 'revenue', 'order_completed:2', 'Đơn hàng #2 đã hoàn tất', 'Khách #2 thanh toán 750.000đ.', '/admin', '{\"order_id\":2,\"user_id\":2,\"total_amount\":750000}', '2026-01-12 07:30:00'),
(21, 'revenue', 'order_completed:1', 'Đơn hàng #1 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":1,\"user_id\":2,\"total_amount\":500000}', '2026-01-05 03:00:00'),
(295, 'ticket', 'ticket_created:8:3', 'Ticket mới từ Khách', 'Test chức năng noti - khach@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3}', '2026-05-10 03:35:10'),
(576, 'ticket', 'ticket_created:2:5', 'Ticket mới từ Test User 1', 'test chức năng noti - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=5', '{\"user_id\":2,\"contact_id\":5}', '2026-05-10 03:36:41'),
(995, 'ticket', 'ticket_created:4:1', 'Ticket mới từ Test User 2', 'Test chức năng noti 2 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1}', '2026-05-10 03:43:45'),
(1657, 'ticket', 'ticket_created:8:4', 'Ticket mới từ khách', 'heheheheheh - khach@gmail.com', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4}', '2026-05-10 03:45:49'),
(2627, 'ticket', 'ticket_created:8:5', 'Ticket mới từ baaaaaa', 'weneedba - ba@gmail.com', '/admincontacts?user_id=8&contact_id=5', '{\"user_id\":8,\"contact_id\":5}', '2026-05-10 03:51:06'),
(2628, 'ticket', 'ticket_created:8:6', 'Ticket mới từ giang', 'need test - giang@gmail.com', '/admincontacts?user_id=8&contact_id=6', '{\"user_id\":8,\"contact_id\":6}', '2026-05-10 03:51:38'),
(3825, 'ticket', 'ticket_created:8:3:20260510155304', 'Ticket mới từ giang', 'Cần tư vấn - giang@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 15:53:04\"}', '2026-05-10 08:53:04'),
(3826, 'ticket', 'ticket_created:2:5:20260510153641', 'Ticket mới từ Test User 1', 'test chức năng noti - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=5', '{\"user_id\":2,\"contact_id\":5,\"created_at\":\"2026-05-10 15:36:41\"}', '2026-05-10 08:36:41'),
(3827, 'ticket', 'ticket_created:8:2:20260508214303', 'Ticket mới từ guest2', 'Test Guest 2 - guest2@gmail.com', '/admincontacts?user_id=8&contact_id=2', '{\"user_id\":8,\"contact_id\":2,\"created_at\":\"2026-05-08 21:43:03\"}', '2026-05-08 14:43:03'),
(3828, 'ticket', 'ticket_created:8:1:20260508214238', 'Ticket mới từ guest', 'Test Guest - guest@gmail.com', '/admincontacts?user_id=8&contact_id=1', '{\"user_id\":8,\"contact_id\":1,\"created_at\":\"2026-05-08 21:42:38\"}', '2026-05-08 14:42:38'),
(3829, 'ticket', 'ticket_created:2:4:20260508211224', 'Ticket mới từ Test User 1', 'Fix - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=4', '{\"user_id\":2,\"contact_id\":4,\"created_at\":\"2026-05-08 21:12:24\"}', '2026-05-08 14:12:24'),
(3830, 'ticket', 'ticket_created:2:3:20260507185743', 'Ticket mới từ Bảo', 'Ý kiến - guest@picoctf.org', '/admincontacts?user_id=2&contact_id=3', '{\"user_id\":2,\"contact_id\":3,\"created_at\":\"2026-05-07 18:57:43\"}', '2026-05-07 11:57:43'),
(3831, 'ticket', 'ticket_created:2:2:20260507170837', 'Ticket mới từ Giang', 'Cần tư vấn gói dịch vụ - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=2', '{\"user_id\":2,\"contact_id\":2,\"created_at\":\"2026-05-07 17:08:37\"}', '2026-05-07 10:08:37'),
(3832, 'ticket', 'ticket_created:2:1:20260506155512', 'Ticket mới từ test', 'need test - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=1', '{\"user_id\":2,\"contact_id\":1,\"created_at\":\"2026-05-06 15:55:12\"}', '2026-05-06 08:55:12'),
(3833, 'ticket', 'ticket_created:8:4:20260510110430', 'Ticket mới từ giang', 'Cần tư vấn - guest@picoctf.org', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4,\"created_at\":\"2026-05-10 11:04:30\"}', '2026-05-10 04:04:30'),
(3834, 'ticket', 'ticket_created:8:4:20260510160430', 'Ticket mới từ giang', 'Cần tư vấn - guest@picoctf.org', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4,\"created_at\":\"2026-05-10 16:04:30\"}', '2026-05-10 09:04:30'),
(3835, 'ticket', 'ticket_created:2:6:20260510110524', 'Ticket mới từ Test User 1', 'Test 2 - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=6', '{\"user_id\":2,\"contact_id\":6,\"created_at\":\"2026-05-10 11:05:24\"}', '2026-05-10 04:05:24'),
(3836, 'ticket', 'ticket_created:2:6:20260510160524', 'Ticket mới từ Test User 1', 'Test 2 - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=6', '{\"user_id\":2,\"contact_id\":6,\"created_at\":\"2026-05-10 16:05:24\"}', '2026-05-10 09:05:24'),
(3837, 'ticket', 'ticket_created:4:1:20260510110614', 'Ticket mới từ Test User 2', 'Test 21111 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1,\"created_at\":\"2026-05-10 11:06:14\"}', '2026-05-10 04:06:14'),
(3838, 'ticket', 'ticket_created:4:1:20260510160614', 'Ticket mới từ Test User 2', 'Test 21111 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1,\"created_at\":\"2026-05-10 16:06:14\"}', '2026-05-10 09:06:14'),
(3839, 'ticket', 'ticket_created:8:3:20260510110659', 'Ticket mới từ Giang', 'need test - alexngo4work@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 11:06:59\"}', '2026-05-10 04:06:59'),
(3840, 'ticket', 'ticket_created:8:5:20260510110719', 'Ticket mới từ Instructor Test', '1111 - admin@example.com', '/admincontacts?user_id=8&contact_id=5', '{\"user_id\":8,\"contact_id\":5,\"created_at\":\"2026-05-10 11:07:19\"}', '2026-05-10 04:07:19'),
(3841, 'ticket', 'ticket_created:8:5:20260510160719', 'Ticket mới từ Instructor Test', '1111 - admin@example.com', '/admincontacts?user_id=8&contact_id=5', '{\"user_id\":8,\"contact_id\":5,\"created_at\":\"2026-05-10 16:07:19\"}', '2026-05-10 09:07:19'),
(3842, 'ticket', 'ticket_created:8:3:20260510160659', 'Ticket mới từ Giang', 'need test - alexngo4work@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:06:59\"}', '2026-05-10 09:06:59'),
(3843, 'ticket', 'ticket_created:8:3:20260510161240', 'Ticket mới từ giang', 'Fix - lemdien258@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:12:40\"}', '2026-05-10 09:12:40'),
(3844, 'ticket', 'ticket_created:2:6:20260510161305', 'Ticket mới từ Test User 1', 'Fix2 - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=6', '{\"user_id\":2,\"contact_id\":6,\"created_at\":\"2026-05-10 16:13:05\"}', '2026-05-10 09:13:05'),
(3845, 'ticket', 'ticket_created:4:1:20260510161319', 'Ticket mới từ Test User 2', 'Fix3 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1,\"created_at\":\"2026-05-10 16:13:19\"}', '2026-05-10 09:13:19'),
(3846, 'ticket', 'ticket_created:8:3:20260510162027', 'Ticket mới từ Giang', 'Cần tư vấn - giang.ngolame@hcmut.edu.vn', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:20:27\"}', '2026-05-10 09:20:27'),
(3847, 'ticket', 'ticket_created:8:3:20260510162059', 'Ticket mới từ giang', 'Test 2 - lemdien258@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:20:59\"}', '2026-05-10 09:20:59'),
(3848, 'ticket', 'ticket_created:8:4:20260510162109', 'Ticket mới từ Instructor Test', 'Asking - admin@example.com', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4,\"created_at\":\"2026-05-10 16:21:09\"}', '2026-05-10 09:21:09'),
(3849, 'revenue', 'order_completed:16', 'Đơn hàng #16 đã hoàn tất', 'Khách #4 thanh toán 500.000đ.', '/admin', '{\"order_id\":16,\"user_id\":4,\"total_amount\":500000}', '2026-05-10 12:07:04'),
(3850, 'ticket', 'ticket_created:8:3:20260510203504', 'Ticket mới từ khách', 'test rate - khach@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 20:35:04\"}', '2026-05-10 13:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai (Guest)',
  `session_id` varchar(100) DEFAULT NULL COMMENT 'Session ID dành cho khách chưa đăng nhập',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `duration_months` int(11) DEFAULT 1 COMMENT 'Số tháng khách muốn thuê'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Gaming Server', 'gaming-server', 'Máy chủ chuyên biệt cho game'),
(2, 'Web Hosting', 'web-hosting', 'Hosting cho website và ứng dụng web'),
(3, 'Shared Hosting', 'shared-hosting', 'Hosting chia sẻ với giá cạnh tranh');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `user_id` int(11) NOT NULL COMMENT 'Thực thể cha. Nếu là khách, gán ID của tài khoản Guest mặc định',
  `contact_id` int(11) NOT NULL COMMENT 'Partial Key',
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`user_id`, `contact_id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(2, 1, 'test', 'test@cloudarena.local', 'need test', 'i wanna help', 'replied', '2026-05-06 08:55:12'),
(2, 2, 'Giang', 'test@cloudarena.local', 'Cần tư vấn gói dịch vụ', 'Dịch vụ này tôi muốn biểt nó có thể tương thích với gói modpack nào ?', 'replied', '2026-05-07 10:08:37'),
(2, 3, 'Bảo', 'guest@picoctf.org', 'Ý kiến', 'Tôi không có ý kiến gì', 'replied', '2026-05-07 11:57:43'),
(2, 5, 'Test User 1', 'test@cloudarena.local', 'test chức năng noti', 'letscheckitout', 'unread', '2026-05-10 08:36:41'),
(8, 1, 'guest', 'guest@gmail.com', 'Test Guest', 'I wanna contact', 'read', '2026-05-08 14:42:38'),
(8, 2, 'guest2', 'guest2@gmail.com', 'Test Guest 2', 'I wanna contact 2', 'unread', '2026-05-08 14:43:03'),
(8, 4, 'Instructor Test', 'admin@example.com', 'Asking', 'aaaaaaaaaaaaaa', 'replied', '2026-05-10 09:21:09');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `status` enum('active','hidden') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `admin_id` int(11) NOT NULL COMMENT 'Thực thể cha: Admin',
  `media_id` int(11) NOT NULL COMMENT 'Partial Key',
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `admin_id` int(11) NOT NULL COMMENT 'Thực thể cha: Admin',
  `news_id` int(11) NOT NULL COMMENT 'Partial Key',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Thuộc tính dẫn xuất: Tổng tiền của đơn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `total_amount`, `created_at`) VALUES
(1, 2, 'completed', 500000.00, '2026-01-05 03:00:00'),
(2, 2, 'completed', 750000.00, '2026-01-12 07:30:00'),
(3, 2, 'completed', 250000.00, '2026-01-20 02:15:00'),
(4, 2, 'completed', 1000000.00, '2026-02-03 04:00:00'),
(5, 2, 'completed', 500000.00, '2026-02-14 09:00:00'),
(6, 2, 'completed', 750000.00, '2026-02-25 03:45:00'),
(7, 2, 'completed', 2000000.00, '2026-03-07 02:00:00'),
(8, 2, 'completed', 1000000.00, '2026-03-15 06:00:00'),
(9, 2, 'completed', 500000.00, '2026-03-22 08:30:00'),
(10, 2, 'completed', 750000.00, '2026-03-28 04:20:00'),
(11, 2, 'completed', 1500000.00, '2026-04-04 03:00:00'),
(12, 2, 'completed', 2000000.00, '2026-04-11 07:00:00'),
(13, 2, 'completed', 500000.00, '2026-04-19 02:30:00'),
(14, 2, 'completed', 2500000.00, '2026-05-01 03:00:00'),
(15, 2, 'completed', 1000000.00, '2026-05-05 07:30:00'),
(16, 4, 'completed', 500000.00, '2026-05-10 12:07:04');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL COMMENT 'Lưu giá cứng tại thời điểm chốt đơn',
  `quantity` int(11) DEFAULT 1,
  `duration_months` int(11) DEFAULT 1 COMMENT 'Số tháng thực tế đã thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL COMMENT 'Lưu mã HTML của trang tĩnh',
  `status` enum('published','draft') DEFAULT 'published',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `ram_mb` int(11) DEFAULT NULL,
  `cpu_cores` int(11) DEFAULT NULL,
  `disk_gb` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('active','hidden') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `ram_mb`, `cpu_cores`, `disk_gb`, `image_url`, `status`, `created_at`) VALUES
(1, 1, 'Gaming Pro 4GB', 'gaming-pro-4gb', 'Server gaming với 4GB RAM, lý tưởng cho game nhỏ', 500000.00, 4096, 4, 50, NULL, 'active', '2026-05-06 08:22:16'),
(2, 1, 'Gaming Pro 8GB', 'gaming-pro-8gb', 'Server gaming với 8GB RAM, hỗ trợ game lớn', 1000000.00, 8192, 8, 100, NULL, 'active', '2026-05-06 08:22:16'),
(3, 1, 'Gaming Pro 16GB', 'gaming-pro-16gb', 'Server gaming cao cấp với 16GB RAM', 2000000.00, 16384, 16, 200, NULL, 'active', '2026-05-06 08:22:16'),
(4, 2, 'Web Basic', 'web-basic', 'Hosting web cơ bản', 250000.00, 2048, 2, 20, NULL, 'active', '2026-05-06 08:22:16'),
(5, 2, 'Web Plus', 'web-plus', 'Hosting web nâng cao', 750000.00, 4096, 4, 50, NULL, 'active', '2026-05-06 08:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `user_id` int(11) NOT NULL COMMENT 'Thực thể cha: Member',
  `review_id` int(11) NOT NULL COMMENT 'Partial Key',
  `product_id` int(11) DEFAULT NULL COMMENT 'XOR: Nhắm tới Sản phẩm',
  `target_admin_id` int(11) DEFAULT NULL COMMENT 'XOR: Nhắm tới cụm khóa của News (admin_id)',
  `target_news_id` int(11) DEFAULT NULL COMMENT 'XOR: Nhắm tới cụm khóa của News (news_id)',
  `rating` int(11) DEFAULT 5,
  `comment` text NOT NULL,
  `status` enum('pending','approved','hidden') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key_name` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key_name`, `value`) VALUES
('about_heading_highlight', 'Chúng Tôi'),
('about_heading_prefix', 'Về'),
('about_para1', 'Cloud Arena tự hào là đơn vị tiên phong trong việc cung cấp các giải pháp máy chủ game hiệu năng cao tại Việt Nam.'),
('about_para2', 'Với đội ngũ kỹ thuật giàu kinh nghiệm và hạ tầng mạng băng thông rộng, chúng tôi cam kết mang lại trải nghiệm chơi game mượt mà nhất cho cộng đồng.'),
('admin_notification_state_1', '{\"last_opened_id\":3850,\"last_opened_at\":\"2026-05-10 20:35:04\"}'),
('contact_page_intro', 'Gửi ticket hỗ trợ cho chúng tôi. Đội ngũ sẽ phản hồi sớm nhất có thể.'),
('contact_page_title', 'Liên Hệ'),
('contact_sidebar_title', 'Thông tin liên hệ'),
('contact_ticket_meta_2_1', '{\"priority\":\"high\",\"admin_reply\":\"no\",\"replied_at\":\"2026-05-06 10:58:51\"}'),
('contact_ticket_meta_2_2', '{\"priority\":\"normal\",\"admin_reply\":\"\\u1ee8ng v\\u1edbi Vanilla b\\u1ea1n nh\\u00e9 !\",\"replied_at\":\"2026-05-08 15:52:24\"}'),
('contact_ticket_meta_2_3', '{\"priority\":\"normal\",\"admin_reply\":\"h\\u1ea3\",\"replied_at\":\"2026-05-07 15:26:15\"}'),
('contact_ticket_meta_2_5', '{\"priority\":\"high\",\"admin_reply\":null,\"replied_at\":null}'),
('contact_ticket_meta_8_1', '{\"priority\":\"high\",\"admin_reply\":null,\"replied_at\":null}'),
('contact_ticket_meta_8_2', '{\"priority\":\"normal\",\"admin_reply\":null,\"replied_at\":null}'),
('contact_ticket_meta_8_4', '{\"priority\":\"low\",\"admin_reply\":\"ok bro\",\"replied_at\":\"2026-05-10 14:05:05\"}'),
('home_about_feat1_text', 'Sử dụng CPU Intel Core i9 & AMD Ryzen mới nhất, cùng ổ cứng NVMe Gen4 cho tốc độ xử lý vượt trội.'),
('home_about_feat1_title', 'Hiệu năng tối đa'),
('home_about_feat2_text', 'Lớp bảo vệ đa tầng giúp lọc bỏ các cuộc tấn công DDoS lên đến hàng trăm Gbps, giữ server luôn ổn định.'),
('home_about_feat2_title', 'Anti-DDoS mạnh mẽ'),
('home_about_feat3_text', 'Dữ liệu của bạn luôn an toàn với hệ thống sao lưu tự động hàng ngày. Khôi phục nhanh chóng khi cần thiết.'),
('home_about_feat3_title', 'Backup tự động'),
('home_about_heading', 'Tại sao chọn G-SERVER?'),
('home_about_kicker', 'Tính năng vượt trội'),
('home_about_lead', 'Nền tảng tập trung cho cộng đồng game thủ: triển khai nhanh, bảo mật cao và vận hành ổn định xuyên suốt.'),
('home_card_tech_title', 'Năng lực công nghệ'),
('home_hero_bg_image', 'hero_bg_bee5ab4cb1755332d8cb6d17dc0d5ad7.gif'),
('home_hero_subtitle', 'Máy chủ game chuyên nghiệp với hiệu năng cao, hỗ trợ modpack và quản lý dễ dàng. Khởi động Server chỉ trong vài phút với công nghệ ảo hóa tiên tiến nhất.'),
('home_hero_title_gradient', 'Game Server Hosting'),
('home_hero_title_plain', 'Cho Mọi Game Thủ'),
('home_product_ids', '5,1,2,3'),
('home_review_key', ''),
('profile_avatar_hint', 'JPG, PNG, GIF, WEBP. Tối đa 2MB.'),
('profile_avatar_upload_label', 'Tải ảnh lên'),
('profile_btn_save', 'Lưu thay đổi'),
('profile_btn_update_password', 'Cập nhật mật khẩu'),
('profile_label_confirm_password', 'Xác nhận mật khẩu'),
('profile_label_current_password', 'Mật khẩu hiện tại'),
('profile_label_display_name', 'Họ tên hiển thị'),
('profile_label_email', 'Địa chỉ email'),
('profile_label_new_password', 'Mật khẩu mới'),
('profile_page_intro', 'Quản lý thông tin tài khoản và bảo mật.'),
('profile_page_title', 'Hồ sơ thành viên'),
('profile_section_avatar_title', 'Ảnh đại diện'),
('profile_section_password_title', 'Đổi mật khẩu'),
('profile_section_personal_title', 'Thông tin cá nhân'),
('site_about_snippet', 'Nền tảng cho thuê Game Server ổn định, hiệu năng cao.'),
('site_address', '268 Lý Thường Kiệt, Q10, TP.HCM'),
('site_contact_email', 'contact@gameserver.vn'),
('site_hotline', '0123 456 789'),
('site_logo_image', 'brand_logo_20260508144225_50ea750b.png'),
('site_logo_text', 'G-SERVER'),
('site_map_embed_url', 'https://www.google.com/maps?q=268+Ly+Thuong+Kiet+Q10+TPHCM&output=embed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Lưu trữ mật khẩu đã được hash (mã hóa)',
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL COMMENT 'Thuộc tính phức hợp (Họ và Tên)',
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','banned') DEFAULT 'active',
  `reset_token` varchar(255) DEFAULT NULL,
  `role` enum('admin','member') NOT NULL COMMENT 'Xử lý phân cấp Disjoint (Admin hoặc Member)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `avatar`, `status`, `reset_token`, `role`, `created_at`) VALUES
(1, 'admin', '$2a$10$iYI.B2yyF7i75alKEO6XHeUMfcZJgz52DTg67oggoVxUVqyBHZmvS', 'admin@cloudarena.local', 'Administrator 1', '/uploads/avatars/admin.png', 'active', NULL, 'admin', '2026-05-06 08:22:16'),
(2, 'testuser', '$2y$10$PfYcDZ13nUgxtOdBsX/FPuWwnxxvJXBPZ2sqMviPZFk.H34fobDLi', 'test@cloudarena.local', 'Test User 1', '/uploads/avatars/av_ba55501f0ec6a5a3769658f31dbf9834.png', 'active', NULL, 'member', '2026-05-06 08:22:16'),
(4, 'testuser2', '$2y$10$/gT5cjOikjQ1BBF/GvhtcubPIEc0xLbpvSr8WMkVDQskfi4sjcTYe', 'test2@cloudarena.local', 'Test User 2', '/uploads/avatars/testuser2.jpg', 'active', NULL, 'member', '2026-05-07 12:41:56'),
(8, 'guest_contact', '$2y$10$jmxg/heRebqUTZev3BYM/.zRGGIvN2k9MR2Fl1l6hT2fQXasUgCGW', 'guest@cloud-arena.local', 'Guest Contact', NULL, 'active', NULL, 'member', '2026-05-08 14:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_services`
--

CREATE TABLE `user_services` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `status` enum('active','suspended','expired') DEFAULT 'active',
  `current_ram_mb` int(11) DEFAULT NULL COMMENT 'Lưu trữ RAM hiện tại (hỗ trợ tính năng mua thêm RAM)',
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_about_admin_id` (`admin_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admin_notifications_source_key` (`source_key`),
  ADD KEY `idx_admin_notifications_created_at` (`created_at`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carts_user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_items_cart_id` (`cart_id`),
  ADD KEY `fk_cart_items_product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_slug` (`slug`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`user_id`,`contact_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`admin_id`,`media_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`admin_id`,`news_id`),
  ADD UNIQUE KEY `uq_news_slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order_id` (`order_id`),
  ADD KEY `fk_order_items_product_id` (`product_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pages_slug` (`slug`),
  ADD KEY `fk_pages_admin_id` (`admin_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_slug` (`slug`),
  ADD KEY `fk_products_category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`user_id`,`review_id`),
  ADD KEY `fk_reviews_product_id` (`product_id`),
  ADD KEY `fk_reviews_news` (`target_admin_id`,`target_news_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indexes for table `user_services`
--
ALTER TABLE `user_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_services_user_id` (`user_id`),
  ADD KEY `fk_user_services_product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about`
--
ALTER TABLE `about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3851;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_services`
--
ALTER TABLE `user_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `about`
--
ALTER TABLE `about`
  ADD CONSTRAINT `fk_about_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_carts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart_id` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `fk_contacts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `fk_media_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `fk_pages_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_news` FOREIGN KEY (`target_admin_id`,`target_news_id`) REFERENCES `news` (`admin_id`, `news_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_services`
--
ALTER TABLE `user_services`
  ADD CONSTRAINT `fk_user_services_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_services_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
