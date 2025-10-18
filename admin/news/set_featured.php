<?php
/**
 * Set Featured Status
 * Set a news article as featured
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

// First, check if the news article exists and is published
$stmt = $conn->prepare("SELECT id, status FROM news WHERE id = ?");
$stmt->bind_param('i', $news_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "ไม่พบข่าวที่ต้องการตั้งเป็นข่าวเด่น";
    header("Location: index.php");
    exit;
}

$news = $result->fetch_assoc();

if ($news['status'] !== 'published') {
    $_SESSION['error_message'] = "ไม่สามารถตั้งเป็นข่าวเด่นได้ เนื่องจากข่าวยังไม่ได้เผยแพร่";
    header("Location: index.php");
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // First, reset all featured news
    $stmt = $conn->prepare("UPDATE news SET is_featured = 0 WHERE is_featured = 1");
    $stmt->execute();
    
    // Then, set the selected news as featured
    $stmt = $conn->prepare("UPDATE news SET is_featured = 1 WHERE id = ?");
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['success_message'] = "ตั้งเป็นข่าวเด่นเรียบร้อยแล้ว";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    error_log("Error setting featured news: " . $e->getMessage());
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการตั้งข่าวเด่น: " . $e->getMessage();
}

// Redirect back to the previous page or index
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header("Location: $redirect");
exit;
?>
