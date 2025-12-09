<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireLogin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Hồ sơ cá nhân";

// Lấy thông tin user
$query = "SELECT u.*, f.faculty_name FROM users u 
          LEFT JOIN faculties f ON u.faculty_id = f.id 
          WHERE u.id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {
        // Nếu đổi mật khẩu
        if (!empty($new_password)) {
            // Kiểm tra mật khẩu cũ
            if ($old_password !== $user['password']) {
                throw new Exception('Mật khẩu cũ không đúng!');
            }
            
            if ($new_password !== $confirm_password) {
                throw new Exception('Mật khẩu mới không khớp!');
            }
            
            if (strlen($new_password) < 6) {
                throw new Exception('Mật khẩu mới phải có ít nhất 6 ký tự!');
            }
            
            $query = "UPDATE users SET full_name = :fname, email = :email, password = :password 
                      WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':password', $new_password);
        } else {
            $query = "UPDATE users SET full_name = :fname, email = :email WHERE id = :id";
            $stmt = $conn->prepare($query);
        }
        
        $stmt->bindParam(':fname', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $_SESSION['full_name'] = $full_name;
            $_SESSION['success'] = "Cập nhật thông tin thành công!";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    redirect('profile.php');
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-person-circle"></i> Hồ sơ cá nhân</h2>
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

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-person-circle" style="font-size: 5rem; color: #6c757d;"></i>
                <h4 class="mt-3"><?php echo $user['full_name']; ?></h4>
                <p class="text-muted mb-1">@<?php echo $user['username']; ?></p>
                <?php if ($user['role'] === 'admin'): ?>
                <span class="badge bg-danger">Admin</span>
                <?php else: ?>
                <span class="badge bg-primary">Giáo vụ</span>
                <?php endif; ?>
                
                <?php if ($user['faculty_name']): ?>
                <hr>
                <p class="mb-0"><strong>Khoa:</strong> <?php echo $user['faculty_name']; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Cập nhật thông tin</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                        <small class="text-muted">Không thể thay đổi tên đăng nhập</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" 
                               value="<?php echo $user['full_name']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" 
                               value="<?php echo $user['email']; ?>">
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Đổi mật khẩu (Để trống nếu không đổi)</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu cũ</label>
                        <input type="password" class="form-control" name="old_password">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" name="new_password">
                        <small class="text-muted">Tối thiểu 6 ký tự</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" name="confirm_password">
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>