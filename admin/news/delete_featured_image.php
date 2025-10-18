<?php
/**
 * Delete Featured Image
 * Remove featured image from a news article
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Check if news ID is provided
if (!isset($_POST['news_id']) || empty($_POST['news_id'])) {
    $_SESSION['error_message'] = "ไม่พบรหัสข่าวที่ต้องการแก้ไข";
    header("Location: index.php");
    exit;
}

$news_id = (int)$_POST['news_id'];

// Get news details to find the featured image path
$news = getNewsById($news_id, $conn);
if (!$news) {
    $_SESSION['error_message'] = "ไม่พบข่าวที่ต้องการแก้ไข";
    header("Location: index.php");
    exit;
}

// Check if there is a featured image
if (empty($news['featured_image'])) {
    $_SESSION['error_message'] = "ไม่พบรูปภาพหลักที่ต้องการลบ";
    header("Location: edit_new.php?id=$news_id");
    exit;
}

// Delete the physical file
$file_path = '../../' . $news['featured_image'];
if (file_exists($file_path)) {
    @unlink($file_path);
}

// Update the database to remove the featured image reference
$stmt = $conn->prepare("UPDATE news SET featured_image = NULL WHERE id = ?");
$stmt->bind_param('i', $news_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "ลบรูปภาพหลักเรียบร้อยแล้ว";
} else {
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบรูปภาพหลัก: " . $conn->error;
}

// Redirect back to the edit page
header("Location: edit_new.php?id=$news_id");
exit;
?>
