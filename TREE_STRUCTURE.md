# CẤU TRÚC CÂY THƯ MỤC CHI TIẾT

```
contract-management/                    # Thư mục gốc dự án
│
├── 📄 .htaccess                        # Bảo mật Apache, URL rewrite
├── 📄 .gitignore                       # Git ignore files
├── 📄 composer.json                    # PHP dependencies
│
├── 📚 FILE_INDEX.md                    # Danh mục tất cả file
├── 📚 INSTALL_GUIDE.md                 # Hướng dẫn cài đặt chi tiết
├── 📚 PROJECT_SUMMARY.md               # Tổng quan dự án
├── 📚 QUICKSTART.md                    # Hướng dẫn cài đặt nhanh
├── 📚 README.md                        # Tổng quan hệ thống
├── 📚 TREE_STRUCTURE.md                # File này - Cấu trúc cây
│
├── 🗄️ database.sql                     # Schema database MySQL
│
├── 🏠 index.php                        # Trang chủ/Dashboard
├── 🔐 login.php                        # Trang đăng nhập
├── 🔐 logout.php                       # Xử lý đăng xuất
│
├── 👥 lecturers.php                    # Danh sách giảng viên
├── 👥 lecturer_import.php              # Xử lý import Excel giảng viên
│
├── 📄 contracts.php                    # Danh sách hợp đồng
├── 📄 contract_create.php              # Form tạo hợp đồng mới
├── 📄 contract_print.php               # Xuất hợp đồng ra DOCX
│
├── 📁 config/                          # Thư mục cấu hình
│   ├── config.php                      # Cấu hình chung (paths, helpers, timezone)
│   └── database.php                    # Class Database - Kết nối PDO
│
├── 📁 includes/                        # Thư mục include chung
│   ├── auth.php                        # Class Auth - Xác thực người dùng
│   ├── header.php                      # Header HTML (navbar, menu)
│   ├── footer.php                      # Footer HTML (scripts)
│   └── helpers.php                     # Helper functions (nếu có)
│
├── 📁 ajax/                            # Thư mục AJAX endpoints
│   ├── get_subjects.php                # Lấy môn học theo nghề (JSON)
│   ├── get_hourly_rates.php            # Lấy mức thù lao theo trình độ (JSON)
│   └── number_to_words.php             # Chuyển số sang chữ tiếng Việt
│
├── 📁 assets/                          # Thư mục tài nguyên tĩnh
│   ├── css/
│   │   └── style.css                   # CSS tùy chỉnh (login, dashboard, tables)
│   ├── js/
│   │   └── main.js                     # JavaScript chính (DataTables, AJAX, events)
│   └── images/                         # Thư mục hình ảnh (trống)
│
├── 📁 uploads/                         # Thư mục upload
│   ├── lecturers/                      # File Excel import giảng viên
│   │   └── .gitkeep                    # Giữ thư mục trong Git
│   └── templates/                      # File mẫu
│       └── lecturer_template.xlsx     # ⭐ File Excel mẫu import (2 sheets)
│
├── 📁 exports/                         # Thư mục xuất file tạm thời
│   └── .gitkeep                        # Giữ thư mục trong Git
│
├── 📁 backups/                         # Thư mục backup database
│   └── .gitkeep                        # Giữ thư mục trong Git
│
└── 📁 vendor/                          # Composer packages (tự động tạo)
    ├── phpoffice/
    │   ├── phpspreadsheet/             # Thư viện xử lý Excel
    │   └── phpword/                    # Thư viện tạo DOCX
    └── autoload.php                    # Composer autoloader
```

---

## 📊 TỔNG KẾT

### Tổng số file: **30 files**

#### File hướng dẫn: **6 files**
- FILE_INDEX.md
- INSTALL_GUIDE.md
- PROJECT_SUMMARY.md
- QUICKSTART.md
- README.md
- TREE_STRUCTURE.md (file này)

#### File cấu hình: **4 files**
- .htaccess
- .gitignore
- composer.json
- database.sql

#### File PHP chính: **9 files**
- index.php
- login.php
- logout.php
- lecturers.php
- lecturer_import.php
- contracts.php
- contract_create.php
- contract_print.php
- (includes/helpers.php)

#### File PHP modules: **6 files**
- config/config.php
- config/database.php
- includes/auth.php
- includes/header.php
- includes/footer.php
- includes/helpers.php

#### File AJAX: **3 files**
- ajax/get_subjects.php
- ajax/get_hourly_rates.php
- ajax/number_to_words.php

#### File assets: **2 files**
- assets/css/style.css
- assets/js/main.js

#### File Excel mẫu: **1 file**
- uploads/templates/lecturer_template.xlsx

#### File gitkeep: **3 files**
- backups/.gitkeep
- exports/.gitkeep
- uploads/lecturers/.gitkeep

---

## 🎯 CHI TIẾT TỪNG FILE

### 📚 **FILE HƯỚNG DẪN**

#### FILE_INDEX.md
```
Mục đích: Danh mục và mô tả tất cả file trong dự án
Nội dung: Liệt kê 42 file, giải thích từng file làm gì
```

#### INSTALL_GUIDE.md
```
Mục đích: Hướng dẫn cài đặt chi tiết từng bước
Nội dung:
- Cài XAMPP (Apache + MySQL + PHP)
- Cài Composer
- Copy code
- Cài dependencies: composer install
- Import database.sql
- Cấu hình
- Troubleshooting
Độ dài: ~300 dòng
```

#### PROJECT_SUMMARY.md
```
Mục đích: Tổng quan dự án, tính năng
Nội dung:
- Giới thiệu hệ thống
- Tính năng Admin vs Giáo vụ
- Mức thù lao
- Cài đặt nhanh
- Điểm nổi bật
Độ dài: ~200 dòng
```

#### QUICKSTART.md
```
Mục đích: Hướng dẫn cài đặt nhanh 5 phút
Nội dung:
- 6 bước cài đặt cơ bản
- Lệnh terminal cụ thể
- Xử lý lỗi nhanh
Độ dài: ~100 dòng
```

#### README.md
```
Mục đích: Tổng quan về hệ thống
Nội dung:
- Tính năng
- Yêu cầu kỹ thuật
- Cấu trúc thư mục
- Hướng dẫn sử dụng
Độ dài: ~250 dòng
```

#### TREE_STRUCTURE.md (File này)
```
Mục đích: Cấu trúc cây thư mục chi tiết
Nội dung: Hiển thị cây thư mục, mô tả từng file
```

---

### ⚙️ **FILE CẤU HÌNH**

#### .htaccess
```
Mục đích: Cấu hình Apache
Nội dung:
- URL rewrite (ẩn .php)
- Bảo mật (chặn truy cập config/)
- Compression
- Cache
- Disable directory listing
```

#### .gitignore
```
Mục đích: Git ignore
Nội dung:
- Ignore vendor/
- Ignore uploads/lecturers/*
- Ignore exports/*
- Ignore .env, .idea, .DS_Store
```

#### composer.json
```json
{
    "require": {
        "phpoffice/phpspreadsheet": "^1.29",
        "phpoffice/phpword": "^1.2"
    }
}
```

#### database.sql
```sql
Mục đích: Schema database MySQL
Nội dung:
- CREATE DATABASE contract_management
- CREATE TABLE users (8 bảng)
- INSERT user admin
- INSERT hourly_rates (6 mức)
- INSERT faculties (3 khoa mẫu)
Độ dài: ~150 dòng
```

---

### 🏠 **FILE PHP CHÍNH**

#### index.php
```
Mục đích: Trang chủ/Dashboard
Features:
- Require login
- Thống kê: Tổng GV, HĐ, Giá trị
- Cards màu sắc
- Bảng HĐ mới nhất
- Phân quyền theo khoa
Dependencies: auth.php, database.php, header.php, footer.php
```

#### login.php
```
Mục đích: Trang đăng nhập
Features:
- Form username/password
- Xác thực qua Auth class
- BCrypt password
- Redirect sau login
- Giao diện gradient đẹp
Dependencies: config.php, auth.php
```

#### logout.php
```
Mục đích: Đăng xuất
Features:
- Session destroy
- Redirect về login
Dependencies: config.php, auth.php
```

#### lecturers.php
```
Mục đích: Danh sách giảng viên
Features:
- DataTable search/sort/paginate
- Nút Import Excel
- Nút Add/Edit/Delete
- Modal import với drag & drop
- Phân quyền theo khoa
Dependencies: auth.php, database.php, header.php, footer.php
```

#### lecturer_import.php
```
Mục đích: Xử lý import Excel
Features:
- Upload file .xlsx
- Validate (trình độ, giới tính, CCCD)
- PhpSpreadsheet đọc Excel
- Insert vào database
- Báo lỗi chi tiết từng dòng
- Transaction
Dependencies: config.php, auth.php, database.php, PhpSpreadsheet
```

#### contracts.php
```
Mục đích: Danh sách hợp đồng
Features:
- DataTable hiển thị HĐ
- Nút Create/Print/Edit/Delete
- Status badges
- Phân quyền theo khoa
Dependencies: auth.php, database.php, header.php, footer.php
```

#### contract_create.php
```
Mục đích: Form tạo hợp đồng
Features:
- Form 4 phần
- Auto load thù lao theo trình độ
- Auto load môn học theo nghề
- Auto tính tổng = giờ × thù lao
- Auto chuyển số sang chữ
- Auto tạo số HĐ
- Validate
Dependencies: auth.php, database.php, header.php, footer.php
```

#### contract_print.php
```
Mục đích: Xuất HĐ ra DOCX
Features:
- PhpWord tạo .docx
- Header: Logo trường, CHXHCNVN
- Điền data từ DB
- Footer: Chữ ký
- Format giống file .doc mẫu
- Download file
Dependencies: auth.php, database.php, PhpWord
```

---

### 📁 **FILE CONFIG/**

#### config/database.php
```php
Class Database {
    - host: localhost
    - db_name: contract_management
    - username: root
    - password: ""
    - getConnection(): PDO
}
```

#### config/config.php
```php
Functions:
- session_start()
- date_default_timezone_set('Asia/Ho_Chi_Minh')
- define BASE_PATH, UPLOAD_PATH, EXPORT_PATH
- isLoggedIn()
- isAdmin()
- redirect($url)
- formatMoney($amount)
- formatDate($date)
- numberToWords($number) // Số sang chữ tiếng Việt
```

---

### 📁 **FILE INCLUDES/**

#### includes/auth.php
```php
Class Auth {
    - login($username, $password)
    - logout()
    - isLoggedIn()
    - isAdmin()
    - requireLogin()
    - requireAdmin()
}
```

#### includes/header.php
```html
- <!DOCTYPE html>
- Bootstrap 5 CSS
- DataTables CSS
- Custom CSS
- Navigation bar
- Phân quyền menu (Admin vs Giáo vụ)
- User dropdown
```

#### includes/footer.php
```html
- </main>
- Footer copyright
- jQuery, Bootstrap JS
- DataTables JS
- Custom JS (main.js)
- </body></html>
```

#### includes/helpers.php
```php
(File trống hoặc các helper functions bổ sung)
```

---

### 📁 **FILE AJAX/**

#### ajax/get_subjects.php
```php
Input: POST profession_id
Output: JSON array subjects
Query: SELECT * FROM subjects WHERE profession_id = ?
```

#### ajax/get_hourly_rates.php
```php
Input: POST education_level, academic_year
Output: JSON array rates (standard, high)
Query: SELECT * FROM hourly_rates WHERE ...
```

#### ajax/number_to_words.php
```php
Input: POST number
Output: Text "bốn triệu năm trăm nghìn đồng"
Function: numberToWords($number)
```

---

### 📁 **FILE ASSETS/**

#### assets/css/style.css
```css
Styles:
- Login page (gradient background, card)
- Dashboard cards (hover effect)
- DataTables custom
- Upload area (drag & drop)
- Buttons, forms
- Alerts, badges
- Responsive
- Print styles
```

#### assets/js/main.js
```javascript
Functions:
- Initialize DataTables
- Auto hide alerts
- Confirm delete
- Format currency
- Drag & drop upload
- Calculate contract total
- Load hourly rates (AJAX)
- Load subjects (AJAX)
- Load professions (AJAX)
```

---

### 📁 **FILE UPLOADS/**

#### uploads/templates/lecturer_template.xlsx
```
Sheet 1: Danh sách giảng viên
- Headers: 16 cột
- Row 2-3: Dữ liệu mẫu

Sheet 2: Hướng dẫn
- 10 điều hướng dẫn chi tiết
```

---

## 📥 CÁCH TẢI VỀ

### Tải toàn bộ:
1. Download file nén: contract-management.tar.gz
2. Extract: tar -xzf contract-management.tar.gz
3. Copy vào: C:\xampp\htdocs\

### Hoặc tải từng file:
1. Mở thư mục: contract-management/
2. Tải từng file theo cây thư mục trên
3. Giữ đúng cấu trúc thư mục

---

## ⚠️ LƯU Ý QUAN TRỌNG

### Thư mục bắt buộc phải có:
```
config/
includes/
ajax/
assets/css/
assets/js/
uploads/templates/
uploads/lecturers/
exports/
backups/
```

### File bắt buộc phải có:
```
database.sql
composer.json
index.php
login.php
config/database.php
config/config.php
includes/auth.php
uploads/templates/lecturer_template.xlsx
```

### Sau khi copy:
```bash
cd C:\xampp\htdocs\contract-management
composer install  # Tạo thư mục vendor/
```

### Import database:
```
phpMyAdmin > SQL > Copy database.sql > Go
```

---

© 2025 Trường Cao đẳng nghề TP. Hồ Chí Minh
