<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding basic analytics data for all teachers...\n\n";
    
    // Get all teachers with sections
    $stmt = $pdo->query("
        SELECT t.id as teacher_id, t.user_id, t.first_name, t.last_name, 
               s.id as section_id, s.section_name, s.grade_level
        FROM teachers t
        JOIN sections s ON t.id = s.adviser_id
        WHERE s.is_active = 1
    ");
    $teacherSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($teacherSections as $ts) {
        echo "Processing {$ts['first_name']} {$ts['last_name']} - {$ts['section_name']}\n";
        
        // Add 5-8 students to each section
        $studentCount = rand(5, 8);
        $studentNames = [
            ['Juan', 'Cruz'], ['Maria', 'Santos'], ['Pedro', 'Garcia'], 
            ['Ana', 'Lopez'], ['Jose', 'Martinez'], ['Carmen', 'Rodriguez'],
            ['Miguel', 'Hernandez'], ['Sofia', 'Gonzalez']
        ];
        
        for ($i = 0; $i < $studentCount; $i++) {
            $name = $studentNames[$i % count($studentNames)];
            $lrn = '1234' . $ts['grade_level'] . str_pad($ts['section_id'], 2, '0', STR_PAD_LEFT) . str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            
            // Check if student exists
            $stmt = $pdo->prepare("SELECT id FROM students WHERE lrn = ?");
            $stmt->execute([$lrn]);
            $existingStudent = $stmt->fetch();
            
            if (!$existingStudent) {
                $stmt = $pdo->prepare("
                    INSERT INTO students (
                        lrn, first_name, last_name, grade_level, gender, 
                        section_id, enrollment_status, school_year, 
                        date_of_birth, address, contact_number, email, 
                        created_at, updated_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, 'enrolled', '2024-2025', 
                        '2010-01-01', '123 Student St', '09123456789', ?, 
                        NOW(), NOW()
                    )
                ");
                
                $gender = ($i % 2 == 0) ? 'Male' : 'Female';
                $email = strtolower($name[0] . '.' . $name[1] . $i) . '@student.lphs.edu';
                
                $stmt->execute([
                    $lrn, $name[0], $name[1], $ts['grade_level'], 
                    $gender, $ts['section_id'], $email
                ]);
            }
        }
        
        // Update section enrollment count
        $stmt = $pdo->prepare("UPDATE sections SET current_enrollment = ? WHERE id = ?");
        $stmt->execute([$studentCount, $ts['section_id']]);
        
        echo "  ✓ Added $studentCount students\n";
    }
    
    echo "\n✅ Basic analytics data added for all teachers!\n";
    echo "All demo teacher accounts should now show analytics data.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}