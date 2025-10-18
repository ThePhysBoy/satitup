<?php
/**
 * หน้าจัดการแกลเลอรี่รูปภาพของพันธมิตร
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Require user to be logged in
requireLogin();

// Check permissions
if (!isPrOfficer() && !isAdmin()) {
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงส่วนนี้";
    header('Location: ../index.php');
    exit;
}

// Check ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$partner_id = intval($_GET['id']);

// Get partner data
$query = "SELECT * FROM partners WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $partner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "ไม่พบข้อมูลพันธมิตร";
    header('Location: index.php');
    exit;
}

$partner = $result->fetch_assoc();

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $errors = [];
    
    if (isset($_FILES['gallery_images'])) {
        $upload_dir = '../../uploads/partners/gallery/';
        $uploaded_count = 0;
        
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_ext, $allowed_ext)) {
                    $new_filename = 'gallery_' . $partner_id . '_' . time() . '_' . uniqid() . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $image_path = 'uploads/partners/gallery/' . $new_filename;
                        $caption = isset($_POST['captions'][$key]) ? trim($_POST['captions'][$key]) : '';
                        
                        // Insert into database
                        $insert_query = "INSERT INTO partner_images (partner_id, image_path, caption, order_number) VALUES (?, ?, ?, 0)";
                        $stmt_insert = $conn->prepare($insert_query);
                        $stmt_insert->bind_param('iss', $partner_id, $image_path, $caption);
                        
                        if ($stmt_insert->execute()) {
                            $uploaded_count++;
                        }
                    }
                }
            }
        }
        
        if ($uploaded_count > 0) {
            $_SESSION['success_message'] = "อัพโหลดรูปภาพสำเร็จ $uploaded_count รูป";
        }
    }
    
    header('Location: manage_gallery.php?id=' . $partner_id);
    exit;
}

// Handle delete image
if (isset($_POST['action']) && $_POST['action'] === 'delete_image' && isset($_POST['image_id'])) {
    $image_id = intval($_POST['image_id']);

    // Verify CSRF token for security
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "โทเคนความปลอดภัยไม่ถูกต้อง";
        header('Location: manage_gallery.php?id=' . $partner_id);
        exit;
    }

    try {
        // Get image path and verify ownership
        $img_query = "SELECT image_path, caption FROM partner_images WHERE id = ? AND partner_id = ?";
        $stmt = $conn->prepare($img_query);
        $stmt->bind_param('ii', $image_id, $partner_id);
        $stmt->execute();
        $img_result = $stmt->get_result();

        if ($row = $img_result->fetch_assoc()) {
            $image_path = $row['image_path'];
            $caption = $row['caption'];

            // Delete file from server
            $full_path = '../../' . $image_path;
            if (file_exists($full_path)) {
                if (unlink($full_path)) {
                    $file_deleted = true;
                } else {
                    $file_deleted = false;
                    error_log("Failed to delete file: $full_path");
                }
            } else {
                $file_deleted = true; // File doesn't exist, consider it deleted
            }

            // Delete from database
            $delete_query = "DELETE FROM partner_images WHERE id = ?";
            $stmt_del = $conn->prepare($delete_query);
            $stmt_del->bind_param('i', $image_id);

            if ($stmt_del->execute()) {
                $deleted_rows = $stmt_del->affected_rows;

                if ($deleted_rows > 0) {
                    $_SESSION['success_message'] = "ลบรูปภาพสำเร็จ" .
                        (!empty($caption) ? ": " . htmlspecialchars($caption) : "");
                } else {
                    $_SESSION['error_message'] = "ไม่สามารถลบข้อมูลจากฐานข้อมูลได้";
                }
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบจากฐานข้อมูล: " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "ไม่พบรูปภาพที่ต้องการลบ หรือไม่มีสิทธิ์เข้าถึง";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        error_log("Error deleting image: " . $e->getMessage());
    }

    header('Location: manage_gallery.php?id=' . $partner_id);
    exit;
}

// Generate CSRF token for security
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get gallery images
$gallery_query = "SELECT * FROM partner_images WHERE partner_id = ? ORDER BY order_number ASC, created_at ASC";
$stmt_gallery = $conn->prepare($gallery_query);
$stmt_gallery->bind_param('i', $partner_id);
$stmt_gallery->execute();
$gallery_result = $stmt_gallery->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการแกลเลอรี่ - <?php echo htmlspecialchars($partner['name']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .upload-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .gallery-item {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .gallery-image {
            height: 200px;
            overflow: hidden;
            background: #f8f9fa;
        }
        
        .gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .gallery-info {
            padding: 1rem;
        }

        /* Modal styles */
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .modal-title {
            color: #dc3545;
            font-weight: 600;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Image preview in modal */
        .modal-body img {
            max-height: 200px;
            width: auto;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-images text-info me-2"></i>
                        จัดการแกลเลอรี่รูปภาพ
                    </h1>
                    <p class="text-muted mb-0">พันธมิตร: <?php echo htmlspecialchars($partner['name']); ?></p>
                </div>
                <div>
                    <a href="edit.php?id=<?php echo $partner_id; ?>" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>แก้ไขข้อมูล
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>กลับ
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>สำเร็จ!</strong> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>เกิดข้อผิดพลาด!</strong> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Upload Section -->
        <div class="upload-section">
            <h4 class="mb-3">
                <i class="fas fa-cloud-upload-alt me-2"></i>
                อัพโหลดรูปภาพใหม่
            </h4>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                
                <div class="mb-3">
                    <label for="gallery_images" class="form-label">เลือกรูปภาพ (สามารถเลือกได้หลายรูป)</label>
                    <input type="file" class="form-control" id="gallery_images" name="gallery_images[]" 
                           accept="image/*" multiple required>
                    <small class="text-muted">รองรับไฟล์ JPG, PNG, GIF</small>
                </div>
                
                <div id="preview-container" class="row g-3 mb-3"></div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>อัพโหลด
                </button>
            </form>
        </div>

        <!-- Gallery Section -->
        <div class="page-header">
            <h4 class="mb-3">
                <i class="fas fa-photo-video me-2"></i>
                แกลเลอรี่รูปภาพทั้งหมด (<?php echo $gallery_result->num_rows; ?> รูป)
            </h4>
            
            <?php if ($gallery_result->num_rows > 0): ?>
            <div class="gallery-grid">
                <?php while ($image = $gallery_result->fetch_assoc()): ?>
                <div class="gallery-item">
                    <div class="gallery-image">
                        <?php if (file_exists('../../' . $image['image_path'])): ?>
                            <img src="../../<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['caption']); ?>">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="gallery-info">
                        <?php if (!empty($image['caption'])): ?>
                        <p class="mb-2 small text-muted">
                            <i class="fas fa-comment me-1"></i>
                            <?php echo htmlspecialchars($image['caption']); ?>
                        </p>
                        <?php endif; ?>
                        <div class="mb-2 small text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            อัพโหลด: <?php echo date('d/m/Y H:i', strtotime($image['created_at'])); ?>
                        </div>
                        <div class="d-flex gap-2">
                            <button onclick="showDeleteModal(<?php echo $image['id']; ?>, '<?php echo htmlspecialchars($image['caption'] ?? 'ไม่มีคำอธิบาย'); ?>', '<?php echo htmlspecialchars($image['image_path']); ?>')"
                                    class="btn btn-sm btn-danger flex-fill"
                                    title="ลบรูปภาพนี้">
                                <i class="fas fa-trash me-1"></i>ลบรูปภาพ
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-images fa-5x text-muted mb-3"></i>
                <h5 class="text-muted">ยังไม่มีรูปภาพในแกลเลอรี่</h5>
                <p class="text-muted mb-4">อัพโหลดรูปภาพแรกของคุณโดยใช้แบบฟอร์มด้านบน</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>เคล็ดลับ:</strong> คุณสามารถอัพโหลดรูปภาพได้หลายรูปพร้อมกัน และเพิ่มคำอธิบายให้แต่ละรูปได้
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Preview images before upload
    document.getElementById('gallery_images').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';

        const files = e.target.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <input type="text" class="form-control form-control-sm"
                                   name="captions[]" placeholder="คำอธิบายรูป (ถ้ามี)">
                        </div>
                    </div>
                `;
                previewContainer.appendChild(col);
            };

            reader.readAsDataURL(file);
        }
    });

    // Show delete confirmation modal
    function showDeleteModal(imageId, caption, imagePath) {
        document.getElementById('deleteImageId').value = imageId;
        document.getElementById('deleteModalLabel').innerHTML =
            'ยืนยันการลบรูปภาพ <i class="fas fa-exclamation-triangle text-warning"></i>';

        const modalBody = document.getElementById('deleteModalBody');
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="../../${imagePath}" class="img-fluid rounded border" alt="รูปภาพที่จะลบ">
                </div>
                <div class="col-md-6">
                    <h6>รายละเอียดรูปภาพ:</h6>
                    <p><strong>คำอธิบาย:</strong> ${caption}</p>
                    <p><strong>ไฟล์:</strong> ${imagePath.split('/').pop()}</p>
                    <div class="alert alert-danger">
                        <i class="fas fa-warning me-2"></i>
                        <strong>คำเตือน:</strong> การลบจะไม่สามารถกู้คืนได้
                    </div>
                </div>
            </div>
        `;

        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Confirm delete action
    function confirmDelete() {
        const imageId = document.getElementById('deleteImageId').value;

        // Create form and submit via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'manage_gallery.php?id=<?php echo $partner_id; ?>';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete_image';

        const imageIdInput = document.createElement('input');
        imageIdInput.type = 'hidden';
        imageIdInput.name = 'image_id';
        imageIdInput.value = imageId;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?php echo $_SESSION['csrf_token']; ?>';

        form.appendChild(actionInput);
        form.appendChild(imageIdInput);
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    }
    </script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">ยืนยันการลบรูปภาพ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    <!-- Modal content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>ยกเลิก
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>ลบรูปภาพ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form inputs for delete action -->
    <input type="hidden" id="deleteImageId" value="">
</body>
</html>

