-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2024 at 05:53 PM
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
-- Database: `aegis`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `AccountID` int(20) NOT NULL,
  `AccountType` varchar(100) NOT NULL,
  `Balance` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`AccountID`, `AccountType`, `Balance`) VALUES
(1235, 'Current', 69400),
(1236, 'Current', 9500),
(1237, 'current', 96000),
(1238, 'current', 100000),
(1239, 'current', 100000),
(1240, 'current', 400),
(1241, 'current', 10),
(1242, 'current', 100000);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL,
  `adminPassword` varchar(8) NOT NULL,
  `admin_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `adminPassword`, `admin_name`) VALUES
(1001, 'admin123', 'Ayesha');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `admin_id`, `action`, `details`, `ip_address`, `timestamp`) VALUES
(1, 1001, 'Managing loan', 'Loan checked', '4646466616', '2024-11-30 21:10:13'),
(3, 1001, 'managing audit', 'audit details', '::1', '2024-11-30 17:30:50'),
(4, 1001, 'managing audit', 'audit details', '::1', '2024-11-30 17:46:31'),
(5, 1001, 'Showing project', 'no details', '::1', '2024-12-04 12:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branchID` int(11) NOT NULL,
  `branchName` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `branchManagerID` int(11) DEFAULT NULL,
  `performanceRating` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`branchID`, `branchName`, `location`, `branchManagerID`, `performanceRating`) VALUES
(102, 'jhsks', 'ssss', 1235, 3.00);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerId` int(11) NOT NULL,
  `account_accountID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Address` varchar(100) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Phone` int(11) NOT NULL,
  `UserPassword` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerId`, `account_accountID`, `Name`, `Email`, `Address`, `DateOfBirth`, `Phone`, `UserPassword`) VALUES
(42, 1237, 'Ayesha Ansari', 'k224453@nu.edu.pk', 'xyz road', '2024-12-04', 13212313, 'abcd1234'),
(43, 1242, 'Anabiya', 'test@example.com', 'cdncs', '2024-12-04', 12131546, 'hello123');

-- --------------------------------------------------------

--
-- Table structure for table `customer_support`
--

CREATE TABLE `customer_support` (
  `Support_id` int(11) NOT NULL,
  `customer_customerID` int(11) NOT NULL,
  `issue_type` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_support`
--

INSERT INTO `customer_support` (`Support_id`, `customer_customerID`, `issue_type`, `description`, `status`, `created_at`) VALUES
(8, 42, 'complaint', 'No complains', 'Closed', '2024-12-04 16:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `departmentID` int(11) NOT NULL,
  `departmentName` varchar(100) NOT NULL,
  `managerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`departmentID`, `departmentName`, `managerID`) VALUES
(102, 'Computer science', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employeeID` int(11) NOT NULL,
  `departmentID` int(11) DEFAULT NULL,
  `branchID` int(11) DEFAULT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `hireDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employeeID`, `departmentID`, `branchID`, `firstName`, `lastName`, `email`, `phoneNumber`, `role`, `salary`, `hireDate`) VALUES
(1235, 102, 102, 'mishkaat', 'yousuf', 'mishkaat@example.com', '2222222', 'Assistant Manager', 11111.00, '2024-12-12'),
(1236, 102, 102, 'Ayesha', 'Ansari', 'k224453@nu.edu.pk', '4464646', 'Manager', 700000.00, '2024-12-04');

-- --------------------------------------------------------

--
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `LoanId` int(11) NOT NULL,
  `a_AccountID` int(11) NOT NULL,
  `LoanType` varchar(20) NOT NULL,
  `Amount` double NOT NULL,
  `InterestRate` decimal(10,2) NOT NULL,
  `Status` tinyint(1) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`LoanId`, `a_AccountID`, `LoanType`, `Amount`, `InterestRate`, `Status`, `StartDate`, `EndDate`) VALUES
(107, 1235, 'Car', 500000, 2.35, 0, '2024-11-11', '2024-11-30'),
(109, 1237, 'Car', 10000, 4.19, 0, '2024-12-04', '2025-01-11');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transactionID` int(11) NOT NULL,
  `account_AccountID` int(20) NOT NULL,
  `transactionDate` date NOT NULL,
  `transactionType` varchar(100) NOT NULL,
  `transactionAmount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transactionID`, `account_AccountID`, `transactionDate`, `transactionType`, `transactionAmount`) VALUES
(19, 1235, '2024-11-17', 'Credit', 50000),
(21, 1235, '2024-11-17', 'debit', 100),
(23, 1235, '2024-11-17', 'debit', 100),
(25, 1235, '2024-11-17', 'debit', 100),
(27, 1236, '2024-11-18', 'debit', 500),
(29, 1235, '2024-11-19', 'debit', 150),
(31, 1235, '2024-11-20', 'debit', 150),
(34, 1235, '2024-12-02', 'credit', 5000),
(35, 1237, '2024-12-04', 'debit', 5000),
(37, 1237, '2024-12-04', 'Credit', 1000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branchID`),
  ADD KEY `branchManagerID` (`branchManagerID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerId`),
  ADD KEY `account_accountID` (`account_accountID`);

--
-- Indexes for table `customer_support`
--
ALTER TABLE `customer_support`
  ADD PRIMARY KEY (`Support_id`),
  ADD KEY `customer_customerID` (`customer_customerID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`departmentID`),
  ADD KEY `managerID` (`managerID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employeeID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `departmentID` (`departmentID`),
  ADD KEY `branchID` (`branchID`);

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD PRIMARY KEY (`LoanId`),
  ADD KEY `a_AccountID` (`a_AccountID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transactionID`),
  ADD KEY `account_AccountID` (`account_AccountID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `AccountID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1243;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1002;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `branchID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `customer_support`
--
ALTER TABLE `customer_support`
  MODIFY `Support_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `departmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1237;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `LoanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`adminID`);

--
-- Constraints for table `branch`
--
ALTER TABLE `branch`
  ADD CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`branchManagerID`) REFERENCES `employee` (`employeeID`);

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`account_accountID`) REFERENCES `account` (`AccountID`);

--
-- Constraints for table `customer_support`
--
ALTER TABLE `customer_support`
  ADD CONSTRAINT `customer_support_ibfk_1` FOREIGN KEY (`customer_customerID`) REFERENCES `customer` (`customerId`);

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `department_ibfk_1` FOREIGN KEY (`managerID`) REFERENCES `employee` (`employeeID`);

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`departmentID`) REFERENCES `department` (`departmentID`),
  ADD CONSTRAINT `employee_ibfk_2` FOREIGN KEY (`branchID`) REFERENCES `branch` (`branchID`);

--
-- Constraints for table `loan`
--
ALTER TABLE `loan`
  ADD CONSTRAINT `loan_ibfk_1` FOREIGN KEY (`a_AccountID`) REFERENCES `account` (`AccountID`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`account_AccountID`) REFERENCES `account` (`AccountID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
