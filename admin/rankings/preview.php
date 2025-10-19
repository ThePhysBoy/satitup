<?php
/**
 * Preview University Ranking Item (Admin)
 */

$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

requireRankingsAccess();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare('SELECT * FROM university_rankings WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$ranking = $result->fetch_assoc();

$additional_links = [];
if (!empty($ranking['additional_links'])) {
    $decoded = json_decode($ranking['additional_links'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $additional_links = $decoded;
    }
}

?><!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูตัวอย่างการจัดอันดับ - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .preview-container {
            max-width: 960px;
            margin: 30px auto;
        }
        .ranking-banner {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
        }
        .ranking-banner img {
            width: 100%;
            display: block;
            object-fit: cover;
            max-height: 420px;
        }
        .ranking-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.55), rgba(0,0,0,0.75));
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 30px;
            color: #fff;
        }
        .badge-theme {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.85rem;
            backdrop-filter: blur(3px);
        }
        .ranking-content-box {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15,30,70,0.12);
            padding: 30px;
        }
        .meta-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .meta-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(78,115,223,0.1);
            color: #4e73df;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .meta-label {
            font-weight: 600;
            color: #2f2f33;
            margin-bottom: 4px;
        }
        .meta-value {
            color: #555;
        }
        .highlight-box {
            background: rgba(255, 215, 0, 0.15);
            border-left: 4px solid #f59e0b;
            padding: 18px 22px;
            border-radius: 12px;
            color: #5a4100;
        }
        .additional-links a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .preview-container {
                margin: 15px;
            }
            .ranking-banner img {
                max-height: 260px;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0"><i class="fas fa-eye me-2 text-primary"></i>ดูตัวอย่างการจัดอันดับ</h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>ย้อนกลับ</a>
        </div>

        <div class="ranking-banner mb-4">
            <?php
            $imagePath = !empty($ranking['image_path']) ? '../../' . $ranking['image_path'] : '../assets/no-image.png';
            ?>
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($ranking['title']); ?>">
            <div class="ranking-overlay">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge-theme"><i class="fas fa-calendar-alt me-2"></i><?php echo !empty($ranking['publication_date']) ? date('d/m/Y', strtotime($ranking['publication_date'])) : 'ไม่ระบุวันที่ประกาศ'; ?></span>
                    <?php if (!empty($ranking['ranking_year'])): ?>
                        <span class="badge-theme"><i class="fas fa-award me-2"></i>ปี <?php echo htmlspecialchars($ranking['ranking_year']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($ranking['ranking_category'])): ?>
                        <span class="badge-theme"><i class="fas fa-layer-group me-2"></i><?php echo htmlspecialchars($ranking['ranking_category']); ?></span>
                    <?php endif; ?>
                </div>
                <h2 class="display-6 fw-bold mb-2"><?php echo htmlspecialchars($ranking['title']); ?></h2>
                <?php if (!empty($ranking['ranking_organization'])): ?>
                    <p class="mb-0"><i class="fas fa-building-columns me-2"></i><?php echo htmlspecialchars($ranking['ranking_organization']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="ranking-content-box">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <?php if (!empty($ranking['ranking_position'])): ?>
                    <div class="meta-item">
                        <div class="meta-icon"><i class="fas fa-trophy"></i></div>
                        <div>
                            <div class="meta-label">อันดับที่ได้รับ</div>
                            <div class="meta-value"><?php echo htmlspecialchars($ranking['ranking_position']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($ranking['ranking_score'])): ?>
                    <div class="meta-item">
                        <div class="meta-icon"><i class="fas fa-chart-line"></i></div>
                        <div>
                            <div class="meta-label">คะแนน</div>
                            <div class="meta-value"><?php echo number_format($ranking['ranking_score'], 2); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($ranking['link'])): ?>
                    <div class="meta-item">
                        <div class="meta-icon"><i class="fas fa-link"></i></div>
                        <div>
                            <div class="meta-label">ลิงก์ต้นฉบับ</div>
                            <div class="meta-value"><a href="<?php echo htmlspecialchars($ranking['link']); ?>" target="_blank">เปิดดูข่าว</a></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <?php if (!empty($ranking['ranking_criteria'])): ?>
                    <div class="meta-item">
                        <div class="meta-icon"><i class="fas fa-clipboard-check"></i></div>
                        <div>
                            <div class="meta-label">เกณฑ์การจัดอันดับ</div>
                            <div class="meta-value"><?php echo nl2br(htmlspecialchars($ranking['ranking_criteria'])); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($ranking['color_theme'])): ?>
                    <div class="meta-item">
                        <div class="meta-icon"><i class="fas fa-palette"></i></div>
                        <div>
                            <div class="meta-label">ธีมสี</div>
                            <div class="meta-value"><span class="badge" style="background: <?php echo htmlspecialchars($ranking['color_theme']); ?>;">ธีมสี</span></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($ranking['achievement_highlights'])): ?>
            <div class="highlight-box mb-4">
                <h5 class="fw-bold mb-2"><i class="fas fa-star me-2"></i>จุดเด่นที่ได้รับการจัดอันดับ</h5>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($ranking['achievement_highlights'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($ranking['description'])): ?>
            <div class="mb-4">
                <h5 class="fw-bold">รายละเอียด</h5>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($ranking['description'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($additional_links)): ?>
            <div class="additional-links mb-3">
                <h6 class="fw-bold">ลิงก์เพิ่มเติม</h6>
                <?php foreach ($additional_links as $label => $url): ?>
                    <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt"></i><?php echo htmlspecialchars($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="text-end mt-4">
                <a href="edit.php?id=<?php echo $ranking['id']; ?>" class="btn btn-primary"><i class="fas fa-edit me-2"></i>แก้ไขข้อมูล</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

