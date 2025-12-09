<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$auth = new Auth();
$auth->requireLogin();

if ($_SESSION['role'] !== 'giao_vu' && $_SESSION['role'] !== 'admin') {
    redirect('lecturers.php');
}

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    try {
        $file = $_FILES['excel_file'];
        
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Lỗi upload file!');
        }
        
        // Kiểm tra loại file
        $allowed = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        if (!in_array($file['type'], $allowed)) {
            throw new Exception('Chỉ chấp nhận file Excel (.xls, .xlsx)!');
        }
        
        // Kiểm tra kích thước
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File quá lớn! Tối đa 5MB.');
        }
        
        // Đọc file Excel
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Bỏ qua header
        array_shift($rows);
        
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        $conn->beginTransaction();
        
        foreach ($rows as $index => $row) {
            $row_num = $index + 2; // +2 vì bắt đầu từ 2 (có header)
            
            // Bỏ qua dòng trống
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Validate dữ liệu bắt buộc
            $full_name = trim($row[0] ?? '');
            $gender = trim($row[1] ?? '');
            $birth_year = !empty($row[2]) ? (int)$row[2] : null;
            $id_number = trim($row[3] ?? '');
            $id_issued_date = !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : null;
            $id_issued_place = trim($row[5] ?? '');
            $education_level = trim($row[6] ?? '');
            $major = trim($row[7] ?? '');
            $pedagogy = trim($row[8] ?? '');
            $address = trim($row[9] ?? '');
            $phone = trim($row[10] ?? '');
            $email = trim($row[11] ?? '');
            $bank_account = trim($row[12] ?? '');
            $bank_name = trim($row[13] ?? '');
            $bank_branch = trim($row[14] ?? '');
            $tax_code = trim($row[15] ?? '') ?: $id_number;
            
            // Kiểm tra các trường bắt buộc
            if (empty($full_name) || empty($gender) || empty($id_number) || empty($education_level)) {
                $errors[] = "Dòng {$row_num}: Thiếu thông tin bắt buộc";
                $error_count++;
                continue;
            }
            
            // Validate giới tính
            if (!in_array($gender, ['Nam', 'Nữ'])) {
                $errors[] = "Dòng {$row_num}: Giới tính phải là 'Nam' hoặc 'Nữ'";
                $error_count++;
                continue;
            }
            
            // Validate trình độ
            if (!in_array($education_level, ['Đại học', 'Thạc sĩ', 'Tiến sĩ'])) {
                $errors[] = "Dòng {$row_num}: Trình độ không hợp lệ";
                $error_count++;
                continue;
            }
            
            // Kiểm tra trùng số CCCD
            $check_query = "SELECT id FROM lecturers WHERE id_number = :id_number AND faculty_id = :faculty_id";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bindParam(':id_number', $id_number);
            $check_stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $errors[] = "Dòng {$row_num}: Số CCCD {$id_number} đã tồn tại";
                $error_count++;
                continue;
            }
            
            // Insert vào database
            $query = "INSERT INTO lecturers (
                faculty_id, full_name, gender, birth_year, id_number, 
                id_issued_date, id_issued_place, education_level, major, pedagogy,
                address, phone, email, bank_account, bank_name, bank_branch, tax_code
            ) VALUES (
                :faculty_id, :full_name, :gender, :birth_year, :id_number,
                :id_issued_date, :id_issued_place, :education_level, :major, :pedagogy,
                :address, :phone, :email, :bank_account, :bank_name, :bank_branch, :tax_code
            )";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':birth_year', $birth_year);
            $stmt->bindParam(':id_number', $id_number);
            $stmt->bindParam(':id_issued_date', $id_issued_date);
            $stmt->bindParam(':id_issued_place', $id_issued_place);
            $stmt->bindParam(':education_level', $education_level);
            $stmt->bindParam(':major', $major);
            $stmt->bindParam(':pedagogy', $pedagogy);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':bank_account', $bank_account);
            $stmt->bindParam(':bank_name', $bank_name);
            $stmt->bindParam(':bank_branch', $bank_branch);
            $stmt->bindParam(':tax_code', $tax_code);
            
            if ($stmt->execute()) {
                $success_count++;
            } else {
                $errors[] = "Dòng {$row_num}: Lỗi khi thêm vào database";
                $error_count++;
            }
        }
        
        $conn->commit();
        
        // Thông báo kết quả
        if ($success_count > 0) {
            $_SESSION['success'] = "Import thành công {$success_count} giảng viên!";
        }
        
        if ($error_count > 0) {
            $_SESSION['error'] = "Có {$error_count} lỗi:<br>" . implode('<br>', array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $_SESSION['error'] .= '<br>... và ' . (count($errors) - 10) . ' lỗi khác';
            }
        }
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollBack();
        }
        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
    }
}

redirect('lecturers.php');
?>
