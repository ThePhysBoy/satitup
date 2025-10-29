<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireRankingsAccess();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM international_assignments WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

if (!$assignment) {
    header('Location: index.php');
    exit;
}

$errors = [];
$title = $assignment['title'];
$person_name = $assignment['person_name'];
$role = $assignment['role'];
$affiliation = $assignment['affiliation'];
$country = $assignment['country'];
$city = $assignment['city'];
$purpose = $assignment['purpose'];
$start_date = $assignment['start_date'];
$end_date = $assignment['end_date'];
$duration_text = $assignment['duration_text'];
$event_name = $assignment['event_name'];
$achievement = $assignment['achievement'];
$description = $assignment['description'];
$video_url = $assignment['video_url'];
$status = $assignment['status'];
$featured = $assignment['featured'];
$published_date = $assignment['published_date'];
$current_cover = $assignment['cover_image'];
$current_gallery = !empty($assignment['gallery_images']) ? json_decode($assignment['gallery_images'], true) : [];
$current_document = $assignment['document_pdf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $person_name = trim($_POST['person_name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $affiliation = trim($_POST['affiliation'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $duration_text = trim($_POST['duration_text'] ?? '');
    $event_name = trim($_POST['event_name'] ?? '');
    $achievement = trim($_POST['achievement'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $featured = isset($_POST['featured']) ? 1 : 0;
    $published_date = $_POST['published_date'] ?? $published_date;

    if ($title === '') {
        $errors[] = 'กรุณากรอกหัวข้อประกาศ';
    }
    if ($person_name === '') {
        $errors[] = 'กรุณากรอกชื่อผู้เดินทาง';
    }
    if ($country === '') {
        $errors[] = 'กรุณากรอกประเทศ';
    }

    $coverPath = $current_cover;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverTmp = $_FILES['cover_image']['tmp_name'];
        $coverExt = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($coverExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $errors[] = 'รูปปกต้องเป็นไฟล์ภาพ (jpg, png, gif, webp)';
        } else {
            $coverDir = '../../images/international/';
            if (!is_dir($coverDir)) {
                mkdir($coverDir, 0755, true);
            }
            $coverFilename = 'international_cover_' . uniqid() . '.' . $coverExt;
            if (move_uploaded_file($coverTmp, $coverDir . $coverFilename)) {
                if (!empty($current_cover) && file_exists('../../' . $current_cover)) {
                    unlink('../../' . $current_cover);
                }
                $coverPath = 'images/international/' . $coverFilename;
            } else {
                $errors[] = 'ไม่สามารถอัปโหลดรูปปกได้';
            }
        }
    }

    $galleryPaths = $current_gallery ?? [];
    if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
        $galleryDir = '../../images/international/gallery/';
        if (!is_dir($galleryDir)) {
            mkdir($galleryDir, 0755, true);
        }
        foreach ($_FILES['gallery_images']['name'] as $idx => $name) {
            if ($_FILES['gallery_images']['error'][$idx] === UPLOAD_ERR_OK && $_FILES['gallery_images']['size'][$idx] > 0) {
                $tmpName = $_FILES['gallery_images']['tmp_name'][$idx];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $errors[] = 'ไฟล์แกลเลอรีต้องเป็นภาพ (jpg, png, gif, webp)';
                    continue;
                }
                $galleryFilename = 'international_gallery_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($tmpName, $galleryDir . $galleryFilename)) {
                    $galleryPaths[] = 'images/international/gallery/' . $galleryFilename;
                }
            }
        }
    }

    $documentPath = $current_document;
    if (isset($_FILES['document_pdf']) && $_FILES['document_pdf']['error'] === UPLOAD_ERR_OK) {
        $documentTmp = $_FILES['document_pdf']['tmp_name'];
        $documentExt = strtolower(pathinfo($_FILES['document_pdf']['name'], PATHINFO_EXTENSION));
        if ($documentExt !== 'pdf') {
            $errors[] = 'ไฟล์เอกสารต้องเป็น PDF เท่านั้น';
        } else {
            $docDir = '../../documents/international/';
            if (!is_dir($docDir)) {
                mkdir($docDir, 0755, true);
            }
            $docFilename = 'international_doc_' . uniqid() . '.pdf';
            if (move_uploaded_file($documentTmp, $docDir . $docFilename)) {
                if (!empty($current_document) && file_exists('../../' . $current_document)) {
                    unlink('../../' . $current_document);
                }
                $documentPath = 'documents/international/' . $docFilename;
            } else {
                $errors[] = 'ไม่สามารถอัปโหลดไฟล์เอกสารได้';
            }
        }
    }

    if (isset($_POST['remove_gallery']) && is_array($_POST['remove_gallery'])) {
        foreach ($_POST['remove_gallery'] as $removePath) {
            $index = array_search($removePath, $galleryPaths, true);
            if ($index !== false) {
                if (file_exists('../../' . $removePath)) {
                    unlink('../../' . $removePath);
                }
                unset($galleryPaths[$index]);
            }
        }
        $galleryPaths = array_values($galleryPaths);
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE international_assignments SET
            title = ?, person_name = ?, role = ?, affiliation = ?, country = ?, city = ?, purpose = ?, start_date = ?, end_date = ?, duration_text = ?,
            event_name = ?, achievement = ?, description = ?, cover_image = ?, gallery_images = ?, document_pdf = ?, video_url = ?,
            status = ?, featured = ?, published_date = ?, updated_by = ?
            WHERE id = ?");

        $galleryJson = !empty($galleryPaths) ? json_encode($galleryPaths) : null;
        $user_id = $_SESSION['user_id'] ?? null;

        $stmt->bind_param(
            'ssssssssssssssssssiii',
            $title,
            $person_name,
            $role,
            $affiliation,
            $country,
            $city,
            $purpose,
            $start_date,
            $end_date,
            $duration_text,
            $event_name,
            $achievement,
            $description,
            $coverPath,
            $galleryJson,
            $documentPath,
            $video_url,
            $status,
            $featured,
            $published_date,
            $user_id,
            $id
        );

        if ($stmt->execute()) {
            header('Location: index.php?updated=1');
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
    <title>แก้ไขประกาศการไปต่างประเทศ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">แก้ไขประกาศการไปต่างประเทศ</h1>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>กลับ</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
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
                    <label class="form-label">ชื่อผู้เดินทาง <span class="text-danger">*</span></label>
                    <input type="text" name="person_name" class="form-control" value="<?php echo htmlspecialchars($person_name); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">บทบาท</label>
                    <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($role); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">สังกัด</label>
                    <input type="text" name="affiliation" class="form-control" value="<?php echo htmlspecialchars($affiliation); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ประเทศ <span class="text-danger">*</span></label>
                    <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($country); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">เมือง</label>
                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">วัตถุประสงค์ <span class="text-danger">*</span></label>
                    <input type="text" name="purpose" class="form-control" value="<?php echo htmlspecialchars($purpose); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ระยะเวลา</label>
                    <input type="text" name="duration_text" class="form-control" value="<?php echo htmlspecialchars($duration_text); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ชื่อเหตุการณ์</label>
                    <input type="text" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event_name); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ผลงาน</label>
                    <input type="text" name="achievement" class="form-control" value="<?php echo htmlspecialchars($achievement); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">รายละเอียด</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ลิงก์วิดีโอ (ถ้ามี)</label>
                    <input type="url" name="video_url" class="form-control" value="<?php echo htmlspecialchars($video_url); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">สถานะ</label>
                    <select name="status" class="form-control">
                        <option value="draft" <?php echo ($status === 'draft') ? 'selected' : ''; ?>>ร่าง</option>
                        <option value="published" <?php echo ($status === 'published') ? 'selected' : ''; ?>>เผยแพร่</option>
                        <option value="archived" <?php echo ($status === 'archived') ? 'selected' : ''; ?>>เก็บถาวร</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">นำเสนอเป็นหน้าหลัก</label>
                    <select name="featured" class="form-control">
                        <option value="0" <?php echo ($featured === 0) ? 'selected' : ''; ?>>ไม่เป็นหน้าหลัก</option>
                        <option value="1" <?php echo ($featured === 1) ? 'selected' : ''; ?>>เป็นหน้าหลัก</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">วันที่เผยแพร่</label>
                    <input type="date" name="published_date" class="form-control" value="<?php echo htmlspecialchars($published_date); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">รูปปก (ถ้ามี)</label>
                    <input type="file" name="cover_image" class="form-control">
                    <?php if (!empty($current_cover)): ?>
                        <p class="text-muted small mt-1">ปัจจุบัน: <a href="../../<?php echo htmlspecialchars($current_cover); ?>" target="_blank"><?php echo htmlspecialchars(basename($current_cover)); ?></a></p>
                        <input type="checkbox" name="remove_cover" value="<?php echo htmlspecialchars($current_cover); ?>"> ลบรูปปกปัจจุบัน
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <label class="form-label">แกลเลอรีภาพ (ถ้ามี)</label>
                    <input type="file" name="gallery_images[]" class="form-control" multiple>
                    <?php if (!empty($current_gallery)): ?>
                        <p class="text-muted small mt-1">ปัจจุบัน:</p>
                        <ul class="list-unstyled small">
                            <?php foreach ($current_gallery as $image): ?>
                                <li><?php echo htmlspecialchars(basename($image)); ?> <input type="checkbox" name="remove_gallery[]" value="<?php echo htmlspecialchars($image); ?>"> ลบ</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <label class="form-label">เอกสาร PDF (ถ้ามี)</label>
                    <input type="file" name="document_pdf" class="form-control">
                    <?php if (!empty($current_document)): ?>
                        <p class="text-muted small mt-1">ปัจจุบัน: <a href="../../<?php echo htmlspecialchars($current_document); ?>" target="_blank"><?php echo htmlspecialchars(basename($current_document)); ?></a></p>
                        <input type="checkbox" name="remove_document" value="<?php echo htmlspecialchars($current_document); ?>"> ลบเอกสารปัจจุบัน
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
