<?php
// Start session
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Include database configuration
require_once '../db/config.php';

// Handle idea status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $idea_id = intval($_POST['idea_id']);
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        $sql = "UPDATE ideas SET status = 'approved' WHERE id = ?";
    } elseif ($action === 'reject') {
        $sql = "UPDATE ideas SET status = 'rejected' WHERE id = ?";
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM ideas WHERE id = ?";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idea_id]);
    
    header('Location: dashboard.php');
    exit();
}

// Get all ideas
$sql = "SELECT * FROM ideas ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$ideas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UrbanSpark</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .idea-card {
            transition: all 0.3s ease;
        }

        .idea-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .action-button {
            transition: all 0.3s ease;
        }

        .action-button:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="../index.html" class="flex items-center">
                        <i class="fas fa-city text-blue-600 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-800">UrbanSpark</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <div class="flex space-x-4">
                <a href="../index.html" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-home mr-2"></i>
                    View Site
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <?php
            // Initialize stats with default values
            $stats = [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ];
            
            foreach ($ideas as $idea) {
                // Set default status if not set
                $status = isset($idea['status']) ? $idea['status'] : 'pending';
                if (!array_key_exists($status, $stats)) {
                    $status = 'pending';
                }
                $stats[$status]++;
            }
            ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Pending Ideas</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['pending']; ?></h3>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Approved Ideas</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['approved']; ?></h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Rejected Ideas</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['rejected']; ?></h3>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ideas List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($ideas as $idea): ?>
                            <tr class="idea-card">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($idea['title']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($idea['email']); ?></div>
                                    <?php if ($idea['file_path']): ?>
                                        <div class="mt-2">
                                            <?php if (strtolower(pathinfo($idea['file_path'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                                                <a href="../<?php echo htmlspecialchars($idea['file_path']); ?>" 
                                                   class="text-blue-600 hover:text-blue-800 flex items-center" target="_blank">
                                                    <i class="fas fa-file-pdf mr-2"></i>
                                                    View PDF
                                                </a>
                                            <?php else: ?>
                                                <a href="../<?php echo htmlspecialchars($idea['file_path']); ?>" 
                                                   class="text-blue-600 hover:text-blue-800 flex items-center" target="_blank">
                                                    <i class="fas fa-image mr-2"></i>
                                                    View Image
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($idea['category']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $status = isset($idea['status']) ? $idea['status'] : 'pending';
                                    $statusClass = 'status-' . $status;
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($idea['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <?php if ($idea['status'] === 'pending'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="action-button text-green-600 hover:text-green-800">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="action-button text-red-600 hover:text-red-800">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this idea?');">
                                            <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="action-button text-gray-600 hover:text-gray-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 