<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header('Location: ../index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM steering_committee WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        header('Location: index.php?success=' . urlencode('ลบข้อมูลเรียบร้อยแล้ว'));
    } else {
        header('Location: index.php?error=' . urlencode('ไม่สามารถลบข้อมูลได้'));
    }
} else {
    header('Location: index.php');
}
exit;
?>
