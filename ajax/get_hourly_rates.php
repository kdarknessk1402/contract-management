<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['education_level'])) {
    echo json_encode(['success' => false, 'message' => 'Missing education_level']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$education_level = $_POST['education_level'];
$academic_year = $_POST['academic_year'] ?? '2025-2026';

$query = "SELECT * FROM hourly_rates 
          WHERE education_level = :education_level 
          AND academic_year = :academic_year 
          AND is_active = 1 
          ORDER BY rate_type";
$stmt = $conn->prepare($query);
$stmt->bindParam(':education_level', $education_level);
$stmt->bindParam(':academic_year', $academic_year);
$stmt->execute();

$rates = $stmt->fetchAll();

echo json_encode(['success' => true, 'rates' => $rates]);
?>
