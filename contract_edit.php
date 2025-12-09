<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireGiaoVu();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Sửa hợp đồng";

if (!isset($_GET['id'])) {
    redirect('contracts.php');
}

$id = $_GET['id'];

// Lấy thông tin hợp đồng
$query = "SELECT c.* FROM contracts c 
          WHERE c.id = :id AND c.faculty_id = :faculty_id AND c.status = 'draft'";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['error'] = "Không tìm thấy hợp đồng hoặc hợp đồng đã được duyệt!";
    redirect('contracts.php');
}

$contract = $stmt->fetch();

// Lấy danh sách giảng viên
$query = "SELECT * FROM lecturers WHERE faculty_id = :faculty_id AND is_active = 1 ORDER BY full_name";
$stmt = $conn->prepare($query);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();
$lecturers = $stmt->fetchAll();

// Lấy danh sách nghề
$query = "SELECT * FROM professions WHERE faculty_id = :faculty_id AND is_active = 1 ORDER BY profession_name";
$stmt = $conn->prepare($query);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();
$professions = $stmt->fetchAll();

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $lecturer_id = $_POST['lecturer_id'];
        $profession_id = $_POST['profession_id'];
        $subject_id = $_POST['subject_id'];
        $class_code = trim($_POST['class_code']);
        $total_hours = (int)$_POST['total_hours'];
        $hourly_rate = (float)str_replace(['.', ','], '', $_POST['hourly_rate']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $academic_year = $_POST['academic_year'];
        $semester = $_POST['semester'];
        $contract_date = $_POST['contract_date'];
        
        $total_amount = $total_hours * $hourly_rate;
        
        $query = "UPDATE contracts SET 
                  lecturer_id = :lecturer_id, profession_id = :profession_id, subject_id = :subject_id,
                  class_code = :class_code, total_hours = :total_hours, hourly_rate = :hourly_rate,
                  total_amount = :total_amount, start_date = :start_date, end_date = :end_date,
                  academic_year = :academic_year, semester = :semester, contract_date = :contract_date
                  WHERE id = :id AND faculty_id = :faculty_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
        $stmt->bindParam(':lecturer_id', $lecturer_id);
        $stmt->bindParam(':profession_id', $profession_id);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':class_code', $class_code);
        $stmt->bindParam(':total_hours', $total_hours);
        $stmt->bindParam(':hourly_rate', $hourly_rate);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':academic_year', $academic_year);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':contract_date', $contract_date);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật hợp đồng thành công!";
            redirect('contracts.php');
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-pencil"></i> Sửa hợp đồng</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="contracts.php" class="btn btn-secondary">
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

<div class="card">
    <div class="card-body">
        <form method="POST" action="" id="contractForm">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-person"></i> Thông tin giảng viên</h5>
                    
                    <div class="mb-3">
                        <label for="lecturer_id" class="form-label">Giảng viên <span class="text-danger">*</span></label>
                        <select class="form-select" id="lecturer_id" name="lecturer_id" required>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($lecturers as $lecturer): ?>
                            <option value="<?php echo $lecturer['id']; ?>" 
                                    data-education="<?php echo $lecturer['education_level']; ?>"
                                    <?php echo $lecturer['id'] == $contract['lecturer_id'] ? 'selected' : ''; ?>>
                                <?php echo $lecturer['full_name']; ?> - <?php echo $lecturer['education_level']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="profession_id" class="form-label">Nghề <span class="text-danger">*</span></label>
                        <select class="form-select" id="profession_id" name="profession_id" required onchange="loadSubjects(this.value)">
                            <option value="">-- Chọn nghề --</option>
                            <?php foreach ($professions as $profession): ?>
                            <option value="<?php echo $profession['id']; ?>"
                                    <?php echo $profession['id'] == $contract['profession_id'] ? 'selected' : ''; ?>>
                                <?php echo $profession['profession_code']; ?> - <?php echo $profession['profession_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Môn học/Mô đun <span class="text-danger">*</span></label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">-- Chọn môn học --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="class_code" class="form-label">Mã lớp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="class_code" name="class_code" 
                               value="<?php echo $contract['class_code']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-cash-stack"></i> Thông tin thù lao</h5>
                    
                    <div class="mb-3">
                        <label for="total_hours" class="form-label">Tổng số giờ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="total_hours" name="total_hours" 
                               value="<?php echo $contract['total_hours']; ?>" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="hourly_rate" class="form-label">Thù lao/giờ <span class="text-danger">*</span></label>
                        <select class="form-select" id="hourly_rate" name="hourly_rate" required>
                            <option value="">-- Chọn mức thù lao --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tổng tiền</label>
                        <div class="alert alert-info mb-0">
                            <h4 class="mb-0" id="total_amount_display"><?php echo formatMoney($contract['total_amount']); ?> đồng</h4>
                            <small class="text-muted" id="total_amount_words"></small>
                        </div>
                        <input type="hidden" id="total_amount" name="total_amount">
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-calendar"></i> Thời gian thực hiện</h5>
                    
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?php echo $contract['start_date']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?php echo $contract['end_date']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-info-circle"></i> Thông tin khác</h5>
                    
                    <div class="mb-3">
                        <label for="academic_year" class="form-label">Năm học <span class="text-danger">*</span></label>
                        <select class="form-select" id="academic_year" name="academic_year" required>
                            <option value="2025-2026" <?php echo $contract['academic_year'] == '2025-2026' ? 'selected' : ''; ?>>2025-2026</option>
                            <option value="2026-2027" <?php echo $contract['academic_year'] == '2026-2027' ? 'selected' : ''; ?>>2026-2027</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="semester" class="form-label">Học kỳ <span class="text-danger">*</span></label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="Học kỳ I" <?php echo $contract['semester'] == 'Học kỳ I' ? 'selected' : ''; ?>>Học kỳ I</option>
                            <option value="Học kỳ II" <?php echo $contract['semester'] == 'Học kỳ II' ? 'selected' : ''; ?>>Học kỳ II</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contract_date" class="form-label">Ngày ký hợp đồng <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="contract_date" name="contract_date" 
                               value="<?php echo $contract['contract_date']; ?>" required>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <button type="button" class="btn btn-secondary" onclick="history.back()">
                    <i class="bi bi-x-circle"></i> Hủy
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load subjects for selected profession
    loadSubjects(<?php echo $contract['profession_id']; ?>, <?php echo $contract['subject_id']; ?>);
    
    // Load hourly rates for selected lecturer
    const educationLevel = $('#lecturer_id').find(':selected').data('education');
    if (educationLevel) {
        loadHourlyRatesByEducation(educationLevel, <?php echo $contract['hourly_rate']; ?>);
    }
    
    $('#lecturer_id').on('change', function() {
        const educationLevel = $(this).find(':selected').data('education');
        if (educationLevel) {
            loadHourlyRatesByEducation(educationLevel);
        }
    });
    
    $('#total_hours, #hourly_rate').on('input change', function() {
        calculateTotal();
    });
    
    // Calculate initial total
    calculateTotal();
});

function loadSubjects(professionId, selectedSubjectId = null) {
    if (!professionId) {
        $('#subject_id').empty().append('<option value="">-- Chọn môn học --</option>');
        return;
    }
    
    $.ajax({
        url: 'ajax/get_subjects.php',
        type: 'POST',
        data: { profession_id: professionId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const subjectSelect = $('#subject_id');
                subjectSelect.empty().append('<option value="">-- Chọn môn học --</option>');
                response.subjects.forEach(function(subject) {
                    const selected = selectedSubjectId && subject.id == selectedSubjectId ? 'selected' : '';
                    subjectSelect.append(
                        `<option value="${subject.id}" ${selected}>${subject.subject_code} - ${subject.subject_name}</option>`
                    );
                });
            }
        }
    });
}

function loadHourlyRatesByEducation(educationLevel, selectedRate = null) {
    $.ajax({
        url: 'ajax/get_hourly_rates.php',
        type: 'POST',
        data: {
            education_level: educationLevel,
            academic_year: $('#academic_year').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const rateSelect = $('#hourly_rate');
                rateSelect.empty().append('<option value="">-- Chọn mức thù lao --</option>');
                response.rates.forEach(function(rate) {
                    const label = rate.rate_type === 'standard' ? 'Mức chuẩn' : 'Mức cao';
                    const selected = selectedRate && rate.amount == selectedRate ? 'selected' : '';
                    rateSelect.append(
                        `<option value="${rate.amount}" ${selected}>${label}: ${formatCurrency(rate.amount)} đồng</option>`
                    );
                });
                calculateTotal();
            }
        }
    });
}

function calculateTotal() {
    const hours = parseInt($('#total_hours').val()) || 0;
    const rate = parseFloat($('#hourly_rate').val()) || 0;
    const total = hours * rate;
    
    $('#total_amount').val(total);
    $('#total_amount_display').text(formatCurrency(total) + ' đồng');
    
    if (total > 0) {
        $.ajax({
            url: 'ajax/number_to_words.php',
            type: 'POST',
            data: { number: total },
            success: function(words) {
                $('#total_amount_words').text('(' + words + ')');
            }
        });
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('vi-VN').format(value);
}
</script>

<?php include 'includes/footer.php'; ?>