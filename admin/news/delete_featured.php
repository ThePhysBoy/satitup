<?php
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireLogin();
if (!isAdmin() && !isPrOfficer()) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['news_id'])) {
	$news_id = (int)$_POST['news_id'];
	$stmt = $conn->prepare("SELECT featured_image FROM news WHERE id=?");
	$stmt->bind_param('i', $news_id);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($res->num_rows===1) {
		$row = $res->fetch_assoc();
		if (!empty($row['featured_image'])) {
			$path = '../../' . $row['featured_image'];
			if (file_exists($path)) { @unlink($path); }
		}
		$u = $conn->prepare("UPDATE news SET featured_image=NULL WHERE id=?");
		$u->bind_param('i', $news_id);
		$u->execute();
		header('Location: edit.php?id='.$news_id.'&success=1'); exit;
	}
}
header('Location: index.php');
