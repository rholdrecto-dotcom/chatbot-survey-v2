-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2026 at 09:19 AM
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
-- Database: `harold_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bot_questions`
--

CREATE TABLE `bot_questions` (
  `id` int(11) NOT NULL,
  `step_name` varchar(50) NOT NULL,
  `question_text` text NOT NULL,
  `is_fixed` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 99
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bot_questions`
--

INSERT INTO `bot_questions` (`id`, `step_name`, `question_text`, `is_fixed`, `sort_order`) VALUES
(1, 'greeting', 'Hello! What is your name?', 0, 1),
(2, 'age', 'How  old areyou?', 1, 3),
(3, 'rating', 'Rate 1-5 how satisfied in your work.', 0, 99),
(4, 'comment', 'Do you have any recomendations or feddback?', 0, 99),
(5, 'Workspace & Environment', 'Is the lighting in your workspace sufficient for you to work comfortably?\"', 0, 99),
(6, 'Workspace & Environment', 'Are you satisfied with the current office temperature?', 0, 99),
(7, 'Workspace & Environment', 'Does the noise level in the office interfere with your ability to concentrate?', 0, 99),
(8, 'Workspace & Environment', 'How would you rate the cleanliness of the common areas and restrooms?', 0, 99),
(9, 'Tools & Productivity', 'Do you have the necessary hardware (PC, monitor, mouse) to perform your tasks efficiently?', 0, 99),
(10, 'Tools & Productivity', 'Is the office internet connection fast and reliable enough for your daily work?', 0, 99),
(11, 'Tools & Productivity', 'Are the software tools provided by the company easy to use and helpful for your job?', 0, 99);

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` varchar(50) DEFAULT NULL,
  `rating` varchar(10) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_responses`
--

INSERT INTO `survey_responses` (`id`, `name`, `age`, `rating`, `comment`, `date_submitted`) VALUES
(1, 'hi goodmorning', '99', '5', 'panget ng botnatin', '2026-04-22 06:30:30'),
(2, 'raihearth', '69', '5', 'how old are you?', '2026-04-22 06:41:03'),
(4, 'harold recto', '', '3', 'wala na', '2026-04-22 07:58:12'),
(6, 'harold', '', '5', 'no', '2026-04-24 05:04:18'),
(10, 'harold', '', '5', 'no', '2026-04-24 05:53:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bot_questions`
--
ALTER TABLE `bot_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bot_questions`
--
ALTER TABLE `bot_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
