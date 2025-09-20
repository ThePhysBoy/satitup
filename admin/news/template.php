<?php
/**
 * Admin News Management Template
 * Modern Glass Morphism Design Template
 */

// This is a template file - include your PHP logic before including this template
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'ระบบจัดการข่าว'; ?> - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Summernote CSS (if needed) -->
    <?php if (isset($include_summernote) && $include_summernote): ?>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <?php endif; ?>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #B8A9D4 0%, #8B7AA8 100%);
            --secondary-gradient: linear-gradient(135deg, #F0A6CA 0%, #EFC3E6 100%);
            --success-gradient: linear-gradient(135deg, #82C09E 0%, #A8DADC 100%);
            --warning-gradient: linear-gradient(135deg, #FFD89B 0%, #F9C784 100%);
            --danger-gradient: linear-gradient(135deg, #F4A7A7 0%, #FFC3C3 100%);

            --primary-color: #8B7AA8;      /* ม่วงอ่อนหลัก */
            --primary-light: #B8A9D4;      /* ม่วงอ่อนมาก */
            --primary-dark: #6B5A88;       /* ม่วงเข้ม */
            --secondary-color: #9C89B8;    /* ม่วงรอง */
            --accent-color: #F0A6CA;       /* ชมพูอ่อน */
            --success-color: #82C09E;      /* เขียวอ่อน */
            --danger-color: #F4A7A7;       /* แดงอ่อน */
            --warning-color: #FFD89B;      /* เหลืองอ่อน */
            --info-color: #87CEEB;         /* ฟ้าอ่อน */

            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --dark-glass: rgba(0, 0, 0, 0.1);

            --shadow-soft: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --shadow-hover: 0 15px 35px 0 rgba(31, 38, 135, 0.4);
            --text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="grad1" cx="50%" cy="50%"><stop offset="0%" stop-color="rgba(255,255,255,0.1)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23grad1)"/><circle cx="800" cy="300" r="150" fill="url(%23grad1)"/><circle cx="400" cy="700" r="120" fill="url(%23grad1)"/><circle cx="900" cy="800" r="80" fill="url(%23grad1)"/></svg>') no-repeat center center;
            background-size: cover;
            pointer-events: none;
            z-index: -1;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--glass-border);
            color: #fff;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            box-shadow: var(--shadow-soft);
        }

        .sidebar .logo {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
        }

        .sidebar .logo h4 {
            color: #fff;
            font-weight: 600;
            text-shadow: var(--text-shadow);
            margin: 0;
        }

        .sidebar .nav-item {
            margin: 0.5rem 1rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            color: #fff;
            transform: translateX(5px);
            box-shadow: 0 4px 15px 0 rgba(31, 38, 135, 0.2);
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .page-header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            animation: slideInDown 0.6s ease-out;
        }

        .page-header h1 {
            color: #fff;
            font-weight: 700;
            font-size: 2rem;
            text-shadow: var(--text-shadow);
            margin: 0;
        }

        .btn-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: #fff;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: var(--shadow-soft);
        }

        .btn-glass:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: #fff;
            background: rgba(255, 255, 255, 0.35);
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary-gradient:hover::before {
            left: 100%;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            color: #fff;
        }

        .card-modern {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: slideInUp 0.6s ease-out;
        }

        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .card-header-modern {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.5rem;
        }

        .card-header-modern h6 {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            text-shadow: var(--text-shadow);
            display: flex;
            align-items: center;
        }

        .card-header-modern h6 i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-body-modern {
            padding: 2rem;
        }

        .form-control-modern,
        .form-select-modern {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: #495057;
        }

        .form-control-modern:focus,
        .form-select-modern:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: scale(1.02);
        }

        .form-label-modern {
            color: #fff;
            font-weight: 500;
            margin-bottom: 0.75rem;
            text-shadow: var(--text-shadow);
            display: flex;
            align-items: center;
        }

        .form-label-modern i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .alert-modern {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 15px;
            color: #fff;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .alert-success-modern {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(32, 201, 151, 0.3);
            border-radius: 15px;
            color: #fff;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .preview-image {
            max-height: 200px;
            max-width: 100%;
            border-radius: 12px;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .image-preview-container {
            display: inline-block;
            position: relative;
            margin: 0.75rem;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .image-preview-container:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .image-preview-container .remove-image {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--danger-gradient);
            color: #fff;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .remove-image:hover {
            transform: scale(1.1);
            background: linear-gradient(135deg, #ff6b95 0%, #ff8e9b 100%);
        }

        .note-editor {
            border-radius: 15px !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .note-toolbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }

        .note-editing-area {
            background: rgba(255, 255, 255, 0.95);
        }

        /* Table styling */
        .table-modern {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .table-modern thead th {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            text-shadow: var(--text-shadow);
            padding: 1rem;
        }

        .table-modern tbody td {
            background: rgba(255, 255, 255, 0.05);
            border: none;
            color: #fff;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover td {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.01);
        }

        /* Status badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .status-draft {
            background: var(--warning-gradient);
            color: #fff;
        }

        .status-published {
            background: var(--success-gradient);
            color: #fff;
        }

        .status-pending {
            background: var(--secondary-gradient);
            color: #fff;
        }

        /* Small text styling */
        .text-muted-glass {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        /* Required asterisk */
        .text-danger {
            color: #ff6b95 !important;
        }

        /* Pagination */
        .pagination-modern .page-link {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: #fff;
            border-radius: 50%;
            margin: 0 0.25rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .pagination-modern .page-link:hover {
            background: rgba(255, 255, 255, 0.35);
            color: #fff;
            transform: translateY(-2px);
        }

        .pagination-modern .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: var(--primary-color);
        }

        /* Animations */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .col-lg-8 .card-modern {
            animation-delay: 0.1s;
        }

        .col-lg-4 .card-modern:nth-child(1) {
            animation-delay: 0.2s;
        }

        .col-lg-4 .card-modern:nth-child(2) {
            animation-delay: 0.3s;
        }

        .col-lg-4 .card-modern:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            main {
                margin-left: 0;
                padding: 1rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .card-body-modern {
                padding: 1.5rem;
            }
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--glass-bg);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h4><i class="fas fa-newspaper me-2"></i>News CMS</h4>
            </div>
            <nav class="nav flex-column py-3">
                <div class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home"></i>
                        หน้าแรก
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-list"></i>
                        รายการข่าว
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="create.php">
                        <i class="fas fa-plus"></i>
                        เพิ่มข่าวใหม่
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fas fa-tags"></i>
                        จัดการหมวดหมู่
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="../users/">
                        <i class="fas fa-users"></i>
                        จัดการผู้ใช้
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        ออกจากระบบ
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <main>
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <h1><?php echo $page_header_icon ?? '<i class="fas fa-cog me-3"></i>'; ?><?php echo $page_title ?? 'จัดการระบบ'; ?></h1>
                    <?php if (isset($back_button) && $back_button): ?>
                    <a href="<?php echo $back_url ?? 'index.php'; ?>" class="btn-glass">
                        <i class="fas fa-arrow-left me-2"></i> <?php echo $back_text ?? 'กลับ'; ?>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Content Area -->
                <?php echo $content ?? ''; ?>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Summernote JS (if needed) -->
    <?php if (isset($include_summernote) && $include_summernote): ?>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <?php endif; ?>

    <!-- Template JavaScript -->
    <script>
        $(document).ready(function() {
            // Initialize Summernote if included
            <?php if (isset($include_summernote) && $include_summernote): ?>
            $('.summernote-editor').summernote({
                height: 400,
                minHeight: 300,
                maxHeight: 600,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'กรอกเนื้อหา...',
                callbacks: {
                    onChange: function(contents, $editable) {
                        // Auto-save functionality could be added here
                    }
                }
            });
            <?php endif; ?>

            // Auto-generate slug from title (if elements exist)
            if ($('#title').length && $('#slug').length) {
                $('#title').on('input', function() {
                    var title = $(this).val();
                    var slug = title
                        .toLowerCase()
                        .replace(/[^\wก-๙]+/g, '-')
                        .replace(/^-+|-+$/g, '');

                    if (!$('#slug').val()) {
                        $('#slug').val(slug);
                    }
                });
            }

            // Image preview functionality (if elements exist)
            if ($('#featured_image').length) {
                $('#featured_image').on('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#featured-image-preview').html(
                                '<img src="' + e.target.result + '" class="preview-image" alt="รูปภาพหลัก">'
                            );
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Gallery images preview (if elements exist)
            if ($('#images').length) {
                $('#images').on('change', function() {
                    const files = this.files;
                    let previewHtml = '';

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            previewHtml += `
                                <div class="image-preview-container">
                                    <img src="${e.target.result}" class="preview-image" alt="Gallery image ${i+1}">
                                    <div class="remove-image" onclick="removeImage(this)">
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                            `;
                            $('#image-previews').html(previewHtml);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Confirm delete functionality
            $('[data-confirm-delete]').on('click', function(e) {
                e.preventDefault();
                const message = $(this).data('confirm-message') || 'คุณต้องการลบรายการนี้ใช่หรือไม่?';
                if (confirm(message)) {
                    window.location.href = $(this).attr('href');
                }
            });
        });

        // Remove image preview
        function removeImage(element) {
            $(element).parent().remove();
        }

        // Loading state for forms
        function setLoadingState(form, loading = true) {
            const submitBtn = form.find('button[type="submit"], input[type="submit"]');
            const originalText = submitBtn.html();

            if (loading) {
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="loading-spinner me-2"></span>กำลังโหลด...');
            } else {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        }
    </script>
</body>
</html>
