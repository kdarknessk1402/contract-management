<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();
$page_title = "Sao lưu dữ liệu";

// Xử lý backup
if (isset($_GET['action']) && $_GET['action'] === 'backup') {
    try {
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $filepath = BACKUP_PATH . $filename;
        
        // Lấy tất cả tables
        $tables = [];
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        $output = "-- Database Backup\n";
        $output .= "-- Created: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            // Get table structure
            $result = $conn->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch(PDO::FETCH_NUM);
            $output .= "\n-- Table: $table\n";
            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            $output .= $row[1] . ";\n\n";
            
            // Get table data
            $result = $conn->query("SELECT * FROM `$table`");
            $num_fields = $result->columnCount();
            
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $output .= "INSERT INTO `$table` VALUES(";
                for ($i = 0; $i < $num_fields; $i++) {
                    if ($row[$i] === null) {
                        $output .= 'NULL';
                    } else {
                        $output .= "'" . addslashes($row[$i]) . "'";
                    }
                    if ($i < ($num_fields - 1)) {
                        $output .= ',';
                    }
                }
                $output .= ");\n";
            }
            $output .= "\n";
        }
        
        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        file_put_contents($filepath, $output);
        
        $_SESSION['success'] = "Sao lưu thành công! File: {$filename}";
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    redirect('backup.php');
}

// Xử lý xóa backup
if (isset($_GET['delete'])) {
    $filename = $_GET['delete'];
    $filepath = BACKUP_PATH . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        $_SESSION['success'] = "Đã xóa file backup!";
    }
    redirect('backup.php');
}

// Lấy danh sách backup files
$backup_files = [];
if (is_dir(BACKUP_PATH)) {
    $files = scandir(BACKUP_PATH);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backup_files[] = [
                'name' => $file,
                'size' => filesize(BACKUP_PATH . $file),
                'date' => filemtime(BACKUP_PATH . $file)
            ];
        }
    }
    usort($backup_files, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-database"></i> Sao lưu dữ liệu</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="backup.php?action=backup" class="btn btn-primary" 
           onclick="return confirm('Bạn có chắc muốn tạo bản sao lưu?')">
            <i class="bi bi-download"></i> Tạo bản sao lưu mới
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
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Danh sách bản sao lưu</h5>
    </div>
    <div class="card-body">
        <?php if (empty($backup_files)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
            <p class="text-muted mt-3">Chưa có bản sao lưu nào</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên file</th>
                        <th>Kích thước</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($backup_files as $file): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><i class="bi bi-file-earmark-zip"></i> <?php echo $file['name']; ?></td>
                        <td><?php echo number_format($file['size'] / 1024, 2); ?> KB</td>
                        <td><?php echo date('d/m/Y H:i:s', $file['date']); ?></td>
                        <td>
                            <a href="backups/<?php echo $file['name']; ?>" 
                               class="btn btn-sm btn-info" download>
                                <i class="bi bi-download"></i> Tải về
                            </a>
                            <a href="backup.php?delete=<?php echo $file['name']; ?>" 
                               class="btn btn-sm btn-danger btn-delete">
                                <i class="bi bi-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>