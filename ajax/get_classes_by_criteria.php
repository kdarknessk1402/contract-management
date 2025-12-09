<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['profession_code']) || !isset($_POST['level']) || 
    !isset($_POST['location_id']) || !isset($_POST['faculty_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    // Tìm profession_id
    $query = "SELECT id FROM professions 
              WHERE profession_code = :profession_code 
              AND level = :level 
              AND faculty_id = :faculty_id
              AND is_active = 1
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_code', $_POST['profession_code']);
    $stmt->bindParam(':level', $_POST['level']);
    $stmt->bindParam(':faculty_id', $_POST['faculty_id']);
    $stmt->execute();
    
    $profession = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$profession) {
        echo json_encode([
            'success' => false, 
            'message' => 'Không tìm thấy nghề với trình độ này'
        ]);
        exit;
    }
    
    // Lấy danh sách lớp
    $query = "SELECT * FROM classes 
              WHERE profession_id = :profession_id 
              AND level = :level
              AND location_id = :location_id
              AND is_active = 1
              ORDER BY class_code";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_id', $profession['id']);
    $stmt->bindParam(':level', $_POST['level']);
    $stmt->bindParam(':location_id', $_POST['location_id']);
    $stmt->execute();
    
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'classes' => $classes
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>