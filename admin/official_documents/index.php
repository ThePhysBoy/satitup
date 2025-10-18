<?php
session_start();
require_once '../includes/db_config.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ดึงข้อมูลเอกสารทั้งหมด
$sql = "SELECT d.*, c.category_name, 
        (SELECT COUNT(*) FROM official_documents_logs WHERE document_id = d.id AND action = 'view') as view_count,
        (SELECT COUNT(*) FROM official_documents_logs WHERE document_id = d.id AND action = 'download') as download_count
        FROM official_documents d
        LEFT JOIN official_documents_categories c ON d.category_id = c.id
        ORDER BY d.created_at DESC";
$result = $conn->query($sql);
$documents = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
}

// ดึงหมวดหมู่ทั้งหมด
$categories_sql = "SELECT * FROM official_documents_categories WHERE status = 'active' ORDER BY doc_type, sort_order";
$categories_result = $conn->query($categories_sql);
$categories = [];
if ($categories_result) {
    while ($cat = $categories_result->fetch_assoc()) {
        $categories[$cat['doc_type']][] = $cat;
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
    <title>จัดการเอกสารราชการ | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
        
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            border-radius: 10px;
        }
        
        .btn-add-new {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .btn-add-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .action-buttons .btn {
            padding: 5px 10px;
            margin: 0 2px;
        }
        
        .doc-type-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .doc-type-regulation { background-color: #e3f2fd; color: #1565c0; }
        .doc-type-rule { background-color: #f3e5f5; color: #7b1fa2; }
        .doc-type-announcement { background-color: #e8f5e9; color: #2e7d32; }
        .doc-type-order { background-color: #fff3e0; color: #e65100; }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stats-card h6 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stats-card h3 {
            color: #333;
            font-weight: bold;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>
    
    <div class="main-container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-file-alt me-2"></i> จัดการเอกสารราชการ</h1>
                    <p class="mb-0">จัดการข้อบังคับ ระเบียบ ประกาศ และคำสั่ง</p>
                </div>
                <button class="btn btn-add-new" onclick="window.location.href='add.php'">
                    <i class="fas fa-plus me-2"></i> เพิ่มเอกสารใหม่
                </button>
            </div>
        </div>
        
        <!-- สถิติ -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h6><i class="fas fa-gavel me-2"></i>ข้อบังคับ</h6>
                    <h3><?php echo count(array_filter($documents, function($d) { return $d['doc_type'] == 'regulation'; })); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h6><i class="fas fa-clipboard-list me-2"></i>ระเบียบ</h6>
                    <h3><?php echo count(array_filter($documents, function($d) { return $d['doc_type'] == 'rule'; })); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h6><i class="fas fa-bullhorn me-2"></i>ประกาศ</h6>
                    <h3><?php echo count(array_filter($documents, function($d) { return $d['doc_type'] == 'announcement'; })); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h6><i class="fas fa-file-alt me-2"></i>คำสั่ง</h6>
                    <h3><?php echo count(array_filter($documents, function($d) { return $d['doc_type'] == 'order'; })); ?></h3>
                </div>
            </div>
        </div>
        
        <!-- ตารางข้อมูล -->
        <div class="card">
            <div class="card-body">
                <table id="documentsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">ประเภท</th>
                            <th width="10%">เลขที่</th>
                            <th width="25%">ชื่อเรื่อง</th>
                            <th width="15%">ผู้ประกาศ</th>
                            <th width="10%">วันที่ประกาศ</th>
                            <th width="8%">สถานะ</th>
                            <th width="7%">การดู</th>
                            <th width="10%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?php echo $doc['id']; ?></td>
                            <td>
                                <span class="doc-type-badge doc-type-<?php echo $doc['doc_type']; ?>">
                                    <?php echo getDocTypeText($doc['doc_type']); ?>
                                </span>
                            </td>
                            <td><?php echo $doc['doc_number'] ?: '-'; ?></td>
                            <td>
                                <?php echo htmlspecialchars($doc['title']); ?>
                                <?php if ($doc['file_path']): ?>
                                    <a href="../../<?php echo $doc['file_path']; ?>" target="_blank" class="ms-2">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($doc['publisher_name']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($doc['publisher_position']); ?></small>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($doc['publish_date'])); ?></td>
                            <td><?php echo getStatusBadge($doc['status']); ?></td>
                            <td>
                                <small>
                                    <i class="fas fa-eye"></i> <?php echo $doc['view_count'] ?: 0; ?><br>
                                    <i class="fas fa-download"></i> <?php echo $doc['download_count'] ?: 0; ?>
                                </small>
                            </td>
                            <td class="action-buttons">
                                <a href="edit.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-warning" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteDocument(<?php echo $doc['id']; ?>)" title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        $('#documentsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
    
    function deleteDocument(id) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "คุณแน่ใจว่าต้องการลบเอกสารนี้?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete.php?id=' + id;
            }
        });
    }
    </script>
</body>
</html>
