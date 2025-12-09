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
        
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        array_shift($rows);
        
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        $conn->beginTransaction();
        
        foreach ($rows as $index => $row) {
            $row_num = $index + 2;
            
            if (empty(array_filter($row))) continue;
            
            $profession_code = trim($row[0] ?? '');
            $subject_code = trim($row[1] ?? '');
            $subject_name = trim($row[2] ?? '');
            $credit_hours = (int)($row[3] ?? 0);
            
            if (empty($profession_code) || empty($subject_code) || empty($subject_name)) {
                $errors[] = "Dòng {$row_num}: Thiếu thông tin bắt buộc";
                $error_count++;
                continue;
            }
            
            // Tìm profession_id từ profession_code
            $pq = "SELECT id FROM professions WHERE profession_code = :code";
            $ps = $conn->prepare($pq);
            $ps->bindParam(':code', $profession_code);
            $ps->execute();
            
            if ($ps->rowCount() === 0) {
                $errors[] = "Dòng {$row_num}: Không tìm thấy nghề {$profession_code}";
                $error_count++;
                continue;
            }
            
            $profession_id = $ps->fetch()['id'];
            
            // Kiểm tra trùng
            $check = "SELECT id FROM subjects WHERE subject_code = :code AND profession_id = :pid";
            $stmt_check = $conn->prepare($check);
            $stmt_check->bindParam(':code', $subject_code);
            $stmt_check->bindParam(':pid', $profession_id);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $errors[] = "Dòng {$row_num}: Mã môn {$subject_code} đã tồn tại trong nghề {$profession_code}";
                $error_count++;
                continue;
            }
            
            $query = "INSERT INTO subjects (profession_id, subject_code, subject_name, credit_hours) 
                      VALUES (:pid, :code, :name, :hours)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':pid', $profession_id);
            $stmt->bindParam(':code', $subject_code);
            $stmt->bindParam(':name', $subject_name);
            $stmt->bindParam(':hours', $credit_hours);
            
            if ($stmt->execute()) {
                $success_count++;
            } else {
                $errors[] = "Dòng {$row_num}: Lỗi khi thêm vào database";
                $error_count++;
            }
        }
        
        $conn->commit();
        
        if ($success_count > 0) {
            $_SESSION['success'] = "Import thành công {$success_count} môn học!";
        }
        
        if ($error_count > 0) {
            $_SESSION['error'] = "Có {$error_count} lỗi:<br>" . implode('<br>', array_slice($errors, 0, 10));
        }
        
    } catch (Exception $e) {
        if (isset($conn)) $conn->rollBack();
        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
    }
}

redirect('subjects.php');
?>