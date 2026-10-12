-- ============================================================
-- Smart RT Database Dump (Optimized for InfinityFree / cPanel)
-- Foreign Key Checks Disabled at Top & Bottom
-- Tables Sorted in Strict Dependency Order
-- ============================================================

SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;
SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;
SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;
SET NAMES utf8mb4;
SET @OLD_TIME_ZONE=@@TIME_ZONE;
SET TIME_ZONE='+00:00';
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

-- Table structure for table `data_rt`
--

DROP TABLE IF EXISTS `data_rt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_rt` (
  `ID_RT` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_rt` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_rw` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelurahan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_ketua_rt` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp_ketua_rt` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp_sekretariat` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID_RT`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_rt`
--

LOCK TABLES `data_rt` WRITE;
/*!40000 ALTER TABLE `data_rt` DISABLE KEYS */;
INSERT INTO `data_rt` VALUES (1,'05','01','Sukamaju','Cilodong','Kota Depok','Bpk. Sukardi Hartono','0812-3456-7890','0812-3456-7890','2026-09-22 00:26:48','2026-09-22 00:26:48'),(2,'01','02','Mekarjaya','Sukmajaya','Kota Depok','Bpk. Bambang Pamungkas',NULL,NULL,'2026-09-22 00:26:48','2026-09-22 00:26:48'),(3,'03','04','Baktijaya','Sukmajaya','Kota Depok','Ibu Sri Mulyani',NULL,NULL,'2026-09-22 00:26:48','2026-09-22 00:26:48'),(4,'07','01','UNTA','kuda','Kota Semarang','BUDI','0812345678','0812345678','2026-10-05 01:13:27','2026-10-05 01:13:27');
/*!40000 ALTER TABLE `data_rt` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `kartu_keluarga`
--

DROP TABLE IF EXISTS `kartu_keluarga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kartu_keluarga` (
  `ID_KK` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID_KK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kartu_keluarga`
--

LOCK TABLES `kartu_keluarga` WRITE;
/*!40000 ALTER TABLE `kartu_keluarga` DISABLE KEYS */;
INSERT INTO `kartu_keluarga` VALUES ('1231241242144444','Jl.Trengguli no 12','2026-10-06 05:53:07','2026-10-06 05:53:07'),('2362174678268345','fdsfsdg','2026-09-22 19:32:38','2026-09-22 19:32:38'),('3201051234567890','Jl. Melati No. 12','2026-09-30 09:25:32','2026-09-30 09:25:32'),('3201051909120002','Harmoni Residence Blok C No. 04','2026-09-22 00:26:49','2026-09-22 00:26:49'),('3201051909120003','Harmoni Residence Blok C No. 08','2026-09-22 00:26:49','2026-09-22 00:26:49'),('3201051909120004','Harmoni Residence Blok A No. 02','2026-09-22 00:26:49','2026-09-22 00:26:49'),('3201051909120005','Harmoni Residence Blok B No. 05','2026-09-22 00:26:49','2026-09-22 00:26:49'),('3201051909120006','Harmoni Residence Blok D No. 07','2026-09-22 00:26:49','2026-09-22 00:26:49'),('3201051909120008','Harmoni Residence Blok B No. 12','2026-09-22 00:26:49','2026-09-22 00:26:49');
/*!40000 ALTER TABLE `kartu_keluarga` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `Admin_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` text COLLATE utf8mb4_unicode_ci,
  `Password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ID_RT` bigint unsigned DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Admin_ID`),
  UNIQUE KEY `admin_username_unique` (`Username`),
  UNIQUE KEY `admin_email_unique` (`email`),
  KEY `admin_id_rt_foreign` (`ID_RT`),
  KEY `admin_google_id_index` (`google_id`),
  CONSTRAINT `admin_id_rt_foreign` FOREIGN KEY (`ID_RT`) REFERENCES `data_rt` (`ID_RT`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'superadmin','heskiacandra@gmail.com','109313813727845207773','https://lh3.googleusercontent.com/a/ACg8ocJ9GZH_PBdHOr2P6-8FFZZvgqUFA23UHtk2_8EHlRn7rJfM6A=s96-c','$2y$12$fUz7lDQRGndmWXv9PMRsUuZO3qPKV42kzcMXo/UvtwEAIRBfcq0CG','super_admin',1,'gH3EAqqrKxyIQtdUfLvSkWhsiLQDjaTLj6T2RPj7AB1SbwBMdsbxLjWJ70b7','2026-09-22 00:26:49','2026-10-05 01:43:16'),(2,'ketua_rt05',NULL,NULL,NULL,'$2y$12$QPj14eI9hSSD/am5vb9I0ejRmdeXj4FxSpwbwpJ1KFDB3vnny1Fgq','admin_rt',1,'w1Zvoi3ECU526IxU2tTCABUNRplgRDhVOxUZgKy3OpmTTABa2SgMTiGugqjw','2026-09-22 00:26:49','2026-09-30 06:21:38'),(3,'bendahara_rt05',NULL,NULL,NULL,'$2y$12$ka6N9BE6g2c0o.TROpjV0.s.cUsD20775tBL4gL9LEb2bVagi9S52','bendahara',1,NULL,'2026-09-22 00:26:49','2026-09-30 06:21:38'),(4,'sekretaris_rt05',NULL,NULL,NULL,'$2y$12$.DAWGiZekZp.k1sOFY.eJOoCClI7DkRLiFSPOPqaRL9SYHBZlNTpK','sekretaris',1,NULL,'2026-09-22 00:26:49','2026-09-30 06:21:38'),(5,'ketua_rt03',NULL,NULL,NULL,'$2y$12$ZTg0.1fNQQi5yDHWgDYNHulR/t/jyG6d0cCrwSOtHwOROZr5aNoVm','admin_rt',3,NULL,'2026-10-05 19:12:55','2026-10-05 19:12:55');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `warga`
--

DROP TABLE IF EXISTS `warga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warga` (
  `NIK` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ID_KK` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_RT` bigint unsigned DEFAULT NULL,
  `Nama_Lengkap` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` text COLLATE utf8mb4_unicode_ci,
  `Username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'warga',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`NIK`),
  UNIQUE KEY `warga_email_unique` (`email`),
  KEY `warga_id_kk_foreign` (`ID_KK`),
  KEY `warga_id_rt_foreign` (`ID_RT`),
  KEY `warga_google_id_index` (`google_id`),
  CONSTRAINT `warga_id_kk_foreign` FOREIGN KEY (`ID_KK`) REFERENCES `kartu_keluarga` (`ID_KK`) ON DELETE SET NULL,
  CONSTRAINT `warga_id_rt_foreign` FOREIGN KEY (`ID_RT`) REFERENCES `data_rt` (`ID_RT`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warga`
--

LOCK TABLES `warga` WRITE;
/*!40000 ALTER TABLE `warga` DISABLE KEYS */;
INSERT INTO `warga` VALUES ('3201051208940001','3201051909120008',1,'Bpk. Hendra Wijaya','0812-3456-7890',NULL,NULL,NULL,'hendra_w','$2y$12$bIQ0O5FxaojRDDzt.ZU/vO9c3Kxo0X0CB6hiL/HAr9ayLjxbwQA4.','1994-08-12','warga',NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),('3201051234567891','3201051234567890',1,'Budi Santoso','081234567890',NULL,NULL,NULL,'budi_santoso','$2y$12$XMzp5eZLjNMK5fVKZlxkC.4Ex7MjAzxrBld.K/ExZzIIenm2ODL4q','1990-01-01','warga',NULL,'2026-09-30 09:25:32','2026-09-30 09:25:32'),('3201223344550005','3201051909120004',1,'Bpk. Agus Santoso','0812-3344-5566',NULL,NULL,NULL,'agus_s','$2y$12$RS010C3NzzmtJ6kdy.Vsa.cmshA/FkY9YLUpmgc1Adc/nPgcvLZpW','1988-06-10','warga',NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),('3201556677880004','3201051909120003',1,'Bpk. Denny Pratama','0813-9988-7766',NULL,NULL,NULL,'denny_p','$2y$12$7XpCWMXFNC3ivHyyQAQ9LOBnPr8ZmVD9OcRkSAAJp1mjfuGAwhpbK','1990-11-15','warga',NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),('3201778899000007','3201051909120005',1,'Ibu Siti Rahma','0857-1122-3344',NULL,NULL,NULL,'siti_r','$2y$12$edS0GLGjSyMKLJs85l.gDuS2TelM4HnPfNkh9Ljdp4F1Fu/jOWD4G','1992-09-02','warga',NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),('3201889900110009','3201051909120006',1,'Ibu Nurhayati','0819-2233-4455',NULL,NULL,NULL,'nurhayati','$2y$12$v0neWBBOy7ibw4ZaQPqUeuUNq77wmkU72W/VXJw3Q1TAXFgzgn5Im','1985-12-19','warga',NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),('3201987654320001','3201051909120002',1,'Ibu Dewi Sartika','0856-1122-3344',NULL,NULL,NULL,'dewi_s','$2y$12$Wu46qwVZkJ4M/TcDTBDUde2B4iPF1BN/vWxuf5sr3U3o2Bz.NWE.S','1996-03-24','warga',NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),('3242342342342434','1231241242144444',1,'Heskia Candra Adinata','081225804124','heskia2007@gmail.com','113518852588396851838','https://lh3.googleusercontent.com/a/ACg8ocL7fZJ52PakFuIfg8Emp9c5cW-uD0Z5RYveV2VC37tP7rI9i7PH=s96-c','heskia2007','$2y$12$ulXYUca3tRIpGNz2shIEvun.guylG6d.HGhPxPegfB6G88qhPnHY6','2007-03-16','warga','W9UX9i3cnwASjxIMy3hWaJzhoey74lTNUUEtW8hSkylWvb5QqOr2obKGZtAw','2026-10-06 05:53:07','2026-10-06 05:53:07'),('3646372463222334','2362174678268345',1,'suie','4325353565',NULL,NULL,NULL,'kia','$2y$12$hr/VspS99s4iSeeAQLwzc.IZwoPGmtbK.tQMgeZXVuoDfZTfp9tUW','2026-08-23','warga',NULL,'2026-09-22 19:32:38','2026-09-22 19:32:38');
/*!40000 ALTER TABLE `warga` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `approval`
--

DROP TABLE IF EXISTS `approval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval` (
  `id_approval` bigint unsigned NOT NULL AUTO_INCREMENT,
  `Admin_ID` bigint unsigned DEFAULT NULL,
  `tabel_sumber` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_referensi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `tgl_approval` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_approval`),
  KEY `approval_admin_id_foreign` (`Admin_ID`),
  CONSTRAINT `approval_admin_id_foreign` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`Admin_ID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval`
--

LOCK TABLES `approval` WRITE;
/*!40000 ALTER TABLE `approval` DISABLE KEYS */;
INSERT INTO `approval` VALUES (1,2,'SURAT','1','Disetujui','Surat pengantar KTP & KK telah diverifikasi dan ditandatangani digital.','2026-09-20 09:15:00','2026-09-22 00:26:51','2026-09-22 00:26:51'),(2,2,'SURAT','2','Disetujui','Surat pengantar SKCK disetujui.','2026-09-19 14:20:00','2026-09-22 00:26:51','2026-09-22 00:26:51'),(3,3,'PEMASUKAN','3','Disetujui','Pembayaran iuran kas via QRIS Rp 75.000 terverifikasi sah masuk rekening kas RT.','2026-09-05 16:45:00','2026-09-22 00:26:51','2026-09-22 00:26:51'),(4,2,'INVENTARIS','1','Disetujui','Peminjaman Tenda Lipat untuk Bpk. Agus Santoso disetujui.','2026-09-20 08:00:00','2026-09-22 00:26:51','2026-09-22 00:26:51'),(5,2,'INVENTARIS','2','Menunggu','Permohonan pinjam Tenda & Kursi oleh Bpk. Denny Pratama untuk acara aqiqah.',NULL,'2026-09-22 00:26:51','2026-09-22 00:26:51'),(6,4,'SURAT','3','Menunggu','Pengajuan Surat Keterangan Usaha (SKU) untuk pendaftaran UMKM binaan RT.',NULL,'2026-09-22 01:41:39','2026-09-22 18:57:10'),(7,2,'PEMASUKAN','4','Disetujui','Konfirmasi pembayaran iuran bulanan September 2026 warga Blok C No. 08 via QRIS.','2026-09-30 16:05:29','2026-09-22 10:55:46','2026-09-30 09:05:29'),(8,2,'KEGIATAN','2','Menunggu','Usulan agenda Senam Kebugaran Lansia dan Cek Tensi Darah berkala di Balai Warga.',NULL,'2026-09-22 10:55:46','2026-09-22 10:55:46'),(9,3,'PENGELUARAN','1','Menunggu','Pengajuan belanja lampu sorot LED 100W lapangan voli balai RT.',NULL,'2026-09-22 10:55:46','2026-09-22 10:55:46'),(10,NULL,'SURAT','7','Menunggu','Permohonan Surat Keterangan Domisili Usaha: Pengajuan untuk izin usaha mikro kuliner balai RT.',NULL,'2026-09-23 01:59:56','2026-09-23 01:59:56'),(11,NULL,'WARGA','3646372463222334','pending','Pendaftaran mandiri akun warga baru',NULL,'2026-09-22 19:32:38','2026-09-22 19:32:38'),(12,2,'INVENTARIS','3','Menunggu','Permohonan pinjam alat Wireless Portable Sound System + 2 Mic oleh Bpk. Hendra Wijaya (NIK: 3201051208940001) untuk keperluan: Acara syukuran keluarga (Periode: 2026-09-23 s.d 2026-09-23).',NULL,'2026-09-22 22:59:54','2026-09-22 22:59:54'),(13,NULL,'WARGA','3201051234567891','pending','Pendaftaran mandiri akun warga baru',NULL,'2026-09-30 09:25:32','2026-09-30 09:25:32'),(14,NULL,'SURAT','8','pending','Pengajuan surat baru via portal warga',NULL,'2026-09-30 16:27:44','2026-09-30 16:27:44'),(15,NULL,'SURAT','8','Menunggu','Permohonan Surat Keterangan Domisili Warga: Persyaratan pembuatan rekening bank baru',NULL,'2026-09-30 16:27:44','2026-09-30 16:27:44'),(16,NULL,'SURAT','9','pending','Pengajuan surat baru via portal warga',NULL,'2026-10-01 17:48:22','2026-10-01 17:48:22'),(17,NULL,'SURAT','9','Menunggu','Permohonan Surat Keterangan Domisili Warga: SKSCK',NULL,'2026-10-01 17:48:22','2026-10-01 17:48:22'),(18,2,'SURAT','10','rejected','Pengajuan surat baru via portal warga | Alasan Penolakan: etrewt','2026-10-05 07:40:57','2026-10-01 18:59:53','2026-10-05 00:40:57'),(19,2,'SURAT','10','rejected','Permohonan Surat Pengantar SKCK Kepolisian: SKCK | Alasan Penolakan: etrewt','2026-10-05 07:40:57','2026-10-01 18:59:53','2026-10-05 00:40:57'),(20,2,'SURAT','11','approved','Pengajuan surat baru via portal warga','2026-10-02 03:09:05','2026-10-02 03:08:49','2026-10-01 20:09:05'),(21,2,'SURAT','11','approved','Permohonan Surat Pengantar Pembuatan / Perubahan KTP & KK: KTP','2026-10-02 03:09:05','2026-10-02 03:08:49','2026-10-01 20:09:05'),(22,2,'SURAT','12','approved','Pengajuan surat baru via portal warga','2026-10-05 07:12:32','2026-10-05 06:49:53','2026-10-05 00:12:32'),(23,2,'SURAT','12','approved','Permohonan Surat Keterangan Kurang Mampu (Beasiswa/Bansos): Buat beasiswa','2026-10-05 07:12:32','2026-10-05 06:49:53','2026-10-05 00:12:32'),(24,NULL,'WARGA','3242342342342434','pending','Pendaftaran mandiri akun warga baru (Google SSO)',NULL,'2026-10-06 05:53:07','2026-10-06 05:53:07');
/*!40000 ALTER TABLE `approval` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `surat`
--

DROP TABLE IF EXISTS `surat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `surat` (
  `surat_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `NIK` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Jenis_surat` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perihal` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_surat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_pengajuan` date NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`surat_id`),
  KEY `surat_nik_foreign` (`NIK`),
  CONSTRAINT `surat_nik_foreign` FOREIGN KEY (`NIK`) REFERENCES `warga` (`NIK`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surat`
--

LOCK TABLES `surat` WRITE;
/*!40000 ALTER TABLE `surat` DISABLE KEYS */;
INSERT INTO `surat` VALUES (1,'3201051208940001','Surat Pengantar Pembuatan KTP & KK Baru','Permohonan surat pengantar untuk pemutakhiran kartu keluarga dan KTP-el ke Kantor Kelurahan Sukamaju.',NULL,'2026-09-20','Disetujui','2026-09-22 00:26:50','2026-09-22 00:26:50'),(2,'3201051208940001','Surat Pengantar SKCK Kepolisian','Kelengkapan administrasi perpanjangan SKCK untuk melamar pekerjaan.',NULL,'2026-09-19','Disetujui','2026-09-22 00:26:50','2026-09-22 00:26:50'),(3,'3201987654320001','Surat Keterangan Domisili Tempat Tinggal','Kelengkapan administrasi pendaftaran sekolah anak dan perbankan.',NULL,'2026-09-18','Disetujui','2026-09-22 00:26:50','2026-09-28 05:30:09'),(4,'3201556677880004','Surat Keterangan Usaha (SKU) Mikro Lingkungan','Pengajuan KUR Mikro Usaha Kuliner Rumahan.',NULL,'2026-09-15','Disetujui','2026-09-22 00:26:50','2026-09-22 00:26:50'),(5,'3201223344550005','Surat Keterangan Izin Keramaian Lingkungan','Izin mengadakan syukuran aqiqah di halaman rumah pada Sabtu mendatang.',NULL,'2026-09-21','Menunggu','2026-09-22 00:26:50','2026-09-22 00:26:50'),(6,'3201051208940001','Surat Keterangan Domisili Warga','Permohonan surat keterangan domisili untuk perbankan.',NULL,'2026-09-22','Menunggu','2026-09-22 12:12:22','2026-09-22 12:12:22'),(7,'3201051208940001','Surat Keterangan Domisili Usaha','Pengajuan untuk izin usaha mikro kuliner balai RT.',NULL,'2026-09-23','Menunggu','2026-09-23 01:59:56','2026-09-23 01:59:56'),(8,'3201051234567891','Surat Keterangan Domisili Warga','Persyaratan pembuatan rekening bank baru',NULL,'2026-09-30','Menunggu','2026-09-30 16:27:44','2026-09-30 16:27:44'),(9,'3201987654320001','Surat Keterangan Domisili Warga','SKSCK',NULL,'2026-10-02','Menunggu','2026-10-01 17:48:22','2026-10-01 10:58:11'),(10,'3201051208940001','Surat Pengantar SKCK Kepolisian','SKCK',NULL,'2026-10-02','rejected','2026-10-01 18:59:53','2026-10-05 00:40:57'),(11,'3201987654320001','Surat Pengantar Pembuatan / Perubahan KTP & KK','KTP','011/SP-RT05/RW01/X/2026','2026-10-02','approved','2026-10-02 03:08:49','2026-10-01 20:09:05'),(12,'3201987654320001','Surat Keterangan Kurang Mampu (Beasiswa/Bansos)','Buat beasiswa','012/SP-RT05/RW01/X/2026','2026-10-05','approved','2026-10-05 06:49:53','2026-10-05 00:12:32');
/*!40000 ALTER TABLE `surat` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `aspirasi`
--

DROP TABLE IF EXISTS `aspirasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aspirasi` (
  `id_aspirasi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `NIK` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Isi_Aspirasi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_aspirasi`),
  KEY `aspirasi_nik_foreign` (`NIK`),
  CONSTRAINT `aspirasi_nik_foreign` FOREIGN KEY (`NIK`) REFERENCES `warga` (`NIK`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aspirasi`
--

LOCK TABLES `aspirasi` WRITE;
/*!40000 ALTER TABLE `aspirasi` DISABLE KEYS */;
INSERT INTO `aspirasi` VALUES (1,'3201556677880004','Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.','2026-09-22 00:26:50','2026-09-22 00:26:50'),(2,'3201889900110009','Saran agar di sekitar pendopo disediakan tempat sampah pilah organik dan anorganik agar warga dan anak-anak terbiasa memilah sampah sejak dini.','2026-09-22 00:26:50','2026-09-22 00:26:50'),(3,'3201051208940001','Mengingat sudah mulai masuk masa pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti lingkungan akhir pekan ini.','2026-09-22 00:26:50','2026-09-22 00:26:50'),(4,'3201051208940001','[fasilitas] Perbaikan Lampu Jalan Blok B: Lampu penerangan jalan di depan Blok B No. 10 padam sejak kemarin malam. Mohon dapat diperbaiki.','2026-09-22 12:16:39','2026-09-22 12:16:39'),(5,'3201051234567891','[Fasilitas Umum] Perbaikan Lampu Taman Blok A: Mohon perbaikan penerangan di area taman Blok A demi keamanan warga di malam hari.','2026-09-30 09:30:32','2026-09-30 09:30:32'),(6,'3201051208940001','[keamanan] Pembanguna: fsuifiseufsiue','2026-10-01 20:13:51','2026-10-01 20:13:51'),(7,'3201987654320001','[kebersihan] Pembanguna: xzvcdszs','2026-10-02 22:00:00','2026-10-02 22:00:00'),(8,'3201889900110009','Uji coba aspirasi baru untuk respon.','2026-10-04 06:35:18','2026-10-04 06:35:18'),(9,'3201889900110009','Uji coba aspirasi baru untuk respon.','2026-10-04 06:35:46','2026-10-04 06:35:46'),(10,'3201889900110009','Uji coba aspirasi baru untuk respon.','2026-10-04 06:36:34','2026-10-04 06:36:34'),(11,'3201051208940001','[keamanan] xcxzc: dd','2026-10-06 05:54:28','2026-10-06 05:54:28');
/*!40000 ALTER TABLE `aspirasi` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `respon_aspirasi`
--

DROP TABLE IF EXISTS `respon_aspirasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `respon_aspirasi` (
  `id_aspirasi` bigint unsigned NOT NULL,
  `nama_responden` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi_respon` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_respon` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_aspirasi`),
  CONSTRAINT `respon_aspirasi_id_aspirasi_foreign` FOREIGN KEY (`id_aspirasi`) REFERENCES `aspirasi` (`id_aspirasi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respon_aspirasi`
--

LOCK TABLES `respon_aspirasi` WRITE;
/*!40000 ALTER TABLE `respon_aspirasi` DISABLE KEYS */;
INSERT INTO `respon_aspirasi` VALUES (1,'Pengurus RT 05','Tanggapan uji coba fitur aspirasi','2026-10-04 13:17:06','2026-09-22 00:26:50','2026-10-04 06:17:06'),(3,'Ibu Ratna Paramitha (Sekretaris)','Sudah diagendakan dalam jadwal kerja bakti serentak Sabtu pagi ini.','2026-09-21 08:45:00','2026-09-22 00:26:50','2026-09-22 00:26:50'),(7,'Pengurus RT (ketua_rt05)','oke\n','2026-10-04 13:43:43','2026-10-04 06:43:43','2026-10-04 06:43:43'),(10,'Pengurus RT (superadmin)','Terima kasih atas aspirasinya, akan segera kami tindak lanjuti.','2026-10-04 13:46:23','2026-10-04 06:46:23','2026-10-04 06:46:23');
/*!40000 ALTER TABLE `respon_aspirasi` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `kegiatan`
--

DROP TABLE IF EXISTS `kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kegiatan` (
  `kegiatan_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ID_RT` bigint unsigned DEFAULT NULL,
  `nama_kegiatan` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_kegiatan` date NOT NULL,
  `lokasi` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Mendatang',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kegiatan_id`),
  KEY `kegiatan_id_rt_foreign` (`ID_RT`),
  CONSTRAINT `kegiatan_id_rt_foreign` FOREIGN KEY (`ID_RT`) REFERENCES `data_rt` (`ID_RT`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kegiatan`
--

LOCK TABLES `kegiatan` WRITE;
/*!40000 ALTER TABLE `kegiatan` DISABLE KEYS */;
INSERT INTO `kegiatan` VALUES (1,1,'Kerja Bakti Massal & Fogging Nyamuk DBD','2026-09-26','Balai Warga & Lingkungan RT 05','Pelaksanaan Sabtu pukul 07.00 WIB. Pembersihan saluran air dan fogging serentak.','Selesai','2026-09-22 00:26:50','2026-10-02 07:27:50'),(2,1,'Pelatihan Hidroponik PKK Mandiri Pangan','2026-10-04','Taman Samping Balai RT','Program pemberdayaan warga: budidaya sayur hidroponik pipa PVC dan nutrisi AB Mix.','Mendatang','2026-09-22 00:26:50','2026-09-22 00:26:50'),(3,1,'Senam Sehat Lansia & Cek Tensi Gratis','2026-09-27','Halaman Balai Warga','Senam kebugaran bersama dilanjutkan pemeriksaan tekanan darah dan gula darah berkala.','Mendatang','2026-09-22 00:26:50','2026-09-22 00:26:50'),(4,1,'MAKAN MAKAN','2026-10-02','GBK','dsfrsd','Mendatang','2026-10-02 07:27:24','2026-10-02 07:27:24');
/*!40000 ALTER TABLE `kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `pemasukan`
--

DROP TABLE IF EXISTS `pemasukan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemasukan` (
  `Tagihan_Id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ID_KK` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ID_RT` bigint unsigned DEFAULT NULL,
  `nama_tagihan` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `tgl_jatuh_tmp` date NOT NULL,
  `status_tagihan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Lunas',
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Tagihan_Id`),
  KEY `pemasukan_id_kk_foreign` (`ID_KK`),
  KEY `pemasukan_id_rt_foreign` (`ID_RT`),
  KEY `pemasukan_order_id_index` (`order_id`),
  CONSTRAINT `pemasukan_id_kk_foreign` FOREIGN KEY (`ID_KK`) REFERENCES `kartu_keluarga` (`ID_KK`) ON DELETE SET NULL,
  CONSTRAINT `pemasukan_id_rt_foreign` FOREIGN KEY (`ID_RT`) REFERENCES `data_rt` (`ID_RT`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemasukan`
--

LOCK TABLES `pemasukan` WRITE;
/*!40000 ALTER TABLE `pemasukan` DISABLE KEYS */;
INSERT INTO `pemasukan` VALUES (1,'3201051909120008','Iuran Warga',1,'Iuran Keamanan & Kebersihan Juli 2026',75000.00,'2026-07-10','Belum Lunas',NULL,'snap-token-IURAN-RT1-TAG1-1791142891','IURAN-RT1-TAG1-1791142891','qris',NULL,'{\"issuer\": \"gopay\", \"acquirer\": \"gopay\", \"currency\": \"IDR\", \"order_id\": \"IURAN-RT1-TAG1-1791142891\", \"expiry_time\": \"2026-10-05 02:56:34\", \"merchant_id\": \"M350420938\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"75000.00\", \"payment_type\": \"qris\", \"signature_key\": \"8bc05a19b5e57b63ad7274902325cdcdc29e227ec641cd2cb5155e5e0f7422e50b6a1c87c9ce8d54c50f9ba75da44daeec3a466c7d56a0bf8f56afbb8b34bab3\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"70d38407-f97d-4529-9dca-f3cd488cbfc2\", \"settlement_time\": \"2026-10-05 02:41:45\", \"transaction_time\": \"2026-10-05 02:41:34\", \"transaction_type\": \"on-us\", \"transaction_status\": \"settlement\", \"merchant_cross_reference_id\": \"1b066ec0-c89a-425d-b1b2-59350a379d99\"}','2026-09-22 00:26:50','2026-10-06 18:24:57'),(2,'3201051909120008','Iuran Warga',1,'Iuran Keamanan & Kebersihan Agustus 2026',75000.00,'2026-08-10','Lunas','uploads/bukti/bukti_iuran_1790129399_6ab334f77bb78.jpg',NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),(3,'3201051909120008','Iuran Warga',1,'Iuran Keamanan & Kebersihan September 2026',75000.00,'2026-09-10','Lunas','uploads/bukti/bukti_iuran_1790129427_6ab335133cbfc.jpg',NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:26:50','2026-09-30 06:21:40'),(4,'3201051909120002','Iuran Warga',1,'Iuran Keamanan & Kebersihan September 2026',75000.00,'2026-09-10','Lunas','uploads/bukti/bukti_iuran_1790129459_6ab3353352069.jpg',NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:26:50','2026-09-30 09:05:29'),(5,'3201051909120003','Iuran Warga',1,'Iuran Keamanan & Kebersihan September 2026',75000.00,'2026-09-10','Lunas',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:26:50','2026-09-22 01:41:39'),(6,'3201051909120004','Iuran Warga',1,'Iuran Keamanan & Kebersihan September 2026',75000.00,'2026-09-10','Lunas',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:26:50','2026-09-22 01:41:39'),(7,'3201051909120005','Iuran Warga',1,'Iuran Keamanan & Kebersihan September 2026',75000.00,'2026-09-10','Belum Lunas',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:26:50','2026-09-22 10:55:46'),(8,'3201051909120006','Iuran Warga',1,'Iuran Keamanan & Kebersihan September 2026',75000.00,'2026-09-10','Belum Lunas',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:26:50','2026-09-22 10:55:46'),(9,'3201051909120008','Iuran Warga',1,'Iuran Keamanan & Kebersihan Oktober 2026',75000.00,'2026-10-31','Lunas',NULL,'snap-token-demo-9-87930fd478d661c7','IURAN-RT1-TAG9-1790954659','qris','2026-10-02 15:25:21','{\"order_id\": \"IURAN-RT1-TAG9-1790954659\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"75000\", \"payment_type\": \"qris\", \"transaction_status\": \"settlement\"}','2026-10-02 08:15:10','2026-10-02 08:25:21'),(10,'3201051909120002','Iuran Warga',1,'Iuran Keamanan & Kebersihan Oktober 2026',75000.00,'2026-10-31','Belum Lunas',NULL,'snap-token-IURAN-RT1-TAG10-1791252284','IURAN-RT1-TAG10-1791252284','qris',NULL,'{\"issuer\": \"airpay shopee\", \"acquirer\": \"gopay\", \"currency\": \"IDR\", \"order_id\": \"IURAN-RT1-TAG10-1791252284\", \"expiry_time\": \"2026-10-06 09:19:51\", \"merchant_id\": \"M350420938\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"75000.00\", \"payment_type\": \"qris\", \"signature_key\": \"b5b23360069f05d59563a4699e1eee203432b29cc2a40c7255a956d2576d16bff05ad6f04d22e6e3130eb0ebf665adef72b6d19675c810eccfedbf9e90d62d25\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"e2c29448-9885-42da-9fcf-4ea71535e5eb\", \"settlement_time\": \"2026-10-06 09:06:23\", \"transaction_time\": \"2026-10-06 09:04:51\", \"transaction_type\": \"off-us\", \"transaction_status\": \"settlement\"}','2026-10-02 21:59:07','2026-10-06 18:24:37'),(11,'1231241242144444','Iuran Warga',1,'Iuran Keamanan & Kebersihan Oktober 2026',75000.00,'2026-10-31','Lunas',NULL,NULL,'SIMULATE-IURAN-TAG11-1791292051','qris','2026-10-06 13:07:31','{\"order_id\": \"SIMULATE-IURAN-TAG11-1791292051\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"75000\", \"payment_type\": \"qris\", \"transaction_status\": \"settlement\"}','2026-10-06 05:53:08','2026-10-06 06:07:31');
/*!40000 ALTER TABLE `pemasukan` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `pengeluaran`
--

DROP TABLE IF EXISTS `pengeluaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengeluaran` (
  `Pengeluaran_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ID_RT` bigint unsigned DEFAULT '1',
  `Kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Keterangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Jumlah_Bayar` decimal(15,2) NOT NULL,
  `Tgl_Bayar` date NOT NULL,
  `Metode_Bayar` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kas Tunai',
  `Bukti_Path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Pengeluaran_id`),
  KEY `pengeluaran_id_rt_foreign` (`ID_RT`),
  CONSTRAINT `pengeluaran_id_rt_foreign` FOREIGN KEY (`ID_RT`) REFERENCES `data_rt` (`ID_RT`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengeluaran`
--

LOCK TABLES `pengeluaran` WRITE;
/*!40000 ALTER TABLE `pengeluaran` DISABLE KEYS */;
INSERT INTO `pengeluaran` VALUES (1,1,'Keamanan','Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',4162500.00,'2026-09-02','Transfer Bank','bukti/nota_lampu_philips.jpg','2026-09-22 00:26:50','2026-09-30 06:21:40'),(2,1,'Kebersihan','Retribusi Angkut Sampah DLH & Kebersihan Selokan',2775000.00,'2026-09-04','Kas Tunai','bukti/nota_lampu_philips.jpg','2026-09-22 00:26:51','2026-09-30 06:21:40'),(3,1,'Fasilitas & Aset','Pembelian Lampu Sorot LED & Kabel Ekstensi Balai',450000.00,'2026-09-10','Kas Tunai','bukti/nota_lampu_philips.jpg','2026-09-22 00:26:51','2026-09-22 00:26:51');
/*!40000 ALTER TABLE `pengeluaran` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `inventaris`
--

DROP TABLE IF EXISTS `inventaris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventaris` (
  `Inventaris_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ID_RT` bigint unsigned DEFAULT NULL,
  `Nama_Barang` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Kode_Barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Jumlah` int NOT NULL DEFAULT '1',
  `Lokasi` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Balai RT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Inventaris_ID`),
  UNIQUE KEY `inventaris_kode_barang_unique` (`Kode_Barang`),
  KEY `inventaris_id_rt_foreign` (`ID_RT`),
  CONSTRAINT `inventaris_id_rt_foreign` FOREIGN KEY (`ID_RT`) REFERENCES `data_rt` (`ID_RT`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventaris`
--

LOCK TABLES `inventaris` WRITE;
/*!40000 ALTER TABLE `inventaris` DISABLE KEYS */;
INSERT INTO `inventaris` VALUES (1,1,'Tenda Lipat (3x6 Meter)','AST-TND-01',2,'Gudang Balai RT 05','2026-09-22 00:26:51','2026-09-22 00:26:51'),(2,1,'Set Kursi Lipat Warga (50 Unit)','AST-KRS-01',101,'Gudang Balai RT 05','2026-09-22 00:26:51','2026-10-02 07:30:57'),(3,1,'Wireless Portable Sound System + 2 Mic','AST-SND-01',1,'Ruang Sekretariat Balai RT','2026-09-22 00:26:51','2026-09-22 00:26:51'),(4,1,'Proyektor Epson & Layar Tripod 84 Inch','AST-PRJ-01',1,'Ruang Sekretariat Balai RT','2026-09-22 00:26:51','2026-09-22 00:26:51'),(5,1,'Mesin Pemotong Rumput Dorong Honda','AST-RMPT-01',2,'Gudang Samping Pos Ronda','2026-09-22 00:26:51','2026-09-22 00:26:51'),(6,1,'Paket Rompi & Senter Ronda Keamanan (6 Set)','AST-RND-01',6,'Pos Ronda Utama RT 05','2026-09-22 00:26:51','2026-09-22 00:26:51');
/*!40000 ALTER TABLE `inventaris` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `pembelian_inventaris`
--

DROP TABLE IF EXISTS `pembelian_inventaris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembelian_inventaris` (
  `Pembelian_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventaris_id` bigint unsigned DEFAULT NULL,
  `Pengeluaran_id` bigint unsigned DEFAULT NULL,
  `tgl_pembelian` date NOT NULL,
  `total_biaya` decimal(15,2) NOT NULL,
  `jumlah_pembelian` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Pembelian_id`),
  KEY `pembelian_inventaris_inventaris_id_foreign` (`inventaris_id`),
  KEY `pembelian_inventaris_pengeluaran_id_foreign` (`Pengeluaran_id`),
  CONSTRAINT `pembelian_inventaris_inventaris_id_foreign` FOREIGN KEY (`inventaris_id`) REFERENCES `inventaris` (`Inventaris_ID`) ON DELETE SET NULL,
  CONSTRAINT `pembelian_inventaris_pengeluaran_id_foreign` FOREIGN KEY (`Pengeluaran_id`) REFERENCES `pengeluaran` (`Pengeluaran_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembelian_inventaris`
--

LOCK TABLES `pembelian_inventaris` WRITE;
/*!40000 ALTER TABLE `pembelian_inventaris` DISABLE KEYS */;
INSERT INTO `pembelian_inventaris` VALUES (1,3,3,'2026-08-15',2450000.00,1,'2026-09-22 00:26:51','2026-09-22 00:26:51'),(2,5,3,'2026-07-20',3200000.00,1,'2026-09-22 00:26:51','2026-09-22 00:26:51');
/*!40000 ALTER TABLE `pembelian_inventaris` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `kontak_rt`
--

DROP TABLE IF EXISTS `kontak_rt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kontak_rt` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ID_RT` bigint unsigned DEFAULT NULL,
  `role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kontak` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_telepon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_whatsapp` tinyint(1) NOT NULL DEFAULT '1',
  `pesan_wa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_tampilan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'footer_darurat',
  `urutan` int NOT NULL DEFAULT '0',
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kontak_rt_id_rt_foreign` (`ID_RT`),
  CONSTRAINT `kontak_rt_id_rt_foreign` FOREIGN KEY (`ID_RT`) REFERENCES `data_rt` (`ID_RT`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontak_rt`
--

LOCK TABLES `kontak_rt` WRITE;
/*!40000 ALTER TABLE `kontak_rt` DISABLE KEYS */;
INSERT INTO `kontak_rt` VALUES (1,1,'Ketua RT 05','Bpk. Sukardi Hartono','0812-3456-7890',1,'Halo Ketua RT 05, saya warga Harmoni Residence','footer_darurat',1,1,'2026-10-05 00:58:06','2026-10-05 00:58:06'),(2,1,'Pos Ronda & Keamanan','Piket Regu Sekuriti','0821-9876-5432',1,'Halo Pos Ronda RT 05, saya warga ingin melapor keadaan keamanan','footer_darurat',2,1,'2026-10-05 00:58:06','2026-10-05 00:58:06'),(3,1,'Bhabinkamtibmas Polsek','Aiptu Hendro Wibowo','0813-8822-1100',1,'Halo Bhabinkamtibmas Polsek, saya warga RT 05 Harmoni Residence','footer_darurat',3,1,'2026-10-05 00:58:06','2026-10-05 00:58:06'),(4,1,'Ambulans & Damkar','Posko Siaga Darurat','112',0,NULL,'footer_darurat',4,0,'2026-10-05 00:58:06','2026-10-05 01:14:19'),(5,1,'Sekretariat Lingkungan','Balai Warga RT 05 / RW 01','0812-3456-7890',1,'Halo Pengurus RT 05, saya warga Harmoni Residence','sekretariat',5,1,'2026-10-05 00:58:06','2026-10-05 00:58:06');
/*!40000 ALTER TABLE `kontak_rt` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` text COLLATE utf8mb4_unicode_ci,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_google_id_index` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Bima Perkasa (Super Admin)','superadmin@ruangwarga.id',NULL,NULL,NULL,'$2y$12$kxBR6pNCQJf4mO6JxpmFmeUGyolJgOztnOd.JtadbQVtGMSzFGIra',NULL,'2026-09-20 20:36:13','2026-09-20 21:46:18'),(2,'Bpk. Sukardi Hartono (Ketua RT 05)','ketua@ruangwarga.id',NULL,NULL,NULL,'$2y$12$WpKlYTaFAWajnsONQP49iOAtGF.Fix4vUnP4O9lNpAUK2byI69cnG',NULL,'2026-09-20 20:36:13','2026-09-20 21:46:18'),(3,'Bpk. Rahmat Hendrawan (Bendahara)','bendahara@ruangwarga.id',NULL,NULL,NULL,'$2y$12$qmApdzCxiYplC6oZqp7Q6Oou3JXDxuhlOJq20knm3qt9YIoXesqdm',NULL,'2026-09-20 20:36:13','2026-09-20 21:46:18'),(4,'Ibu Ratna Paramitha (Sekretaris)','sekretaris@ruangwarga.id',NULL,NULL,NULL,'$2y$12$rs17P3kdy5/0Pg.vCELAA.UsJKhmxKvujhkJwGQUfFvGXB0/yl6wC',NULL,'2026-09-20 20:36:13','2026-09-20 21:46:18'),(5,'Bpk. Hendra Wijaya (Warga)','hendra@ruangwarga.id',NULL,NULL,NULL,'$2y$12$N9xHXomLnxPh04rX3c1.Ju5opvUN9ghL7YDy0WIoO4XzCzsw0HcWO',NULL,'2026-09-20 20:36:13','2026-09-20 21:46:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('aF4zbzJDdye7CC2HApF2nfZl7RyqLkFilSqzlXkH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI2ZmhzUkl4ajdRVHh5bjFVUlNnTFhpbjFaSlhoRHNqaldDQXFIUDMzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hdXRoXC9nb29nbGVcL3JlZGlyZWN0P2ludGVudD1yZWdpc3RlciIsInJvdXRlIjoiYXV0aC5nb29nbGUucmVkaXJlY3QifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJzdGF0ZSI6ImhWd1VhSVR6SUNhaVpOc0dyRFFDeUNKbHo5UUtzcjVvcVNZM3BSNHoiLCJnb29nbGVfYXV0aF9pbnRlbnQiOiJyZWdpc3RlciJ9',1791291018),('ixQQ2gdTd8hJ4muRvch1YvtSKgQ6aiVsswJXlAOC',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ1UlV5WlE0emcwUlNOUUJPT0lMdEtPZzNOMmllcmFaOHJydVRCTExlIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2FkbWluXC9wZW1hc3VrYW5zIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5yZXNvdXJjZXMucGVtYXN1a2Fucy5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiYWN0aXZlX3JvbGUiOiJhZG1pbl9ydCIsImF1dGhfdXNlciI6eyJ1c2VybmFtZSI6ImtldHVhX3J0MDUiLCJyb2xlIjoiYWRtaW5fcnQiLCJBZG1pbl9JRCI6MiwiSURfUlQiOjF9LCJ0YWJsZXMiOnsiNjRlZTJhZDhkNmMwYjg4ZDE1YTQwNmFmMGU2Y2ZkMzBfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJpZF9hcHByb3ZhbCIsImxhYmVsIjoiSUQgVGlrZXQiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoidGFiZWxfc3VtYmVyIiwibGFiZWwiOiJLYXRlZ29yaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJzb3VyY2VfZGV0YWlsIiwibGFiZWwiOiJVcmFpYW4gUGVuZ2FqdWFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNhdGF0YW4iLCJsYWJlbCI6IktldGVyYW5nYW4gXC8gS2VwZXJsdWFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6Ildha3R1IE1hc3VrIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InN0YXR1cyIsImxhYmVsIjoiU3RhdHVzIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJkZjZmN2RlZjU3NDlhNWFmNTBjYWI2ZTAzNGVjYjU5ZV9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Ik5JSyIsImxhYmVsIjoiTklLIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Ik5hbWFfTGVuZ2thcCIsImxhYmVsIjoiTmFtYSBMZW5na2FwIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5vX3RlbHAiLCJsYWJlbCI6Ik5vLiBXaGF0c0FwcCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJJRF9LSyIsImxhYmVsIjoiTm8uIEthcnR1IEtlbHVhcmdhIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6dHJ1ZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpmYWxzZX0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IklEX1JUIiwibGFiZWwiOiJSVCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ0Z2xfbGFoaXIiLCJsYWJlbCI6IlRnbCBMYWhpciIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJjcmVhdGVkX2F0IiwibGFiZWwiOiJUZXJkYWZ0YXIiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6ZmFsc2UsImlzVG9nZ2xlYWJsZSI6dHJ1ZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0Ijp0cnVlfV0sIjBkOGU4ODAyMzE4ZTEwNGYwNmU1ZjAzNzkyNzc4MmVjX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiaWRfYXNwaXJhc2kiLCJsYWJlbCI6IklEIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IlRhbmdnYWwgTWFzdWsiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoid2FyZ2EuTmFtYV9MZW5na2FwIiwibGFiZWwiOiJQZW5naXJpbSBBc3BpcmFzaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJJc2lfQXNwaXJhc2kiLCJsYWJlbCI6IkN1cGxpa2FuIElzaSBBc3BpcmFzaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyZXNwb25Bc3BpcmFzaS5pc2lfcmVzcG9uIiwibGFiZWwiOiJTdGF0dXMgUmVzcG9uIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJmYmQ1MGJiMjg5OTNjNjQ0NjUwZjE3M2YyNzg1MDlmNl9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IlRhZ2loYW5fSWQiLCJsYWJlbCI6IklEIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5hbWFfa2VwYWxhX2tlbHVhcmdhIiwibGFiZWwiOiJXYXJnYSBcLyBOby4gS0siLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibmFtYV90YWdpaGFuIiwibGFiZWwiOiJSaW5jaWFuIFRhZ2loYW4iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibm9taW5hbCIsImxhYmVsIjoiTm9taW5hbCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJzdGF0dXNfdGFnaWhhbiIsImxhYmVsIjoiU3RhdHVzIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImJ1a3RpX2JheWFyIiwibGFiZWwiOiJCdWt0aSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJtZXRvZGVfcGVtYmF5YXJhbiIsImxhYmVsIjoiTWV0b2RlIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJmYmQ1MGJiMjg5OTNjNjQ0NjUwZjE3M2YyNzg1MDlmNl9wZXJfcGFnZSI6IjI1In19',1791292097),('jn0XybP3eRvx1ysV6tdsLED4cTFrGHJ1UOv8v87H',3201987654320001,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJaUGE0Z2JwWm9NRmE2eFVvWTdHWFlWMU9DRDZkbkRndGJvV2RuYjdyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9wb3J0YWwiLCJyb3V0ZSI6InBvcnRhbC5ob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2FyZ2FfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6IjMyMDE5ODc2NTQzMjAwMDEiLCJhY3RpdmVfcm9sZSI6IndhcmdhIiwiY2l0aXplbl9uaWsiOiIzMjAxOTg3NjU0MzIwMDAxIiwiY2l0aXplbl9uYW1lIjoiSWJ1IERld2kgU2FydGlrYSIsImNpdGl6ZW5faWQiOiIzMjAxOTg3NjU0MzIwMDAxIiwiY2l0aXplbl9ob3VzZSI6Ikhhcm1vbmkgUmVzaWRlbmNlIEJsb2sgQyBOby4gMDQifQ==',1791794140),('rtmEQyBAS6vWY83MldFauF6q24wuWWtBT6p8GgQ5',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJnR1dSdXlMbGNQcjRCM1cwRlVLTDdLQjhGaUowcGtNQWNqNmZRSzdHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pbiIsInJvdXRlIjoiZmlsYW1lbnQuYWRtaW4ucGFnZXMuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsImFjdGl2ZV9yb2xlIjoiYWRtaW5fcnQiLCJhdXRoX3VzZXIiOnsidXNlcm5hbWUiOiJrZXR1YV9ydDA1Iiwicm9sZSI6ImFkbWluX3J0IiwiQWRtaW5fSUQiOjIsIklEX1JUIjoxfX0=',1791336179),('zgUzcg6aZtQmTg8BCo2FIdRNGYufzWKURrw3v9WB',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJXOWxSWGlpRU1HdHhoaUJBeEtMQjI3dVJ0OHJxM3o0Q3lxNjlhRVJCIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC9rb250YWstcnRzIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5yZXNvdXJjZXMua29udGFrLXJ0cy5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiYWN0aXZlX3JvbGUiOiJhZG1pbl9ydCIsImF1dGhfdXNlciI6eyJ1c2VybmFtZSI6ImtldHVhX3J0MDUiLCJyb2xlIjoiYWRtaW5fcnQiLCJBZG1pbl9JRCI6MiwiSURfUlQiOjF9LCJ0YWJsZXMiOnsiNjRlZTJhZDhkNmMwYjg4ZDE1YTQwNmFmMGU2Y2ZkMzBfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJpZF9hcHByb3ZhbCIsImxhYmVsIjoiSUQgVGlrZXQiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoidGFiZWxfc3VtYmVyIiwibGFiZWwiOiJLYXRlZ29yaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJzb3VyY2VfZGV0YWlsIiwibGFiZWwiOiJVcmFpYW4gUGVuZ2FqdWFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNhdGF0YW4iLCJsYWJlbCI6IktldGVyYW5nYW4gXC8gS2VwZXJsdWFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6Ildha3R1IE1hc3VrIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InN0YXR1cyIsImxhYmVsIjoiU3RhdHVzIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJmYmQ1MGJiMjg5OTNjNjQ0NjUwZjE3M2YyNzg1MDlmNl9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IlRhZ2loYW5fSWQiLCJsYWJlbCI6IklEIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5hbWFfa2VwYWxhX2tlbHVhcmdhIiwibGFiZWwiOiJXYXJnYSBcLyBOby4gS0siLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibmFtYV90YWdpaGFuIiwibGFiZWwiOiJSaW5jaWFuIFRhZ2loYW4iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibm9taW5hbCIsImxhYmVsIjoiTm9taW5hbCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJzdGF0dXNfdGFnaWhhbiIsImxhYmVsIjoiU3RhdHVzIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImJ1a3RpX2JheWFyIiwibGFiZWwiOiJCdWt0aSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJtZXRvZGVfcGVtYmF5YXJhbiIsImxhYmVsIjoiTWV0b2RlIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJkYmVmMjk1YmI4ZjYyZDE0NDU1ZTVmMTM1ODM4YTU1Y19jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImlkX2FwcHJvdmFsIiwibGFiZWwiOiJJRCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ0YWJlbF9zdW1iZXIiLCJsYWJlbCI6IkthdGVnb3JpIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InNvdXJjZV9kZXRhaWwiLCJsYWJlbCI6IlVyYWlhbiBQZW5nYWp1YW4iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY2F0YXRhbiIsImxhYmVsIjoiQ2F0YXRhbiBWZXJpZmlrYXNpIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InN0YXR1cyIsImxhYmVsIjoiU3RhdHVzIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InRnbF9hcHByb3ZhbCIsImxhYmVsIjoiV2FrdHUgUHJvc2VzIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH1dLCJkZjZmN2RlZjU3NDlhNWFmNTBjYWI2ZTAzNGVjYjU5ZV9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Ik5JSyIsImxhYmVsIjoiTklLIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Ik5hbWFfTGVuZ2thcCIsImxhYmVsIjoiTmFtYSBMZW5na2FwIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5vX3RlbHAiLCJsYWJlbCI6Ik5vLiBXaGF0c0FwcCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJJRF9LSyIsImxhYmVsIjoiTm8uIEthcnR1IEtlbHVhcmdhIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6dHJ1ZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpmYWxzZX0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IklEX1JUIiwibGFiZWwiOiJSVCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ0Z2xfbGFoaXIiLCJsYWJlbCI6IlRnbCBMYWhpciIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJjcmVhdGVkX2F0IiwibGFiZWwiOiJUZXJkYWZ0YXIiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6ZmFsc2UsImlzVG9nZ2xlYWJsZSI6dHJ1ZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0Ijp0cnVlfV0sIjhiNzMxYTViZWM4YzY0YmU0MmMzOGEyYzk3MjFhYmMxX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiSURfS0siLCJsYWJlbCI6Ik5vbW9yIEtLIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IkFsYW1hdCIsImxhYmVsIjoiQWxhbWF0IERvbWlzaWxpIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IndhcmdhX2NvdW50IiwibGFiZWwiOiJKdW1sYWggQW5nZ290YSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJjcmVhdGVkX2F0IiwibGFiZWwiOiJUZXJkYWZ0YXIgU2VqYWsiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfV0sImFiYjkxODQxNzc0ZjFhZmYwMDc3MzNhZjhjNGVkYTk2X2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiUGVuZ2VsdWFyYW5faWQiLCJsYWJlbCI6IklEIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IkthdGVnb3JpIiwibGFiZWwiOiJLYXRlZ29yaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJLZXRlcmFuZ2FuIiwibGFiZWwiOiJLZXRlcmFuZ2FuIFwvIEtlcGVybHVhbiIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJKdW1sYWhfQmF5YXIiLCJsYWJlbCI6Ik5vbWluYWwgS2VsdWFyIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IlRnbF9CYXlhciIsImxhYmVsIjoiVGFuZ2dhbCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJNZXRvZGVfQmF5YXIiLCJsYWJlbCI6Ik1ldG9kZSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9XSwiMTIzZGJlODUwMjMyNzg0YTU2YjZhM2EwNjY3ZTRiM2VfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJzdXJhdF9pZCIsImxhYmVsIjoiTm8uIFJlZiIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOnRydWUsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6ZmFsc2V9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ3YXJnYS5OYW1hX0xlbmdrYXAiLCJsYWJlbCI6IlBlbW9ob24iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiSmVuaXNfc3VyYXQiLCJsYWJlbCI6IkxheWFuYW4gJmFtcDsgS2VwZXJsdWFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5vbW9yX3N1cmF0IiwibGFiZWwiOiJOb21vciBSZWdpc3RyYXNpIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InRnbF9wZW5nYWp1YW4iLCJsYWJlbCI6IlRhbmdnYWwgTWFzdWsiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoic3RhdHVzIiwibGFiZWwiOiJTdGF0dXMiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfV0sIjI0MmJmOWU4NjE0YTAyZWJhOWMwNzVlOGNjZGJjNGVjX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoia2VnaWF0YW5faWQiLCJsYWJlbCI6IklEIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Im5hbWFfa2VnaWF0YW4iLCJsYWJlbCI6Ik5hbWEgQWdlbmRhICZhbXA7IFJpbmNpYW4iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoidGdsX2tlZ2lhdGFuIiwibGFiZWwiOiJKYWR3YWwgUGVsYWtzYW5hYW4iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibG9rYXNpIiwibGFiZWwiOiJMb2thc2kiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoic3RhdHVzIiwibGFiZWwiOiJTdGF0dXMiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZGF0YVJ0Lm5vbW9yX3J0IiwibGFiZWwiOiJVbml0IFJUIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOmZhbHNlLCJpc1RvZ2dsZWFibGUiOnRydWUsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6dHJ1ZX1dLCI2NTYyMGEzODk1MTQxMWU4NDAyNTAwMmUyMmQ4ZTJiZF9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IktvZGVfQmFyYW5nIiwibGFiZWwiOiJLb2RlIEFzZXQiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiTmFtYV9CYXJhbmciLCJsYWJlbCI6Ik5hbWEgQXNldCAmYW1wOyBMb2thc2kiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiSnVtbGFoIiwibGFiZWwiOiJLZXRlcnNlZGlhYW4iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoicGVtaW5qYW1fYWt0aWYiLCJsYWJlbCI6IlBlbWluamFtIEFrdGlmIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Ikxva2FzaSIsImxhYmVsIjoiTG9rYXNpIFBlbnlpbXBhbmFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOmZhbHNlLCJpc1RvZ2dsZWFibGUiOnRydWUsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6dHJ1ZX0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImRhdGFSdC5ub21vcl9ydCIsImxhYmVsIjoiVW5pdCBSVCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJjcmVhdGVkX2F0IiwibGFiZWwiOiJUZ2wgUmVnaXN0cmFzaSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9XSwiMGQ4ZTg4MDIzMThlMTA0ZjA2ZTVmMDM3OTI3NzgyZWNfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJpZF9hc3BpcmFzaSIsImxhYmVsIjoiSUQiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY3JlYXRlZF9hdCIsImxhYmVsIjoiVGFuZ2dhbCBNYXN1ayIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ3YXJnYS5OYW1hX0xlbmdrYXAiLCJsYWJlbCI6IlBlbmdpcmltIEFzcGlyYXNpIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IklzaV9Bc3BpcmFzaSIsImxhYmVsIjoiQ3VwbGlrYW4gSXNpIEFzcGlyYXNpIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InJlc3BvbkFzcGlyYXNpLmlzaV9yZXNwb24iLCJsYWJlbCI6IlN0YXR1cyBSZXNwb24iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfV0sIjg5NTNmYzVmMGYyOGU0MzQzYzEzOTA2MmE3ZDc3NWUyX2NvbHVtbnMiOlt7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiQWRtaW5fSUQiLCJsYWJlbCI6IklEIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IlVzZXJuYW1lIiwibGFiZWwiOiJVc2VybmFtZSBcLyBBa3VuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImVtYWlsIiwibGFiZWwiOiJFbWFpbCBHb29nbGUgXC8gS29udGFrIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6Imdvb2dsZV9zdGF0dXMiLCJsYWJlbCI6IkxvZ2luIEdvb2dsZSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyb2xlIiwibGFiZWwiOiJQZXJhbiBcLyBKYWJhdGFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6IklEX1JUIiwibGFiZWwiOiJXaWxheWFoIFJUIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IkRpZGFmdGFya2FuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOmZhbHNlLCJpc1RvZ2dsZWFibGUiOnRydWUsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6dHJ1ZX1dLCJjOWZlOWRhZWM1MGYxM2UxMTJjMDc2MGIwMzdiMWFhYV9jb2x1bW5zIjpbeyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InVydXRhbiIsImxhYmVsIjoiIyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJyb2xlIiwibGFiZWwiOiJKYWJhdGFuIFwvIExheWFuYW4iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibmFtYV9rb250YWsiLCJsYWJlbCI6Ik5hbWEgUGV0dWdhcyBcLyBQSUMiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibm9tb3JfdGVsZXBvbiIsImxhYmVsIjoiTm9tb3IgVGVsZXBvbiBcLyBXQSIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJpc193aGF0c2FwcCIsImxhYmVsIjoiV2hhdHNBcHAiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoibG9rYXNpX3RhbXBpbGFuIiwibGFiZWwiOiJQZW5lbXBhdGFuIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImlzX2FrdGlmIiwibGFiZWwiOiJBa3RpZiIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9XX19',1791338474);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_21_000001_create_ruang_warga_tables',2),(5,'2026_09_21_000002_create_asset_inventory_tables',3),(6,'2026_09_22_000001_create_erd_ruang_warga_tables',4),(7,'2026_09_23_000002_create_sp_dashboard_summary_rt',5),(8,'2026_09_23_000003_create_sp_proses_approval',6),(9,'2026_09_23_000004_create_sp_ajukan_surat_warga',7),(10,'2026_09_23_000005_create_database_triggers',8),(11,'2026_09_23_000006_setup_dcl_user_security',8),(12,'2026_09_30_030851_add_remember_token_to_admin_and_warga_tables',9),(13,'2026_09_30_131949_add_bukti_bayar_to_pemasukan_and_id_rt_to_pengeluaran',10),(14,'2026_09_30_230000_add_nomor_surat_to_surat_and_status_to_kegiatan',11),(15,'2026_10_02_220000_add_payment_gateway_fields_to_pemasukan_table',12),(16,'2026_10_05_130000_add_google_auth_fields_to_users_warga_admin',13),(17,'2026_10_05_150000_create_kontak_rt_table',14);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `approval_tickets`
--

DROP TABLE IF EXISTS `approval_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rt_unit_id` bigint unsigned DEFAULT NULL,
  `citizen_id` bigint unsigned DEFAULT NULL,
  `applicant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `house` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tabel_sumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tab` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perihal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `detail_lengkap` text COLLATE utf8mb4_unicode_ci,
  `preview_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_meta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_pengurus` text COLLATE utf8mb4_unicode_ci,
  `approved_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_tickets_ticket_code_unique` (`ticket_code`),
  KEY `approval_tickets_rt_unit_id_foreign` (`rt_unit_id`),
  KEY `approval_tickets_citizen_id_foreign` (`citizen_id`),
  CONSTRAINT `approval_tickets_citizen_id_foreign` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_tickets_rt_unit_id_foreign` FOREIGN KEY (`rt_unit_id`) REFERENCES `rt_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_tickets`
--

LOCK TABLES `approval_tickets` WRITE;
/*!40000 ALTER TABLE `approval_tickets` DISABLE KEYS */;
INSERT INTO `approval_tickets` VALUES (1,'#APP-2026-001',1,1,'Bpk. Hendra Wijaya','Blok B No. 12','3201051208940001','0812-3456-7890','SURAT','surat','Surat Pengantar Pembuatan KTP & KK Baru','-','pending','Permohonan surat pengantar untuk perpanjangan identitas KTP elektronik dan pemutakhiran anggota keluarga yang baru lahir.','document','Draft Surat Pengantar RT No. 470/05/IX/2026','Dilengkapi Barcode Digital Sign Ketua RT 05',NULL,NULL,NULL,'2026-09-20 02:30:00','2026-09-20 21:12:16'),(2,'#APP-2026-002',1,2,'Ibu Dewi Sartika','Blok C No. 04','3201987654320001','0821-4455-6677','PEMASUKAN','keuangan','Iuran Keamanan & Sampah Periode September 2026','Rp 75.000','approved','Konfirmasi pembayaran iuran bulanan via QRIS Standar Bank Mandiri (Ref: #TX-99210-QRIS).','receipt','Struk Pembayaran QRIS Nasional (NMID: ID1029384756)','Nominal: Rp 75.000 • Status Bank: BERHASIL','Barang inventaris telah selesai dikembalikan ke gudang balai RT.','Bendahara RT 05','2026-09-22 01:41:39','2026-09-20 02:15:00','2026-09-22 22:54:48'),(3,'#APP-2026-003',1,5,'Bpk. Taryo (Teknisi RT)','Pos Ronda Utama','3201556677880004','0813-9988-7766','PENGELUARAN','keuangan','Klaim Penggantian Lampu LED PJU Gang Mawar (3 Titik)','Rp 345.000','pending','Pembelian 3 unit bohlam LED Philips 24W outdoor dan kabel instalasi tahan panas untuk lampu penerangan gang Blok C.','receipt','Nota Toko Listrik Terang Abadi No. INV-88219','Total: Rp 345.000 • Stempel Lunas',NULL,NULL,NULL,'2026-09-19 09:45:00','2026-09-20 20:36:13'),(4,'#APP-2026-004',1,6,'Ibu Nurhayati (Kader PKK)','Blok D No. 07','3201889900110009','0819-2233-4455','KEGIATAN','kegiatan','Pengajuan Anggaran Pelatihan Hidroponik Ibu-Ibu RT','Rp 650.000','pending','Program pemberdayaan warga mandiri pangan: pengadaan bibit sayur, netpot, nutrisi AB Mix dan modul instalasi pipa hidroponik di taman samping balai warga.','event','Proposal Singkat PKK RT 05 No. 03/PKK/IX/2026','Rencana Pelaksanaan: Minggu, 4 Oktober 2026',NULL,NULL,NULL,'2026-09-18 07:00:00','2026-09-20 20:36:13'),(5,'#APP-2026-005',1,3,'Bpk. Denny Pratama','Blok C No. 08','3201556677880004','0813-9988-7766','ASET','aset','Peminjaman Tenda Lipat 3x3m & 20 Kursi Plastik RT','-','pending','Peminjaman perlengkapan inventaris balai warga untuk keperluan syukuran aqiqah anak di halaman rumah.','asset','Formulir Inventaris Aset RT 05 Kode ASET-TND-02','Jadwal Pakai: 26-27 September 2026 (2 Hari)',NULL,NULL,NULL,'2026-09-17 04:20:00','2026-09-20 20:36:13'),(6,'#APP-2026-006',1,4,'Ibu Siti Aminah','Blok A No. 05','3201778899000005','087811223399','SURAT','surat','Surat Pengantar Pembuatan Paspor','-','approved','Permohonan surat pengantar pembuatan paspor ibadah umroh','document','Draft Surat Pengantar RT No. 474/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT','Disetujui Ketua RT. Silakan unduh draft surat ber-QR code.','Ketua RT 05','2026-09-20 20:37:39','2026-09-20 20:37:27','2026-09-20 20:37:39'),(7,'#APP-2026-007',1,1,'Hendra Wijaya','B-12','3201234567890123','08123456789','SURAT','surat','Surat Keterangan Kurang Mampu (Beasiswa/Bansos)','-','rejected','jdsfhdjshdsjk','document','Draft Surat Pengantar RT No. 475/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT','',NULL,NULL,'2026-09-20 21:07:15','2026-09-20 21:07:36'),(8,'#APP-2026-008',1,7,'Hendra Wijaya','Blok B-12','3201051208940001','08123456789','SURAT','surat','Surat Keterangan Domisili Warga','-','pending','Permohonan surat keterangan domisili untuk perbankan.','document','Draft Surat Pengantar RT No. 477/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT',NULL,NULL,NULL,'2026-09-22 12:12:22','2026-09-22 12:12:22'),(9,'#SRT-0008',1,NULL,'Budi Santoso','Jl. Melati No. 12','3201051234567891','081234567890','SURAT','surat','Surat Keterangan Domisili Warga','-','pending','Persyaratan pembuatan rekening bank baru','document','Draft Surat Pengantar RT No. 478/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT',NULL,NULL,NULL,'2026-09-30 09:27:44','2026-09-30 09:27:44'),(10,'#SRT-0009',1,NULL,'Ibu Dewi Sartika','Harmoni Residence Blok C No. 04','3201987654320001','0856-1122-3344','SURAT','surat','Surat Keterangan Domisili Warga','-','pending','SKSCK','document','Draft Surat Pengantar RT No. 479/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT',NULL,NULL,NULL,'2026-10-01 10:48:22','2026-10-01 10:58:11'),(11,'#SRT-0010',1,NULL,'Bpk. Hendra Wijaya','Harmoni Residence Blok B No. 12','3201051208940001','0812-3456-7890','SURAT','surat','Surat Pengantar SKCK Kepolisian','-','pending','SKCK','document','Draft Surat Pengantar RT No. 4710/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT',NULL,NULL,NULL,'2026-10-01 11:59:53','2026-10-01 11:59:53'),(12,'#SRT-0011',1,NULL,'Ibu Dewi Sartika','Harmoni Residence Blok C No. 04','3201987654320001','0856-1122-3344','SURAT','surat','Surat Pengantar Pembuatan / Perubahan KTP & KK','-','pending','KTP','document','Draft Surat Pengantar RT No. 4711/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT',NULL,NULL,NULL,'2026-10-01 20:08:49','2026-10-01 20:08:49'),(13,'#SRT-0012',1,NULL,'Ibu Dewi Sartika','Harmoni Residence Blok C No. 04','3201987654320001','0856-1122-3344','SURAT','surat','Surat Keterangan Kurang Mampu (Beasiswa/Bansos)','-','pending','Buat beasiswa','document','Draft Surat Pengantar RT No. 4712/05/IX/2026','Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT',NULL,NULL,NULL,'2026-10-04 23:49:53','2026-10-04 23:49:53');
/*!40000 ALTER TABLE `approval_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `aspirations`
--

DROP TABLE IF EXISTS `aspirations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aspirations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rt_unit_id` bigint unsigned DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Warga Anonim',
  `blok` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Harmoni',
  `is_anonim` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu Tanggapan',
  `tanggapan_rt` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aspirations_rt_unit_id_foreign` (`rt_unit_id`),
  CONSTRAINT `aspirations_rt_unit_id_foreign` FOREIGN KEY (`rt_unit_id`) REFERENCES `rt_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aspirations`
--

LOCK TABLES `aspirations` WRITE;
/*!40000 ALTER TABLE `aspirations` DISABLE KEYS */;
INSERT INTO `aspirations` VALUES (1,1,'Keamanan','Penambahan Lampu Sorot LED di Ujung Blok D Menghadap Taman','Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.','Bpk. Denny Pratama','Blok C No. 08',0,'Diproses','Terima kasih usulannya Pak Denny. Pembelian lampu sudah diajukan ke bendahara dan akan dipasang teknisi minggu ini.','2026-09-20 20:36:13','2026-09-20 20:36:13'),(2,1,'Kebersihan','Pengadaan Tempat Sampah Terpilah Organik & Anorganik di Balai RT','Saran agar di sekitar pendopo disediakan tempat sampah pilah agar warga dan anak-anak terbiasa memilah sampah sejak dini.','Ibu Nurhayati','Blok D No. 07',0,'Menunggu Tanggapan',NULL,'2026-09-20 20:36:13','2026-09-20 20:36:13'),(3,1,'Fasilitas','Pembersihan Saluran Air Sebelum Musim Hujan Tiba','Mengingat sudah mulai masuk pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti.','Warga Anonim','Kerahasiaan Terjaga',1,'Diproses','Diagendakan pada kerja bakti serentak Sabtu ini.','2026-09-20 20:36:13','2026-09-20 20:36:13'),(4,1,'Keamanan','Penambahan Lampu Sorot LED di Ujung Blok D Menghadap Taman','Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.','Bpk. Denny Pratama','Blok C No. 08',0,'Diproses','Terima kasih usulannya Pak Denny. Pembelian lampu sudah diajukan ke bendahara dan akan dipasang teknisi minggu ini.','2026-09-20 20:54:30','2026-09-20 20:54:30'),(5,1,'Kebersihan','Pengadaan Tempat Sampah Terpilah Organik & Anorganik di Balai RT','Saran agar di sekitar pendopo disediakan tempat sampah pilah agar warga dan anak-anak terbiasa memilah sampah sejak dini.','Ibu Nurhayati','Blok D No. 07',0,'Menunggu Tanggapan',NULL,'2026-09-20 20:54:30','2026-09-20 20:54:30'),(6,1,'Fasilitas','Pembersihan Saluran Air Sebelum Musim Hujan Tiba','Mengingat sudah mulai masuk pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti.','Warga Anonim','Kerahasiaan Terjaga',1,'Diproses','Diagendakan pada kerja bakti serentak Sabtu ini.','2026-09-20 20:54:30','2026-09-20 20:54:30'),(7,1,'Keamanan','Penambahan Lampu Sorot LED di Ujung Blok D Menghadap Taman','Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.','Bpk. Denny Pratama','Blok C No. 08',0,'Diproses','Terima kasih usulannya Pak Denny. Pembelian lampu sudah diajukan ke bendahara dan akan dipasang teknisi minggu ini.','2026-09-20 20:58:17','2026-09-20 20:58:17'),(8,1,'Kebersihan','Pengadaan Tempat Sampah Terpilah Organik & Anorganik di Balai RT','Saran agar di sekitar pendopo disediakan tempat sampah pilah agar warga dan anak-anak terbiasa memilah sampah sejak dini.','Ibu Nurhayati','Blok D No. 07',0,'Menunggu Tanggapan',NULL,'2026-09-20 20:58:17','2026-09-20 20:58:17'),(9,1,'Fasilitas','Pembersihan Saluran Air Sebelum Musim Hujan Tiba','Mengingat sudah mulai masuk pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti.','Warga Anonim','Kerahasiaan Terjaga',1,'Diproses','Diagendakan pada kerja bakti serentak Sabtu ini.','2026-09-20 20:58:17','2026-09-20 20:58:17'),(10,1,'Keamanan','Penambahan Lampu Sorot LED di Ujung Blok D Menghadap Taman','Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.','Bpk. Denny Pratama','Blok C No. 08',0,'Diproses','Terima kasih usulannya Pak Denny. Pembelian lampu sudah diajukan ke bendahara dan akan dipasang teknisi minggu ini.','2026-09-20 21:12:16','2026-09-20 21:12:16'),(11,1,'Kebersihan','Pengadaan Tempat Sampah Terpilah Organik & Anorganik di Balai RT','Saran agar di sekitar pendopo disediakan tempat sampah pilah agar warga dan anak-anak terbiasa memilah sampah sejak dini.','Ibu Nurhayati','Blok D No. 07',0,'Menunggu Tanggapan',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(12,1,'Fasilitas','Pembersihan Saluran Air Sebelum Musim Hujan Tiba','Mengingat sudah mulai masuk pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti.','Warga Anonim','Kerahasiaan Terjaga',1,'Diproses','Diagendakan pada kerja bakti serentak Sabtu ini.','2026-09-20 21:12:16','2026-09-20 21:12:16'),(13,1,'Keamanan','Penambahan Lampu Sorot LED di Ujung Blok D Menghadap Taman','Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.','Bpk. Denny Pratama','Blok C No. 08',0,'Diproses','Terima kasih usulannya Pak Denny. Pembelian lampu sudah diajukan ke bendahara dan akan dipasang teknisi minggu ini.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(14,1,'Kebersihan','Pengadaan Tempat Sampah Terpilah Organik & Anorganik di Balai RT','Saran agar di sekitar pendopo disediakan tempat sampah pilah agar warga dan anak-anak terbiasa memilah sampah sejak dini.','Ibu Nurhayati','Blok D No. 07',0,'Menunggu Tanggapan',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(15,1,'Fasilitas','Pembersihan Saluran Air Sebelum Musim Hujan Tiba','Mengingat sudah mulai masuk pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti.','Warga Anonim','Kerahasiaan Terjaga',1,'Diproses','Diagendakan pada kerja bakti serentak Sabtu ini.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(16,1,'fasilitas','Perbaikan Lampu Jalan Blok B','Lampu penerangan jalan di depan Blok B No. 10 padam sejak kemarin malam. Mohon dapat diperbaiki.','Hendra Wijaya','B-12',0,'Menunggu Tanggapan',NULL,'2026-09-22 12:16:39','2026-09-22 12:16:39');
/*!40000 ALTER TABLE `aspirations` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `asset_items`
--

DROP TABLE IF EXISTS `asset_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_stok` int NOT NULL DEFAULT '1',
  `stok_tersedia` int NOT NULL DEFAULT '1',
  `stok_dipinjam` int NOT NULL DEFAULT '0',
  `lokasi_penyimpanan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Gudang Balai Warga RT 05',
  `kondisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baik',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '?',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_items_kode_aset_unique` (`kode_aset`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_items`
--

LOCK TABLES `asset_items` WRITE;
/*!40000 ALTER TABLE `asset_items` DISABLE KEYS */;
INSERT INTO `asset_items` VALUES (1,'AST-TND-01','Tenda Lipat (3x6 Meter)','Tenda & Terpal',2,2,0,'Gudang Balai Warga RT 05','Baik','🎪','Rangka baja kokoh dengan terpal kanopi anti air warna biru, cocok untuk hajatan dan acara warga.','2026-09-20 21:46:18','2026-09-22 22:54:48'),(2,'AST-KRS-01','Set Kursi Lipat Warga (50 Unit)','Kursi & Meja',2,1,1,'Gudang Balai Warga RT 05','Baik','🪑','Kursi lipat besi pipa chrome dengan bantalan busa hitam tebal, kapasitas beban 100 kg.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(3,'AST-SND-01','Wireless Portable Sound System + 2 Mic','Elektronik & Audio',1,0,1,'Ruang Sekretariat Balai RT','Baik','🔊','Speaker portable troli 12 inch dengan Bluetooth, USB, baterai tahan 6 jam, dan 2 mic wireless UHF.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(4,'AST-PRJ-01','Proyektor Epson & Layar Tripod 84 Inch','Elektronik & Audio',1,1,0,'Ruang Sekretariat Balai RT','Baik','📽️','Proyektor 3LCD resolusi Full HD dengan koneksi HDMI/VGA + tripod screen ukuran 84 inch.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(5,'AST-RMPT-01','Mesin Pemotong Rumput Dorong Honda','Kebersihan Lingkungan',2,2,0,'Gudang Kebersihan Samping Pos Ronda','Baik','🧹','Mesin pemotong rumput mesin bensin 4-tak untuk kegiatan kerja bakti lingkungan.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(6,'AST-RND-01','Paket Rompi & Senter Ronda Keamanan (6 Set)','Keamanan Lingkungan',6,6,0,'Pos Ronda Utama RT 05','Baik','🔦','Rompi spotlight scotlight hijau + senter SWAT rechargeable untuk petugas ronda malam.','2026-09-20 21:46:18','2026-09-20 21:46:18');
/*!40000 ALTER TABLE `asset_items` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `asset_loans`
--

DROP TABLE IF EXISTS `asset_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_loans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_pinjam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_item_id` bigint unsigned DEFAULT NULL,
  `nama_alat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `nama_peminjam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blok_rumah` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `tujuan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `kondisi_keluar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baik & Lengkap',
  `kondisi_kembali` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `approved_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_petugas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_loans_kode_pinjam_unique` (`kode_pinjam`),
  KEY `asset_loans_asset_item_id_foreign` (`asset_item_id`),
  CONSTRAINT `asset_loans_asset_item_id_foreign` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_loans`
--

LOCK TABLES `asset_loans` WRITE;
/*!40000 ALTER TABLE `asset_loans` DISABLE KEYS */;
INSERT INTO `asset_loans` VALUES (1,'#PINJAM-2026-001','#APP-2026-002',1,'Tenda Lipat (3x6 Meter)','AST-TND-01',1,'Bpk. Agus Santoso','Blok A No. 02','0812-3344-5566','2026-09-20','2026-09-22','Acara Khitanan Anak & Pengajian Keluarga di halaman rumah.','returned','Baik & Lengkap (Kaki tenda & terpal biru mulus)','Baik & Bersih','2026-09-23','Bpk. Sukardi Hartono (Ketua RT)','Telah diperiksa kembali dan disimpan rapi di gudang balai warga.','2026-09-20 21:46:18','2026-09-22 22:54:48'),(2,'#PINJAM-2026-002','#APP-2026-003',2,'Set Kursi Lipat Warga (50 Unit)','AST-KRS-01',1,'Ibu Siti Rahma','Blok B No. 05','0857-1122-3344','2026-09-21','2026-09-23','Pertemuan Arisan PKK RT 05 & Posyandu Balita RW 01.','returned','50 Unit Kursi Besi Lengkap & Bersih','Baik & Lengkap','2026-10-02','Ibu Ratna Paramitha (Sekretaris)','Dipakai bersama di balai dan halaman rumah B-05.','2026-09-20 21:46:18','2026-10-02 07:30:57'),(3,'#PINJAM-2026-003','#APP-2026-006',3,'Wireless Portable Sound System + 2 Mic','AST-SND-01',1,'Ibu Siti Rahma','Blok B No. 05','0857-1122-3344','2026-09-21','2026-09-21','Pengeras suara untuk senam pagi warga lansia dan PKK di Balai RT.','borrowed','1 Speaker + 2 Mic UHF Wireless + Adaptor Charger',NULL,NULL,'Ibu Ratna Paramitha (Sekretaris)','Jatuh tempo pengembalian sore ini jam 17.00 WIB.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(4,'#PINJAM-2026-004','#APP-2026-005',1,'Tenda Lipat (3x6 Meter)','AST-TND-01',1,'Bpk. Denny Pratama','Blok C No. 08','0813-9988-7766','2026-09-26','2026-09-27','Peminjaman perlengkapan inventaris balai warga untuk keperluan syukuran aqiqah anak di halaman rumah.','pending','Baik & Lengkap',NULL,NULL,NULL,'Diajukan melalui Portal Mandiri Warga. Menunggu ACC Ketua RT.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(5,'#PINJAM-2026-005','#APP-2026-007',4,'Proyektor Epson & Layar Tripod 84 Inch','AST-PRJ-01',1,'Bpk. Hendra Wijaya','Blok B No. 12','0812-3456-7890','2026-09-24','2026-09-24','Nonton bersama (Nobar) pertandingan Timnas Indonesia di Balai RT bersama pemuda dan bapak-bapak warga.','pending','Baik & Lengkap',NULL,NULL,NULL,'Kegiatan kepemudaan non-komersial. Bebas biaya peminjaman.','2026-09-20 21:46:18','2026-09-20 21:46:18'),(6,'#PINJAM-2026-000','#APP-2026-000',5,'Mesin Pemotong Rumput Dorong Honda','AST-RMPT-01',1,'Bpk. Bambang Pamungkas','Blok D No. 03','0813-7788-9900','2026-09-14','2026-09-15','Kerja bakti pembersihan rumput liar di bahu jalan Blok D dan taman anak.','returned','Baik & Bersih','Selesai & Bersih, pisau potong utuh, tangki bensin terisi normal','2026-09-15','Bpk. Sukardi Hartono (Ketua RT)','Telah diperiksa kembali oleh pengurus dan disimpan rapi di gudang.','2026-09-20 21:46:18','2026-09-20 21:46:18');
/*!40000 ALTER TABLE `asset_loans` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `cash_mutations`
--

DROP TABLE IF EXISTS `cash_mutations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_mutations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rt_unit_id` bigint unsigned DEFAULT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perihal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `nama_pihak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_blok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `tanggal` date NOT NULL,
  `bukti_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_mutations_rt_unit_id_foreign` (`rt_unit_id`),
  CONSTRAINT `cash_mutations_rt_unit_id_foreign` FOREIGN KEY (`rt_unit_id`) REFERENCES `rt_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_mutations`
--

LOCK TABLES `cash_mutations` WRITE;
/*!40000 ALTER TABLE `cash_mutations` DISABLE KEYS */;
INSERT INTO `cash_mutations` VALUES (1,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 1 (45 KK)',3375000.00,'Warga RT 05','Semua Blok','September 2026','approved','2026-09-05',NULL,'2026-09-20 20:36:13','2026-09-20 20:36:13'),(2,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 2 (32 KK)',2400000.00,'Warga RT 05','Blok A & B','September 2026','approved','2026-09-12',NULL,'2026-09-20 20:36:13','2026-09-20 20:36:13'),(3,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 3 (15 KK via QRIS)',1125000.00,'Warga RT 05','Blok C & D','September 2026','approved','2026-09-19',NULL,'2026-09-20 20:36:13','2026-09-20 20:36:13'),(4,1,NULL,'pengeluaran','Keamanan','Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',4162500.00,'Bpk. Agus, Bpk. Supri & Tim','Pos Ronda','September 2026','approved','2026-09-02',NULL,'2026-09-20 20:36:13','2026-09-20 20:36:13'),(5,1,NULL,'pengeluaran','Kebersihan','Retribusi Angkut Sampah DLH & Kebersihan Selokan',2775000.00,'Petugas Truk Sampah DLH','Wilayah RT 05','September 2026','approved','2026-09-04',NULL,'2026-09-20 20:36:13','2026-09-20 20:36:13'),(6,1,NULL,'pengeluaran','Fasum & Listrik','Token Listrik Balai Warga & Pompa Air Bersih',450000.00,'PLN Token Balai','Balai Warga','September 2026','approved','2026-09-08',NULL,'2026-09-20 20:36:13','2026-09-20 20:36:13'),(7,1,'#APP-2026-002','pemasukan','Iuran Warga','Iuran Keamanan & Sampah Periode September 2026',75000.00,'Ibu Dewi Sartika','Blok C No. 04','September 2026','approved','2026-09-20',NULL,'2026-09-20 20:36:13','2026-09-22 01:41:39'),(8,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 1 (45 KK)',3375000.00,'Warga RT 05','Semua Blok','September 2026','approved','2026-09-05',NULL,'2026-09-20 20:54:30','2026-09-20 20:54:30'),(9,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 2 (32 KK)',2400000.00,'Warga RT 05','Blok A & B','September 2026','approved','2026-09-12',NULL,'2026-09-20 20:54:30','2026-09-20 20:54:30'),(10,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 3 (15 KK via QRIS)',1125000.00,'Warga RT 05','Blok C & D','September 2026','approved','2026-09-19',NULL,'2026-09-20 20:54:30','2026-09-20 20:54:30'),(11,1,NULL,'pengeluaran','Keamanan','Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',4162500.00,'Bpk. Agus, Bpk. Supri & Tim','Pos Ronda','September 2026','approved','2026-09-02',NULL,'2026-09-20 20:54:30','2026-09-20 20:54:30'),(12,1,NULL,'pengeluaran','Kebersihan','Retribusi Angkut Sampah DLH & Kebersihan Selokan',2775000.00,'Petugas Truk Sampah DLH','Wilayah RT 05','September 2026','approved','2026-09-04',NULL,'2026-09-20 20:54:30','2026-09-20 20:54:30'),(13,1,NULL,'pengeluaran','Fasum & Listrik','Token Listrik Balai Warga & Pompa Air Bersih',450000.00,'PLN Token Balai','Balai Warga','September 2026','approved','2026-09-08',NULL,'2026-09-20 20:54:30','2026-09-20 20:54:30'),(14,1,'#APP-2026-002','pemasukan','Iuran Warga','Iuran Keamanan & Sampah Periode September 2026',75000.00,'Ibu Dewi Sartika','Blok C No. 04','September 2026','approved','2026-09-20',NULL,'2026-09-20 20:54:30','2026-09-22 01:41:39'),(15,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 1 (45 KK)',3375000.00,'Warga RT 05','Semua Blok','September 2026','approved','2026-09-05',NULL,'2026-09-20 20:58:17','2026-09-20 20:58:17'),(16,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 2 (32 KK)',2400000.00,'Warga RT 05','Blok A & B','September 2026','approved','2026-09-12',NULL,'2026-09-20 20:58:17','2026-09-20 20:58:17'),(17,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 3 (15 KK via QRIS)',1125000.00,'Warga RT 05','Blok C & D','September 2026','approved','2026-09-19',NULL,'2026-09-20 20:58:17','2026-09-20 20:58:17'),(18,1,NULL,'pengeluaran','Keamanan','Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',4162500.00,'Bpk. Agus, Bpk. Supri & Tim','Pos Ronda','September 2026','approved','2026-09-02',NULL,'2026-09-20 20:58:17','2026-09-20 20:58:17'),(19,1,NULL,'pengeluaran','Kebersihan','Retribusi Angkut Sampah DLH & Kebersihan Selokan',2775000.00,'Petugas Truk Sampah DLH','Wilayah RT 05','September 2026','approved','2026-09-04',NULL,'2026-09-20 20:58:17','2026-09-20 20:58:17'),(20,1,NULL,'pengeluaran','Fasum & Listrik','Token Listrik Balai Warga & Pompa Air Bersih',450000.00,'PLN Token Balai','Balai Warga','September 2026','approved','2026-09-08',NULL,'2026-09-20 20:58:17','2026-09-20 20:58:17'),(21,1,'#APP-2026-002','pemasukan','Iuran Warga','Iuran Keamanan & Sampah Periode September 2026',75000.00,'Ibu Dewi Sartika','Blok C No. 04','September 2026','approved','2026-09-20',NULL,'2026-09-20 20:58:17','2026-09-22 01:41:39'),(22,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 1 (45 KK)',3375000.00,'Warga RT 05','Semua Blok','September 2026','approved','2026-09-05',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(23,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 2 (32 KK)',2400000.00,'Warga RT 05','Blok A & B','September 2026','approved','2026-09-12',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(24,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 3 (15 KK via QRIS)',1125000.00,'Warga RT 05','Blok C & D','September 2026','approved','2026-09-19',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(25,1,NULL,'pengeluaran','Keamanan','Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',4162500.00,'Bpk. Agus, Bpk. Supri & Tim','Pos Ronda','September 2026','approved','2026-09-02',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(26,1,NULL,'pengeluaran','Kebersihan','Retribusi Angkut Sampah DLH & Kebersihan Selokan',2775000.00,'Petugas Truk Sampah DLH','Wilayah RT 05','September 2026','approved','2026-09-04',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(27,1,NULL,'pengeluaran','Fasum & Listrik','Token Listrik Balai Warga & Pompa Air Bersih',450000.00,'PLN Token Balai','Balai Warga','September 2026','approved','2026-09-08',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(28,1,'#APP-2026-002','pemasukan','Iuran Warga','Iuran Keamanan & Sampah Periode September 2026',75000.00,'Ibu Dewi Sartika','Blok C No. 04','September 2026','approved','2026-09-20',NULL,'2026-09-20 21:12:16','2026-09-22 01:41:39'),(29,1,'#TX-HW-0726','pemasukan','Iuran Warga','Iuran Keamanan & Kebersihan Periode Juli 2026',75000.00,'Bpk. Hendra Wijaya','Blok B No. 12','Juli 2026','approved','2026-07-08',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(30,1,'#TX-HW-0826','pemasukan','Iuran Warga','Iuran Keamanan & Kebersihan Periode Agustus 2026',75000.00,'Bpk. Hendra Wijaya','Blok B No. 12','Agustus 2026','approved','2026-08-06',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(31,1,'#TX-HW-0926','pemasukan','Iuran Warga','Iuran Keamanan & Kebersihan Periode September 2026',75000.00,'Bpk. Hendra Wijaya','Blok B No. 12','September 2026','approved','2026-09-05',NULL,'2026-09-20 21:12:16','2026-09-20 21:12:16'),(32,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 1 (45 KK)',3375000.00,'Warga RT 05','Semua Blok','September 2026','approved','2026-09-05',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(33,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 2 (32 KK)',2400000.00,'Warga RT 05','Blok A & B','September 2026','approved','2026-09-12',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(34,1,NULL,'pemasukan','Iuran Warga','Penerimaan Iuran Warga Batch 3 (15 KK via QRIS)',1125000.00,'Warga RT 05','Blok C & D','September 2026','approved','2026-09-19',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(35,1,NULL,'pengeluaran','Keamanan','Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',4162500.00,'Bpk. Agus, Bpk. Supri & Tim','Pos Ronda','September 2026','approved','2026-09-02',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(36,1,NULL,'pengeluaran','Kebersihan','Retribusi Angkut Sampah DLH & Kebersihan Selokan',2775000.00,'Petugas Truk Sampah DLH','Wilayah RT 05','September 2026','approved','2026-09-04',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(37,1,NULL,'pengeluaran','Fasum & Listrik','Token Listrik Balai Warga & Pompa Air Bersih',450000.00,'PLN Token Balai','Balai Warga','September 2026','approved','2026-09-08',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(38,1,'#APP-2026-002','pemasukan','Iuran Warga','Iuran Keamanan & Sampah Periode September 2026',75000.00,'Ibu Dewi Sartika','Blok C No. 04','September 2026','approved','2026-09-20',NULL,'2026-09-20 21:46:18','2026-09-22 01:41:39'),(39,1,'#TX-HW-0726','pemasukan','Iuran Warga','Iuran Keamanan & Kebersihan Periode Juli 2026',75000.00,'Bpk. Hendra Wijaya','Blok B No. 12','Juli 2026','approved','2026-07-08',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(40,1,'#TX-HW-0826','pemasukan','Iuran Warga','Iuran Keamanan & Kebersihan Periode Agustus 2026',75000.00,'Bpk. Hendra Wijaya','Blok B No. 12','Agustus 2026','approved','2026-08-06',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18'),(41,1,'#TX-HW-0926','pemasukan','Iuran Warga','Iuran Keamanan & Kebersihan Periode September 2026',75000.00,'Bpk. Hendra Wijaya','Blok B No. 12','September 2026','approved','2026-09-05',NULL,'2026-09-20 21:46:18','2026-09-20 21:46:18');
/*!40000 ALTER TABLE `cash_mutations` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `citizens`
--

DROP TABLE IF EXISTS `citizens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citizens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rt_unit_id` bigint unsigned DEFAULT NULL,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_kk` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_domisili` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tetap',
  `is_kepala_keluarga` tinyint(1) NOT NULL DEFAULT '0',
  `pekerjaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'L',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `citizens_nik_unique` (`nik`),
  KEY `citizens_rt_unit_id_foreign` (`rt_unit_id`),
  CONSTRAINT `citizens_rt_unit_id_foreign` FOREIGN KEY (`rt_unit_id`) REFERENCES `rt_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citizens`
--

LOCK TABLES `citizens` WRITE;
/*!40000 ALTER TABLE `citizens` DISABLE KEYS */;
INSERT INTO `citizens` VALUES (1,1,'3201234567890123','3201230101900001','Bpk. Hendra Wijaya','Blok B No. 12','0812-3456-7890','tetap',1,'Karyawan Swasta','L','2026-09-20 20:36:13','2026-09-20 20:36:13'),(2,1,'3201987654320001','3201230202900002','Ibu Dewi Sartika','Blok C No. 04','0821-4455-6677','tetap',1,'Wiraswasta','P','2026-09-20 20:36:13','2026-09-20 20:36:13'),(3,1,'3201556677880004','3201230303900003','Bpk. Denny Pratama','Blok C No. 08','0813-9988-7766','kontrak',1,'Pegawai BUMN','L','2026-09-20 20:36:13','2026-09-20 20:36:13'),(4,1,'3201778899000005','3201230404900004','Ibu Siti Aminah','Blok A No. 05','0878-1122-3399','tetap',0,'Ibu Rumah Tangga','P','2026-09-20 20:36:13','2026-09-20 20:36:13'),(5,1,'3201665544330007','3201230505900005','Bpk. Taryo (Teknisi RT)','Pos Ronda Utama','0852-7788-9900','tetap',1,'Teknisi Listrik','L','2026-09-20 20:36:13','2026-09-20 20:36:13'),(6,1,'3201889900110009','3201230606900006','Ibu Nurhayati','Blok D No. 07','0819-2233-4455','tetap',0,'Guru','P','2026-09-20 20:36:13','2026-09-20 20:36:13'),(7,1,'3201051208940001','3201051909120008','Bpk. Hendra Wijaya','Blok B No. 12','0812-3456-7890','tetap',1,'Karyawan Swasta / BUMN','L','2026-09-20 20:54:30','2026-09-20 20:54:30'),(8,1,'3201055504880002','3201051909120015','Ibu Dewi Sartika','Blok C No. 04','0813-9876-5432','tetap',1,'Wiraswasta Kuliner','P','2026-09-20 20:54:30','2026-09-20 20:54:30'),(9,1,'3201050807900003','3201051909120042','Bpk. Denny Pratama','Blok C No. 08','0857-1234-9988','kontrak',1,'Tenaga Pengajar / Dosen','L','2026-09-20 20:54:30','2026-09-20 20:54:30'),(10,1,'3201056511870004','3201051909120021','Ibu Siti Aminah','Blok A No. 05','0878-1122-3344','tetap',0,'Guru Madrasah','P','2026-09-20 20:54:30','2026-09-20 20:54:30'),(11,1,'3201052203090005','3201051909120008','Dimas Arya Nugraha','Blok B No. 12','0821-5566-7788','tetap',0,'Pelajar SMA','L','2026-09-20 20:54:30','2026-09-20 20:54:30'),(12,1,'3201051402920006','3201051909120055','Bpk. Ridwan Santoso','Blok D No. 02','0819-9988-7766','kontrak',1,'Konsultan IT','L','2026-09-20 20:54:30','2026-09-20 20:54:30'),(13,1,'3201054809830007','3201051909120063','Ibu Euis Komala','Blok A No. 11','0812-8899-0011','tetap',1,'Aparatur Sipil Negara (ASN)','P','2026-09-20 20:54:30','2026-09-20 20:54:30'),(14,1,'3201052906950008','3201051909120078','Bpk. Fajar Ramadhan','Blok C No. 15','0822-1144-7788','tetap',1,'Arsitek Desain','L','2026-09-20 20:54:30','2026-09-20 20:54:30'),(15,1,'3201053001850009','3201051909120099','Bpk. Taryo (Teknisi RT)','Pos Ronda Utama','0852-7788-9900','tetap',1,'Teknisi Listrik & Fasum','L','2026-09-20 20:54:30','2026-09-20 20:54:30');
/*!40000 ALTER TABLE `citizens` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `official_letters`
--

DROP TABLE IF EXISTS `official_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `official_letters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt_unit_id` bigint unsigned DEFAULT NULL,
  `citizen_id` bigint unsigned DEFAULT NULL,
  `nama_warga` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_surat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keperluan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `qr_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_terbit` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `official_letters_nomor_surat_unique` (`nomor_surat`),
  KEY `official_letters_rt_unit_id_foreign` (`rt_unit_id`),
  KEY `official_letters_citizen_id_foreign` (`citizen_id`),
  CONSTRAINT `official_letters_citizen_id_foreign` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE SET NULL,
  CONSTRAINT `official_letters_rt_unit_id_foreign` FOREIGN KEY (`rt_unit_id`) REFERENCES `rt_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `official_letters`
--

LOCK TABLES `official_letters` WRITE;
/*!40000 ALTER TABLE `official_letters` DISABLE KEYS */;
INSERT INTO `official_letters` VALUES (1,'470/05/IX/2026','#APP-2026-001',1,1,'Bpk. Hendra Wijaya','3201051208940001','Blok B No. 12','Surat Pengantar Pembuatan KTP & KK Baru','Permohonan surat pengantar untuk pemutakhiran kartu keluarga dan KTP-el ke Kantor Kelurahan Sukamaju.','Selesai','QR-RW05-470-2026','2026-09-20','2026-09-20 20:36:13','2026-09-20 21:12:16'),(2,'471/05/IX/2026',NULL,1,2,'Ibu Dewi Sartika','3201987654320001','Blok C No. 04','Surat Keterangan Domisili Tempat Tinggal','Kelengkapan administrasi pendaftaran sekolah anak dan perbankan.','Siap Ambil','QR-RW05-471-2026','2026-09-18','2026-09-20 20:36:13','2026-09-20 20:36:13'),(3,'472/05/IX/2026',NULL,1,3,'Bpk. Denny Pratama','3201556677880004','Blok C No. 08','Surat Keterangan Usaha (SKU) Mikro Lingkungan','Pengajuan KUR Mikro Usaha Kuliner Rumahan.','Selesai','QR-RW05-472-2026','2026-09-15','2026-09-20 20:36:13','2026-09-20 20:36:13'),(4,'474/05/IX/2026','#APP-2026-006',1,4,'Ibu Siti Aminah','3201778899000005','Blok A No. 05','Surat Pengantar Pembuatan Paspor','Permohonan surat pengantar pembuatan paspor ibadah umroh','Draft Terbit','QR-RW05-474-05-IX-2026','2026-09-21','2026-09-20 20:37:27','2026-09-20 20:37:39'),(5,'475/05/IX/2026','#APP-2026-007',1,1,'Hendra Wijaya','3201234567890123','B-12','Surat Keterangan Kurang Mampu (Beasiswa/Bansos)','jdsfhdjshdsjk','Ditolak','QR-RW05-475-05-IX-2026','2026-09-21','2026-09-20 21:07:15','2026-09-20 21:07:36'),(6,'470/024/RT-05/IX/2026','#APP-2026-008',1,1,'Bpk. Hendra Wijaya','3201051208940001','Blok B No. 12','Surat Pengantar SKCK Kepolisian','Kelengkapan administrasi perpanjangan SKCK untuk keperluan melamar pekerjaan.','Selesai','QR-RW05-470-024-2026','2026-09-19','2026-09-20 21:12:16','2026-09-20 21:12:16'),(7,'477/05/IX/2026','#APP-2026-008',1,7,'Hendra Wijaya','3201051208940001','Blok B-12','Surat Keterangan Domisili Warga','Permohonan surat keterangan domisili untuk perbankan.','Pending','QR-RW05-477-05-IX-2026','2026-09-22','2026-09-22 12:12:22','2026-09-22 12:12:22'),(8,'478/05/IX/2026','#SRT-0008',1,NULL,'Budi Santoso','3201051234567891','Jl. Melati No. 12','Surat Keterangan Domisili Warga','Persyaratan pembuatan rekening bank baru','Pending','QR-RW05-478-05-IX-2026','2026-09-30','2026-09-30 09:27:44','2026-09-30 09:27:44'),(9,'479/05/IX/2026','#SRT-0009',1,NULL,'Ibu Dewi Sartika','3201987654320001','Harmoni Residence Blok C No. 04','Surat Keterangan Domisili Warga','SKSCK','Pending','QR-RW05-479-05-IX-2026','2026-10-01','2026-10-01 10:48:22','2026-10-01 10:58:11'),(10,'4710/05/IX/2026','#SRT-0010',1,NULL,'Bpk. Hendra Wijaya','3201051208940001','Harmoni Residence Blok B No. 12','Surat Pengantar SKCK Kepolisian','SKCK','Pending','QR-RW05-4710-05-IX-2026','2026-10-01','2026-10-01 11:59:53','2026-10-01 11:59:53'),(11,'4711/05/IX/2026','#SRT-0011',1,NULL,'Ibu Dewi Sartika','3201987654320001','Harmoni Residence Blok C No. 04','Surat Pengantar Pembuatan / Perubahan KTP & KK','KTP','Pending','QR-RW05-4711-05-IX-2026','2026-10-02','2026-10-01 20:08:49','2026-10-01 20:08:49'),(12,'4712/05/IX/2026','#SRT-0012',1,NULL,'Ibu Dewi Sartika','3201987654320001','Harmoni Residence Blok C No. 04','Surat Keterangan Kurang Mampu (Beasiswa/Bansos)','Buat beasiswa','Pending','QR-RW05-4712-05-IX-2026','2026-10-05','2026-10-04 23:49:53','2026-10-04 23:49:53');
/*!40000 ALTER TABLE `official_letters` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--

-- Table structure for table `rt_units`
--

DROP TABLE IF EXISTS `rt_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rt_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_rt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kawasan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelurahan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kecamatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ketua_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ketua_kontak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rt_units_kode_rt_unique` (`kode_rt`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rt_units`
--

LOCK TABLES `rt_units` WRITE;
/*!40000 ALTER TABLE `rt_units` DISABLE KEYS */;
INSERT INTO `rt_units` VALUES (1,'RT 05 / RW 01','Harmoni Residence','Sukamaju','Cilodong','Kota Depok','Bpk. Sukardi Hartono','0812-9876-5432','aktif','2026-09-20 20:36:13','2026-09-20 20:36:13'),(2,'RT 01 / RW 02','Griya Asri Pratama','Mekarjaya','Sukmajaya','Kota Depok','Bpk. Bambang Pamungkas','0813-1122-3344','aktif','2026-09-20 20:36:13','2026-09-20 20:36:13'),(3,'RT 03 / RW 04','Taman Pesona Boulevard','Baktijaya','Sukmajaya','Kota Depok','Ibu Sri Mulyani','0857-9988-7711','aktif','2026-09-20 20:36:13','2026-09-20 20:36:13');
/*!40000 ALTER TABLE `rt_units` ENABLE KEYS */;
UNLOCK TABLES;

--

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;
SET SQL_MODE=@OLD_SQL_MODE;

-- Dump completed successfully