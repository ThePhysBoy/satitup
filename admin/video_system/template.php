<?php
/**
 * Admin Video Management Template
 */

// Set header to UTF-8
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'ระบบจัดการวิดีโอ'; ?> - ระบบจัดการเว็บไซต์โรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

        /* Navigation Pills */
        .nav-pills .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-pills .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-pills .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Card Styles */
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Buttons */
        .btn {
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        /* Table */
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
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
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>
                    <a href="../index.php" class="text-white text-decoration-none">
                        <i class="fas fa-video me-2"></i>ระบบจัดการวิดีโอ
                    </a>
                </h3>
            </div>
            
            <!-- Include sidebar menu -->
            <?php include 'sidebar_menu.php'; ?>
        </div>

        <!-- Page Content -->
        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                unset($_SESSION['message']); 
                unset($_SESSION['msg_type']);
                ?>
            <?php endif; ?>
            
            <?php echo isset($content) ? $content : ''; ?>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <?php if (isset($custom_scripts)): ?>
    <?php echo $custom_scripts; ?>
    <?php endif; ?>
</body>
</html>
