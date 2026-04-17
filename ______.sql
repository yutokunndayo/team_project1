-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 17, 2026 at 04:26 PM
-- Server version: 8.4.2
-- PHP Version: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `災害`
--

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int NOT NULL,
  `名前` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `con_password` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `携帯電話` int NOT NULL,
  `所属部署` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `役職` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `採用日` date NOT NULL,
  `誕生日` date NOT NULL,
  `住所` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `名前`, `email`, `password`, `con_password`, `携帯電話`, `所属部署`, `役職`, `採用日`, `誕生日`, `住所`, `is_admin`, `created_at`) VALUES
(1, 'Tan San', 'tansan@gmail.com', '12345678', '12345678', 80882311, '営業部', '社員', '2026-04-16', '2006-04-21', '大阪', 0, '2026-04-17 06:04:26'),
(2, 'HaMashou San', 'hamashou@gmail.com', '12345678', '12345678', 89324118, '財務部', '社員', '2026-04-16', '2004-04-14', '大阪', 0, '2026-04-17 06:04:26'),
(3, 'Han San', 'han@gmail.com', '12345678', '12345678', 8932123, '総務部', '社員', '2026-04-16', '2020-04-06', 'Juso', 1, '2026-04-17 06:21:12'),
(4, 'Hnin San', 'hnin@gmail.com', '12345678', '12345678', 9271323, '企画部', '社員', '2026-04-15', '2026-04-02', 'Osaka', 0, '2026-04-17 06:21:12'),
(5, 'Yuuto San', 'yuuto@gmail.com', '12345678', '12345678', 184721, '開発部', '社員', '2026-04-16', '2026-04-14', '和歌山', 1, '2026-04-17 06:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `社員番号` int NOT NULL,
  `名前` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `部門` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `報告` enum('安全','安全じゃない') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`社員番号`, `名前`, `部門`, `comment`, `報告`, `created_at`) VALUES
(2250266, 'Han San', '総務部', '大丈夫です', '安全', '2026-04-17 07:21:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
