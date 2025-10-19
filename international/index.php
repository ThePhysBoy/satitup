<?php
require_once '../header.php';
require_once '../navbar.php';

$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');

$sql = "SELECT id, title, person_name, role, affiliation, country, city, start_date, end_date, cover_image, published_date
        FROM international_assignments
        WHERE status = 'published'
        ORDER BY published_date DESC";
$result = $conn->query($sql);
$assignments = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<section class="international-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold mb-3">ประกาศการไปต่างประเทศ</h1>
                <p class="text-muted mb-0">รวบรวมข่าวสารการไปต่างประเทศของนักเรียนและบุคลากร โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (empty($assignments)): ?>
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <p>ยังไม่มีประกาศการไปต่างประเทศในขณะนี้</p>
                </div>
            <?php else: ?>
                <?php foreach ($assignments as $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow border-0 h-100 international-card">
                            <div class="international-image" style="background-image: url('../<?php echo htmlspecialchars($item['cover_image']); ?>');"></div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($item['person_name']); ?></h5>
                                <?php if (!empty($item['role'])): ?>
                                    <p class="text-muted mb-1"><i class="fas fa-id-badge me-2"></i><?php echo htmlspecialchars($item['role']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['affiliation'])): ?>
                                    <p class="text-muted mb-1"><i class="fas fa-school me-2"></i><?php echo htmlspecialchars($item['affiliation']); ?></p>
                                <?php endif; ?>
                                <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt me-2 text-danger"></i><?php echo htmlspecialchars($item['city'] ? $item['city'] . ', ' : '') . htmlspecialchars($item['country']); ?></p>
                                <h6 class="fw-bold text-primary mb-2"><?php echo htmlspecialchars($item['title']); ?></h6>
                                <?php if (!empty($item['start_date'])): ?>
                                    <p class="mb-2"><i class="fas fa-plane-departure me-2 text-success"></i>เริ่มเดินทาง: <?php echo date('d F Y', strtotime($item['start_date'])); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['end_date'])): ?>
                                    <p class="text-muted mb-3"><i class="fas fa-plane-arrival me-2 text-secondary"></i>กลับจากต่างประเทศ: <?php echo date('d F Y', strtotime($item['end_date'])); ?></p>
                                <?php endif; ?>
                                <div class="mt-auto">
                                    <a href="view.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary w-100"><i class="fas fa-eye me-2"></i>ดูรายละเอียด</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.international-card {
    border-radius: 20px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.international-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.international-image {
    position: relative;
    height: 220px;
    background-size: cover;
    background-position: center;
}
</style>

<?php include_once '../footer.php'; ?>
