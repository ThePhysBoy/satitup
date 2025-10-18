<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireNewsAccess();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$category = $_POST['category'] ?? 'announcement';
$status = $_POST['status'] ?? 'open';
$budget = $_POST['budget'] !== '' ? floatval($_POST['budget']) : null;
$announce_date = $_POST['announce_date'] ?? null;
$department = trim($_POST['department'] ?? '');
$content = trim($_POST['content'] ?? '');

// Update basic fields
$stmt = $conn->prepare("UPDATE announcements SET title=?, content=?, category=?, status=?, budget=?, announce_date=?, department=? WHERE id=?");
$stmt->bind_param('ssssdssi', $title, $content, $category, $status, $budget, $announce_date, $department, $id);
$stmt->execute();

// Handle optional PDF upload
if (!empty($_FILES['pdf_file']['name']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../uploads/announcements/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($_FILES['pdf_file']['name']));
    $newName = uniqid('ann_') . '_' . $safeName;
    $target = $upload_dir . $newName;
    if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target)) {
        $file_path = 'uploads/announcements/' . $newName;
        $file_name = $safeName;
        $s2 = $conn->prepare("UPDATE announcements SET file_path=?, file_name=? WHERE id=?");
        $s2->bind_param('ssi', $file_path, $file_name, $id);
        $s2->execute();
    }
}

$_SESSION['success_message'] = 'บันทึกการแก้ไขเรียบร้อย';
header('Location: dashboard.php');
exit;
?>


