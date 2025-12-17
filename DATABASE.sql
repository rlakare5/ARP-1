-- Bhairavnath Construction ARP Database
-- Run this SQL file in phpMyAdmin to create the database

CREATE DATABASE IF NOT EXISTS bhairavnath_construction;
USE bhairavnath_construction;

-- Admin Table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    experience VARCHAR(50),
    certifications TEXT,
    phone VARCHAR(20),
    address TEXT,
    about TEXT,
    profile_image VARCHAR(255),
    hero_image VARCHAR(255) DEFAULT 'assets/images/hero-bg.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    project_type ENUM('Road', 'Residential', 'Commercial', 'Infrastructure') NOT NULL,
    location VARCHAR(255),
    duration VARCHAR(100),
    budget VARCHAR(100),
    description TEXT,
    status ENUM('Completed', 'Ongoing') DEFAULT 'Ongoing',
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Project Media Table (images and videos)
CREATE TABLE IF NOT EXISTS project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    media_type ENUM('image', 'video') NOT NULL DEFAULT 'image',
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inquiries Table
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin (Password: admin123)
INSERT INTO admin (email, password, name, experience, certifications, phone, address, about) VALUES 
('admin@bhairavnath.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bhairavnath Construction', '15+ Years', 'Licensed Civil Engineer, ISO Certified', '+91 9876543210', 'Maharashtra, India', 'We are a trusted construction company specializing in building construction and road projects. With over 15 years of experience, we deliver quality work with transparency and dedication.');

-- Insert Sample Services
INSERT INTO services (name, description, icon) VALUES 
('Building Construction', 'Complete residential and commercial building construction with modern techniques and quality materials.', 'fas fa-building'),
('Road Construction', 'Professional road construction and maintenance services for government and private projects.', 'fas fa-road'),
('Infrastructure Development', 'Large-scale infrastructure projects including bridges, drainage systems, and public facilities.', 'fas fa-city'),
('Renovation & Remodeling', 'Transform your existing space with our expert renovation and remodeling services.', 'fas fa-hammer'),
('Project Consultation', 'Expert consultation for your construction projects, including cost estimation and planning.', 'fas fa-clipboard-list'),
('Quality Inspection', 'Thorough quality inspection and certification services for construction projects.', 'fas fa-search');

-- Media Gallery Table (for images and videos)
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

-- Insert Sample Projects
INSERT INTO projects (name, project_type, location, duration, budget, description, status, is_published) VALUES 
('Modern Residential Complex', 'Residential', 'Pune, Maharashtra', '18 Months', '₹5 Crore', 'A modern 3-story residential complex with 12 premium apartments featuring contemporary design and amenities.', 'Completed', 1),
('Highway Extension Project', 'Road', 'Nashik Highway', '24 Months', '₹15 Crore', 'Major highway extension and widening project covering 10km stretch with proper drainage and safety measures.', 'Ongoing', 1),
('Commercial Shopping Center', 'Commercial', 'Mumbai Suburbs', '12 Months', '₹8 Crore', 'State-of-the-art shopping center with multiple retail spaces, parking facilities, and modern architecture.', 'Completed', 1);
