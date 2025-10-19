<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireRankingsAccess();

$errors = [];
$title = '';
$position_title = '';
$reference_number = '';
$department = '';
$employment_type = '';
$number_of_positions = 1;
$responsibilities = '';
$qualifications = '';
$benefits = '';
$application_process = '';
$salary_range = '';
$published_date = date('Y-m-d');
$application_deadline = date('Y-m-d', strtotime('+15 days'));
$interview_date = '';
$contact_person = '';
$contact_phone = '';
$contact_email = '';
$status = 'draft';
$notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $position_title = trim($_POST['position_title'] ?? '');
    $reference_number = trim($_POST['reference_number'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $employment_type = trim($_POST['employment_type'] ?? '');
    $number_of_positions = isset($_POST['number_of_positions']) ? (int)$_POST['number_of_positions'] : 1;
    $responsibilities = trim($_POST['responsibilities'] ?? '');
    $qualifications = trim($_POST['qualifications'] ?? '');
    $benefits = trim($_POST['benefits'] ?? '');
    $application_process = trim($_POST['application_process'] ?? '');
    $salary_range = trim($_POST['salary_range'] ?? '');
    $published_date = $_POST['published_date'] ?? date('Y-m-d');
    $application_deadline = $_POST['application_deadline'] ?? date('Y-m-d', strtotime('+15 days'));
    $interview_date = $_POST['interview_date'] ?? '';
    $contact_person = trim($_POST['contact_person'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $notes = trim($_POST['notes'] ?? '');

    if ($title === '') {
        $errors[] = 'กรุณากรอกหัวข้อประกาศ';
    }
    if ($position_title === '') {
        $errors[] = 'กรุณากรอกชื่อตำแหน่งงาน';
    }

    $pdfPath = '';
    if (isset($_FILES['document_pdf']) && $_FILES['document_pdf']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['document_pdf']['tmp_name'];
        $fileName = basename($_FILES['document_pdf']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExt !== 'pdf') {
            $errors[] = 'อนุญาตเฉพาะไฟล์ PDF เท่านั้น';
        } else {
            $newName = 'recruitment_' . uniqid() . '.pdf';
            $uploadDir = '../../documents/recruitments/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            if (move_uploaded_file($fileTmp, $uploadDir . $newName)) {
                $pdfPath = 'documents/recruitments/' . $newName;
            } else {
                $errors[] = 'ไม่สามารถบันทึกไฟล์ PDF ได้';
            }
        }
    } else {
        $errors[] = 'กรุณาอัปโหลดไฟล์ PDF รายละเอียดประกาศ';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO recruitment_announcements (
            title, position_title, reference_number, department, employment_type, number_of_positions,
            responsibilities, qualifications, benefits, application_process, salary_range,
            published_date, application_deadline, interview_date, document_pdf,
            contact_person, contact_phone, contact_email, status, notes, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $user_id = $_SESSION['user_id'] ?? null;

        $stmt->bind_param(
            'ssssisssssssssssssssi',
            $title,
            $position_title,
            $reference_number,
            $department,
            $employment_type,
            $number_of_positions,
            $responsibilities,
            $qualifications,
            $benefits,
            $application_process,
            $salary_range,
            $published_date,
            $application_deadline,
            $interview_date,
            $pdfPath,
            $contact_person,
            $contact_phone,
            $contact_email,
            $status,
            $notes,
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
    <title>เพิ่มประกาศรับสมัครงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">เพิ่มประกาศรับสมัครงาน</h1>
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
                    <label class="form-label">ชื่อตำแหน่งงาน <span class="text-danger">*</span></label>
                    <input type="text" name="position_title" class="form-control" value="<?php echo htmlspecialchars($position_title); ?>" required>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">เลขที่อ้างอิง</label>
                    <input type="text" name="reference_number" class="form-control" value="<?php echo htmlspecialchars($reference_number); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">หน่วยงาน</label>
                    <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($department); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">รูปแบบการจ้างงาน</label>
                    <input type="text" name="employment_type" class="form-control" value="<?php echo htmlspecialchars($employment_type); ?>">
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-3">
                    <label class="form-label">จำนวนอัตรา</label>
                    <input type="number" name="number_of_positions" class="form-control" min="1" value="<?php echo htmlspecialchars($number_of_positions); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่ประกาศ <span class="text-danger">*</span></label>
                    <input type="date" name="published_date" class="form-control" value="<?php echo $published_date; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">ปิดรับสมัคร <span class="text-danger">*</span></label>
                    <input type="date" name="application_deadline" class="form-control" value="<?php echo $application_deadline; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันสัมภาษณ์ (ถ้ามี)</label>
                    <input type="date" name="interview_date" class="form-control" value="<?php echo htmlspecialchars($interview_date); ?>">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">หน้าที่ความรับผิดชอบ</label>
                <textarea name="responsibilities" class="form-control" rows="4"><?php echo htmlspecialchars($responsibilities); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">คุณสมบัติผู้สมัคร</label>
                <textarea name="qualifications" class="form-control" rows="4"><?php echo htmlspecialchars($qualifications); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">สวัสดิการ</label>
                <textarea name="benefits" class="form-control" rows="3"><?php echo htmlspecialchars($benefits); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">ขั้นตอนการสมัคร</label>
                <textarea name="application_process" class="form-control" rows="3"><?php echo htmlspecialchars($application_process); ?></textarea>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">ช่วงเงินเดือน</label>
                    <input type="text" name="salary_range" class="form-control" value="<?php echo htmlspecialchars($salary_range); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ไฟล์รายละเอียด (PDF) <span class="text-danger">*</span></label>
                    <input type="file" name="document_pdf" class="form-control" accept="application/pdf" required>
                </div>
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
                        <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>ปิดรับสมัคร</option>
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
