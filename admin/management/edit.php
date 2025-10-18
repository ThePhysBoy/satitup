<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header('Location: ../index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM management WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$management = $stmt->get_result()->fetch_assoc();
if (!$management) { header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $management_position = trim($_POST['management_position'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $google_scholar_link = trim($_POST['google_scholar_link'] ?? '');
    $order_number = (int)($_POST['order_number'] ?? 0);
    $status = $_POST['status'] ?? 'active';

    if ($title === '' || $first_name === '' || $last_name === '' || $management_position === '') {
        $errors[] = 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน';
    }

    // Handle image upload optional
    $image_path = $management['image_path'];
    
    // Handle image delete
    if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
        if (!empty($management['image_path']) && file_exists('../../' . $management['image_path'])) {
            unlink('../../' . $management['image_path']);
        }
        $image_path = '';
    }
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($_FILES['image']['type'], $allowed)) {
            $errors[] = 'รองรับเฉพาะไฟล์ JPG, PNG, WEBP';
        } else {
            $dir = '../../uploads/management';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $name = uniqid('m_') . '_' . basename($_FILES['image']['name']);
            $name = preg_replace('/[^a-zA-Z0-9_\.-]/','',$name);
            if (move_uploaded_file($_FILES['image']['tmp_name'], "$dir/$name")) {
                $image_path = 'uploads/management/' . $name;
            } else {
                $errors[] = 'อัปโหลดรูปไม่สำเร็จ';
            }
        }
    }
    
    // Handle CV upload
    $cv_path = $management['cv_path'];
    $cv_updated_at = $management['cv_updated_at'];
    
    // Handle CV delete
    if (isset($_POST['delete_cv']) && $_POST['delete_cv'] == '1') {
        if (!empty($management['cv_path']) && file_exists('../../' . $management['cv_path'])) {
            unlink('../../' . $management['cv_path']);
        }
        $cv_path = '';
        $cv_updated_at = null;
    }
    // Handle CV upload
    else if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['application/pdf'];
        if (!in_array($_FILES['cv']['type'], $allowed)) {
            $errors[] = 'CV ต้องเป็นไฟล์ PDF เท่านั้น';
        } else if ($_FILES['cv']['size'] > 50 * 1024 * 1024) { // 50MB limit
            $errors[] = 'ไฟล์ CV มีขนาดใหญ่เกิน 50MB';
        } else {
            $cv_dir = '../../uploads/management/cv';
            if (!is_dir($cv_dir)) mkdir($cv_dir, 0777, true);
            
            // Delete old CV if exists
            if (!empty($management['cv_path']) && file_exists('../../' . $management['cv_path'])) {
                unlink('../../' . $management['cv_path']);
            }
            
            $cv_name = uniqid('cv_') . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', basename($_FILES['cv']['name']));
            if (move_uploaded_file($_FILES['cv']['tmp_name'], "$cv_dir/$cv_name")) {
                $cv_path = 'uploads/management/cv/' . $cv_name;
                $cv_updated_at = date('Y-m-d H:i:s');
            } else {
                $errors[] = 'อัปโหลด CV ไม่สำเร็จ';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE management SET title=?, first_name=?, last_name=?, management_position=?, image_path=?, email=?, phone=?, bio=?, google_scholar_link=?, cv_path=?, cv_updated_at=?, order_number=?, status=? WHERE id=?");
        $stmt->bind_param('sssssssssssisi', $title, $first_name, $last_name, $management_position, $image_path, $email, $phone, $bio, $google_scholar_link, $cv_path, $cv_updated_at, $order_number, $status, $id);
        if ($stmt->execute()) {
            header('Location: index.php?success=' . urlencode('บันทึกการแก้ไขเรียบร้อย'));
            exit;
        } else {
            $errors[] = 'บันทึกข้อมูลไม่สำเร็จ: ' . $conn->error;
        }
    }
}

$page_title = 'แก้ไขผู้บริหาร';
$include_summernote = true;
$page_header_icon = '<i class="fas fa-user-tie me-3"></i>';
$back_button = true; $back_url = 'index.php'; $back_text = 'กลับไปหน้ารายการ';

ob_start();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">แก้ไขผู้บริหาร</h1>
        <a href="index.php" class="btn btn-secondary btn-sm rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i> กลับไปหน้ารายการ</a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">คำนำหน้า *</label>
                            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($management['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อ *</label>
                            <input type="text" name="first_name" class="form-control" required value="<?php echo htmlspecialchars($management['first_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">นามสกุล *</label>
                            <input type="text" name="last_name" class="form-control" required value="<?php echo htmlspecialchars($management['last_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ตำแหน่งบริหาร *</label>
                            <input type="text" name="management_position" class="form-control" required value="<?php echo htmlspecialchars($management['management_position']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รูปภาพ</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <?php if (!empty($management['image_path'])): ?>
                                <div class="mt-2">
                                    <img src="../../<?php echo htmlspecialchars($management['image_path']); ?>" class="img-thumbnail" style="max-height:120px;">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="delete_image" name="delete_image" value="1">
                                        <label class="form-check-label text-danger" for="delete_image">
                                            <i class="fas fa-trash me-1"></i> ลบรูปภาพ
                                        </label>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-muted mt-1"><i class="fas fa-info-circle"></i> ยังไม่มีรูปภาพ</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($management['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($management['phone']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ลำดับการแสดงผล</label>
                            <input type="number" name="order_number" class="form-control" value="<?php echo (int)$management['order_number']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo $management['status']==='active'?'selected':''; ?>>เปิดใช้งาน</option>
                                <option value="inactive" <?php echo $management['status']==='inactive'?'selected':''; ?>>ปิดใช้งาน</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap text-primary"></i> Google Scholar Link
                            </label>
                            <input type="url" name="google_scholar_link" class="form-control" 
                                   placeholder="https://scholar.google.com/citations?user=XXXXX"
                                   value="<?php echo htmlspecialchars($management['google_scholar_link'] ?? ''); ?>">
                            <small class="text-muted">ใส่ URL โปรไฟล์ Google Scholar (ถ้ามี)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-pdf text-danger"></i> CV (Curriculum Vitae)
                            </label>
                            <input type="file" name="cv" class="form-control" accept=".pdf">
                            <?php if (!empty($management['cv_path'])): ?>
                                <div class="mt-2 p-2 bg-light rounded">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    <a href="../../<?php echo htmlspecialchars($management['cv_path']); ?>" target="_blank" class="text-decoration-none">
                                        ดู CV ปัจจุบัน
                                    </a>
                                    <?php if (!empty($management['cv_updated_at'])): ?>
                                        <small class="text-muted d-block">
                                            อัพเดทเมื่อ: <?php echo date('d/m/Y H:i', strtotime($management['cv_updated_at'])); ?>
                                        </small>
                                    <?php endif; ?>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="delete_cv" name="delete_cv" value="1">
                                        <label class="form-check-label text-danger" for="delete_cv">
                                            <i class="fas fa-trash me-1"></i> ลบไฟล์ CV
                                        </label>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-muted mt-1"><i class="fas fa-info-circle"></i> ยังไม่มีไฟล์ CV</div>
                            <?php endif; ?>
                            <small class="text-muted">รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน 50MB</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">ประวัติ/ข้อมูลเพิ่มเติม</label>
                            <textarea name="bio" class="form-control summernote"><?php echo htmlspecialchars($management['bio']); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary px-5" type="submit"><i class="fas fa-save me-2"></i> บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include '../news/template.php';
?>

