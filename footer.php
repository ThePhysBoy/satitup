    <!-- Footer CSS -->
    <style>
    /* Footer Styles */
    .main-footer {
        background-color: #2c3e50;
        color: #ecf0f1;
        padding-top: 60px;
        margin-top: 80px;
    }
    
    .footer-top {
        padding-bottom: 40px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .footer-widget {
        margin-bottom: 30px;
    }
    
    .footer-logo {
        max-height: 80px;
        width: auto;
    }
    
    .widget-title {
        color: #fff;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }
    
    .widget-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background-color: var(--primary-color, #8B7AA8);
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 10px;
    }
    
    .footer-links a {
        color: #bdc3c7;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .footer-links a:hover {
        color: #fff;
        padding-left: 5px;
    }
    
    .social-links {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .social-links a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        transition: all 0.3s ease;
    }
    
    .social-links a:hover {
        background-color: var(--primary-color, #8B7AA8);
        transform: translateY(-3px);
    }
    
    .newsletter-form {
        margin-top: 20px;
    }
    
    .newsletter-form .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    
    .newsletter-form .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    
    .newsletter-form .btn-primary {
        background-color: var(--primary-color, #8B7AA8);
        border-color: var(--primary-color, #8B7AA8);
    }
    
    .visitor-counter {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }
    
    .counter-display {
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .footer-bottom {
        background-color: #1a252f;
        padding: 20px 0;
    }
    
    .copyright {
        color: #95a5a6;
        font-size: 0.9rem;
    }
    
    .footer-bottom-links a {
        color: #95a5a6;
        text-decoration: none;
        font-size: 0.9rem;
        margin: 0 10px;
    }
    
    .footer-bottom-links a:hover {
        color: #fff;
    }
    
    .separator {
        color: #7f8c8d;
        margin: 0 5px;
    }
    
    /* Back to Top Button */
    #backToTop {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background-color: var(--primary-color, #8B7AA8);
        color: #fff;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        z-index: 999;
    }
    
    #backToTop:hover {
        background-color: var(--primary-dark, #7A6897);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .footer-widget {
            text-align: center;
        }
        
        .widget-title:after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .social-links {
            justify-content: center;
        }
        
        .footer-bottom {
            text-align: center;
        }
        
        .footer-bottom-links {
            margin-top: 10px;
        }
    }
    </style>
    
    <!-- Footer Section -->
    <footer class="main-footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <!-- School Info -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-widget">
                            <img src="images/logo-school-white.png" alt="โรงเรียนสาธิต" class="footer-logo mb-3" height="80">
                            <h4 class="widget-title">โรงเรียนสาธิตมหาวิทยาลัยพะเยา</h4>
                            <p class="text-light">
                                Demonstration School of University of Phayao
                            </p>
                            <p class="mt-3">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                19 หมู่ 2 ตำบลแม่กา อำเภอเมืองพะเยา<br>
                                <span class="ms-4">จังหวัดพะเยา 56000</span>
                            </p>
                            <p>
                                <i class="fas fa-phone me-2"></i> 054-466666 
                            </p>
                            <p>
                                <i class="fas fa-envelope me-2"></i> satit@up.ac.th
                            </p>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="widget-title">ลิงก์ด่วน</h5>
                            <ul class="footer-links">
                                <li><a href="about-history.php"><i class="fas fa-angle-right me-2"></i>เกี่ยวกับเรา</a></li>
                                <li><a href="academic-curriculum.php"><i class="fas fa-angle-right me-2"></i>หลักสูตร</a></li>
                                <li><a href="admission-info.php"><i class="fas fa-angle-right me-2"></i>การรับสมัคร</a></li>
                                <li><a href="student-activities.php"><i class="fas fa-angle-right me-2"></i>กิจกรรม</a></li>
                                <li><a href="news.php"><i class="fas fa-angle-right me-2"></i>ข่าวสาร</a></li>
                                <li><a href="contact.php"><i class="fas fa-angle-right me-2"></i>ติดต่อเรา</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Services -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="widget-title">บริการออนไลน์</h5>
                            <ul class="footer-links">
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>ระบบจัดการเรียนรู้ (LMS)</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>ระบบห้องสมุดออนไลน์</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>ตรวจสอบผลการเรียน</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>ดาวน์โหลดเอกสาร</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>ปฏิทินกิจกรรม</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>อีเมลสำหรับนักเรียน</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Social & Newsletter -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="widget-title">ติดตามเรา</h5>
                            <div class="social-links mb-3">
                                <a href="https://www.facebook.com/desup.official" class="social-icon facebook" title="Facebook" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-icon instagram" title="Instagram" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-icon youtube" title="YouTube" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                <a href="#" class="social-icon line" title="LINE" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-line"></i>
                                </a>
                                <a href="https://www.tiktok.com/@desup_satitphayao?_t=ZS-8znTHBxQaDS&_r=1" class="social-icon tiktok" title="TikTok" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                            </div>
                            
                            <h5 class="widget-title mt-4">รับข่าวสาร</h5>
                            <form class="newsletter-form">
                                <div class="input-group">
                                    <input type="email" class="form-control" placeholder="อีเมลของคุณ" required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Visitor Counter -->
                            <div class="visitor-counter mt-4">
                                <h6 class="mb-2">จำนวนผู้เยี่ยมชม</h6>
                                <div class="counter-display">
                                    <i class="fas fa-users me-2"></i>
                                    <span id="visitorCount">1,234,567</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright mb-0">
                            &copy; 2024 โรงเรียนสาธิตมหาวิทยาลัยพะเยา. สงวนลิขสิทธิ์.
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="footer-bottom-links">
                            <a href="privacy-policy.php">นโยบายความเป็นส่วนตัว</a>
                            <span class="separator">|</span>
                            <a href="terms.php">ข้อกำหนดการใช้งาน</a>
                            <span class="separator">|</span>
                            <a href="sitemap.php">แผนผังเว็บไซต์</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" title="กลับขึ้นด้านบน">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Navbar Bootstrap Fix JS (แก้ไขปัญหา dropdown) -->
    <script src="js/navbar-bootstrap-fix.js"></script>
    
    <!-- Owl Carousel JS (ถ้ามี) -->
    <script src="js/owl.carousel.min.js" onerror="console.log('Owl Carousel not found')"></script>
    
    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Back to Top Button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#backToTop').css('display', 'flex').hide().fadeIn();
                } else {
                    $('#backToTop').fadeOut();
                }
            });
            
            $('#backToTop').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
            
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Dropdown submenu จะถูกจัดการใน navbar-bootstrap-fix.js แล้ว
        });
        
        // MathJax Configuration
        window.MathJax = {
            tex: {
                inlineMath: [['$','$'], ['\\(','\\)']],
                displayMath: [['$$','$$'], ['\\[','\\]']],
                processEscapes: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre']
            }
        };
    </script>
    
    <!-- MathJax -->
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    
    <?php
    // เพิ่มแชทบอท (ถ้ามีไฟล์ตั้งค่าแชทบอท)
    if (file_exists(dirname(__FILE__) . '/chatbot/chatbot-config.php')) {
        require_once dirname(__FILE__) . '/chatbot/chatbot-config.php';
        display_chatbot();
    }
    ?>
    
</body>
</html>