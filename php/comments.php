<?php
header('Content-Type: application/json');
require_once '../db/config.php';

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case 'GET':
            // Get comments for an idea
            $idea_id = isset($_GET['idea_id']) ? (int)$_GET['idea_id'] : 0;
            
            if ($idea_id <= 0) {
                throw new Exception('Invalid idea ID');
            }

            $sql = "SELECT id, user_name, comment_text, created_at, likes 
                    FROM comments 
                    WHERE idea_id = ? 
                    ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idea_id]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'comments' => $comments]);
            break;

        case 'POST':
            // Add new comment
            $data = json_decode(file_get_contents('php://input'), true);
            
            $idea_id = isset($data['idea_id']) ? (int)$data['idea_id'] : 0;
            $user_name = isset($data['user_name']) ? sanitize_input($data['user_name']) : '';
            $comment_text = isset($data['comment_text']) ? sanitize_input($data['comment_text']) : '';

            if ($idea_id <= 0 || empty($user_name) || empty($comment_text)) {
                throw new Exception('Missing required fields');
            }

            $sql = "INSERT INTO comments (idea_id, user_name, comment_text) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idea_id, $user_name, $comment_text]);

            // Return the newly created comment
            $comment_id = $pdo->lastInsertId();
            $sql = "SELECT id, user_name, comment_text, created_at, likes 
                    FROM comments 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'comment' => $comment]);
            break;

        case 'PUT':
            // Update comment likes
            $data = json_decode(file_get_contents('php://input'), true);
            $comment_id = isset($data['comment_id']) ? (int)$data['comment_id'] : 0;

            if ($comment_id <= 0) {
                throw new Exception('Invalid comment ID');
            }

            $sql = "UPDATE comments SET likes = likes + 1 WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$comment_id]);

            // Get updated likes count
            $sql = "SELECT likes FROM comments WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$comment_id]);
            $likes = $stmt->fetchColumn();

            echo json_encode(['success' => true, 'likes' => $likes]);
            break;

        default:
            throw new Exception('Unsupported request method');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 