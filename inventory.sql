-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql307.infinityfree.com
-- Generation Time: Dec 21, 2023 at 11:18 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_35573318_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productname` varchar(255) NOT NULL,
  `productstock` int(10) NOT NULL,
  `productbalance` int(10) NOT NULL,
  `productimg` varchar(2555) NOT NULL,
  `productid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productname`, `productstock`, `productbalance`, `productimg`, `productid`) VALUES
('KIT PANTAS MEMBACA', 1, 67, 'uploads/SET KIT PANTAS.png', 6),
('KIT COMBO', 1, 10, 'uploads/20231108_163439_1.png', 16),
('KIT EASY READER', 1, 2, 'uploads/20231207_152600.png', 17),
('KIT READ HERO', 1, 0, 'uploads/SET BUKU BM.png', 18),
('SET BUKU READ HERO', 1, 0, 'uploads/BUKU BM.png', 19),
('SET BUKU EASY READER', 1, 150, 'uploads/BUKU BI.png', 20),
('REWARD CHART', 1, 711, 'uploads/REWARD CHART.png', 21),
('STAR', 1, 469, 'uploads/STAR-01.png', 22),
('LESSON PLAN', 1, 1, 'uploads/LESSON PLAN.png', 23),
('PANDUAN HURUF', 1, 1, 'uploads/PANDUAN HURUF.png', 24),
('SET BBM', 1, 10, 'uploads/3.png', 25),
('BANNER', 1, 1, 'uploads/2-1.png', 26),
('T-STAND', 1, 1, 'uploads/1.png', 27),
('PAPER BAG JELAJAH HEROES', 15, 15, 'uploads/BEG.png', 31),
('COLOUR PENSIL', 16, 15, 'uploads/COLOR PENCIL.png', 32),
('BUKU CERITA CERDIK SI KELDAI', 104, 104, 'uploads/CERDIK SI KELDAI.png', 33),
('BUKU CERITA ANGSA EMAS', 127, 126, 'uploads/ANGSA EMAS.png', 34),
('BUKU CERITA SOMBONG MEMBAWA PADAH', 120, 120, 'uploads/SOMBONG MEMBAWA PADAH.png', 35),
('BUKU CERITA MAKAN MALAM SI MUSANG', 58, 57, 'uploads/MAKAN MALAM SI MUSANG.png', 36);

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `profileid` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Email` varchar(20) NOT NULL,
  `Password` text NOT NULL,
  `profiletype` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profileid`, `Username`, `Email`, `Password`, `profiletype`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin', 'Admin'),
(7, 'afifah', 'afifah.intern@gmail.', '1234', 'User'),
(8, 'hazreen', 'arisha.ameterna@gmai', 'Arisha@98', 'User'),
(9, 'nadia shahila', 'nadiashahila.ametern', 'Nadia@96', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stockid` int(11) NOT NULL,
  `stockamount` int(11) NOT NULL,
  `stockreject` int(11) NOT NULL,
  `stockaccept` int(11) NOT NULL,
  `stockdate` datetime NOT NULL DEFAULT current_timestamp(),
  `productbalance` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`stockid`, `stockamount`, `stockreject`, `stockaccept`, `stockdate`, `productbalance`, `productid`, `username`) VALUES
(44, 50, 10, 40, '2023-12-13 03:27:55', 41, 17, 'hazreen'),
(45, 70, 0, 70, '2023-12-17 21:38:17', 70, 6, 'hazreen'),
(46, 16, 0, 16, '2023-12-17 21:38:33', 17, 16, 'hazreen'),
(47, 164, 0, 164, '2023-12-17 21:38:47', 205, 17, 'hazreen'),
(48, 0, 0, 0, '2023-12-17 21:38:56', 1, 18, 'hazreen'),
(49, 0, 0, 0, '2023-12-17 21:39:12', 1, 19, 'hazreen'),
(50, 164, 0, 164, '2023-12-17 21:39:40', 165, 20, 'hazreen'),
(51, 2, 0, 2, '2023-12-17 21:39:50', 207, 17, 'hazreen'),
(52, 725, 0, 725, '2023-12-17 21:40:01', 726, 21, 'hazreen'),
(53, 497, 0, 497, '2023-12-17 21:40:22', 498, 22, 'hazreen'),
(54, 15, 0, 15, '2023-12-17 21:41:00', 32, 16, 'hazreen'),
(55, 9, 0, 9, '2023-12-17 21:43:16', 10, 25, 'hazreen'),
(56, 6, 0, 6, '2023-12-17 21:56:37', 22, 16, 'hazreen'),
(57, 2, 0, 2, '2023-12-17 22:53:09', 21, 16, 'hazreen');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transactionid` int(11) NOT NULL,
  `transactionamount` int(11) NOT NULL,
  `transactiontype` varchar(5) NOT NULL,
  `transactiondate` datetime NOT NULL DEFAULT current_timestamp(),
  `productid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transactionid`, `transactionamount`, `transactiontype`, `transactiondate`, `productid`, `username`) VALUES
(81, 2, 'Out', '2023-12-07 15:51:34', 6, 'afifah'),
(82, 16, 'In', '2023-12-07 15:53:38', 6, 'afifah'),
(83, 40, 'In', '2023-12-13 03:27:55', 17, 'hazreen'),
(84, 1, 'Out', '2023-12-13 03:29:49', 6, 'hazreen'),
(85, 70, 'In', '2023-12-17 21:38:17', 6, 'hazreen'),
(86, 16, 'In', '2023-12-17 21:38:33', 16, 'hazreen'),
(87, 164, 'In', '2023-12-17 21:38:47', 17, 'hazreen'),
(88, 0, 'In', '2023-12-17 21:38:56', 18, 'hazreen'),
(89, 0, 'In', '2023-12-17 21:39:12', 19, 'hazreen'),
(90, 164, 'In', '2023-12-17 21:39:40', 20, 'hazreen'),
(91, 2, 'In', '2023-12-17 21:39:50', 17, 'hazreen'),
(92, 725, 'In', '2023-12-17 21:40:01', 21, 'hazreen'),
(93, 497, 'In', '2023-12-17 21:40:22', 22, 'hazreen'),
(94, 15, 'In', '2023-12-17 21:41:00', 16, 'hazreen'),
(95, 16, 'Out', '2023-12-17 21:41:31', 16, 'hazreen'),
(96, 205, 'Out', '2023-12-17 21:41:47', 17, 'hazreen'),
(97, 1, 'Out', '2023-12-17 21:41:58', 18, 'hazreen'),
(98, 0, 'Out', '2023-12-17 21:42:09', 19, 'hazreen'),
(99, 1, 'Out', '2023-12-17 21:42:19', 20, 'hazreen'),
(100, 1, 'Out', '2023-12-17 21:42:30', 21, 'hazreen'),
(101, 1, 'Out', '2023-12-17 21:42:44', 22, 'hazreen'),
(102, 9, 'In', '2023-12-17 21:43:16', 25, 'hazreen'),
(103, 6, 'In', '2023-12-17 21:56:37', 16, 'hazreen'),
(104, 0, 'Out', '2023-12-17 22:38:55', 19, 'hazreen'),
(105, 2, 'Out', '2023-12-17 22:39:01', 16, 'hazreen'),
(106, 1, 'Out', '2023-12-17 22:39:13', 16, 'hazreen'),
(107, 2, 'In', '2023-12-17 22:53:09', 16, 'hazreen'),
(108, 2, 'Out', '2023-12-17 22:53:16', 16, 'hazreen'),
(109, 14, 'Out', '2023-12-17 22:53:24', 20, 'hazreen'),
(110, 1, 'Out', '2023-12-17 22:53:33', 19, 'hazreen'),
(111, 14, 'Out', '2023-12-17 22:53:43', 22, 'hazreen'),
(112, 14, 'Out', '2023-12-17 22:53:43', 22, 'hazreen'),
(113, 14, 'Out', '2023-12-17 22:53:52', 21, 'hazreen'),
(114, 2, 'Out', '2023-12-18 00:50:49', 6, 'hazreen'),
(115, 3, 'Out', '2023-12-19 22:01:26', 16, 'hazreen'),
(116, 1, 'Out', '2023-12-19 22:01:31', 6, 'hazreen'),
(117, 1, 'Out', '2023-12-19 22:01:41', 32, 'hazreen'),
(118, 1, 'Out', '2023-12-19 22:01:56', 34, 'hazreen'),
(119, 1, 'Out', '2023-12-19 22:02:41', 36, 'hazreen'),
(120, 5, 'Out', '2023-12-21 22:13:15', 16, 'hazreen'),
(121, 1, 'Out', '2023-12-21 22:44:50', 16, 'hazreen');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productid`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`profileid`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stockid`),
  ADD KEY `product_id` (`productid`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transactionid`),
  ADD KEY `productid` (`productid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `productid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `profileid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stockid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transactionid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `product_id` FOREIGN KEY (`productid`) REFERENCES `product` (`productid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `productid` FOREIGN KEY (`productid`) REFERENCES `product` (`productid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
