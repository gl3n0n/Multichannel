-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2015 at 06:39 AM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `multichannel`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_logs`
--

CREATE TABLE IF NOT EXISTS `api_logs` (
`ApiLogId` int(11) unsigned NOT NULL,
  `BrandId` int(11) unsigned DEFAULT NULL,
  `CampaignId` int(11) unsigned DEFAULT NULL,
  `ChannelId` int(11) unsigned DEFAULT NULL,
  `ServerIP` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `IPCalled` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE IF NOT EXISTS `brands` (
`BrandId` int(11) unsigned NOT NULL,
  `ClientId` int(11) unsigned NOT NULL,
  `BrandName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Duration` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`BrandId`, `ClientId`, `BrandName`, `Description`, `Duration`, `Status`, `DateCreated`, `CreatedBy`, `DateUpdated`, `UpdatedBy`) VALUES
(1, 1, 'Maggie', 'Magic Sarap', '30 Days', 'ACTIVE', '2015-02-25 19:47:01', 1, '2015-02-25 11:47:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE IF NOT EXISTS `campaigns` (
`CampaignId` int(11) unsigned NOT NULL,
  `BrandId` int(11) unsigned NOT NULL DEFAULT '0',
  `CampaignName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Duration` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

CREATE TABLE IF NOT EXISTS `channels` (
`ChannelId` int(11) unsigned NOT NULL,
  `BrandId` int(11) unsigned DEFAULT NULL,
  `CampaignId` int(11) unsigned DEFAULT NULL,
  `ChannelName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Duration` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
`ClientId` int(11) unsigned NOT NULL,
  `CompanyName` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Landline` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`ClientId`, `CompanyName`, `Address`, `Email`, `Landline`, `Status`, `DateCreated`, `CreatedBy`, `DateUpdated`, `UpdatedBy`) VALUES
(1, 'Uniliver', 'Makati City', 'uniliver@uniliver.com', '93423232', 'ACTIVE', '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cms_logs`
--

CREATE TABLE IF NOT EXISTS `cms_logs` (
`CmsLogId` int(11) unsigned NOT NULL,
  `UserId` int(11) unsigned NOT NULL,
  `TableAffected` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SqlQuery` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `LogDate` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

CREATE TABLE IF NOT EXISTS `coupon` (
`CouponId` int(11) unsigned NOT NULL,
  `Code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `TypeId` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Source` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `HasImage` enum('0','1') COLLATE utf8_unicode_ci DEFAULT '0',
  `ExpiryDate` datetime DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
`CustomerId` int(11) unsigned NOT NULL,
  `ClientId` int(11) unsigned NOT NULL,
  `Username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `FirstName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MiddleName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LastName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Gender` enum('M','F') COLLATE utf8_unicode_ci DEFAULT 'M',
  `ContactNumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_points`
--

CREATE TABLE IF NOT EXISTS `customer_points` (
`PointId` int(11) unsigned NOT NULL,
  `ClientId` int(11) unsigned NOT NULL,
  `Balance` int(11) unsigned NOT NULL,
  `Used` int(11) unsigned NOT NULL,
  `Total` int(11) unsigned NOT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `points`
--

CREATE TABLE IF NOT EXISTS `points` (
`PointsId` int(11) unsigned NOT NULL,
  `BrandId` int(11) unsigned DEFAULT NULL,
  `CampaignId` int(11) unsigned DEFAULT NULL,
  `ChannelId` int(11) unsigned DEFAULT NULL,
  `From` datetime DEFAULT NULL,
  `To` datetime DEFAULT NULL,
  `Value` int(11) unsigned DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `points_multiplier`
--

CREATE TABLE IF NOT EXISTS `points_multiplier` (
`MultiplierId` int(11) unsigned NOT NULL,
  `PointsId` int(11) unsigned DEFAULT NULL,
  `Multiplier` int(11) unsigned DEFAULT NULL,
  `From` datetime DEFAULT NULL,
  `To` datetime DEFAULT NULL,
  `Value` int(11) unsigned DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `raffle`
--

CREATE TABLE IF NOT EXISTS `raffle` (
`RaffleId` int(11) unsigned NOT NULL,
  `Source` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `NoOfWinners` int(11) NOT NULL DEFAULT '0',
  `FdaNo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `DrawDate` datetime DEFAULT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `raffle_details`
--

CREATE TABLE IF NOT EXISTS `raffle_details` (
`RaffleDetailId` int(11) unsigned NOT NULL,
  `RaffleId` int(11) unsigned NOT NULL,
  `RewardId` int(11) unsigned NOT NULL,
  `UserId` int(11) NOT NULL DEFAULT '0',
  `Position` int(11) NOT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `redeemed_reward`
--

CREATE TABLE IF NOT EXISTS `redeemed_reward` (
`RedeemedId` int(11) unsigned NOT NULL,
  `RewardId` int(11) unsigned NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Source` enum('CAMPAIGN','BRANDS','POINTS','COUPONING') COLLATE utf8_unicode_ci DEFAULT 'BRANDS',
  `Action` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rewards_list`
--

CREATE TABLE IF NOT EXISTS `rewards_list` (
`RewardId` int(11) unsigned NOT NULL,
  `Title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `HasImage` enum('0','1') COLLATE utf8_unicode_ci DEFAULT '0',
  `Availability` datetime DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reward_details`
--

CREATE TABLE IF NOT EXISTS `reward_details` (
`RewardConfigId` int(11) unsigned NOT NULL,
  `RewardId` int(11) unsigned NOT NULL,
  `Limitations` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Availability` datetime DEFAULT NULL,
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`UserId` int(11) unsigned NOT NULL,
  `ClientId` int(11) DEFAULT NULL,
  `FirstName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MiddleName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LastName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Gender` enum('M','F') COLLATE utf8_unicode_ci DEFAULT 'M',
  `Birthdate` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ContactNumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `AccessType` enum('SUPERADMIN','ADMIN','CAMPAIGNMANAGER','MANAGER') COLLATE utf8_unicode_ci DEFAULT 'MANAGER',
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserId`, `ClientId`, `FirstName`, `MiddleName`, `LastName`, `Gender`, `Birthdate`, `ContactNumber`, `Address`, `Email`, `Username`, `Password`, `AccessType`, `Status`, `DateCreated`, `CreatedBy`, `DateUpdated`, `UpdatedBy`) VALUES
(1, NULL, 'Yuri', NULL, 'Santos', 'M', NULL, NULL, NULL, NULL, 'yurisantos', '5f4dcc3b5aa765d61d8327deb882cf99', 'SUPERADMIN', 'ACTIVE', NULL, NULL, '2015-02-26 02:21:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_access`
--

CREATE TABLE IF NOT EXISTS `user_access` (
`UserAccessId` int(11) unsigned NOT NULL,
  `ModuleId` int(11) unsigned DEFAULT NULL,
  `UserId` int(11) unsigned DEFAULT NULL,
  `AccessType` enum('ADMIN','CLIENT','CUSTOMER') COLLATE utf8_unicode_ci DEFAULT 'CLIENT',
  `Status` enum('PENDING','ACTIVE','INACTIVE') COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `DateCreated` datetime DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `DateUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdatedBy` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_logs`
--
ALTER TABLE `api_logs`
 ADD PRIMARY KEY (`ApiLogId`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
 ADD PRIMARY KEY (`BrandId`), ADD KEY `BrandName_idx` (`BrandName`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
 ADD PRIMARY KEY (`BrandId`), ADD KEY `CampaignId_idx` (`CampaignId`);

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
 ADD PRIMARY KEY (`ChannelId`), ADD KEY `ChannelId_idx` (`ChannelId`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
 ADD PRIMARY KEY (`ClientId`), ADD KEY `CompanyName` (`CompanyName`);

--
-- Indexes for table `cms_logs`
--
ALTER TABLE `cms_logs`
 ADD PRIMARY KEY (`CmsLogId`);

--
-- Indexes for table `coupon`
--
ALTER TABLE `coupon`
 ADD PRIMARY KEY (`CouponId`), ADD KEY `Code_idx` (`Code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
 ADD PRIMARY KEY (`CustomerId`), ADD KEY `Username` (`Username`);

--
-- Indexes for table `customer_points`
--
ALTER TABLE `customer_points`
 ADD PRIMARY KEY (`PointId`), ADD KEY `ClientId` (`ClientId`);

--
-- Indexes for table `points`
--
ALTER TABLE `points`
 ADD PRIMARY KEY (`PointsId`);

--
-- Indexes for table `points_multiplier`
--
ALTER TABLE `points_multiplier`
 ADD PRIMARY KEY (`MultiplierId`);

--
-- Indexes for table `raffle`
--
ALTER TABLE `raffle`
 ADD PRIMARY KEY (`RaffleId`), ADD KEY `Source_idx` (`Source`);

--
-- Indexes for table `raffle_details`
--
ALTER TABLE `raffle_details`
 ADD PRIMARY KEY (`RaffleDetailId`), ADD KEY `UserId_idx` (`UserId`);

--
-- Indexes for table `redeemed_reward`
--
ALTER TABLE `redeemed_reward`
 ADD PRIMARY KEY (`RedeemedId`), ADD KEY `Source_idx` (`Source`);

--
-- Indexes for table `rewards_list`
--
ALTER TABLE `rewards_list`
 ADD PRIMARY KEY (`RewardId`), ADD KEY `Title_idx` (`Title`);

--
-- Indexes for table `reward_details`
--
ALTER TABLE `reward_details`
 ADD PRIMARY KEY (`RewardConfigId`), ADD KEY `Limitations_idx` (`Limitations`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`UserId`), ADD KEY `Username_idx` (`Username`);

--
-- Indexes for table `user_access`
--
ALTER TABLE `user_access`
 ADD PRIMARY KEY (`UserAccessId`), ADD KEY `ModuleId_idx` (`ModuleId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_logs`
--
ALTER TABLE `api_logs`
MODIFY `ApiLogId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
MODIFY `BrandId` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
MODIFY `CampaignId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `channels`
--
ALTER TABLE `channels`
MODIFY `ChannelId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
MODIFY `ClientId` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `cms_logs`
--
ALTER TABLE `cms_logs`
MODIFY `CmsLogId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `coupon`
--
ALTER TABLE `coupon`
MODIFY `CouponId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
MODIFY `CustomerId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_points`
--
ALTER TABLE `customer_points`
MODIFY `PointId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `points`
--
ALTER TABLE `points`
MODIFY `PointsId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `points_multiplier`
--
ALTER TABLE `points_multiplier`
MODIFY `MultiplierId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `raffle`
--
ALTER TABLE `raffle`
MODIFY `RaffleId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `raffle_details`
--
ALTER TABLE `raffle_details`
MODIFY `RaffleDetailId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `redeemed_reward`
--
ALTER TABLE `redeemed_reward`
MODIFY `RedeemedId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rewards_list`
--
ALTER TABLE `rewards_list`
MODIFY `RewardId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reward_details`
--
ALTER TABLE `reward_details`
MODIFY `RewardConfigId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `UserId` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_access`
--
ALTER TABLE `user_access`
MODIFY `UserAccessId` int(11) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
