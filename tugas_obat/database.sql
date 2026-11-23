-- ========================================================
-- Database Schema: Sistem Informasi Rumah Sakit / Klinik
-- Database: `rumah sakit`
-- ========================================================

CREATE DATABASE IF NOT EXISTS `rumah sakit` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rumah sakit`;

SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW IF EXISTS `resep_obat`;
DROP TABLE IF EXISTS `resep_obat`;
DROP TABLE IF EXISTS `pemeriksaan_obat`;
DROP TABLE IF EXISTS `pemeriksaan`;
DROP TABLE IF EXISTS `obat`;
DROP TABLE IF EXISTS `dokter`;
DROP TABLE IF EXISTS `pasien`;
DROP TABLE IF EXISTS `admin_users`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Tabel: pasien
-- --------------------------------------------------------
CREATE TABLE `pasien` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `no_registrasi` varchar(255) NOT NULL,
  `nama_pasien` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `keluhan` varchar(255) NOT NULL DEFAULT '',
  `tensi_darah` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: dokter
-- --------------------------------------------------------
CREATE TABLE `dokter` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_dokter` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `spesialis` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: obat
-- --------------------------------------------------------
CREATE TABLE `obat` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `kode_obat` varchar(255) NOT NULL,
  `nama_obat` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_obat` (`kode_obat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: pemeriksaan
-- --------------------------------------------------------
CREATE TABLE `pemeriksaan` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `id_pasien` int(5) NOT NULL,
  `id_dokter` int(5) NOT NULL,
  `tgl_periksa` date NOT NULL,
  `keluhan` varchar(255) NOT NULL,
  `tensi_darah` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_pasien` (`id_pasien`),
  KEY `id_dokter` (`id_dokter`),
  CONSTRAINT `fk_pemeriksaan_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pemeriksaan_dokter` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: pemeriksaan_obat
-- --------------------------------------------------------
CREATE TABLE `pemeriksaan_obat` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `id_pemeriksaan` int(5) NOT NULL,
  `id_id_obat` int(5) NOT NULL,
  `aturan_pakai` varchar(255) NOT NULL DEFAULT '3x1 Sehari Sesudah Makan',
  PRIMARY KEY (`id`),
  KEY `id_pemeriksaan` (`id_pemeriksaan`),
  KEY `id_id_obat` (`id_id_obat`),
  CONSTRAINT `fk_pemeriksaan_obat_pemeriksaan` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `pemeriksaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pemeriksaan_obat_obat` FOREIGN KEY (`id_id_obat`) REFERENCES `obat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- View: resep_obat (Updatable View untuk CRUD Resep)
-- --------------------------------------------------------
CREATE OR REPLACE VIEW `resep_obat` AS
SELECT 
  `id` AS `id_resep_obat`,
  `id_pemeriksaan` AS `id_periksa`,
  `id_id_obat` AS `id_obat`,
  `aturan_pakai`
FROM `pemeriksaan_obat`;

-- --------------------------------------------------------
-- Tabel: admin_users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id_admin` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_users` (`id_admin`, `username`, `password_hash`) VALUES
(1, 'admin', '123')
ON DUPLICATE KEY UPDATE `username`='admin';

-- ========================================================
-- DATA CONTOH (SEED DATA)
-- ========================================================

-- Data Dokter
INSERT INTO `dokter` (`id`, `username`, `password`, `nama_dokter`, `alamat`, `tgl_lahir`, `jenis`, `spesialis`) VALUES
(1, 'dr_rahman', '123', 'dr. Rahman Putra, Sp.PD', 'Jl. Pemuda No. 45, Surabaya', '1982-05-14', 'Laki-laki', 'Penyakit Dalam'),
(2, 'dr_melati', '123', 'dr. Melati Kusuma, Sp.A', 'Jl. Diponegoro No. 12, Sidoarjo', '1987-08-22', 'Perempuan', 'Anak'),
(3, 'dr_budi', '123', 'dr. Budi Santoso', 'Jl. Basuki Rahmat No. 88, Surabaya', '1990-12-03', 'Laki-laki', 'Umum'),
(4, 'dr_anisa', '123', 'dr. Anisa Fitri, Sp.JP', 'Jl. Ahmad Yani No. 15, Gresik', '1985-03-30', 'Perempuan', 'Jantung & Pembuluh Darah');

-- Data Pasien (Dilengkapi Keluhan & Tensi Darah sesuai wireframe "dr. tabel pasien")
INSERT INTO `pasien` (`id`, `no_registrasi`, `nama_pasien`, `alamat`, `tgl_lahir`, `keluhan`, `tensi_darah`) VALUES
(1, 'REG-2026-001', 'Ahmad Fauzi', 'Jl. Ngagel Jaya Timur No. 10, Surabaya', '1994-04-18', 'Demam tinggi selama 3 hari disertai pusing dan lemas', '120/80 mmHg'),
(2, 'REG-2026-002', 'Siti Rahmawati', 'Jl. Kutisari Indah Blok B-3, Surabaya', '1988-11-25', 'Batuk berdahak dan pilek lebih dari seminggu', '110/70 mmHg'),
(3, 'REG-2026-003', 'Dimas Prasetyo', 'Jl. Raya Waru No. 54, Sidoarjo', '2001-09-09', 'Nyeri lambung dan mual setelah makan pedas', '115/75 mmHg'),
(4, 'REG-2026-004', 'Kartika Dewi', 'Jl. Veteran No. 27, Gresik', '1996-02-14', 'Jantung berdebar dan mudah lelah saat beraktivitas', '135/85 mmHg'),
(5, 'REG-2026-005', 'Bambang Sutrisno', 'Jl. Darmo Permai No. 102, Surabaya', '1975-07-30', 'Pusing bagian tengkuk leher dan badan kaku', '150/95 mmHg');

-- Data Obat
INSERT INTO `obat` (`id`, `kode_obat`, `nama_obat`) VALUES
(1, 'OBT-001', 'Paracetamol 500mg Tablet'),
(2, 'OBT-002', 'Amoxicillin 500mg Kapsul'),
(3, 'OBT-003', 'Antasida Doen Tablet'),
(4, 'OBT-004', 'Cefixime 100mg Kapsul'),
(5, 'OBT-005', 'Cetirizine 10mg Tablet'),
(6, 'OBT-006', 'Metformin 500mg Tablet'),
(7, 'OBT-007', 'Amlodipine 5mg Tablet'),
(8, 'OBT-008', 'Vitamin C 500mg IPI');

-- Data Pemeriksaan
INSERT INTO `pemeriksaan` (`id`, `id_pasien`, `id_dokter`, `tgl_periksa`, `keluhan`, `tensi_darah`) VALUES
(1, 1, 1, '2026-11-01', 'Demam tinggi selama 3 hari disertai pusing dan lemas', '120/80 mmHg'),
(2, 2, 2, '2026-11-02', 'Batuk berdahak dan pilek lebih dari seminggu', '110/70 mmHg'),
(3, 3, 3, '2026-11-03', 'Nyeri lambung dan mual setelah makan pedas', '115/75 mmHg'),
(4, 4, 4, '2026-11-04', 'Jantung berdebar dan mudah lelah saat beraktivitas', '135/85 mmHg'),
(5, 5, 1, '2026-11-05', 'Pusing bagian tengkuk leher dan badan kaku', '150/95 mmHg');

-- Data Resep Obat (pemeriksaan_obat)
INSERT INTO `pemeriksaan_obat` (`id`, `id_pemeriksaan`, `id_id_obat`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 8),
(4, 2, 5),
(5, 2, 8),
(6, 3, 3),
(7, 3, 1),
(8, 4, 7),
(9, 5, 7),
(10, 5, 1);
