-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 12:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kos_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `sender_type`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'user', 'Halo admin, saya mau tanya untuk kamar di cabang Babarsari masih tersedia?', 1, '2025-08-25 02:00:00'),
(2, 2, 'admin', 'Halo Kak Budi! Untuk cabang Babarsari masih tersedia beberapa kamar. Kakak mau tipe yang mana?', 1, '2025-08-25 02:05:00'),
(3, 2, 'user', 'Yang AC dengan kamar mandi dalam ada yang kosong kak?', 1, '2025-08-25 02:10:00'),
(4, 2, 'admin', 'Ada kak! Kamar A102 dan A103 masih tersedia. A102 harga Rp 1.000.000/bulan, A103 harga Rp 900.000/bulan. Mau survey dulu?', 1, '2025-08-25 02:15:00'),
(5, 2, 'user', 'Boleh kak, saya mau lihat yang A103 dulu. Bisa survey kapan ya?', 1, '2025-08-25 02:20:00'),
(6, 2, 'admin', 'Bisa kapan aja kak, jam 08.00-20.00. Langsung datang ke lokasi ya, nanti akan ditemani staff kami.', 1, '2025-08-25 02:25:00'),
(7, 3, 'user', 'Min, kamar C101 fasilitas lengkap ya? Ada kulkas mini juga?', 1, '2025-08-10 07:00:00'),
(8, 3, 'admin', 'Betul kak Sari! Kamar C101 premium dengan fasilitas lengkap termasuk kulkas mini, TV LED, AC, dan meja rias. Harga Rp 1.500.000/bulan.', 1, '2025-08-10 07:10:00'),
(9, 3, 'user', 'Kalau yang C102 bedanya apa ya min?', 1, '2025-08-10 07:15:00'),
(10, 3, 'admin', 'C102 tidak ada kulkas mini dan meja rias kak, tapi ukurannya tetap luas 4x4m dengan AC dan TV LED. Harganya Rp 1.300.000/bulan.', 1, '2025-08-10 07:20:00'),
(11, 4, 'user', 'Admin, saya penghuni lama dari cabang Kaliurang. Mau pindah ke Babarsari bisa?', 1, '2025-09-15 03:00:00'),
(12, 4, 'admin', 'Bisa kak Andi! Kebetulan di Babarsari masih ada kamar kosong. Mau yang tipe apa kak?', 1, '2025-09-15 03:05:00'),
(13, 4, 'user', 'Yang ada AC dan kamar mandi dalam. Budget sekitar 1 juta.', 1, '2025-09-15 03:10:00'),
(14, 4, 'admin', 'Cocok kak! Ada kamar A202 harga Rp 1.000.000/bulan dengan AC dan kamar mandi dalam. Mau booking?', 1, '2025-09-15 03:15:00'),
(15, 4, 'user', 'Oke min, saya ambil yang A202. Kapan bisa pindah?', 1, '2025-09-15 03:20:00'),
(16, 4, 'admin', 'Bisa mulai tanggal 1 Oktober kak. Nanti saya siapkan administrasinya ya!', 1, '2025-09-15 03:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id` int(11) NOT NULL,
  `kos_id` int(11) NOT NULL,
  `nomor` varchar(20) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `ukuran` varchar(20) DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `status` enum('tersedia','terisi','maintenance') DEFAULT 'tersedia',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`id`, `kos_id`, `nomor`, `harga`, `ukuran`, `fasilitas`, `status`, `foto`, `created_at`, `updated_at`) VALUES
(1, 1, 'A101', 1200000.00, '4x4 m', 'Kasur Queen, Lemari 2 Pintu, Meja Belajar, AC, Kamar Mandi Dalam, TV LED 32 inch', 'tersedia', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(2, 1, 'A102', 1000000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(3, 1, 'A103', 900000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'terisi', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(4, 1, 'A104', 850000.00, '3x3 m', 'Kasur Single, Lemari, Meja Belajar, Kipas Angin, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(5, 1, 'A201', 1200000.00, '4x4 m', 'Kasur Queen, Lemari 2 Pintu, Meja Belajar, AC, Kamar Mandi Dalam, TV LED 32 inch', 'tersedia', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(6, 1, 'A202', 1000000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'terisi', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(7, 1, 'A203', 900000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(8, 1, 'A204', 850000.00, '3x3 m', 'Kasur Single, Lemari, Meja Belajar, Kipas Angin, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_babarsari.png', '2025-12-08 14:42:40', '2025-12-08 16:34:09'),
(9, 2, 'B101', 950000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_seturan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:37:27'),
(10, 2, 'B102', 950000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_seturan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:37:27'),
(11, 2, 'B103', 800000.00, '3x3 m', 'Kasur Single, Lemari, Meja Belajar, Kipas Angin, Kamar Mandi Dalam', 'terisi', 'kamar_kos_seturan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:37:27'),
(12, 2, 'B104', 800000.00, '3x3 m', 'Kasur Single, Lemari, Meja Belajar, Kipas Angin, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_seturan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:37:27'),
(13, 2, 'B105', 700000.00, '3x3 m', 'Kasur Single, Lemari, Kipas Angin, Kamar Mandi Luar', 'tersedia', 'kamar_kos_seturan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:37:27'),
(14, 2, 'B106', 700000.00, '3x3 m', 'Kasur Single, Lemari, Kipas Angin, Kamar Mandi Luar', 'tersedia', 'kamar_kos_seturan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:37:27'),
(15, 3, 'C101', 1500000.00, '4x5 m', 'Kasur Queen, Lemari 3 Pintu, Meja Rias, Meja Belajar, AC, Kamar Mandi Dalam, TV LED, Kulkas Mini', 'tersedia', 'kamar_kos_putri_gejayan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:35:12'),
(16, 3, 'C102', 1300000.00, '4x4 m', 'Kasur Queen, Lemari 2 Pintu, Meja Belajar, AC, Kamar Mandi Dalam, TV LED', 'terisi', 'kamar_kos_putri_gejayan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:35:12'),
(17, 3, 'C103', 1100000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_putri_gejayan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:35:12'),
(18, 3, 'C104', 1100000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_putri_gejayan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:35:12'),
(19, 3, 'C201', 1500000.00, '4x5 m', 'Kasur Queen, Lemari 3 Pintu, Meja Rias, Meja Belajar, AC, Kamar Mandi Dalam, TV LED, Kulkas Mini', 'tersedia', 'kamar_kos_putri_gejayan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:35:12'),
(20, 3, 'C202', 1300000.00, '4x4 m', 'Kasur Queen, Lemari 2 Pintu, Meja Belajar, AC, Kamar Mandi Dalam, TV LED', 'tersedia', 'kamar_kos_putri_gejayan.jpg', '2025-12-08 14:42:40', '2025-12-08 16:35:12'),
(21, 4, 'D101', 1100000.00, '4x4 m', 'Kasur Single, Lemari 2 Pintu, Meja Belajar, AC, Kamar Mandi Dalam, TV LED', 'tersedia', 'kamar_kos_putra_kaliurang.jpeg', '2025-12-08 14:42:40', '2025-12-08 16:37:51'),
(22, 4, 'D102', 950000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_putra_kaliurang.jpeg', '2025-12-08 14:42:40', '2025-12-08 16:37:51'),
(23, 4, 'D103', 950000.00, '3x4 m', 'Kasur Single, Lemari, Meja Belajar, AC, Kamar Mandi Dalam', 'terisi', 'kamar_kos_putra_kaliurang.jpeg', '2025-12-08 14:42:40', '2025-12-08 16:37:51'),
(24, 4, 'D104', 800000.00, '3x3 m', 'Kasur Single, Lemari, Meja Belajar, Kipas Angin, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_putra_kaliurang.jpeg', '2025-12-08 14:42:40', '2025-12-08 16:37:51'),
(25, 4, 'D105', 800000.00, '3x3 m', 'Kasur Single, Lemari, Meja Belajar, Kipas Angin, Kamar Mandi Dalam', 'tersedia', 'kamar_kos_putra_kaliurang.jpeg', '2025-12-08 14:42:40', '2025-12-08 16:37:51'),
(26, 4, 'D106', 650000.00, '3x3 m', 'Kasur Single, Lemari, Kipas Angin, Kamar Mandi Luar', 'tersedia', 'kamar_kos_putra_kaliurang.jpeg', '2025-12-08 14:42:40', '2025-12-08 16:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `kos`
--

CREATE TABLE `kos` (
  `id` int(11) NOT NULL,
  `pemilik_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tipe` enum('putra','putri','campur') DEFAULT 'campur',
  `fasilitas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kos`
--

INSERT INTO `kos` (`id`, `pemilik_id`, `nama`, `alamat`, `deskripsi`, `latitude`, `longitude`, `foto`, `tipe`, `fasilitas`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kos Atma Babarsari', 'Jl. Babarsari No. 44, Caturtunggal, Depok, Sleman, Yogyakarta 55281', 'Cabang utama Kos Atma yang berlokasi strategis di kawasan Babarsari, hanya 5 menit dari Universitas Atma Jaya Yogyakarta. Cocok untuk mahasiswa UAJY dan universitas sekitar. Lingkungan aman, nyaman, dan dekat dengan berbagai fasilitas seperti minimarket, tempat makan, dan laundry.', -7.77990000, 110.41470000, 'kos_babarsari.jpg', 'campur', 'WiFi Gratis, Parkir Motor & Mobil, Dapur Bersama, Ruang Tamu AC, CCTV 24 Jam, Mesin Cuci, Air Panas, Cleaning Service', '2025-12-08 14:42:40', '2025-12-08 16:23:29'),
(2, 1, 'Kos Atma Seturan', 'Jl. Seturan Raya No. 88, Caturtunggal, Depok, Sleman, Yogyakarta 55281', 'Cabang Kos Atma di kawasan Seturan yang dikenal sebagai area mahasiswa. Dekat dengan berbagai kampus seperti UPN, STIE YKPN, dan Amikom. Akses mudah ke pusat hiburan dan kuliner Seturan.', -7.77450000, 110.40980000, 'kos_seturan.jpeg', 'campur', 'WiFi Gratis, Parkir Motor, Dapur Bersama, CCTV 24 Jam, Laundry Kiloan, Kantin', '2025-12-08 14:42:40', '2025-12-08 16:25:47'),
(3, 1, 'Kos Atma Putri Gejayan', 'Jl. Gejayan No. 15, Condongcatur, Depok, Sleman, Yogyakarta 55283', 'Cabang Kos Atma khusus putri dengan sistem keamanan ketat. Lokasi premium di kawasan Gejayan, dekat Hartono Mall dan UGM. Ideal untuk mahasiswi yang mengutamakan kenyamanan dan keamanan.', -7.78230000, 110.39120000, 'kos_putri_gejayan.jpg', 'putri', 'WiFi Gratis, AC di Setiap Kamar, Parkir Motor, Security 24 Jam, CCTV, Dapur Bersama, Ruang Jemur, Air Panas', '2025-12-08 14:42:40', '2025-12-08 16:29:03'),
(4, 1, 'Kos Atma Putra Kaliurang', 'Jl. Kaliurang KM 5.5 No. 10, Caturtunggal, Depok, Sleman, Yogyakarta 55281', 'Cabang Kos Atma khusus putra di kawasan Kaliurang. Dekat dengan kampus UGM, UII, dan UNY. Suasana asri dan sejuk khas Kaliurang dengan fasilitas lengkap untuk mahasiswa.', -7.76560000, 110.38780000, 'kos_putra_kaliurang.jpeg', 'putra', 'WiFi Gratis, Parkir Motor & Mobil, Dapur Bersama, CCTV 24 Jam, Ruang Olahraga, Kantin, Laundry', '2025-12-08 14:42:40', '2025-12-08 16:29:17');

-- --------------------------------------------------------

--
-- Table structure for table `penyewa`
--

CREATE TABLE `penyewa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('aktif','selesai','batal') DEFAULT 'aktif',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyewa`
--

INSERT INTO `penyewa` (`id`, `user_id`, `kamar_id`, `tanggal_masuk`, `tanggal_keluar`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 2, 3, '2025-09-01', NULL, 'aktif', 'Mahasiswa UAJY semester 5, Jurusan Teknik Informatika', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(2, 3, 16, '2025-08-15', NULL, 'aktif', 'Mahasiswi UGM semester 3, Jurusan Kedokteran', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(3, 4, 6, '2025-10-01', NULL, 'aktif', 'Mahasiswa UAJY semester 7, Jurusan Manajemen', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(4, 5, 11, '2025-07-01', NULL, 'aktif', 'Mahasiswi UPN semester 5, Jurusan Akuntansi', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(5, 4, 23, '2024-09-01', '2025-09-30', 'selesai', 'Pindah ke cabang Babarsari karena lebih dekat kampus', '2025-12-08 14:42:40', '2025-12-08 14:42:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `nama`, `telepon`, `alamat`, `foto`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@kosatma.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Kos Atma', '082112345678', NULL, NULL, 'admin', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(2, 'budi', 'budi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', '081234567890', 'Jl. Babarsari No. 10, Sleman', NULL, 'user', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(3, 'sari', 'sari@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sari Dewi', '085678901234', 'Jl. Seturan Raya No. 25, Sleman', NULL, 'user', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(4, 'andi', 'andi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Andi Wijaya', '087890123456', 'Jl. Colombo No. 5, Yogyakarta', NULL, 'user', '2025-12-08 14:42:40', '2025-12-08 14:42:40'),
(5, 'dewi', 'dewi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dewi Lestari', '089012345678', 'Jl. Kaliurang KM 7, Sleman', NULL, 'user', '2025-12-08 14:42:40', '2025-12-08 14:42:40');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_kos_stats`
-- (See below for the actual view)
--
CREATE TABLE `v_kos_stats` (
`id` int(11)
,`nama` varchar(100)
,`tipe` enum('putra','putri','campur')
,`alamat` text
,`foto` varchar(255)
,`latitude` decimal(10,8)
,`longitude` decimal(11,8)
,`total_kamar` bigint(21)
,`kamar_tersedia` decimal(22,0)
,`harga_terendah` decimal(12,2)
,`harga_tertinggi` decimal(12,2)
);

-- --------------------------------------------------------

--
-- Structure for view `v_kos_stats`
--
DROP TABLE IF EXISTS `v_kos_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_kos_stats`  AS SELECT `k`.`id` AS `id`, `k`.`nama` AS `nama`, `k`.`tipe` AS `tipe`, `k`.`alamat` AS `alamat`, `k`.`foto` AS `foto`, `k`.`latitude` AS `latitude`, `k`.`longitude` AS `longitude`, count(`km`.`id`) AS `total_kamar`, sum(case when `km`.`status` = 'tersedia' then 1 else 0 end) AS `kamar_tersedia`, min(`km`.`harga`) AS `harga_terendah`, max(`km`.`harga`) AS `harga_tertinggi` FROM (`kos` `k` left join `kamar` `km` on(`k`.`id` = `km`.`kos_id`)) GROUP BY `k`.`id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kos_id` (`kos_id`);

--
-- Indexes for table `kos`
--
ALTER TABLE `kos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemilik_id` (`pemilik_id`);

--
-- Indexes for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kamar_id` (`kamar_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `kos`
--
ALTER TABLE `kos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kamar`
--
ALTER TABLE `kamar`
  ADD CONSTRAINT `kamar_ibfk_1` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kos`
--
ALTER TABLE `kos`
  ADD CONSTRAINT `kos_ibfk_1` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD CONSTRAINT `penyewa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penyewa_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
