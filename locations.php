<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Quản lý cơ sở";

// Xử lý thêm cơ sở
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $query = "INSERT INTO locations (location_name, location_code) VALUES (:location_name, :location_code)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':location_name', $_POST['location_name']);
        $stmt->bindParam(':location_code', $_POST['location_code']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Thêm cơ sở thành công!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    redirect('locations.php');
}

// Xử lý sửa cơ sở
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    try {
        $query = "UPDATE locations SET location_name = :location_name, location_code = :location_code WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $_POST['id']);
        $stmt->bindParam(':location_name', $_POST['location_name']);
        $stmt->bindParam(':location_code', $_POST['location_code']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật cơ sở thành công!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    redirect('locations.php');
}

// Xử lý xóa cơ sở
if (isset($_GET['delete'])) {
    try {
        $id = $_GET['delete'];
        $query = "UPDATE locations SET is_active = 0 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa cơ sở thành công!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    redirect('locations.php');
}

// Lấy danh sách cơ sở
$query = "SELECT * FROM locations WHERE is_active = 1 ORDER BY location_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$locations = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-geo-alt"></i> Quản lý cơ sở</h2>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle"></i> Thêm cơ sở
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
                        <th>Mã cơ sở</th>
                        <th>Tên cơ sở</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($locations as $location): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><span class="badge bg-primary"><?php echo $location['location_code']; ?></span></td>
                        <td><?php echo $location['location_name']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" 
                                    onclick="editLocation(<?php echo htmlspecialchars(json_encode($location)); ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="?delete=<?php echo $location['id']; ?>" 
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

<!-- Modal Thêm -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm cơ sở mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mã cơ sở <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="location_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên cơ sở <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="location_name" required>
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
                <h5 class="modal-title">Sửa cơ sở</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mã cơ sở <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="location_code" id="edit_location_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên cơ sở <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="location_name" id="edit_location_name" required>
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
function editLocation(location) {
    document.getElementById('edit_id').value = location.id;
    document.getElementById('edit_location_code').value = location.location_code;
    document.getElementById('edit_location_name').value = location.location_name;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>