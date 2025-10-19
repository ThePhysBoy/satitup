<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireRankingsAccess();

$errors = [];
$title = '';
$reference_number = '';
$procurement_method = '';
$department = '';
$description = '';
$budget_amount = '';
$currency = 'THB';
$published_date = date('Y-m-d');
$closing_date = date('Y-m-d', strtotime('+15 days'));
$contact_person = '';
$contact_phone = '';
$contact_email = '';
$status = 'draft';
$notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $reference_number = trim($_POST['reference_number'] ?? '');
    $procurement_method = trim($_POST['procurement_method'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $budget_amount = $_POST['budget_amount'] !== '' ? (float)$_POST['budget_amount'] : null;
    $currency = trim($_POST['currency'] ?? 'THB');
    $published_date = $_POST['published_date'] ?? date('Y-m-d');
    $closing_date = $_POST['closing_date'] ?? date('Y-m-d', strtotime('+15 days'));
    $contact_person = trim($_POST['contact_person'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $notes = trim($_POST['notes'] ?? '');

    if ($title === '') {
        $errors[] = 'กรุณากรอกชื่อประกาศ';
    }

    if (empty($_FILES['document_pdf']['name'])) {
        $errors[] = 'กรุณาอัปโหลดไฟล์ PDF ประกอบประกาศ';
    }

    $pdfPath = '';
    if (isset($_FILES['document_pdf']) && $_FILES['document_pdf']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['document_pdf']['tmp_name'];
        $fileName = basename($_FILES['document_pdf']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, ['pdf'])) {
            $errors[] = 'อนุญาตเฉพาะไฟล์ PDF';
        } else {
            $newName = 'procurement_' . uniqid() . '.pdf';
            $uploadDir = '../../documents/procurements/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            if (move_uploaded_file($fileTmp, $uploadDir . $newName)) {
                $pdfPath = 'documents/procurements/' . $newName;
            } else {
                $errors[] = 'ไม่สามารถบันทึกไฟล์ PDF ได้';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO procurement_announcements (
            title, reference_number, procurement_method, department, description, budget_amount, currency,
            published_date, closing_date, document_pdf, contact_person, contact_phone, contact_email,
            status, notes, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $user_id = $_SESSION['user_id'] ?? null;

        $stmt->bind_param(
            'sssssdsssssssssi',
            $title,
            $reference_number,
            $procurement_method,
            $department,
            $description,
            $budget_amount,
            $currency,
            $published_date,
            $closing_date,
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
    <title>เพิ่มประกาศจัดซื้อจัดจ้าง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">เพิ่มประกาศจัดซื้อจัดจ้าง</h1>
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
            <div class="mb-3">
                <label class="form-label">ชื่อประกาศ <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">เลขที่อ้างอิง</label>
                    <input type="text" name="reference_number" class="form-control" value="<?php echo htmlspecialchars($reference_number); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วิธีจัดซื้อจัดจ้าง</label>
                    <input type="text" name="procurement_method" class="form-control" value="<?php echo htmlspecialchars($procurement_method); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">หน่วยงาน/กลุ่มงาน</label>
                    <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($department); ?>">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">รายละเอียด</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">งบประมาณ (บาท)</label>
                    <input type="number" name="budget_amount" class="form-control" step="0.01" value="<?php echo htmlspecialchars($budget_amount); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">สกุลเงิน</label>
                    <input type="text" name="currency" class="form-control" value="<?php echo htmlspecialchars($currency); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่ประกาศ</label>
                    <input type="date" name="published_date" class="form-control" value="<?php echo $published_date; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">หมดเขต</label>
                    <input type="date" name="closing_date" class="form-control" value="<?php echo $closing_date; ?>" required>
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">ไฟล์ประกาศ (PDF) <span class="text-danger">*</span></label>
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
                        <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>เผยแพร่</option>
                        <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>สิ้นสุด</option>
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
