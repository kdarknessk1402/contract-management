-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 10, 2025 lúc 01:50 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `contract_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_code` varchar(50) NOT NULL,
  `class_name` varchar(200) NOT NULL,
  `profession_id` int(11) NOT NULL,
  `level` enum('Trung cấp','Cao đẳng','Cao đẳng liên thông') NOT NULL,
  `location_id` int(11) NOT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `classes`
--

INSERT INTO `classes` (`id`, `class_code`, `class_name`, `profession_id`, `level`, `location_id`, `academic_year`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'K47CNTT-CD', 'Lớp Công nghệ thông tin K47 Cao đẳng', 1, 'Cao đẳng', 1, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 'K47CNTT-TC', 'Lớp Công nghệ thông tin K47 Trung cấp', 2, 'Trung cấp', 1, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(3, 'K47CNTT-LT', 'Lớp Công nghệ thông tin K47 Liên thông', 3, 'Cao đẳng liên thông', 2, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(4, 'K48CNTT-CD', 'Lớp Công nghệ thông tin K48 Cao đẳng', 1, 'Cao đẳng', 3, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contracts`
--

CREATE TABLE `contracts` (
  `id` int(11) NOT NULL,
  `contract_number` varchar(50) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `profession_id` int(11) NOT NULL,
  `level` enum('Trung cấp','Cao đẳng','Cao đẳng liên thông') DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `total_hours` int(11) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `contract_date` date NOT NULL,
  `status` enum('draft','approved','completed','cancelled') DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `faculties`
--

CREATE TABLE `faculties` (
  `id` int(11) NOT NULL,
  `faculty_code` varchar(50) NOT NULL,
  `faculty_name` varchar(200) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `faculties`
--

INSERT INTO `faculties` (`id`, `faculty_code`, `faculty_name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CNTT', 'Khoa Công nghệ thông tin', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 'KT', 'Khoa Kinh tế', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(3, 'XD', 'Khoa Xây dựng', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(4, 'CK', 'Khoa Cơ khí', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hourly_rates_by_location`
--

CREATE TABLE `hourly_rates_by_location` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `education_level` enum('Đại học','Thạc sĩ','Tiến sĩ') NOT NULL,
  `rate_per_hour` decimal(10,2) NOT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hourly_rates_by_location`
--

INSERT INTO `hourly_rates_by_location` (`id`, `location_id`, `education_level`, `rate_per_hour`, `academic_year`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Đại học', 70000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 1, 'Thạc sĩ', 75000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(3, 1, 'Tiến sĩ', 80000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(4, 2, 'Đại học', 70000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(5, 2, 'Thạc sĩ', 75000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(6, 2, 'Tiến sĩ', 80000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(7, 3, 'Đại học', 90000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(8, 3, 'Thạc sĩ', 90000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(9, 3, 'Tiến sĩ', 95000.00, '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `gender` enum('Nam','Nữ') NOT NULL,
  `birth_year` int(11) DEFAULT NULL,
  `id_number` varchar(12) NOT NULL,
  `id_issued_date` date DEFAULT NULL,
  `id_issued_place` varchar(200) DEFAULT NULL,
  `education_level` enum('Đại học','Thạc sĩ','Tiến sĩ') NOT NULL,
  `major` varchar(200) DEFAULT NULL,
  `pedagogy` enum('Có','Không') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_branch` varchar(200) DEFAULT NULL,
  `tax_code` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lecturers`
--

INSERT INTO `lecturers` (`id`, `faculty_id`, `full_name`, `gender`, `birth_year`, `id_number`, `id_issued_date`, `id_issued_place`, `education_level`, `major`, `pedagogy`, `address`, `phone`, `email`, `bank_account`, `bank_name`, `bank_branch`, `tax_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nguyễn Văn A', 'Nam', 1985, '001085001234', NULL, NULL, 'Thạc sĩ', 'Công nghệ phần mềm', NULL, NULL, '0901234567', 'nguyenvana@example.com', NULL, NULL, NULL, '001085001234', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 1, 'Trần Thị B', 'Nữ', 1990, '001090005678', NULL, NULL, 'Đại học', 'Hệ thống thông tin', NULL, NULL, '0907654321', 'tranthib@example.com', NULL, NULL, NULL, '001090005678', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(3, 1, 'Lê Văn C', 'Nam', 1982, '001082009999', NULL, NULL, 'Tiến sĩ', 'Trí tuệ nhân tạo', NULL, NULL, '0909999999', 'levanc@example.com', NULL, NULL, NULL, '001082009999', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `location_code` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `locations`
--

INSERT INTO `locations` (`id`, `location_name`, `location_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cơ sở 1', 'CS1', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 'Cơ sở 2', 'CS2', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(3, 'Cơ sở Bình Dương', 'CSBD', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `professions`
--

CREATE TABLE `professions` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `profession_code` varchar(50) NOT NULL,
  `level` enum('Trung cấp','Cao đẳng','Cao đẳng liên thông') NOT NULL DEFAULT 'Cao đẳng',
  `profession_name` varchar(200) NOT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `professions`
--

INSERT INTO `professions` (`id`, `faculty_id`, `profession_code`, `level`, `profession_name`, `academic_year`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'CNTT01', 'Cao đẳng', 'Công nghệ thông tin', '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 1, 'CNTT01', 'Trung cấp', 'Công nghệ thông tin', '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(3, 1, 'CNTT01', 'Cao đẳng liên thông', 'Công nghệ thông tin', '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(4, 1, 'TKDH', 'Cao đẳng', 'Thiết kế đồ họa', '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(5, 1, 'TKDH', 'Trung cấp', 'Thiết kế đồ họa', '2025-2026', 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `profession_id` int(11) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `credit_hours` int(11) DEFAULT 0 COMMENT 'Số giờ của môn học',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `subjects`
--

INSERT INTO `subjects` (`id`, `profession_id`, `subject_code`, `subject_name`, `credit_hours`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'MH001', 'Lập trình Java', 60, 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 1, 'MH002', 'Cơ sở dữ liệu', 45, 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(3, 1, 'MH003', 'Mạng máy tính', 50, 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(4, 2, 'MH004', 'Lập trình C++', 40, 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(5, 2, 'MH005', 'Tin học văn phòng', 30, 1, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(6, 3, 'MH006', 'Lập trình Web', 55, 1, '2025-12-09 08:55:22', '2025-12-09 08:55:22'),
(7, 3, 'MH007', 'Phân tích thiết kế hệ thống', 50, 1, '2025-12-09 08:55:22', '2025-12-09 08:55:22'),
(8, 4, 'MH008', 'Photoshop nâng cao', 45, 1, '2025-12-09 08:55:22', '2025-12-09 08:55:22'),
(9, 4, 'MH009', 'Illustrator', 40, 1, '2025-12-09 08:55:22', '2025-12-09 08:55:22'),
(10, 5, 'MH010', 'Photoshop cơ bản', 35, 1, '2025-12-09 08:55:22', '2025-12-09 08:55:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','giao_vu') NOT NULL DEFAULT 'giao_vu',
  `faculty_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `faculty_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin123', 'Quản trị viên', 'admin@example.com', 'admin', NULL, 1, NULL, '2025-12-09 08:19:43', '2025-12-09 08:19:43'),
(2, 'giaovu_cntt', 'giaovu123', 'Giáo vụ Khoa CNTT', 'giaovu.cntt@example.com', 'giao_vu', 1, 1, NULL, '2025-12-09 08:19:43', '2025-12-09 08:19:43');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_code` (`class_code`),
  ADD KEY `idx_classes_profession` (`profession_id`),
  ADD KEY `idx_classes_location` (`location_id`);

--
-- Chỉ mục cho bảng `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_number` (`contract_number`),
  ADD KEY `profession_id` (`profession_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_contracts_lecturer` (`lecturer_id`),
  ADD KEY `idx_contracts_faculty` (`faculty_id`),
  ADD KEY `idx_contracts_status` (`status`),
  ADD KEY `idx_contracts_date` (`contract_date`),
  ADD KEY `contracts_location_fk` (`location_id`),
  ADD KEY `contracts_class_fk` (`class_id`);

--
-- Chỉ mục cho bảng `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculty_code` (`faculty_code`);

--
-- Chỉ mục cho bảng `hourly_rates_by_location`
--
ALTER TABLE `hourly_rates_by_location`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rate` (`location_id`,`education_level`,`academic_year`);

--
-- Chỉ mục cho bảng `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD KEY `idx_lecturers_faculty` (`faculty_id`),
  ADD KEY `idx_lecturers_name` (`full_name`);

--
-- Chỉ mục cho bảng `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location_code` (`location_code`);

--
-- Chỉ mục cho bảng `professions`
--
ALTER TABLE `professions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_profession` (`faculty_id`,`profession_code`,`level`),
  ADD KEY `idx_professions_faculty` (`faculty_id`);

--
-- Chỉ mục cho bảng `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subject` (`profession_id`,`subject_code`),
  ADD KEY `idx_subjects_profession` (`profession_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `hourly_rates_by_location`
--
ALTER TABLE `hourly_rates_by_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `professions`
--
ALTER TABLE `professions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_class_fk` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_ibfk_3` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_ibfk_4` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_ibfk_5` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_ibfk_6` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_ibfk_7` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_ibfk_8` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_location_fk` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hourly_rates_by_location`
--
ALTER TABLE `hourly_rates_by_location`
  ADD CONSTRAINT `hourly_rates_by_location_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `lecturers_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `professions`
--
ALTER TABLE `professions`
  ADD CONSTRAINT `professions_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
