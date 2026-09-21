CREATE DATABASE IF NOT EXISTS school_repair
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_repair;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    role ENUM('admin','teacher','technician') NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE repair_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_no VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    building VARCHAR(100) NOT NULL,
    room VARCHAR(50) NOT NULL,
    problem_type ENUM('electrical','furniture','computer','aircon','other') NOT NULL,
    description TEXT NOT NULL,
    urgency ENUM('low','medium','high') DEFAULT 'medium',
    status ENUM('pending','accepted','in_progress','completed') DEFAULT 'pending',
    assigned_to INT NULL,
    due_date DATE NULL,
    completion_date DATE NULL,
    cost DECIMAL(10,2) NULL,
    result TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

CREATE TABLE repair_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    repair_id INT NOT NULL,
    image_type ENUM('before','after') NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (repair_id) REFERENCES repair_requests(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    repair_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (repair_id) REFERENCES repair_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- รหัสผ่านตัวอย่างทั้งหมดคือ 123456
INSERT INTO users (username,password,fullname,role,email) VALUES
('admin', MD5('123456'), 'ผู้ดูแลระบบ', 'admin', 'admin@school.local'),
('teacher', MD5('123456'), 'ครูตัวอย่าง', 'teacher', 'teacher@school.local'),
('tech', MD5('123456'), 'ช่างตัวอย่าง', 'technician', 'tech@school.local');
