<?php
/**
 * Preview Slideshow Item
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to be logged in as admin
requireAdmin();

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Get slideshow item
$stmt = $conn->prepare("SELECT * FROM slideshow WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if slideshow item exists
if ($result->num_rows !== 1) {
    header("Location: index.php");
    exit;
}

$slide = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูตัวอย่างสไลด์ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .slide-preview {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 30px rgba(0,0,0,0.15);
        }
        
        .slide-preview img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .carousel-caption {
            background: rgba(0,0,0,0.5);
            border-radius: 10px;
            padding: 20px;
            bottom: 50px;
            max-width: 80%;
            margin: 0 auto;
            left: 10%;
            right: 10%;
        }
        
        .slide-info {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 5px 30px rgba(0,0,0,0.15);
        }
        
        .info-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: linear-gradient(135deg, #3a5fc8 0%, #1a3ba5 100%);
        }
        
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1 class="h3 mb-0"><i class="fas fa-eye me-2 text-primary"></i>ตัวอย่างสไลด์</h1>
            <a href="index.php" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i> กลับไปยังรายการสไลด์
            </a>
        </div>
        
        <div class="slide-preview">
            <img src="../../<?php echo htmlspecialchars($slide['image_path']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" class="img-fluid">
            
            <div class="carousel-caption d-none d-md-block">
                <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">
                    <?php echo htmlspecialchars($slide['title']); ?>
                </h1>
                
                <?php if (!empty($slide['description'])): ?>
                    <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        <?php echo htmlspecialchars($slide['description']); ?>
                    </p>
                <?php endif; ?>
                
                <?php if (!empty($slide['link'])): ?>
                    <a href="#" class="btn btn-primary btn-lg rounded-pill px-4 animate__animated animate__fadeInUp animate__delay-2s">
                        เรียนรู้เพิ่มเติม <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="slide-info">
            <h2 class="h4 mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>ข้อมูลสไลด์</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <h5>หัวข้อ</h5>
                        <p class="mb-0"><?php echo htmlspecialchars($slide['title']); ?></p>
                    </div>
                    
                    <div class="info-item">
                        <h5>คำอธิบาย</h5>
                        <p class="mb-0"><?php echo empty($slide['description']) ? 'ไม่มีคำอธิบาย' : htmlspecialchars($slide['description']); ?></p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-item">
                        <h5>ลิงก์</h5>
                        <p class="mb-0">
                            <?php if (empty($slide['link'])): ?>
                                ไม่มีลิงก์
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($slide['link']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($slide['link']); ?>
                                    <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="info-item">
                        <h5>สถานะ</h5>
                        <p class="mb-0">
                            <?php if ($slide['active']): ?>
                                <span class="badge bg-success">แสดง</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">ซ่อน</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="info-item">
                        <h5>ลำดับการแสดงผล</h5>
                        <p class="mb-0"><?php echo $slide['display_order']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <a href="edit.php?id=<?php echo $slide['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> แก้ไขสไลด์นี้
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
