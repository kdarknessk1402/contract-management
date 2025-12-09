<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Quản lý môn học/Mô đun";

// Lấy danh sách nghề
$query = "SELECT p.*, f.faculty_code 
          FROM professions p 
          JOIN faculties f ON p.faculty_id = f.id 
          WHERE p.is_active = 1 
          ORDER BY f.faculty_name, p.level, p.profession_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$professions = $stmt->fetchAll();

// Xử lý thêm môn học
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $query = "INSERT INTO subjects (profession_id, subject_code, subject_name, credit_hours) 
                  VALUES (:profession_id, :subject_code, :subject_name, :credit_hours)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':profession_id', $_POST['profession_id']);
        $stmt->bindParam(':subject_code', $_POST['subject_code']);
        $stmt->bindParam(':subject_name', $_POST['subject_name']);
        $stmt->bindParam(':credit_hours', $_POST['credit_hours']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Thêm môn học thành công!";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Mã môn học đã tồn tại trong nghề này!";
        } else {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
    }
    redirect('subjects.php');
}

// Xử lý sửa môn học
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    try {
        $query = "UPDATE subjects SET 
                  profession_id = :profession_id,
                  subject_code = :subject_code, 
                  subject_name = :subject_name,
                  credit_hours = :credit_hours
                  WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $_POST['id']);
        $stmt->bindParam(':profession_id', $_POST['profession_id']);
        $stmt->bindParam(':subject_code', $_POST['subject_code']);
        $stmt->bindParam(':subject_name', $_POST['subject_name']);
        $stmt->bindParam(':credit_hours', $_POST['credit_hours']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật môn học thành công!";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Mã môn học đã tồn tại trong nghề này!";
        } else {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
    }
    redirect('subjects.php');
}

// Xử lý xóa môn học
if (isset($_GET['delete'])) {
    try {
        $id = $_GET['delete'];
        $query = "UPDATE subjects SET is_active = 0 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa môn học thành công!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    redirect('subjects.php');
}

// Lấy danh sách môn học
$query = "SELECT s.*, p.profession_name, p.profession_code, p.level, f.faculty_code 
          FROM subjects s
          JOIN professions p ON s.profession_id = p.id
          JOIN faculties f ON p.faculty_id = f.id
          WHERE s.is_active = 1 
          ORDER BY f.faculty_name, p.level, p.profession_name, s.subject_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$subjects = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-book"></i> Quản lý môn học/Mô đun</h2>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle"></i> Thêm môn học
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
                        <th>Mã MH</th>
                        <th>Tên môn học/Mô đun</th>
                        <th>Số TC</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><span class="badge bg-primary"><?php echo $subject['faculty_code']; ?></span></td>
                        <td><span class="badge bg-info"><?php echo $subject['profession_code']; ?></span></td>
                        <td>
                            <span class="badge bg-<?php echo $subject['level'] === 'Cao đẳng' ? 'success' : 'warning'; ?>">
                                <?php echo $subject['level']; ?>
                            </span>
                        </td>
                        <td><strong><?php echo $subject['subject_code']; ?></strong></td>
                        <td><?php echo $subject['subject_name']; ?></td>
                        <td><?php echo $subject['credit_hours']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" 
                                    onclick="editSubject(<?php echo htmlspecialchars(json_encode($subject)); ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="?delete=<?php echo $subject['id']; ?>" 
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
                <h5 class="modal-title">Import môn học từ Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="subject_import.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        File Excel cần có các cột: <strong>profession_code, subject_code, subject_name, credit_hours</strong>
                        <br>
                        <a href="uploads/templates/subject_template.xlsx" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-download"></i> Tải template mẫu
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel</label>
                        <div class="upload-area" onclick="document.getElementById('excel_file_subject').click()">
                            <i class="bi bi-cloud-upload" style="font-size: 3rem;"></i>
                            <p>Kéo thả file vào đây hoặc click để chọn</p>
                        </div>
                        <input type="file" class="form-control d-none" id="excel_file_subject" 
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
                <h5 class="modal-title">Thêm môn học mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nghề <span class="text-danger">*</span></label>
                        <select class="form-select" name="profession_id" required>
                            <option value="">-- Chọn nghề --</option>
                            <?php foreach ($professions as $profession): ?>
                            <option value="<?php echo $profession['id']; ?>">
                                [<?php echo $profession['faculty_code']; ?>] 
                                <?php echo $profession['profession_code']; ?> - 
                                <?php echo $profession['profession_name']; ?> 
                                (<?php echo $profession['level']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mã môn học <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject_code" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên môn học <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số tín chỉ</label>
                        <input type="number" class="form-control" name="credit_hours" min="0">
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
                <h5 class="modal-title">Sửa môn học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nghề <span class="text-danger">*</span></label>
                        <select class="form-select" name="profession_id" id="edit_profession_id" required>
                            <option value="">-- Chọn nghề --</option>
                            <?php foreach ($professions as $profession): ?>
                            <option value="<?php echo $profession['id']; ?>">
                                [<?php echo $profession['faculty_code']; ?>] 
                                <?php echo $profession['profession_code']; ?> - 
                                <?php echo $profession['profession_name']; ?>
                                (<?php echo $profession['level']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mã môn học <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject_code" id="edit_subject_code" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên môn học <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject_name" id="edit_subject_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số tín chỉ</label>
                        <input type="number" class="form-control" name="credit_hours" id="edit_credit_hours" min="0">
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
document.getElementById('excel_file_subject').addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        document.querySelector('.file-name').textContent = file.name;
        document.querySelector('.file-size').textContent = ' (' + (file.size / 1024).toFixed(2) + ' KB)';
        document.querySelector('.file-info').style.display = 'block';
    }
});

function editSubject(subject) {
    document.getElementById('edit_id').value = subject.id;
    document.getElementById('edit_profession_id').value = subject.profession_id;
    document.getElementById('edit_subject_code').value = subject.subject_code;
    document.getElementById('edit_subject_name').value = subject.subject_name;
    document.getElementById('edit_credit_hours').value = subject.credit_hours || '';
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>