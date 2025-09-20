<?php
// Include database connection
require_once '../includes/db_config.php';

// Get all departments
$sql = "SELECT * FROM departments ORDER BY type, order_number, name";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบ Departments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .type-badge {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
        .type-academic { background-color: #d4edda; color: #155724; }
        .type-support { background-color: #fff3cd; color: #856404; }
        .type-primary { background-color: #d1ecf1; color: #0c5460; }
        .type-service { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">🔍 ตรวจสอบข้อมูล Departments ในฐานข้อมูล</h2>
        
        <div class="alert alert-info">
            <strong>📊 จำนวน Departments ทั้งหมด:</strong> <?php echo $result->num_rows; ?> รายการ
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>ชื่อแผนก/หน่วยงาน</th>
                        <th>ประเภท (Type)</th>
                        <th>คำอธิบาย</th>
                        <th>ลำดับ</th>
                        <th>วันที่สร้าง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $types_count = ['academic' => 0, 'support' => 0, 'primary' => 0, 'service' => 0];
                    while($row = $result->fetch_assoc()): 
                        if (isset($types_count[$row['type']])) {
                            $types_count[$row['type']]++;
                        }
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td class="text-center">
                            <span class="badge type-badge type-<?php echo $row['type']; ?>">
                                <?php 
                                switch($row['type']) {
                                    case 'academic': echo 'สายวิชาการ'; break;
                                    case 'support': echo 'สายสนับสนุน'; break;
                                    case 'primary': echo 'ประถมศึกษา'; break;
                                    case 'service': echo 'สายบริการ (เก่า)'; break;
                                    default: echo $row['type'];
                                }
                                ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['description'] ?? '-'); ?></td>
                        <td class="text-center"><?php echo $row['order_number']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">สายวิชาการ</h5>
                        <p class="card-text display-4"><?php echo $types_count['academic']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">ประถมศึกษา</h5>
                        <p class="card-text display-4"><?php echo $types_count['primary']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">สายสนับสนุน</h5>
                        <p class="card-text display-4"><?php echo $types_count['support']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">สายบริการ (เก่า)</h5>
                        <p class="card-text display-4"><?php echo $types_count['service']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="update_departments.php" class="btn btn-primary me-2">
                <i class="fas fa-sync"></i> รันสคริปต์อัปเดต Departments
            </a>
            <a href="index.php" class="btn btn-secondary me-2">
                <i class="fas fa-users"></i> จัดการบุคลากร
            </a>
            <a href="create.php" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มบุคลากร
            </a>
        </div>

        <?php if ($types_count['service'] > 0): ?>
        <div class="alert alert-warning mt-4">
            <strong>⚠️ คำเตือน:</strong> ยังมี departments ประเภท 'service' (เก่า) อยู่ <?php echo $types_count['service']; ?> รายการ 
            ควรรัน <a href="update_departments.php">update_departments.php</a> เพื่ออัปเดตเป็น 'support'
        </div>
        <?php endif; ?>

        <?php if ($types_count['primary'] == 0): ?>
        <div class="alert alert-danger mt-4">
            <strong>❌ ปัญหา:</strong> ไม่พบ departments ประเภท 'primary' (ประถมศึกษา) เลย! 
            กรุณารัน <a href="update_departments.php">update_departments.php</a> เพื่อเพิ่มข้อมูลประถมศึกษา
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
