<?php
// Simple script to create system_settings table
$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS `system_settings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `setting_key` varchar(100) NOT NULL,
      `setting_value` text NOT NULL,
      `description` varchar(255) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "Table created successfully\n";
    
    // Insert default quarter setting
    $sql = "INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
    ('current_quarter', '2', 'Current active quarter for grading')
    ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)";
    
    $pdo->exec($sql);
    echo "Default quarter setting inserted\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>