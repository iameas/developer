-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2025 at 10:50 PM
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
-- Database: `drauigpay`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

CREATE TABLE `t_users` (
  `id` int(11) NOT NULL,
  `role` enum('customer') NOT NULL,
  `username` varchar(200) DEFAULT NULL,
  `bvn` varchar(11) NOT NULL,
  `account_id` varchar(255) NOT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `sex` enum('male','female') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `transaction_pin` char(4) DEFAULT NULL,
  `token_pin` char(6) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `drauig_id` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `country_code` varchar(5) NOT NULL,
  `country` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `role`, `username`, `bvn`, `account_id`, `fname`, `lname`, `sex`, `date_of_birth`, `transaction_pin`, `token_pin`, `password`, `drauig_id`, `email`, `image`, `phone`, `is_verified`, `country_code`, `country`, `created_at`, `updated_at`) VALUES
(25, 'customer', 'iameas', '', '9132765539', 'ThankGod', 'Emmanuel', NULL, NULL, NULL, NULL, '03675b9a47a8bd1eae702c438fca69ae', 'vPgIdvQlVULo133MUXYkwqjWBhPLarbO8qCx', 'iamearldoss@gmail.com', 'customer/src/img/uploads/Rccg_logo.png', '09132765539', 1, '', '', '2025-02-04 21:39:29', '2025-02-04 21:41:51');

-- --------------------------------------------------------

--
-- Table structure for table `t_verification_codes`
--

CREATE TABLE `t_verification_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `status` enum('pending','verified','expired') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_verification_codes`
--

INSERT INTO `t_verification_codes` (`id`, `user_id`, `verification_code`, `status`, `created_at`, `expires_at`) VALUES
(29, 25, 'QSOqHm', 'verified', '2025-02-04 21:39:29', '2025-02-04 22:39:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_verification_codes`
--
ALTER TABLE `t_verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `t_verification_codes`
--
ALTER TABLE `t_verification_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_verification_codes`
--
ALTER TABLE `t_verification_codes`
  ADD CONSTRAINT `t_verification_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `t_users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
