<?php
/**
 * Delete Featured Status
 * Remove featured status from a news article
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ไม่พบรหัสข่าวที่ต้องการแก้ไข";
    header("Location: index.php");
    exit;
}

$news_id = (int)$_GET['id'];

// Update the news to remove featured status
$stmt = $conn->prepare("UPDATE news SET is_featured = 0 WHERE id = ?");
$stmt->bind_param('i', $news_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "ยกเลิกสถานะข่าวเด่นเรียบร้อยแล้ว";
} else {
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการยกเลิกสถานะข่าวเด่น: " . $conn->error;
}

// Redirect back to the previous page or index
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header("Location: $redirect");
exit;
?>