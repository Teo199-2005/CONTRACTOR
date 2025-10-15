<?php
// Simple migration script to move existing enrollment documents
$oldBasePath = 'writable/uploads/enrollment/';
$newBasePath = 'public/uploads/enrollment_documents/';

// Create new directory if it doesn't exist
if (!is_dir($newBasePath)) {
    mkdir($newBasePath, 0755, true);
}

$moved = 0;
$errors = 0;

// Scan old directory structure
if (is_dir($oldBasePath)) {
    $studentDirs = scandir($oldBasePath);
    
    foreach ($studentDirs as $studentDir) {
        if ($studentDir === '.' || $studentDir === '..' || $studentDir === 'index.html') {
            continue;
        }
        
        $studentPath = $oldBasePath . $studentDir;
        if (is_dir($studentPath)) {
            $files = scandir($studentPath);
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                
                $oldFilePath = $studentPath . '/' . $file;
                $newFilePath = $newBasePath . $file;
                
                if (file_exists($oldFilePath) && !file_exists($newFilePath)) {
                    if (copy($oldFilePath, $newFilePath)) {
                        echo "Moved: {$oldFilePath} -> {$newFilePath}\n";
                        $moved++;
                    } else {
                        echo "Error moving: {$oldFilePath}\n";
                        $errors++;
                    }
                }
            }
        }
    }
}

echo "\nMigration complete!\n";
echo "Files moved: {$moved}\n";
echo "Errors: {$errors}\n";

// Now update database paths
echo "\nUpdating database paths...\n";

// Database connection
$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update file paths in database
    $stmt = $pdo->prepare("UPDATE enrollment_documents SET file_path = ? WHERE file_path LIKE 'uploads/enrollment/%'");
    
    $documents = $pdo->query("SELECT id, file_path FROM enrollment_documents WHERE file_path LIKE 'uploads/enrollment/%'")->fetchAll();
    
    $updated = 0;
    foreach ($documents as $doc) {
        $newPath = basename($doc['file_path']);
        $stmt->execute([$newPath]);
        $updated++;
        echo "Updated DB path for document ID {$doc['id']}: {$doc['file_path']} -> {$newPath}\n";
    }
    
    echo "\nDatabase update complete! Updated {$updated} records.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>