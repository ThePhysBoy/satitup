<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header('Location: ../index.php');
    exit;
}

// Fetch committee list
$result = $conn->query("SELECT * FROM steering_committee ORDER BY order_number, first_name");
$committee = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// กำหนดชื่อหมวดหมู่
$category_names = [
    'president' => 'ประธาน',
    'vp_dean' => 'รองอธิการบดีและคณบดี',
    'expert' => 'กรรมการผู้ทรงคุณวุฒิ',
    'school_rep' => 'ผู้แทนโรงเรียนและฝ่ายเลขานุการ'
];

$page_title = 'จัดการคณะกรรมการอำนวยการ';
$include_summernote = false;
$page_header_icon = '<i class="fas fa-university me-3"></i>';
$back_button = false;

ob_start();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">คณะกรรมการอำนวยการ</h1>
        <a href="create.php" class="btn btn-primary btn-sm rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มกรรมการ
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
                            <th>ตำแหน่ง</th>
                            <th>บทบาท</th>
                            <th>หมวดหมู่</th>
                            <th>สถานะ</th>
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($committee as $c): ?>
                        <tr>
                            <td class="text-center"><?php echo (int)$c['order_number']; ?></td>
                            <td class="text-center">
                                <?php if (!empty($c['image_path'])): ?>
                                    <img src="../../<?php echo htmlspecialchars($c['image_path']); ?>" class="img-thumbnail" style="max-height:60px;">
                                <?php else: ?>
                                    <img src="../../assets/img/user-placeholder.png" class="img-thumbnail" style="max-height:60px;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($c['title'] . ' ' . $c['first_name'] . ' ' . $c['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['position']); ?></td>
                            <td><?php echo htmlspecialchars($c['role']); ?></td>
                            <td><?php echo $category_names[$c['category']] ?? $c['category']; ?></td>
                            <td>
                                <?php if ($c['status'] === 'active'): ?>
                                    <span class="badge bg-success">เปิดใช้งาน</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">ปิดใช้งาน</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit.php?id=<?php echo $c['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="view.php?id=<?php echo $c['id']; ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                    <button onclick="deleteCommittee(<?php echo $c['id']; ?>)" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($committee)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">ยังไม่มีข้อมูลคณะกรรมการ</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteCommittee(id) {
    if (confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?')) {
        window.location.href = 'delete.php?id=' + id;
    }
}
</script>

<?php
$content = ob_get_clean();
include '../news/template.php';
?>
