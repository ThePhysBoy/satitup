<?php
// เชื่อมต่อฐานข้อมูล
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'satitup';
$db_port = 3306;

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// รับ ID จาก URL
$hall_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($hall_id == 0) {
    header("Location: index.php");
    exit();
}

// ดึงข้อมูลหลัก
$stmt = $conn->prepare("SELECT * FROM hall_of_fame WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $hall_id);
$stmt->execute();
$result = $stmt->get_result();
$achievement = $result->fetch_assoc();

if (!$achievement) {
    header("Location: index.php");
    exit();
}

// บันทึกการเข้าชม
$log_stmt = $conn->prepare("INSERT INTO hall_of_fame_logs (hall_id, action, ip_address, user_agent) VALUES (?, 'view', ?, ?)");
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$log_stmt->bind_param("iss", $hall_id, $ip, $user_agent);
$log_stmt->execute();

// อัปเดทจำนวนการดู
$update_stmt = $conn->prepare("UPDATE hall_of_fame SET views = views + 1 WHERE id = ?");
$update_stmt->bind_param("i", $hall_id);
$update_stmt->execute();

// ดึงรูปภาพเพิ่มเติม
$gallery_stmt = $conn->prepare("SELECT * FROM hall_of_fame_gallery WHERE hall_id = ? ORDER BY sort_order");
$gallery_stmt->bind_param("i", $hall_id);
$gallery_stmt->execute();
$gallery_result = $gallery_stmt->get_result();
$gallery = [];
while ($row = $gallery_result->fetch_assoc()) {
    $gallery[] = $row;
}

// ดึงรายการที่เกี่ยวข้อง
$related_stmt = $conn->prepare("SELECT id, title, student_name, image_path, category 
                                FROM hall_of_fame 
                                WHERE category = ? AND id != ? AND status = 'active'
                                ORDER BY RAND()
                                LIMIT 4");
$related_stmt->bind_param("si", $achievement['category'], $hall_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related = [];
while ($row = $related_result->fetch_assoc()) {
    $related[] = $row;
}

// ฟังก์ชันสำหรับหมวดหมู่
function getCategoryInfo($category) {
    $categories = [
        'academic' => ['name' => 'วิชาการ', 'icon' => 'fa-graduation-cap', 'color' => '#3498db'],
        'sports' => ['name' => 'กีฬา', 'icon' => 'fa-trophy', 'color' => '#e74c3c'],
        'music' => ['name' => 'ดนตรี', 'icon' => 'fa-music', 'color' => '#9b59b6'],
        'scholarship' => ['name' => 'ทุนการศึกษา', 'icon' => 'fa-award', 'color' => '#f39c12'],
        'outstanding' => ['name' => 'ความโดดเด่น', 'icon' => 'fa-star', 'color' => '#27ae60']
    ];
    return isset($categories[$category]) ? $categories[$category] : ['name' => $category, 'icon' => 'fa-medal', 'color' => '#666'];
}

$cat_info = getCategoryInfo($achievement['category']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($achievement['title']); ?> - หอเกียรติยศ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: #f8f9fa;
        }
        
        .hero-section {
            background: linear-gradient(135deg, <?php echo $cat_info['color']; ?> 0%, <?php echo $cat_info['color']; ?>dd 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
        
        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            font-size: 1.1rem;
        }
        
        .hero-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .content-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #495057;
        }
        
        .info-value {
            flex: 1;
            color: #212529;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            aspect-ratio: 1;
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        
        .certificate-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            background: <?php echo $cat_info['color']; ?>;
            color: white;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .certificate-btn:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .related-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .related-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .related-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .related-body {
            padding: 15px;
        }
        
        .related-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .related-student {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.8rem;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="index.php">หอเกียรติยศ</a></li>
                    <li class="breadcrumb-item"><a href="index.php?category=<?php echo $achievement['category']; ?>">
                        <?php echo $cat_info['name']; ?>
                    </a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($achievement['student_name']); ?></li>
                </ol>
            </nav>
            
            <h1 class="hero-title">
                <i class="fas <?php echo $cat_info['icon']; ?> me-2"></i>
                <?php echo htmlspecialchars($achievement['title']); ?>
            </h1>
            
            <div class="hero-meta">
                <span>
                    <i class="fas fa-user-graduate"></i>
                    <?php echo htmlspecialchars($achievement['student_name']); ?>
                </span>
                <?php if ($achievement['class']): ?>
                <span>
                    <i class="fas fa-school"></i>
                    <?php echo htmlspecialchars($achievement['class']); ?>
                </span>
                <?php endif; ?>
                <?php if ($achievement['year']): ?>
                <span>
                    <i class="fas fa-calendar"></i>
                    ปีการศึกษา <?php echo htmlspecialchars($achievement['year']); ?>
                </span>
                <?php endif; ?>
                <span>
                    <i class="fas fa-eye"></i>
                    <?php echo number_format($achievement['views']); ?> การดู
                </span>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="content-section">
                    <?php if ($achievement['image_path']): ?>
                    <img src="../<?php echo htmlspecialchars($achievement['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($achievement['student_name']); ?>" 
                         class="main-image">
                    <?php endif; ?>
                    
                    <?php if ($achievement['achievement']): ?>
                    <h3 class="mb-3">ผลงานและความสำเร็จ</h3>
                    <p style="white-space: pre-line; line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($achievement['achievement'])); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($achievement['description']): ?>
                    <h3 class="mt-4 mb-3">รายละเอียดเพิ่มเติม</h3>
                    <div style="white-space: pre-line; line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($achievement['description'])); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($gallery)): ?>
                    <h3 class="mt-4 mb-3">รูปภาพเพิ่มเติม</h3>
                    <div class="gallery-grid">
                        <?php foreach ($gallery as $img): ?>
                        <div class="gallery-item" onclick="viewImage('../<?php echo htmlspecialchars($img['image_path']); ?>')">
                            <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($img['caption']); ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="col-lg-4">
                <div class="content-section">
                    <h4 class="mb-3">ข้อมูลรางวัล</h4>
                    <div class="info-card">
                        <div class="info-row">
                            <div class="info-label">ประเภท</div>
                            <div class="info-value">
                                <span class="badge" style="background: <?php echo $cat_info['color']; ?>;">
                                    <i class="fas <?php echo $cat_info['icon']; ?> me-1"></i>
                                    <?php echo $cat_info['name']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">นักเรียน</div>
                            <div class="info-value"><?php echo htmlspecialchars($achievement['student_name']); ?></div>
                        </div>
                        <?php if ($achievement['class']): ?>
                        <div class="info-row">
                            <div class="info-label">ระดับชั้น</div>
                            <div class="info-value"><?php echo htmlspecialchars($achievement['class']); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($achievement['year']): ?>
                        <div class="info-row">
                            <div class="info-label">ปีการศึกษา</div>
                            <div class="info-value"><?php echo htmlspecialchars($achievement['year']); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($achievement['date_achieved']): ?>
                        <div class="info-row">
                            <div class="info-label">วันที่ได้รับ</div>
                            <div class="info-value">
                                <?php echo date('d/m/Y', strtotime($achievement['date_achieved'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($achievement['certificate_path']): ?>
                    <div class="text-center mt-4">
                        <a href="../<?php echo htmlspecialchars($achievement['certificate_path']); ?>" 
                           target="_blank" class="certificate-btn">
                            <i class="fas fa-certificate"></i>
                            ดูใบประกาศเกียรติคุณ
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($related)): ?>
                <div class="content-section">
                    <h4 class="mb-3">รางวัลที่เกี่ยวข้อง</h4>
                    <div class="related-grid">
                        <?php foreach ($related as $rel): ?>
                        <a href="view.php?id=<?php echo $rel['id']; ?>" class="related-card">
                            <?php if ($rel['image_path']): ?>
                            <img src="../<?php echo htmlspecialchars($rel['image_path']); ?>" 
                                 class="related-image"
                                 alt="<?php echo htmlspecialchars($rel['student_name']); ?>">
                            <?php else: ?>
                            <div class="related-image d-flex align-items-center justify-content-center">
                                <i class="fas <?php echo getCategoryInfo($rel['category'])['icon']; ?>" 
                                   style="font-size: 3rem; color: #dee2e6;"></i>
                            </div>
                            <?php endif; ?>
                            <div class="related-body">
                                <div class="related-title">
                                    <?php echo mb_substr(htmlspecialchars($rel['title']), 0, 50); ?>...
                                </div>
                                <div class="related-student">
                                    <?php echo htmlspecialchars($rel['student_name']); ?>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="text-center my-4">
            <a href="index.php?category=<?php echo $achievement['category']; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> กลับไปหอเกียรติยศ
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function viewImage(src) {
        // สร้าง modal แสดงรูปภาพขนาดใหญ่
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); display: flex; align-items: center; justify-content: center; z-index: 9999; cursor: pointer;';
        modal.innerHTML = `<img src="${src}" style="max-width: 90%; max-height: 90%; border-radius: 10px;">`;
        modal.onclick = () => document.body.removeChild(modal);
        document.body.appendChild(modal);
    }
    </script>
</body>
</html>

<?php
$conn->close();
?>
