<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    echo "CHECKING TEACHER-USER CONNECTION:\n";
    
    // Check users table for demo teacher
    $result = $db->query("SELECT id, email FROM users WHERE email LIKE '%demo%'");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "User ID: " . $row['id'] . ", Email: " . $row['email'] . "\n";
        
        // Check if this user has a teacher record
        $teacherCheck = $db->prepare("SELECT id, user_id, first_name, last_name FROM teachers WHERE user_id = ?");
        $teacherCheck->execute([$row['id']]);
        $teacher = $teacherCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($teacher) {
            echo "  -> Teacher Record: ID " . $teacher['id'] . ", Name: " . $teacher['first_name'] . " " . $teacher['last_name'] . "\n";
            
            // Check sections assigned to this teacher
            $sectionCheck = $db->prepare("SELECT id, section_name, grade_level FROM sections WHERE adviser_id = ?");
            $sectionCheck->execute([$teacher['id']]);
            $sections = $sectionCheck->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  -> Assigned Sections: " . count($sections) . "\n";
            foreach ($sections as $section) {
                echo "     - " . $section['section_name'] . " (Grade " . $section['grade_level'] . ")\n";
                
                // Check students in this section
                $studentCheck = $db->prepare("SELECT COUNT(*) as count FROM students WHERE section_id = ? AND enrollment_status = 'enrolled'");
                $studentCheck->execute([$section['id']]);
                $studentCount = $studentCheck->fetch(PDO::FETCH_ASSOC);
                echo "       Students: " . $studentCount['count'] . "\n";
            }
        } else {
            echo "  -> No teacher record found\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>