<?php
session_start();
require_once '../includes/db_config.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ดึงสถิติเอกสารแต่ละประเภท
$stats = [
    'regulation' => 0,
    'rule' => 0,
    'announcement' => 0,
    'order' => 0
];

// นับจำนวนเอกสารแต่ละประเภท
$types = ['regulation', 'rule', 'announcement', 'order'];
foreach ($types as $type) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM official_documents WHERE doc_type = ? AND status = 'active'");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats[$type] = $result->fetch_assoc()['count'];
    $stmt->close();
}

// ดึงเอกสารล่าสุด 10 รายการ
$recent_sql = "SELECT d.*, c.category_name 
               FROM official_documents d
               LEFT JOIN official_documents_categories c ON d.category_id = c.id
               ORDER BY d.created_at DESC
               LIMIT 10";
$recent_result = $conn->query($recent_sql);
$recent_docs = [];
if ($recent_result) {
    while ($row = $recent_result->fetch_assoc()) {
        $recent_docs[] = $row;
    }
}

// ดึงเอกสารที่มีคนดูมากสุด 5 อันดับ
$popular_sql = "SELECT d.*, c.category_name,
                (SELECT COUNT(*) FROM official_documents_logs WHERE document_id = d.id AND action = 'view') as view_count
                FROM official_documents d
                LEFT JOIN official_documents_categories c ON d.category_id = c.id
                WHERE d.status = 'active'
                ORDER BY view_count DESC
                LIMIT 5";
$popular_result = $conn->query($popular_sql);
$popular_docs = [];
if ($popular_result) {
    while ($row = $popular_result->fetch_assoc()) {
        $popular_docs[] = $row;
    }
}

// ฟังก์ชันแปลงประเภทเอกสาร
function getDocTypeText($type) {
    $types = [
        'regulation' => 'ข้อบังคับ',
        'rule' => 'ระเบียบ',
        'announcement' => 'ประกาศ',
        'order' => 'คำสั่ง'
    ];
    return isset($types[$type]) ? $types[$type] : $type;
}

// ฟังก์ชันแปลงสถานะ
function getStatusBadge($status) {
    $badges = [
        'active' => '<span class="badge bg-success">เผยแพร่</span>',
        'inactive' => '<span class="badge bg-secondary">ไม่เผยแพร่</span>',
        'draft' => '<span class="badge bg-warning">ฉบับร่าง</span>'
    ];
    return isset($badges[$status]) ? $badges[$status] : $status;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดเอกสารราชการ | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f5f5f5;
        }
        
        .main-container {
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-icon {
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        
        .recent-docs {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .doc-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .doc-item:last-child {
            border-bottom: none;
        }
        
        .bg-regulation { background: linear-gradient(135deg, #3498db, #5dade2); }
        .bg-rule { background: linear-gradient(135deg, #9b59b6, #bb8fce); }
        .bg-announcement { background: linear-gradient(135deg, #27ae60, #52be80); }
        .bg-order { background: linear-gradient(135deg, #e67e22, #f39c12); }
    </style>
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>
    
    <div class="main-container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-chart-pie me-2"></i> แดชบอร์ดเอกสารราชการ</h1>
                    <p class="mb-0">สรุปภาพรวมและสถิติเอกสารราชการ</p>
                </div>
                <a href="index.php" class="btn btn-light">
                    <i class="fas fa-list me-2"></i> ดูเอกสารทั้งหมด
                </a>
            </div>
        </div>
        
        <!-- สถิติแต่ละประเภท -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-regulation text-white position-relative">
                    <i class="fas fa-gavel stat-icon"></i>
                    <div class="stat-label">ข้อบังคับ</div>
                    <div class="stat-number"><?php echo $stats['regulation']; ?></div>
                    <small>เอกสาร</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card bg-rule text-white position-relative">
                    <i class="fas fa-clipboard-list stat-icon"></i>
                    <div class="stat-label">ระเบียบ</div>
                    <div class="stat-number"><?php echo $stats['rule']; ?></div>
                    <small>เอกสาร</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card bg-announcement text-white position-relative">
                    <i class="fas fa-bullhorn stat-icon"></i>
                    <div class="stat-label">ประกาศ</div>
                    <div class="stat-number"><?php echo $stats['announcement']; ?></div>
                    <small>เอกสาร</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card bg-order text-white position-relative">
                    <i class="fas fa-file-alt stat-icon"></i>
                    <div class="stat-label">คำสั่ง</div>
                    <div class="stat-number"><?php echo $stats['order']; ?></div>
                    <small>เอกสาร</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- เอกสารล่าสุด -->
            <div class="col-md-7">
                <div class="recent-docs">
                    <h5 class="mb-4">
                        <i class="fas fa-clock me-2"></i> เอกสารล่าสุด
                    </h5>
                    <?php if (!empty($recent_docs)): ?>
                        <?php foreach ($recent_docs as $doc): ?>
                        <div class="doc-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                    <span class="badge bg-secondary ms-2"><?php echo getDocTypeText($doc['doc_type']); ?></span>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($doc['created_at'])); ?>
                                    </small>
                                </div>
                                <div>
                                    <?php echo getStatusBadge($doc['status']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีเอกสาร</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- เอกสารยอดนิยม -->
            <div class="col-md-5">
                <div class="recent-docs">
                    <h5 class="mb-4">
                        <i class="fas fa-fire me-2"></i> เอกสารยอดนิยม
                    </h5>
                    <?php if (!empty($popular_docs)): ?>
                        <?php foreach ($popular_docs as $index => $doc): ?>
                        <div class="doc-item">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-danger rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        <?php echo $index + 1; ?>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <strong><?php echo htmlspecialchars($doc['title']); ?></strong><br>
                                    <small class="text-muted">
                                        <i class="fas fa-eye"></i> <?php echo $doc['view_count']; ?> การดู
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีข้อมูลการดู</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- ปุ่มด่วน -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="recent-docs">
                    <h5 class="mb-4">
                        <i class="fas fa-rocket me-2"></i> ปุ่มด่วน
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="add.php" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-plus me-2"></i> เพิ่มเอกสารใหม่
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="index.php" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-list me-2"></i> จัดการเอกสาร
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="../../news_announcements.php" target="_blank" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-globe me-2"></i> ดูหน้าแสดงผล
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="../index.php" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-home me-2"></i> กลับหน้าหลัก
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
