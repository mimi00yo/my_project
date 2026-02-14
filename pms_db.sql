-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 02:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `issue` text NOT NULL,
  `requested_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `scheduled_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `issue`, `requested_date`, `status`, `admin_message`, `created_at`, `scheduled_date`) VALUES
(2, 1, 'Covid Test', '2026-02-28', 'approved', 'Arrive at the morning 9:00am', '2026-02-04 19:24:37', '2026-02-28'),
(3, 3, 'Gyno checkup', '2026-03-04', 'approved', 'doctor available on 5th so one day extended !', '2026-02-04 19:40:14', '2026-03-05'),
(4, 5, 'Blood Test of uric acid', '2026-02-06', 'approved', 'we have doctors available on day after tomorrow not tomorrow , so get ready and come at sharp 9:00am !', '2026-02-05 08:43:16', '2026-02-07'),
(5, 5, 'Commoncold', '2026-02-09', 'approved', 'abcc', '2026-02-06 01:20:45', '2026-02-10'),
(6, 5, 'Headache', '2026-02-11', 'approved', 'abccc', '2026-02-06 01:24:30', '2026-02-08'),
(7, 5, 'Dressing', '2026-02-15', 'approved', 'gfcvnb', '2026-02-06 01:50:00', '2026-02-18'),
(8, 3, 'Tonsil', '2026-02-11', 'rejected', 'few days back recently not available', '2026-02-08 11:43:16', '2026-03-01'),
(9, 3, 'eye checkup', '2026-02-11', 'approved', 'next month', '2026-02-08 11:46:59', '2026-03-02'),
(10, 3, 'I have stomach ache', '2026-02-11', 'pending', NULL, '2026-02-08 12:19:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 'Your account has been approved. You can now login.', 1, '2026-02-04 16:54:22'),
(2, 3, 'Your appointment request was approved. ', 1, '2026-02-04 17:11:36'),
(3, 3, 'A new medical report was uploaded: Stone surgery report', 1, '2026-02-04 18:50:04'),
(4, 3, 'Password reset request received. Admin will reset your password soon.', 1, '2026-02-04 19:05:31'),
(5, 3, 'Your password has been reset by admin. Please login with your new password.', 1, '2026-02-04 19:10:44'),
(6, 1, 'Your account has been approved. You can now login.', 0, '2026-02-04 19:23:15'),
(7, 5, 'Your account has been approved. You can now login.', 1, '2026-02-05 08:41:42'),
(8, 5, 'A new medical report was uploaded: cvn c', 0, '2026-02-06 01:27:17'),
(9, 8, 'Your account has been approved. You can now login.', 0, '2026-02-08 09:10:39'),
(10, 9, 'Your account has been approved. You can now login.', 0, '2026-02-08 16:32:27'),
(11, 5, 'Password reset request received. Admin will reset your password soon.', 0, '2026-02-08 18:39:02'),
(12, 5, 'Your password has been reset by admin. Please login with your new password.', 0, '2026-02-08 19:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--

CREATE TABLE `password_reset_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('pending','done') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_requests`
--

INSERT INTO `password_reset_requests` (`id`, `user_id`, `reason`, `status`, `created_at`) VALUES
(1, 3, 'I forgot !!!', 'done', '2026-02-04 19:05:31'),
(2, 5, 'I forgot my pw', 'done', '2026-02-08 18:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` enum('admin','patient') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `patient_id`, `title`, `file_path`, `uploaded_by`, `uploaded_at`) VALUES
(1, 3, 'Stone surgery report', 'uploads/reports/report_3_1770231004.jpg', 'admin', '2026-02-04 18:50:04'),
(2, 5, 'cvn c', 'uploads/reports/report_5_1770341237.jpg', 'admin', '2026-02-06 01:27:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','patient') NOT NULL DEFAULT 'patient',
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `role`, `status`, `created_at`) VALUES
(1, 'Ram Moktan', 'ram100@gmail.com', '9841065875', '$2y$10$k0xMVKF8uOahdD196wYCUO/A7Q.Pie1j.cw974GXS6mhQkCE12wf.', 'patient', 'approved', '2026-02-04 15:40:20'),
(3, 'Kumari Bomjan', 'kumari200@gmail.com', '', '$2y$10$52D6OIOdMu8B1xR9vXHOTOy193D/OSthsHE6k0KmQRUQQ1oQRORJi', 'patient', 'approved', '2026-02-04 16:15:08'),
(4, 'Admin', 'admin@gmail.com', '', '$2y$10$kzJYrmYvM5860KTc/it1iOORX0adxF0nztvLiKPd3kIo6f8bCL6lC', 'admin', 'approved', '2026-02-04 16:45:37'),
(5, 'Rashmi Tamang', 'rashmitamang070@gmail.com', '', '$2y$10$6gJ8JXg5Tl2h7gPspTLPMefzoMlFbcsWvCeB/TglxrGqgKCrHgUPi', 'patient', 'approved', '2026-02-05 08:41:17'),
(6, 'Alice Tamang', 'hisashiburi3202@gmail.com', '', '$2y$10$vuys.nGojgqYs2QgpaICC.V/YrmFfeWKt5bDPJsOoHW/CFFS.ooeW', 'patient', 'pending', '2026-02-08 08:23:10'),
(8, 'Totoro  Tamang', 'totoro@gmail.com', '', '$2y$10$XwAnUw37C3XJBOKllgpZF.0Z.ca/Ar7hwibv0W8NpbHlz2/0an1Pe', 'patient', 'approved', '2026-02-08 08:24:45'),
(9, 'Ponyo Tamang', 'ponyo07@gmail.com', '', '$2y$10$rzn0zell0py04ZLioiZB9OzwVDsUqK2rlPfuIDKnYYKPtrbTbkNDy', 'patient', 'approved', '2026-02-08 16:31:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD CONSTRAINT `password_reset_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
