<!-- Quick Links Section -->
<link href="css/quick-links-fix.css" rel="stylesheet">
<style>
/* Override สำหรับแสดงรูป icons */
.quick-link-icon-image {
    width: 200px !important;
    height: 200px !important;
    margin: 0 auto 15px !important;
    border-radius: 18px !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15) !important;
    overflow: visible !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: transparent !important;
    padding: 0 !important;
}



/* ซ่อน Font Awesome icons ถ้ามี */
.quick-link-icon {
    display: none !important;
}

/* ซ่อนข้อความใต้ไอคอน เพื่อให้เห็นไอคอนใหญ่ชัดเจน */
.quick-link-content {
    display: none !important;
}

/* ซ่อนลูกศรใต้ไอคอน */
.quick-link-hover {
    display: none !important;
}

.manual-text {
    margin-top: 4px;
    text-align: center;
    --manual-color1: #667eea;
    --manual-color2: #764ba2;
    --manual-shadow: rgba(102, 126, 234, 0.35);
}

.manual-text-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--manual-color1), var(--manual-color2));
    color: #fff;
    font-size: 16px;
    text-decoration: none;
    box-shadow: 0 6px 14px var(--manual-shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.manual-text-link:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 22px var(--manual-shadow);
    background: linear-gradient(135deg, var(--manual-color2), var(--manual-color1));
    color: #fff;
}
</style>
<section class="quick-links-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">บริการออนไลน์</h2>
        </div>
        
        <!-- Centered Grid Container for better alignment -->
        <div class="row g-3 justify-content-center quick-links-grid">
            <!-- Link 1: จองห้องประชุมออนไลน์ -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://sites.google.com/up.ac.th/desupbooking" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/1.jpg" alt="จองห้องประชุมออนไลน์">
                        </div>
                        <div class="quick-link-content">
                            <h5>จองห้องประชุมออนไลน์</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#6f3df4; --manual-color2:#a855f7; --manual-shadow:rgba(111, 61, 244, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="booking-room" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Link 2: จองห้องประชุม -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://sites.google.com/up.ac.th/meeting-calendar" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/2.jpg" alt="จองห้องประชุม">
                        </div>
                        <div class="quick-link-content">
                            <h5>จองห้องประชุม</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#1d4ed8; --manual-color2:#3b82f6; --manual-shadow:rgba(59, 130, 246, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="meeting-calendar" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Link 3: UP DMS -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://dms.up.ac.th/dms_main/data/login.aspx" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/3.png" alt="UP DMS">
                        </div>
                        <div class="quick-link-content">
                            <h5>UP DMS</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#0ea5e9; --manual-color2:#38bdf8; --manual-shadow:rgba(14, 165, 233, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="up-dms" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Link 4: Smart HR -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://smarthr.up.ac.th/smart/main/Defaultpage/default.aspx" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/4.png" alt="Smart HR">
                        </div>
                        <div class="quick-link-content">
                            <h5>Smart HR</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#16a34a; --manual-color2:#22c55e; --manual-shadow:rgba(34, 197, 94, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="smart-hr" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Link 5: ติดตามเอกสารการเงิน -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://dev.citcoms.up.ac.th/track" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/5.png" alt="ติดตามเอกสารการเงิน">
                        </div>
                        <div class="quick-link-content">
                            <h5>ติดตามเอกสารการเงิน</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#fb923c; --manual-color2:#f97316; --manual-shadow:rgba(249, 115, 22, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="finance-track" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Link 6: วัสดุคงคลัง (IMS) -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://finance.up.ac.th/ims/Main/DefaultPage/" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/6.png" alt="วัสดุคงคลัง (IMS)">
                        </div>
                        <div class="quick-link-content">
                            <h5>วัสดุคงคลัง (IMS)</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#f87171; --manual-color2:#ef4444; --manual-shadow:rgba(239, 68, 68, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="ims" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Link 7: UP Mail -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://mail.google.com" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/7.png" alt="UP Mail">
                        </div>
                        <div class="quick-link-content">
                            <h5>UP Mail</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#a855f7; --manual-color2:#7c3aed; --manual-shadow:rgba(168, 85, 247, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="upmail" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Link 8: eBudget -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://finance.up.ac.th/upreceipt/Main/DefaultPage/login.aspx" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/8.png" alt="eBudget">
                        </div>
                        <div class="quick-link-content">
                            <h5>eBudget</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#06b6d4; --manual-color2:#0ea5e9; --manual-shadow:rgba(6, 182, 212, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="ebudget" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Link 9: ค่าบำรุงการศึกษา/ค่าธรรมเนียม -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://academic.satit.up.ac.th/" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/9.png" alt="ค่าบำรุงการศึกษา/ค่าธรรมเนียม">
                        </div>
                        <div class="quick-link-content">
                            <h5>ค่าบำรุงการศึกษา/ค่าธรรมเนียม</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#4ade80; --manual-color2:#22c55e; --manual-shadow:rgba(34, 197, 94, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="payment" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Link 10: ระบบนักเรียนออนไลน์ -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="https://academic.satit.up.ac.th/" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/10.png" alt="ระบบนักเรียนออนไลน์">
                        </div>
                        <div class="quick-link-content">
                            <h5>ระบบนักเรียนออนไลน์</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#facc15; --manual-color2:#f97316; --manual-shadow:rgba(250, 204, 21, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="student-online" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Link 11: ห้องปฏิบัติการทางวิทยาศาสตร์ -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="#" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/11.png" alt="ห้องปฏิบัติการทางวิทยาศาสตร์">
                        </div>
                        <div class="quick-link-content">
                            <h5>ห้องปฏิบัติการทางวิทยาศาสตร์</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#fb923c; --manual-color2:#f97316; --manual-shadow:rgba(249, 115, 22, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="science-lab" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Link 12: ระบบบริหารงานวิชาการ -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 quick-link-item">
                <div class="quick-link-wrapper">
                    <a href="#" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon-image">
                            <img src="icon/12.png" alt="ระบบบริหารงานวิชาการ">
                        </div>
                        <div class="quick-link-content">
                            <h5>ระบบบริหารงานวิชาการ</h5>
                        </div>
                        <div class="quick-link-hover">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    <div class="manual-text" style="--manual-color1:#f472b6; --manual-color2:#ec4899; --manual-shadow:rgba(244, 114, 182, 0.35);">
                        <a href="#" class="manual-text-link" data-manual="academic-system" title="คู่มือการใช้งาน" aria-label="คู่มือการใช้งาน">
                            <i class="fas fa-book-open"></i>
                        </a>
                    </div>
                </div>
            </div>
            

        </div>
    </div>
</section>

<!-- Manual Modal -->
<div class="modal fade" id="manualModal" tabindex="-1" aria-labelledby="manualModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manualModalLabel">คู่มือการใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                    <p class="text-muted">คู่มือการใช้งานสำหรับระบบนี้กำลังจัดทำ...</p>
                    <p class="text-muted">กรุณาติดต่อเจ้าหน้าที่หากต้องการความช่วยเหลือ</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Initialize Quick Links Animation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add stagger animation on page load
    const quickLinks = document.querySelectorAll('.quick-link-card');
    quickLinks.forEach((link, index) => {
        setTimeout(() => {
            link.classList.add('animate-in');
        }, index * 50);
    });
    
    // Add ripple effect on click
    quickLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Handle manual links (text links under cards)
    const manualLinks = document.querySelectorAll('.manual-text-link');
    const manualAliasMap = {
        'upmail': 'email-up',
        'ebudget': 'e-receipt',
        'science-lab': 'lab',
        'academic-system': 'academic',
        'student-online': 'academic'
    };
    manualLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const manualTypeOriginal = this.getAttribute('data-manual');
            const manualType = manualAliasMap[manualTypeOriginal] || manualTypeOriginal;
            
            // Load manual data
            fetch('/satitup/manuals/manual_links.json')
                .then(response => response.json())
                .then(data => {
                    const manualData = data[manualType];
                    const modalTitle = document.getElementById('manualModalLabel');
                    const modalBody = document.querySelector('#manualModal .modal-body');
                    
                    // ถ้ามีลิงก์คู่มือ ให้พาไปที่ลิงก์โดยตรง (PDF > Video > Online)
                    if (manualData) {
                        if (manualData.pdf_url) {
                            window.open(manualData.pdf_url, '_blank');
                            return;
                        } else if (manualData.video_url) {
                            window.open(manualData.video_url, '_blank');
                            return;
                        } else if (manualData.online_doc) {
                            window.open(manualData.online_doc, '_blank');
                            return;
                        }
                    }

                    // ถ้าไม่มีข้อมูลให้แสดงข้อความแจ้งเตือนในโมดัล
                    {
                        modalBody.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                                <p class="text-muted">คู่มือการใช้งานสำหรับระบบนี้กำลังจัดทำ...</p>
                                <p class="text-muted">กรุณาติดต่อเจ้าหน้าที่หากต้องการความช่วยเหลือ</p>
                            </div>`;
                    }
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('manualModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error loading manual data:', error);
                    const modalTitle = document.getElementById('manualModalLabel');
                    const modalBody = document.querySelector('#manualModal .modal-body');
                    
                    modalTitle.textContent = 'คู่มือการใช้งาน';
                    modalBody.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                            <p class="text-muted">คู่มือการใช้งานสำหรับระบบนี้กำลังจัดทำ...</p>
                            <p class="text-muted">กรุณาติดต่อเจ้าหน้าที่หากต้องการความช่วยเหลือ</p>
                        </div>`;
                    
                    const modal = new bootstrap.Modal(document.getElementById('manualModal'));
                    modal.show();
                });
        });
    });
});
</script>
