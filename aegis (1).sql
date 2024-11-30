-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2024 at 11:46 AM
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
(1234, 'Savings', 11750),
(1235, 'Current', 13950),
(1236, 'Current', 9500),
(1237, 'current', 100000),
(1238, 'current', 100000),
(1239, 'current', 100000);

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

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branchID` int(11) NOT NULL,
  `branchName` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `branchManagerID` int(11) NOT NULL,
  `performanceRating` decimal(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(37, 1235, 'Ayesha Ansari', 'k224453@nu.edu.pk', 'Flat No. N-4, Ashraf Plaza, phase-2, Sector 14-B, Shadman Town', '2004-12-15', 2147483647, 'ayesha12'),
(39, 1238, 'Amna Ansari', 'amnaansari.12.2006@gmail.com', 'Flat No. N-4, Ashraf Plaza, phase-2, Sector 14-B, Shadman Town', '2006-10-12', 2147483647, 'check456'),
(40, 1239, 'Amna Ansari', 'aishaansarih2o@gmail.com', 'thsthwt', '2024-11-20', 0, 'jbabiogaoba');

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
(6, 37, 'inquiry', 'Umm i wanna know the developer of this bank, the one whose name start with A. I have one more hint, it ends on A too. ', 'Closed', '2024-11-20 17:33:35');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `departmentID` int(11) NOT NULL,
  `departmentName` varchar(100) NOT NULL,
  `managerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employeeID` int(11) NOT NULL,
  `departmentID` int(11) NOT NULL,
  `branchID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phoneNumber` varchar(15) NOT NULL,
  `role` varchar(50) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `hireDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(101, 1234, 'Car', 1500, 1.00, 0, '2024-11-16', '2024-11-30'),
(106, 1234, 'Car', 500000, 2.42, 0, '2024-11-01', '2024-11-30'),
(107, 1235, 'Car', 500000, 2.35, 0, '2024-11-11', '2024-11-30');

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
(3, 1235, '2024-11-17', 'debit', 50),
(4, 1234, '2024-11-17', 'credit', 50),
(5, 1235, '2024-11-17', 'debit', 50),
(6, 1234, '2024-11-17', 'credit', 50),
(7, 1235, '2024-11-17', 'debit', 50),
(8, 1234, '2024-11-17', 'credit', 50),
(9, 1235, '2024-11-17', 'debit', 50),
(10, 1234, '2024-11-17', 'credit', 50),
(11, 1235, '2024-11-17', 'debit', 50),
(12, 1234, '2024-11-17', 'credit', 50),
(13, 1235, '2024-11-17', 'debit', 50),
(14, 1234, '2024-11-17', 'credit', 50),
(15, 1235, '2024-11-17', 'debit', 50),
(16, 1234, '2024-11-17', 'credit', 50),
(17, 1235, '2024-11-17', 'debit', 50),
(18, 1234, '2024-11-17', 'credit', 50),
(19, 1235, '2024-11-17', 'debit', 50),
(20, 1234, '2024-11-17', 'credit', 50),
(21, 1235, '2024-11-17', 'debit', 100),
(22, 1234, '2024-11-17', 'credit', 100),
(23, 1235, '2024-11-17', 'debit', 100),
(24, 1234, '2024-11-17', 'credit', 100),
(25, 1235, '2024-11-17', 'debit', 100),
(26, 1234, '2024-11-17', 'credit', 100),
(27, 1236, '2024-11-18', 'debit', 500),
(28, 1234, '2024-11-18', 'credit', 500),
(29, 1235, '2024-11-19', 'debit', 150),
(30, 1234, '2024-11-19', 'credit', 150),
(31, 1235, '2024-11-20', 'debit', 150),
(32, 1234, '2024-11-20', 'credit', 150);

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
  MODIFY `AccountID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1240;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1002;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `branchID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `customer_support`
--
ALTER TABLE `customer_support`
  MODIFY `Support_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `departmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1235;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `LoanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
