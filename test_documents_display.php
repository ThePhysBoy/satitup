<?php
// ทดสอบการดึงข้อมูลเอกสารราชการ
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

// ดึงข้อมูลเอกสารราชการจากฐานข้อมูล
$official_docs = [
    'regulation' => [],
    'rule' => [],
    'announcement' => [],
    'order' => []
];

// ตรวจสอบว่ามีตารางหรือไม่
$table_check = $conn->query("SHOW TABLES LIKE 'official_documents'");
if ($table_check && $table_check->num_rows > 0) {
    echo "<h2>พบตาราง official_documents ✅</h2>";
    
    // ดึงเอกสารแต่ละประเภท
    $types = ['regulation', 'rule', 'announcement', 'order'];
    foreach ($types as $type) {
        $stmt = $conn->prepare("SELECT d.*, c.category_name 
                               FROM official_documents d
                               LEFT JOIN official_documents_categories c ON d.category_id = c.id
                               WHERE d.doc_type = ? AND d.status = 'active'
                               ORDER BY d.publish_date DESC, d.created_at DESC
                               LIMIT 10");
        if ($stmt) {
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $res = $stmt->get_result();
            $official_docs[$type] = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    }
} else {
    echo "<h2>❌ ไม่พบตาราง official_documents</h2>";
}

// แสดงผลข้อมูลที่ดึงได้
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบการแสดงผลเอกสารราชการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>ข้อมูลเอกสารราชการในฐานข้อมูล</h1>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-gavel"></i> ข้อบังคับ
                    </div>
                    <div class="card-body">
                        <h2><?php echo count($official_docs['regulation']); ?></h2>
                        <small>เอกสาร</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-clipboard-list"></i> ระเบียบ
                    </div>
                    <div class="card-body">
                        <h2><?php echo count($official_docs['rule']); ?></h2>
                        <small>เอกสาร</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-bullhorn"></i> ประกาศ
                    </div>
                    <div class="card-body">
                        <h2><?php echo count($official_docs['announcement']); ?></h2>
                        <small>เอกสาร</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-file-alt"></i> คำสั่ง
                    </div>
                    <div class="card-body">
                        <h2><?php echo count($official_docs['order']); ?></h2>
                        <small>เอกสาร</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- แสดงรายละเอียด -->
        <div class="mt-5">
            <h3>รายละเอียดเอกสาร</h3>
            
            <!-- ข้อบังคับ -->
            <div class="mt-4">
                <h4 class="text-primary"><i class="fas fa-gavel"></i> ข้อบังคับ (<?php echo count($official_docs['regulation']); ?>)</h4>
                <?php if (!empty($official_docs['regulation'])): ?>
                <ul class="list-group">
                    <?php foreach ($official_docs['regulation'] as $doc): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                        <?php if ($doc['doc_number']): ?>
                            <span class="badge bg-secondary"><?php echo $doc['doc_number']; ?></span>
                        <?php endif; ?>
                        <br>
                        <small>
                            วันที่ประกาศ: <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?> |
                            ผู้ประกาศ: <?php echo htmlspecialchars($doc['publisher_name']); ?> 
                            (<?php echo htmlspecialchars($doc['publisher_position']); ?>)
                        </small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted">ไม่มีข้อมูล</p>
                <?php endif; ?>
            </div>
            
            <!-- ระเบียบ -->
            <div class="mt-4">
                <h4 class="text-success"><i class="fas fa-clipboard-list"></i> ระเบียบ (<?php echo count($official_docs['rule']); ?>)</h4>
                <?php if (!empty($official_docs['rule'])): ?>
                <ul class="list-group">
                    <?php foreach ($official_docs['rule'] as $doc): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                        <?php if ($doc['doc_number']): ?>
                            <span class="badge bg-secondary"><?php echo $doc['doc_number']; ?></span>
                        <?php endif; ?>
                        <br>
                        <small>
                            วันที่ประกาศ: <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?> |
                            ผู้ประกาศ: <?php echo htmlspecialchars($doc['publisher_name']); ?> 
                            (<?php echo htmlspecialchars($doc['publisher_position']); ?>)
                        </small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted">ไม่มีข้อมูล</p>
                <?php endif; ?>
            </div>
            
            <!-- ประกาศ -->
            <div class="mt-4">
                <h4 class="text-warning"><i class="fas fa-bullhorn"></i> ประกาศ (<?php echo count($official_docs['announcement']); ?>)</h4>
                <?php if (!empty($official_docs['announcement'])): ?>
                <ul class="list-group">
                    <?php foreach ($official_docs['announcement'] as $doc): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                        <?php if ($doc['doc_number']): ?>
                            <span class="badge bg-secondary"><?php echo $doc['doc_number']; ?></span>
                        <?php endif; ?>
                        <br>
                        <small>
                            วันที่ประกาศ: <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?> |
                            ผู้ประกาศ: <?php echo htmlspecialchars($doc['publisher_name']); ?> 
                            (<?php echo htmlspecialchars($doc['publisher_position']); ?>)
                        </small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted">ไม่มีข้อมูล</p>
                <?php endif; ?>
            </div>
            
            <!-- คำสั่ง -->
            <div class="mt-4">
                <h4 class="text-danger"><i class="fas fa-file-alt"></i> คำสั่ง (<?php echo count($official_docs['order']); ?>)</h4>
                <?php if (!empty($official_docs['order'])): ?>
                <ul class="list-group">
                    <?php foreach ($official_docs['order'] as $doc): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                        <?php if ($doc['doc_number']): ?>
                            <span class="badge bg-secondary"><?php echo $doc['doc_number']; ?></span>
                        <?php endif; ?>
                        <br>
                        <small>
                            วันที่ประกาศ: <?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?> |
                            ผู้ประกาศ: <?php echo htmlspecialchars($doc['publisher_name']); ?> 
                            (<?php echo htmlspecialchars($doc['publisher_position']); ?>)
                        </small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted">ไม่มีข้อมูล</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-5">
            <a href="news_announcements.php" class="btn btn-primary">ดูหน้าแสดงผลจริง</a>
            <a href="admin/official_documents/index.php" class="btn btn-success">หน้าจัดการเอกสาร</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
