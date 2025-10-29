<?php
require_once __DIR__ . '/../admin/includes/db_config.php';
require_once __DIR__ . '/../admin/news/news_functions.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 25;
$offset = ($page - 1) * $per_page;

$conditions = ["n.status='published'"];
$params = [];
$types = '';
if ($search !== '') { $conditions[] = '(n.title LIKE ? OR n.content LIKE ?)'; $params[]="%$search%"; $params[]="%$search%"; $types.='ss'; }
$where = 'WHERE '.implode(' AND ', $conditions);

// total
$sql = "SELECT COUNT(*) as total FROM news n $where";
$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = max(1, ceil($total/$per_page));

// list - เพิ่ม views, likes, sdg_goals
$sql = "SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image, n.published_at, n.views, n.likes, n.sdg_goals
        FROM news n 
        $where 
        ORDER BY n.published_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) { $params2 = $params; $types2 = $types.'ii'; $params2[]=$per_page; $params2[]=$offset; $stmt->bind_param($types2, ...$params2); }
else { $stmt->bind_param('ii', $per_page, $offset); }
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$sdg_meta = [
    1 => ['color' => '#E5243B', 'name' => 'SDG 1: ขจัดความยากจน'],
    2 => ['color' => '#DDA63A', 'name' => 'SDG 2: ขจัดความหิวโหย'],
    3 => ['color' => '#4C9F38', 'name' => 'SDG 3: สุขภาพและความเป็นอยู่ที่ดี'],
    4 => ['color' => '#C5192D', 'name' => 'SDG 4: การศึกษาที่มีคุณภาพ'],
    5 => ['color' => '#FF3A21', 'name' => 'SDG 5: ความเท่าเทียมทางเพศ'],
    6 => ['color' => '#26BDE2', 'name' => 'SDG 6: น้ำสะอาดและสุขาภิบาล'],
    7 => ['color' => '#FCC30B', 'name' => 'SDG 7: พลังงานสะอาดที่เข้าถึงได้'],
    8 => ['color' => '#A21942', 'name' => 'SDG 8: งานที่มีคุณค่าและการเติบโตทางเศรษฐกิจ'],
    9 => ['color' => '#FD6925', 'name' => 'SDG 9: อุตสาหกรรม นวัตกรรม และโครงสร้างพื้นฐาน'],
    10 => ['color' => '#DD1367', 'name' => 'SDG 10: ลดความเหลื่อมล้ำ'],
    11 => ['color' => '#FD9D24', 'name' => 'SDG 11: เมืองและชุมชนที่ยั่งยืน'],
    12 => ['color' => '#BF8B2E', 'name' => 'SDG 12: การบริโภคและการผลิตที่ยั่งยืน'],
    13 => ['color' => '#3F7E44', 'name' => 'SDG 13: การดำเนินการด้านสภาพภูมิอากาศ'],
    14 => ['color' => '#0A97D9', 'name' => 'SDG 14: ชีวิตใต้น้ำ'],
    15 => ['color' => '#56C02B', 'name' => 'SDG 15: ชีวิตบนบก'],
    16 => ['color' => '#00689D', 'name' => 'SDG 16: สันติภาพ ความยุติธรรม และสถาบันที่เข้มแข็ง'],
    17 => ['color' => '#19486A', 'name' => 'SDG 17: ความร่วมมือเพื่อบรรลุเป้าหมาย'],
];
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
		
		/* Portfolio Card Styles - เหมือน news_announcements.php */
		.portfolio-item {
			margin-bottom: 30px;
		}
		
		.portfolio-content {
			position: relative;
			overflow: visible;
			border-radius: 12px;
			background: #fff;
			border: 1px solid #e0e0e0;
			transition: all 0.3s ease;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
			cursor: pointer;
			display: flex;
			flex-direction: column;
			height: 100%;
		}
		
		.portfolio-content:hover {
			transform: translateY(-8px);
			box-shadow: 0 12px 32px rgba(0,0,0,0.15);
			border-color: #0066cc;
		}
		
		.portfolio-image {
			position: relative;
			overflow: hidden;
			background: #f5f5f5;
			border-top-left-radius: 12px;
			border-top-right-radius: 12px;
			width: 100%;
			min-height: 200px;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		
		.portfolio-image img {
			width: 100%;
			height: auto;
			max-height: 300px;
			display: block;
			object-fit: contain;
			object-position: center;
			border-top-left-radius: 12px;
			border-top-right-radius: 12px;
			transition: transform 0.4s ease;
		}
		
		.portfolio-content:hover .portfolio-image img {
			transform: scale(1.08);
		}
		
		.portfolio-meta {
			padding: 15px;
			flex: 1;
			display: flex;
			flex-direction: column;
		}
		
		.news-meta-top {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-bottom: 8px;
		}
		
		.sdg-badges {
			display: flex;
			flex-wrap: wrap;
			gap: 4px;
			flex: 1 1 auto;
		}
		
		.sdg-badge {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 19px;
			height: 19px;
			border-radius: 5px;
			color: white;
			font-weight: bold;
			font-size: 10px;
			box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
			transition: all 0.3s ease;
			opacity: 0.6;
			position: relative;
		}
		
		.sdg-badge:hover {
			opacity: 0.9;
			transform: scale(1.1);
		}
		
		.sdg-badge::after {
			content: attr(data-sdg-name);
			position: absolute;
			bottom: 100%;
			left: 50%;
			transform: translateX(-50%);
			background: rgba(0, 0, 0, 0.9);
			color: #fff;
			padding: 3px 6px;
			border-radius: 5px;
			font-size: 9px;
			white-space: nowrap;
			opacity: 0;
			pointer-events: none;
			transition: opacity 0.3s ease;
			z-index: 1000;
			margin-bottom: 5px;
		}
		
		.sdg-badge:hover::after {
			opacity: 1;
		}
		
		.news-activity-date {
			font-size: 11px;
			color: #333;
			font-weight: 500;
			display: inline-flex;
			align-items: center;
			gap: 3px;
			margin-left: auto;
		}
		
		.news-activity-date i {
			font-size: 11px;
			color: #666;
		}
		
		.news-activity-title {
			font-size: 14px;
			font-weight: 400;
			margin-bottom: 15px;
			line-height: 1.5;
			overflow: hidden;
			display: -webkit-box;
			-webkit-line-clamp: 3;
			line-clamp: 3;
			-webkit-box-orient: vertical;
		}
		
		.news-activity-title::before {
			content: "📢 ";
			margin-right: 4px;
			font-size: 13px;
		}
		
		.news-activity-title a {
			color: inherit;
			text-decoration: none;
			transition: color 0.2s ease;
		}
		
		.news-activity-title a:hover {
			color: #0066cc;
		}
		
		.news-excerpt {
			display: block;
			font-size: 12px;
			font-weight: 300;
			color: #666;
			line-height: 1.4;
			margin-top: 8px;
			opacity: 1;
		}
		
		.news-stats {
			display: flex;
			align-items: center;
			gap: 8px;
			font-size: 13px;
			color: #666;
			margin-top: auto;
			padding-top: 10px;
		}
		
		.news-stats .news-views i {
			font-size: 17.5px !important;
		}
		
		.news-stats .news-views,
		.news-stats .news-like-button {
			display: inline-flex;
			align-items: center;
			gap: 3.2px;
		}
		
		.news-stats .news-views i,
		.news-stats .news-like-button i {
			font-size: 13.5px;
		}
		
		.news-like-button {
			border: none;
			background: transparent;
			padding: 0;
			color: #888;
			cursor: pointer;
			transition: color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
			font-size: 13px;
		}
		
		.news-like-button:hover {
			color: #1877f2;
			transform: scale(1.1);
		}
		
		.news-like-button.liked,
		.news-like-button.liked:hover {
			color: #1877f2;
		}
		
		.news-like-button.liked i {
			animation: likeAnimation 0.3s ease;
		}
		
		@keyframes likeAnimation {
			0% { transform: scale(1); }
			50% { transform: scale(1.3); }
			100% { transform: scale(1); }
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
				<div class="col-md-9">
					<div class="input-group">
						<span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
						<input class="form-control border-start-0" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหาข่าว...">
					</div>
				</div>
				<div class="col-md-3 d-grid">
					<button class="btn btn-primary search-btn">
						<i class="fas fa-search me-2"></i>ค้นหา
					</button>
				</div>
			</form>
		</div>

		<div class="news-grid">
			<?php foreach ($items as $n): 
				$detailUrl = !empty($n['slug']) ? 'detail.php?slug=' . urlencode($n['slug']) : 'detail.php?id=' . $n['id'];
				$imagePath = !empty($n['featured_image']) ? '../' . ltrim($n['featured_image'], '/') : '../images/comingsoon.png';
				$views = isset($n['views']) ? (int)$n['views'] : 0;
				$likes = isset($n['likes']) ? (int)$n['likes'] : 0;
			?>
			<div class="portfolio-item">
				<div class="portfolio-content h-100" data-detail-url="<?php echo htmlspecialchars($detailUrl); ?>" data-item-id="<?php echo (int)$n['id']; ?>" data-item-type="news" role="link" tabindex="0">
					<div class="portfolio-image">
						<img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($n['title']); ?>" onerror="this.src='../images/comingsoon.png'">
					</div>
					<div class="portfolio-meta">
						<div class="news-meta-top">
							<?php if (!empty($n['sdg_goals'])): ?>
							<div class="sdg-badges">
							<?php
							$sdgs = explode(',', $n['sdg_goals']);
							foreach ($sdgs as $sdg):
								$sdg = trim($sdg);
								if (isset($sdg_meta[$sdg])):
									$meta = $sdg_meta[$sdg];
							?>
							<span class="sdg-badge" style="background-color: <?php echo $meta['color']; ?>" data-sdg-name="<?php echo htmlspecialchars($meta['name']); ?>">
								<?php echo $sdg; ?>
							</span>
							<?php
								endif;
							endforeach;
							?>
							</div>
							<?php else: ?>
							<div class="sdg-badges"></div>
							<?php endif; ?>
							<div class="news-activity-date">
								<i class="fas fa-calendar-alt"></i>
								<?php echo $n['published_at'] ? date('d/m/Y', strtotime($n['published_at'])) : date('d/m/Y'); ?>
							</div>
						</div>
						<h3 class="news-activity-title">
							<a class="news-detail-link" href="<?php echo htmlspecialchars($detailUrl); ?>" target="_blank" rel="noopener" data-item-type="news" data-item-id="<?php echo (int)$n['id']; ?>">
								<?php echo htmlspecialchars($n['title']); ?>
							</a>
						</h3>
						<?php if (!empty($n['excerpt'])): 
							$rawExcerpt = trim(strip_tags($n['excerpt']));
							$maxChars = 100;
							if (mb_strlen($rawExcerpt, 'UTF-8') > $maxChars) {
								$truncated = mb_substr($rawExcerpt, 0, $maxChars, 'UTF-8');
								$lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
								if ($lastSpace !== false) {
									$truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
								}
								$rawExcerpt = rtrim($truncated, ",.;-") . '...';
							}
						?>
						<div class="news-excerpt" title="<?php echo htmlspecialchars($rawExcerpt); ?>">
							<?php echo htmlspecialchars($rawExcerpt); ?>
						</div>
						<?php endif; ?>
						<div class="news-stats">
							<span class="news-views" title="จำนวนผู้เข้าชม">
								<i class="fas fa-eye"></i>
								<span class="news-views-count" data-count="<?php echo $views; ?>" data-item-type="news"><?php echo number_format($views); ?></span>
							</span>
							<button class="news-like-button" type="button" data-item-id="<?php echo $n['id']; ?>" data-item-type="news" title="ถูกใจ" aria-label="ถูกใจข่าวนี้ (จำนวน <?php echo number_format($likes); ?> ครั้ง)">
								<i class="far fa-thumbs-up"></i>
								<span class="news-like-count" data-count="<?php echo $likes; ?>"><?php echo number_format($likes); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

		<?php if (count($items)===0): ?>
		<div class="alert alert-info text-center py-5 mt-4">
			<i class="fas fa-info-circle fa-3x mb-3"></i>
			<h4>ไม่พบบทความ</h4>
			<p class="mb-0">ไม่พบบทความที่ตรงกับเงื่อนไขการค้นหา กรุณาลองค้นหาด้วยคำค้นอื่น</p>
		</div>
		<?php endif; ?>

		<?php if ($total_pages > 1) { ?>
		<nav class="mt-5">
			<ul class="pagination justify-content-center">
				<?php if ($page > 1) { ?>
				<li class="page-item">
					<a class="page-link" href="?page=<?php echo $page-1; ?>&q=<?php echo urlencode($search); ?>" aria-label="Previous">
						<span aria-hidden="true">&laquo;</span>
					</a>
				</li>
				<?php } ?>

				<?php 
				for ($i = 1; $i <= $total_pages; $i++) {
				?>
				<li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
					<a class="page-link" href="?page=<?php echo $i; ?>&q=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
				</li>
				<?php 
				} 
				?>

				<?php if ($page < $total_pages) { ?>
				<li class="page-item">
					<a class="page-link" href="?page=<?php echo $page+1; ?>&q=<?php echo urlencode($search); ?>" aria-label="Next">
						<span aria-hidden="true">&raquo;</span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</nav>
		<?php } ?>
	</div>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
	// Like and View functionality - เหมือน news_announcements.php
	document.addEventListener('DOMContentLoaded', function() {
		// Card click to view
		document.querySelectorAll('.portfolio-content[data-detail-url]').forEach(function(card) {
			const detailUrl = card.dataset.detailUrl;
			const itemId = card.dataset.itemId;
			const itemType = card.dataset.itemType || 'news';
			
			card.addEventListener('click', function(e) {
				if (e.target.closest('.news-like-button') || e.target.closest('.news-detail-link')) {
					return;
				}
				
				// Increment view count
				const viewsEl = card.querySelector('.news-views-count');
				if (viewsEl) {
					const prev = parseInt(viewsEl.dataset.count || '0', 10);
					const next = prev + 1;
					viewsEl.dataset.count = next;
					viewsEl.textContent = next.toLocaleString();
				}
				
				// Send view count to server
				fetch('increment_view.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ news_id: parseInt(itemId, 10) }),
					keepalive: true
				}).catch(() => {});
				
				// Open in new tab
				window.open(detailUrl, '_blank');
			});
			
			// Keyboard accessibility
			card.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					card.click();
				}
			});
		});
		
		// Like button functionality
		document.querySelectorAll('.news-like-button').forEach(function(button) {
			const itemId = button.dataset.itemId;
			const itemType = button.dataset.itemType || 'news';
			const storageKey = `content_like_${itemType}_${itemId}`;
			const iconEl = button.querySelector('i');
			const countEl = button.querySelector('.news-like-count');
			
			// Check if already liked
			if (localStorage.getItem(storageKey)) {
				button.classList.add('liked');
				if (iconEl.classList.contains('far')) {
					iconEl.classList.remove('far');
					iconEl.classList.add('fas');
				}
			}
			
			button.addEventListener('click', function(e) {
				e.stopPropagation();
				
				if (button.classList.contains('liked')) {
					return;
				}
				
				// Optimistic update
				const currentCount = parseInt(countEl.dataset.count || '0', 10);
				const newCount = currentCount + 1;
				countEl.dataset.count = newCount;
				countEl.textContent = newCount.toLocaleString();
				button.classList.add('liked');
				
				if (iconEl.classList.contains('far')) {
					iconEl.classList.remove('far');
					iconEl.classList.add('fas');
				}
				
				// Send to server
				fetch('like.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ news_id: parseInt(itemId, 10) })
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						localStorage.setItem(storageKey, '1');
						if (data.likes) {
							countEl.dataset.count = data.likes;
							countEl.textContent = data.likes.toLocaleString();
						}
					} else {
						// Revert on error
						countEl.dataset.count = currentCount;
						countEl.textContent = currentCount.toLocaleString();
						button.classList.remove('liked');
						if (iconEl.classList.contains('fas')) {
							iconEl.classList.remove('fas');
							iconEl.classList.add('far');
						}
					}
				})
				.catch(() => {
					// Revert on error
					countEl.dataset.count = currentCount;
					countEl.textContent = currentCount.toLocaleString();
					button.classList.remove('liked');
					if (iconEl.classList.contains('fas')) {
						iconEl.classList.remove('fas');
						iconEl.classList.add('far');
					}
				});
			});
		});
	});
	</script>
</body>
</html>
