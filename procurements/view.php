<?php
require_once '../header.php';
require_once '../navbar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');

$stmt = $conn->prepare("SELECT * FROM procurement_announcements WHERE id = ? AND status IN ('published','closed')");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$announcement = $result->fetch_assoc();

if (!$announcement) {
    include '../footer.php';
    exit('<section class="py-5"><div class="container text-center"><h2 class="text-danger">ไม่พบประกาศนี้</h2><p class="text-muted">กรุณาตรวจสอบอีกครั้ง หรือกลับไปยังหน้ารายการประกาศ</p><a href="index.php" class="btn btn-primary mt-3"><i class="fas fa-arrow-left me-2"></i>กลับหน้ารายการ</a></div></section>');
}
?>
<section class="procurement-detail-section py-5">
    <div class="container">
        <div class="row align-items-start mb-4">
            <div class="col-lg-8">
                <span class="badge bg-primary mb-3"><i class="fas fa-shopping-cart me-2"></i>ประกาศจัดซื้อจัดจ้าง</span>
                <h1 class="display-6 fw-bold mb-2"><?php echo htmlspecialchars($announcement['title']); ?></h1>
                <p class="text-muted mb-1"><i class="fas fa-hashtag me-2"></i>เลขที่อ้างอิง: <?php echo htmlspecialchars($announcement['reference_number'] ?? '-'); ?></p>
                <p class="text-muted"><i class="fas fa-building me-2"></i>หน่วยงาน: <?php echo htmlspecialchars($announcement['department'] ?? '-'); ?></p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="../<?php echo htmlspecialchars($announcement['document_pdf']); ?>" class="btn btn-outline-primary" target="_blank" rel="noopener"><i class="fas fa-file-pdf me-2"></i>ดาวน์โหลดประกาศ (PDF)</a>
                <a href="index.php" class="btn btn-light ms-lg-2 mt-2 mt-lg-0"><i class="fas fa-arrow-left me-2"></i>กลับหน้ารายการ</a>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>รายละเอียดประกาศ</h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($announcement['description'] ?? 'ไม่มีรายละเอียดเพิ่มเติม')); ?></p>
                        <?php if (!empty($announcement['notes'])): ?>
                            <div class="alert alert-info"><i class="fas fa-lightbulb me-2"></i><?php echo nl2br(htmlspecialchars($announcement['notes'])); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-day me-2 text-primary"></i>กำหนดการ</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>วันที่ประกาศ:</strong> <?php echo date('d/m/Y', strtotime($announcement['published_date'])); ?></p>
                        <p class="mb-2"><strong>วันที่สิ้นสุด:</strong> <?php echo date('d/m/Y', strtotime($announcement['closing_date'])); ?></p>
                        <p class="mb-0"><strong>สถานะ:</strong>
                            <?php
                            $badge = 'secondary';
                            $label = 'ฉบับร่าง';
                            if ($announcement['status'] === 'published') { $badge = 'success'; $label = 'เปิดรับเสนอราคา'; }
                            elseif ($announcement['status'] === 'closed') { $badge = 'secondary'; $label = 'สิ้นสุดแล้ว'; }
                            elseif ($announcement['status'] === 'cancelled') { $badge = 'danger'; $label = 'ยกเลิก'; }
                            ?>
                            <span class="badge bg-<?php echo $badge; ?> ms-1"><?php echo $label; ?></span>
                        </p>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-phone me-2 text-primary"></i>ข้อมูลติดต่อ</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>ผู้ติดต่อ:</strong> <?php echo htmlspecialchars($announcement['contact_person'] ?? '-'); ?></p>
                        <p class="mb-2"><strong>โทรศัพท์:</strong> <?php echo htmlspecialchars($announcement['contact_phone'] ?? '-'); ?></p>
                        <p class="mb-0"><strong>อีเมล:</strong> <?php echo htmlspecialchars($announcement['contact_email'] ?? '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once '../footer.php'; ?>
