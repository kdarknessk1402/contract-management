<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Quản lý nghề";

// Lấy danh sách khoa
$query = "SELECT * FROM faculties WHERE is_active = 1 ORDER BY faculty_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$faculties = $stmt->fetchAll();

// Xử lý thêm nghề
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $query = "INSERT INTO professions (faculty_id, profession_code, level, profession_name, academic_year) 
                  VALUES (:faculty_id, :profession_code, :level, :profession_name, :academic_year)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':faculty_id', $_POST['faculty_id']);
        $stmt->bindParam(':profession_code', $_POST['profession_code']);
        $stmt->bindParam(':level', $_POST['level']);
        $stmt->bindParam(':profession_name', $_POST['profession_name']);
        $stmt->bindParam(':academic_year', $_POST['academic_year']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Thêm nghề thành công!";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Mã nghề đã tồn tại trong khoa này!";
        } else {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
    }
    redirect('professions.php');
}

// Xử lý sửa nghề
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    try {
        $query = "UPDATE professions SET 
                  faculty_id = :faculty_id, 
                  profession_code = :profession_code,
                  level = :level,
                  profession_name = :profession_name, 
                  academic_year = :academic_year 
                  WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $_POST['id']);
        $stmt->bindParam(':faculty_id', $_POST['faculty_id']);
        $stmt->bindParam(':profession_code', $_POST['profession_code']);
        $stmt->bindParam(':level', $_POST['level']);
        $stmt->bindParam(':profession_name', $_POST['profession_name']);
        $stmt->bindParam(':academic_year', $_POST['academic_year']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật nghề thành công!";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Mã nghề đã tồn tại trong khoa này!";
        } else {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
    }
    redirect('professions.php');
}

// Xử lý xóa nghề
if (isset($_GET['delete'])) {
    try {
        $id = $_GET['delete'];
        $query = "UPDATE professions SET is_active = 0 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa nghề thành công!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    redirect('professions.php');
}

// Lấy danh sách nghề
$query = "SELECT p.*, f.faculty_name, f.faculty_code 
          FROM professions p 
          JOIN faculties f ON p.faculty_id = f.id 
          WHERE p.is_active = 1 
          ORDER BY f.faculty_name, p.level, p.profession_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$professions = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-briefcase"></i> Quản lý nghề</h2>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle"></i> Thêm nghề
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
            <table class="table table-hover DataTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã khoa</th>
                        <th>Mã nghề</th>
                        <th>Trình độ</th>
                        <th>Tên nghề</th>
                        <th>Năm học</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($professions as $profession): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><span class="badge bg-primary"><?php echo $profession['faculty_code']; ?></span></td>
                        <td><strong><?php echo $profession['profession_code']; ?></strong></td>
                        <td>
                            <span class="badge bg-<?php echo $profession['level'] === 'Cao đẳng' ? 'success' : 'warning'; ?>">
                                <?php echo $profession['level']; ?>
                            </span>
                        </td>
                        <td><?php echo $profession['profession_name']; ?></td>
                        <td><?php echo $profession['academic_year']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" 
                                    onclick="editProfession(<?php echo htmlspecialchars(json_encode($profession)); ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="?delete=<?php echo $profession['id']; ?>" 
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

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import nghề từ Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="profession_import.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        File Excel cần có các cột: <strong>faculty_code, profession_code, level, profession_name, academic_year</strong>
                        <br>
                        <small>Trình độ (level) phải là: <strong>Trung cấp</strong> hoặc <strong>Cao đẳng</strong></small>
                        <br>
                        <a href="uploads/templates/profession_template.xlsx" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-download"></i> Tải template mẫu
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel</label>
                        <div class="upload-area" onclick="document.getElementById('excel_file_profession').click()">
                            <i class="bi bi-cloud-upload" style="font-size: 3rem;"></i>
                            <p>Kéo thả file vào đây hoặc click để chọn</p>
                        </div>
                        <input type="file" class="form-control d-none" id="excel_file_profession" 
                               name="excel_file" accept=".xlsx,.xls" required>
                        <div class="file-info mt-2" style="display: none;">
                            <i class="bi bi-file-earmark-excel text-success"></i>
                            <span class="file-name"></span>
                            <span class="file-size text-muted"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thêm -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm nghề mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Khoa <span class="text-danger">*</span></label>
                        <select class="form-select" name="faculty_id" required>
                            <option value="">-- Chọn khoa --</option>
                            <?php foreach ($faculties as $faculty): ?>
                            <option value="<?php echo $faculty['id']; ?>">
                                <?php echo $faculty['faculty_code']; ?> - <?php echo $faculty['faculty_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mã nghề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="profession_code" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Trình độ <span class="text-danger">*</span></label>
                        <select class="form-select" name="level" required>
                            <option value="">-- Chọn trình độ --</option>
                            <option value="Trung cấp">Trung cấp</option>
                            <option value="Cao đẳng">Cao đẳng</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên nghề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="profession_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Năm học <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="academic_year" 
                               placeholder="VD: 2025-2026" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa nghề</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Khoa <span class="text-danger">*</span></label>
                        <select class="form-select" name="faculty_id" id="edit_faculty_id" required>
                            <option value="">-- Chọn khoa --</option>
                            <?php foreach ($faculties as $faculty): ?>
                            <option value="<?php echo $faculty['id']; ?>">
                                <?php echo $faculty['faculty_code']; ?> - <?php echo $faculty['faculty_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mã nghề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="profession_code" id="edit_profession_code" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Trình độ <span class="text-danger">*</span></label>
                        <select class="form-select" name="level" id="edit_level" required>
                            <option value="">-- Chọn trình độ --</option>
                            <option value="Trung cấp">Trung cấp</option>
                            <option value="Cao đẳng">Cao đẳng</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên nghề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="profession_name" id="edit_profession_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Năm học <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="academic_year" 
                               id="edit_academic_year" placeholder="VD: 2025-2026" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('excel_file_profession').addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        document.querySelector('.file-name').textContent = file.name;
        document.querySelector('.file-size').textContent = ' (' + (file.size / 1024).toFixed(2) + ' KB)';
        document.querySelector('.file-info').style.display = 'block';
    }
});

function editProfession(profession) {
    document.getElementById('edit_id').value = profession.id;
    document.getElementById('edit_faculty_id').value = profession.faculty_id;
    document.getElementById('edit_profession_code').value = profession.profession_code;
    document.getElementById('edit_level').value = profession.level;
    document.getElementById('edit_profession_name').value = profession.profession_name;
    document.getElementById('edit_academic_year').value = profession.academic_year;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>