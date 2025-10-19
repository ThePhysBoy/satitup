<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');

$stmt = $conn->prepare("SELECT * FROM training_announcements WHERE id = ? AND status IN ('open','closed')");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$training = $result->fetch_assoc();

require_once '../header.php';
require_once '../navbar.php';

if (!$training) {
    echo '<section class="py-5"><div class="container text-center"><h2 class="text-danger">ไม่พบประกาศอบรมนี้</h2><p class="text-muted">กรุณาตรวจสอบอีกครั้ง หรือกลับไปยังหน้ารายการประกาศ</p><a href="index.php" class="btn btn-primary mt-3"><i class="fas fa-arrow-left me-2"></i>กลับหน้ารายการ</a></div></section>';
    include '../footer.php';
    exit;
}

$documentRelative = ltrim($training['document_pdf'] ?? '', '/');
$documentPath = __DIR__ . '/../' . $documentRelative;
$documentUrl = '../' . $documentRelative;
$pdfExists = !empty($documentRelative) && file_exists($documentPath);

$additionalFiles = [];
if (!empty($training['additional_files'])) {
    $decoded = json_decode($training['additional_files'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $additionalFiles = $decoded;
    }
}
?>
<section class="training-detail-section py-5">
    <div class="container">
        <div class="row align-items-start mb-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark mb-3"><i class="fas fa-chalkboard-teacher me-2"></i>ประกาศอบรม</span>
                <h1 class="display-6 fw-bold mb-2"><?php echo htmlspecialchars($training['training_topic'] ?? $training['title']); ?></h1>
                <p class="text-muted mb-1"><i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($training['title']); ?></p>
                <p class="text-muted"><i class="fas fa-building me-2"></i>หน่วยงานผู้จัด: <?php echo htmlspecialchars($training['host_department'] ?? '-'); ?></p>
                <?php if (!empty($training['training_type'])): ?>
                    <span class="badge bg-secondary">รูปแบบการอบรม: <?php echo htmlspecialchars($training['training_type']); ?></span>
                <?php endif; ?>
                <?php if (!empty($training['target_audience'])): ?>
                    <span class="badge bg-info text-dark ms-2">กลุ่มเป้าหมาย: <?php echo htmlspecialchars($training['target_audience']); ?></span>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 text-lg-end">
                <?php if ($pdfExists): ?>
                    <a href="<?php echo $documentUrl; ?>" class="btn btn-outline-primary" target="_blank" rel="noopener"><i class="fas fa-file-pdf me-2"></i>ดาวน์โหลดเอกสารอบรม</a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-light ms-lg-2 mt-2 mt-lg-0"><i class="fas fa-arrow-left me-2"></i>กลับหน้ารายการ</a>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>รายละเอียดการอบรม</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($training['description'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($training['description'])); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($training['training_dates'])): ?>
                            <div class="alert alert-light border"><i class="fas fa-calendar me-2"></i><strong>กำหนดการอบรม:</strong> <?php echo nl2br(htmlspecialchars($training['training_dates'])); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($training['agenda'])): ?>
                            <h6 class="fw-bold mt-3">กำหนดการ (Agenda)</h6>
                            <p><?php echo nl2br(htmlspecialchars($training['agenda'])); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($training['location'])): ?>
                            <p class="mb-0"><strong><i class="fas fa-map-marker-alt me-2 text-danger"></i>สถานที่:</strong> <?php echo htmlspecialchars($training['location']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($training['price'])): ?>
                            <p class="mb-0"><strong><i class="fas fa-money-bill-wave me-2 text-success"></i>ค่าลงทะเบียน:</strong> <?php echo htmlspecialchars($training['price']); ?></p>
                        <?php endif; ?>

                        <?php if ($pdfExists): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-file-pdf me-2 text-danger"></i>เอกสารประกอบ</h6>
                                <div class="ratio ratio-16x9">
                                    <iframe src="<?php echo $documentUrl; ?>#view=FitH" title="ประกาศอบรม" class="border rounded" allowfullscreen></iframe>
                                </div>
                                <small class="text-muted d-block mt-2">* หากไฟล์ไม่แสดงผล กรุณาคลิกปุ่มดาวน์โหลดเพื่อเปิดในหน้าต่างใหม่</small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-4"><i class="fas fa-exclamation-triangle me-2"></i>ไม่พบไฟล์ PDF ของประกาศนี้</div>
                        <?php endif; ?>

                        <?php if (!empty($additionalFiles)): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold mb-2"><i class="fas fa-paperclip me-2"></i>ไฟล์แนบเพิ่มเติม</h6>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($additionalFiles as $label => $filePath): ?>
                                        <li class="list-group-item px-0">
                                            <a href="<?php echo '../' . ltrim($filePath, '/'); ?>" target="_blank" rel="noopener" class="text-decoration-none">
                                                <i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($label); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-day me-2 text-primary"></i>กำหนดการสำคัญ</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>วันที่ประกาศ:</strong> <?php echo date('d/m/Y', strtotime($training['published_date'])); ?></p>
                        <?php if (!empty($training['registration_deadline'])): ?>
                            <p class="mb-2"><strong>ปิดรับสมัคร:</strong> <?php echo date('d/m/Y', strtotime($training['registration_deadline'])); ?></p>
                        <?php endif; ?>
                        <p class="mb-0"><strong>สถานะ:</strong>
                            <?php
                            $badge = $training['status'] === 'open' ? 'success' : ($training['status'] === 'closed' ? 'secondary' : 'danger');
                            $label = $training['status'] === 'open' ? 'เปิดรับสมัคร' : ($training['status'] === 'closed' ? 'สิ้นสุดการรับสมัคร' : 'ยกเลิก');
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
                        <p class="mb-2"><strong>ผู้ติดต่อ:</strong> <?php echo htmlspecialchars($training['contact_person'] ?? '-'); ?></p>
                        <p class="mb-2"><strong>โทรศัพท์:</strong> <?php echo htmlspecialchars($training['contact_phone'] ?? '-'); ?></p>
                        <p class="mb-0"><strong>อีเมล:</strong> <?php echo htmlspecialchars($training['contact_email'] ?? '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once '../footer.php'; ?>
