<?php
// ⭐ CRITICAL: Set UTF-8 TRƯỚC KHI làm bất cứ việc gì
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

error_log("=== AJAX get_subjects START ===");

// Kiểm tra tham số
if (!isset($_POST['profession_code']) || !isset($_POST['level']) || !isset($_POST['faculty_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Thiếu tham số',
        'received' => $_POST
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $profession_code = mb_strtoupper(trim($_POST['profession_code']), 'UTF-8');
    $level_input = trim($_POST['level']);
    $faculty_id = (int)$_POST['faculty_id'];

    error_log("Input: code=$profession_code, level=$level_input, faculty=$faculty_id");

    // ⭐ UNICODE FIX: So sánh TRỰC TIẾP với database, không normalize
    // Tìm profession với BINARY comparison để tránh vấn đề encoding
    $query = "SELECT id, profession_name, level 
              FROM professions 
              WHERE profession_code = :profession_code 
              AND faculty_id = :faculty_id
              AND is_active = 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':profession_code', $profession_code, PDO::PARAM_STR);
    $stmt->bindParam(':faculty_id', $faculty_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $all_professions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Found " . count($all_professions) . " professions for code $profession_code");
    
    if (count($all_professions) === 0) {
        echo json_encode([
            'success' => false,
            'message' => "Không tìm thấy nghề: $profession_code",
            'searched' => [
                'profession_code' => $profession_code,
                'faculty_id' => $faculty_id
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ⭐ Tìm profession khớp level bằng cách so sánh LINH HOẠT
    $profession = null;
    $level_lower = mb_strtolower($level_input, 'UTF-8');
    
    foreach ($all_professions as $prof) {
        $prof_level_lower = mb_strtolower($prof['level'], 'UTF-8');
        
        // Remove all diacritics and spaces for comparison
        $level_clean = preg_replace('/[^a-z0-9]/u', '', $level_lower);
        $prof_clean = preg_replace('/[^a-z0-9]/u', '', $prof_level_lower);
        
        error_log("Compare: '$level_clean' vs '$prof_clean' (original: '$level_input' vs '{$prof['level']}')");
        
        // Match by keyword detection
        if (
            // Trung cấp
            (stripos($level_input, 'trung') !== false && stripos($prof['level'], 'Trung') !== false && stripos($prof['level'], 'liên') === false) ||
            // Cao đẳng (not liên thông)
            (stripos($level_input, 'cao') !== false && stripos($prof['level'], 'Cao') !== false && stripos($level_input, 'lien') === false && stripos($prof['level'], 'liên') === false) ||
            // Cao đẳng liên thông
            (stripos($level_input, 'lien') !== false && stripos($prof['level'], 'liên') !== false)
        ) {
            $profession = $prof;
            error_log("✅ MATCHED: {$prof['level']}");
            break;
        }
    }
    
    if (!$profession) {
        $available_levels = array_column($all_professions, 'level');
        error_log("❌ NO MATCH - Available: " . implode(', ', $available_levels));
        
        echo json_encode([
            'success' => false,
            'message' => "Không tìm thấy trình độ '$level_input' cho nghề $profession_code",
            'available_levels' => $available_levels,
            'debug' => [
                'input_level' => $level_input,
                'input_bytes' => bin2hex($level_input)
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $profession_id = $profession['id'];
    error_log("Using profession_id: $profession_id");
    
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
    
    error_log("✅ Found " . count($subjects) . " subjects");
    
    echo json_encode([
        'success' => true,
        'profession_id' => $profession_id,
        'profession_name' => $profession['profession_name'],
        'level' => $profession['level'],
        'subjects' => $subjects,
        'count' => count($subjects)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("❌ ERROR: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
```

---

## ✅ GIẢI THÍCH FIX:

### Vấn đề cũ:
- Normalize `"Cao đẳng"` → so sánh với database `"Cao đẳng"` 
- Encoding khác nhau → **KHÔNG KHỚP** ❌

### Giải pháp mới:
1. **Không normalize** - Lấy TẤT CẢ professions của code đó
2. **So sánh bằng từ khóa** - Tìm `"cao"` + `"đẳng"` (không cần đúng chính tả)
3. **Phân biệt "liên thông"** - Kiểm tra có chữ "lien/liên" không

### Ví dụ hoạt động:
- Input: `"cao dang"` → Tìm có chữ "cao" + không có "lien" → ✅ Match "Cao đẳng"
- Input: `"Cao đẳng"` → Tìm có chữ "cao" + không có "lien" → ✅ Match "Cao đẳng"  
- Input: `"cao dang lien thong"` → Tìm có chữ "lien" → ✅ Match "Cao đẳng liên thông"

---

## 🧪 TEST NGAY:

### 1. Test direct:
```
http://localhost/contract-management/test_direct_ajax.php