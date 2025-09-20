<?php
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';
requireLogin();
if (!isAdmin() && !isPrOfficer()) { header('Location: ../index.php'); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news = $id>0 ? getNewsById($id, $conn) : null;
if (!$news) { header('Location: index.php'); exit; }
$images = getNewsImages($news['id'], $conn);
?>
<!DOCTYPE html>
<html lang="th">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ดูข่าว - ระบบจัดการเว็บไซต์</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
	<div class="container py-4">
		<a href="index.php" class="btn btn-secondary mb-3">ย้อนกลับ</a>
		<div class="card">
			<div class="card-body">
				<h3><?php echo htmlspecialchars($news['title']); ?></h3>
				<p class="text-muted mb-2">หมวดหมู่: <?php echo htmlspecialchars($news['category_name']); ?> | โดย <?php echo htmlspecialchars($news['full_name']??$news['username']); ?></p>
				<?php if ($news['featured_image']): ?><img class="img-fluid rounded mb-3" src="../../<?php echo htmlspecialchars($news['featured_image']); ?>"><?php endif; ?>
				<div><?php echo $news['content']; ?></div>
				<?php if (count($images)>0): ?>
					<hr>
					<div class="row g-3">
						<?php foreach($images as $img): ?>
						<div class="col-md-3"><img src="../../<?php echo htmlspecialchars($img['image_path']); ?>" class="img-fluid rounded"></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</body>
</html>
