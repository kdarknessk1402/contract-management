<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['profession_id']) || !isset($_POST['level'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT s.* FROM subjects s
              JOIN professions p ON s.profession_id = p.id
              WHERE p.id = :profession_id 
              AND p.level = :level
              AND s.is_active = 1
              ORDER BY s.subject_name";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_id', $_POST['profession_id']);
    $stmt->bindParam(':level', $_POST['level']);
    $stmt->execute();
    
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'subjects' => $subjects
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>