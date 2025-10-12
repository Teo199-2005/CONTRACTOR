<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    $teacherId = 11; // Demo Teacher ID
    
    echo "TESTING STUDENT QUERY FOR TEACHER ID: $teacherId\n\n";
    
    // Test the exact query from the controller
    $query = "
        SELECT students.*, sections.section_name
        FROM students 
        LEFT JOIN sections ON sections.id = students.section_id
        WHERE sections.adviser_id = ? 
        AND students.enrollment_status = 'enrolled'
        ORDER BY students.last_name ASC
    ";
    
    $result = $db->prepare($query);
    $result->execute([$teacherId]);
    $students = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Query result: " . count($students) . " students found\n\n";
    
    if (count($students) > 0) {
        foreach ($students as $student) {
            echo "Student: " . $student['first_name'] . " " . $student['last_name'] . 
                 " (ID: " . $student['id'] . ", Section: " . ($student['section_name'] ?? 'NULL') . ")\n";
        }
    } else {
        echo "No students found. Let's debug...\n\n";
        
        // Check sections assigned to this teacher
        echo "SECTIONS ASSIGNED TO TEACHER $teacherId:\n";
        $sectionQuery = $db->prepare("SELECT id, section_name, grade_level FROM sections WHERE adviser_id = ?");
        $sectionQuery->execute([$teacherId]);
        $sections = $sectionQuery->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($sections as $section) {
            echo "Section: " . $section['section_name'] . " (ID: " . $section['id'] . ")\n";
            
            // Check students in this section
            $studentQuery = $db->prepare("SELECT id, first_name, last_name, enrollment_status FROM students WHERE section_id = ?");
            $studentQuery->execute([$section['id']]);
            $sectionStudents = $studentQuery->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  Students in section: " . count($sectionStudents) . "\n";
            foreach ($sectionStudents as $student) {
                echo "    - " . $student['first_name'] . " " . $student['last_name'] . 
                     " (Status: " . $student['enrollment_status'] . ")\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>