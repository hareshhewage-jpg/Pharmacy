-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 03:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pharmacy`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

CREATE TABLE `bill_items` (
  `item_id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `med_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_items`
--

INSERT INTO `bill_items` (`item_id`, `bill_id`, `med_id`, `qty`, `price`, `subtotal`) VALUES
(1, 0, 1, 1, 45.00, 45.00),
(2, 0, 2, 2, 750.00, 1500.00),
(3, 0, 1, 1, 45.00, 45.00),
(4, 0, 1, 10, 45.00, 450.00),
(5, 0, 1, 80, 45.00, 3600.00);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(18) NOT NULL,
  `f_name` varchar(200) NOT NULL,
  `m_name` varchar(200) NOT NULL,
  `s_name` varchar(200) NOT NULL,
  `fl_name` varchar(350) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` date NOT NULL,
  `age` int(3) NOT NULL,
  `address` varchar(500) NOT NULL,
  `city` varchar(250) NOT NULL,
  `con_no` int(25) NOT NULL,
  `email` varchar(25) NOT NULL,
  `pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `cus_id`, `f_name`, `m_name`, `s_name`, `fl_name`, `gender`, `dob`, `age`, `address`, `city`, `con_no`, `email`, `pass`) VALUES
(30, 'CUS0001', 'Haresh', 'Sapath', 'Hewage', 'Haresh Sapath Hewage', 'Male', '1990-03-10', 36, '101 විනයාලංකාර මාවත', 'කොළඹ', 716043227, 'hareshhewage@gmail.com', 'P@ssw0rd'),
(31, 'CUS0002', 'Hashan', '', 'Perera', 'Hashan  Perera', 'Male', '2005-03-10', 21, 'No.31, Murutalawa Road,\r\nPeradeniya', 'Kandy', 716666666, 'kandyfellow@gmail.com', 'P@ssw0rd'),
(58, 'CUS0003', 'Kasun', '', 'Shanaka', 'Kasun  Shanaka', 'Male', '2006-06-12', 19, 'Colombo Sri Lanka', 'Kandy', 716043227, 'haresh.groupit@delmege.co', 'P@ssw0rd'),
(59, 'CUS0004', 'Nisal', '', 'Rathnayake', 'Nisal  Rathnayake', 'Male', '2000-03-10', 26, 'Kandy', 'Mathara', 716043227, 'nisal.groupit@delmege.com', '$2y$10$4pUdXMNiAJeSdAshTZEGb.ndw/j/3KIswNhjaWMLa1060HG38.gB.'),
(79, 'CUS0005', 'Haresh', 'Sapath', 'Hewage', 'Haresh Sapath Hewage', 'Male', '1998-03-10', 28, '101 විනයාලංකාර මාවත', 'කොළඹ', 712626265, 'hareshhewage@gmail.com', '$2y$10$uJvzOzaWJF//9JL4zeSwY.aTi0DTVpcTZD/zfsE7gsGabmRca2Cua'),
(94, 'CUS0006', 'Kushan', 'Naveendra', 'Rathnayake', 'Kushan Naveendra Rathnayake', 'Male', '1990-08-25', 35, 'Goyindala,\r\nPilimathalawa', 'Kandy', 718053556, 'kushan@gmail.com', 'P@ssw0rd'),
(95, 'CUS0007', 'Shanaz', 'Sapath', 'T', 'Shanaz Sapath T', 'Male', '1990-03-10', 36, '32', 'Kandy', 716043227, 'hareshhewage@gmail.com', 'P@ssw0rd'),
(96, 'CUS0008', 'Kanchana', 'Madushan', 'Wimalasooriya', 'Kanchana Madushan Wimalasooriya', 'Male', '1992-10-10', 33, '123456789', 'Kandy', 766225050, 'Kanchan@gmail.com', 'P@ssw0rd'),
(97, 'CUS0009', 'Hashini', 'V', 'Hewage', 'Hashini V Hewage', 'Female', '1991-12-21', 34, '101 විනයාලංකාර මාවත', 'කොළඹ', 713552422, 'Hashi@gmail.com', '161ebd7d45089b3446ee4e0d86dbcf92'),
(98, 'CUS0010', 'Haresh', 'Sapath', 'Hewage', 'Haresh Sapath Hewage', 'Male', '1990-09-19', 35, '101 විනයාලංකාර මාවත', 'කොළඹ', 716043227, 'hareshhewage@gmail.com', '161ebd7d45089b3446ee4e0d86dbcf92'),
(99, 'CUS0011', '', '', '', 'Haresh Sapath Hewage', '', '0000-00-00', 0, '101 විනයාලංකාර මාවත++++++++++++++++++++', '', 716043227, 'hareshhewage@gmail.com', ''),
(100, 'CUS0012', 'Dinusha', '', 'Heenkenda', 'Dinusha  Heenkenda', 'Male', '1985-02-12', 41, '101 විනයාලංකාර මාවත', 'කොළඹ', 716043227, 'dinusha.groupit@delmege.c', 'P@ssw0rd'),
(101, 'CUS0013', 'Thilina', 'Sapath', 'Manoj', 'Thilina Sapath Manoj', 'Male', '1994-02-08', 32, '101 විනයාලංකාර මාවත', 'කොළඹ', 716043227, 'thilina.groupit@delmege.c', 'P@ssw0rd'),
(102, 'CUS0014', 'Chathuni', 'Suraksha', 'Rajapaksha', 'Chathuni Suraksha Rajapaksha', 'Female', '1999-06-01', 27, '45, Mathara Road,', 'Mathara', 716043227, 'chathuni.groupit@delmege.', '161ebd7d45089b3446ee4e0d86dbcf92'),
(103, 'CUS0015', 'Nisal', '', 'Rathnayake', 'Nisal  Rathnayake', 'Male', '1990-03-10', 36, 'Colombo Rd,\r\nColombo', 'Colombo', 776523344, 'nisal.groupit@delmege.com', 'd41d8cd98f00b204e9800998ecf8427e'),
(104, 'CUS0016', 'Amila', 'Sampath', 'Wijesena', 'Amila Sampath Wijesena', 'Male', '1985-03-10', 41, 'Nittabuwa rd,\r\nNittabuwa', 'Nittabuwa', 713552422, 'Amila.groupit@delmege.com', '161ebd7d45089b3446ee4e0d86dbcf92');

-- --------------------------------------------------------

--
-- Table structure for table `grn`
--

CREATE TABLE `grn` (
  `grn_id` int(11) NOT NULL,
  `po_no` varchar(20) DEFAULT NULL,
  `item_no` varchar(20) DEFAULT NULL,
  `received_qty` int(11) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grn`
--

INSERT INTO `grn` (`grn_id`, `po_no`, `item_no`, `received_qty`, `expiry_date`, `created_at`) VALUES
(26, 'PO0003', 'ITM0006', 12, '2026-05-31', '2026-05-24 11:31:22'),
(27, 'PO0003', 'ITM0008', 12, '2026-05-31', '2026-05-24 11:31:22'),
(28, 'PO0005', 'ITM0002', 50, '2026-08-31', '2026-06-01 07:59:19'),
(29, 'PO0005', 'ITM0006', 50, '2026-08-31', '2026-06-01 07:59:19'),
(30, 'PO0005', 'ITM0002', 50, '2026-08-31', '2026-06-01 07:59:52'),
(31, 'PO0005', 'ITM0006', 50, '2026-08-31', '2026-06-01 07:59:52'),
(32, 'PO0003', 'ITM0009', 1000, '2026-06-01', '2026-06-01 08:04:35'),
(33, 'PO0004', 'ITM0003', 1000, '0000-00-00', '2026-06-01 11:13:59'),
(34, 'PO0004', 'ITM0004', 10, '0000-00-00', '2026-06-01 11:13:59'),
(35, 'PO0004', 'ITM0009', 1, '0000-00-00', '2026-06-01 11:13:59'),
(36, 'PO0002', 'ITM0001', 10, '2026-06-17', '2026-06-01 11:16:22'),
(37, 'PO0002', 'ITM0007', 10, '2026-06-09', '2026-06-01 11:16:22'),
(38, 'PO0006', 'ITM0004', 10, '0000-00-00', '2026-06-01 11:17:48'),
(39, 'PO0007', 'ITM0001', 10, '2026-06-09', '2026-06-01 11:27:44'),
(40, 'PO0007', 'ITM0002', 10, '2026-06-09', '2026-06-01 11:27:44'),
(41, 'PO0007', 'ITM0004', 12, '2026-06-16', '2026-06-01 11:27:44'),
(42, 'PO0009', 'ITM0002', 1, '2026-06-11', '2026-06-01 11:28:41'),
(43, 'PO0011', 'ITM0002', 5, '2026-06-03', '2026-06-03 05:26:52'),
(44, 'PO0011', 'ITM0002', 5, '2026-06-11', '2026-06-03 05:27:38'),
(45, 'PO0011', 'ITM0005', 10, '2026-06-17', '2026-06-03 05:27:38'),
(46, 'PO0012', 'ITM0005', 1000, '2026-06-30', '2026-06-08 07:23:25'),
(47, 'PO0013', 'ITM0003', 2000, '2026-12-31', '2026-06-11 09:30:10'),
(48, 'PO0013', 'ITM0008', 1000, '2026-12-31', '2026-06-11 09:30:10'),
(49, 'PO0014', 'ITM0004', 100, '2026-06-12', '2026-06-11 11:01:51'),
(50, 'PO0014', 'ITM0007', 10, '2026-06-13', '2026-06-11 11:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `item_master`
--

CREATE TABLE `item_master` (
  `item_no` varchar(10) NOT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `unit_measurement` varchar(50) DEFAULT NULL,
  `manufacturer_country` varchar(100) DEFAULT NULL,
  `pack_size` int(11) DEFAULT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_master`
--

INSERT INTO `item_master` (`item_no`, `item_name`, `description`, `brand`, `unit_measurement`, `manufacturer_country`, `pack_size`, `unit_cost`, `unit_price`, `stock_qty`) VALUES
('ITM0001', 'Detol ', 'Detol Plaster', 'detol', 'Capsule', 'india', 10, 450.00, 500.00, 0),
('ITM0002', 'Diclofenac', '', 'diclomed', 'Tablet', 'Italy', 12, 0.25, 2.00, 6),
('ITM0003', 'Salon Plast', '', 'Salon Plast', 'Nos', 'Itali', 5, 250.00, 275.00, 1990),
('ITM0004', 'Vintogino', 'Cream', 'Vintogino', 'Nos', 'India', 1, 420.00, 475.00, 97),
('ITM0005', 'Bandage', '', '', 'Nos', 'Italy', 1, 750.00, 825.00, 1000),
('ITM0006', 'Vicks', '', 'Inhaler', 'Nos', 'India', 1, 200.00, 250.00, 0),
('ITM0007', 'Plaster', 'Detol Plaster', 'Detol', 'Nos', 'India', 25, 15.00, 20.00, 9),
('ITM0008', 'Disprin', '', 'AER', 'Tablet', 'Sri Lanka', 10, 12.00, 123.00, 1000),
('ITM0009', 'Asprin', 'Balm', 'PCC', 'Tablet', 'Sri Lanka', 10, 12.00, 123.00, 0),
('ITM0010', 'Panadol', '', 'Panadol', 'Tablet', 'Italy', 10, 1.50, 2.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `med_id` int(11) NOT NULL,
  `med_name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `exp_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine`
--

INSERT INTO `medicine` (`med_id`, `med_name`, `category`, `qty`, `price`, `exp_date`) VALUES
(1, 'Penadol', 'Tablet', 8, 45.00, '2026-05-10'),
(2, 'Palster', 'Bandege', 98, 750.00, '2028-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(150) NOT NULL,
  `age` int(11) NOT NULL,
  `allergies` text DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `prescription_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Open','Rejected','Invoiced') NOT NULL DEFAULT 'Open',
  `cus_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `patient_name`, `age`, `allergies`, `contact_no`, `prescription_file`, `created_at`, `status`, `cus_id`) VALUES
(13, 'Amila', 41, 'No', '0716043227', '20260611_135722_0716043227_6a2aa2a2be50d.pdf', '2026-06-11 11:57:22', 'Open', 'CUS0016'),
(14, 'Amila', 62, 'No', '0716045223', '20260611_140914_0716045223_6a2aa56af05f2.png', '2026-06-11 12:09:14', 'Open', 'CUS0010');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `po_id` int(11) NOT NULL,
  `po_no` varchar(20) DEFAULT NULL,
  `vendor_id` varchar(20) DEFAULT NULL,
  `vendor_name` varchar(150) DEFAULT NULL,
  `po_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order`
--

INSERT INTO `purchase_order` (`po_id`, `po_no`, `vendor_id`, `vendor_name`, `po_date`, `created_at`, `status`) VALUES
(23, 'PO0003', 'VEN0002', 'Alcon', '2026-05-24', '2026-05-24 11:25:01', 'COMPLETED'),
(24, 'PO0004', 'VEN0003', 'Eden', '2026-05-25', '2026-05-24 11:34:01', 'COMPLETED'),
(29, 'PO0002', 'VEN0003', 'Eden', '2026-05-27', '2026-05-24 14:03:24', 'COMPLETED'),
(30, 'PO0003', 'VEN0003', 'Eden', '2026-05-20', '2026-05-24 14:03:40', 'COMPLETED'),
(31, 'PO0004', 'VEN0003', 'Eden', '2026-05-25', '2026-05-24 14:04:00', 'COMPLETED'),
(34, 'PO0005', 'VEN0002', 'Alcon', '2026-06-01', '2026-06-01 07:58:20', 'COMPLETED'),
(35, 'PO0006', 'VEN0002', 'Alcon', '0000-00-00', '2026-06-01 11:17:39', 'COMPLETED'),
(36, 'PO0007', 'VEN0002', 'Alcon', '2026-06-19', '2026-06-01 11:21:13', 'OPEN'),
(37, 'PO0008', 'VEN0003', 'Eden', '2026-06-02', '2026-06-01 11:21:58', 'OPEN'),
(38, 'PO0009', 'VEN0002', 'Alcon', '2026-06-05', '2026-06-01 11:22:27', 'COMPLETED'),
(39, 'PO0010', 'VEN0002', 'Alcon', '2026-06-03', '2026-06-03 05:25:30', 'OPEN'),
(40, 'PO0011', 'VEN0002', 'Alcon', '2026-06-04', '2026-06-03 05:25:58', 'COMPLETED'),
(41, 'PO0012', 'VEN0002', 'Alcon', '2026-06-01', '2026-06-08 07:23:06', 'COMPLETED'),
(42, 'PO0013', 'VEN0002', 'Alcon', '2026-06-10', '2026-06-11 09:29:38', 'COMPLETED'),
(43, 'PO0014', 'VEN0001', 'Eden Pharmasyticals', '2026-06-01', '2026-06-11 11:01:34', 'COMPLETED');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `po_no` varchar(20) DEFAULT NULL,
  `item_no` varchar(20) DEFAULT NULL,
  `item_name` varchar(150) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `po_no`, `item_no`, `item_name`, `expiry_date`, `cost`, `qty`, `total`) VALUES
(7, 'PO0007', 'ITM0001', 'Nicotin', '0000-00-00', 1500.00, 10, 0.00),
(8, 'PO0007', 'ITM0002', 'Plaster', '0000-00-00', 1500.00, 10, 0.00),
(9, 'PO0008', 'ITM0010', 'Panadol', '0000-00-00', 1500.00, 10, 15000.00),
(45, 'PO0003', 'ITM0008', 'Plaster', NULL, 12.00, 12, 144.00),
(46, 'PO0003', 'ITM0006', 'Nicotin', NULL, 123.00, 12, 1476.00),
(52, 'PO0002', 'ITM0007', 'Plaster', NULL, 100.00, 10, 1000.00),
(53, 'PO0002', 'ITM0001', 'Plaster', NULL, 450.00, 10, 4500.00),
(54, 'PO0003', 'ITM0009', 'Balm', NULL, 12.00, 1000, 12000.00),
(58, 'PO0004', 'ITM0003', 'Panadol', NULL, 15.00, 1000, 15000.00),
(59, 'PO0004', 'ITM0004', 'Panadol', NULL, 12.00, 10, 120.00),
(60, 'PO0004', 'ITM0009', 'Balm', NULL, 12.00, 1, 12.00),
(64, 'PO0005', 'ITM0006', 'Nicotin', NULL, 123.00, 100, 12300.00),
(65, 'PO0005', 'ITM0002', 'Nicotin', NULL, 1500.00, 100, 150000.00),
(66, 'PO0006', 'ITM0004', 'Panadol', NULL, 12.00, 10, 120.00),
(67, 'PO0007', 'ITM0004', 'Panadol', NULL, 12.00, 12, 144.00),
(68, 'PO0007', 'ITM0004', 'Panadol', NULL, 12.00, 12, 144.00),
(69, 'PO0008', 'ITM0005', 'Plaster', NULL, 1500.00, 12, 18000.00),
(70, 'PO0009', 'ITM0002', 'Nicotin', NULL, 1500.00, 1, 1500.00),
(71, 'PO0010', 'ITM0004', 'Panadol', NULL, 12.00, 1, 12.00),
(72, 'PO0011', 'ITM0005', 'Plaster', NULL, 1500.00, 10, 15000.00),
(73, 'PO0011', 'ITM0002', 'Nicotin', NULL, 1500.00, 10, 15000.00),
(74, 'PO0012', 'ITM0005', 'Plaster', NULL, 1500.00, 1000, 1500000.00),
(75, 'PO0013', 'ITM0008', 'Plaster', NULL, 12.00, 1000, 12000.00),
(76, 'PO0013', 'ITM0003', 'Panadol', NULL, 15.00, 2000, 30000.00),
(77, 'PO0014', 'ITM0004', 'Panadol', NULL, 12.00, 100, 1200.00),
(78, 'PO0014', 'ITM0007', 'Plaster', NULL, 100.00, 10, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales_order`
--

CREATE TABLE `sales_order` (
  `so_id` int(11) NOT NULL,
  `so_no` varchar(20) DEFAULT NULL,
  `so_date` date DEFAULT NULL,
  `cus_id` varchar(20) DEFAULT NULL,
  `cus_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_order`
--

INSERT INTO `sales_order` (`so_id`, `so_no`, `so_date`, `cus_id`, `cus_name`, `address`, `city`, `total_amount`, `created_at`) VALUES
(1, 'INV00001', '2026-06-01', 'CUS0008', '', '', '', 0.00, '2026-06-01 10:43:22'),
(2, 'INV00002', '2026-06-01', 'CUS0005', 'Haresh Sapath Hewage', '101 විනයාලංකාර මාවත', 'කොළඹ', 16800.00, '2026-06-01 10:46:12'),
(3, 'INV00003', '2026-06-01', 'CUS0004', 'Nisal  Rathnayake', 'Kandy', 'Mathara', 500.00, '2026-06-01 10:50:01'),
(4, 'INV00004', '2026-06-01', 'CUS0004', 'Nisal  Rathnayake', 'Kandy', 'Mathara', 16500.00, '2026-06-01 11:29:17'),
(5, 'INV00005', '2026-06-01', 'CUS0005', 'Haresh Sapath Hewage', '101 විනයාලංකාර මාවත', 'කොළඹ', 3300.00, '2026-06-01 11:29:49'),
(6, 'INV00006', '2026-06-01', 'CUS0002', 'Hashan  Perera', 'No.31, Murutalawa Road,Peradeniya', 'Kandy', 6600.00, '2026-06-01 13:10:59'),
(7, 'INV00007', '2026-06-01', 'CUS0005', 'Haresh Sapath Hewage', '101 විනයාලංකාර මාවත', 'කොළඹ', 5000.00, '2026-06-01 13:13:56'),
(8, 'INV00008', '2026-06-03', 'CUS0008', 'Kanchana Madushan Wimalasooriya', '123456789', 'Kandy', 1650.00, '2026-06-03 05:30:57'),
(9, 'INV00009', '2026-06-07', 'CUS0002', 'Hashan  Perera', 'No.31, Murutalawa Road,Peradeniya', 'Kandy', 3300.00, '2026-06-07 08:11:56'),
(10, 'INV00010', '2026-06-11', 'CUS0010', 'Haresh Sapath Hewage', '101 විනයාලංකාර මාවත', 'කොළඹ', 100.00, '2026-06-11 12:11:47');

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` int(11) NOT NULL,
  `so_no` varchar(20) DEFAULT NULL,
  `item_no` varchar(20) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `so_no`, `item_no`, `item_name`, `expiry_date`, `price`, `qty`, `total`) VALUES
(1, 'INV00002', 'ITM0005', 'Plaster', '0000-00-00', 30.00, 10, 300.00),
(2, 'INV00002', 'ITM0004', 'Panadol', '0000-00-00', 1650.00, 10, 16500.00),
(3, 'INV00003', 'ITM0003', 'Panadol', NULL, 40.00, 10, 400.00),
(4, 'INV00003', 'ITM0007', 'Plaster', NULL, 100.00, 1, 100.00),
(5, 'INV00004', 'ITM0002', 'Nicotin', NULL, 1650.00, 10, 16500.00),
(6, 'INV00005', 'ITM0002', 'Nicotin', NULL, 1650.00, 2, 3300.00),
(7, 'INV00006', 'ITM0004', 'Panadol', NULL, 1650.00, 4, 6600.00),
(8, 'INV00007', 'ITM0001', 'Plaster', NULL, 500.00, 10, 5000.00),
(9, 'INV00008', 'ITM0004', 'Panadol', NULL, 1650.00, 1, 1650.00),
(10, 'INV00009', 'ITM0002', 'Nicotin', NULL, 1650.00, 2, 3300.00),
(11, 'INV00010', 'ITM0002', 'Nicotin', NULL, 100.00, 1, 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `emp_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('Admin','Pharmacist','Cashier','Manager') NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `emp_id`, `full_name`, `username`, `password`, `user_role`, `status`, `created_date`) VALUES
(1, '000119', 'Haresh Hewage', 'Haresh', '$2y$10$k2jccmN9.PkiReCbZAl6Xe9sugpECyS0pIbklFYtFEDtH0uGFqlOi', 'Admin', 'Active', '2026-06-07 12:48:04'),
(2, '000100', 'Hashini', 'Hashini', '$2y$10$S9DRmLxhjLkTM5EUxP60ee5fxmIu60eLH2dlpkd/E3akimrtBGrQO', 'Pharmacist', 'Active', '2026-06-07 09:36:07'),
(4, 'EMP0120', 'Shanaz', 'Shanaz', '$2y$10$AsPiUeVIglK8SAOWUYkUI.Bs4RIS/X74KX6okYS7Wogn.ua2ThG2m', 'Admin', 'Active', '2026-06-11 07:26:24');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `vendor_id` varchar(10) NOT NULL,
  `vendor_name` varchar(100) DEFAULT NULL,
  `business_registration` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `address2` text DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`vendor_id`, `vendor_name`, `business_registration`, `country`, `postal_code`, `address`, `address2`, `contact_person`, `mobile`, `contact_no`, `fax`, `email`) VALUES
('VEN0001', 'Eden Pharmasyticals', '14567', 'Sri Lanka', '456', '42', '45', 'Sunil', '0748033651', '0812388646', '0812388616', 'haresh.groupit@delmege.com');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_master`
--

CREATE TABLE `vendor_master` (
  `id` int(11) NOT NULL,
  `vendor_id` varchar(20) NOT NULL,
  `vendor_name` varchar(150) NOT NULL,
  `business_registration` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `address1` text DEFAULT NULL,
  `address2` text DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `contact_no` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_master`
--

INSERT INTO `vendor_master` (`id`, `vendor_id`, `vendor_name`, `business_registration`, `country`, `postal_code`, `address1`, `address2`, `contact_person`, `mobile`, `contact_no`, `fax`, `email`, `created_at`) VALUES
(1, 'VEN0001', 'Eden Pharmasyticals', '123456', 'UK', '00010', '123', '', 'Tharushi', '', '', '', '', '2026-05-18 02:30:30'),
(5, 'VEN0002', 'Alcon', '789', 'Sri Lanka', '01000', '101 විනයාලංකාර මාවත', '', 'Haresh Sapath Hewage', '0716043227', '', '', 'hareshhewage@gmail.com', '2026-05-18 02:35:52'),
(7, 'VEN0003', 'Eden', '', 'Japan', '0000006459', '', '', 'Lee', '', '', '', '', '2026-05-18 02:39:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cus_id` (`cus_id`);

--
-- Indexes for table `grn`
--
ALTER TABLE `grn`
  ADD PRIMARY KEY (`grn_id`);

--
-- Indexes for table `item_master`
--
ALTER TABLE `item_master`
  ADD PRIMARY KEY (`item_no`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`med_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`po_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_order`
--
ALTER TABLE `sales_order`
  ADD PRIMARY KEY (`so_id`),
  ADD UNIQUE KEY `so_no` (`so_no`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `vendor_master`
--
ALTER TABLE `vendor_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_id` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `grn`
--
ALTER TABLE `grn`
  MODIFY `grn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `med_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `sales_order`
--
ALTER TABLE `sales_order`
  MODIFY `so_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendor_master`
--
ALTER TABLE `vendor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
