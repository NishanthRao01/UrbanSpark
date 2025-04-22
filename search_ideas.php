<?php
header('Content-Type: application/json');
require_once 'db/config.php';

try {
    $query = $_GET['q'] ?? '';
    
    if (empty($query)) {
        echo json_encode([]);
        exit;
    }

    // Search in titles and descriptions
    $sql = "SELECT id, title 
            FROM ideas 
            WHERE LOWER(title) LIKE LOWER(?) 
               OR LOWER(description) LIKE LOWER(?)
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%{$query}%";
    $stmt->execute([$searchTerm, $searchTerm]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search failed']);
}
?> 