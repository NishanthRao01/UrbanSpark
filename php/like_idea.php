<?php
// Start session
session_start();

// Include database configuration
require_once '../db/config.php';

// Initialize response
$response = ['success' => false, 'likes' => 0];

// Check if idea_id is provided
if (isset($_POST['idea_id'])) {
    try {
        $idea_id = intval($_POST['idea_id']);
        
        // Get current likes
        $stmt = $pdo->prepare("SELECT likes FROM ideas WHERE id = :id");
        $stmt->execute([':id' => $idea_id]);
        $idea = $stmt->fetch();
        
        if ($idea) {
            $new_likes = $idea['likes'] + 1;
            
            // Update likes count
            $update_stmt = $pdo->prepare("UPDATE ideas SET likes = :likes WHERE id = :id");
            $update_stmt->execute([
                ':likes' => $new_likes,
                ':id' => $idea_id
            ]);
            
            $response['success'] = true;
            $response['likes'] = $new_likes;
        }
    } catch (PDOException $e) {
        $response['error'] = "Database error: " . $e->getMessage();
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 