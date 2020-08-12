-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 12, 2020 at 11:20 PM
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
  `lid` int(255) NOT NULL,
  `locMax` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Account`
--

INSERT INTO `Account` (`username`, `psswrd`, `aid`, `email`, `b_date`, `name`, `lid`, `locMax`) VALUES
('AdminTest', 'test987', '1', 'test12@test.testwork', '2020-07-11', 'yessir', 1, 5),
('shadowbarker', 'darkness1', NULL, 'okok@yesyes.cool', '2020-05-13', 'bil', 2, 5),
('lightbringer', 'holyone0', NULL, 'tippytop@one.two', '2020-03-08', 'kyle', 3, 5),
('avidgamer', 'funzi', NULL, 'gumgum@three.tree', '2020-01-03', 'cucumber', 4, 5),
('test', '$2y$10$TlDCTUSLYPzfxhhfz.8Jf.ox5A.IAX8XAP79Cb8KL.9UTdZX7ZT0q', '2', 'haggerto@oregonstate.edu', '1970-01-01', 'Test User', 5, 5),
('tester1', '$2y$10$XkqZQyst0NuOjPuzxft0wexEeiQRaV6OwrVoIy2/9sSJh1/jxr002', NULL, 'test@gmail.com', '2020-08-04', 'tester1', 7, 5),
('princeri', '$2y$10$q7y7NEQbloMIoL7fx6EMc.PEAaGNkJY8me6h0hB2U5sFO4AJmhUqi', NULL, 'princeri@oregonstate.edu', '1998-04-17', 'Riley Prince', 8, 5),
('tester5', '$2y$10$ejVwXYrO71iGUfJ6zDPq8.7zefDyLYvPz59.kT67ARJZ9dSImR5ce', NULL, 'lol@gg.net', '2020-07-09', 'okgg', 11, 5),
('tester2', '$2y$10$Z5KNaShMR6gAlOSrDD8vueZv/znkbmE6bMsPSDNfvUFeyJRlQ79XG', NULL, 'ok@gg.com', '2020-07-02', 'testcreate', 12, 5),
('tester3', '$2y$10$6nLncJxHPfPvN11.PLtE3u1GyjupIWlo3tLKx8dNnW8xW4lJ5Ugne', NULL, 'ok@gg.net', '2020-07-25', 'timber', 13, 5);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_lid` int(255) NOT NULL,
  `lat` varchar(9) NOT NULL,
  `lon` varchar(9) NOT NULL,
  `is_subscribed` varchar(3) NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `name`, `user_lid`, `lat`, `lon`, `is_subscribed`) VALUES
(1, 'Corvallis', 5, '44.5781', '-123.2752', 'YES'),
(2, 'New York', 5, '40.7143', '-74.0060', 'NO'),
(3, 'Albany', 1, '44.6367', '-123.1047', 'NO');

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
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `user_lid` (`user_lid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Account`
--
ALTER TABLE `Account`
  MODIFY `lid` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`user_lid`) REFERENCES `Account` (`lid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
