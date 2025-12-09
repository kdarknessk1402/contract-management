# HỆ THỐNG QUẢN LÝ HỢP ĐỒNG THỈNH GIẢNG

Hệ thống quản lý hợp đồng thỉnh giảng cho Trường Cao đẳng nghề TP. HCM

## TÍNH NĂNG CHÍNH

### Dành cho GIÁO VỤ:
1. **Quản lý giảng viên thỉnh giảng**
   - Import danh sách giảng viên từ file Excel
   - Thêm/Sửa/Xóa thông tin giảng viên
   - Quản lý theo khoa

2. **Quản lý hợp đồng**
   - Tạo hợp đồng thỉnh giảng
   - Chọn giảng viên, môn học, số giờ
   - Tự động tính thù lao theo trình độ
   - In hợp đồng ra file DOCX (giữ nguyên định dạng như mẫu)
   - Xuất hợp đồng với header/footer đầy đủ

### Dành cho ADMIN:
1. **Quản lý danh mục**
   - Quản lý Khoa
   - Quản lý Nghề (theo niên khóa)
   - Quản lý Môn học (theo nghề)

2. **Quản lý hệ thống**
   - Quản lý người dùng (Admin, Giáo vụ)
   - Cấu hình thù lao theo trình độ
   - Sao lưu dữ liệu

## YÊU CẦU HỆ THỐNG

- **PHP**: >= 7.4
- **MySQL**: >= 5.7
- **Composer**: Để cài đặt dependencies
- **Web Server**: Apache/Nginx
- **Extensions PHP cần thiết**:
  - php-mbstring
  - php-zip
  - php-gd
  - php-xml
  - php-pdo
  - php-mysql

## HƯỚNG DẪN CÀI ĐẶT (WINDOWS 11)

### Bước 1: Cài đặt XAMPP
1. Tải XAMPP từ: https://www.apachefriends.org/
2. Cài đặt XAMPP vào `C:\xampp`
3. Khởi động Apache và MySQL trong XAMPP Control Panel

### Bước 2: Cài đặt Composer
1. Tải Composer từ: https://getcomposer.org/download/
2. Chạy file `Composer-Setup.exe`
3. Chọn đường dẫn PHP: `C:\xampp\php\php.exe`
4. Hoàn tất cài đặt

### Bước 3: Copy code vào XAMPP
1. Copy thư mục `contract-management` vào `C:\xampp\htdocs\`
2. Đường dẫn đầy đủ: `C:\xampp\htdocs\contract-management\`

### Bước 4: Cài đặt Dependencies PHP
Mở **Command Prompt** (hoặc PowerShell) trong VS Code:

```bash
# Di chuyển vào thư mục dự án
cd C:\xampp\htdocs\contract-management

# Cài đặt dependencies qua Composer
composer install
```

**Lưu ý**: Nếu lệnh `composer` không hoạt động, sử dụng đường dẫn đầy đủ:
```bash
C:\ProgramData\ComposerSetup\bin\composer.phar install
```

### Bước 5: Tạo Database
1. Mở trình duyệt, truy cập: `http://localhost/phpmyadmin`
2. Nhấn vào tab "SQL"
3. Copy toàn bộ nội dung file `database.sql` và paste vào
4. Nhấn nút "Go" để thực thi

### Bước 6: Cấu hình kết nối Database
Mở file `config/database.php`, kiểm tra thông tin kết nối:

```php
private $host = "localhost";
private $db_name = "contract_management";
private $username = "root";  
private $password = "";      // Mặc định XAMPP không có password
```

**Nếu MySQL có password**, sửa dòng `private $password = "your_password";`

### Bước 7: Tạo thư mục có quyền ghi
Tạo các thư mục sau và cấp quyền Full Control (nếu cần):

```
uploads/lecturers/
uploads/templates/
exports/
backups/
```

### Bước 8: Truy cập hệ thống
Mở trình duyệt và truy cập:
```
http://localhost/contract-management/
```

**Thông tin đăng nhập mặc định:**
- Username: `admin`
- Password: `admin123`

## CẤU TRÚC THƯ MỤC

```
contract-management/
├── config/                 # File cấu hình
│   ├── config.php         # Cấu hình chung
│   └── database.php       # Kết nối database
├── includes/              # File include
│   ├── auth.php           # Xác thực
│   ├── header.php         # Header
│   └── footer.php         # Footer
├── assets/                # Tài nguyên
│   ├── css/              # CSS
│   ├── js/               # JavaScript
│   └── images/           # Hình ảnh
├── ajax/                  # AJAX handlers
├── uploads/               # Upload files
│   ├── lecturers/        # File import
│   └── templates/        # File mẫu Excel
├── exports/               # File export
├── backups/               # File backup
├── vendor/                # Composer packages
├── *.php                  # Các trang PHP
├── composer.json          # Composer config
├── database.sql           # Database schema
└── README.md             # File này
```

## SỬ DỤNG HỆ THỐNG

### 1. Thiết lập ban đầu (Admin)

#### Bước 1: Quản lý Khoa
- Truy cập: **Quản trị > Quản lý Khoa**
- Thêm các khoa: CNTT, Cơ khí, Du lịch, v.v.

#### Bước 2: Quản lý Nghề
- Truy cập: **Quản trị > Quản lý Nghề**
- Thêm nghề cho từng khoa
- Chọn niên khóa: 2025-2026

#### Bước 3: Quản lý Môn học
- Truy cập: **Quản trị > Quản lý Môn học**
- Thêm các môn học cho từng nghề
- Nhập số giờ tín chỉ

#### Bước 4: Tạo tài khoản Giáo vụ
- Truy cập: **Quản trị > Quản lý người dùng**
- Tạo tài khoản cho giáo vụ mỗi khoa
- Chọn vai trò: **Giáo vụ**
- Gán khoa tương ứng

#### Bước 5: Kiểm tra mức thù lao
- Truy cập: **Quản trị > Cấu hình thù lao**
- Hệ thống đã có sẵn mức thù lao mặc định:
  - Đại học: 70,000đ (chuẩn), 90,000đ (cao)
  - Thạc sĩ: 75,000đ (chuẩn), 90,000đ (cao)
  - Tiến sĩ: 90,000đ (chuẩn), 100,000đ (cao)

### 2. Import giảng viên (Giáo vụ)

#### Bước 1: Tải file mẫu
- Truy cập: **Quản lý giảng viên**
- Nhấn nút **Import Excel**
- Tải file mẫu: `lecturer_template.xlsx`

#### Bước 2: Điền thông tin
Mở file Excel và điền các thông tin:
- **Họ và tên** (*): Bắt buộc
- **Giới tính** (*): Nam hoặc Nữ
- **Năm sinh**: Ví dụ: 1985
- **Số CCCD** (*): 12 số
- **Ngày cấp**: dd/mm/yyyy
- **Nơi cấp**: CA TP.HCM
- **Trình độ** (*): Đại học, Thạc sĩ, hoặc Tiến sĩ
- **Chuyên ngành**: Công nghệ thông tin
- **Sư phạm**: Có/Không
- **Địa chỉ**: Địa chỉ đầy đủ
- **Điện thoại**: 0901234567
- **Email**: email@example.com
- **Số tài khoản**: Số TK ngân hàng
- **Tên ngân hàng**: Vietcombank
- **Chi nhánh**: CN TP.HCM
- **Mã số thuế**: Mặc định = Số CCCD

#### Bước 3: Upload file
- Xóa dữ liệu mẫu trong Excel
- Lưu file với định dạng .xlsx
- Upload file vào hệ thống
- Hệ thống sẽ validate và import

### 3. Tạo hợp đồng (Giáo vụ)

#### Bước 1: Tạo hợp đồng mới
- Truy cập: **Quản lý hợp đồng**
- Nhấn **Tạo hợp đồng mới**

#### Bước 2: Nhập thông tin
1. **Chọn giảng viên**: Hệ thống sẽ tự động lấy trình độ
2. **Chọn nghề**: Chọn nghề đào tạo
3. **Chọn môn học**: Danh sách môn học sẽ load theo nghề
4. **Nhập mã lớp**: Ví dụ: CNTT21A
5. **Nhập số giờ**: Tổng số giờ giảng dạy
6. **Chọn mức thù lao**: Hệ thống tự động load theo trình độ
7. **Tổng tiền**: Tự động tính = Số giờ x Thù lao/giờ
8. **Ngày bắt đầu/kết thúc**: Thời gian giảng dạy
9. **Năm học**: 2025-2026
10. **Học kỳ**: Học kỳ I, II
11. **Ngày ký HĐ**: Ngày ký hợp đồng

#### Bước 3: Lưu và in hợp đồng
- Nhấn **Lưu hợp đồng**
- Hệ thống tự động tạo số HĐ: 0001/HĐ-CĐN/2025
- Nhấn nút **In** để xuất file DOCX
- File DOCX giữ nguyên định dạng như mẫu, bao gồm header, footer

## MẪU FILE EXCEL IMPORT

File mẫu đã được tạo sẵn tại: `uploads/templates/lecturer_template.xlsx`

Có 2 sheet:
1. **Danh sách giảng viên**: Sheet chứa dữ liệu mẫu
2. **Hướng dẫn**: Hướng dẫn chi tiết cách điền

## PHÂN QUYỀN HỆ THỐNG

### Vai trò Admin:
- Quản lý toàn bộ hệ thống
- Quản lý Khoa, Nghề, Môn học
- Quản lý người dùng
- Cấu hình thù lao
- Backup dữ liệu
- Xem tất cả dữ liệu

### Vai trò Giáo vụ:
- Quản lý giảng viên (của khoa mình)
- Import giảng viên từ Excel
- Tạo/In hợp đồng
- Xem hợp đồng (của khoa mình)

## TROUBLESHOOTING

### Lỗi: "Call to undefined function mb_strlen"
**Giải pháp**: Enable extension `php_mbstring` trong `php.ini`
```ini
extension=mbstring
```
Sau đó restart Apache

### Lỗi: "Class 'ZipArchive' not found"
**Giải pháp**: Enable extension `php_zip` trong `php.ini`
```ini
extension=zip
```
Sau đó restart Apache

### Lỗi: Cannot write to uploads/templates
**Giải pháp**: Cấp quyền Full Control cho thư mục:
- Chuột phải vào thư mục > Properties > Security
- Chọn Users > Edit > Check Full Control

### Lỗi: Composer not found
**Giải pháp**: Thêm Composer vào PATH:
1. Tìm folder cài Composer (thường là `C:\ProgramData\ComposerSetup\bin`)
2. Thêm vào PATH trong Environment Variables
3. Restart Command Prompt

### Lỗi khi in hợp đồng: "Failed to load template"
**Giải pháp**: 
1. Kiểm tra folder `exports/` có tồn tại
2. Cấp quyền ghi cho folder exports
3. Kiểm tra PHP có đủ memory: `memory_limit = 256M` trong php.ini

## HỖ TRỢ

Nếu gặp vấn đề, vui lòng kiểm tra:
1. PHP version: `php -v`
2. Composer version: `composer -V`
3. MySQL đang chạy trong XAMPP
4. Apache đang chạy trong XAMPP
5. Các extension PHP đã enable

## DEMO ACCOUNT

- **Admin**: admin / admin123
- **Giáo vụ**: (Tạo mới trong Admin panel)

## LICENSE

© 2025 Trường Cao đẳng nghề TP. Hồ Chí Minh
