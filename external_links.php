<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน่วยงานภายนอก</title>
    
    <!-- Bootstrap CSS (เพื่อให้ Grid ทำงาน) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1e3a8a; /* Dark blue */
            --secondary-color: #3b82f6; /* Medium blue */
            --accent-color: #06b6d4; /* Cyan */
            --bg-light: #f8f9fa; /* Light grey background */
            --bg-dark: #e9ecef; /* Slightly darker grey for sections */
            --card-bg: #ffffff; /* White card background */
            --shadow-light: 0 4px 15px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
            --text-dark: #212529; /* Dark text */
            --text-muted: #6c757d; /* Muted text */
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Sarabun', sans-serif; /* Use Sarabun as primary font */
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Kanit', sans-serif; /* Kanit for headings */
            color: var(--primary-color);
        }

        /* External Links Section */
        .external-links-section {
            background-color: var(--bg-dark);
            padding: 80px 0; /* Increased padding for more breathing room */
            border-top: 1px solid var(--border-color);
        }

        .external-links-section .section-title {
            font-size: 2.8rem; /* Larger title */
            font-weight: 600; /* Bolder title */
            color: var(--primary-color);
            margin-bottom: 3rem; /* More space below title */
            position: relative;
            display: inline-block;
        }

        .external-links-section .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 5px;
            background: linear-gradient(90deg, 
                #FF6B35, #F7931E, #FDC830, 
                #8B7AA8, #A698BC,
                #FF6B35, #F7931E
            );
            margin: 10px auto 0;
            border-radius: 3px;
            animation: gradientMove 3s ease infinite, glowLine 2s ease-in-out infinite;
            background-size: 200% 100%;
            box-shadow: 0 0 15px rgba(255, 165, 0, 0.5);
        }

        @keyframes gradientMove {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes glowLine {
            0%, 100% {
                box-shadow: 0 0 15px rgba(255, 165, 0, 0.5);
            }
            50% {
                box-shadow: 0 0 25px rgba(255, 165, 0, 0.8),
                            0 0 35px rgba(255, 215, 0, 0.5);
            }
        }

        .external-link-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: var(--shadow-light);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px solid transparent;
            overflow: hidden;
            position: relative;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }

        /* Animated rainbow gradient border effect */
        .external-link-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            padding: 3px;
            background: linear-gradient(
                135deg, 
                #FF6B35, #F7931E, #FDC830, 
                #8B7AA8, #A698BC, 
                #FF6B35, #F7931E
            );
            background-size: 300% 300%;
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .external-link-card:hover::before {
            opacity: 1;
            animation: borderRotate 4s linear infinite;
        }

        @keyframes borderRotate {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Multi-color shine effect */
        .external-link-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 20%,
                rgba(255, 165, 0, 0.6) 35%,
                rgba(255, 215, 0, 0.8) 50%,
                rgba(255, 107, 53, 0.6) 65%,
                transparent 80%
            );
            transform: rotate(45deg);
            transition: all 0.6s ease;
            opacity: 0;
        }

        .external-link-card:hover::after {
            opacity: 1;
            animation: shine 1.2s ease-in-out infinite;
        }

        @keyframes shine {
            0% {
                left: -50%;
                top: -50%;
            }
            100% {
                left: 150%;
                top: 150%;
            }
        }

        .external-link-card:hover {
            transform: translateY(-12px) scale(1.03) rotateX(5deg);
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.4), 
                        0 0 40px rgba(255, 165, 0, 0.3),
                        0 0 60px rgba(255, 215, 0, 0.2),
                        inset 0 0 30px rgba(255, 215, 0, 0.1);
            background: linear-gradient(145deg, #ffffff, #fff9f0);
            animation: glowPulse 2s ease-in-out infinite;
        }

        @keyframes glowPulse {
            0%, 100% {
                box-shadow: 0 20px 40px rgba(255, 107, 53, 0.4), 
                            0 0 40px rgba(255, 165, 0, 0.3),
                            0 0 60px rgba(255, 215, 0, 0.2);
            }
            50% {
                box-shadow: 0 25px 50px rgba(255, 107, 53, 0.6), 
                            0 0 60px rgba(255, 165, 0, 0.5),
                            0 0 80px rgba(255, 215, 0, 0.4),
                            0 0 100px rgba(255, 107, 53, 0.2);
            }
        }

        .external-link-card img {
            max-width: 100%;
            max-height: 120px;
            margin-bottom: 20px;
            object-fit: contain;
            filter: grayscale(70%) brightness(1.1);
            opacity: 0.85;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }
        
        .external-link-card:hover img {
            filter: grayscale(0%) brightness(1.2) contrast(1.1) 
                    drop-shadow(0 8px 20px rgba(255, 107, 53, 0.6))
                    drop-shadow(0 0 15px rgba(255, 165, 0, 0.4));
            opacity: 1;
            transform: scale(1.2) translateY(-5px) rotateY(5deg);
            animation: float 3s ease-in-out infinite, sparkle 1.5s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: scale(1.2) translateY(-5px) rotateY(5deg);
            }
            50% {
                transform: scale(1.2) translateY(-10px) rotateY(-5deg);
            }
        }

        @keyframes sparkle {
            0%, 100% {
                filter: grayscale(0%) brightness(1.2) contrast(1.1) 
                        drop-shadow(0 8px 20px rgba(255, 107, 53, 0.6))
                        drop-shadow(0 0 15px rgba(255, 165, 0, 0.4));
            }
            50% {
                filter: grayscale(0%) brightness(1.3) contrast(1.2) 
                        drop-shadow(0 10px 30px rgba(255, 107, 53, 0.8))
                        drop-shadow(0 0 25px rgba(255, 215, 0, 0.6));
            }
        }

        .external-link-card p {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-dark);
            line-height: 1.5;
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
        }

        .external-link-card:hover p {
            background: linear-gradient(90deg, #FF6B35, #F7931E, #FDC830);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transform: translateY(-3px) scale(1.05);
            text-shadow: 0 0 20px rgba(255, 165, 0, 0.5);
            filter: drop-shadow(0 2px 8px rgba(255, 107, 53, 0.4));
            animation: textGlow 2s ease-in-out infinite;
        }

        @keyframes textGlow {
            0%, 100% {
                filter: drop-shadow(0 2px 8px rgba(255, 107, 53, 0.4));
            }
            50% {
                filter: drop-shadow(0 2px 15px rgba(255, 165, 0, 0.8));
            }
        }

        .external-link-item {
            text-decoration: none;
            display: block;
            height: 100%;
            perspective: 1000px;
        }

        /* Active/Click effect - Orange explosion */
        .external-link-card:active {
            transform: translateY(-8px) scale(0.98);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.7), 
                        0 0 50px rgba(255, 165, 0, 0.6),
                        0 0 80px rgba(255, 215, 0, 0.5),
                        inset 0 0 40px rgba(255, 215, 0, 0.3);
            background: linear-gradient(145deg, #fff9f0, #ffedd5);
            animation: clickBurst 0.4s ease;
        }

        @keyframes clickBurst {
            0% {
                box-shadow: 0 15px 35px rgba(255, 107, 53, 0.7), 
                            0 0 50px rgba(255, 165, 0, 0.6);
            }
            50% {
                box-shadow: 0 20px 50px rgba(255, 107, 53, 1), 
                            0 0 80px rgba(255, 165, 0, 0.9),
                            0 0 120px rgba(255, 215, 0, 0.8),
                            0 0 160px rgba(255, 107, 53, 0.5);
            }
            100% {
                box-shadow: 0 15px 35px rgba(255, 107, 53, 0.7), 
                            0 0 50px rgba(255, 165, 0, 0.6);
            }
        }

        .external-link-card:active::before {
            animation: borderBurst 0.4s ease;
        }

        @keyframes borderBurst {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 1;
                padding: 5px;
                filter: brightness(1.5);
            }
        }

        /* Fade in animation on page load */
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

        /* Initial state for fade in animation */
        .col-lg-2 .external-link-card {
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
        }

        /* Stagger animation delay for each card */
        .col-lg-2:nth-child(1) .external-link-card { animation-delay: 0.1s; }
        .col-lg-2:nth-child(2) .external-link-card { animation-delay: 0.2s; }
        .col-lg-2:nth-child(3) .external-link-card { animation-delay: 0.3s; }
        .col-lg-2:nth-child(4) .external-link-card { animation-delay: 0.4s; }
        .col-lg-2:nth-child(5) .external-link-card { animation-delay: 0.5s; }
        .col-lg-2:nth-child(6) .external-link-card { animation-delay: 0.6s; }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .external-links-section {
                padding: 60px 0;
            }
            .external-links-section .section-title {
                font-size: 2.2rem;
                margin-bottom: 2.5rem;
            }
            .external-link-card {
                padding: 25px 15px;
            }
            .external-link-card img {
                max-height: 100px;
                margin-bottom: 15px;
            }
            .external-link-card p {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 767px) {
            .external-links-section {
                padding: 40px 0;
            }
            .external-links-section .section-title {
                font-size: 1.8rem;
                margin-bottom: 2rem;
            }
            .external-link-card {
                padding: 20px 10px;
                border-radius: 15px;
            }
            .external-link-card img {
                max-height: 85px;
                margin-bottom: 10px;
            }
            .external-link-card p {
                font-size: 0.85rem;
            }
            .row.g-4 > div {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
        }

        @media (max-width: 575px) {
            .external-links-section .section-title {
                font-size: 1.6rem;
            }
            .external-link-card {
                padding: 15px 10px;
            }
            .external-link-card img {
                max-height: 70px;
            }
            .external-link-card p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

<!-- External Links Section -->
<section class="external-links-section">
    <div class="container">
        <div class="section-header text-center">
            <h4 class="section-title">เครือข่ายความร่วมมือ</h4>
        </div>
        
        <div class="row g-4 justify-content-center pt-4"> <!-- เพิ่ม g-4 เพื่อเพิ่มระยะห่าง และ pt-4 เพื่อเพิ่มช่องว่างจากหัวข้อ -->
            
            <!-- สสวท. -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="https://www.ipst.ac.th/" target="_blank" rel="noopener noreferrer" class="external-link-item">
                    <div class="external-link-card">
                        <img src="images/logos/ipst-logo.png" alt="สสวท." onerror="this.src='images/comingsoon.png'" loading="lazy">
                        <p>สสวท.</p>
                    </div>
                </a>
            </div>
            
            <!-- สอวช. -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="https://www.nxpo.or.th/" target="_blank" rel="noopener noreferrer" class="external-link-item">
                    <div class="external-link-card">
                        <img src="images/logos/nxpo-logo.png" alt="สอวช." onerror="this.src='images/comingsoon.png'" loading="lazy">
                        <p>สอวช.</p>
                    </div>
                </a>
            </div>
            
            <!-- กระทรวง อว. -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="https://www.mhesi.go.th/" target="_blank" rel="noopener noreferrer" class="external-link-item">
                    <div class="external-link-card">
                        <img src="images/logos/mhesi-logo.jpg" alt="กระทรวง อว." onerror="this.src='images/comingsoon.png'" loading="lazy">
                        <p>กระทรวง อว.</p>
                    </div>
                </a>
            </div>
            
            <!-- กระทรวงศึกษาธิการ -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="https://www.moe.go.th/" target="_blank" rel="noopener noreferrer" class="external-link-item">
                    <div class="external-link-card">
                        <img src="images/logos/moe-logo.jpg" alt="กระทรวงศึกษาธิการ" onerror="this.src='images/comingsoon.png'" loading="lazy">
                        <p>กระทรวงศึกษาธิการ</p>
                    </div>
                </a>
            </div>
            
            <!-- วช. -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="https://nrct.go.th/" target="_blank" rel="noopener noreferrer" class="external-link-item">
                    <div class="external-link-card">
                        <img src="images/logos/nrct-logo.jpg" alt="วช." onerror="this.src='images/comingsoon.png'" loading="lazy">
                        <p>วช.</p>
                    </div>
                </a>
            </div>
            
            <!-- NARIT -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="https://www.narit.or.th/" target="_blank" rel="noopener noreferrer" class="external-link-item">
                    <div class="external-link-card">
                        <img src="images/logos/narit-logo.jpg" alt="NARIT" onerror="this.src='images/comingsoon.png'" loading="lazy">
                        <p>NARIT</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

</body>
</html>
