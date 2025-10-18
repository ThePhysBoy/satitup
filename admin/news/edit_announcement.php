<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireNewsAccess();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Load announcement
$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$ann = $res ? $res->fetch_assoc() : null;

$page_title = 'แก้ไขประกาศ';
$page_header_icon = '<i class="fas fa-edit me-2"></i>';

if (!$ann) {
    $_SESSION['error_message'] = 'ไม่พบประกาศที่ต้องการแก้ไข';
    header('Location: dashboard.php');
    exit;
}

ob_start();
?>

<div class="card-modern">
    <div class="card-header-modern">
        <h6 class="m-0">แก้ไขประกาศ</h6>
    </div>
    <div class="card-body-modern">
        <form method="post" action="update_announcement.php" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="id" value="<?php echo $ann['id']; ?>">
            <div class="col-md-8">
                <label class="form-label">หัวข้อประกาศ</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($ann['title']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">หมวดหมู่</label>
                <select name="category" class="form-select">
                    <option value="announcement" <?php echo $ann['category']==='announcement'?'selected':''; ?>>คำสั่งและประกาศ</option>
                    <option value="procurement" <?php echo $ann['category']==='procurement'?'selected':''; ?>>การจัดซื้อจัดจ้าง</option>
                    <option value="recruitment" <?php echo $ann['category']==='recruitment'?'selected':''; ?>>การรับสมัครงาน</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="open" <?php echo $ann['status']==='open'?'selected':''; ?>>เปิดรับ</option>
                    <option value="result" <?php echo $ann['status']==='result'?'selected':''; ?>>ประกาศผล</option>
                    <option value="closed" <?php echo $ann['status']==='closed'?'selected':''; ?>>ปิดรับ</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">งบประมาณ (บาท)</label>
                <input type="number" step="0.01" name="budget" class="form-control" value="<?php echo htmlspecialchars($ann['budget']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">วันที่ประกาศ</label>
                <input type="date" name="announce_date" class="form-control" value="<?php echo $ann['announce_date'] ? htmlspecialchars($ann['announce_date']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">ฝ่าย/หน่วยงาน</label>
                <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($ann['department'] ?? ''); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">รายละเอียดประกาศ</label>
                <textarea name="content" rows="5" class="form-control"><?php echo htmlspecialchars($ann['content'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-8">
                <label class="form-label">แนบไฟล์ PDF (อัปโหลดใหม่เพื่อแทนที่)</label>
                <input type="file" name="pdf_file" accept="application/pdf" class="form-control">
                <?php if (!empty($ann['file_path'])): ?>
                    <small class="text-muted">ไฟล์เดิม: <?php echo htmlspecialchars($ann['file_name']); ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-modern w-100">
                    <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>


