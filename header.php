<?php
$currentDir = basename(dirname($_SERVER['SCRIPT_FILENAME']));
$basePath = in_array($currentDir, ['curriculum', 'admin', 'staff', 'rankings', 'procurements']) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>โรงเรียนสาธิตมหาวิทยาลัยพะเยา | Demonstration School of University of Phayao</title>
    
    <!-- Meta Tags สำหรับ SEO -->
    <meta name="description" content="โรงเรียนสาธิตมหาวิทยาลัยพะเยา มุ่งมั่นพัฒนาการศึกษาระดับมัธยมศึกษา สร้างเยาวชนคุณภาพสู่อนาคตที่ยั่งยืน">
    <meta name="keywords" content="โรงเรียนสาธิต, มหาวิทยาลัยพะเยา, การศึกษา, มัธยมศึกษา, พะเยา">
    <meta name="author" content="โรงเรียนสาธิตมหาวิทยาลัยพะเยา">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#8B7AA8">
    
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link href="<?php echo $basePath; ?>assets/fontawesome/css/all.min.css" rel="stylesheet">
    <script src="<?php echo $basePath; ?>assets/fontawesome/js/all.min.js" defer></script>
    
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- News Images Fix CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/news-images-fix.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    
    <!-- CSS Variables และ Styles -->
    <style>
        :root {
            --primary-color: #8B7AA8;
            --primary-light: #A698BC;
            --primary-dark: #6A5C82;
            --secondary-color: #8B7AA8;
            --secondary-light: #A698BC;
            --secondary-dark: #6A5C82;
            --accent-color: #F9A826;
            --text-dark: #333333;
            --text-medium: #666666;
            --text-light: #999999;
            --bg-light: #F8F9FA;
            --bg-medium: #E9ECEF;
            --bg-dark: #DEE2E6;
        }

        /* Fix Font Awesome Icons */
        .fa, .fas, .far, .fab, .fa-solid, .fa-regular, .fa-brands {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
            font-weight: 900;
        }
        
        .far, .fa-regular {
            font-weight: 400 !important;
        }
        
        .fab, .fa-brands {
            font-family: "Font Awesome 6 Brands" !important;
            font-weight: 400 !important;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Sarabun', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        /* Top Bar Styles */
        .top-bar {
            background: linear-gradient(135deg, #8B7AA8, #A698BC);
            color: white;
            padding: 10px 0;
            font-size: 14px;
        }
        
        .top-bar a {
            color: white;
            text-decoration: none;
            margin: 0 5px;
        }
        
        .top-bar .btn-outline-light {
            border-color: rgba(255,255,255,0.5);
            color: white;
        }
        
        .top-bar .btn-outline-light:hover {
            background-color: rgba(255,255,255,0.2);
            border-color: white;
        }
        
        .top-bar .btn-primary {
            background-color: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
        }
        
        .top-bar .btn-primary:hover {
            background-color: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
        }
        
        /* Section Separator */
        .section-separator {
            height: 2px;
            background: linear-gradient(90deg, transparent, #8B7AA8, transparent);
            margin: 20px 0;
        }
        
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: #8B7AA8;
            color: white;
            border: none;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .back-to-top:hover {
            background: #6A5C82;
            transform: translateY(-5px);
        }
        
        /* Fix jQuery Compatibility */
        /* เพิ่มเพื่อแก้ปัญหา plugin เก่าที่ใช้ andSelf() */
    </style>
    
    <script>
        // jQuery Compatibility Fix
        if (typeof jQuery !== 'undefined' && !jQuery.fn.andSelf) {
            jQuery.fn.andSelf = jQuery.fn.addBack;
            console.log('jQuery compatibility: Added andSelf() as alias for addBack()');
        }
        
        if (typeof jQuery !== 'undefined' && !jQuery.fn.size) {
            jQuery.fn.size = function() {
                return this.length;
            };
        }
    </script>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="top-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>โรงเรียนสาธิตมหาวิทยาลัยพะเยา จังหวัดพะเยา</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="top-links">
                        <a href="https://www.tiktok.com/@desup_satitphayao?_t=ZS-8znTHBxQaDS&_r=1" class="btn btn-sm btn-outline-light rounded-pill" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://www.facebook.com/desup.official" class="btn btn-sm btn-outline-light rounded-pill" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-pill" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-pill" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="tel:054466666" class="btn btn-sm btn-outline-light rounded-pill">
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
