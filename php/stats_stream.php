<?php
require_once __DIR__ . '/../db/config.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

while (true) {
    try {
        // Get category distribution
        $categoryQuery = "SELECT category, COUNT(*) as count FROM ideas GROUP BY category";
        $stmt = $pdo->query($categoryQuery);
        $categories = $stmt->fetchAll();
        
        // Get status distribution
        $statusQuery = "SELECT status, COUNT(*) as count FROM ideas GROUP BY status";
        $stmt = $pdo->query($statusQuery);
        $statusStats = $stmt->fetchAll();
        
        // Get implementation time distribution
        $timeQuery = "SELECT 
            CASE 
                WHEN implementation_time <= 3 THEN 'Short Term (1-3 months)'
                WHEN implementation_time <= 12 THEN 'Medium Term (4-12 months)'
                ELSE 'Long Term (>12 months)'
            END as time_range,
            COUNT(*) as count
            FROM ideas 
            GROUP BY 
            CASE 
                WHEN implementation_time <= 3 THEN 'Short Term (1-3 months)'
                WHEN implementation_time <= 12 THEN 'Medium Term (4-12 months)'
                ELSE 'Long Term (>12 months)'
            END";
        $stmt = $pdo->query($timeQuery);
        $timeStats = $stmt->fetchAll();
        
        // Get impact statistics
        $impactQuery = "SELECT 
            AVG(people_affected) as avg_people_affected,
            AVG(cost_savings) as avg_cost_savings,
            AVG(environmental_impact) as avg_environmental_impact
            FROM ideas";
        $stmt = $pdo->query($impactQuery);
        $impactStats = $stmt->fetch();
        
        $stats = [
            'categories' => $categories,
            'status' => $statusStats,
            'implementation_time' => $timeStats,
            'impact' => $impactStats,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo "data: " . json_encode($stats) . "\n\n";
        ob_flush();
        flush();
        
    } catch (Exception $e) {
        echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
        ob_flush();
        flush();
    }
    
    // Wait for 10 seconds before next update
    sleep(10);
} 