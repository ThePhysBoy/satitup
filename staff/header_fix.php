<?php
// ไฟล์นี้ใช้สำหรับแก้ไขการเรียกใช้ header.php ในหน้า staff/index.php และหน้าอื่นๆ ในโฟลเดอร์ staff
// โดยจะทำการแก้ไขเส้นทางของไฟล์ CSS และ JS ให้ถูกต้อง

// กำหนดตัวแปร $page_title ถ้ายังไม่มีการกำหนด
if (!isset($page_title)) {
    $page_title = "บุคลากร | โรงเรียนสาธิตมหาวิทยาลัยพะเยา";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page_title; ?> | โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Meta Tags สำหรับ SEO -->
    <meta name="description" content="บุคลากรโรงเรียนสาธิตมหาวิทยาลัยพะเยา ทั้งสายวิชาการและสายบริการ">
    <meta name="keywords" content="โรงเรียนสาธิต, มหาวิทยาลัยพะเยา, บุคลากร, ครู, อาจารย์, พะเยา">
    <meta name="author" content="โรงเรียนสาธิตมหาวิทยาลัยพะเยา">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#8B7AA8">
    
    <!-- ไอคอนเว็บไซต์ (Favicon) -->
    <link rel="icon" type="image/x-icon" href="../img/logo_up32x.ico">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Original CSS Files -->
    <link href="../css/owl.carousel.min.css" rel="stylesheet">
    <link href="../css/owl.theme.default.min.css" rel="stylesheet">
    
    <!-- Custom School Theme CSS -->
    <link href="../css/style-school.css" rel="stylesheet">
    
    <!-- Hover Dropdown CSS -->
    <link href="../css/hover-dropdown.css" rel="stylesheet">
    
    <!-- Contact Fix CSS -->
    <link href="../css/contact-fix.css" rel="stylesheet">
    
    <!-- Button Position Fix CSS -->
    <link href="../css/button-position-fix.css" rel="stylesheet">
    
    <!-- Chatbot Animation CSS -->
    <link href="../css/chatbot-animation.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- MathJax -->
    <script type="text/javascript" id="MathJax-script" async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
    </script>
    <script>
        MathJax = {
            tex: {
                inlineMath: [['$','$'], ['\\(','\\)']]
            },
            chtml: {
                matchFontHeight: false
            },
            options: {
                ignoreHtmlClass: 'noforprocess',
                processHtmlClass: 'process-mathjax'
            }
        };
    </script>
</head>
<body>
    <!-- แถบด้านบน (Top Bar) -->
    <div class="top-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="top-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>โรงเรียนสาธิตมหาวิทยาลัยพะเยา จ.พะเยา</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="top-links">
                        <a href="https://www.tiktok.com/@desup_satitphayao?_t=ZS-8znTHBxQaDS&_r=1" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://www.facebook.com/desup.official" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-pill me-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="tel:054466666" class="btn btn-sm btn-outline-light rounded-pill me-2">
                            <i class="fas fa-phone"></i> 054-466666
                        </a>
                        <a href="https://academic.satit.up.ac.th/index#/teacher/processgradeinfo" class="btn btn-sm btn-primary rounded-pill" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-user-graduate"></i> ระบบนักเรียน
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>