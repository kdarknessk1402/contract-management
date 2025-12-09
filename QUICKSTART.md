# HƯỚNG DẪN CÀI ĐẶT NHANH

## Cài đặt trong 5 phút

### 1. Cài đặt XAMPP
```
- Tải: https://www.apachefriends.org/
- Cài đặt vào C:\xampp
- Khởi động Apache + MySQL
```

### 2. Cài đặt Composer  
```
- Tải: https://getcomposer.org/download/
- Chạy Composer-Setup.exe
- Chọn PHP: C:\xampp\php\php.exe
```

### 3. Copy code
```
Copy thư mục contract-management vào C:\xampp\htdocs\
```

### 4. Cài đặt dependencies (trong VS Code Terminal)
```bash
cd C:\xampp\htdocs\contract-management
composer install
```

### 5. Tạo database
```
- Mở: http://localhost/phpmyadmin
- Tab SQL > Copy nội dung database.sql > Go
```

### 6. Truy cập hệ thống
```
URL: http://localhost/contract-management/
User: admin
Pass: admin123
```

## Thứ tự sử dụng

### Admin làm trước:
1. Quản trị > Quản lý Khoa (Thêm khoa)
2. Quản trị > Quản lý Nghề (Thêm nghề)
3. Quản trị > Quản lý Môn học (Thêm môn)
4. Quản trị > Quản lý người dùng (Tạo tài khoản Giáo vụ)

### Giáo vụ làm sau:
1. Quản lý giảng viên > Import Excel (Tải file mẫu, điền, upload)
2. Quản lý hợp đồng > Tạo hợp đồng mới
3. Nhấn nút In để xuất DOCX

## File mẫu Excel
```
Vị trí: uploads/templates/lecturer_template.xlsx
Hoặc tải từ nút "Import Excel" trong trang Quản lý giảng viên
```

## Lưu ý quan trọng

### Khi import Excel:
- Trình độ phải đúng: "Đại học", "Thạc sĩ", hoặc "Tiến sĩ"
- Giới tính phải đúng: "Nam" hoặc "Nữ"  
- Số CCCD phải 12 số
- Ngày cấp định dạng: dd/mm/yyyy

### Khi tạo hợp đồng:
- Chọn giảng viên trước → Hệ thống tự load mức thù lao theo trình độ
- Chọn nghề → Hệ thống tự load danh sách môn học
- Nhập số giờ + chọn thù lao → Tự động tính tổng tiền

### Khi in hợp đồng:
- File DOCX tự động giữ nguyên định dạng
- Có đầy đủ header, footer
- Tự động điền thông tin từ database

## Xử lý lỗi nhanh

### Lỗi Composer:
```bash
# Dùng đường dẫn đầy đủ
C:\ProgramData\ComposerSetup\bin\composer.phar install
```

### Lỗi PHP Extension:
```
Mở C:\xampp\php\php.ini
Bỏ dấu ; trước:
extension=mbstring
extension=zip
extension=gd
extension=pdo_mysql

Sau đó restart Apache
```

### Lỗi quyền ghi file:
```
Chuột phải folder > Properties > Security > Users > Full Control
Áp dụng cho các folder: uploads, exports, backups
```

## Hỗ trợ

Nếu gặp lỗi, kiểm tra:
1. ✓ XAMPP Apache đang chạy
2. ✓ XAMPP MySQL đang chạy  
3. ✓ Database đã import
4. ✓ Composer đã cài
5. ✓ Vendor folder đã tạo (sau composer install)

---
© 2025 Trường Cao đẳng nghề TP. HCM
