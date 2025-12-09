<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;

$auth = new Auth();
$auth->requireLogin();

if (!isset($_GET['id'])) {
    redirect('contracts.php');
}

$database = new Database();
$conn = $database->getConnection();
$contract_id = $_GET['id'];

// Lấy thông tin hợp đồng
$query = "SELECT c.*, 
          l.full_name, l.gender, l.birth_year, l.id_number, l.id_issued_date, l.id_issued_place,
          l.education_level, l.major, l.pedagogy, l.address, l.phone, l.email,
          l.bank_account, l.bank_name, l.bank_branch, l.tax_code,
          s.subject_name, s.subject_code,
          p.profession_name, p.profession_code,
          f.faculty_name
          FROM contracts c
          JOIN lecturers l ON c.lecturer_id = l.id
          JOIN subjects s ON c.subject_id = s.id
          JOIN professions p ON c.profession_id = p.id
          JOIN faculties f ON c.faculty_id = f.id
          WHERE c.id = :id AND c.faculty_id = :faculty_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $contract_id);
$stmt->bindParam(':faculty_id', $_SESSION['faculty_id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['error'] = 'Không tìm thấy hợp đồng!';
    redirect('contracts.php');
}

$contract = $stmt->fetch();

// Tạo document Word
$phpWord = new PhpWord();

// Set font mặc định
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(13);

// Tạo section với thiết lập trang
$section = $phpWord->addSection([
    'marginTop' => 567,    // 1cm
    'marginBottom' => 567,
    'marginLeft' => 1134,  // 2cm
    'marginRight' => 567,
    'headerHeight' => 720,
    'footerHeight' => 720
]);

// Header
$header = $section->addHeader();
$headerTable = $header->addTable();
$headerTable->addRow();
$headerTable->addCell(4500)->addText('ỦY BAN NHÂN DÂN', ['bold' => false, 'size' => 13]);
$rightCell = $headerTable->addCell(5000);
$rightCell->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
$rightCell->addText('Độc lập - Tự do - Hạnh phúc', ['bold' => true, 'size' => 13, 'underline' => 'single'], ['alignment' => 'center']);

$headerTable->addRow();
$headerTable->addCell(4500)->addText('THÀNH PHỐ HỒ CHÍ MINH', ['bold' => false, 'size' => 13]);
$headerTable->addCell(5000);

$headerTable->addRow();
$leftCell2 = $headerTable->addCell(4500);
$leftCell2->addText('TRƯỜNG CAO ĐẲNG NGHỀ', ['bold' => true, 'size' => 13]);
$leftCell2->addText('THÀNH PHỐ HỒ CHÍ MINH', ['bold' => true, 'size' => 13]);
$headerTable->addCell(5000);

$headerTable->addRow();
$headerTable->addCell(4500)->addText('Số: ' . $contract['contract_number'], ['bold' => false, 'size' => 13]);
$headerTable->addCell(5000);

$header->addTextBreak(1);

// Tiêu đề
$section->addText('HỢP ĐỒNG THỈNH GIẢNG', [
    'bold' => true,
    'size' => 16
], ['alignment' => 'center']);

$section->addTextBreak(1);

// Căn cứ pháp lý
$section->addText('Căn cứ Bộ Luật Dân sự ngày 01 tháng 01 năm 2017;', ['italic' => true, 'size' => 13]);
$section->addTextBreak(0.5);

$section->addText('Căn cứ Quyết định số 490/QĐ-CĐN ngày 23 tháng 08 năm 2024 của Trường Cao đẳng nghề Thành phố Hồ Chí Minh về việc ban hành Quy định mức thanh toán thù lao Nhà giáo thỉnh giảng (chính quy); Thù lao vượt giờ (phụ trội) tại Trường Cao đẳng nghề Thành phố Hồ Chí Minh;', ['italic' => true, 'size' => 13]);
$section->addTextBreak(0.5);

$section->addText('Căn cứ vào nhu cầu và khả năng của hai bên;', ['italic' => true, 'size' => 13]);
$section->addTextBreak(0.5);

// Ngày ký
$contract_date_arr = explode('-', $contract['contract_date']);
$contract_day = $contract_date_arr[2];
$contract_month = $contract_date_arr[1];
$contract_year = $contract_date_arr[0];

$section->addText("Hôm nay, ngày {$contract_day} tháng {$contract_month} năm {$contract_year}, tại Trường Cao đẳng nghề TP. Hồ Chí Minh, chúng tôi gồm:", ['italic' => true, 'size' => 13]);
$section->addTextBreak(1);

// Bên A
$section->addText('Bên A: Trường Cao đẳng nghề Thành phố Hồ Chí Minh', ['bold' => true, 'size' => 13]);
$section->addText('Địa chỉ: Số 235 Hoàng Sa, phường Tân Định, Thành phố Hồ Chí Minh.', ['size' => 13]);
$section->addText('Điện thoại: (028). 38. 438. 720 Fax: (028). 38. 435. 537.', ['size' => 13]);
$section->addText('Đại diện: Ông TS. Trần Kim Tuyền Chức vụ: Hiệu trưởng', ['size' => 13]);
$section->addText('Số tài khoản: 3716.2.1046476.00000 tại Kho bạc Nhà nước Khu vực II.', ['size' => 13]);
$section->addTextBreak(1);

// Bên B
$section->addText('Bên B: Thầy/Cô: ' . $contract['full_name'] . ' Năm sinh: ' . ($contract['birth_year'] ?? ''), ['bold' => true, 'size' => 13]);
$section->addText('Số CMND/Căn cước: ' . $contract['id_number'] . ' Ngày cấp: ' . ($contract['id_issued_date'] ? formatDate($contract['id_issued_date']) : ''), ['size' => 13]);
$section->addText('Nơi cấp: ' . ($contract['id_issued_place'] ?? ''), ['size' => 13]);
$section->addText('Trình độ: ' . $contract['education_level'] . '; Chuyên ngành đào tạo: ' . ($contract['major'] ?? ''), ['size' => 13]);
$section->addText('Sư phạm: ' . ($contract['pedagogy'] ?? ''), ['size' => 13]);
$section->addText('Địa chỉ nhà: ' . ($contract['address'] ?? ''), ['size' => 13]);
$section->addText('Điện thoại DĐ/bàn: ' . ($contract['phone'] ?? '') . ' Email: ' . ($contract['email'] ?? ''), ['size' => 13]);
$section->addText('Số tài khoản: ' . ($contract['bank_account'] ?? '') . ', tại Ngân hàng ' . ($contract['bank_name'] ?? ''), ['size' => 13]);
$section->addText('Chi nhánh: ' . ($contract['bank_branch'] ?? ''), ['size' => 13]);
$section->addText('Mã số thuế cá nhân: ' . ($contract['tax_code'] ?? ''), ['size' => 13]);
$section->addTextBreak(1);

$section->addText('Sau khi trao đổi, thỏa thuận hai bên đồng ý ký hợp đồng này với các điều khoản như sau:', ['size' => 13]);
$section->addTextBreak(1);

// Điều 1
$section->addText('Điều 1: Nội dung hợp đồng.', ['bold' => true, 'size' => 13]);
$section->addTextBreak(0.5);

$section->addText('1. Bên A đồng ý mời và Bên B đồng ý nhận thực hiện việc giảng dạy Môn học/Mô đun: ' . $contract['subject_name'] . ', Tổng số giờ: ' . $contract['total_hours'] . ' giờ theo chương trình và kế hoạch đào tạo tại trình độ Cao đẳng, Trung cấp đã ban hành của Bên A', ['size' => 13]);
$section->addTextBreak(0.5);

$section->addText('Nghề: ' . $contract['profession_name'] . ', Mã lớp: ' . $contract['class_code'], ['size' => 13]);
$section->addTextBreak(0.5);

$start_date_arr = explode('-', $contract['start_date']);
$end_date_arr = explode('-', $contract['end_date']);

$section->addText('2. Thời gian giảng dạy trong năm học ' . $contract['academic_year'] . ' (' . $contract['semester'] . '): Từ ngày ' . $start_date_arr[2] . '/' . $start_date_arr[1] . '/' . $start_date_arr[0] . ' đến ngày ' . $end_date_arr[2] . '/' . $end_date_arr[1] . '/' . $end_date_arr[0] . '.', ['size' => 13]);
$section->addTextBreak(0.5);

$section->addText('3. Địa điểm: tại trường Cao đẳng nghề TP. Hồ Chí Minh, cụ thể theo thời khoá biểu.', ['size' => 13]);
$section->addTextBreak(1);

// Điều 2
$section->addText('Điều 2: Thù lao và phương thức thanh toán.', ['bold' => true, 'size' => 13]);
$section->addTextBreak(0.5);

$section->addText('1. Thù lao:', ['size' => 13]);
$section->addTextBreak(0.5);

$total_in_words = numberToWords($contract['total_amount']);
$section->addText('Tổng giá trị hợp đồng: ' . $contract['total_hours'] . ' x ' . formatMoney($contract['hourly_rate']) . ' = ' . formatMoney($contract['total_amount']) . ' đồng', ['bold' => true, 'size' => 13]);
$section->addText('(Bằng chữ: ' . ucfirst($total_in_words) . ' đồng)', ['italic' => true, 'size' => 13]);
$section->addTextBreak(0.5);

$section->addText('Trong đó, thù lao một giờ dạy: ' . formatMoney($contract['hourly_rate']) . ' đồng', ['italic' => true, 'size' => 13]);
$section->addTextBreak(0.5);

$section->addText('2. Phương thức thanh toán: bằng chuyển khoản 100% khi bên B thực hiện đầy đủ các điều khoản hợp đồng', ['size' => 13]);
$section->addTextBreak(1);

// Footer với chữ ký
$section->addTextBreak(2);

$signTable = $section->addTable();
$signTable->addRow();
$signTable->addCell(4500)->addText('ĐẠI DIỆN BÊN A', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
$signTable->addCell(1000);
$signTable->addCell(4000)->addText('BÊN B', ['bold' => true, 'size' => 13], ['alignment' => 'center']);

$signTable->addRow();
$signTable->addCell(4500)->addText('HIỆU TRƯỞNG', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
$signTable->addCell(1000);
$signTable->addCell(4000);

$signTable->addRow(1000); // Khoảng trống cho chữ ký
$signTable->addCell(4500);
$signTable->addCell(1000);
$signTable->addCell(4000);

$signTable->addRow();
$signTable->addCell(4500)->addText('TS. Trần Kim Tuyền', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
$signTable->addCell(1000);
$signTable->addCell(4000)->addText($contract['full_name'], ['bold' => true, 'size' => 13], ['alignment' => 'center']);

// Lưu file
$filename = 'HD_' . $contract['contract_number'] . '_' . time() . '.docx';
$filepath = EXPORT_PATH . $filename;

$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($filepath);

// Download file
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
readfile($filepath);

// Xóa file sau khi download
unlink($filepath);
exit;
?>
