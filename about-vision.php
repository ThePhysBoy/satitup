<?php
// Session & environment setup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

$isAdminLoggedIn = isset($_SESSION['user_id'], $_SESSION['role'])
    && in_array($_SESSION['role'], ['admin', 'pr_officer'], true);

include_once 'header.php';
include_once 'navbar.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --vision-primary: #7F6CB2;
        --vision-secondary: #4B7CC2;
        --vision-text-dark: #2c3e50;
        --vision-text-medium: #5d648c;
        --vision-text-light: #888fb0;
    }

    body {
        font-family: 'Sarabun', sans-serif;
        background: linear-gradient(135deg, #f8f9ff 0%, #f1f3ff 50%, #ffffff 100%);
        color: var(--vision-text-dark);
    }

    .vision-wrapper {
        max-width: 1100px;
        margin: 60px auto;
        padding: 0 20px 60px;
    }

    .vision-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 25px 60px rgba(115, 125, 182, 0.15);
        overflow: hidden;
    }

    .card-header-gradient {
        background: linear-gradient(135deg, #7F6CB2 0%, #4B7CC2 100%);
        padding: 40px 50px;
        color: #ffffff;
        position: relative;
    }

    .card-header-gradient::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, rgba(255, 255, 255, 0.25), transparent 70%);
        opacity: 1;
        pointer-events: none;
    }

    .card-title {
        font-size: 2.6rem;
        font-weight: 700;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .card-subtitle {
        font-size: 1.2rem;
        opacity: 0.85;
        position: relative;
        z-index: 1;
    }

    .content-section {
        padding: 40px 50px;
        display: grid;
        gap: 32px;
    }

    .content-block {
        background: #f8f9ff;
        border-radius: 18px;
        padding: 28px 32px;
        border: 1px solid rgba(127, 108, 178, 0.15);
        position: relative;
        overflow: hidden;
    }

    .content-block::before {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle at center, rgba(127, 108, 178, 0.15), transparent 70%);
        top: -90px;
        right: -60px;
    }

    .content-block h3 {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #4B4F93;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .content-block h3 i {
        font-size: 1.4rem;
        color: #7F6CB2;
    }

    .thai-script {
        font-size: 1.1rem;
        color: #2f3d6e;
        margin-bottom: 8px;
        line-height: 1.8;
    }

    .pali-script {
        font-size: 1rem;
        color: #566089;
        margin-bottom: 8px;
        font-style: italic;
    }

    .english-script {
        font-size: 1rem;
        color: #6b739c;
        margin-bottom: 0;
    }

    .mission-list, .values-list {
        display: grid;
        gap: 16px;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .mission-item, .values-item {
        display: flex;
        gap: 14px;
        background: #ffffff;
        border-radius: 14px;
        padding: 18px 22px;
        border: 1px solid rgba(127, 108, 178, 0.12);
        box-shadow: 0 6px 14px rgba(103, 114, 169, 0.08);
        align-items: flex-start;
    }

    .mission-item .number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7F6CB2 0%, #4B7CC2 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        flex-shrink: 0;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        margin-top: 10px;
    }

    .values-item h4 {
        font-size: 1.15rem;
        margin-bottom: 6px;
        color: #413C8F;
        font-weight: 600;
    }

    .values-item p {
        margin: 0;
        color: #5d648c;
        line-height: 1.5;
        font-size: 0.95rem;
    }

    .signature-block {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 1rem;
        color: #5a628a;
        margin-top: 12px;
    }

    .admin-note {
        margin-top: 20px;
        font-size: 0.95rem;
        color: #888fb0;
    }

    @media (max-width: 768px) {
        .card-header-gradient {
            padding: 32px 28px;
            text-align: center;
        }

        .card-title {
            font-size: 2rem;
        }

        .content-section {
            padding: 28px;
        }

        .content-block {
            padding: 24px;
        }
    }
</style>

<div class="vision-wrapper">
    <div class="vision-card">
        <div class="card-header-gradient">
            <h1 class="card-title">ปรัชญา วิสัยทัศน์ และพันธกิจ</h1>
            <p class="card-subtitle">โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
        </div>

        <div class="content-section">
            <section class="content-block">
                <h3><i class="fas fa-yin-yang"></i> ปรัชญา</h3>
                <p class="pali-script">“ปญฺญาชีวี เสฏฐชีวี นาม” (ปัญญาชีวี เสฏฐะชีวี นาม)</p>
                <p class="thai-script">ดำรงชีวิตด้วยปัญญาประเสริฐที่สุด</p>
                <p class="english-script">A Life of Wisdom Is the Most Wondrous of All</p>
            </section>

            <section class="content-block">
                <h3><i class="fas fa-bullseye"></i> ปณิธาน</h3>
                <p class="thai-script">“ปัญญาระดับรากฐานคือความเข้มแข็งของเยาวชนในชุมชน”</p>
            </section>

            <section class="content-block">
                <h3><i class="fas fa-lightbulb"></i> วิสัยทัศน์</h3>
                <p class="thai-script">“โรงเรียนศิลปวิทยาศาสตร์ต้นแบบด้วยนวัตกรรมการศึกษา”</p>
            </section>

            <section class="content-block">
                <h3><i class="fas fa-flag-checkered"></i> พันธกิจ</h3>
                <ul class="mission-list">
                    <li class="mission-item">
                        <span class="number">1</span>
                        <span>จัดการเรียนการสอนตามแนวทางศิลปะวิทยาศาสตร์ (Liberal Art Education)</span>
                    </li>
                    <li class="mission-item">
                        <span class="number">2</span>
                        <span>วิจัยและนวัตกรรมด้านการเรียนการสอน เพื่อเผยแพร่และสร้างเครือข่ายทางด้านวิชาการ</span>
                    </li>
                    <li class="mission-item">
                        <span class="number">3</span>
                        <span>ทำนุบำรุงศิลปะ วัฒนธรรม ภูมิปัญญาท้องถิ่น อนุรักษ์พลังงานและสิ่งแวดล้อม</span>
                    </li>
                    <li class="mission-item">
                        <span class="number">4</span>
                        <span>บริหารจัดการที่ทันสมัย โปร่งใส มีธรรมาภิบาล และมีประสิทธิภาพ</span>
                    </li>
                </ul>
            </section>

            <section class="content-block">
                <h3><i class="fas fa-user-graduate"></i> อัตลักษณ์นักเรียน</h3>
                <p class="thai-script">สุนทรียภาพ สุขภาพ และบุคลิกภาพ</p>
            </section>

            <section class="content-block">
                <h3><i class="fas fa-handshake"></i> ค่านิยมองค์กร T-E-A-M</h3>
                <div class="values-grid">
                    <div class="values-item">
                        <div>
                            <h4>T — Transformation</h4>
                            <p>ปรับเปลี่ยนและพัฒนาสู่ความเป็นเลิศอย่างต่อเนื่อง</p>
                        </div>
                    </div>
                    <div class="values-item">
                        <div>
                            <h4>E — Excellence</h4>
                            <p>มุ่งมั่นสร้างคุณภาพในทุกมิติของการจัดการศึกษา</p>
                        </div>
                    </div>
                    <div class="values-item">
                        <div>
                            <h4>A — Appreciation</h4>
                            <p>ให้คุณค่าและเคารพศักยภาพของทุกคนในชุมชนการเรียนรู้</p>
                        </div>
                    </div>
                    <div class="values-item">
                        <div>
                            <h4>M — Management</h4>
                            <p>บริหารจัดการด้วยหลักธรรมาภิบาล ความรับผิดชอบ และประสิทธิภาพ</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content-block">
                <h3><i class="fas fa-feather-alt"></i> ลงนาม</h3>
                <div class="signature-block">
                    <span>รองศาสตราจารย์ ดร.ชยันต์ บุณยรักษ์</span>
                    <span>ผู้อำนวยการโรงเรียนสาธิตมหาวิทยาลัยพะเยา</span>
                </div>
                <?php if ($isAdminLoggedIn): ?>
                <div class="admin-note">
                    * ผู้ดูแลระบบสามารถแก้ไขข้อมูลได้ที่ <a href="admin/management/index.php">ระบบผู้บริหาร</a>
                </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>

