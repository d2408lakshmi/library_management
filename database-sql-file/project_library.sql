-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 02, 2025 at 09:34 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `adhaar_id` varchar(12) DEFAULT NULL,
  `email_id` varchar(100) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirm_password` varchar(255) NOT NULL,
  `admin_image` varchar(255) DEFAULT NULL,
  `admin_type` varchar(20) DEFAULT 'Admin',
  `admin_added` datetime NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `firstname`, `middlename`, `lastname`, `adhaar_id`, `email_id`, `contact`, `username`, `password`, `confirm_password`, `admin_image`, `admin_type`, `admin_added`) VALUES
(1, 'John', 'A.', 'Doe', '123456789012', 'johndoe@example.com', '9876543210', 'admin', 'admin123', 'admin123', 'avatar-366-456318.jpg', 'Admin', '2023-10-26 10:00:00'),
(2, 'Jane', 'B.', 'Smith', '210987654321', 'janesmith@example.com', '9876543211', 'jane.librarian', 'librarian123', 'librarian123', 'user.png', 'Librarian', '2023-10-26 10:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `allowed_book`
--

DROP TABLE IF EXISTS `allowed_book`;
CREATE TABLE IF NOT EXISTS `allowed_book` (
  `allowed_book_id` int NOT NULL AUTO_INCREMENT,
  `qntty_books` int NOT NULL,
  PRIMARY KEY (`allowed_book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `allowed_book`
--

INSERT INTO `allowed_book` (`allowed_book_id`, `qntty_books`) VALUES
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `allowed_days`
--

DROP TABLE IF EXISTS `allowed_days`;
CREATE TABLE IF NOT EXISTS `allowed_days` (
  `allowed_days_id` int NOT NULL AUTO_INCREMENT,
  `no_of_days` int NOT NULL,
  PRIMARY KEY (`allowed_days_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `allowed_days`
--

INSERT INTO `allowed_days` (`allowed_days_id`, `no_of_days`) VALUES
(1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `barcode`
--

DROP TABLE IF EXISTS `barcode`;
CREATE TABLE IF NOT EXISTS `barcode` (
  `barcode_id` int NOT NULL AUTO_INCREMENT,
  `pre_barcode` varchar(20) DEFAULT NULL,
  `mid_barcode` int DEFAULT NULL,
  `suf_barcode` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`barcode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barcode`
--

INSERT INTO `barcode` (`barcode_id`, `pre_barcode`, `mid_barcode`, `suf_barcode`) VALUES
(1, 'KIT', 1000, 'VNS'),
(2, 'KIT', 1001, 'VNS'),
(3, 'KIT', 1002, 'VNS'),
(4, 'KIT', 1003, 'VNS'),
(5, 'KIT', 1004, 'VNS');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
CREATE TABLE IF NOT EXISTS `book` (
  `book_id` int NOT NULL AUTO_INCREMENT,
  `book_title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `author_2` varchar(255) DEFAULT NULL,
  `author_3` varchar(255) DEFAULT NULL,
  `author_4` varchar(255) DEFAULT NULL,
  `author_5` varchar(255) DEFAULT NULL,
  `book_pub` varchar(255) DEFAULT NULL,
  `publisher_name` varchar(255) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `copyright_year` varchar(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `book_barcode` varchar(100) NOT NULL,
  `book_image` varchar(255) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `book_title`, `category`, `author`, `author_2`, `author_3`, `author_4`, `author_5`, `book_pub`, `publisher_name`, `isbn`, `copyright_year`, `status`, `book_barcode`, `book_image`, `date_added`, `remarks`) VALUES
(1, 'The C Programming Language', 'CSE', 'Brian W. Kernighan', 'Dennis M. Ritchie', NULL, NULL, NULL, 'Prentice Hall', 'Prentice Hall PTR', '978-0131103627', '1988', 'New', 'KIT1001VNS', 'ANSI.jpg', '2023-10-28 09:00:00', 'Available'),
(2, 'Introduction to Algorithms', 'CSE', 'Thomas H. Cormen', 'Charles E. Leiserson', 'Ronald L. Rivest', 'Clifford Stein', NULL, 'MIT Press', 'The MIT Press', '978-0262033848', '2009', 'New', 'KIT1002VNS', 'Digital-Design.jpg', '2023-10-28 09:05:00', 'Available'),
(3, 'Mechanical Engineering Design', 'ME', 'Joseph E. Shigley', 'Charles R. Mischke', NULL, NULL, NULL, 'McGraw-Hill', 'McGraw-Hill Education', '978-0073398204', '2014', 'New', 'KIT1003VNS', '9788131762318.jpg', '2023-10-28 09:10:00', 'Not Available'),
(4, 'Pride and Prejudice', 'EN', 'Jane Austen', NULL, NULL, NULL, NULL, 'T. Egerton', 'T. Egerton, Whitehall', '978-1503290563', '1813', 'Lost', 'KIT1004VNS', '9788131762318.jpg', '2023-10-28 09:15:00', 'Not Available'),
(5, 'A Brief History of Time', 'Civil', 'Stephen Hawking', NULL, NULL, NULL, NULL, 'Bantam Dell', 'Bantam Dell Publishing Group', '978-0553380163', '1988', 'New', 'KIT1005VNS', '9788131762318.jpg', '2023-10-28 09:20:00', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_book`
--

DROP TABLE IF EXISTS `borrow_book`;
CREATE TABLE IF NOT EXISTS `borrow_book` (
  `borrow_book_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `date_borrowed` datetime NOT NULL,
  `due_date` datetime NOT NULL,
  `date_returned` datetime DEFAULT NULL,
  `borrowed_status` varchar(20) DEFAULT NULL,
  `book_penalty` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`borrow_book_id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `borrow_book`
--

INSERT INTO `borrow_book` (`borrow_book_id`, `user_id`, `book_id`, `date_borrowed`, `due_date`, `date_returned`, `borrowed_status`, `book_penalty`) VALUES
(1, 1, 3, '2023-11-01 14:00:00', '2023-11-08 14:00:00', NULL, 'borrowed', '0');

-- --------------------------------------------------------

--
-- Table structure for table `penalty`
--

DROP TABLE IF EXISTS `penalty`;
CREATE TABLE IF NOT EXISTS `penalty` (
  `penalty_id` int NOT NULL AUTO_INCREMENT,
  `penalty_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`penalty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penalty`
--

INSERT INTO `penalty` (`penalty_id`, `penalty_amount`) VALUES
(1, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE IF NOT EXISTS `report` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `user_id` int NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `detail_action` varchar(100) NOT NULL,
  `date_transaction` datetime NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`report_id`, `book_id`, `user_id`, `admin_name`, `detail_action`, `date_transaction`) VALUES
(1, 1, 2, 'Jane B. Smith', 'Borrowed Book', '2023-10-29 10:00:00'),
(2, 1, 2, 'Jane B. Smith', 'Returned Book', '2023-11-04 12:00:00'),
(3, 3, 1, 'Jane B. Smith', 'Borrowed Book', '2023-11-01 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `return_book`
--

DROP TABLE IF EXISTS `return_book`;
CREATE TABLE IF NOT EXISTS `return_book` (
  `return_book_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `date_borrowed` datetime NOT NULL,
  `due_date` datetime NOT NULL,
  `date_returned` datetime NOT NULL,
  `book_penalty` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`return_book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `return_book`
--

INSERT INTO `return_book` (`return_book_id`, `user_id`, `book_id`, `date_borrowed`, `due_date`, `date_returned`, `book_penalty`) VALUES
(1, 2, 1, '2023-10-29 10:00:00', '2023-11-05 10:00:00', '2023-11-04 12:00:00', '0');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `roll_number` varchar(50) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `address` text,
  `type` varchar(20) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `user_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `roll_number` (`roll_number`),
  UNIQUE KEY `contact` (`contact`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `roll_number`, `firstname`, `middlename`, `lastname`, `contact`, `gender`, `address`, `type`, `branch`, `user_added`) VALUES
(1, 'CSE001', 'Peter', 'C.', 'Jones', '8877665544', 'Male', '123 Tech Lane, Silicon Valley', 'Student', 'CSE', '2023-10-27 11:00:00'),
(2, 'ME001', 'Mary', 'D.', 'Williams', '8877665533', 'Female', '456 Gear Street, Detroit', 'Student', 'ME', '2023-10-27 11:02:00'),
(3, 'TCH01', 'Robert', 'E.', 'Brown', '8877665522', 'Male', '789 Knowledge Ave, Cambridge', 'Teacher', 'EN', '2023-10-27 11:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
CREATE TABLE IF NOT EXISTS `user_log` (
  `user_log_id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `admin_type` varchar(20) DEFAULT NULL,
  `date_log` datetime NOT NULL,
  PRIMARY KEY (`user_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`user_log_id`, `firstname`, `middlename`, `lastname`, `admin_type`, `date_log`) VALUES
(1, 'John', 'A.', 'Doe', 'Admin', '2023-11-01 09:00:00'),
(2, 'Jane', 'B.', 'Smith', 'Librarian', '2023-11-01 09:05:00'),
(3, 'John', 'A.', 'Doe', 'Admin', '2025-09-03 01:01:11'),
(4, 'John', 'A.', 'Doe', 'Admin', '2025-09-03 01:10:18'),
(5, 'John', 'A.', 'Doe', 'Admin', '2025-09-03 02:08:44'),
(6, 'John', 'A.', 'Doe', 'Admin', '2025-09-03 02:34:31');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_book`
--
ALTER TABLE `borrow_book`
  ADD CONSTRAINT `borrow_book_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `borrow_book_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
