<?php
// Include database configuration
require_once '../db/config.php';

try {
    // Get the admin record
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        echo "<h2>Admin Record Details:</h2>";
        echo "Username: " . htmlspecialchars($admin['username']) . "<br>";
        echo "Password Hash: " . htmlspecialchars($admin['password']) . "<br>";
        echo "Password Length: " . strlen($admin['password']) . "<br>";
        
        // Test password verification
        $testPassword = 'admin123';
        $isValid = password_verify($testPassword, $admin['password']);
        echo "Password Verification Test: " . ($isValid ? "SUCCESS" : "FAILED") . "<br>";
        
        // If verification failed, let's try to fix it
        if (!$isValid) {
            echo "<h3>Attempting to fix password hash...</h3>";
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password = :password WHERE username = 'admin'");
            $stmt->execute([':password' => $newHash]);
            echo "New password hash set: " . $newHash . "<br>";
            echo "Please try logging in again with username: admin and password: admin123";
        }
    } else {
        echo "Admin user not found in database.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 