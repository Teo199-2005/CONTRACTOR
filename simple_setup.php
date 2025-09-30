<?php
// Simple direct database setup - bypasses CodeIgniter issues
echo "LPHS SMS Simple Database Setup\n";
echo "==============================\n\n";

// Database configuration - update these values
$host = 'localhost';
$username = 'root';  // Change if different
$password = '';      // Change if different
$database = 'lphs_sms'; // Change if different

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected successfully!\n\n";
    
    // Check existing tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "❌ No tables found! Creating basic tables...\n";
        
        // Create basic tables
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS sections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                section_name VARCHAR(100) NOT NULL,
                grade_level INT NOT NULL,
                school_year VARCHAR(20) NOT NULL,
                max_capacity INT DEFAULT 40,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS subjects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                subject_code VARCHAR(20) NOT NULL,
                subject_name VARCHAR(100) NOT NULL,
                grade_level INT NOT NULL,
                units DECIMAL(3,1) DEFAULT 1.0,
                is_core BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS announcements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                body TEXT NOT NULL,
                target_roles VARCHAR(100) DEFAULT 'all',
                published_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        echo "✅ Basic tables created!\n\n";
    } else {
        echo "✅ Found " . count($tables) . " existing tables:\n";
        foreach ($tables as $table) {
            echo "  - " . $table . "\n";
        }
        echo "\n";
    }
    
    // Add sample data
    echo "Adding sample data...\n";
    
    // Add sections
    $sections = [
        ['St. Francis', 7, '2024-2025'],
        ['St. Clare', 7, '2024-2025'],
        ['St. Anthony', 7, '2024-2025'],
        ['St. Joseph', 8, '2024-2025'],
        ['St. Mary', 8, '2024-2025'],
        ['St. Peter', 8, '2024-2025']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO sections (section_name, grade_level, school_year) VALUES (?, ?, ?)");
    foreach ($sections as $section) {
        $stmt->execute($section);
    }
    echo "✅ Added " . count($sections) . " sections\n";
    
    // Add subjects
    $subjects = [
        ['ENG7', 'English 7', 7],
        ['MATH7', 'Mathematics 7', 7],
        ['SCI7', 'Science 7', 7],
        ['ENG8', 'English 8', 8],
        ['MATH8', 'Mathematics 8', 8],
        ['SCI8', 'Science 8', 8]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO subjects (subject_code, subject_name, grade_level) VALUES (?, ?, ?)");
    foreach ($subjects as $subject) {
        $stmt->execute($subject);
    }
    echo "✅ Added " . count($subjects) . " subjects\n";
    
    // Add announcements
    $announcements = [
        ['Welcome to School Year 2024-2025', 'welcome-sy-2024-2025', 'We welcome all students to the new school year!', 'all'],
        ['Enrollment Period Extended', 'enrollment-extended', 'Enrollment extended until June 30, 2024.', 'all'],
        ['Parent-Teacher Conference', 'ptc-schedule', 'Parent-Teacher conferences on October 15-16, 2024.', 'parent,teacher']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO announcements (title, slug, body, target_roles) VALUES (?, ?, ?, ?)");
    foreach ($announcements as $announcement) {
        $stmt->execute($announcement);
    }
    echo "✅ Added " . count($announcements) . " announcements\n";
    
    // Show final counts
    echo "\nFinal Database Status:\n";
    echo "======================\n";
    
    $count = $pdo->query("SELECT COUNT(*) FROM sections")->fetchColumn();
    echo "Sections: " . $count . "\n";
    
    $count = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
    echo "Subjects: " . $count . "\n";
    
    $count = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
    echo "Announcements: " . $count . "\n";
    
    echo "\n✅ Setup completed successfully!\n";
    echo "Your admin dashboard should now show data instead of 0.\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "\nPlease check your database configuration:\n";
    echo "- Host: $host\n";
    echo "- Database: $database\n";
    echo "- Username: $username\n";
    echo "- Password: [hidden]\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}



