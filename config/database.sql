-- ລະບົບລົງທະບຽນໃບປະກາດນີຍະບັດ (Certificate Registration System)
-- ເວີຊັນ: 2.0.0
-- ວັນທີ່: 13 ຕຸລາ 2025
-- ຄຳອະທິບາຍ: ສ້າງຖານຂໍ້ມູນແລະຕາຕະລາງພ້ອມການຮອງຮັບ UTF-8 ສຳລັບພາສາລາວ

-- ============================================================================
-- ຂັ້ນຕອນທີ 1: ສ້າງຖານຂໍ້ມູນ (Create Database)
-- ============================================================================
CREATE DATABASE IF NOT EXISTS cert_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE cert_system;

-- ============================================================================
-- ຂັ້ນຕອນທີ 2: ສ້າງຕາຕະລາງຜູ້ໃຊ້ (Users Table)
-- ============================================================================
-- ຕາຕະລາງສຳລັບເກັບຂໍ້ມູນຜູ້ໃຊ້ລະບົບ (Admin, Staff, Viewer)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE COMMENT 'ຊື່ຜູ້ໃຊ້',
    password VARCHAR(255) NOT NULL COMMENT 'ລະຫັດຜ່ານ (Hashed)',
    role ENUM('admin','staff','viewer') NOT NULL DEFAULT 'viewer' COMMENT 'ບົດບາດຜູ້ໃຊ້',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'ວັນທີ່ສ້າງບັນຊີ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- ຂັ້ນຕອນທີ 3: ສ້າງຕາຕະລາງລົງທະບຽນ (Registrations Table)
-- ============================================================================
-- ຕາຕະລາງສຳລັບເກັບຂໍ້ມູນການລົງທະບຽນຂອງນິສິດ
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(50) NOT NULL UNIQUE COMMENT 'ລະຫັດນິສິດ',
    
    -- ຂໍ້ມູນສ່ວນຕົວ (Personal Information)
    first_name VARCHAR(100) NOT NULL COMMENT 'ຊື່ (ພາສາລາວ)',
    first_name_en VARCHAR(100) NULL COMMENT 'ຊື່ (ພາສາອັງກິດ) - v2.0',
    last_name VARCHAR(100) NOT NULL COMMENT 'ນາມສະກຸນ (ພາສາລາວ)',
    last_name_en VARCHAR(100) NULL COMMENT 'ນາມສະກຸນ (ພາສາອັງກິດ) - v2.0',
    
    -- ຂໍ້ມູນການສຶກສາ (Education Information)
    major VARCHAR(100) NOT NULL COMMENT 'ສາຂາວິຊາ',
    graduation_year YEAR NOT NULL COMMENT 'ປີຈົບການສຶກສາ',
    
    -- ຂໍ້ມູນການຕິດຕໍ່ (Contact Information)
    email VARCHAR(150) NOT NULL COMMENT 'ອີເມວ',
    phone VARCHAR(20) NOT NULL COMMENT 'ເບີໂທລະສັບ',
    
    -- ໄຟລ໌ເອກະສານ (Document Files)
    profile_image VARCHAR(255) COMMENT 'ຮູບໂປຣໄຟລ໌',
    payment_proof VARCHAR(255) COMMENT 'ໃບຢັ້ງຢືນການຈ່າຍເງິນ',
    
    -- ຂໍ້ມູນການຊຳລະເງິນ (Payment Information)
    amount DECIMAL(10,2) DEFAULT 200000.00 COMMENT 'ຈຳນວນເງິນ (ກີບ) - v2.0',
    
    -- ສະຖານະແລະວັນທີ່ (Status and Date)
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' COMMENT 'ສະຖານະການລົງທະບຽນ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'ວັນທີ່ລົງທະບຽນ',
    
    -- Index ສຳລັບການຄົ້ນຫາທີ່ໄວຂຶ້ນ (Search Performance)
    INDEX idx_first_name_en (first_name_en),
    INDEX idx_last_name_en (last_name_en),
    INDEX idx_student_code (student_code),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- ຂັ້ນຕອນທີ 4: ເພີ່ມຂໍ້ມູນເລີ່ມຕົ້ນ (Insert Default Data)
-- ============================================================================

-- ເພີ່ມບັນຊີ Admin ເລີ່ມຕົ້ນ (Default Admin Account)
-- Username: admin
-- Password: admin123 (ກະລຸນາປ່ຽນຫຼັງຕິດຕັ້ງ!)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- ເພີ່ມຂໍ້ມູນຕົວຢ່າງສຳລັບທົດສອບ (Sample Data for Testing)
INSERT INTO registrations (student_code, first_name, first_name_en, last_name, last_name_en, major, graduation_year, email, phone, amount, status) VALUES
('STU001', 'ສົມຊາຍ', 'Somchai', 'ວົງສະວັນ', 'Vongsavan', 'ວິສະວະກຳຄອມພິວເຕີ', 2024, 'somchai@email.com', '020-5555-0001', 200000.00, 'approved'),
('STU002', 'ນາງສາວ', 'Nangxao', 'ພູວີ', 'Phouvee', 'ເສດຖະກິດ', 2024, 'phouvee@email.com', '020-5555-0002', 250000.00, 'pending'),
('STU003', 'ທ້າວ', 'Thao', 'ບຸນມີ', 'Bounmee', 'ກົດໝາຍ', 2024, 'bounmee@email.com', '020-5555-0003', 300000.00, 'rejected')
ON DUPLICATE KEY UPDATE student_code=student_code;

-- ============================================================================
-- ຂັ້ນຕອນທີ 5: ສຳເລັດການຕິດຕັ້ງ (Installation Complete)
-- ============================================================================
-- ✅ ຖານຂໍ້ມູນສ້າງສຳເລັດ!
-- ✅ ຕາຕະລາງທັງໝົດພ້ອມໃຊ້ງານ!
-- ✅ ຂໍ້ມູນເລີ່ມຕົ້ນເພີ່ມແລ້ວ!
--
-- ຂໍ້ມູນການເຂົ້າສູ່ລະບົບ:
-- URL: http://localhost/registrations/admin/
-- Username: admin
-- Password: admin123
--
-- ⚠️ ສຳຄັນ: ກະລຸນາປ່ຽນລະຫັດຜ່ານ Admin ທັນທີຫຼັງການຕິດຕັ້ງ!
-- ============================================================================