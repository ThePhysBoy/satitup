<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- School Information -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-logo">
                    <img src="../images/logo@2x.png" alt="โรงเรียนสาธิตมหาวิทยาลัยพะเยา" class="img-fluid mb-3" style="max-height: 80px;">
                    <h5>โรงเรียนสาธิตมหาวิทยาลัยพะเยา</h5>
                    <p class="mb-2">Demonstration School of University of Phayao</p>
                </div>
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt me-2"></i> 19 หมู่ 2 ตำบลแม่กา อำเภอเมือง จังหวัดพะเยา 56000</p>
                    <p><i class="fas fa-phone me-2"></i> 054-466-666 ต่อ 1374</p>
                    <p><i class="fas fa-envelope me-2"></i> desup@up.ac.th</p>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="footer-heading">ลิงก์ด่วน</h5>
                <div class="row">
                    <div class="col-6">
                        <ul class="footer-links">
                            <li><a href="../about-history.php">ประวัติโรงเรียน</a></li>
                            <li><a href="../about-vision.php">วิสัยทัศน์ / พันธกิจ</a></li>
                            <li><a href="../about-director.php">ผู้อำนวยการ</a></li>
                            <li><a href="../academic-curriculum.php">หลักสูตร</a></li>
                            <li><a href="../academic-calendar.php">ปฏิทินการศึกษา</a></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul class="footer-links">
                            <li><a href="../student-activities.php">กิจกรรมนักเรียน</a></li>
                            <li><a href="../student-council.php">สภานักเรียน</a></li>
                            <li><a href="../admission-info.php">การรับสมัคร</a></li>
                            <li><a href="../news.php">ข่าวสาร</a></li>
                            <li><a href="../contact.php">ติดต่อเรา</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Social Media and Newsletter -->
            <div class="col-lg-4">
                <h5 class="footer-heading">ติดตามเรา</h5>
                <div class="social-links mb-4">
                    <a href="https://www.facebook.com/desup.official" target="_blank" rel="noopener noreferrer" class="social-link">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.tiktok.com/@desup_satitphayao?_t=ZS-8znTHBxQaDS&_r=1" target="_blank" rel="noopener noreferrer" class="social-link">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
                
                <h5 class="footer-heading">รับข่าวสาร</h5>
                <p>ลงทะเบียนเพื่อรับข่าวสารและกิจกรรมล่าสุดจากโรงเรียน</p>
                <form class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="อีเมลของคุณ" required>
                        <button class="btn btn-primary" type="submit">สมัคร</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Copyright -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-md-0">© 2023 โรงเรียนสาธิตมหาวิทยาลัยพะเยา. สงวนลิขสิทธิ์.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <ul class="footer-bottom-links">
                        <li><a href="../privacy-policy.php">นโยบายความเป็นส่วนตัว</a></li>
                        <li><a href="../terms-of-service.php">ข้อกำหนดการใช้งาน</a></li>
                        <li><a href="../sitemap.php">แผนผังเว็บไซต์</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" class="btn btn-primary back-to-top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Chatbot Button -->
<button id="chatbot-button" class="btn btn-primary chatbot-button">
    <i class="fas fa-comment-dots"></i>
</button>

<!-- Chatbot Modal -->
<div class="modal fade" id="chatbotModal" tabindex="-1" aria-labelledby="chatbotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatbotModalLabel">
                    <i class="fas fa-robot me-2"></i> แชทบอทช่วยเหลือ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="chatbot-messages">
                    <div class="message bot-message">
                        สวัสดีครับ! มีอะไรให้ช่วยเหลือไหมครับ?
                    </div>
                </div>
                <div class="chatbot-input mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Owl Carousel JS -->
<script src="../js/owl.carousel.min.js"></script>

<!-- Custom School JS -->
<script src="../js/script-school.js"></script>

<!-- Hover Dropdown JS -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Enable dropdown on hover for desktop
        const dropdowns = document.querySelectorAll('.dropdown');
        
        if (window.matchMedia('(min-width: 992px)').matches) {
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('mouseenter', function() {
                    this.querySelector('.dropdown-toggle').click();
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    this.querySelector('.dropdown-toggle').click();
                });
            });
        }
        
        // Handle nested dropdowns
        const dropdownSubmenus = document.querySelectorAll('.dropdown-submenu');
        
        dropdownSubmenus.forEach(submenu => {
            submenu.addEventListener('mouseenter', function() {
                const dropdown = this.querySelector('.dropdown-menu');
                if (dropdown) {
                    dropdown.classList.add('show');
                }
            });
            
            submenu.addEventListener('mouseleave', function() {
                const dropdown = this.querySelector('.dropdown-menu');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            });
            
            // For mobile
            const toggles = submenu.querySelectorAll('.dropdown-toggle');
            toggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const dropdown = this.nextElementSibling;
                    if (dropdown) {
                        dropdown.classList.toggle('show');
                    }
                });
            });
        });
        
        // Back to top button
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Chatbot button
        const chatbotButton = document.getElementById('chatbot-button');
        const chatbotModal = new bootstrap.Modal(document.getElementById('chatbotModal'));
        
        chatbotButton.addEventListener('click', function() {
            chatbotModal.show();
        });
        
        // Add animation class to chatbot button
        setTimeout(() => {
            chatbotButton.classList.add('animate');
        }, 2000);
    });
</script>

<!-- Add custom CSS for footer styling -->
<style>
/* Footer Styles */
.footer {
    background-color: #333;
    color: #fff;
    padding-top: 50px;
    margin-top: 50px;
}

.footer-logo h5 {
    margin-bottom: 5px;
    font-weight: 600;
}

.footer-heading {
    color: #fff;
    font-weight: 600;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 10px;
}

.footer-heading:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 2px;
    background-color: var(--primary-color, #8B7AA8);
}

.footer-links {
    list-style: none;
    padding-left: 0;
    margin-bottom: 20px;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #fff;
    text-decoration: underline;
}

.social-links {
    display: flex;
    gap: 10px;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    transition: all 0.3s;
}

.social-link:hover {
    background-color: var(--primary-color, #8B7AA8);
    color: #fff;
    transform: translateY(-3px);
}

.newsletter-form .form-control {
    background-color: rgba(255, 255, 255, 0.1);
    border: none;
    color: #fff;
}

.newsletter-form .form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.footer-bottom {
    background-color: #222;
    padding: 15px 0;
    margin-top: 40px;
}

.footer-bottom-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.footer-bottom-links li {
    margin-left: 20px;
    position: relative;
}

.footer-bottom-links li:not(:last-child):after {
    content: '|';
    position: absolute;
    right: -12px;
    color: rgba(255, 255, 255, 0.3);
}

.footer-bottom-links a {
    color: #999;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s;
}

.footer-bottom-links a:hover {
    color: #fff;
}

.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary-color, #8B7AA8);
    color: white;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1000;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.chatbot-button {
    position: fixed;
    bottom: 30px;
    left: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary-color, #8B7AA8);
    color: white;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.chatbot-button.animate {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(139, 122, 168, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(139, 122, 168, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(139, 122, 168, 0);
    }
}

@media (max-width: 767px) {
    .footer-bottom-links {
        justify-content: center;
        margin-top: 10px;
    }
    
    .footer-bottom-links li {
        margin: 0 10px;
    }
    
    .col-md-6.text-md-end {
        text-align: center !important;
    }
    
    .col-md-6 p {
        text-align: center;
        margin-bottom: 10px;
    }
}
</style>