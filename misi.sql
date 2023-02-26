-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2022 at 06:55 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `misi`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_monitoring`
--

CREATE TABLE `admin_monitoring` (
  `admin_monitoring_id` int(11) NOT NULL,
  `admin_id` int(20) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `page_details` text NOT NULL,
  `admin_activity` text NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `admin_tbl`
--

CREATE TABLE `admin_tbl` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(150) NOT NULL,
  `admin_email` varchar(150) NOT NULL,
  `admin_password` varchar(20) NOT NULL,
  `admin_phone` varchar(11) NOT NULL,
  `admin_role_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `authority` varchar(255) DEFAULT NULL,
  `create_by` varchar(150) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` varchar(150) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `status` enum('1','0') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `authorization_setup`
--

CREATE TABLE `authorization_setup` (
  `authorization_id` int(11) NOT NULL,
  `admin_user_role_id` int(11) NOT NULL,
  `authorizer_count` int(11) NOT NULL,
  `status` enum('A','I','D') NOT NULL,
  `create_by` varchar(50) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `authorization_user_assign`
--

CREATE TABLE `authorization_user_assign` (
  `authorization_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `status` enum('A','I','D') NOT NULL,
  `create_by` varchar(50) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blood_group`
--

CREATE TABLE `blood_group` (
  `id` int(11) NOT NULL,
  `blood_group_name` varchar(100) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `department_user`
--

CREATE TABLE `department_user` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `sms_status` enum('A','I','C','D','P') NOT NULL,
  `email_status` enum('A','I','C','D','P') NOT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `occupation`
--

CREATE TABLE `occupation` (
  `id` int(11) NOT NULL,
  `occupation_name` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `patient_file_upload`
--

CREATE TABLE `patient_file_upload` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_location` varchar(255) NOT NULL,
  `file_type` enum('NID','Driving','Others') NOT NULL,
  `file_remarks` text DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `status` enum('A','I','P','D','C') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `patient_info`
--

CREATE TABLE `patient_info` (
  `id` int(11) NOT NULL,
  `patient_first_name` varchar(255) NOT NULL,
  `patient_last_name` varchar(255) NOT NULL,
  `patient_picture_name` varchar(255) NOT NULL,
  `patient_picture_location` varchar(255) NOT NULL,
  `patient_email` varchar(200) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `patient_alternet_phone` varchar(20) NOT NULL,
  `patient_address` text NOT NULL,
  `patient_area` varchar(255) NOT NULL,
  `patient_city` varchar(255) NOT NULL,
  `patient_country` varchar(255) NOT NULL,
  `bsn_number` varchar(255) NOT NULL,
  `dob_number` varchar(255) NOT NULL,
  `insurance_number` varchar(255) NOT NULL,
  `emergency_contact` varchar(255) NOT NULL,
  `age` varchar(255) NOT NULL,
  `sex` enum('Male','Female','Others') NOT NULL,
  `marital_status` enum('Single','Married','Divorced') NOT NULL,
  `medical_history` text NOT NULL,
  `date_of_birth` varchar(255) NOT NULL,
  `blood_group` enum('A+','B+','O+','AB+','A-','B-','O-','AB-') NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `admin_remarks` text NOT NULL,
  `patient_password` varchar(255) NOT NULL,
  `status` enum('A','I','P','D','C') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_category`
--

CREATE TABLE `service_category` (
  `id` int(11) NOT NULL,
  `service_category_name` varchar(255) NOT NULL,
  `status` enum('A','I','D','P','C') NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_subcategory`
--

CREATE TABLE `service_subcategory` (
  `id` int(11) NOT NULL,
  `service_subcategory_name` varchar(255) NOT NULL,
  `status` enum('A','I','D','P','C') NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `service_category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `state_city`
--

CREATE TABLE `state_city` (
  `id` int(11) NOT NULL,
  `state_city_name` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `therapist_degree`
--

CREATE TABLE `therapist_degree` (
  `id` int(11) NOT NULL,
  `degree_name` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `therapist_file_upload`
--

CREATE TABLE `therapist_file_upload` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_location` varchar(255) NOT NULL,
  `file_type` enum('Driving','NID','Passport','Others') NOT NULL,
  `therapist_id` int(100) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `therapist_info`
--

CREATE TABLE `therapist_info` (
  `id` int(11) NOT NULL,
  `therapist_first_name` varchar(255) NOT NULL,
  `therapist_last_name` varchar(255) NOT NULL,
  `therapist_email` varchar(255) NOT NULL,
  `therapist_phone` varchar(20) NOT NULL,
  `residential_address` varchar(255) NOT NULL,
  `language_preference` enum('English','Dutch') NOT NULL,
  `bsn_number` varchar(100) NOT NULL,
  `dob_number` varchar(100) NOT NULL,
  `insurance_number` varchar(100) NOT NULL,
  `emergency_contact` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Others') NOT NULL,
  `date_of_birth` varchar(50) NOT NULL,
  `therapist_type_id` int(10) NOT NULL,
  `blood_group_id` int(10) NOT NULL,
  `state_city_id` int(10) NOT NULL,
  `country_id` int(10) NOT NULL,
  `therapist_degree_id` int(10) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `therapist_service`
--

CREATE TABLE `therapist_service` (
  `id` int(11) NOT NULL,
  `therapist_service_name` varchar(255) NOT NULL,
  `status` enum('A','I','D','P','C') NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `service_category_id` int(11) NOT NULL,
  `service_subcategory_id` int(11) DEFAULT NULL,
  `therapist_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `therapist_type`
--

CREATE TABLE `therapist_type` (
  `id` int(11) NOT NULL,
  `therapist_type_name` varchar(255) NOT NULL,
  `remarks` int(11) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_department`
--

CREATE TABLE `ticket_department` (
  `id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('A','I','C','D','P') NOT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `user_role_name` varchar(200) NOT NULL,
  `status` enum('A','I','D','P','C') NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `create_by` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_monitoring`
--
ALTER TABLE `admin_monitoring`
  ADD PRIMARY KEY (`admin_monitoring_id`);

--
-- Indexes for table `admin_tbl`
--
ALTER TABLE `admin_tbl`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `authorization_setup`
--
ALTER TABLE `authorization_setup`
  ADD PRIMARY KEY (`authorization_id`);

--
-- Indexes for table `authorization_user_assign`
--
ALTER TABLE `authorization_user_assign`
  ADD PRIMARY KEY (`authorization_id`);

--
-- Indexes for table `blood_group`
--
ALTER TABLE `blood_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_user`
--
ALTER TABLE `department_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `occupation`
--
ALTER TABLE `occupation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_file_upload`
--
ALTER TABLE `patient_file_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_info`
--
ALTER TABLE `patient_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_category`
--
ALTER TABLE `service_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_subcategory`
--
ALTER TABLE `service_subcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `state_city`
--
ALTER TABLE `state_city`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapist_degree`
--
ALTER TABLE `therapist_degree`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapist_file_upload`
--
ALTER TABLE `therapist_file_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapist_info`
--
ALTER TABLE `therapist_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapist_service`
--
ALTER TABLE `therapist_service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapist_type`
--
ALTER TABLE `therapist_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_department`
--
ALTER TABLE `ticket_department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_monitoring`
--
ALTER TABLE `admin_monitoring`
  MODIFY `admin_monitoring_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9784;

--
-- AUTO_INCREMENT for table `admin_tbl`
--
ALTER TABLE `admin_tbl`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `authorization_setup`
--
ALTER TABLE `authorization_setup`
  MODIFY `authorization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `authorization_user_assign`
--
ALTER TABLE `authorization_user_assign`
  MODIFY `authorization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `blood_group`
--
ALTER TABLE `blood_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_user`
--
ALTER TABLE `department_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `occupation`
--
ALTER TABLE `occupation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_file_upload`
--
ALTER TABLE `patient_file_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_info`
--
ALTER TABLE `patient_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_category`
--
ALTER TABLE `service_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_subcategory`
--
ALTER TABLE `service_subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `state_city`
--
ALTER TABLE `state_city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `therapist_degree`
--
ALTER TABLE `therapist_degree`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `therapist_file_upload`
--
ALTER TABLE `therapist_file_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `therapist_info`
--
ALTER TABLE `therapist_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `therapist_service`
--
ALTER TABLE `therapist_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `therapist_type`
--
ALTER TABLE `therapist_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_department`
--
ALTER TABLE `ticket_department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
