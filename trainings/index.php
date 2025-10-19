<?php
require_once '../header.php';
require_once '../navbar.php';

$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');

$sql = "SELECT id, title, training_topic, host_department, training_type, published_date, registration_deadline, status
        FROM training_announcements
        WHERE status IN ('open','closed')
        ORDER BY published_date DESC";
$result = $conn->query($sql);
$list = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<section class="training-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold">ประกาศการอบรม</h1>
                <p class="text-muted">ติดตามข่าวอบรมและกิจกรรมพัฒนาศักยภาพจากโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="#" class="btn btn-primary"><i class="fas fa-file-alt me-2"></i>ดาวน์โหลดแบบฟอร์มลงทะเบียน</a>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>หัวข้อการอบรม</th>
                                <th>หน่วยงานผู้จัด</th>
                                <th>รูปแบบ</th>
                                <th>ประกาศ</th>
                                <th>ปิดรับ</th>
                                <th>สถานะ</th>
                                <th class="text-end">รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">ยังไม่มีประกาศการอบรมในขณะนี้</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['training_topic'] ?? $item['title']); ?></strong><br>
                                            <small class="text-muted">หัวข้อประกาศ: <?php echo htmlspecialchars($item['title']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['host_department'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($item['training_type'] ?? '-'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($item['published_date'])); ?></td>
                                        <td><?php echo !empty($item['registration_deadline']) ? date('d/m/Y', strtotime($item['registration_deadline'])) : '-'; ?></td>
                                        <td>
                                            <?php
                                            $badge = $item['status'] === 'open' ? 'success' : ($item['status'] === 'closed' ? 'secondary' : 'warning');
                                            $label = $item['status'] === 'open' ? 'เปิดรับสมัคร' : ($item['status'] === 'closed' ? 'สิ้นสุดการรับสมัคร' : 'รอดำเนินการ');
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>"><?php echo $label; ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="view.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once '../footer.php'; ?>
