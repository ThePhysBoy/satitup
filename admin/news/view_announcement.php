<?php
// Set header to UTF-8 before any output
header('Content-Type: text/html; charset=UTF-8');

// View a single announcement in full
require_once '../includes/db_config.php';
// Public view support: if ?public=1 present, do NOT require admin session
$isPublic = isset($_GET['public']) && $_GET['public'] === '1';
if (!$isPublic) {
    require_once '../includes/auth_functions.php';
    requireNewsAccess();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT a.*, u.full_name FROM announcements a LEFT JOIN users u ON u.id = a.created_by WHERE a.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$ann = $res && $res->num_rows === 1 ? $res->fetch_assoc() : null;

$page_title = $ann ? 'รายละเอียดประกาศ' : 'ไม่พบประกาศ';
$page_header_icon = '<i class="fas fa-bullhorn me-2"></i>';

ob_start();
?>

<?php if (!$ann): ?>
    <div class="card-modern">
        <div class="card-body-modern">
            ไม่พบประกาศที่ต้องการ
        </div>
    </div>
<?php else: ?>
    <div class="card-modern mb-3">
        <div class="card-header-modern">
            <h5 class="m-0"><?php echo htmlspecialchars($ann['title'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h5>
        </div>
        <div class="card-body-modern">
            <div class="row mb-3">
                <div class="col-md-3"><strong>หมวดหมู่:</strong>
                    <?php
                        $map = ['announcement'=>'คำสั่งและประกาศ','procurement'=>'การจัดซื้อจัดจ้าง','recruitment'=>'การรับสมัครงาน'];
                        $catKey = isset($ann['category']) ? (string)$ann['category'] : '';
                        echo $map[$catKey] ?? '-';
                    ?>
                </div>
                <div class="col-md-3"><strong>สถานะ:</strong>
                    <?php if ($ann['status']==='open'): ?>
                        <span class="badge bg-info">เปิดรับ</span>
                    <?php elseif ($ann['status']==='result'): ?>
                        <span class="badge bg-primary">ประกาศผล</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">ปิดรับ</span>
                    <?php endif; ?>
                </div>
                <div class="col-md-3"><strong>งบประมาณ:</strong> <?php echo $ann['budget'] ? number_format($ann['budget'],0) . ' บาท' : '-'; ?></div>
                <div class="col-md-3"><strong>วันที่ประกาศ:</strong> <?php echo $ann['announce_date'] ? date('d/m/Y', strtotime($ann['announce_date'])) : date('d/m/Y', strtotime($ann['created_at'])); ?></div>
            </div>
            <div class="mb-3"><strong>ฝ่าย/หน่วยงาน:</strong> <?php echo htmlspecialchars($ann['department'] ?? '-', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>
            <?php if (!empty($ann['content'])): ?>
                <div class="mb-3"><?php echo nl2br(htmlspecialchars($ann['content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')); ?></div>
            <?php endif; ?>
            <?php if (!empty($ann['file_path'])): ?>
                <div class="mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ไฟล์ PDF พร้อมดาวน์โหลดแล้ว</strong> - ใช้วิธีด้านล่างเพื่อดู PDF
                    </div>
                </div>

                <div class="card-modern">
                    <div class="card-header-modern">
                        <strong><i class="fas fa-file-pdf me-2"></i>แสดงเอกสาร PDF</strong>
                    </div>
                    <div class="card-body-modern p-0">
                        <div class="pdf-container" style="width: 100%; min-height: 900px; border: 1px solid #ddd; background: #f8f9fa;">
                            <!-- Control Buttons -->
                            <div class="pdf-controls" style="padding: 15px; background: white; border-bottom: 1px solid #ddd;">
                                <div class="btn-group" role="group">
                                    <a href="../../pdf_viewer.php?file=<?php echo urlencode(basename($ann['file_path'])); ?>" 
                                       target="_blank" 
                                       class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> เปิดในแท็บใหม่
                                    </a>
                                    <a href="../../pdf_viewer.php?file=<?php echo urlencode(basename($ann['file_path'])); ?>&download=1" 
                                       class="btn btn-success">
                                        <i class="fas fa-download me-1"></i> ดาวน์โหลด
                                    </a>
                                    <button onclick="toggleFullscreen()" class="btn btn-info">
                                        <i class="fas fa-expand me-1"></i> เต็มหน้าจอ
                                    </button>
                                </div>
                            </div>

                            <!-- PDF Display Area -->
                            <div id="pdf-display-area" style="width: 100%; height: 900px;">
                                <iframe
                                    id="pdf-iframe"
                                    src="../../pdf_viewer.php?file=<?php echo urlencode(basename($ann['file_path'])); ?>"
                                    width="100%"
                                    height="100%"
                                    style="border: none;"
                                    allowfullscreen
                                >
                                    <object
                                        data="../../pdf_viewer.php?file=<?php echo urlencode(basename($ann['file_path'])); ?>"
                                        type="application/pdf"
                                        width="100%"
                                        height="100%"
                                        style="border: none;"
                                    >
                                        <div class="alert alert-warning text-center" style="margin-top: 50px;">
                                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                            <h5>ไม่สามารถแสดง PDF ได้</h5>
                                            <p>กรุณาใช้ปุ่ม "เปิดในแท็บใหม่" หรือ "ดาวน์โหลด" ด้านบน</p>
                                        </div>
                                    </object>
                                </iframe>
                            </div>
                        </div>

                        <script>
                        function toggleFullscreen() {
                            const pdfContainer = document.getElementById('pdf-display-area');
                            const btn = event.target.closest('button');
                            
                            if (!document.fullscreenElement) {
                                // Enter fullscreen
                                if (pdfContainer.requestFullscreen) {
                                    pdfContainer.requestFullscreen();
                                } else if (pdfContainer.webkitRequestFullscreen) {
                                    pdfContainer.webkitRequestFullscreen();
                                } else if (pdfContainer.msRequestFullscreen) {
                                    pdfContainer.msRequestFullscreen();
                                }
                                btn.innerHTML = '<i class="fas fa-compress me-1"></i> ออกจากเต็มหน้าจอ';
                            } else {
                                // Exit fullscreen
                                if (document.exitFullscreen) {
                                    document.exitFullscreen();
                                } else if (document.webkitExitFullscreen) {
                                    document.webkitExitFullscreen();
                                } else if (document.msExitFullscreen) {
                                    document.msExitFullscreen();
                                }
                                btn.innerHTML = '<i class="fas fa-expand me-1"></i> เต็มหน้าจอ';
                            }
                        }

                        // Auto-adjust PDF height to viewport
                        window.addEventListener('load', function() {
                            const pdfArea = document.getElementById('pdf-display-area');
                            if (pdfArea) {
                                const windowHeight = window.innerHeight;
                                const offset = pdfArea.getBoundingClientRect().top;
                                const newHeight = windowHeight - offset - 50; // Leave some margin
                                pdfArea.style.height = newHeight + 'px';
                            }
                        });

                        // Adjust on window resize
                        window.addEventListener('resize', function() {
                            const pdfArea = document.getElementById('pdf-display-area');
                            if (pdfArea && !document.fullscreenElement) {
                                const windowHeight = window.innerHeight;
                                const offset = pdfArea.getBoundingClientRect().top;
                                const newHeight = windowHeight - offset - 50;
                                pdfArea.style.height = newHeight + 'px';
                            }
                        });
                        </script>

                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-footer-modern">
            ผู้ประกาศ: <?php echo htmlspecialchars($ann['full_name'] ?? 'admin'); ?> | สร้างเมื่อ: <?php echo date('d/m/Y H:i', strtotime($ann['created_at'])); ?>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'template.php';
?>


