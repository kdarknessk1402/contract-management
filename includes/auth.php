<?php
class Auth {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = "Vui lòng đăng nhập để tiếp tục!";
            redirect('login.php');
        }
    }
    
    public function requireAdmin() {
        $this->requireLogin();
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
            redirect('index.php');
        }
    }
    
    public function requireGiaoVu() {
        $this->requireLogin();
        if (!in_array($_SESSION['role'], ['admin', 'giao_vu'])) {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
            redirect('index.php');
        }
    }
    
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }
    
    public function isGiaoVu() {
        return $this->isLoggedIn() && in_array($_SESSION['role'], ['admin', 'giao_vu']);
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    public function getRole() {
        return $_SESSION['role'] ?? null;
    }
    
    public function getFacultyId() {
        return $_SESSION['faculty_id'] ?? null;
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        redirect('login.php');
    }
    
    public function login($username, $password) {
        require_once __DIR__ . '/../config/database.php';
        
        $database = new Database();
        $conn = $database->getConnection();
        
        try {
            $query = "SELECT u.*, f.faculty_name 
                      FROM users u 
                      LEFT JOIN faculties f ON u.faculty_id = f.id 
                      WHERE u.username = :username 
                      AND u.is_active = 1";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // So sánh password (plain text - không khuyến khích trong production)
                if ($password === $user['password']) {
                    // Cập nhật last_login
                    $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindParam(':id', $user['id']);
                    $updateStmt->execute();
                    
                    // Lưu thông tin vào session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['faculty_id'] = $user['faculty_id'];
                    $_SESSION['faculty_name'] = $user['faculty_name'];
                    
                    return true;
                }
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function checkPermission($required_role) {
        $this->requireLogin();
        
        $user_role = $_SESSION['role'];
        
        // Admin có tất cả quyền
        if ($user_role === 'admin') {
            return true;
        }
        
        // Kiểm tra quyền cụ thể
        if ($user_role === $required_role) {
            return true;
        }
        
        return false;
    }
}
?>