<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Quản lý Khoa";

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $query = "DELETE FROM faculties WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Đã xóa khoa thành công!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Không thể xóa khoa! (Có thể đang có dữ liệu liên quan)";
    }
    redirect('faculties.php');
}

// Xử lý thêm/sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $faculty_code = trim($_POST['faculty_code']);
    $faculty_name = trim($_POST['faculty_name']);
    $description = trim($_POST['description']);
    
    try {
        if ($id) {
            // Sửa
            $query = "UPDATE faculties SET faculty_code = :code, faculty_name = :name, description = :desc WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $message = "Cập nhật khoa thành công!";
        } else {
            // Thêm
            $query = "INSERT INTO faculties (faculty_code, faculty_name, description) VALUES (:code, :name, :desc)";
            $stmt = $conn->prepare($query);
            $message = "Thêm khoa thành công!";
        }
        
        $stmt->bindParam(':code', $faculty_code);
        $stmt->bindParam(':name', $faculty_name);
        $stmt->bindParam(':desc', $description);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = $message;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: Mã khoa đã tồn tại!";
    }
    redirect('faculties.php');
}

// Lấy dữ liệu để sửa
$edit_data = null;
if (isset($_GET['edit'])) {
    $query = "SELECT * FROM faculties WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['edit']);
    $stmt->execute();
    $edit_data = $stmt->fetch();
}

// Lấy danh sách
$query = "SELECT * FROM faculties ORDER BY faculty_code";
$stmt = $conn->prepare($query);
$stmt->execute();
$faculties = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-building"></i> Quản lý Khoa</h2>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle"></i> Thêm Khoa
        </button>
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
                        <th>Mã khoa</th>
                        <th>Tên khoa</th>
                        <th>Mô tả</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($faculties as $faculty): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><strong><?php echo $faculty['faculty_code']; ?></strong></td>
                        <td><?php echo $faculty['faculty_name']; ?></td>
                        <td><?php echo $faculty['description']; ?></td>
                        <td><?php echo formatDate($faculty['created_at']); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" 
                                    onclick="editFaculty(<?php echo htmlspecialchars(json_encode($faculty)); ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="faculties.php?delete=<?php echo $faculty['id']; ?>" 
                               class="btn btn-sm btn-danger btn-delete">
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Khoa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="faculty_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Mã khoa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="faculty_code" id="faculty_code" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên khoa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="faculty_name" id="faculty_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-excel"></i> Import danh sách Khoa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="faculty_import.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Hướng dẫn:</strong><br>
                        1. Tải file mẫu Excel <a href="uploads/templates/faculty_template.xlsx" class="alert-link">tại đây</a><br>
                        2. Điền thông tin khoa (Mã khoa, Tên khoa, Mô tả)<br>
                        3. Upload file đã điền thông tin
                    </div>
                    
                    <div class="upload-area">
                        <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3">Kéo thả file Excel vào đây</h5>
                        <p class="text-muted">hoặc</p>
                        <input type="file" name="excel_file" id="excel_file_faculty" class="d-none" accept=".xlsx,.xls" required>
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('excel_file_faculty').click()">
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

<script>
function editFaculty(data) {
    document.getElementById('modalTitle').textContent = 'Sửa Khoa';
    document.getElementById('faculty_id').value = data.id;
    document.getElementById('faculty_code').value = data.faculty_code;
    document.getElementById('faculty_name').value = data.faculty_name;
    document.getElementById('description').value = data.description;
    new bootstrap.Modal(document.getElementById('addModal')).show();
}

// Reset form khi đóng modal
document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitle').textContent = 'Thêm Khoa';
    document.getElementById('faculty_id').value = '';
    document.getElementById('faculty_code').value = '';
    document.getElementById('faculty_name').value = '';
    document.getElementById('description').value = '';
});

// File upload handler
document.getElementById('excel_file_faculty').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(2);
        
        document.querySelector('.file-name').textContent = fileName;
        document.querySelector('.file-size').textContent = fileSize + ' KB';
        document.querySelector('.file-info').style.display = 'block';
    }
});
</script>

<?php include 'includes/footer.php'; ?>