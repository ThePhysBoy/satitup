<!-- Video Quick Links Section -->
<section class="video-quick-links-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">คลังวีดิทัศน์</h2>
            <p class="section-subtitle">รวมวีดิทัศน์แนะนำและกิจกรรมต่าง ๆ ของโรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
        </div>
        
        <div class="row g-4">
            <!-- Video Link: แนะนำโรงเรียน -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-school-intro" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>แนะนำโรงเรียน</h5>
                        <span class="video-link-subtitle">ข้อมูลทั่วไปและประวัติ</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
            
            <!-- Video Link: การทดลอง -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-experiments" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>การทดลอง</h5>
                        <span class="video-link-subtitle">วิทยาศาสตร์และกิจกรรม</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
            
            <!-- Video Link: การนำเสนอไปญี่ปุ่น -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-japan-presentation" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>นำเสนอไปญี่ปุ่น</h5>
                        <span class="video-link-subtitle">โครงการแลกเปลี่ยน</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
            
            <!-- Video Link: เรียนภาษาอังกฤษ -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-english-learning" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-language"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>เรียนภาษาอังกฤษ</h5>
                        <span class="video-link-subtitle">เทคนิคและบทเรียน</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
            
            <!-- Video Link: แนะนำมหาวิทยาลัย -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-university-intro" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>แนะนำมหาวิทยาลัย</h5>
                        <span class="video-link-subtitle">สู่รั้วอุดมศึกษา</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
            
            <!-- Video Link: กีฬา -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-sports" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>กีฬา</h5>
                        <span class="video-link-subtitle">กิจกรรมและการแข่งขัน</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
            
            <!-- Video Link: การแข่งขัน -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-competition" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>การแข่งขัน</h5>
                        <span class="video-link-subtitle">ผลงานนักเรียน</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
            
            <!-- Video Link: ไปออสเตรเลีย -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="#video-australia" class="video-link-card">
                    <div class="video-link-icon">
                        <i class="fas fa-kangaroo"></i>
                    </div>
                    <div class="video-link-content">
                        <h5>ไปออสเตรเลีย</h5>
                        <span class="video-link-subtitle">โครงการแลกเปลี่ยน</span>
                    </div>
                    <div class="video-link-hover">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- View All Videos Button -->
        <div class="text-center mt-5">
            <a href="#all-videos" class="btn btn-view-all">
                ดูวีดิทัศน์ทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Initialize Video Links Animation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoLinks = document.querySelectorAll('.video-link-card');
    videoLinks.forEach((link, index) => {
        setTimeout(() => {
            link.classList.add('animate-in');
        }, index * 70);
    });
});
</script>
