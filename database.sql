-- Database: contract_management
CREATE DATABASE IF NOT EXISTS contract_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE contract_management;

-- Bảng người dùng
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'giao_vu') NOT NULL,
    faculty_id INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng Khoa
CREATE TABLE faculties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_code VARCHAR(20) UNIQUE NOT NULL,
    faculty_name VARCHAR(200) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng Nghề
CREATE TABLE professions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    profession_code VARCHAR(20) NOT NULL,
    profession_name VARCHAR(200) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE
);

-- Bảng Môn học
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    profession_id INT NOT NULL,
    subject_code VARCHAR(20) NOT NULL,
    subject_name VARCHAR(200) NOT NULL,
    credit_hours INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profession_id) REFERENCES professions(id) ON DELETE CASCADE
);

-- Bảng Giảng viên
CREATE TABLE lecturers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('Nam', 'Nữ') NOT NULL,
    birth_year INT,
    id_number VARCHAR(20) NOT NULL,
    id_issued_date DATE,
    id_issued_place VARCHAR(200),
    education_level ENUM('Đại học', 'Thạc sĩ', 'Tiến sĩ') NOT NULL,
    major VARCHAR(200),
    pedagogy VARCHAR(100),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    bank_account VARCHAR(50),
    bank_name VARCHAR(200),
    bank_branch VARCHAR(200),
    tax_code VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id)
);

-- Bảng Hợp đồng
CREATE TABLE contracts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contract_number VARCHAR(50) UNIQUE NOT NULL,
    lecturer_id INT NOT NULL,
    faculty_id INT NOT NULL,
    profession_id INT NOT NULL,
    subject_id INT NOT NULL,
    class_code VARCHAR(50) NOT NULL,
    total_hours INT NOT NULL,
    hourly_rate DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    contract_date DATE NOT NULL,
    status ENUM('draft', 'approved', 'completed', 'cancelled') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id),
    FOREIGN KEY (faculty_id) REFERENCES faculties(id),
    FOREIGN KEY (profession_id) REFERENCES professions(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Bảng cấu hình thù lao
CREATE TABLE hourly_rates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    education_level ENUM('Đại học', 'Thạc sĩ', 'Tiến sĩ') NOT NULL,
    rate_type ENUM('standard', 'high') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng activity logs
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert user admin mặc định (password: admin123)
-- Password được lưu dạng text để dễ quản lý
INSERT INTO users (username, password, full_name, email, role) 
VALUES ('admin', 'admin123', 'Quản trị viên', 'admin@cdnhcm.edu.vn', 'admin');

-- Insert mức thù lao mặc định
INSERT INTO hourly_rates (education_level, rate_type, amount, academic_year) VALUES
('Đại học', 'standard', 70000.00, '2025-2026'),
('Đại học', 'high', 90000.00, '2025-2026'),
('Thạc sĩ', 'standard', 75000.00, '2025-2026'),
('Thạc sĩ', 'high', 90000.00, '2025-2026'),
('Tiến sĩ', 'standard', 90000.00, '2025-2026'),
('Tiến sĩ', 'high', 100000.00, '2025-2026');

-- Insert khoa mẫu
INSERT INTO faculties (faculty_code, faculty_name, description) VALUES
('CNTT', 'Khoa Công nghệ Thông tin', 'Khoa đào tạo ngành CNTT'),
('CK', 'Khoa Cơ khí', 'Khoa đào tạo ngành Cơ khí'),
('DL', 'Khoa Du lịch - Nhà hàng - Khách sạn', 'Khoa đào tạo ngành Du lịch');
