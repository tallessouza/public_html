CREATE TABLE IF NOT EXISTS `health_check_result_history_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `check_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `check_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_message` text COLLATE utf8mb4_unicode_ci,
  `short_summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` json NOT NULL,
  `ended_at` timestamp NOT NULL,
  `batch` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `health_check_result_history_items_created_at_index` (`created_at`),
  KEY `health_check_result_history_items_batch_index` (`batch`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- `health_check_result_history_items`
--

-- INSERT INTO `health_check_result_history_items` (`id`, `check_name`, `check_label`, `status`, `notification_message`, `short_summary`, `meta`, `ended_at`, `batch`, `created_at`, `updated_at`) VALUES
-- (1, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\": true, \"expected\": false}', '2023-06-05 13:29:05', '67152dd0-ec8d-440c-b211-d6f9c4a119a7', '2023-06-05 13:29:08', '2023-06-05 13:29:08'),
-- (2, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\": \"local\", \"expected\": \"production\"}', '2023-06-05 13:29:05', '67152dd0-ec8d-440c-b211-d6f9c4a119a7', '2023-06-05 13:29:08', '2023-06-05 13:29:08'),
-- (3, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\": \"mysql\"}', '2023-06-05 13:29:05', '67152dd0-ec8d-440c-b211-d6f9c4a119a7', '2023-06-05 13:29:08', '2023-06-05 13:29:08'),
-- (4, 'UsedDiskSpace', 'Used Disk Space', 'warning', 'The disk is almost full (89% used).', '89%', '{\"disk_space_used_percentage\": 89}', '2023-06-05 13:29:05', '67152dd0-ec8d-440c-b211-d6f9c4a119a7', '2023-06-05 13:29:08', '2023-06-05 13:29:08'),
-- (5, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\": true, \"expected\": false}', '2023-06-05 13:31:06', '272c5975-2618-4d43-a1d6-5ba7d0b40620', '2023-06-05 13:31:06', '2023-06-05 13:31:06'),
-- (6, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\": \"local\", \"expected\": \"production\"}', '2023-06-05 13:31:06', '272c5975-2618-4d43-a1d6-5ba7d0b40620', '2023-06-05 13:31:06', '2023-06-05 13:31:06'),
-- (7, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\": \"mysql\"}', '2023-06-05 13:31:06', '272c5975-2618-4d43-a1d6-5ba7d0b40620', '2023-06-05 13:31:06', '2023-06-05 13:31:06'),
-- (8, 'UsedDiskSpace', 'Used Disk Space', 'warning', 'The disk is almost full (89% used).', '89%', '{\"disk_space_used_percentage\": 89}', '2023-06-05 13:31:06', '272c5975-2618-4d43-a1d6-5ba7d0b40620', '2023-06-05 13:31:06', '2023-06-05 13:31:06'),
-- (9, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\": true, \"expected\": false}', '2023-06-05 13:31:14', 'e12f2d94-cb0e-433e-9183-966078b88c70', '2023-06-05 13:31:14', '2023-06-05 13:31:14'),
-- (10, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\": \"local\", \"expected\": \"production\"}', '2023-06-05 13:31:14', 'e12f2d94-cb0e-433e-9183-966078b88c70', '2023-06-05 13:31:14', '2023-06-05 13:31:14'),
-- (11, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\": \"mysql\"}', '2023-06-05 13:31:14', 'e12f2d94-cb0e-433e-9183-966078b88c70', '2023-06-05 13:31:14', '2023-06-05 13:31:14'),
-- (12, 'UsedDiskSpace', 'Used Disk Space', 'warning', 'The disk is almost full (89% used).', '89%', '{\"disk_space_used_percentage\": 89}', '2023-06-05 13:31:14', 'e12f2d94-cb0e-433e-9183-966078b88c70', '2023-06-05 13:31:14', '2023-06-05 13:31:14'),
-- (13, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\": true, \"expected\": false}', '2023-06-05 18:06:18', '413fede9-8c2c-40aa-b36e-c3849ca27b1a', '2023-06-05 18:06:18', '2023-06-05 18:06:18'),
-- (14, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\": \"local\", \"expected\": \"production\"}', '2023-06-05 18:06:18', '413fede9-8c2c-40aa-b36e-c3849ca27b1a', '2023-06-05 18:06:18', '2023-06-05 18:06:18'),
-- (15, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\": \"mysql\"}', '2023-06-05 18:06:18', '413fede9-8c2c-40aa-b36e-c3849ca27b1a', '2023-06-05 18:06:18', '2023-06-05 18:06:18'),
-- (16, 'UsedDiskSpace', 'Used Disk Space', 'failed', 'The disk is almost full (92% used).', '92%', '{\"disk_space_used_percentage\": 92}', '2023-06-05 18:06:18', '413fede9-8c2c-40aa-b36e-c3849ca27b1a', '2023-06-05 18:06:18', '2023-06-05 18:06:18');
-- COMMIT;

