<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireLogin();

$database = new Database();
$conn = $database->getConnection();

$page_title = "Trang chủ";

// Lấy thống kê
$faculty_filter = $_SESSION['role'] === 'giao_vu' ? " WHERE l.faculty_id = :faculty_id" : "";

// Số giảng viên
$query = "SELECT COUNT(*) as total FROM lecturers l" . $faculty_filter;
$stmt = $conn->prepare($query);
if ($_SESSION['role'] === 'giao_vu') {
    $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
}
$stmt->execute();
$total_lecturers = $stmt->fetch()['total'];

// Số hợp đồng
$faculty_filter_contracts = $_SESSION['role'] === 'giao_vu' ? " WHERE c.faculty_id = :faculty_id" : "";
$query = "SELECT COUNT(*) as total FROM contracts c" . $faculty_filter_contracts;
$stmt = $conn->prepare($query);
if ($_SESSION['role'] === 'giao_vu') {
    $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
}
$stmt->execute();
$total_contracts = $stmt->fetch()['total'];

// Hợp đồng trong tháng
$query = "SELECT COUNT(*) as total FROM contracts c
          WHERE MONTH(c.created_at) = MONTH(CURRENT_DATE()) 
          AND YEAR(c.created_at) = YEAR(CURRENT_DATE())" . 
          ($_SESSION['role'] === 'giao_vu' ? " AND c.faculty_id = :faculty_id" : "");
$stmt = $conn->prepare($query);
if ($_SESSION['role'] === 'giao_vu') {
    $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
}
$stmt->execute();
$contracts_this_month = $stmt->fetch()['total'];

// Tổng giá trị hợp đồng
$query = "SELECT SUM(c.total_amount) as total FROM contracts c
          WHERE c.status != 'cancelled'" . 
          ($_SESSION['role'] === 'giao_vu' ? " AND c.faculty_id = :faculty_id" : "");
$stmt = $conn->prepare($query);
if ($_SESSION['role'] === 'giao_vu') {
    $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
}
$stmt->execute();
$total_value = $stmt->fetch()['total'] ?? 0;

// Hợp đồng mới nhất
$query = "SELECT c.*, l.full_name as lecturer_name, s.subject_name, f.faculty_name 
          FROM contracts c 
          JOIN lecturers l ON c.lecturer_id = l.id 
          JOIN subjects s ON c.subject_id = s.id 
          JOIN faculties f ON c.faculty_id = f.id" . 
          ($_SESSION['role'] === 'giao_vu' ? " WHERE c.faculty_id = :faculty_id" : "") . 
          " ORDER BY c.created_at DESC LIMIT 10";
$stmt = $conn->prepare($query);
if ($_SESSION['role'] === 'giao_vu') {
    $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
}
$stmt->execute();
$recent_contracts = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">Xin chào, <strong><?php echo $_SESSION['full_name']; ?></strong>! 
        <?php if ($_SESSION['role'] === 'giao_vu'): ?>
            <?php 
            $fq = "SELECT faculty_name FROM faculties WHERE id = :fid";
            $fs = $conn->prepare($fq);
            $fs->bindParam(':fid', $_SESSION['faculty_id']);
            $fs->execute();
            $faculty = $fs->fetch();
            ?>
            (<?php echo $faculty['faculty_name'] ?? ''; ?>)
        <?php endif; ?>
        </p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-2">Giảng viên</h6>
                        <h2 class="mb-0"><?php echo number_format($total_lecturers); ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card dashboard-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-2">Tổng hợp đồng</h6>
                        <h2 class="mb-0"><?php echo number_format($total_contracts); ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-file-text"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card dashboard-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-2">HĐ tháng này</h6>
                        <h2 class="mb-0"><?php echo number_format($contracts_this_month); ?></h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card dashboard-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-2">Tổng giá trị</h6>
                        <h2 class="mb-0"><?php echo number_format($total_value / 1000000, 1); ?>M</h2>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Hợp đồng mới nhất</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Số HĐ</th>
                                <th>Giảng viên</th>
                                <th>Môn học</th>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                <th>Khoa</th>
                                <?php endif; ?>
                                <th>Số giờ</th>
                                <th>Tổng tiền</th>
                                <th>Ngày tạo</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_contracts) > 0): ?>
                                <?php foreach ($recent_contracts as $contract): ?>
                                <tr>
                                    <td><strong><?php echo $contract['contract_number']; ?></strong></td>
                                    <td><?php echo $contract['lecturer_name']; ?></td>
                                    <td><?php echo $contract['subject_name']; ?></td>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <td><?php echo $contract['faculty_name']; ?></td>
                                    <?php endif; ?>
                                    <td><?php echo $contract['total_hours']; ?> giờ</td>
                                    <td><strong><?php echo formatMoney($contract['total_amount']); ?> đ</strong></td>
                                    <td><?php echo formatDate($contract['created_at']); ?></td>
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo $_SESSION['role'] === 'admin' ? '8' : '7'; ?>" class="text-center text-muted">
                                        Chưa có hợp đồng nào
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>