<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Quản lý hợp đồng thỉnh giảng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-file-earmark-text"></i> Hợp đồng thỉnh giảng
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Trang chủ</a>
                    </li>
                    <?php if ($_SESSION['role'] === 'giao_vu'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="lecturers.php"><i class="bi bi-people"></i> Giảng viên</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contracts.php"><i class="bi bi-file-text"></i> Hợp đồng</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Quản trị
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="faculties.php"><i class="bi bi-building"></i> Quản lý Khoa</a></li>
                            <li><a class="dropdown-item" href="professions.php"><i class="bi bi-briefcase"></i> Quản lý Nghề</a></li>
                            <li><a class="dropdown-item" href="subjects.php"><i class="bi bi-book"></i> Quản lý Môn học</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="users.php"><i class="bi bi-person"></i> Quản lý người dùng</a></li>
                            <li><a class="dropdown-item" href="hourly_rates.php"><i class="bi bi-cash"></i> Cấu hình thù lao</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="backup.php"><i class="bi bi-download"></i> Sao lưu dữ liệu</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo $_SESSION['full_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Hồ sơ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <main class="container-fluid py-4">
