<?php
/**
 * News Dashboard
 * Shows statistics and overview of news system
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Get statistics
$stats = getNewsStatistics($conn);
$recent_news = getLatestNews($conn, 5);
$popular_news = getMostViewedNews($conn, 5);
// ไม่ดึงข้อมูลหมวดหมู่แล้วเพราะเราไม่ใช้หมวดหมู่อีกต่อไป
$categories = [];
// Fetch last 6 announcements (PDF based)
$ann_sql = "SELECT a.*, u.full_name
            FROM announcements a
            LEFT JOIN users u ON u.id = a.created_by
            ORDER BY a.created_at DESC
            LIMIT 6";
$ann_res = $conn->query($ann_sql);
$latest_ann = $ann_res ? $ann_res->fetch_all(MYSQLI_ASSOC) : [];

// Set page variables
$page_title = 'แดชบอร์ดระบบจัดการข่าว';
$page_header_icon = '<i class="fas fa-tachometer-alt me-2"></i>';

// Build content
ob_start();
?>

<div class="row">
    <!-- Quick Create Announcement (with PDF) -->
    <div class="col-12 mb-4">
        <div class="card-modern">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fas fa-bullhorn me-2"></i>ประกาศด่วนพร้อมแนบไฟล์ PDF</h6>
            </div>
            <div class="card-body-modern">
                <form method="post" action="quick_announce.php" enctype="multipart/form-data" class="row g-3" novalidate>
                    <div class="col-md-8">
                        <label class="form-label">หัวข้อประกาศ <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="กรอกหัวข้อประกาศ เช่น ประกวดราคาซื้อครุภัณฑ์..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">หมวดหมู่หลัก</label>
                        <select name="category" id="form-category" class="form-select">
                            <option value="announcement">คำสั่งและประกาศ</option>
                            <option value="procurement">การจัดซื้อจัดจ้าง</option>
                            <option value="recruitment">การรับสมัครงาน</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">วันที่ประกาศ</label>
                        <input type="date" name="announce_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ฝ่าย/หน่วยงาน</label>
                        <input type="text" name="department" class="form-control" value="ฝ่ายพัสดุ">
                    </div>
                    <div class="col-12">
                        <label class="form-label">รายละเอียดประกาศ</label>
                        <textarea name="content" rows="3" class="form-control" placeholder="พิมพ์รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                        <div class="form-text">หากมีรายละเอียดมาก แนบเป็น PDF และสรุปประเด็นสำคัญสั้นๆ ในช่องนี้</div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">แนบไฟล์ PDF</label>
                        <input type="file" name="pdf_file" accept="application/pdf" class="form-control" required>
                        <small class="text-muted">รองรับเฉพาะ PDF ขนาดไม่เกิน 10MB</small>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-modern w-100">
                            <i class="fas fa-upload me-1"></i> บันทึกประกาศ
                        </button>
                    </div>

                    <!-- Fields for sub-types (conditionally shown) -->
                    <div class="col-12">
                        <div id="block-announcement" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">ประเภทเอกสาร (สำหรับ: คำสั่งและประกาศ)</label>
                                <select name="doc_type" class="form-select">
                                    <option value="announcement">ประกาศ</option>
                                    <option value="order">คำสั่ง</option>
                                    <option value="regulation">ระเบียบ</option>
                                    <option value="rule">ข้อบังคับ</option>
                                    <option value="act">พระราชบัญญัติ</option>
                                </select>
                            </div>
                        </div>

                        <div id="block-procurement" class="row g-3" style="display:none">
                            <div class="col-md-4">
                                <label class="form-label">งบประมาณ (บาท)</label>
                                <input type="number" step="0.01" name="budget" class="form-control" placeholder="เช่น 2500000">
                            </div>
                        </div>

                        <div id="block-recruitment" class="row g-3" style="display:none">
                            <div class="col-md-4">
                                <label class="form-label">ประเภทงาน</label>
                                <select name="job_type" class="form-select">
                                    <option value="full-time">เต็มเวลา</option>
                                    <option value="part-time">นอกเวลา</option>
                                    <option value="contract">สัญญาจ้าง</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เงินเดือน/ค่าตอบแทน</label>
                                <input type="text" name="salary" class="form-control" placeholder="เช่น 20,000 - 25,000 บาท หรือ 300 บาท/ชั่วโมง">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Toggle sub-type blocks by category (announcement/order/regulation/rule/act vs procurement vs recruitment)
        document.addEventListener('DOMContentLoaded', function(){
            const cat = document.getElementById('form-category');
            const blockAnn = document.getElementById('block-announcement');
            const blockProc = document.getElementById('block-procurement');
            const blockRec = document.getElementById('block-recruitment');
            function updateBlocks(){
                const v = cat.value;
                blockAnn.style.display = (v === 'announcement') ? '' : 'none';
                blockProc.style.display = (v === 'procurement') ? '' : 'none';
                blockRec.style.display = (v === 'recruitment') ? '' : 'none';
            }
            cat.addEventListener('change', updateBlocks);
            updateBlocks();
        });
    </script>
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-primary border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-primary">ข่าวทั้งหมด</h6>
                        <div class="h3"><?php echo $stats['total']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-success border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-success">ข่าวที่เผยแพร่</h6>
                        <div class="h3"><?php echo $stats['published']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-warning border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-warning">ข่าวแบบร่าง</h6>
                        <div class="h3"><?php echo $stats['draft']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-edit fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-modern border-start border-info border-4">
            <div class="card-body-modern">
                <div class="row">
                    <div class="col">
                        <h6 class="text-info">ยอดการอ่านทั้งหมด</h6>
                        <div class="h3"><?php echo $stats['total_views']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Latest Announcements (PDF) -->
    <div class="col-12">
        <div class="card-modern mb-4">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fas fa-clipboard-list me-2"></i>ประกาศล่าสุด</h6>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>หัวข้อ</th>
                                <th>งบประมาณ</th>
                                <th>สถานะ</th>
                                <th>วันที่</th>
                                <th>ฝ่าย</th>
                                <th>ผู้ประกาศ</th>
                                <th>ไฟล์</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($latest_ann) > 0): ?>
                                <?php foreach ($latest_ann as $row): ?>
                                <tr>
                                    <td>
                                        <a class="text-decoration-none" href="view_announcement.php?id=<?php echo $row['id']; ?>">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $row['budget'] !== null ? number_format($row['budget'], 0) . ' บาท' : '-'; ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'open'): ?>
                                            <span class="badge bg-info">เปิดรับ</span>
                                        <?php elseif ($row['status'] === 'result'): ?>
                                            <span class="badge bg-primary">ประกาศผล</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ปิดรับ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['announce_date'] ? date('d/m/Y', strtotime($row['announce_date'])) : date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['department'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['full_name'] ?? 'admin'); ?></td>
                                    <td>
                                        <?php if (!empty($row['file_path'])): ?>
                                            <a class="btn btn-sm btn-outline-primary" href="../<?php echo $row['file_path']; ?>" target="_blank">
                                                <i class="fas fa-file-pdf"></i> เปิดไฟล์
                                            </a>
                                        <?php else: ?>-
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-warning" href="edit_announcement.php?id=<?php echo $row['id']; ?>">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>
                                        <form method="post" action="delete_announcement.php" style="display:inline" onsubmit="return confirm('ยืนยันการลบประกาศนี้?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">ยังไม่มีประกาศ</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent News -->
    <div class="col-lg-6">
        <div class="card-modern mb-4">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fas fa-clock me-2"></i>ข่าวล่าสุด</h6>
                <a href="index.php" class="btn btn-sm btn-primary btn-modern">ดูทั้งหมด</a>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>หัวข้อ</th>
                                <th>วันที่</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_news) > 0): ?>
                                <?php foreach ($recent_news as $news): ?>
                                    <tr>
                                        <td>
                                            <a href="edit_new.php?id=<?php echo $news['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars(mb_strimwidth($news['title'], 0, 40, '...')); ?>
                                            </a>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($news['created_at'])); ?></td>
                                        <td>
                                            <?php if ($news['status'] == 'published'): ?>
                                                <span class="badge bg-success">เผยแพร่</span>
                                            <?php elseif ($news['status'] == 'draft'): ?>
                                                <span class="badge bg-warning text-dark">แบบร่าง</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">รอตรวจสอบ</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">ไม่พบข้อมูลข่าว</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular News -->
    <div class="col-lg-6">
        <div class="card-modern mb-4">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fas fa-fire me-2"></i>ข่าวยอดนิยม</h6>
                <a href="index.php?sort=views" class="btn btn-sm btn-primary btn-modern">ดูทั้งหมด</a>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>หัวข้อ</th>
                                <th>ยอดอ่าน</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($popular_news) > 0): ?>
                                <?php foreach ($popular_news as $news): ?>
                                    <tr>
                                        <td>
                                            <a href="edit_new.php?id=<?php echo $news['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars(mb_strimwidth($news['title'], 0, 40, '...')); ?>
                                            </a>
                                        </td>
                                        <td><i class="fas fa-eye me-1"></i><?php echo $news['views']; ?></td>
                                        <td>
                                            <?php if ($news['status'] == 'published'): ?>
                                                <span class="badge bg-success">เผยแพร่</span>
                                            <?php elseif ($news['status'] == 'draft'): ?>
                                                <span class="badge bg-warning text-dark">แบบร่าง</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">รอตรวจสอบ</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">ไม่พบข้อมูลข่าว</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- ไม่แสดงสถิติหมวดหมู่อีกต่อไปเพราะเราไม่ใช้หมวดหมู่อีกแล้ว -->

    <!-- Quick Actions -->
    <div class="col-lg-6">
        <div class="card-modern mb-4">
            <div class="card-header-modern">
                <h6 class="m-0"><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h6>
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="create.php" class="btn btn-success btn-modern w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i>
                            <div>เพิ่มข่าวใหม่</div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="search.php" class="btn btn-warning btn-modern w-100 py-3">
                            <i class="fas fa-search fa-2x mb-2"></i>
                            <div>ค้นหาข่าว</div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="export_form.php" class="btn btn-primary btn-modern w-100 py-3">
                            <i class="fas fa-file-export fa-2x mb-2"></i>
                            <div>ส่งออกข้อมูล</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the template
include 'template.php';
?>