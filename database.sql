-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.42 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for event_v2
USE `u492478120_event_v2`;

-- Dumping structure for table event_v2.attendees
DROP TABLE IF EXISTS `attendees`;
CREATE TABLE IF NOT EXISTS `attendees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_item_id` int NOT NULL,
  `full_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `checked_in_at` datetime DEFAULT NULL,
  `checked_in_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `checked_in_by` (`checked_in_by`),
  KEY `idx_ticket_code` (`ticket_code`),
  CONSTRAINT `attendees_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendees_ibfk_2` FOREIGN KEY (`checked_in_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.attendees: ~3 rows (approximately)
INSERT INTO `attendees` (`id`, `order_item_id`, `full_name`, `email`, `ticket_code`, `checked_in_at`, `checked_in_by`) VALUES
	(1, 1, 'User UOC', 'ireneolsonfb@gmail.com', 'GRP1', NULL, NULL),
	(2, 1, 'User UOC', 'ireneolsonfb@gmail.com', 'GRP1', NULL, NULL),
	(3, 2, 'User UOC', 'ireneolsonfb@gmail.com', 'GA9HM7K6', '2025-08-21 22:30:40', 1);

-- Dumping structure for table event_v2.categories
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.categories: ~4 rows (approximately)
INSERT INTO `categories` (`id`, `name`) VALUES
	(1, 'Music'),
	(2, 'Conference'),
	(3, 'Workshop'),
	(4, 'Sports');

-- Dumping structure for table event_v2.coordinator_applications
DROP TABLE IF EXISTS `coordinator_applications`;
CREATE TABLE IF NOT EXISTS `coordinator_applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `organization_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `experience_years` int DEFAULT '0',
  `previous_events` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `motivation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applied_at` timestamp NULL DEFAULT NULL,
  `documents_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected','reapplied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `reviewed_by` (`reviewed_by`),
  CONSTRAINT `coordinator_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coordinator_applications_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.coordinator_applications: ~1 rows (approximately)
INSERT INTO `coordinator_applications` (`id`, `user_id`, `organization_name`, `organization_type`, `experience_years`, `previous_events`, `motivation`, `contact_email`, `contact_phone`, `website`, `social_media`, `applied_at`, `documents_path`, `status`, `admin_notes`, `created_at`, `reviewed_at`, `reviewed_by`) VALUES
	(1, 6, 'GLOBAL EVENT SOLUTIONS PVT LTD', 'company', 3, '', 'We want sell tickets for our events', 'induwaraashinsana7@gmail.com', '0779190005', '', '', '2025-08-21 15:05:20', NULL, 'approved', '', '2025-08-21 14:21:53', '2025-08-21 15:05:40', 1);

-- Dumping structure for table event_v2.events
DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `venue` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organizer` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_organizer` tinyint(1) DEFAULT '1',
  `show_booking_phone` tinyint(1) DEFAULT '1',
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `status` enum('draft','pending_approval','published','archived','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `waitlist_enabled` tinyint(1) DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_by` int DEFAULT NULL,
  `archive_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reactivation_requested` tinyint(1) DEFAULT '0',
  `reactivation_requested_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`),
  KEY `fk_events_archived_by` (`archived_by`),
  KEY `idx_events_archived` (`archived_at`,`archived_by`),
  KEY `idx_events_reactivation` (`reactivation_requested`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `events_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_events_archived_by` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.events: ~2 rows (approximately)
INSERT INTO `events` (`id`, `title`, `category`, `venue`, `image_path`, `organizer`, `booking_phone`, `show_organizer`, `show_booking_phone`, `starts_at`, `ends_at`, `status`, `description`, `waitlist_enabled`, `created_by`, `approval_status`, `approved_by`, `approved_at`, `rejection_reason`, `created_at`, `archived_at`, `archived_by`, `archive_reason`, `reactivation_requested`, `reactivation_requested_at`) VALUES
	(4, 'KUMARAYAN', '', 'Cinnamon Lakeside Indoor Marquee', 'uploads/events/event_4_1755792794.png', 'GLOBAL EVENT SOLUTIONS PVT LTD', '0779190005', 1, 1, '2025-08-30 19:00:00', '2025-08-31 00:00:00', 'published', '"KUMARAYAN" ORGANIZED BY GLOBAL EVENT SOLUTIONS ON THE 30TH OF AUGUST FROM 07.00PM, AT THE IMPERIAL COURT, CINNAMON LAKESIDE IS AN EVENT BY THE PRINCES OF ROMAMCE IN OUR MUSIC INDUSTRY.\r\nLOCK THE DATES FOR AN EVENING WITH PRINCES OF ROMANCE', 1, 6, 'approved', 1, '2025-08-21 16:20:52', '', '2025-08-21 15:15:57', NULL, NULL, NULL, 0, NULL),
	(5, 'DADDY & 2FORTY2', 'Conference', 'Taj Samudra Colombo', 'uploads/events/event_5_1755800749.webp', 'GLOBAL EVENT SOLUTIONS PVT LTD', '0779190005', 1, 1, '2025-09-05 18:00:00', '2025-09-06 00:00:00', 'published', 'Two pioneers and icons of Sri Lanka’s music band scene, DADDY and 2FORTY2, will perform together for the very first time. While you may have seen them at the same events in separate sessions, this special occasion marks the first time they will share the stage, performing together in joint sessions as well as in their own individual sets throughout the night. This is your opportunity to experience a performance that goes beyond a typical concert and offers a truly unique musical concept. Join us for “DADDY & 2FORTY2” on 5th September 2025 at Taj Samudra Hotel Colombo, from 7:30 PM onwards. This promises to be one of the finest musical events in Sri Lanka’s hotel entertainment scene.', 1, 6, 'pending', 1, '2025-08-21 18:19:05', '', '2025-08-21 18:18:37', NULL, NULL, NULL, 0, NULL);

-- Dumping structure for table event_v2.event_waitlist
DROP TABLE IF EXISTS `event_waitlist`;
CREATE TABLE IF NOT EXISTS `event_waitlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `joined_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('waiting','invited','purchased','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'waiting',
  `invited_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `reminder_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_event` (`user_id`,`event_id`),
  KEY `idx_event_status` (`event_id`,`status`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_status_expires` (`status`,`expires_at`),
  CONSTRAINT `fk_waitlist_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_waitlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.event_waitlist: ~1 rows (approximately)
INSERT INTO `event_waitlist` (`id`, `event_id`, `user_id`, `email`, `name`, `joined_at`, `status`, `invited_at`, `expires_at`, `expired_at`, `reminder_sent_at`, `created_at`, `updated_at`) VALUES
	(1, 5, 1, 'no-email@placeholder.com', 'Administrator', '2025-08-21 19:19:05', 'waiting', NULL, NULL, NULL, NULL, '2025-08-21 19:19:05', '2025-08-21 19:19:05');

-- Dumping structure for table event_v2.orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `event_id` int NOT NULL,
  `total_cents` int NOT NULL,
  `currency` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LKR',
  `status` enum('pending','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.orders: ~2 rows (approximately)
INSERT INTO `orders` (`id`, `user_id`, `event_id`, `total_cents`, `currency`, `status`, `created_at`) VALUES
	(1, 2, 4, 500000, 'LKR', 'paid', '2025-08-21 16:49:59'),
	(2, 2, 4, 250000, 'LKR', 'paid', '2025-08-21 16:59:06');

-- Dumping structure for table event_v2.order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `ticket_type_id` int NOT NULL,
  `unit_price_cents` int NOT NULL,
  `qty` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `ticket_type_id` (`ticket_type_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.order_items: ~2 rows (approximately)
INSERT INTO `order_items` (`id`, `order_id`, `ticket_type_id`, `unit_price_cents`, `qty`) VALUES
	(1, 1, 6, 250000, 2),
	(2, 2, 6, 250000, 1);

-- Dumping structure for table event_v2.promocodes
DROP TABLE IF EXISTS `promocodes`;
CREATE TABLE IF NOT EXISTS `promocodes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kind` enum('percent','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `value` int NOT NULL DEFAULT '0',
  `valid_from` datetime DEFAULT NULL,
  `valid_to` datetime DEFAULT NULL,
  `uses_limit` int NOT NULL DEFAULT '0',
  `uses_count` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_event_code` (`event_id`,`code`),
  CONSTRAINT `promocodes_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.promocodes: ~0 rows (approximately)

-- Dumping structure for table event_v2.ticket_types
DROP TABLE IF EXISTS `ticket_types`;
CREATE TABLE IF NOT EXISTS `ticket_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price_cents` int NOT NULL DEFAULT '0',
  `quantity_available` int NOT NULL DEFAULT '0',
  `max_per_order` int NOT NULL DEFAULT '10',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `currency` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LKR',
  `qty_total` int NOT NULL DEFAULT '0',
  `qty_sold` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `ticket_types_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.ticket_types: ~1 rows (approximately)
INSERT INTO `ticket_types` (`id`, `event_id`, `name`, `description`, `price_cents`, `quantity_available`, `max_per_order`, `created_at`, `updated_at`, `currency`, `qty_total`, `qty_sold`) VALUES
	(6, 4, 'Silver', '', 250000, 50, 10, '2025-08-21 16:44:03', '2025-08-21 16:59:37', 'LKR', 100, 3);

-- Dumping structure for table event_v2.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('ordinary','admin','checker','coordinator') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ordinary',
  `active` tinyint(1) DEFAULT '1',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coordinator_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coordinator_applied_at` timestamp NULL DEFAULT NULL,
  `coordinator_approved_at` timestamp NULL DEFAULT NULL,
  `coordinator_approved_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `coordinator_approved_by` (`coordinator_approved_by`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`coordinator_approved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table event_v2.users: ~6 rows (approximately)
INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `active`, `email`, `phone`, `organization`, `coordinator_status`, `coordinator_applied_at`, `coordinator_approved_at`, `coordinator_approved_by`, `created_at`) VALUES
	(1, 'Administrator', 'admin', 'admin123', 'admin', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-21 13:32:30'),
	(2, 'User UOC', 'uoc', 'uoc', 'ordinary', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-21 13:32:30'),
	(3, 'Ticket Checker', 'checker', 'checker', 'checker', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-21 13:32:30'),
	(4, 'John Event Organizer', 'johnevents', 'password123', 'coordinator', 1, 'john@events.com', '0771234567', 'Event Masters Ltd', 'approved', '2025-08-21 13:32:31', NULL, NULL, '2025-08-21 13:32:31'),
	(5, 'Sarah Festival Coordinator', 'sarahfest', 'password123', 'coordinator', 1, 'sarah@festivals.lk', '0777654321', 'Festival Pro', 'pending', '2025-08-21 13:32:31', NULL, NULL, '2025-08-21 13:32:31'),
	(6, 'Induwara Ashinsana', 'ashinsana', 'Induwara@2004', 'coordinator', 1, 'induwaraashinsana7@gmail.com', '0779190005', 'GLOBAL EVENT SOLUTIONS PVT LTD', 'approved', '2025-08-21 14:21:53', '2025-08-21 15:05:40', 1, '2025-08-21 14:21:53');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
