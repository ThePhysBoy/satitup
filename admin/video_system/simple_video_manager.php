<?php
session_start();
require_once '../includes/auth_functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "satitup";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Auto-create tables if not exists
$create_categories = "CREATE TABLE IF NOT EXISTS `video_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$conn->query($create_categories);

$create_videos = "CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `youtube_id` varchar(50) NOT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `event_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `featured` (`featured`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$conn->query($create_videos);

// Add youtube_url column if not exists
$check_column = $conn->query("SHOW COLUMNS FROM videos LIKE 'youtube_url'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE videos ADD COLUMN youtube_url VARCHAR(255) DEFAULT NULL AFTER youtube_id");
}

// Check and create default category
$check_cat = $conn->query("SELECT COUNT(*) as count FROM video_categories");
$row = $check_cat->fetch_assoc();
if ($row['count'] == 0) {
    $conn->query("INSERT INTO video_categories (name, slug) VALUES ('ทั่วไป', 'general')");
}

// Get YouTube ID from URL
function getYoutubeId($url) {
    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

// Handle form submissions
$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM videos WHERE id = $id")) {
        $message = "ลบวิดีโอสำเร็จ!";
    } else {
        $error = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $youtube_url = $_POST['youtube_url'];
    $description = $conn->real_escape_string($_POST['description']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    
    // Extract YouTube ID
    $youtube_id = getYoutubeId($youtube_url);
    
    if (empty($title) || empty($youtube_url)) {
        $error = "กรุณากรอกชื่อและลิงก์วิดีโอ!";
    } elseif (empty($youtube_id)) {
        $error = "ลิงก์ YouTube ไม่ถูกต้อง!";
    } else {
        // If setting as featured, unset others
        if ($featured) {
            $conn->query("UPDATE videos SET featured = 0");
        }
        
        if ($video_id > 0) {
            // Update existing video
            $sql = "UPDATE videos SET 
                    title = '$title',
                    description = '$description',
                    youtube_url = '$youtube_url',
                    youtube_id = '$youtube_id',
                    featured = $featured,
                    updated_at = NOW()
                    WHERE id = $video_id";
            
            if ($conn->query($sql)) {
                $message = "แก้ไขวิดีโอสำเร็จ!";
            } else {
                $error = "เกิดข้อผิดพลาด: " . $conn->error;
            }
        } else {
            // Add new video
            $sql = "INSERT INTO videos (title, description, youtube_url, youtube_id, featured, category_id) 
                    VALUES ('$title', '$description', '$youtube_url', '$youtube_id', $featured, 1)";
            
            if ($conn->query($sql)) {
                $message = "เพิ่มวิดีโอสำเร็จ!";
            } else {
                $error = "เกิดข้อผิดพลาด: " . $conn->error;
            }
        }
    }
}

// Get video for editing
$edit_video = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM videos WHERE id = $id");
    $edit_video = $result->fetch_assoc();
}

// Get all videos
$videos = $conn->query("SELECT * FROM videos ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการวิดีโอ - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Kanit', sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 0;
        }
        .navbar-custom .navbar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .main-container {
            margin-top: 30px;
            margin-bottom: 50px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-size: 1.2rem;
            font-weight: 500;
        }
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .video-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }
        .video-table thead {
            background: #f8f9fa;
        }
        .video-table th {
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
            font-weight: 600;
        }
        .video-table td {
            padding: 15px;
            vertical-align: middle;
        }
        .video-thumbnail {
            width: 120px;
            height: 68px;
            object-fit: cover;
            border-radius: 8px;
        }
        .badge-featured {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-action {
            padding: 5px 12px;
            border-radius: 8px;
            border: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-edit {
            background: #28a745;
            color: white;
        }
        .btn-edit:hover {
            background: #218838;
            transform: scale(1.05);
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background: #c82333;
            transform: scale(1.05);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .youtube-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            display: none;
        }
        .youtube-preview.show {
            display: block;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-custom">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-video"></i> ระบบจัดการวิดีโอ
            </span>
            <div>
                <a href="../news/dashboard.php" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-<?php echo $edit_video ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo $edit_video ? 'แก้ไขวิดีโอ' : 'เพิ่มวิดีโอใหม่'; ?>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="">
                    <?php if ($edit_video): ?>
                        <input type="hidden" name="video_id" value="<?php echo $edit_video['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-heading"></i> ชื่อวิดีโอที่จะแสดง
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   class="form-control" 
                                   placeholder="เช่น กิจกรรมวันวิทยาศาสตร์ ประจำปี 2567"
                                   value="<?php echo $edit_video ? htmlspecialchars($edit_video['title']) : ''; ?>"
                                   required>
                            <small class="text-muted">ชื่อนี้จะแสดงบนเว็บไซต์</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="fab fa-youtube"></i> ลิงก์ YouTube
                                <span class="text-danger">*</span>
                            </label>
                            <input type="url" 
                                   name="youtube_url" 
                                   id="youtube_url"
                                   class="form-control" 
                                   placeholder="https://www.youtube.com/watch?v=xxxxx หรือ https://youtu.be/xxxxx"
                                   value="<?php echo $edit_video ? htmlspecialchars($edit_video['youtube_url']) : ''; ?>"
                                   required>
                            <small class="text-muted">คัดลอกลิงก์มาจาก YouTube โดยตรง</small>
                            
                            <div id="youtube-preview" class="youtube-preview">
                                <strong>ตัวอย่าง:</strong>
                                <div id="preview-content"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left"></i> คำอธิบายแบบย่อ
                            </label>
                            <textarea name="description" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="อธิบายเนื้อหาวิดีโอแบบสั้นๆ (ไม่บังคับ)"><?php echo $edit_video ? htmlspecialchars($edit_video['description']) : ''; ?></textarea>
                            <small class="text-muted">ใส่รายละเอียดสั้นๆ เพื่อให้ผู้ชมทราบเนื้อหา</small>
                        </div>
                        
                        <div class="col-md-12 mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="featured" 
                                       id="featured"
                                       <?php echo ($edit_video && $edit_video['featured']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="featured">
                                    <i class="fas fa-star"></i> ตั้งเป็นวิดีโอแนะนำ (จะแสดงเด่นในหน้าแรก)
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-custom">
                                <i class="fas fa-save"></i> 
                                <?php echo $edit_video ? 'บันทึกการแก้ไข' : 'เพิ่มวิดีโอ'; ?>
                            </button>
                            <?php if ($edit_video): ?>
                                <a href="simple_video_manager.php" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Videos List -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> รายการวิดีโอทั้งหมด
                <span class="badge bg-white text-dark ms-2">
                    <?php echo $videos->num_rows; ?> วิดีโอ
                </span>
            </div>
            <div class="card-body p-0">
                <?php if ($videos->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table video-table mb-0">
                            <thead>
                                <tr>
                                    <th width="150">ภาพตัวอย่าง</th>
                                    <th>ชื่อวิดีโอ</th>
                                    <th>คำอธิบาย</th>
                                    <th width="100">การดู</th>
                                    <th width="100">สถานะ</th>
                                    <th width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($video = $videos->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $thumbnail = "https://img.youtube.com/vi/{$video['youtube_id']}/mqdefault.jpg";
                                            ?>
                                            <img src="<?php echo $thumbnail; ?>" 
                                                 alt="<?php echo htmlspecialchars($video['title']); ?>" 
                                                 class="video-thumbnail"
                                                 onerror="this.src='https://via.placeholder.com/120x68?text=No+Image'">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($video['title']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> 
                                                <?php echo date('d/m/Y', strtotime($video['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                            $desc = htmlspecialchars($video['description']);
                                            echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">
                                                <?php echo number_format($video['views']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($video['featured']): ?>
                                                <span class="badge-featured">
                                                    <i class="fas fa-star"></i> แนะนำ
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?edit=<?php echo $video['id']; ?>" 
                                                   class="btn btn-action btn-edit">
                                                    <i class="fas fa-edit"></i> แก้ไข
                                                </a>
                                                <a href="?delete=<?php echo $video['id']; ?>" 
                                                   class="btn btn-action btn-delete"
                                                   onclick="return confirm('ต้องการลบวิดีโอนี้?')">
                                                    <i class="fas fa-trash"></i> ลบ
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-video-slash"></i>
                        <h4>ยังไม่มีวิดีโอ</h4>
                        <p>เริ่มเพิ่มวิดีโอแรกของคุณด้านบน</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card">
            <div class="card-body text-center">
                <h5>ลิงก์ที่เกี่ยวข้อง</h5>
                <div class="mt-3">
                    <a href="../../video_quick_links.php" target="_blank" class="btn btn-outline-primary me-2">
                        <i class="fas fa-external-link-alt"></i> ดูหน้าแสดงวิดีโอ
                    </a>
                    <a href="../../video_system/all_videos.php" target="_blank" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-th"></i> ดูวิดีโอทั้งหมด
                    </a>
                    <a href="../../test_video_system.php" target="_blank" class="btn btn-outline-info">
                        <i class="fas fa-vial"></i> ทดสอบระบบ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // YouTube URL Preview
        document.getElementById('youtube_url')?.addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('youtube-preview');
            const content = document.getElementById('preview-content');
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                // Extract video ID
                let videoId = '';
                const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/);
                
                if (match) {
                    videoId = match[1];
                    preview.classList.add('show');
                    content.innerHTML = `
                        <img src="https://img.youtube.com/vi/${videoId}/mqdefault.jpg" 
                             style="max-width: 200px; border-radius: 8px;" 
                             onerror="this.src='https://via.placeholder.com/200x112?text=Invalid+Video'">
                        <br>
                        <small class="text-muted">Video ID: ${videoId}</small>
                    `;
                } else {
                    preview.classList.remove('show');
                }
            } else {
                preview.classList.remove('show');
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
