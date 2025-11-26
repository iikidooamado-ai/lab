-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 04:31 PM
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
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `id` int(11) NOT NULL,
  `lab_name` varchar(100) NOT NULL,
  `color` varchar(20) DEFAULT '#00aaff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labs`
--

INSERT INTO `labs` (`id`, `lab_name`, `color`) VALUES
(15, 'Lab 1', '#00aaff'),
(16, 'Lab 2', '#00aaff'),
(17, 'Lab 3', '#00aaff'),
(18, 'Lab 4', '#00aaff'),
(19, 'Lab 5', '#00aaff');

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`id`, `user_id`, `name`) VALUES
(1, NULL, 'Prof. Juan Dela Cruz'),
(2, NULL, 'Prof. Maria Santos'),
(3, NULL, 'Prof. Jose Rizal'),
(4, NULL, 'Prof. Ana Cruz'),
(5, NULL, 'Prof. Jason Derulo'),
(6, NULL, 'Prof. Brad Pit');

-- --------------------------------------------------------

--
-- Table structure for table `professor_subjects`
--

CREATE TABLE `professor_subjects` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professor_subjects`
--

INSERT INTO `professor_subjects` (`id`, `professor_id`, `subject_id`, `created_at`) VALUES
(1, 1, 1, '2025-10-01 13:34:10'),
(2, 1, 2, '2025-10-01 13:34:10'),
(4, 3, 4, '2025-10-01 13:34:10'),
(5, 4, 1, '2025-10-01 13:34:10'),
(6, 4, 3, '2025-10-01 13:34:10'),
(7, 5, 5, '2025-11-03 11:48:28'),
(8, 6, 7, '2025-11-03 11:48:46');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `professor_subject_id` int(11) NOT NULL,
  `auto_shift` tinyint(1) NOT NULL DEFAULT 0,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `week` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `lab_id`, `section_id`, `professor_subject_id`, `auto_shift`, `day`, `start_time`, `end_time`, `created_at`, `week`) VALUES
(1, 15, 9, 1, 1, 'Monday', '07:30:00', '09:00:00', '2025-11-07 10:59:44', 1),
(2, 16, 10, 5, 1, 'Monday', '09:00:00', '10:30:00', '2025-11-07 10:59:44', 1),
(3, 17, 11, 2, 1, 'Monday', '10:30:00', '12:00:00', '2025-11-07 10:59:44', 1),
(4, 18, 12, 6, 1, 'Monday', '13:00:00', '14:30:00', '2025-11-07 10:59:44', 1),
(5, 19, 13, 4, 1, 'Monday', '14:30:00', '16:00:00', '2025-11-07 10:59:44', 1),
(6, 15, 9, 7, 1, 'Monday', '16:00:00', '17:30:00', '2025-11-07 10:59:44', 1),
(7, 16, 10, 8, 1, 'Tuesday', '07:30:00', '09:00:00', '2025-11-07 10:59:44', 1),
(8, 17, 11, 1, 1, 'Tuesday', '09:00:00', '10:30:00', '2025-11-07 10:59:44', 1),
(9, 18, 12, 5, 1, 'Tuesday', '10:30:00', '12:00:00', '2025-11-07 10:59:44', 1),
(10, 19, 13, 2, 1, 'Tuesday', '13:00:00', '14:30:00', '2025-11-07 10:59:44', 1),
(11, 15, 9, 6, 1, 'Tuesday', '14:30:00', '16:00:00', '2025-11-07 10:59:44', 1),
(12, 16, 10, 4, 1, 'Tuesday', '16:00:00', '17:30:00', '2025-11-07 10:59:44', 1),
(13, 17, 11, 7, 1, 'Wednesday', '07:30:00', '09:00:00', '2025-11-07 10:59:44', 1),
(14, 18, 12, 8, 1, 'Wednesday', '09:00:00', '10:30:00', '2025-11-07 10:59:44', 1),
(15, 19, 13, 1, 1, 'Wednesday', '10:30:00', '12:00:00', '2025-11-07 10:59:44', 1),
(16, 15, 9, 5, 1, 'Wednesday', '13:00:00', '14:30:00', '2025-11-07 10:59:44', 1),
(17, 16, 10, 2, 1, 'Wednesday', '14:30:00', '16:00:00', '2025-11-07 10:59:44', 1),
(18, 17, 11, 6, 1, 'Wednesday', '16:00:00', '17:30:00', '2025-11-07 10:59:44', 1),
(19, 18, 12, 4, 1, 'Thursday', '07:30:00', '09:00:00', '2025-11-07 10:59:44', 1),
(20, 19, 13, 7, 1, 'Thursday', '09:00:00', '10:30:00', '2025-11-07 10:59:44', 1),
(21, 15, 9, 8, 1, 'Thursday', '10:30:00', '12:00:00', '2025-11-07 10:59:44', 1),
(22, 16, 10, 1, 1, 'Thursday', '13:00:00', '14:30:00', '2025-11-07 10:59:44', 1),
(23, 17, 11, 5, 1, 'Thursday', '14:30:00', '16:00:00', '2025-11-07 10:59:44', 1),
(24, 18, 12, 2, 1, 'Thursday', '16:00:00', '17:30:00', '2025-11-07 10:59:44', 1),
(25, 19, 13, 6, 1, 'Friday', '07:30:00', '09:00:00', '2025-11-07 10:59:44', 1),
(26, 15, 9, 4, 1, 'Friday', '09:00:00', '10:30:00', '2025-11-07 10:59:44', 1),
(27, 16, 10, 7, 1, 'Friday', '10:30:00', '12:00:00', '2025-11-07 10:59:44', 1),
(28, 17, 11, 8, 1, 'Friday', '13:00:00', '14:30:00', '2025-11-07 10:59:44', 1),
(29, 18, 12, 1, 1, 'Friday', '14:30:00', '16:00:00', '2025-11-07 10:59:44', 1),
(30, 19, 13, 5, 1, 'Friday', '16:00:00', '17:30:00', '2025-11-07 10:59:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_name`) VALUES
(9, 'BSIT 1A'),
(10, 'BSIT 1B'),
(11, 'BSIT 2A'),
(12, 'BSIT 2B'),
(13, 'BSIT 1C');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `created_at`) VALUES
(1, 'CS101', 'Introduction to Programming', '2025-10-01 13:33:16'),
(2, 'CS102', 'Computer Organization', '2025-10-01 13:33:16'),
(3, 'CS201', 'Database Systems', '2025-10-01 13:33:16'),
(4, 'CS202', 'Data Structures and Algorithms', '2025-10-01 13:33:16'),
(5, 'CS301', 'Php Lavarell', '2025-11-03 11:47:40'),
(7, 'CS303', 'Vue and JS', '2025-11-03 11:48:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','faculty','student') NOT NULL DEFAULT 'faculty',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(3, 'Admin', '$2y$10$Aotqh1BQeS3WuAcLxxpFFu831.0Qd3vswtl2y1P/pB/h5wdwBJYre', 'admin', '2025-10-01 13:17:08'),
(4, 'Faculty', '$2y$10$DZul0/tJzulxvPV2Hy6ftO8IHB.a1OZ8c23syTwRNXNa.bu3OAw1y', 'faculty', '2025-10-01 15:56:58'),
(5, 'Muhamad', '$2y$10$do7m6GTMpb3F8hizZ9aFZOn2XhUsBluq8PbJv/Wvn0C8DyhewVbv6', 'admin', '2025-11-07 07:01:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_professors_user` (`user_id`);

--
-- Indexes for table `professor_subjects`
--
ALTER TABLE `professor_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_prof_subject` (`professor_id`,`subject_id`),
  ADD KEY `fk_ps_sub` (`subject_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sched_lab` (`lab_id`),
  ADD KEY `fk_sched_sec` (`section_id`),
  ADD KEY `fk_sched_profsub` (`professor_subject_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `professor_subjects`
--
ALTER TABLE `professor_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `professors`
--
ALTER TABLE `professors`
  ADD CONSTRAINT `fk_professors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `professor_subjects`
--
ALTER TABLE `professor_subjects`
  ADD CONSTRAINT `fk_ps_prof` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_sub` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_sched_lab` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sched_profsub` FOREIGN KEY (`professor_subject_id`) REFERENCES `professor_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sched_sec` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
