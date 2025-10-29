<?php
session_start();
require_once '../includes/db_config.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!function_exists('hall_ensure_column')) {
    function hall_ensure_column(mysqli $conn, string $column, string $definition): void
    {
        $check = $conn->query("SHOW COLUMNS FROM `hall_of_fame` LIKE '" . $conn->real_escape_string($column) . "'");
        if ($check && $check->num_rows === 0) {
            $conn->query("ALTER TABLE `hall_of_fame` ADD COLUMN `$column` $definition");
        }
    }
}

hall_ensure_column($conn, 'sdg_goals', 'VARCHAR(255) DEFAULT NULL');
hall_ensure_column($conn, 'likes', 'INT UNSIGNED NOT NULL DEFAULT 0');
hall_ensure_column($conn, 'views', 'INT UNSIGNED NOT NULL DEFAULT 0');

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

// ตรวจสอบว่ามี ID ส่งมาหรือไม่
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ไม่พบข้อมูลที่ต้องการแก้ไข";
    header("Location: index.php");
    exit();
}

$hall_id = intval($_GET['id']);

// ดึงข้อมูลเดิม
$sql = "SELECT * FROM hall_of_fame WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hall_id);
$stmt->execute();
$result = $stmt->get_result();
$achievement = $result->fetch_assoc();

if (!$achievement) {
    $_SESSION['error'] = "ไม่พบข้อมูลที่ต้องการแก้ไข";
    header("Location: index.php");
    exit();
}

$selected_sdgs = !empty($achievement['sdg_goals']) ? array_filter(array_map('trim', explode(',', $achievement['sdg_goals']))) : [];
$sdg_values = array_map(static fn($value) => (string)(int)$value, $selected_sdgs);

// ดึงรูปภาพเพิ่มเติม
$gallery_sql = "SELECT * FROM hall_of_fame_gallery WHERE hall_id = ? ORDER BY sort_order";
$gallery_stmt = $conn->prepare($gallery_sql);
$gallery_stmt->bind_param("i", $hall_id);
$gallery_stmt->execute();
$gallery_result = $gallery_stmt->get_result();
$gallery = [];
while ($row = $gallery_result->fetch_assoc()) {
    $gallery[] = $row;
}

// จัดการเมื่อส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $student_name = $_POST['student_name'];
    $class = $_POST['class'];
    $year = $_POST['year'];
    $achievement_text = $_POST['achievement'];
    $description = $_POST['description'];
    $date_achieved = $_POST['date_achieved'] ?: null;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = $_POST['status'];
    $selected_sdgs = (isset($_POST['sdg_goals']) && is_array($_POST['sdg_goals'])) ? array_values(array_unique($_POST['sdg_goals'])) : [];
    $sdg_values = array_map(static fn($value) => (string)(int)$value, $selected_sdgs);
    $sdg_goals = !empty($sdg_values) ? implode(',', $sdg_values) : '';
    
    // จัดการอัพโหลดรูปภาพหลัก
    $image_path = $achievement['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../uploads/hall_of_fame/' . $category . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'hall_' . date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // ลบไฟล์เก่า
                if ($achievement['image_path'] && file_exists('../../' . $achievement['image_path'])) {
                    unlink('../../' . $achievement['image_path']);
                }
                $image_path = 'uploads/hall_of_fame/' . $category . '/' . $new_filename;
            }
        }
    }
    
    // จัดการอัพโหลดใบประกาศ
    $certificate_path = $achievement['certificate_path'];
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $upload_dir = '../../uploads/hall_of_fame/' . $category . '/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['certificate']['name'], PATHINFO_EXTENSION);
        $new_filename = 'cert_' . date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;
        
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
                // ลบไฟล์เก่า
                if ($achievement['certificate_path'] && file_exists('../../' . $achievement['certificate_path'])) {
                    unlink('../../' . $achievement['certificate_path']);
                }
                $certificate_path = 'uploads/hall_of_fame/' . $category . '/certificates/' . $new_filename;
            }
        }
    }
    
    // อัปเดทข้อมูลในฐานข้อมูล
    $sql = "UPDATE hall_of_fame SET 
            category = ?, title = ?, student_name = ?, class = ?, year = ?, 
            achievement = ?, description = ?, image_path = ?, certificate_path = ?, 
            date_achieved = ?, sdg_goals = ?, featured = ?, status = ?
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssisi", 
        $category, $title, $student_name, $class, $year, $achievement_text, $description,
        $image_path, $certificate_path, $date_achieved, $sdg_goals, $featured, $status, $hall_id
    );
    
    if ($stmt->execute()) {
        // จัดการลบรูปภาพแกลเลอรี่
        if (isset($_POST['delete_gallery']) && is_array($_POST['delete_gallery'])) {
            foreach ($_POST['delete_gallery'] as $gallery_id) {
                $del_sql = "SELECT image_path FROM hall_of_fame_gallery WHERE id = ?";
                $del_stmt = $conn->prepare($del_sql);
                $del_stmt->bind_param("i", $gallery_id);
                $del_stmt->execute();
                $del_result = $del_stmt->get_result();
                if ($del_row = $del_result->fetch_assoc()) {
                    if (file_exists('../../' . $del_row['image_path'])) {
                        unlink('../../' . $del_row['image_path']);
                    }
                }
                
                $del_sql = "DELETE FROM hall_of_fame_gallery WHERE id = ?";
                $del_stmt = $conn->prepare($del_sql);
                $del_stmt->bind_param("i", $gallery_id);
                $del_stmt->execute();
            }
        }
        
        // จัดการอัพโหลดรูปภาพเพิ่มเติม (Gallery)
        if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
            $gallery_dir = '../../uploads/hall_of_fame/' . $category . '/gallery/';
            if (!is_dir($gallery_dir)) {
                mkdir($gallery_dir, 0777, true);
            }
            
            $gallery_stmt = $conn->prepare("INSERT INTO hall_of_fame_gallery (hall_id, image_path, caption, sort_order) VALUES (?, ?, ?, ?)");
            
            $max_order_sql = "SELECT MAX(sort_order) as max_order FROM hall_of_fame_gallery WHERE hall_id = ?";
            $max_stmt = $conn->prepare($max_order_sql);
            $max_stmt->bind_param("i", $hall_id);
            $max_stmt->execute();
            $max_result = $max_stmt->get_result();
            $max_order = $max_result->fetch_assoc()['max_order'] ?? 0;
            
            for ($i = 0; $i < count($_FILES['gallery']['name']); $i++) {
                if ($_FILES['gallery']['error'][$i] == 0) {
                    $file_extension = pathinfo($_FILES['gallery']['name'][$i], PATHINFO_EXTENSION);
                    $new_filename = 'gallery_' . date('YmdHis') . '_' . $i . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $gallery_dir . $new_filename;
                    
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array(strtolower($file_extension), $allowed_types)) {
                        if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $target_file)) {
                            $gallery_path = 'uploads/hall_of_fame/' . $category . '/gallery/' . $new_filename;
                            $caption = isset($_POST['gallery_captions'][$i]) ? $_POST['gallery_captions'][$i] : '';
                            $sort_order = ++$max_order;
                            
                            $gallery_stmt->bind_param("issi", $hall_id, $gallery_path, $caption, $sort_order);
                            $gallery_stmt->execute();
                        }
                    }
                }
            }
        }
        
        $_SESSION['success'] = "แก้ไขข้อมูลสำเร็จ";
        header("Location: index.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขรางวัล - หอเกียรติยศ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: #f5f5f5;
        }
        
        .sidebar {
            background: #2c3e50;
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .main-content {
            padding: 30px;
        }
        
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .form-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 10px;
        }
        
        .gallery-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .gallery-preview .gallery-item {
            position: relative;
            width: 100px;
            height: 100px;
        }
        
        .gallery-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .gallery-delete {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .existing-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .existing-gallery .gallery-item {
            position: relative;
            width: 120px;
        }
        
        .existing-gallery img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .existing-gallery .delete-checkbox {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        .sdg-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
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
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4">
                    <i class="fas fa-trophy"></i> Admin Panel
                </h4>
                <nav class="nav flex-column">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="add.php">
                        <i class="fas fa-plus me-2"></i> เพิ่มรางวัล
                    </a>
                    <a class="nav-link active" href="#">
                        <i class="fas fa-edit me-2"></i> แก้ไขรางวัล
                    </a>
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home me-2"></i> กลับหน้า Admin
                    </a>
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="form-card">
                    <div class="form-header">
                        <h3><i class="fas fa-edit me-2"></i> แก้ไขรางวัล</h3>
                        <p class="text-muted mb-0">แก้ไขข้อมูลรางวัลและผลงานของนักเรียน</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- ข้อมูลพื้นฐาน -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">เลือกหมวดหมู่</option>
                                        <option value="academic" <?php echo $achievement['category'] == 'academic' ? 'selected' : ''; ?>>วิชาการ</option>
                                        <option value="sports" <?php echo $achievement['category'] == 'sports' ? 'selected' : ''; ?>>กีฬา</option>
                                        <option value="music" <?php echo $achievement['category'] == 'music' ? 'selected' : ''; ?>>ดนตรี</option>
                                        <option value="scholarship" <?php echo $achievement['category'] == 'scholarship' ? 'selected' : ''; ?>>ทุนการศึกษา</option>
                                        <option value="outstanding" <?php echo $achievement['category'] == 'outstanding' ? 'selected' : ''; ?>>ความโดดเด่น</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="active" <?php echo $achievement['status'] == 'active' ? 'selected' : ''; ?>>เผยแพร่</option>
                                        <option value="inactive" <?php echo $achievement['status'] == 'inactive' ? 'selected' : ''; ?>>ไม่เผยแพร่</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อรางวัล/ผลงาน <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" 
                                           value="<?php echo htmlspecialchars($achievement['title']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อนักเรียน <span class="text-danger">*</span></label>
                                    <input type="text" name="student_name" class="form-control" 
                                           value="<?php echo htmlspecialchars($achievement['student_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">ระดับชั้น</label>
                                    <input type="text" name="class" class="form-control" 
                                           value="<?php echo htmlspecialchars($achievement['class']); ?>" 
                                           placeholder="เช่น ม.6/1">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">ปีการศึกษา</label>
                                    <input type="text" name="year" class="form-control" 
                                           value="<?php echo htmlspecialchars($achievement['year']); ?>" 
                                           placeholder="เช่น 2567">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">วันที่ได้รับรางวัล</label>
                                    <input type="date" name="date_achieved" class="form-control"
                                           value="<?php echo $achievement['date_achieved']; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3 pt-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="featured" class="form-check-input" id="featured"
                                               <?php echo $achievement['featured'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="featured">
                                            <i class="fas fa-star text-warning"></i> แนะนำ (Featured)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ผลงานและคำอธิบาย -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">ผลงานและความสำเร็จ</label>
                                    <textarea name="achievement" class="form-control" rows="4" 
                                              placeholder="อธิบายผลงาน รางวัล หรือความสำเร็จที่ได้รับ"><?php echo htmlspecialchars($achievement['achievement']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">รายละเอียดเพิ่มเติม</label>
                                    <textarea name="description" class="form-control" rows="4" 
                                              placeholder="รายละเอียดเพิ่มเติม เบื้องหลัง หรือเรื่องราวที่น่าสนใจ"><?php echo htmlspecialchars($achievement['description']); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
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
                                    <small class="text-muted">เลือกหลายข้อได้ เพื่อสะท้อนผลกระทบของผลงานต่อ SDGs</small>
                                </div>
                            </div>
                            
                            <!-- อัพโหลดไฟล์ -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">รูปภาพหลัก</label>
                                    <?php if ($achievement['image_path']): ?>
                                    <div>
                                        <img src="../../<?php echo htmlspecialchars($achievement['image_path']); ?>" 
                                             class="preview-image mb-2">
                                        <p class="text-muted small">รูปภาพปัจจุบัน</p>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" name="image" class="form-control" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">รองรับ: JPG, PNG, GIF, WEBP (เลือกใหม่เพื่อเปลี่ยน)</small>
                                    <div id="imagePreview"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ใบประกาศเกียรติคุณ</label>
                                    <?php if ($achievement['certificate_path']): ?>
                                    <div>
                                        <a href="../../<?php echo htmlspecialchars($achievement['certificate_path']); ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-info mb-2">
                                            <i class="fas fa-file-pdf"></i> ดูไฟล์ปัจจุบัน
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" name="certificate" class="form-control" 
                                           accept=".pdf,image/*">
                                    <small class="text-muted">รองรับ: PDF, JPG, PNG (เลือกใหม่เพื่อเปลี่ยน)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">รูปภาพเพิ่มเติม (Gallery)</label>
                                    
                                    <?php if (!empty($gallery)): ?>
                                    <div class="existing-gallery">
                                        <p class="w-100 text-muted">รูปภาพปัจจุบัน (ติ๊กเพื่อลบ):</p>
                                        <?php foreach ($gallery as $img): ?>
                                        <div class="gallery-item">
                                            <img src="../../<?php echo htmlspecialchars($img['image_path']); ?>">
                                            <input type="checkbox" name="delete_gallery[]" value="<?php echo $img['id']; ?>" 
                                                   class="delete-checkbox form-check-input">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <input type="file" name="gallery[]" class="form-control" 
                                           accept="image/*" multiple onchange="previewGallery(this)">
                                    <small class="text-muted">สามารถเลือกหลายรูปได้ (เพิ่มเติมจากที่มีอยู่)</small>
                                    <div id="galleryPreview" class="gallery-preview"></div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<div class="mt-2"><p class="text-success small">รูปภาพใหม่:</p><img src="${e.target.result}" class="preview-image"></div>`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function previewGallery(input) {
        const preview = document.getElementById('galleryPreview');
        preview.innerHTML = '';
        
        if (input.files) {
            const label = document.createElement('p');
            label.className = 'w-100 text-success small mt-2';
            label.textContent = 'รูปภาพใหม่ที่จะเพิ่ม:';
            preview.appendChild(label);
            
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'gallery-item';
                    div.innerHTML = `<img src="${e.target.result}">`;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }
    </script>
</body>
</html>
