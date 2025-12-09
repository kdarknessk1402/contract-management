# HƯỚNG DẪN CÀI ĐẶT VÀ SỬ DỤNG HỆ THỐNG QUẢN LÝ HỢP ĐỒNG THỈNH GIẢNG

## MỤC LỤC
1. [Giới thiệu](#giới-thiệu)
2. [Cài đặt hệ thống](#cài-đặt-hệ-thống)
3. [Cấu trúc dự án](#cấu-trúc-dự-án)
4. [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)
5. [Xử lý lỗi](#xử-lý-lỗi)

---

## GIỚI THIỆU

Hệ thống quản lý hợp đồng thỉnh giảng được thiết kế cho Trường Cao đẳng nghề TP.HCM với các tính năng:

### Tính năng chính:
✅ **Quản lý giảng viên**: Import từ Excel, thêm/sửa/xóa
✅ **Tạo hợp đồng**: Chọn giảng viên, môn học, tự động tính thù lao
✅ **In hợp đồng**: Xuất ra file DOCX giữ nguyên định dạng mẫu (header, footer)
✅ **Phân quyền**: Admin quản trị hệ thống, Giáo vụ quản lý theo khoa
✅ **Tự động hóa**: Tự động tạo số HĐ, tính tổng tiền, chuyển số sang chữ

### Quy tắc hệ thống:
- Giáo vụ chỉ thấy dữ liệu của khoa mình
- Admin thấy tất cả dữ liệu
- Thù lao theo trình độ:
  - Đại học: 70,000đ - 90,000đ
  - Thạc sĩ: 75,000đ - 90,000đ
  - Tiến sĩ: 90,000đ - 100,000đ

---

## CÀI ĐẶT HỆ THỐNG

### Bước 1: Cài đặt XAMPP (Web Server + MySQL)

#### 1.1. Tải XAMPP
- Truy cập: https://www.apachefriends.org/
- Tải phiên bản mới nhất cho Windows
- Kích thước: ~150MB

#### 1.2. Cài đặt XAMPP
```
1. Chạy file xampp-windows-x64-8.x.x-installer.exe
2. Chọn thư mục cài đặt: C:\xampp
3. Chọn các component:
   ✓ Apache
   ✓ MySQL
   ✓ PHP
   ✓ phpMyAdmin
4. Nhấn Next > Install > Finish
```

#### 1.3. Khởi động Services
```
1. Mở XAMPP Control Panel
2. Nhấn Start cho Apache
3. Nhấn Start cho MySQL
4. Đảm bảo 2 services có màu xanh
```

### Bước 2: Cài đặt Composer (PHP Package Manager)

#### 2.1. Tải Composer
- Truy cập: https://getcomposer.org/download/
- Tải file: Composer-Setup.exe
- Kích thước: ~2MB

#### 2.2. Cài đặt Composer
```
1. Chạy file Composer-Setup.exe
2. Chọn "Install for all users"
3. Chọn đường dẫn PHP:
   C:\xampp\php\php.exe
4. Nhấn Next > Install > Finish
```

#### 2.3. Kiểm tra Composer
Mở Command Prompt (trong VS Code: Ctrl+`):
```bash
composer -V
```
Nếu thấy: `Composer version x.x.x` → Thành công!

### Bước 3: Copy dự án vào XAMPP

#### 3.1. Vị trí đặt code
```
Copy thư mục contract-management vào:
C:\xampp\htdocs\

Đường dẫn đầy đủ:
C:\xampp\htdocs\contract-management\
```

#### 3.2. Kiểm tra cấu trúc
```
C:\xampp\htdocs\contract-management\
├── config/
├── includes/
├── assets/
├── ajax/
├── uploads/
├── index.php
├── login.php
├── database.sql
├── composer.json
└── README.md
```

### Bước 4: Cài đặt PHP Dependencies

#### 4.1. Mở Terminal trong VS Code
```
Menu: Terminal > New Terminal
Hoặc: Ctrl+`
```

#### 4.2. Di chuyển vào thư mục dự án
```bash
cd C:\xampp\htdocs\contract-management
```

#### 4.3. Cài đặt packages qua Composer
```bash
composer install
```

**Lưu ý**: Quá trình này sẽ tải và cài:
- phpoffice/phpspreadsheet (xử lý Excel)
- phpoffice/phpword (tạo file DOCX)

**Thời gian**: Khoảng 1-2 phút

**Nếu lỗi "composer not found"**, dùng đường dẫn đầy đủ:
```bash
C:\ProgramData\ComposerSetup\bin\composer.phar install
```

#### 4.4. Kiểm tra kết quả
Sau khi hoàn tất, kiểm tra thư mục `vendor/` đã được tạo:
```
C:\xampp\htdocs\contract-management\vendor\
```

### Bước 5: Tạo Database

#### 5.1. Mở phpMyAdmin
```
Truy cập: http://localhost/phpmyadmin
```

#### 5.2. Import database
```
1. Nhấn tab "SQL" ở menu trên
2. Mở file database.sql trong dự án
3. Copy TOÀN BỘ nội dung file
4. Paste vào ô "Run SQL query"
5. Nhấn nút "Go" ở góc dưới bên phải
```

#### 5.3. Kiểm tra kết quả
```
- Bên trái sẽ xuất hiện database: contract_management
- Click vào sẽ thấy các bảng:
  ✓ users
  ✓ faculties
  ✓ professions
  ✓ subjects
  ✓ lecturers
  ✓ contracts
  ✓ hourly_rates
  ✓ activity_logs
```

### Bước 6: Cấu hình hệ thống

#### 6.1. Kiểm tra kết nối database
Mở file: `config/database.php`

```php
private $host = "localhost";
private $db_name = "contract_management";
private $username = "root";
private $password = "";  // XAMPP mặc định không có password
```

**Lưu ý**: Nếu MySQL có password, sửa dòng password.

#### 6.2. Tạo thư mục cần thiết
Đảm bảo các thư mục sau tồn tại:
```
uploads/lecturers/
uploads/templates/
exports/
backups/
```

Nếu chưa có, tạo trong File Explorer hoặc:
```bash
mkdir uploads\lecturers
mkdir uploads\templates  
mkdir exports
mkdir backups
```

#### 6.3. Cấp quyền ghi (nếu cần)
```
1. Chuột phải vào từng thư mục
2. Properties > Security
3. Chọn Users > Edit
4. Check "Full Control"
5. Apply > OK
```

### Bước 7: Truy cập hệ thống

#### 7.1. Mở trình duyệt
```
URL: http://localhost/contract-management/
```

#### 7.2. Đăng nhập
```
Username: admin
Password: admin123
```

#### 7.3. Kiểm tra
Sau khi đăng nhập thành công, bạn sẽ thấy:
- Dashboard với thống kê
- Menu điều hướng
- Các chức năng quản trị

---

## CẤU TRÚC DỰ ÁN

### Thư mục chính
```
contract-management/
│
├── config/                    # Cấu hình hệ thống
│   ├── config.php            # Cấu hình chung (timezone, paths, helpers)
│   └── database.php          # Kết nối database
│
├── includes/                  # File include chung
│   ├── auth.php              # Xác thực người dùng
│   ├── header.php            # Header HTML
│   └── footer.php            # Footer HTML
│
├── assets/                    # Tài nguyên tĩnh
│   ├── css/
│   │   └── style.css         # CSS tùy chỉnh
│   ├── js/
│   │   └── main.js           # JavaScript chính
│   └── images/               # Hình ảnh
│
├── ajax/                      # AJAX endpoints
│   ├── get_subjects.php      # Lấy môn học theo nghề
│   ├── get_hourly_rates.php  # Lấy mức thù lao theo trình độ
│   └── number_to_words.php   # Chuyển số sang chữ
│
├── uploads/                   # File upload
│   ├── lecturers/            # File import giảng viên
│   └── templates/            # File mẫu Excel
│       └── lecturer_template.xlsx
│
├── exports/                   # File export (tạm thời)
│
├── backups/                   # File backup database
│
├── vendor/                    # Composer packages (tự động tạo)
│
├── Main Pages                 # Các trang chính
│   ├── index.php             # Dashboard
│   ├── login.php             # Đăng nhập
│   ├── logout.php            # Đăng xuất
│   ├── lecturers.php         # Danh sách giảng viên
│   ├── lecturer_import.php   # Import giảng viên
│   ├── contracts.php         # Danh sách hợp đồng
│   ├── contract_create.php   # Tạo hợp đồng
│   └── contract_print.php    # In hợp đồng (DOCX)
│
└── Config Files
    ├── composer.json          # Composer dependencies
    ├── database.sql           # Database schema
    ├── README.md             # Hướng dẫn chi tiết
    └── QUICKSTART.md         # Hướng dẫn nhanh
```

### Database Schema

```sql
users               # Người dùng (Admin, Giáo vụ)
├── id
├── username
├── password
├── role (admin/giao_vu)
└── faculty_id

faculties           # Khoa
├── id
├── faculty_code
└── faculty_name

professions         # Nghề
├── id
├── faculty_id
├── profession_code
├── profession_name
└── academic_year

subjects            # Môn học
├── id
├── profession_id
├── subject_code
├── subject_name
└── credit_hours

lecturers           # Giảng viên
├── id
├── faculty_id
├── full_name
├── education_level
├── id_number
└── ... (các thông tin khác)

contracts           # Hợp đồng
├── id
├── contract_number
├── lecturer_id
├── subject_id
├── total_hours
├── hourly_rate
├── total_amount
└── ...

hourly_rates        # Mức thù lao
├── id
├── education_level
├── rate_type (standard/high)
├── amount
└── academic_year
```

---

## HƯỚNG DẪN SỬ DỤNG

### PHẦN 1: Thiết lập ban đầu (Admin)

#### 1.1. Quản lý Khoa

**Bước 1**: Đăng nhập với tài khoản `admin`

**Bước 2**: Truy cập menu `Quản trị > Quản lý Khoa`

**Bước 3**: Thêm các khoa
```
Ví dụ:
- Mã khoa: CNTT
  Tên khoa: Khoa Công nghệ Thông tin

- Mã khoa: CK
  Tên khoa: Khoa Cơ khí

- Mã khoa: DL
  Tên khoa: Khoa Du lịch - Nhà hàng - Khách sạn
```

#### 1.2. Quản lý Nghề

**Bước 1**: Truy cập `Quản trị > Quản lý Nghề`

**Bước 2**: Nhấn "Thêm nghề"

**Bước 3**: Điền thông tin
```
- Chọn khoa: CNTT
- Mã nghề: CNTT_01
- Tên nghề: Lập trình viên
- Niên khóa: 2025-2026
```

**Bước 4**: Thêm tất cả các nghề của từng khoa

#### 1.3. Quản lý Môn học

**Bước 1**: Truy cập `Quản trị > Quản lý Môn học`

**Bước 2**: Thêm môn học cho từng nghề
```
Ví dụ:
- Chọn nghề: Lập trình viên (CNTT_01)
- Mã môn: MH001
- Tên môn: Lập trình C#
- Số giờ: 60
```

#### 1.4. Tạo tài khoản Giáo vụ

**Bước 1**: Truy cập `Quản trị > Quản lý người dùng`

**Bước 2**: Nhấn "Thêm người dùng"

**Bước 3**: Điền thông tin
```
- Tên đăng nhập: giaovu_cntt
- Mật khẩu: password123
- Họ tên: Nguyễn Văn A
- Email: giaovu@cdnhcm.edu.vn
- Vai trò: Giáo vụ
- Khoa: Khoa Công nghệ Thông tin
```

**Lưu ý**: Mỗi khoa nên có ít nhất 1 tài khoản Giáo vụ

#### 1.5. Kiểm tra mức thù lao

**Bước 1**: Truy cập `Quản trị > Cấu hình thù lao`

**Bước 2**: Kiểm tra các mức thù lao mặc định:
```
Đại học:
- Mức chuẩn: 70,000đ
- Mức cao: 90,000đ

Thạc sĩ:
- Mức chuẩn: 75,000đ
- Mức cao: 90,000đ

Tiến sĩ:
- Mức chuẩn: 90,000đ
- Mức cao: 100,000đ
```

**Bước 3**: Chỉnh sửa nếu cần (theo quy định mới)

---

### PHẦN 2: Import giảng viên (Giáo vụ)

#### 2.1. Tải file mẫu Excel

**Bước 1**: Đăng nhập với tài khoản Giáo vụ

**Bước 2**: Truy cập `Quản lý giảng viên`

**Bước 3**: Nhấn nút "Import Excel"

**Bước 4**: Tải file mẫu `lecturer_template.xlsx`

#### 2.2. Điền thông tin vào Excel

**Mở file Excel**, bạn sẽ thấy 2 sheet:
- `Danh sách giảng viên`: Chứa dữ liệu mẫu
- `Hướng dẫn`: Hướng dẫn chi tiết

**Các cột bắt buộc** (có dấu *):
1. **Họ và tên**: Nguyễn Văn A
2. **Giới tính**: Nam hoặc Nữ (chính xác)
3. **Số CCCD**: 001085001234 (12 số)
4. **Trình độ**: Đại học, Thạc sĩ, hoặc Tiến sĩ (chính xác)

**Các cột tùy chọn**:
5. Năm sinh: 1985
6. Ngày cấp: 01/01/2015 (dd/mm/yyyy)
7. Nơi cấp: CA TP.HCM
8. Chuyên ngành: Công nghệ thông tin
9. Sư phạm: Có
10. Địa chỉ: 123 Nguyễn Huệ, Q.1, TP.HCM
11. Điện thoại: 0901234567
12. Email: nguyenvana@email.com
13. Số tài khoản: 1234567890
14. Tên ngân hàng: Vietcombank
15. Chi nhánh: CN TP.HCM
16. Mã số thuế: 001085001234 (mặc định = Số CCCD)

**Ví dụ 1 dòng dữ liệu**:
```
Nguyễn Văn A | Nam | 1985 | 001085001234 | 01/01/2015 | CA TP.HCM | Thạc sĩ | ...
```

#### 2.3. Upload file

**Bước 1**: Xóa 2 dòng dữ liệu mẫu trong Excel

**Bước 2**: Lưu file với định dạng `.xlsx`

**Bước 3**: Quay lại trang Import, nhấn "Chọn file" hoặc kéo thả file vào

**Bước 4**: Nhấn "Upload và Import"

**Kết quả**:
- Thành công: "Import thành công X giảng viên!"
- Lỗi: Hệ thống sẽ liệt kê các lỗi cụ thể

**Các lỗi thường gặp**:
- ❌ Trình độ không đúng → Phải là "Đại học", "Thạc sĩ", "Tiến sĩ"
- ❌ Giới tính không đúng → Phải là "Nam" hoặc "Nữ"
- ❌ Số CCCD đã tồn tại → Trùng với giảng viên khác
- ❌ Thiếu thông tin bắt buộc → Kiểm tra các cột có dấu *

---

### PHẦN 3: Tạo hợp đồng (Giáo vụ)

#### 3.1. Tạo hợp đồng mới

**Bước 1**: Truy cập `Quản lý hợp đồng`

**Bước 2**: Nhấn "Tạo hợp đồng mới"

#### 3.2. Điền thông tin hợp đồng

**PHẦN 1: Thông tin giảng viên**

1. **Chọn giảng viên**:
   - Chọn từ danh sách dropdown
   - Hệ thống tự động lấy trình độ của giảng viên
   - VD: Nguyễn Văn A - Thạc sĩ

2. **Chọn nghề**:
   - Chọn nghề đào tạo
   - VD: CNTT_01 - Lập trình viên

3. **Chọn môn học**:
   - Sau khi chọn nghề, danh sách môn học sẽ tự động load
   - VD: MH001 - Lập trình C#

4. **Nhập mã lớp**:
   - VD: CNTT21A

**PHẦN 2: Thông tin thù lao**

5. **Nhập tổng số giờ**:
   - VD: 60

6. **Chọn mức thù lao**:
   - Sau khi chọn giảng viên, hệ thống tự động load mức thù lao theo trình độ
   - VD với Thạc sĩ:
     - Mức chuẩn: 75,000đ
     - Mức cao: 90,000đ
   - Chọn 1 trong 2 mức

7. **Tổng tiền**:
   - Tự động tính: Số giờ x Thù lao/giờ
   - VD: 60 x 75,000 = 4,500,000 đồng
   - Tự động chuyển sang chữ: "Bốn triệu năm trăm nghìn đồng"

**PHẦN 3: Thời gian thực hiện**

8. **Ngày bắt đầu**: 01/01/2026

9. **Ngày kết thúc**: 28/02/2026

**PHẦN 4: Thông tin khác**

10. **Năm học**: 2025-2026

11. **Học kỳ**: Học kỳ II

12. **Ngày ký hợp đồng**: 15/12/2025 (mặc định là hôm nay)

#### 3.3. Lưu hợp đồng

**Bước 1**: Kiểm tra lại toàn bộ thông tin

**Bước 2**: Nhấn nút "Lưu hợp đồng"

**Kết quả**:
- Hệ thống tự động tạo số HĐ: `0001/HĐ-CĐN/2025`
- Hiển thị thông báo: "Tạo hợp đồng thành công! Số HĐ: 0001/HĐ-CĐN/2025"
- Chuyển về trang danh sách hợp đồng

#### 3.4. In hợp đồng

**Bước 1**: Trong danh sách hợp đồng, tìm hợp đồng vừa tạo

**Bước 2**: Nhấn nút "In" (icon máy in)

**Kết quả**:
- Tự động tải file DOCX về máy
- Tên file: `HD_0001-HĐ-CĐN-2025_xxxxx.docx`
- File giữ nguyên định dạng như mẫu:
  ✅ Header với thông tin trường
  ✅ Tiêu đề HỢP ĐỒNG THỈNH GIẢNG
  ✅ Các điều khoản đầy đủ
  ✅ Thông tin giảng viên từ database
  ✅ Thông tin môn học, lớp, số giờ
  ✅ Thù lao chi tiết
  ✅ Footer với chữ ký

**Bước 3**: Mở file DOCX bằng Microsoft Word

**Bước 4**: Kiểm tra và in ra giấy để ký

---

## XỬ LÝ LỖI

### 1. Lỗi cài đặt Composer

**Triệu chứng**:
```
'composer' is not recognized as an internal or external command
```

**Nguyên nhân**: Composer chưa được thêm vào PATH

**Giải pháp 1**: Thêm vào PATH thủ công
```
1. Tìm folder Composer (thường là C:\ProgramData\ComposerSetup\bin)
2. Mở: This PC > Properties > Advanced system settings
3. Environment Variables > System variables > Path > Edit
4. New > Paste đường dẫn Composer
5. OK > Restart Command Prompt
```

**Giải pháp 2**: Dùng đường dẫn đầy đủ
```bash
C:\ProgramData\ComposerSetup\bin\composer.phar install
```

### 2. Lỗi PHP Extension

**Triệu chứng**:
```
Call to undefined function mb_strlen
Class 'ZipArchive' not found
```

**Nguyên nhân**: PHP extensions chưa được enable

**Giải pháp**:
```
1. Mở file: C:\xampp\php\php.ini
2. Tìm các dòng sau và bỏ dấu ; ở đầu:
   ;extension=mbstring    →  extension=mbstring
   ;extension=zip         →  extension=zip
   ;extension=gd          →  extension=gd
   ;extension=pdo_mysql   →  extension=pdo_mysql
3. Lưu file
4. Restart Apache trong XAMPP Control Panel
```

### 3. Lỗi quyền ghi file

**Triệu chứng**:
```
Failed to create file
Permission denied
```

**Nguyên nhân**: Thư mục không có quyền ghi

**Giải pháp**:
```
1. Chuột phải vào thư mục (uploads, exports, backups)
2. Properties > Security
3. Chọn Users > Edit
4. Check "Full Control"
5. Apply > OK
```

### 4. Lỗi kết nối MySQL

**Triệu chứng**:
```
SQLSTATE[HY000] [1045] Access denied
```

**Nguyên nhân**: Sai thông tin đăng nhập MySQL

**Giải pháp**:
```
Mở file: config/database.php

Kiểm tra:
- $username = "root"
- $password = ""  // XAMPP mặc định trống

Nếu MySQL có password, sửa dòng password
```

### 5. Lỗi Import Excel

**Triệu chứng**:
```
Dòng X: Trình độ không hợp lệ
Dòng Y: Giới tính phải là 'Nam' hoặc 'Nữ'
```

**Nguyên nhân**: Dữ liệu không đúng định dạng

**Giải pháp**:
```
Kiểm tra lại file Excel:
✓ Trình độ: Phải đúng "Đại học", "Thạc sĩ", hoặc "Tiến sĩ"
✓ Giới tính: Phải đúng "Nam" hoặc "Nữ"
✓ Số CCCD: Phải 12 số, không trùng
✓ Ngày cấp: Định dạng dd/mm/yyyy
```

### 6. Lỗi In hợp đồng

**Triệu chứng**:
```
Failed to load template
Cannot create file
```

**Nguyên nhân**: Folder exports không có quyền ghi hoặc thiếu dependencies

**Giải pháp**:
```
1. Kiểm tra folder exports/ tồn tại
2. Cấp quyền Full Control cho folder
3. Kiểm tra vendor/ đã cài đặt:
   composer install
4. Kiểm tra PHP memory:
   Mở php.ini, sửa:
   memory_limit = 256M
```

### 7. Lỗi đăng nhập

**Triệu chứng**:
```
Tên đăng nhập hoặc mật khẩu không đúng
```

**Giải pháp**:
```
1. Kiểm tra database đã import chưa
2. Thử đăng nhập:
   - Username: admin
   - Password: admin123

3. Nếu vẫn lỗi, reset password trong phpMyAdmin:
   UPDATE users 
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
   WHERE username = 'admin'
   
   Password mới: admin123
```

---

## LIÊN HỆ HỖ TRỢ

Nếu gặp vấn đề, vui lòng kiểm tra:

1. ✅ XAMPP Apache đang chạy (màu xanh)
2. ✅ XAMPP MySQL đang chạy (màu xanh)
3. ✅ Database contract_management đã import
4. ✅ Composer đã cài đặt thành công
5. ✅ Folder vendor/ đã tồn tại (sau composer install)
6. ✅ PHP extensions đã enable (mbstring, zip, gd, pdo_mysql)
7. ✅ Các folder uploads, exports, backups có quyền ghi

---

© 2025 Trường Cao đẳng nghề TP. Hồ Chí Minh
