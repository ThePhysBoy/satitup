<?php
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) {
    header('Location: ../index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { 
    header('Location: index.php'); 
    exit; 
}

$stmt = $conn->prepare("SELECT * FROM management WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$management = $stmt->get_result()->fetch_assoc();

if (!$management) { 
    header('Location: index.php'); 
    exit; 
}

$page_title = 'ข้อมูลผู้บริหาร';
$include_summernote = false;
$page_header_icon = '<i class="fas fa-user-tie me-3"></i>';
$back_button = true; 
$back_url = 'index.php'; 
$back_text = 'กลับไปหน้ารายการ';

ob_start();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">ข้อมูลผู้บริหาร</h1>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning btn-sm rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> แก้ไข
            </a>
            <a href="index.php" class="btn btn-secondary btn-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> กลับไปหน้ารายการ
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ข้อมูลส่วนตัว -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>ข้อมูลส่วนตัว
                    </h6>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($management['image_path'])): ?>
                        <img src="../../<?php echo htmlspecialchars($management['image_path']); ?>" 
                             class="img-fluid rounded shadow mb-3" 
                             style="max-height:300px;width:auto;">
                    <?php else: ?>
                        <div class="text-center p-5 bg-light rounded mb-3">
                            <i class="fas fa-user-circle text-secondary" style="font-size:150px;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h4 class="mb-2">
                        <?php echo htmlspecialchars($management['title'] . ' ' . $management['first_name'] . ' ' . $management['last_name']); ?>
                    </h4>
                    <p class="text-primary font-weight-bold">
                        <?php echo nl2br(htmlspecialchars($management['management_position'])); ?>
                    </p>
                    
                    <?php if (!empty($management['email'])): ?>
                    <p class="mb-2">
                        <i class="fas fa-envelope me-2 text-muted"></i>
                        <?php echo htmlspecialchars($management['email']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($management['phone'])): ?>
                    <p class="mb-2">
                        <i class="fas fa-phone me-2 text-muted"></i>
                        <?php echo htmlspecialchars($management['phone']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <p class="text-muted mb-1">ลำดับการแสดงผล</p>
                            <h5><?php echo (int)$management['order_number']; ?></h5>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">สถานะ</p>
                            <?php if ($management['status'] === 'active'): ?>
                                <span class="badge bg-success">เปิดใช้งาน</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">ปิดใช้งาน</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ประวัติ/ข้อมูลเพิ่มเติม -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>ประวัติ/ข้อมูลเพิ่มเติม
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($management['bio'])): ?>
                        <div class="bio-content">
                            <?php echo $management['bio']; // HTML content from summernote ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-5">
                            <i class="fas fa-file-alt mb-3" style="font-size:48px;"></i><br>
                            ไม่มีข้อมูลประวัติ
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ข้อมูลระบบ -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog me-2"></i>ข้อมูลระบบ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">ID:</td>
                                    <td><?php echo $management['id']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">คำนำหน้า:</td>
                                    <td><?php echo htmlspecialchars($management['title']); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ชื่อ:</td>
                                    <td><?php echo htmlspecialchars($management['first_name']); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">นามสกุล:</td>
                                    <td><?php echo htmlspecialchars($management['last_name']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">ตำแหน่งบริหาร:</td>
                                    <td><?php echo htmlspecialchars($management['management_position']); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">เส้นทางรูปภาพ:</td>
                                    <td><?php echo !empty($management['image_path']) ? htmlspecialchars($management['image_path']) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ลำดับการแสดงผล:</td>
                                    <td><?php echo (int)$management['order_number']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">สถานะ:</td>
                                    <td>
                                        <?php if ($management['status'] === 'active'): ?>
                                            <span class="badge bg-success">เปิดใช้งาน</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ปิดใช้งาน</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bio-content {
    font-size: 1rem;
    line-height: 1.7;
}
.bio-content h1, 
.bio-content h2, 
.bio-content h3, 
.bio-content h4, 
.bio-content h5, 
.bio-content h6 {
    color: #2c3e50;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
.bio-content ul, 
.bio-content ol {
    margin-left: 20px;
    margin-bottom: 1rem;
}
.bio-content p {
    margin-bottom: 1rem;
}
.bio-content img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    margin: 1rem 0;
}
.bio-content blockquote {
    border-left: 4px solid #4e73df;
    padding-left: 1rem;
    color: #6c757d;
    font-style: italic;
    margin: 1rem 0;
}
.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}
</style>

<?php
$content = ob_get_clean();
include '../news/template.php';
?>
