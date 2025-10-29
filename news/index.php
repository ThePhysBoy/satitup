<?php
require_once __DIR__ . '/../admin/includes/db_config.php';
require_once __DIR__ . '/../admin/news/news_functions.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 25;
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
			background-color: #0066cc;
			color: white;
			padding: 40px 0;
			margin-bottom: 40px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
		}
		.news-card {
		border: 1px solid #e0e0e0;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 4px 12px rgba(0,0,0,0.1);
		transition: all 0.3s ease;
		background: #fff;
		cursor: pointer;
		}
		.news-card:hover {
			transform: translateY(-8px);
			box-shadow: 0 12px 32px rgba(0,0,0,0.15);
			border-color: #0066cc;
		}
	.news-img {
		height: 200px;
		object-fit: cover;
		object-position: center;
		width: 100%;
		background-color: #f5f5f5;
		display: block;
		transition: transform 0.4s ease;
	}
		
		.news-card:hover .news-img {
			transform: scale(1.08);
		}
	.card-body {
		padding: 16px;
		min-height: 150px;
		position: relative;
	}
	.card-title {
		font-size: 16px;
		font-weight: 500;
		margin-bottom: 12px;
		color: #333;
		display: -webkit-box;
		-webkit-line-clamp: 3;
		line-clamp: 3;
		-webkit-box-orient: vertical;
		overflow: hidden;
		min-height: 70px;
		line-height: 1.45;
	}
		
		.card-title::before {
			content: "📢 ";
			margin-right: 5px;
			font-size: 16px;
		}
		.card-text {
			display: none;
		}
		.search-box {
			background-color: white;
			padding: 24px;
			border-radius: 12px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.05);
			margin-bottom: 40px;
			border: 1px solid #e0e0e0;
		}
		.search-btn {
			border-radius: 4px;
			padding: 10px 24px;
			font-weight: 500;
			background-color: #0066cc;
			border-color: #0066cc;
			transition: all 0.2s ease;
		}
		.search-btn:hover {
			background-color: #0052a3;
			border-color: #0052a3;
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
		}
		.home-btn {
			border-radius: 6px;
			padding: 10px 24px;
			font-weight: 500;
			background-color: white;
			color: #0066cc;
			border: 1px solid #0066cc;
			transition: all 0.2s ease;
		}
		.home-btn:hover {
			background-color: #0066cc;
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
		}
	.pagination .page-link {
		color: #0066cc;
		border-radius: 4px;
		margin: 0 3px;
		min-width: 40px;
		height: 40px;
		text-align: center;
		line-height: 24px;
	}
		.pagination .page-item.active .page-link {
			background-color: #0066cc;
			border-color: #0066cc;
		}
		.category-badge {
			display: inline-block;
			background: #f0f0f0;
			color: #666;
			font-size: 12px;
			font-weight: 500;
			padding: 5px 12px;
			border-radius: 4px;
			margin-bottom: 12px;
			margin-right: 5px;
		}
		.date-badge {
			position: absolute;
			top: 15px;
			right: 15px;
			background: rgba(255, 255, 255, 0.95);
			color: #333;
			padding: 8px 14px;
			border-radius: 6px;
			font-size: 13px;
			font-weight: 500;
			backdrop-filter: blur(5px);
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
			z-index: 2;
	}

	.news-grid {
		display: grid;
		grid-template-columns: repeat(5, minmax(0, 1fr));
		gap: 24px;
	}

	@media (max-width: 1400px) {
		.news-grid {
			grid-template-columns: repeat(4, minmax(0, 1fr));
		}
	}

	@media (max-width: 1200px) {
		.news-grid {
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}
	}

	@media (max-width: 992px) {
		.news-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}
	}

	@media (max-width: 576px) {
		.news-grid {
			grid-template-columns: repeat(1, minmax(0, 1fr));
		}
	}
	</style>
</head>
<body>
	<div class="page-header">
		<div class="container">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h1 class="h2 mb-0">กิจกรรมประชาสัมพันธ์ / ข่าวสาร</h1>
					<p class="text-white-50 mb-0">Activities / News</p>
				</div>
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

		<div class="news-grid">
			<?php foreach ($items as $n): ?>
			<article class="card news-card h-100">
				<div class="position-relative">
					<?php if ($n['featured_image']): ?>
						<img class="news-img" src="../<?php echo htmlspecialchars($n['featured_image']); ?>" alt="<?php echo htmlspecialchars($n['title']); ?>">
					<?php else: ?>
						<img class="news-img" src="../images/comingsoon.png" alt="ไม่มีรูปภาพ">
					<?php endif; ?>
					<span class="date-badge">
						<?php echo $n['published_at'] ? date('d/m/Y', strtotime($n['published_at'])) : 'ไม่ระบุ'; ?>
					</span>
				</div>
				<div class="card-body">
					<?php if (!empty($n['category_name'])): ?>
					<div class="category-badge">
						<?php echo htmlspecialchars($n['category_name'] ?? 'ทั่วไป'); ?>
					</div>
					<?php endif; ?>
					<h5 class="card-title"><?php echo htmlspecialchars($n['title']); ?></h5>
					<a class="stretched-link" href="detail.php?slug=<?php echo urlencode($n['slug']); ?>"></a>
				</div>
			</article>
			<?php endforeach; ?>
		</div>

		<?php if (count($items)===0): ?>
		<div class="alert alert-info text-center py-5 mt-4">
			<i class="fas fa-info-circle fa-3x mb-3"></i>
			<h4>ไม่พบบทความ</h4>
			<p class="mb-0">ไม่พบบทความที่ตรงกับเงื่อนไขการค้นหา กรุณาลองค้นหาด้วยคำค้นอื่น</p>
		</div>
		<?php endif; ?>

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
