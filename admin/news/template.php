<?php
/**
 * Admin News Management Template
 * Modern Glass Morphism Design Template
 */

// Set header to UTF-8
header('Content-Type: text/html; charset=UTF-8');

// This is a template file - include your PHP logic before including this template
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'ระบบจัดการข่าว'; ?> - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Summernote CSS (if needed) -->
    <?php if (isset($include_summernote) && $include_summernote): ?>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .note-toolbar {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
    <?php endif; ?>

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            width: 280px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 15px 0;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar-header h3 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .content {
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 30px;
            transition: all 0.3s;
        }

        .card-modern {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            margin-bottom: 25px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        }

        .card-header-modern {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }

        .card-body-modern {
            padding: 20px;
        }

        .card-footer-modern {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .btn-modern {
            border-radius: 10px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .form-control-modern {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 10px 15px;
        }

        .form-select-modern {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 10px 15px;
        }

        .table-modern {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }

        .table-modern thead th {
            background: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            font-weight: 600;
        }

        .pagination-modern .page-link {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #333;
            margin: 0 3px;
            border-radius: 8px;
        }

        .pagination-modern .page-link:hover {
            background: rgba(255, 255, 255, 0.9);
        }

        .pagination-modern .page-item.active .page-link {
            background: #764ba2;
            border-color: #764ba2;
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
            margin: 10px;
        }

        .image-preview-container img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .image-preview-container .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 25px;
            cursor: pointer;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>

    <?php if (isset($custom_styles)): ?>
    <style>
        <?php echo $custom_styles; ?>
    </style>
    <?php endif; ?>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="admin-sidebar" style="display: none;">
            <div class="sidebar-header">
                <h3>
                    <a href="../index.php" class="text-white text-decoration-none">
                        <i class="fas fa-school me-2"></i>ระบบจัดการ
                    </a>
                </h3>
            </div>
            
            <!-- Include sidebar menu -->
            <?php include 'sidebar_menu.php'; ?>
        </div>

        <!-- Page Content -->
        <div class="content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h2 class="text-white">
                    <?php echo isset($page_header_icon) ? $page_header_icon : ''; ?>
                    <?php echo isset($page_title) ? $page_title : 'ระบบจัดการข่าวประชาสัมพันธ์'; ?>
                </h2>
                
                <?php if (isset($back_button) && $back_button): ?>
                    <a href="<?php echo isset($back_button_url) ? $back_button_url : 'index.php'; ?>" class="btn btn-light btn-modern">
                        <i class="fas fa-arrow-left me-2"></i><?php echo isset($back_button_text) ? $back_button_text : 'กลับ'; ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php echo isset($content) ? $content : ''; ?>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Summernote JS (if needed) -->
    <?php if (isset($include_summernote) && $include_summernote): ?>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <?php endif; ?>
    
    <?php if (isset($custom_scripts)): ?>
    <?php echo $custom_scripts; ?>
    <?php endif; ?>

    <script>
        // If not public view, show sidebar
        (function(){
            try{
                const params = new URLSearchParams(window.location.search);
                const isPublic = params.get('public') === '1';
                const sb = document.getElementById('admin-sidebar');
                if (sb && !isPublic) sb.style.display = '';
            }catch(e){}
        })();
    </script>
</body>
</html>