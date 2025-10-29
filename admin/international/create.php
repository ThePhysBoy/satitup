<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireRankingsAccess();

$sdg_options = [
    1 => ['name' => 'ขจัดความยากจน', 'color' => '#E5243B'],
    2 => ['name' => 'ขจัดความหิวโหย', 'color' => '#DDA63A'],
    3 => ['name' => 'สุขภาพและความเป็นอยู่ที่ดี', 'color' => '#4C9F38'],
    4 => ['name' => 'การศึกษาที่มีคุณภาพ', 'color' => '#C5192D'],
    5 => ['name' => 'ความเท่าเทียมทางเพศ', 'color' => '#FF3A21'],
    6 => ['name' => 'น้ำสะอาดและสุขาภิบาล', 'color' => '#26BDE2'],
    7 => ['name' => 'พลังงานสะอาดที่เข้าถึงได้', 'color' => '#FCC30B'],
    8 => ['name' => 'งานที่มีคุณค่าและการเติบโตทางเศรษฐกิจ', 'color' => '#A21942'],
    9 => ['name' => 'อุตสาหกรรม นวัตกรรม และโครงสร้างพื้นฐาน', 'color' => '#FD6925'],
    10 => ['name' => 'ลดความเหลื่อมล้ำ', 'color' => '#DD1367'],
    11 => ['name' => 'เมืองและชุมชนที่ยั่งยืน', 'color' => '#FD9D24'],
    12 => ['name' => 'การบริโภคและการผลิตที่ยั่งยืน', 'color' => '#BF8B2E'],
    13 => ['name' => 'การดำเนินการด้านสภาพภูมิอากาศ', 'color' => '#3F7E44'],
    14 => ['name' => 'ชีวิตใต้น้ำ', 'color' => '#0A97D9'],
    15 => ['name' => 'ชีวิตบนบก', 'color' => '#56C02B'],
    16 => ['name' => 'สันติภาพ ความยุติธรรม และสถาบันที่เข้มแข็ง', 'color' => '#00689D'],
    17 => ['name' => 'ความร่วมมือเพื่อบรรลุเป้าหมาย', 'color' => '#19486A'],
];

if (!function_exists('international_ensure_column')) {
    function international_ensure_column(mysqli $conn, string $column, string $definition): void
    {
        $check = $conn->query("SHOW COLUMNS FROM `international_assignments` LIKE '" . $conn->real_escape_string($column) . "'");
        if ($check && $check->num_rows === 0) {
            $conn->query("ALTER TABLE `international_assignments` ADD COLUMN `$column` $definition");
        }
    }
}

international_ensure_column($conn, 'sdg_goals', 'VARCHAR(255) DEFAULT NULL');
international_ensure_column($conn, 'likes', 'INT UNSIGNED NOT NULL DEFAULT 0');
international_ensure_column($conn, 'views', 'INT UNSIGNED NOT NULL DEFAULT 0');

$errors = [];
$title = '';
$person_name = '';
$role = '';
$affiliation = '';
$country = '';
$city = '';
$purpose = '';
$start_date = '';
$end_date = '';
$duration_text = '';
$event_name = '';
$achievement = '';
$description = '';
$video_url = '';
$status = 'published';
$featured = 0;
$published_date = date('Y-m-d');
$selected_sdgs = [];
$sdg_values = [];

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
    $published_date = $_POST['published_date'] ?? date('Y-m-d');
    $selected_sdgs = (isset($_POST['sdg_goals']) && is_array($_POST['sdg_goals'])) ? array_values(array_unique($_POST['sdg_goals'])) : [];
    $sdg_values = array_map(static fn($value) => (string)(int)$value, $selected_sdgs);
    $sdg_goals = !empty($sdg_values) ? implode(',', $sdg_values) : '';

    if ($title === '') {
        $errors[] = 'กรุณากรอกหัวข้อประกาศ';
    }
    if ($person_name === '') {
        $errors[] = 'กรุณากรอกชื่อผู้เดินทาง';
    }
    if ($country === '') {
        $errors[] = 'กรุณากรอกประเทศ';
    }

    $coverPath = '';
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
                $coverPath = 'images/international/' . $coverFilename;
            } else {
                $errors[] = 'ไม่สามารถอัปโหลดรูปปกได้';
            }
        }
    } else {
        $errors[] = 'กรุณาอัปโหลดรูปปก';
    }

    $galleryPaths = [];
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

    $documentPath = '';
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
                $documentPath = 'documents/international/' . $docFilename;
            } else {
                $errors[] = 'ไม่สามารถอัปโหลดไฟล์เอกสารได้';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO international_assignments (
            title, person_name, role, affiliation, country, city, purpose, start_date, end_date, duration_text,
            event_name, achievement, description, cover_image, gallery_images, document_pdf, video_url, sdg_goals,
            status, featured, published_date, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $galleryJson = !empty($galleryPaths) ? json_encode($galleryPaths) : null;
        $user_id = $_SESSION['user_id'] ?? null;

        $stmt->bind_param(
            'sssssssssssssssssssisi',
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
            $sdg_goals,
            $status,
            $featured,
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
    <title>เพิ่มประกาศการไปต่างประเทศ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sdg-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 10px;
        }

        .sdg-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #e5e5e5;
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            transition: all 0.2s ease;
        }

        .sdg-option:hover {
            border-color: var(--sdg-color, #7b3b95);
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .sdg-option input[type="checkbox"] {
            accent-color: var(--sdg-color, #7b3b95);
            transform: scale(1.05);
        }

        .sdg-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--sdg-color, #7b3b95);
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .sdg-name {
            font-size: 0.85rem;
            color: #555;
            line-height: 1.3;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">เพิ่มประกาศการไปต่างประเทศ</h1>
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
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">บทบาท/ตำแหน่ง</label>
                    <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($role); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">สังกัด/หน่วยงาน</label>
                    <input type="text" name="affiliation" class="form-control" value="<?php echo htmlspecialchars($affiliation); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ประเทศ <span class="text-danger">*</span></label>
                    <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($country); ?>" required>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">เมือง</label>
                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันเดินทาง</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันกลับ</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">ระยะเวลา (เช่น 5 วัน)</label>
                    <input type="text" name="duration_text" class="form-control" value="<?php echo htmlspecialchars($duration_text); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ชื่องาน/โครงการ</label>
                    <input type="text" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event_name); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">สถานะประกาศ</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>ฉบับร่าง</option>
                        <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>เผยแพร่</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">วัตถุประสงค์ของการเดินทาง</label>
                <textarea name="purpose" class="form-control" rows="3"><?php echo htmlspecialchars($purpose); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">รายละเอียดเพิ่มเติม</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">ผลงาน/ความสำเร็จ</label>
                <textarea name="achievement" class="form-control" rows="3"><?php echo htmlspecialchars($achievement); ?></textarea>
            </div>

            <div class="mt-3">
                <label class="form-label">เป้าหมายการพัฒนาที่ยั่งยืน (SDGs)</label>
                <div class="sdg-grid">
                    <?php foreach ($sdg_options as $sdgNumber => $sdg): 
                        $isChecked = in_array((string)$sdgNumber, $sdg_values, true);
                    ?>
                    <label class="sdg-option" style="--sdg-color: <?php echo htmlspecialchars($sdg['color']); ?>;">
                        <input type="checkbox" name="sdg_goals[]" value="<?php echo $sdgNumber; ?>" <?php echo $isChecked ? 'checked' : ''; ?>>
                        <span class="sdg-number"><?php echo $sdgNumber; ?></span>
                        <span class="sdg-name"><?php echo htmlspecialchars($sdg['name']); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <small class="text-muted">เลือกหลายข้อได้ เพื่อสะท้อนผลการเดินทางที่เกี่ยวข้องกับ SDGs</small>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">วิดีโอที่เกี่ยวข้อง (ลิงก์ YouTube หรืออื่น ๆ)</label>
                    <input type="url" name="video_url" class="form-control" value="<?php echo htmlspecialchars($video_url); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่ประกาศ</label>
                    <input type="date" name="published_date" class="form-control" value="<?php echo htmlspecialchars($published_date); ?>">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="featured" id="featured" <?php echo $featured ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="featured">แสดงเป็นไฮไลต์</label>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">รูปปก <span class="text-danger">*</span></label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">รูปแกลเลอรี (อัปโหลดได้หลายรูป)</label>
                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">ไฟล์เอกสาร (PDF)</label>
                <input type="file" name="document_pdf" class="form-control" accept="application/pdf">
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
