-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2025 at 06:05 PM
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
-- Database: `mynsu`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(11) NOT NULL,
  `contact` bigint(20) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `registration_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `contact`, `level`, `registration_date`) VALUES
(39, 1234567890, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `aims`
--

CREATE TABLE `aims` (
  `user_id` int(11) NOT NULL,
  `credit` double(5,2) DEFAULT NULL,
  `cgpa` double(3,2) DEFAULT NULL,
  `grade` varchar(2) DEFAULT NULL,
  `waived` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `code` varchar(7) NOT NULL,
  `credit` double(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_requests`
--

CREATE TABLE `course_requests` (
  `user_id` int(11) NOT NULL,
  `code` varchar(7) NOT NULL,
  `credit` double(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drafts`
--

CREATE TABLE `drafts` (
  `user_id` int(11) NOT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `role` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrolled`
--

CREATE TABLE `enrolled` (
  `user_id` int(11) NOT NULL,
  `grade` varchar(2) NOT NULL,
  `code` varchar(7) NOT NULL,
  `initial` varchar(5) DEFAULT NULL,
  `experience` tinyint(4) DEFAULT NULL,
  `semester` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrolls`
--

CREATE TABLE `enrolls` (
  `user_id` int(11) NOT NULL,
  `grade` varchar(2) NOT NULL,
  `code` varchar(7) NOT NULL,
  `day` varchar(2) DEFAULT NULL,
  `timing` time DEFAULT NULL,
  `room` varchar(10) DEFAULT NULL,
  `section` tinyint(4) DEFAULT NULL,
  `initial` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE `info` (
  `user_id` int(11) NOT NULL,
  `password` varchar(60) NOT NULL,
  `security_variable` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`user_id`, `password`, `security_variable`) VALUES
(39, '$2y$10$KGcwZS.T8PP.TphrpKbEwOb8f4ZstctdLUBLs6S5t2NuBlAF.P2vO', '$2y$10$ADnzRzEgLAt94X.RZEBU9.NzkBqdJ48IQS914EyKEuQL0oC0N34/C');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `user_id` int(11) NOT NULL,
  `notice_id` smallint(9) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` varchar(500) NOT NULL,
  `notice_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_rate`
--

CREATE TABLE `platform_rate` (
  `user_id` int(11) NOT NULL,
  `user_interface` tinyint(4) NOT NULL,
  `user_functionalities` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retakes`
--

CREATE TABLE `retakes` (
  `user_id` int(11) NOT NULL,
  `semester` tinyint(4) NOT NULL,
  `code` varchar(7) NOT NULL,
  `grade` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `user_id` int(11) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `nsu_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`user_id`, `student_id`, `nsu_email`) VALUES
(39, 2222222222, 'temp.user@northsouth.edu');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `initial` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_requests`
--

CREATE TABLE `teacher_requests` (
  `user_id` int(11) NOT NULL,
  `initial` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `registration_date` date DEFAULT curdate(),
  `login_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `registration_date`, `login_date`) VALUES
(39, 'temp', 'user', 'temp.user@gmail.com', '0000-00-00', '2025-03-01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `contact` (`contact`);

--
-- Indexes for table `aims`
--
ALTER TABLE `aims`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `course_requests`
--
ALTER TABLE `course_requests`
  ADD PRIMARY KEY (`user_id`,`code`,`credit`);

--
-- Indexes for table `drafts`
--
ALTER TABLE `drafts`
  ADD PRIMARY KEY (`user_id`,`role`);

--
-- Indexes for table `enrolled`
--
ALTER TABLE `enrolled`
  ADD PRIMARY KEY (`user_id`,`code`),
  ADD KEY `enrolled_ibfk_2` (`code`),
  ADD KEY `enrolled_ibfk_3` (`initial`);

--
-- Indexes for table `enrolls`
--
ALTER TABLE `enrolls`
  ADD PRIMARY KEY (`user_id`,`code`),
  ADD KEY `enrolls_ibfk_2` (`code`),
  ADD KEY `enrolls_ibfk_3` (`initial`);

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`notice_id`),
  ADD UNIQUE KEY `notice_id` (`notice_id`),
  ADD KEY `notices_ibfk_1` (`user_id`);

--
-- Indexes for table `platform_rate`
--
ALTER TABLE `platform_rate`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `retakes`
--
ALTER TABLE `retakes`
  ADD PRIMARY KEY (`user_id`,`semester`,`code`),
  ADD KEY `retakes_ibfk_2` (`code`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`initial`);

--
-- Indexes for table `teacher_requests`
--
ALTER TABLE `teacher_requests`
  ADD PRIMARY KEY (`user_id`,`initial`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `notice_id` smallint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `aims`
--
ALTER TABLE `aims`
  ADD CONSTRAINT `aims_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_requests`
--
ALTER TABLE `course_requests`
  ADD CONSTRAINT `course_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `drafts`
--
ALTER TABLE `drafts`
  ADD CONSTRAINT `drafts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrolled`
--
ALTER TABLE `enrolled`
  ADD CONSTRAINT `enrolled_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrolled_ibfk_2` FOREIGN KEY (`code`) REFERENCES `courses` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrolled_ibfk_3` FOREIGN KEY (`initial`) REFERENCES `teachers` (`initial`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrolls`
--
ALTER TABLE `enrolls`
  ADD CONSTRAINT `enrolls_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrolls_ibfk_2` FOREIGN KEY (`code`) REFERENCES `courses` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrolls_ibfk_3` FOREIGN KEY (`initial`) REFERENCES `teachers` (`initial`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `info`
--
ALTER TABLE `info`
  ADD CONSTRAINT `info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admins` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `platform_rate`
--
ALTER TABLE `platform_rate`
  ADD CONSTRAINT `platform_rate_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `retakes`
--
ALTER TABLE `retakes`
  ADD CONSTRAINT `retakes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `retakes_ibfk_2` FOREIGN KEY (`code`) REFERENCES `courses` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_requests`
--
ALTER TABLE `teacher_requests`
  ADD CONSTRAINT `teacher_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `students` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
