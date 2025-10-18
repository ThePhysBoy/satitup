<?php
session_start();
require_once '../includes/db_config.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ตรวจสอบว่ามี ID ส่งมาหรือไม่
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ไม่พบเอกสารที่ต้องการลบ";
    header("Location: index.php");
    exit();
}

$document_id = intval($_GET['id']);

// ดึงข้อมูลเอกสารเพื่อลบไฟล์
$sql = "SELECT file_path FROM official_documents WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();

if (!$document) {
    $_SESSION['error'] = "ไม่พบเอกสารที่ต้องการลบ";
    header("Location: index.php");
    exit();
}

// ลบไฟล์ PDF (ถ้ามี)
if ($document['file_path'] && file_exists('../../' . $document['file_path'])) {
    unlink('../../' . $document['file_path']);
}

// ลบข้อมูลจากฐานข้อมูล (logs จะถูกลบอัตโนมัติผ่าน ON DELETE CASCADE)
$sql = "DELETE FROM official_documents WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $document_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "ลบเอกสารสำเร็จ";
} else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบเอกสาร: " . $conn->error;
}

header("Location: index.php");
exit();
?>
