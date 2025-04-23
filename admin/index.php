<?php
// Start output buffering to prevent any unwanted output
ob_start();

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Function to handle JSON responses
function sendJsonResponse($data) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Start session for login management
session_start();

// Admin credentials
$adminEmail = "anime@admin.com";
$adminPassword = "@PriyanshuAnimeGame";

// Check if user is logging in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email === $adminEmail && $password === $adminPassword) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        // Redirect to clear POST data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $loginError = "Invalid email or password";
    }
}

// Process video upload form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["videoFile"]) && isset($_SESSION['admin_logged_in'])) {
    // Set JSON header
    header('Content-Type: application/json');
    
    // Database connection settings
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "reel_db";

    // Create database connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        $response = array(
            "status" => "error",
            "message" => "Connection failed: " . $conn->connect_error
        );
        echo json_encode($response);
        exit;
    }

    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === FALSE) {
        $response = array(
            "status" => "error",
            "message" => "Error creating database: " . $conn->error
        );
        echo json_encode($response);
        exit;
    }

    // Switch to the new database
    $conn->select_db($dbname);

    // Create table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS videos (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        category VARCHAR(50) NOT NULL,
        video_path VARCHAR(255) NOT NULL,
        thumbnail_path VARCHAR(255),
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === FALSE) {
        $response = array(
            "status" => "error",
            "message" => "Error creating table: " . $conn->error
        );
        echo json_encode($response);
        exit;
    }

    // Get form data
    $title = $conn->real_escape_string($_POST['videoTitle']);
    $description = $conn->real_escape_string($_POST['videoDescription']);
    $category = $conn->real_escape_string($_POST['videoCategory']);
    
    // Handle video file upload
    $targetVideoDir = "../assets/videos/";
    $videoFileName = basename($_FILES["videoFile"]["name"]);
    $videoFileType = strtolower(pathinfo($videoFileName, PATHINFO_EXTENSION));
    $uniqueVideoName = uniqid() . '_' . $videoFileName;
    $targetVideoPath = $targetVideoDir . $uniqueVideoName;
    $uploadOk = 1;
    
    // Allow certain video file formats
    $allowedVideoFormats = array("mp4", "avi", "mov", "webm", "mkv");
    if (!in_array($videoFileType, $allowedVideoFormats)) {
        $response = array(
            "status" => "error",
            "message" => "Sorry, only MP4, AVI, MOV, WEBM & MKV files are allowed for videos."
        );
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo json_encode($response);
        exit;
    }
    
    // Process thumbnail
    $thumbnailPath = "";
    if(isset($_FILES["thumbnailFile"]) && $_FILES["thumbnailFile"]["error"] == 0) {
        $targetThumbDir = "../assets/images/";
        $thumbFileName = basename($_FILES["thumbnailFile"]["name"]);
        $thumbFileType = strtolower(pathinfo($thumbFileName, PATHINFO_EXTENSION));
        $uniqueThumbName = uniqid() . '_' . $thumbFileName;
        $targetThumbPath = $targetThumbDir . $uniqueThumbName;
        
        // Allow certain image file formats
        $allowedImageFormats = array("jpg", "jpeg", "png", "gif");
        if (in_array($thumbFileType, $allowedImageFormats)) {
            if (move_uploaded_file($_FILES["thumbnailFile"]["tmp_name"], $targetThumbPath)) {
                $thumbnailPath = "assets/images/" . $uniqueThumbName;
            }
        }
    }
    
    // Generate thumbnail from video if no thumbnail was uploaded
    if ($thumbnailPath == "") {
        // Default thumbnails based on category
        if ($category == "anime") {
            $thumbnailPath = "assets/images/anime-thumb.jpg";
        } else {
            $thumbnailPath = "assets/images/game-thumb.jpg";
        }
    }
    
    // Upload the video file
    if (move_uploaded_file($_FILES["videoFile"]["tmp_name"], $targetVideoPath)) {
        // Insert into database
        $videoDbPath = "assets/videos/" . $uniqueVideoName;
        $sql = "INSERT INTO videos (title, description, category, video_path, thumbnail_path) VALUES ('$title', '$description', '$category', '$videoDbPath', '$thumbnailPath')";
          if ($conn->query($sql) === TRUE) {
            sendJsonResponse([
                "status" => "success",
                "message" => "The video has been uploaded and saved to the database."
            ]);
        } else {
            sendJsonResponse([
                "status" => "error",
                "message" => "Error: " . $sql . " " . $conn->error
            ]);
        }
    } else {
        sendJsonResponse([
            "status" => "error",
            "message" => "Sorry, there was an error uploading your file."
        ]);
    }
}

// Check if user is logging out
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_email']);
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reel - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            color: #333;
        }
        
        .login-title {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .login-form .form-group {
            margin-bottom: 20px;
        }
        
        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .login-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        
        .error-message {
            color: #ff3333;
            background-color: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-home a {
            color: #6e8efb;
            text-decoration: none;
        }
        
        .back-to-home a:hover {
            text-decoration: underline;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            color: #333;
        }
        
        .admin-user .user-email {
            margin-right: 15px;
        }
        
        .logout-btn {
            background-color: #ff4d6d;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #ff3356;
        }
        
        .admin-actions {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        
        .admin-actions button {
            padding: 10px 20px;
            background-color: #6e8efb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .admin-actions button:hover {
            background-color: #5678e0;
        }
        
        .admin-actions button.active {
            background-color: #5678e0;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        
        .section {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .section.active {
            display: block;
            opacity: 1;
        }
        
        .video-list {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-radius: 10px;
            padding: 20px;
        }

        /* Manage Videos Section Improvements */
        #manageSection {
            margin-top: 20px;
        }

        .video-item {
            background: white;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }

        .video-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .video-category {
            font-size: 0.8em;
            padding: 3px 10px;
            border-radius: 15px;
            margin-left: 10px;
            color: white;
            text-transform: capitalize;
        }

        .video-category.anime {
            background: #ff4d6d;
        }

        .video-category.game {
            background: #4d79ff;
        }

        .delete-btn {
            background: #ff4d6d !important;
            color: white !important;
            padding: 8px 15px !important;
            border-radius: 5px !important;
            opacity: 1 !important;
        }

        .delete-btn:hover {
            background: #ff3356 !important;
        }

        .no-videos {
            text-align: center;
            padding: 30px;
            color: #666;
            font-size: 1.1em;
        }

        .error-message {
            color: #ff3356;
            text-align: center;
            padding: 20px;
        }

        .loading-text {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .loading-text i {
            margin-right: 10px;
        }

        #manageSection h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #6e8efb;
            padding-bottom: 10px;
        }

        /* Popup Styling */
        .popup {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 20px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            z-index: 10000;
            animation: slideIn 0.5s ease;
        }

        .popup-content {
            text-align: center;
        }

        .popup i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .popup.success-popup i {
            color: #4CAF50;
        }

        .popup.error-popup i {
            color: #f44336;
        }

        .popup h3 {
            margin: 5px 0;
        }

        .popup.fade-out {
            animation: slideOut 0.5s ease forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Background Video -->
    <div class="bg-video-container">
        <video id="bgVideo" autoplay muted loop>
            <source src="../assets/videos/background.mp4" type="video/mp4">
            <source src="../assets/videos/background.mkv" type="video/x-matroska">
            Your browser does not support the video tag.
        </video>
    </div>

    <?php if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
    <!-- Login Form -->
    <div class="login-container">
        <h1 class="login-title"><i class="fas fa-lock"></i> Admin Login</h1>
        
        <?php if (isset($loginError)): ?>
            <div class="error-message">
                <?php echo $loginError; ?>
            </div>
        <?php endif; ?>
        
        <form class="login-form" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn" name="login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="back-to-home">
            <a href="../index.php"><i class="fas fa-home"></i> Back to Home</a>
        </div>
    </div>
    <?php else: ?>
    <!-- Admin Panel -->
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-lock"></i> Admin Panel</h1>
            
            <div class="admin-user">
                <span class="user-email"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
                <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="admin-actions">
            <button id="uploadBtn" class="active"><i class="fas fa-upload"></i> Upload Video</button>
            <button id="manageBtn"><i class="fas fa-list"></i> Manage Videos</button>
        </div>
        
        <div class="section active" id="uploadSection">
            <form class="upload-form" id="uploadForm" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="videoTitle">Video Title</label>
                    <input type="text" id="videoTitle" name="videoTitle" required>
                </div>
                
                <div class="form-group">
                    <label for="videoDescription">Video Description (Optional)</label>
                    <textarea id="videoDescription" name="videoDescription"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="videoCategory">Category</label>
                    <select id="videoCategory" name="videoCategory" required>
                        <option value="">Select Category</option>
                        <option value="anime">Anime</option>
                        <option value="game">Game</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="videoFile">Video File</label>
                    <input type="file" id="videoFile" name="videoFile" accept="video/*" required>
                </div>
                
                <div class="form-group">
                    <label for="thumbnailFile">Thumbnail Image (Optional)</label>
                    <input type="file" id="thumbnailFile" name="thumbnailFile" accept="image/*">
                </div>
                
                <button type="submit" class="upload-btn">
                    <i class="fas fa-upload"></i> Upload Video
                </button>
            </form>

            <div id="uploadStatus" style="margin-top: 20px; display: none;"></div>
        </div>
        
        <div class="section" id="manageSection">
            <h2>Manage Videos</h2>
            <div class="video-list" id="videoList">
                <!-- Videos will be populated here -->
                <p class="loading-text"><i class="fas fa-spinner fa-spin"></i> Loading videos...</p>
            </div>
        </div>
    </div>    <script>
        // Function to load videos
        function loadVideos() {
            const videoList = document.getElementById('videoList');
            videoList.innerHTML = '<p class="loading-text"><i class="fas fa-spinner fa-spin"></i> Loading videos...</p>';
            
            fetch('get_videos.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    if (data.videos.length === 0) {
                        videoList.innerHTML = '<p class="no-videos">No videos found.</p>';
                    } else {
                        let html = '';
                        data.videos.forEach(video => {
                            const uploadDate = new Date(video.upload_date).toLocaleString();
                            html += `
                                <div class="video-item" data-id="${video.id}">
                                    <div class="details">
                                        <h3>${video.title} 
                                            <span class="video-category ${video.category}">${video.category}</span>
                                        </h3>
                                        <p>${video.description || 'No description'}</p>
                                        <small><i class="far fa-clock"></i> ${uploadDate}</small>
                                    </div>
                                    <div class="actions">
                                        <button onclick="deleteVideo(${video.id})" class="delete-btn">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        videoList.innerHTML = html;
                    }
                } else {
                    videoList.innerHTML = `<p class="error-message">Error: ${data.message}</p>`;
                }
            })
            .catch(error => {
                videoList.innerHTML = `<p class="error-message">Error loading videos: ${error.message}</p>`;
            });
        }

        // Initialize admin panel functionality
        document.addEventListener('DOMContentLoaded', function() {
            const uploadBtn = document.getElementById('uploadBtn');
            const manageBtn = document.getElementById('manageBtn');
            const uploadSection = document.getElementById('uploadSection');
            const manageSection = document.getElementById('manageSection');
            
            // Tab switching functionality
            uploadBtn.addEventListener('click', function() {
                uploadBtn.classList.add('active');
                manageBtn.classList.remove('active');
                uploadSection.classList.add('active');
                manageSection.classList.remove('active');
            });
            
            manageBtn.addEventListener('click', function() {
                manageBtn.classList.add('active');
                uploadBtn.classList.remove('active');
                manageSection.classList.add('active');
                uploadSection.classList.remove('active');
                loadVideos(); // Load videos when switching to manage tab
            });

            // Load videos initially if on manage section
            if (manageSection.classList.contains('active')) {
                loadVideos();
            }
            
            // Rest of your existing form handling code...
        });

        // Delete video function
        window.deleteVideo = function(id) {
            if (confirm('Are you sure you want to delete this video?')) {
                const videoItem = document.querySelector(`.video-item[data-id="${id}"]`);
                if (videoItem) {
                    videoItem.style.opacity = '0.5';
                }

                fetch('delete_video.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        if (videoItem) {
                            videoItem.style.opacity = '0';
                            videoItem.style.height = '0';
                            videoItem.style.margin = '0';
                            videoItem.style.padding = '0';
                            setTimeout(() => {
                                videoItem.remove();
                                // Check if there are no more videos
                                if (document.querySelectorAll('.video-item').length === 0) {
                                    document.getElementById('videoList').innerHTML = '<p class="no-videos">No videos found.</p>';
                                }
                            }, 300);
                        }
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    alert('Error deleting video: ' + error.message);
                    if (videoItem) {
                        videoItem.style.opacity = '1';
                    }
                });
            }
        };
    </script>
    <?php endif; ?>
</body>
</html>
