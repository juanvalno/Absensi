-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2025 at 01:28 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-gaji`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensis`
--

CREATE TABLE `absensis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` char(36) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jadwalkerja_id` char(36) DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `total_jam` varchar(255) DEFAULT NULL,
  `keterlambatan` int(11) NOT NULL DEFAULT 0,
  `pulang_awal` int(11) NOT NULL DEFAULT 0,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Cuti','Libur') NOT NULL DEFAULT 'Hadir',
  `jenis_absensi_masuk` enum('Manual','Mesin') NOT NULL DEFAULT 'Manual',
  `mesinabsensi_masuk_id` bigint(20) UNSIGNED DEFAULT NULL,
  `jenis_absensi_pulang` enum('Manual','Mesin') NOT NULL DEFAULT 'Manual',
  `mesinabsensi_pulang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bagians`
--

CREATE TABLE `bagians` (
  `id` char(36) NOT NULL,
  `id_departemen` char(36) DEFAULT NULL,
  `name_bagian` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bagians`
--

INSERT INTO `bagians` (`id`, `id_departemen`, `name_bagian`, `created_at`, `updated_at`) VALUES
('050286f2-6af3-43b6-a907-225291209968', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', 'Product Research', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('0572e999-61dc-4b77-a749-b1252847cde5', '0d33ea07-1c73-44d7-96cb-e22e4e58383f', 'Training', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('0e91751a-bbdd-4ffa-9f97-f95f34d48ede', '80ea17f2-7e25-4f74-b0a6-5721a5ac9634', 'Brand Management', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('1ad7e8ca-ffa0-44ca-a253-6178c9f84919', '9e55583d-5f03-43bb-9613-c2c70174caca', 'Business Development', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('2e28a07e-b95f-4fa9-9b7f-b5ad3a630921', '2e47e408-385a-48aa-85b2-861d39cfefb2', 'Network Administration', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('3d0304b2-c58d-45ac-94d1-f47ae936d191', '80ea17f2-7e25-4f74-b0a6-5721a5ac9634', 'Digital Marketing', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('45af1d61-5098-4574-99bc-b00bb43857c7', '9e555abd-a95f-4bc6-ab61-0302b4e2988a', 'Customer Support', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('580b5614-ce20-449d-94c7-32c9606fd03e', '13f417af-423c-42f5-87b2-0a40886d98d4', 'Budgeting', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('5aa9668f-60ed-45c4-80e1-811d42e957a9', '2e47e408-385a-48aa-85b2-861d39cfefb2', 'Software Development', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('7bc367b4-0f3f-4705-bc3e-e07ef7611531', '8268a97c-33d4-4eeb-9056-77ad36036fe4', 'Production', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('922779fd-c456-477f-9df0-e34793ba5c67', '13f417af-423c-42f5-87b2-0a40886d98d4', 'Accounting', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('9e3af954-2ba0-4329-b9f5-f75e08b732ba', '0d33ea07-1c73-44d7-96cb-e22e4e58383f', 'Recruitment', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('9e556843-5538-424d-a438-47a72fe9ade7', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', 'PRIMARY', '2025-03-01 23:23:06', '2025-03-12 02:39:25'),
('9e556857-5649-49ce-9949-4149d24004ff', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', 'SKT', '2025-03-01 23:23:19', '2025-03-01 23:23:19'),
('9e556958-6dcb-42fa-882e-5c12a06f95dc', '9e55583d-5f03-43bb-9613-c2c70174caca', 'Bagian HRD', '2025-03-01 23:26:08', '2025-03-01 23:26:08'),
('acff90f3-be56-46b6-87b5-695dd3ede402', 'dc07c5b7-9cd8-4363-8b5c-b2b3deb540f0', 'Office Management', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('b424a4ac-5a92-4624-8574-e98505555f8d', '0d33ea07-1c73-44d7-96cb-e22e4e58383f', 'Payroll', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('c42d018d-fac1-423c-9a37-bb085e3ff7da', '8268a97c-33d4-4eeb-9056-77ad36036fe4', 'Quality Control', '2025-03-14 14:48:57', '2025-03-14 14:48:57'),
('fca123c9-4a49-4159-b64b-211622e30d33', 'a44cfe3a-cf78-4d58-81ce-a41b82215fd7', 'Legal Affairs', '2025-03-14 14:48:57', '2025-03-14 14:48:57');

-- --------------------------------------------------------

--
-- Table structure for table `cuti_karyawans`
--

CREATE TABLE `cuti_karyawans` (
  `id` char(36) NOT NULL,
  `id_karyawan` char(36) DEFAULT NULL,
  `jenis_cuti` enum('Izin','Cuti') NOT NULL,
  `tanggal_mulai_cuti` date NOT NULL,
  `tanggal_akhir_cuti` date NOT NULL,
  `jumlah_hari_cuti` int(11) NOT NULL,
  `cuti_disetujui` varchar(255) DEFAULT NULL,
  `master_cuti_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bukti` varchar(255) NOT NULL,
  `id_supervisor` char(36) DEFAULT NULL,
  `status_acc` enum('Menunggu Persetujuan','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu Persetujuan',
  `keterangan_tolak` text DEFAULT NULL,
  `tanggal_approval` timestamp NULL DEFAULT NULL,
  `approved_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cuti_karyawans`
--

INSERT INTO `cuti_karyawans` (`id`, `id_karyawan`, `jenis_cuti`, `tanggal_mulai_cuti`, `tanggal_akhir_cuti`, `jumlah_hari_cuti`, `cuti_disetujui`, `master_cuti_id`, `bukti`, `id_supervisor`, `status_acc`, `keterangan_tolak`, `tanggal_approval`, `approved_by`, `created_at`, `updated_at`) VALUES
('9e67a09e-9c1c-404c-bffa-acac0a1abe3e', '9e556bd4-4d16-4653-a564-714661526bb3', 'Izin', '2025-03-11', '2025-03-13', 3, NULL, 1, 'cuti-izin-1741679135.jpg', '9e556bd4-4d16-4653-a564-714661526bb3', 'Menunggu Persetujuan', NULL, NULL, NULL, '2025-03-11 00:45:35', '2025-03-11 00:45:35'),
('9e67a0d2-69ff-4dc7-9d6d-4663ba60d3c1', '9e556bd4-4d16-4653-a564-714661526bb3', 'Izin', '2025-03-11', '2025-03-13', 3, '3', 1, 'cuti-izin-1741679169.jpg', '9e556bd4-4d16-4653-a564-714661526bb3', 'Disetujui', NULL, '2025-03-13 15:19:34', NULL, '2025-03-11 00:46:09', '2025-03-13 15:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `departemens`
--

CREATE TABLE `departemens` (
  `id` char(36) NOT NULL,
  `name_departemen` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departemens`
--

INSERT INTO `departemens` (`id`, `name_departemen`, `created_at`, `updated_at`) VALUES
('0d33ea07-1c73-44d7-96cb-e22e4e58383f', 'Sales', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('13f417af-423c-42f5-87b2-0a40886d98d4', 'Research and Development', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('2e47e408-385a-48aa-85b2-861d39cfefb2', 'Operations', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('80ea17f2-7e25-4f74-b0a6-5721a5ac9634', 'Human Resources', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('8268a97c-33d4-4eeb-9056-77ad36036fe4', 'Customer Service', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('9e55583d-5f03-43bb-9613-c2c70174caca', 'HRD', '2025-03-01 22:38:18', '2025-03-01 22:38:18'),
('9e555abd-a95f-4bc6-ab61-0302b4e2988a', 'PURCHASING', '2025-03-01 22:45:17', '2025-03-01 22:45:17'),
('9e556803-1a16-47c7-bc1d-da5d44d2ea51', 'PRODUKSI', '2025-03-01 23:22:24', '2025-03-01 23:22:24'),
('9e705907-8a09-4cd6-9173-70015f3257d9', 'Test', '2025-03-15 08:47:51', '2025-03-15 08:47:51'),
('9e705912-b16d-4ef5-88a0-5188d4bbb691', 'test 1', '2025-03-15 08:47:59', '2025-03-15 08:47:59'),
('9e705924-6e37-4d6b-8427-062e10089644', 'tes 2', '2025-03-15 08:48:10', '2025-03-15 08:48:10'),
('a44cfe3a-cf78-4d58-81ce-a41b82215fd7', 'Information Technology', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('dc07c5b7-9cd8-4363-8b5c-b2b3deb540f0', 'Administration', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('e7f23a09-8441-45c8-b5bf-58a592461d83', 'Marketing', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('f0186412-37f3-4090-a940-83d4e7eb4ff8', 'Finance', '2025-03-14 14:44:22', '2025-03-14 14:44:22'),
('f4a7f1f3-fdab-4f79-8891-03d3c0ecac5b', 'Legal', '2025-03-14 14:44:22', '2025-03-14 14:44:22');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hariliburs`
--

CREATE TABLE `hariliburs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `nama_libur` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hariliburs`
--

INSERT INTO `hariliburs` (`id`, `tanggal`, `nama_libur`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, '2025-01-05', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(2, '2025-01-12', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(3, '2025-01-19', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(4, '2025-01-26', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(5, '2025-02-02', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(6, '2025-02-09', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(7, '2025-02-16', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(8, '2025-02-23', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(9, '2025-03-02', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(10, '2025-03-09', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(11, '2025-03-16', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(12, '2025-03-23', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(13, '2025-03-30', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(14, '2025-04-06', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(15, '2025-04-13', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(16, '2025-04-20', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(17, '2025-04-27', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(18, '2025-05-04', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(19, '2025-05-11', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(20, '2025-05-18', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(21, '2025-05-25', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(22, '2025-06-01', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(23, '2025-06-08', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(24, '2025-06-15', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(25, '2025-06-22', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(26, '2025-06-29', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(27, '2025-07-06', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(28, '2025-07-13', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(29, '2025-07-20', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(30, '2025-07-27', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(31, '2025-08-03', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:03', '2025-03-15 19:15:03'),
(32, '2025-08-10', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(33, '2025-08-17', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(34, '2025-08-24', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(35, '2025-08-31', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(36, '2025-09-07', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(37, '2025-09-14', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(38, '2025-09-21', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(39, '2025-09-28', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(40, '2025-10-05', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(41, '2025-10-12', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(42, '2025-10-19', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(43, '2025-10-26', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(44, '2025-11-02', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(45, '2025-11-09', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(46, '2025-11-16', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(47, '2025-11-23', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(48, '2025-11-30', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(49, '2025-12-07', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(50, '2025-12-14', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(51, '2025-12-21', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04'),
(52, '2025-12-28', 'Hari Minggu', 'Hari Minggu', '2025-03-15 19:15:04', '2025-03-15 19:15:04');

-- --------------------------------------------------------

--
-- Table structure for table `jabatans`
--

CREATE TABLE `jabatans` (
  `id` char(36) NOT NULL,
  `name_jabatan` varchar(255) NOT NULL,
  `gaji_pokok` int(11) NOT NULL,
  `premi` int(11) NOT NULL,
  `tunjangan_jabatan` int(11) NOT NULL,
  `uang_lembur_biasa` int(11) NOT NULL,
  `uang_lembur_libur` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jabatans`
--

INSERT INTO `jabatans` (`id`, `name_jabatan`, `gaji_pokok`, `premi`, `tunjangan_jabatan`, `uang_lembur_biasa`, `uang_lembur_libur`, `created_at`, `updated_at`) VALUES
('9e556489-36dc-4c4e-8790-cd554257d853', 'Manager', 5000000, 300000, 1000000, 100000, 150000, '2025-03-01 23:12:41', '2025-03-01 23:16:34'),
('9e557da8-54fb-4070-81ae-0cf559e9b22f', 'HARIAN', 2500000, 50000, 10000, 20000, 30000, '2025-03-02 00:22:55', '2025-03-02 00:22:55'),
('9e615d7c-81d1-4d85-ac81-da80dff91930', 'Staff', 2500000, 0, 0, 0, 0, '2025-03-07 22:02:54', '2025-03-14 23:32:59'),
('9e69dcea-cab6-4ab2-92a3-4bfee0f4a3e1', 'ASISTEN MANAGER', 1, 1, 0, 1, 1, '2025-03-12 03:25:51', '2025-03-12 03:27:11');

-- --------------------------------------------------------

--
-- Table structure for table `jadwalkerjas`
--

CREATE TABLE `jadwalkerjas` (
  `id` char(36) NOT NULL,
  `tanggal` date NOT NULL,
  `shift_id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwalkerjas`
--

INSERT INTO `jadwalkerjas` (`id`, `tanggal`, `shift_id`, `karyawan_id`, `created_at`, `updated_at`) VALUES
('0bf0d9be-a2b9-4f90-bef2-a60e1fcaf263', '2025-03-18', 6, '9e6f085f-eef4-4d67-baaf-65791ba99cf7', '2025-03-16 06:51:05', '2025-03-16 06:51:05'),
('20d456e4-ffcd-4f26-a864-ce953e694dc2', '2025-03-18', 7, '9e696c8c-5893-40b5-9e9c-c6e02809cd82', '2025-03-16 21:40:03', '2025-03-16 21:40:03'),
('2f25d25d-528e-4378-ac8a-1f0b6afd1a74', '2025-03-20', 7, '9e696c8c-5893-40b5-9e9c-c6e02809cd82', '2025-03-16 21:40:03', '2025-03-16 21:40:03'),
('38659a1d-cc32-40bd-8095-13c0596ff436', '2025-03-18', 7, '9e696bff-742a-485c-9326-81ed50d907f0', '2025-03-16 21:40:03', '2025-03-16 21:40:03'),
('426bb881-9baa-413d-80a2-a675623a99f9', '2025-03-16', 6, '9e69e75c-a8e9-4b08-b886-74fd6f04e78e', '2025-03-16 06:51:05', '2025-03-16 06:51:05'),
('6445c62d-ef53-43ff-ae44-c036bf9424e2', '2025-03-19', 7, '9e696bff-742a-485c-9326-81ed50d907f0', '2025-03-16 21:40:03', '2025-03-16 21:40:03'),
('668c617d-77be-4896-bb30-5e9b998396ef', '2025-03-17', 6, '9e69e75c-a8e9-4b08-b886-74fd6f04e78e', '2025-03-16 06:51:05', '2025-03-16 06:51:05'),
('6f74b27e-24a3-47d9-b681-2b51f72b66ef', '2025-03-18', 6, '9e69e75c-a8e9-4b08-b886-74fd6f04e78e', '2025-03-16 06:51:05', '2025-03-16 06:51:05'),
('88ad72f4-8e5d-48ac-b850-40c4bf16568f', '2025-03-20', 7, '9e696bff-742a-485c-9326-81ed50d907f0', '2025-03-16 21:40:03', '2025-03-16 21:40:03'),
('c453f2bf-a5ff-4c73-bc8a-3ae68c9ae659', '2025-03-19', 7, '9e696c8c-5893-40b5-9e9c-c6e02809cd82', '2025-03-16 21:40:03', '2025-03-16 21:40:03'),
('cc713289-e010-4604-bcfc-c110218372d7', '2025-03-21', 7, '9e696c8c-5893-40b5-9e9c-c6e02809cd82', '2025-03-16 21:40:03', '2025-03-16 21:40:03'),
('cd6a6e31-5301-4f6d-b034-bb783d797e76', '2025-03-21', 9, '9e696bff-742a-485c-9326-81ed50d907f0', '2025-03-16 21:40:03', '2025-03-16 21:53:11'),
('cf5e76a6-b50c-4cce-96e2-6c121d4af6c7', '2025-03-16', 6, '9e6f085f-eef4-4d67-baaf-65791ba99cf7', '2025-03-16 06:51:05', '2025-03-16 06:51:05'),
('d1a1ac31-ac63-4aa5-b86b-24c370f75ac0', '2025-03-17', 6, '9e6f085f-eef4-4d67-baaf-65791ba99cf7', '2025-03-16 06:51:05', '2025-03-16 06:51:05');

-- --------------------------------------------------------

--
-- Table structure for table `karyawans`
--

CREATE TABLE `karyawans` (
  `id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nik_karyawan` varchar(255) NOT NULL,
  `nama_karyawan` varchar(255) NOT NULL,
  `foto_karyawan` varchar(255) DEFAULT NULL,
  `statuskaryawan` enum('Bulanan','Harian','Borongan') NOT NULL,
  `id_departemen` char(36) DEFAULT NULL,
  `id_bagian` char(36) DEFAULT NULL,
  `tgl_awalmmasuk` date NOT NULL,
  `tahun_keluar` date DEFAULT NULL,
  `id_jabatan` char(36) DEFAULT NULL,
  `id_profesi` char(36) DEFAULT NULL,
  `nik` varchar(16) NOT NULL,
  `kk` varchar(16) NOT NULL,
  `statuskawin` enum('Lajang','Kawin','Cerai Hidup','Cerai Mati') NOT NULL,
  `pendidikan_terakhir` enum('SD/MI','SMP/MTS','SMA/SMK/MA','S1','S2','S3','Lainnya') NOT NULL,
  `id_programstudi` char(36) DEFAULT NULL,
  `no_hp` varchar(16) NOT NULL,
  `ortu_bapak` varchar(255) NOT NULL,
  `ortu_ibu` varchar(255) NOT NULL,
  `ukuran_kemeja` enum('S','M','L','XL','XXL','XXXL') NOT NULL,
  `ukuran_celana` varchar(5) NOT NULL,
  `ukuran_sepatu` varchar(5) NOT NULL,
  `jml_anggotakk` varchar(5) NOT NULL,
  `upload_ktp` varchar(255) DEFAULT NULL,
  `nama_bank` varchar(100) DEFAULT NULL,
  `nomor_rekening` varchar(50) DEFAULT NULL,
  `nama_pemilik_rekening` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karyawans`
--

INSERT INTO `karyawans` (`id`, `user_id`, `nik_karyawan`, `nama_karyawan`, `foto_karyawan`, `statuskaryawan`, `id_departemen`, `id_bagian`, `tgl_awalmmasuk`, `tahun_keluar`, `id_jabatan`, `id_profesi`, `nik`, `kk`, `statuskawin`, `pendidikan_terakhir`, `id_programstudi`, `no_hp`, `ortu_bapak`, `ortu_ibu`, `ukuran_kemeja`, `ukuran_celana`, `ukuran_sepatu`, `jml_anggotakk`, `upload_ktp`, `nama_bank`, `nomor_rekening`, `nama_pemilik_rekening`, `created_at`, `updated_at`, `deleted_at`) VALUES
('9e556bd4-4d16-4653-a564-714661526bb3', 3, '202503001', 'BASUKI', 'basuki-1740900350.jpg', 'Bulanan', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', '9e556843-5538-424d-a438-47a72fe9ade7', '2025-03-02', NULL, '9e556489-36dc-4c4e-8790-cd554257d853', '9e55607b-7e32-4fd9-9334-1b6c64d525f8', '1234567891', '12345678911', 'Lajang', 'SD/MI', NULL, '081325675595', 'Sujud', 'Sapar', 'L', '40', '39', '3', 'ktp-basuki-1740897184.jpg', NULL, NULL, NULL, '2025-03-01 23:33:04', '2025-03-12 05:53:37', NULL),
('9e557e7b-8ea2-4749-b07b-d48242fea96e', NULL, '202503002', 'DIDIK', 'didik-1740900314.jpg', 'Harian', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', '9e556857-5649-49ce-9949-4149d24004ff', '2025-03-02', '2025-03-11', '9e557da8-54fb-4070-81ae-0cf559e9b22f', '9e5561fd-5db7-487e-884f-007eed204321', '0123456789123456', '0123456789123456', 'Kawin', 'SMP/MTS', NULL, '0813456255512', 'JAGUAR', 'KASMINI', 'L', '40', '39', '11', 'ktp-didik-1740900314.jpg', NULL, NULL, NULL, '2025-03-02 00:25:14', '2025-03-11 14:30:28', NULL),
('9e68d7e2-a69d-4b33-86db-b2b62ed4be37', NULL, '202503003', 'Yuda Putra', 'yuda-putra-1741731357.jpg', 'Bulanan', '9e55583d-5f03-43bb-9613-c2c70174caca', '9e556958-6dcb-42fa-882e-5c12a06f95dc', '2025-03-11', NULL, '9e615d7c-81d1-4d85-ac81-da80dff91930', '9e55607b-7e32-4fd9-9334-1b6c64d525f8', '331223322118887', '331223322118888', 'Kawin', 'SMA/SMK/MA', '9e68d265-ca66-45c9-a120-cc47ebda54c9', '089789678567', 'Rudi', 'Risa', 'M', '32', '43', '3', 'ktp-yuda-putra-1741731357.jpg', NULL, NULL, NULL, '2025-03-11 15:15:57', '2025-03-11 15:15:57', NULL),
('9e696bff-742a-485c-9326-81ed50d907f0', NULL, '202503004', 'Vania Risyatika Nurjannah', 'vania-risyatika-nurjannah-1741756206.jpg', 'Bulanan', '9e55583d-5f03-43bb-9613-c2c70174caca', '9e556958-6dcb-42fa-882e-5c12a06f95dc', '2025-03-12', NULL, '9e615d7c-81d1-4d85-ac81-da80dff91930', '9e55607b-7e32-4fd9-9334-1b6c64d525f8', '331223322118882', '331223322118883', 'Lajang', 'SMA/SMK/MA', '9e68d265-ca66-45c9-a120-cc47ebda54c9', '089890890890', 'Ayah', 'Ibu', 'L', '32', '44', '5', 'ktp-vania-risyatika-nurjannah-1741756206.jpg', NULL, NULL, NULL, '2025-03-11 22:10:06', '2025-03-11 22:10:06', NULL),
('9e696c8c-5893-40b5-9e9c-c6e02809cd82', NULL, '202503005', 'Intan Noor Aini', 'intan-noor-aini-1741756298.jpg', 'Harian', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', '9e556843-5538-424d-a438-47a72fe9ade7', '2025-03-12', NULL, '9e557da8-54fb-4070-81ae-0cf559e9b22f', '9e55607b-7e32-4fd9-9334-1b6c64d525f8', '331223322118886', '331223322118885', 'Lajang', 'SMA/SMK/MA', '9e68d265-ca66-45c9-a120-cc47ebda54c9', '08787687676', 'Ayah Intan', 'Ibu Intan', 'XL', '31', '34', '4', 'ktp-intan-noor-aini-1741756298.jpg', NULL, NULL, NULL, '2025-03-11 22:11:38', '2025-03-11 22:11:38', NULL),
('9e69c984-64f5-415b-bf35-7b7ab09ff6a4', NULL, '202503006', 'KARMAIN', 'karmain-1741771896.jpg', 'Harian', '9e555abd-a95f-4bc6-ab61-0302b4e2988a', '45af1d61-5098-4574-99bc-b00bb43857c7', '2025-03-12', NULL, '9e615d7c-81d1-4d85-ac81-da80dff91930', '9e55644a-fc17-471b-b40e-b4d4c339ef00', '6025916033742121', '1773306827513810', 'Cerai Hidup', 'SMA/SMK/MA', '9e556def-9c0d-4560-87d7-86f1f8d52012', '081325675595', 'Parjo', 'tuminem', 'L', '40', '40', '7', 'ktp-karmain-1741771896.jpg', NULL, NULL, NULL, '2025-03-12 02:31:36', '2025-03-16 05:35:30', NULL),
('9e69cfab-4083-42e2-b36a-cfbb010c5b79', NULL, '202503007', 'KOWALSKI', 'kowalski-1741772928.jpg', 'Borongan', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', '9e556843-5538-424d-a438-47a72fe9ade7', '2025-03-12', NULL, '9e556489-36dc-4c4e-8790-cd554257d853', '9e55607b-7e32-4fd9-9334-1b6c64d525f8', '123', '456', 'Cerai Hidup', 'SD/MI', NULL, '0830218321932', 'Randu', 'Umi Aning', 'L', '40', '40', '5', 'ktp-kowalski-1741772928.jpg', NULL, NULL, NULL, '2025-03-12 02:48:48', '2025-03-12 02:48:48', NULL),
('9e69e75c-a8e9-4b08-b886-74fd6f04e78e', NULL, '202503008', 'Atmojo', 'atmojo-1741776903.jpg', 'Harian', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', '9e556843-5538-424d-a438-47a72fe9ade7', '2025-03-12', NULL, '9e615d7c-81d1-4d85-ac81-da80dff91930', '9e557e71-2ebe-49b7-9496-307361749ad8', '6112876497897578', '6616314070270517', 'Kawin', 'SMP/MTS', NULL, '089325675596', 'Tarzan', 'Marjan', 'L', '40', '40', '4', 'ktp-atmojo-1741776903.jpg', 'BNI', '0993082108', 'Atmojo Sanjoyo', '2025-03-12 03:55:03', '2025-03-16 05:35:04', NULL),
('9e6a056c-36ef-4f45-afa8-7d20de1be026', NULL, '202503009', 'Hambalang', 'hambalang-1741781946.jpg', 'Bulanan', '9e556803-1a16-47c7-bc1d-da5d44d2ea51', '9e556843-5538-424d-a438-47a72fe9ade7', '2025-03-12', NULL, '9e69dcea-cab6-4ab2-92a3-4bfee0f4a3e1', '9e55644a-fc17-471b-b40e-b4d4c339ef00', '4293112656439281', '458326873858851', 'Kawin', 'S1', '9e556db9-cabc-4010-aceb-a17ea6a71a22', '0876515443551', 'Nasmoco', 'Humaniora', 'L', '40', '40', '3', 'ktp-hambalang-1741781946.jpg', 'BCA', '123456547', 'Bolang', '2025-03-12 05:19:07', '2025-03-12 05:19:07', NULL),
('9e6f085f-eef4-4d67-baaf-65791ba99cf7', NULL, '202503010', 'Adi', 'adi-1741997190.jpg', 'Bulanan', 'dc07c5b7-9cd8-4363-8b5c-b2b3deb540f0', 'acff90f3-be56-46b6-87b5-695dd3ede402', '2025-03-15', NULL, '9e615d7c-81d1-4d85-ac81-da80dff91930', '9e5561fd-5db7-487e-884f-007eed204321', '3312233221188802', '3312233221188812', 'Lajang', 'SMA/SMK/MA', '9e68d265-ca66-45c9-a120-cc47ebda54c9', '08787687678', 'Rudi', 'Ibu Intan', 'L', '31', '43', '4', 'ktp-adi-1741997190.jpg', 'BCA', '123123123', 'Adi', '2025-03-14 17:06:30', '2025-03-14 17:28:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kuota_cuti_tahunans`
--

CREATE TABLE `kuota_cuti_tahunans` (
  `id` char(36) NOT NULL,
  `karyawan_id` char(36) NOT NULL,
  `tahun` int(11) NOT NULL,
  `kuota_awal` int(11) NOT NULL,
  `kuota_digunakan` int(11) NOT NULL DEFAULT 0,
  `kuota_sisa` int(11) NOT NULL,
  `tanggal_expired` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kuota_cuti_tahunans`
--

INSERT INTO `kuota_cuti_tahunans` (`id`, `karyawan_id`, `tahun`, `kuota_awal`, `kuota_digunakan`, `kuota_sisa`, `tanggal_expired`, `keterangan`, `created_at`, `updated_at`) VALUES
('9e6cda66-beb3-472c-a3fb-1d615ab82cae', '9e556bd4-4d16-4653-a564-714661526bb3', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cda66-d65f-4291-ad74-c52827d4b9d8', '9e557e7b-8ea2-4749-b07b-d48242fea96e', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cda66-d831-4186-9406-a2ef0f6259a5', '9e6a056c-36ef-4f45-afa8-7d20de1be026', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cda66-dbea-427a-a23e-adefd804fde5', '9e696c8c-5893-40b5-9e9c-c6e02809cd82', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cda66-de1c-4ce4-8623-3b829224a79a', '9e69c984-64f5-415b-bf35-7b7ab09ff6a4', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cda66-dffa-4b22-a56d-b43bbbba3e69', '9e69cfab-4083-42e2-b36a-cfbb010c5b79', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cda66-e248-40a3-967f-f9c3cb21de70', '9e696bff-742a-485c-9326-81ed50d907f0', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cda66-e416-471d-b144-ad79763c8531', '9e68d7e2-a69d-4b33-86db-b2b62ed4be37', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 15:06:18', '2025-03-13 15:06:18'),
('9e6cdf6a-a635-4b17-b49b-f3de16922598', '9e69e75c-a8e9-4b08-b886-74fd6f04e78e', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-ac29-421c-9eb4-b9d453733d85', '9e556bd4-4d16-4653-a564-714661526bb3', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-ae20-425a-b4e3-8032b64b19f7', '9e557e7b-8ea2-4749-b07b-d48242fea96e', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-b032-451d-85c2-101c6eaabbce', '9e6a056c-36ef-4f45-afa8-7d20de1be026', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-b208-408b-b41c-78a44f67efa7', '9e696c8c-5893-40b5-9e9c-c6e02809cd82', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-b3e3-4398-a99d-16b0ee71d778', '9e69c984-64f5-415b-bf35-7b7ab09ff6a4', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-b5ca-4153-ae38-5cd694c2a654', '9e69cfab-4083-42e2-b36a-cfbb010c5b79', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-b7a0-4070-856b-d9905da5bea5', '9e696bff-742a-485c-9326-81ed50d907f0', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('9e6cdf6a-b999-4580-ad7c-1de0a6a958e3', '9e68d7e2-a69d-4b33-86db-b2b62ed4be37', 2026, 12, 0, 12, NULL, NULL, '2025-03-13 15:20:19', '2025-03-13 15:20:19'),
('f35506b7-37cb-4c42-97d3-3426f61cc071', '9e69e75c-a8e9-4b08-b886-74fd6f04e78e', 2025, 12, 0, 12, NULL, NULL, '2025-03-13 14:45:07', '2025-03-13 14:45:07');

-- --------------------------------------------------------

--
-- Table structure for table `lemburs`
--

CREATE TABLE `lemburs` (
  `id` char(36) NOT NULL,
  `karyawan_id` char(36) DEFAULT NULL,
  `supervisor_id` char(36) DEFAULT NULL,
  `tanggal_lembur` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_lembur` char(255) DEFAULT NULL,
  `lembur_disetujui` char(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jenis_lembur` enum('Hari Kerja','Hari Libur') NOT NULL,
  `status` enum('Menunggu Persetujuan','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu Persetujuan',
  `keterangan_tolak` text DEFAULT NULL,
  `tanggal_approval` timestamp NULL DEFAULT NULL,
  `approved_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mastercutis`
--

CREATE TABLE `mastercutis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uraian` varchar(255) NOT NULL,
  `is_bulanan` tinyint(4) NOT NULL DEFAULT 0,
  `cuti_max` varchar(255) DEFAULT NULL,
  `izin_max` varchar(255) DEFAULT NULL,
  `is_potonggaji` tinyint(4) NOT NULL DEFAULT 0,
  `nominal` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mastercutis`
--

INSERT INTO `mastercutis` (`id`, `uraian`, `is_bulanan`, `cuti_max`, `izin_max`, `is_potonggaji`, `nominal`, `created_at`, `updated_at`) VALUES
(1, 'Sakit', 0, '3', '3', 0, NULL, '2025-03-01 21:41:25', '2025-03-01 21:41:25'),
(2, 'Menikah', 0, '3', '3', 0, NULL, '2025-03-11 07:57:22', '2025-03-11 07:57:22'),
(3, 'Menikah', 1, '3', '3', 0, NULL, '2025-03-11 21:40:25', '2025-03-11 21:40:25'),
(4, 'Cuti untuk Karyawan Bulanan', 1, '2', '2', 0, NULL, '2025-03-15 16:25:38', '2025-03-15 16:25:38');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `text` varchar(255) NOT NULL,
  `type` enum('header','menu') NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `permission` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `text`, `type`, `icon`, `route`, `parent_id`, `permission`, `order`, `created_at`, `updated_at`) VALUES
(1, 'MAIN MENU', 'header', NULL, NULL, NULL, NULL, 1, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(2, 'Dashboard', 'menu', 'fas fa-tachometer-alt', 'admin.dashboard', NULL, NULL, 2, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(4, 'Kelola Pengguna', 'menu', 'fas fa-users', 'users.index', 30, 'users.view', 4, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(5, 'Kelola Peran Pengguna', 'menu', NULL, 'roles.index', 30, 'roles.view', 5, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(6, 'Kelola Hak Akses', 'menu', NULL, 'permissions.index', 30, 'permissions.view', 6, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(8, 'Menu Management', 'menu', 'fas fa-bars', 'menu.index', 28, 'menu.view', 34, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(9, 'Role Access', 'menu', 'fas fa-lock', 'role-access.index', 28, 'roles.view', 33, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(10, 'User Access', 'menu', 'fas fa-user-lock', 'user-access.index', 28, 'users.view', 32, '2025-03-01 21:37:54', '2025-03-13 14:31:32'),
(12, 'Master Cuti', 'menu', NULL, 'mastercutis.index', 29, 'mastercutis.view', 14, '2025-03-01 21:39:25', '2025-03-13 14:31:32'),
(13, 'Master Shift', 'menu', NULL, 'shifts.index', 29, 'shifts.view', 13, '2025-03-01 21:52:12', '2025-03-13 14:31:32'),
(14, 'Master Bagian', 'menu', NULL, 'bagians.index', 29, 'bagians.view', 10, '2025-03-01 22:12:23', '2025-03-13 14:31:32'),
(15, 'Master Departemen', 'menu', NULL, 'departemens.index', 29, 'departemens.view', 9, '2025-03-01 22:13:20', '2025-03-13 14:31:32'),
(16, 'Master Program Studi', 'menu', NULL, 'program_studis.index', 29, 'program_studi.view', 15, '2025-03-01 22:14:16', '2025-03-13 14:31:32'),
(17, 'Master Karyawan', 'menu', NULL, 'karyawans.index', 29, 'karyawans.view', 8, '2025-03-01 22:15:14', '2025-03-13 14:31:32'),
(19, 'Ajuan Cuti Karyawan', 'menu', 'fas fa-users', 'cuti_karyawans.index', 31, 'cuti_karyawan.view', 18, '2025-03-01 22:23:36', '2025-03-13 14:31:32'),
(20, 'Master Jabatan', 'menu', NULL, 'jabatans.index', 29, 'jabatans.view', 11, '2025-03-01 22:32:42', '2025-03-13 14:31:32'),
(21, 'Master Profesi', 'menu', NULL, 'profesis.index', 29, 'profesis.view', 12, '2025-03-01 22:33:25', '2025-03-13 14:31:32'),
(22, 'Ajuan Lembur', 'menu', NULL, 'lemburs.index', 31, 'lemburs.view', 19, '2025-03-02 00:21:14', '2025-03-13 14:31:32'),
(24, 'Jadwal Kerja', 'menu', NULL, 'jadwalkerjas.index', 32, 'jadwalkerjas.view', 23, '2025-03-02 00:49:41', '2025-03-13 14:31:32'),
(25, 'Master Hari Libur', 'menu', NULL, 'hariliburs.index', 29, 'hariliburs.view', 16, '2025-03-02 01:03:14', '2025-03-13 14:31:32'),
(26, 'Data Mesin Absensi', 'menu', NULL, 'mesinabsensis.index', 32, 'mesin_absensi.view', 25, '2025-03-02 01:32:46', '2025-03-13 14:31:32'),
(28, 'Settings', 'menu', NULL, NULL, NULL, NULL, 31, '2025-03-06 14:12:31', '2025-03-13 14:31:32'),
(29, 'Master Data', 'menu', 'fa fa-bars', NULL, NULL, NULL, 7, '2025-03-06 14:47:17', '2025-03-13 14:31:32'),
(30, 'Pengguna', 'menu', 'fas fa-users', NULL, NULL, NULL, 3, '2025-03-06 14:53:01', '2025-03-13 14:31:32'),
(31, 'Ajuan Data', 'menu', 'fa fa-paperclip', NULL, NULL, NULL, 17, '2025-03-06 14:56:02', '2025-03-13 14:31:32'),
(32, 'Absensi', 'menu', 'fas fa-users', NULL, NULL, NULL, 20, '2025-03-06 14:59:15', '2025-03-13 14:31:32'),
(33, 'SETTINGS', 'header', NULL, NULL, NULL, NULL, 30, '2025-03-06 15:00:34', '2025-03-13 14:31:32'),
(34, 'Kelola Uang Tunggu', 'menu', NULL, 'uangtunggus.index', 32, 'uang_tunggu.view', 24, '2025-03-07 12:38:10', '2025-03-13 14:31:32'),
(35, 'Kelola Absensi', 'menu', NULL, 'absensis.index', 32, 'absensis.view', 21, '2025-03-07 13:46:14', '2025-03-13 14:31:32'),
(36, 'Master Potongan', 'menu', NULL, 'potongans.index', 32, 'potongans.view', 27, '2025-03-10 06:42:57', '2025-03-13 14:31:32'),
(38, 'Master Periode Gaji', 'menu', NULL, 'periodegaji.index', 32, 'periode_gaji.view', 26, '2025-03-10 07:46:05', '2025-03-13 14:31:32'),
(39, 'Penggajian', 'menu', NULL, NULL, NULL, NULL, 28, '2025-03-10 17:03:02', '2025-03-13 14:31:32'),
(40, 'Kelola Penggajian', 'menu', NULL, 'penggajian.index', 39, 'penggajians.view', 29, '2025-03-10 17:03:35', '2025-03-13 14:31:32'),
(41, 'Kuota Cuti Tahunan', 'menu', NULL, 'kuota-cuti.index', 32, 'kuota_cuti_tahunan.view', 22, '2025-03-13 14:31:16', '2025-03-13 14:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `mesinabsensis`
--

CREATE TABLE `mesinabsensis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat_ip` varchar(255) NOT NULL,
  `kunci_komunikasi` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `status_aktif` tinyint(4) NOT NULL DEFAULT 1,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mesinabsensis`
--

INSERT INTO `mesinabsensis` (`id`, `nama`, `alamat_ip`, `kunci_komunikasi`, `lokasi`, `status_aktif`, `keterangan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'Mesin 1', '192.168.0.199', '0', 'Depan new', 1, NULL, '2025-03-09 06:47:30', '2025-03-13 09:46:08', NULL),
(4, 'Mesin 2', '192.168.0.198', '0', 'Tengah', 1, NULL, '2025-03-09 13:09:12', '2025-03-13 09:49:47', '2025-03-13 09:49:47'),
(5, 'Mesin 2', '192.168.0.198', '0', 'Tengah', 1, NULL, '2025-03-13 09:50:02', '2025-03-13 09:52:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_02_23_220250_create_permission_tables', 1),
(6, '2025_02_23_231056_create_menus_table', 1),
(7, '2025_03_01_160341_create_departemens_table', 1),
(8, '2025_03_01_160853_create_bagians_table', 1),
(9, '2025_03_01_225636_modify_from_bagians_table', 1),
(10, '2025_03_01_230152_create_program_studis_table', 1),
(11, '2025_03_02_000316_create_hariliburs_table', 1),
(12, '2025_03_02_003056_create_profesis_table', 1),
(13, '2025_03_02_004552_create_jabatans_table', 1),
(14, '2025_03_02_011703_create_karyawans_table', 1),
(15, '2025_03_02_041959_create_mastershifts_table', 1),
(16, '2025_03_02_044548_create_shifts_table', 2),
(17, '2025_03_02_050109_create_cuti_karyawans_table', 3),
(18, '2025_03_02_053646_create_lemburs_table', 4),
(19, '2025_03_02_072956_create_jadwalkerjas_table', 5),
(20, '2025_03_02_082521_create_mesinabsensis_table', 6),
(24, '2025_03_07_192803_create_uangtunggus_table', 7),
(25, '2025_03_07_202602_create_absensis_table', 7),
(28, '2025_03_10_131938_create_potongans_table', 8),
(29, '2025_03_10_140212_create_periode_gaji_table', 9),
(31, '2025_03_10_140338_create_periodegajis_table', 10),
(32, '2025_03_10_142946_create_periodegajis_table', 11),
(33, '2025_03_10_231023_create_penggajians_table', 12),
(34, '2025_03_11_073701_create_kuota_cuti_tahunans_table', 13),
(35, '2025_03_11_075911_add_user_id_to_karyawans_table', 14),
(36, '2025_03_11_085330_add_deleted_at_to_karyawans_table', 15),
(37, '2025_03_12_103334_modify_add_from_karyawans', 16),
(38, '2025_03_12_132826_add_education_level_to_program_studis_table', 17),
(39, '2025_03_13_163113_create_attendance_logs_table', 18),
(40, '2025_03_13_163114_create_kuota_cuti_tahunans_table', 19),
(41, '2025_03_02_000000_add_education_type_to_program_studis_table', 20),
(44, '2025_03_15_043724_add_verification_fields_to_penggajians_table', 21),
(45, '2025_03_15_043777_add_verification_fields_to_penggajians_table', 22),
(46, '2025_03_15_050337_create_verifikasi_penggajian_table', 23),
(47, '2024_01_20_create_special_permissions_table', 24);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 3),
(1, 'App\\Models\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penggajians`
--

CREATE TABLE `penggajians` (
  `id` char(36) NOT NULL,
  `id_periode` bigint(20) UNSIGNED NOT NULL,
  `id_karyawan` char(36) NOT NULL,
  `periode_awal` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL,
  `tunjangan` decimal(15,2) DEFAULT 0.00,
  `detail_tunjangan` longtext DEFAULT NULL,
  `potongan` decimal(15,2) DEFAULT 0.00,
  `detail_potongan` longtext DEFAULT NULL,
  `detail_departemen` longtext DEFAULT NULL,
  `gaji_bersih` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status_verifikasi` enum('Menunggu Verifikasi','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi',
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `keterangan_verifikasi` text DEFAULT NULL,
  `verifikasi_oleh` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penggajians`
--

INSERT INTO `penggajians` (`id`, `id_periode`, `id_karyawan`, `periode_awal`, `periode_akhir`, `gaji_pokok`, `tunjangan`, `detail_tunjangan`, `potongan`, `detail_potongan`, `detail_departemen`, `gaji_bersih`, `created_at`, `updated_at`, `status_verifikasi`, `tanggal_verifikasi`, `keterangan_verifikasi`, `verifikasi_oleh`) VALUES
('9e6f949d-2e66-491c-8bdc-d902ba116222', 1, '9e696bff-742a-485c-9326-81ed50d907f0', '2025-01-25', '2025-01-26', 2500000.00, 500000.00, '[{\"nama\":\"Tunjangan Profesi\",\"nominal\":\"500000\"}]', 158333.00, '[{\"nama\":\"BPJS Kesehatan\",\"nominal\":\"25000\"},{\"nama\":\"BPJS Ketenagakerjaan\",\"nominal\":\"50000\"},{\"nama\":\"Potongan Ketidakhadiran\",\"nominal\":\"83333\"}]', '{\"departemen\":\"HRD\",\"bagian\":\"Bagian HRD\",\"jabatan\":\"Staff\",\"profesi\":\"Staff Level 1\"}', 2841667.00, '2025-03-14 23:38:38', '2025-03-14 23:38:38', 'Menunggu Verifikasi', NULL, NULL, NULL),
('9e6fb416-17b3-486b-84d1-37304c5474a0', 20, '9e696bff-742a-485c-9326-81ed50d907f0', '2025-01-20', '2025-01-26', 2500000.00, 545000.00, '[{\"nama\":\"Tunjangan Profesi\",\"nominal\":\"500000\"},{\"nama\":\"Uang Makan\",\"nominal\":\"45000\"}]', 575000.00, '[{\"nama\":\"BPJS Kesehatan\",\"nominal\":\"25000\"},{\"nama\":\"BPJS Ketenagakerjaan\",\"nominal\":\"50000\"},{\"nama\":\"Potongan Ketidakhadiran\",\"nominal\":\"500000\"}]', '{\"departemen\":\"HRD\",\"bagian\":\"Bagian HRD\",\"jabatan\":\"Staff\",\"profesi\":\"Staff Level 1\"}', 2470000.00, '2025-03-15 01:06:39', '2025-03-15 01:06:39', 'Menunggu Verifikasi', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `periodegajis`
--

CREATE TABLE `periodegajis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_periode` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periodegajis`
--

INSERT INTO `periodegajis` (`id`, `nama_periode`, `tanggal_mulai`, `tanggal_selesai`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Gaji Bulanan January 2025', '2025-01-25', '2025-01-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:29', '2025-03-15 00:08:03'),
(4, 'Gaji Bulanan April 2025', '2025-04-25', '2025-04-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(5, 'Gaji Bulanan May 2025', '2025-05-25', '2025-05-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(6, 'Gaji Bulanan June 2025', '2025-06-25', '2025-06-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(7, 'Gaji Bulanan July 2025', '2025-07-25', '2025-07-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(8, 'Gaji Bulanan August 2025', '2025-08-25', '2025-08-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(9, 'Gaji Bulanan September 2025', '2025-09-25', '2025-09-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-11 19:57:38'),
(10, 'Gaji Bulanan October 2025', '2025-10-25', '2025-10-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(11, 'Gaji Bulanan November 2025', '2025-11-25', '2025-11-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(12, 'Gaji Bulanan December 2025', '2025-12-25', '2025-12-26', 'nonaktif', 'Periode gaji bulanan yang dibuat otomatis', '2025-03-10 08:18:30', '2025-03-10 08:19:57'),
(13, 'Gaji Mingguan 03 - 09 Mar 2025', '2025-03-03', '2025-03-09', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-10 15:47:34', '2025-03-10 15:47:34'),
(14, 'Gaji Mingguan 10 - 16 Mar 2025', '2025-03-10', '2025-03-16', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-10 15:47:34', '2025-03-10 15:47:34'),
(15, 'Gaji Mingguan 17 - 23 Mar 2025', '2025-03-17', '2025-03-23', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-10 15:47:34', '2025-03-10 15:47:34'),
(16, 'Gaji Mingguan 24 - 30 Mar 2025', '2025-03-24', '2025-03-30', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-10 15:47:34', '2025-03-10 15:47:34'),
(17, 'Gaji Mingguan 31 - 06 Apr 2025', '2025-03-31', '2025-04-06', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-10 15:47:34', '2025-03-10 15:47:34'),
(18, 'Gaji Mingguan 06 - 12 Jan 2025', '2025-01-06', '2025-01-12', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-11 19:56:22', '2025-03-11 19:56:22'),
(19, 'Gaji Mingguan 13 - 19 Jan 2025', '2025-01-13', '2025-01-19', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-11 19:56:22', '2025-03-11 19:56:22'),
(20, 'Gaji Mingguan 20 - 26 Jan 2025', '2025-01-20', '2025-01-26', 'aktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-11 19:56:22', '2025-03-15 00:08:03'),
(21, 'Gaji Mingguan 27 - 02 Feb 2025', '2025-01-27', '2025-02-02', 'nonaktif', 'Periode gaji mingguan yang dibuat otomatis', '2025-03-11 19:56:22', '2025-03-11 19:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'users.view', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(2, 'users.create', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(3, 'users.edit', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(4, 'users.delete', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(5, 'roles.view', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(6, 'roles.create', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(7, 'roles.edit', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(8, 'roles.delete', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(9, 'permissions.view', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(10, 'permissions.create', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(11, 'permissions.edit', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(12, 'permissions.delete', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(13, 'menu.view', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(14, 'menu.create', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(15, 'menu.edit', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(16, 'menu.delete', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(17, 'bagians.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(18, 'bagians.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(19, 'bagians.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(20, 'bagians.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(21, 'departemens.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(22, 'departemens.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(23, 'departemens.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(24, 'departemens.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(25, 'hariliburs.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(26, 'hariliburs.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(27, 'hariliburs.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(28, 'hariliburs.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(29, 'hariliburs.generateSundaysForm', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(30, 'hariliburs.generateSundays', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(31, 'jabatans.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(32, 'jabatans.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(33, 'jabatans.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(34, 'jabatans.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(35, 'karyawans.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(36, 'karyawans.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(37, 'karyawans.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(38, 'karyawans.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(39, 'mastercutis.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(40, 'mastercutis.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(41, 'mastercutis.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(42, 'mastercutis.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(43, 'menus.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(44, 'menus.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(45, 'menus.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(46, 'menus.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(47, 'profesis.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(48, 'profesis.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(49, 'profesis.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(50, 'profesis.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(51, 'program_studi.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(52, 'program_studi.create', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(53, 'program_studi.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(54, 'program_studi.delete', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(55, 'role_access.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(56, 'role_access.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(57, 'role_access.copyPermissions', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(58, 'user_access.view', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(59, 'user_access.edit', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(60, 'user_access.copyAccess', 'web', '2025-03-01 21:38:14', '2025-03-01 21:38:14'),
(61, 'shifts.view', 'web', '2025-03-01 21:49:35', '2025-03-01 21:49:35'),
(62, 'shifts.create', 'web', '2025-03-01 21:49:35', '2025-03-01 21:49:35'),
(63, 'shifts.edit', 'web', '2025-03-01 21:49:35', '2025-03-01 21:49:35'),
(64, 'shifts.delete', 'web', '2025-03-01 21:49:35', '2025-03-01 21:49:35'),
(65, 'cuti_karyawan.view', 'web', '2025-03-01 22:08:41', '2025-03-01 22:08:41'),
(66, 'cuti_karyawan.create', 'web', '2025-03-01 22:08:41', '2025-03-01 22:08:41'),
(67, 'cuti_karyawan.edit', 'web', '2025-03-01 22:08:41', '2025-03-01 22:08:41'),
(68, 'cuti_karyawan.delete', 'web', '2025-03-01 22:08:41', '2025-03-01 22:08:41'),
(69, 'cuti_karyawan.approve', 'web', '2025-03-01 22:08:41', '2025-03-01 22:08:41'),
(70, 'cuti_karyawan.approvalForm', 'web', '2025-03-01 22:08:41', '2025-03-01 22:08:41'),
(71, 'lemburs.view', 'web', '2025-03-02 00:19:47', '2025-03-02 00:19:47'),
(72, 'lemburs.create', 'web', '2025-03-02 00:19:47', '2025-03-02 00:19:47'),
(73, 'lemburs.edit', 'web', '2025-03-02 00:19:47', '2025-03-02 00:19:47'),
(74, 'lemburs.delete', 'web', '2025-03-02 00:19:47', '2025-03-02 00:19:47'),
(75, 'lemburs.approve', 'web', '2025-03-02 00:19:47', '2025-03-02 00:19:47'),
(76, 'lemburs.approvalForm', 'web', '2025-03-02 00:19:47', '2025-03-02 00:19:47'),
(77, 'jadwalkerjas.view', 'web', '2025-03-02 00:48:54', '2025-03-02 00:48:54'),
(78, 'jadwalkerjas.create', 'web', '2025-03-02 00:48:54', '2025-03-02 00:48:54'),
(79, 'jadwalkerjas.edit', 'web', '2025-03-02 00:48:54', '2025-03-02 00:48:54'),
(80, 'jadwalkerjas.delete', 'web', '2025-03-02 00:48:54', '2025-03-02 00:48:54'),
(81, 'jadwalkerjas.report', 'web', '2025-03-02 00:48:54', '2025-03-02 00:48:54'),
(82, 'jadwal_kerja.view', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(83, 'jadwal_kerja.create', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(84, 'jadwal_kerja.edit', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(85, 'jadwal_kerja.delete', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(86, 'jadwal_kerja.report', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(87, 'mesin_absensi.view', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(88, 'mesin_absensi.create', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(89, 'mesin_absensi.edit', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(90, 'mesin_absensi.delete', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(91, 'mesin_absensi.toggleStatus', 'web', '2025-03-02 01:32:04', '2025-03-02 01:32:04'),
(92, 'uang_tunggu.view', 'web', '2025-03-07 12:35:35', '2025-03-07 12:35:35'),
(93, 'uang_tunggu.create', 'web', '2025-03-07 12:35:35', '2025-03-07 12:35:35'),
(94, 'uang_tunggu.edit', 'web', '2025-03-07 12:35:35', '2025-03-07 12:35:35'),
(95, 'uang_tunggu.delete', 'web', '2025-03-07 12:35:35', '2025-03-07 12:35:35'),
(96, 'absensis.view', 'web', '2025-03-07 13:29:52', '2025-03-07 13:29:52'),
(97, 'absensis.create', 'web', '2025-03-07 13:29:52', '2025-03-07 13:29:52'),
(98, 'absensis.edit', 'web', '2025-03-07 13:29:52', '2025-03-07 13:29:52'),
(99, 'absensis.delete', 'web', '2025-03-07 13:29:52', '2025-03-07 13:29:52'),
(100, 'karyawans.search', 'web', '2025-03-07 21:07:00', '2025-03-07 21:07:00'),
(101, 'mesin_absensi.testConnection', 'web', '2025-03-07 21:07:01', '2025-03-07 21:07:01'),
(102, 'mesin_absensi.downloadLogs', 'web', '2025-03-07 21:07:01', '2025-03-07 21:07:01'),
(103, 'mesin_absensi.processLogs', 'web', '2025-03-07 21:07:01', '2025-03-07 21:07:01'),
(104, 'mesin_absensi.uploadNames', 'web', '2025-03-07 21:07:01', '2025-03-07 21:07:01'),
(105, 'mesin_absensi.uploadNamesBatch', 'web', '2025-03-07 21:07:01', '2025-03-07 21:07:01'),
(106, 'mesin_absensi.downloadLogsRange', 'web', '2025-03-09 07:42:41', '2025-03-09 07:42:41'),
(107, 'mesin_absensi.downloadLogsUser', 'web', '2025-03-09 07:42:41', '2025-03-09 07:42:41'),
(108, 'mesin_absensi.syncAllUsers', 'web', '2025-03-09 07:42:41', '2025-03-09 07:42:41'),
(109, 'mesin_absensi.deleteUser', 'web', '2025-03-09 07:42:41', '2025-03-09 07:42:41'),
(112, 'mesin_absensi.uploadDirectBatch', 'web', '2025-03-10 06:41:36', '2025-03-10 06:41:36'),
(113, 'mesin_absensi.cloneUsers', 'web', '2025-03-10 06:41:36', '2025-03-10 06:41:36'),
(114, 'mesin_absensi.autoDetectIp', 'web', '2025-03-10 06:41:36', '2025-03-10 06:41:36'),
(115, 'potongans.view', 'web', '2025-03-10 06:41:36', '2025-03-10 06:41:36'),
(116, 'potongans.create', 'web', '2025-03-10 06:41:36', '2025-03-10 06:41:36'),
(117, 'potongans.edit', 'web', '2025-03-10 06:41:36', '2025-03-10 06:41:36'),
(118, 'potongans.delete', 'web', '2025-03-10 06:41:36', '2025-03-10 06:41:36'),
(119, 'periode_gaji.view', 'web', '2025-03-10 07:07:57', '2025-03-10 07:07:57'),
(120, 'periode_gaji.create', 'web', '2025-03-10 07:07:57', '2025-03-10 07:07:57'),
(121, 'periode_gaji.edit', 'web', '2025-03-10 07:07:57', '2025-03-10 07:07:57'),
(122, 'periode_gaji.delete', 'web', '2025-03-10 07:07:57', '2025-03-10 07:07:57'),
(123, 'periode_gaji.generateMonthly', 'web', '2025-03-10 07:07:57', '2025-03-10 07:07:57'),
(124, 'penggajians.view', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(125, 'penggajians.create', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(126, 'penggajians.edit', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(127, 'penggajians.delete', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(128, 'penggajians.reportByPeriod', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(129, 'penggajians.reportByDepartment', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(130, 'penggajians.addComponent', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(131, 'penggajians.removeComponent', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(132, 'penggajians.generatePayslip', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(133, 'penggajians.exportExcel', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(134, 'penggajians.batchProcess', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(135, 'periode_gaji.generateWeekly', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(136, 'periode_gaji.deleteMultiple', 'web', '2025-03-10 16:38:44', '2025-03-10 16:38:44'),
(137, 'absensis.checkSchedule', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(138, 'karyawans.user', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(139, 'karyawans.approval', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(140, 'karyawans.resign', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(141, 'penggajians.generatePayslips', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(142, 'penggajians.review', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(143, 'penggajians.process', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(144, 'users.resetPassword', 'web', '2025-03-12 02:57:06', '2025-03-12 02:57:06'),
(145, 'absensis.fetchData', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(146, 'absensis.startSync', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(147, 'absensis.fetchLatestData', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(148, 'absensis.dailyReport', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(149, 'absensis.employeeReport', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(150, 'kuota_cuti_tahunan.view', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(151, 'kuota_cuti_tahunan.create', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(152, 'kuota_cuti_tahunan.edit', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(153, 'kuota_cuti_tahunan.report', 'web', '2025-03-13 14:19:14', '2025-03-13 14:19:14'),
(154, 'absensis.checkout', 'web', '2025-03-16 03:05:34', '2025-03-16 03:05:34'),
(155, 'kuota_cuti_tahunan.delete', 'web', '2025-03-16 03:05:34', '2025-03-16 03:05:34'),
(156, 'kuota_cuti_tahunan.generateMassal', 'web', '2025-03-16 03:05:34', '2025-03-16 03:05:34'),
(162, 'jadwal_kerja.toggleBackdate', 'web', '2025-03-16 06:25:23', '2025-03-16 06:25:23'),
(167, 'jadwal_kerja.backdate', 'web', '2025-03-16 06:33:45', '2025-03-16 06:33:45'),
(169, 'fungsi_khusus.AktifBackdate', 'web', '2025-03-16 21:30:11', '2025-03-16 21:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `potongans`
--

CREATE TABLE `potongans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_potongan` varchar(255) NOT NULL,
  `type` enum('wajib','tidak_wajib') NOT NULL DEFAULT 'tidak_wajib',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `potongans`
--

INSERT INTO `potongans` (`id`, `nama_potongan`, `type`, `created_at`, `updated_at`) VALUES
(1, 'BPJS Kesehatan', 'wajib', '2025-03-10 06:43:54', '2025-03-10 06:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `profesis`
--

CREATE TABLE `profesis` (
  `id` char(36) NOT NULL,
  `name_profesi` varchar(255) NOT NULL,
  `tunjangan_profesi` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profesis`
--

INSERT INTO `profesis` (`id`, `name_profesi`, `tunjangan_profesi`, `created_at`, `updated_at`) VALUES
('9e55607b-7e32-4fd9-9334-1b6c64d525f8', 'Staff Level 1', 500000, '2025-03-01 23:01:21', '2025-03-12 02:38:13'),
('9e5561fd-5db7-487e-884f-007eed204321', 'Admin 1', 5000000, '2025-03-01 23:05:34', '2025-03-01 23:05:34'),
('9e55644a-fc17-471b-b40e-b4d4c339ef00', 'Admin 2', 0, '2025-03-01 23:12:00', '2025-03-01 23:12:00'),
('9e557e71-2ebe-49b7-9496-307361749ad8', 'PROFESI 1', 100000, '2025-03-02 00:25:07', '2025-03-02 01:54:31');

-- --------------------------------------------------------

--
-- Table structure for table `program_studis`
--

CREATE TABLE `program_studis` (
  `id` char(36) NOT NULL,
  `name_programstudi` varchar(255) NOT NULL,
  `education_type` enum('SMA','non-SMA') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_studis`
--

INSERT INTO `program_studis` (`id`, `name_programstudi`, `education_type`, `created_at`, `updated_at`) VALUES
('9e556db9-cabc-4010-aceb-a17ea6a71a22', 'EKONOMI DAN BISNIS', 'SMA', '2025-03-01 23:38:23', '2025-03-01 23:38:23'),
('9e556def-9c0d-4560-87d7-86f1f8d52012', 'KECANTIKAN', 'SMA', '2025-03-01 23:38:58', '2025-03-01 23:38:58'),
('9e68d265-ca66-45c9-a120-cc47ebda54c9', 'Teknik Kendaraan Ringan', 'SMA', '2025-03-11 15:00:36', '2025-03-11 15:00:36'),
('9e6edc38-0e3d-4d6b-9340-254be28e36bd', 'Teknik Industri', 'non-SMA', '2025-03-14 15:03:02', '2025-03-14 15:06:46'),
('9e6edcb1-d730-4899-9d06-ddbb7ebe8d89', 'Teknik Elektro', 'non-SMA', '2025-03-14 15:04:22', '2025-03-14 15:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-03-01 21:37:54', '2025-03-01 21:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(150, 1),
(151, 1),
(152, 1),
(153, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_shift` varchar(255) NOT NULL,
  `jenis_shift` varchar(255) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `kode_shift`, `jenis_shift`, `jam_masuk`, `jam_pulang`, `created_at`, `updated_at`) VALUES
(6, 'KERJA-002', 'Jam Siang', '09:00:00', '17:00:00', '2025-03-11 14:58:14', '2025-03-11 14:58:14'),
(7, 'SHIFT-001', 'Shift Pagi', '06:30:00', '14:30:00', '2025-03-11 14:58:34', '2025-03-11 14:58:34'),
(8, 'SHIFT-002', 'Shift Siang', '08:30:00', '16:30:00', '2025-03-11 14:58:52', '2025-03-11 14:58:52'),
(9, 'SHIFT-003', 'Shift 3', '20:00:00', '04:00:00', '2025-03-12 03:36:14', '2025-03-12 05:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `special_permissions`
--

CREATE TABLE `special_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `special_permissions`
--

INSERT INTO `special_permissions` (`id`, `kode`, `nama`, `deskripsi`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin.absensis.backdate', 'Izin Input Mundur Absensi', 'Mengizinkan pengguna untuk input absensi dengan tanggal mundur', 1, '2025-03-16 03:06:26', '2025-03-16 03:06:26'),
(2, 'special-permissions.backdate', 'Izin Input Tanggal Mundur', 'Mengizinkan pengguna untuk input data dengan tanggal mundur', 1, '2025-03-16 03:48:51', '2025-03-16 03:48:51'),
(3, 'absensis.backdate', 'Izin Input Tanggal Mundur', 'Mengizinkan pengguna untuk input data dengan tanggal mundur', 1, '2025-03-16 03:50:55', '2025-03-16 03:50:55');

-- --------------------------------------------------------

--
-- Table structure for table `uangtunggus`
--

CREATE TABLE `uangtunggus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` char(36) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `nominal` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `uangtunggus`
--

INSERT INTO `uangtunggus` (`id`, `karyawan_id`, `tanggal_mulai`, `tanggal_selesai`, `nominal`, `created_at`, `updated_at`) VALUES
(1, '9e6f085f-eef4-4d67-baaf-65791ba99cf7', '2025-03-17', '2025-03-18', 30000.00, '2025-03-17 01:42:55', '2025-03-17 01:42:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@admin.com', NULL, '$2y$10$nD0QrBTQ5CgD4OlTEQDkQObOGfr5WpF5Abp8KGobEzSLEaqerf/f6', NULL, '2025-03-01 21:37:54', '2025-03-01 21:37:54'),
(3, 'Basuki', 'basuki@test.com', NULL, '$2y$10$b6txoytnEzl2DFkfSk3XuO.9Fp7wpFQkM1BXYMr25vsdCPAqFIZ/2', NULL, '2025-03-11 19:09:34', '2025-03-12 02:38:06'),
(4, 'Rizq Alwan Fauzan', 'alwan@gmail.com', NULL, '$2y$10$9snZ2TmAfp8XLDN4jMfCu.sCniCzZSOa8ujkejOy5HWHBooN90jNi', NULL, '2025-03-12 02:40:23', '2025-03-12 02:40:23');

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_penggajian`
--

CREATE TABLE `verifikasi_penggajian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_penggajian` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Menunggu Verifikasi','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi',
  `keterangan` text DEFAULT NULL,
  `total_verifikasi` decimal(15,2) NOT NULL,
  `departemen_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensis`
--
ALTER TABLE `absensis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensis_karyawan_id_foreign` (`karyawan_id`),
  ADD KEY `absensis_jadwalkerja_id_foreign` (`jadwalkerja_id`),
  ADD KEY `absensis_mesinabsensi_masuk_id_foreign` (`mesinabsensi_masuk_id`),
  ADD KEY `absensis_mesinabsensi_pulang_id_foreign` (`mesinabsensi_pulang_id`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bagians`
--
ALTER TABLE `bagians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bagians_id_departemen_foreign` (`id_departemen`);

--
-- Indexes for table `cuti_karyawans`
--
ALTER TABLE `cuti_karyawans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuti_karyawans_id_karyawan_foreign` (`id_karyawan`),
  ADD KEY `cuti_karyawans_id_supervisor_foreign` (`id_supervisor`),
  ADD KEY `cuti_karyawans_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `departemens`
--
ALTER TABLE `departemens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hariliburs`
--
ALTER TABLE `hariliburs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jabatans`
--
ALTER TABLE `jabatans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwalkerjas`
--
ALTER TABLE `jadwalkerjas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwalkerjas_shift_id_foreign` (`shift_id`),
  ADD KEY `jadwalkerjas_karyawan_id_foreign` (`karyawan_id`);

--
-- Indexes for table `karyawans`
--
ALTER TABLE `karyawans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawans_id_departemen_foreign` (`id_departemen`),
  ADD KEY `karyawans_id_bagian_foreign` (`id_bagian`),
  ADD KEY `karyawans_id_jabatan_foreign` (`id_jabatan`),
  ADD KEY `karyawans_id_profesi_foreign` (`id_profesi`),
  ADD KEY `karyawans_id_programstudi_foreign` (`id_programstudi`),
  ADD KEY `karyawans_user_id_foreign` (`user_id`);

--
-- Indexes for table `kuota_cuti_tahunans`
--
ALTER TABLE `kuota_cuti_tahunans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kuota_cuti_tahunans_karyawan_id_tahun_unique` (`karyawan_id`,`tahun`);

--
-- Indexes for table `lemburs`
--
ALTER TABLE `lemburs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lemburs_karyawan_id_foreign` (`karyawan_id`),
  ADD KEY `lemburs_supervisor_id_foreign` (`supervisor_id`),
  ADD KEY `lemburs_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `mastercutis`
--
ALTER TABLE `mastercutis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menus_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `mesinabsensis`
--
ALTER TABLE `mesinabsensis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `penggajians`
--
ALTER TABLE `penggajians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penggajians_id_periode_foreign` (`id_periode`),
  ADD KEY `penggajians_id_karyawan_foreign` (`id_karyawan`),
  ADD KEY `penggajians_verifikasi_oleh_foreign` (`verifikasi_oleh`);

--
-- Indexes for table `periodegajis`
--
ALTER TABLE `periodegajis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `potongans`
--
ALTER TABLE `potongans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profesis`
--
ALTER TABLE `profesis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_studis`
--
ALTER TABLE `program_studis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `special_permissions`
--
ALTER TABLE `special_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uangtunggus`
--
ALTER TABLE `uangtunggus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uangtunggus_karyawan_id_foreign` (`karyawan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `verifikasi_penggajian`
--
ALTER TABLE `verifikasi_penggajian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verifikasi_penggajian_id_penggajian_foreign` (`id_penggajian`),
  ADD KEY `verifikasi_penggajian_user_id_foreign` (`user_id`),
  ADD KEY `verifikasi_penggajian_departemen_id_foreign` (`departemen_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensis`
--
ALTER TABLE `absensis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hariliburs`
--
ALTER TABLE `hariliburs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `mastercutis`
--
ALTER TABLE `mastercutis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `mesinabsensis`
--
ALTER TABLE `mesinabsensis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `periodegajis`
--
ALTER TABLE `periodegajis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `potongans`
--
ALTER TABLE `potongans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `special_permissions`
--
ALTER TABLE `special_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `uangtunggus`
--
ALTER TABLE `uangtunggus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `verifikasi_penggajian`
--
ALTER TABLE `verifikasi_penggajian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensis`
--
ALTER TABLE `absensis`
  ADD CONSTRAINT `absensis_jadwalkerja_id_foreign` FOREIGN KEY (`jadwalkerja_id`) REFERENCES `jadwalkerjas` (`id`),
  ADD CONSTRAINT `absensis_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`),
  ADD CONSTRAINT `absensis_mesinabsensi_masuk_id_foreign` FOREIGN KEY (`mesinabsensi_masuk_id`) REFERENCES `mesinabsensis` (`id`),
  ADD CONSTRAINT `absensis_mesinabsensi_pulang_id_foreign` FOREIGN KEY (`mesinabsensi_pulang_id`) REFERENCES `mesinabsensis` (`id`);

--
-- Constraints for table `bagians`
--
ALTER TABLE `bagians`
  ADD CONSTRAINT `bagians_id_departemen_foreign` FOREIGN KEY (`id_departemen`) REFERENCES `departemens` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cuti_karyawans`
--
ALTER TABLE `cuti_karyawans`
  ADD CONSTRAINT `cuti_karyawans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cuti_karyawans_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cuti_karyawans_id_supervisor_foreign` FOREIGN KEY (`id_supervisor`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `jadwalkerjas`
--
ALTER TABLE `jadwalkerjas`
  ADD CONSTRAINT `jadwalkerjas_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwalkerjas_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `karyawans`
--
ALTER TABLE `karyawans`
  ADD CONSTRAINT `karyawans_id_bagian_foreign` FOREIGN KEY (`id_bagian`) REFERENCES `bagians` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `karyawans_id_departemen_foreign` FOREIGN KEY (`id_departemen`) REFERENCES `departemens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `karyawans_id_jabatan_foreign` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `karyawans_id_profesi_foreign` FOREIGN KEY (`id_profesi`) REFERENCES `profesis` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `karyawans_id_programstudi_foreign` FOREIGN KEY (`id_programstudi`) REFERENCES `program_studis` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `karyawans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kuota_cuti_tahunans`
--
ALTER TABLE `kuota_cuti_tahunans`
  ADD CONSTRAINT `kuota_cuti_tahunans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lemburs`
--
ALTER TABLE `lemburs`
  ADD CONSTRAINT `lemburs_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lemburs_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lemburs_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penggajians`
--
ALTER TABLE `penggajians`
  ADD CONSTRAINT `penggajians_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawans` (`id`),
  ADD CONSTRAINT `penggajians_id_periode_foreign` FOREIGN KEY (`id_periode`) REFERENCES `periodegajis` (`id`),
  ADD CONSTRAINT `penggajians_verifikasi_oleh_foreign` FOREIGN KEY (`verifikasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `uangtunggus`
--
ALTER TABLE `uangtunggus`
  ADD CONSTRAINT `uangtunggus_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`);

--
-- Constraints for table `verifikasi_penggajian`
--
ALTER TABLE `verifikasi_penggajian`
  ADD CONSTRAINT `verifikasi_penggajian_departemen_id_foreign` FOREIGN KEY (`departemen_id`) REFERENCES `departemens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `verifikasi_penggajian_id_penggajian_foreign` FOREIGN KEY (`id_penggajian`) REFERENCES `penggajians` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `verifikasi_penggajian_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
