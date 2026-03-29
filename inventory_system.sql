-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2026 at 04:21 PM
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
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `Batch_ID` int(11) NOT NULL,
  `Item_ID` int(11) NOT NULL,
  `Purchase_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Manufacturing_Date` date NOT NULL,
  `Expiry_Date` date NOT NULL
) ;

--
-- Dumping data for table `batch`
--

INSERT INTO `batch` (`Batch_ID`, `Item_ID`, `Purchase_ID`, `Quantity`, `Manufacturing_Date`, `Expiry_Date`) VALUES
(1, 1, 1, 50, '2026-03-18', '2026-04-05'),
(2, 3, 2, 12, '2026-03-20', '2026-03-31'),
(3, 2, 1, 8, '2026-03-10', '2026-03-24'),
(4, 4, 3, 5, '2026-03-21', '2026-05-15'),
(5, 5, 2, 27, '2026-03-22', '2026-06-01'),
(10, 1, 7, 30, '2026-02-11', '2026-02-28'),
(11, 14, 7, 70, '2026-04-11', '2026-05-11'),
(12, 14, 9, 5, '2024-01-01', '2024-01-02');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Category_ID`, `Category_Name`) VALUES
(2, 'Bakery'),
(1, 'Dairy'),
(4, 'Fruits'),
(3, 'Groceries');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `Item_ID` int(11) NOT NULL,
  `Item_Name` varchar(100) NOT NULL,
  `Category_ID` int(11) NOT NULL,
  `Min_Stock` int(11) NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`Item_ID`, `Item_Name`, `Category_ID`, `Min_Stock`) VALUES
(1, 'Milk Packet', 1, 30),
(2, 'Curd Cup', 1, 15),
(3, 'Bread Loaf', 2, 10),
(4, 'Wheat Flour', 3, 25),
(5, 'Chips Pack', 3, 30),
(14, 'Apple', 4, 50),
(15, 'Pineapple', 4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `Purchase_ID` int(11) NOT NULL,
  `Supplier_ID` int(11) NOT NULL,
  `Purchase_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`Purchase_ID`, `Supplier_ID`, `Purchase_Date`) VALUES
(1, 1, '2026-03-20'),
(2, 2, '2026-03-22'),
(3, 1, '2026-03-25'),
(4, 3, '2026-02-03'),
(5, 3, '2026-03-02'),
(6, 3, '2026-02-11'),
(7, 3, '2026-02-11'),
(8, 8, '2026-01-12'),
(9, 8, '2026-02-12');

-- --------------------------------------------------------

--
-- Table structure for table `sale`
--

CREATE TABLE `sale` (
  `Sale_ID` int(11) NOT NULL,
  `Batch_ID` int(11) NOT NULL,
  `Quantity_Sold` int(11) NOT NULL,
  `Sale_Date` date NOT NULL
) ;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`Sale_ID`, `Batch_ID`, `Quantity_Sold`, `Sale_Date`) VALUES
(1, 5, 1, '2026-01-05'),
(2, 5, 6, '2026-02-11'),
(3, 5, 7, '2026-02-05'),
(4, 5, 3, '2026-05-11');

--
-- Triggers `sale`
--
DELIMITER $$
CREATE TRIGGER `trg_check_expiry` BEFORE INSERT ON `sale` FOR EACH ROW BEGIN
    DECLARE batch_expiry DATE;

    SELECT Expiry_Date
    INTO batch_expiry
    FROM BATCH
    WHERE Batch_ID = NEW.Batch_ID;

    IF batch_expiry < NEW.Sale_Date THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Sale not allowed: batch is expired.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_check_stock` BEFORE INSERT ON `sale` FOR EACH ROW BEGIN
    DECLARE available_qty INT;

    SELECT Quantity
    INTO available_qty
    FROM BATCH
    WHERE Batch_ID = NEW.Batch_ID;

    IF NEW.Quantity_Sold > available_qty THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Sale not allowed: insufficient stock in batch.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_batch_after_sale` AFTER INSERT ON `sale` FOR EACH ROW BEGIN
    UPDATE BATCH
    SET Quantity = Quantity - NEW.Quantity_Sold
    WHERE Batch_ID = NEW.Batch_ID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `Supplier_ID` int(11) NOT NULL,
  `Supplier_Name` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`Supplier_ID`, `Supplier_Name`, `Email`) VALUES
(1, 'FreshFarm Suppliers', 'freshfarm@gmail.com'),
(2, 'DailyNeeds Wholesale', 'dailyneeds@gmail.com'),
(3, 'DairyFarms', 'farms@gmail.com'),
(8, 'FruitFarms', 'fruit@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_phone`
--

CREATE TABLE `supplier_phone` (
  `Supplier_ID` int(11) NOT NULL,
  `Phone_No` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_phone`
--

INSERT INTO `supplier_phone` (`Supplier_ID`, `Phone_No`) VALUES
(1, '9123456780'),
(1, '9876543210'),
(2, '9090909090'),
(2, '9988776655'),
(3, '2345678901'),
(3, '5656565676'),
(8, '9855454545');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`Batch_ID`),
  ADD KEY `fk_batch_item` (`Item_ID`),
  ADD KEY `fk_batch_purchase` (`Purchase_ID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Category_ID`),
  ADD UNIQUE KEY `Category_Name` (`Category_Name`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`Item_ID`),
  ADD UNIQUE KEY `Item_Name` (`Item_Name`),
  ADD KEY `fk_item_category` (`Category_ID`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`Purchase_ID`),
  ADD KEY `fk_purchase_supplier` (`Supplier_ID`);

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`Sale_ID`),
  ADD KEY `fk_sale_batch` (`Batch_ID`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`Supplier_ID`),
  ADD UNIQUE KEY `unique_supplier_name` (`Supplier_Name`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `supplier_phone`
--
ALTER TABLE `supplier_phone`
  ADD PRIMARY KEY (`Supplier_ID`,`Phone_No`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batch`
--
ALTER TABLE `batch`
  MODIFY `Batch_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `Category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `Item_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `Purchase_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `Sale_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `Supplier_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batch`
--
ALTER TABLE `batch`
  ADD CONSTRAINT `fk_batch_item` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_batch_purchase` FOREIGN KEY (`Purchase_ID`) REFERENCES `purchase` (`Purchase_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `fk_item_category` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `fk_purchase_supplier` FOREIGN KEY (`Supplier_ID`) REFERENCES `supplier` (`Supplier_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `sale`
--
ALTER TABLE `sale`
  ADD CONSTRAINT `fk_sale_batch` FOREIGN KEY (`Batch_ID`) REFERENCES `batch` (`Batch_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_phone`
--
ALTER TABLE `supplier_phone`
  ADD CONSTRAINT `fk_supplier_phone_supplier` FOREIGN KEY (`Supplier_ID`) REFERENCES `supplier` (`Supplier_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
