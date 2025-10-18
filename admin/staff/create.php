<?php
// Include necessary files
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once './staff_functions.php';

// Require user to be logged in and have permission
requireLogin();
if (!canManageStaff()) {
    header("Location: ../index.php");
    exit;
}

// Get departments for dropdown
$departments = getDepartments(null, $conn);

// Debug: Show current departments
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<!-- Debug: Found " . count($departments) . " departments -->";
    foreach ($departments as $dept) {
        echo "<!-- ID: {$dept['id']}, Name: {$dept['name']}, Type: {$dept['type']} -->";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $title = trim($_POST['title'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $expertise = trim($_POST['expertise'] ?? '');
    $work_status = $_POST['work_status'] ?? 'working';
    $bio = trim($_POST['bio'] ?? '');
    $google_scholar_url = trim($_POST['google_scholar_url'] ?? '');
    $is_head = isset($_POST['is_head']) ? 1 : 0;
    $order_number = (int)($_POST['order_number'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    // Validate positions
    $positions = [];
    $position_names = $_POST['position_name'] ?? [];
    $is_primary_positions = $_POST['is_primary_position'] ?? [];
    
    foreach ($position_names as $key => $position_name) {
        if (!empty(trim($position_name))) {
            $positions[] = [
                'name' => trim($position_name),
                'is_primary' => isset($is_primary_positions[$key]) ? 1 : 0
            ];
        }
    }
    
    // Validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "กรุณาระบุคำนำหน้าชื่อ";
    }
    
    if (empty($first_name)) {
        $errors[] = "กรุณาระบุชื่อ";
    }
    
    if (empty($last_name)) {
        $errors[] = "กรุณาระบุนามสกุล";
    }
    
    if ($department_id <= 0) {
        $errors[] = "กรุณาเลือกหน่วยงาน/กลุ่มสาระ";
    } elseif (empty($departments)) {
        $errors[] = "ไม่มีหน่วยงานให้เลือก กรุณาเพิ่มหน่วยงานก่อน";
    }
    
    if (empty($positions)) {
        $errors[] = "กรุณาระบุตำแหน่งอย่างน้อย 1 ตำแหน่ง";
    }
    
    // Process image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadStaffPhoto($_FILES['image']);

        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        } else {
            $errors[] = $upload_result['error'];
        }
    }

    // Process CV file upload
    $cv_file_path = '';
    if (isset($_FILES['cv_file_path']) && $_FILES['cv_file_path']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadStaffCV($_FILES['cv_file_path']);

        if ($upload_result['success']) {
            $cv_file_path = $upload_result['path'];
        } else {
            $errors[] = $upload_result['error'];
        }
    }
    
    // If no errors, insert data into database
    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert staff data - ตรวจสอบว่ามี column ใหม่หรือไม่
            $check_col = $conn->query("SHOW COLUMNS FROM staff LIKE 'position'");
            if ($check_col->num_rows > 0) {
                // มี column ใหม่
                $stmt = $conn->prepare("INSERT INTO staff (title, first_name, last_name, position, department_id, image_path, email, phone, expertise, bio, cv_file_path, google_scholar_url, work_status, is_head, order_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssissssssssiss", $title, $first_name, $last_name, $position, $department_id, $image_path, $email, $phone, $expertise, $bio, $cv_file_path, $google_scholar_url, $work_status, $is_head, $order_number, $status);
            } else {
                // ยังไม่มี column ใหม่ ใช้แบบเดิม
                $stmt = $conn->prepare("INSERT INTO staff (title, first_name, last_name, department_id, image_path, email, phone, bio, cv_file_path, google_scholar_url, is_head, order_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssississsiis", $title, $first_name, $last_name, $department_id, $image_path, $email, $phone, $bio, $cv_file_path, $google_scholar_url, $is_head, $order_number, $status);
            }
            $stmt->execute();
            
            $staff_id = $conn->insert_id;
            
            // Insert positions
            $pos_stmt = $conn->prepare("INSERT INTO staff_positions (staff_id, position_name, is_primary) VALUES (?, ?, ?)");
            
            foreach ($positions as $position) {
                $pos_stmt->bind_param("isi", $staff_id, $position['name'], $position['is_primary']);
                $pos_stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Redirect to index page with success message
            header("Location: index.php?success=เพิ่มข้อมูลบุคลากรเรียบร้อยแล้ว");
            exit;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $errors[] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            
            // Delete uploaded image if exists
            if (!empty($image_path) && file_exists('../../' . $image_path)) {
                unlink('../../' . $image_path);
            }
        }
    }
}

// Set page title
$page_title = "เพิ่มบุคลากรใหม่";
$include_summernote = true;

// Set template variables
$page_header_icon = '<i class="fas fa-user-plus me-3"></i>';
$back_button = true;
$back_url = 'index.php';
$back_text = 'กลับไปหน้ารายการ';

// Custom scripts for this page
$custom_scripts = <<<EOT
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Summernote
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
    
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewImg = imagePreview.querySelector('img');
    
    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreviewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            
            reader.readAsDataURL(this.files[0]);
        } else {
            imagePreview.classList.add('d-none');
        }
    });
    
    // Add/remove position fields
    let positionIndex = 0;
    const positionsContainer = document.getElementById('positions-container');
    const addPositionBtn = document.getElementById('add-position');
    
    addPositionBtn.addEventListener('click', function() {
        positionIndex++;
        const newPosition = document.createElement('div');
        newPosition.className = 'position-item mb-2 d-flex align-items-center';
        newPosition.innerHTML = `
            <input type="text" class="form-control me-2" name="position_name[]" placeholder="ชื่อตำแหน่ง" required>
            <div class="form-check me-2">
                <input class="form-check-input primary-position" type="checkbox" name="is_primary_position[\${positionIndex}]" id="is_primary_position_\${positionIndex}">
                <label class="form-check-label" for="is_primary_position_\${positionIndex}">หลัก</label>
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-position">
                <i class="fas fa-times"></i>
            </button>
        `;
        positionsContainer.appendChild(newPosition);
        
        // Show remove button for first position if there are now multiple positions
        if (positionsContainer.querySelectorAll('.position-item').length > 1) {
            const firstRemoveBtn = positionsContainer.querySelector('.position-item:first-child .remove-position');
            if (firstRemoveBtn) {
                firstRemoveBtn.classList.remove('d-none');
            }
        }
        
        // Add event listener for new primary position checkbox
        const newCheckbox = newPosition.querySelector('.primary-position');
        newCheckbox.addEventListener('change', handlePrimaryPositionChange);
    });
    
    // Remove position
    positionsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-position') || e.target.parentElement.classList.contains('remove-position')) {
            const button = e.target.classList.contains('remove-position') ? e.target : e.target.parentElement;
            const positionItem = button.closest('.position-item');
            positionItem.remove();
            
            // Hide remove button for first position if there's only one position left
            if (positionsContainer.querySelectorAll('.position-item').length === 1) {
                const firstRemoveBtn = positionsContainer.querySelector('.position-item:first-child .remove-position');
                if (firstRemoveBtn) {
                    firstRemoveBtn.classList.add('d-none');
                }
            }
        }
    });
    
    // Handle primary position checkboxes (only one can be checked)
    function handlePrimaryPositionChange(e) {
        const checkboxes = document.querySelectorAll('.primary-position');
        if (e.target.checked) {
            checkboxes.forEach(cb => {
                if (cb !== e.target) {
                    cb.checked = false;
                }
            });
        } else {
            // If unchecking, make sure at least one is checked
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (!anyChecked && checkboxes.length > 0) {
                checkboxes[0].checked = true;
            }
        }
    }
    
    // Add event listeners for existing primary position checkboxes
    document.querySelectorAll('.primary-position').forEach(checkbox => {
        checkbox.addEventListener('change', handlePrimaryPositionChange);
    });
});
</script>
EOT;

// Start content output
ob_start();
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">เพิ่มบุคลากรใหม่</h1>
        <a href="index.php" class="btn btn-secondary btn-sm rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> กลับไปหน้ารายการ
        </a>
    </div>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Staff Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">กรอกข้อมูลบุคลากร</h6>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">ข้อมูลส่วนตัว</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label for="title" class="col-sm-3 col-form-label">คำนำหน้าชื่อ <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="title" name="title" required 
                                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                                               placeholder="เช่น นาย, นาง, นางสาว, ดร., ผศ., รศ., ศ.">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="first_name" class="col-sm-3 col-form-label">ชื่อ <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="first_name" name="first_name" required 
                                               value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="last_name" class="col-sm-3 col-form-label">นามสกุล <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="last_name" name="last_name" required 
                                               value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="position" class="col-sm-3 col-form-label">ตำแหน่ง</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="position" name="position" 
                                               value="<?php echo isset($_POST['position']) ? htmlspecialchars($_POST['position']) : ''; ?>"
                                               placeholder="เช่น ครู, ครูผู้ช่วย, ครูชำนาญการ">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="email" class="col-sm-3 col-form-label">อีเมล</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="phone" class="col-sm-3 col-form-label">เบอร์โทรศัพท์</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="image" class="col-sm-3 col-form-label">รูปภาพ</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <small class="form-text text-muted">รองรับไฟล์ภาพ JPG, PNG, WEBP ขนาดไม่เกิน 5MB</small>
                                        <div id="image-preview" class="mt-2 d-none">
                                            <img src="" class="img-thumbnail" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Department and Position -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">ข้อมูลหน่วยงานและตำแหน่ง</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label for="department_id" class="col-sm-3 col-form-label">หน่วยงาน <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <?php if (empty($departments)): ?>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                ไม่พบข้อมูลหน่วยงาน กรุณาเพิ่มหน่วยงานก่อน
                                                <a href="department_manager.php" class="btn btn-sm btn-primary ms-2">จัดการหน่วยงาน</a>
                                            </div>
                                            <select class="form-select" id="department_id" name="department_id" disabled>
                                                <option value="">-- ไม่มีหน่วยงานให้เลือก --</option>
                                            </select>
                                        <?php else: ?>
                                            <select class="form-select" id="department_id" name="department_id" required>
                                                <option value="">-- เลือกหน่วยงาน/กลุ่มสาระ --</option>
                                                <?php
                                                // Group departments by type
                                                $grouped_departments = [
                                                    'academic' => [],
                                                    'support' => [],
                                                    'primary' => []
                                                ];

                                                foreach ($departments as $dept) {
                                                    if (isset($grouped_departments[$dept['type']])) {
                                                        $grouped_departments[$dept['type']][] = $dept;
                                                    }
                                                }
                                                ?>

                                                <?php if (!empty($grouped_departments['academic'])): ?>
                                                    <optgroup label="สายวิชาการ">
                                                        <?php foreach ($grouped_departments['academic'] as $dept): ?>
                                                            <option value="<?php echo $dept['id']; ?>" <?php echo isset($_POST['department_id']) && $_POST['department_id'] == $dept['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($dept['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endif; ?>

                                                <?php if (!empty($grouped_departments['primary'])): ?>
                                                    <optgroup label="ประถมศึกษา">
                                                        <?php foreach ($grouped_departments['primary'] as $dept): ?>
                                                            <option value="<?php echo $dept['id']; ?>" <?php echo isset($_POST['department_id']) && $_POST['department_id'] == $dept['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($dept['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endif; ?>

                                                <?php if (!empty($grouped_departments['support'])): ?>
                                                    <optgroup label="สายสนับสนุน">
                                                        <?php foreach ($grouped_departments['support'] as $dept): ?>
                                                            <option value="<?php echo $dept['id']; ?>" <?php echo isset($_POST['department_id']) && $_POST['department_id'] == $dept['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($dept['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endif; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">ตำแหน่ง <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div id="positions-container">
                                            <div class="position-item mb-2 d-flex align-items-center">
                                                <input type="text" class="form-control me-2" name="position_name[]" placeholder="ชื่อตำแหน่ง" required>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input primary-position" type="checkbox" name="is_primary_position[0]" id="is_primary_position_0" checked>
                                                    <label class="form-check-label" for="is_primary_position_0">หลัก</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger remove-position d-none">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" id="add-position" class="btn btn-sm btn-success mt-2">
                                            <i class="fas fa-plus me-1"></i> เพิ่มตำแหน่ง
                                        </button>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-9 offset-sm-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_head" name="is_head" 
                                                   <?php echo isset($_POST['is_head']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_head">
                                                เป็นหัวหน้าหน่วยงาน/กลุ่มสาระ
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="order_number" class="col-sm-3 col-form-label">ลำดับการแสดงผล</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="order_number" name="order_number" min="0" 
                                               value="<?php echo isset($_POST['order_number']) ? (int)$_POST['order_number'] : 0; ?>">
                                        <small class="form-text text-muted">ตัวเลขน้อยจะแสดงก่อน (0 คือค่าเริ่มต้น)</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="work_status" class="col-sm-3 col-form-label">สถานะการทำงาน</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" id="work_status" name="work_status">
                                            <option value="working" <?php echo (!isset($_POST['work_status']) || $_POST['work_status'] === 'working') ? 'selected' : ''; ?>>ปฏิบัติงาน</option>
                                            <option value="retired" <?php echo (isset($_POST['work_status']) && $_POST['work_status'] === 'retired') ? 'selected' : ''; ?>>เกษียณอายุ</option>
                                            <option value="leave" <?php echo (isset($_POST['work_status']) && $_POST['work_status'] === 'leave') ? 'selected' : ''; ?>>ลาศึกษาต่อ</option>
                                            <option value="resigned" <?php echo (isset($_POST['work_status']) && $_POST['work_status'] === 'resigned') ? 'selected' : ''; ?>>ลาออก</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="status" class="col-sm-3 col-form-label">สถานะในระบบ</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" <?php echo (!isset($_POST['status']) || $_POST['status'] === 'active') ? 'selected' : ''; ?>>เปิดใช้งาน</option>
                                            <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>ปิดใช้งาน</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">ข้อมูลเพิ่มเติม</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label for="expertise" class="col-sm-2 col-form-label">ความเชี่ยวชาญ</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="expertise" name="expertise" rows="3"
                                                  placeholder="เช่น Microfluidics / MeV Ion Beam / Image processing"><?php echo isset($_POST['expertise']) ? htmlspecialchars($_POST['expertise']) : ''; ?></textarea>
                                        <small class="form-text text-muted">ระบุความเชี่ยวชาญหรือสาขาที่สนใจ</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="cv_file_path" class="col-sm-2 col-form-label">ไฟล์ CV (PDF)</label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control" id="cv_file_path" name="cv_file_path" accept=".pdf">
                                        <small class="form-text text-muted">อัปโหลดไฟล์ PDF ของประวัติส่วนตัว (ไม่บังคับ)</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="google_scholar_url" class="col-sm-2 col-form-label">Google Scholar</label>
                                    <div class="col-sm-10">
                                        <input type="url" class="form-control" id="google_scholar_url" name="google_scholar_url"
                                               value="<?php echo isset($_POST['google_scholar_url']) ? htmlspecialchars($_POST['google_scholar_url']) : ''; ?>"
                                               placeholder="https://scholar.google.com/citations?user=...">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="bio" class="col-sm-2 col-form-label">ประวัติและผลงาน</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control summernote" id="bio" name="bio"><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Page Content -->

<?php
// End content output
$content = ob_get_clean();

// Include template
include '../news/template.php';
?>
