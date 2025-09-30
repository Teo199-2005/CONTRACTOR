-- Create password_reset_requests table
CREATE TABLE IF NOT EXISTS `password_reset_requests` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL,
    `email` varchar(255) NOT NULL,
    `token` varchar(64) NOT NULL UNIQUE,
    `expires_at` datetime NOT NULL,
    `status` enum('pending','approved','rejected','used','expired') NOT NULL DEFAULT 'pending',
    `approved_by` int(11) unsigned NULL,
    `approved_at` datetime NULL,
    `used_at` datetime NULL,
    `admin_notes` text NULL,
    `created_at` datetime NULL,
    `updated_at` datetime NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_token` (`token`),
    KEY `idx_status` (`status`),
    KEY `idx_expires_at` (`expires_at`),
    CONSTRAINT `fk_password_reset_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_password_reset_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
