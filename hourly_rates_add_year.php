<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    
    $academic_year = trim($_POST['academic_year']);
    
    try {
        // Kiểm tra niên khóa đã tồn tại chưa
        $check = "SELECT COUNT(*) as cnt FROM hourly_rates WHERE academic_year = :year";
        $stmt = $conn->prepare($check);
        $stmt->bindParam(':year', $academic_year);
        $stmt->execute();
        
        if ($stmt->fetch()['cnt'] > 0) {
            throw new Exception("Niên khóa {$academic_year} đã tồn tại!");
        }
        
        // Lấy mức thù lao hiện tại (của niên khóa mới nhất)
        $query = "SELECT * FROM hourly_rates 
                  WHERE academic_year = (SELECT MAX(academic_year) FROM hourly_rates)";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $current_rates = $stmt->fetchAll();
        
        if (empty($current_rates)) {
            // Nếu chưa có, tạo mức mặc định
            $default_rates = [
                ['Đại học', 'standard', 70000],
                ['Đại học', 'high', 90000],
                ['Thạc sĩ', 'standard', 75000],
                ['Thạc sĩ', 'high', 90000],
                ['Tiến sĩ', 'standard', 90000],
                ['Tiến sĩ', 'high', 100000]
            ];
            
            $conn->beginTransaction();
            
            foreach ($default_rates as $rate) {
                $query = "INSERT INTO hourly_rates (education_level, rate_type, amount, academic_year) 
                          VALUES (:edu, :type, :amount, :year)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':edu', $rate[0]);
                $stmt->bindParam(':type', $rate[1]);
                $stmt->bindParam(':amount', $rate[2]);
                $stmt->bindParam(':year', $academic_year);
                $stmt->execute();
            }
            
            $conn->commit();
        } else {
            // Copy từ niên khóa hiện tại
            $conn->beginTransaction();
            
            foreach ($current_rates as $rate) {
                $query = "INSERT INTO hourly_rates (education_level, rate_type, amount, academic_year, is_active) 
                          VALUES (:edu, :type, :amount, :year, 1)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':edu', $rate['education_level']);
                $stmt->bindParam(':type', $rate['rate_type']);
                $stmt->bindParam(':amount', $rate['amount']);
                $stmt->bindParam(':year', $academic_year);
                $stmt->execute();
            }
            
            $conn->commit();
        }
        
        $_SESSION['success'] = "Đã tạo mức thù lao cho niên khóa {$academic_year} thành công!";
        
    } catch (Exception $e) {
        if (isset($conn)) $conn->rollBack();
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
}

redirect('hourly_rates.php');
?>
