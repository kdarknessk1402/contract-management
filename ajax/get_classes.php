<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['profession_id']) || !isset($_POST['level']) || !isset($_POST['location_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT * FROM classes 
              WHERE profession_id = :profession_id 
              AND level = :level
              AND location_id = :location_id
              AND is_active = 1
              ORDER BY class_code";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_id', $_POST['profession_id']);
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