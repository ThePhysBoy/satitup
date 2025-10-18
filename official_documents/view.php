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

// รับ ID จาก URL
$document_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ดึงข้อมูลเอกสาร
$stmt = $conn->prepare("SELECT d.*, c.category_name 
                        FROM official_documents d
                        LEFT JOIN official_documents_categories c ON d.category_id = c.id
                        WHERE d.id = ? AND d.status = 'active'");
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();

if (!$document) {
    header("Location: index.php");
    exit();
}

// บันทึกการเข้าชม
$log_stmt = $conn->prepare("INSERT INTO official_documents_logs (document_id, action, ip_address, user_agent, created_at) VALUES (?, 'view', ?, ?, NOW())");
$action = 'view';
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$log_stmt->bind_param("iss", $document_id, $ip, $user_agent);
$log_stmt->execute();

// อัพเดทจำนวนการดู
$update_stmt = $conn->prepare("UPDATE official_documents SET views = views + 1 WHERE id = ?");
$update_stmt->bind_param("i", $document_id);
$update_stmt->execute();

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

// ฟังก์ชันสีของประเภท
function getDocTypeColor($type) {
    $colors = [
        'regulation' => '#3498db',
        'rule' => '#9b59b6',
        'announcement' => '#27ae60',
        'order' => '#e67e22'
    ];
    return isset($colors[$type]) ? $colors[$type] : '#666';
}

// ฟังก์ชันไอคอนของประเภท
function getDocTypeIcon($type) {
    $icons = [
        'regulation' => 'fa-gavel',
        'rule' => 'fa-clipboard-list',
        'announcement' => 'fa-bullhorn',
        'order' => 'fa-file-alt'
    ];
    return isset($icons[$type]) ? $icons[$type] : 'fa-file';
}

// ดึงเอกสารที่เกี่ยวข้อง
$related_stmt = $conn->prepare("SELECT id, title, doc_number, publish_date 
                                FROM official_documents 
                                WHERE doc_type = ? AND id != ? AND status = 'active'
                                ORDER BY publish_date DESC
                                LIMIT 5");
$related_stmt->bind_param("si", $document['doc_type'], $document_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related_docs = [];
while ($row = $related_result->fetch_assoc()) {
    $related_docs[] = $row;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document['title']); ?> - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .document-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .document-header {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .document-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, <?php echo getDocTypeColor($document['doc_type']); ?>, <?php echo getDocTypeColor($document['doc_type']); ?>99);
        }
        
        .doc-type-badge {
            display: inline-flex;
            align-items: center;
            background: <?php echo getDocTypeColor($document['doc_type']); ?>;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .doc-type-badge i {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        
        .document-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .document-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #f0f0f0;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
        }
        
        .meta-item i {
            color: <?php echo getDocTypeColor($document['doc_type']); ?>;
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        
        .meta-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-right: 5px;
        }
        
        .meta-value {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .document-content {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .content-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            color: <?php echo getDocTypeColor($document['doc_type']); ?>;
            margin-right: 10px;
        }
        
        .pdf-viewer {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            border: 2px dashed #dee2e6;
        }
        
        .pdf-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        
        .download-btn {
            background: linear-gradient(135deg, <?php echo getDocTypeColor($document['doc_type']); ?> 0%, <?php echo getDocTypeColor($document['doc_type']); ?>CC 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 10px;
        }
        
        .download-btn:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .download-btn i {
            margin-right: 8px;
        }
        
        .view-btn {
            background: linear-gradient(135deg, #3498db 0%, #5dade2 100%);
        }
        
        .sidebar {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .related-docs {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .related-docs li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .related-docs li:last-child {
            border-bottom: none;
        }
        
        .related-docs a {
            color: #2c3e50;
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
        }
        
        .related-docs a:hover {
            color: <?php echo getDocTypeColor($document['doc_type']); ?>;
            transform: translateX(5px);
        }
        
        .stats-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .breadcrumb-nav {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .breadcrumb {
            margin: 0;
            background: transparent;
        }
        
        .breadcrumb-item a {
            color: #7f8c8d;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: <?php echo getDocTypeColor($document['doc_type']); ?>;
        }
        
        .breadcrumb-item.active {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .no-pdf-message {
            background: #fff3cd;
            color: #856404;
            padding: 15px 20px;
            border-radius: 10px;
            border-left: 4px solid #ffc107;
        }
        
        @media (max-width: 768px) {
            .document-header {
                padding: 25px;
            }
            
            .document-title {
                font-size: 1.5rem;
            }
            
            .document-meta {
                gap: 15px;
            }
            
            .document-content {
                padding: 25px;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .document-header,
        .document-content,
        .sidebar {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .document-content {
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }
        
        .sidebar {
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a></li>
                <li class="breadcrumb-item"><a href="news_announcements.php">ประกาศและคำสั่ง</a></li>
                <li class="breadcrumb-item active"><?php echo getDocTypeText($document['doc_type']); ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Document Header -->
                <div class="document-header">
                    <div class="doc-type-badge">
                        <i class="fas <?php echo getDocTypeIcon($document['doc_type']); ?>"></i>
                        <?php echo getDocTypeText($document['doc_type']); ?>
                    </div>
                    
                    <h1 class="document-title"><?php echo htmlspecialchars($document['title']); ?></h1>
                    
                    <?php if ($document['doc_number']): ?>
                    <div class="mb-3">
                        <span class="badge bg-secondary" style="font-size: 1rem;">
                            เลขที่ <?php echo htmlspecialchars($document['doc_number']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="document-meta">
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span class="meta-label">ผู้ประกาศ:</span>
                            <span class="meta-value"><?php echo htmlspecialchars($document['publisher_name']); ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="fas fa-briefcase"></i>
                            <span class="meta-label">ตำแหน่ง:</span>
                            <span class="meta-value"><?php echo htmlspecialchars($document['publisher_position']); ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="meta-label">วันที่ประกาศ:</span>
                            <span class="meta-value"><?php echo date('d/m/Y', strtotime($document['publish_date'])); ?></span>
                        </div>
                        
                        <?php if ($document['effective_date']): ?>
                        <div class="meta-item">
                            <i class="fas fa-calendar-check"></i>
                            <span class="meta-label">วันที่มีผลบังคับใช้:</span>
                            <span class="meta-value"><?php echo date('d/m/Y', strtotime($document['effective_date'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Document Content -->
                <div class="document-content">
                    <?php if ($document['description']): ?>
                    <div class="content-section">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            รายละเอียด
                        </h3>
                        <p><?php echo nl2br(htmlspecialchars($document['description'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($document['category_name']): ?>
                    <div class="content-section">
                        <h3 class="section-title">
                            <i class="fas fa-folder"></i>
                            หมวดหมู่
                        </h3>
                        <span class="badge bg-light text-dark" style="font-size: 1rem; padding: 8px 15px;">
                            <?php echo htmlspecialchars($document['category_name']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="content-section">
                        <h3 class="section-title">
                            <i class="fas fa-file-pdf"></i>
                            ไฟล์เอกสาร
                        </h3>
                        
                        <?php if ($document['file_path']): ?>
                        <div class="pdf-viewer">
                            <i class="fas fa-file-pdf pdf-icon"></i>
                            <h4>ไฟล์ PDF พร้อมดาวน์โหลด</h4>
                            <p class="text-muted mb-4">คลิกปุ่มด้านล่างเพื่อดูหรือดาวน์โหลดเอกสาร</p>
                            
                            <div>
                                <a href="../<?php echo htmlspecialchars($document['file_path']); ?>" target="_blank" class="download-btn view-btn">
                                    <i class="fas fa-eye"></i> ดูเอกสาร
                                </a>
                                <a href="../<?php echo htmlspecialchars($document['file_path']); ?>" download class="download-btn">
                                    <i class="fas fa-download"></i> ดาวน์โหลด PDF
                                </a>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="no-pdf-message">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ไม่มีไฟล์แนบสำหรับเอกสารนี้
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Stats Box -->
                <div class="stats-box">
                    <div class="stats-number">
                        <i class="fas fa-eye"></i> <?php echo number_format($document['views']); ?>
                    </div>
                    <div class="stats-label">จำนวนผู้เข้าชม</div>
                </div>
                
                <!-- Related Documents -->
                <?php if (!empty($related_docs)): ?>
                <div class="sidebar">
                    <h4 class="mb-3">
                        <i class="fas fa-link me-2" style="color: <?php echo getDocTypeColor($document['doc_type']); ?>;"></i>
                        <?php echo getDocTypeText($document['doc_type']); ?>อื่นๆ
                    </h4>
                    <ul class="related-docs">
                        <?php foreach ($related_docs as $related): ?>
                        <li>
                            <a href="view.php?id=<?php echo $related['id']; ?>">
                                <strong><?php echo htmlspecialchars($related['title']); ?></strong>
                                <?php if ($related['doc_number']): ?>
                                    <span class="text-muted">(<?php echo $related['doc_number']; ?>)</span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt"></i> 
                                    <?php echo date('d/m/Y', strtotime($related['publish_date'])); ?>
                                </small>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Back Button -->
                <div class="sidebar mt-3">
                    <a href="news_announcements.php" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i> กลับหน้าประกาศ
                    </a>
                    <a href="index.php" class="btn btn-primary w-100 mt-2">
                        <i class="fas fa-home me-2"></i> กลับหน้าหลัก
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
