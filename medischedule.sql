-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2025 at 11:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medischedule`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_bill` (IN `p_appointment_id` INT, IN `p_amount` DECIMAL(10,2), IN `p_status` VARCHAR(20), IN `p_issued_at` TIMESTAMP)   BEGIN
    -- Set default for p_status if not provided
    SET p_status = IFNULL(p_status, 'unpaid');

    -- Set default for p_issued_at if not provided
    SET p_issued_at = IFNULL(p_issued_at, CURRENT_TIMESTAMP);

    -- Insert the bill into the bills table
    INSERT INTO bills (appointment_id, amount, status, issued_at)
    VALUES (p_appointment_id, p_amount, p_status, p_issued_at);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_doctor` (IN `p_first_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_specialization` VARCHAR(100), IN `p_phone_number` VARCHAR(15), IN `p_address` TEXT, IN `p_experience_years` INT, IN `p_password` VARCHAR(255))   BEGIN
    INSERT INTO doctors (first_name, last_name, email, specialization, phone_number, address, experience_years, password)
    VALUES (p_first_name, p_last_name, p_email, p_specialization, p_phone_number, p_address, p_experience_years, p_password);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_doctor` (IN `p_doctor_id` INT)   BEGIN
    DELETE FROM doctors WHERE doctor_id = p_doctor_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mark_bill_as_paid` (IN `p_bill_id` INT)   BEGIN
    UPDATE bills
    SET status = 'paid'
    WHERE bill_id = p_bill_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', 'admin_123');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('upcoming','completed') DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `reason`, `appointment_time`, `created_at`, `status`) VALUES
(8, 1, 1, '2025-04-18 00:00:00', 'chest pain', '13:30:00', '2025-04-17 06:01:39', 'completed'),
(9, 1, 2, '2025-04-24 00:00:00', 'skin problem ', '15:41:00', '2025-04-17 06:07:22', ''),
(10, 7, 3, '2025-04-20 00:00:00', 'bone fracture ', '18:00:00', '2025-04-17 06:32:11', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`bill_id`, `appointment_id`, `amount`, `status`, `issued_at`) VALUES
(9, 8, 400.00, 'paid', '2025-04-17 02:57:32'),
(10, 10, 2500.00, 'paid', '2025-04-17 03:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `experience_years` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `first_name`, `last_name`, `email`, `password`, `specialization`, `phone_number`, `address`, `experience_years`) VALUES
(1, 'John', 'Doe', 'john.doe@example.com', 'pass123', 'Cardiology', '1234567890', '123 Heart St.', 12),
(2, 'Sarah', 'Smith', 'sarah.smith@example.com', '1234567', 'Neurology', '2345678901', '456 Brain Ave.', 10),
(3, 'Mike', 'Brown', 'mike.brown@example.com', 'pass123', 'Orthopedics', '3456789012', '789 Bone Rd.', 8),
(5, 'David', 'Johnson', 'david.johnson@example.com', 'pass123', 'Dermatology', '5678901234', '654 Skin Blvd.', 9),
(6, 'mamta', 'shah', 'mamtashah12nov@gmail.com', '123456', 'psychology', '4523235323', 'lalbaug,mumbai', 14);

-- --------------------------------------------------------

--
-- Table structure for table `health_records`
--

CREATE TABLE `health_records` (
  `record_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `prescription` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `health_records`
--

INSERT INTO `health_records` (`record_id`, `appointment_id`, `diagnosis`, `prescription`, `notes`, `recorded_at`) VALUES
(8, 8, 'Panic attacks ', 'BISOBIS', '10mg per day for a year at night', '2025-04-17 02:55:38'),
(9, 10, 'hair line fracture on skull', 'bed rest for 3 months ', 'operations not required ', '2025-04-17 03:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `first_name`, `last_name`, `email`, `password`, `dob`, `gender`, `phone_number`, `address`) VALUES
(1, 'Oshan', 'Shah', 'oshan.shah@somaiya.edu', '$2y$10$o8RrQ1IrHvm2ttIx569sG.LtXJCWZbrB3.Es2Y6GidVtQrZE9InAm', '2005-02-09', 'male', '8433842064', 'Mumbai-400012'),
(7, 'Purab', 'Madhan', 'purab.madhan@somaiya.edu', '$2y$10$y7lU0hNjoXduNRuUv0c1i.lxtIuHvLUchA3FXaiOCP97Ul1zUsVhu', '2005-03-25', 'male', '9920019635', 'juhu '),
(9, 'Purab', 'Madhan', 'purab.madhan@gmail.com', '$2y$10$F0elLjqZQRClesq90JBKEubHv6SE5e0LK25qOTBRGOQP5x31MOUKy', '2005-03-25', 'male', '9920019634', 'juhu '),
(10, 'Purab', 'Madhan', 'purab.madhan@somaiya.ed', '$2y$10$ezO/b1Tlefk31wv5edQ5/uCGqYuyNmwGe.E2TSQFXBjw7CBFglxGG', '2005-03-07', 'male', '9820193432', 'vile parle '),
(11, 'paars', 'k', 'paaras.k@hotgmail.com', '$2y$10$eV8n3J7tv95hjFIahg5tkOQrmqcFdi/dnTk1YFkhNelqSZ/RXOd8e', '2004-03-19', 'male', '777756293', 'thane ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD UNIQUE KEY `unique_doctor_schedule` (`appointment_date`,`doctor_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `health_records`
--
ALTER TABLE `health_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `health_records`
--
ALTER TABLE `health_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE;

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE;

--
-- Constraints for table `health_records`
--
ALTER TABLE `health_records`
  ADD CONSTRAINT `health_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
