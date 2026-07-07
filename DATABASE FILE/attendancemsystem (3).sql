-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jan 2026 pada 02.07
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendancemsystem`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbladmin`
--

CREATE TABLE `tbladmin` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tbladmin`
--

INSERT INTO `tbladmin` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`) VALUES
(1, 'Admin', 'Liam', 'admin@mail.com', 'D00F5D5217896FB7FD601412CB890830');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblattendance`
--

CREATE TABLE `tblattendance` (
  `Id` int(10) NOT NULL,
  `admissionNo` varchar(255) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `meetingId` int(11) DEFAULT NULL,
  `meetingNo` int(3) NOT NULL,
  `sessionTermId` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL,
  `dateTimeTaken` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tblattendance`
--

INSERT INTO `tblattendance` (`Id`, `admissionNo`, `classId`, `classArmId`, `meetingId`, `meetingNo`, `sessionTermId`, `status`, `dateTimeTaken`) VALUES
(162, 'ASDFLKJ', '1', '2', NULL, 0, '1', '1', '2020-11-01'),
(163, 'HSKSDD', '1', '2', NULL, 0, '1', '1', '2020-11-01'),
(164, 'JSLDKJ', '1', '2', NULL, 0, '1', '1', '2020-11-01'),
(172, 'HSKDS9EE', '1', '4', NULL, 0, '1', '1', '2020-11-01'),
(171, 'JKADA', '1', '4', NULL, 0, '1', '0', '2020-11-01'),
(170, 'JSFSKDJ', '1', '4', NULL, 0, '1', '1', '2020-11-01'),
(173, 'ASDFLKJ', '1', '2', NULL, 0, '1', '1', '2020-11-19'),
(174, 'HSKSDD', '1', '2', NULL, 0, '1', '1', '2020-11-19'),
(175, 'JSLDKJ', '1', '2', NULL, 0, '1', '1', '2020-11-19'),
(176, 'JSFSKDJ', '1', '4', NULL, 0, '1', '0', '2021-07-15'),
(177, 'JKADA', '1', '4', NULL, 0, '1', '0', '2021-07-15'),
(178, 'HSKDS9EE', '1', '4', NULL, 0, '1', '0', '2021-07-15'),
(179, 'ASDFLKJ', '1', '2', NULL, 0, '1', '0', '2021-09-27'),
(180, 'HSKSDD', '1', '2', NULL, 0, '1', '1', '2021-09-27'),
(181, 'JSLDKJ', '1', '2', NULL, 0, '1', '1', '2021-09-27'),
(182, 'ASDFLKJ', '1', '2', NULL, 0, '1', '0', '2021-10-06'),
(183, 'HSKSDD', '1', '2', NULL, 0, '1', '0', '2021-10-06'),
(184, 'JSLDKJ', '1', '2', NULL, 0, '1', '1', '2021-10-06'),
(185, 'ASDFLKJ', '1', '2', NULL, 0, '1', '0', '2021-10-07'),
(186, 'HSKSDD', '1', '2', NULL, 0, '1', '0', '2021-10-07'),
(187, 'JSLDKJ', '1', '2', NULL, 0, '1', '0', '2021-10-07'),
(188, 'AMS110', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(189, 'AMS133', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(190, 'AMS135', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(191, 'AMS144', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(192, 'AMS148', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(193, 'AMS151', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(194, 'AMS159', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(195, 'AMS161', '4', '6', NULL, 0, '1', '0', '2021-10-07'),
(196, 'AMS110', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(197, 'AMS133', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(198, 'AMS135', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(199, 'AMS144', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(200, 'AMS148', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(201, 'AMS151', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(202, 'AMS159', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(203, 'AMS161', '4', '6', NULL, 0, '1', '0', '2025-11-09'),
(204, 'AMS110', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(205, 'AMS133', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(206, 'AMS135', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(207, 'AMS144', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(208, 'AMS148', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(209, 'AMS151', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(210, 'AMS159', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(211, 'AMS161', '4', '6', NULL, 0, '1', '0', '2025-11-10'),
(212, 'AMS005', '1', '2', NULL, 0, '1', '1', '2025-11-10'),
(213, 'AMS007', '1', '2', NULL, 0, '1', '1', '2025-11-10'),
(214, 'AMS005', '1', '2', NULL, 0, '1', '1', '2025-11-17'),
(215, 'AMS110', '4', '6', NULL, 0, '1', '1', '2025-11-17'),
(216, 'AMS133', '4', '6', NULL, 1, '1', '1', '2025-11-17'),
(217, 'AMS005', '1', '2', NULL, 1, '1', '1', '2025-11-18'),
(218, 'AMS110', '4', '6', NULL, 1, '1', '1', '2025-11-18'),
(219, 'AMS144', '4', '6', NULL, 1, '1', '1', '2025-11-18'),
(220, 'AMS133', '4', '6', 9, 0, '1', '0', '2025-11-30 16:38:26'),
(221, 'AMS005', '4', '6', 10, 0, '1', '1', '2025-12-07 13:55:36'),
(222, 'AMS133', '4', '6', 10, 0, '1', '1', '2025-12-07 14:09:44'),
(223, 'AMS151', '4', '6', 10, 0, '1', '2', '2025-12-07 14:12:48'),
(224, 'AMS148', '4', '6', 10, 0, '1', '1', '2025-12-07 14:13:01'),
(225, 'AMS005', '4', '6', 11, 0, '1', '0', '2025-12-07 23:55:22'),
(226, 'AMS005', '4', '6', 12, 0, '1', '1', '2025-12-08 00:05:31'),
(227, 'AMS133', '4', '6', 13, 0, '1', '1', '2025-12-08 00:08:30'),
(228, 'AMS159', '4', '6', 13, 0, '1', '1', '2025-12-08 00:08:33'),
(229, 'AMS135', '4', '6', 12, 0, '1', '1', '2025-12-08 00:08:49'),
(230, 'AMS151', '4', '6', 12, 0, '1', '2', '2025-12-08 00:08:51'),
(231, 'AMS148', '4', '6', 12, 0, '1', '1', '2025-12-08 00:08:53'),
(232, 'AMS011', '1', '2', 14, 0, '1', '0', '2025-12-08 00:11:50'),
(233, 'AMS007', '1', '2', 14, 0, '1', '0', '2025-12-08 00:13:17'),
(234, 'AMS005', '4', '6', 15, 0, '1', '1', '2025-12-08 03:13:38'),
(235, 'AMS005', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(236, 'AMS110', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(237, 'AMS133', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(238, 'AMS144', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(239, 'AMS148', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(240, 'AMS151', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(241, 'AMS159', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(242, 'AMS161', '4', '6', NULL, 0, '1', '0', '2026-01-07'),
(243, 'AMS999', '4', '6', NULL, 0, '1', '0', '2026-01-07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblclass`
--

CREATE TABLE `tblclass` (
  `Id` int(10) NOT NULL,
  `className` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tblclass`
--

INSERT INTO `tblclass` (`Id`, `className`) VALUES
(1, '7'),
(3, '8'),
(4, '9');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblclassarms`
--

CREATE TABLE `tblclassarms` (
  `Id` int(10) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmName` varchar(255) NOT NULL,
  `isAssigned` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tblclassarms`
--

INSERT INTO `tblclassarms` (`Id`, `classId`, `classArmName`, `isAssigned`) VALUES
(2, '1', 'C', '1'),
(4, '1', 'D', '1'),
(5, '3', 'B', '1'),
(6, '4', 'A', '1'),
(30, '4', 'D', ''),
(29, '4', 'C', ''),
(28, '4', 'B', ''),
(27, '4', 'A', ''),
(26, '3', 'D', ''),
(25, '3', 'C', ''),
(24, '3', 'B', ''),
(23, '3', 'A', ''),
(22, '1', 'D', ''),
(21, '1', 'C', ''),
(20, '1', 'B', ''),
(19, '1', 'A', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblclassteacher`
--

CREATE TABLE `tblclassteacher` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phoneNo` varchar(50) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tblclassteacher`
--

INSERT INTO `tblclassteacher` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`, `phoneNo`, `classId`, `classArmId`, `dateCreated`) VALUES
(7, 'Anna', 'Carter', 'acarter@mail.com', '827ccb0eea8a706c4c34a16891f84e7b', '01000000031', '1', '2', '2025-12-08 06:38:38'),
(8, 'Brian', 'Adams', 'badams@mail.com', '827ccb0eea8a706c4c34a16891f84e7b', '01000000032', '1', '4', '2025-12-08 06:38:38'),
(9, 'Daniel', 'Stone', 'dstone@mail.com', '827ccb0eea8a706c4c34a16891f84e7b', '01000000033', '3', '5', '2025-12-08 06:38:38'),
(6, 'John', 'Greenwood', 'jwood@mail.com', '32250170a0dca92d53ec9624f336ca24', '0100000030', '4', '6', '2020-10-07'),
(10, 'sunardi', 'ardi', 'sunardi@gmail.com', 'pass123', '098321234', '3', '25', '2025-12-10 04:10:55'),
(11, 'and', 'end', 'nataa69@gmail.com', 'pass123', '081243508733', '1', '19', '2026-01-08 00:41:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblmeetings`
--

CREATE TABLE `tblmeetings` (
  `Id` int(11) NOT NULL,
  `classId` int(11) NOT NULL,
  `classArmId` int(11) NOT NULL,
  `meetingNo` int(11) NOT NULL,
  `meetingDate` date NOT NULL,
  `meetingTime` time DEFAULT NULL,
  `meetingEndTime` time DEFAULT NULL,
  `cutoffTime` time NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdBy` int(11) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tblmeetings`
--

INSERT INTO `tblmeetings` (`Id`, `classId`, `classArmId`, `meetingNo`, `meetingDate`, `meetingTime`, `meetingEndTime`, `cutoffTime`, `description`, `createdBy`, `dateCreated`) VALUES
(6, 4, 6, 1, '2025-11-19', '02:05:00', '03:05:00', '00:00:00', NULL, 6, '2025-11-18 18:05:52'),
(7, 4, 6, 2, '2025-11-19', '07:27:00', '08:27:00', '00:00:00', NULL, 6, '2025-11-18 23:27:29'),
(8, 4, 6, 4, '2025-11-30', '20:00:00', '20:55:00', '00:00:00', NULL, 6, '2025-11-30 12:00:58'),
(9, 4, 6, 5, '2025-11-30', '23:11:00', '12:11:00', '00:00:00', NULL, 6, '2025-11-30 15:11:40'),
(10, 4, 6, 6, '2025-12-07', '20:54:00', '23:55:00', '00:00:00', NULL, 6, '2025-12-07 12:55:11'),
(11, 4, 6, 7, '2025-12-08', '06:52:00', '07:52:00', '00:00:00', NULL, 6, '2025-12-07 22:52:14'),
(12, 4, 6, 8, '2025-12-08', '19:04:00', '09:04:00', '00:00:00', NULL, 6, '2025-12-07 23:04:53'),
(13, 4, 6, 9, '2025-12-08', '19:06:00', '04:06:00', '00:00:00', NULL, 6, '2025-12-07 23:06:29'),
(14, 1, 2, 1, '2025-12-08', '19:10:00', '00:10:00', '00:00:00', NULL, 7, '2025-12-07 23:10:55'),
(15, 4, 6, 10, '2025-12-08', '10:12:00', '12:12:00', '00:00:00', NULL, 6, '2025-12-08 02:12:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblsessionterm`
--

CREATE TABLE `tblsessionterm` (
  `Id` int(10) NOT NULL,
  `sessionName` varchar(50) NOT NULL,
  `termId` varchar(50) NOT NULL,
  `isActive` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tblsessionterm`
--

INSERT INTO `tblsessionterm` (`Id`, `sessionName`, `termId`, `isActive`, `dateCreated`) VALUES
(1, '2019/2020', '1', '1', '2020-10-31'),
(3, '2019/2020', '2', '0', '2020-10-31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblstudents`
--

CREATE TABLE `tblstudents` (
  `Id` int(10) NOT NULL,
  `fullName` varchar(150) NOT NULL,
  `admissionNumber` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tblstudents`
--

INSERT INTO `tblstudents` (`Id`, `fullName`, `admissionNumber`, `password`, `classId`, `classArmId`, `dateCreated`) VALUES
(1, 'Thomas Griswold', 'AMS005', '12345', '4', '6', '2020-10-31'),
(3, 'Samuel Rosella', 'AMS007', '12345', '1', '2', '2020-10-31'),
(4, 'Milagros Lawson', 'AMS011', '12345', '3', '25', '2020-10-31'),
(5, 'Luis Ayo', 'AMS012', '12345', '1', '4', '2020-10-31'),
(6, 'Sandra Salgado', 'AMS015', '12345', '1', '4', '2020-10-31'),
(7, 'Smith Mack', 'AMS017', '12345', '1', '4', '2020-10-31'),
(8, 'Juliana Debiie', 'AMS019', '12345', '3', '5', '2020-10-31'),
(9, 'Richard Grimmer', 'AMS021', '12345', '3', '5', '2020-10-31'),
(10, 'Jon Boller', 'AMS110', '12345', '4', '6', '2021-10-07'),
(11, 'Aida Hawley', 'AMS133', '12345', '4', '6', '2021-10-07'),
(12, 'Miguel Bush', 'AMS135', '12345', '4', '30', '2021-10-07'),
(13, 'Sergio Hammons', 'AMS144', '12345', '4', '6', '2021-10-07'),
(14, 'Lyn Rogers', 'AMS148', '12345', '4', '6', '2021-10-07'),
(15, 'James Dominick', 'AMS151', '12345', '4', '6', '2021-10-07'),
(16, 'Ethel Quin', 'AMS159', '12345', '4', '6', '2021-10-07'),
(17, 'Roland Estrada', 'AMS161', '12345', '4', '6', '2021-10-07'),
(18, 'KING RYAN', 'AMS999', '', '4', '6', '2025-12-09'),
(19, 'yuzuf mansur', 'AMS997', '12345', '3', '5', '2026-01-07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblteacher_class`
--

CREATE TABLE `tblteacher_class` (
  `id` int(10) NOT NULL,
  `teacherId` int(10) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tblteacher_class`
--

INSERT INTO `tblteacher_class` (`id`, `teacherId`, `classId`, `classArmId`, `isActive`, `createdAt`) VALUES
(1, 7, '1', '2', 1, '2026-01-07 17:38:25'),
(2, 8, '1', '4', 1, '2026-01-07 17:38:25'),
(3, 9, '3', '5', 1, '2026-01-07 17:38:25'),
(4, 6, '4', '6', 1, '2026-01-07 17:38:25'),
(5, 10, '3', '25', 1, '2026-01-07 17:38:25'),
(6, 11, '1', '19', 1, '2026-01-07 17:38:25'),
(8, 11, '1', '20', 1, '2026-01-07 20:02:31'),
(10, 6, '3', '19', 1, '2026-01-07 20:03:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblterm`
--

CREATE TABLE `tblterm` (
  `Id` int(10) NOT NULL,
  `termName` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tblterm`
--

INSERT INTO `tblterm` (`Id`, `termName`) VALUES
(1, 'First'),
(2, 'Second'),
(3, 'Third');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblclass`
--
ALTER TABLE `tblclass`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblclassarms`
--
ALTER TABLE `tblclassarms`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblmeetings`
--
ALTER TABLE `tblmeetings`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblsessionterm`
--
ALTER TABLE `tblsessionterm`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`Id`);

--
-- Indeks untuk tabel `tblteacher_class`
--
ALTER TABLE `tblteacher_class`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_teacher_class` (`teacherId`,`classId`,`classArmId`),
  ADD KEY `idx_teacher` (`teacherId`),
  ADD KEY `idx_class` (`classId`,`classArmId`);

--
-- Indeks untuk tabel `tblterm`
--
ALTER TABLE `tblterm`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tblattendance`
--
ALTER TABLE `tblattendance`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT untuk tabel `tblclass`
--
ALTER TABLE `tblclass`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tblclassarms`
--
ALTER TABLE `tblclassarms`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tblmeetings`
--
ALTER TABLE `tblmeetings`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `tblsessionterm`
--
ALTER TABLE `tblsessionterm`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `tblteacher_class`
--
ALTER TABLE `tblteacher_class`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tblterm`
--
ALTER TABLE `tblterm`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
