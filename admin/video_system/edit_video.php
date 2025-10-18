<?php
require_once '../includes/auth_functions.php';
require_once '../../video_system/includes/video_functions.php';

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$video_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$video = getVideoById($video_id);

if (!$video) {
    $_SESSION['message'] = "ไม่พบข้อมูลวิดีโอ!";
    $_SESSION['msg_type'] = "danger";
    header('Location: index.php');
    exit;
}

$categories = getAllCategories();
$errors = [];

// Pre-fill form with existing data
$title = $video['title'];
$description = $video['description'];
$youtube_url = $video['youtube_url'];
$category_id = $video['category_id'];
$is_featured = $video['is_featured'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $youtube_url = trim($_POST['youtube_url']);
    $category_id = (int)$_POST['category_id'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($title)) { array_push($errors, "กรุณาระบุชื่อวิดีโอ"); }
    if (empty($youtube_url)) { array_push($errors, "กรุณาระบุลิงก์ YouTube"); }
    if (empty($category_id)) { array_push($errors, "กรุณาเลือกหมวดหมู่"); }

    if (count($errors) == 0) {
        global $video_conn;
        $thumbnail_url = getYoutubeThumbnail($youtube_url);

        // If setting as featured, unfeature all others first
        if ($is_featured) {
            $video_conn->query("UPDATE videos SET is_featured = FALSE");
        }

        $stmt = $video_conn->prepare("UPDATE videos SET title = ?, description = ?, youtube_url = ?, thumbnail_url = ?, category_id = ?, is_featured = ? WHERE id = ?");
        $stmt->bind_param("ssssiii", $title, $description, $youtube_url, $thumbnail_url, $category_id, $is_featured, $video_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "แก้ไขวิดีโอเรียบร้อยแล้ว!";
            $_SESSION['msg_type'] = "success";
            header('Location: index.php');
            exit;
        } else {
            array_push($errors, "เกิดข้อผิดพลาดในการแก้ไขวิดีโอ: " . $video_conn->error);
        }
    }
}

$page_title = "แก้ไขวิดีโอ";

// Start output buffering
ob_start();
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">แก้ไขวิดีโอ</h1>

    <?php if (count($errors) > 0): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลวิดีโอ</h6>
                </div>
                <div class="card-body">
                    <form action="edit_video.php?id=<?php echo $video_id; ?>" method="POST">
                        <div class="form-group">
                            <label for="title">ชื่อวิดีโอ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="youtube_url">ลิงก์ YouTube <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($youtube_url); ?>" placeholder="เช่น https://www.youtube.com/watch?v=VIDEO_ID" required>
                            <small class="form-text text-muted">รองรับลิงก์ในรูปแบบ https://www.youtube.com/watch?v=VIDEO_ID หรือ https://youtu.be/VIDEO_ID</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">คำอธิบายวิดีโอ</label>
                            <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">หมวดหมู่ <span class="text-danger">*</span></label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">เลือกหมวดหมู่</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" <?php echo ($is_featured) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="is_featured">ตั้งเป็นวิดีโอแนะนำ (Featured)</label>
                                <small class="form-text text-muted">หากเลือกตัวเลือกนี้ วิดีโอนี้จะแสดงเป็นวิดีโอแนะนำในหน้าแรก และจะยกเลิกวิดีโอแนะนำอื่นๆ</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> บันทึกการแก้ไข
                            </button>
                            <a href="index.php" class="btn btn-secondary ml-2">ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ตัวอย่างวิดีโอ</h6>
                </div>
                <div class="card-body">
                    <div class="embed-responsive embed-responsive-16by9 mb-3">
                        <iframe class="embed-responsive-item" src="<?php echo getYoutubeEmbedUrl($youtube_url); ?>" allowfullscreen></iframe>
                    </div>
                    <div class="text-center">
                        <p class="mb-0"><strong>ภาพตัวอย่าง:</strong></p>
                        <img src="<?php echo getYoutubeThumbnail($youtube_url); ?>" class="img-fluid mt-2 border" alt="Thumbnail">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // อัพเดตตัวอย่างวิดีโอเมื่อเปลี่ยนลิงก์ YouTube
    $('#youtube_url').on('change', function() {
        var url = $(this).val();
        if (url) {
            var videoId = getYoutubeVideoId(url);
            if (videoId) {
                var embedUrl = 'https://www.youtube.com/embed/' + videoId;
                var thumbnailUrl = 'https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg';
                
                // อัพเดตตัวอย่างวิดีโอ
                $('.embed-responsive-item').attr('src', embedUrl);
                $('.card-body img').attr('src', thumbnailUrl);
            }
        }
    });

    // ฟังก์ชันแยก Video ID จากลิงก์ YouTube
    function getYoutubeVideoId(url) {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        var match = url.match(regExp);
        if (match && match[2].length == 11) {
            return match[2];
        } else {
            return null;
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include 'template.php';
?>