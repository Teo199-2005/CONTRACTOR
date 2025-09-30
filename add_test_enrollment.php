<?php
// Add test enrollment data
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lphs_sms';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Adding Test Enrollment Data</h2>";
    
    // Check if we already have pending enrollments
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM students WHERE enrollment_status = 'pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "<p>✅ Found {$result['count']} pending enrollments already in the system.</p>";
    } else {
        echo "<p>Adding test enrollment data...</p>";
        
        // Add a test pending enrollment
        $stmt = $pdo->prepare("
            INSERT INTO students (
                first_name, last_name, middle_name, gender, date_of_birth, 
                place_of_birth, nationality, religion, email, contact_number, 
                address, emergency_contact_name, emergency_contact_number, 
                emergency_contact_relationship, grade_level, school_year, 
                enrollment_status, created_at, updated_at
            ) VALUES (
                'Maria', 'Santos', 'Cruz', 'Female', '2008-05-15',
                'Tagbilaran City, Bohol', 'Filipino', 'Catholic', 'maria.santos@email.com', '09171234567',
                'Purok 1, Barangay Poblacion, Panglao, Bohol', 'Rosa Santos', '09181234567',
                'Mother', 9, '2024-2025', 'pending', NOW(), NOW()
            )
        ");
        $stmt->execute();
        
        $stmt = $pdo->prepare("
            INSERT INTO students (
                first_name, last_name, middle_name, gender, date_of_birth, 
                place_of_birth, nationality, religion, email, contact_number, 
                address, emergency_contact_name, emergency_contact_number, 
                emergency_contact_relationship, grade_level, school_year, 
                enrollment_status, created_at, updated_at
            ) VALUES (
                'Juan', 'Dela Cruz', 'Garcia', 'Male', '2009-03-20',
                'Panglao, Bohol', 'Filipino', 'Catholic', 'juan.delacruz@email.com', '09171234568',
                'Purok 2, Barangay Lourdes, Panglao, Bohol', 'Pedro Dela Cruz', '09181234568',
                'Father', 8, '2024-2025', 'pending', NOW(), NOW()
            )
        ");
        $stmt->execute();
        
        echo "<p>✅ Added 2 test pending enrollments.</p>";
    }
    
    // Show current pending enrollments
    echo "<h3>Current Pending Enrollments:</h3>";
    $stmt = $pdo->query("
        SELECT id, first_name, last_name, grade_level, enrollment_status, created_at 
        FROM students 
        WHERE enrollment_status = 'pending' 
        ORDER BY created_at DESC
    ");
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($pending) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>
                <th>ID</th>
                <th>Name</th>
                <th>Grade</th>
                <th>Status</th>
                <th>Applied</th>
              </tr>";
        
        foreach ($pending as $student) {
            echo "<tr>";
            echo "<td>{$student['id']}</td>";
            echo "<td>{$student['first_name']} {$student['last_name']}</td>";
            echo "<td>Grade {$student['grade_level']}</td>";
            echo "<td style='color: orange; font-weight: bold;'>{$student['enrollment_status']}</td>";
            echo "<td>{$student['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No pending enrollments found.</p>";
    }
    
    echo "<h3>Test the Enrollments Page</h3>";
    echo "<p><a href='http://localhost:8080/admin/enrollments' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Manage Enrollments</a></p>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Database Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>
