-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2017 at 03:29 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `jobportal`

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255),
  `user_type` enum('applicant', 'employer', 'admin') NOT NULL,
  `hash` varchar(255),
  `active` int(11) NOT NULL DEFAULT '0', 
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP, 
  `status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `users` (`email`, `password`, `profile_image`, `user_type`, `active`, `status`)
VALUES ('admin@gmail.com', 'admin', '', 'admin', 1, 'Active');

-- --------------------------------------------------------
-- Table structure for table `provinces`
CREATE TABLE `provinces` (
  `id_province` int(11) NOT NULL AUTO_INCREMENT,
  `province_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_province`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `provinces`
INSERT INTO `provinces` (`id_province`, `province_name`) VALUES
(1, 'Leyte');

-- --------------------------------------------------------
-- Table structure for table `cities`
CREATE TABLE `cities` (
  `id_city` int(11) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(255) NOT NULL,
  `id_province` int(11) NOT NULL,
  PRIMARY KEY (`id_city`),
  FOREIGN KEY (`id_province`) REFERENCES `provinces`(`id_province`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `cities`
INSERT INTO `cities` (`id_city`, `city_name`, `id_province`) VALUES
(1, 'Tacloban City', 1),
(2, 'Ormoc City', 1),
(3, 'Baybay City', 1),
(4, 'Palo', 1),
(5, 'Abuyog', 1),
(6, 'Carigara', 1),
(7, 'Tanauan', 1),
(8, 'Dulag', 1),
(9, 'Buraen', 1),
(10, 'Hindang', 1);

-- --------------------------------------------------------
-- Table structure for table `applicants`
CREATE TABLE `applicants` (
  `id_applicant` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255),
  `gender` varchar(10) NOT NULL,
  `age` int(11),
  `dob` date NOT NULL,
  `street` varchar(255),
  `id_city` int(11), -- Allow NULL values
  `id_province` int(11), -- Allow NULL values
  `contactno` varchar(15) NOT NULL,
  `aboutme` text,
  `preferred_job` varchar(255),
  `education` varchar(255),
  `resume` varchar(255),
  PRIMARY KEY (`id_applicant`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_city`) REFERENCES `cities`(`id_city`) ON DELETE SET NULL,
  FOREIGN KEY (`id_province`) REFERENCES `provinces`(`id_province`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

-- Table structure for table `employers`
CREATE TABLE `employers` (
  `id_employer` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL, 
  `middlename` varchar(255),  
  `lastname` varchar(255) NOT NULL, 
  `gender` varchar(10) NOT NULL, 
  `dob` date NOT NULL,
  `age` int(11),
  `street` varchar(255) NOT NULL,  
  `id_city` int(11),
  `id_province` int(11),
  `contactno` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company_name` varchar(255),
  `registration_no` varchar(255),
  `aboutme` text,
  PRIMARY KEY (`id_employer`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_city`) REFERENCES `cities`(`id_city`) ON DELETE SET NULL,
  FOREIGN KEY (`id_province`) REFERENCES `provinces`(`id_province`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `certificates` (
  `id_certificate` int(11) NOT NULL AUTO_INCREMENT,
  `certificate_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_certificate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `certificates` (`id_certificate`, `certificate_name`) VALUES
(1, 'EIM NCII'),
(2, 'CSS NCII');


CREATE TABLE `certifications` (
  `id_certification` int(11) NOT NULL AUTO_INCREMENT,
  `id_certificate` int(11) NOT NULL,  -- Foreign key to certificate_names
  `training_center` varchar(255),
  `certificate_no` varchar(255),
  `issuance_date` date,
  `expiration_date` date,
  `sector` varchar(255),
  `certificate_image` varchar(255),
  `id_applicant` int(11) NOT NULL,
  PRIMARY KEY (`id_certification`),
  FOREIGN KEY (`id_applicant`) REFERENCES `applicants`(`id_applicant`) ON DELETE CASCADE,
  FOREIGN KEY (`id_certificate`) REFERENCES `certificates`(`id_certificate`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
