<!-- Academic Programs Section -->
<style>
    .academic-programs-section {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    .academic-programs-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
        animation: float 20s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(120deg); }
        66% { transform: translate(-20px, 20px) rotate(240deg); }
    }
    
    .section-header {
        position: relative;
        z-index: 1;
        margin-bottom: 60px;
    }
    
    .section-header h2 {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
        animation: fadeInDown 0.8s ease;
    }
    
    .section-header p {
        font-size: 1.25rem;
        color: #666666;
        animation: fadeInUp 0.8s ease;
    }
    
    .program-container {
        position: relative;
        z-index: 1;
    }
    
    .program-card {
        background: white;
        border-radius: 20px;
        padding: 35px 25px;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.8s ease backwards;
    }
    
    .program-card:nth-child(1) { animation-delay: 0.1s; }
    .program-card:nth-child(2) { animation-delay: 0.2s; }
    .program-card:nth-child(3) { animation-delay: 0.3s; }
    .program-card:nth-child(4) { animation-delay: 0.4s; }
    
    .program-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        background-size: 200% 100%;
        animation: gradient 3s ease infinite;
    }
    
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .program-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .program-card:hover .program-icon {
        transform: rotateY(360deg) scale(1.1);
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .program-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.6s ease;
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        position: relative;
    }
    
    .program-icon::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: inherit;
        filter: blur(20px);
        opacity: 0.4;
        z-index: -1;
    }
    
    .program-icon i {
        font-size: 2.5rem;
        color: white;
    }
    
    .program-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        transition: color 0.3s ease;
    }
    
    .program-card:hover .program-title {
        color: #667eea;
    }
    
    .program-description {
        color: #666666;
        line-height: 1.7;
        margin-bottom: 25px;
        min-height: 100px;
        font-size: 0.95rem;
    }
    
    .program-features {
        list-style: none;
        padding: 0;
        margin: 20px 0;
        text-align: left;
    }
    
    .program-features li {
        padding: 8px 0;
        color: #666666;
        font-size: 0.9rem;
        position: relative;
        padding-left: 25px;
    }
    
    .program-features li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #667eea;
        font-weight: bold;
    }
    
    .btn-program {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        position: relative;
        overflow: hidden;
    }
    
    .btn-program::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-program:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .btn-program:hover::before {
        left: 100%;
    }
    
    .special-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 5px 10px rgba(240, 87, 108, 0.3);
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .section-header h2 {
            font-size: 2rem;
        }
        .program-card {
            margin-bottom: 20px;
        }
    }
</style>

<section class="academic-programs-section">
    <div class="container">
        <div class="section-header text-center">
            <h2>หลักสูตรการศึกษา</h2>
            <p>พัฒนาศักยภาพนักเรียนด้วยหลักสูตรที่หลากหลายและทันสมัย</p>
        </div>
        
        <div class="row program-container">
            <!-- หลักสูตรประถมศึกษา -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h4 class="program-title">หลักสูตรประถมศึกษา</h4>
                    <p class="program-description">
                        ปูพื้นฐานการเรียนรู้ที่แข็งแกร่ง พัฒนาทักษะพื้นฐานด้านภาษา คณิตศาสตร์ 
                        และวิทยาศาสตร์
                    </p>
                    <ul class="program-features">
                        <li>เรียนรู้แบบบูรณาการ</li>
                        <li>เน้นกิจกรรมสร้างสรรค์</li>
                        <li>พัฒนาทักษะชีวิต</li>
                    </ul>
                    <a href="curriculum/curriculum_primary.php" class="btn-program" target="_blank">
                        เรียนรู้เพิ่มเติม <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- ศิลปวิทยาศาสตร์ ม.ต้น -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4 class="program-title">ศิลปวิทยาศาสตร์ ม.ต้น</h4>
                    <p class="program-description">
                        พัฒนาความรู้ความสามารถรอบด้าน เตรียมพร้อมสู่ระดับที่สูงขึ้น
                    </p>
                    <ul class="program-features">
                        <li>วิทย์-คณิต เข้มข้น</li>
                        <li>ภาษาต่างประเทศ</li>
                        <li>ศิลปะและวัฒนธรรม</li>
                    </ul>
                    <a href="curriculum/curriculum_arts_science_lower.php" class="btn-program" target="_blank">
                        เรียนรู้เพิ่มเติม <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- ศิลปวิทยาศาสตร์ ม.ปลาย -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4 class="program-title">ศิลปวิทยาศาสตร์ ม.ปลาย</h4>
                    <p class="program-description">
                        มุ่งเน้นความเป็นเลิศทางวิชาการ เตรียมพร้อมสู่มหาวิทยาลัยชั้นนำ
                    </p>
                    <ul class="program-features">
                        <li>3 แผนการเรียน</li>
                        <li>เตรียมสอบ TCAS</li>
                        <li>โครงการแลกเปลี่ยน</li>
                    </ul>
                    <a href="curriculum/curriculum_arts_science_upper.php" class="btn-program" target="_blank">
                        เรียนรู้เพิ่มเติม <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- โครงการ วมว.มพ. -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="program-card">
                    <span class="special-badge">พิเศษ</span>
                    <div class="program-icon">
                        <i class="fas fa-atom"></i>
                    </div>
                    <h4 class="program-title">โครงการ วมว.มพ.</h4>
                    <p class="program-description">
                        ห้องเรียนวิทยาศาสตร์พิเศษ ความร่วมมือกับมหาวิทยาลัยพะเยา
                    </p>
                    <ul class="program-features">
                        <li>โควตา ม.พะเยา</li>
                        <li>ห้องปฏิบัติการพิเศษ</li>
                        <li>อาจารย์ผู้เชี่ยวชาญ</li>
                    </ul>
                    <a href="curriculum/curriculum_scius.php" class="btn-program" target="_blank">
                        เรียนรู้เพิ่มเติม <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="text-center mt-5">
            <p class="text-muted">
                <i class="fas fa-info-circle"></i> 
                สนใจสมัครเรียนหรือสอบถามเพิ่มเติม โทร. 054-466666
            </p>
        </div>
    </div>
</section>

<script>
// Add smooth scroll animation on page load
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.program-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>