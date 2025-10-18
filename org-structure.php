<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงสร้างองค์กร - โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .page-header {
            background: white;
            padding: 30px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .page-title {
            font-family: 'Kanit', sans-serif;
            text-align: center;
            color: #1e3a8a;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .page-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 1.1rem;
        }

        .pdf-container {
            max-width: 1400px;
            margin: 0 auto 30px;
            padding: 0 20px;
        }

        .pdf-wrapper {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            position: relative;
        }

        .pdf-toolbar {
            background: #f8f9fa;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e9ecef;
        }

        .pdf-toolbar h5 {
            margin: 0;
            color: #1e3a8a;
            font-weight: 600;
        }

        .btn-download {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .pdf-embed {
            width: 100%;
            height: 85vh;
            border: none;
            display: block;
        }

        .pdf-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .pdf-info p {
            margin: 0;
            color: #856404;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.8rem;
            }

            .page-subtitle {
                font-size: 0.95rem;
            }

            .pdf-embed {
                height: 70vh;
            }

            .pdf-toolbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }

        /* Loading animation */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fade-out {
            animation: fadeOut 0.3s ease;
            animation-fill-mode: forwards;
        }

        @keyframes fadeOut {
            to { opacity: 0; visibility: hidden; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-header">
        <h1 class="page-title">โครงสร้างองค์กร</h1>
        <p class="page-subtitle">โรงเรียนสาธิตมหาวิทยาลัยพะเยา</p>
    </div>

    <div class="pdf-container">
        <div class="pdf-info">
            <p>
                <i class="fas fa-info-circle me-2"></i>
                <strong>หมายเหตุ:</strong> หากไม่สามารถแสดง PDF ได้ กรุณาคลิกปุ่ม "ดาวน์โหลด PDF" เพื่อดาวน์โหลดและเปิดด้วยโปรแกรมอ่าน PDF
            </p>
        </div>

        <div class="pdf-wrapper">
            <div class="pdf-toolbar">
                <h5>
                    <i class="fas fa-sitemap me-2"></i>
                    แผนผังโครงสร้างองค์กร
                </h5>
                <a href="structure-org/structure-org.pdf" download class="btn-download">
                    <i class="fas fa-download"></i>
                    ดาวน์โหลด PDF
                </a>
            </div>

            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
            </div>

            <embed 
                src="structure-org/structure-org.pdf#toolbar=1&navpanes=0&scrollbar=1" 
                type="application/pdf" 
                class="pdf-embed"
                id="pdfEmbed"
            >
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Hide loading overlay when PDF is loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.classList.add('fade-out');
                    setTimeout(function() {
                        overlay.style.display = 'none';
                    }, 300);
                }
            }, 1000);
        });

        // Check if PDF embed is supported
        const pdfEmbed = document.getElementById('pdfEmbed');
        pdfEmbed.addEventListener('error', function() {
            // If embed fails, redirect to direct PDF link
            console.log('PDF embed failed, opening in new window');
        });
    </script>
</body>
</html>