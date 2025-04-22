<?php
// Start session
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/../db/config.php';

// Direct debug output
echo "<pre>Debug Mode\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST Data received:\n";
    print_r($_POST);
    echo "\nFiles Data received:\n";
    print_r($_FILES);

    try {
        // Basic data collection
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? '';
        $email = $_POST['email'] ?? '';
        
        echo "\nProcessing submission with:\n";
        echo "Title: $title\n";
        echo "Category: $category\n";
        echo "Email: $email\n";

        // Basic validation
        if (empty($title) || empty($description) || empty($category) || empty($email)) {
            throw new Exception("All fields are required");
        }

        // Get impact metrics
        $people_affected = (int)($_POST['people_affected'] ?? 0);
        $cost_savings = (int)($_POST['cost_savings'] ?? 0);
        $environmental_impact = (int)($_POST['environmental_impact'] ?? 0);
        $implementation_time = (int)($_POST['implementation_time'] ?? 1);

        echo "\nImpact metrics:\n";
        echo "People: $people_affected\n";
        echo "Cost: $cost_savings\n";
        echo "Environmental: $environmental_impact\n";
        echo "Time: $implementation_time\n";

        // File handling
        $file_path = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
                $file_path = 'uploads/' . $file_name;
                echo "\nFile uploaded: $file_path\n";
            }
        }

        echo "\nAttempting database connection...\n";
        
        // Verify database connection
        if (!isset($pdo)) {
            throw new Exception("No database connection available");
        }

        // Simple insert query
        $sql = "INSERT INTO ideas (
            title, description, category, email, file_path,
            people_affected, cost_savings, environmental_impact, implementation_time
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )";

        echo "Preparing SQL statement...\n";
        $stmt = $pdo->prepare($sql);
        
        echo "Executing with parameters...\n";
        $result = $stmt->execute([
            $title, $description, $category, $email, $file_path,
            $people_affected, $cost_savings, $environmental_impact, $implementation_time
        ]);

        if ($result) {
            $id = $pdo->lastInsertId();
            echo "\nSuccess! Idea inserted with ID: $id\n";
            $_SESSION['success'] = true;
            $_SESSION['message'] = "Your idea has been submitted successfully! (ID: $id)";
        } else {
            throw new Exception("Failed to insert data");
        }

        echo "\nRedirecting to form page...</pre>";
        header("Location: ../submit.php");
        exit();

    } catch (Exception $e) {
        echo "\nError occurred: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "</pre>";
        $_SESSION['success'] = false;
        $_SESSION['message'] = "Error: " . $e->getMessage();
        // Don't redirect in debug mode
        // header("Location: ../submit.php");
        // exit();
    }
} else {
    echo "No POST data received</pre>";
    $_SESSION['success'] = false;
    $_SESSION['message'] = "Invalid request method";
    header("Location: ../submit.php");
    exit();
}
?> 