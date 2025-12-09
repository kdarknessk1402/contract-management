<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireGiaoVu();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Tạo hợp đồng mới";

// Lấy danh sách giảng viên thuộc khoa của giáo vụ
$query = "SELECT * FROM lecturers WHERE faculty_id = :faculty_id AND is_active = 1 ORDER BY full_name";
$stmt = $conn->prepare($query);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();
$lecturers = $stmt->fetchAll();

// Lấy danh sách nghề thuộc khoa (KHÔNG lọc theo level ở đây)
$query = "SELECT DISTINCT profession_code, profession_name, id FROM professions 
          WHERE faculty_id = :faculty_id AND is_active = 1 
          GROUP BY profession_code, profession_name
          ORDER BY profession_name";
$stmt = $conn->prepare($query);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();
$professions = $stmt->fetchAll();

// Lấy danh sách cơ sở
$query = "SELECT * FROM locations WHERE is_active = 1 ORDER BY location_name";
$stmt = $conn->prepare($query);
$stmt->execute();
$locations = $stmt->fetchAll();

// Xử lý tạo hợp đồng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $lecturer_id = $_POST['lecturer_id'];
        $profession_id = $_POST['profession_id'];
        $level = $_POST['level'];
        $subject_id = $_POST['subject_id'];
        $location_id = $_POST['location_id'];
        $class_id = $_POST['class_id'];
        $total_hours = (int)$_POST['total_hours'];
        $hourly_rate = (float)str_replace(['.', ','], '', $_POST['hourly_rate']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $academic_year = $_POST['academic_year'];
        $semester = $_POST['semester'];
        $contract_date = $_POST['contract_date'];
        
        $total_amount = $total_hours * $hourly_rate;
        
        // Tạo số hợp đồng tự động
        $contract_number = 'HD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $query = "INSERT INTO contracts 
                  (contract_number, lecturer_id, profession_id, level, subject_id, location_id, class_id, 
                   total_hours, hourly_rate, total_amount, start_date, end_date, 
                   academic_year, semester, contract_date, faculty_id, status, created_by) 
                  VALUES 
                  (:contract_number, :lecturer_id, :profession_id, :level, :subject_id, :location_id, :class_id,
                   :total_hours, :hourly_rate, :total_amount, :start_date, :end_date, 
                   :academic_year, :semester, :contract_date, :faculty_id, 'draft', :created_by)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':contract_number', $contract_number);
        $stmt->bindParam(':lecturer_id', $lecturer_id);
        $stmt->bindParam(':profession_id', $profession_id);
        $stmt->bindParam(':level', $level);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':location_id', $location_id);
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':total_hours', $total_hours);
        $stmt->bindParam(':hourly_rate', $hourly_rate);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':academic_year', $academic_year);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':contract_date', $contract_date);
        $stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
        $stmt->bindParam(':created_by', $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Tạo hợp đồng thành công! Số HĐ: $contract_number";
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
        <h2><i class="bi bi-file-earmark-plus"></i> Tạo hợp đồng mới</h2>
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
                    <h5 class="mb-3 text-primary">
                        <i class="bi bi-person-badge"></i> Bước 1: Chọn giảng viên & nghề
                    </h5>
                    
                    <div class="mb-3">
                        <label for="lecturer_id" class="form-label">
                            Giảng viên <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="lecturer_id" name="lecturer_id" required>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($lecturers as $lecturer): ?>
                            <option value="<?php echo $lecturer['id']; ?>" 
                                    data-education="<?php echo $lecturer['education_level']; ?>">
                                <?php echo $lecturer['full_name']; ?> - <?php echo $lecturer['education_level']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="profession_code" class="form-label">
                            Nghề <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="profession_code" name="profession_code" required>
                            <option value="">-- Chọn nghề --</option>
                            <?php foreach ($professions as $profession): ?>
                            <option value="<?php echo $profession['profession_code']; ?>">
                                <?php echo $profession['profession_code']; ?> - <?php echo $profession['profession_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="level" class="form-label">
                            Trình độ <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="">-- Chọn trình độ --</option>
                            <option value="Trung cấp">Trung cấp</option>
                            <option value="Cao đẳng">Cao đẳng</option>
                            <option value="Cao đẳng liên thông">Cao đẳng liên thông</option>
                        </select>
                    </div>
                    
                    <input type="hidden" id="profession_id" name="profession_id">
                    
                    <hr>
                    
                    <h5 class="mb-3 text-success">
                        <i class="bi bi-book"></i> Bước 2: Chọn môn học
                    </h5>
                    
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">
                            Môn học/Mô đun <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="subject_id" name="subject_id" required disabled>
                            <option value="">-- Chọn nghề và trình độ trước --</option>
                        </select>
                        <small class="text-muted">Số giờ sẽ tự động load từ môn học</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="mb-3 text-info">
                        <i class="bi bi-geo-alt"></i> Bước 3: Chọn cơ sở & lớp
                    </h5>
                    
                    <div class="mb-3">
                        <label for="location_id" class="form-label">
                            Cơ sở <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            <option value="">-- Chọn cơ sở --</option>
                            <?php foreach ($locations as $location): ?>
                            <option value="<?php echo $location['id']; ?>">
                                <?php echo $location['location_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Giá giờ sẽ thay đổi theo cơ sở</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="class_id" class="form-label">
                            Lớp <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="class_id" name="class_id" required disabled>
                            <option value="">-- Chọn nghề, trình độ và cơ sở trước --</option>
                        </select>
                    </div>
                    
                    <hr>
                    
                    <h5 class="mb-3 text-warning">
                        <i class="bi bi-cash-stack"></i> Thông tin thù lao
                    </h5>
                    
                    <div class="mb-3">
                        <label for="total_hours" class="form-label">
                            Tổng số giờ <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control bg-light" id="total_hours" 
                               name="total_hours" min="1" required readonly>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Tự động từ môn học
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="hourly_rate" class="form-label">
                            Thù lao/giờ (VNĐ) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control bg-light" id="hourly_rate" 
                               name="hourly_rate" required readonly>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Tự động từ cơ sở + trình độ GV
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tổng tiền</label>
                        <div class="alert alert-success mb-0">
                            <h4 class="mb-1" id="total_amount_display">
                                <i class="bi bi-cash"></i> 0 đồng
                            </h4>
                            <small class="text-muted" id="total_amount_words"></small>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-3 text-secondary">
                        <i class="bi bi-calendar-event"></i> Thông tin hợp đồng
                    </h5>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="contract_date" class="form-label">
                            Ngày ký HĐ <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="contract_date" 
                               name="contract_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">
                            Ngày bắt đầu <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="start_date" 
                               name="start_date" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">
                            Ngày kết thúc <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="end_date" 
                               name="end_date" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="academic_year" class="form-label">
                            Năm học <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="academic_year" name="academic_year" required>
                            <option value="2024-2025">2024-2025</option>
                            <option value="2025-2026" selected>2025-2026</option>
                            <option value="2026-2027">2026-2027</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="semester" class="form-label">
                            Học kỳ <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="Học kỳ I">Học kỳ I</option>
                            <option value="Học kỳ II">Học kỳ II</option>
                            <option value="Học kỳ hè">Học kỳ hè</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="text-end">
                <button type="button" class="btn btn-secondary btn-lg" onclick="history.back()">
                    <i class="bi bi-x-circle"></i> Hủy
                </button>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Tạo hợp đồng
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Khi chọn nghề và trình độ → Load môn học và profession_id
    $('#profession_code, #level').on('change', function() {
        loadSubjects();
    });
    
    // Khi chọn nghề, trình độ và cơ sở → Load lớp
    $('#profession_code, #level, #location_id').on('change', function() {
        loadClasses();
    });
    
    // Khi chọn giảng viên và cơ sở → Load giá giờ
    $('#lecturer_id, #location_id').on('change', function() {
        loadHourlyRate();
    });
    
    // Khi chọn môn học → Load số giờ
    $('#subject_id').on('change', function() {
        loadSubjectHours();
    });
    
    // Tính tổng tiền
    $('#total_hours, #hourly_rate').on('input change', function() {
        calculateTotal();
    });
});

function loadSubjects() {
    const professionCode = $('#profession_code').val();
    const level = $('#level').val();
    
    if (!professionCode || !level) {
        $('#subject_id').prop('disabled', true)
                       .empty()
                       .append('<option value="">-- Chọn nghề và trình độ trước --</option>');
        return;
    }
    
    $.ajax({
        url: 'ajax/get_subjects_by_profession_level.php',
        type: 'POST',
        data: { 
            profession_code: professionCode,
            level: level,
            faculty_id: <?php echo $_SESSION['faculty_id']; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Lưu profession_id
                $('#profession_id').val(response.profession_id);
                
                // Load subjects
                const subjectSelect = $('#subject_id');
                subjectSelect.prop('disabled', false)
                           .empty()
                           .append('<option value="">-- Chọn môn học --</option>');
                
                response.subjects.forEach(function(subject) {
                    subjectSelect.append(
                        `<option value="${subject.id}" data-hours="${subject.credit_hours}">
                            ${subject.subject_code} - ${subject.subject_name} (${subject.credit_hours} giờ)
                        </option>`
                    );
                });
            } else {
                alert(response.message || 'Không tìm thấy môn học');
            }
        },
        error: function() {
            alert('Lỗi khi tải môn học');
        }
    });
}

function loadClasses() {
    const professionCode = $('#profession_code').val();
    const level = $('#level').val();
    const locationId = $('#location_id').val();
    
    if (!professionCode || !level || !locationId) {
        $('#class_id').prop('disabled', true)
                     .empty()
                     .append('<option value="">-- Chọn nghề, trình độ và cơ sở trước --</option>');
        return;
    }
    
    $.ajax({
        url: 'ajax/get_classes_by_criteria.php',
        type: 'POST',
        data: { 
            profession_code: professionCode,
            level: level,
            location_id: locationId,
            faculty_id: <?php echo $_SESSION['faculty_id']; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const classSelect = $('#class_id');
                classSelect.prop('disabled', false)
                          .empty()
                          .append('<option value="">-- Chọn lớp --</option>');
                
                response.classes.forEach(function(cls) {
                    classSelect.append(
                        `<option value="${cls.id}">${cls.class_code} - ${cls.class_name}</option>`
                    );
                });
            } else {
                alert(response.message || 'Không tìm thấy lớp học');
            }
        },
        error: function() {
            alert('Lỗi khi tải lớp học');
        }
    });
}

function loadHourlyRate() {
    const lecturerId = $('#lecturer_id').val();
    const locationId = $('#location_id').val();
    const educationLevel = $('#lecturer_id').find(':selected').data('education');
    
    if (!lecturerId || !locationId || !educationLevel) {
        return;
    }
    
    $.ajax({
        url: 'ajax/get_hourly_rate_by_location.php',
        type: 'POST',
        data: { 
            location_id: locationId,
            education_level: educationLevel,
            academic_year: $('#academic_year').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#hourly_rate').val(formatCurrency(response.rate));
                calculateTotal();
            } else {
                alert(response.message || 'Không tìm thấy mức giá');
            }
        },
        error: function() {
            alert('Lỗi khi tải giá giờ');
        }
    });
}

function loadSubjectHours() {
    const selectedOption = $('#subject_id').find(':selected');
    const hours = selectedOption.data('hours') || 0;
    $('#total_hours').val(hours);
    calculateTotal();
}

function calculateTotal() {
    const hours = parseInt($('#total_hours').val()) || 0;
    const rateStr = $('#hourly_rate').val().replace(/[.,]/g, '');
    const rate = parseFloat(rateStr) || 0;
    const total = hours * rate;
    
    $('#total_amount_display').html(`<i class="bi bi-cash"></i> ${formatCurrency(total)} đồng`);
    
    if (total > 0) {
        $.ajax({
            url: 'ajax/number_to_words.php',
            type: 'POST',
            data: { number: total },
            success: function(words) {
                $('#total_amount_words').text('(' + words + ')');
            }
        });
    } else {
        $('#total_amount_words').text('');
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('vi-VN').format(value);
}

// DEBUG: Kiểm tra khi chọn nghề và trình độ
$('#profession_code, #level').on('change', function() {
    console.log('Profession Code:', $('#profession_code').val());
    console.log('Level:', $('#level').val());
    console.log('Faculty ID:', <?php echo $_SESSION['faculty_id']; ?>);
});
</script>

<?php include 'includes/footer.php'; ?>