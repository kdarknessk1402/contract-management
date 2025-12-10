<?php
// ⭐ BẮT BUỘC: Set UTF-8 ngay từ đầu
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header('Content-Type: application/json; charset=utf-8');

// Debug logging
error_log("=== AJAX REQUEST ===");
error_log("POST data: " . print_r($_POST, true));

// Kiểm tra tham số
if (!isset($_POST['profession_code']) || !isset($_POST['level']) || !isset($_POST['faculty_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Thiếu tham số bắt buộc',
        'received' => $_POST
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// ⭐ Lấy input và TRIM kỹ
$profession_code = mb_strtoupper(trim($_POST['profession_code']), 'UTF-8');
$level_raw = trim($_POST['level']);
$faculty_id = (int)$_POST['faculty_id'];

error_log("Normalized input: profession_code='$profession_code', level='$level_raw', faculty_id=$faculty_id");

// ⭐ SIMPLE APPROACH: So sánh CHÍNH XÁC với 3 giá trị có thể
$valid_levels = ['Trung cấp', 'Cao đẳng', 'Cao đẳng liên thông'];

// Tìm level matching (không phân biệt hoa thường, bỏ qua khoảng trắng thừa)
$level_normalized = null;
foreach ($valid_levels as $valid_level) {
    // So sánh không phân biệt hoa/thường, bỏ qua khoảng trắng
    $clean_input = preg_replace('/\s+/', ' ', mb_strtolower($level_raw, 'UTF-8'));
    $clean_valid = preg_replace('/\s+/', ' ', mb_strtolower($valid_level, 'UTF-8'));
    
    if ($clean_input === $clean_valid) {
        $level_normalized = $valid_level;
        break;
    }
}

if (!$level_normalized) {
    error_log("Level not matched: '$level_raw'");
    echo json_encode([
        'success' => false,
        'message' => 'Trình độ không hợp lệ',
        'received_level' => $level_raw,
        'received_level_hex' => bin2hex($level_raw), // Debug encoding
        'valid_levels' => $valid_levels
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

error_log("Level matched: '$level_normalized'");

try {
    // ⭐ Tìm profession_id
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
        error_log("Profession not found");
        
        // Debug: Lấy TẤT CẢ professions có profession_code này
        $debugQuery = "SELECT id, profession_code, level, profession_name, faculty_id 
                       FROM professions 
                       WHERE UPPER(profession_code) = :profession_code 
                       AND is_active = 1";
        $debugStmt = $conn->prepare($debugQuery);
        $debugStmt->bindParam(':profession_code', $profession_code, PDO::PARAM_STR);
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
            'available_professions' => $available,
            'debug_query' => $query
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $profession_id = $profession['id'];
    error_log("Found profession_id: $profession_id");
    
    // ⭐ Lấy môn học
    $query = "SELECT id, subject_code, subject_name, credit_hours 
              FROM subjects 
              WHERE profession_id = :profession_id 
              AND is_active = 1
              ORDER BY subject_code";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_id', $profession_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Found " . count($subjects) . " subjects");
    
    echo json_encode([
        'success' => true,
        'profession_id' => $profession_id,
        'profession_name' => $profession['profession_name'],
        'level' => $profession['level'],
        'subjects' => $subjects,
        'count' => count($subjects)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi database: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>