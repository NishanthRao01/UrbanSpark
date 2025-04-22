<!DOCTYPE html>
<?php
session_start();
$message = '';
$messageType = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = isset($_SESSION['success']) && $_SESSION['success'] ? 'success' : 'error';
    unset($_SESSION['message']);
    unset($_SESSION['success']);
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Your Idea - UrbanSpark</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- GSAP for advanced animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #10b981;
            --accent: #8b5cf6;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .nav-link {
            position: relative;
            overflow: hidden;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::after {
            transform: translateX(0);
        }

        .form-container {
            perspective: 1000px;
            transform-style: preserve-3d;
            transition: transform 0.5s ease;
        }

        .form-container:hover {
            transform: translateY(-5px) rotateX(5deg);
        }

        .form-group {
            position: relative;
            margin-bottom: 2rem;
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }

        .form-group:hover {
            transform: translateY(-2px);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .success-message {
            background-color: #10b981;
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .slider {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: #e5e7eb;
            outline: none;
            -webkit-appearance: none;
        }

        .slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #4f46e5;
            cursor: pointer;
            transition: all 0.2s;
        }

        .slider::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        .impact-summary {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }

        .submit-button {
            position: relative;
            overflow: hidden;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            transition: all 0.3s ease;
        }

        .submit-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .submit-button:hover::before {
            left: 100%;
        }

        .submit-button:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .floating-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.1;
        }

        .floating-element {
            position: absolute;
            background: var(--primary);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(10px, -10px) rotate(90deg);
            }
            50% {
                transform: translate(0, -20px) rotate(180deg);
            }
            75% {
                transform: translate(-10px, -10px) rotate(270deg);
            }
        }

        .impact-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .impact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .progress-ring__circle {
            transition: stroke-dashoffset 0.3s ease;
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            animation: slideDown 0.5s ease-out;
        }

        .message.success {
            background-color: #10b981;
            color: white;
        }

        .message.error {
            background-color: #ef4444;
            color: white;
        }

        .message i {
            margin-right: 0.5rem;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Floating Background Elements -->
    <div class="floating-bg" id="floatingBg"></div>

    <!-- Navigation -->
    <nav class="bg-white shadow-lg mb-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.html" class="flex items-center">
                        <i class="fas fa-city text-indigo-600 text-2xl mr-2"></i>
                        <span class="text-xl font-bold">UrbanSpark</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.html" class="text-gray-700 hover:text-indigo-600">Home</a>
                    <a href="submit.php" class="text-gray-700 hover:text-indigo-600">Submit Idea</a>
                    <a href="ideas.php" class="text-gray-700 hover:text-indigo-600">Ideas</a>
                    <a href="stats.html" class="text-gray-700 hover:text-indigo-600">Stats</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Submission Form -->
    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-center mb-8 text-indigo-600">Submit Your Idea</h2>
            
            <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <div id="successMessage" class="success-message hidden">
                <i class="fas fa-check-circle mr-2"></i>
                Your idea has been submitted successfully!
            </div>

            <form id="ideaForm" action="php/submit.php" method="POST" enctype="multipart/form-data">
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                    <div id="similarIdeas" class="mt-2 text-sm"></div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="description">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-control" required></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="tags">Tags/Keywords (comma separated)</label>
                    <input type="text" id="tags" name="tags" class="form-control" placeholder="e.g., sustainability, technology, community">
                    <p class="text-sm text-gray-600 mt-1">Add relevant tags to help others find your idea</p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-4">Idea Impact Calculator</h3>
                    
                    <input type="hidden" id="people_affected" name="people_affected" value="0">
                    <input type="hidden" id="cost_savings" name="cost_savings" value="0">
                    <input type="hidden" id="environmental_impact" name="environmental_impact" value="0">
                    <input type="hidden" id="implementation_time" name="implementation_time" value="1">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">
                            Estimated Number of People Affected: <span id="peopleAffectedValue">0</span>
                        </label>
                        <input type="range" id="peopleAffected" class="slider" min="0" max="100000" step="1000" value="0">
                        <div class="flex justify-between text-sm text-gray-600 mt-1">
                            <span>0</span>
                            <span>100,000+</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">
                            Potential Annual Cost Savings: <span id="costSavingsValue">₹0</span>
                        </label>
                        <input type="range" id="costSavings" class="slider" min="0" max="10000000" step="100000" value="0">
                        <div class="flex justify-between text-sm text-gray-600 mt-1">
                            <span>₹0</span>
                            <span>₹1 Crore+</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">
                            Environmental Impact Score: <span id="environmentalImpactValue">0</span>
                        </label>
                        <input type="range" id="environmentalImpact" class="slider" min="0" max="10" step="1" value="0">
                        <div class="flex justify-between text-sm text-gray-600 mt-1">
                            <span>Low</span>
                            <span>High</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">
                            Implementation Time: <span id="implementationTimeValue">1 month</span>
                        </label>
                        <input type="range" id="implementationTime" class="slider" min="1" max="36" step="1" value="1">
                        <div class="flex justify-between text-sm text-gray-600 mt-1">
                            <span>1 month</span>
                            <span>3 years</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-white rounded-lg shadow-sm">
                        <h4 class="text-lg font-bold mb-3">Overall Impact Score</h4>
                        <div class="flex items-center">
                            <div class="w-full bg-gray-200 rounded-full h-4 mr-4">
                                <div id="impactScoreBar" class="bg-blue-600 h-4 rounded-full" style="width: 0%"></div>
                            </div>
                            <span id="impactScore" class="text-lg font-bold">0</span>/100
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Based on people affected, cost savings, environmental impact, and implementation time</p>
                    </div>

                    <div id="impactSummary" class="impact-summary mt-4"></div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="category">Category</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select a category</option>
                        <option value="Infrastructure">Infrastructure</option>
                        <option value="Environment">Environment</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Technology">Technology</option>
                        <option value="Education">Education</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="email">Contact Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="file">
                        Attach File (optional)
                    </label>
                    <input type="file" id="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <p class="text-sm text-gray-600 mt-1">PDF, JPG, PNG (max. 5MB)</p>
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-full font-bold hover:bg-indigo-700 transition duration-300">
                        Submit Idea
                    </button>
                </div>
            </form>
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
            const sliders = {
                peopleAffected: document.getElementById('peopleAffected'),
                costSavings: document.getElementById('costSavings'),
                environmentalImpact: document.getElementById('environmentalImpact'),
                implementationTime: document.getElementById('implementationTime')
            };

            const hiddenInputs = {
                peopleAffected: document.getElementById('people_affected'),
                costSavings: document.getElementById('cost_savings'),
                environmentalImpact: document.getElementById('environmental_impact'),
                implementationTime: document.getElementById('implementation_time')
            };

            const valueDisplays = {
                peopleAffected: document.getElementById('peopleAffectedValue'),
                costSavings: document.getElementById('costSavingsValue'),
                environmentalImpact: document.getElementById('environmentalImpactValue'),
                implementationTime: document.getElementById('implementationTimeValue')
            };

            const impactSummary = document.getElementById('impactSummary');

            function formatNumber(num) {
                return new Intl.NumberFormat('en-IN').format(num);
            }

            function updateImpactSummary() {
                const people = parseInt(sliders.peopleAffected.value);
                const savings = parseInt(sliders.costSavings.value);
                const envImpact = parseInt(sliders.environmentalImpact.value);
                const time = parseInt(sliders.implementationTime.value);

                hiddenInputs.peopleAffected.value = people;
                hiddenInputs.costSavings.value = savings;
                hiddenInputs.environmentalImpact.value = envImpact;
                hiddenInputs.implementationTime.value = time;

                let summary = '';
                
                if (people > 0) {
                    summary += `<p class="mb-2">• Will benefit approximately ${formatNumber(people)} people</p>`;
                }
                
                if (savings > 0) {
                    summary += `<p class="mb-2">• Potential annual savings of ₹${formatNumber(savings)}</p>`;
                }
                
                if (envImpact > 0) {
                    const impactLevel = envImpact <= 3 ? 'Low' : (envImpact <= 7 ? 'Medium' : 'High');
                    summary += `<p class="mb-2">• ${impactLevel} environmental impact potential</p>`;
                }
                
                summary += `<p class="mb-2">• Estimated implementation time: ${time} month${time > 1 ? 's' : ''}</p>`;

                impactSummary.innerHTML = summary;
            }

            function calculateImpactScore() {
                const people = parseInt(sliders.peopleAffected.value);
                const savings = parseInt(sliders.costSavings.value);
                const envImpact = parseInt(sliders.environmentalImpact.value);
                const time = parseInt(sliders.implementationTime.value);

                // Calculate individual scores (0-25 each)
                const peopleScore = Math.min(25, (people / 100000) * 25);
                const savingsScore = Math.min(25, (savings / 10000000) * 25);
                const envScore = (envImpact / 10) * 25;
                const timeScore = Math.max(0, 25 - ((time - 1) / 35) * 25);

                // Calculate total score
                const totalScore = Math.round(peopleScore + savingsScore + envScore + timeScore);

                // Update the impact score display
                const impactScoreBar = document.getElementById('impactScoreBar');
                const impactScore = document.getElementById('impactScore');
                impactScoreBar.style.width = totalScore + '%';
                impactScore.textContent = totalScore;
            }

            Object.keys(sliders).forEach(key => {
                sliders[key].addEventListener('input', function() {
                    if (key === 'costSavings') {
                        valueDisplays[key].textContent = '₹' + formatNumber(this.value);
                    } else if (key === 'implementationTime') {
                        valueDisplays[key].textContent = this.value + ' month' + (this.value > 1 ? 's' : '');
                    } else {
                        valueDisplays[key].textContent = formatNumber(this.value);
                    }
                    updateImpactSummary();
                    calculateImpactScore();
                });
            });

            // Similar Ideas Search
            let searchTimeout;
            const title = document.getElementById('title');
            title.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();
                
                // Search for similar ideas
                searchTimeout = setTimeout(() => {
                    if (query.length < 3) {
                        document.getElementById('similarIdeas').innerHTML = '';
                        return;
                    }
                    
                    fetch(`search_ideas.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            const similarIdeasDiv = document.getElementById('similarIdeas');
                            if (data.length > 0) {
                                const html = data.map(idea => `
                                    <div class="p-2 border-b border-gray-200">
                                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                        ${idea.title}
                                    </div>
                                `).join('');
                                similarIdeasDiv.innerHTML = `
                                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                                        <h3 class="text-lg font-semibold mb-2">Similar Ideas Found:</h3>
                                        ${html}
                                    </div>
                                `;
                            } else {
                                similarIdeasDiv.innerHTML = '';
                            }
                        })
                        .catch(error => {
                            console.error('Search failed:', error);
                        });
                }, 300);
            });

            // Add message auto-hide
            const message = document.querySelector('.message');
            if (message) {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-20px)';
                    message.style.transition = 'all 0.5s ease-out';
                    setTimeout(() => {
                        message.remove();
                    }, 500);
                }, 5000);
            }

            // Initialize the impact score
            calculateImpactScore();
            updateImpactSummary();
        });
    </script>
</body>
</html> 