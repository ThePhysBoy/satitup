<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireRankingsAccess();

$errors = [];
$title = '';
$training_topic = '';
$reference_number = '';
$host_department = '';
$training_type = '';
$target_audience = '';
$location = '';
$training_dates = '';
$registration_deadline = date('Y-m-d', strtotime('+15 days'));
$price = '';
$agenda = '';
$description = '';
$contact_person = '';
$contact_phone = '';
$contact_email = '';
$status = 'draft';
$notes = '';
$published_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $training_topic = trim($_POST['training_topic'] ?? '');
    $reference_number = trim($_POST['reference_number'] ?? '');
    $host_department = trim($_POST['host_department'] ?? '');
    $training_type = trim($_POST['training_type'] ?? '');
    $target_audience = trim($_POST['target_audience'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $training_dates = trim($_POST['training_dates'] ?? '');
    $registration_deadline = $_POST['registration_deadline'] ?? null;
    $price = trim($_POST['price'] ?? '');
    $agenda = trim($_POST['agenda'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $notes = trim($_POST['notes'] ?? '');
    $published_date = $_POST['published_date'] ?? date('Y-m-d');

    if ($title === '') {
        $errors[] = 'กรุณากรอกหัวข้อประกาศ';
    }

    $pdfPath = '';
    if (isset($_FILES['document_pdf']) && $_FILES['document_pdf']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['document_pdf']['tmp_name'];
        $fileName = basename($_FILES['document_pdf']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExt !== 'pdf') {
            $errors[] = 'อนุญาตเฉพาะไฟล์ PDF';
        } else {
            $newName = 'training_' . uniqid() . '.pdf';
            $uploadDir = '../../documents/trainings/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            if (move_uploaded_file($fileTmp, $uploadDir . $newName)) {
                $pdfPath = 'documents/trainings/' . $newName;
            } else {
                $errors[] = 'ไม่สามารถบันทึกไฟล์ PDF ได้';
            }
        }
    } else {
        $errors[] = 'กรุณาอัปโหลดไฟล์ PDF รายละเอียดการอบรม';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO training_announcements (
            title, training_topic, reference_number, host_department, training_type, target_audience,
            location, training_dates, registration_deadline, price, agenda, description, document_pdf,
            contact_person, contact_phone, contact_email, status, notes, published_date, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $user_id = $_SESSION['user_id'] ?? null;

        $stmt->bind_param(
            'sssssssssssssssssssi',
            $title,
            $training_topic,
            $reference_number,
            $host_department,
            $training_type,
            $target_audience,
            $location,
            $training_dates,
            $registration_deadline,
            $price,
            $agenda,
            $description,
            $pdfPath,
            $contact_person,
            $contact_phone,
            $contact_email,
            $status,
            $notes,
            $published_date,
            $user_id
        );

        if ($stmt->execute()) {
            header('Location: index.php?success=1');
            exit;
        } else {
            $errors[] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มประกาศอบรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">เพิ่มประกาศอบรม</h1>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>กลับ</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">หัวข้อประกาศ <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">หัวข้อการอบรม</label>
                    <input type="text" name="training_topic" class="form-control" value="<?php echo htmlspecialchars($training_topic); ?>">
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">เลขที่อ้างอิง</label>
                    <input type="text" name="reference_number" class="form-control" value="<?php echo htmlspecialchars($reference_number); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">หน่วยงานผู้จัด</label>
                    <input type="text" name="host_department" class="form-control" value="<?php echo htmlspecialchars($host_department); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">รูปแบบการอบรม</label>
                    <input type="text" name="training_type" class="form-control" value="<?php echo htmlspecialchars($training_type); ?>">
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">กลุ่มเป้าหมาย</label>
                    <input type="text" name="target_audience" class="form-control" value="<?php echo htmlspecialchars($target_audience); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">สถานที่</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($location); ?>">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">กำหนดการอบรม</label>
                <textarea name="training_dates" class="form-control" rows="3"><?php echo htmlspecialchars($training_dates); ?></textarea>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">ปิดรับสมัคร</label>
                    <input type="date" name="registration_deadline" class="form-control" value="<?php echo $registration_deadline; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ค่าลงทะเบียน</label>
                    <input type="text" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันที่ประกาศ</label>
                    <input type="date" name="published_date" class="form-control" value="<?php echo $published_date; ?>" required>
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">หัวข้อการอบรม (Agenda)</label>
                <textarea name="agenda" class="form-control" rows="3"><?php echo htmlspecialchars($agenda); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">รายละเอียดเพิ่มเติม</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">ไฟล์รายละเอียด (PDF) <span class="text-danger">*</span></label>
                <input type="file" name="document_pdf" class="form-control" accept="application/pdf" required>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">ผู้ติดต่อ</label>
                    <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($contact_person); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">โทรศัพท์</label>
                    <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($contact_phone); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($contact_email); ?>">
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">สถานะ</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>ฉบับร่าง</option>
                        <option value="open" <?php echo $status === 'open' ? 'selected' : ''; ?>>เปิดรับสมัคร</option>
                        <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>สิ้นสุดแล้ว</option>
                        <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($notes); ?></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>บันทึกประกาศ</button>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
