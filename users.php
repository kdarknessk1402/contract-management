<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Quản lý người dùng";

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Không thể xóa tài khoản đang đăng nhập!";
    } else {
        try {
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Đã xóa người dùng thành công!";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Không thể xóa người dùng!";
        }
    }
    redirect('users.php');
}

// Xử lý thêm/sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $faculty_id = $role === 'giao_vu' ? $_POST['faculty_id'] : null;
    $password = $_POST['password'] ?? '';
    
    try {
        if ($id) {
            // Sửa
            if (!empty($password)) {
                $query = "UPDATE users SET username = :username, password = :password, full_name = :fname, 
                          email = :email, role = :role, faculty_id = :fid WHERE id = :id";
            } else {
                $query = "UPDATE users SET username = :username, full_name = :fname, 
                          email = :email, role = :role, faculty_id = :fid WHERE id = :id";
            }
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            if (!empty($password)) {
                $stmt->bindParam(':password', $password);
            }
            $message = "Cập nhật người dùng thành công!";
        } else {
            // Thêm
            if (empty($password)) {
                throw new Exception('Mật khẩu không được để trống!');
            }
            $query = "INSERT INTO users (username, password, full_name, email, role, faculty_id) 
                      VALUES (:username, :password, :fname, :email, :role, :fid)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':password', $password);
            $message = "Thêm người dùng thành công!";
        }
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fname', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':fid', $faculty_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = $message;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: Tên đăng nhập đã tồn tại!";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    redirect('users.php');
}

// Lấy danh sách khoa
$query = "SELECT * FROM faculties ORDER BY faculty_code";
$stmt = $conn->prepare($query);
$stmt->execute();
$faculties = $stmt->fetchAll();

// Lấy danh sách người dùng
$query = "SELECT u.*, f.faculty_name FROM users u 
          LEFT JOIN faculties f ON u.faculty_id = f.id 
          ORDER BY u.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-people"></i> Quản lý người dùng</h2>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle"></i> Thêm người dùng
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
                        <th>Tên đăng nhập</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Khoa</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><strong><?php echo $user['username']; ?></strong></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                            <span class="badge bg-primary">Giáo vụ</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $user['faculty_name'] ?? '-'; ?></td>
                        <td><?php echo formatDate($user['created_at']); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" 
                                    onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="users.php?delete=<?php echo $user['id']; ?>" 
                               class="btn btn-sm btn-danger btn-delete">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
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
                    <h5 class="modal-title" id="modalTitle">Thêm người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="user_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger" id="pwd_required">*</span></label>
                        <input type="password" class="form-control" name="password" id="password">
                        <small class="text-muted" id="pwd_note" style="display:none;">Để trống nếu không đổi mật khẩu</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" id="full_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select class="form-select" name="role" id="role" required onchange="toggleFaculty()">
                            <option value="admin">Admin</option>
                            <option value="giao_vu">Giáo vụ</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="faculty_group" style="display:none;">
                        <label class="form-label">Khoa <span class="text-danger">*</span></label>
                        <select class="form-select" name="faculty_id" id="faculty_id">
                            <option value="">-- Chọn khoa --</option>
                            <?php foreach ($faculties as $faculty): ?>
                            <option value="<?php echo $faculty['id']; ?>">
                                <?php echo $faculty['faculty_code']; ?> - <?php echo $faculty['faculty_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
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

<script>
function toggleFaculty() {
    const role = document.getElementById('role').value;
    const facultyGroup = document.getElementById('faculty_group');
    const facultySelect = document.getElementById('faculty_id');
    
    if (role === 'giao_vu') {
        facultyGroup.style.display = 'block';
        facultySelect.required = true;
    } else {
        facultyGroup.style.display = 'none';
        facultySelect.required = false;
        facultySelect.value = '';
    }
}

function editUser(data) {
    document.getElementById('modalTitle').textContent = 'Sửa người dùng';
    document.getElementById('user_id').value = data.id;
    document.getElementById('username').value = data.username;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('pwd_required').style.display = 'none';
    document.getElementById('pwd_note').style.display = 'block';
    document.getElementById('full_name').value = data.full_name;
    document.getElementById('email').value = data.email || '';
    document.getElementById('role').value = data.role;
    document.getElementById('faculty_id').value = data.faculty_id || '';
    toggleFaculty();
    new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitle').textContent = 'Thêm người dùng';
    this.querySelector('form').reset();
    document.getElementById('user_id').value = '';
    document.getElementById('password').required = true;
    document.getElementById('pwd_required').style.display = 'inline';
    document.getElementById('pwd_note').style.display = 'none';
    toggleFaculty();
});

toggleFaculty();
</script>

<?php include 'includes/footer.php'; ?>