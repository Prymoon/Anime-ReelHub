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

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reel_db";

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? intval($data['id']) : 0;

if ($id <= 0) {
    $response = array(
        "status" => "error",
        "message" => "Invalid video ID"
    );
    echo json_encode($response);
    exit;
}

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response = array(
        "status" => "error",
        "message" => "Connection failed: " . $conn->connect_error
    );
    echo json_encode($response);
    exit;
}

// Get video paths before deleting
$sql = "SELECT video_path, thumbnail_path FROM videos WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $videoPath = "../" . $row['video_path'];
    $thumbnailPath = "../" . $row['thumbnail_path'];
    
    // Delete the video file if it exists and is not a default thumbnail
    if (file_exists($videoPath)) {
        unlink($videoPath);
    }
    
    // Delete the thumbnail if it exists and is not a default thumbnail
    if (file_exists($thumbnailPath) && 
        !strpos($thumbnailPath, "anime-thumb.jpg") && 
        !strpos($thumbnailPath, "game-thumb.jpg")) {
        unlink($thumbnailPath);
    }
    
    // Delete from database
    $sql = "DELETE FROM videos WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $response = array(
            "status" => "success",
            "message" => "Video deleted successfully"
        );
    } else {
        $response = array(
            "status" => "error",
            "message" => "Error deleting video: " . $conn->error
        );
    }
} else {
    $response = array(
        "status" => "error",
        "message" => "Video not found"
    );
}

echo json_encode($response);
$conn->close();
?>
