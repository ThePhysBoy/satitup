<?php
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireLogin();
if (!isAdmin() && !isPrOfficer()) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['image_id'])) {
	$image_id = (int)$_POST['image_id'];
	$stmt = $conn->prepare("SELECT image_path, news_id FROM news_images WHERE id=?");
	$stmt->bind_param('i', $image_id);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($res->num_rows===1) {
		$row = $res->fetch_assoc();
		$path = '../../' . $row['image_path'];
		if (file_exists($path)) { @unlink($path); }
		$del = $conn->prepare("DELETE FROM news_images WHERE id=?");
		$del->bind_param('i', $image_id);
		$del->execute();
		header('Location: edit.php?id='.(int)$row['news_id'].'&success=1'); exit;
	}
}
header('Location: index.php');
