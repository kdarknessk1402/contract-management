<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$auth = new Auth();
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['excel_file'])) {
    redirect('professions.php');
}

$database = new Database();
$conn = $database->getConnection();

try {
    $file = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    
    // Bỏ qua dòng tiêu đề
    array_shift($rows);
    
    $success_count = 0;
    $error_count = 0;
    $errors = [];
    
    $conn->beginTransaction();
    
    foreach ($rows as $index => $row) {
        $row_num = $index + 2;
        
        // Kiểm tra dòng trống
        if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
            continue;
        }
        
        $faculty_code = trim($row[0]);
        $profession_code = trim($row[1]);
        $level = trim($row[2]);
        $profession_name = trim($row[3]);
        $academic_year = trim($row[4]);
        
        // Validate
        if (empty($faculty_code) || empty($profession_code) || empty($level) || empty($profession_name)) {
            $errors[] = "Dòng $row_num: Thiếu thông tin bắt buộc";
            $error_count++;
            if (count($errors) >= 10) break;
            continue;
        }
        
        // Validate level
        if (!in_array($level, ['Trung cấp', 'Cao đẳng'])) {
            $errors[] = "Dòng $row_num: Trình độ phải là 'Trung cấp' hoặc 'Cao đẳng'";
            $error_count++;
            if (count($errors) >= 10) break;
            continue;
        }
        
        // Validate academic year format
        if (!empty($academic_year) && !preg_match('/^\d{4}-\d{4}$/', $academic_year)) {
            $errors[] = "Dòng $row_num: Năm học không đúng định dạng (VD: 2025-2026)";
            $error_count++;
            if (count($errors) >= 10) break;
            continue;
        }
        
        // Tìm faculty_id từ faculty_code
        $query = "SELECT id FROM faculties WHERE faculty_code = :faculty_code AND is_active = 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':faculty_code', $faculty_code);
        $stmt->execute();
        $faculty = $stmt->fetch();
        
        if (!$faculty) {
            $errors[] = "Dòng $row_num: Không tìm thấy khoa với mã '$faculty_code'";
            $error_count++;
            if (count($errors) >= 10) break;
            continue;
        }
        
        $faculty_id = $faculty['id'];
        
        // Kiểm tra trùng profession_code trong cùng faculty
        $query = "SELECT id FROM professions 
                  WHERE faculty_id = :faculty_id 
                  AND profession_code = :profession_code 
                  AND is_active = 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':faculty_id', $faculty_id);
        $stmt->bindParam(':profession_code', $profession_code);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $errors[] = "Dòng $row_num: Mã nghề '$profession_code' đã tồn tại trong khoa '$faculty_code'";
            $error_count++;
            if (count($errors) >= 10) break;
            continue;
        }
        
        // Thêm vào database
        $query = "INSERT INTO professions (faculty_id, profession_code, level, profession_name, academic_year) 
                  VALUES (:faculty_id, :profession_code, :level, :profession_name, :academic_year)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':faculty_id', $faculty_id);
        $stmt->bindParam(':profession_code', $profession_code);
        $stmt->bindParam(':level', $level);
        $stmt->bindParam(':profession_name', $profession_name);
        $stmt->bindParam(':academic_year', $academic_year);
        
        if ($stmt->execute()) {
            $success_count++;
        } else {
            $errors[] = "Dòng $row_num: Lỗi khi thêm dữ liệu";
            $error_count++;
            if (count($errors) >= 10) break;
        }
    }
    
    if ($error_count > 0) {
        $conn->rollBack();
        $_SESSION['error'] = "Import thất bại! Có $error_count lỗi:<br>" . implode('<br>', $errors);
    } else {
        $conn->commit();
        $_SESSION['success'] = "Import thành công $success_count nghề!";
    }
    
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['error'] = "Lỗi: " . $e->getMessage();
}

redirect('professions.php');
?>