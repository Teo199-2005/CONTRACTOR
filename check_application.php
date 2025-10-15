<?php
// Check existing application
$host = 'localhost';
$database = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Checking Next Year Applications ===\n\n";
    
    // Get all applications
    $stmt = $pdo->query("SELECT * FROM next_year_applications");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($applications)) {
        echo "No applications found.\n";
    } else {
        foreach ($applications as $app) {
            echo "Application ID: {$app['id']}\n";
            echo "Student ID: {$app['student_id']}\n";
            echo "Current Grade: {$app['current_grade_level']}\n";
            echo "Next Grade: {$app['next_grade_level']}\n";
            echo "GWA: {$app['gwa']}\n";
            echo "School Year: {$app['school_year']}\n";
            echo "Status: {$app['status']}\n";
            echo "Applied At: {$app['applied_at']}\n";
            echo "---\n";
        }
    }
    
    // Check if student can apply again
    $stmt = $pdo->prepare("SELECT * FROM next_year_applications WHERE student_id = ? AND school_year = ?");
    $stmt->execute([1, '2026-2027']);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        echo "\nStudent ID 1 already has an application for 2026-2027:\n";
        echo "Status: {$existing['status']}\n";
        echo "Applied: {$existing['applied_at']}\n";
        
        // Delete the existing application to allow testing
        echo "\nDeleting existing application to allow testing...\n";
        $stmt = $pdo->prepare("DELETE FROM next_year_applications WHERE id = ?");
        $stmt->execute([$existing['id']]);
        echo "Application deleted. Student can now apply again.\n";
    } else {
        echo "\nNo existing application found for student ID 1 and school year 2026-2027.\n";
        echo "Student can apply.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>