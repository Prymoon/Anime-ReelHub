<?php
// Set JSON header
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reel_db";

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

// Get all videos from database
$sql = "SELECT * FROM videos ORDER BY upload_date DESC";
$result = $conn->query($sql);

$videos = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

$response = array(
    "status" => "success",
    "videos" => $videos
);

echo json_encode($response);

$conn->close();
?>
