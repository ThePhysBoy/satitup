<?php
// Quick announcement handler: save record and upload PDF
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireNewsAccess();

// Helper to respond with redirect back to dashboard with flash
function goBack($type, $msg) {
    // Use template-compatible keys
    if ($type === 'success') {
        $_SESSION['success_message'] = $msg;
    } else {
        $_SESSION['error_message'] = $msg;
    }
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    goBack('error', 'วิธีการเรียกไม่ถูกต้อง');
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$category = $_POST['category'] ?? 'announcement';
$status = $_POST['status'] ?? 'open';
$budget = isset($_POST['budget']) && $_POST['budget'] !== '' ? floatval($_POST['budget']) : null;
$announce_date = $_POST['announce_date'] ?? null;
$department = trim($_POST['department'] ?? 'ฝ่ายพัสดุ');
$doc_type = $_POST['doc_type'] ?? null;        // สำหรับหมวดคำสั่งและประกาศ
$job_type = $_POST['job_type'] ?? null;        // สำหรับรับสมัครงาน
$salary = $_POST['salary'] ?? null;            // สำหรับรับสมัครงาน

if ($title === '') {
    goBack('error', 'กรุณากรอกหัวข้อประกาศ');
}

// Validate and upload PDF
$file_path = null;
$file_name = null;

if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    goBack('error', 'กรุณาเลือกไฟล์ PDF');
}

$file = $_FILES['pdf_file'];
$allowed = ['application/pdf'];
if (!in_array($file['type'], $allowed)) {
    goBack('error', 'รองรับเฉพาะไฟล์ PDF เท่านั้น');
}

if ($file['size'] > 10 * 1024 * 1024) { // 10MB
    goBack('error', 'ไฟล์มีขนาดเกิน 10MB');
}

$upload_dir = '../../uploads/announcements';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        goBack('error', 'ไม่สามารถสร้างโฟลเดอร์อัพโหลดได้');
    }
}

$safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
$newName = uniqid('ann_') . '_' . $safeName;
$target = $upload_dir . '/' . $newName;

if (!move_uploaded_file($file['tmp_name'], $target)) {
    goBack('error', 'อัพโหลดไฟล์ไม่สำเร็จ');
}

$file_path = 'uploads/announcements/' . $newName;
$file_name = $safeName;

// Insert to DB
$stmt = $conn->prepare("INSERT INTO announcements (title, content, category, status, budget, announce_date, department, doc_type, job_type, salary, file_path, file_name, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    goBack('error', 'เกิดข้อผิดพลาดในการเตรียมคำสั่งฐานข้อมูล');
}

$uid = $_SESSION['user_id'] ?? null;
// Types: s s s s d s s s s s s s i
$stmt->bind_param('ssssdsssssssi', $title, $content, $category, $status, $budget, $announce_date, $department, $doc_type, $job_type, $salary, $file_path, $file_name, $uid);

if ($stmt->execute()) {
    goBack('success', 'บันทึกประกาศเรียบร้อย');
} else {
    goBack('error', 'บันทึกประกาศไม่สำเร็จ: ' . $stmt->error);
}


