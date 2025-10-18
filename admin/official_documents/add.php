<?php
session_start();
require_once '../includes/db_config.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
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

// จัดการเมื่อส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doc_type = $_POST['doc_type'];
    $doc_number = $_POST['doc_number'] ?: null;
    $category_id = $_POST['category_id'] ?: null;
    $title = $_POST['title'];
    $description = $_POST['description'] ?: null;
    $publisher_name = $_POST['publisher_name'];
    $publisher_position = $_POST['publisher_position'];
    $publish_date = $_POST['publish_date'];
    $effective_date = $_POST['effective_date'] ?: null;
    $status = $_POST['status'];
    
    // จัดการอัพโหลดไฟล์
    $file_path = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $upload_dir = '../../uploads/official_documents/';
        
        // กำหนดโฟลเดอร์ตามประเภท
        $type_folders = [
            'regulation' => 'regulations',
            'rule' => 'rules',
            'announcement' => 'announcements',
            'order' => 'orders'
        ];
        
        $target_folder = $upload_dir . $type_folders[$doc_type] . '/';
        
        // สร้างชื่อไฟล์ใหม่
        $file_extension = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
        $new_filename = date('Ymd_His') . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_folder . $new_filename;
        
        // ตรวจสอบประเภทไฟล์
        if (strtolower($file_extension) == 'pdf') {
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_file)) {
                $file_path = 'uploads/official_documents/' . $type_folders[$doc_type] . '/' . $new_filename;
            }
        }
    }
    
    // บันทึกลงฐานข้อมูล
    $sql = "INSERT INTO official_documents 
            (doc_type, doc_number, category_id, title, description, file_path, 
             publisher_name, publisher_position, publish_date, effective_date, 
             status, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $created_by = $_SESSION['user_id'];
    
    $stmt->bind_param("ssissssssssi", 
        $doc_type, $doc_number, $category_id, $title, $description, $file_path,
        $publisher_name, $publisher_position, $publish_date, $effective_date,
        $status, $created_by
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มเอกสารสำเร็จ";
        header("Location: index.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มเอกสารใหม่ | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f5f5f5;
        }
        
        .main-container {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .required::after {
            content: " *";
            color: red;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        
        .form-section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>
    
    <div class="main-container">
        <div class="page-header">
            <h1><i class="fas fa-plus-circle me-2"></i> เพิ่มเอกสารใหม่</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-white-50">จัดการเอกสาร</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">เพิ่มเอกสารใหม่</li>
                </ol>
            </nav>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="form-card">
            <div class="form-section">
                <h5 class="section-title">ข้อมูลเอกสาร</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="doc_type" class="form-label required">ประเภทเอกสาร</label>
                        <select class="form-select" id="doc_type" name="doc_type" required onchange="updateCategories()">
                            <option value="">-- เลือกประเภท --</option>
                            <option value="regulation">ข้อบังคับ</option>
                            <option value="rule">ระเบียบ</option>
                            <option value="announcement">ประกาศ</option>
                            <option value="order">คำสั่ง</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">หมวดหมู่</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="doc_number" class="form-label">เลขที่เอกสาร</label>
                        <input type="text" class="form-control" id="doc_number" name="doc_number" placeholder="เช่น 1/2567">
                    </div>
                    
                    <div class="col-md-8 mb-3">
                        <label for="title" class="form-label required">ชื่อเรื่อง</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียด</label>
                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="pdf_file" class="form-label">ไฟล์ PDF</label>
                    <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
                    <small class="form-text text-muted">รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน 10MB</small>
                </div>
            </div>
            
            <div class="form-section">
                <h5 class="section-title">ข้อมูลผู้ประกาศ</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="publisher_name" class="form-label required">ชื่อผู้ประกาศ</label>
                        <input type="text" class="form-control" id="publisher_name" name="publisher_name" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="publisher_position" class="form-label required">ตำแหน่ง</label>
                        <input type="text" class="form-control" id="publisher_position" name="publisher_position" required>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h5 class="section-title">วันที่และสถานะ</h5>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="publish_date" class="form-label required">วันที่ประกาศ</label>
                        <input type="date" class="form-control" id="publish_date" name="publish_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="effective_date" class="form-label">วันที่มีผลบังคับใช้</label>
                        <input type="date" class="form-control" id="effective_date" name="effective_date">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label required">สถานะ</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">เผยแพร่</option>
                            <option value="draft">ฉบับร่าง</option>
                            <option value="inactive">ไม่เผยแพร่</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> ยกเลิก
                </a>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-save me-2"></i> บันทึกเอกสาร
                </button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // ข้อมูลหมวดหมู่จาก PHP
    const categories = <?php echo json_encode($categories); ?>;
    
    function updateCategories() {
        const docType = document.getElementById('doc_type').value;
        const categorySelect = document.getElementById('category_id');
        
        // ล้างตัวเลือกเดิม
        categorySelect.innerHTML = '<option value="">-- เลือกหมวดหมู่ --</option>';
        
        // เพิ่มหมวดหมู่ตามประเภทที่เลือก
        if (docType && categories[docType]) {
            categories[docType].forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.category_name;
                categorySelect.appendChild(option);
            });
        }
    }
    </script>
</body>
</html>
