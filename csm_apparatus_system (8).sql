-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 01:01 PM
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
-- Database: `csm_apparatus_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `description`, `timestamp`, `ip_address`) VALUES
(1, 1, 'Logout', 'User logged out.', '2025-10-26 21:09:42', '::1'),
(2, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 21:11:16', '::1'),
(3, 1, 'Logout', 'User logged out.', '2025-10-26 21:14:23', '::1'),
(4, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 21:19:49', '::1'),
(5, 1, 'Logout', 'User logged out.', '2025-10-26 21:45:09', '::1'),
(6, 12, 'Login', 'WMSU student login.', '2025-10-26 21:45:20', '::1'),
(7, 12, 'Logout', 'User logged out.', '2025-10-26 21:53:53', '::1'),
(8, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 21:53:59', '::1'),
(9, 1, 'Logout', 'User logged out.', '2025-10-26 22:08:23', '::1'),
(10, 12, 'Login', 'WMSU student login.', '2025-10-26 22:08:31', '::1'),
(11, 12, 'Logout', 'User logged out.', '2025-10-26 22:12:05', '::1'),
(12, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 22:12:06', '::1'),
(13, 1, 'Logout', 'User logged out.', '2025-10-26 22:22:01', '::1'),
(14, 12, 'Login', 'WMSU student login.', '2025-10-26 22:22:09', '::1'),
(15, 12, 'Logout', 'User logged out.', '2025-10-26 22:31:52', '::1'),
(16, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 22:35:20', '::1'),
(17, 1, 'Logout', 'User logged out.', '2025-10-26 22:35:25', '::1'),
(18, 12, 'Login', 'WMSU student login.', '2025-10-26 22:35:33', '::1'),
(19, 12, 'Logout', 'User logged out.', '2025-10-26 22:39:12', '::1'),
(20, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 22:39:13', '::1'),
(21, 1, 'Logout', 'User logged out.', '2025-10-26 22:39:25', '::1'),
(22, 12, 'Login', 'WMSU student login.', '2025-10-26 22:39:33', '::1'),
(23, 12, 'Logout', 'User logged out.', '2025-10-26 22:49:21', '::1'),
(24, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 22:49:24', '::1'),
(25, 1, 'Logout', 'User logged out.', '2025-10-26 22:58:15', '::1'),
(26, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 22:58:16', '::1'),
(27, 1, 'Logout', 'User logged out.', '2025-10-26 23:18:22', '::1'),
(28, 12, 'Login', 'WMSU student login.', '2025-10-26 23:18:30', '::1'),
(29, 12, 'Logout', 'User logged out.', '2025-10-26 23:21:06', '::1'),
(30, 1, 'Login', 'Admin or faculty login successful.', '2025-10-26 23:21:07', '::1'),
(31, 1, 'Logout', 'User logged out.', '2025-10-26 23:57:09', '::1'),
(32, 12, 'Login', 'WMSU student login.', '2025-10-26 23:57:24', '::1'),
(33, 12, 'Logout', 'User logged out.', '2025-10-27 00:02:06', '::1'),
(34, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 00:02:08', '::1'),
(35, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 00:05:25', '::1'),
(36, 1, 'Logout', 'User logged out.', '2025-10-27 00:29:52', '::1'),
(37, 12, 'Login', 'WMSU student login.', '2025-10-27 00:30:01', '::1'),
(38, 12, 'Logout', 'User logged out.', '2025-10-27 00:37:10', '::1'),
(39, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 00:37:13', '::1'),
(40, 1, 'Logout', 'User logged out.', '2025-10-27 00:51:30', '::1'),
(41, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 00:51:33', '::1'),
(42, 4, 'Logout', 'User logged out.', '2025-10-27 00:51:45', '::1'),
(43, 12, 'Login', 'WMSU student login.', '2025-10-27 00:51:55', '::1'),
(44, 12, 'Borrow Request', 'Requested Beaker (Qty: 19)', '2025-10-27 00:56:10', '::1'),
(45, 12, 'Logout', 'User logged out.', '2025-10-27 00:56:22', '::1'),
(46, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 00:56:24', '::1'),
(47, 4, 'Approve Request', 'Approved request #7', '2025-10-27 00:57:09', '::1'),
(48, 4, 'Logout', 'User logged out.', '2025-10-27 00:57:14', '::1'),
(49, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 00:57:19', '::1'),
(50, 1, 'Release Apparatus', 'Released apparatus for request #7', '2025-10-27 00:57:37', '::1'),
(51, 1, 'Logout', 'User logged out.', '2025-10-27 01:15:58', '::1'),
(52, 12, 'Login', 'WMSU student login.', '2025-10-27 01:16:05', '::1'),
(53, 12, 'Logout', 'User logged out.', '2025-10-27 01:16:21', '::1'),
(54, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 01:16:24', '::1'),
(55, 1, 'Logout', 'User logged out.', '2025-10-27 01:26:59', '::1'),
(56, 12, 'Login', 'WMSU student login.', '2025-10-27 01:27:05', '::1'),
(57, 12, 'Logout', 'User logged out.', '2025-10-27 01:27:09', '::1'),
(58, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 01:27:11', '::1'),
(59, 1, 'Logout', 'User logged out.', '2025-10-27 01:50:46', '::1'),
(60, 12, 'Login', 'WMSU student login.', '2025-10-27 01:50:57', '::1'),
(61, 12, 'Logout', 'User logged out.', '2025-10-27 01:55:31', '::1'),
(62, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 01:55:34', '::1'),
(63, 4, 'Logout', 'User logged out.', '2025-10-27 01:57:00', '::1'),
(64, 12, 'Login', 'WMSU student login.', '2025-10-27 01:59:14', '::1'),
(65, 12, 'Logout', 'User logged out.', '2025-10-27 01:59:22', '::1'),
(66, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 01:59:24', '::1'),
(67, 4, 'Logout', 'User logged out.', '2025-10-27 02:11:20', '::1'),
(68, 12, 'Login', 'WMSU student login.', '2025-10-27 02:11:29', '::1'),
(69, 12, 'Borrow Request', 'Requested Beaker (Qty: 1)', '2025-10-27 02:21:02', '::1'),
(70, 12, 'Logout', 'User logged out.', '2025-10-27 02:21:09', '::1'),
(71, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 02:21:12', '::1'),
(72, 1, 'Return Apparatus', 'Marked request #7 as returned', '2025-10-27 02:21:47', '::1'),
(73, 1, 'Logout', 'User logged out.', '2025-10-27 02:32:08', '::1'),
(74, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 02:55:03', '::1'),
(75, 1, 'Logout', 'User logged out.', '2025-10-27 03:06:37', '::1'),
(76, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:06:42', '::1'),
(77, 4, 'Logout', 'User logged out.', '2025-10-27 03:07:00', '::1'),
(78, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:07:03', '::1'),
(79, 1, 'Logout', 'User logged out.', '2025-10-27 03:08:40', '::1'),
(80, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:08:43', '::1'),
(81, 4, 'Logout', 'User logged out.', '2025-10-27 03:11:44', '::1'),
(82, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:11:48', '::1'),
(83, 1, 'Logout', 'User logged out.', '2025-10-27 03:12:14', '::1'),
(84, 12, 'Login', 'WMSU student login.', '2025-10-27 03:12:23', '::1'),
(85, 12, 'Logout', 'User logged out.', '2025-10-27 03:13:03', '::1'),
(86, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:13:08', '::1'),
(87, 1, 'Delete User', 'Deleted user ID: 2', '2025-10-27 03:32:01', '::1'),
(88, 1, 'Delete User', 'Deleted user ID: 2', '2025-10-27 03:32:52', '::1'),
(89, 1, 'Delete User', 'Deleted user ID: 2', '2025-10-27 03:33:10', '::1'),
(90, 1, 'Delete User', 'Deleted user ID: 2', '2025-10-27 03:33:12', '::1'),
(91, 1, 'Delete User', 'Deleted user ID: 2', '2025-10-27 03:33:22', '::1'),
(92, 1, 'Logout', 'User logged out.', '2025-10-27 03:33:50', '::1'),
(93, 12, 'Login', 'WMSU student login.', '2025-10-27 03:33:59', '::1'),
(94, 12, 'Logout', 'User logged out.', '2025-10-27 03:34:12', '::1'),
(95, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:34:14', '::1'),
(96, 1, 'Logout', 'User logged out.', '2025-10-27 03:34:53', '::1'),
(97, 12, 'Login', 'WMSU student login.', '2025-10-27 03:35:14', '::1'),
(98, 12, 'Logout', 'User logged out.', '2025-10-27 03:35:39', '::1'),
(99, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:35:40', '::1'),
(100, 1, 'Logout', 'User logged out.', '2025-10-27 03:42:24', '::1'),
(101, 12, 'Login', 'WMSU student login.', '2025-10-27 03:42:33', '::1'),
(102, 12, 'Logout', 'User logged out.', '2025-10-27 03:42:53', '::1'),
(103, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:42:55', '::1'),
(104, 1, 'Logout', 'User logged out.', '2025-10-27 03:46:03', '::1'),
(105, 12, 'Login', 'WMSU student login.', '2025-10-27 03:46:40', '::1'),
(106, 12, 'Borrow Request', 'Requested Bunsen Burner (Qty: 9)', '2025-10-27 03:47:28', '::1'),
(107, 12, 'Logout', 'User logged out.', '2025-10-27 03:47:38', '::1'),
(108, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:47:41', '::1'),
(109, 4, 'Approve Request', 'Approved request #9', '2025-10-27 03:47:51', '::1'),
(110, 4, 'Logout', 'User logged out.', '2025-10-27 03:47:54', '::1'),
(111, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 03:47:57', '::1'),
(112, 1, 'Release Apparatus', 'Released apparatus for request #9', '2025-10-27 03:48:05', '::1'),
(113, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 10:51:13', '::1'),
(114, 1, 'Logout', 'User logged out.', '2025-10-27 10:52:37', '::1'),
(115, 12, 'Login', 'WMSU student login.', '2025-10-27 10:53:01', '::1'),
(116, 12, 'Logout', 'User logged out.', '2025-10-27 10:54:13', '::1'),
(117, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 10:54:15', '::1'),
(118, 1, 'Logout', 'User logged out.', '2025-10-27 10:55:42', '::1'),
(119, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 10:55:47', '::1'),
(120, 4, 'Logout', 'User logged out.', '2025-10-27 10:56:00', '::1'),
(121, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 10:59:11', '::1'),
(122, 4, 'Logout', 'User logged out.', '2025-10-27 11:01:44', '::1'),
(123, 13, 'Login', 'New WMSU student auto-registered.', '2025-10-27 11:03:09', '::1'),
(124, 13, 'Logout', 'User logged out.', '2025-10-27 11:03:25', '::1'),
(125, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 11:03:30', '::1'),
(126, 1, 'Logout', 'User logged out.', '2025-10-27 11:03:51', '::1'),
(127, 12, 'Login', 'WMSU student login.', '2025-10-27 11:04:04', '::1'),
(128, 12, 'Logout', 'User logged out.', '2025-10-27 11:04:19', '::1'),
(129, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 11:21:19', '::1'),
(130, 1, 'Logout', 'User logged out.', '2025-10-27 13:46:06', '::1'),
(131, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 13:54:09', '::1'),
(132, 4, 'Logout', 'User logged out.', '2025-10-27 13:54:13', '::1'),
(133, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 13:54:15', '::1'),
(134, 1, 'Logout', 'User logged out.', '2025-10-27 14:02:16', '::1'),
(135, 12, 'Login', 'WMSU student login.', '2025-10-27 14:02:30', '::1'),
(136, 12, 'Logout', 'User logged out.', '2025-10-27 14:07:18', '::1'),
(137, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:11:51', '::1'),
(138, 1, 'Logout', 'User logged out.', '2025-10-27 14:12:10', '::1'),
(139, 13, 'Login', 'WMSU student login.', '2025-10-27 14:12:25', '::1'),
(140, 13, 'Borrow Request', 'Requested Bunsen Burner (Qty: 1)', '2025-10-27 14:17:42', '::1'),
(141, 13, 'Logout', 'User logged out.', '2025-10-27 14:18:33', '::1'),
(142, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:18:34', '::1'),
(143, 1, 'Logout', 'User logged out.', '2025-10-27 14:19:06', '::1'),
(144, 12, 'Login', 'WMSU student login.', '2025-10-27 14:19:22', '::1'),
(145, 12, 'Logout', 'User logged out.', '2025-10-27 14:22:57', '::1'),
(146, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:22:59', '::1'),
(147, 1, 'Approve Request', 'Approved request #10', '2025-10-27 14:24:00', '::1'),
(148, 1, 'Logout', 'User logged out.', '2025-10-27 14:24:03', '::1'),
(149, 12, 'Login', 'WMSU student login.', '2025-10-27 14:24:16', '::1'),
(150, 12, 'Borrow Request', 'Requested Thermometer (Qty: 4)', '2025-10-27 14:25:49', '::1'),
(151, 12, 'Logout', 'User logged out.', '2025-10-27 14:25:59', '::1'),
(152, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:26:01', '::1'),
(153, 1, 'Approve Request', 'Approved request #11', '2025-10-27 14:27:50', '::1'),
(154, 1, 'Logout', 'User logged out.', '2025-10-27 14:40:21', '::1'),
(155, 12, 'Login', 'WMSU student login.', '2025-10-27 14:40:29', '::1'),
(156, 12, 'Borrow Request', 'Requested Thermometer (Qty: 4)', '2025-10-27 14:44:36', '::1'),
(157, 12, 'Logout', 'User logged out.', '2025-10-27 14:44:41', '::1'),
(158, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:44:42', '::1'),
(159, 1, 'Approve Request', 'Approved request #12', '2025-10-27 14:44:52', '::1'),
(160, 1, 'Release Apparatus', 'Released apparatus for request #12', '2025-10-27 14:45:19', '::1'),
(161, 1, 'Logout', 'User logged out.', '2025-10-27 14:49:46', '::1'),
(162, 12, 'Login', 'WMSU student login.', '2025-10-27 14:49:55', '::1'),
(163, 12, 'Logout', 'User logged out.', '2025-10-27 14:50:12', '::1'),
(164, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:50:14', '::1'),
(165, 1, 'Return Apparatus', 'Marked request #12 as returned', '2025-10-27 14:50:19', '::1'),
(166, 1, 'Logout', 'User logged out.', '2025-10-27 14:50:22', '::1'),
(167, 12, 'Login', 'WMSU student login.', '2025-10-27 14:50:34', '::1'),
(168, 12, 'Logout', 'User logged out.', '2025-10-27 14:50:51', '::1'),
(169, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:51:23', '::1'),
(170, 1, 'Delete User', 'Deleted user ID: 10', '2025-10-27 14:51:38', '::1'),
(171, 1, 'Delete User', 'Deleted user ID: 9', '2025-10-27 14:51:42', '::1'),
(172, 1, 'Delete User', 'Deleted user ID: 3', '2025-10-27 14:52:33', '::1'),
(173, 1, 'Delete User', 'Deleted user ID: 8', '2025-10-27 14:52:37', '::1'),
(174, 1, 'Delete User', 'Deleted user ID: 8', '2025-10-27 14:54:51', '::1'),
(175, 1, 'Logout', 'User logged out.', '2025-10-27 14:54:55', '::1'),
(176, 12, 'Login', 'WMSU student login.', '2025-10-27 14:55:04', '::1'),
(177, 12, 'Borrow Request', 'Requested Beaker (Qty: 15)', '2025-10-27 14:56:35', '::1'),
(178, 12, 'Logout', 'User logged out.', '2025-10-27 14:58:17', '::1'),
(179, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 14:58:19', '::1'),
(180, 1, 'Logout', 'User logged out.', '2025-10-27 15:05:26', '::1'),
(181, 12, 'Login', 'WMSU student login.', '2025-10-27 15:05:38', '::1'),
(182, 12, 'Logout', 'User logged out.', '2025-10-27 15:21:19', '::1'),
(183, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:21:21', '::1'),
(184, 1, 'Logout', 'User logged out.', '2025-10-27 15:21:28', '::1'),
(185, 12, 'Login', 'WMSU student login.', '2025-10-27 15:21:43', '::1'),
(186, 12, 'Logout', 'User logged out.', '2025-10-27 15:22:52', '::1'),
(187, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:22:53', '::1'),
(188, 1, 'Logout', 'User logged out.', '2025-10-27 15:23:03', '::1'),
(189, 12, 'Login', 'WMSU student login.', '2025-10-27 15:23:20', '::1'),
(190, 12, 'Logout', 'User logged out.', '2025-10-27 15:23:50', '::1'),
(191, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:24:40', '::1'),
(192, 1, 'Approve Request', 'Approved request #13', '2025-10-27 15:24:48', '::1'),
(193, 1, 'Logout', 'User logged out.', '2025-10-27 15:24:50', '::1'),
(194, 12, 'Login', 'WMSU student login.', '2025-10-27 15:25:07', '::1'),
(195, 12, 'Logout', 'User logged out.', '2025-10-27 15:25:39', '::1'),
(196, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:25:41', '::1'),
(197, 1, 'Logout', 'User logged out.', '2025-10-27 15:34:23', '::1'),
(198, 12, 'Login', 'WMSU student login.', '2025-10-27 15:34:36', '::1'),
(199, 12, 'Logout', 'User logged out.', '2025-10-27 15:34:47', '::1'),
(200, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:34:51', '::1'),
(201, 4, 'Logout', 'User logged out.', '2025-10-27 15:35:08', '::1'),
(202, 12, 'Login', 'WMSU student login.', '2025-10-27 15:35:23', '::1'),
(203, 12, 'Borrow Request', 'Requested Beaker (Qty: 20)', '2025-10-27 15:35:54', '::1'),
(204, 12, 'Logout', 'User logged out.', '2025-10-27 15:36:01', '::1'),
(205, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:36:03', '::1'),
(206, 4, 'Approve Request', 'Approved request #14', '2025-10-27 15:36:09', '::1'),
(207, 4, 'Logout', 'User logged out.', '2025-10-27 15:36:12', '::1'),
(208, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:36:15', '::1'),
(209, 1, 'Release Apparatus', 'Released apparatus for request #14', '2025-10-27 15:36:23', '::1'),
(210, 1, 'Logout', 'User logged out.', '2025-10-27 15:36:29', '::1'),
(211, 12, 'Login', 'WMSU student login.', '2025-10-27 15:36:37', '::1'),
(212, 12, 'Logout', 'User logged out.', '2025-10-27 15:36:42', '::1'),
(213, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:36:58', '::1'),
(214, 1, 'Logout', 'User logged out.', '2025-10-27 15:48:04', '::1'),
(215, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:48:13', '::1'),
(216, 1, 'Logout', 'User logged out.', '2025-10-27 15:57:23', '::1'),
(217, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 15:57:27', '::1'),
(218, 1, 'Logout', 'User logged out.', '2025-10-27 16:00:07', '::1'),
(219, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 16:00:11', '::1'),
(220, 4, 'Logout', 'User logged out.', '2025-10-27 16:03:11', '::1'),
(221, 12, 'Login', 'WMSU student login.', '2025-10-27 16:03:23', '::1'),
(222, 12, 'Logout', 'User logged out.', '2025-10-27 16:05:19', '::1'),
(223, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 16:05:24', '::1'),
(224, 4, 'Logout', 'User logged out.', '2025-10-27 16:05:29', '::1'),
(225, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 16:05:33', '::1'),
(226, 1, 'Logout', 'User logged out.', '2025-10-27 16:06:19', '::1'),
(227, 12, 'Login', 'WMSU student login.', '2025-10-27 16:06:28', '::1'),
(228, 12, 'Logout', 'User logged out.', '2025-10-27 16:18:31', '::1'),
(229, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 16:18:33', '::1'),
(230, 1, 'Logout', 'User logged out.', '2025-10-27 16:20:04', '::1'),
(231, 12, 'Login', 'WMSU student login.', '2025-10-27 16:20:33', '::1'),
(232, 12, 'Borrow Request', 'Requested Thong (Qty: 5)', '2025-10-27 16:22:38', '::1'),
(233, 12, 'Logout', 'User logged out.', '2025-10-27 16:22:42', '::1'),
(234, 4, 'Login', 'Admin or faculty login successful.', '2025-10-27 16:22:48', '::1'),
(235, 4, 'Approve Request', 'Approved request #15', '2025-10-27 16:22:58', '::1'),
(236, 4, 'Logout', 'User logged out.', '2025-10-27 16:23:14', '::1'),
(237, 1, 'Login', 'Admin or faculty login successful.', '2025-10-27 16:23:18', '::1'),
(238, 1, 'Release Apparatus', 'Released apparatus for request #15', '2025-10-27 16:23:33', '::1'),
(239, 1, 'Return Apparatus', 'Marked request #15 as returned', '2025-10-27 16:24:28', '::1'),
(240, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:33:18', '::1'),
(241, 1, 'Logout', 'User logged out.', '2025-11-04 18:33:52', '::1'),
(242, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:38:17', '::1'),
(243, 1, 'Logout', 'User logged out.', '2025-11-04 18:42:14', '::1'),
(244, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:42:36', '::1'),
(245, 1, 'Logout', 'User logged out.', '2025-11-04 18:44:15', '::1'),
(246, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:44:20', '::1'),
(247, 1, 'Logout', 'User logged out.', '2025-11-04 18:44:26', '::1'),
(248, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:44:47', '::1'),
(249, 1, 'Logout', 'User logged out.', '2025-11-04 18:45:29', '::1'),
(250, 12, 'Login', 'WMSU student login.', '2025-11-04 18:46:22', '::1'),
(251, 12, 'Logout', 'User logged out.', '2025-11-04 18:47:06', '::1'),
(252, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:48:55', '::1'),
(253, 1, 'Logout', 'User logged out.', '2025-11-04 18:49:26', '::1'),
(254, 12, 'Login', 'WMSU student login.', '2025-11-04 18:50:05', '::1'),
(255, 12, 'Borrow Request', 'Requested Flask (Qty: 15)', '2025-11-04 18:51:21', '::1'),
(256, 12, 'Logout', 'User logged out.', '2025-11-04 18:51:42', '::1'),
(257, 4, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:51:46', '::1'),
(258, 4, 'Approve Request', 'Approved request #16', '2025-11-04 18:52:05', '::1'),
(259, 4, 'Logout', 'User logged out.', '2025-11-04 18:52:12', '::1'),
(260, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:52:16', '::1'),
(261, 1, 'Release Apparatus', 'Released apparatus for request #16', '2025-11-04 18:52:37', '::1'),
(262, 1, 'Return Apparatus', 'Marked request #16 as returned', '2025-11-04 18:53:12', '::1'),
(263, 1, 'Logout', 'User logged out.', '2025-11-04 18:53:24', '::1'),
(264, 12, 'Login', 'WMSU student login.', '2025-11-04 18:53:43', '::1'),
(265, 12, 'Logout', 'User logged out.', '2025-11-04 18:54:03', '::1'),
(266, 1, 'Login', 'Admin or faculty login successful.', '2025-11-04 18:54:25', '::1'),
(267, 1, 'Logout', 'User logged out.', '2025-11-04 18:54:40', '::1'),
(268, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 13:05:01', '::1'),
(269, 1, 'Logout', 'User logged out.', '2025-11-09 13:05:14', '::1'),
(270, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 13:05:16', '::1'),
(271, 1, 'Logout', 'User logged out.', '2025-11-09 13:31:01', '::1'),
(272, 12, 'Login', 'WMSU student login.', '2025-11-09 13:31:09', '::1'),
(273, 12, 'Logout', 'User logged out.', '2025-11-09 13:31:12', '::1'),
(274, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 13:31:14', '::1'),
(275, 1, 'Logout', 'User logged out.', '2025-11-09 13:34:59', '::1'),
(276, 4, 'Login', 'Admin or faculty login successful.', '2025-11-09 13:35:02', '::1'),
(277, 4, 'Logout', 'User logged out.', '2025-11-09 13:35:10', '::1'),
(278, 12, 'Login', 'WMSU student login.', '2025-11-09 13:35:30', '::1'),
(279, 12, 'Logout', 'User logged out.', '2025-11-09 13:38:16', '::1'),
(280, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 13:38:19', '::1'),
(281, 1, 'Logout', 'User logged out.', '2025-11-09 13:57:14', '::1'),
(282, 4, 'Login', 'Admin or faculty login successful.', '2025-11-09 13:57:18', '::1'),
(283, 4, 'Logout', 'User logged out.', '2025-11-09 13:57:25', '::1'),
(284, 12, 'Login', 'WMSU student login.', '2025-11-09 13:57:33', '::1'),
(285, 12, 'Logout', 'User logged out.', '2025-11-09 13:57:44', '::1'),
(286, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 13:57:50', '::1'),
(287, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 20:24:52', '::1'),
(288, 1, 'Logout', 'User logged out.', '2025-11-09 20:37:14', '::1'),
(289, 4, 'Login', 'Admin or faculty login successful.', '2025-11-09 20:37:18', '::1'),
(290, 4, 'Logout', 'User logged out.', '2025-11-09 20:37:34', '::1'),
(291, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 20:37:40', '::1'),
(292, 1, 'Logout', 'User logged out.', '2025-11-09 20:37:52', '::1'),
(293, 1, 'Login', 'Admin or faculty login successful.', '2025-11-09 20:44:45', '::1'),
(294, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 13:36:39', '::1'),
(295, 1, 'Logout', 'User logged out.', '2025-11-10 13:36:49', '::1'),
(296, 12, 'Login', 'WMSU student login.', '2025-11-10 13:37:02', '::1'),
(297, 12, 'Logout', 'User logged out.', '2025-11-10 13:50:16', '::1'),
(298, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 13:50:18', '::1'),
(299, 1, 'Logout', 'User logged out.', '2025-11-10 13:51:01', '::1'),
(300, 12, 'Login', 'WMSU student login.', '2025-11-10 13:51:12', '::1'),
(301, 12, 'Logout', 'User logged out.', '2025-11-10 13:52:13', '::1'),
(302, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 13:52:14', '::1'),
(303, 1, 'Revert Approval', 'Reverted approval for request #10', '2025-11-10 13:52:35', '::1'),
(304, 1, 'Reject Request', 'Rejected request #10', '2025-11-10 13:52:45', '::1'),
(305, 1, 'Add Apparatus', 'Added apparatus: FLorence Flask (Condition: new)', '2025-11-10 13:54:52', '::1'),
(306, 1, 'Logout', 'User logged out.', '2025-11-10 13:56:38', '::1'),
(307, 12, 'Login', 'WMSU student login.', '2025-11-10 13:56:46', '::1'),
(308, 12, 'Borrow Request', 'Requested 1 apparatus item(s)', '2025-11-10 13:59:34', '::1'),
(309, 12, 'Logout', 'User logged out.', '2025-11-10 13:59:42', '::1'),
(310, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 13:59:46', '::1'),
(311, 1, 'Approve Request', 'Approved and reserved apparatus for request #17', '2025-11-10 14:00:00', '::1'),
(312, 1, 'Revert Approval', 'Reverted approval for request #17', '2025-11-10 14:00:13', '::1'),
(313, 1, 'Logout', 'User logged out.', '2025-11-10 14:00:27', '::1'),
(314, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 14:00:35', '::1'),
(315, 1, 'Logout', 'User logged out.', '2025-11-10 14:00:39', '::1'),
(316, 12, 'Login', 'WMSU student login.', '2025-11-10 14:00:49', '::1'),
(317, 12, 'Logout', 'User logged out.', '2025-11-10 14:10:43', '::1'),
(318, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 14:10:45', '::1'),
(319, 1, 'Logout', 'User logged out.', '2025-11-10 14:11:36', '::1'),
(320, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 14:13:00', '::1'),
(321, 1, 'Logout', 'User logged out.', '2025-11-10 14:13:18', '::1'),
(322, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 14:13:20', '::1'),
(323, 1, 'Logout', 'User logged out.', '2025-11-10 14:20:48', '::1'),
(324, 1, 'Login', 'Admin or faculty login successful.', '2025-11-10 14:21:08', '::1'),
(325, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 01:02:01', '::1'),
(326, 1, 'Logout', 'User logged out.', '2025-11-17 01:02:54', '::1'),
(327, 12, 'Login', 'WMSU student login.', '2025-11-17 01:03:24', '::1'),
(328, 12, 'Logout', 'User logged out.', '2025-11-17 01:04:17', '::1'),
(329, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 01:04:18', '::1'),
(330, 1, 'Logout', 'User logged out.', '2025-11-17 01:09:06', '::1'),
(331, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 01:32:11', '::1'),
(332, 1, 'Logout', 'User logged out.', '2025-11-17 01:32:19', '::1'),
(333, 12, 'Login', 'WMSU student login.', '2025-11-17 01:32:28', '::1'),
(334, 12, 'Logout', 'User logged out.', '2025-11-17 02:15:21', '::1'),
(335, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 02:15:23', '::1'),
(336, 1, 'Logout', 'User logged out.', '2025-11-17 02:15:28', '::1'),
(337, 12, 'Login', 'WMSU student login.', '2025-11-17 02:15:35', '::1'),
(338, 12, 'Logout', 'User logged out.', '2025-11-17 02:24:35', '::1'),
(339, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 02:24:38', '::1'),
(340, 1, 'Logout', 'User logged out.', '2025-11-17 02:34:17', '::1'),
(341, 12, 'Login', 'WMSU student login.', '2025-11-17 02:34:40', '::1'),
(342, 12, 'Borrow Request', 'Requested Beaker 500mL (Qty: 20)', '2025-11-17 02:58:31', '::1'),
(343, 12, 'Borrow Request', 'Requested Beaker 500mL (Qty: 1)', '2025-11-17 04:02:11', '::1'),
(344, 12, 'Borrow Request', 'Requested Resistor Assortment Pack (Qty: 2)', '2025-11-17 04:04:59', '::1'),
(345, 12, 'Borrow Request', 'Requested Florence Flask (Qty: 3)', '2025-11-17 04:04:59', '::1'),
(346, 12, 'Logout', 'User logged out.', '2025-11-17 04:05:09', '::1'),
(347, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 04:05:11', '::1'),
(348, 1, 'Reject Request', 'Rejected request #21', '2025-11-17 04:06:56', '::1'),
(349, 1, 'Approve Request', 'Approved and reserved apparatus for request #17', '2025-11-17 04:07:01', '::1'),
(350, 1, 'Release Apparatus', 'Released apparatus for request #17', '2025-11-17 04:07:27', '::1'),
(351, 1, 'Logout', 'User logged out.', '2025-11-17 04:07:41', '::1'),
(352, 12, 'Login', 'WMSU student login.', '2025-11-17 04:07:50', '::1'),
(353, 12, 'Borrow Request', 'Requested Alcohol Lamp (Qty: 1)', '2025-11-17 04:17:47', '::1'),
(354, 12, 'Borrow Request', 'Requested Beaker 250mL (Qty: 4)', '2025-11-17 04:17:47', '::1'),
(355, 12, 'Logout', 'User logged out.', '2025-11-17 04:17:58', '::1'),
(356, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 04:17:59', '::1'),
(357, 1, 'Logout', 'User logged out.', '2025-11-17 04:25:22', '::1'),
(358, 12, 'Login', 'WMSU student login.', '2025-11-17 04:25:37', '::1'),
(359, 12, 'Logout', 'User logged out.', '2025-11-17 04:25:58', '::1'),
(360, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 04:26:00', '::1'),
(361, 1, 'Logout', 'User logged out.', '2025-11-17 04:31:04', '::1'),
(362, 12, 'Login', 'WMSU student login.', '2025-11-17 04:31:18', '::1'),
(363, 12, 'Borrow Request', 'Requested Beaker 500mL (Qty: 3)', '2025-11-17 04:32:17', '::1'),
(364, 12, 'Borrow Request', 'Requested Sodium Chloride (NaCl) (Qty: 3)', '2025-11-17 04:32:17', '::1'),
(365, 12, 'Logout', 'User logged out.', '2025-11-17 04:32:24', '::1'),
(366, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 04:32:26', '::1'),
(367, 1, 'Approve Request', 'Approved and reserved apparatus for request #18', '2025-11-17 04:36:29', '::1'),
(368, 1, 'Logout', 'User logged out.', '2025-11-17 04:36:40', '::1'),
(369, 12, 'Login', 'WMSU student login.', '2025-11-17 04:37:03', '::1'),
(370, 12, 'Logout', 'User logged out.', '2025-11-17 04:37:14', '::1'),
(371, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 04:37:37', '::1'),
(372, 1, 'Logout', 'User logged out.', '2025-11-17 04:53:45', '::1'),
(373, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 08:39:13', '::1'),
(374, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 10:07:45', '::1'),
(375, 1, 'Logout', 'User logged out.', '2025-11-17 10:12:20', '::1'),
(376, 12, 'Login', 'WMSU student login.', '2025-11-17 10:12:27', '::1'),
(377, 12, 'Logout', 'User logged out.', '2025-11-17 10:12:39', '::1'),
(378, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 10:12:41', '::1'),
(379, 1, 'Send Notification', 'Sent to students: ewan', '2025-11-17 10:33:18', '::1'),
(380, 1, 'Logout', 'User logged out.', '2025-11-17 10:33:23', '::1'),
(381, 12, 'Login', 'WMSU student login.', '2025-11-17 10:33:36', '::1'),
(382, 12, 'Logout', 'User logged out.', '2025-11-17 10:40:59', '::1'),
(383, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 10:41:03', '::1'),
(384, 1, 'Send Notification', 'Sent to students: CSM Apparatus Borrowing - Request Approved', '2025-11-17 10:45:01', '::1'),
(385, 1, 'Send Notification', 'Sent to students: CSM Apparatus Borrowing - Request Approved', '2025-11-17 10:48:24', '::1'),
(386, 1, 'Send Notification', 'Sent to students: CSM Apparatus Borrowing - Request Approved', '2025-11-17 10:49:48', '::1'),
(387, 1, 'Logout', 'User logged out.', '2025-11-17 10:51:18', '::1'),
(388, 12, 'Login', 'WMSU student login.', '2025-11-17 10:51:29', '::1'),
(389, 12, 'Logout', 'User logged out.', '2025-11-17 10:52:55', '::1'),
(390, 12, 'Login', 'WMSU student login.', '2025-11-17 10:53:21', '::1'),
(391, 12, 'Logout', 'User logged out.', '2025-11-17 10:53:35', '::1'),
(392, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 10:53:37', '::1'),
(393, 1, 'Send Notification', 'Sent to students: CSM Apparatus Borrowing - Return Reminder', '2025-11-17 10:54:03', '::1'),
(394, 1, 'Logout', 'User logged out.', '2025-11-17 10:54:10', '::1'),
(395, 12, 'Login', 'WMSU student login.', '2025-11-17 10:54:20', '::1'),
(396, 12, 'Logout', 'User logged out.', '2025-11-17 10:54:29', '::1'),
(397, 12, 'Login', 'WMSU student login.', '2025-11-17 10:54:37', '::1'),
(398, 12, 'Borrow Request', 'Requested FLorence Flask (Qty: 2)', '2025-11-17 10:55:10', '::1'),
(399, 12, 'Logout', 'User logged out.', '2025-11-17 10:55:40', '::1'),
(400, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 10:55:42', '::1'),
(401, 1, 'Logout', 'User logged out.', '2025-11-17 11:57:27', '::1'),
(402, 12, 'Login', 'WMSU student login.', '2025-11-17 11:57:35', '::1'),
(403, 12, 'Logout', 'User logged out.', '2025-11-17 11:58:37', '::1'),
(404, 1, 'Login', 'Admin or faculty login successful.', '2025-11-17 11:58:39', '::1'),
(405, 12, 'Login', 'WMSU student login.', '2025-11-23 21:27:04', '::1'),
(406, 12, 'Borrow Request', 'Requested Compound Microscope (Qty: 5)', '2025-11-23 21:28:13', '::1'),
(407, 12, 'Borrow Request', 'Requested Resistor Assortment Pack (Qty: 5)', '2025-11-23 21:32:48', '::1'),
(408, 12, 'Borrow Request', 'Requested Stopwatch (Qty: 4)', '2025-11-23 21:34:33', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `apparatus`
--

CREATE TABLE `apparatus` (
  `apparatus_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `condition` varchar(50) NOT NULL DEFAULT 'Good',
  `description` text DEFAULT NULL,
  `status` enum('Available','Borrowed','Damaged','Maintenance') DEFAULT 'Available',
  `item_condition` enum('new','old') NOT NULL DEFAULT 'new' COMMENT 'Indicates whether the apparatus is new or used/old'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apparatus`
--

INSERT INTO `apparatus` (`apparatus_id`, `name`, `category`, `quantity`, `condition`, `description`, `status`, `item_condition`) VALUES
(19, 'FLorence Flask', 'Flask', 12, 'Good', NULL, 'Available', 'new'),
(20, 'Beaker 250mL', 'Beaker', 40, 'good', 'Standard glass beaker for heating and mixing', 'Available', 'new'),
(21, 'Beaker 500mL', 'Beaker', 5, 'good', 'Large-volume beaker for experiments', 'Available', ''),
(22, 'Florence Flask', 'Flask', 18, 'good', 'Heat-resistant boiling flask', 'Available', 'new'),
(23, 'Erlenmeyer Flask 250mL', 'Flask', 20, 'damaged', 'Minor cracks but still usable for non-heating tasks', 'Available', ''),
(24, 'Test Tube', 'Glassware', 150, 'good', 'General purpose laboratory tubes', 'Available', 'new'),
(25, 'Test Tube Rack', 'Glassware', 20, 'good', 'Holds test tubes', 'Available', 'new'),
(26, 'Glass Tubing Pack', 'Glass Tubing', 50, 'good', 'Different lengths of glass tubes', 'Available', 'new'),
(27, 'Graduated Cylinder 100mL', 'Measuring Device', 30, 'good', 'Volume measurement cylinder', 'Available', 'new'),
(28, 'Digital Balance', 'Measuring Device', 5, 'damaged', 'Needs calibration; minor sensor issues', 'Available', ''),
(29, 'Thermometer (Glass)', 'Measuring Device', 45, 'good', 'Measures temperature', 'Available', 'new'),
(30, 'Resistor Assortment Pack', 'Electrical Components', 200, 'good', 'Mixed resistor values', 'Available', 'new'),
(31, 'Breadboard', 'Electrical Components', 30, 'good', 'Reusable solderless board', 'Available', ''),
(35, 'Hot Plate', 'Equipment', 6, 'good', 'Heating surface for experiments', 'Available', 'new'),
(36, 'Magnetic Stirrer', 'Equipment', 4, 'good', 'Stirs solutions using magnetic force', 'Available', 'new'),
(37, 'Compound Microscope', 'Microscope', 7, 'good', 'High magnification microscope', 'Available', ''),
(38, 'Stereo Microscope', 'Microscope', 3, 'damaged', 'Loose focus knob', 'Available', ''),
(39, 'Bunsen Burner', 'Thermal Apparatus', 20, 'good', 'Gas burner for heating', 'Available', ''),
(40, 'Alcohol Lamp', 'Thermal Apparatus', 15, 'good', 'Alternative heat source', 'Available', 'new'),
(44, 'Safety Goggles', 'Protective Gear', 50, 'good', 'Eye protection equipment', 'Available', 'new'),
(45, 'Laboratory Gloves', 'Protective Gear', 100, 'good', 'Chemical-resistant gloves', 'Available', 'new'),
(48, 'Sodium Chloride (NaCl)', 'Reagent', 5, 'good', 'Laboratory-grade salt for experiments', 'Available', 'new'),
(49, 'Hydrochloric Acid 1M', 'Reagent', 3, 'good', 'Standard acidic reagent', 'Available', 'new'),
(50, 'Lab Notebook', 'Miscellaneous', 30, 'good', 'Record of experiments', 'Available', 'new'),
(51, 'Stopwatch', 'Miscellaneous', 12, 'good', 'Measures time intervals', 'Available', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `apparatus_history`
--

CREATE TABLE `apparatus_history` (
  `history_id` int(11) NOT NULL,
  `apparatus_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `request_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `student_first_name` varchar(50) NOT NULL,
  `student_last_name` varchar(50) NOT NULL,
  `student_mi` varchar(5) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `apparatus_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `concentration` varchar(100) DEFAULT NULL,
  `date_requested` date DEFAULT NULL,
  `date_needed` date DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('pending','approved','released','returned','rejected') DEFAULT 'pending',
  `date_returned` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`request_id`, `student_id`, `full_name`, `student_first_name`, `student_last_name`, `student_mi`, `faculty_id`, `apparatus_id`, `quantity`, `concentration`, `date_requested`, `date_needed`, `time_from`, `time_to`, `subject`, `schedule`, `room`, `purpose`, `status`, `date_returned`, `remarks`, `archived`) VALUES
(17, 12, '', '', '', NULL, 4, 19, 3, '2M', '2025-11-10', '2025-11-13', '13:56:00', '16:56:00', 'BIO101', 'Thursday, Nov 13, 2025 from 1:56 PM to 4:56 PM', 'CSM 101', 'Distillation', 'released', NULL, NULL, 0),
(18, 12, '', '', '', NULL, 4, 21, 20, 'EWAN', '2025-11-17', '2025-11-27', '14:58:00', '19:58:00', 'BIO101', 'Thursday, Nov 27, 2025 from 2:58 PM to 7:58 PM', 'MS 301', 'YES', 'approved', NULL, NULL, 0),
(19, 12, NULL, '', '', NULL, 4, 21, 1, '50 mL', '2025-11-17', '2025-11-20', '11:01:00', '04:01:00', '', 'Thursday, Nov 20, 2025 from 11:01 AM to 4:01 AM', 'CSM 103', 'ewannadsnandnasdnandansd', 'pending', NULL, NULL, 0),
(20, 12, NULL, '', '', NULL, 4, 30, 2, 'N/A', '2025-11-17', '2025-11-20', '04:04:00', '07:04:00', 'BIO101', 'Thursday, Nov 20, 2025 from 4:04 AM to 7:04 AM', 'CSM 103', 'eawndsannadnasndasndnd', 'pending', NULL, NULL, 0),
(21, 12, NULL, '', '', NULL, 4, 22, 3, '100 mL', '2025-11-17', '2025-11-20', '04:04:00', '07:04:00', 'BIO101', 'Thursday, Nov 20, 2025 from 4:04 AM to 7:04 AM', 'CSM 103', 'eawndsannadnasndasndnd', 'rejected', NULL, NULL, 0),
(22, 12, NULL, '', '', NULL, 4, 40, 1, 'N/A', '2025-11-17', '2025-11-21', '04:17:00', '16:17:00', 'BIO101', 'Friday, Nov 21, 2025 from 4:17 AM to 4:17 PM', 'MS 301', 'ewadsbabdbsadbasdbadbadb', 'pending', NULL, NULL, 0),
(23, 12, NULL, '', '', NULL, 4, 20, 4, '250 mL', '2025-11-17', '2025-11-21', '04:17:00', '16:17:00', 'BIO101', 'Friday, Nov 21, 2025 from 4:17 AM to 4:17 PM', 'MS 301', 'ewadsbabdbsadbasdbadbadb', 'pending', NULL, NULL, 0),
(24, 12, NULL, '', '', NULL, 4, 21, 3, '500 mL', '2025-11-17', '2025-11-21', '04:31:00', '16:31:00', 'CHEM101', 'Friday, Nov 21, 2025 from 4:31 AM to 4:31 PM', 'MS 301', 'EAWNDNSADNASDNANDADSAM', 'pending', NULL, NULL, 0),
(25, 12, NULL, '', '', NULL, 4, 48, 3, '1M', '2025-11-17', '2025-11-21', '04:31:00', '16:31:00', 'CHEM101', 'Friday, Nov 21, 2025 from 4:31 AM to 4:31 PM', 'MS 301', 'EAWNDNSADNASDNANDADSAM', 'pending', NULL, NULL, 0),
(26, 12, NULL, '', '', NULL, 4, 19, 2, '100 mL', '2025-11-17', '2025-11-20', '10:54:00', '00:54:00', 'BIO101', 'Thursday, Nov 20, 2025 from 10:54 AM to 12:54 AM', 'CSM 206', 'enawnadsnnasdnadnasndasn', 'pending', NULL, NULL, 0),
(28, 12, NULL, '', '', NULL, 4, 37, 5, 'N/A', '2025-11-23', '2025-11-26', '11:27:00', '21:27:00', 'CHEM101', 'Wednesday, Nov 26, 2025 from 11:27 AM to 9:27 PM', 'CSM 206', 'i want to borrow this apparatus', 'pending', NULL, NULL, 0),
(29, 12, NULL, '', '', NULL, 4, 30, 5, 'N/A', '2025-11-23', '2025-11-27', '11:32:00', '18:32:00', '', 'Thursday, Nov 27, 2025 from 11:32 AM to 6:32 PM', 'MS 302', '', 'pending', NULL, NULL, 0),
(30, 12, NULL, '', '', NULL, 4, 51, 4, 'N/A', '2025-11-23', '2025-12-01', '11:34:00', '21:34:00', 'ewan', 'Monday, Dec 1, 2025 from 11:34 AM to 9:34 PM', 'CSM 101', 'wdpsadasdp[asdposa', 'pending', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `subject` varchar(500) NOT NULL,
  `message` text NOT NULL,
  `status` enum('queued','sent','failed') DEFAULT 'queued',
  `sent_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_email`, `recipient_name`, `subject`, `message`, `status`, `sent_by`, `created_at`) VALUES
(2, 'ae202403606@wmsu.edu.ph', NULL, 'ewan', 'hello', 'queued', 1, '2025-11-17 02:33:18'),
(9, 'ae202403473@wmsu.edu.ph', NULL, 'CSM Apparatus Borrowing - Return Reminder', 'Dear Student,\r\n\r\nThis is a friendly reminder that your borrowed apparatus is due for return soon. Please make arrangements to return the items on time.\r\n\r\nThank you,\r\nCSM Laboratory Staff', 'queued', 1, '2025-11-17 02:54:03'),
(10, 'ae202403606@wmsu.edu.ph', NULL, 'CSM Apparatus Borrowing - Return Reminder', 'Dear Student,\r\n\r\nThis is a friendly reminder that your borrowed apparatus is due for return soon. Please make arrangements to return the items on time.\r\n\r\nThank you,\r\nCSM Laboratory Staff', 'queued', 1, '2025-11-17 02:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `notification_preferences`
--

CREATE TABLE `notification_preferences` (
  `pref_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) DEFAULT 1,
  `overdue_alerts` tinyint(1) DEFAULT 1,
  `approval_notifications` tinyint(1) DEFAULT 1,
  `low_stock_alerts` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

CREATE TABLE `penalties` (
  `penalty_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date_imposed` date DEFAULT NULL,
  `date_paid` date DEFAULT NULL,
  `status` enum('unpaid','paid','waived') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `return_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `returned_by` int(11) NOT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `return_date` datetime NOT NULL DEFAULT current_timestamp(),
  `condition_on_return` enum('good','damaged','lost','other') DEFAULT 'good',
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `middle_initial` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_backups`
--

CREATE TABLE `system_backups` (
  `backup_id` int(11) NOT NULL,
  `backup_name` varchar(255) NOT NULL,
  `backup_size` bigint(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `backup_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'penalty_per_day', '50.00', 'Penalty amount per day for late returns', '2025-11-09 13:28:04'),
(2, 'damage_penalty', '100.00', 'Penalty amount for damaged items', '2025-11-09 13:28:04'),
(3, 'low_stock_threshold', '5', 'Alert threshold for low stock items', '2025-11-09 13:28:04'),
(4, 'session_timeout', '3600', 'Session timeout in seconds (1 hour)', '2025-11-09 13:28:04'),
(5, 'system_name', 'CSM Apparatus Borrowing System', 'System display name', '2025-11-09 13:28:04'),
(6, 'school_logo', 'assets/image.png', 'Path to school logo', '2025-11-09 13:28:04');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `transaction_code` varchar(50) DEFAULT NULL,
  `date_borrowed` datetime NOT NULL,
  `date_returned` datetime DEFAULT NULL,
  `condition_on_return` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `borrow_request_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `request_id`, `transaction_code`, `date_borrowed`, `date_returned`, `condition_on_return`, `remarks`, `borrow_request_id`) VALUES
(14, 17, 'TXN-20251117-F8D7D9', '2025-11-17 04:07:27', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_initial` char(1) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','faculty','student','assistant') NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `middle_initial`, `username`, `password`, `role`, `full_name`, `email`) VALUES
(1, '', '', NULL, 'admin', '12345', 'admin', 'System Administrator', 'admin@csm.edu.ph'),
(4, '', '', NULL, 'faculty1', '12345', 'faculty', 'Faculty User', NULL),
(12, '', '', NULL, 'ae202403473', 'XNFOZQ', 'student', NULL, 'ae202403473@wmsu.edu.ph'),
(13, '', '', NULL, 'ae202403606', '123456', 'student', NULL, 'ae202403606@wmsu.edu.ph');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `email_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `related_id` int(11) DEFAULT NULL COMMENT 'ID of related record (request_id, transaction_id, etc.)',
  `related_type` varchar(50) DEFAULT NULL COMMENT 'Type of related record (borrow_request, penalty, etc.)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `email_sent`, `created_at`, `related_id`, `related_type`) VALUES
(1, 4, 'New Borrow Request', ' has requested Beaker 500mL (Qty: 1) for .', 'info', 0, 0, '2025-11-16 20:02:11', 343, 'borrow_request'),
(3, 4, 'New Borrow Request', ' has requested Florence Flask (Qty: 3) for BIO101.', 'info', 0, 0, '2025-11-16 20:04:59', 21, 'borrow_request'),
(4, 12, 'Request Rejected', 'Your borrow request #21 has been rejected.', 'danger', 1, 0, '2025-11-16 20:06:56', 21, 'borrow_request'),
(5, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested Alcohol Lamp (Qty: 1) for BIO101.', 'info', 0, 0, '2025-11-16 20:17:47', 22, 'borrow_request'),
(6, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested Beaker 250mL (Qty: 4) for BIO101.', 'info', 0, 0, '2025-11-16 20:17:47', 23, 'borrow_request'),
(7, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested Beaker 500mL (Qty: 3) for CHEM101.', 'info', 0, 0, '2025-11-16 20:32:17', 24, 'borrow_request'),
(8, 1, 'New Borrow Request', 'A new borrow request has been submitted by ae202403473@wmsu.edu.ph for Beaker 500mL (Qty: 3).', 'info', 1, 0, '2025-11-16 20:32:17', 24, 'borrow_request'),
(9, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested Sodium Chloride (NaCl) (Qty: 3) for CHEM101.', 'info', 0, 0, '2025-11-16 20:32:17', 25, 'borrow_request'),
(10, 1, 'New Borrow Request', 'A new borrow request has been submitted by ae202403473@wmsu.edu.ph for Sodium Chloride (NaCl) (Qty: 3).', 'info', 0, 0, '2025-11-16 20:32:17', 25, 'borrow_request'),
(13, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested FLorence Flask (Qty: 2) for BIO101.', 'info', 0, 0, '2025-11-17 02:55:10', 26, 'borrow_request'),
(14, 1, 'New Borrow Request', 'A new borrow request has been submitted by ae202403473@wmsu.edu.ph for FLorence Flask (Qty: 2).', 'info', 1, 0, '2025-11-17 02:55:10', 26, 'borrow_request'),
(15, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested Compound Microscope (Qty: 5) for CHEM101.', 'info', 0, 0, '2025-11-23 13:28:13', 28, 'borrow_request'),
(16, 1, 'New Borrow Request', 'A new borrow request has been submitted by ae202403473@wmsu.edu.ph for Compound Microscope (Qty: 5).', 'info', 0, 0, '2025-11-23 13:28:13', 28, 'borrow_request'),
(17, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested Resistor Assortment Pack (Qty: 5) for .', 'info', 0, 0, '2025-11-23 13:32:48', 29, 'borrow_request'),
(18, 1, 'New Borrow Request', 'A new borrow request has been submitted by ae202403473@wmsu.edu.ph for Resistor Assortment Pack (Qty: 5).', 'info', 0, 0, '2025-11-23 13:32:48', 29, 'borrow_request'),
(19, 4, 'New Borrow Request', 'ae202403473@wmsu.edu.ph has requested Stopwatch (Qty: 4) for ewan.', 'info', 0, 0, '2025-11-23 13:34:33', 30, 'borrow_request'),
(20, 1, 'New Borrow Request', 'A new borrow request has been submitted by ae202403473@wmsu.edu.ph for Stopwatch (Qty: 4).', 'info', 0, 0, '2025-11-23 13:34:33', 30, 'borrow_request');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_timestamp` (`timestamp`),
  ADD KEY `idx_activity_action` (`action`);

--
-- Indexes for table `apparatus`
--
ALTER TABLE `apparatus`
  ADD PRIMARY KEY (`apparatus_id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `apparatus` ADD FULLTEXT KEY `ft_apparatus_search` (`name`,`category`);

--
-- Indexes for table `apparatus_history`
--
ALTER TABLE `apparatus_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_apparatus` (`apparatus_id`),
  ADD KEY `idx_changed_by` (`changed_by`);

--
-- Indexes for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_faculty` (`faculty_id`),
  ADD KEY `idx_date_needed` (`date_needed`),
  ADD KEY `idx_borrow_date_requested` (`date_requested`),
  ADD KEY `fk_borrow_apparatus` (`apparatus_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sent_by` (`sent_by`);

--
-- Indexes for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD PRIMARY KEY (`pref_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `penalties`
--
ALTER TABLE `penalties`
  ADD PRIMARY KEY (`penalty_id`),
  ADD KEY `idx_transaction` (`transaction_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_penalties_status` (`status`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `fk_returns_processed_by` (`processed_by`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_backups`
--
ALTER TABLE `system_backups`
  ADD PRIMARY KEY (`backup_id`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `fk_borrow_request` (`borrow_request_id`),
  ADD KEY `idx_transactions_dates` (`date_borrowed`,`date_returned`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);
ALTER TABLE `users` ADD FULLTEXT KEY `ft_users_search` (`full_name`,`email`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_token` (`session_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=409;

--
-- AUTO_INCREMENT for table `apparatus`
--
ALTER TABLE `apparatus`
  MODIFY `apparatus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `apparatus_history`
--
ALTER TABLE `apparatus_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  MODIFY `pref_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penalties`
--
ALTER TABLE `penalties`
  MODIFY `penalty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_backups`
--
ALTER TABLE `system_backups`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `apparatus_history`
--
ALTER TABLE `apparatus_history`
  ADD CONSTRAINT `fk_history_apparatus` FOREIGN KEY (`apparatus_id`) REFERENCES `apparatus` (`apparatus_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD CONSTRAINT `borrow_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `borrow_requests_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_apparatus` FOREIGN KEY (`apparatus_id`) REFERENCES `apparatus` (`apparatus_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_borrow_apparatus` FOREIGN KEY (`apparatus_id`) REFERENCES `apparatus` (`apparatus_id`) ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sent_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD CONSTRAINT `fk_notif_pref_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `penalties`
--
ALTER TABLE `penalties`
  ADD CONSTRAINT `penalties_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `fk_returns_processed_by` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `borrow_requests` (`request_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_borrow_request` FOREIGN KEY (`borrow_request_id`) REFERENCES `borrow_requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transaction_request` FOREIGN KEY (`request_id`) REFERENCES `borrow_requests` (`request_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `fk_user_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
