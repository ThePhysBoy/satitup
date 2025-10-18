<?php
require_once '../includes/auth_functions.php';
require_once '../../video_system/includes/video_functions.php';

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$categories = getAllCategories();
$errors = [];

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

        $stmt = $video_conn->prepare("INSERT INTO videos (title, description, youtube_url, thumbnail_url, category_id, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $title, $description, $youtube_url, $thumbnail_url, $category_id, $is_featured);

        if ($stmt->execute()) {
            $_SESSION['message'] = "เพิ่มวิดีโอเรียบร้อยแล้ว!";
            $_SESSION['msg_type'] = "success";
            header('Location: index.php');
            exit;
        } else {
            array_push($errors, "เกิดข้อผิดพลาดในการเพิ่มวิดีโอ: " . $video_conn->error);
        }
    }
}

$page_title = "เพิ่มวิดีโอใหม่";

// Start output buffering
ob_start();
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">เพิ่มวิดีโอใหม่</h1>

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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ข้อมูลวิดีโอ</h6>
        </div>
        <div class="card-body">
            <form action="add_video.php" method="POST">
                <div class="form-group">
                    <label for="title">ชื่อวิดีโอ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="youtube_url">ลิงก์ YouTube <span class="text-danger">*</span></label>
                    <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($youtube_url ?? ''); ?>" placeholder="เช่น https://www.youtube.com/watch?v=VIDEO_ID" required>
                    <small class="form-text text-muted">รองรับลิงก์ในรูปแบบ https://www.youtube.com/watch?v=VIDEO_ID หรือ https://youtu.be/VIDEO_ID</small>
                </div>
                
                <div class="form-group">
                    <label for="description">คำอธิบายวิดีโอ</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category_id">หมวดหมู่ <span class="text-danger">*</span></label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">เลือกหมวดหมู่</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($category_id) && $category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" <?php echo (isset($is_featured) && $is_featured) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="is_featured">ตั้งเป็นวิดีโอแนะนำ (Featured)</label>
                        <small class="form-text text-muted">หากเลือกตัวเลือกนี้ วิดีโอนี้จะแสดงเป็นวิดีโอแนะนำในหน้าแรก และจะยกเลิกวิดีโอแนะนำอื่นๆ</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึกวิดีโอ
                    </button>
                    <a href="index.php" class="btn btn-secondary ml-2">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // แสดงตัวอย่างวิดีโอเมื่อกรอกลิงก์ YouTube
    $('#youtube_url').on('change', function() {
        var url = $(this).val();
        if (url) {
            var videoId = getYoutubeVideoId(url);
            if (videoId) {
                // ถ้าไม่มีชื่อวิดีโอ ให้ดึงชื่อจาก YouTube API (ต้องเพิ่มการเชื่อมต่อ API)
                if ($('#title').val() === '') {
                    // ในอนาคตอาจเพิ่มการดึงชื่อวิดีโอจาก YouTube API
                }
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