<?php
session_start();

// Temporarily bypass auth check for testing
// Comment out these lines to enable auth check
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_type'] = 'admin';

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
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$conn->query($create_videos);

// Add youtube_url column if not exists
$check_column = $conn->query("SHOW COLUMNS FROM videos LIKE 'youtube_url'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE videos ADD COLUMN youtube_url VARCHAR(255) DEFAULT NULL AFTER youtube_id");
}

// Add active column if not exists
$check_active = $conn->query("SHOW COLUMNS FROM videos LIKE 'active'");
if ($check_active->num_rows == 0) {
    $conn->query("ALTER TABLE videos ADD COLUMN active TINYINT(1) DEFAULT 1");
}

// Add event_date column if not exists
$check_event = $conn->query("SHOW COLUMNS FROM videos LIKE 'event_date'");
if ($check_event->num_rows == 0) {
    $conn->query("ALTER TABLE videos ADD COLUMN event_date DATE DEFAULT NULL");
}

// Add updated_at column if not exists
$check_updated = $conn->query("SHOW COLUMNS FROM videos LIKE 'updated_at'");
if ($check_updated->num_rows == 0) {
    $conn->query("ALTER TABLE videos ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
}

// Check if categories exist, if not add default
$cat_check = $conn->query("SELECT COUNT(*) as count FROM video_categories");
$cat_count = $cat_check->fetch_assoc()['count'];

if ($cat_count == 0) {
    $conn->query("INSERT INTO video_categories (name, slug) VALUES ('ทั่วไป', 'general')");
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'add' || $action == 'edit') {
            $title = $conn->real_escape_string($_POST['title']);
            $youtube_url = $conn->real_escape_string($_POST['youtube_url']);
            $description = $conn->real_escape_string($_POST['description']);
            $featured = isset($_POST['featured']) ? 1 : 0;
            $category_id = 1; // Default category
            
            // Extract YouTube ID from URL
            $youtube_id = '';
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $match)) {
                $youtube_id = $match[1];
            } else if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $youtube_url)) {
                $youtube_id = $youtube_url;
                $youtube_url = 'https://www.youtube.com/watch?v=' . $youtube_id;
            }
            
            if ($action == 'add') {
                $sql = "INSERT INTO videos (title, youtube_id, youtube_url, description, category_id, featured, active) 
                        VALUES ('$title', '$youtube_id', '$youtube_url', '$description', $category_id, $featured, 1)";
                
                if ($conn->query($sql)) {
                    $success = "วิดีโอถูกเพิ่มเรียบร้อยแล้ว";
                } else {
                    $error = "Error: " . $conn->error;
                }
            } else if ($action == 'edit' && isset($_POST['video_id'])) {
                $video_id = intval($_POST['video_id']);
                $sql = "UPDATE videos 
                        SET title='$title', youtube_id='$youtube_id', youtube_url='$youtube_url', 
                            description='$description', featured=$featured 
                        WHERE id=$video_id";
                
                if ($conn->query($sql)) {
                    $success = "วิดีโอถูกอัปเดตเรียบร้อยแล้ว";
                } else {
                    $error = "Error: " . $conn->error;
                }
            }
        } else if ($action == 'delete' && isset($_POST['video_id'])) {
            $video_id = intval($_POST['video_id']);
            $sql = "DELETE FROM videos WHERE id=$video_id";
            
            if ($conn->query($sql)) {
                $success = "วิดีโอถูกลบเรียบร้อยแล้ว";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}

// Get video for editing
$edit_video = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM videos WHERE id=$edit_id");
    if ($result && $result->num_rows > 0) {
        $edit_video = $result->fetch_assoc();
    }
}

// Get all videos
$videos = $conn->query("SELECT * FROM videos ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการวิดีโอ (ง่าย) - โรงเรียนสาธิต</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .container-fluid {
            padding: 20px;
        }
        .video-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .video-thumbnail {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-action {
            padding: 5px 10px;
            font-size: 0.875rem;
        }
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .featured-badge {
            background: #ffc107;
            color: #000;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.75rem;
        }
        #preview-thumbnail {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stats-card {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-video"></i> จัดการวิดีโอ (ง่าย)</h1>
                    <p class="mb-0">เพิ่ม แก้ไข และจัดการวิดีโอ YouTube ได้อย่างง่ายดาย</p>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">
                                    <?php 
                                    $total = $conn->query("SELECT COUNT(*) as c FROM videos")->fetch_assoc()['c'];
                                    echo $total;
                                    ?>
                                </div>
                                <div>วิดีโอทั้งหมด</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number">
                                    <?php 
                                    $featured = $conn->query("SELECT COUNT(*) as c FROM videos WHERE featured=1")->fetch_assoc()['c'];
                                    echo $featured;
                                    ?>
                                </div>
                                <div>วิดีโอแนะนำ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="form-section">
            <h3><?php echo $edit_video ? 'แก้ไขวิดีโอ' : 'เพิ่มวิดีโอใหม่'; ?></h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $edit_video ? 'edit' : 'add'; ?>">
                <?php if ($edit_video): ?>
                <input type="hidden" name="video_id" value="<?php echo $edit_video['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">ชื่อวิดีโอ *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo $edit_video ? htmlspecialchars($edit_video['title']) : ''; ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="youtube_url" class="form-label">ลิงค์ YouTube *</label>
                            <input type="text" class="form-control" id="youtube_url" name="youtube_url" 
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   value="<?php echo $edit_video ? htmlspecialchars($edit_video['youtube_url']) : ''; ?>"
                                   required>
                            <small class="text-muted">คัดลอกลิงก์มาจาก YouTube โดยตรง</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">คำอธิบาย</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo $edit_video ? htmlspecialchars($edit_video['description']) : ''; ?></textarea>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                   <?php echo ($edit_video && $edit_video['featured']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="featured">
                                <i class="fas fa-star text-warning"></i> ตั้งเป็นวิดีโอแนะนำ
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img id="preview-thumbnail" style="display: none;">
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $edit_video ? 'อัปเดต' : 'บันทึก'; ?>
                    </button>
                    <?php if ($edit_video): ?>
                    <a href="simple_video_manager_noauth.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> ยกเลิก
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Videos List -->
        <div class="form-section">
            <h3>รายการวิดีโอทั้งหมด</h3>
            
            <?php if ($videos && $videos->num_rows > 0): ?>
                <?php while($video = $videos->fetch_assoc()): ?>
                <div class="video-card">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://img.youtube.com/vi/<?php echo $video['youtube_id']; ?>/mqdefault.jpg" 
                                 class="video-thumbnail" alt="<?php echo htmlspecialchars($video['title']); ?>">
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">
                                <?php echo htmlspecialchars($video['title']); ?>
                                <?php if ($video['featured']): ?>
                                <span class="featured-badge"><i class="fas fa-star"></i> แนะนำ</span>
                                <?php endif; ?>
                            </h5>
                            <p class="text-muted mb-0">
                                <?php echo htmlspecialchars($video['description'] ?: 'ไม่มีคำอธิบาย'); ?>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-eye"></i> <?php echo $video['views']; ?> views |
                                <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($video['created_at'])); ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="https://www.youtube.com/watch?v=<?php echo $video['youtube_id']; ?>" 
                               target="_blank" class="btn btn-sm btn-info btn-action">
                                <i class="fas fa-external-link-alt"></i> ดู
                            </a>
                            <a href="?edit=<?php echo $video['id']; ?>" class="btn btn-sm btn-warning btn-action">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <form method="POST" action="" style="display: inline;" 
                                  onsubmit="return confirm('ต้องการลบวิดีโอนี้?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger btn-action">
                                    <i class="fas fa-trash"></i> ลบ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> ยังไม่มีวิดีโอ กรุณาเพิ่มวิดีโอใหม่
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับสู่หน้า Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview thumbnail when URL changes
        document.getElementById('youtube_url').addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('preview-thumbnail');
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                // Extract video ID
                let videoId = '';
                const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
                
                if (match) {
                    videoId = match[1];
                } else if (/^[a-zA-Z0-9_-]{11}$/.test(url)) {
                    videoId = url;
                }
                
                if (videoId) {
                    preview.src = `https://img.youtube.com/vi/${videoId}/mqdefault.jpg`;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
