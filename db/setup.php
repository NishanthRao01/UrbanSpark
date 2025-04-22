<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config.php';

try {
    // Read the schema file
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\nDatabase setup completed successfully!";
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} 