<?php
/**
 * News Delete
 * Delete a news article
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ไม่พบรหัสข่าวที่ต้องการลบ";
    header("Location: index.php");
    exit;
}

$news_id = (int)$_GET['id'];

// Get news details before deletion (for image cleanup)
$news = getNewsById($news_id, $conn);
if (!$news) {
    $_SESSION['error_message'] = "ไม่พบข่าวที่ต้องการลบ";
    header("Location: index.php");
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Delete news images
    $stmt = $conn->prepare("SELECT image_path FROM news_images WHERE news_id = ?");
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Delete physical file
        $file_path = '../../' . $row['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete image records
    $stmt = $conn->prepare("DELETE FROM news_images WHERE news_id = ?");
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    
    // Delete featured image if exists
    if (!empty($news['featured_image'])) {
        $featured_image_path = '../../' . $news['featured_image'];
        if (file_exists($featured_image_path)) {
            unlink($featured_image_path);
        }
    }
    
    // Delete gallery images if exists
    if (!empty($news['gallery_image_1'])) {
        $gallery_image_path = '../../' . $news['gallery_image_1'];
        if (file_exists($gallery_image_path)) {
            unlink($gallery_image_path);
        }
    }
    
    if (!empty($news['gallery_image_2'])) {
        $gallery_image_path = '../../' . $news['gallery_image_2'];
        if (file_exists($gallery_image_path)) {
            unlink($gallery_image_path);
        }
    }
    
    if (!empty($news['gallery_image_3'])) {
        $gallery_image_path = '../../' . $news['gallery_image_3'];
        if (file_exists($gallery_image_path)) {
            unlink($gallery_image_path);
        }
    }
    
    // Delete news record
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['success_message'] = "ลบข่าวเรียบร้อยแล้ว";
    header("Location: index.php");
    exit;
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    error_log("Error deleting news: " . $e->getMessage());
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบข่าว: " . $e->getMessage();
    header("Location: index.php");
    exit;
}
?>
