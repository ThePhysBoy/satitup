<?php
require_once '../header.php';
require_once '../navbar.php';

$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');

$sql = "SELECT id, title, position_title, department, employment_type, number_of_positions, published_date, application_deadline, status
        FROM recruitment_announcements
        WHERE status IN ('open','closed')
        ORDER BY published_date DESC";
$result = $conn->query($sql);
$recruitments = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<section class="recruitment-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold">ประกาศรับสมัครงาน</h1>
                <p class="text-muted">ติดตามตำแหน่งงานว่างและโอกาสร่วมงานกับโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="#" class="btn btn-primary"><i class="fas fa-file-alt me-2"></i>ดาวน์โหลดใบสมัคร</a>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ตำแหน่ง</th>
                                <th>หน่วยงาน</th>
                                <th>รูปแบบการจ้างงาน</th>
                                <th>จำนวน</th>
                                <th>ประกาศ</th>
                                <th>ปิดรับ</th>
                                <th>สถานะ</th>
                                <th class="text-end">ดูรายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recruitments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">ยังไม่มีประกาศรับสมัครงานในขณะนี้</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recruitments as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['position_title'] ?? $item['title']); ?></strong><br>
                                            <small class="text-muted">หัวข้อ: <?php echo htmlspecialchars($item['title']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['department'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($item['employment_type'] ?? '-'); ?></td>
                                        <td><?php echo (int)($item['number_of_positions'] ?? 1); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($item['published_date'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($item['application_deadline'])); ?></td>
                                        <td>
                                            <?php
                                            $status = $item['status'];
                                            $badge = $status === 'open' ? 'success' : ($status === 'closed' ? 'secondary' : 'warning');
                                            $label = $status === 'open' ? 'เปิดรับสมัคร' : ($status === 'closed' ? 'ปิดรับสมัคร' : 'รอตรวจสอบ');
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
