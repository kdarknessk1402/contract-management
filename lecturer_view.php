<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireGiaoVu();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Chi tiết giảng viên";

if (!isset($_GET['id'])) {
    redirect('lecturers.php');
}

$id = $_GET['id'];

// Lấy thông tin giảng viên
$query = "SELECT l.*, f.faculty_name FROM lecturers l 
          JOIN faculties f ON l.faculty_id = f.id 
          WHERE l.id = :id AND l.faculty_id = :faculty_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['error'] = "Không tìm thấy giảng viên!";
    redirect('lecturers.php');
}

$lecturer = $stmt->fetch();

// Lấy danh sách hợp đồng của giảng viên
$query = "SELECT c.*, s.subject_name FROM contracts c 
          JOIN subjects s ON c.subject_id = s.id 
          WHERE c.lecturer_id = :lecturer_id 
          ORDER BY c.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':lecturer_id', $id);
$stmt->execute();
$contracts = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-person-badge"></i> Chi tiết giảng viên</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="lecturer_edit.php?id=<?php echo $id; ?>" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Sửa
        </a>
        <a href="lecturers.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <i class="bi bi-person-circle" style="font-size: 5rem; color: #6c757d;"></i>
                <h4 class="mt-3"><?php echo $lecturer['full_name']; ?></h4>
                <span class="badge bg-info"><?php echo $lecturer['education_level']; ?></span>
                <hr>
                <p class="mb-1"><strong>Khoa:</strong> <?php echo $lecturer['faculty_name']; ?></p>
                <p class="mb-1"><strong>Giới tính:</strong> <?php echo $lecturer['gender']; ?></p>
                <?php if ($lecturer['birth_year']): ?>
                <p class="mb-0"><strong>Năm sinh:</strong> <?php echo $lecturer['birth_year']; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thông tin cá nhân</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Số CCCD:</strong> <?php echo $lecturer['id_number']; ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Ngày cấp:</strong> <?php echo $lecturer['id_issued_date'] ? formatDate($lecturer['id_issued_date']) : '-'; ?>
                    </div>
                    <div class="col-md-12 mb-2">
                        <strong>Nơi cấp:</strong> <?php echo $lecturer['id_issued_place'] ?: '-'; ?>
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
                    <div class="col-md-4 mb-2">
                        <strong>Trình độ:</strong> <?php echo $lecturer['education_level']; ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Chuyên ngành:</strong> <?php echo $lecturer['major'] ?: '-'; ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Sư phạm:</strong> <?php echo $lecturer['pedagogy'] ?: '-'; ?>
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
                    <div class="col-md-12 mb-2">
                        <strong>Địa chỉ:</strong> <?php echo $lecturer['address'] ?: '-'; ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Điện thoại:</strong> <?php echo $lecturer['phone'] ?: '-'; ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Email:</strong> <?php echo $lecturer['email'] ?: '-'; ?>
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
                    <div class="col-md-4 mb-2">
                        <strong>Số TK:</strong> <?php echo $lecturer['bank_account'] ?: '-'; ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Ngân hàng:</strong> <?php echo $lecturer['bank_name'] ?: '-'; ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Chi nhánh:</strong> <?php echo $lecturer['bank_branch'] ?: '-'; ?>
                    </div>
                    <div class="col-md-12 mb-2">
                        <strong>MST:</strong> <?php echo $lecturer['tax_code'] ?: '-'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-file-text"></i> Lịch sử hợp đồng</h5>
    </div>
    <div class="card-body">
        <?php if (count($contracts) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Số HĐ</th>
                        <th>Môn học</th>
                        <th>Số giờ</th>
                        <th>Tổng tiền</th>
                        <th>Ngày ký</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($contracts as $contract): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><strong><?php echo $contract['contract_number']; ?></strong></td>
                        <td><?php echo $contract['subject_name']; ?></td>
                        <td><?php echo $contract['total_hours']; ?> giờ</td>
                        <td><strong><?php echo formatMoney($contract['total_amount']); ?> đ</strong></td>
                        <td><?php echo formatDate($contract['contract_date']); ?></td>
                        <td>
                            <?php
                            $status_class = [
                                'draft' => 'secondary',
                                'approved' => 'success',
                                'completed' => 'info',
                                'cancelled' => 'danger'
                            ];
                            $status_text = [
                                'draft' => 'Nháp',
                                'approved' => 'Đã duyệt',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy'
                            ];
                            ?>
                            <span class="badge bg-<?php echo $status_class[$contract['status']]; ?>">
                                <?php echo $status_text[$contract['status']]; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
            <p class="text-muted mt-3">Chưa có hợp đồng nào</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>