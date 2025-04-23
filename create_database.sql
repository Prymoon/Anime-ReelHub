-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS reel_db;

-- Switch to the newly created database
USE reel_db;

-- Create the videos table if it doesn't exist
CREATE TABLE IF NOT EXISTS videos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL,
    video_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Create some sample data
-- INSERT INTO videos (title, description, category, video_path, thumbnail_path)
-- VALUES ('Sample Anime Video', 'A sample anime video for demonstration', 'anime', 'assets/videos/sample_anime.mp4', 'assets/images/anime-thumb.jpg');

-- INSERT INTO videos (title, description, category, video_path, thumbnail_path)
-- VALUES ('Sample Game Video', 'A sample game video for demonstration', 'game', 'assets/videos/sample_game.mp4', 'assets/images/game-thumb.jpg');
