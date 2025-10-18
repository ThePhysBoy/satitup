<?php
require_once __DIR__ . '/../admin/includes/db_config.php';
require_once __DIR__ . '/../admin/news/news_functions.php';

require_once __DIR__ . '/functions.php';

// รองรับทั้ง id และ slug
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = $_GET['slug'] ?? '';

// ใช้ฟังก์ชันจาก functions.php
if ($id > 0) {
    $news = getNewsDetail($id, $conn);
} elseif ($slug !== '') {
    $news = getNewsDetail($slug, $conn);
} else {
    // ถ้าไม่มีทั้ง id และ slug
    header('Location: index.php');
    exit;
}

// ถ้าไม่พบข่าว ให้กลับไปหน้าหลัก
if (!$news) { 
    header('Location: index.php'); 
    exit; 
}

// ข้อมูลเพิ่มเติมสำหรับแสดงผล
$images = getNewsImages($news['id'], $conn); // ฟังก์ชันจาก admin/news/news_functions.php
$category_name = $news['category_name'] ?? 'ทั่วไป';
$author_name = $news['full_name'] ?? $news['username'] ?? 'ระบบ';
$published_date = $news['published_at'] ? date('d F Y', strtotime($news['published_at'])) : date('d F Y', strtotime($news['created_at']));
?>
<!DOCTYPE html>
<html lang="th">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo htmlspecialchars($news['title']); ?> - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		:root {
			--primary-color: #8B7AA8;      /* ม่วงอ่อนหลัก */
			--primary-light: #B8A9D4;      /* ม่วงอ่อนมาก */
			--primary-dark: #6B5A88;       /* ม่วงเข้ม */
			--secondary-color: #9C89B8;    /* ม่วงรอง */
			--accent-color: #F0A6CA;       /* ชมพูอ่อน */
			--light-accent: #F3EDF7;       /* ม่วงอ่อนมากๆ */
			--text-color: #4A4A4A;         /* สีข้อความหลัก */
			--text-muted: #6C757D;         /* สีข้อความรอง */
			--border-color: #E1D9EB;       /* สีขอบม่วงอ่อน */
			--bg-light: #FAFAFA;           /* พื้นหลังอ่อน */
			--white: #FFFFFF;              /* สีขาว */
			--shadow: 0 2px 6px rgba(139, 122, 168, 0.1);
			--shadow-hover: 0 4px 12px rgba(139, 122, 168, 0.15);
			--border-radius: 4px;
			--header-bg: linear-gradient(135deg, #B8A9D4 0%, #8B7AA8 100%);
			--heading-color: #6B5A88;      /* สีหัวข้อ */
			--link-color: #9C89B8;         /* สีลิงก์ */
			--link-hover: #6B5A88;         /* สีลิงก์เมื่อโฮเวอร์ */
		}

		body {
			font-family: 'Prompt', sans-serif;
			background-color: var(--bg-light);
			color: var(--text-color);
			line-height: 1.6;
		}
		
		.page-header {
			background: var(--header-bg);
			color: white;
			padding: 2rem 0;
			margin-bottom: 2rem;
			box-shadow: var(--shadow);
		}
		
		.page-header h1 {
			font-size: 2rem;
			font-weight: 600;
			margin-bottom: 0.5rem;
		}
		
		.breadcrumb-item a {
			color: rgba(255,255,255,0.8);
			text-decoration: none;
		}
		
		.breadcrumb-item a:hover {
			color: white;
			text-decoration: underline;
		}
		
		.breadcrumb-item.active {
			color: rgba(255,255,255,0.6);
		}
		
		.breadcrumb-item+.breadcrumb-item::before {
			color: rgba(255,255,255,0.6);
		}

		/* Hero Section */
		.hero-section {
			background: var(--white);
			border: none;
			border-radius: var(--border-radius);
			padding: 0;
			margin-bottom: 2rem;
			box-shadow: var(--shadow);
			position: relative;
		}

		.hero-image {
			width: 100%;
			height: auto;
			max-height: 500px;
			min-height: 300px;
			object-fit: contain;
			object-position: center;
			border-radius: var(--border-radius);
			border: none;
			background-color: #f8f9fa;
			display: block;
			margin: 0 auto;
		}
		
		.image-overlay {
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
			padding: 2rem 1.5rem 1.5rem;
			border-bottom-left-radius: var(--border-radius);
			border-bottom-right-radius: var(--border-radius);
		}
		
		.image-caption {
			color: white;
			font-size: 1.1rem;
			font-weight: 500;
			text-shadow: 0 1px 2px rgba(0,0,0,0.5);
		}

		/* Gallery Section */
		.gallery-section {
			background: var(--white);
			border: none;
			border-radius: var(--border-radius);
			padding: 1.5rem;
			margin-bottom: 2rem;
			box-shadow: var(--shadow);
		}
		
		.gallery-title {
			color: var(--primary-color);
			font-weight: 600;
			margin-bottom: 1rem;
			padding-bottom: 0.75rem;
			border-bottom: 2px solid var(--light-accent);
		}

		.gallery-thumbnails {
			display: flex;
			gap: 0.75rem;
			flex-wrap: wrap;
			justify-content: flex-start;
		}
		
		.gallery-thumbnails .image-container {
			width: 100px;
			height: 75px;
			margin-bottom: 0.5rem;
			background-color: transparent;
			padding: 0;
		}

		.gallery-thumb {
			width: 100px;
			height: 75px;
			object-fit: cover;
			object-position: center;
			border-radius: var(--border-radius);
			cursor: pointer;
			border: 2px solid var(--border-color);
			transition: all 0.2s ease;
			box-shadow: 0 2px 4px rgba(0,0,0,0.05);
			background-color: #f8f9fa;
		}

		.gallery-thumb:hover {
			border-color: var(--accent-color);
			transform: translateY(-2px);
			box-shadow: var(--shadow-hover);
		}

		.gallery-thumb.active {
			border-color: var(--accent-color);
			box-shadow: 0 0 0 2px var(--light-accent);
		}

		/* Content Section */
		.content-section {
			background: var(--white);
			border: none;
			border-radius: var(--border-radius);
			padding: 2rem;
			box-shadow: var(--shadow);
			margin-bottom: 2rem;
		}

		.content-title {
			font-size: 2rem;
			font-weight: 700;
			color: var(--heading-color);
			margin-bottom: 1.5rem;
			line-height: 1.3;
			border-bottom: 2px solid var(--light-accent);
			padding-bottom: 1rem;
		}

		.content-text {
			font-size: 1.05rem;
			line-height: 1.8;
			color: var(--text-color);
			margin-bottom: 2rem;
		}

		.content-text h1, .content-text h2, .content-text h3,
		.content-text h4, .content-text h5, .content-text h6 {
			color: var(--heading-color);
			margin-top: 2rem;
			margin-bottom: 1rem;
			font-weight: 600;
		}
		
		.content-text h2 {
			font-size: 1.75rem;
			border-left: 4px solid var(--accent-color);
			padding-left: 1rem;
		}

		.content-text p {
			margin-bottom: 1.25rem;
		}

		.content-text img {
			max-width: 100%;
			width: auto;
			height: auto;
			border-radius: var(--border-radius);
			margin: 1.5rem auto;
			box-shadow: var(--shadow);
			object-fit: contain;
			display: block;
			max-height: 600px;
		}
		
		/* Specific handling for large portrait images */
		.content-text img[width], .content-text img[height] {
			max-width: 100% !important;
			width: auto !important;
			height: auto !important;
			max-height: 600px !important;
		}
		
		.content-text ul, .content-text ol {
			margin-bottom: 1.5rem;
			padding-left: 1.5rem;
		}
		
		.content-text li {
			margin-bottom: 0.5rem;
		}

		/* Meta Information */
		.meta-section {
			background: var(--light-accent);
			border: none;
			border-radius: var(--border-radius);
			padding: 1.25rem;
			margin-bottom: 2rem;
			box-shadow: var(--shadow);
			border-left: 4px solid var(--accent-color);
		}
		
		.meta-title {
			color: var(--heading-color);
			font-weight: 600;
			margin-bottom: 1rem;
			font-size: 1.1rem;
		}

		.meta-item {
			display: flex;
			align-items: center;
			margin-bottom: 0.75rem;
			font-size: 0.95rem;
			color: var(--text-color);
		}

		.meta-item i {
			width: 20px;
			margin-right: 0.75rem;
			color: var(--accent-color);
		}

		.meta-item:last-child {
			margin-bottom: 0;
		}
		
		.meta-value {
			font-weight: 500;
		}

		/* Author Section */
		.author-section {
			background: var(--white);
			border: none;
			border-radius: var(--border-radius);
			padding: 1.75rem;
			box-shadow: var(--shadow);
			position: relative;
			overflow: hidden;
		}
		
		.author-section::after {
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			width: 150px;
			height: 100%;
			background: linear-gradient(135deg, transparent 0%, var(--light-accent) 100%);
			opacity: 0.6;
			z-index: 0;
		}

		.author-avatar {
			width: 70px;
			height: 70px;
			border-radius: 50%;
			background: var(--primary-color);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.75rem;
			color: white;
			box-shadow: var(--shadow);
			position: relative;
			z-index: 1;
			border: 3px solid var(--accent-color);
		}
		
		.author-info {
			position: relative;
			z-index: 1;
		}

		.author-name {
			font-size: 1.25rem;
			font-weight: 600;
			color: var(--heading-color);
			margin-bottom: 0.25rem;
		}

		.author-role {
			color: var(--accent-color);
			font-size: 0.9rem;
			font-weight: 500;
		}
		
		.author-contact {
			margin-top: 1rem;
		}
		
		.author-contact a {
			color: var(--link-color);
			text-decoration: none;
			font-size: 0.9rem;
			display: inline-flex;
			align-items: center;
			margin-right: 1rem;
		}
		
		.author-contact a:hover {
			color: var(--link-hover);
			text-decoration: underline;
		}
		
		.author-contact i {
			margin-right: 0.5rem;
		}

		/* Navigation */
		.nav-section {
			background: var(--white);
			border: none;
			border-radius: var(--border-radius);
			padding: 1.5rem;
			margin-bottom: 2rem;
			box-shadow: var(--shadow);
		}

		.nav-btn {
			background: var(--primary-color);
			border: none;
			color: white;
			padding: 0.6rem 1.5rem;
			border-radius: 4px;
			font-weight: 500;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			transition: all 0.2s ease;
		}

		.nav-btn:hover {
			background: var(--primary-light);
			color: white;
			transform: translateY(-1px);
			box-shadow: var(--shadow-hover);
		}

		.nav-btn i {
			margin-right: 0.5rem;
		}

		/* Category Badge */
		.category-badge {
			background: var(--primary-color);
			color: white;
			padding: 0.4rem 1rem;
			border-radius: 4px;
			font-size: 0.85rem;
			font-weight: 500;
			display: inline-block;
			box-shadow: var(--shadow);
		}
		
		/* Related News */
		.related-news {
			background: var(--white);
			border: none;
			border-radius: var(--border-radius);
			padding: 1.5rem;
			margin-bottom: 2rem;
			box-shadow: var(--shadow);
			border-top: 3px solid var(--accent-color);
		}
		
		.related-title {
			color: var(--heading-color);
			font-weight: 600;
			margin-bottom: 1.25rem;
			padding-bottom: 0.75rem;
			border-bottom: 2px solid var(--light-accent);
			position: relative;
		}
		
		.related-title::after {
			content: '';
			position: absolute;
			bottom: -2px;
			left: 0;
			width: 60px;
			height: 2px;
			background-color: var(--accent-color);
		}
		
		.related-item {
			padding: 0.75rem 0;
			border-bottom: 1px solid var(--border-color);
		}
		
		.related-item:last-child {
			border-bottom: none;
		}
		
		.related-item a {
			color: var(--text-color);
			text-decoration: none;
			font-weight: 500;
			transition: color 0.2s ease;
			display: block;
		}
		
		.related-item a:hover {
			color: var(--link-color);
		}
		
		.related-date {
			color: var(--text-muted);
			font-size: 0.85rem;
			margin-top: 0.25rem;
		}

		/* Share Buttons */
		.share-buttons {
			margin-top: 1.5rem;
		}
		
		.share-title {
			font-size: 1rem;
			font-weight: 600;
			color: var(--primary-color);
			margin-bottom: 0.75rem;
		}
		
		.share-btn {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 36px;
			height: 36px;
			border-radius: 50%;
			background: var(--light-accent);
			color: var(--primary-color);
			margin-right: 0.5rem;
			text-decoration: none;
			transition: all 0.2s ease;
			border: 2px solid transparent;
		}
		
		.share-btn:hover {
			background: var(--accent-color);
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 3px 10px rgba(139, 122, 168, 0.3);
		}
		
		/* Print Styles */
		@media print {
			.nav-section, .gallery-section, .share-buttons, .related-news {
				display: none;
			}
			
			.hero-section, .content-section, .meta-section, .author-section {
				box-shadow: none;
				border: 1px solid #ddd;
			}
			
			.content-title {
				font-size: 1.5rem;
			}
		}

		/* Image Display Container */
		.image-container {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 100%;
			height: auto;
			overflow: hidden;
			position: relative;
			background-color: #f8f9fa;
			border-radius: var(--border-radius);
		}
		
		/* Main image container for featured image */
		.hero-section .image-container {
			max-height: 500px;
			background-color: #f0f0f0;
			padding: 10px;
		}
		
		/* Responsive Design */
		@media (max-width: 768px) {
			.hero-image {
				max-height: 350px;
				min-height: 200px;
			}
			
			.content-text img {
				max-height: 400px;
				margin: 1rem auto;
			}
			
			.page-header {
				padding: 1.5rem 0;
			}
			
			.page-header h1 {
				font-size: 1.5rem;
			}

			.content-title {
				font-size: 1.5rem;
			}
			
			.content-text {
				font-size: 1rem;
			}

			.gallery-thumbnails {
				justify-content: flex-start;
			}

			.gallery-thumb {
				width: 70px;
				height: 55px;
			}

			.content-section, .meta-section, .author-section {
				padding: 1.25rem;
			}
		}
	</style>
</head>
<body>
	<!-- Page Header -->
	<div class="page-header">
		<div class="container">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
					<li class="breadcrumb-item"><a href="index.php">ข่าวและกิจกรรม</a></li>
					<li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($category_name); ?></li>
				</ol>
			</nav>
			<h1><?php echo htmlspecialchars($news['title']); ?></h1>
			<div class="d-flex align-items-center">
				<div class="category-badge me-3">
					<i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($category_name); ?>
				</div>
				<div class="meta-item me-3">
					<i class="fas fa-calendar-alt"></i>
					<span class="meta-value"><?php echo htmlspecialchars($published_date); ?></span>
				</div>
				<div class="meta-item">
					<i class="fas fa-eye"></i>
					<span class="meta-value"><?php echo number_format((int)$news['views'] + 1); ?> ครั้ง</span>
				</div>
			</div>
		</div>
	</div>

	<div class="container py-4">
		<div class="row">
			<div class="col-lg-8">
				<!-- Hero Section -->
				<div class="hero-section">
					<div class="image-container">
						<?php $mainSrc = $news['featured_image'] ? ('../'.$news['featured_image']) : '../images/comingsoon.png'; ?>
						<img id="mainImage" src="<?php echo htmlspecialchars($mainSrc); ?>" class="hero-image" alt="<?php echo htmlspecialchars($news['title']); ?>">
					</div>
					<?php if (!empty($news['excerpt'])): ?>
					<div class="image-overlay">
						<div class="image-caption"><?php echo htmlspecialchars($news['excerpt']); ?></div>
					</div>
					<?php endif; ?>
				</div>

				<!-- Gallery Section -->
				<?php if (count($images) > 0): ?>
				<div class="gallery-section">
					<h5 class="gallery-title"><i class="fas fa-images me-2"></i>รูปภาพประกอบ</h5>
					<div class="gallery-thumbnails">
						<?php foreach($images as $idx => $img): ?>
							<div class="image-container">
								<img src="../<?php echo htmlspecialchars($img['image_path']); ?>"
									data-src="../<?php echo htmlspecialchars($img['image_path']); ?>"
									class="gallery-thumb <?php echo $idx === 0 ? 'active' : ''; ?>"
									alt="รูปภาพที่ <?php echo $idx + 1; ?>"
									onclick="changeMainImage(this)">
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>

				<!-- Content Section -->
				<div class="content-section">
					<h2 class="content-title"><?php echo htmlspecialchars($news['title']); ?></h2>
					<div class="content-text">
						<?php echo $news['content']; ?>
					</div>
					
					<!-- Share Buttons -->
					<div class="share-buttons">
						<div class="share-title"><i class="fas fa-share-alt me-2"></i>แชร์ข่าวนี้</div>
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn">
							<i class="fab fa-facebook-f"></i>
						</a>
						<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($news['title']); ?>" target="_blank" class="share-btn">
							<i class="fab fa-twitter"></i>
						</a>
						<a href="https://line.me/R/msg/text/?<?php echo urlencode($news['title'].' http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn">
							<i class="fab fa-line"></i>
						</a>
						<a href="mailto:?subject=<?php echo urlencode($news['title']); ?>&body=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" class="share-btn">
							<i class="fas fa-envelope"></i>
						</a>
					</div>
				</div>
			</div>
			
			<div class="col-lg-4">
				<!-- Author Section -->
				<div class="author-section mb-4">
					<div class="d-flex">
						<div class="author-avatar me-3">
							<i class="fas fa-user"></i>
						</div>
						<div class="author-info">
							<div class="author-name"><?php echo htmlspecialchars($author_name); ?></div>
							<div class="author-role">ผู้ประกาศข่าว</div>
							<div class="author-contact">
								<a href="mailto:info@satitup.ac.th"><i class="fas fa-envelope"></i>ติดต่อ</a>
								<a href="#"><i class="fas fa-user-circle"></i>ดูโปรไฟล์</a>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Meta Information -->
				<div class="meta-section mb-4">
					<h5 class="meta-title"><i class="fas fa-info-circle me-2"></i>ข้อมูลข่าว</h5>
					<div class="meta-item">
						<i class="fas fa-calendar-alt"></i>
						<span>วันที่เผยแพร่: <span class="meta-value"><?php echo htmlspecialchars($published_date); ?></span></span>
					</div>
					<div class="meta-item">
						<i class="fas fa-eye"></i>
						<span>จำนวนผู้เข้าชม: <span class="meta-value"><?php echo number_format((int)$news['views'] + 1); ?> ครั้ง</span></span>
					</div>
					<div class="meta-item">
						<i class="fas fa-tag"></i>
						<span>หมวดหมู่: <span class="meta-value"><?php echo htmlspecialchars($category_name); ?></span></span>
					</div>
					<div class="meta-item">
						<i class="fas fa-user"></i>
						<span>ผู้เขียน: <span class="meta-value"><?php echo htmlspecialchars($author_name); ?></span></span>
					</div>
				</div>
				
				<!-- Related News -->
				<div class="related-news">
					<h5 class="related-title"><i class="fas fa-newspaper me-2"></i>ข่าวที่เกี่ยวข้อง</h5>
					<?php
					// Get related news
					$stmt = $conn->prepare("SELECT id, title, slug, published_at, created_at FROM news 
											WHERE category_id = ? AND id != ? AND status = 'published' 
											ORDER BY published_at DESC LIMIT 5");
					$stmt->bind_param('ii', $news['category_id'], $news['id']);
					$stmt->execute();
					$related_news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
					
					if (count($related_news) > 0):
						foreach($related_news as $related):
							$related_date = $related['published_at'] ? date('d/m/Y', strtotime($related['published_at'])) : date('d/m/Y', strtotime($related['created_at']));
					?>
					<div class="related-item">
						<a href="detail.php?slug=<?php echo htmlspecialchars($related['slug']); ?>">
							<?php echo htmlspecialchars($related['title']); ?>
						</a>
						<div class="related-date">
							<i class="fas fa-calendar-alt me-1"></i><?php echo htmlspecialchars($related_date); ?>
						</div>
					</div>
					<?php 
						endforeach;
					else:
					?>
					<div class="text-muted text-center py-3">ไม่พบข่าวที่เกี่ยวข้อง</div>
					<?php endif; ?>
					
					<div class="text-center mt-3">
						<a href="index.php?category=<?php echo $news['category_id']; ?>" class="btn btn-outline-primary btn-sm">
							<i class="fas fa-list me-1"></i>ดูข่าวทั้งหมดในหมวดหมู่นี้
						</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="mt-4 text-center">
			<a href="index.php" class="btn btn-primary me-2">
				<i class="fas fa-arrow-left me-1"></i>กลับไปหน้าข่าวทั้งหมด
			</a>
			<a href="../index.php" class="btn btn-secondary">
				<i class="fas fa-home me-1"></i>กลับหน้าหลัก
			</a>
		</div>
	</div>

	<!-- Footer -->
	<footer class="bg-dark text-white py-4 mt-5" style="background-color: #6B5A88;">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<h5 style="color: #B8A9D4;">โรงเรียนสาธิตมหาวิทยาลัยพะเยา</h5>
					<p>
						<i class="fas fa-map-marker-alt me-2" style="color: #F0A6CA;"></i> 19 หมู่ 2 ตำบลแม่กา อำเภอเมือง จังหวัดพะเยา 56000<br>
						<i class="fas fa-phone me-2" style="color: #F0A6CA;"></i> 054-466-666 ต่อ 1374, 1375<br>
						<i class="fas fa-envelope me-2" style="color: #F0A6CA;"></i> satitup@up.ac.th
					</p>
				</div>
				<div class="col-md-3">
					<h5 style="color: #B8A9D4;">ลิงก์ด่วน</h5>
					<ul class="list-unstyled">
						<li><a href="../index.php" style="color: #F0A6CA; text-decoration: none;">หน้าหลัก</a></li>
						<li><a href="../about.php" style="color: #F0A6CA; text-decoration: none;">เกี่ยวกับเรา</a></li>
						<li><a href="index.php" style="color: #F0A6CA; text-decoration: none;">ข่าวและกิจกรรม</a></li>
						<li><a href="../contact.php" style="color: #F0A6CA; text-decoration: none;">ติดต่อเรา</a></li>
					</ul>
				</div>
				<div class="col-md-3">
					<h5 style="color: #B8A9D4;">ติดตามเรา</h5>
					<div class="d-flex">
						<a href="#" class="me-3 fs-5" style="color: #F0A6CA;"><i class="fab fa-facebook"></i></a>
						<a href="#" class="me-3 fs-5" style="color: #F0A6CA;"><i class="fab fa-twitter"></i></a>
						<a href="#" class="me-3 fs-5" style="color: #F0A6CA;"><i class="fab fa-youtube"></i></a>
						<a href="#" class="fs-5" style="color: #F0A6CA;"><i class="fab fa-line"></i></a>
					</div>
				</div>
			</div>
			<hr style="border-color: #9C89B8;">
			<div class="text-center">
				<small>&copy; <?php echo date('Y'); ?> โรงเรียนสาธิตมหาวิทยาลัยพะเยา. สงวนลิขสิทธิ์.</small>
			</div>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// Change main image when gallery thumbnail is clicked
		function changeMainImage(element) {
			const mainImage = document.getElementById('mainImage');
			const newSrc = element.getAttribute('data-src');

			// Remove active class from all thumbnails
			document.querySelectorAll('.gallery-thumb').forEach(thumb => {
				thumb.classList.remove('active');
			});

			// Add active class to clicked thumbnail
			element.classList.add('active');

			// Change main image with fade effect
			mainImage.style.opacity = '0.7';
			setTimeout(() => {
				mainImage.src = newSrc;
				mainImage.style.opacity = '1';
			}, 150);
		}

		// Document ready
		document.addEventListener('DOMContentLoaded', function() {
			// Smooth scroll for anchor links
			document.querySelectorAll('a[href^="#"]').forEach(anchor => {
				anchor.addEventListener('click', function (e) {
					e.preventDefault();
					const target = document.querySelector(this.getAttribute('href'));
					if (target) {
						target.scrollIntoView({
							behavior: 'smooth'
						});
					}
				});
			});

			// Image error handling
			document.querySelectorAll('img').forEach(img => {
				img.addEventListener('error', function() {
					this.src = '../images/comingsoon.png';
					this.alt = 'Image not available';
				});
			});
			
			// Add transition for image changes
			const mainImage = document.getElementById('mainImage');
			if (mainImage) {
				mainImage.style.transition = 'opacity 0.3s ease';
			}
			
			// Add active class to current category in navigation
			const currentCategory = '<?php echo htmlspecialchars($category_name); ?>';
			document.querySelectorAll('.nav-link').forEach(link => {
				if (link.textContent.trim() === currentCategory) {
					link.classList.add('active');
				}
			});
		});
	</script>
</body>
</html>
