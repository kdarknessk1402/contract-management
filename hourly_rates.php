<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Cấu hình mức thù lao";

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        foreach ($_POST['rates'] as $id => $amount) {
            $amount = (float)str_replace(['.', ','], '', $amount);
            $query = "UPDATE hourly_rates SET amount = :amount WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        
        $conn->commit();
        $_SESSION['success'] = "Cập nhật mức thù lao thành công!";
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    redirect('hourly_rates.php');
}

// Lấy danh sách mức thù lao
$query = "SELECT * FROM hourly_rates ORDER BY academic_year DESC, 
          FIELD(education_level, 'Tiến sĩ', 'Thạc sĩ', 'Đại học'), 
          FIELD(rate_type, 'standard', 'high')";
$stmt = $conn->prepare($query);
$stmt->execute();
$rates = $stmt->fetchAll();

// Group by academic year
$rates_by_year = [];
foreach ($rates as $rate) {
    $rates_by_year[$rate['academic_year']][] = $rate;
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-cash-stack"></i> Cấu hình mức thù lao</h2>
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

<form method="POST" action="">
    <?php foreach ($rates_by_year as $year => $year_rates): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-calendar"></i> Niên khóa <?php echo $year; ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="25%">Trình độ</th>
                            <th width="25%">Loại mức</th>
                            <th width="35%">Mức thù lao (đồng/giờ)</th>
                            <th width="15%">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($year_rates as $rate): ?>
                        <tr>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $rate['education_level']; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $rate['rate_type'] === 'standard' ? 'Mức chuẩn' : 'Mức cao'; ?>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           name="rates[<?php echo $rate['id']; ?>]" 
                                           value="<?php echo formatMoney($rate['amount']); ?>"
                                           required>
                                    <span class="input-group-text">đồng</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($rate['is_active']): ?>
                                <span class="badge bg-success">Đang dùng</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Không dùng</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-save"></i> Lưu thay đổi
        </button>
    </div>
</form>

<?php include 'includes/footer.php'; ?>