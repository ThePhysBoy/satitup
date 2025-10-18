<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireNewsAccess();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);

// Remove file if exists
$stmt = $conn->prepare("SELECT file_path FROM announcements WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    if (!empty($row['file_path'])) {
        $path = '../../' . $row['file_path'];
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}

// Delete row
$d = $conn->prepare("DELETE FROM announcements WHERE id=?");
$d->bind_param('i', $id);
$d->execute();

$_SESSION['success_message'] = 'ลบประกาศเรียบร้อย';
header('Location: dashboard.php');
exit;
?>


