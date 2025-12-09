<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    try {
        $file = $_FILES['excel_file'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Lỗi upload file!');
        }
        
        $allowed = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        if (!in_array($file['type'], $allowed)) {
            throw new Exception('Chỉ chấp nhận file Excel (.xls, .xlsx)!');
        }
        
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        array_shift($rows); // Bỏ header
        
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        $conn->beginTransaction();
        
        foreach ($rows as $index => $row) {
            $row_num = $index + 2;
            
            if (empty(array_filter($row))) continue;
            
            $faculty_code = trim($row[0] ?? '');
            $faculty_name = trim($row[1] ?? '');
            $description = trim($row[2] ?? '');
            
            if (empty($faculty_code) || empty($faculty_name)) {
                $errors[] = "Dòng {$row_num}: Thiếu mã khoa hoặc tên khoa";
                $error_count++;
                continue;
            }
            
            // Kiểm tra trùng
            $check = "SELECT id FROM faculties WHERE faculty_code = :code";
            $stmt_check = $conn->prepare($check);
            $stmt_check->bindParam(':code', $faculty_code);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $errors[] = "Dòng {$row_num}: Mã khoa {$faculty_code} đã tồn tại";
                $error_count++;
                continue;
            }
            
            $query = "INSERT INTO faculties (faculty_code, faculty_name, description) VALUES (:code, :name, :desc)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':code', $faculty_code);
            $stmt->bindParam(':name', $faculty_name);
            $stmt->bindParam(':desc', $description);
            
            if ($stmt->execute()) {
                $success_count++;
            } else {
                $errors[] = "Dòng {$row_num}: Lỗi khi thêm vào database";
                $error_count++;
            }
        }
        
        $conn->commit();
        
        if ($success_count > 0) {
            $_SESSION['success'] = "Import thành công {$success_count} khoa!";
        }
        
        if ($error_count > 0) {
            $_SESSION['error'] = "Có {$error_count} lỗi:<br>" . implode('<br>', array_slice($errors, 0, 10));
        }
        
    } catch (Exception $e) {
        if (isset($conn)) $conn->rollBack();
        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
    }
}

redirect('faculties.php');
?>