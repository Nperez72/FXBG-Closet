-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
<<<<<<< Updated upstream
-- Host: localhost:3306
-- Generation Time: Nov 17, 2025 at 05:48 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26
=======
-- Host: localhost
-- Generation Time: Dec 01, 2025 at 04:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
>>>>>>> Stashed changes

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pridedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbaccounts`
--

CREATE TABLE `dbaccounts` (
<<<<<<< Updated upstream
  `username` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` text COLLATE utf8mb4_general_ci NOT NULL,
  `type` int NOT NULL
) ;
=======
  `username` varchar(256) NOT NULL,
  `email` varchar(256) DEFAULT NULL,
  `password` text NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
>>>>>>> Stashed changes

--
-- Dumping data for table `dbaccounts`
--

INSERT INTO `dbaccounts` (`username`, `email`, `password`, `type`) VALUES
('admin', 'admin@example.com', '$2y$10$3VG4XS1am7XIB4hlCnmOSut3dQFELyo.e0DCge8sQ0yqD932DNLyq', 2),
('coordinator', NULL, '$2y$10$5M4vOQnvVzmBPFvBV7y6XePx/62JYtktoZtNKruDdBRFA5PvtDPrS', 1),
('vmsroot', 'rootadmin@example.com', '$2y$10$azSckAKgmDvY5stYh5iXtuOpADqsCHZX.3q9QW8uZHVXk.9BSYO3W', 2),
('volunteer', NULL, '$2y$10$uRS4b4Ye/HxLip.9WUncnuu3a9qqxc8lkD2uL6vpl9epjwgkdC8AG', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dbarchived_volunteers`
--

CREATE TABLE `dbarchived_volunteers` (
  `id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` text,
  `first_name` text NOT NULL,
  `last_name` text,
  `street_address` text,
  `city` text,
  `state` text,
  `zip_code` text,
  `phone1` varchar(12) NOT NULL,
  `phone1type` text,
  `emergency_contact_phone` varchar(12) DEFAULT NULL,
  `emergency_contact_phone_type` text,
  `birthday` text,
  `email` text,
  `emergency_contact_first_name` text NOT NULL,
  `contact_num` varchar(12) NOT NULL,
  `emergency_contact_relation` text NOT NULL,
  `contact_method` text,
  `type` text,
  `status` text,
  `notes` text,
  `password` text,
  `skills` text NOT NULL,
  `interests` text NOT NULL,
  `archived_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `emergency_contact_last_name` text NOT NULL,
  `is_new_volunteer` tinyint(1) NOT NULL DEFAULT '1',
  `is_community_service_volunteer` tinyint(1) NOT NULL DEFAULT '0',
  `total_hours_volunteered` decimal(5,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dbarchived_volunteers`
--

INSERT INTO `dbarchived_volunteers` (`id`, `start_date`, `first_name`, `last_name`, `street_address`, `city`, `state`, `zip_code`, `phone1`, `phone1type`, `emergency_contact_phone`, `emergency_contact_phone_type`, `birthday`, `email`, `emergency_contact_first_name`, `contact_num`, `emergency_contact_relation`, `contact_method`, `type`, `status`, `notes`, `password`, `skills`, `interests`, `archived_date`, `emergency_contact_last_name`, `is_new_volunteer`, `is_community_service_volunteer`, `total_hours_volunteered`) VALUES
('stephen_davies', '2022-05-10', 'Stephen', 'Davies', '456 Maple Avenue', 'Fredericksburg', 'VA', '22401', '5405557890', 'mobile', '5405551111', 'home', '1988-11-02', 'stephendavies@email.com', 'Robert', '5405551111', 'Father', 'phone', 'volunteer', 'Inactive', 'Archived due to relocation', '$2y$10$ABC789xyz456LMN123DEF', 'Music, Painting', 'Event Coordination', '2025-03-18 16:56:44', 'Davies', 0, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `dbclosetinventory`
--

CREATE TABLE `dbclosetinventory` (
  `item_name` text NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbclosetuse`
--

CREATE TABLE `dbclosetuse` (
  `user_first_name` text NOT NULL,
  `user_last_name` text NOT NULL,
  `use_date` date NOT NULL,
  `item_type` text NOT NULL,
  `quantity_taken` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbdiscussions`
--

CREATE TABLE `dbdiscussions` (
  `author_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbdiscussions`
--

INSERT INTO `dbdiscussions` (`author_id`, `title`, `body`, `time`) VALUES
('vmsroot', 'test', 'this is test', '2025-04-30-10:13');

-- --------------------------------------------------------

--
-- Table structure for table `dbeventmedia`
--

CREATE TABLE `dbeventmedia` (
  `media_id` int NOT NULL,
  `activity_id` int NOT NULL,
  `file_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_format` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbeventpersons`
--

CREATE TABLE `dbeventpersons` (
  `eventID` int NOT NULL,
  `userID` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbeventpersons`
--

INSERT INTO `dbeventpersons` (`eventID`, `userID`, `position`, `notes`) VALUES
(64, 'vmsroot', 'v', 'Skills:  | Dietary restrictions:  | Disabilities:  | Materials: '),
(100, 'john_doe', 'v', 'Skills:  | Dietary restrictions:  | Disabilities:  | Materials: '),
(64, 'vmsroot', 'v', 'Skills:  | Dietary restrictions:  | Disabilities:  | Materials: '),
(100, 'john_doe', 'v', 'Skills:  | Dietary restrictions:  | Disabilities:  | Materials: ');

-- --------------------------------------------------------

--
-- Table structure for table `dbevents`
--

CREATE TABLE `dbevents` (
  `id` int NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `startTime` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endTime` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `completed` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `restricted_signup` tinyint(1) NOT NULL,
<<<<<<< Updated upstream
  `location` text COLLATE utf8mb4_unicode_ci,
  `type` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `volunteer_coordinator` int DEFAULT NULL
=======
  `location` text DEFAULT NULL,
  `type` text NOT NULL,
  `volunteer_coordinator` int(11) DEFAULT NULL
>>>>>>> Stashed changes
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbevents`
--

INSERT INTO `dbevents` (`id`, `name`, `date`, `startTime`, `endTime`, `description`, `capacity`, `completed`, `restricted_signup`, `location`, `type`, `volunteer_coordinator`) VALUES
(112, 'DOGGIE WALKIES', '2025-04-30', '13:00', '15:00', 'walking the doggies in the woods', 20, 'yes', 0, 'Miami, USA', 'blah', NULL),
(117, 'Color Test', '2025-11-11', '13:00', '14:00', 'Testing the colors in the calendar', 12, 'yes', 0, '', 'Test', 16),
(118, 'Halloween Event', '2025-10-31', '18:00', '20:30', 'It is halloween!!', 50, 'no', 0, 'Fredericksburg, VA', 'Holiday', NULL),
(119, 'party :)', '2026-01-14', '01:00', '01:01', 'dancin', 1, 'no', 0, 'my house', 'party :)', NULL),
(120, 'SDLFjkafs', '2025-09-10', '12:00', '14:00', 'j;aksdfj', 99999, 'no', 0, 'asdf;j', 'sadj', NULL),
(121, 'Whikey Valor Tasting', '2025-09-24', '15:00', '18:00', 'Come have a taste of fine barrel aged whiskey with fellow Vets.', 25, 'no', 0, 'Old Silk Mill', 'Tasting', NULL),
(122, 'Event', '2025-12-01', '13:00', '14:00', 'Use Case Event', 77, 'no', 0, 'UMW', 'Group', NULL),
(123, 'Ethan&#039;s Birthday Party', '2025-10-03', '07:30', '19:30', 'Ethan is going to eat my cake.', 2147483647, 'no', 0, 'Eagle 225', 'Party', NULL),
(124, 'Example event', '2025-09-11', '12:00', '14:00', 'This is a test event', 42, 'no', 0, 'UMW', 'A test', NULL),
(125, 'Pet Adoption', '2025-09-13', '11:00', '17:00', 'Pet Adoption', 50, 'no', 0, 'Fredericksburg, Virginia', 'Pet Adoption', NULL),
(126, 'Squirrel Watching', '2025-09-22', '06:00', '09:00', 'Watch the squirrels to make sure they do not eat the bird seed', 6, 'no', 0, '275 Butler Rd, Fredericksburg, VA 22405', 'Squirrel', NULL),
(127, 'Whoosky Volar Tasting', '2025-09-15', '09:00', '13:00', 'Test Event', 42, 'no', 0, 'House', 'Get-Together', NULL),
(128, 'Event', '2025-12-01', '13:30', '14:00', 'Use Case Event', 77, 'no', 0, 'UMW', 'Person', NULL),
(129, 'Test event Woak', '2025-10-31', '15:00', '18:00', 'testing thsi woa', 99, 'no', 0, 'required but not listed', 'not listed as req', NULL),
(130, 'Class Example', '2025-09-24', '12:00', '14:00', 'This is an example', 10, 'no', 0, 'Farmer', 'Shit storm', NULL),
(131, 'FXBG Tester', '2025-11-11', '18:00', '20:30', 'First test event!!!!', 100, 'yes', 0, 'UMW Campus', 'Test', NULL),
(132, 'FXBG Fix', '2025-11-25', '06:00', '07:00', 'Test Desc', 999, 'yes', 0, '', 'Random Stuff', NULL),
(133, 'Test', '2025-11-10', '18:00', '20:30', 'a', 999, 'no', 0, '', 'a', NULL),
(134, 'New Test', '2025-11-12', '06:00', '07:00', 'AAA', 999, 'no', 0, '', 'AAA', 18),
(135, 'Next Test', '2025-11-14', '18:00', '20:30', 'AAAA', 999, 'no', 0, 'UMW Campus', 'AAA', NULL),
(137, 'Thanksgiving', '2025-11-27', '16:00', '19:00', 'Description!!!', 50, 'no', 0, 'Fredericksburg, VA', 'Holiday', NULL),
(138, 'VCTest1-NewVersion', '2025-12-01', '18:00', '20:30', 'First test event!!!!', 400, 'no', 0, '', 'Test', 18),
(139, 'VCTest2', '2025-11-30', '06:00', '07:00', 'AAA', 999, 'no', 0, '', 'Test', 18),
(140, 'VCTest3', '2025-12-16', '06:00', '07:00', 'AAAA', 999, 'no', 0, '', 'Test', 18),
(141, 'VCTest4', '2025-12-02', '18:00', '20:30', 'AAA', 999, 'no', 0, 'UMW Campus', 'Test', 18);

-- --------------------------------------------------------

--
-- Table structure for table `dbgroups`
--

CREATE TABLE `dbgroups` (
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbgroups`
--

INSERT INTO `dbgroups` (`group_name`, `color_level`) VALUES
('cool guys', 'green'),
('test', 'green');

-- --------------------------------------------------------

--
-- Table structure for table `dbmessages`
--

CREATE TABLE `dbmessages` (
  `id` int NOT NULL,
  `senderID` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipientID` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wasRead` tinyint(1) NOT NULL DEFAULT '0',
  `prioritylevel` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbmessages`
--

INSERT INTO `dbmessages` (`id`, `senderID`, `recipientID`, `title`, `body`, `time`, `wasRead`, `prioritylevel`) VALUES
(27, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:35', 0, 0),
(28, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:35', 0, 0),
(29, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:35', 0, 0),
(30, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:35', 0, 0),
(32, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:35', 0, 0),
(34, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:35', 1, 0),
(36, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:36', 0, 0),
(37, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:36', 0, 0),
(38, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:36', 0, 0),
(39, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:36', 0, 0),
(41, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:36', 0, 0),
(43, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:36', 1, 0),
(45, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:38', 0, 0),
(46, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:38', 0, 0),
(47, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:38', 0, 0),
(48, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:38', 0, 0),
(50, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:38', 0, 0),
(52, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-15:38', 1, 0),
(54, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-16:47', 0, 0),
(55, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-16:47', 0, 0),
(56, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-16:47', 0, 0),
(57, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-16:47', 0, 0),
(59, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-16:47', 0, 0),
(61, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-16:47', 1, 0),
(63, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:48', 0, 0),
(64, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:48', 0, 0),
(65, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:48', 0, 0),
(66, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:48', 0, 0),
(68, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:48', 0, 0),
(70, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:48', 1, 0),
(72, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:50', 0, 0),
(73, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:50', 0, 0),
(74, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:50', 0, 0),
(75, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:50', 0, 0),
(77, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:50', 0, 0),
(79, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:50', 1, 0),
(81, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:52', 0, 0),
(82, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:52', 0, 0),
(83, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:52', 0, 0),
(84, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:52', 0, 0),
(86, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:52', 0, 0),
(88, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:52', 1, 0),
(90, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:53', 0, 0),
(91, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:53', 0, 0),
(92, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:53', 0, 0),
(93, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:53', 0, 0),
(95, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:53', 0, 0),
(97, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:53', 1, 0),
(99, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(100, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(101, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(102, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(104, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(106, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 1, 0),
(108, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(109, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(110, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(111, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(113, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 0, 0),
(115, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-19:55', 1, 0),
(117, 'vmsroot', 'BobVolunteer', 'You have been added to a group. View under Groups page.', 'You have been added to a', '2025-04-29-19:58', 0, 0),
(119, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(120, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(121, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(122, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(124, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(126, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 1, 0),
(128, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(129, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(130, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(131, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(133, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(135, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 1, 0),
(137, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(138, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(139, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(140, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(142, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 0, 0),
(144, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:01', 1, 0),
(152, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(153, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(154, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(155, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(157, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(159, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 1, 0),
(161, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(162, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(163, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(164, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(166, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(168, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 1, 0),
(170, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(171, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(172, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(173, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(175, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(177, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 1, 0),
(179, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(180, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(181, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(182, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(184, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 0, 0),
(186, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:03', 1, 0),
(188, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(189, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(190, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(191, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(193, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(195, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 1, 0),
(197, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(198, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(199, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(200, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(202, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 0, 0),
(204, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:06', 1, 0),
(206, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(207, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(208, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(209, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(211, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(213, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 1, 0),
(215, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(216, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(217, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(218, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(220, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(222, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 1, 0),
(224, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(225, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(226, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(227, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(229, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 0, 0),
(231, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:08', 1, 0),
(233, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(234, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(235, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(236, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(238, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(240, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 1, 0),
(242, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(243, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(244, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(245, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(247, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 0, 0),
(249, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:09', 1, 0),
(251, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(252, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(253, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(254, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(256, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(258, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 1, 0),
(260, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(261, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(262, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(263, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(265, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(267, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 1, 0),
(269, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(270, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(271, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(272, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(274, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(276, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 1, 0),
(278, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(279, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(280, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(281, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(283, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 0, 0),
(285, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:13', 1, 0),
(288, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:54', 0, 0),
(289, 'vmsroot', 'jane_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:54', 0, 0),
(290, 'vmsroot', 'john_doe', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:54', 0, 0),
(291, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:54', 0, 0),
(292, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:54', 0, 0),
(293, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:54', 0, 0),
(295, 'vmsroot', 'volunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-20:54', 1, 0),
(300, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:21', 0, 0),
(301, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:21', 0, 0),
(302, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:21', 0, 0),
(303, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:21', 0, 0),
(309, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:30', 1, 0),
(310, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:30', 0, 0),
(311, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:30', 0, 0),
(312, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:30', 0, 0),
(313, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:30', 0, 0),
(315, 'vmsroot', 'Volunteer1', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:30', 0, 0),
(318, 'vmsroot', 'ameyer3', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-21:31', 1, 0),
(319, 'vmsroot', 'maddiev', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-21:31', 0, 0),
(323, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:43', 1, 0),
(324, 'vmsroot', 'ameyer32', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:43', 0, 0),
(325, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:43', 0, 0),
(326, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:43', 0, 0),
(327, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:43', 0, 0),
(328, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:43', 0, 0),
(330, 'vmsroot', 'Volunteer1', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:43', 0, 0),
(332, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:44', 1, 0),
(333, 'vmsroot', 'ameyer32', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:44', 0, 0),
(334, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:44', 0, 0),
(335, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:44', 0, 0),
(336, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:44', 0, 0),
(337, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:44', 0, 0),
(339, 'vmsroot', 'Volunteer1', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:44', 0, 0),
(340, 'vmsroot', 'ameyer32', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-21:45', 0, 0),
(343, 'vmsroot', 'ameyer123', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 0, 0),
(344, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 1, 0),
(345, 'vmsroot', 'ameyer32', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 0, 0),
(346, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 0, 0),
(347, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 0, 0),
(348, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 0, 0),
(349, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 0, 0),
(351, 'vmsroot', 'Volunteer1', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:50', 0, 0),
(352, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:52', 1, 0),
(353, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:52', 0, 0),
(354, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:52', 0, 0),
(355, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:52', 0, 0),
(356, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-21:52', 0, 0),
(358, 'vmsroot', 'BobVolunteer', 'You have been added to a group. View under Groups page.', 'You have been added to DAWGS', '2025-04-29-21:52', 0, 0),
(360, 'vmsroot', 'lukeg', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-21:53', 0, 0),
(361, 'vmsroot', 'maddiev', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-21:53', 0, 0),
(364, 'vmsroot', 'ameyer3', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-22:00', 1, 0),
(370, 'vmsroot', 'michellevb', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-22:07', 0, 0),
(372, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 1, 0),
(373, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(374, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(375, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(376, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(377, 'vmsroot', 'michellevb', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(381, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 1, 0),
(382, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(383, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(384, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(385, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(386, 'vmsroot', 'michellevb', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-29-22:20', 0, 0),
(388, 'vmsroot', 'ameyer3', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-22:21', 1, 0),
(389, 'vmsroot', 'maddiev', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-22:22', 0, 0),
(392, 'vmsroot', 'test_acc', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-29-23:44', 0, 0),
(394, 'vmsroot', 'BobVolunteer', 'You have been added to a group. View under Groups page.', 'You have been added to t', '2025-04-30-08:16', 0, 0),
(395, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-30-10:13', 1, 0),
(396, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-30-10:13', 0, 0),
(397, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-30-10:13', 0, 0),
(398, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-30-10:13', 0, 0),
(399, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-30-10:13', 0, 0),
(400, 'vmsroot', 'michellevb', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-30-10:13', 0, 0),
(401, 'vmsroot', 'test_acc', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-04-30-10:13', 0, 0),
(405, 'vmsroot', 'ameyer3', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-30-10:15', 1, 0),
(406, 'vmsroot', 'BobVolunteer', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-30-10:15', 0, 0),
(407, 'vmsroot', 'lukeg', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-30-10:15', 0, 0),
(409, 'vmsroot', 'Volunteer25', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-30-10:21', 1, 0),
(412, 'vmsroot', 'lukeg', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-04-30-13:13', 0, 0),
(414, 'vmsroot', 'ameyer3', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 1, 0),
(415, 'vmsroot', 'BobVolunteer', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 0, 0),
(416, 'vmsroot', 'lukeg', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 0, 0),
(417, 'vmsroot', 'maddiev', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 0, 0),
(418, 'vmsroot', 'michael_smith', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 0, 0),
(419, 'vmsroot', 'michellevb', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 0, 0),
(420, 'vmsroot', 'test_acc', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 0, 0),
(422, 'vmsroot', 'Volunteer25', 'A new discussion has been created. View under discussions page.', 'New Discussion', '2025-05-01-11:32', 0, 0),
(423, 'vmsroot', 'maddiev', 'You have been added to a group. View under Groups page.', 'You have been added to test', '2025-05-01-11:32', 0, 0),
(427, 'vmsroot', 'vmsroot', 'You have been added to a group. View under Groups page.', 'You have been added to cool guys', '2025-09-10-11:35', 1, 0),
(428, 'vmsroot', 'vmsroot', 'vmsroot has replied to test. View under discussions page.', 'A user has replied to a discussion.', '2025-09-10-11:40', 1, 0),
(429, 'vmsroot', 'vmsroot', 'testvolunteer has been added as a volunteer', 'New volunteer account has been created', '2025-10-19-11:20', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dbpendingsignups`
--

CREATE TABLE `dbpendingsignups` (
  `username` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eventname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbpendingsignups`
--

INSERT INTO `dbpendingsignups` (`username`, `eventname`, `role`, `notes`) VALUES
('vmsroot', '108', 'v', 'Skills: non | Dietary restrictions: ojnjo | Disabilities: jonoj | Materials: knock'),
('vmsroot', '101', 'v', 'Skills: rvwav | Dietary restrictions: varv | Disabilities: var | Materials: arv'),
('vmsroot', '108', 'v', 'Skills: non | Dietary restrictions: ojnjo | Disabilities: jonoj | Materials: knock'),
('vmsroot', '101', 'v', 'Skills: rvwav | Dietary restrictions: varv | Disabilities: var | Materials: arv');

-- --------------------------------------------------------

--
-- Table structure for table `dbpersonhours`
--

CREATE TABLE `dbpersonhours` (
  `personID` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eventID` int NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dbpersonhours`
--

INSERT INTO `dbpersonhours` (`personID`, `eventID`, `start_time`, `end_time`) VALUES
('john_doe', 100, '2024-11-23 22:00:00', '2024-11-23 23:00:00'),
('john_doe', 100, '2024-11-23 22:00:00', '2024-11-23 23:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `dbpersons`
--

CREATE TABLE `dbpersons` (
  `person_id` int NOT NULL,
  `id` varchar(256) NOT NULL,
<<<<<<< Updated upstream
  `role_type` int NOT NULL,
=======
  `role_type` int(11) NOT NULL,
>>>>>>> Stashed changes
  `role_name` varchar(256) DEFAULT NULL,
  `start_date` text,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `street_address` text,
  `city` text,
  `state` varchar(2) DEFAULT NULL,
  `zip_code` text,
  `phone1` varchar(12) DEFAULT NULL,
  `phone1type` text,
  `emergency_contact_phone` varchar(12) DEFAULT NULL,
  `emergency_contact_phone_type` text,
  `birthday` text,
  `email` text NOT NULL,
  `emergency_contact_first_name` text,
  `contact_num` varchar(255) DEFAULT NULL,
  `emergency_contact_relation` text,
  `contact_method` text,
  `type` text,
  `status` text,
  `notes` text,
  `password` text,
  `skills` text,
  `interests` text,
  `archived` tinyint(1) DEFAULT NULL,
  `emergency_contact_last_name` text,
  `is_new_volunteer` tinyint(1) DEFAULT '1',
  `is_community_service_volunteer` tinyint(1) DEFAULT '0',
  `total_hours_volunteered` decimal(5,2) DEFAULT '0.00',
  `volunteer_of_the_month` tinyint(1) DEFAULT '0',
  `votm_awarded_month` date DEFAULT NULL,
  `training_level` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dbpersons`
--

INSERT INTO `dbpersons` (`person_id`, `id`, `role_type`, `role_name`, `start_date`, `first_name`, `last_name`, `street_address`, `city`, `state`, `zip_code`, `phone1`, `phone1type`, `emergency_contact_phone`, `emergency_contact_phone_type`, `birthday`, `email`, `emergency_contact_first_name`, `contact_num`, `emergency_contact_relation`, `contact_method`, `type`, `status`, `notes`, `password`, `skills`, `interests`, `archived`, `emergency_contact_last_name`, `is_new_volunteer`, `is_community_service_volunteer`, `total_hours_volunteered`, `volunteer_of_the_month`, `votm_awarded_month`, `training_level`) VALUES
(1, 'ameyer3', 0, NULL, '2025-03-26', 'Aidan', 'Meyer', '1541 Surry Hill Court', 'Charlottesville', 'VA', '22901', '4344222910', 'home', '4344222910', 'home', '2003-08-17', 'aidanmeyer32@gmail.com', 'Aidan', 'n/a', 'Father', NULL, 'volunteer', 'Active', NULL, '$2y$10$0R5pX4uTxS0JZ4rc7dGprOK4c/d1NEs0rnnaEmnW4sz8JIQVyNdBC', 'a', 'a', 0, 'Meyer', 0, 0, 70.00, 1, '2025-09-10', NULL),
(2, 'BobVolunteer', 0, NULL, '2025-04-29', 'Bob', 'SPCA', '123 Dog Ave', 'Dogville', 'VA', '54321', '9806761234', 'home', '1234567788', 'home', '2020-03-03', 'fred54321@gmail.com', 'Luke', 'n/a', 'Bff', NULL, 'volunteer', 'Active', NULL, '$2y$10$4wUwAW0yoizxi5UFy1/OZu.yfYY7rzUsuYcZCdvfplLj95r7OknvG', 'No epic skills', 'No interests', 0, 'Blair', 0, 0, 70.00, 0, NULL, 'None'),
(3, 'lukeg', 0, NULL, '2025-04-29', 'Luke', 'Gibson', '22 N Ave', 'Fredericksburg', 'VA', '22401', '1234567890', 'cellphone', '1234567890', 'cellphone', '2025-04-28', 'volunteer@volunteer.com', 'NoName', 'n/a', 'Brother', NULL, 'volunteer', 'Active', NULL, '$2y$10$KsNVJYhvO5D287GpKYsIPuci9FnL.Eng9R6lBpaetu2Y0yVJ7Uuiq', 'reading', 'none', 0, 'YesName', 0, 0, 0.00, 0, NULL, 'None'),
(4, 'maddiev', 0, NULL, '2025-04-28', 'maddie', 'van buren', '123 Blue st', 'fred', 'VA', '12343', '1234567890', 'cellphone', '1234567819', 'cellphone', '2003-05-17', 'mvanbure@mail.umw.edu', 'mommy', 'n/a', 'mom', NULL, 'volunteer', 'Active', NULL, '$2y$10$0mv3.e6gjqoIg.HfT5qVXOsI.Ca5E93DAy8BnT124W1PvMDxpfoxy', 'coding', 'yoga', 0, 'van buren', 0, 0, -8.98, 0, NULL, 'None'),
(5, 'michael_smith', 0, NULL, '2025-03-16', 'Michael', 'Smith', '789 Pine Street', 'Charlottesville', 'VA', '22903', '4345559876', 'mobile', '4345553322', 'work', '1995-08-22', 'michaelsmith@email.com', 'Sarah', '4345553322', 'Sister', 'email', 'volunteer', 'Active', '', '$2y$10$XYZ789xyz456LMN123DEF', 'Cooking, Basketball', 'Homeless Shelter Assistance', 0, 'Smith', 0, 1, 0.00, 0, NULL, NULL),
(6, 'michellevb', 0, NULL, '2025-04-29', 'Michelle', 'Van Buren', '1234 Red St', 'Freddy', 'VA', '22401', '1234567890', 'cellphone', '0987654321', 'cellphone', '1980-08-18', 'michelle.vb@gmail.com', 'Madison', 'n/a', 'daughter', NULL, 'volunteer', 'Active', NULL, '$2y$10$bkqOWUdIJoSa6kZoRo5KH.cerZkBQf74RYsponUUgefJxNc8ExppK', 'programming', 'doggies', 0, 'Van Buren', 0, 0, 60.00, 0, NULL, 'None'),
(7, 'test_acc', 0, NULL, '2025-04-29', 'test', 'test', 'test', 'test', 'VA', '22405', '5555555555', 'cellphone', '5555555555', 'cellphone', '2003-03-03', 'test@gmail.com', 'test', 'n/a', 't', NULL, 'volunteer', 'Active', NULL, '$2y$10$kpVA41EXvoJyv896uDBEF.fHCPmSlkVSaXjHojBl7DqbRnEm//kxy', '', '', 0, 'test', 0, 0, -4.99, 0, NULL, 'None'),
(8, 'vmsroot', 2, NULL, NULL, 'vmsroot', '', 'N/A', 'N/A', 'VA', 'N/A', '', 'N/A', 'N/A', 'N/A', NULL, '', 'vmsroot', 'N/A', 'N/A', 'email', 'superadmin', 'Active', 'System root user account', '$2y$10$.3p8xvmUqmxNztEzMJQRBesLDwdiRU3xnt/HOcJtsglwsbUk88VTO', 'N/A', 'N/A', 0, 'vmsroot', 0, 0, 0.00, 0, NULL, NULL),
(9, 'Volunteer25', 0, NULL, '2025-04-30', 'Volley', 'McTear', '123 Dog St', 'Dogville', 'VA', '56748', '9887765543', 'home', '6565651122', 'home', '2025-04-29', 'volly@gmail.com', 'Holly', 'n/a', 'Besty', NULL, 'volunteer', 'Active', NULL, '$2y$10$45gKdbjW78pNKX/5ROtb7eU9OykSCsP/QCyTAvqBtord4J7V3Ywga', 'None', 'None', 0, 'McTear', 0, 0, 10.00, 0, NULL, 'None'),
(10, 'ameyer123', 0, NULL, '2025-05-01', 'Aidan', 'Meyer', '1541 Surry Hill Court', 'Charlottesville', 'VA', '22901', '4344222910', 'home', '4344222910', 'home', '2003-08-17', 'aidanmeyer32@gmail.com', 'Aidan', 'n/a', 'Father', NULL, 'participant', 'Inactive', NULL, '$2y$10$2VDZjrW0EacO0VA5hIYIl.fKqPC5wUdSSQ1lXXRSgC0eWxVslPcOC', 'a', 'a', 0, 'Meyer', 0, 0, 0.00, 0, NULL, 'None'),
(11, 'testvolunteer', 0, NULL, '2025-10-19', 'TestVolunteer', 'N/A', 'N/A', 'N/A', 'VA', '12345', '1234567890', 'cellphone', '1234567890', 'cellphone', '2002-12-12', 'testvolunteer@email.com', 'N/A', NULL, 'N/A', NULL, 'participant', 'Active', NULL, '$2y$10$pruoDpDlIjCnKcP70/8J3eFofrrBVxwD.7ADrYcpWUhOFEv87xHmS', 'N/A', 'N/A', 0, 'N/A', 1, 0, 0.00, 0, NULL, 'None'),
(12, 'volunteer', 0, NULL, NULL, 'Volunteer', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'volunteer@example.com', NULL, NULL, NULL, NULL, 'volunteer', 'Active', NULL, NULL, NULL, NULL, 0, NULL, 1, 0, 0.00, 0, NULL, NULL),
(13, 'admin', 2, NULL, NULL, 'Admin', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin@example.com', NULL, NULL, NULL, NULL, 'admin', 'Active', NULL, NULL, NULL, NULL, 0, NULL, 1, 0, 0.00, 0, NULL, NULL),
(14, 'coord1', 1, 'Volunteer Coordinator', NULL, 'Jane', 'Doe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'janedoe@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(15, 'coord2', 1, 'Volunteer Coordinator', '', 'John', 'Doe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'johndoe@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(16, 'coord3', 1, 'Volunteer Coordinator', '', 'Emily', 'Smith', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'emilysmith123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(17, 'coord4', 1, 'Volunteer Coordinator', NULL, 'Erin', 'Johnson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ejohnson456@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(18, 'coord5', 1, 'Volunteer Coordinator', NULL, 'Avery', 'Anderson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'averyaaa@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(19, 'boardm1', 1, 'Board Member', NULL, 'John', 'Heart', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'johnheart@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(20, 'boardm2', 1, 'Board Member', NULL, 'Amy', 'Matthews', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'amymatthews1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(21, 'boardm3', 1, 'Board Member', NULL, 'Matthew', 'Jennings', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mattjennings@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(22, 'boardm4', 1, 'Board Member', NULL, 'Brian', 'Parker', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'bparker@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(23, 'boardm5', 1, 'Board Member', NULL, 'Harold', 'Williams', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'harold12345@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL),
(24, 'coordinator', 1, NULL, NULL, 'Coordinator', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'coordinator@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.00, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dbshifts`
--

CREATE TABLE `dbshifts` (
  `shift_id` int NOT NULL,
  `person_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time DEFAULT NULL,
  `totalHours` decimal(5,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbshifts`
--

INSERT INTO `dbshifts` (`shift_id`, `person_id`, `date`, `startTime`, `endTime`, `totalHours`, `description`) VALUES
(14, 'maddiev', '2025-04-29', '20:22:29', '00:30:40', 0.13, 'a'),
(15, 'ameyer3', '2025-04-29', '20:24:27', '00:30:36', 0.10, 'a'),
(16, 'jane_doe', '2025-04-29', '20:26:29', '00:30:40', 0.07, 'a'),
(17, 'ameyer3', '2025-04-29', '20:31:30', '00:32:09', 0.00, 'a'),
(18, 'jane_doe', '2025-04-29', '20:31:31', '00:32:09', 0.00, 'a'),
(19, 'ameyer3', '2025-04-29', '20:32:14', '00:32:39', 0.00, 'hello'),
(20, 'ameyer3', '2025-04-29', '21:25:49', '01:26:17', 0.00, 'hello'),
(21, 'ameyer32', '2025-04-29', '21:35:01', '01:35:25', 0.00, 'hello'),
(22, 'ameyer123', '2025-04-29', '21:48:53', '01:49:13', 0.00, 'hello'),
(23, 'ameyer3', '2025-04-29', '21:56:37', '01:56:54', 0.00, 'hello'),
(24, 'ameyer3', '2025-04-29', '22:03:00', '02:03:18', 0.00, 'hello'),
(25, 'michellevb', '2025-04-29', '22:08:04', '02:08:36', 0.00, 'yay'),
(26, 'ameyer3', '2025-04-29', '22:24:27', '02:24:43', 0.00, 'hello'),
(27, 'test_acc', '2025-04-29', '23:44:58', '23:45:40', -23.99, 'test'),
(28, 'BobVolunteer', '2025-04-30', '08:14:55', '12:15:09', 0.00, 'good job'),
(29, 'BobVolunteer', '2025-04-30', '08:15:29', NULL, NULL, NULL),
(30, 'Volunteer25', '2025-04-30', '10:21:39', '14:22:09', 0.00, 'test'),
(31, 'ameyer3', '2025-05-01', '11:37:23', '15:37:49', 0.00, 'hello'),
(32, 'lukeg', '2025-07-09', '10:57:46', '10:57:57', 0.00, 'Laundry'),
(33, 'lukeg', '2025-07-09', '11:04:46', NULL, NULL, NULL),
(34, 'vmsroot', '2025-09-10', '11:36:05', NULL, NULL, NULL),
(35, 'volunteer', '2025-10-20', '16:49:31', '16:52:57', 0.05, 'left'),
(36, 'vmsroot', '2025-11-17', '09:00:04', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dbsupplies`
--

CREATE TABLE `dbsupplies` (
<<<<<<< Updated upstream
  `supply_id` INT NOT NULL AUTO_INCREMENT,
  `item_type` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` INT NOT NULL,
  `description` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `date_submitted` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `reserve_status` VARCHAR(20) NOT NULL DEFAULT 'unreserved',
  PRIMARY KEY (`supply_id`)
=======
  `supply_id` int(11) NOT NULL,
  `item_type` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` text NOT NULL,
  `date_submitted` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `reserve_status` varchar(20) NOT NULL DEFAULT 'unreserved'
>>>>>>> Stashed changes
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbsupplies`
--

<<<<<<< Updated upstream
INSERT INTO `dbsupplies` (`supply_id`, `item_type`, `quantity`, `description`, `date_submitted`, `status`, `reserve_status` ) VALUES
=======
INSERT INTO `dbsupplies` (`supply_id`, `item_type`, `quantity`, `description`, `date_submitted`, `status`, `reserve_status`) VALUES
>>>>>>> Stashed changes
(1, 'flyers', 20, 'Fun and colorful!', '2025-11-03', 'pending', 'reserved');

-- --------------------------------------------------------

--
-- Table structure for table `dbvolunteeractivity`
--

CREATE TABLE `dbvolunteeractivity` (
  `activity_id` int NOT NULL,
  `person_id` int NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(4,2) NOT NULL,
<<<<<<< Updated upstream
  `event_id` int NOT NULL,
  `interactions` text COLLATE utf8mb4_general_ci,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  `photo_id` int DEFAULT NULL
=======
  `event_id` int(11) NOT NULL,
  `interactions` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `photo_id` int(11) DEFAULT NULL
>>>>>>> Stashed changes
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_replies`
--

CREATE TABLE `discussion_replies` (
  `reply_id` int NOT NULL,
  `user_reply_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discussion_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_reply_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussion_replies`
--

INSERT INTO `discussion_replies` (`reply_id`, `user_reply_id`, `author_id`, `discussion_title`, `reply_body`, `parent_reply_id`, `created_at`) VALUES
(12, 'Volunteer25', 'Volunteer25', 'test', 'great idea!', '9', '2025-04-30-10:24'),
(13, 'vmsroot', 'vmsroot', 'test', 'test', NULL, '2025-05-01-11:31'),
(14, 'ameyer3', 'ameyer3', 'test', 'hello', '13', '2025-05-01-11:38'),
(15, 'ameyer3', 'vmsroot', 'test', 'hello', NULL, '2025-05-01-11:38'),
(16, 'vmsroot', 'vmsroot', 'test', 'testt', NULL, '2025-09-10-11:40');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_hours_snapshot`
--

CREATE TABLE `monthly_hours_snapshot` (
  `id` int NOT NULL,
  `person_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `month_year` date DEFAULT NULL,
  `hours` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_hours_snapshot`
--

INSERT INTO `monthly_hours_snapshot` (`id`, `person_id`, `month_year`, `hours`) VALUES
(36, 'ameyer3', '2025-03-15', 77),
(37, 'jane_doe', '2025-03-15', 0),
(38, 'john_doe', '2025-03-15', 0),
(39, 'michael_smith', '2025-03-15', 0),
(40, 'vmsroot', '2025-03-15', 0),
(57, 'ameyer3', '2025-04-01', 96),
(58, 'jane_doe', '2025-04-01', 3),
(59, 'john_doe', '2025-04-01', 6),
(60, 'michael_smith', '2025-04-01', 8),
(61, 'vmsroot', '2025-04-01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`user_id`, `group_name`) VALUES
('ameyer3', 'test'),
('BobVolunteer', 'test'),
('vmsroot', 'cool guys');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbaccounts`
--
ALTER TABLE `dbaccounts`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `uq_dbaccounts_email` (`email`);

--
-- Indexes for table `dbarchived_volunteers`
--
ALTER TABLE `dbarchived_volunteers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbdiscussions`
--
ALTER TABLE `dbdiscussions`
  ADD PRIMARY KEY (`author_id`(255),`title`);

--
-- Indexes for table `dbeventmedia`
--
ALTER TABLE `dbeventmedia`
  ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `dbeventpersons`
--
ALTER TABLE `dbeventpersons`
  ADD KEY `FKeventID` (`eventID`),
  ADD KEY `FKpersonID` (`userID`);

--
-- Indexes for table `dbevents`
--
ALTER TABLE `dbevents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbgroups`
--
ALTER TABLE `dbgroups`
  ADD PRIMARY KEY (`group_name`);

--
-- Indexes for table `dbmessages`
--
ALTER TABLE `dbmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbpersonhours`
--
ALTER TABLE `dbpersonhours`
  ADD KEY `FkpersonID2` (`personID`),
  ADD KEY `FKeventID3` (`eventID`);

--
-- Indexes for table `dbpersons`
--
ALTER TABLE `dbpersons`
  ADD PRIMARY KEY (`person_id`) USING BTREE;

--
-- Indexes for table `dbshifts`
--
ALTER TABLE `dbshifts`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `dbsupplies`
--
<<<<<<< Updated upstream
=======
ALTER TABLE `dbsupplies`
  ADD PRIMARY KEY (`supply_id`);
>>>>>>> Stashed changes

--
-- Indexes for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `fk_author` (`author_id`),
  ADD KEY `fk_user` (`user_reply_id`),
  ADD KEY `fk_parent` (`parent_reply_id`);

--
-- Indexes for table `monthly_hours_snapshot`
--
ALTER TABLE `monthly_hours_snapshot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`user_id`,`group_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbeventmedia`
--
ALTER TABLE `dbeventmedia`
  MODIFY `media_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbevents`
--
ALTER TABLE `dbevents`
<<<<<<< Updated upstream
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;
=======
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;
>>>>>>> Stashed changes

--
-- AUTO_INCREMENT for table `dbmessages`
--
ALTER TABLE `dbmessages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=430;

--
-- AUTO_INCREMENT for table `dbpersons`
--
ALTER TABLE `dbpersons`
<<<<<<< Updated upstream
  MODIFY `person_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
=======
  MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
>>>>>>> Stashed changes

--
-- AUTO_INCREMENT for table `dbshifts`
--
ALTER TABLE `dbshifts`
<<<<<<< Updated upstream
  MODIFY `shift_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
=======
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
>>>>>>> Stashed changes

--
-- AUTO_INCREMENT for table `dbsupplies`
--
ALTER TABLE `dbsupplies`
<<<<<<< Updated upstream
  MODIFY `supply_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
=======
  MODIFY `supply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
>>>>>>> Stashed changes

--
-- AUTO_INCREMENT for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
<<<<<<< Updated upstream
  MODIFY `activity_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
=======
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
>>>>>>> Stashed changes

--
-- AUTO_INCREMENT for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  MODIFY `reply_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `monthly_hours_snapshot`
--
ALTER TABLE `monthly_hours_snapshot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
