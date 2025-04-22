<?php
require_once __DIR__ . '/../db/config.php';
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\SecureServer;
use React\Socket\Server;

class StatsWebSocket implements MessageComponentInterface {
    protected $clients;
    protected $pdo;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->pdo = $GLOBALS['pdo'];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->sendStats($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        if ($msg === 'fetch_stats') {
            $this->sendStats($from);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    protected function sendStats($conn) {
        try {
            // Get category distribution
            $categoryQuery = "SELECT category, COUNT(*) as count FROM ideas GROUP BY category";
            $stmt = $this->pdo->query($categoryQuery);
            $categories = $stmt->fetchAll();
            
            // Get status distribution
            $statusQuery = "SELECT status, COUNT(*) as count FROM ideas GROUP BY status";
            $stmt = $this->pdo->query($statusQuery);
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
            $stmt = $this->pdo->query($timeQuery);
            $timeStats = $stmt->fetchAll();
            
            // Get impact statistics
            $impactQuery = "SELECT 
                AVG(people_affected) as avg_people_affected,
                AVG(cost_savings) as avg_cost_savings,
                AVG(environmental_impact) as avg_environmental_impact
                FROM ideas";
            $stmt = $this->pdo->query($impactQuery);
            $impactStats = $stmt->fetch();
            
            $stats = [
                'categories' => $categories,
                'status' => $statusStats,
                'implementation_time' => $timeStats,
                'impact' => $impactStats,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $conn->send(json_encode($stats));
        } catch (\Exception $e) {
            $conn->send(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function broadcastStats() {
        foreach ($this->clients as $client) {
            $this->sendStats($client);
        }
    }
}

// Create event loop and socket
$loop = Factory::create();
$socket = new Server('0.0.0.0:8080', $loop);

$statsServer = new StatsWebSocket();
$server = new IoServer(
    new HttpServer(
        new WsServer(
            $statsServer
        )
    ),
    $socket,
    $loop
);

// Set up periodic database check
$loop->addPeriodicTimer(1, function() use ($statsServer) {
    $statsServer->broadcastStats();
});

echo "WebSocket server started on port 8080\n";
$loop->run(); 