<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['profession_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing profession_id']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$profession_id = $_POST['profession_id'];

$query = "SELECT * FROM subjects WHERE profession_id = :profession_id AND is_active = 1 ORDER BY subject_name";
$stmt = $conn->prepare($query);
$stmt->bindParam(':profession_id', $profession_id);
$stmt->execute();

$subjects = $stmt->fetchAll();

echo json_encode(['success' => true, 'subjects' => $subjects]);
?>
