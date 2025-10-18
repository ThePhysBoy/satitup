<?php
// ดึงข้อมูลเอกสาร
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'satitup';
$db_port = 3306;

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// รับพารามิเตอร์การกรอง
$doc_type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

// สร้าง query สำหรับนับจำนวนทั้งหมด
$count_query = "SELECT COUNT(*) as total FROM official_documents WHERE status = 'active'";
$where_conditions = ["status = 'active'"];

if ($doc_type_filter) {
    $where_conditions[] = "doc_type = '" . $conn->real_escape_string($doc_type_filter) . "'";
}

if ($search) {
    $search_safe = $conn->real_escape_string($search);
    $where_conditions[] = "(title LIKE '%$search_safe%' OR doc_number LIKE '%$search_safe%' OR description LIKE '%$search_safe%')";
}

$where_clause = " WHERE " . implode(" AND ", $where_conditions);

// นับจำนวนทั้งหมด
$count_query = "SELECT COUNT(*) as total FROM official_documents" . $where_clause;
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// ดึงข้อมูลเอกสาร
$sql = "SELECT d.*, c.category_name 
        FROM official_documents d
        LEFT JOIN official_documents_categories c ON d.category_id = c.id" 
        . $where_clause . 
        " ORDER BY d.publish_date DESC, d.created_at DESC
        LIMIT $offset, $per_page";

$result = $conn->query($sql);
$documents = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
}

// ดึงสถิติจำนวนเอกสารแต่ละประเภท
$stats = [
    'regulation' => 0,
    'rule' => 0,
    'announcement' => 0,
    'order' => 0
];

$stats_sql = "SELECT doc_type, COUNT(*) as count 
              FROM official_documents 
              WHERE status = 'active' 
              GROUP BY doc_type";
$stats_result = $conn->query($stats_sql);
if ($stats_result) {
    while ($row = $stats_result->fetch_assoc()) {
        $stats[$row['doc_type']] = $row['count'];
    }
}

// ฟังก์ชันต่างๆ
function getDocTypeText($type) {
    $types = [
        'regulation' => 'ข้อบังคับ',
        'rule' => 'ระเบียบ',
        'announcement' => 'ประกาศ',
        'order' => 'คำสั่ง'
    ];
    return isset($types[$type]) ? $types[$type] : $type;
}

function getDocTypeColor($type) {
    $colors = [
        'regulation' => '#3498db',
        'rule' => '#9b59b6',
        'announcement' => '#27ae60',
        'order' => '#e67e22'
    ];
    return isset($colors[$type]) ? $colors[$type] : '#666';
}

function getDocTypeIcon($type) {
    $icons = [
        'regulation' => 'fa-gavel',
        'rule' => 'fa-clipboard-list',
        'announcement' => 'fa-bullhorn',
        'order' => 'fa-file-alt'
    ];
    return isset($icons[$type]) ? $icons[$type] : 'fa-file';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เอกสารราชการ - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .page-subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--doc-color), var(--doc-color-light));
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stat-card.active {
            background: linear-gradient(135deg, var(--doc-color), var(--doc-color-light));
            color: white;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .filter-bar {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .documents-grid {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .document-item {
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 0;
            transition: all 0.3s ease;
        }
        
        .document-item:last-child {
            border-bottom: none;
        }
        
        .document-item:hover {
            background: #f8f9fa;
            margin: 0 -20px;
            padding: 20px;
            border-radius: 10px;
        }
        
        .doc-type-tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
        }
        
        .doc-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .doc-title:hover {
            color: #667eea;
        }
        
        .doc-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .doc-meta i {
            margin: 0 5px;
            color: #95a5a6;
        }
        
        .doc-number {
            background: #e9ecef;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding-left: 45px;
            border-radius: 25px;
            border: 2px solid #e9ecef;
        }
        
        .search-box input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }
        
        .pagination-container {
            margin-top: 30px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #adb5bd;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.8rem;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">เอกสารราชการ</h1>
                    <p class="page-subtitle">ข้อบังคับ ระเบียบ ประกาศ และคำสั่ง</p>
                </div>
                <div>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i> กลับหน้าหลัก
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card <?php echo $doc_type_filter == 'regulation' ? 'active' : ''; ?>" 
                 style="--doc-color: #3498db; --doc-color-light: #5dade2;"
                 onclick="filterByType('regulation')">
                <i class="fas fa-gavel stat-icon"></i>
                <div class="stat-number"><?php echo $stats['regulation']; ?></div>
                <div class="stat-label">ข้อบังคับ</div>
            </div>
            
            <div class="stat-card <?php echo $doc_type_filter == 'rule' ? 'active' : ''; ?>"
                 style="--doc-color: #9b59b6; --doc-color-light: #bb8fce;"
                 onclick="filterByType('rule')">
                <i class="fas fa-clipboard-list stat-icon"></i>
                <div class="stat-number"><?php echo $stats['rule']; ?></div>
                <div class="stat-label">ระเบียบ</div>
            </div>
            
            <div class="stat-card <?php echo $doc_type_filter == 'announcement' ? 'active' : ''; ?>"
                 style="--doc-color: #27ae60; --doc-color-light: #52be80;"
                 onclick="filterByType('announcement')">
                <i class="fas fa-bullhorn stat-icon"></i>
                <div class="stat-number"><?php echo $stats['announcement']; ?></div>
                <div class="stat-label">ประกาศ</div>
            </div>
            
            <div class="stat-card <?php echo $doc_type_filter == 'order' ? 'active' : ''; ?>"
                 style="--doc-color: #e67e22; --doc-color-light: #f39c12;"
                 onclick="filterByType('order')">
                <i class="fas fa-file-alt stat-icon"></i>
                <div class="stat-number"><?php echo $stats['order']; ?></div>
                <div class="stat-label">คำสั่ง</div>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <form method="GET" class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control" 
                               placeholder="ค้นหาเอกสาร..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <?php if ($doc_type_filter): ?>
                        <input type="hidden" name="type" value="<?php echo htmlspecialchars($doc_type_filter); ?>">
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <?php if ($doc_type_filter || $search): ?>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> ล้างตัวกรอง
                    </a>
                    <?php endif; ?>
                    <span class="ms-3 text-muted">
                        พบ <?php echo $total_records; ?> รายการ
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Documents List -->
        <div class="documents-grid">
            <?php if (!empty($documents)): ?>
                <?php foreach ($documents as $doc): ?>
                <div class="document-item">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <span class="doc-type-tag" style="background: <?php echo getDocTypeColor($doc['doc_type']); ?>;">
                                    <i class="fas <?php echo getDocTypeIcon($doc['doc_type']); ?> me-2"></i>
                                    <?php echo getDocTypeText($doc['doc_type']); ?>
                                </span>
                                <?php if ($doc['doc_number']): ?>
                                    <span class="doc-number ms-2"><?php echo htmlspecialchars($doc['doc_number']); ?></span>
                                <?php endif; ?>
                                <?php if ($doc['file_path']): ?>
                                    <i class="fas fa-file-pdf text-danger ms-2" title="มีไฟล์ PDF"></i>
                                <?php endif; ?>
                            </div>
                            <a href="view.php?id=<?php echo $doc['id']; ?>" class="doc-title">
                                <?php echo htmlspecialchars($doc['title']); ?>
                            </a>
                            <div class="doc-meta">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($doc['publisher_name']); ?>
                                <i class="fas fa-calendar-alt ms-3"></i> <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?>
                                <?php if ($doc['views'] > 0): ?>
                                    <i class="fas fa-eye ms-3"></i> <?php echo number_format($doc['views']); ?> การดู
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="view.php?id=<?php echo $doc['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-2"></i> ดูรายละเอียด
                            </a>
                            <?php if ($doc['file_path']): ?>
                            <a href="../<?php echo htmlspecialchars($doc['file_path']); ?>" 
                               class="btn btn-outline-danger btn-sm" download>
                                <i class="fas fa-download me-2"></i> PDF
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $doc_type_filter ? '&type='.$doc_type_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $doc_type_filter ? '&type='.$doc_type_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $doc_type_filter ? '&type='.$doc_type_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h4>ไม่พบเอกสาร</h4>
                    <p>ไม่มีเอกสารที่ตรงกับเงื่อนไขการค้นหา</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function filterByType(type) {
        if (type) {
            window.location.href = '?type=' + type;
        } else {
            window.location.href = 'index.php';
        }
    }
    </script>
</body>
</html>

<?php
$conn->close();
?>
