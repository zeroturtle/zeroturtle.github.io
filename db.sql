-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 12:11 PM
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
-- Database: `optimus`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `REGISTERED` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`ID`, `USERNAME`, `PASSWORD`, `EMAIL`, `ACTIVE`, `REGISTERED`) VALUES
(2, 'miketyson', '$2y$10$YuSfRs1ttXf7GVMyY9ZFHOzRDCvhFtosINJFZMSeyeIPeZYPnWRAm', 'miketyson@fuck.you', 1, '2025-03-12 06:13:14'),
(9, 'John', '$2y$10$5z96mQngEIabmf99Ce7dReRHRMcf93rVdPsRdBdmb807UsaOAVFRm', 'zeroturtle@ua.fm', 1, '2025-03-28 06:19:32');

-- --------------------------------------------------------

--
-- Table structure for table `competition`
--

CREATE TABLE `competition` (
  `COMPETITION_ID` int(11) NOT NULL,
  `DESCRIPTION` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`DESCRIPTION`)),
  `LICENCE_ID` int(11) NOT NULL,
  `VISIBLE` tinyint(1) DEFAULT 1,
  `CREATED` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `competition`
--

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `downloads`
--

INSERT INTO `downloads` (`id`, `filename`, `ip_address`, `created_at`) VALUES
(49, 'optimus_50_ru.zip', '127.0.0.1', '2025-03-19 19:41:05'),
(48, 'Reports_en.zip', '127.0.0.1', '2025-03-19 16:47:31');

-- --------------------------------------------------------

--
-- Table structure for table `licence`
--

CREATE TABLE `licence` (
  `LICENCE_ID` int(11) NOT NULL,
  `NUMBER` varchar(32) NOT NULL,
  `NAME` varchar(127) NOT NULL,
  `EMAIL` varchar(127) NOT NULL,
  `TITLE` varchar(127) DEFAULT NULL,
  `COMPANY` varchar(127) DEFAULT NULL,
  `DATESTART` date NOT NULL,
  `DATEEND` date NOT NULL,
  `LICENCETYPE` tinyint(4) NOT NULL,
  `EVENTTYPES` int(11) NOT NULL,
  `LICENCEHASH` varchar(32) NOT NULL,
  `ACTIVE` tinyint(1) DEFAULT 1,
  `CREATED` timestamp NOT NULL DEFAULT current_timestamp(),
  `ACCOUNT_ID` int(11) NOT NULL,
  `VERSION` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `licence`
--

INSERT INTO `licence` (`LICENCE_ID`, `NUMBER`, `NAME`, `EMAIL`, `TITLE`, `COMPANY`, `DATESTART`, `DATEEND`, `LICENCETYPE`, `EVENTTYPES`, `LICENCEHASH`, `ACTIVE`, `CREATED`, `ACCOUNT_ID`, `VERSION`) VALUES
(38, '67dd2fb72b341', 'john scatman', 'zeroturtle@ua.fm', 'founder', '', '2025-03-21', '2026-03-21', 0, 4766, '8a33ec62005b77ffdf3e5c7924877bd6', 1, '2025-03-21 09:21:59', 0, ''),
(37, '67d5237ec7707', 'john scatman', 'dd2@fds.cc', 'founder', '', '2025-03-15', '2026-03-15', 0, 4766, '3b03610a5e3611e1da12148c9a76a488', 1, '2025-03-15 06:51:42', 0, ''),
(39, '73B4A2C6-D591-464B-BE83-97CA80FE', '???? UPF', 'zeroturtle@ua.fm', '', 'Pi-SUN', '2025-04-20', '2026-04-20', 0, 4766, '27eada2c12e06b8bac9841b74dffb7a8', 1, '2025-04-20 10:08:39', 9, 'OPTIMUS=5.0.0.14;FERRET=2.0.0.27;VANGUARD=3.0.0.12;ZODIAK=2.3.0.18'),
(40, '190051E7-F81D-4068-B78C-BD8CDE9A', '???? UPF', 'zeroturtle@ua.fm', '', 'Pi-SUN', '2025-04-20', '2026-04-20', 0, 4766, 'c51ef7bd8dfcdf7d76fde411f1dca228', 1, '2025-04-20 10:08:56', 9, 'OPTIMUS=5.0.0.14;FERRET=2.0.0.27;VANGUARD=3.0.0.12;ZODIAK=2.3.0.18');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `LICENCE_ID` int(11) NOT NULL,
  `VERSION` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`LICENCE_ID`, `VERSION`) VALUES
(37, 'OPTIMUS=5.0.0.14;FERRET=2.0.0.27;VANGUARD=3.0.0.12;ZODIAK=2.3.0.18'),
(38, 'OPTIMUS=5.0.0.14;FERRET=2.0.0.27;VANGUARD=3.0.0.12;ZODIAK=2.3.0.18');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `PROMO_ID` int(11) NOT NULL,
  `CODE` varchar(32) NOT NULL,
  `DATESTART` date NOT NULL,
  `DATEEND` date NOT NULL,
  `TYPE_ID` tinyint(4) NOT NULL,
  `PROMO_TYPE` tinyint(4) NOT NULL,
  `ACTIVE` tinyint(1) DEFAULT 1,
  `CREATED` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resetpasswords`
--

CREATE TABLE `resetpasswords` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `EXPDATE` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `competition`
--
ALTER TABLE `competition`
  ADD PRIMARY KEY (`COMPETITION_ID`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `licence`
--
ALTER TABLE `licence`
  ADD PRIMARY KEY (`LICENCE_ID`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD KEY `LICENCE_ID` (`LICENCE_ID`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`PROMO_ID`);

--
-- Indexes for table `resetpasswords`
--
ALTER TABLE `resetpasswords`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `competition`
--
ALTER TABLE `competition`
  MODIFY `COMPETITION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `licence`
--
ALTER TABLE `licence`
  MODIFY `LICENCE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `PROMO_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resetpasswords`
--
ALTER TABLE `resetpasswords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
