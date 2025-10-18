<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header('Location: ../index.php');
    exit;
}

// Handle form submit
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

    // Upload image
    $image_path = '';
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
    $cv_path = '';
    $cv_updated_at = null;
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['application/pdf'];
        if (!in_array($_FILES['cv']['type'], $allowed)) {
            $errors[] = 'CV ต้องเป็นไฟล์ PDF เท่านั้น';
        } else if ($_FILES['cv']['size'] > 50 * 1024 * 1024) { // 50MB limit
            $errors[] = 'ไฟล์ CV มีขนาดใหญ่เกิน 50MB';
        } else {
            $cv_dir = '../../uploads/management/cv';
            if (!is_dir($cv_dir)) mkdir($cv_dir, 0777, true);
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
        $stmt = $conn->prepare("INSERT INTO management (title, first_name, last_name, management_position, image_path, email, phone, bio, google_scholar_link, cv_path, cv_updated_at, order_number, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssssssssis', $title, $first_name, $last_name, $management_position, $image_path, $email, $phone, $bio, $google_scholar_link, $cv_path, $cv_updated_at, $order_number, $status);
        if ($stmt->execute()) {
            header('Location: index.php?success=' . urlencode('เพิ่มข้อมูลผู้บริหารเรียบร้อย'));
            exit;
        } else {
            $errors[] = 'บันทึกข้อมูลไม่สำเร็จ: ' . $conn->error;
        }
    }
}

$page_title = 'เพิ่มผู้บริหาร';
$include_summernote = true;
$page_header_icon = '<i class="fas fa-user-tie me-3"></i>';
$back_button = true; $back_url = 'index.php'; $back_text = 'กลับไปหน้ารายการ';

ob_start();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">เพิ่มผู้บริหาร</h1>
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
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อ *</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">นามสกุล *</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ตำแหน่งบริหาร *</label>
                            <input type="text" name="management_position" class="form-control" required placeholder="เช่น ผู้อำนวยการ, รองผู้อำนวยการ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รูปภาพ</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">รองรับ JPG, PNG, WEBP</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ลำดับการแสดงผล</label>
                            <input type="number" name="order_number" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>เปิดใช้งาน</option>
                                <option value="inactive">ปิดใช้งาน</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap text-primary"></i> Google Scholar Link
                            </label>
                            <input type="url" name="google_scholar_link" class="form-control" 
                                   placeholder="https://scholar.google.com/citations?user=XXXXX">
                            <small class="text-muted">ใส่ URL โปรไฟล์ Google Scholar (ถ้ามี)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-pdf text-danger"></i> CV (Curriculum Vitae)
                            </label>
                            <input type="file" name="cv" class="form-control" accept=".pdf">
                            <small class="text-muted">รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน 50MB</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">ประวัติ/ข้อมูลเพิ่มเติม</label>
                            <textarea name="bio" class="form-control summernote"></textarea>
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

