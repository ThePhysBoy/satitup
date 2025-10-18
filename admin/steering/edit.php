<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header('Location: ../index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { 
    header('Location: index.php'); 
    exit; 
}

$stmt = $conn->prepare("SELECT * FROM steering_committee WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$committee = $stmt->get_result()->fetch_assoc();
if (!$committee) { 
    header('Location: index.php'); 
    exit; 
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $category = $_POST['category'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $order_number = (int)($_POST['order_number'] ?? 0);
    $status = $_POST['status'] ?? 'active';

    if ($first_name === '' || $last_name === '' || $role === '' || $category === '') {
        $errors[] = 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน';
    }

    // Handle image delete request
    $image_path = $committee['image_path'];
    if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
        // Delete the old image file
        if (!empty($committee['image_path']) && file_exists('../../' . $committee['image_path'])) {
            unlink('../../' . $committee['image_path']);
        }
        $image_path = '';
    }
    // Handle image upload optional
    else if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($_FILES['image']['type'], $allowed)) {
            $errors[] = 'รองรับเฉพาะไฟล์ JPG, PNG, WEBP';
        } else {
            $dir = '../../uploads/steering';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $name = uniqid('sc_') . '_' . basename($_FILES['image']['name']);
            $name = preg_replace('/[^a-zA-Z0-9_\.-]/','',$name);
            if (move_uploaded_file($_FILES['image']['tmp_name'], "$dir/$name")) {
                // Delete old image if exists
                if (!empty($committee['image_path']) && file_exists('../../' . $committee['image_path'])) {
                    unlink('../../' . $committee['image_path']);
                }
                $image_path = 'uploads/steering/' . $name;
            } else {
                $errors[] = 'อัปโหลดรูปไม่สำเร็จ';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE steering_committee SET title=?, first_name=?, last_name=?, position=?, role=?, category=?, image_path=?, email=?, phone=?, bio=?, order_number=?, status=? WHERE id=?");
        $stmt->bind_param('ssssssssssisi', $title, $first_name, $last_name, $position, $role, $category, $image_path, $email, $phone, $bio, $order_number, $status, $id);
        if ($stmt->execute()) {
            header('Location: index.php?success=' . urlencode('บันทึกการแก้ไขเรียบร้อย'));
            exit;
        } else {
            $errors[] = 'บันทึกข้อมูลไม่สำเร็จ: ' . $conn->error;
        }
    }
}

$page_title = 'แก้ไขกรรมการอำนวยการ';
$include_summernote = true;
$page_header_icon = '<i class="fas fa-university me-3"></i>';
$back_button = true; 
$back_url = 'index.php'; 
$back_text = 'กลับไปหน้ารายการ';

ob_start();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">แก้ไขกรรมการอำนวยการ</h1>
        <a href="index.php" class="btn btn-secondary btn-sm rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> กลับไปหน้ารายการ
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">คำนำหน้า</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($committee['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อ *</label>
                            <input type="text" name="first_name" class="form-control" required value="<?php echo htmlspecialchars($committee['first_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">นามสกุล *</label>
                            <input type="text" name="last_name" class="form-control" required value="<?php echo htmlspecialchars($committee['last_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ตำแหน่งหลัก</label>
                            <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($committee['position']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">บทบาทในคณะกรรมการ *</label>
                            <input type="text" name="role" class="form-control" required value="<?php echo htmlspecialchars($committee['role']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รูปภาพ</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <?php if (!empty($committee['image_path'])): ?>
                                <div class="mt-2">
                                    <img src="../../<?php echo htmlspecialchars($committee['image_path']); ?>" class="img-thumbnail" style="max-height:120px;">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="delete_image" name="delete_image" value="1">
                                        <label class="form-check-label text-danger" for="delete_image">
                                            <i class="fas fa-trash me-1"></i> ลบรูปภาพนี้
                                        </label>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-muted mt-1">
                                    <i class="fas fa-info-circle"></i> ยังไม่มีรูปภาพ
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">หมวดหมู่ *</label>
                            <select name="category" class="form-select" required>
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                <option value="president" <?php echo $committee['category']==='president'?'selected':''; ?>>ประธาน</option>
                                <option value="vp_dean" <?php echo $committee['category']==='vp_dean'?'selected':''; ?>>รองอธิการบดีและคณบดี</option>
                                <option value="expert" <?php echo $committee['category']==='expert'?'selected':''; ?>>กรรมการผู้ทรงคุณวุฒิ</option>
                                <option value="school_rep" <?php echo $committee['category']==='school_rep'?'selected':''; ?>>ผู้แทนโรงเรียนและฝ่ายเลขานุการ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($committee['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($committee['phone']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ลำดับการแสดงผล</label>
                            <input type="number" name="order_number" class="form-control" value="<?php echo (int)$committee['order_number']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo $committee['status']==='active'?'selected':''; ?>>เปิดใช้งาน</option>
                                <option value="inactive" <?php echo $committee['status']==='inactive'?'selected':''; ?>>ปิดใช้งาน</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">ประวัติ/ข้อมูลเพิ่มเติม</label>
                            <textarea name="bio" class="form-control summernote"><?php echo htmlspecialchars($committee['bio']); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary px-5" type="submit">
                        <i class="fas fa-save me-2"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include '../news/template.php';
?>
