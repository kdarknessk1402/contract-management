<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireGiaoVu();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Thêm giảng viên";

// Xử lý thêm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "INSERT INTO lecturers (
            faculty_id, full_name, gender, birth_year, id_number, 
            id_issued_date, id_issued_place, education_level, major, pedagogy,
            address, phone, email, bank_account, bank_name, bank_branch, tax_code
        ) VALUES (
            :faculty_id, :full_name, :gender, :birth_year, :id_number,
            :id_issued_date, :id_issued_place, :education_level, :major, :pedagogy,
            :address, :phone, :email, :bank_account, :bank_name, :bank_branch, :tax_code
        )";
        
        $stmt = $conn->prepare($query);
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
            $_SESSION['success'] = "Thêm giảng viên thành công!";
            redirect('lecturers.php');
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: Số CCCD đã tồn tại!";
    }
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-person-plus"></i> Thêm giảng viên</h2>
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
                    <input type="text" class="form-control" name="full_name" required>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                    <select class="form-select" name="gender" required>
                        <option value="">-- Chọn --</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Năm sinh</label>
                    <input type="number" class="form-control" name="birth_year" min="1950" max="2010">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Số CCCD <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="id_number" maxlength="12" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày cấp</label>
                    <input type="date" class="form-control" name="id_issued_date">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nơi cấp</label>
                    <input type="text" class="form-control" name="id_issued_place">
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
                        <option value="">-- Chọn --</option>
                        <option value="Đại học">Đại học</option>
                        <option value="Thạc sĩ">Thạc sĩ</option>
                        <option value="Tiến sĩ">Tiến sĩ</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Chuyên ngành</label>
                    <input type="text" class="form-control" name="major">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sư phạm</label>
                    <select class="form-select" name="pedagogy">
                        <option value="">-- Chọn --</option>
                        <option value="Có">Có</option>
                        <option value="Không">Không</option>
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
                    <textarea class="form-control" name="address" rows="2"></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Điện thoại</label>
                    <input type="text" class="form-control" name="phone">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
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
                    <input type="text" class="form-control" name="bank_account">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngân hàng</label>
                    <input type="text" class="form-control" name="bank_name">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Chi nhánh</label>
                    <input type="text" class="form-control" name="bank_branch">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Mã số thuế</label>
                    <input type="text" class="form-control" name="tax_code">
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
            <i class="bi bi-save"></i> Lưu giảng viên
        </button>
    </div>
</form>

<?php include 'includes/footer.php'; ?>