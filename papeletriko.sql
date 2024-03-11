-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2023 at 12:42 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `papeletriko`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_activity`
--

CREATE TABLE `ci_activity` (
  `ID` int(11) NOT NULL,
  `ACTIVITY_TYPE` varchar(255) NOT NULL,
  `START_DTTM` datetime NOT NULL,
  `END_DTTM` datetime NOT NULL,
  `MODULE_HEADER` int(11) NOT NULL,
  `PROPERTY_1` mediumtext NOT NULL,
  `PROPERTY_2` mediumtext NOT NULL,
  `PROPERTY_3` mediumtext NOT NULL,
  `ACTIVE_SW` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_activity`
--

INSERT INTO `ci_activity` (`ID`, `ACTIVITY_TYPE`, `START_DTTM`, `END_DTTM`, `MODULE_HEADER`, `PROPERTY_1`, `PROPERTY_2`, `PROPERTY_3`, `ACTIVE_SW`) VALUES
(1, 'Essay', '2023-12-18 12:38:00', '2023-12-18 15:30:00', 14, 'Sample Essay Activity', 'Do this and that', 'Write a summary of the first Topic.**\nWrite a summary of the second Topic.**\nWrite a summary of the third Topic.**\nWrite a summary of the fourth Topic.**', 1),
(2, 'Fill in the Blank', '2023-12-18 15:05:00', '2023-12-18 17:00:00', 14, 'Sample Fill In the Blank Activity', 'Fill in the Blank', 'A _ brown _ jumps over _ lazy dog.', 1),
(3, 'Essay', '2023-12-18 18:25:00', '2023-12-18 18:30:00', 14, 'Sample Essay Activity', 'Sample Instruction', 'Magsulat ng buod sa ganto.**\nWrite a summary of the second Topic.**', 1),
(4, 'Fill in the Blank', '2023-12-18 18:32:00', '2023-12-18 18:33:00', 15, 'Sample Fill In the Blank Activity1', 'sample', 'Ako si _ ay nangangakong _.', 1),
(5, 'Essay', '2023-12-18 19:08:00', '2023-12-18 19:11:00', 14, 'cxzcxz', 'zxczxc', 'Write a summary of the first Topic.**\nWrite a summary of the second Topic.**', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ci_activity_submission`
--

CREATE TABLE `ci_activity_submission` (
  `ID` int(11) NOT NULL,
  `ACTIVITY_ID` int(11) NOT NULL,
  `SUBMISSION_DTTM` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `USER_ID` int(11) NOT NULL,
  `LATESUBMISSION_SW` tinyint(1) NOT NULL,
  `PROPERTY_1` mediumtext NOT NULL,
  `PROPERTY_2` mediumtext NOT NULL,
  `PROPERTY_3` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_activity_submission`
--

INSERT INTO `ci_activity_submission` (`ID`, `ACTIVITY_ID`, `SUBMISSION_DTTM`, `USER_ID`, `LATESUBMISSION_SW`, `PROPERTY_1`, `PROPERTY_2`, `PROPERTY_3`) VALUES
(1, 1, '2023-12-18 06:48:24', 5, 0, '|>Write a summary of the first Topic.*_Summary of the first Topic.\n|>Write a summary of the second Topic.*_Summary of the second Topic.\n|>Write a summary of the third Topic.*_Summary of the third Topic.\n|>Write a summary of the fourth Topic.*_Summary of the fourth Topic.', '', ''),
(2, 2, '2023-12-18 07:53:29', 5, 0, 'A <b>quick</b> brown <b>fox</b> jumps over <b>cxcsdf</b> lazy dog.', '', ''),
(3, 3, '2023-12-18 10:26:09', 5, 0, '|>Magsulat ng buod sa ganto.*_Ang buo ay ganto lamang kaikli\n|>Write a summary of the second Topic.*_Ang buo ay ganto lamang kaikli', '', ''),
(4, 4, '2023-12-18 10:33:00', 5, 0, 'Ako si <b>Mitch</b> ay nangangakong <b>pogi</b>.', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `ci_modules_headers`
--

CREATE TABLE `ci_modules_headers` (
  `ID` int(11) NOT NULL,
  `MODULE_TITLE` varchar(255) NOT NULL,
  `DESCRIPTION` varchar(255) NOT NULL,
  `LONG_DESCRIPTION` longtext NOT NULL,
  `SECTION` varchar(255) NOT NULL,
  `COVER_FILENAME` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_modules_headers`
--

INSERT INTO `ci_modules_headers` (`ID`, `MODULE_TITLE`, `DESCRIPTION`, `LONG_DESCRIPTION`, `SECTION`, `COVER_FILENAME`) VALUES
(14, 'Module Title 1', 'This is module 1.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ac fermentum nunc.', 'Agila', 'Screenshot 2023-04-10 153349.png'),
(15, 'Module Title 2', 'Welcome to module 2.', 'Integer euismod, odio in euismod malesuada, lectus magna sodales tellus.', 'Agila', 'Screenshot 2023-04-10 153626.png'),
(16, 'Module Title 3', 'Edited Some description', 'Long Description', 'Dragon', 'Screenshot 2023-05-08 185625.png');

-- --------------------------------------------------------

--
-- Table structure for table `ci_modules_topics`
--

CREATE TABLE `ci_modules_topics` (
  `ID` int(11) NOT NULL,
  `HEADER_ID` int(11) NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `TEXT_CONTENT` longtext NOT NULL,
  `IMG_CONTENT` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_modules_topics`
--

INSERT INTO `ci_modules_topics` (`ID`, `HEADER_ID`, `TITLE`, `TEXT_CONTENT`, `IMG_CONTENT`) VALUES
(7, 14, 'Sample Topic 1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Pharetra vel turpis nunc eget lorem dolor sed viverra. Nunc id cursus metus aliquam. Et magnis dis parturient montes nascetur ridiculus mus. Lobortis elementum nibh tellus molestie nunc non blandit massa. Nisi lacus sed viverra tellus in hac. Sed sed risus pretium quam vulputate dignissim suspendisse in est. Enim tortor at auctor urna nunc id cursus metus. Mauris sit amet massa vitae tortor condimentum lacinia. Ut enim blandit volutpat maecenas. Donec ultrices tincidunt arcu non sodales neque sodales ut. Nibh venenatis cras sed felis eget velit aliquet. Tortor at auctor urna nunc id cursus metus aliquam eleifend. Eget egestas purus viverra accumsan in. Arcu cursus euismod quis viverra nibh cras pulvinar mattis. Nisl nunc mi ipsum faucibus vitae aliquet nec ullamcorper. Eu tincidunt tortor aliquam nulla facilisi cras fermentum odio eu.\n[[Screenshot 2023-04-10 153832.png]]\nLaoreet id donec ultrices tincidunt arcu non sodales neque sodales. Mattis enim ut tellus elementum sagittis. Tellus molestie nunc non blandit massa enim nec dui. Mattis aliquam faucibus purus in massa tempor. Eu lobortis elementum nibh tellus molestie nunc non. Nunc sed blandit libero volutpat sed. Lobortis elementum nibh tellus molestie nunc non blandit. Vestibulum sed arcu non odio euismod lacinia. Sed libero enim sed faucibus turpis in eu mi bibendum. Auctor augue mauris augue neque gravida. Pellentesque habitant morbi tristique senectus. Sem integer vitae justo eget magna fermentum iaculis. Erat nam at lectus urna duis convallis convallis. Vel eros donec ac odio tempor orci dapibus ultrices in. Nisi est sit amet facilisis. Neque sodales ut etiam sit amet nisl. At in tellus integer feugiat. Nisi est sit amet facilisis magna etiam tempor orci. Massa enim nec dui nunc mattis enim ut tellus. This is a sample link [https://loremipsum.io/generator/]', 'Screenshot 2023-04-10 153832.png'),
(8, 14, 'Sample Topic 2', 'Sample content only', 'Screenshot 2023-04-10 220911.png'),
(9, 15, 'Sample Topic for Module 2', 'This is sample content for first topic in module 2. This is a sample image [[Screenshot 2023-05-06 153548.png]].\n\nThis is a sample link [https://colorhunt.co/palette/1d5d9b75c2f6f4d160fbeeac]\n\nThis is sample image link from google: [https://www.google.com/url?sa=i&url=https%3A%2F%2Ftl.wikipedia.org%2Fwiki%2FAklat&psig=AOvVaw22H1rDfLrtVGnIj0VPv-jr&ust=1702980978245000&source=images&cd=vfe&opi=89978449&ved=0CBIQjRxqFwoTCICZ1tXgmIMDFQAAAAAdAAAAABAD]', 'Screenshot 2023-05-06 153548.png');

-- --------------------------------------------------------

--
-- Table structure for table `ci_section`
--

CREATE TABLE `ci_section` (
  `ID` int(11) NOT NULL,
  `SECTION_NAME` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_section`
--

INSERT INTO `ci_section` (`ID`, `SECTION_NAME`) VALUES
(1, 'Agila'),
(2, 'Dragon');

-- --------------------------------------------------------

--
-- Table structure for table `ci_user`
--

CREATE TABLE `ci_user` (
  `ID` int(11) NOT NULL,
  `FIRST_NAME` varchar(255) NOT NULL,
  `MIDDLE_NAME` varchar(255) NOT NULL,
  `LAST_NAME` varchar(255) NOT NULL,
  `SECTION` varchar(255) NOT NULL,
  `USERNAME` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `USER_GROUP` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_user`
--

INSERT INTO `ci_user` (`ID`, `FIRST_NAME`, `MIDDLE_NAME`, `LAST_NAME`, `SECTION`, `USERNAME`, `PASSWORD`, `USER_GROUP`) VALUES
(2, 'Mitch', 'Almarinez', 'Serrano', 'Agila', 'admin', '$2y$10$NaS1IhAj7m.HLF3JwRz3fO/NivICdI71JtielBo6zy6o0GYKAAffu', 'Admin'),
(5, 'John', 'Philippines', 'Doe', 'Agila', 'student', '$2y$10$c2JUQNNco4BoQleyI8CbQedZyFEoaPpzwJgH6zbQF2zlvxE0Tfsi2', 'Regular'),
(6, 'Melanie', 'Brown', 'Gray', 'Dragon', 'GrayM', '$2y$10$tDvtGwU1OjcG3iErcbH2PuANoyK0p0wAVPbxBDO/r6wCGZr.UkJzW', 'Regular');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_activity`
--
ALTER TABLE `ci_activity`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ci_activity_submission`
--
ALTER TABLE `ci_activity_submission`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ci_modules_headers`
--
ALTER TABLE `ci_modules_headers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ci_modules_topics`
--
ALTER TABLE `ci_modules_topics`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ci_section`
--
ALTER TABLE `ci_section`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ci_user`
--
ALTER TABLE `ci_user`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ci_activity`
--
ALTER TABLE `ci_activity`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ci_activity_submission`
--
ALTER TABLE `ci_activity_submission`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ci_modules_headers`
--
ALTER TABLE `ci_modules_headers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ci_modules_topics`
--
ALTER TABLE `ci_modules_topics`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ci_section`
--
ALTER TABLE `ci_section`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ci_user`
--
ALTER TABLE `ci_user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
