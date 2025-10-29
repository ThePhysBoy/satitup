<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');

$stmt = $conn->prepare("SELECT * FROM international_assignments WHERE id = ? AND status = 'published'");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();
$stmt->close();

if ($assignment) {
    $updateViewsStmt = $conn->prepare("UPDATE international_assignments SET views = COALESCE(views, 0) + 1 WHERE id = ?");
    if ($updateViewsStmt) {
        $updateViewsStmt->bind_param('i', $id);
        $updateViewsStmt->execute();
        $updateViewsStmt->close();
        $assignment['views'] = ($assignment['views'] ?? 0) + 1;
    }
}

require_once '../header.php';
require_once '../navbar.php';

if (!$assignment) {
    echo '<section class="py-5"><div class="container text-center"><h2 class="text-danger">ไม่พบประกาศนี้</h2><p class="text-muted">กรุณาตรวจสอบอีกครั้ง หรือกลับไปยังหน้ารายการ</p><a class="btn btn-primary mt-3" href="index.php"><i class="fas fa-arrow-left me-2"></i>กลับหน้ารายการ</a></div></section>';
    include '../footer.php';
    exit;
}

$gallery = [];
if (!empty($assignment['gallery_images'])) {
    $decoded = json_decode($assignment['gallery_images'], true);
    if (is_array($decoded)) {
        $gallery = $decoded;
    }
}

$documentRelative = $assignment['document_pdf'] ?? '';
$documentUrl = !empty($documentRelative) ? '../' . ltrim($documentRelative, '/') : '';
$pdfExists = !empty($documentRelative) && file_exists(__DIR__ . '/../' . ltrim($documentRelative, '/'));
?>
<section class="international-view-section py-5">
    <div class="container">
        <div class="row mb-4 align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary mb-3"><i class="fas fa-globe me-2"></i>ประกาศการไปต่างประเทศ</span>
                <h1 class="display-6 fw-bold mb-2"><?php echo htmlspecialchars($assignment['title']); ?></h1>
                <p class="text-muted mb-2"><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($assignment['person_name']); ?></p>
                <?php if (!empty($assignment['role'])): ?>
                    <p class="text-muted mb-1"><i class="fas fa-id-badge me-2"></i><?php echo htmlspecialchars($assignment['role']); ?></p>
                <?php endif; ?>
                <?php if (!empty($assignment['affiliation'])): ?>
                    <p class="text-muted mb-1"><i class="fas fa-school me-2"></i><?php echo htmlspecialchars($assignment['affiliation']); ?></p>
                <?php endif; ?>
                <p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i><?php echo htmlspecialchars($assignment['city'] ? $assignment['city'] . ', ' : '') . htmlspecialchars($assignment['country']); ?></p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <?php if ($pdfExists): ?>
                    <a href="<?php echo $documentUrl; ?>" class="btn btn-outline-primary" target="_blank" rel="noopener"><i class="fas fa-file-pdf me-2"></i>ดาวน์โหลดเอกสาร</a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-light ms-lg-2 mt-2 mt-lg-0"><i class="fas fa-arrow-left me-2"></i>กลับหน้ารายการ</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="ratio ratio-16x9">
                        <img src="../<?php echo htmlspecialchars($assignment['cover_image']); ?>" class="card-img-top object-fit-cover" alt="<?php echo htmlspecialchars($assignment['title']); ?>">
                    </div>
                    <div class="card-body">
                        <?php if (!empty($assignment['purpose'])): ?>
                            <h5 class="fw-bold"><i class="fas fa-lightbulb me-2"></i>วัตถุประสงค์</h5>
                            <p><?php echo htmlspecialchars($assignment['purpose']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($assignment['description'])): ?>
                            <h5 class="fw-bold mt-4"><i class="fas fa-info-circle me-2"></i>รายละเอียด</h5>
                            <p><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($assignment['achievement'])): ?>
                            <div class="alert alert-success mt-3"><i class="fas fa-trophy me-2"></i><?php echo nl2br(htmlspecialchars($assignment['achievement'])); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($assignment['video_url'])): ?>
                            <div class="mt-4">
                                <h5 class="fw-bold"><i class="fas fa-video me-2"></i>วิดีโอเกี่ยวข้อง</h5>
                                <div class="ratio ratio-16x9">
                                    <iframe src="<?php echo htmlspecialchars($assignment['video_url']); ?>" title="International Video" allowfullscreen></iframe>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($pdfExists): ?>
                            <div class="mt-4">
                                <h5 class="fw-bold"><i class="fas fa-file-pdf me-2"></i>เอกสารเพิ่มเติม</h5>
                                <div class="ratio ratio-16x9">
                                    <iframe src="<?php echo $documentUrl; ?>#view=FitH" title="International Document" class="border rounded"></iframe>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($gallery)): ?>
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-images me-2"></i>แกลเลอรีภาพ</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <?php foreach ($gallery as $img): ?>
                                    <div class="col-md-6">
                                        <div class="ratio ratio-16x9">
                                            <img src="../<?php echo htmlspecialchars($img); ?>" class="rounded object-fit-cover" alt="Gallery">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-plane me-2"></i>กำหนดการเดินทาง</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($assignment['start_date'])): ?>
                            <p class="mb-2"><strong>เริ่มเดินทาง:</strong> <?php echo date('d F Y', strtotime($assignment['start_date'])); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($assignment['end_date'])): ?>
                            <p class="mb-2"><strong>สิ้นสุด:</strong> <?php echo date('d F Y', strtotime($assignment['end_date'])); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($assignment['duration_text'])): ?>
                            <p class="mb-0"><strong>ระยะเวลา:</strong> <?php echo htmlspecialchars($assignment['duration_text']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($assignment['event_name'])): ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-star me-2"></i>งานที่เข้าร่วม</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><?php echo htmlspecialchars($assignment['event_name']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>ข้อมูลเพิ่มเติม</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($assignment['published_date'])): ?>
                            <p><strong>ประกาศเมื่อ:</strong> <?php echo date('d F Y', strtotime($assignment['published_date'])); ?></p>
                        <?php endif; ?>
                        <p>สถานะ: <span class="badge bg-success">เผยแพร่</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once '../footer.php'; ?>
