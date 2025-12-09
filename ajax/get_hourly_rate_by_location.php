<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['location_id']) || !isset($_POST['education_level'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT rate_per_hour FROM hourly_rates_by_location 
              WHERE location_id = :location_id 
              AND education_level = :education_level
              AND academic_year = :academic_year
              AND is_active = 1
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':location_id', $_POST['location_id']);
    $stmt->bindParam(':education_level', $_POST['education_level']);
    $stmt->bindParam(':academic_year', $_POST['academic_year']);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'rate' => $result['rate_per_hour']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy mức giá phù hợp'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>