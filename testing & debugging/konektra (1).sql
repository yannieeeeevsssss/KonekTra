-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2024 at 08:17 PM
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
-- Database: `konektra`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id_applicant` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `gender` varchar(10) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `dob` date NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `id_city` int(11) DEFAULT NULL,
  `id_province` int(11) DEFAULT NULL,
  `contactno` varchar(15) NOT NULL,
  `aboutme` text DEFAULT NULL,
  `preferred_job` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id_applicant`, `id_user`, `firstname`, `lastname`, `middlename`, `gender`, `age`, `dob`, `street`, `id_city`, `id_province`, `contactno`, `aboutme`, `preferred_job`, `education`, `resume`) VALUES
(1, 21, 'Mae Ann Jean', 'Anol', 'Macabutas', 'Female', 24, '2000-08-20', 'L23B23 North Hill Arbours', 1, 1, '0926543302', 'Hi, Im Mae Ann and has a lot of exp', '[{\"value\":\"IT\"},{\"value\":\"Electrician\"}]', 'College', '6713ddd7146ff.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id_application` int(11) NOT NULL,
  `id_jobs` int(11) NOT NULL,
  `id_applicant` int(11) NOT NULL,
  `resume` varchar(255) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('Pending','Hired','Rejected') NOT NULL DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id_application`, `id_jobs`, `id_applicant`, `resume`, `cover_letter`, `status`, `applied_at`) VALUES
(2, 3, 1, 'SCHOLARSHIP.pdf', 'Hi, I\'m Mae Ann and I\'m interested on the job you\'re offering.', 'Hired', '2024-10-26 09:46:22'),
(3, 4, 1, 'SCHOLARSHIP.pdf', 'Hi I\'m a professional electrician so please choose me', 'Rejected', '2024-10-26 12:48:24'),
(4, 5, 1, 'SCHOLARSHIP.pdf', 'I\'m a good plumber so choose me.', 'Pending', '2024-10-26 16:37:03');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id_certificate` int(11) NOT NULL,
  `certificate_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id_certificate`, `certificate_name`) VALUES
(1, 'EIM NCII'),
(2, 'CSS NCII');

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `id_certification` int(11) NOT NULL,
  `id_certificate` int(11) NOT NULL,
  `training_center` varchar(255) DEFAULT NULL,
  `certificate_no` varchar(255) DEFAULT NULL,
  `issuance_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `sector` varchar(255) DEFAULT NULL,
  `certificate_image` varchar(255) DEFAULT NULL,
  `id_applicant` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `certifications`
--

INSERT INTO `certifications` (`id_certification`, `id_certificate`, `training_center`, `certificate_no`, `issuance_date`, `expiration_date`, `sector`, `certificate_image`, `id_applicant`) VALUES
(1, 1, 'Asian Development Foundation College', '8240932', '2024-10-14', '2024-10-22', NULL, 'uploads/certificate/6713ddd71b455.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id_city` int(11) NOT NULL,
  `city_name` varchar(255) NOT NULL,
  `id_province` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cities`
--

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

--
-- Table structure for table `employers`
--

CREATE TABLE `employers` (
  `id_employer` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` date NOT NULL,
  `age` int(11) DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `id_city` int(11) DEFAULT NULL,
  `id_province` int(11) DEFAULT NULL,
  `contactno` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `aboutme` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employers`
--

INSERT INTO `employers` (`id_employer`, `id_user`, `firstname`, `middlename`, `lastname`, `gender`, `dob`, `age`, `street`, `id_city`, `id_province`, `contactno`, `email`, `company_name`, `registration_no`, `aboutme`) VALUES
(7, 22, 'Mae Ann Jean', 'Maca', 'Anol', 'Female', '2000-01-21', 24, 'North Hill Arbours', 1, 1, '09265433027', 'yanniewritess1@gmail.com', 'KonekTra', '1234567', 'This is a sample company');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id_jobs` int(11) NOT NULL,
  `id_employer` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `min_salary` decimal(10,2) DEFAULT NULL,
  `max_salary` decimal(10,2) DEFAULT NULL,
  `job_type` set('Task Based','Full-Time','On-Site','Remote') NOT NULL,
  `deadline` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Open','Closed','Filled') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id_jobs`, `id_employer`, `job_title`, `location`, `description`, `min_salary`, `max_salary`, `job_type`, `deadline`, `created_at`, `status`) VALUES
(3, 7, 'Front Desk Officer', 'P. Burgos St. Tacloban City', 'Sample Description', 40000.00, 50008.00, 'Full-Time,On-Site', '2024-10-25', '2024-10-22 15:58:35', 'Open'),
(4, 7, 'Electrician', 'P. Burgos St. Tacloban City', 'This is a sample description for the electrician', 20000.00, 30000.00, 'Task Based,On-Site', '2024-10-31', '2024-10-26 12:47:45', 'Open'),
(5, 7, 'Plumber', 'Lopez Jaena St. Tacloban City', 'This is another sample description for the job plumber', 5000.00, 6000.00, 'Task Based,On-Site', '2024-10-31', '2024-10-26 16:35:55', 'Open');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id_province` int(11) NOT NULL,
  `province_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id_province`, `province_name`) VALUES
(1, 'Leyte');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `user_type` enum('applicant','employer','admin') NOT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `email`, `password`, `profile_image`, `user_type`, `hash`, `active`, `approved_at`, `created_at`, `status`) VALUES
(1, 'admin@gmail.com', 'admin', '', 'admin', NULL, 1, NULL, '2024-10-04 12:51:14', 'Active'),
(21, '1mae2ann@gmail.com', 'ZGM3NzIxMjU3NGFjMTE0MWVlMTQ1N2Y3NGVmOWYyYmE=', '6713ddd715215.jpg', 'applicant', '3e6360a647a39fc97782d2abda5b4a55', 1, NULL, '2024-10-20 00:27:03', 'Active'),
(22, 'yanniewritess1@gmail.com', 'ZGM3NzIxMjU3NGFjMTE0MWVlMTQ1N2Y3NGVmOWYyYmE=', '67152e2f3c40e.png', 'employer', '31a1569dbdebbe3e1d37ad2ed703a6e1', 1, NULL, '2024-10-21 00:22:07', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id_applicant`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_city` (`id_city`),
  ADD KEY `id_province` (`id_province`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id_application`),
  ADD KEY `id_job` (`id_jobs`),
  ADD KEY `id_applicant` (`id_applicant`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id_certificate`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id_certification`),
  ADD KEY `id_applicant` (`id_applicant`),
  ADD KEY `id_certificate` (`id_certificate`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id_city`),
  ADD KEY `id_province` (`id_province`);

--
-- Indexes for table `employers`
--
ALTER TABLE `employers`
  ADD PRIMARY KEY (`id_employer`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_city` (`id_city`),
  ADD KEY `id_province` (`id_province`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id_jobs`),
  ADD KEY `id_employer` (`id_employer`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id_province`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id_applicant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id_application` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id_certificate` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id_certification` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id_city` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employers`
--
ALTER TABLE `employers`
  MODIFY `id_employer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id_jobs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id_province` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `applicants_ibfk_2` FOREIGN KEY (`id_city`) REFERENCES `cities` (`id_city`) ON DELETE SET NULL,
  ADD CONSTRAINT `applicants_ibfk_3` FOREIGN KEY (`id_province`) REFERENCES `provinces` (`id_province`) ON DELETE SET NULL;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`id_jobs`) REFERENCES `jobs` (`id_jobs`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`id_applicant`) REFERENCES `applicants` (`id_applicant`) ON DELETE CASCADE;

--
-- Constraints for table `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`id_applicant`) REFERENCES `applicants` (`id_applicant`) ON DELETE CASCADE,
  ADD CONSTRAINT `certifications_ibfk_2` FOREIGN KEY (`id_certificate`) REFERENCES `certificates` (`id_certificate`) ON DELETE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`id_province`) REFERENCES `provinces` (`id_province`) ON DELETE CASCADE;

--
-- Constraints for table `employers`
--
ALTER TABLE `employers`
  ADD CONSTRAINT `employers_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `employers_ibfk_2` FOREIGN KEY (`id_city`) REFERENCES `cities` (`id_city`) ON DELETE SET NULL,
  ADD CONSTRAINT `employers_ibfk_3` FOREIGN KEY (`id_province`) REFERENCES `provinces` (`id_province`) ON DELETE SET NULL;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`id_employer`) REFERENCES `employers` (`id_employer`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
