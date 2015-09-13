-- phpMyAdmin SQL Dump
-- version 4.3.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 13, 2015 at 09:42 AM
-- Server version: 5.5.38
-- PHP Version: 5.6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `imgProc_DB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Companies`
--

CREATE TABLE `Companies` (
  `id` int(30) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Companies`
--

INSERT INTO `Companies` (`id`, `name`) VALUES
(1, ''),
(2, 'CompanyA'),
(7, 'CompanyB');

-- --------------------------------------------------------

--
-- Table structure for table `Options`
--

CREATE TABLE `Options` (
  `id` int(20) NOT NULL,
  `ratio_id` int(20) NOT NULL,
  `comp_id` int(20) NOT NULL,
  `x_small` int(20) NOT NULL,
  `y_small` int(20) NOT NULL,
  `x_med` int(20) NOT NULL,
  `y_med` int(20) NOT NULL,
  `x_large` int(20) NOT NULL,
  `y_large` int(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Options`
--

INSERT INTO `Options` (`id`, `ratio_id`, `comp_id`, `x_small`, `y_small`, `x_med`, `y_med`, `x_large`, `y_large`) VALUES
(2, 0, 0, 0, 0, 0, 0, 0, 0),
(10, 5, 6, 500, 500, 600, 600, 700, 700),
(16, 5, 2, 500, 500, 600, 600, 900, 900);

-- --------------------------------------------------------

--
-- Table structure for table `Ratios`
--

CREATE TABLE `Ratios` (
  `id` int(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` float DEFAULT NULL,
  `fk_comp` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Ratios`
--

INSERT INTO `Ratios` (`id`, `name`, `value`, `fk_comp`) VALUES
(5, 'Square', 1, 0),
(6, 'Rect', 5, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Companies`
--
ALTER TABLE `Companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Options`
--
ALTER TABLE `Options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Ratios`
--
ALTER TABLE `Ratios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Companies`
--
ALTER TABLE `Companies`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `Options`
--
ALTER TABLE `Options`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `Ratios`
--
ALTER TABLE `Ratios`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;