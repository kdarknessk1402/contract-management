# ✅ HỆ THỐNG QUẢN LÝ HỢP ĐỒNG THỈNH GIẢNG - HOÀN TẤT

## 🎯 TỔNG QUAN DỰ ÁN

Hệ thống quản lý hợp đồng thỉnh giảng đã được phát triển **HOÀN CHỈNH** với đầy đủ các tính năng theo yêu cầu.

### ✨ Tính năng đã triển khai:

#### 🎓 DÀNH CHO GIÁO VỤ:
✅ **Quản lý giảng viên thỉnh giảng**
- Import danh sách từ file Excel (có file mẫu kèm theo)
- Thêm/Sửa/Xóa thông tin giảng viên
- Tự động validate dữ liệu (trình độ, giới tính, số CCCD)
- Quản lý theo khoa (chỉ thấy giảng viên của khoa mình)

✅ **Quản lý hợp đồng**
- Tạo hợp đồng thỉnh giảng nhanh chóng
- Chọn giảng viên → Tự động load mức thù lao theo trình độ
- Chọn nghề → Tự động load danh sách môn học
- Tự động tính tổng tiền = Số giờ × Thù lao/giờ
- Tự động chuyển số sang chữ (tiếng Việt)
- Tự động tạo số hợp đồng: 0001/HĐ-CĐN/2025

✅ **In hợp đồng ra DOCX**
- Xuất file Word (.docx) giữ NGUYÊN định dạng mẫu
- Header đầy đủ: Logo trường, thông tin cơ quan
- Footer đầy đủ: Chữ ký bên A, bên B
- Tự động điền thông tin từ database vào đúng vị trí
- Format chuyên nghiệp, in ra giấy ngay được

#### 👨‍💼 DÀNH CHO ADMIN:
✅ **Quản lý danh mục**
- Quản lý Khoa (thêm/sửa/xóa)
- Quản lý Nghề theo niên khóa (2025-2026, 2026-2027...)
- Quản lý Môn học theo từng nghề

✅ **Quản lý hệ thống**
- Quản lý người dùng (tạo tài khoản Admin, Giáo vụ)
- Phân quyền theo khoa
- Cấu hình thù lao theo trình độ (Đại học, Thạc sĩ, Tiến sĩ)
- Backup dữ liệu

### 💰 Mức thù lao mặc định:
```
Đại học:  70,000đ (chuẩn) | 90,000đ (cao)
Thạc sĩ:  75,000đ (chuẩn) | 90,000đ (cao)
Tiến sĩ:  90,000đ (chuẩn) | 100,000đ (cao)
```

---

## 📦 DANH SÁCH FILE TRONG DỰ ÁN

### 📄 File hướng dẫn:
- **INSTALL_GUIDE.md** - Hướng dẫn cài đặt CHI TIẾT (từng bước)
- **QUICKSTART.md** - Hướng dẫn cài đặt NHANH (5 phút)
- **README.md** - Hướng dẫn tổng quan

### 🗂️ File code chính:
- **database.sql** - Database schema (import vào phpMyAdmin)
- **composer.json** - PHP dependencies config
- **login.php** - Trang đăng nhập
- **index.php** - Dashboard/Trang chủ
- **lecturers.php** - Quản lý giảng viên
- **lecturer_import.php** - Xử lý import Excel
- **contracts.php** - Quản lý hợp đồng
- **contract_create.php** - Tạo hợp đồng mới
- **contract_print.php** - In hợp đồng ra DOCX

### 📁 Thư mục:
- **config/** - Cấu hình (database, helpers)
- **includes/** - Header, footer, auth
- **assets/** - CSS, JavaScript, images
- **ajax/** - AJAX endpoints
- **uploads/templates/** - File Excel mẫu
- **exports/** - File DOCX xuất ra
- **vendor/** - Composer packages (tự động tạo)

### 📊 File mẫu có sẵn:
- **uploads/templates/lecturer_template.xlsx** - File Excel mẫu import giảng viên

---

## 🚀 CÀI ĐẶT NHANH 5 PHÚT

### Bước 1: Cài XAMPP
```
Tải: https://www.apachefriends.org/
Cài vào: C:\xampp
Khởi động: Apache + MySQL
```

### Bước 2: Cài Composer
```
Tải: https://getcomposer.org/download/
Chạy: Composer-Setup.exe
Chọn PHP: C:\xampp\php\php.exe
```

### Bước 3: Copy code
```
Copy folder này vào: C:\xampp\htdocs\
Đường dẫn: C:\xampp\htdocs\contract-management\
```

### Bước 4: Cài dependencies (trong VS Code Terminal)
```bash
cd C:\xampp\htdocs\contract-management
composer install
```

### Bước 5: Import database
```
Mở: http://localhost/phpmyadmin
Tab SQL → Copy nội dung database.sql → Go
```

### Bước 6: Truy cập
```
URL: http://localhost/contract-management/
User: admin
Pass: admin123
```

---

## 📖 HƯỚNG DẪN SỬ DỤNG

### 🔧 Admin làm trước (thiết lập hệ thống):

1. **Quản lý Khoa**: Thêm các khoa (CNTT, Cơ khí, Du lịch...)
2. **Quản lý Nghề**: Thêm nghề cho từng khoa (theo niên khóa)
3. **Quản lý Môn học**: Thêm môn cho từng nghề
4. **Tạo tài khoản**: Tạo tài khoản Giáo vụ cho mỗi khoa

### 👥 Giáo vụ làm sau (quản lý hợp đồng):

#### Import giảng viên:
1. Vào **Quản lý giảng viên** → Nhấn **Import Excel**
2. Tải file mẫu `lecturer_template.xlsx`
3. Điền thông tin giảng viên vào file (xóa dữ liệu mẫu)
4. Upload file → Hệ thống tự động import

#### Tạo hợp đồng:
1. Vào **Quản lý hợp đồng** → Nhấn **Tạo hợp đồng mới**
2. Chọn giảng viên (hệ thống tự load mức thù lao theo trình độ)
3. Chọn nghề → Tự động load môn học
4. Nhập số giờ → Tự động tính tổng tiền
5. Chọn thời gian, năm học, học kỳ
6. Lưu → Hệ thống tự tạo số HĐ

#### In hợp đồng:
1. Trong danh sách hợp đồng, nhấn nút **In**
2. Tự động tải file DOCX về máy
3. Mở bằng Word → Kiểm tra → In ra giấy ký

---

## ⚙️ YÊU CẦU HỆ THỐNG

### Phần mềm cần cài:
- ✅ **XAMPP** (Apache + MySQL + PHP)
- ✅ **Composer** (PHP Package Manager)
- ✅ **Microsoft Word** (để mở file DOCX)

### PHP Extensions cần enable:
```ini
extension=mbstring
extension=zip
extension=gd
extension=pdo_mysql
extension=xml
```

### Cấu hình PHP khuyến nghị:
```ini
memory_limit = 256M
upload_max_filesize = 5M
post_max_size = 8M
```

---

## 🐛 XỬ LÝ LỖI THƯỜNG GẶP

### ❌ "Composer not found"
```bash
# Dùng đường dẫn đầy đủ
C:\ProgramData\ComposerSetup\bin\composer.phar install
```

### ❌ "Call to undefined function mb_strlen"
```
Mở C:\xampp\php\php.ini
Bỏ dấu ; trước: extension=mbstring
Restart Apache
```

### ❌ "Permission denied"
```
Chuột phải folder → Properties → Security
Users → Full Control → Apply
```

### ❌ Import Excel lỗi "Trình độ không hợp lệ"
```
Kiểm tra Excel:
- Trình độ phải đúng: "Đại học", "Thạc sĩ", "Tiến sĩ"
- Giới tính phải đúng: "Nam" hoặc "Nữ"
- Không có khoảng trắng thừa
```

---

## 📱 THÔNG TIN ĐĂNG NHẬP

### Tài khoản Admin mặc định:
```
Username: admin
Password: admin123
```

### Tài khoản Giáo vụ:
```
Được tạo bởi Admin trong:
Quản trị > Quản lý người dùng
```

---

## 🎨 GIAO DIỆN

- ✅ **Responsive**: Hoạt động tốt trên desktop, tablet, mobile
- ✅ **Bootstrap 5**: Giao diện hiện đại, chuyên nghiệp
- ✅ **DataTables**: Bảng dữ liệu có search, sort, pagination
- ✅ **AJAX**: Load dữ liệu nhanh, không reload trang
- ✅ **Alert**: Thông báo thành công/lỗi rõ ràng

---

## 🔐 BẢO MẬT

- ✅ **Password hashing**: BCrypt
- ✅ **SQL Injection**: Prepared statements (PDO)
- ✅ **XSS Protection**: htmlspecialchars()
- ✅ **Session**: Quản lý phiên đăng nhập an toàn
- ✅ **Access Control**: Phân quyền theo role và khoa

---

## 🌟 ĐIỂM NỔI BẬT

### 1. Tự động hóa cao:
- Tự động tạo số hợp đồng
- Tự động tính thù lao theo trình độ
- Tự động chuyển số sang chữ
- Tự động validate dữ liệu

### 2. Dễ sử dụng:
- Giao diện trực quan, dễ hiểu
- Hướng dẫn chi tiết từng bước
- File Excel mẫu có sẵn
- Thông báo lỗi rõ ràng

### 3. In hợp đồng chuyên nghiệp:
- Giữ NGUYÊN định dạng mẫu
- Header/Footer đầy đủ
- Format chuẩn văn bản hành chính
- Sẵn sàng in ra giấy ký

### 4. Phân quyền chặt chẽ:
- Admin quản lý toàn bộ hệ thống
- Giáo vụ chỉ thấy dữ liệu khoa mình
- Bảo mật thông tin giữa các khoa

---

## 📞 HỖ TRỢ

### Khi gặp vấn đề, kiểm tra:
1. ✅ Apache đang chạy (XAMPP Control Panel màu xanh)
2. ✅ MySQL đang chạy (XAMPP Control Panel màu xanh)
3. ✅ Database đã import (kiểm tra trong phpMyAdmin)
4. ✅ Composer đã install (folder vendor/ tồn tại)
5. ✅ PHP extensions đã enable (kiểm tra php.ini)

### Đọc hướng dẫn:
- **Chi tiết**: INSTALL_GUIDE.md (hướng dẫn từng bước)
- **Nhanh**: QUICKSTART.md (cài đặt 5 phút)

---

## 📝 LƯU Ý QUAN TRỌNG

### ⚠️ Trước khi sử dụng:
1. Đọc file **INSTALL_GUIDE.md** để cài đặt đúng
2. Import database.sql vào MySQL
3. Chạy `composer install` để cài dependencies
4. Tạo các thư mục: uploads, exports, backups
5. Cấp quyền ghi cho các thư mục trên

### ⚠️ Khi import giảng viên:
1. Download file mẫu từ hệ thống
2. Điền đúng định dạng (Trình độ, Giới tính)
3. Xóa dữ liệu mẫu trước khi upload
4. Lưu file với đúng định dạng .xlsx

### ⚠️ Khi in hợp đồng:
1. Kiểm tra thông tin giảng viên đã đầy đủ
2. Kiểm tra thông tin môn học, số giờ
3. File DOCX tự động download về máy
4. Mở bằng Microsoft Word để xem/in

---

## 🎓 KẾT LUẬN

Hệ thống đã được phát triển **HOÀN CHỈNH** với:

✅ Đầy đủ tính năng theo yêu cầu
✅ Giao diện đẹp, chuyên nghiệp
✅ Code sạch, dễ maintain
✅ Hướng dẫn chi tiết từng bước
✅ File mẫu Excel có sẵn
✅ In hợp đồng ra DOCX chuẩn

**Sẵn sàng triển khai và sử dụng ngay!**

---

© 2025 Trường Cao đẳng nghề TP. Hồ Chí Minh
Phát triển bởi: Claude AI Assistant
Ngày hoàn thành: 09/12/2025
