<?php
require_once '../header.php';
require_once '../navbar.php';

$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');

$sql = "SELECT id, title, reference_number, procurement_method, department, published_date, closing_date, document_pdf, status
        FROM procurement_announcements
        WHERE status IN ('published', 'closed')
        ORDER BY published_date DESC";
$result = $conn->query($sql);
$announcements = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<section class="procurement-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold">ประกาศจัดซื้อจัดจ้าง</h1>
                <p class="text-muted">ติดตามประกาศเชิญชวนเสนอราคาและผลการจัดซื้อจัดจ้างล่าสุดของโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="#" class="btn btn-primary"><i class="fas fa-file-pdf me-2"></i>แบบฟอร์มสำคัญ</a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ชื่อประกาศ</th>
                                <th scope="col">เลขที่อ้างอิง</th>
                                <th scope="col">วิธีจัดซื้อจัดจ้าง</th>
                                <th scope="col">วันที่ประกาศ</th>
                                <th scope="col">สิ้นสุด</th>
                                <th scope="col">สถานะ</th>
                                <th scope="col" class="text-end">ดูรายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($announcements)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">ยังไม่มีประกาศจัดซื้อจัดจ้างในขณะนี้</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($announcements as $announce): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($announce['title']); ?></strong><br>
                                            <small class="text-muted">หน่วยงาน: <?php echo htmlspecialchars($announce['department'] ?? '-'); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($announce['reference_number'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($announce['procurement_method'] ?? '-'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($announce['published_date'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($announce['closing_date'])); ?></td>
                                        <td>
                                            <?php
                                            $badge = 'secondary';
                                            $label = 'ฉบับร่าง';
                                            if ($announce['status'] === 'published') { $badge = 'success'; $label = 'เปิดรับเสนอราคา'; }
                                            elseif ($announce['status'] === 'closed') { $badge = 'secondary'; $label = 'สิ้นสุดแล้ว'; }
                                            elseif ($announce['status'] === 'cancelled') { $badge = 'danger'; $label = 'ยกเลิก'; }
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>"><?php echo $label; ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="view.php?id=<?php echo $announce['id']; ?>" class="btn btn-outline-primary btn-sm" title="ดูรายละเอียด">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
