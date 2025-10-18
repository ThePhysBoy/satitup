<?php
require_once '../includes/auth_functions.php';
require_once '../../video_system/includes/video_functions.php';

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Get videos for display
$videos = getLatestVideos(999); // Fetch all videos

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $video_id = (int)$_GET['id'];
    global $video_conn;
    $stmt = $video_conn->prepare("DELETE FROM videos WHERE id = ?");
    $stmt->bind_param("i", $video_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "วิดีโอถูกลบเรียบร้อยแล้ว!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบวิดีโอ: " . $video_conn->error;
        $_SESSION['msg_type'] = "danger";
    }
    header('Location: index.php');
    exit;
}

// Handle featured action
if (isset($_GET['action']) && $_GET['action'] == 'feature' && isset($_GET['id'])) {
    $video_id = (int)$_GET['id'];
    global $video_conn;
    // Unset all other featured videos first
    $video_conn->query("UPDATE videos SET is_featured = FALSE");
    // Set the selected video as featured
    $stmt = $video_conn->prepare("UPDATE videos SET is_featured = TRUE WHERE id = ?");
    $stmt->bind_param("i", $video_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "ตั้งเป็นวิดีโอแนะนำเรียบร้อยแล้ว!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการตั้งค่าวิดีโอแนะนำ: " . $video_conn->error;
        $_SESSION['msg_type'] = "danger";
    }
    header('Location: index.php');
    exit;
}

// Handle unfeature action
if (isset($_GET['action']) && $_GET['action'] == 'unfeature' && isset($_GET['id'])) {
    $video_id = (int)$_GET['id'];
    global $video_conn;
    $stmt = $video_conn->prepare("UPDATE videos SET is_featured = FALSE WHERE id = ?");
    $stmt->bind_param("i", $video_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "ยกเลิกการตั้งเป็นวิดีโอแนะนำเรียบร้อยแล้ว!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการยกเลิกวิดีโอแนะนำ: " . $video_conn->error;
        $_SESSION['msg_type'] = "danger";
    }
    header('Location: index.php');
    exit;
}

$page_title = "จัดการวิดีโอ";

// Start output buffering
ob_start();
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">จัดการวิดีโอ</h1>

    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php
    unset($_SESSION['message']);
    unset($_SESSION['msg_type']);
    ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">รายการวิดีโอทั้งหมด</h6>
            <a href="add_video.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle fa-sm"></i> เพิ่มวิดีโอใหม่
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="videoTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="120">รูปภาพ</th>
                            <th>ชื่อวิดีโอ</th>
                            <th>หมวดหมู่</th>
                            <th width="100">วิดีโอแนะนำ</th>
                            <th width="120">วันที่</th>
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($videos)): ?>
                            <?php $i = 1; foreach ($videos as $video): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <img src="<?php echo getYoutubeThumbnail($video['youtube_url']); ?>" alt="Thumbnail" class="img-thumbnail" width="100">
                                    </td>
                                    <td><?php echo htmlspecialchars($video['title']); ?></td>
                                    <td>
                                        <?php 
                                        $video_detail = getVideoById($video['id']);
                                        echo htmlspecialchars($video_detail['category_name'] ?? 'ไม่มีหมวดหมู่'); 
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($video['is_featured']): ?>
                                            <span class="badge badge-success">ใช่</span>
                                            <a href="index.php?action=unfeature&id=<?php echo $video['id']; ?>" class="btn btn-warning btn-sm mt-1">ยกเลิก</a>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">ไม่</span>
                                            <a href="index.php?action=feature&id=<?php echo $video['id']; ?>" class="btn btn-info btn-sm mt-1">ตั้งเป็นแนะนำ</a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($video['upload_date'])); ?></td>
                                    <td>
                                        <a href="edit_video.php?id=<?php echo $video['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit fa-sm"></i> แก้ไข
                                        </a>
                                        <a href="index.php?action=delete&id=<?php echo $video['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบวิดีโอนี้?');">
                                            <i class="fas fa-trash-alt fa-sm"></i> ลบ
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">ไม่พบข้อมูลวิดีโอ</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#videoTable').DataTable({
        "language": {
            "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
            "zeroRecords": "ไม่พบข้อมูล",
            "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
            "infoEmpty": "ไม่มีข้อมูล",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
            "search": "ค้นหา:",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            }
        },
        "order": [[ 5, "desc" ]] // เรียงตามวันที่ล่าสุด
    });
});
</script>

<?php
$content = ob_get_clean();
include 'template.php';
?>