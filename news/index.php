<?php
require_once __DIR__ . '/../admin/includes/db_config.php';
require_once __DIR__ . '/../admin/news/news_functions.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$conditions = ["n.status='published'"];
$params = [];
$types = '';
if ($category_id > 0) { $conditions[] = 'n.category_id=?'; $params[]=$category_id; $types.='i'; }
if ($search !== '') { $conditions[] = '(n.title LIKE ? OR n.content LIKE ?)'; $params[]="%$search%"; $params[]="%$search%"; $types.='ss'; }
$where = 'WHERE '.implode(' AND ', $conditions);

// total
$sql = "SELECT COUNT(*) as total FROM news n $where";
$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = max(1, ceil($total/$per_page));

// list
$sql = "SELECT n.*, c.name AS category_name FROM news n LEFT JOIN news_categories c ON c.id=n.category_id $where ORDER BY n.published_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) { $params2 = $params; $types2 = $types.'ii'; $params2[]=$per_page; $params2[]=$offset; $stmt->bind_param($types2, ...$params2); }
else { $stmt->bind_param('ii', $per_page, $offset); }
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$cats = $conn->query("SELECT * FROM news_categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ข่าวและกิจกรรม - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		body {
			font-family: 'Prompt', sans-serif;
			background-color: #f8f9fa;
		}
		.page-header {
			background-color: #7b3b95;
			color: white;
			padding: 30px 0;
			margin-bottom: 30px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
		}
		.news-card {
			border: none;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 5px 15px rgba(0,0,0,.08);
			transition: all 0.3s ease;
		}
		.news-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 10px 20px rgba(0,0,0,.12);
		}
		.news-img {
			height: 200px;
			object-fit: cover;
			width: 100%;
			background-color: #f0f0f0;
		}
		.card-body {
			padding: 20px;
		}
		.card-title {
			font-weight: 600;
			margin-bottom: 10px;
			color: #333;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
			height: 48px;
		}
		.card-text {
			display: -webkit-box;
			-webkit-line-clamp: 3;
			-webkit-box-orient: vertical;
			overflow: hidden;
			height: 72px;
		}
		.search-box {
			background-color: white;
			padding: 20px;
			border-radius: 12px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.05);
			margin-bottom: 30px;
		}
		.search-btn {
			border-radius: 50px;
			padding: 8px 20px;
			font-weight: 500;
		}
		.home-btn {
			border-radius: 50px;
			padding: 8px 20px;
			font-weight: 500;
			background-color: white;
			color: #7b3b95;
			border: 2px solid #7b3b95;
		}
		.home-btn:hover {
			background-color: #7b3b95;
			color: white;
		}
		.pagination .page-link {
			color: #7b3b95;
			border-radius: 50%;
			margin: 0 3px;
			width: 40px;
			height: 40px;
			text-align: center;
			line-height: 24px;
		}
		.pagination .page-item.active .page-link {
			background-color: #7b3b95;
			border-color: #7b3b95;
		}
		.category-badge {
			background-color: #e9ecef;
			color: #495057;
			font-size: 0.75rem;
			padding: 4px 10px;
			border-radius: 50px;
			margin-right: 5px;
		}
		.date-badge {
			color: #6c757d;
			font-size: 0.75rem;
		}
	</style>
</head>
<body>
	<div class="page-header">
		<div class="container">
			<div class="d-flex justify-content-between align-items-center">
				<h1 class="h2 mb-0">ข่าวและกิจกรรม</h1>
				<a href="../index.php" class="btn home-btn">
					<i class="fas fa-home me-2"></i>กลับหน้าหลัก
				</a>
			</div>
		</div>
	</div>

	<div class="container pb-5">
		<div class="search-box">
			<form class="row g-3">
				<div class="col-md-5">
					<div class="input-group">
						<span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
						<input class="form-control border-start-0" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหาข่าว...">
					</div>
				</div>
				<div class="col-md-4">
					<select class="form-select" name="category">
						<option value="0">ทุกหมวดหมู่</option>
						<?php foreach($cats as $c): ?>
						<option value="<?php echo $c['id']; ?>" <?php echo $category_id==$c['id']?'selected':''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3 d-grid">
					<button class="btn btn-primary search-btn">
						<i class="fas fa-search me-2"></i>ค้นหา
					</button>
				</div>
			</form>
		</div>

		<div class="row g-4">
			<?php foreach ($items as $n): ?>
			<div class="col-md-6 col-lg-4">
				<div class="card news-card h-100">
					<div class="position-relative">
						<?php if ($n['featured_image']): ?>
							<img class="news-img" src="../<?php echo htmlspecialchars($n['featured_image']); ?>" alt="<?php echo htmlspecialchars($n['title']); ?>">
						<?php else: ?>
							<img class="news-img" src="../images/comingsoon.png" alt="ไม่มีรูปภาพ">
						<?php endif; ?>
					</div>
					<div class="card-body">
						<div class="d-flex align-items-center mb-2">
							<span class="category-badge"><?php echo htmlspecialchars($n['category_name'] ?? 'ทั่วไป'); ?></span>
							<span class="date-badge">
								<i class="far fa-calendar-alt me-1"></i>
								<?php echo $n['published_at'] ? date('d/m/Y', strtotime($n['published_at'])) : 'ไม่ระบุ'; ?>
							</span>
						</div>
						<h5 class="card-title"><?php echo htmlspecialchars($n['title']); ?></h5>
						<p class="card-text text-muted"><?php echo htmlspecialchars($n['excerpt'] ?? substr(strip_tags($n['content']), 0, 150) . '...'); ?></p>
						<div class="d-flex justify-content-between align-items-center">
							<small class="text-muted">
								<i class="fas fa-user me-1"></i>
								<?php echo htmlspecialchars($n['full_name'] ?? 'ระบบ'); ?>
							</small>
							<a class="btn btn-sm btn-outline-primary" href="detail.php?slug=<?php echo urlencode($n['slug']); ?>">
								อ่านเพิ่มเติม <i class="fas fa-arrow-right ms-1"></i>
							</a>
						</div>
						<a class="stretched-link" href="detail.php?slug=<?php echo urlencode($n['slug']); ?>"></a>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			
			<?php if (count($items)===0): ?>
			<div class="col-12">
				<div class="alert alert-info text-center py-5">
					<i class="fas fa-info-circle fa-3x mb-3"></i>
					<h4>ไม่พบบทความ</h4>
					<p class="mb-0">ไม่พบบทความที่ตรงกับเงื่อนไขการค้นหา กรุณาลองค้นหาด้วยคำค้นอื่น</p>
				</div>
			</div>
			<?php endif; ?>
		</div>

		<?php if ($total_pages>1): ?>
		<nav class="mt-5">
			<ul class="pagination justify-content-center">
				<?php if ($page > 1): ?>
				<li class="page-item">
					<a class="page-link" href="?page=<?php echo $page-1; ?>&category=<?php echo $category_id; ?>&q=<?php echo urlencode($search); ?>" aria-label="Previous">
						<span aria-hidden="true">&laquo;</span>
					</a>
				</li>
				<?php endif; ?>
				
				<?php for($i=1;$i<=$total_pages;$i++): ?>
				<li class="page-item <?php echo $i==$page?'active':''; ?>">
					<a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category_id; ?>&q=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
				</li>
				<?php endfor; ?>
				
				<?php if ($page < $total_pages): ?>
				<li class="page-item">
					<a class="page-link" href="?page=<?php echo $page+1; ?>&category=<?php echo $category_id; ?>&q=<?php echo urlencode($search); ?>" aria-label="Next">
						<span aria-hidden="true">&raquo;</span>
					</a>
				</li>
				<?php endif; ?>
			</ul>
		</nav>
		<?php endif; ?>
	</div>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
