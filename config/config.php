<?php
// Cấu hình múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Cấu hình hiển thị lỗi (development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hàm redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Hàm format ngày tháng
function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

// Hàm format tiền
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.');
}

// Hàm format datetime
function formatDateTime($datetime) {
    if (empty($datetime)) return '-';
    return date('d/m/Y H:i', strtotime($datetime));
}

// Hàm kiểm tra quyền
function hasPermission($required_role) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    
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
?>