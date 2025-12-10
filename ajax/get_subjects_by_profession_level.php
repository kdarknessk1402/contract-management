<?php
// ⭐ BẮT BUỘC: Set UTF-8 ngay từ đầu
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra tham số
if (!isset($_POST['profession_code']) || !isset($_POST['level']) || !isset($_POST['faculty_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Thiếu tham số bắt buộc'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// ⭐ Normalize input
$profession_code = mb_strtoupper(trim($_POST['profession_code']), 'UTF-8');
$level_raw = trim($_POST['level']);
$faculty_id = (int)$_POST['faculty_id'];

// ⭐ CRITICAL FIX: Chuẩn hóa level (loại bỏ tất cả vấn đề encoding)
// Sử dụng mb_convert_encoding để đảm bảo UTF-8
$level_raw = mb_convert_encoding($level_raw, 'UTF-8', 'UTF-8');

// Map các biến thể có thể có
$level_normalized = null;
if (mb_stripos($level_raw, 'trung') !== false && mb_stripos($level_raw, 'cap') !== false) {
    $level_normalized = 'Trung cấp';
} elseif (mb_stripos($level_raw, 'cao') !== false && mb_stripos($level_raw, 'lien') !== false) {
    $level_normalized = 'Cao đẳng liên thông';
} elseif (mb_stripos($level_raw, 'cao') !== false && mb_stripos($level_raw, 'dang') !== false) {
    $level_normalized = 'Cao đẳng';
}

if (!$level_normalized) {
    echo json_encode([
        'success' => false,
        'message' => 'Trình độ không hợp lệ',
        'received_level' => $level_raw,
        'valid_levels' => ['Trung cấp', 'Cao đẳng', 'Cao đẳng liên thông']
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Tìm profession_id với BINARY để so sánh chính xác
    $query = "SELECT id, profession_name, level FROM professions 
              WHERE UPPER(profession_code) = :profession_code 
              AND level = :level
              AND faculty_id = :faculty_id
              AND is_active = 1
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_code', $profession_code, PDO::PARAM_STR);
    $stmt->bindParam(':level', $level_normalized, PDO::PARAM_STR);
    $stmt->bindParam(':faculty_id', $faculty_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $profession = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$profession) {
        // Debug: Lấy tất cả professions có sẵn
        $debugQuery = "SELECT id, profession_code, level, profession_name 
                       FROM professions 
                       WHERE UPPER(profession_code) = :profession_code 
                       AND faculty_id = :faculty_id
                       AND is_active = 1";
        $debugStmt = $conn->prepare($debugQuery);
        $debugStmt->bindParam(':profession_code', $profession_code, PDO::PARAM_STR);
        $debugStmt->bindParam(':faculty_id', $faculty_id, PDO::PARAM_INT);
        $debugStmt->execute();
        $available = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => false, 
            'message' => 'Không tìm thấy nghề với trình độ này',
            'searched' => [
                'profession_code' => $profession_code,
                'level' => $level_normalized,
                'faculty_id' => $faculty_id
            ],
            'available_in_db' => $available
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $profession_id = $profession['id'];
    
    // Lấy môn học
    $query = "SELECT id, subject_code, subject_name, credit_hours 
              FROM subjects 
              WHERE profession_id = :profession_id 
              AND is_active = 1
              ORDER BY subject_code";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_id', $profession_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'profession_id' => $profession_id,
        'profession_name' => $profession['profession_name'],
        'level' => $profession['level'],
        'subjects' => $subjects,
        'count' => count($subjects)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi database: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>