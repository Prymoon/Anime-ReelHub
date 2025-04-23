<?php
// If the user is already logged in, get their name from the session
session_start();
$userName = isset($_SESSION['userName']) ? $_SESSION['userName'] : '';

// Process form submission if any
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['userName'])) {
        $_SESSION['userName'] = $_POST['userName'];
        $userName = $_SESSION['userName'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReelHub - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
</head>
<body class="home-page">
    <!-- Background Video with Audio -->
    <div class="bg-video-container">
        <video id="bgVideo" autoplay muted loop playsinline>
            <source src="assets/videos/background.mkv" type="video/x-matroska">
            <source src="assets/videos/background.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Audio Control -->
    <div class="sound-toggle">
        <button id="soundToggle" title="Toggle Sound">
            <i class="fas fa-volume-mute"></i>
        </button>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="welcome-section">
            <h1 class="main-title">Welcome to ReelHub</h1>
            <p>Your entertainment hub for anime and games</p>
            
            <form id="nameForm" class="name-form" method="post">
                <div class="input-group">
                    <input type="text" id="userName" name="userName" placeholder="What's your name?" value="<?php echo htmlspecialchars($userName); ?>" required>
                </div>
                <button type="submit" class="submit-btn">Get Started</button>
            </form>
            
            <div class="categories-container">
                <div class="category-box anime" onclick="location.href='pages/anime.php'">
                    <img src="assets/images/anime-thumb.jpg" alt="Anime">
                    <h2>Anime</h2>
                </div>
                <div class="category-box game" onclick="location.href='pages/game.php'">
                    <img src="assets/images/game-thumb.jpg" alt="Game">
                    <h2>Games</h2>
                </div>
            </div>        </div>    </div>
      <!-- Admin Link with Login Button -->
    <div class="admin-link">
        <a href="admin/index.php" class="admin-button" title="Admin Panel">
            <i class="fas fa-lock"></i> Admin Login
        </a>
    </div>
    
    <!-- Footer with Copyright -->
    <footer class="site-footer">
        <div class="footer-content">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> ReelHub. All Rights Reserved.
            </div>
            <div class="social-links">
                <a href="https://instagram.com/pry_uchiha" target="_blank" title="Follow us on Instagram">
                    <i class="fab fa-instagram"></i> @pry_uchiha
                </a>
            </div>
        </div>
    </footer>
      <script src="js/main.js"></script>
    <script src="js/animations.js"></script>
      <style>        .admin-link {
            position: fixed;
            bottom: 80px;  /* Increased to appear above footer */
            right: 20px;
            z-index: 90;   /* Reduced z-index to appear below footer */
        }
        
        .admin-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .admin-button:hover {
            background-color: rgba(106, 142, 251, 0.8);
            transform: translateY(-3px);
        }
    </style>
    
    <?php if ($userName): ?>
    <script>
        // If user name is already set (from session), hide the form and show categories
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.getElementById('nameForm').classList.add('hide');
                document.querySelector('.categories-container').classList.add('show');
            }, 500);
        });
    </script>
    <?php endif; ?>
</body>
</html>
