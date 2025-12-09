<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireLogin();

// Chỉ giáo vụ mới được truy cập
if ($_SESSION['role'] !== 'giao_vu' && $_SESSION['role'] !== 'admin') {
    redirect('index.php');
}

$database = new Database();
$conn = $database->getConnection();
$page_title = "Quản lý giảng viên";

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM lecturers WHERE id = :id AND faculty_id = :faculty_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã xóa giảng viên thành công!";
    }
    redirect('lecturers.php');
}

// Lấy danh sách giảng viên
$query = "SELECT l.*, f.faculty_name 
          FROM lecturers l 
          JOIN faculties f ON l.faculty_id = f.id 
          WHERE l.faculty_id = :faculty_id 
          ORDER BY l.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();
$lecturers = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-people"></i> Quản lý giảng viên</h2>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <a href="lecturer_add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm giảng viên
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover data-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ tên</th>
                        <th>Giới tính</th>
                        <th>Số CCCD</th>
                        <th>Trình độ</th>
                        <th>Chuyên ngành</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($lecturers as $lecturer): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><?php echo $lecturer['full_name']; ?></td>
                        <td><?php echo $lecturer['gender']; ?></td>
                        <td><?php echo $lecturer['id_number']; ?></td>
                        <td><?php echo $lecturer['education_level']; ?></td>
                        <td><?php echo $lecturer['major']; ?></td>
                        <td><?php echo $lecturer['phone']; ?></td>
                        <td><?php echo $lecturer['email']; ?></td>
                        <td>
                            <a href="lecturer_edit.php?id=<?php echo $lecturer['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="lecturers.php?delete=<?php echo $lecturer['id']; ?>" 
                               class="btn btn-sm btn-danger btn-delete" title="Xóa">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-excel"></i> Import danh sách giảng viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="lecturer_import.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Hướng dẫn:</strong><br>
                        1. Tải file mẫu Excel <a href="templates/lecturer_template.xlsx" class="alert-link">tại đây</a><br>
                        2. Điền thông tin giảng viên vào file mẫu<br>
                        3. Upload file đã điền thông tin
                    </div>
                    
                    <div class="upload-area">
                        <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3">Kéo thả file Excel vào đây</h5>
                        <p class="text-muted">hoặc</p>
                        <input type="file" name="excel_file" id="excel_file" class="d-none" 
                               accept=".xlsx,.xls" required>
                        <button type="button" class="btn btn-outline-primary" 
                                onclick="document.getElementById('excel_file').click()">
                            Chọn file
                        </button>
                    </div>
                    
                    <div class="file-info mt-3" style="display: none;">
                        <div class="alert alert-success">
                            <i class="bi bi-file-earmark-check"></i> 
                            File: <span class="file-name"></span> (<span class="file-size"></span>)
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload và Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
