<?php
// Các hàm hỗ trợ chung

// Kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kiểm tra quyền admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Kiểm tra quyền giáo vụ
function isGiaoVu() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'giao_vu';
}

// Redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Clean input
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Format số tiền VND
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

// Format ngày tháng
function formatDate($date) {
    if (empty($date)) return '';
    return date('d/m/Y', strtotime($date));
}

// Format ngày giờ
function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

// Chuyển số thành chữ (tiếng Việt)
function numberToWords($number) {
    $number = intval($number);
    if ($number == 0) return 'Không đồng';
    
    $unit = ['', 'nghìn', 'triệu', 'tỷ'];
    $digit = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    
    $result = '';
    $unitIndex = 0;
    
    while ($number > 0) {
        $temp = $number % 1000;
        if ($temp > 0) {
            $str = '';
            $hundred = floor($temp / 100);
            $ten = floor(($temp % 100) / 10);
            $one = $temp % 10;
            
            if ($hundred > 0) {
                $str .= $digit[$hundred] . ' trăm ';
            }
            
            if ($ten > 1) {
                $str .= $digit[$ten] . ' mươi ';
                if ($one == 1) {
                    $str .= 'mốt ';
                } else if ($one > 0) {
                    $str .= $digit[$one] . ' ';
                }
            } else if ($ten == 1) {
                $str .= 'mười ';
                if ($one > 0) {
                    $str .= $digit[$one] . ' ';
                }
            } else if ($ten == 0 && $one > 0) {
                if ($hundred > 0) {
                    $str .= 'lẻ ';
                }
                $str .= $digit[$one] . ' ';
            }
            
            $str .= $unit[$unitIndex] . ' ';
            $result = $str . $result;
        }
        
        $number = floor($number / 1000);
        $unitIndex++;
    }
    
    $result = trim($result);
    $result = ucfirst($result) . ' đồng';
    $result = preg_replace('/\s+/', ' ', $result);
    
    return $result;
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate số điện thoại
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10,11}$/', $phone);
}

// Generate mã số tự động
function generateCode($prefix, $lastNumber) {
    $number = intval($lastNumber) + 1;
    return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
}

// Upload file
function uploadFile($file, $targetDir = 'uploads/') {
    $uploadDir = BASE_PATH . '/public/' . $targetDir;
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $uploadDir . $fileName;
    
    // Kiểm tra file extension
    $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Định dạng file không hợp lệ!'];
    }
    
    // Kiểm tra kích thước
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File quá lớn! Tối đa 5MB'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'filename' => $fileName, 'path' => $targetFile];
    }
    
    return ['success' => false, 'message' => 'Lỗi upload file!'];
}

// Pagination
function pagination($total, $page, $limit, $url) {
    $totalPages = ceil($total / $limit);
    
    if ($totalPages <= 1) return '';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous
    if ($page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . ($page - 1) . '">‹</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">‹</span></li>';
    }
    
    // Pages
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=1">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next
    if ($page < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . ($page + 1) . '">›</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">›</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

// Log activity
function logActivity($userId, $action, $tableName, $recordId = null, $oldData = null, $newData = null) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, table_name, record_id, old_data, new_data, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt->execute([
            $userId,
            $action,
            $tableName,
            $recordId,
            $oldData ? json_encode($oldData) : null,
            $newData ? json_encode($newData) : null,
            $ipAddress,
            $userAgent
        ]);
    } catch (Exception $e) {
        // Log error nhưng không throw để không ảnh hưởng luồng chính
        error_log("Log activity error: " . $e->getMessage());
    }
}
?>
