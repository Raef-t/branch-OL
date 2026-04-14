-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: olamaa_institute
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_branches`
--

DROP TABLE IF EXISTS `academic_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_branches`
--

LOCK TABLES `academic_branches` WRITE;
/*!40000 ALTER TABLE `academic_branches` DISABLE KEYS */;
INSERT INTO `academic_branches` VALUES (1,'بكالوريا علمي','الفرع العلمي لطلاب المرحلة الثانوية','2026-02-26 04:42:53','2026-02-26 04:42:53'),(2,'بكالوريا أدبي','الفرع الأدبي لطلاب المرحلة الثانوية','2026-02-26 04:42:53','2026-02-26 04:42:53'),(3,'تاسع','طلاب الصف التاسع الأساسي','2026-02-26 04:42:53','2026-02-26 04:42:53'),(4,'قسم تكنولوجيا المعلومات',NULL,'2026-03-26 10:30:53','2026-03-26 10:30:53');
/*!40000 ALTER TABLE `academic_branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_records`
--

DROP TABLE IF EXISTS `academic_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `record_type` varchar(255) DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academic_records_student_id_foreign` (`student_id`),
  CONSTRAINT `academic_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_records`
--

LOCK TABLES `academic_records` WRITE;
/*!40000 ALTER TABLE `academic_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_branch_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `batch_id` bigint(20) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL DEFAULT 'present',
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `device_id` varchar(100) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_institute_branch_id_foreign` (`institute_branch_id`),
  KEY `attendances_student_id_foreign` (`student_id`),
  KEY `attendances_batch_id_foreign` (`batch_id`),
  KEY `attendances_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `attendances_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audits`
--

DROP TABLE IF EXISTS `audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(1023) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audits_user_id_user_type_index` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audits`
--

LOCK TABLES `audits` WRITE;
/*!40000 ALTER TABLE `audits` DISABLE KEYS */;
INSERT INTO `audits` VALUES (1,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',1,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"saturday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":1}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(2,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',2,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"sunday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":2}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(3,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',3,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"tuesday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":3}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(4,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',4,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"wednesday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":4}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(5,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',5,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"friday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":5}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(6,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',6,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"saturday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":6}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(7,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',7,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"sunday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":7}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(8,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',8,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"monday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":8}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(9,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',9,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"friday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":9}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(10,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',10,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"saturday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":10}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(11,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',11,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"thursday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":11}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(12,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',12,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"friday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":12}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(13,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',13,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"saturday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":13}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(14,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',14,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"sunday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":14}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(15,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',15,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"friday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":15}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(16,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',16,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"saturday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":16}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(17,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',17,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"sunday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":17}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(18,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',18,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"monday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":18}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(19,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',19,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"friday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_124452_2HMeHn_sol1\",\"id\":19}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_124452_2HMeHn_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 16:45:16','2026-03-27 16:45:16'),(20,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',20,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"sunday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":20}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(21,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',21,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"monday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":21}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(22,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',22,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"tuesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":22}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(23,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',23,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"wednesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":23}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(24,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',24,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"friday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":24}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(25,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',25,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"saturday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":25}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(26,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',26,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"tuesday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":26}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(27,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',27,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"thursday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":27}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(28,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',28,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"friday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":28}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(29,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',29,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"saturday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":29}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(30,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',30,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"sunday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":30}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(31,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',31,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"monday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":31}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(32,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',32,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"saturday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":32}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(33,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',33,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"thursday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":33}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(34,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',34,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"friday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":34}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(35,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',35,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"saturday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":35}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(36,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',36,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"sunday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":36}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(37,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',37,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"wednesday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":37}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(38,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',38,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"thursday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_111300_SxFYfN_sol1\",\"id\":38}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_111300_SxFYfN_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 18:01:54','2026-03-27 18:01:54'),(39,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',39,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"monday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":39}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(40,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',40,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"tuesday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":40}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(41,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',41,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"wednesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":41}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(42,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',42,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"thursday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":42}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(43,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',43,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"friday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":43}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(44,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',44,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"sunday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":44}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(45,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',45,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"monday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":45}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(46,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',46,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"tuesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":46}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(47,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',47,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"thursday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":47}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(48,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',48,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"saturday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":48}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(49,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',49,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"sunday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":49}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(50,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',50,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"tuesday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":50}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(51,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',51,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"saturday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":51}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(52,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',52,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"thursday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":52}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(53,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',53,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"friday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":53}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(54,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',54,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"sunday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":54}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(55,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',55,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"monday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":55}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(56,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',56,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"thursday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":56}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(57,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',57,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"friday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":2,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":57}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:15:41','2026-03-27 20:15:41'),(58,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',58,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"monday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":58}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(59,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',59,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"tuesday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":59}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(60,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',60,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"wednesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":60}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(61,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',61,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"thursday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":61}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(62,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',62,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"friday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":62}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(63,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',63,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"sunday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":63}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(64,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',64,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"monday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":64}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(65,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',65,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"tuesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":65}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(66,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',66,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"thursday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":66}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(67,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',67,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"saturday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":67}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(68,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',68,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"sunday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":68}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(69,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',69,'[]','{\"batch_subject_id\":6,\"day_of_week\":\"tuesday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":69}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(70,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',70,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"saturday\",\"period_number\":2,\"start_time\":\"08:50\",\"end_time\":\"09:35\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":70}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(71,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',71,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"thursday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":71}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(72,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',72,'[]','{\"batch_subject_id\":7,\"day_of_week\":\"friday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":72}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(73,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',73,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"sunday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":73}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(74,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',74,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"monday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":74}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(75,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',75,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"thursday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":75}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(76,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',76,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"friday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol1\",\"id\":76}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol1/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:24:27','2026-03-27 20:24:27'),(77,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',77,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"saturday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":77}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(78,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',78,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"tuesday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":78}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(79,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',79,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"wednesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":79}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(80,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',80,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"thursday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":80}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(81,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',81,'[]','{\"batch_subject_id\":4,\"day_of_week\":\"friday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":81}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(82,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',82,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"tuesday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":82}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(83,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',83,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"wednesday\",\"period_number\":3,\"start_time\":\"09:40\",\"end_time\":\"10:25\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":83}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(84,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',84,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"thursday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":84}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(85,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',85,'[]','{\"batch_subject_id\":5,\"day_of_week\":\"friday\",\"period_number\":4,\"start_time\":\"10:30\",\"end_time\":\"11:15\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":85}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(86,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',86,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"tuesday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":86}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(87,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',87,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"wednesday\",\"period_number\":1,\"start_time\":\"08:00\",\"end_time\":\"08:45\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":87}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(88,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',88,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"thursday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":88}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(89,'user',1,'created','Modules\\ClassSchedules\\Models\\ClassSchedule',89,'[]','{\"batch_subject_id\":8,\"day_of_week\":\"friday\",\"period_number\":5,\"start_time\":\"11:20\",\"end_time\":\"12:05\",\"class_room_id\":11,\"is_default\":true,\"is_active\":true,\"description\":\"\\u062a\\u0645 \\u0627\\u0644\\u062a\\u0648\\u0644\\u064a\\u062f \\u0622\\u0644\\u064a\\u0627\\u064b \\u0639\\u0628\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c \\u0627\\u0644\\u0630\\u0643\\u064a - \\u0645\\u062c\\u0645\\u0648\\u0639\\u0629: draft_20260327_161109_YNoNuO_sol2\",\"id\":89}','http://localhost/OlamaaInstitute/backend/public/api/class-schedules/drafts/draft_20260327_161109_YNoNuO_sol2/publish','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-27 20:30:52','2026-03-27 20:30:52'),(90,'user',1,'updated','Modules\\Instructors\\Models\\Instructor',10,'{\"preferences\":null}','{\"preferences\":\"{\\\"priority_level\\\":3,\\\"blocked_slots\\\":{\\\"tuesday\\\":[3]},\\\"preferred_days\\\":[],\\\"avoid_days\\\":[\\\"saturday\\\"],\\\"preferred_slots\\\":[],\\\"avoid_slots\\\":[2]}\"}','http://localhost/OlamaaInstitute/backend/public/api/teachers/10','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-28 10:15:46','2026-03-28 10:15:46'),(91,'user',1,'created','Modules\\InstituteBranches\\Models\\InstituteBranch',3,'[]','{\"name\":\"\\u0627\\u0644\\u0641\\u0631\\u0639 \\u0627\\u0644\\u0623\\u0648\\u0644\",\"address\":\"\\u062d\\u0644\\u0628\",\"code\":\"first\",\"phone\":null,\"email\":null,\"manager_name\":\"\\u0645\\u062f\\u064a\\u0631\",\"is_active\":1,\"id\":3}','http://localhost/OlamaaInstitute/backend/public/api/institute-branches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 14:28:55','2026-03-29 14:28:55'),(92,'user',1,'created','Modules\\Batches\\Models\\Batch',3,'[]','{\"institute_branch_id\":3,\"academic_branch_id\":1,\"class_room_id\":3,\"name\":\"\\u0634\\u0639\\u0628\\u0629 \\u0627\\u0644\\u0623\\u0648\\u0627\\u0626\\u0644\",\"gender_type\":\"male\",\"is_archived\":false,\"is_hidden\":false,\"is_completed\":false,\"id\":3}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 14:30:15','2026-03-29 14:30:15'),(93,'user',1,'created','Modules\\Families\\Models\\Family',1,'[]','{\"user_id\":null,\"id\":1}','http://localhost/OlamaaInstitute/backend/public/api/enrollments','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 14:40:17','2026-03-29 14:40:17'),(94,'user',1,'created','parent',1,'[]','{\"family_id\":1,\"first_name\":\"eyJpdiI6Ink2ZFJab1pSTFR1MzBXWjJDUG1Banc9PSIsInZhbHVlIjoibi8rdkpUYUpsLzBpRGNhQTI3RllkQT09IiwibWFjIjoiYWI1NGQ4NmIxMGFkNTMyMGZjZjc0NzA2MGZhOWM0NjU4YjkyZDcyOTA2YTg2ODQwY2M2YjQ0Nzk2ZDBhYjk5YyIsInRhZyI6IiJ9\",\"first_name_hash\":\"581eb8b00de2d8a0776cc9746fcee5f996682ca3\",\"last_name\":\"eyJpdiI6ImpjUllocitqWkVkc0g5Y3JjdndlbUE9PSIsInZhbHVlIjoiNHViTjN5QzUwbzUwYzRINVUyZDJMQT09IiwibWFjIjoiOWM2Y2QzZTVkMWMwNDQ3NjFlNTNhZmFmNWYxZTU4ODc2ZGEwYmFjZWE5ZGNhNzVmNjFmMWJmYjNkN2IwYTM1MiIsInRhZyI6IiJ9\",\"last_name_hash\":\"81f955f92a04bd93e46cc943fa3eaaaf77954418\",\"national_id\":null,\"national_id_hash\":null,\"occupation\":null,\"address\":null,\"relationship\":\"father\",\"is_primary_contact\":true,\"id\":1}','http://localhost/OlamaaInstitute/backend/public/api/enrollments','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 14:40:17','2026-03-29 14:40:17'),(95,'user',1,'created','parent',2,'[]','{\"family_id\":1,\"first_name\":\"eyJpdiI6ImhtcXJJcWtDK2s3eUdyUHdZM1IwZUE9PSIsInZhbHVlIjoiOVZoamRqQ1dLazg5ZDJnZUtWZ3VqZz09IiwibWFjIjoiZjAwNDEzYzM4YTUzYmVhY2NjMmI0OWYzMmFhMzI0ZGQ5ZTM4ZDQwMmIzYjgwMjg2MDJmNTIyMjg1NTM1NzU4MyIsInRhZyI6IiJ9\",\"first_name_hash\":\"828c61ff36d46d6eccb6051287e2e9158344f2af\",\"last_name\":\"eyJpdiI6Ik5FS09panNyMDBJT0xZcFZHVjlaWkE9PSIsInZhbHVlIjoibE5rMVcyQisxcC82YWhHVTRkdmxBZz09IiwibWFjIjoiZjFjMmFhNDcyODBlYTg2YjVkOGFlNmE4N2MzNzg2YjgyZWJiYzFkMWNjNzc2ZTVjN2NlMmUwOGJjMGY0OWY2ZiIsInRhZyI6IiJ9\",\"last_name_hash\":\"81f955f92a04bd93e46cc943fa3eaaaf77954418\",\"national_id\":null,\"national_id_hash\":null,\"occupation\":null,\"address\":null,\"relationship\":\"mother\",\"is_primary_contact\":false,\"id\":2}','http://localhost/OlamaaInstitute/backend/public/api/enrollments','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 14:40:17','2026-03-29 14:40:17'),(96,'user',1,'created','student',1,'[]','{\"first_name\":\"eyJpdiI6IlFjUmI2cm1XYXlqdFNpVWI4S2dQQWc9PSIsInZhbHVlIjoiQjZ3SkxSdFVvN1lSQXZyQS9GOENzdz09IiwibWFjIjoiMjc0ZTc5YzFlMWI3ZWE0Yjk3MTUyMDFjMDdiOTRhMGZlYjM3ZDJhMmIxNmJhN2IzYzZjNmJjYjM3YjBhYTVmZSIsInRhZyI6IiJ9\",\"first_name_hash\":\"9ac0bbb8ad65e4416e872f0349349d3e82759057\",\"last_name\":\"eyJpdiI6InJaNzVZM2xXQnNHY1dMQmlLYTFaNlE9PSIsInZhbHVlIjoiR3k5TVh5a3hWSlZ3NTRSYTRiSHl1UT09IiwibWFjIjoiOTI5YmZiY2JlMmIwODUzNzBlYjAzY2E5MmM3YWFmZTgxZmZiODYzZDRiMDhhNmNiMjU0MzIzNjBlYzA3OGRkNSIsInRhZyI6IiJ9\",\"last_name_hash\":\"81f955f92a04bd93e46cc943fa3eaaaf77954418\",\"institute_branch_id\":\"1\",\"branch_id\":\"1\",\"city_id\":\"3\",\"family_id\":1,\"profile_photo_url\":null,\"id_card_photo_url\":null,\"id\":1}','http://localhost/OlamaaInstitute/backend/public/api/enrollments','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 14:40:17','2026-03-29 14:40:17'),(97,'user',1,'updated','student',1,'{\"institute_branch_id\":1,\"first_name\":\"eyJpdiI6IlFjUmI2cm1XYXlqdFNpVWI4S2dQQWc9PSIsInZhbHVlIjoiQjZ3SkxSdFVvN1lSQXZyQS9GOENzdz09IiwibWFjIjoiMjc0ZTc5YzFlMWI3ZWE0Yjk3MTUyMDFjMDdiOTRhMGZlYjM3ZDJhMmIxNmJhN2IzYzZjNmJjYjM3YjBhYTVmZSIsInRhZyI6IiJ9\",\"last_name\":\"eyJpdiI6InJaNzVZM2xXQnNHY1dMQmlLYTFaNlE9PSIsInZhbHVlIjoiR3k5TVh5a3hWSlZ3NTRSYTRiSHl1UT09IiwibWFjIjoiOTI5YmZiY2JlMmIwODUzNzBlYjAzY2E5MmM3YWFmZTgxZmZiODYzZDRiMDhhNmNiMjU0MzIzNjBlYzA3OGRkNSIsInRhZyI6IiJ9\"}','{\"institute_branch_id\":3,\"first_name\":\"eyJpdiI6ImVsU0c4NVFQRGFrRTNpMXZxckdNd2c9PSIsInZhbHVlIjoiS3Q5eG1qNUpYSkV2YmVTL2ttNWFWdz09IiwibWFjIjoiYWRhZDhlNDI5MzdlOTMxN2UzYzg0MDRkZDg5YWVlZmM1ZWExZDYzZjdlOGE3MjU0NjYyYTU3MzQ1MTk4ZjQzNSIsInRhZyI6IiJ9\",\"last_name\":\"eyJpdiI6IlNsb2FZYW0vRWdNVW1zbUcyalozY1E9PSIsInZhbHVlIjoiT3RLMTBTR1J4OXg0bHVHbXYyTDN4UT09IiwibWFjIjoiYzIwYzQ3NjY2OTcyNmI1YmMwNzBkN2IxMmRiOWMzOWNkNzQzNDk1YTEyNDkwNDY5NTkxOTIxZWQ1YWJjNmJlNSIsInRhZyI6IiJ9\"}','http://localhost/OlamaaInstitute/backend/public/api/students/1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 14:41:50','2026-03-29 14:41:50'),(98,'user',1,'updated','Modules\\Batches\\Models\\Batch',4,'{\"is_archived\":0,\"is_hidden\":0,\"is_completed\":0,\"gender_type\":null}','{\"is_archived\":true,\"is_hidden\":false,\"is_completed\":false,\"gender_type\":\"male\"}','http://localhost/OlamaaInstitute/backend/public/api/batches/4','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:05:29','2026-03-29 15:05:29'),(99,'user',1,'created','Modules\\Batches\\Models\\Batch',5,'[]','{\"institute_branch_id\":3,\"academic_branch_id\":3,\"class_room_id\":4,\"name\":\"\\u062a\\u0627\\u0633\\u0639 \\u0630\\u0643\\u0648\\u0631\",\"gender_type\":\"male\",\"is_archived\":false,\"is_hidden\":false,\"is_completed\":false,\"id\":5}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:12:33','2026-03-29 15:12:33'),(100,'user',1,'created','Modules\\Batches\\Models\\Batch',7,'[]','{\"institute_branch_id\":3,\"academic_branch_id\":3,\"class_room_id\":3,\"name\":\"\\u062a\\u0627\\u0633\\u0639 \\u0627\\u0646\\u0627\\u062b\",\"gender_type\":\"mixed\",\"is_archived\":false,\"is_hidden\":false,\"is_completed\":false,\"id\":7}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(101,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',17,'[]','{\"batch_id\":7,\"subject_id\":1,\"assignment_date\":\"2026-03-29T11:39:28.572073Z\",\"is_active\":true,\"id\":17}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(102,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',18,'[]','{\"batch_id\":7,\"subject_id\":2,\"assignment_date\":\"2026-03-29T11:39:28.588263Z\",\"is_active\":true,\"id\":18}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(103,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',19,'[]','{\"batch_id\":7,\"subject_id\":3,\"assignment_date\":\"2026-03-29T11:39:28.624915Z\",\"is_active\":true,\"id\":19}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(104,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',20,'[]','{\"batch_id\":7,\"subject_id\":4,\"assignment_date\":\"2026-03-29T11:39:28.676379Z\",\"is_active\":true,\"id\":20}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(105,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',21,'[]','{\"batch_id\":7,\"subject_id\":5,\"assignment_date\":\"2026-03-29T11:39:28.710020Z\",\"is_active\":true,\"id\":21}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(106,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',22,'[]','{\"batch_id\":7,\"subject_id\":6,\"assignment_date\":\"2026-03-29T11:39:28.751341Z\",\"is_active\":true,\"id\":22}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(107,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',23,'[]','{\"batch_id\":7,\"subject_id\":7,\"assignment_date\":\"2026-03-29T11:39:28.771351Z\",\"is_active\":true,\"id\":23}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(108,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',24,'[]','{\"batch_id\":7,\"subject_id\":8,\"assignment_date\":\"2026-03-29T11:39:28.784407Z\",\"is_active\":true,\"id\":24}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(109,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',25,'[]','{\"batch_id\":7,\"subject_id\":9,\"assignment_date\":\"2026-03-29T11:39:28.799351Z\",\"is_active\":true,\"id\":25}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(110,'user',1,'created','Modules\\BatchSubjects\\Models\\BatchSubject',26,'[]','{\"batch_id\":7,\"subject_id\":10,\"assignment_date\":\"2026-03-29T11:39:28.834819Z\",\"is_active\":true,\"id\":26}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(111,'user',1,'created','Modules\\BatchStudents\\Models\\BatchStudent',1,'[]','{\"batch_id\":\"5\",\"student_id\":1,\"id\":1}','http://localhost/OlamaaInstitute/backend/public/api/batch-students','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 16:15:20','2026-03-29 16:15:20'),(112,'user',1,'updated','Modules\\Batches\\Models\\Batch',5,'{\"is_archived\":0,\"is_hidden\":0,\"is_completed\":0}','{\"is_archived\":false,\"is_hidden\":true,\"is_completed\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/5','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 16:16:57','2026-03-29 16:16:57'),(113,'user',1,'updated','Modules\\Batches\\Models\\Batch',7,'{\"is_hidden\":0}','{\"is_hidden\":true}','http://localhost/OlamaaInstitute/backend/public/api/batches/7/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 16:40:26','2026-03-29 16:40:26'),(114,'user',1,'updated','Modules\\Batches\\Models\\Batch',7,'{\"is_hidden\":1}','{\"is_hidden\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/7/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 16:41:56','2026-03-29 16:41:56'),(115,'user',1,'updated','Modules\\Batches\\Models\\Batch',3,'{\"is_archived\":0}','{\"is_archived\":true}','http://localhost/OlamaaInstitute/backend/public/api/batches/3/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 16:43:44','2026-03-29 16:43:44'),(116,'user',1,'updated','Modules\\Batches\\Models\\Batch',3,'{\"is_archived\":1}','{\"is_archived\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/3/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 16:44:04','2026-03-29 16:44:04'),(117,'user',1,'updated','Modules\\Batches\\Models\\Batch',7,'{\"is_hidden\":0}','{\"is_hidden\":true}','http://localhost/OlamaaInstitute/backend/public/api/batches/7/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 17:02:21','2026-03-29 17:02:21'),(118,'user',1,'updated','Modules\\Batches\\Models\\Batch',7,'{\"is_hidden\":1}','{\"is_hidden\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/7/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 17:07:23','2026-03-29 17:07:23'),(119,'user',1,'updated','Modules\\Batches\\Models\\Batch',6,'{\"is_archived\":0}','{\"is_archived\":true}','http://localhost/OlamaaInstitute/backend/public/api/batches/6/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 17:18:27','2026-03-29 17:18:27'),(120,'user',1,'updated','Modules\\Batches\\Models\\Batch',6,'{\"is_archived\":1}','{\"is_archived\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/6/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 17:19:30','2026-03-29 17:19:30'),(121,'user',1,'updated','Modules\\Batches\\Models\\Batch',5,'{\"is_hidden\":0}','{\"is_hidden\":true}','http://localhost/OlamaaInstitute/backend/public/api/batches/5/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 17:58:22','2026-03-29 17:58:22'),(122,'user',1,'updated','Modules\\Batches\\Models\\Batch',5,'{\"is_hidden\":1}','{\"is_hidden\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/5/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 17:58:38','2026-03-29 17:58:38'),(123,'user',1,'updated','Modules\\Batches\\Models\\Batch',5,'{\"is_completed\":0}','{\"is_completed\":true}','http://localhost/OlamaaInstitute/backend/public/api/batches/5/toggle-status','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 18:01:57','2026-03-29 18:01:57'),(124,'user',1,'updated','Modules\\BatchStudents\\Models\\BatchStudent',1,'{\"batch_id\":5}','{\"batch_id\":7}','http://localhost/OlamaaInstitute/backend/public/api/batch-students/1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 20:11:36','2026-03-29 20:11:36'),(125,'user',1,'created','Modules\\Batches\\Models\\Batch',8,'[]','{\"name\":\"Dummy Batch\",\"is_archived\":false,\"is_hidden\":false,\"is_completed\":false,\"id\":8}','http://localhost/OlamaaInstitute/backend/public/api/batches','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 20:46:32','2026-03-29 20:46:32'),(126,'user',1,'updated','Modules\\Batches\\Models\\Batch',5,'{\"is_archived\":0,\"is_hidden\":0,\"is_completed\":1,\"gender_type\":\"male\"}','{\"is_archived\":false,\"is_hidden\":false,\"is_completed\":true,\"gender_type\":\"mixed\"}','http://localhost/OlamaaInstitute/backend/public/api/batches/5','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 20:51:54','2026-03-29 20:51:54'),(127,'user',1,'updated','Modules\\Batches\\Models\\Batch',6,'{\"academic_branch_id\":1,\"is_archived\":0,\"is_hidden\":0,\"is_completed\":0}','{\"academic_branch_id\":3,\"is_archived\":false,\"is_hidden\":false,\"is_completed\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 21:37:37','2026-03-29 21:37:37'),(128,'user',1,'updated','Modules\\Batches\\Models\\Batch',6,'{\"academic_branch_id\":3,\"is_archived\":0,\"is_hidden\":0,\"is_completed\":0}','{\"academic_branch_id\":2,\"is_archived\":false,\"is_hidden\":false,\"is_completed\":false}','http://localhost/OlamaaInstitute/backend/public/api/batches/6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-03-29 21:38:50','2026-03-29 21:38:50'),(129,'user',1,'created','employee',1,'[]','{\"first_name\":\"\\u0633\\u064a\\u0628\\u0633\\u064a\",\"last_name\":\"\\u0633\\u064a\\u0628\\u0633\\u064a\\u0628\",\"job_title\":\"\\u0633\\u0633\\u064a\\u0628\\u0633\\u064a\",\"job_type\":\"accountant\",\"hire_date\":\"2026-04-01\",\"phone\":\"+9630999999999\",\"institute_branch_id\":3,\"is_active\":true,\"id\":1}','http://localhost/OlamaaInstitute/backend/public/api/employees','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:03:36','2026-04-01 22:03:36'),(134,'user',1,'created','employee',2,'[]','{\"first_name\":\"\\u0633\\u064a\\u0634\\u0633\",\"last_name\":\"\\u0634\\u0633\\u064a\\u0634\\u0633\\u064a\",\"job_title\":\"\\u0634\\u0633\\u064a\\u0634\\u064a\\u0633\",\"job_type\":\"supervisor\",\"hire_date\":\"2026-04-01\",\"phone\":\"+9630999999999\",\"institute_branch_id\":1,\"is_active\":true,\"id\":2}','http://localhost/OlamaaInstitute/backend/public/api/employees','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:20:56','2026-04-01 22:20:56'),(144,'user',1,'created','user',16,'[]','{\"unique_id\":\"OEM-9389546\",\"name\":\"\\u0633\\u064a\\u0634\\u0633 \\u0634\\u0633\\u064a\\u0634\\u0633\\u064a\",\"password\":\"$2y$12$I2QtECYlCTZMr0tCzrS3G.n.506hGMmU5xvewmM0.5nEA\\/NnLgOw.\",\"is_approved\":true,\"force_password_change\":true,\"id\":16}','http://localhost/OlamaaInstitute/backend/public/api/employees/2/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:41:01','2026-04-01 22:41:01'),(145,'user',1,'updated','employee',2,'{\"user_id\":null}','{\"user_id\":16}','http://localhost/OlamaaInstitute/backend/public/api/employees/2/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:41:01','2026-04-01 22:41:01'),(146,'user',1,'updated','employee',2,'{\"is_active\":1}','{\"is_active\":false}','http://localhost/OlamaaInstitute/backend/public/api/employees/2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:41:22','2026-04-01 22:41:22'),(147,'user',1,'created','user',17,'[]','{\"unique_id\":\"OEM-5985980\",\"name\":\"\\u0633\\u064a\\u0628\\u0633\\u064a \\u0633\\u064a\\u0628\\u0633\\u064a\\u0628\",\"password\":\"$2y$12$5Kqj6zml.jlbQ5FzH6aAVekReFZOeKjIyDFbfOSIR4x1isK.c5QYS\",\"is_approved\":true,\"force_password_change\":true,\"id\":17}','http://localhost/OlamaaInstitute/backend/public/api/employees/1/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:57:59','2026-04-01 22:57:59'),(148,'user',1,'updated','employee',1,'{\"user_id\":null}','{\"user_id\":17}','http://localhost/OlamaaInstitute/backend/public/api/employees/1/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:57:59','2026-04-01 22:57:59'),(149,'user',1,'updated','user',17,'{\"role\":null}','{\"role\":\"admin\"}','http://localhost/OlamaaInstitute/backend/public/api/users/17/roles','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:58:33','2026-04-01 22:58:33'),(150,'user',1,'created','employee',3,'[]','{\"first_name\":\"\\u0633\\u064a\\u0628\",\"last_name\":\"\\u0633\\u064a\\u0628\",\"job_title\":\"\\u0633\\u064a\\u0628\",\"job_type\":\"accountant\",\"hire_date\":\"2026-04-01\",\"phone\":\"+9630999999999\",\"institute_branch_id\":3,\"is_active\":true,\"id\":3}','http://localhost/OlamaaInstitute/backend/public/api/employees','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:59:08','2026-04-01 22:59:08'),(151,'user',1,'created','user',18,'[]','{\"unique_id\":\"OEM-2906449\",\"name\":\"\\u0633\\u064a\\u0628 \\u0633\\u064a\\u0628\",\"password\":\"$2y$12$bTmJtheyUWY9hWEFpUvsY.V2zSzA1N1ijWJhm41fhEvGAXHfFCpgq\",\"is_approved\":true,\"force_password_change\":true,\"id\":18}','http://localhost/OlamaaInstitute/backend/public/api/employees/3/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:59:33','2026-04-01 22:59:33'),(152,'user',1,'updated','employee',3,'{\"user_id\":null}','{\"user_id\":18}','http://localhost/OlamaaInstitute/backend/public/api/employees/3/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 22:59:33','2026-04-01 22:59:33'),(153,'user',1,'updated','user',18,'{\"role\":null}','{\"role\":\"admin\"}','http://localhost/OlamaaInstitute/backend/public/api/users/18/roles','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:00:27','2026-04-01 23:00:27'),(154,'user',1,'updated','employee',1,'{\"is_active\":1}','{\"is_active\":false}','http://localhost/OlamaaInstitute/backend/public/api/employees/1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:19:14','2026-04-01 23:19:14'),(155,'user',1,'created','employee',4,'[]','{\"first_name\":\"\\u0633\\u064a\\u0621\",\"last_name\":\"\\u0626\\u0621\\u064a\",\"job_title\":\"~\\u0652\",\"job_type\":\"supervisor\",\"hire_date\":\"2026-04-01\",\"phone\":\"+9630976765454\",\"institute_branch_id\":3,\"is_active\":true,\"id\":4}','http://localhost/OlamaaInstitute/backend/public/api/employees','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:19:43','2026-04-01 23:19:43'),(156,'user',1,'created','employee',5,'[]','{\"first_name\":\"\\u0636\\u0635\\u062b\\u064a\",\"last_name\":\"\\u0636\\u0635\\u062b\",\"job_title\":\"\\u0636\\u0635\\u062b\",\"job_type\":\"coordinator\",\"hire_date\":\"2026-04-01\",\"phone\":\"+9630967743534\",\"institute_branch_id\":1,\"is_active\":true,\"id\":5}','http://localhost/OlamaaInstitute/backend/public/api/employees','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:21:21','2026-04-01 23:21:21'),(157,'user',1,'created','user',19,'[]','{\"unique_id\":\"OEM-8437004\",\"name\":\"\\u0636\\u0635\\u062b\\u064a \\u0636\\u0635\\u062b\",\"password\":\"$2y$12$Nlq.2xyVfhVzn.zvyXM8pee3u6YsWNv4SfwIYvwZOS4jlc9JOjGkW\",\"is_approved\":true,\"force_password_change\":true,\"id\":19}','http://localhost/OlamaaInstitute/backend/public/api/employees/5/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:22:08','2026-04-01 23:22:08'),(158,'user',1,'updated','employee',5,'{\"user_id\":null}','{\"user_id\":19}','http://localhost/OlamaaInstitute/backend/public/api/employees/5/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:22:08','2026-04-01 23:22:08'),(159,'user',1,'created','user',20,'[]','{\"unique_id\":\"OEM-5391112\",\"name\":\"\\u0633\\u064a\\u0621 \\u0626\\u0621\\u064a\",\"password\":\"$2y$12$tNBOcHa52bzRK5cCO1LDg.563TxU8KKSomfurqoLixSL6nKpa2b5.\",\"is_approved\":true,\"force_password_change\":true,\"id\":20}','http://localhost/OlamaaInstitute/backend/public/api/employees/4/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:22:22','2026-04-01 23:22:22'),(160,'user',1,'updated','employee',4,'{\"user_id\":null}','{\"user_id\":20}','http://localhost/OlamaaInstitute/backend/public/api/employees/4/activate-user','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:22:22','2026-04-01 23:22:22'),(161,'user',1,'updated','user',20,'{\"role\":null}','{\"role\":\"admin\"}','http://localhost/OlamaaInstitute/backend/public/api/users/20/roles','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:22:37','2026-04-01 23:22:37'),(162,'user',1,'updated','employee',5,'{\"is_active\":1}','{\"is_active\":false}','http://localhost/OlamaaInstitute/backend/public/api/employees/5','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:22:52','2026-04-01 23:22:52'),(163,'user',1,'updated','employee',2,'{\"is_active\":0}','{\"is_active\":true}','http://localhost/OlamaaInstitute/backend/public/api/employees/2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-01 23:30:54','2026-04-01 23:30:54'),(164,'user',1,'created','employee',6,'[]','{\"first_name\":\"\\u062b\\u0635\\u0642\",\"last_name\":\"\\u0635\\u062b\\u0642\",\"job_title\":\"\\u0635\\u062b\\u0642\",\"job_type\":\"supervisor\",\"hire_date\":\"2026-04-01\",\"phone\":\"+9630986765343\",\"institute_branch_id\":3,\"is_active\":true,\"id\":6}','http://localhost/OlamaaInstitute/backend/public/api/employees','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-02 00:03:04','2026-04-02 00:03:04'),(165,'user',1,'updated','user',16,'{\"role\":null}','{\"role\":\"employee\"}','http://localhost/OlamaaInstitute/backend/public/api/users/16/roles','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',NULL,'2026-04-02 00:16:23','2026-04-02 00:16:23');
/*!40000 ALTER TABLE `audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authorized_devices`
--

DROP TABLE IF EXISTS `authorized_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authorized_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authorized_devices_device_id_unique` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authorized_devices`
--

LOCK TABLES `authorized_devices` WRITE;
/*!40000 ALTER TABLE `authorized_devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `authorized_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch_employees`
--

DROP TABLE IF EXISTS `batch_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'supervisor',
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `assignment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `batch_employees_batch_id_employee_id_unique` (`batch_id`,`employee_id`),
  KEY `batch_employees_employee_id_foreign` (`employee_id`),
  KEY `batch_employees_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `batch_employees_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `batch_employees_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `batch_employees_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_employees`
--

LOCK TABLES `batch_employees` WRITE;
/*!40000 ALTER TABLE `batch_employees` DISABLE KEYS */;
/*!40000 ALTER TABLE `batch_employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch_student`
--

DROP TABLE IF EXISTS `batch_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch_student` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_partial` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'false = الطالب مسجل بكامل مواد الدفعة, true = الطالب مسجل بمواد محددة فقط',
  PRIMARY KEY (`id`),
  UNIQUE KEY `batch_student_batch_id_student_id_unique` (`batch_id`,`student_id`),
  KEY `batch_student_student_id_foreign` (`student_id`),
  CONSTRAINT `batch_student_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `batch_student_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_student`
--

LOCK TABLES `batch_student` WRITE;
/*!40000 ALTER TABLE `batch_student` DISABLE KEYS */;
INSERT INTO `batch_student` VALUES (1,7,1,'2026-03-29 16:15:20','2026-03-29 20:11:35',0);
/*!40000 ALTER TABLE `batch_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch_student_subjects`
--

DROP TABLE IF EXISTS `batch_student_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch_student_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_student_id` bigint(20) unsigned NOT NULL,
  `batch_subject_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active' COMMENT 'active | dropped | completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `batch_student_subjects_batch_student_id_batch_subject_id_unique` (`batch_student_id`,`batch_subject_id`),
  KEY `batch_student_subjects_batch_subject_id_foreign` (`batch_subject_id`),
  CONSTRAINT `batch_student_subjects_batch_student_id_foreign` FOREIGN KEY (`batch_student_id`) REFERENCES `batch_student` (`id`) ON DELETE CASCADE,
  CONSTRAINT `batch_student_subjects_batch_subject_id_foreign` FOREIGN KEY (`batch_subject_id`) REFERENCES `batch_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_student_subjects`
--

LOCK TABLES `batch_student_subjects` WRITE;
/*!40000 ALTER TABLE `batch_student_subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `batch_student_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch_subjects`
--

DROP TABLE IF EXISTS `batch_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20) unsigned NOT NULL,
  `class_room_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `weekly_lessons` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'عدد الحصص الأسبوعية المطلوبة لهذه المادة',
  `instructor_subject_id` bigint(20) unsigned DEFAULT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `assignment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `batch_subjects_batch_id_foreign` (`batch_id`),
  KEY `batch_subjects_subject_id_foreign` (`subject_id`),
  KEY `batch_subjects_instructor_subject_id_foreign` (`instructor_subject_id`),
  KEY `batch_subjects_assigned_by_foreign` (`assigned_by`),
  KEY `batch_subjects_class_room_id_foreign` (`class_room_id`),
  CONSTRAINT `batch_subjects_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  CONSTRAINT `batch_subjects_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`),
  CONSTRAINT `batch_subjects_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `batch_subjects_instructor_subject_id_foreign` FOREIGN KEY (`instructor_subject_id`) REFERENCES `instructor_subjects` (`id`),
  CONSTRAINT `batch_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_subjects`
--

LOCK TABLES `batch_subjects` WRITE;
/*!40000 ALTER TABLE `batch_subjects` DISABLE KEYS */;
INSERT INTO `batch_subjects` VALUES (1,1,NULL,26,4,1,NULL,NULL,NULL,1,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(2,1,NULL,27,3,2,NULL,NULL,NULL,1,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(3,1,NULL,28,3,3,NULL,NULL,NULL,1,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(4,2,NULL,29,5,4,NULL,NULL,NULL,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(5,2,NULL,30,4,7,NULL,NULL,NULL,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(6,2,NULL,31,3,8,NULL,NULL,NULL,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(7,2,NULL,32,3,10,NULL,NULL,NULL,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(8,2,NULL,33,4,12,NULL,NULL,NULL,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(9,6,NULL,11,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:06','2026-03-29 15:35:06'),(10,6,NULL,12,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:06','2026-03-29 15:35:06'),(11,6,NULL,13,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:06','2026-03-29 15:35:06'),(12,6,NULL,14,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:06','2026-03-29 15:35:06'),(13,6,NULL,15,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:06','2026-03-29 15:35:06'),(14,6,NULL,16,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:06','2026-03-29 15:35:06'),(15,6,NULL,17,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:06','2026-03-29 15:35:06'),(16,6,NULL,18,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:35:07','2026-03-29 15:35:07'),(17,7,NULL,1,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(18,7,NULL,2,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(19,7,NULL,3,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(20,7,NULL,4,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(21,7,NULL,5,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(22,7,NULL,6,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(23,7,NULL,7,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(24,7,NULL,8,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(25,7,NULL,9,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(26,7,NULL,10,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 15:39:28','2026-03-29 15:39:28'),(27,1,NULL,29,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(28,1,NULL,30,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(29,1,NULL,31,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(30,1,NULL,32,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(31,1,NULL,33,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(32,2,NULL,26,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(33,2,NULL,27,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(34,2,NULL,28,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(35,3,NULL,11,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(36,3,NULL,12,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(37,3,NULL,13,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(38,3,NULL,14,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(39,3,NULL,15,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(40,3,NULL,16,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(41,3,NULL,17,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(42,3,NULL,18,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(43,5,NULL,1,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(44,5,NULL,2,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(45,5,NULL,3,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(46,5,NULL,4,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(47,5,NULL,5,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(48,5,NULL,6,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(49,5,NULL,7,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(50,5,NULL,8,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(51,5,NULL,9,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46'),(52,5,NULL,10,0,NULL,NULL,'2026-03-29',NULL,1,'2026-03-29 16:10:46','2026-03-29 16:10:46');
/*!40000 ALTER TABLE `batch_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batches`
--

DROP TABLE IF EXISTS `batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academic_branch_id` bigint(20) unsigned DEFAULT NULL,
  `class_room_id` bigint(20) unsigned DEFAULT NULL,
  `institute_branch_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gender_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `batches_institute_branch_id_foreign` (`institute_branch_id`),
  KEY `batches_academic_branch_id_foreign` (`academic_branch_id`),
  KEY `batches_class_room_id_foreign` (`class_room_id`),
  CONSTRAINT `batches_academic_branch_id_foreign` FOREIGN KEY (`academic_branch_id`) REFERENCES `academic_branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `batches_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `batches_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batches`
--

LOCK TABLES `batches` WRITE;
/*!40000 ALTER TABLE `batches` DISABLE KEYS */;
INSERT INTO `batches` VALUES (1,4,10,1,'دورة البرمجة الشاملة - دفعة 2026','2026-03-26','2026-09-26',0,0,0,'2026-03-26 10:37:34','2026-03-26 10:37:34',NULL),(2,4,11,1,'دبلوم الذكاء الاصطناعي - الدفعة الثانية','2026-03-27','2026-11-27',0,0,0,'2026-03-27 12:14:59','2026-03-27 12:14:59','mixed'),(3,1,3,3,'شعبة الأوائل',NULL,NULL,0,0,0,'2026-03-29 14:30:15','2026-03-29 16:44:04','male'),(4,1,NULL,1,'Test Batch',NULL,NULL,1,0,0,'2026-03-29 14:57:57','2026-03-29 15:05:29','male'),(5,3,4,3,'تاسع ذكور',NULL,NULL,0,0,1,'2026-03-29 15:12:33','2026-03-29 20:51:54','mixed'),(6,2,NULL,1,'Script Test Batch 1774784106','2026-03-29',NULL,0,0,0,'2026-03-29 15:35:06','2026-03-29 21:38:50',NULL),(7,3,3,3,'تاسع اناث',NULL,NULL,0,0,0,'2026-03-29 15:39:28','2026-03-29 17:07:23','mixed'),(8,NULL,NULL,NULL,'Dummy Batch',NULL,NULL,0,0,0,'2026-03-29 20:46:32','2026-03-29 20:46:32',NULL);
/*!40000 ALTER TABLE `batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buses`
--

DROP TABLE IF EXISTS `buses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `route_description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buses`
--

LOCK TABLES `buses` WRITE;
/*!40000 ALTER TABLE `buses` DISABLE KEYS */;
INSERT INTO `buses` VALUES (1,'أبو عمر',30,'أبو عمر','خط حلب الجديدة – الفرقان',1,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(2,'عمار كرمان',30,'عمار كرمان','خط الحمدانية – الفرقان',1,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(3,'الخال',25,'الخال','خط صلاح الدين – حلب الجديدة',1,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(4,'هشام',25,'هشام','خط السكري – الفرقان',1,'2026-02-26 04:42:53','2026-02-26 04:42:53');
/*!40000 ALTER TABLE `buses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'دمشق','مدينة سورية',1,'2026-02-26 04:42:27','2026-02-26 04:42:27'),(2,'ريف دمشق','مدينة سورية',1,'2026-02-26 04:42:28','2026-02-26 04:42:28'),(3,'حلب','مدينة سورية',1,'2026-02-26 04:42:33','2026-02-26 04:42:33'),(4,'حمص','مدينة سورية',1,'2026-02-26 04:42:39','2026-02-26 04:42:39'),(5,'حماة','مدينة سورية',1,'2026-02-26 04:42:44','2026-02-26 04:42:44'),(6,'اللاذقية','مدينة سورية',1,'2026-02-26 04:42:44','2026-02-26 04:42:44'),(7,'طرطوس','مدينة سورية',1,'2026-02-26 04:42:44','2026-02-26 04:42:44'),(8,'إدلب','مدينة سورية',1,'2026-02-26 04:42:44','2026-02-26 04:42:44'),(9,'دير الزور','مدينة سورية',1,'2026-02-26 04:42:44','2026-02-26 04:42:44'),(10,'الرقة','مدينة سورية',1,'2026-02-26 04:42:45','2026-02-26 04:42:45'),(11,'الحسكة','مدينة سورية',1,'2026-02-26 04:42:45','2026-02-26 04:42:45'),(12,'درعا','مدينة سورية',1,'2026-02-26 04:42:45','2026-02-26 04:42:45'),(13,'السويداء','مدينة سورية',1,'2026-02-26 04:42:46','2026-02-26 04:42:46'),(14,'القنيطرة','مدينة سورية',1,'2026-02-26 04:42:46','2026-02-26 04:42:46');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_rooms`
--

DROP TABLE IF EXISTS `class_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_branch_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_rooms_institute_branch_id_foreign` (`institute_branch_id`),
  CONSTRAINT `class_rooms_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_rooms`
--

LOCK TABLES `class_rooms` WRITE;
/*!40000 ALTER TABLE `class_rooms` DISABLE KEYS */;
INSERT INTO `class_rooms` VALUES (1,NULL,'القاعة 1','CR-1',30,NULL,'2026-02-26 04:42:51','2026-02-26 04:42:51'),(2,NULL,'القاعة 2','CR-2',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(3,NULL,'القاعة 3','CR-3',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(4,NULL,'القاعة 4','CR-4',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(5,NULL,'القاعة 5','CR-5',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(6,NULL,'القاعة 6','CR-6',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(7,NULL,'القاعة 7','CR-7',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(8,NULL,'القاعة 8','CR-8',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(9,NULL,'القاعة 9','CR-9',30,NULL,'2026-02-26 04:42:52','2026-02-26 04:42:52'),(10,1,'قاعة البرمجة 101',NULL,25,NULL,'2026-03-26 10:35:45','2026-03-26 10:35:45'),(11,1,'قاعة البيانات 102',NULL,30,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59');
/*!40000 ALTER TABLE `class_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_schedules`
--

DROP TABLE IF EXISTS `class_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_subject_id` bigint(20) unsigned NOT NULL,
  `day_of_week` enum('saturday','sunday','monday','tuesday','wednesday','thursday','friday') DEFAULT NULL,
  `period_number` tinyint(3) unsigned NOT NULL COMMENT 'رقم الحصة في اليوم (1-5)',
  `schedule_date` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `class_room_id` bigint(20) unsigned DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_schedules_batch_subject_id_foreign` (`batch_subject_id`),
  KEY `class_schedules_class_room_id_foreign` (`class_room_id`),
  CONSTRAINT `class_schedules_batch_subject_id_foreign` FOREIGN KEY (`batch_subject_id`) REFERENCES `batch_subjects` (`id`),
  CONSTRAINT `class_schedules_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_schedules`
--

LOCK TABLES `class_schedules` WRITE;
/*!40000 ALTER TABLE `class_schedules` DISABLE KEYS */;
INSERT INTO `class_schedules` VALUES (77,4,'saturday',1,NULL,'08:00:00','08:45:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(78,4,'tuesday',3,NULL,'09:40:00','10:25:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(79,4,'wednesday',5,NULL,'11:20:00','12:05:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(80,4,'thursday',3,NULL,'09:40:00','10:25:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(81,4,'friday',3,NULL,'09:40:00','10:25:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(82,5,'tuesday',4,NULL,'10:30:00','11:15:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(83,5,'wednesday',3,NULL,'09:40:00','10:25:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(84,5,'thursday',4,NULL,'10:30:00','11:15:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(85,5,'friday',4,NULL,'10:30:00','11:15:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(86,8,'tuesday',5,NULL,'11:20:00','12:05:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(87,8,'wednesday',1,NULL,'08:00:00','08:45:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(88,8,'thursday',5,NULL,'11:20:00','12:05:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52'),(89,8,'friday',5,NULL,'11:20:00','12:05:00',11,1,1,'تم التوليد آلياً عبر المعالج الذكي - مجموعة: draft_20260327_161109_YNoNuO_sol2','2026-03-27 20:30:52','2026-03-27 20:30:52');
/*!40000 ALTER TABLE `class_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_details`
--

DROP TABLE IF EXISTS `contact_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guardian_id` bigint(20) unsigned DEFAULT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `family_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('phone','email','address','whatsapp','landline') NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `owner_type` varchar(20) DEFAULT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `supports_call` tinyint(1) NOT NULL DEFAULT 1,
  `supports_whatsapp` tinyint(1) NOT NULL DEFAULT 0,
  `supports_sms` tinyint(1) NOT NULL DEFAULT 0,
  `is_sms_stopped` tinyint(1) NOT NULL DEFAULT 0,
  `stop_sms_from` date DEFAULT NULL,
  `stop_sms_to` date DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_details_guardian_id_foreign` (`guardian_id`),
  KEY `contact_details_student_id_foreign` (`student_id`),
  KEY `contact_details_family_id_foreign` (`family_id`),
  CONSTRAINT `contact_details_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contact_details_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contact_details_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_details`
--

LOCK TABLES `contact_details` WRITE;
/*!40000 ALTER TABLE `contact_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `door_devices`
--

DROP TABLE IF EXISTS `door_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `door_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL COMMENT 'معرف فريد للجهاز، مثل DOOR_MAIN_01',
  `name` varchar(255) NOT NULL COMMENT 'اسم الجهاز لعرضه',
  `location` varchar(255) DEFAULT NULL COMMENT 'موقع الجهاز، مثل المدخل الرئيسي',
  `api_key` varchar(64) NOT NULL COMMENT 'مفتاح API للمصادقة من الأجهزة',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'هل الجهاز مفعل للتوليد',
  `last_seen_at` timestamp NULL DEFAULT NULL COMMENT 'آخر مرة تواصل فيها الجهاز',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `door_devices_device_id_unique` (`device_id`),
  UNIQUE KEY `door_devices_api_key_unique` (`api_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `door_devices`
--

LOCK TABLES `door_devices` WRITE;
/*!40000 ALTER TABLE `door_devices` DISABLE KEYS */;
INSERT INTO `door_devices` VALUES (1,'DOOR_MAIN_01','جهاز الباب الرئيسي','المدخل الشمالي','123',1,NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54');
/*!40000 ALTER TABLE `door_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `door_sessions`
--

DROP TABLE IF EXISTS `door_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `door_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` bigint(20) unsigned NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `door_sessions_session_token_unique` (`session_token`),
  KEY `door_sessions_device_id_foreign` (`device_id`),
  KEY `door_sessions_student_id_foreign` (`student_id`),
  CONSTRAINT `door_sessions_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `door_devices` (`id`),
  CONSTRAINT `door_sessions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `door_sessions`
--

LOCK TABLES `door_sessions` WRITE;
/*!40000 ALTER TABLE `door_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `door_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_institute_branch`
--

DROP TABLE IF EXISTS `employee_institute_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_institute_branch` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `institute_branch_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_institute_branch_employee_id_institute_branch_id_unique` (`employee_id`,`institute_branch_id`),
  KEY `employee_institute_branch_institute_branch_id_foreign` (`institute_branch_id`),
  CONSTRAINT `employee_institute_branch_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_institute_branch_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_institute_branch`
--

LOCK TABLES `employee_institute_branch` WRITE;
/*!40000 ALTER TABLE `employee_institute_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_institute_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `institute_branch_id` bigint(20) unsigned DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `job_type` varchar(255) NOT NULL,
  `hire_date` date NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_user_id_unique` (`user_id`),
  KEY `employees_institute_branch_id_foreign` (`institute_branch_id`),
  CONSTRAINT `employees_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,17,3,'سيبسي','سيبسيب','سسيبسي','accountant','2026-04-01','+9630999999999',0,'2026-04-01 22:03:36','2026-04-01 23:19:14',NULL),(2,16,1,'سيشس','شسيشسي','شسيشيس','supervisor','2026-04-01','+9630999999999',1,'2026-04-01 22:20:56','2026-04-01 23:30:54',NULL),(3,18,3,'سيب','سيب','سيب','accountant','2026-04-01','+9630999999999',1,'2026-04-01 22:59:08','2026-04-01 22:59:33',NULL),(4,20,3,'سيء','ئءي','~ْ','supervisor','2026-04-01','+9630976765454',1,'2026-04-01 23:19:43','2026-04-01 23:22:22',NULL),(5,19,1,'ضصثي','ضصث','ضصث','coordinator','2026-04-01','+9630967743534',0,'2026-04-01 23:21:21','2026-04-01 23:22:52',NULL),(6,NULL,3,'ثصق','صثق','صثق','supervisor','2026-04-01','+9630986765343',1,'2026-04-02 00:03:04','2026-04-02 00:03:04',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollment_contracts`
--

DROP TABLE IF EXISTS `enrollment_contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollment_contracts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `total_amount_usd` text DEFAULT NULL,
  `total_amount_usd_hash` varchar(64) DEFAULT NULL,
  `discount_percentage` text DEFAULT NULL,
  `discount_amount` text DEFAULT NULL,
  `discount_amount_hash` varchar(64) DEFAULT NULL,
  `discount_reason` varchar(255) DEFAULT NULL COMMENT 'سبب الخصم، اختياري',
  `discount_percentage_hash` varchar(64) DEFAULT NULL,
  `final_amount_usd` text DEFAULT NULL,
  `final_amount_usd_hash` varchar(64) DEFAULT NULL,
  `paid_amount_usd` text DEFAULT NULL,
  `paid_amount_usd_hash` varchar(64) DEFAULT NULL,
  `exchange_rate_at_enrollment` decimal(10,4) DEFAULT NULL,
  `final_amount_syp` text DEFAULT NULL,
  `final_amount_syp_hash` varchar(64) DEFAULT NULL,
  `agreed_at` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `first_payment_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mode` enum('automatic','manual') NOT NULL DEFAULT 'automatic',
  `installments_count` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `enrollment_contracts_student_id_foreign` (`student_id`),
  KEY `enrollment_contracts_first_payment_id_foreign` (`first_payment_id`),
  CONSTRAINT `enrollment_contracts_first_payment_id_foreign` FOREIGN KEY (`first_payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `enrollment_contracts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollment_contracts`
--

LOCK TABLES `enrollment_contracts` WRITE;
/*!40000 ALTER TABLE `enrollment_contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `enrollment_contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_result_edit_requests`
--

DROP TABLE IF EXISTS `exam_result_edit_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_result_edit_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('update','delete') NOT NULL DEFAULT 'update',
  `exam_result_id` bigint(20) unsigned NOT NULL,
  `requester_id` bigint(20) unsigned NOT NULL,
  `original_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`original_data`)),
  `proposed_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`proposed_changes`)),
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_result_edit_requests_requester_id_foreign` (`requester_id`),
  KEY `exam_result_edit_requests_exam_result_id_status_index` (`exam_result_id`,`status`),
  CONSTRAINT `exam_result_edit_requests_exam_result_id_foreign` FOREIGN KEY (`exam_result_id`) REFERENCES `exam_results` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_result_edit_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_result_edit_requests`
--

LOCK TABLES `exam_result_edit_requests` WRITE;
/*!40000 ALTER TABLE `exam_result_edit_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_result_edit_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_results`
--

DROP TABLE IF EXISTS `exam_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `obtained_marks` decimal(5,2) NOT NULL,
  `is_passed` tinyint(1) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_results_exam_id_student_id_unique` (`exam_id`,`student_id`),
  KEY `exam_results_student_id_foreign` (`student_id`),
  CONSTRAINT `exam_results_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`),
  CONSTRAINT `exam_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_results`
--

LOCK TABLES `exam_results` WRITE;
/*!40000 ALTER TABLE `exam_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_types`
--

DROP TABLE IF EXISTS `exam_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_types`
--

LOCK TABLES `exam_types` WRITE;
/*!40000 ALTER TABLE `exam_types` DISABLE KEYS */;
INSERT INTO `exam_types` VALUES (1,'test','اختبار قصير أو دوري','2026-02-26 04:42:46','2026-02-26 04:42:46'),(2,'exam','امتحان رسمي أو نهائي','2026-02-26 04:42:46','2026-02-26 04:42:46');
/*!40000 ALTER TABLE `exam_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_subject_id` bigint(20) unsigned NOT NULL,
  `exam_type_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `exam_date` date NOT NULL,
  `exam_time` time DEFAULT NULL,
  `exam_end_time` time DEFAULT NULL,
  `total_marks` int(11) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  `status` enum('scheduled','completed','cancelled','postponed') NOT NULL DEFAULT 'scheduled',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exams_batch_subject_id_foreign` (`batch_subject_id`),
  KEY `exams_exam_type_id_foreign` (`exam_type_id`),
  CONSTRAINT `exams_batch_subject_id_foreign` FOREIGN KEY (`batch_subject_id`) REFERENCES `batch_subjects` (`id`),
  CONSTRAINT `exams_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
-- Table structure for table `families`
--

DROP TABLE IF EXISTS `families`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `families` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `families_user_id_unique` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `families`
--

LOCK TABLES `families` WRITE;
/*!40000 ALTER TABLE `families` DISABLE KEYS */;
INSERT INTO `families` VALUES (1,NULL,'2026-03-29 14:40:17','2026-03-29 14:40:17');
/*!40000 ALTER TABLE `families` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fcm_tokens`
--

DROP TABLE IF EXISTS `fcm_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fcm_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` text NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `device_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`device_info`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fcm_tokens_user_id_index` (`user_id`),
  CONSTRAINT `fcm_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcm_tokens`
--

LOCK TABLES `fcm_tokens` WRITE;
/*!40000 ALTER TABLE `fcm_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcm_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guardians`
--

DROP TABLE IF EXISTS `guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guardians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `family_id` bigint(20) unsigned DEFAULT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `national_id` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_primary_contact` tinyint(1) NOT NULL DEFAULT 0,
  `occupation` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `relationship` enum('father','mother','legal_guardian','other') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_name_hash` varchar(255) DEFAULT NULL,
  `last_name_hash` varchar(255) DEFAULT NULL,
  `national_id_hash` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guardians_first_name_hash_index` (`first_name_hash`),
  KEY `guardians_last_name_hash_index` (`last_name_hash`),
  KEY `guardians_national_id_hash_index` (`national_id_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardians`
--

LOCK TABLES `guardians` WRITE;
/*!40000 ALTER TABLE `guardians` DISABLE KEYS */;
INSERT INTO `guardians` VALUES (1,1,'eyJpdiI6Ink2ZFJab1pSTFR1MzBXWjJDUG1Banc9PSIsInZhbHVlIjoibi8rdkpUYUpsLzBpRGNhQTI3RllkQT09IiwibWFjIjoiYWI1NGQ4NmIxMGFkNTMyMGZjZjc0NzA2MGZhOWM0NjU4YjkyZDcyOTA2YTg2ODQwY2M2YjQ0Nzk2ZDBhYjk5YyIsInRhZyI6IiJ9','eyJpdiI6ImpjUllocitqWkVkc0g5Y3JjdndlbUE9PSIsInZhbHVlIjoiNHViTjN5QzUwbzUwYzRINVUyZDJMQT09IiwibWFjIjoiOWM2Y2QzZTVkMWMwNDQ3NjFlNTNhZmFmNWYxZTU4ODc2ZGEwYmFjZWE5ZGNhNzVmNjFmMWJmYjNkN2IwYTM1MiIsInRhZyI6IiJ9',NULL,NULL,1,NULL,NULL,'father','2026-03-29 14:40:17','2026-03-29 14:40:17','581eb8b00de2d8a0776cc9746fcee5f996682ca3','81f955f92a04bd93e46cc943fa3eaaaf77954418',NULL),(2,1,'eyJpdiI6ImhtcXJJcWtDK2s3eUdyUHdZM1IwZUE9PSIsInZhbHVlIjoiOVZoamRqQ1dLazg5ZDJnZUtWZ3VqZz09IiwibWFjIjoiZjAwNDEzYzM4YTUzYmVhY2NjMmI0OWYzMmFhMzI0ZGQ5ZTM4ZDQwMmIzYjgwMjg2MDJmNTIyMjg1NTM1NzU4MyIsInRhZyI6IiJ9','eyJpdiI6Ik5FS09panNyMDBJT0xZcFZHVjlaWkE9PSIsInZhbHVlIjoibE5rMVcyQisxcC82YWhHVTRkdmxBZz09IiwibWFjIjoiZjFjMmFhNDcyODBlYTg2YjVkOGFlNmE4N2MzNzg2YjgyZWJiYzFkMWNjNzc2ZTVjN2NlMmUwOGJjMGY0OWY2ZiIsInRhZyI6IiJ9',NULL,NULL,0,NULL,NULL,'mother','2026-03-29 14:40:17','2026-03-29 14:40:17','828c61ff36d46d6eccb6051287e2e9158344f2af','81f955f92a04bd93e46cc943fa3eaaaf77954418',NULL);
/*!40000 ALTER TABLE `guardians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institute_branches`
--

DROP TABLE IF EXISTS `institute_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institute_branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `manager_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL COMMENT 'رمز الهاتف الدولي للدولة، مثل: +963',
  PRIMARY KEY (`id`),
  UNIQUE KEY `institute_branches_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institute_branches`
--

LOCK TABLES `institute_branches` WRITE;
/*!40000 ALTER TABLE `institute_branches` DISABLE KEYS */;
INSERT INTO `institute_branches` VALUES (1,'فرع الاختبار الرئيسي','TEST01','دمشق - المزة','0912345678',NULL,NULL,1,'2026-03-26 10:28:59','2026-03-26 10:28:59',NULL),(3,'الفرع الأول','first','حلب',NULL,NULL,'مدير',1,'2026-03-29 14:28:55','2026-03-29 14:28:55',NULL);
/*!40000 ALTER TABLE `institute_branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructor_institute_branch`
--

DROP TABLE IF EXISTS `instructor_institute_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructor_institute_branch` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `instructor_id` bigint(20) unsigned NOT NULL,
  `institute_branch_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `instructor_branch_unique` (`instructor_id`,`institute_branch_id`),
  KEY `instructor_institute_branch_institute_branch_id_foreign` (`institute_branch_id`),
  CONSTRAINT `instructor_institute_branch_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `instructor_institute_branch_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructor_institute_branch`
--

LOCK TABLES `instructor_institute_branch` WRITE;
/*!40000 ALTER TABLE `instructor_institute_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `instructor_institute_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructor_subjects`
--

DROP TABLE IF EXISTS `instructor_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructor_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `instructor_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `instructor_subjects_instructor_id_subject_id_unique` (`instructor_id`,`subject_id`),
  KEY `instructor_subjects_subject_id_foreign` (`subject_id`),
  CONSTRAINT `instructor_subjects_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `instructor_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructor_subjects`
--

LOCK TABLES `instructor_subjects` WRITE;
/*!40000 ALTER TABLE `instructor_subjects` DISABLE KEYS */;
INSERT INTO `instructor_subjects` VALUES (1,1,26,1,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(2,1,27,1,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(3,2,28,1,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(4,3,29,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(5,4,29,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(6,3,30,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(7,5,30,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(8,7,31,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(9,8,31,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(10,6,32,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(11,7,32,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(12,9,33,1,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(13,10,33,1,'2026-03-27 12:14:59','2026-03-27 12:14:59');
/*!40000 ALTER TABLE `instructor_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructors`
--

DROP TABLE IF EXISTS `instructors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `institute_branch_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `hire_date` date NOT NULL,
  `profile_photo_url` varchar(500) DEFAULT NULL,
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `instructors_user_id_foreign` (`user_id`),
  KEY `instructors_institute_branch_id_foreign` (`institute_branch_id`),
  CONSTRAINT `instructors_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `instructors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructors`
--

LOCK TABLES `instructors` WRITE;
/*!40000 ALTER TABLE `instructors` DISABLE KEYS */;
INSERT INTO `instructors` VALUES (1,NULL,1,'د. أحمد المحمد','0933112233','علوم حاسب','2026-03-26',NULL,NULL,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(2,NULL,1,'م. ليلى حسن','0944556677','رياضيات','2026-03-26',NULL,NULL,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(3,NULL,1,'د. مروان العلي','0933001122','الذكاء الاصطناعي','2026-03-27',NULL,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(4,NULL,1,'م. سارة المحمود','0933001133','لغة بايثون','2026-03-27',NULL,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(5,NULL,1,'د. سمير الخطيب','0933001144','تعلم الآلة','2026-03-27',NULL,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(6,NULL,1,'م. رنا الجاسم','0933001155','رياضيات البيانات','2026-03-27',NULL,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(7,NULL,1,'د. خالد الشامي','0933001166','هندسة البيانات','2026-03-27',NULL,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(8,NULL,1,'م. نور الصالح','0933001177','علوم البيانات','2026-03-27',NULL,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(9,NULL,1,'د. يوسف الصالح','0933001188','التعلم العميق','2026-03-27',NULL,NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(10,NULL,1,'م. أمل العلي','0933001199','البيانات الضخمة','2026-03-27',NULL,'{\"priority_level\":3,\"blocked_slots\":{\"tuesday\":[3]},\"preferred_days\":[],\"avoid_days\":[\"saturday\"],\"preferred_slots\":[],\"avoid_slots\":[2]}','2026-03-27 12:14:59','2026-03-28 10:15:43');
/*!40000 ALTER TABLE `instructors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
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
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
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
-- Table structure for table `knowledge_sources`
--

DROP TABLE IF EXISTS `knowledge_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `knowledge_sources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_sources_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `knowledge_sources`
--

LOCK TABLES `knowledge_sources` WRITE;
/*!40000 ALTER TABLE `knowledge_sources` DISABLE KEYS */;
/*!40000 ALTER TABLE `knowledge_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_templates`
--

DROP TABLE IF EXISTS `message_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('sms','in_app','email','media','all') DEFAULT NULL,
  `category` enum('general','attendance','absence','behavior','exam','financial') NOT NULL DEFAULT 'general',
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_templates`
--

LOCK TABLES `message_templates` WRITE;
/*!40000 ALTER TABLE `message_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_09_18_090507_create_permission_tables',1),(5,'2025_09_18_114623_create_institute_branches_table',1),(6,'2025_09_23_075543_add_country_code_to_institute_branches_table',1),(7,'2025_09_24_075235_add_fields_to_users_table',1),(8,'2025_09_24_082510_create_cities_table',1),(9,'2025_09_24_082512_create_student_statuses_table',1),(10,'2025_09_24_082514_create_academic_branches_table',1),(11,'2025_09_24_082517_create_message_templates_table',1),(12,'2025_09_24_082520_create_authorized_devices_table',1),(13,'2025_09_24_101546_create_buses_table',1),(14,'2025_09_24_101915_create_students_table',1),(15,'2025_09_24_103357_create_guardians_table',1),(16,'2025_09_24_104549_create_families_table',1),(17,'2025_09_24_110943_create_employees_table',1),(18,'2025_09_24_112321_create_instructors_table',1),(19,'2025_09_24_113058_create_employee_institute_branch_table',1),(20,'2025_09_24_113155_create_instructor_institute_branch_table',1),(21,'2025_09_25_071415_create_enrollment_contracts_table',1),(22,'2025_09_25_074731_create_batches_table',1),(23,'2025_09_25_075610_create_subjects_table',1),(24,'2025_09_25_075710_create_instructor_subjects_table',1),(25,'2025_09_25_083508_create_exam_types_table',1),(26,'2025_09_25_094659_create_batch_subjects_table',1),(27,'2025_09_25_095040_create_exams_table',1),(28,'2025_09_25_095041_create_exam_results_table',1),(29,'2025_09_25_100048_create_door_devices_table',1),(30,'2025_09_25_100049_create_door_sessions_table',1),(31,'2025_09_25_100240_create_class_schedules_table',1),(32,'2025_09_26_134159_create_audits_table',1),(33,'2025_09_27_093248_add_name_and_password_to_users_table',1),(34,'2025_09_27_102001_create_academic_records_table',1),(35,'2025_09_28_091350_make_email_nullable_in_users_table',1),(36,'2025_09_28_111127_add_timestamps_to_students_table',1),(37,'2025_09_30_090439_create_contact_details_table',1),(38,'2025_10_01_070313_create_payment_installments_table',1),(39,'2025_10_02_120847_add_academic_branch_id_to_batches_table',1),(40,'2025_10_06_101037_create_payments_table',1),(41,'2025_10_13_090401_create_attendances_table',1),(42,'2025_10_15_105700_create_personal_access_tokens_table',1),(43,'2025_10_18_075628_create_batch_student_table',1),(44,'2025_10_18_103408_update_guardians_table_for_encryption',1),(45,'2025_10_19_085325_add_encrypted_fields_and_hashes_to_students_table',1),(46,'2025_10_20_090409_update_role_enum_in_users_table',1),(47,'2025_10_21_075632_add_fcm_token_to_users_table',1),(48,'2025_11_01_085716_add_institute_branch_id_to_employees_table',1),(49,'2025_11_02_064713_add_mode_to_enrollment_contracts_table',1),(50,'2025_11_04_090646_create_fcm_tokens_table',1),(51,'2025_11_04_091625_remove_fcm_token_from_users_table',1),(52,'2025_11_06_082010_make_recorded_by_nullable_in_attendances_table',1),(53,'2025_11_08_095133_add_photo_path_to_employees_table',1),(54,'2025_11_10_072513_create_payment_edit_requests_table',1),(55,'2025_11_11_100845_alter_token_column_in_fcm_tokens_table',1),(56,'2025_11_11_113008_create_exam_result_edit_requests_table',1),(57,'2025_11_12_060754_add_health_and_psychological_status_to_students_table',1),(58,'2025_11_13_084101_add_profile_photo_to_instructors_table',1),(59,'2025_11_13_084842_add_paid_amount_usd_to_enrollment_contracts_table',1),(60,'2025_11_18_092405_alter_record_type_on_academic_records_table',1),(61,'2025_11_19_080015_create_settings_table',1),(62,'2025_11_19_081424_add_maintenance_message_to_settings_table',1),(63,'2025_11_20_074331_create_scheduled_tasks_table',1),(64,'2025_11_22_094104_create_class_rooms_table',1),(65,'2025_11_23_085522_create_student_exit_logs_table',1),(66,'2025_11_24_073848_add_class_room_id_to_batch_subject_table',1),(67,'2025_11_25_081617_add_employee_id_to_batch_subject_table',1),(68,'2025_12_02_100125_add_gender_type_to_batches_table',1),(69,'2025_12_04_084809_add_reason_column_to_payments_table',1),(70,'2025_12_09_064156_create_batch_employees_table',1),(71,'2025_12_09_112553_add_down_payment_and_installments_count_to_enrollment_contracts_table',1),(72,'2025_12_10_084632_add_down_payment_syp_to_enrollment_contracts_table',1),(73,'2025_12_14_081515_add_is_partial_to_batch_student_table',1),(74,'2025_12_14_085010_create_batch_student_subjects_table',1),(75,'2025_12_16_102236_rename_description_to_note_in_payments_table',1),(76,'2025_12_16_111756_rename_note_to_description_in_payments_table',1),(77,'2025_12_18_102622_make_nullable_instructor_subject_in_batch_subjects_table',1),(78,'2025_12_18_113731_create_knowledge_sources_table',1),(79,'2025_12_21_104538_remove_payment_installments_id_from_payments_table',1),(80,'2025_12_21_104838_add_class_room_id_to_batches_table',1),(81,'2025_12_21_174535_add_nullable_assignment_date_time_to_batch_subjects_table',1),(82,'2025_12_21_180307_make_assigned_by_nullable_in_batch_subjects_table',1),(83,'2025_12_21_183211_update_room_and_add_period_to_class_schedules_table',1),(84,'2025_12_24_113738_create_schools_table',1),(85,'2025_12_25_100339_add_cascade_to_fcm_tokens_to_fcm_tokens_table',1),(86,'2025_12_26_142405_add_institute_branch_id_to_instructors_table',1),(87,'2025_12_27_093025_add_institute_branch_id_to_class_rooms_table',1),(88,'2025_12_29_114733_update_students_bus_fk_on_delete_set_null',1),(89,'2025_12_30_072904_make_students_foreign_columns_nullable',1),(90,'2025_12_30_072920_alter_students_foreign_keys_nullable_on_delete',1),(91,'2026_01_03_162711_remove_down_payments_from_enrollment_contracts_table',1),(92,'2026_01_03_180413_remove_fields_from_payments_table',1),(93,'2026_01_04_071823_add_action_to_payment_edit_requests_table',1),(94,'2026_01_04_104621_add_type_to_exam_result_edit_requests_table',1),(95,'2026_01_06_113337_add_category_to_message_templates_table',1),(96,'2026_01_06_190404_add_school_id_to_students_table',1),(97,'2026_01_10_105816_create_student_messages_table',1),(98,'2026_01_16_205030_add_exam_end_time_to_exams_table',1),(99,'2026_01_29_093805_update_message_templates_add_types',1),(100,'2026_02_01_195756_create_notifications_table',1),(101,'2026_02_01_200550_create_notification_recipients_table',1),(102,'2026_02_01_201057_create_notification_attachments_table',1),(103,'2026_02_01_232332_add_target_snapshot_to_notifications_table',1),(104,'2026_02_03_081912_make_some_fields_null_in_payments_table',1),(105,'2026_02_03_092517_make_proposed_changes__null_in_paymentEditRequests_table',1),(106,'2026_02_04_093549_add_first_payment_id_to_enrollment_contracts_table',1),(107,'2026_02_05_071626_add_encryption_fields_to_enrollment_contracts_table',1),(108,'2026_02_05_072531_add_discount_percentage_hash_to_enrollment_contracts_table',1),(109,'2026_02_05_074639_add_hash_fields_to_payments_table',1),(110,'2026_02_05_080450_encrypt_fields_in_payment_installments_table',1),(111,'2026_02_11_072626_add_unique_constraint_to_exam_results_table',1),(112,'2026_02_15_082950_add_postponed_status_to_exams_table',1),(113,'2026_02_15_103610_create_modification_requests_table',1),(114,'2026_02_15_110230_add_discount_reason_to_enrollment_contracts_table',1),(115,'2026_02_18_170255_increase_encrypted_fields_length_in_students_and_guardians',1),(116,'2026_03_01_000001_make_students_required_columns_nullable',2),(117,'2026_03_05_120000_expand_contact_details_usage_and_owner_fields',3),(118,'2026_03_05_130455_make_batches_columns_nullable',3),(119,'2026_03_05_130605_make_academic_records_type_nullable',3),(120,'2026_03_05_183352_make_guardian_id_nullable_in_contact_details_table',3),(121,'2026_03_06_090000_add_landline_type_to_contact_details_table',3),(122,'2026_03_07_092012_make_enrollment_date_nullable_in_students_table',3),(123,'2026_03_08_143000_add_sms_stop_fields_to_contact_details_table',3),(124,'2026_03_14_110000_add_discount_amount_to_enrollment_contracts_table',3),(125,'2026_03_25_100301_add_weekly_lessons_to_batch_subjects_table',3),(127,'2026_03_25_100339_create_schedule_drafts_table',4),(128,'2026_03_27_111430_make_draft_fields_nullable_in_schedule_drafts_table',5),(129,'2026_03_27_123500_add_preferences_to_instructors_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'user',1),(1,'user',18),(1,'user',20),(2,'user',18),(5,'user',16),(5,'user',17);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modification_requests`
--

DROP TABLE IF EXISTS `modification_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modification_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modification_requests`
--

LOCK TABLES `modification_requests` WRITE;
/*!40000 ALTER TABLE `modification_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `modification_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_attachments`
--

DROP TABLE IF EXISTS `notification_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_attachments_notification_id_foreign` (`notification_id`),
  CONSTRAINT `notification_attachments_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_attachments`
--

LOCK TABLES `notification_attachments` WRITE;
/*!40000 ALTER TABLE `notification_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_recipients`
--

DROP TABLE IF EXISTS `notification_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_recipients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_recipients_notification_id_user_id_unique` (`notification_id`,`user_id`),
  KEY `notification_recipients_user_id_foreign` (`user_id`),
  CONSTRAINT `notification_recipients_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_recipients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_recipients`
--

LOCK TABLES `notification_recipients` WRITE;
/*!40000 ALTER TABLE `notification_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `template_id` bigint(20) unsigned DEFAULT NULL,
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `sender_type` varchar(255) DEFAULT NULL,
  `target_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_snapshot`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_template_id_foreign` (`template_id`),
  KEY `notifications_sender_id_foreign` (`sender_id`),
  CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notifications_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `message_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
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
-- Table structure for table `payment_edit_requests`
--

DROP TABLE IF EXISTS `payment_edit_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_edit_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `requester_id` bigint(20) unsigned NOT NULL,
  `original_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`original_data`)),
  `proposed_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`proposed_changes`)),
  `action` varchar(255) NOT NULL DEFAULT 'update' COMMENT 'update | delete',
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewer_comment` text DEFAULT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_edit_requests_payment_id_foreign` (`payment_id`),
  KEY `payment_edit_requests_requester_id_foreign` (`requester_id`),
  KEY `payment_edit_requests_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `payment_edit_requests_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_edit_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_edit_requests_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_edit_requests`
--

LOCK TABLES `payment_edit_requests` WRITE;
/*!40000 ALTER TABLE `payment_edit_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_edit_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_installments`
--

DROP TABLE IF EXISTS `payment_installments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_installments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_contract_id` bigint(20) unsigned NOT NULL,
  `installment_number` int(10) unsigned NOT NULL,
  `due_date` date NOT NULL,
  `planned_amount_usd` text DEFAULT NULL,
  `planned_amount_usd_hash` varchar(64) DEFAULT NULL,
  `paid_amount_usd` text DEFAULT NULL,
  `paid_amount_usd_hash` varchar(64) DEFAULT NULL,
  `exchange_rate_at_due_date` decimal(10,4) DEFAULT NULL,
  `planned_amount_syp` text DEFAULT NULL,
  `planned_amount_syp_hash` varchar(64) DEFAULT NULL,
  `status` enum('pending','paid','overdue','skipped') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_installments_enrollment_contract_id_status_index` (`enrollment_contract_id`,`status`),
  CONSTRAINT `payment_installments_enrollment_contract_id_foreign` FOREIGN KEY (`enrollment_contract_id`) REFERENCES `enrollment_contracts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_installments`
--

LOCK TABLES `payment_installments` WRITE;
/*!40000 ALTER TABLE `payment_installments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_installments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(255) NOT NULL,
  `institute_branch_id` bigint(20) unsigned NOT NULL,
  `enrollment_contract_id` bigint(20) unsigned NOT NULL,
  `amount_usd` text DEFAULT NULL,
  `amount_usd_hash` varchar(64) DEFAULT NULL,
  `amount_syp` text DEFAULT NULL,
  `amount_syp_hash` varchar(64) DEFAULT NULL,
  `exchange_rate_at_payment` decimal(10,4) DEFAULT NULL,
  `currency` enum('USD','SYP') NOT NULL DEFAULT 'SYP',
  `paid_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_receipt_number_unique` (`receipt_number`),
  KEY `payments_institute_branch_id_foreign` (`institute_branch_id`),
  KEY `payments_enrollment_contract_id_foreign` (`enrollment_contract_id`),
  CONSTRAINT `payments_enrollment_contract_id_foreign` FOREIGN KEY (`enrollment_contract_id`) REFERENCES `enrollment_contracts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'students.view','sanctum','2026-02-26 04:42:16','2026-02-26 04:42:16'),(2,'students.create','sanctum','2026-02-26 04:42:17','2026-02-26 04:42:17'),(3,'students.update','sanctum','2026-02-26 04:42:17','2026-02-26 04:42:17'),(4,'batches.view','sanctum','2026-02-26 04:42:17','2026-02-26 04:42:17'),(5,'batches.create','sanctum','2026-02-26 04:42:17','2026-02-26 04:42:17'),(6,'batches.update','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(7,'subjects.view','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(8,'subjects.create','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(9,'subjects.update','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(10,'attendances.view','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(11,'attendances.create','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(12,'attendances.update','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(13,'payments.view','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(14,'payments.create','sanctum','2026-02-26 04:42:18','2026-02-26 04:42:18'),(15,'payments.update','sanctum','2026-02-26 04:42:19','2026-02-26 04:42:19'),(16,'exams.view','sanctum','2026-02-26 04:42:19','2026-02-26 04:42:19'),(17,'exams.create','sanctum','2026-02-26 04:42:19','2026-02-26 04:42:19'),(18,'exams.update','sanctum','2026-02-26 04:42:19','2026-02-26 04:42:19'),(19,'exam_results.view','sanctum','2026-02-26 04:42:19','2026-02-26 04:42:19'),(20,'exam_results.create','sanctum','2026-02-26 04:42:19','2026-02-26 04:42:19'),(21,'exam_results.update','sanctum','2026-02-26 04:42:20','2026-02-26 04:42:20'),(22,'reports.view','sanctum','2026-02-26 04:42:20','2026-02-26 04:42:20'),(23,'reports.create','sanctum','2026-02-26 04:42:21','2026-02-26 04:42:21'),(24,'reports.update','sanctum','2026-02-26 04:42:21','2026-02-26 04:42:21'),(25,'message_templates.view','sanctum','2026-02-26 04:42:21','2026-02-26 04:42:21'),(26,'message_templates.create','sanctum','2026-02-26 04:42:22','2026-02-26 04:42:22'),(27,'message_templates.update','sanctum','2026-02-26 04:42:22','2026-02-26 04:42:22');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'user',1,'auth-token','93235755687d4b4b8d35444a160483a42ae8727969209a0c96d6fdf6fa763127','[\"*\"]','2026-02-26 16:33:11',NULL,'2026-02-26 04:52:13','2026-02-26 16:33:11'),(2,'user',1,'auth-token','796294d8c7bd5a7b5bdc060fe54464080c8421acd6ae33f5391ae346aa48268c','[\"*\"]','2026-02-26 17:26:55',NULL,'2026-02-26 17:18:01','2026-02-26 17:26:55'),(3,'user',1,'auth-token','8c27620959cddb3bc3ffe491811f84a08a52e6a0304aee63e190f4ac6c4fe273','[\"*\"]','2026-03-26 13:19:44',NULL,'2026-03-26 13:16:07','2026-03-26 13:19:44'),(4,'user',1,'auth-token','f0ebc20b7e7b07e5b93b631c0318f86e5322cf0952de03a330b3d290b7195ec4','[\"*\"]','2026-03-29 22:12:56',NULL,'2026-03-26 13:20:13','2026-03-29 22:12:56'),(5,'user',1,'auth-token','aeca5af57280e384fff86aad6e741ad135455a9ad11c5e1e3005d9d88fe7b682','[\"*\"]','2026-03-26 13:56:09',NULL,'2026-03-26 13:45:41','2026-03-26 13:56:09'),(6,'user',1,'auth-token','f142c44f5e06ee70967051cec3a7d0e820ddb65347f77a8c497f8e1734b14271','[\"*\"]','2026-03-26 14:09:09',NULL,'2026-03-26 13:56:38','2026-03-26 14:09:09'),(7,'user',1,'auth-token','01e962aba09566942a5927baeda638e86461fe45d1ad7c51e491f180aa7db2d8','[\"*\"]','2026-03-27 10:37:15',NULL,'2026-03-26 14:09:29','2026-03-27 10:37:15'),(8,'user',1,'auth-token','e343765c8ae969a27d2d3d1dd49c34e0448ea4c110084c9bb80e4a345b0ff076','[\"*\"]','2026-03-27 11:20:42',NULL,'2026-03-27 11:20:31','2026-03-27 11:20:42'),(9,'user',1,'auth-token','271f444d0d45a4f02f527bb78a98ae348c32e96282c91d5594ec31dcb2b31828','[\"*\"]','2026-04-02 00:05:32',NULL,'2026-03-27 11:20:53','2026-04-02 00:05:32'),(10,'user',1,'auth-token','9179883ee7742407b703411628c7e8481efad0b1a5e07a95def3b6093175cafe','[\"*\"]',NULL,NULL,'2026-03-27 19:35:36','2026-03-27 19:35:36'),(11,'user',1,'auth-token','991aba01ca2ae90a0caccaee62003f8771ba3096c96ceca5054bec27320b7c27','[\"*\"]','2026-03-29 12:31:27',NULL,'2026-03-27 19:40:37','2026-03-29 12:31:27'),(12,'user',1,'auth-token','384711aed720392fedb6c037a33cd1ae40bc0837bdb5b654a571157689632ad0','[\"*\"]','2026-03-29 21:26:28',NULL,'2026-03-29 20:09:58','2026-03-29 21:26:28'),(13,'user',1,'auth-token','179f9ba3658ce0a4362256fa61b35afb554e583af1b23637110d8c59e63c770f','[\"*\"]','2026-04-01 22:57:33',NULL,'2026-04-01 22:02:15','2026-04-01 22:57:33'),(14,'user',1,'auth-token','48222fb370a64029e1bc809db43242d63f90758333d78b531118a28c07233b4d','[\"*\"]','2026-04-02 00:03:08',NULL,'2026-04-01 22:57:36','2026-04-02 00:03:08'),(15,'user',1,'auth-token','7cb2e4af9484f54d05be69f530b683c257d9c620a5f029d9f4554b1e57eaa256','[\"*\"]','2026-04-02 00:03:37',NULL,'2026-04-02 00:03:24','2026-04-02 00:03:37'),(16,'user',1,'auth-token','f17873c7d46b3ec66a75e4fa60b94fcdf972ab8207a280e92d87d57c2c7a33ca','[\"*\"]','2026-04-02 00:05:14',NULL,'2026-04-02 00:05:03','2026-04-02 00:05:14'),(17,'user',1,'auth-token','fc2c5f6d01a871eda34bbe892d05805c25917338725fbb402d0f0ccbc673fd8c','[\"*\"]','2026-04-02 00:42:50',NULL,'2026-04-02 00:05:28','2026-04-02 00:42:50'),(18,'user',20,'auth-token','e09af19a0e5198f311c9eb2e39e4fd16c76a0e183e33435430fe198ca9a36540','[\"*\"]','2026-04-02 00:42:50',NULL,'2026-04-02 00:32:15','2026-04-02 00:42:50');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,6),(2,1),(2,2),(2,5),(3,1),(3,2),(3,5),(4,1),(4,2),(5,1),(5,2),(5,5),(6,1),(6,2),(6,5),(7,1),(7,2),(7,3),(8,1),(9,1),(10,1),(10,2),(11,1),(11,3),(12,1),(12,3),(13,1),(13,4),(13,6),(14,1),(14,4),(15,1),(16,1),(16,3),(17,1),(18,1),(19,1),(20,1),(20,3),(21,1),(21,3),(22,1),(22,2),(22,4),(22,6),(23,1),(24,1),(25,1),(26,1),(27,1);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23'),(2,'manager','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23'),(3,'teacher','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23'),(4,'employee_accountant','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23'),(5,'employee_data_entry','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23'),(6,'employee_auditor','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23'),(7,'student','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23'),(8,'parent','sanctum','2026-02-26 04:42:23','2026-02-26 04:42:23');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_drafts`
--

DROP TABLE IF EXISTS `schedule_drafts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule_drafts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `draft_group_id` varchar(255) NOT NULL COMMENT 'معرف مجموعة المسودات (للسماح بعدة نسخ)',
  `batch_subject_id` bigint(20) unsigned NOT NULL,
  `day_of_week` varchar(255) DEFAULT NULL,
  `period_number` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `class_room_id` bigint(20) unsigned DEFAULT NULL,
  `is_conflict` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'هل يوجد تعارض في هذه الحصة؟',
  `conflict_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_drafts_batch_subject_id_foreign` (`batch_subject_id`),
  KEY `schedule_drafts_class_room_id_foreign` (`class_room_id`),
  KEY `schedule_drafts_draft_group_id_index` (`draft_group_id`),
  CONSTRAINT `schedule_drafts_batch_subject_id_foreign` FOREIGN KEY (`batch_subject_id`) REFERENCES `batch_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_drafts_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=354 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_drafts`
--

LOCK TABLES `schedule_drafts` WRITE;
/*!40000 ALTER TABLE `schedule_drafts` DISABLE KEYS */;
INSERT INTO `schedule_drafts` VALUES (316,'draft_20260327_161109_YNoNuO_sol1',4,'monday',2,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(317,'draft_20260327_161109_YNoNuO_sol1',4,'tuesday',2,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(318,'draft_20260327_161109_YNoNuO_sol1',4,'wednesday',5,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(319,'draft_20260327_161109_YNoNuO_sol1',4,'thursday',1,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(320,'draft_20260327_161109_YNoNuO_sol1',4,'friday',3,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(321,'draft_20260327_161109_YNoNuO_sol1',5,'sunday',3,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(322,'draft_20260327_161109_YNoNuO_sol1',5,'monday',1,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(323,'draft_20260327_161109_YNoNuO_sol1',5,'tuesday',5,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(324,'draft_20260327_161109_YNoNuO_sol1',5,'thursday',4,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(325,'draft_20260327_161109_YNoNuO_sol1',6,'saturday',3,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(326,'draft_20260327_161109_YNoNuO_sol1',6,'sunday',1,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(327,'draft_20260327_161109_YNoNuO_sol1',6,'tuesday',1,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(328,'draft_20260327_161109_YNoNuO_sol1',7,'saturday',2,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(329,'draft_20260327_161109_YNoNuO_sol1',7,'thursday',3,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(330,'draft_20260327_161109_YNoNuO_sol1',7,'friday',5,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(331,'draft_20260327_161109_YNoNuO_sol1',8,'sunday',5,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(332,'draft_20260327_161109_YNoNuO_sol1',8,'monday',5,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(333,'draft_20260327_161109_YNoNuO_sol1',8,'thursday',5,NULL,NULL,2,0,NULL,'2026-03-27 20:11:14','2026-03-27 20:11:14'),(334,'draft_20260327_161109_YNoNuO_sol1',8,'friday',4,NULL,NULL,2,0,NULL,'2026-03-27 20:11:15','2026-03-27 20:11:15');
/*!40000 ALTER TABLE `schedule_drafts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduled_tasks`
--

DROP TABLE IF EXISTS `scheduled_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheduled_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_name` varchar(255) NOT NULL,
  `last_run_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scheduled_tasks_task_name_unique` (`task_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduled_tasks`
--

LOCK TABLES `scheduled_tasks` WRITE;
/*!40000 ALTER TABLE `scheduled_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schools` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('public','private','other') DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schools`
--

LOCK TABLES `schools` WRITE;
/*!40000 ALTER TABLE `schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
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
INSERT INTO `sessions` VALUES ('fAY5m4NkhjA9X3ZAhVW579O1wyWuTqaoLZQvuZ2f',NULL,'::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-GB) WindowsPowerShell/5.1.19041.2673','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUHA0V2VnRmc1QUIwODJ0ZTZBRmdRSnQxZDJ2cGxUNERldWNoN3FYVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly9sb2NhbGhvc3QvT2xhbWFhSW5zdGl0dXRlL2JhY2tlbmQvcHVibGljIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1774623878);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `is_system_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `maintenance_message` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,1,NULL,'2026-02-26 04:42:26','2026-04-02 00:16:56');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_exit_logs`
--

DROP TABLE IF EXISTS `student_exit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_exit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `exit_date` date NOT NULL,
  `exit_time` time NOT NULL,
  `return_time` time DEFAULT NULL,
  `exit_type` varchar(50) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `recorded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_exit_logs_recorded_by_foreign` (`recorded_by`),
  KEY `student_exit_logs_student_id_exit_date_index` (`student_id`,`exit_date`),
  CONSTRAINT `student_exit_logs_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_exit_logs_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_exit_logs`
--

LOCK TABLES `student_exit_logs` WRITE;
/*!40000 ALTER TABLE `student_exit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_exit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_messages`
--

DROP TABLE IF EXISTS `student_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `template_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('sent','failed') NOT NULL DEFAULT 'sent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_messages_student_id_foreign` (`student_id`),
  KEY `student_messages_template_id_foreign` (`template_id`),
  CONSTRAINT `student_messages_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_messages_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `message_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_messages`
--

LOCK TABLES `student_messages` WRITE;
/*!40000 ALTER TABLE `student_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_statuses`
--

DROP TABLE IF EXISTS `student_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_statuses_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_statuses`
--

LOCK TABLES `student_statuses` WRITE;
/*!40000 ALTER TABLE `student_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_branch_id` bigint(20) unsigned DEFAULT NULL,
  `family_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `profile_photo_url` varchar(500) DEFAULT NULL,
  `id_card_photo_url` varchar(500) DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `start_attendance_date` date DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `previous_school_name` varchar(255) DEFAULT NULL,
  `national_id` text DEFAULT NULL,
  `how_know_institute` varchar(255) DEFAULT NULL,
  `bus_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `health_status` varchar(255) DEFAULT NULL,
  `psychological_status` varchar(255) DEFAULT NULL,
  `status_id` bigint(20) unsigned DEFAULT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  `qr_code_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_name_hash` varchar(255) DEFAULT NULL,
  `last_name_hash` varchar(255) DEFAULT NULL,
  `national_id_hash` varchar(255) DEFAULT NULL,
  `school_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_user_id_unique` (`user_id`),
  KEY `students_first_name_hash_index` (`first_name_hash`),
  KEY `students_last_name_hash_index` (`last_name_hash`),
  KEY `students_national_id_hash_index` (`national_id_hash`),
  KEY `students_institute_branch_id_foreign` (`institute_branch_id`),
  KEY `students_branch_id_foreign` (`branch_id`),
  KEY `students_bus_id_foreign` (`bus_id`),
  KEY `students_status_id_foreign` (`status_id`),
  KEY `students_city_id_foreign` (`city_id`),
  KEY `students_school_id_foreign` (`school_id`),
  CONSTRAINT `students_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `academic_branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_bus_id_foreign` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_institute_branch_id_foreign` FOREIGN KEY (`institute_branch_id`) REFERENCES `institute_branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `student_statuses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,3,1,NULL,'eyJpdiI6ImVsU0c4NVFQRGFrRTNpMXZxckdNd2c9PSIsInZhbHVlIjoiS3Q5eG1qNUpYSkV2YmVTL2ttNWFWdz09IiwibWFjIjoiYWRhZDhlNDI5MzdlOTMxN2UzYzg0MDRkZDg5YWVlZmM1ZWExZDYzZjdlOGE3MjU0NjYyYTU3MzQ1MTk4ZjQzNSIsInRhZyI6IiJ9','eyJpdiI6IlNsb2FZYW0vRWdNVW1zbUcyalozY1E9PSIsInZhbHVlIjoiT3RLMTBTR1J4OXg0bHVHbXYyTDN4UT09IiwibWFjIjoiYzIwYzQ3NjY2OTcyNmI1YmMwNzBkN2IxMmRiOWMzOWNkNzQzNDk1YTEyNDkwNDY5NTkxOTIxZWQ1YWJjNmJlNSIsInRhZyI6IiJ9',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,'2026-03-29 14:40:17','2026-03-29 14:41:50','9ac0bbb8ad65e4416e872f0349349d3e82759057','81f955f92a04bd93e46cc943fa3eaaaf77954418',NULL,NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academic_branch_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subjects_academic_branch_id_foreign` (`academic_branch_id`),
  CONSTRAINT `subjects_academic_branch_id_foreign` FOREIGN KEY (`academic_branch_id`) REFERENCES `academic_branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,3,'اللغة العربية',NULL,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(2,3,'اللغة الإنجليزية',NULL,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(3,3,'الرياضيات',NULL,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(4,3,'العلوم العامة',NULL,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(5,3,'الفيزياء',NULL,'2026-02-26 04:42:53','2026-02-26 04:42:53'),(6,3,'الكيمياء',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(7,3,'التاريخ',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(8,3,'الجغرافيا',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(9,3,'التربية الدينية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(10,3,'التربية الوطنية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(11,1,'اللغة العربية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(12,1,'اللغة الإنجليزية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(13,1,'الرياضيات',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(14,1,'الفيزياء',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(15,1,'الكيمياء',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(16,1,'العلوم',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(17,1,'التربية الدينية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(18,1,'التربية الوطنية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(19,2,'اللغة العربية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(20,2,'اللغة الإنجليزية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(21,2,'التاريخ',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(22,2,'الجغرافيا',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(23,2,'الفلسفة',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(24,2,'التربية الدينية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(25,2,'التربية الوطنية',NULL,'2026-02-26 04:42:54','2026-02-26 04:42:54'),(26,4,'أساسيات البرمجة',NULL,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(27,4,'الخوارزميات',NULL,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(28,4,'قواعد البيانات',NULL,'2026-03-26 10:37:34','2026-03-26 10:37:34'),(29,4,'لغة بايثون للذكاء الاصطناعي',NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(30,4,'تعلم الآلة (Machine Learning)',NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(31,4,'مبادئ هندسة البيانات',NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(32,4,'الرياضيات المتقدمة للذكاء الاصطناعي',NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59'),(33,4,'الشبكات العصبية والتعلم العميق',NULL,'2026-03-27 12:14:59','2026-03-27 12:14:59');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','employee','student','family') DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `force_password_change` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_unique_id_unique` (`unique_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'OAD-00001',NULL,NULL,'المشرف العام','$2y$12$sxVM6l.FNLpgEgshiseLAuDlsXNH6L4t4PIdErXbcdO3XJ9dZ.pOC','admin',1,0,NULL,'2026-02-26 04:42:26','2026-02-26 04:42:26'),(2,'admin',NULL,NULL,'Admin User','$2y$12$17W6klTWDFpRAn0EpePsyOtaH7BoCH3ukLYFI3Km4YWYyLgggxH.e','admin',1,0,NULL,'2026-03-26 13:15:58','2026-03-26 13:15:58'),(16,'OEM-9389546',NULL,NULL,'سيشس شسيشسي','$2y$12$I2QtECYlCTZMr0tCzrS3G.n.506hGMmU5xvewmM0.5nEA/NnLgOw.','employee',1,1,NULL,'2026-04-01 22:41:01','2026-04-02 00:16:23'),(17,'OEM-5985980',NULL,NULL,'سيبسي سيبسيب','$2y$12$5Kqj6zml.jlbQ5FzH6aAVekReFZOeKjIyDFbfOSIR4x1isK.c5QYS','admin',1,1,NULL,'2026-04-01 22:57:59','2026-04-01 22:58:33'),(18,'OEM-2906449',NULL,NULL,'سيب سيب','$2y$12$bTmJtheyUWY9hWEFpUvsY.V2zSzA1N1ijWJhm41fhEvGAXHfFCpgq','admin',1,1,NULL,'2026-04-01 22:59:33','2026-04-01 23:00:27'),(19,'OEM-8437004',NULL,NULL,'ضصثي ضصث','$2y$12$Nlq.2xyVfhVzn.zvyXM8pee3u6YsWNv4SfwIYvwZOS4jlc9JOjGkW',NULL,1,1,NULL,'2026-04-01 23:22:08','2026-04-01 23:22:08'),(20,'OEM-5391112',NULL,NULL,'سيء ئءي','$2y$12$tNBOcHa52bzRK5cCO1LDg.563TxU8KKSomfurqoLixSL6nKpa2b5.','admin',1,1,NULL,'2026-04-01 23:22:22','2026-04-01 23:22:37');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-01 16:42:53
