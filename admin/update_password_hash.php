<?php
// Include database configuration
require_once '../db/config.php';

try {
    // Get the current admin record
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        // Hash the password
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        
        // Update the password
        $stmt = $pdo->prepare("UPDATE admins SET password = :password WHERE username = 'admin'");
        $stmt->execute([':password' => $hashedPassword]);
        
        echo "Password has been updated with a proper hash.<br>";
        echo "You can now log in with:<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Admin user not found.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 