-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2024 at 08:48 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `draft_launchp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_ideation`
--

CREATE TABLE `comment_ideation` (
  `commentID` int(11) NOT NULL,
  `ideationID` int(11) NOT NULL,
  `comment_overview` text NOT NULL,
  `comment_logo` text NOT NULL,
  `comment_canvas` text NOT NULL,
  `comment_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_pitching`
--

CREATE TABLE `comment_pitching` (
  `commentID` int(11) NOT NULL,
  `pitchingID` int(11) NOT NULL,
  `comment_video` text NOT NULL,
  `comment_deck` text NOT NULL,
  `comment_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_registration`
--

CREATE TABLE `company_registration` (
  `Company_ID` int(11) NOT NULL,
  `Student_ID` varchar(20) NOT NULL,
  `Company_name` varchar(100) NOT NULL,
  `Company_logo` varchar(100) NOT NULL,
  `Company_description` text NOT NULL,
  `Registration_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_registration`
--

INSERT INTO `company_registration` (`Company_ID`, `Student_ID`, `Company_name`, `Company_logo`, `Company_description`, `Registration_date`) VALUES
(81, '00-UR-0000', 'Tekno', 'images/65a0b5122ece0.png\r\n', 'Tekno Desc', '2024-01-11 07:44:19'),
(82, '00-UR-0000', 'Hello', 'images/65a0b5122ece0.png', 'Hello po', '2024-01-12 11:42:10'),
(83, '00-UR-0000', 'ds', 'images/65a0c0902e5ad.png', 'ds', '2024-01-12 12:31:12'),
(84, '00-UR-0000', 'SmartCo', 'images/65a0c0a185945.jpg', 'da', '2024-01-12 12:31:29');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `EvaluationID` int(11) NOT NULL,
  `Phase` varchar(50) NOT NULL,
  `Project_ID` int(11) NOT NULL,
  `Evaluator_ID` int(11) NOT NULL,
  `Comments` text NOT NULL,
  `ApprovalStatus` varchar(50) NOT NULL,
  `Evaluation_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ideation_phase`
--

CREATE TABLE `ideation_phase` (
  `IdeationID` int(11) NOT NULL,
  `Project_ID` int(11) NOT NULL,
  `Project_logo` varchar(255) NOT NULL,
  `Project_Overview` text NOT NULL,
  `Project_Modelcanvas` varchar(255) NOT NULL,
  `Submission_date` datetime NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor_registration`
--

CREATE TABLE `instructor_registration` (
  `Instructor_ID` int(11) NOT NULL,
  `empID` varchar(255) NOT NULL,
  `Instructor_fname` varchar(100) NOT NULL,
  `Instructor_lname` varchar(100) NOT NULL,
  `Instructor_email` varchar(100) NOT NULL,
  `Instructor_password` varchar(255) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `Instructor_contactno` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor_registration`
--

INSERT INTO `instructor_registration` (`Instructor_ID`, `empID`, `Instructor_fname`, `Instructor_lname`, `Instructor_email`, `Instructor_password`, `Department`, `Instructor_contactno`) VALUES
(30, 'URDA-0000', 'JC', 'Reyes', 'jcreyes@psu.edu.ph', '123', 'College of Computing', '00000000000'),
(31, 'URDA-1111', 'Arni', 'Tamayo', 'arnitamayo@psu.edu.ph', '123', 'College of Computing', '1111111111'),
(32, 'URDA-2222', 'Mike', 'Acosta', 'mikeacosta@psu.edu.ph', '123', 'College of Computing', '22222222222');

-- --------------------------------------------------------

--
-- Table structure for table `investor_request`
--

CREATE TABLE `investor_request` (
  `InvestorRequestID` int(11) NOT NULL,
  `PublishedProjectID` int(11) NOT NULL,
  `InvestorName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `SourceofIncome` varchar(100) NOT NULL,
  `IdentityProof` varchar(255) NOT NULL,
  `RequestedDocuments` varchar(255) NOT NULL,
  `Submission_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invitation`
--

CREATE TABLE `invitation` (
  `InvitationID` int(11) NOT NULL,
  `ProjectID` int(11) DEFAULT NULL,
  `InviterID` varchar(50) DEFAULT NULL,
  `InviteeID` varchar(50) DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending',
  `InvitationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation`
--

INSERT INTO `invitation` (`InvitationID`, `ProjectID`, `InviterID`, `InviteeID`, `Status`, `InvitationDate`) VALUES
(5, 45, '00-UR-0000', '11-UR-1111', 'PENDING', '2024-01-10 23:46:19'),
(6, 46, '00-UR-0000', '11-UR-1111', 'PENDING', '2024-01-10 23:46:19'),
(9, 49, '00-UR-0000', '11-UR-1111', 'PENDING', '2024-01-10 23:46:19');

-- --------------------------------------------------------

--
-- Table structure for table `pitching_phase`
--

CREATE TABLE `pitching_phase` (
  `PitchingID` int(11) NOT NULL,
  `Project_ID` int(11) NOT NULL,
  `VideoPitch` varchar(255) NOT NULL,
  `PitchDeck` varchar(255) NOT NULL,
  `Submission_date` datetime NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `Project_ID` int(11) NOT NULL,
  `Company_ID` int(11) NOT NULL,
  `Project_title` varchar(100) NOT NULL,
  `Project_Description` text NOT NULL,
  `Project_date` datetime NOT NULL,
  `STATUS` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`Project_ID`, `Company_ID`, `Project_title`, `Project_Description`, `Project_date`, `STATUS`) VALUES
(45, 81, 'Helti: HealthCare System for Barangay San Manuel, Dapitan, Urdaneta', 'Heltia is a healthcare system', '2024-01-11 07:46:19', 'PRIVATE'),
(46, 81, 'Dyslexium', 'An app for people struggling with Dyslexia', '2024-01-11 07:46:19', 'PRIVATE'),
(49, 81, 'JuanaBeSafe', 'JuanaBeSafe is a system for people suffering violence', '2024-01-11 07:46:19', 'PRIVATE');

-- --------------------------------------------------------

--
-- Table structure for table `project_evaluator`
--

CREATE TABLE `project_evaluator` (
  `evaluatorassign_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_evaluator`
--

INSERT INTO `project_evaluator` (`evaluatorassign_id`, `project_id`, `evaluator_id`) VALUES
(7, 49, 30);

-- --------------------------------------------------------

--
-- Table structure for table `project_member`
--

CREATE TABLE `project_member` (
  `Projectmember_ID` int(11) NOT NULL,
  `Project_ID` int(11) NOT NULL,
  `Student_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_mentor`
--

CREATE TABLE `project_mentor` (
  `Mentorassign_ID` int(11) NOT NULL,
  `Project_ID` int(11) NOT NULL,
  `Mentor_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_mentor`
--

INSERT INTO `project_mentor` (`Mentorassign_ID`, `Project_ID`, `Mentor_ID`) VALUES
(4, 49, 32);

-- --------------------------------------------------------

--
-- Table structure for table `published_project`
--

CREATE TABLE `published_project` (
  `PublishedProjectID` int(11) NOT NULL,
  `Project_ID` int(11) NOT NULL,
  `Published_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_registration`
--

CREATE TABLE `student_registration` (
  `Student_ID` varchar(50) NOT NULL,
  `Student_fname` varchar(100) NOT NULL,
  `Student_lname` varchar(100) NOT NULL,
  `Student_email` varchar(100) NOT NULL,
  `Student_password` varchar(255) NOT NULL,
  `Course` varchar(255) NOT NULL,
  `Year` varchar(20) NOT NULL,
  `Block` varchar(20) NOT NULL,
  `Student_contactno` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_registration`
--

INSERT INTO `student_registration` (`Student_ID`, `Student_fname`, `Student_lname`, `Student_email`, `Student_password`, `Course`, `Year`, `Block`, `Student_contactno`) VALUES
('00-UR-0000', 'Lorem', 'Ipsum', '00ur0000@psu.edu.ph', '123', 'BS Information Technology', '1st Year', 'A', '0000000000'),
('11-UR-1111', 'Ann', 'Jann', '11ur1111@psu.edu.ph', '123', 'BS Civil Engineering', '3rd Year', 'B', '111111111'),
('22ur2222', 'Aeila', 'Dee', '22ur2222@psu.edu.ph', '123', 'BS Information Technology', '1st Year', 'D', '2222222222');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `comment_ideation`
--
ALTER TABLE `comment_ideation`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `fk_ideation` (`ideationID`);

--
-- Indexes for table `comment_pitching`
--
ALTER TABLE `comment_pitching`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `fk_pitching_comment` (`pitchingID`);

--
-- Indexes for table `company_registration`
--
ALTER TABLE `company_registration`
  ADD PRIMARY KEY (`Company_ID`),
  ADD KEY `Fk_company_registration` (`Student_ID`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`EvaluationID`),
  ADD KEY `Fk_one_evaluation` (`Evaluator_ID`),
  ADD KEY `Fk_two_ecaluation` (`Project_ID`);

--
-- Indexes for table `ideation_phase`
--
ALTER TABLE `ideation_phase`
  ADD PRIMARY KEY (`IdeationID`),
  ADD KEY `Fk_ideation_phase` (`Project_ID`);

--
-- Indexes for table `instructor_registration`
--
ALTER TABLE `instructor_registration`
  ADD PRIMARY KEY (`Instructor_ID`) USING BTREE,
  ADD UNIQUE KEY `Instructor_ID` (`Instructor_ID`),
  ADD UNIQUE KEY `Instructor_email` (`Instructor_email`),
  ADD UNIQUE KEY `empID` (`empID`);

--
-- Indexes for table `investor_request`
--
ALTER TABLE `investor_request`
  ADD PRIMARY KEY (`InvestorRequestID`),
  ADD KEY `Fk_investor` (`PublishedProjectID`);

--
-- Indexes for table `invitation`
--
ALTER TABLE `invitation`
  ADD PRIMARY KEY (`InvitationID`),
  ADD KEY `ProjectID` (`ProjectID`),
  ADD KEY `InviterID` (`InviterID`),
  ADD KEY `InviteeID` (`InviteeID`);

--
-- Indexes for table `pitching_phase`
--
ALTER TABLE `pitching_phase`
  ADD PRIMARY KEY (`PitchingID`),
  ADD KEY `Fk_pitching` (`Project_ID`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`Project_ID`),
  ADD KEY `Fk_project_owner` (`Company_ID`);

--
-- Indexes for table `project_evaluator`
--
ALTER TABLE `project_evaluator`
  ADD PRIMARY KEY (`evaluatorassign_id`),
  ADD UNIQUE KEY `project_id` (`project_id`,`evaluator_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `project_member`
--
ALTER TABLE `project_member`
  ADD PRIMARY KEY (`Projectmember_ID`),
  ADD KEY `Fk_project_member` (`Project_ID`),
  ADD KEY `FkTwo_project_member_` (`Student_ID`);

--
-- Indexes for table `project_mentor`
--
ALTER TABLE `project_mentor`
  ADD PRIMARY KEY (`Mentorassign_ID`),
  ADD UNIQUE KEY `Project_ID` (`Project_ID`,`Mentor_ID`),
  ADD KEY `Fk_mentor_assign` (`Project_ID`),
  ADD KEY `FkTwo_mentor_assign` (`Mentor_ID`);

--
-- Indexes for table `published_project`
--
ALTER TABLE `published_project`
  ADD PRIMARY KEY (`PublishedProjectID`),
  ADD KEY `Fk_published` (`Project_ID`);

--
-- Indexes for table `student_registration`
--
ALTER TABLE `student_registration`
  ADD PRIMARY KEY (`Student_ID`),
  ADD UNIQUE KEY `Student_email` (`Student_email`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_ideation`
--
ALTER TABLE `comment_ideation`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comment_pitching`
--
ALTER TABLE `comment_pitching`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_registration`
--
ALTER TABLE `company_registration`
  MODIFY `Company_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `EvaluationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ideation_phase`
--
ALTER TABLE `ideation_phase`
  MODIFY `IdeationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `instructor_registration`
--
ALTER TABLE `instructor_registration`
  MODIFY `Instructor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `investor_request`
--
ALTER TABLE `investor_request`
  MODIFY `InvestorRequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invitation`
--
ALTER TABLE `invitation`
  MODIFY `InvitationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pitching_phase`
--
ALTER TABLE `pitching_phase`
  MODIFY `PitchingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `Project_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `project_evaluator`
--
ALTER TABLE `project_evaluator`
  MODIFY `evaluatorassign_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_member`
--
ALTER TABLE `project_member`
  MODIFY `Projectmember_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_mentor`
--
ALTER TABLE `project_mentor`
  MODIFY `Mentorassign_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `published_project`
--
ALTER TABLE `published_project`
  MODIFY `PublishedProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment_ideation`
--
ALTER TABLE `comment_ideation`
  ADD CONSTRAINT `fk_ideation` FOREIGN KEY (`ideationID`) REFERENCES `ideation_phase` (`IdeationID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comment_pitching`
--
ALTER TABLE `comment_pitching`
  ADD CONSTRAINT `fk_pitching_comment` FOREIGN KEY (`pitchingID`) REFERENCES `pitching_phase` (`PitchingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company_registration`
--
ALTER TABLE `company_registration`
  ADD CONSTRAINT `Fk_company_registration` FOREIGN KEY (`Student_ID`) REFERENCES `student_registration` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `Fk_one_evaluation` FOREIGN KEY (`Evaluator_ID`) REFERENCES `instructor_registration` (`Instructor_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_two_ecaluation` FOREIGN KEY (`Project_ID`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ideation_phase`
--
ALTER TABLE `ideation_phase`
  ADD CONSTRAINT `Fk_ideation_phase` FOREIGN KEY (`Project_ID`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `investor_request`
--
ALTER TABLE `investor_request`
  ADD CONSTRAINT `Fk_investor` FOREIGN KEY (`PublishedProjectID`) REFERENCES `published_project` (`PublishedProjectID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invitation`
--
ALTER TABLE `invitation`
  ADD CONSTRAINT `invitation_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invitation_ibfk_2` FOREIGN KEY (`InviterID`) REFERENCES `student_registration` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invitation_ibfk_3` FOREIGN KEY (`InviteeID`) REFERENCES `student_registration` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pitching_phase`
--
ALTER TABLE `pitching_phase`
  ADD CONSTRAINT `Fk_pitching` FOREIGN KEY (`Project_ID`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `Fk_project_owner` FOREIGN KEY (`Company_ID`) REFERENCES `company_registration` (`Company_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_evaluator`
--
ALTER TABLE `project_evaluator`
  ADD CONSTRAINT `project_evaluator_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_evaluator_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `instructor_registration` (`Instructor_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_member`
--
ALTER TABLE `project_member`
  ADD CONSTRAINT `FkTwo_project_member_` FOREIGN KEY (`Student_ID`) REFERENCES `student_registration` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_project_member` FOREIGN KEY (`Project_ID`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_mentor`
--
ALTER TABLE `project_mentor`
  ADD CONSTRAINT `Fk_mentor_assign` FOREIGN KEY (`Project_ID`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `published_project`
--
ALTER TABLE `published_project`
  ADD CONSTRAINT `Fk_published` FOREIGN KEY (`Project_ID`) REFERENCES `project` (`Project_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
