-- Migration script for existing database
-- Run this in phpMyAdmin if you already have the database set up

-- Add media_type column to project_images table if it doesn't exist
ALTER TABLE project_images 
ADD COLUMN IF NOT EXISTS media_type ENUM('image', 'video') NOT NULL DEFAULT 'image' AFTER image_path;

-- Add hero_image column to admin table if it doesn't exist
ALTER TABLE admin 
ADD COLUMN IF NOT EXISTS hero_image VARCHAR(255) DEFAULT 'assets/images/hero-bg.jpg' AFTER profile_image;

-- Create media_gallery table if it doesn't exist
CREATE TABLE IF NOT EXISTS media_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('image', 'video') NOT NULL DEFAULT 'image',
    thumbnail_path VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Update existing project_images records to have media_type = 'image'
UPDATE project_images SET media_type = 'image' WHERE media_type IS NULL;

-- Set default hero image for existing admin
UPDATE admin SET hero_image = 'assets/images/hero-bg.jpg' WHERE hero_image IS NULL;
