<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireLogin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Quản lý hợp đồng";

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM contracts WHERE id = :id AND faculty_id = :faculty_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã xóa hợp đồng thành công!";
    }
    redirect('contracts.php');
}

// Lấy danh sách hợp đồng
$query = "SELECT c.*, l.full_name as lecturer_name, s.subject_name, 
          p.profession_name, f.faculty_name, l.education_level
          FROM contracts c 
          JOIN lecturers l ON c.lecturer_id = l.id 
          JOIN subjects s ON c.subject_id = s.id
          JOIN professions p ON c.profession_id = p.id
          JOIN faculties f ON c.faculty_id = f.id 
          WHERE c.faculty_id = :faculty_id 
          ORDER BY c.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();
$contracts = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-file-text"></i> Quản lý hợp đồng</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="contract_create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo hợp đồng mới
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
                        <th>Số HĐ</th>
                        <th>Giảng viên</th>
                        <th>Môn học</th>
                        <th>Lớp</th>
                        <th>Số giờ</th>
                        <th>Thù lao/giờ</th>
                        <th>Tổng tiền</th>
                        <th>Ngày bắt đầu</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($contracts as $contract): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><?php echo $contract['contract_number']; ?></td>
                        <td><?php echo $contract['lecturer_name']; ?></td>
                        <td><?php echo $contract['subject_name']; ?></td>
                        <td><?php echo $contract['class_code']; ?></td>
                        <td><?php echo $contract['total_hours']; ?></td>
                        <td><?php echo formatMoney($contract['hourly_rate']); ?>đ</td>
                        <td><strong><?php echo formatMoney($contract['total_amount']); ?>đ</strong></td>
                        <td><?php echo formatDate($contract['start_date']); ?></td>
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
                        <td>
                            <a href="contract_print.php?id=<?php echo $contract['id']; ?>" 
                               class="btn btn-sm btn-info" title="In hợp đồng" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="contract_edit.php?id=<?php echo $contract['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($contract['status'] === 'draft'): ?>
                            <a href="contracts.php?delete=<?php echo $contract['id']; ?>" 
                               class="btn btn-sm btn-danger btn-delete" title="Xóa">
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

<?php include 'includes/footer.php'; ?>
