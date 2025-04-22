<?php
// Start session
session_start();

// Include database configuration
require_once __DIR__ . '/php/db/config.php';

// Get filter and sort parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build SQL query
$sql = "SELECT * FROM ideas WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}
if ($status !== 'all') {
    $sql .= " AND status = :status";
    $params[':status'] = $status;
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
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ideas = $stmt->fetchAll();
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
    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- Custom JS -->
    <script src="js/particles.js"></script>
    <script src="js/animations.js"></script>
    <style>
        /* Modern gradient background */
        body {
            background: linear-gradient(135deg, #f6f8ff 0%, #f1f5ff 100%);
            min-height: 100vh;
        }

        /* Glassmorphism effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        /* Particle canvas */
        #particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: -1;
            width: 100%;
            height: 100vh;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Advanced card hover effects */
        .idea-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0) scale(1);
            transform-origin: center;
        }

        .idea-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Magnetic button effect */
        .magnetic-button {
            position: relative;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        /* Neon glow effect */
        .neon-glow {
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5),
                       0 0 20px rgba(59, 130, 246, 0.3),
                       0 0 30px rgba(59, 130, 246, 0.1);
        }

        /* Advanced animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Comment animation */
        .comment-appear {
            animation: commentSlideIn 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes commentSlideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Like button animation */
        .like-button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .like-button:hover {
            transform: scale(1.2);
        }

        .like-button.liked {
            animation: likeEffect 0.5s cubic-bezier(0.17, 0.89, 0.32, 1.49);
        }

        @keyframes likeEffect {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.4); }
        }

        /* Fancy scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #3b82f6, #60a5fa);
            border-radius: 5px;
        }

        /* Text gradient effect */
        .text-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
<body>
    <!-- Particle background -->
    <canvas id="particle-canvas"></canvas>

    <!-- Navigation with glass effect -->
    <nav class="glass-card sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.html" class="flex items-center group">
                        <i class="fas fa-city text-blue-600 text-3xl mr-2 float-animation"></i>
                        <span class="text-xl font-bold text-gradient">UrbanSpark</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.html" class="magnetic-button text-gray-700 hover:text-blue-600 px-3 py-2">Home</a>
                    <a href="submit.php" class="magnetic-button text-gray-700 hover:text-blue-600 px-3 py-2">Submit Idea</a>
                    <a href="ideas.php" class="magnetic-button text-gray-700 hover:text-blue-600 px-3 py-2">Ideas</a>
                    <a href="stats.html" class="magnetic-button text-gray-700 hover:text-blue-600 px-3 py-2">Stats</a>
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
                <a href="?category=&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo !$category ? 'active' : ''; ?>">
                    All
                </a>
                <a href="?category=Infrastructure&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Infrastructure' ? 'active' : ''; ?>">
                    Infrastructure
                </a>
                <a href="?category=Environment&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Environment' ? 'active' : ''; ?>">
                    Environment
                </a>
                <a href="?category=Transportation&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Transportation' ? 'active' : ''; ?>">
                    Transportation
                </a>
                <a href="?category=Technology&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Technology' ? 'active' : ''; ?>">
                    Technology
                </a>
                <a href="?category=Education&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Education' ? 'active' : ''; ?>">
                    Education
                </a>
                <a href="?category=Healthcare&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Healthcare' ? 'active' : ''; ?>">
                    Healthcare
                </a>
                <a href="?category=Other&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $category === 'Other' ? 'active' : ''; ?>">
                    Other
                </a>
            </div>
            <div class="flex space-x-2">
                <a href="?category=<?php echo $category; ?>&sort=newest&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $sort === 'newest' ? 'active' : ''; ?>">
                    Newest
                </a>
                <a href="?category=<?php echo $category; ?>&sort=oldest&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $sort === 'oldest' ? 'active' : ''; ?>">
                    Oldest
                </a>
                <a href="?category=<?php echo $category; ?>&sort=likes&status=<?php echo $status; ?>" 
                   class="filter-button px-4 py-2 rounded-full <?php echo $sort === 'likes' ? 'active' : ''; ?>">
                    Most Liked
                </a>
            </div>
        </div>

        <!-- Ideas Grid -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            <?php foreach ($ideas as $idea): ?>
                <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden idea-card" data-idea-id="<?php echo $idea['id']; ?>">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                <?php echo htmlspecialchars($idea['category']); ?>
                            </span>
                            <span class="text-gray-500 text-sm">
                                <?php echo date('M d, Y', strtotime($idea['created_at'])); ?>
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($idea['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo nl2br(htmlspecialchars($idea['description'])); ?></p>
                        
                        <?php if (!empty($idea['file_path'])): ?>
                            <div class="mb-4">
                                <a href="<?php echo htmlspecialchars($idea['file_path']); ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                    <i class="fas <?php echo pathinfo($idea['file_path'], PATHINFO_EXTENSION) === 'pdf' ? 'fa-file-pdf' : 'fa-file-image'; ?> mr-2"></i>
                                    View Attachment
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php 
                            $tags = explode(',', $idea['tags']);
                            foreach ($tags as $tag):
                                if (trim($tag)):
                            ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                    #<?php echo htmlspecialchars(trim($tag)); ?>
                                </span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>

                        <!-- Comments Section -->
                        <div class="mt-8 border-t pt-6">
                            <h4 class="text-xl font-semibold mb-4">Comments</h4>
                            
                            <!-- Comment Form -->
                            <form class="comment-form mb-6">
                                <div class="flex gap-4 mb-4">
                                    <div class="flex-1">
                                        <input type="text" 
                                               class="comment-name w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Your name" 
                                               required>
                                    </div>
                                    <button type="submit" 
                                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Comment
                                    </button>
                                </div>
                                <textarea class="comment-text w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                          rows="3" 
                                          placeholder="Share your thoughts..."
                                          required></textarea>
                            </form>

                            <!-- Comments List -->
                            <div class="comments-list space-y-4">
                                <!-- Comments will be loaded here -->
                            </div>

                            <!-- Loading Spinner -->
                            <div class="loading-spinner hidden flex justify-center py-4">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Load comments for each idea
        document.querySelectorAll('.idea-card').forEach(card => {
            loadComments(card.dataset.ideaId);
        });

        // Handle comment form submissions
        document.querySelectorAll('.comment-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const ideaCard = this.closest('.idea-card');
                const ideaId = ideaCard.dataset.ideaId;
                const nameInput = this.querySelector('.comment-name');
                const textArea = this.querySelector('.comment-text');

                submitComment(ideaId, nameInput.value, textArea.value)
                    .then(() => {
                        // Clear form
                        nameInput.value = '';
                        textArea.value = '';
                        // Reload comments
                        loadComments(ideaId);
                    })
                    .catch(error => {
                        console.error('Error submitting comment:', error);
                        alert('Failed to submit comment. Please try again.');
                    });
            });
        });

        // Initialize particle background
        const canvas = document.getElementById('particle-canvas');
        new ParticleNetwork(canvas);
    });

    function loadComments(ideaId) {
        const card = document.querySelector(`.idea-card[data-idea-id="${ideaId}"]`);
        const commentsList = card.querySelector('.comments-list');
        const spinner = card.querySelector('.loading-spinner');

        spinner.classList.remove('hidden');
        commentsList.innerHTML = '';

        fetch(`php/comments.php?idea_id=${ideaId}`)
            .then(response => response.json())
            .then(data => {
                spinner.classList.add('hidden');
                if (data.success && data.comments.length > 0) {
                    commentsList.innerHTML = data.comments.map(comment => `
                        <div class="comment bg-gray-50 rounded-lg p-4 transition duration-200 hover:bg-gray-100">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                                        ${comment.user_name.charAt(0).toUpperCase()}
                                    </div>
                                    <div class="ml-3">
                                        <h5 class="font-semibold">${comment.user_name}</h5>
                                        <span class="text-sm text-gray-500">
                                            ${new Date(comment.created_at).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'short',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            })}
                                        </span>
                                    </div>
                                </div>
                                <button class="like-button flex items-center text-gray-500 hover:text-blue-600 transition duration-200"
                                        onclick="likeComment(${comment.id}, this)">
                                    <i class="far fa-heart mr-1"></i>
                                    <span class="likes-count">${comment.likes}</span>
                                </button>
                            </div>
                            <p class="text-gray-700 mt-2">${comment.comment_text}</p>
                        </div>
                    `).join('');
                } else {
                    commentsList.innerHTML = `
                        <div class="text-center text-gray-500 py-4">
                            No comments yet. Be the first to comment!
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading comments:', error);
                spinner.classList.add('hidden');
                commentsList.innerHTML = `
                    <div class="text-center text-red-500 py-4">
                        Failed to load comments. Please try again later.
                    </div>
                `;
            });
    }

    function submitComment(ideaId, userName, commentText) {
        return fetch('php/comments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                idea_id: ideaId,
                user_name: userName,
                comment_text: commentText
            })
        }).then(response => response.json());
    }

    function likeComment(commentId, button) {
        fetch('php/comments.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment_id: commentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const likesCount = button.querySelector('.likes-count');
                likesCount.textContent = data.likes;
                
                // Animate the like button
                button.querySelector('i').classList.remove('far');
                button.querySelector('i').classList.add('fas', 'text-red-500');
                button.classList.add('liked');
                
                // Add a heart animation
                const heart = document.createElement('div');
                heart.className = 'floating-heart';
                heart.innerHTML = '❤️';
                button.appendChild(heart);
                setTimeout(() => heart.remove(), 1000);
            }
        })
        .catch(error => {
            console.error('Error liking comment:', error);
            alert('Failed to like comment. Please try again.');
        });
    }
    </script>

    <style>
    .comment {
        opacity: 0;
        animation: fadeIn 0.5s ease forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .floating-heart {
        position: absolute;
        font-size: 1.5rem;
        pointer-events: none;
        animation: floatUp 1s ease-out forwards;
    }

    @keyframes floatUp {
        0% {
            transform: translate(-50%, 0) scale(0);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -20px) scale(1.2);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -40px) scale(1);
            opacity: 0;
        }
    }

    .like-button {
        position: relative;
        transition: transform 0.2s ease;
    }

    .like-button:hover {
        transform: scale(1.1);
    }

    .like-button.liked i {
        transform: scale(1.2);
        transition: transform 0.2s ease;
    }
    </style>
</body>
</html> 