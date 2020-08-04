-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 27, 2020 at 01:39 AM
-- Server version: 10.4.13-MariaDB-log
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs361_haggerto`
--

-- --------------------------------------------------------

--
-- Table structure for table `Account`
--

CREATE TABLE `Account` (
  `username` varchar(255) NOT NULL,
  `psswrd` varchar(255) NOT NULL,
  `aid` varchar(10) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `b_date` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `lid` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Account`
--

INSERT INTO `Account` (`username`, `psswrd`, `aid`, `email`, `b_date`, `name`, `lid`) VALUES
('AdminTest', 'test987', '1', 'test12@test.testwork', '2020-07-11', 'yessir', 1),
('shadowbarker', 'darkness1', NULL, 'okok@yesyes.cool', '2020-05-13', 'bil', 2),
('lightbringer', 'holyone0', NULL, 'tippytop@one.two', '2020-03-08', 'kyle', 3),
('avidgamer', 'funzi', NULL, 'gumgum@three.tree', '2020-01-03', 'cucumber', 4),
('test', '$2y$10$TlDCTUSLYPzfxhhfz.8Jf.ox5A.IAX8XAP79Cb8KL.9UTdZX7ZT0q', NULL, 'haggerto@oregonstate.edu', '1970-01-01', 'Logged In', 5),
('test2', '$2y$10$0ba0T7A5dZb.cbZ51.EX/uLV0RO50wdk30.mmZZdxwYdeBikc3Boe', NULL, 'email@test.com', '1970-01-01', 'Test Account 2', 6),
('tester1', '$2y$10$XkqZQyst0NuOjPuzxft0wexEeiQRaV6OwrVoIy2/9sSJh1/jxr002', NULL, 'test@gmail.com', '2020-08-04', 'tester1', 7);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `name` varchar(255) DEFAULT NULL,
  `user_lid` int(255) NOT NULL,
  `lat` varchar(9) NOT NULL,
  `lon` varchar(9) NOT NULL,
  `is_subscribed` varchar(3) NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`name`, `user_lid`, `lat`, `lon`, `is_subscribed`) VALUES
('Corvallis', 5, '44.5781', '-123.2752', 'YES'),
('New York', 5, '40.7143', '-74.0060', 'NO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Account`
--
ALTER TABLE `Account`
  ADD PRIMARY KEY (`lid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `lid` (`lid`),
  ADD UNIQUE KEY `aid` (`aid`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD KEY `user_lid` (`user_lid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Account`
--
ALTER TABLE `Account`
  MODIFY `lid` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`user_lid`) REFERENCES `Account` (`lid`);
COMMIT;

--
-- added column to table `account`
ALTER TABLE Acccount
  ADD locMax int(11);

--
-- removed Constraints for aid on table `account`
ALTER TABLE Account
  DROP INDEX aid;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
