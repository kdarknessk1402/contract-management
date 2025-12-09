# DANH MỤC TẤT CẢ FILE TRONG DỰ ÁN

## 📚 FILE HƯỚNG DẪN (ĐỌC TRƯỚC)

### 1. PROJECT_SUMMARY.md
**Mục đích**: Tổng quan toàn bộ dự án
**Nội dung**: 
- Giới thiệu tính năng
- Hướng dẫn cài đặt nhanh
- Thông tin đăng nhập
- Lưu ý quan trọng

### 2. INSTALL_GUIDE.md  
**Mục đích**: Hướng dẫn cài đặt CHI TIẾT từng bước
**Nội dung**:
- Cài XAMPP, Composer
- Cấu hình database
- Import dữ liệu
- Xử lý lỗi thường gặp

### 3. QUICKSTART.md
**Mục đích**: Hướng dẫn cài đặt NHANH 5 phút
**Nội dung**: 
- 6 bước cài đặt cơ bản
- Lệnh terminal cụ thể
- Troubleshooting nhanh

### 4. README.md
**Mục đích**: Tổng quan về hệ thống
**Nội dung**:
- Tính năng hệ thống
- Yêu cầu kỹ thuật
- Cấu trúc thư mục

---

## 🗄️ FILE DATABASE

### database.sql
**Mục đích**: Schema database MySQL
**Nội dung**:
- Tạo database: contract_management
- 8 bảng: users, faculties, professions, subjects, lecturers, contracts, hourly_rates, activity_logs
- Dữ liệu mẫu: user admin, mức thù lao, khoa mẫu
**Cách dùng**: Import vào phpMyAdmin

---

## ⚙️ FILE CẤU HÌNH

### config/database.php
**Mục đích**: Kết nối database
**Nội dung**:
- Class Database với PDO
- Host: localhost
- DB: contract_management
- User: root
- Pass: "" (trống)

### config/config.php
**Mục đích**: Cấu hình chung hệ thống
**Nội dung**:
- Session start
- Timezone: Asia/Ho_Chi_Minh
- Define paths: BASE_PATH, UPLOAD_PATH, EXPORT_PATH
- Helper functions: formatMoney(), formatDate(), numberToWords()

### composer.json
**Mục đích**: Quản lý PHP dependencies
**Nội dung**:
- phpoffice/phpspreadsheet: Xử lý Excel
- phpoffice/phpword: Tạo file DOCX
**Cách dùng**: `composer install`

---

## 🔐 FILE XÁC THỰC

### includes/auth.php
**Mục đích**: Class Auth xử lý đăng nhập
**Nội dung**:
- login(): Xác thực user
- logout(): Đăng xuất
- isLoggedIn(): Kiểm tra đã login
- isAdmin(): Kiểm tra quyền admin
- requireLogin(): Bắt buộc đăng nhập
- requireAdmin(): Bắt buộc admin

### login.php
**Mục đích**: Trang đăng nhập
**Nội dung**:
- Form login với username/password
- Validate credentials
- Redirect về index.php sau khi login
- Giao diện đẹp với gradient background

### logout.php
**Mục đích**: Xử lý đăng xuất
**Nội dung**:
- Session destroy
- Redirect về login.php

---

## 🎨 FILE GIAO DIỆN

### includes/header.php
**Mục đích**: Header HTML chung
**Nội dung**:
- Bootstrap 5, DataTables, Icons
- Navigation menu
- Phân quyền menu (Admin vs Giáo vụ)
- User dropdown

### includes/footer.php
**Mục đích**: Footer HTML chung
**Nội dung**:
- Copyright
- Load JS: jQuery, Bootstrap, DataTables
- Load custom JS: assets/js/main.js

### assets/css/style.css
**Mục đích**: CSS tùy chỉnh
**Nội dung**:
- Login page styling
- Dashboard cards
- DataTables custom
- Upload area
- Responsive design
- Print styles

### assets/js/main.js
**Mục đích**: JavaScript chung
**Nội dung**:
- Initialize DataTables
- Auto hide alerts
- Confirm delete
- Format currency
- Upload drag & drop
- Calculate contract total
- Load hourly rates
- Load subjects/professions

---

## 🏠 TRANG DASHBOARD

### index.php
**Mục đích**: Trang chủ/Dashboard
**Nội dung**:
- Thống kê: Số giảng viên, hợp đồng, tổng giá trị
- Cards màu sắc đẹp
- Bảng hợp đồng mới nhất
- Phân quyền theo khoa

---

## 👥 QUẢN LÝ GIẢNG VIÊN

### lecturers.php
**Mục đích**: Danh sách giảng viên
**Nội dung**:
- Bảng DataTable hiển thị giảng viên
- Nút Import Excel
- Nút Thêm/Sửa/Xóa
- Modal import với drag & drop
- Phân quyền theo khoa

### lecturer_import.php
**Mục đích**: Xử lý import Excel
**Nội dung**:
- Upload file Excel
- Validate dữ liệu (trình độ, giới tính, CCCD)
- Insert vào database
- Báo lỗi chi tiết từng dòng
- Transaction để đảm bảo data integrity

### uploads/templates/lecturer_template.xlsx
**Mục đích**: File Excel mẫu
**Nội dung**:
- Sheet 1: Danh sách giảng viên (có 2 dòng mẫu)
- Sheet 2: Hướng dẫn chi tiết
- 16 cột: Họ tên, Giới tính, Năm sinh, CCCD, Ngày cấp, Nơi cấp, Trình độ, Chuyên ngành, Sư phạm, Địa chỉ, Điện thoại, Email, STK, Ngân hàng, Chi nhánh, MST

---

## 📄 QUẢN LÝ HỢP ĐỒNG

### contracts.php
**Mục đích**: Danh sách hợp đồng
**Nội dung**:
- Bảng DataTable hiển thị hợp đồng
- Nút Tạo hợp đồng mới
- Nút In/Sửa/Xóa
- Status badges (Nháp, Đã duyệt, Hoàn thành, Đã hủy)
- Phân quyền theo khoa

### contract_create.php
**Mục đích**: Form tạo hợp đồng mới
**Nội dung**:
- Form 4 phần: Thông tin GV, Thù lao, Thời gian, Thông tin khác
- Dropdown chọn giảng viên
- Auto load mức thù lao theo trình độ
- Auto load môn học theo nghề
- Auto tính tổng tiền = Số giờ × Thù lao
- Auto chuyển số sang chữ
- Auto tạo số HĐ: 0001/HĐ-CĐN/2025
- Validate form

### contract_print.php
**Mục đích**: Xuất hợp đồng ra file DOCX
**Nội dung**:
- Sử dụng PhpWord để tạo DOCX
- Tạo header: Logo trường, CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM
- Điền thông tin giảng viên từ database
- Điền thông tin môn học, số giờ, thù lao
- Tạo footer: Chữ ký bên A, bên B
- Format giống file .doc mẫu
- Download về máy

---

## 🔌 FILE AJAX

### ajax/get_subjects.php
**Mục đích**: Lấy danh sách môn học theo nghề
**Input**: profession_id
**Output**: JSON array môn học
**Dùng trong**: contract_create.php

### ajax/get_hourly_rates.php
**Mục đích**: Lấy mức thù lao theo trình độ
**Input**: education_level, academic_year
**Output**: JSON array rates (standard, high)
**Dùng trong**: contract_create.php

### ajax/number_to_words.php
**Mục đích**: Chuyển số sang chữ tiếng Việt
**Input**: number (số tiền)
**Output**: Text (bốn triệu năm trăm nghìn đồng)
**Dùng trong**: contract_create.php

---

## 📊 CẤU TRÚC DATABASE

```
users                    # Người dùng
├── id
├── username
├── password (BCrypt)
├── role (admin/giao_vu)
└── faculty_id

faculties               # Khoa
├── id
├── faculty_code
└── faculty_name

professions            # Nghề
├── id
├── faculty_id
├── profession_code
├── profession_name
└── academic_year

subjects               # Môn học
├── id
├── profession_id
├── subject_code
├── subject_name
└── credit_hours

lecturers              # Giảng viên
├── id
├── faculty_id
├── full_name
├── gender
├── birth_year
├── id_number
├── education_level
├── major
├── phone
├── email
└── ... (16 trường)

contracts              # Hợp đồng
├── id
├── contract_number
├── lecturer_id
├── subject_id
├── total_hours
├── hourly_rate
├── total_amount
├── start_date
├── end_date
└── ... (15 trường)

hourly_rates           # Mức thù lao
├── id
├── education_level
├── rate_type (standard/high)
├── amount
└── academic_year

activity_logs          # Log hoạt động
├── id
├── user_id
├── action
└── created_at
```

---

## 🎯 WORKFLOW SỬ DỤNG

### ADMIN (làm trước):
1. Login: admin/admin123
2. Tạo Khoa → Nghề → Môn học
3. Tạo tài khoản Giáo vụ cho mỗi khoa
4. Kiểm tra mức thù lao

### GIÁO VỤ (làm sau):
1. Login với tài khoản được cấp
2. Import giảng viên từ Excel
3. Tạo hợp đồng
4. In hợp đồng ra DOCX

---

## 📦 FILE CẦN TẢI VỀ

### File chính:
✅ **contract-management.tar.gz** - Toàn bộ source code (đã đóng gói)

### Hoặc download từng file/folder:
✅ **config/** - Cấu hình
✅ **includes/** - Header, footer, auth
✅ **assets/** - CSS, JS
✅ **ajax/** - AJAX handlers
✅ **uploads/templates/** - File Excel mẫu
✅ **database.sql** - Database
✅ **composer.json** - Dependencies
✅ Các file .php chính

---

## 🚀 CÀI ĐẶT

### Bước 1: Extract file
```bash
tar -xzf contract-management.tar.gz
Copy vào: C:\xampp\htdocs\
```

### Bước 2: Install dependencies
```bash
cd C:\xampp\htdocs\contract-management
composer install
```

### Bước 3: Import database
```
Mở: http://localhost/phpmyadmin
Import file: database.sql
```

### Bước 4: Truy cập
```
URL: http://localhost/contract-management/
Login: admin / admin123
```

---

## ⚠️ LƯU Ý

1. **Phải cài Composer trước** khi chạy `composer install`
2. **Phải import database.sql** vào phpMyAdmin
3. **Các folder cần có quyền ghi**: uploads/, exports/, backups/
4. **File Excel mẫu** đã có sẵn trong uploads/templates/
5. **Đọc INSTALL_GUIDE.md** để biết chi tiết từng bước

---

© 2025 Trường Cao đẳng nghề TP. Hồ Chí Minh
