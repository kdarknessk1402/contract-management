<?php
require_once 'config/config.php';
require_once 'config/database.php';

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

$database = new Database();
$conn = $database->getConnection();

echo "<h1>🔍 DEBUG DATABASE</h1>";

// 1. Kiểm tra professions
echo "<h2>1️⃣ Professions trong DB:</h2>";
$query = "SELECT id, profession_code, level, profession_name, faculty_id, is_active 
          FROM professions 
          ORDER BY profession_code, level";
$stmt = $conn->prepare($query);
$stmt->execute();
$professions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Code</th><th>Level</th><th>Name</th><th>Faculty</th><th>Active</th></tr>";
foreach ($professions as $p) {
    echo "<tr>";
    echo "<td>{$p['id']}</td>";
    echo "<td>{$p['profession_code']}</td>";
    echo "<td><strong>{$p['level']}</strong> (Hex: " . bin2hex($p['level']) . ")</td>";
    echo "<td>{$p['profession_name']}</td>";
    echo "<td>{$p['faculty_id']}</td>";
    echo "<td>{$p['is_active']}</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Test query
echo "<h2>2️⃣ Test Query:</h2>";
$test_code = 'CNTT01';
$test_level = 'Cao đẳng';
$test_faculty = 1;

echo "<p>Searching for: <strong>$test_code / $test_level / faculty=$test_faculty</strong></p>";

$query = "SELECT * FROM professions 
          WHERE UPPER(profession_code) = :code 
          AND level = :level
          AND faculty_id = :faculty
          AND is_active = 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':code', $test_code);
$stmt->bindParam(':level', $test_level);
$stmt->bindParam(':faculty', $test_faculty);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "<pre style='background: #d4edda; padding: 10px;'>";
    echo "✅ FOUND:\n";
    print_r($result);
    echo "</pre>";
    
    // Lấy subjects
    $subQuery = "SELECT * FROM subjects WHERE profession_id = :pid AND is_active = 1";
    $subStmt = $conn->prepare($subQuery);
    $subStmt->bindParam(':pid', $result['id']);
    $subStmt->execute();
    $subjects = $subStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📚 Subjects:</h3>";
    if (count($subjects) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Hours</th></tr>";
        foreach ($subjects as $s) {
            echo "<tr>";
            echo "<td>{$s['id']}</td>";
            echo "<td>{$s['subject_code']}</td>";
            echo "<td>{$s['subject_name']}</td>";
            echo "<td>{$s['credit_hours']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Không có môn học nào!</p>";
    }
} else {
    echo "<p style='color: red;'>❌ NOT FOUND</p>";
}

// 3. Test với các level khác
echo "<h2>3️⃣ Test tất cả levels:</h2>";
$test_levels = ['Trung cấp', 'Cao đẳng', 'Cao đẳng liên thông'];

foreach ($test_levels as $level) {
    echo "<h4>Testing: <strong>$level</strong></h4>";
    
    $query = "SELECT * FROM professions 
              WHERE profession_code = :code 
              AND level = :level
              AND faculty_id = :faculty
              AND is_active = 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':code', $test_code);
    $stmt->bindParam(':level', $level);
    $stmt->bindParam(':faculty', $test_faculty);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Found profession_id: {$result['id']}</p>";
    } else {
        echo "<p style='color: red;'>❌ Not found</p>";
    }
}
?>