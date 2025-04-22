-- Create database
CREATE DATABASE IF NOT EXISTS urbanspark;
USE urbanspark;

-- Drop existing table if it exists
DROP TABLE IF EXISTS ideas;

-- Create ideas table with basic structure
CREATE TABLE ideas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    file_path VARCHAR(255),
    people_affected INT DEFAULT 0,
    cost_savings INT DEFAULT 0,
    environmental_impact INT DEFAULT 0,
    implementation_time INT DEFAULT 1,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 