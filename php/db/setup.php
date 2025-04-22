<?php
// Include database configuration
require_once 'config.php';

try {
    // Create admins table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Admins table created successfully.<br>";

    // Check if default admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetchColumn();

    if (!$adminExists) {
        // Create default admin account
        $username = 'admin';
        $password = 'admin123'; // Default password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword
        ]);

        echo "Default admin account created successfully.<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Please change these credentials after first login for security.<br>";
    } else {
        echo "Default admin account already exists.<br>";
    }

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 