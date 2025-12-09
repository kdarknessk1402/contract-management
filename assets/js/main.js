// assets/js/main.js
$(document).ready(function() {
    // Initialize DataTables
    if ($('.data-table').length) {
        $('.data-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
            },
            pageLength: 20,
            order: [[0, 'desc']]
        });
    }
    
    // Auto hide alerts
    $('.alert').not('.alert-permanent').delay(5000).fadeOut('slow');
    
    // Confirm delete
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            e.preventDefault();
        }
    });
    
    // Format currency input
    $('.currency-input').on('blur', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(formatCurrency(value));
    });
    
    // Upload area drag & drop
    const uploadArea = $('.upload-area');
    
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    uploadArea.on('dragleave', function() {
        $(this).removeClass('dragover');
    });
    
    uploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        const files = e.originalEvent.dataTransfer.files;
        const fileInput = $(this).find('input[type="file"]');
        fileInput[0].files = files;
        handleFileSelect(files[0]);
    });
    
    // File input change
    $('input[type="file"]').on('change', function() {
        if (this.files && this.files[0]) {
            handleFileSelect(this.files[0]);
        }
    });
    
    // Calculate contract total
    $('#total_hours, #hourly_rate').on('input', function() {
        calculateTotal();
    });
    
    $('#education_level').on('change', function() {
        loadHourlyRates();
    });
});

function formatCurrency(value) {
    return new Intl.NumberFormat('vi-VN').format(value);
}

function handleFileSelect(file) {
    const fileName = file.name;
    const fileSize = (file.size / 1024 / 1024).toFixed(2);
    
    $('.file-name').text(fileName);
    $('.file-size').text(fileSize + ' MB');
    $('.file-info').show();
}

function calculateTotal() {
    const hours = parseInt($('#total_hours').val()) || 0;
    const rate = parseFloat($('#hourly_rate').val().replace(/[^\d]/g, '')) || 0;
    const total = hours * rate;
    
    $('#total_amount').val(formatCurrency(total));
    $('#total_amount_display').text(formatCurrency(total) + ' đồng');
}

function loadHourlyRates() {
    const educationLevel = $('#education_level').val();
    const academicYear = $('#academic_year').val() || '2025-2026';
    
    if (!educationLevel) return;
    
    $.ajax({
        url: 'ajax/get_hourly_rates.php',
        type: 'POST',
        data: {
            education_level: educationLevel,
            academic_year: academicYear
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const rateSelect = $('#hourly_rate');
                rateSelect.empty();
                response.rates.forEach(function(rate) {
                    const label = rate.rate_type === 'standard' ? 'Mức chuẩn' : 'Mức cao';
                    rateSelect.append(
                        $('<option></option>')
                            .val(rate.amount)
                            .text(label + ': ' + formatCurrency(rate.amount) + ' đồng')
                    );
                });
                calculateTotal();
            }
        }
    });
}

// Load subjects by profession
function loadSubjects(professionId) {
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
                    subjectSelect.append(
                        $('<option></option>')
                            .val(subject.id)
                            .text(subject.subject_code + ' - ' + subject.subject_name)
                            .data('hours', subject.credit_hours)
                    );
                });
            }
        }
    });
}

// Load professions by faculty
function loadProfessions(facultyId) {
    if (!facultyId) {
        $('#profession_id').empty().append('<option value="">-- Chọn nghề --</option>');
        return;
    }
    
    $.ajax({
        url: 'ajax/get_professions.php',
        type: 'POST',
        data: { faculty_id: facultyId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const professionSelect = $('#profession_id');
                professionSelect.empty().append('<option value="">-- Chọn nghề --</option>');
                response.professions.forEach(function(profession) {
                    professionSelect.append(
                        $('<option></option>')
                            .val(profession.id)
                            .text(profession.profession_code + ' - ' + profession.profession_name)
                    );
                });
            }
        }
    });
}

// Export table to Excel
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table);
    XLSX.writeFile(wb, filename + '.xlsx');
}

// Print contract
function printContract(contractId) {
    window.open('print_contract.php?id=' + contractId, '_blank');
}
