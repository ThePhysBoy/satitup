<?php
// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($conn) || !($conn instanceof mysqli)) {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    //$db_name = 'school_satitup';
    $db_name = 'satitup';
    $db_port = 3306;
    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    
    if ($conn && !$conn->connect_error) {
        $conn->set_charset('utf8mb4');
    }
}

// ตรวจสอบว่ามีไฟล์ functions.php หรือไม่
if (file_exists(__DIR__ . '/news/functions.php')) {
    require_once __DIR__ . '/news/functions.php';
}

if (!function_exists('satitup_table_exists')) {
    function satitup_table_exists(mysqli $conn, string $table): bool
    {
        $table = $conn->real_escape_string($table);
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        return $result && $result->num_rows > 0;
    }
}

if (!function_exists('satitup_ensure_column')) {
    function satitup_ensure_column(mysqli $conn, string $table, string $column, string $definition): void
    {
        if (!satitup_table_exists($conn, $table)) {
            return;
        }
        $tableEsc = $conn->real_escape_string($table);
        $columnEsc = $conn->real_escape_string($column);
        $check = $conn->query("SHOW COLUMNS FROM `$tableEsc` LIKE '$columnEsc'");
        if ($check && $check->num_rows === 0) {
            $conn->query("ALTER TABLE `$tableEsc` ADD COLUMN `$columnEsc` $definition");
        }
    }
}

if ($conn && !$conn->connect_error) {
    // Ensure hall_of_fame columns
    satitup_ensure_column($conn, 'hall_of_fame', 'views', 'INT UNSIGNED NOT NULL DEFAULT 0');
    satitup_ensure_column($conn, 'hall_of_fame', 'likes', 'INT UNSIGNED NOT NULL DEFAULT 0');
    satitup_ensure_column($conn, 'hall_of_fame', 'sdg_goals', 'VARCHAR(255) DEFAULT NULL');

    // Ensure international_assignments columns
    satitup_ensure_column($conn, 'international_assignments', 'views', 'INT UNSIGNED NOT NULL DEFAULT 0');
    satitup_ensure_column($conn, 'international_assignments', 'likes', 'INT UNSIGNED NOT NULL DEFAULT 0');
    satitup_ensure_column($conn, 'international_assignments', 'sdg_goals', 'VARCHAR(255) DEFAULT NULL');
}

$global_sdg_meta = [
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

if (!function_exists('satitup_render_portfolio_card')) {
    function satitup_render_portfolio_card(array $item, string $detailUrl, array $sdgMeta, string $type, string $title, string $imagePath, string $altText, string $dateText, string $excerpt = '', array $metaLines = []): void
    {
        // ถ้า path ไม่ขึ้นต้นด้วย http, https หรือ / แสดงว่าเป็น relative path
        $imageSrc = $imagePath;
        if (!preg_match('#^(https?://|/)#', $imagePath)) {
            // ถ้าไม่มี / ข้างหน้าและไม่ใช่ URL สมบูรณ์ ให้ใช้แบบ relative จาก root
            $imageSrc = $imagePath;
        }
        $alt = htmlspecialchars($altText, ENT_QUOTES, 'UTF-8');
        $titleHtml = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $excerptHtml = htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8');
        $dateHtml = htmlspecialchars($dateText, ENT_QUOTES, 'UTF-8');
        $itemId = (int)($item['id'] ?? 0);
        $views = isset($item['view_count']) ? (int)$item['view_count'] : (int)($item['views'] ?? 0);
        $likes = (int)($item['likes'] ?? 0);
        $sdgBadgesHtml = '';
        $sdgGoals = [];
        if (!empty($item['sdg_goals'])) {
            $sdgGoals = array_filter(array_map('trim', explode(',', $item['sdg_goals'])));
        }
        foreach ($sdgGoals as $sdg) {
            if ($sdg === '') {
                continue;
            }
            $sdgKey = (int)$sdg;
            if (!isset($sdgMeta[$sdgKey])) {
                continue;
            }
            $sdgInfo = $sdgMeta[$sdgKey];
            $badgeColor = htmlspecialchars($sdgInfo['color'], ENT_QUOTES, 'UTF-8');
            $badgeLabel = htmlspecialchars($sdgInfo['name'], ENT_QUOTES, 'UTF-8');
            $sdgNumber = htmlspecialchars((string)$sdgKey, ENT_QUOTES, 'UTF-8');
            $sdgBadgesHtml .= "<span class=\"sdg-badge\" style=\"background-color: {$badgeColor};\" data-sdg-name=\"{$badgeLabel}\">{$sdgNumber}</span>";
        }

        $metaLinesHtml = '';
        if (!empty($metaLines)) {
            $metaLinesHtml .= '<div class="news-meta-extra">';
            foreach ($metaLines as $line) {
                $metaLinesHtml .= '<div class="news-meta-extra-line">' . $line . '</div>';
            }
            $metaLinesHtml .= '</div>';
        }

        $viewsFormatted = number_format($views);
        $likesFormatted = number_format($likes);
        $detailUrlSafe = htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8');
        $itemTypeSafe = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');

        echo '<div class="portfolio-item isotope-item">';
        echo '<div class="portfolio-content h-100" data-detail-url="' . $detailUrlSafe . '" data-item-type="' . $itemTypeSafe . '" data-item-id="' . $itemId . '" role="link" tabindex="0">';
        echo '<div class="portfolio-image">';
        echo '<img src="' . $imageSrc . '" alt="' . $alt . '" onerror="this.src=\'images/comingsoon.png\'">';
        echo '</div>';
        echo '<div class="portfolio-meta">';
        echo '<div class="news-meta-top">';
        if ($sdgBadgesHtml !== '') {
            echo '<div class="sdg-badges">' . $sdgBadgesHtml . '</div>';
        } else {
            echo '<div class="sdg-badges"></div>';
        }
        echo '<div class="news-activity-date"><i class="fas fa-calendar-alt"></i>' . $dateHtml . '</div>';
        echo '</div>';
        echo '<h3 class="news-activity-title">';
        echo '<a class="news-detail-link" href="' . $detailUrlSafe . '" target="_blank" rel="noopener" data-item-type="' . $itemTypeSafe . '" data-item-id="' . $itemId . '">' . $titleHtml . '</a>';
        echo '</h3>';
        if ($excerptHtml !== '') {
            echo '<div class="news-excerpt" title="' . $excerptHtml . '">' . $excerptHtml . '</div>';
        }
        echo $metaLinesHtml;
        echo '<div class="news-stats mt-2">';
        echo '<span class="news-views" title="จำนวนผู้เข้าชม">';
        echo '<i class="fas fa-eye"></i>';
        echo '<span class="news-views-count" data-count="' . $views . '" data-item-type="' . $itemTypeSafe . '">' . $viewsFormatted . '</span>';
        echo '</span>';
        echo '<button class="news-like-button" type="button" data-item-id="' . $itemId . '" data-item-type="' . $itemTypeSafe . '" title="ถูกใจ" aria-label="ถูกใจข่าวนี้ (จำนวน ' . $likesFormatted . ' ครั้ง)">';
        echo '<i class="far fa-thumbs-up"></i>';
        echo '<span class="news-like-count" data-count="' . $likes . '">' . $likesFormatted . '</span>';
        echo '</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('satitup_trim_text')) {
    function satitup_trim_text(string $text, int $maxChars = 100): string
    {
        $clean = trim(strip_tags($text));
        if ($clean === '') {
            return '';
        }
        if (mb_strlen($clean, 'UTF-8') <= $maxChars) {
            return $clean;
        }
        $truncated = mb_substr($clean, 0, $maxChars, 'UTF-8');
        $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
        if ($lastSpace !== false && $lastSpace > ($maxChars * 0.6)) {
            $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
        }
        return rtrim($truncated, ",.;- ") . '...';
    }
}

if (!function_exists('satitup_prepare_hall_meta_lines')) {
    function satitup_prepare_hall_meta_lines(array $item): array
    {
        $lines = [];
        if (!empty($item['student_name'])) {
            $lines[] = '<i class="fas fa-user-graduate"></i> ' . htmlspecialchars($item['student_name'], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($item['class'])) {
            $lines[] = '<i class="fas fa-school"></i> ' . htmlspecialchars($item['class'], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($item['year'])) {
            $lines[] = '<i class="fas fa-calendar-alt"></i> ปีการศึกษา ' . htmlspecialchars($item['year'], ENT_QUOTES, 'UTF-8');
        }
        return $lines;
    }
}

if (!function_exists('satitup_prepare_international_meta_lines')) {
    function satitup_prepare_international_meta_lines(array $assignment): array
    {
        $lines = [];
        if (!empty($assignment['person_name'])) {
            $lines[] = '<i class="fas fa-user"></i> ' . htmlspecialchars($assignment['person_name'], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($assignment['role'])) {
            $lines[] = '<i class="fas fa-id-badge"></i> ' . htmlspecialchars($assignment['role'], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($assignment['country']) || !empty($assignment['city'])) {
            $location = trim(($assignment['city'] ?? '') . (empty($assignment['city']) || empty($assignment['country']) ? '' : ', ') . ($assignment['country'] ?? ''));
            if ($location !== '') {
                $lines[] = '<i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
            }
        }
        if (!empty($assignment['purpose'])) {
            $lines[] = '<i class="fas fa-lightbulb"></i> ' . htmlspecialchars(satitup_trim_text($assignment['purpose'], 80), ENT_QUOTES, 'UTF-8');
        }
        return $lines;
    }
}

if (!function_exists('satitup_render_hall_item')) {
    function satitup_render_hall_item(array $item, array $sdgMeta): void
    {
        $imagePath = !empty($item['image_path']) ? $item['image_path'] : 'images/comingsoon.png';
        $alt = $item['student_name'] ?: $item['title'];
        $dateText = '';
        if (!empty($item['date_achieved'])) {
            $timestamp = strtotime($item['date_achieved']);
            if ($timestamp) {
                $dateText = date('d/m/Y', $timestamp);
            }
        }
        if ($dateText === '' && !empty($item['created_at'])) {
            $timestamp = strtotime($item['created_at']);
            if ($timestamp) {
                $dateText = date('d/m/Y', $timestamp);
            }
        }
        if ($dateText === '') {
            $dateText = date('d/m/Y');
        }

        $excerptSource = $item['achievement'] ?? $item['description'] ?? '';
        $excerpt = satitup_trim_text($excerptSource, 120);
        $metaLines = satitup_prepare_hall_meta_lines($item);
        $detailUrl = 'hall_of_fame/view.php?id=' . (int)($item['id'] ?? 0);

        satitup_render_portfolio_card(
            $item,
            $detailUrl,
            $sdgMeta,
            'hall',
            $item['title'] ?? '',
            $imagePath,
            $alt,
            $dateText,
            $excerpt,
            $metaLines
        );
    }
}

if (!function_exists('satitup_render_international_item')) {
    function satitup_render_international_item(array $assignment, array $sdgMeta): void
    {
        $imagePath = !empty($assignment['cover_image']) ? $assignment['cover_image'] : 'images/comingsoon.png';
        $alt = $assignment['title'] ?? 'International Assignment';
        $dateText = '';
        if (!empty($assignment['start_date'])) {
            $timestamp = strtotime($assignment['start_date']);
            if ($timestamp) {
                $dateText = date('d/m/Y', $timestamp);
            }
        }
        if ($dateText === '' && !empty($assignment['published_date'])) {
            $timestamp = strtotime($assignment['published_date']);
            if ($timestamp) {
                $dateText = date('d/m/Y', $timestamp);
            }
        }
        if ($dateText === '') {
            $dateText = date('d/m/Y');
        }

        $excerptSource = $assignment['achievement'] ?? $assignment['description'] ?? $assignment['purpose'] ?? '';
        $excerpt = satitup_trim_text($excerptSource, 120);
        $metaLines = satitup_prepare_international_meta_lines($assignment);
        $detailUrl = 'international/view.php?id=' . (int)($assignment['id'] ?? 0);

        satitup_render_portfolio_card(
            $assignment,
            $detailUrl,
            $sdgMeta,
            'international',
            $assignment['title'] ?? '',
            $imagePath,
            $alt,
            $dateText,
            $excerpt,
            $metaLines
        );
    }
}

if (!isset($latest_news) || empty($latest_news)) {
    if (function_exists('getLatestNews')) {
        $latest_news = getLatestNews($conn, 15, 0); // ดึงข่าวทั้งหมดโดยไม่สนใจหมวดหมู่
    } else {
        $latest_news = [];
        if ($conn && !$conn->connect_error) {
            $stmt = $conn->prepare("SELECT n.id, n.title, n.slug, n.excerpt, n.published_at, n.created_at, n.featured_image, n.views as view_count, n.likes, n.sdg_goals, u.full_name, u.username
                                    FROM news n
                                    LEFT JOIN users u ON n.author_id = u.id
                                    WHERE n.status = 'published'
                                    ORDER BY COALESCE(n.published_at, n.created_at) DESC
                                    LIMIT 15");
            if ($stmt) {
                $stmt->execute();
                $res = $stmt->get_result();
                $latest_news = $res->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            }
        }
    }
}

// ดึงข้อมูลเอกสารราชการจากฐานข้อมูล
$official_docs = [
    'regulation' => [],
    'rule' => [],
    'announcement' => [],
    'order' => []
];

if ($conn && !$conn->connect_error) {
    // ตรวจสอบว่ามีตารางหรือไม่
    $table_check = $conn->query("SHOW TABLES LIKE 'official_documents'");
    if ($table_check && $table_check->num_rows > 0) {
        // ดึงเอกสารแต่ละประเภท
        $types = ['regulation', 'rule', 'announcement', 'order'];
        foreach ($types as $type) {
            $stmt = $conn->prepare("SELECT d.*, c.category_name 
                                   FROM official_documents d
                                   LEFT JOIN official_documents_categories c ON d.category_id = c.id
                                   WHERE d.doc_type = ? AND d.status = 'active'
                                   ORDER BY d.publish_date DESC, d.created_at DESC
                                   LIMIT 5");
            if ($stmt) {
                $stmt->bind_param("s", $type);
                $stmt->execute();
                $res = $stmt->get_result();
                $official_docs[$type] = $res->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            }
        }
    }
}

// สำหรับการเข้ากันได้กับโค้ดเดิม
if (!isset($announcements) || empty($announcements)) {
    $announcements = $official_docs['announcement'];
}

$latest = $latest_news ?? [];

$news_items = [];
if (!empty($latest)) {
    foreach ($latest as $item) {
        // Fix image path
        $cover = '';
        if (!empty($item['featured_image'])) {
            $cover = $item['featured_image'];
            // Remove leading slash if exists
            $cover = ltrim($cover, '/');
            // Remove admin/ prefix if exists
            if (strpos($cover, 'admin/') === 0) {
                $cover = substr($cover, 6);
            }
        } else if (!empty($item['gallery_image'])) {
            $cover = $item['gallery_image'];
            $cover = ltrim($cover, '/');
            if (strpos($cover, 'admin/') === 0) {
                $cover = substr($cover, 6);
            }
        } else {
            $cover = 'images/comingsoon.png';
        }

        $displayDate = $item['display_date'] ?? ($item['published_at'] ?? $item['created_at']);

        // ดึงข้อมูลยอดวิวและไลค์
        $views = $item['view_count'] ?? $item['views'] ?? 0;
        $likes = $item['likes'] ?? 0;

        $item['cover_image'] = $cover;
        $item['display_date'] = $displayDate;
        $item['views'] = $views;
        $item['likes'] = $likes;
        $item['sdg_goals'] = $item['sdg_goals'] ?? '';
        $news_items[] = $item;
    }
}

$procurements = [];
if ($conn && !$conn->connect_error) {
    $table_check = $conn->query("SHOW TABLES LIKE 'procurement_announcements'");
    if ($table_check && $table_check->num_rows > 0) {
        $sql = "SELECT id, title, reference_number, procurement_method, department, published_date, closing_date, document_pdf, status
                FROM procurement_announcements
                WHERE status IN ('published','closed')
                ORDER BY published_date DESC
                LIMIT 15";
        $result = $conn->query($sql);
        if ($result) {
            $procurements = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
}

$recruitments = [];
if ($conn && !$conn->connect_error) {
    $table_check = $conn->query("SHOW TABLES LIKE 'recruitment_announcements'");
    if ($table_check && $table_check->num_rows > 0) {
        $sql = "SELECT id, title, position_title, reference_number, department, employment_type, published_date, application_deadline, status
                FROM recruitment_announcements
                WHERE status IN ('open','closed')
                ORDER BY published_date DESC
                LIMIT 15";
        $result = $conn->query($sql);
        if ($result) {
            $recruitments = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
}

$trainings = [];
if ($conn && !$conn->connect_error) {
    $table_check = $conn->query("SHOW TABLES LIKE 'training_announcements'");
    if ($table_check && $table_check->num_rows > 0) {
        $sql = "SELECT id, title, training_topic, reference_number, host_department, training_type, published_date, registration_deadline, status
                FROM training_announcements
                WHERE status IN ('open','closed')
                ORDER BY published_date DESC
                LIMIT 15";
        $result = $conn->query($sql);
        if ($result) {
            $trainings = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
}

$international_assignments = [];
if ($conn && !$conn->connect_error) {
    $table_check = $conn->query("SHOW TABLES LIKE 'international_assignments'");
    if ($table_check && $table_check->num_rows > 0) {
        $sql = "SELECT id, title, person_name, role, affiliation, country, city, start_date, end_date, cover_image,
                       views, likes, sdg_goals, purpose, achievement, description, published_date, created_at
                FROM international_assignments
                WHERE status = 'published'
                ORDER BY created_at DESC, published_date DESC
                LIMIT 15";
        $result = $conn->query($sql);
        if ($result) {
            $international_assignments = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
}
?>

<!-- เริ่มต้นส่วนข่าวสารและประกาศ -->
<section class="news-announcements-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title" style="font-size: 32px; font-weight: 500; color: #333; margin-bottom: 12px;">กิจกรรมประชาสัมพันธ์ / ข่าวสาร</h2>
            <p class="section-subtitle" style="font-size: 16px; color: #666;">Activities / News</p>
        </div>
        
        <!-- สไตล์สำหรับรายการประกาศ -->
        <style>
        /* Portfolio Grid Styles */
        .news-portfolio .portfolio-filters {
            padding: 0;
            margin: 0 0 30px 0;
            list-style: none;
            text-align: center;
            border-bottom: 1px solid #e8e8e8;
            padding-bottom: 15px;
        }
        
        .news-portfolio .portfolio-filters li {
            cursor: pointer;
            display: inline-block;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 400;
            line-height: 1;
            color: #666;
            margin: 0 2px;
            transition: all 0.2s ease;
            border-radius: 0;
            border: none;
            background: none;
            position: relative;
        }
        
        .news-portfolio .portfolio-filters li:hover {
            color: #0066cc;
        }
        
        .news-portfolio .portfolio-filters li.filter-active {
            color: #0066cc;
            font-weight: 500;
        }
        
        .news-portfolio .portfolio-filters li.filter-active::after {
            content: "";
            position: absolute;
            bottom: -16px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0066cc;
        }
        
        .news-portfolio .portfolio-filters li::before {
            content: "";
            margin: 0 8px;
            color: #ddd;
        }
        
        .news-portfolio .portfolio-filters li:first-child::before {
            display: none;
        }
        
        .news-portfolio .portfolio-filters li:not(:first-child)::before {
            content: "|";
            position: absolute;
            left: -5px;
            color: #ddd;
        }
        
        .news-portfolio .portfolio-item {
            margin-bottom: 30px;
        }
        
        .news-portfolio .portfolio-content {
            position: relative;
            overflow: visible;
            border-radius: 12px;
            background: #fff;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            cursor: pointer;
        }

        .portfolio-content[data-detail-url] {
            cursor: pointer;
        }

        .portfolio-content[data-detail-url]:focus {
            outline: 3px solid rgba(139, 122, 168, 0.6);
            outline-offset: 4px;
        }
        
        .news-portfolio .portfolio-content:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.15);
            border-color: #0066cc;
        }
        
        /* ใช้สำหรับทุกส่วน: ข่าว, หอเกียรติยศ, กิจกรรมต่างประเทศ */
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
        
        /* Remove overlay - keep it simple */
        .news-portfolio .portfolio-info {
            display: none;
        }
        
        .news-activity-date {
            position: static;
            background: transparent;
            color: #333;
            padding: 0;
            border-radius: 0;
            font-weight: 500;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-top: 8px;
            margin-left: auto;
        }

        .news-activity-date i {
            color: #666;
            font-size: 11px;
        }
        
        /* SDG Badges Styles */
        .news-meta-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
            width: 100%;
        }

        .sdg-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: flex-start;
        }

        .sdg-badges:empty {
            display: none;
        }

        .news-meta-top .sdg-badges {
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
            cursor: pointer;
        }
        
        .sdg-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            opacity: 0.9;
        }

        .sdg-badge::after {
            content: attr(data-sdg-name);
            position: absolute;
            top: calc(100% + 6px);
            left: 50%;
            transform: translate(-50%, -6px);
            background: rgba(33, 33, 33, 0.92);
            color: #fff;
            padding: 3px 6px;
            border-radius: 5px;
            font-size: 9px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 20;
        }

        .sdg-badge::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translate(-50%, -4px);
            border-width: 6px 6px 0 6px;
            border-style: solid;
            border-color: rgba(33, 33, 33, 0.92) transparent transparent transparent;
            opacity: 0;
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 19;
        }

        .sdg-badge:hover::after,
        .sdg-badge:hover::before {
            opacity: 1;
            transform: translate(-50%, 0);
        }
        
        .news-category-badge {
            display: inline-block;
            background: #f0f0f0;
            color: #666;
            font-size: 9.6px;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
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
            min-height: 60.8px;
            color: #333;
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
            display: inline-flex;
            align-items: center;
            gap: 4px;
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

        .news-meta-extra {
            margin-top: 8px;
            font-size: 11px;
            font-weight: 300;
            color: #555;
            line-height: 1.5;
        }

        .news-meta-extra-line + .news-meta-extra-line {
            margin-top: 4px;
        }

        .news-stats {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
        }
        
        /* ปรับขนาดไอคอนดวงตาและไลค์ */
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
            transform: translateY(-1px);
        }

        .news-like-button.liked {
            color: #1877f2;
        }

        .news-like-button.liked i {
            font-weight: 900;
        }

        .news-like-button.loading {
            opacity: 0.5;
            pointer-events: none;
        }

        .news-like-count,
        .news-views-count {
            min-width: 9.6px;
        }
        
.portfolio-meta {
    padding: 15px;
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 100px;
    background: #fff;
    position: relative;
}
        
        .portfolio-meta .text-muted {
            font-size: 13px;
        }
        
        .portfolio-meta-content {
            flex: 1 1 auto;
        }
        
        .portfolio-meta-footer {
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
            display: none;
        }
        
        /* สไตล์สำหรับเมนูย่อย */
        .announcement-submenu .nav-link {
            color: #333;
            font-weight: 500;
            border-radius: 20px;
            padding: 8px 15px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .announcement-submenu .nav-link.active {
            background-color: #7b3b95;
            color: #fff;
        }
        
        .announcement-submenu .nav-link:hover:not(.active) {
            background-color: #f0f0f0;
        }
        
        /* สไตล์สำหรับรายการประกาศ */
        .announcement-list .card {
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .announcement-list .list-group-item {
            padding: 18px 25px;
            border-left: 3px solid transparent;
            border-right: none;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .announcement-list .list-group-item:hover {
            background: linear-gradient(90deg, #f8f5ff 0%, #ffffff 100%);
            border-left-color: #7b3b95;
            transform: translateX(5px);
        }
        
        .announcement-list .list-group-item:nth-child(even) {
            background-color: #fafafa;
        }
        
        .announcement-list .list-group-item:last-child {
            border-bottom: none;
        }
        
        .announcement-list .list-group-item:first-child {
            border-top: none;
        }
        
        .announcement-list .list-group-item:last-child {
            border-bottom: none;
        }
        
        .announcement-title {
            color: #333;
            font-weight: 500;
            text-decoration: none;
            display: block;
            margin-bottom: 5px;
            transition: color 0.3s ease;
        }
        
        .announcement-title:hover {
            color: #7b3b95;
        }
        
        .announcement-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .announcement-author {
            color: #888;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        /* แอนิเมชันสำหรับรายการ */
        .list-group-item.animate-in {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Custom grid for 5 columns */
        .custom-news-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5rem;
        }

        .custom-news-grid .portfolio-item {
            width: 100%;
            margin-bottom: 0;
        }

        @media (max-width: 1200px) {
            .custom-news-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .custom-news-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .custom-news-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ปรับการแสดงผลบนอุปกรณ์มือถือ */
        @media (max-width: 767px) {
            .news-activity-title {
                font-size: 12px;
                font-weight: 400;
                min-height: 48px;
            }

            .portfolio-meta {
                padding: 10px;
                min-height: 72px;
            }

        .news-excerpt {
            font-size: 11px;
            font-weight: 300;
        }

        .news-stats {
            font-size: 12px;
                gap: 6px;
            }

            .news-stats .news-views,
            .news-stats .news-like-button {
                gap: 2.6px;
            }

            .news-stats .news-views i,
            .news-stats .news-like-button i {
                font-size: 12.2px;
        }

        .news-portfolio .portfolio-filters li {
                font-size: 10.4px;
                padding: 6px 10px;
            }
            
            .news-activity-date {
                font-size: 11px;
                padding: 0;
            }
            .news-meta-top {
                flex-wrap: wrap;
                gap: 5px;
            }
            .news-meta-top .sdg-badges {
                flex-basis: 100%;
            }
            .news-meta-top .news-activity-date {
                margin-left: 0;
            }
            
            .news-category-badge {
                font-size: 8.8px;
                padding: 3px 8px;
            }
        }

        /* ============================================= */
        /* GOLDEN EFFECT STYLES FOR HALL OF FAME CARDS */
        /* ============================================= */

        /* Golden effect for hall of fame cards */
        .hall-of-fame-card {
            position: relative;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Golden animated border - placed outside card */
        .hall-of-fame-card::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: calc(0.375rem + 3px);
            background: linear-gradient(45deg,
                #FF6B35, #F7931E, #FDC830, #FFD700,
                #FFA500, #FF6347, #FF6B35
            );
            background-size: 400% 400%;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: gradient-animation 3s ease infinite;
            z-index: -1;
        }

        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Show golden border on hover */
        .hall-of-fame-card:hover::before {
            opacity: 1;
        }

        /* Shine sweep effect */
        .hall-of-fame-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 215, 0, 0.5) 50%,
                transparent 70%
            );
            transform: rotate(45deg) translateX(-100%);
            transition: transform 0.6s;
            opacity: 0;
            pointer-events: none;
        }

        .hall-of-fame-card:hover::after {
            transform: rotate(45deg) translateX(100%);
            opacity: 1;
        }

        /* Main hover transformation */
        .hall-of-fame-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow:
                0 20px 40px rgba(255, 107, 53, 0.4),
                0 0 60px rgba(255, 165, 0, 0.3),
                0 0 100px rgba(255, 215, 0, 0.2);
            animation: glow-pulse 2s ease-in-out infinite;
        }

        /* Glow pulse animation */
        @keyframes glow-pulse {
            0%, 100% {
                box-shadow:
                    0 20px 40px rgba(255, 107, 53, 0.4),
                    0 0 60px rgba(255, 165, 0, 0.3),
                    0 0 100px rgba(255, 215, 0, 0.2);
            }
            50% {
                box-shadow:
                    0 25px 50px rgba(255, 107, 53, 0.6),
                    0 0 80px rgba(255, 165, 0, 0.5),
                    0 0 120px rgba(255, 215, 0, 0.3);
            }
        }

        /* Image effects */
        .hall-of-fame-card .card-img-top {
            transition: all 0.5s ease;
            filter: brightness(0.95);
        }

        .hall-of-fame-card:hover .card-img-top {
            filter: brightness(1.2) saturate(1.3);
            transform: scale(1.05);
        }

        /* Text golden gradient on hover */
        .hall-of-fame-card .card-title {
            transition: all 0.5s ease;
        }

        .hall-of-fame-card:hover .card-title {
            background: linear-gradient(45deg, #FF6B35, #FFA500, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transform: scale(1.05);
        }

        .hall-of-fame-card:hover .card-text small {
            background: linear-gradient(45deg, #FF6B35, #FFA500, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Button glow on hover */
        .hall-of-fame-card:hover .btn-primary {
            box-shadow: 0 0 20px rgba(255, 165, 0, 0.5);
            transform: scale(1.05);
        }
        </style>

        <!-- สไตล์เพิ่มเติมสำหรับแท็บเมนู -->
        <style>
        /* ปรับแต่งแท็บเมนูให้สวยงามและเป็นระเบียบ */
        .news-tabs-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem !important;
        }

        .news-tabs {
            border: none;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 8px;
            gap: 4px;
        }

        .news-tabs .nav-link {
            border: none !important;
            border-radius: 8px !important;
            color: #495057;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 10px 16px;
            margin: 2px;
            transition: all 0.3s ease;
            background: transparent;
            white-space: nowrap;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .news-tabs .nav-link:hover {
            background: rgba(255,255,255,0.8);
            color: #007bff;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,123,255,0.15);
        }

        .news-tabs .nav-link.active {
            background: #fff !important;
            color: #007bff !important;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }

        .news-tabs .nav-link i {
            font-size: 1rem;
            opacity: 0.8;
        }

        .news-tabs .nav-link.active i {
            color: #007bff;
            opacity: 1;
        }

        /* Responsive design สำหรับแท็บ */
        @media (max-width: 1199px) {
            .news-tabs {
                gap: 2px;
            }

            .news-tabs .nav-link {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 991px) {
            .news-tabs-wrapper {
                margin-bottom: 1.5rem !important;
            }

            .news-tabs {
                padding: 6px;
                gap: 1px;
            }

            .news-tabs .nav-link {
                padding: 6px 10px;
                font-size: 0.8rem;
                min-height: 40px;
            }

            .news-tabs .nav-link i {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 767px) {
            .news-tabs-wrapper {
                margin-bottom: 1rem !important;
            }

            .news-tabs {
                padding: 4px;
                gap: 0;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }

            .news-tabs .nav-link {
                padding: 8px 6px;
                font-size: 0.75rem;
                min-height: 36px;
                text-align: center;
            }

            .news-tabs .nav-link i {
                display: block;
                margin-bottom: 2px;
                font-size: 1rem;
            }

            .news-tabs .nav-link span {
                font-size: 0.7rem !important;
                line-height: 1.2;
            }
        }

        @media (max-width: 575px) {
            .news-tabs {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            }

            .news-tabs .nav-link {
                padding: 6px 4px;
                font-size: 0.7rem;
            }

            .news-tabs .nav-link i {
                font-size: 0.9rem;
            }

            .news-tabs .nav-link span {
                display: none;
            }

            .news-tabs .nav-link i {
                margin-bottom: 0;
            }
        }

        /* เพิ่มลูกเล่นให้แท็บดูมีมิติ */
        .news-tabs-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(0,123,255,0.2) 50%, transparent 100%);
        }

        .news-tabs .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #007bff;
            transition: width 0.3s ease;
        }

        .news-tabs .nav-link.active::after {
            width: 60%;
        }

        /* Animation สำหรับแท็บที่ active */
        @keyframes tabPulse {
            0% { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            50% { box-shadow: 0 4px 12px rgba(0,123,255,0.3); }
            100% { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        }

        .news-tabs .nav-link.active {
            animation: tabPulse 2s ease-in-out infinite;
        }
        </style>

        <!-- แถบเมนู Tab สำหรับเลือกประเภทข่าวสาร -->
        <div class="news-tabs-wrapper mb-4">
            <ul class="nav nav-tabs news-tabs justify-content-center flex-wrap" id="newsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="news-activities-tab" data-bs-toggle="tab" data-bs-target="#news-activities" type="button" role="tab">
                        <i class="fas fa-camera"></i>
                        <span class="d-none d-sm-inline">ภาพข่าวกิจกรรม</span>
                        <span class="d-sm-none">ภาพข่าว</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                        <i class="fas fa-file-signature"></i>
                        <span class="d-none d-md-inline">คำสั่งและประกาศ</span>
                        <span class="d-md-none">คำสั่ง</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="procurement-tab" data-bs-toggle="tab" data-bs-target="#procurement" type="button" role="tab">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="d-none d-lg-inline">การจัดซื้อจัดจ้าง</span>
                        <span class="d-lg-none">จัดซื้อ</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="recruitment-tab" data-bs-toggle="tab" data-bs-target="#recruitment" type="button" role="tab">
                        <i class="fas fa-user-tie"></i>
                        <span class="d-none d-md-inline">การรับสมัครงาน</span>
                        <span class="d-md-none">สมัครงาน</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="training-tab" data-bs-toggle="tab" data-bs-target="#training" type="button" role="tab">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span class="d-none d-lg-inline">การอบรม</span>
                        <span class="d-lg-none">อบรม</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="competition-tab" data-bs-toggle="tab" data-bs-target="#competition" type="button" role="tab">
                        <i class="fas fa-trophy"></i>
                        <span class="d-none d-md-inline">หอเกียรติยศ</span>
                        <span class="d-md-none">เกียรติยศ</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="international-tab" data-bs-toggle="tab" data-bs-target="#international" type="button" role="tab">
                        <i class="fas fa-globe"></i>
                        <span class="d-none d-lg-inline">กิจกรรมต่างประเทศ  
                        <span class="d-lg-none">กิจกรรมต่างประเทศ</span>
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- เนื้อหาของแต่ละแท็บ -->
        <div class="tab-content" id="newsTabContent">
            <!-- เนื้อหาแท็บ: ภาพข่าวกิจกรรม (แสดงเริ่มต้น) -->
            <div class="tab-pane fade show active" id="news-activities" role="tabpanel">
                <div class="news-portfolio portfolio">
                    <div class="isotope-layout" data-layout="fitRows" data-default-filter="*">
<?php if (!empty($news_items)): ?>
                        <!-- ลบตัวกรองหมวดหมู่ออก เพราะเราไม่ใช้หมวดหมู่แล้ว -->

                        <div class="isotope-container custom-news-grid">
<?php foreach ($news_items as $item): ?>
<?php
    $detailUrl = !empty($item['slug'])
        ? 'news/detail.php?slug=' . urlencode($item['slug'])
        : 'news/detail.php?id=' . $item['id'];
?>
                            <div class="portfolio-item isotope-item">
                                <div class="portfolio-content h-100" data-detail-url="<?php echo htmlspecialchars($detailUrl); ?>" data-item-id="<?php echo (int)$item['id']; ?>" data-item-type="news" role="link" tabindex="0">
                                    <div class="portfolio-image">
                                        <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($item['title']); ?>" onerror="this.src='images/comingsoon.png'">
                                    </div>
                                    <div class="portfolio-meta">
                                        <div class="news-meta-top">
                                            <?php if (!empty($item['sdg_goals'])): ?>
                                            <div class="sdg-badges">
                                            <?php
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
                                            $sdgs = explode(',', $item['sdg_goals']);
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
                                            <?php endif; ?>
                                            <div class="news-activity-date">
                                                <i class="fas fa-calendar-alt"></i>
                                                <?php echo date('d/m/Y', strtotime($item['display_date'])); ?>
                                            </div>
                                        </div>
                                        <h3 class="news-activity-title">
                                            <a class="news-detail-link" href="<?php echo htmlspecialchars($detailUrl); ?>" data-item-id="<?php echo (int)$item['id']; ?>" data-item-type="news" target="_blank" rel="noopener">
                                                <?php echo htmlspecialchars($item['title']); ?>
                                            </a>
                                        </h3>
                                        <?php if (!empty($item['excerpt'])): ?>
                                        <?php
                                            $rawExcerpt = trim(strip_tags($item['excerpt']));
                                            $maxChars = 100;
                                            if (mb_strlen($rawExcerpt, 'UTF-8') > $maxChars) {
                                                $truncated = mb_substr($rawExcerpt, 0, $maxChars, 'UTF-8');
                                                // ตัดคำให้จบที่วรรคเพื่อไม่ให้ขาดกลางคำภาษาอังกฤษ
                                                $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
                                                if ($lastSpace !== false) {
                                                    $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
                                                }
                                                $rawExcerpt = rtrim($truncated, ",.;-") . '...';
                                            }
                                            $displayExcerpt = $rawExcerpt;
                                        ?>
                                        <div class="news-excerpt" title="<?php echo htmlspecialchars($displayExcerpt); ?>">
                                            <?php echo htmlspecialchars($displayExcerpt); ?>
                                        </div>
                                        <?php endif; ?>
                                        <!-- แสดงยอดวิวและไลค์ -->
                                        <div class="news-stats mt-2">
                                            <span class="news-views" title="จำนวนผู้เข้าชม">
                                                <i class="fas fa-eye"></i>
                                                <span class="news-views-count" data-count="<?php echo isset($item['view_count']) ? (int)$item['view_count'] : (isset($item['views']) ? (int)$item['views'] : 0); ?>" data-item-type="news"><?php echo number_format(isset($item['view_count']) ? (int)$item['view_count'] : (isset($item['views']) ? (int)$item['views'] : 0)); ?></span>
                                            </span>
                                            <button class="news-like-button" type="button" data-item-id="<?php echo $item['id']; ?>" data-item-type="news" title="ถูกใจ" aria-label="ถูกใจข่าวนี้ (จำนวน <?php echo number_format($item['likes']); ?> ครั้ง)">
                                                <i class="far fa-thumbs-up"></i>
                                                <span class="news-like-count" data-count="<?php echo (int)$item['likes']; ?>"><?php echo number_format($item['likes']); ?></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
<?php endforeach; ?>
                        </div>
<?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            ยังไม่มีข่าวเผยแพร่ หรือกรุณาตรวจสอบการเชื่อมต่อฐานข้อมูล
                            <br><small>ลองเข้า <a href="test_database.php" target="_blank">test_database.php</a> เพื่อตรวจสอบฐานข้อมูล</small>
                        </div>
<?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="news/index.php" class="btn btn-view-all">
                        ข่าวทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- เนื้อหาแท็บ: คำสั่งและประกาศ -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="announcement-submenu mb-4">
                    <ul class="nav nav-pills justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="#regulations" data-bs-toggle="pill">
                                <i class="fas fa-gavel"></i> ข้อบังคับ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#rules" data-bs-toggle="pill">
                                <i class="fas fa-clipboard-list"></i> ระเบียบ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#announcement" data-bs-toggle="pill">
                                <i class="fas fa-bullhorn"></i> ประกาศ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#order" data-bs-toggle="pill">
                                <i class="fas fa-file-alt"></i> คำสั่ง
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content">
                    <!-- ข้อบังคับ -->
                    <div class="tab-pane fade show active" id="regulations">
                        <div class="announcement-list">
                            <div class="card">
                                <div class="card-body p-0">
                                    <?php if (!empty($official_docs['regulation'])): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($official_docs['regulation'] as $doc): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-start">
                                                        <a href="official_documents/view.php?id=<?php echo $doc['id']; ?>" class="announcement-title text-decoration-none">
                                                            <?php if ($doc['file_path']): ?>
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-file-alt text-secondary me-2"></i>
                                                            <?php endif; ?>
                                                            <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                                        </a>
                                                        <?php if ($doc['doc_number']): ?>
                                                            <span class="badge bg-secondary ms-2" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['doc_number']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($doc['publisher_name']); ?></small>
                                                    <small class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['publisher_position']); ?></small>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <div class="p-5 text-center">
                                        <i class="fas fa-gavel fa-3x text-muted mb-3 opacity-50"></i>
                                        <h5 class="text-muted">ยังไม่มีข้อบังคับ</h5>
                                        <p class="text-muted small">กำลังรอการเผยแพร่</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ระเบียบ -->
                    <div class="tab-pane fade" id="rules">
                        <div class="announcement-list">
                            <div class="card">
                                <div class="card-body p-0">
                                    <?php if (!empty($official_docs['rule'])): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($official_docs['rule'] as $doc): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-start">
                                                        <a href="official_documents/view.php?id=<?php echo $doc['id']; ?>" class="announcement-title text-decoration-none">
                                                            <?php if ($doc['file_path']): ?>
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-file-alt text-secondary me-2"></i>
                                                            <?php endif; ?>
                                                            <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                                        </a>
                                                        <?php if ($doc['doc_number']): ?>
                                                            <span class="badge bg-secondary ms-2" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['doc_number']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($doc['publisher_name']); ?></small>
                                                    <small class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['publisher_position']); ?></small>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <div class="p-4 text-center">
                                        <i class="fas fa-clipboard-list fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">ยังไม่มีระเบียบ</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ประกาศ -->
                    <div class="tab-pane fade" id="announcement">
                        <div class="announcement-list">
                            <div class="card">
                                <div class="card-body p-0">
                                    <?php if (!empty($official_docs['announcement'])): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($official_docs['announcement'] as $doc): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-start">
                                                        <a href="official_documents/view.php?id=<?php echo $doc['id']; ?>" class="announcement-title text-decoration-none">
                                                            <?php if ($doc['file_path']): ?>
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-file-alt text-secondary me-2"></i>
                                                            <?php endif; ?>
                                                            <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                                        </a>
                                                        <?php if ($doc['doc_number']): ?>
                                                            <span class="badge bg-secondary ms-2" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['doc_number']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($doc['publisher_name']); ?></small>
                                                    <small class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['publisher_position']); ?></small>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <div class="p-4 text-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        ยังไม่มีประกาศ
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- คำสั่ง -->
                    <div class="tab-pane fade" id="order">
                        <div class="announcement-list">
                            <div class="card">
                                <div class="card-body p-0">
                                    <?php if (!empty($official_docs['order'])): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($official_docs['order'] as $doc): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-start">
                                                        <a href="official_documents/view.php?id=<?php echo $doc['id']; ?>" class="announcement-title text-decoration-none">
                                                            <?php if ($doc['file_path']): ?>
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-file-alt text-secondary me-2"></i>
                                                            <?php endif; ?>
                                                            <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                                        </a>
                                                        <?php if ($doc['doc_number']): ?>
                                                            <span class="badge bg-secondary ms-2" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['doc_number']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($doc['publisher_name']); ?></small>
                                                    <small class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($doc['publisher_position']); ?></small>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <div class="p-4 text-center">
                                        <i class="fas fa-file-alt fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">ยังไม่มีคำสั่ง</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="news/announcements.php" class="btn btn-view-all">
                        ดูทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- แท็บอื่นๆ -->
            <div class="tab-pane fade" id="procurement" role="tabpanel">
                <div class="announcement-list">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>ประกาศจัดซื้อจัดจ้างล่าสุด</h5>
                            <a href="procurements/index.php" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">ดูทั้งหมด</a>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($procurements)): ?>
                                <?php foreach ($procurements as $item): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="procurements/view.php?id=<?php echo $item['id']; ?>" class="text-decoration-none" target="_blank" rel="noopener">
                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                </a>
                                            </h6>
                                            <div class="text-muted">
                                                <small><i class="fas fa-hashtag me-1"></i>เลขที่อ้างอิง: <?php echo htmlspecialchars($item['reference_number'] ?? '-'); ?></small>
                                                <small class="ms-3"><i class="fas fa-building me-1"></i><?php echo htmlspecialchars($item['department'] ?? ''); ?></small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div><span class="badge bg-<?php echo ($item['status'] === 'closed') ? 'secondary' : 'success'; ?>"><?php echo ($item['status'] === 'closed') ? 'สิ้นสุดแล้ว' : 'เปิดรับเสนอราคา'; ?></span></div>
                                            <small class="text-muted d-block">ประกาศ: <?php echo date('d/m/Y', strtotime($item['published_date'])); ?></small>
                                            <small class="text-muted d-block">หมดเขต: <?php echo date('d/m/Y', strtotime($item['closing_date'])); ?></small>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>ยังไม่มีประกาศจัดซื้อจัดจ้างในขณะนี้
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="recruitment" role="tabpanel">
                <div class="announcement-list">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>ประกาศรับสมัครงานล่าสุด</h5>
                            <a href="recruitments/index.php" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">ดูทั้งหมด</a>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($recruitments)): ?>
                                <?php foreach ($recruitments as $item): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="recruitments/view.php?id=<?php echo $item['id']; ?>" class="text-decoration-none" target="_blank" rel="noopener">
                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                </a>
                                            </h6>
                                            <div class="text-muted">
                                                <small><i class="fas fa-briefcase me-1"></i><?php echo htmlspecialchars($item['position_title'] ?? '-'); ?></small>
                                                <small class="ms-3"><i class="fas fa-building me-1"></i><?php echo htmlspecialchars($item['department'] ?? ''); ?></small>
                                                <small class="ms-3"><i class="fas fa-id-card me-1"></i>เลขอ้างอิง: <?php echo htmlspecialchars($item['reference_number'] ?? '-'); ?></small>
                                            </div>
                                            <?php if (!empty($item['employment_type'])): ?>
                                            <div><small class="badge bg-secondary">รูปแบบงาน: <?php echo htmlspecialchars($item['employment_type']); ?></small></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <?php
                                            $status = $item['status'];
                                            $labelClass = $status === 'open' ? 'success' : 'secondary';
                                            $labelText = $status === 'open' ? 'เปิดรับสมัคร' : 'ปิดรับสมัคร';
                                            ?>
                                            <span class="badge bg-<?php echo $labelClass; ?>"><?php echo $labelText; ?></span>
                                            <small class="text-muted d-block">ประกาศ: <?php echo date('d/m/Y', strtotime($item['published_date'])); ?></small>
                                            <small class="text-muted d-block">ปิดรับ: <?php echo date('d/m/Y', strtotime($item['application_deadline'])); ?></small>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>ยังไม่มีประกาศรับสมัครงานในขณะนี้
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="training" role="tabpanel">
                <div class="announcement-list">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>ประกาศอบรมล่าสุด</h5>
                            <a href="trainings/index.php" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">ดูทั้งหมด</a>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($trainings)): ?>
                                <?php foreach ($trainings as $item): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="trainings/view.php?id=<?php echo $item['id']; ?>" class="text-decoration-none" target="_blank" rel="noopener">
                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                </a>
                                            </h6>
                                            <div class="text-muted">
                                                <small><i class="fas fa-book-open me-1"></i><?php echo htmlspecialchars($item['training_topic'] ?? '-'); ?></small>
                                                <small class="ms-3"><i class="fas fa-building me-1"></i><?php echo htmlspecialchars($item['host_department'] ?? ''); ?></small>
                                                <small class="ms-3"><i class="fas fa-id-card me-1"></i>เลขอ้างอิง: <?php echo htmlspecialchars($item['reference_number'] ?? '-'); ?></small>
                                            </div>
                                            <?php if (!empty($item['training_type'])): ?>
                                            <div><small class="badge bg-secondary">รูปแบบอบรม: <?php echo htmlspecialchars($item['training_type']); ?></small></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <?php
                                            $status = $item['status'];
                                            $badge = $status === 'open' ? 'success' : ($status === 'closed' ? 'secondary' : 'warning');
                                            $label = $status === 'open' ? 'เปิดรับสมัคร' : ($status === 'closed' ? 'สิ้นสุดการรับสมัคร' : 'รอดำเนินการ');
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>"><?php echo $label; ?></span>
                                            <small class="text-muted d-block">ประกาศ: <?php echo date('d/m/Y', strtotime($item['published_date'])); ?></small>
                                            <?php if (!empty($item['registration_deadline'])): ?>
                                            <small class="text-muted d-block">ปิดรับ: <?php echo date('d/m/Y', strtotime($item['registration_deadline'])); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>ยังไม่มีประกาศอบรมในขณะนี้
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="text-end mt-3">
                        <a href="trainings/index.php" class="btn btn-outline-primary" target="_blank" rel="noopener">ดูประกาศอบรมทั้งหมด</a>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="competition" role="tabpanel">
                <!-- หมวดหมู่ย่อยหอเกียรติยศ -->
                <div class="announcement-submenu mb-4">
                    <ul class="nav nav-pills justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="#hall-academic" data-bs-toggle="pill">
                                <i class="fas fa-graduation-cap"></i> วิชาการ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#hall-sports" data-bs-toggle="pill">
                                <i class="fas fa-trophy"></i> กีฬา
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#hall-music" data-bs-toggle="pill">
                                <i class="fas fa-music"></i> ดนตรี
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#hall-scholarship" data-bs-toggle="pill">
                                <i class="fas fa-award"></i> ทุนการศึกษา
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#hall-outstanding" data-bs-toggle="pill">
                                <i class="fas fa-star"></i> ความโดดเด่น
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- เนื้อหาหอเกียรติยศ -->
                <div class="tab-content">
                    <?php 
                    // ดึงข้อมูลหอเกียรติยศ
                    $hall_categories = ['academic' => 'วิชาการ', 'sports' => 'กีฬา', 'music' => 'ดนตรี', 
                                       'scholarship' => 'ทุนการศึกษา', 'outstanding' => 'ความโดดเด่น'];
                    $hall_data = [];
                    
                    foreach (array_keys($hall_categories) as $category) {
                        $hall_sql = "SELECT id, title, student_name, class, year, achievement, description, image_path, 
                                            date_achieved, category, featured, status, views, likes, sdg_goals, created_at, updated_at
                                     FROM hall_of_fame 
                                    WHERE category = '$category' AND status = 'active' 
                                    ORDER BY created_at DESC, updated_at DESC
                                    LIMIT 15";
                        $hall_result = $conn->query($hall_sql);
                        $hall_data[$category] = [];
                        if ($hall_result) {
                            while ($row = $hall_result->fetch_assoc()) {
                                $hall_data[$category][] = $row;
                            }
                        }
                    }
                    ?>
                    
                    <!-- วิชาการ -->
                    <div class="tab-pane fade show active" id="hall-academic">
                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">
                            <?php if (!empty($hall_data['academic'])): ?>
                                <?php foreach ($hall_data['academic'] as $item): ?>
                                <div class="col">
                                    <?php satitup_render_hall_item($item, $global_sdg_meta); ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="col-12 text-center p-4">
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีข้อมูลรางวัลด้านวิชาการ</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- กีฬา -->
                    <div class="tab-pane fade" id="hall-sports">
                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">
                            <?php if (!empty($hall_data['sports'])): ?>
                                <?php foreach ($hall_data['sports'] as $item): ?>
                                <div class="col">
                                    <?php satitup_render_hall_item($item, $global_sdg_meta); ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="col-12 text-center p-4">
                                <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีข้อมูลรางวัลด้านกีฬา</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- ดนตรี -->
                    <div class="tab-pane fade" id="hall-music">
                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">
                            <?php if (!empty($hall_data['music'])): ?>
                                <?php foreach ($hall_data['music'] as $item): ?>
                                <div class="col">
                                    <?php satitup_render_hall_item($item, $global_sdg_meta); ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="col-12 text-center p-4">
                                <i class="fas fa-music fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีข้อมูลรางวัลด้านดนตรี</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- ทุนการศึกษา -->
                    <div class="tab-pane fade" id="hall-scholarship">
                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">
                            <?php if (!empty($hall_data['scholarship'])): ?>
                                <?php foreach ($hall_data['scholarship'] as $item): ?>
                                <div class="col">
                                    <?php satitup_render_hall_item($item, $global_sdg_meta); ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="col-12 text-center p-4">
                                <i class="fas fa-award fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีข้อมูลทุนการศึกษา</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- ความโดดเด่น -->
                    <div class="tab-pane fade" id="hall-outstanding">
                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">
                            <?php if (!empty($hall_data['outstanding'])): ?>
                                <?php foreach ($hall_data['outstanding'] as $item): ?>
                                <div class="col">
                                    <?php satitup_render_hall_item($item, $global_sdg_meta); ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="col-12 text-center p-4">
                                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีข้อมูลความโดดเด่น</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- ลิงก์ดูทั้งหมด -->
                <div class="text-center mt-4">
                    <a href="hall_of_fame/index.php" class="btn btn-outline-primary">
                        <i class="fas fa-trophy me-2"></i> ดูหอเกียรติยศทั้งหมด
                    </a>
                </div>
                <div class="text-center mt-3">
                    <a href="trainings/index.php" class="btn btn-outline-primary" target="_blank" rel="noopener">ดูทั้งหมด</a>
                </div>
            </div>
            
            <div class="tab-pane fade" id="international" role="tabpanel">
                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">
                    <?php if (!empty($international_assignments)): ?>
                        <?php foreach (array_slice($international_assignments, 0, 15) as $assignment): ?>
                                <div class="col">
                            <?php satitup_render_international_item($assignment, $global_sdg_meta); ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="col-12 text-center p-4">
                    <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                            <p class="text-muted">ยังไม่มีข้อมูลการไปต่างประเทศ</p>
                </div>
                            <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS เพิ่มเติมสำหรับการ์ดข่าว -->
<style>
.news-activity-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    transition: all 0.3s ease;
    background-color: #fff;
    height: 100%;
}

.news-activity-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(123, 59, 149, 0.2);
}

.news-activity-image {
    position: relative;
    overflow: hidden;
    height: 240px;
}

.news-activity-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.news-activity-card:hover .news-activity-image img {
    transform: scale(1.05);
}

.news-activity-date {
    position: static;
    background: transparent;
    color: #333;
    padding: 0;
    border-radius: 0;
    font-weight: 500;
    font-size: 11px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    margin-top: 8px;
    margin-left: auto;
}

.news-activity-date i {
    color: #666;
    font-size: 11px;
}

.news-activity-content {
    padding: 20px;
}

.news-activity-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
    line-height: 1.4;
}

.news-activity-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.news-activity-title a:hover {
    color: #7b3b95;
}

.news-excerpt {
    color: var(--text-medium, #666);
    font-size: 0.85rem; /* ขนาดที่อ่านได้ */
    line-height: 1.6;
    margin-bottom: 15px;
    font-weight: 300; /* ความหนาปานกลาง */
}

.news-activity-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #666;
    font-size: 0.9rem;
    margin-top: 15px;
}

.news-activity-author {
    display: flex;
    align-items: center;
}

.news-activity-author i {
    margin-right: 5px;
    color: #7b3b95;
}

/* Responsive */
@media (max-width: 767px) {
    .news-activity-image {
        height: 180px;
    }
    
    .news-activity-content {
        padding: 15px;
    }
    
    .news-activity-title {
        font-size: 1.1rem;
    }
}

 /* ปุ่มดูทั้งหมด */
 .btn-view-all {
     background: #0066cc;
     color: white;
     border: none;
     padding: 12px 32px;
     border-radius: 6px;
     font-weight: 500;
     font-size: 15px;
     transition: all 0.2s ease;
     text-decoration: none;
     display: inline-block;
 }
 
 .btn-view-all:hover {
     background: #0052a3;
     color: white;
     text-decoration: none;
     transform: translateY(-2px);
     box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
 }
</style>

<!-- JavaScript สำหรับการทำงานของแท็บ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // เริ่มต้น Bootstrap tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#newsTab button'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});
</script>

<!-- สคริปต์สำหรับ Isotope Filtering -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js" integrity="sha512-Zq2BOxyP1hF5CRwN1wP2Gqmtqz87R3U8GkS/EYbHrXlYh0uJp8p1Wn3NE6p3wG2FJ+3I1u2KfDX7gXw3ptzk9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.isotope-layout').forEach(function(layoutEl) {
        var container = layoutEl.querySelector('.isotope-container');
        if (!container) return;

        var iso = new Isotope(container, {
            itemSelector: '.isotope-item',
            layoutMode: 'fitRows',
            filter: layoutEl.getAttribute('data-default-filter') || '*'
        });

        var filters = layoutEl.querySelectorAll('.isotope-filters li');
        filters.forEach(function(filterEl) {
            filterEl.addEventListener('click', function() {
                layoutEl.querySelector('.filter-active').classList.remove('filter-active');
                this.classList.add('filter-active');
                iso.arrange({ filter: this.getAttribute('data-filter') });
            });
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const numberFormatter = (typeof Intl !== 'undefined')
        ? new Intl.NumberFormat('th-TH')
        : null;

    const formatNumber = (value) => numberFormatter
        ? numberFormatter.format(value)
        : value.toString();

    const viewEndpointMap = {
        news: 'news/increment_view.php',
        hall: 'hall_of_fame/increment_view.php',
        international: 'international/increment_view.php'
    };

    const likeEndpointMap = {
        news: 'news/like.php',
        hall: 'hall_of_fame/like.php',
        international: 'international/like.php'
    };

    const buildViewPayload = (type, id) => {
        switch (type) {
            case 'hall':
                return { hall_id: id };
            case 'international':
                return { assignment_id: id };
            default:
                return { news_id: id };
        }
    };

    const buildLikePayload = (type, id) => {
        switch (type) {
            case 'hall':
                return { hall_id: id };
            case 'international':
                return { assignment_id: id };
            default:
                return { news_id: id };
        }
    };

    const incrementItemView = (itemType, itemId) => {
        const parsedId = parseInt(itemId, 10);
        if (!parsedId) {
            return;
        }

        const endpoint = viewEndpointMap[itemType] || viewEndpointMap.news;
        const payloadObj = buildViewPayload(itemType, parsedId);
        const payload = JSON.stringify(payloadObj);

        if (navigator.sendBeacon) {
            try {
                const blob = new Blob([payload], { type: 'application/json' });
                navigator.sendBeacon(endpoint, blob);
                return;
            } catch (error) {
                // fallback สู่ fetch
            }
        }

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: payload,
            keepalive: true
        }).catch(() => {});
    };

    const parseCount = (element) => {
        const raw = element.dataset.count || element.textContent || '0';
        const numeric = parseInt(raw.toString().replace(/[^0-9-]/g, ''), 10);
        return Number.isNaN(numeric) ? 0 : numeric;
    };

    const bumpViewCount = (card) => {
        if (!card) return;
        const viewsEl = card.querySelector('.news-views-count');
        if (!viewsEl) return;
        const prev = parseCount(viewsEl);
        const next = prev + 1;
        viewsEl.dataset.count = next;
        viewsEl.textContent = formatNumber(next);
    };

    const setLikedState = (button, iconEl, liked) => {
        if (liked) {
            button.classList.add('liked');
            button.dataset.liked = 'true';
            if (iconEl.classList.contains('far')) {
                iconEl.classList.remove('far');
            }
            iconEl.classList.add('fas');
        } else {
            button.classList.remove('liked');
            button.dataset.liked = 'false';
            if (iconEl.classList.contains('fas')) {
                iconEl.classList.remove('fas');
            }
            iconEl.classList.add('far');
        }
    };

    const updateCount = (element, value) => {
        element.dataset.count = value;
        element.textContent = formatNumber(value);
    };

    const updateLikeAriaLabel = (button, value) => {
        const formatted = formatNumber(value);
        button.setAttribute('aria-label', `ถูกใจรายการนี้ (จำนวน ${formatted} ครั้ง)`);
        button.setAttribute('title', `ถูกใจ (${formatted})`);
    };

    document.querySelectorAll('.news-like-button').forEach(function (button) {
        const itemIdRaw = button.dataset.itemId;
        const itemType = button.dataset.itemType || 'news';
        const storageKey = `content_like_${itemType}_${itemIdRaw}`;
        const parsedId = parseInt(itemIdRaw, 10);
        const iconEl = button.querySelector('i');
        const countEl = button.querySelector('.news-like-count');

        if (!button.dataset.liked) {
            button.dataset.liked = 'false';
        }

        button.addEventListener('click', function (e) {
            e.stopPropagation();
        }, true);

        updateLikeAriaLabel(button, parseCount(countEl));

        if (localStorage.getItem(storageKey)) {
            setLikedState(button, iconEl, true);
        }

        button.addEventListener('click', function () {
            if (button.classList.contains('loading') || button.dataset.liked === 'true') {
                return;
            }

            if (!parsedId) {
                return;
            }

            const previousCount = parseCount(countEl);
            const optimisticCount = previousCount + 1;

            setLikedState(button, iconEl, true);
            updateCount(countEl, optimisticCount);
            updateLikeAriaLabel(button, optimisticCount);

            button.classList.add('loading');

            const endpoint = likeEndpointMap[itemType] || likeEndpointMap.news;
            const payloadObj = buildLikePayload(itemType, parsedId);

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payloadObj)
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        if (typeof data.likes !== 'undefined') {
                            const parsed = parseInt(data.likes, 10);
                            const likesValue = Number.isNaN(parsed) ? optimisticCount : Math.max(parsed, optimisticCount);
                            updateCount(countEl, likesValue);
                            updateLikeAriaLabel(button, likesValue);
                        }

                        try {
                            localStorage.setItem(storageKey, '1');
                        } catch (error) {
                            console.warn('ไม่สามารถบันทึกสถานะการกดถูกใจในเครื่องได้', error);
                        }
                    } else {
                        updateCount(countEl, previousCount);
                        setLikedState(button, iconEl, false);
                        updateLikeAriaLabel(button, previousCount);
                    }
                })
                .catch(error => {
                    console.error('ไม่สามารถบันทึกการกดถูกใจได้', error);
                    updateCount(countEl, previousCount);
                    setLikedState(button, iconEl, false);
                    updateLikeAriaLabel(button, previousCount);
                })
                .finally(() => {
                    button.classList.remove('loading');
                });
        });
    });

    document.querySelectorAll('.portfolio-content[data-detail-url]').forEach(function (card) {
        const url = card.dataset.detailUrl;
        if (!url) return;

        const itemId = card.dataset.itemId;
        const itemType = card.dataset.itemType || 'news';

        const openDetail = function () {
            if (itemId) {
                incrementItemView(itemType, itemId);
            }
            bumpViewCount(card);
            window.open(url, '_blank', 'noopener');
        };

        card.addEventListener('click', function (event) {
            if (event.target.closest('.news-like-button') || event.target.closest('.news-detail-link') || event.target.tagName === 'A' || event.target.tagName === 'BUTTON') {
                return;
            }
            openDetail();
        });

        card.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                if (event.target.closest('.news-like-button') || event.target.closest('.news-detail-link')) {
                    return;
                }
                event.preventDefault();
                openDetail();
            }
        });
    });

    document.querySelectorAll('.news-detail-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.stopPropagation();
            const card = link.closest('.portfolio-content');
            if (!card) return;
            bumpViewCount(card);
            const relatedItemId = link.dataset.itemId || card.dataset.itemId;
            const relatedItemType = link.dataset.itemType || card.dataset.itemType || 'news';
            if (relatedItemId) {
                incrementItemView(relatedItemType, relatedItemId);
            }
        });
    });
});
</script>
