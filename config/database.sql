-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2024 at 04:16 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_stock`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `st_id` int(11) NOT NULL,
  `us_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `cart_date` date NOT NULL,
  `cart_time` time NOT NULL,
  `cart_status_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `st_id`, `us_id`, `dept_id`, `cart_date`, `cart_time`, `cart_status_id`) VALUES
(1, 1, 1, 1, '2024-09-06', '14:29:00', 'A'),
(2, 1, 2, 1, '2024-09-09', '08:44:00', 'A'),
(3, 1, 2, 1, '2024-09-09', '08:47:00', 'A'),
(4, 1, 2, 0, '0000-00-00', '00:00:00', 'TBC'),
(5, 1, 1, 2, '2024-09-09', '09:15:00', 'R'),
(6, 1, 1, 2, '2024-09-09', '10:11:00', 'R'),
(7, 1, 1, 1, '2024-09-11', '07:42:00', 'A'),
(8, 1, 1, 2, '2024-09-11', '12:44:00', 'A'),
(9, 2, 2, 2, '2024-09-11', '13:57:00', 'R'),
(10, 1, 1, 3, '2024-09-16', '11:21:00', 'A'),
(11, 2, 3, 2, '2024-09-12', '10:25:00', 'A'),
(12, 2, 3, 4, '2024-09-16', '11:16:00', 'A'),
(13, 1, 1, 1, '2024-09-16', '14:08:00', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `cart_detail`
--

CREATE TABLE `cart_detail` (
  `cart_detail_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `cart_amount` int(11) NOT NULL,
  `cart_detail` varchar(255) NOT NULL,
  `cart_status_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_detail`
--

INSERT INTO `cart_detail` (`cart_detail_id`, `cart_id`, `prod_id`, `cart_amount`, `cart_detail`, `cart_status_id`) VALUES
(1, 1, 122, 10, '-', 'A'),
(2, 2, 122, 10, '-', 'A'),
(3, 3, 3, 10, '-', 'A'),
(5, 5, 122, 20, '-', 'R'),
(6, 6, 3, 10, '-', 'R'),
(7, 7, 3, 10, '-', 'A'),
(8, 8, 122, 20, '-', 'A'),
(9, 9, 1, 10, '-', 'R'),
(14, 11, 1, 10, '-', 'A'),
(15, 12, 2, 10, '-', 'A'),
(16, 10, 3, 30, '-', 'A'),
(17, 13, 4, 10, '-', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `cart_status`
--

CREATE TABLE `cart_status` (
  `cart_status_id` varchar(50) NOT NULL,
  `cart_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_status`
--

INSERT INTO `cart_status` (`cart_status_id`, `cart_status`) VALUES
('A', 'Approved'),
('P', 'Pending'),
('R', 'Reject'),
('TBC', 'To be confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(10) NOT NULL,
  `dept_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `dept_name`) VALUES
(1, 'IT'),
(2, 'HR'),
(3, 'CS'),
(4, 'AC');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `prod_id` int(10) NOT NULL,
  `prod_name` varchar(255) NOT NULL,
  `prod_amount` int(11) NOT NULL,
  `prod_amount_min` int(11) NOT NULL,
  `prod_price` float NOT NULL,
  `prod_unit` varchar(100) NOT NULL,
  `prod_type_id` int(11) NOT NULL,
  `prod_date` date NOT NULL,
  `st_id` int(11) NOT NULL,
  `prod_status` varchar(100) NOT NULL,
  `prod_img` text NOT NULL,
  `prod_detail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`prod_id`, `prod_name`, `prod_amount`, `prod_amount_min`, `prod_price`, `prod_unit`, `prod_type_id`, `prod_date`, `st_id`, `prod_status`, `prod_img`, `prod_detail`) VALUES
(1, 'ปากกาน้ำเงิน', 40, 1, 10, 'แท่ง / ด้าม', 1, '2024-08-14', 2, 'A', '48398632420240906_035225.jpg', '-'),
(2, 'ปากกาแดง', 40, 5, 10, 'แท่ง / ด้าม', 1, '2024-08-14', 2, 'A', '57893164420240906_035247.jpg', '-'),
(3, 'คีบอร์ด', 170, 5, 300, 'ชิ้น', 2, '2024-08-14', 1, 'A', '100376288520240821_082202.jpg', '-'),
(4, 'เม้าส์', 50, 10, 200, 'อัน', 2, '2024-08-14', 1, 'A', '100487991420240821_082223.jpg', '-'),
(122, 'หูฟัง', 19, 20, 300, 'อัน', 2, '0000-00-00', 1, 'A', '175691110820240906_092651.jpg', '-');

-- --------------------------------------------------------

--
-- Table structure for table `product_status`
--

CREATE TABLE `product_status` (
  `prod_status` varchar(100) NOT NULL,
  `prod_status_desc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_status`
--

INSERT INTO `product_status` (`prod_status`, `prod_status_desc`) VALUES
('A', 'Active'),
('I', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `prod_type_id` int(11) NOT NULL,
  `prod_type_desc` varchar(100) NOT NULL,
  `st_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`prod_type_id`, `prod_type_desc`, `st_id`) VALUES
(1, 'เครื่องเขียน', 2),
(2, 'อุปกรณ์คอมพิวเตอร์', 1);

-- --------------------------------------------------------

--
-- Table structure for table `receive_product`
--

CREATE TABLE `receive_product` (
  `rec_id` int(11) NOT NULL,
  `rec_date` date NOT NULL,
  `rec_time` time NOT NULL,
  `us_id` int(11) NOT NULL,
  `st_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receive_product`
--

INSERT INTO `receive_product` (`rec_id`, `rec_date`, `rec_time`, `us_id`, `st_id`) VALUES
(1, '2024-09-06', '15:35:00', 1, 1),
(2, '2024-09-06', '14:41:00', 1, 1),
(3, '2024-09-06', '16:52:00', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `receive_product_detail`
--

CREATE TABLE `receive_product_detail` (
  `rec_detail_id` int(11) NOT NULL,
  `rec_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `rec_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receive_product_detail`
--

INSERT INTO `receive_product_detail` (`rec_detail_id`, `rec_id`, `prod_id`, `rec_amount`) VALUES
(1, 1, 3, 100),
(2, 2, 3, 50),
(3, 3, 3, 10);

-- --------------------------------------------------------

--
-- Table structure for table `stock_main`
--

CREATE TABLE `stock_main` (
  `st_id` int(11) NOT NULL,
  `st_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_main`
--

INSERT INTO `stock_main` (`st_id`, `st_name`) VALUES
(1, 'Server Room'),
(2, 'HR Room'),
(3, 'All Stock');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `us_id` int(10) NOT NULL,
  `us_name` varchar(255) NOT NULL,
  `us_email` varchar(255) NOT NULL,
  `us_password` varchar(255) NOT NULL,
  `us_level_id` varchar(50) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `st_id` int(11) NOT NULL,
  `us_status_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`us_id`, `us_name`, `us_email`, `us_password`, `us_level_id`, `dept_id`, `st_id`, `us_status_id`) VALUES
(1, 'Andy', 'admin', '$2y$10$76KoB3S5soiDYT78Bi44leQg7Zf5AXP88JexKYZBHonufmwD9P7wG', 'A', 1, 1, 'A'),
(2, 'Folk', 'thai.maintenance@optinova.com', '$2y$10$b7EWOg1kmG/A9B6Au2FZ6.ONnSvIAEr8yeZxoxgiAeHEqyj09yU/i', 'U', 1, 3, 'A'),
(3, 'Toys', 'Admin2', '$2y$10$saYwzOFxAEYSYciTFwjA..84DQ4rIwlyvTaSgQULZ21EwJs/zRSvK', 'A', 2, 2, 'A'),
(4, 'Non', 'thai.dev@optinova.com', '$2y$10$xLriRUZGxVF2U9ZKS9XA9u2Rh.QRI/mTsqq6HO/sY4RWrXZrBFS6e', 'U', 2, 3, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `user_permission`
--

CREATE TABLE `user_permission` (
  `us_level_id` varchar(50) NOT NULL,
  `us_level_desc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_permission`
--

INSERT INTO `user_permission` (`us_level_id`, `us_level_desc`) VALUES
('A', 'Admin'),
('U', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `user_status`
--

CREATE TABLE `user_status` (
  `us_status_id` varchar(50) NOT NULL,
  `us_status_desc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_status`
--

INSERT INTO `user_status` (`us_status_id`, `us_status_desc`) VALUES
('A', 'Active'),
('D', 'Deactive');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_detail`
--
ALTER TABLE `cart_detail`
  ADD PRIMARY KEY (`cart_detail_id`);

--
-- Indexes for table `cart_status`
--
ALTER TABLE `cart_status`
  ADD PRIMARY KEY (`cart_status_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`prod_id`),
  ADD KEY `stock_pd` (`st_id`),
  ADD KEY `prod_type` (`prod_type_id`),
  ADD KEY `prod_status` (`prod_status`);

--
-- Indexes for table `product_status`
--
ALTER TABLE `product_status`
  ADD PRIMARY KEY (`prod_status`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`prod_type_id`),
  ADD KEY `st_product` (`st_id`);

--
-- Indexes for table `receive_product`
--
ALTER TABLE `receive_product`
  ADD PRIMARY KEY (`rec_id`);

--
-- Indexes for table `receive_product_detail`
--
ALTER TABLE `receive_product_detail`
  ADD PRIMARY KEY (`rec_detail_id`);

--
-- Indexes for table `stock_main`
--
ALTER TABLE `stock_main`
  ADD PRIMARY KEY (`st_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`us_id`),
  ADD KEY `dept` (`dept_id`),
  ADD KEY `stock_main` (`st_id`),
  ADD KEY `user_status` (`us_status_id`);

--
-- Indexes for table `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`us_status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cart_detail`
--
ALTER TABLE `cart_detail`
  MODIFY `cart_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `prod_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `prod_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `receive_product`
--
ALTER TABLE `receive_product`
  MODIFY `rec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `receive_product_detail`
--
ALTER TABLE `receive_product_detail`
  MODIFY `rec_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_main`
--
ALTER TABLE `stock_main`
  MODIFY `st_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `us_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `prod_status` FOREIGN KEY (`prod_status`) REFERENCES `product_status` (`prod_status`),
  ADD CONSTRAINT `prod_type` FOREIGN KEY (`prod_type_id`) REFERENCES `product_type` (`prod_type_id`),
  ADD CONSTRAINT `stock_pd` FOREIGN KEY (`st_id`) REFERENCES `stock_main` (`st_id`);

--
-- Constraints for table `product_type`
--
ALTER TABLE `product_type`
  ADD CONSTRAINT `st_product` FOREIGN KEY (`st_id`) REFERENCES `stock_main` (`st_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `dept` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `stock_main` FOREIGN KEY (`st_id`) REFERENCES `stock_main` (`st_id`),
  ADD CONSTRAINT `user_status` FOREIGN KEY (`us_status_id`) REFERENCES `user_status` (`us_status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
