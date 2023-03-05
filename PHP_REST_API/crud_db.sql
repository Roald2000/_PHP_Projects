-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2023 at 03:35 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crud_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `crud_tbl`
--

CREATE TABLE `crud_tbl` (
  `row_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` decimal(12,2) UNSIGNED NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `crud_tbl`
--

INSERT INTO `crud_tbl` (`row_id`, `item_name`, `item_price`, `timestamp`) VALUES
(64, 'Paper', '12.00', '2023-03-04 22:17:14'),
(65, 'Pen', '12.00', '2023-03-04 22:17:19'),
(66, 'Book', '250.00', '2023-03-04 22:17:23'),
(67, 'Marker', '25.00', '2023-03-04 22:17:50');

-- --------------------------------------------------------

--
-- Table structure for table `todo_tbl`
--

CREATE TABLE `todo_tbl` (
  `row_id` bigint(20) NOT NULL,
  `todo` varchar(255) NOT NULL,
  `is_done` int(1) UNSIGNED ZEROFILL NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `todo_tbl`
--

INSERT INTO `todo_tbl` (`row_id`, `todo`, `is_done`, `timestamp`) VALUES
(2, 'New App', 1, '2023-02-26 14:30:13'),
(3, 'Create App', 0, '2023-02-26 14:18:23'),
(4, 'Create App', 0, '2023-02-26 14:18:24');

-- --------------------------------------------------------

--
-- Stand-in structure for view `todo_view`
-- (See below for the actual view)
--
CREATE TABLE `todo_view` (
`row_id` bigint(20)
,`todo` varchar(255)
,`is_done` int(1) unsigned zerofill
,`timestamp` varchar(89)
);

-- --------------------------------------------------------

--
-- Structure for view `todo_view`
--
DROP TABLE IF EXISTS `todo_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `todo_view`  AS SELECT `todo_tbl`.`row_id` AS `row_id`, `todo_tbl`.`todo` AS `todo`, `todo_tbl`.`is_done` AS `is_done`, date_format(`todo_tbl`.`timestamp`,'%M %D, %Y @ %r') AS `timestamp` FROM `todo_tbl``todo_tbl`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crud_tbl`
--
ALTER TABLE `crud_tbl`
  ADD PRIMARY KEY (`row_id`);

--
-- Indexes for table `todo_tbl`
--
ALTER TABLE `todo_tbl`
  ADD PRIMARY KEY (`row_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crud_tbl`
--
ALTER TABLE `crud_tbl`
  MODIFY `row_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `todo_tbl`
--
ALTER TABLE `todo_tbl`
  MODIFY `row_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
