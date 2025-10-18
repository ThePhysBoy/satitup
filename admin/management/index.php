<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header('Location: ../index.php');
    exit;
}

// Fetch management list
$result = $conn->query("SELECT * FROM management ORDER BY order_number, first_name");
$management = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$page_title = 'จัดการผู้บริหาร';
$include_summernote = false;
$page_header_icon = '<i class="fas fa-user-tie me-3"></i>';
$back_button = false;

ob_start();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">ผู้บริหาร</h1>
        <a href="create.php" class="btn btn-primary btn-sm rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มผู้บริหาร
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="60">ลำดับ</th>
                            <th width="80">รูปภาพ</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>ตำแหน่งบริหาร</th>
                            <th>สถานะ</th>
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($management as $m): ?>
                        <tr>
                            <td class="text-center"><?php echo (int)$m['order_number']; ?></td>
                            <td class="text-center">
                                <?php if (!empty($m['image_path'])): ?>
                                    <img src="../../<?php echo htmlspecialchars($m['image_path']); ?>" class="img-thumbnail" style="max-height:60px;">
                                <?php else: ?>
                                    <img src="../../assets/img/user-placeholder.png" class="img-thumbnail" style="max-height:60px;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($m['title'] . ' ' . $m['first_name'] . ' ' . $m['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($m['management_position']); ?></td>
                            <td>
                                <?php if ($m['status'] === 'active'): ?>
                                    <span class="badge bg-success">เปิดใช้งาน</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">ปิดใช้งาน</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit.php?id=<?php echo $m['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="view.php?id=<?php echo $m['id']; ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($management)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">ยังไม่มีข้อมูลผู้บริหาร</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include '../news/template.php';
?>

