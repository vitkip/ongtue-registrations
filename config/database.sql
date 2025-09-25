-- Certificate Registration System Database
-- Create database and tables with UTF-8 support

CREATE DATABASE cert_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE cert_system;

-- Users table for admin login
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','staff','viewer') NOT NULL DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Registrations table for student data
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    major VARCHAR(100) NOT NULL,
    graduation_year YEAR NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    profile_image VARCHAR(255),
    payment_proof VARCHAR(255),
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample data for testing
INSERT INTO registrations (student_code, first_name, last_name, major, graduation_year, email, phone, status) VALUES
('STU001', 'ສົມຊາຍ', 'ວົງສະວັນ', 'ວິສະວະກຳຄອມພິວເຕີ', 2024, 'somchai@email.com', '020-5555-0001', 'approved'),
('STU002', 'ນາງສາວ', 'ພູວີ', 'ເສດຖະກິດ', 2024, 'phouvee@email.com', '020-5555-0002', 'pending'),
('STU003', 'ທ້າວ', 'ບຸນມີ', 'ກົດໝາຍ', 2024, 'bounmee@email.com', '020-5555-0003', 'rejected');