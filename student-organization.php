<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>องค์การนักเรียน - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" integrity="sha512-j2kbVxzAfz5WNjLqKJ2V//ZhzUtw3d7tEjkSDmMNKu/Njj+7r1OqpV+HWq5iHMkKcQJ1u" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #8B7AA8;
            --secondary-color: #A698BC;
            --accent-color: #F9A826;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            color: #333;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .section-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 40px;
            transition: transform 0.3s ease;
        }

        .section-card:hover {
            transform: translateY(-5px);
        }

        .org-structure {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 60px 0;
        }

        .activity-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .activity-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .activity-image {
            height: 200px;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .activity-content {
            padding: 20px;
        }

        .leader-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .leader-avatar {
            width: 120px;
            height: 120px;
            background: var(--primary-color);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-users me-3"></i>
                    องค์การนักเรียน
                </h1>
                <p class="lead">
                    สถาบันหลักที่พัฒนาผู้นำนักเรียน ส่งเสริมการมีส่วนร่วม และสร้างประสบการณ์การเรียนรู้ที่ยั่งยืน
                </p>
            </div>
        </div>
    </div>
</section>

<!-- คณะกรรมการองค์การนักเรียน -->
<section class="org-structure">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold" style="color: var(--primary-color);">คณะกรรมการองค์การนักเรียน</h2>
                <p class="lead text-muted">ประจำปีการศึกษา <?php echo date('Y') + 543; ?></p>
            </div>
        </div>

        <div class="row g-4">
            <!-- นายกองค์การนักเรียน -->
            <div class="col-lg-4 col-md-6">
                <div class="leader-card">
                    <div class="leader-avatar">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h5 class="fw-bold">นายกองค์การนักเรียน</h5>
                    <p class="text-muted">ผู้นำสูงสุดขององค์การนักเรียน</p>
                    <small class="text-primary">รับผิดชอบการบริหารงานองค์การนักเรียนทั้งหมด</small>
                </div>
            </div>

            <!-- รองนายกองค์การนักเรียน -->
            <div class="col-lg-4 col-md-6">
                <div class="leader-card">
                    <div class="leader-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h5 class="fw-bold">รองนายกองค์การนักเรียน</h5>
                    <p class="text-muted">ผู้ช่วยเหลือและสนับสนุนนายก</p>
                    <small class="text-primary">ช่วยเหลือการปฏิบัติงานและบริหารกิจกรรม</small>
                </div>
            </div>

            <!-- เลขานุการ -->
            <div class="col-lg-4 col-md-6">
                <div class="leader-card">
                    <div class="leader-avatar">
                        <i class="fas fa-clipboard"></i>
                    </div>
                    <h5 class="fw-bold">เลขานุการ</h5>
                    <p class="text-muted">ผู้จัดการเอกสารและข้อมูล</p>
                    <small class="text-primary">จัดทำบันทึกการประชุมและเอกสารสำคัญ</small>
                </div>
            </div>

            <!-- เหรัญญิก -->
            <div class="col-lg-4 col-md-6">
                <div class="leader-card">
                    <div class="leader-avatar">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h5 class="fw-bold">เหรัญญิก</h5>
                    <p class="text-muted">ผู้จัดการด้านการเงิน</p>
                    <small class="text-primary">บริหารจัดการงบประมาณและบัญชี</small>
                </div>
            </div>

            <!-- ประชาสัมพันธ์ -->
            <div class="col-lg-4 col-md-6">
                <div class="leader-card">
                    <div class="leader-avatar">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h5 class="fw-bold">ฝ่ายประชาสัมพันธ์</h5>
                    <p class="text-muted">ผู้รับผิดชอบการสื่อสาร</p>
                    <small class="text-primary">เผยแพร่ข้อมูลข่าวสารและกิจกรรม</small>
                </div>
            </div>

            <!-- ฝ่ายกิจกรรม -->
            <div class="col-lg-4 col-md-6">
                <div class="leader-card">
                    <div class="leader-avatar">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5 class="fw-bold">ฝ่ายกิจกรรม</h5>
                    <p class="text-muted">ผู้จัดการกิจกรรมนักเรียน</p>
                    <small class="text-primary">วางแผนและดำเนินกิจกรรมต่างๆ</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- กิจกรรมองค์การนักเรียน -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold" style="color: var(--primary-color);">กิจกรรมหลัก</h2>
                <p class="lead text-muted">กิจกรรมที่ส่งเสริมการพัฒนานักเรียนอย่างรอบด้าน</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="activity-card">
                    <div class="activity-image" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <div class="activity-content">
                        <h5 class="fw-bold mb-3">กิจกรรมพัฒนาผู้นำ</h5>
                        <p class="text-muted">พัฒนาทักษะความเป็นผู้นำ การทำงานเป็นทีม และการแก้ปัญหา</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="activity-card">
                    <div class="activity-image" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="activity-content">
                        <h5 class="fw-bold mb-3">กิจกรรมจิตอาสา</h5>
                        <p class="text-muted">ส่งเสริมจิตสำนึกที่ดีต่อสังคมและการช่วยเหลือผู้อื่น</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="activity-card">
                    <div class="activity-image" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="activity-content">
                        <h5 class="fw-bold mb-3">กิจกรรมส่งเสริมสิ่งแวดล้อม</h5>
                        <p class="text-muted">สร้างจิตสำนึกในการอนุรักษ์สิ่งแวดล้อมและทรัพยากรธรรมชาติ</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="activity-card">
                    <div class="activity-image" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="activity-content">
                        <h5 class="fw-bold mb-3">กิจกรรมศิลปวัฒนธรรม</h5>
                        <p class="text-muted">ส่งเสริมและรักษาวัฒนธรรมไทย รวมถึงกิจกรรมทางศิลปะ</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="activity-card">
                    <div class="activity-image" style="background: linear-gradient(135deg, #ffecd2, #fcb69f);">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="activity-content">
                        <h5 class="fw-bold mb-3">กิจกรรมกีฬาและนันทนาการ</h5>
                        <p class="text-muted">ส่งเสริมสุขภาพกายและใจผ่านกิจกรรมกีฬาและนันทนาการ</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="activity-card">
                    <div class="activity-image" style="background: linear-gradient(135deg, #a8edea, #fed6e3);">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="activity-content">
                        <h5 class="fw-bold mb-3">กิจกรรมสัมพันธ์</h5>
                        <p class="text-muted">สร้างความสัมพันธ์ที่ดีระหว่างนักเรียน ครู และชุมชน</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- เป้าหมายและพันธกิจ -->
<section class="py-5" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-card">
                    <h2 class="text-center fw-bold mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-bullseye me-3"></i>
                        เป้าหมายและพันธกิจ
                    </h2>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-star me-2"></i>
                                เป้าหมาย
                            </h5>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    พัฒนานักเรียนให้เป็นผู้นำที่มีคุณภาพ
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    ส่งเสริมการมีส่วนร่วมในกิจกรรมโรงเรียน
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    สร้างประสบการณ์การเรียนรู้ที่ยั่งยืน
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    เสริมสร้างคุณธรรมและจริยธรรม
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-flag me-2"></i>
                                พันธกิจ
                            </h5>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    จัดกิจกรรมที่เป็นประโยชน์ต่อนักเรียน
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    สนับสนุนและพัฒนาศักยภาพนักเรียน
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    สร้างความสัมพันธ์ที่ดีกับชุมชน
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    เป็นตัวแทนนักเรียนในการแสดงความคิดเห็น
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
