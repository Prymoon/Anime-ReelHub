<?php
// Start the session
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reel_db";

// The category to filter by
$category = "anime";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get videos from the specified category
$sql = "SELECT * FROM videos WHERE category = '$category' ORDER BY upload_date DESC";
$result = $conn->query($sql);

$videos = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reel - Anime</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="category-page anime-page">
    <!-- Background Video (Same as home page) -->
    <div class="bg-video-container">
        <video id="bgVideo" autoplay muted loop playsinline>
            <source src="../assets/videos/background.mp4" type="video/mp4">
            <source src="../assets/videos/background.mkv" type="video/x-matroska">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Audio Control -->
    <div class="sound-toggle">
        <button id="soundToggle" title="Toggle Sound">
            <i class="fas fa-volume-mute"></i>
        </button>
    </div>    <!-- Back Button (only shows if user hasn't entered name) -->
    <?php if (!isset($_SESSION['userName'])): ?>
    <a href="../index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
    </a>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="container">
        <h1 class="main-title">Anime Collection</h1>
        
        <div class="video-grid" id="animeVideoGrid">
            <?php if (count($videos) > 0): ?>
                <?php foreach ($videos as $video): ?>
                    <div class="video-card" data-video-src="../<?php echo htmlspecialchars($video['video_path']); ?>">
                        <div class="video-thumbnail">
                            <img src="../<?php echo htmlspecialchars($video['thumbnail_path']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                            <div class="video-play-btn">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                            <p class="video-description"><?php echo htmlspecialchars($video['description'] ?: 'No description available.'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-videos">
                    <p>No anime videos available yet. Please check back later or visit the admin panel to upload some!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Video Overlay for Playing Videos -->
    <div class="video-overlay">
        <div class="video-player-container">
            <button class="close-video">
                <i class="fas fa-times"></i>
            </button>
            <video id="videoPlayer" class="video-player" controls>
                <source src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
