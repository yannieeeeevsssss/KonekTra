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


INSERT INTO `users` (`id_user`, `email`, `password`, `profile_image`, `user_type`, `hash`, `active`, `approved_at`, `created_at`, `status`) VALUES
(1, 'admin@gmail.com', 'admin', '', 'admin', NULL, 1, NULL, '2024-10-04 12:51:14', 'Active'),
(15, 'yanniewritess1@gmail.com', 'MDdiNDMyZDI1MTcwYjQ2OWI1NzA5NWNhMjY5YmMyMDI=', '6702657b8737b.jpg', 'employer', '89b09427baee2237f1b2c1b7abfb7284', 1, NULL, '2024-10-06 18:24:59', 'Active'),
(20, '1mae2ann@gmail.com', 'ZGM3NzIxMjU3NGFjMTE0MWVlMTQ1N2Y3NGVmOWYyYmE=', '6703f66f92683.jpg', 'employer', '98a2a2ca113912ac3836ede3879fdec7', 1, NULL, '2024-10-07 22:55:43', 'Active');


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


INSERT INTO `employers` (`id_employer`, `id_user`, `firstname`, `middlename`, `lastname`, `gender`, `dob`, `age`, `street`, `id_city`, `id_province`, `contactno`, `email`, `company_name`, `registration_no`, `aboutme`) VALUES
(2, 15, 'Mae Ann Jean', 'Macabutas', 'Anol', 'Female', '2005-02-06', 19, 'L23B23 North Hill Arbours', 1, 1, '0926543302', 'yanniewritess1@gmail.com', 'KonekTra', '45656', 'dasdkawodpwd'),
(6, 20, 'Mae Ann Jean', 'Macabutas', 'Anol', 'Female', '2002-03-07', 22, 'L23B23 North Hill Arbours', 1, 1, '0926543302', '1mae2ann@gmail.com', 'KonekTra', '45656', 'fdsafgsrthtd');

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

CREATE TABLE `jobs` (
  `id_jobs` int(11) NOT NULL AUTO_INCREMENT,
  `id_employer` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `min_salary` decimal(10,2),
  `max_salary` decimal(10,2),
  `job_type` set('Task Based', 'Full-Time', 'On-Site', 'Remote') NOT NULL,
  `deadline` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jobs`),
  FOREIGN KEY (`id_employer`) REFERENCES `employers`(`id_employer`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `applications` (
  `id_application` int(11) NOT NULL AUTO_INCREMENT,
  `id_job` int(11) NOT NULL,
  `id_applicant` int(11) NOT NULL,
  `resume` varchar(255) NOT NULL,
  `cover_letter` text,
  `status` enum('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_application`),
  FOREIGN KEY (`id_job`) REFERENCES `jobs`(`id_jobs`) ON DELETE CASCADE,
  FOREIGN KEY (`id_applicant`) REFERENCES `applicants`(`id_applicant`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
