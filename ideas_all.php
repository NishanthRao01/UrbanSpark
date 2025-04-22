<?php
// Start session
session_start();

// Include database configuration
require_once __DIR__ . '/db/config.php';

// Get filter and sort parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build SQL query
$sql = "SELECT * FROM ideas";
if ($category) {
    $sql .= " WHERE category = ?";
}
switch ($sort) {
    case 'likes':
        $sql .= " ORDER BY likes DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    default:
        $sql .= " ORDER BY created_at DESC";
}

// Prepare and execute query
$stmt = $conn->prepare($sql);
if ($category) {
    $stmt->bind_param("s", $category);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas Gallery - UrbanSpark</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .idea-card {
            transition: all 0.3s ease;
            transform: translateY(0);
        }

        .idea-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .like-button {
            transition: all 0.3s ease;
        }

        .like-button:hover {
            transform: scale(1.1);
        }

        .like-button.liked {
            color: #ef4444;
        }

        .filter-button {
            transition: all 0.3s ease;
        }

        .filter-button.active {
            background-color: #3b82f6;
            color: white;
        }

        .success-message {
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
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
                    <a href="../index.html" class="text-gray-700 hover:text-blue-600 px-3 py-2">Home</a>
                    <a href="../submit.html" class="text-gray-700 hover:text-blue-600 px-3 py-2">Submit Idea</a>
                    <a href="ideas_all.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Ideas</a>
                    <a href="../stats.html" class="text-gray-700 hover:text-blue-600 px-3 py-2">Stats</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Success Message -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message bg-green-500 text-white p-4 text-center">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Filters and Sort -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div class="flex space-x-2 mb-4 md:mb-0 overflow-x-auto">
                <a href="?category=&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo !$category ? 'active' : ''; ?>">
                    All
                </a>
                <a href="?category=Infrastructure&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Infrastructure' ? 'active' : ''; ?>">
                    Infrastructure
                </a>
                <a href="?category=Environment&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Environment' ? 'active' : ''; ?>">
                    Environment
                </a>
                <a href="?category=Transportation&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Transportation' ? 'active' : ''; ?>">
                    Transportation
                </a>
                <a href="?category=Technology&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Technology' ? 'active' : ''; ?>">
                    Technology
                </a>
                <a href="?category=Education&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Education' ? 'active' : ''; ?>">
                    Education
                </a>
                <a href="?category=Healthcare&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Healthcare' ? 'active' : ''; ?>">
                    Healthcare
                </a>
                <a href="?category=Other&sort=<?php echo $sort; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Other' ? 'active' : ''; ?>">
                    Other
                </a>
            </div>
            <div class="flex space-x-2">
                <a href="?category=<?php echo $category; ?>&sort=newest" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $sort === 'newest' ? 'active' : ''; ?>">
                    Newest
                </a>
                <a href="?category=<?php echo $category; ?>&sort=oldest" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $sort === 'oldest' ? 'active' : ''; ?>">
                    Oldest
                </a>
                <a href="?category=<?php echo $category; ?>&sort=likes" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $sort === 'likes' ? 'active' : ''; ?>">
                    Most Liked
                </a>
            </div>
        </div>

        <!-- Ideas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while ($idea = $result->fetch_assoc()): ?>
                <div class="idea-card bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            <?php echo htmlspecialchars($idea['category']); ?>
                        </span>
                        <span class="status-badge status-<?php echo $idea['status']; ?>">
                            <?php echo ucfirst($idea['status']); ?>
                        </span>
                    </div>
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($idea['title']); ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($idea['description']); ?></p>
                    
                    <?php if ($idea['file_path']): ?>
                        <div class="mb-4">
                            <?php if (strtolower(pathinfo($idea['file_path'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                                <a href="uploads/<?php echo htmlspecialchars($idea['file_path']); ?>" 
                                   class="text-blue-600 hover:text-blue-800 flex items-center" target="_blank">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    View PDF
                                </a>
                            <?php else: ?>
                                <img src="uploads/<?php echo htmlspecialchars($idea['file_path']); ?>" 
                                     alt="Idea image" class="w-full rounded-lg">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center">
                        <button class="like-button flex items-center space-x-2 text-gray-600 hover:text-red-500"
                                data-idea-id="<?php echo $idea['id']; ?>"
                                onclick="toggleLike(this)">
                            <i class="fas fa-heart"></i>
                            <span class="like-count"><?php echo $idea['likes']; ?></span>
                        </button>
                        <a href="mailto:<?php echo htmlspecialchars($idea['email']); ?>" 
                           class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p>&copy; 2024 UrbanSpark. All rights reserved.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-blue-400"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-blue-400"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Like functionality
        function toggleLike(button) {
            const ideaId = button.dataset.ideaId;
            const likeCount = button.querySelector('.like-count');
            
            fetch('php/like_idea.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `idea_id=${ideaId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCount.textContent = data.likes;
                    button.classList.toggle('liked');
                }
            });
        }
    </script>
</body>
</html> 