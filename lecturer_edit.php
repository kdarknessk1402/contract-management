<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireGiaoVu();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Sửa giảng viên";

if (!isset($_GET['id'])) {
    redirect('lecturers.php');
}

$id = $_GET['id'];

// Lấy thông tin giảng viên
$query = "SELECT * FROM lecturers WHERE id = :id AND faculty_id = :faculty_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['error'] = "Không tìm thấy giảng viên!";
    redirect('lecturers.php');
}

$lecturer = $stmt->fetch();

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "UPDATE lecturers SET 
            full_name = :full_name, gender = :gender, birth_year = :birth_year, 
            id_number = :id_number, id_issued_date = :id_issued_date, id_issued_place = :id_issued_place,
            education_level = :education_level, major = :major, pedagogy = :pedagogy,
            address = :address, phone = :phone, email = :email,
            bank_account = :bank_account, bank_name = :bank_name, bank_branch = :bank_branch, tax_code = :tax_code
            WHERE id = :id AND faculty_id = :faculty_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
        $stmt->bindParam(':full_name', $_POST['full_name']);
        $stmt->bindParam(':gender', $_POST['gender']);
        $stmt->bindParam(':birth_year', $_POST['birth_year']);
        $stmt->bindParam(':id_number', $_POST['id_number']);
        $stmt->bindParam(':id_issued_date', $_POST['id_issued_date']);
        $stmt->bindParam(':id_issued_place', $_POST['id_issued_place']);
        $stmt->bindParam(':education_level', $_POST['education_level']);
        $stmt->bindParam(':major', $_POST['major']);
        $stmt->bindParam(':pedagogy', $_POST['pedagogy']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':bank_account', $_POST['bank_account']);
        $stmt->bindParam(':bank_name', $_POST['bank_name']);
        $stmt->bindParam(':bank_branch', $_POST['bank_branch']);
        $tax_code = !empty($_POST['tax_code']) ? $_POST['tax_code'] : $_POST['id_number'];
        $stmt->bindParam(':tax_code', $tax_code);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật giảng viên thành công!";
            redirect('lecturers.php');
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-pencil"></i> Sửa giảng viên</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="lecturers.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="">
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin cá nhân</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name" 
                           value="<?php echo $lecturer['full_name']; ?>" required>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                    <select class="form-select" name="gender" required>
                        <option value="Nam" <?php echo $lecturer['gender'] === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo $lecturer['gender'] === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Năm sinh</label>
                    <input type="number" class="form-control" name="birth_year" 
                           value="<?php echo $lecturer['birth_year']; ?>" min="1950" max="2010">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Số CCCD <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="id_number" 
                           value="<?php echo $lecturer['id_number']; ?>" maxlength="12" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày cấp</label>
                    <input type="date" class="form-control" name="id_issued_date" 
                           value="<?php echo $lecturer['id_issued_date']; ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nơi cấp</label>
                    <input type="text" class="form-control" name="id_issued_place" 
                           value="<?php echo $lecturer['id_issued_place']; ?>">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Trình độ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Trình độ <span class="text-danger">*</span></label>
                    <select class="form-select" name="education_level" required>
                        <option value="Đại học" <?php echo $lecturer['education_level'] === 'Đại học' ? 'selected' : ''; ?>>Đại học</option>
                        <option value="Thạc sĩ" <?php echo $lecturer['education_level'] === 'Thạc sĩ' ? 'selected' : ''; ?>>Thạc sĩ</option>
                        <option value="Tiến sĩ" <?php echo $lecturer['education_level'] === 'Tiến sĩ' ? 'selected' : ''; ?>>Tiến sĩ</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Chuyên ngành</label>
                    <input type="text" class="form-control" name="major" 
                           value="<?php echo $lecturer['major']; ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sư phạm</label>
                    <select class="form-select" name="pedagogy">
                        <option value="">-- Chọn --</option>
                        <option value="Có" <?php echo $lecturer['pedagogy'] === 'Có' ? 'selected' : ''; ?>>Có</option>
                        <option value="Không" <?php echo $lecturer['pedagogy'] === 'Không' ? 'selected' : ''; ?>>Không</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Liên hệ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <textarea class="form-control" name="address" rows="2"><?php echo $lecturer['address']; ?></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Điện thoại</label>
                    <input type="text" class="form-control" name="phone" 
                           value="<?php echo $lecturer['phone']; ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo $lecturer['email']; ?>">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Thông tin ngân hàng</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Số tài khoản</label>
                    <input type="text" class="form-control" name="bank_account" 
                           value="<?php echo $lecturer['bank_account']; ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngân hàng</label>
                    <input type="text" class="form-control" name="bank_name" 
                           value="<?php echo $lecturer['bank_name']; ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Chi nhánh</label>
                    <input type="text" class="form-control" name="bank_branch" 
                           value="<?php echo $lecturer['bank_branch']; ?>">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Mã số thuế</label>
                    <input type="text" class="form-control" name="tax_code" 
                           value="<?php echo $lecturer['tax_code']; ?>">
                    <small class="text-muted">Để trống sẽ dùng số CCCD</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end">
        <button type="button" class="btn btn-secondary" onclick="history.back()">
            <i class="bi bi-x-circle"></i> Hủy
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Lưu thay đổi
        </button>
    </div>
</form>

<?php include 'includes/footer.php'; ?>