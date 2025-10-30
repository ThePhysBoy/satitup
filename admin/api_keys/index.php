<?php
session_start();
require_once '../../db_connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Handle actions
$message = '';
$error = '';

// Update API Key
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_api'])) {
        $id = intval($_POST['id']);
        $api_key = trim($_POST['api_key']);
        $api_description = trim($_POST['api_description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        
        $update_query = "UPDATE api_keys SET 
                        api_key = ?, 
                        api_description = ?, 
                        is_active = ?,
                        expires_at = ?,
                        updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('ssisi', $api_key, $api_description, $is_active, $expires_at, $id);
        
        if ($stmt->execute()) {
            $message = "อัพเดท API Key สำเร็จ";
        } else {
            $error = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
    
    // Add new API Key
    if (isset($_POST['add_api'])) {
        $api_name = trim($_POST['api_name']);
        $api_key = trim($_POST['api_key']);
        $api_description = trim($_POST['api_description']);
        $service_provider = trim($_POST['service_provider']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        
        $insert_query = "INSERT INTO api_keys (api_name, api_key, api_description, service_provider, is_active, expires_at, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $created_by = $_SESSION['username'] ?? 'Admin';
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param('ssssiss', $api_name, $api_key, $api_description, $service_provider, $is_active, $expires_at, $created_by);
        
        if ($stmt->execute()) {
            $message = "เพิ่ม API Key ใหม่สำเร็จ";
        } else {
            if ($conn->errno == 1062) {
                $error = "ชื่อ API นี้มีอยู่แล้ว";
            } else {
                $error = "เกิดข้อผิดพลาด: " . $conn->error;
            }
        }
    }
    
    // Delete API Key
    if (isset($_POST['delete_api'])) {
        $id = intval($_POST['id']);
        
        $delete_query = "DELETE FROM api_keys WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            $message = "ลบ API Key สำเร็จ";
        } else {
            $error = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
}

// Get all API Keys
$api_keys_query = "SELECT * FROM api_keys ORDER BY api_name ASC";
$api_keys_result = $conn->query($api_keys_query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการ API Keys - ระบบจัดการเว็บไซต์</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Prompt', sans-serif;
        }
        
        .container {
            padding-top: 30px;
            padding-bottom: 30px;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        
        .api-key-input {
            font-family: monospace;
            background: #f8f9fa;
        }
        
        .badge-active {
            background: #28a745;
        }
        
        .badge-inactive {
            background: #dc3545;
        }
        
        .api-card {
            transition: all 0.3s ease;
        }
        
        .api-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .btn-copy {
            cursor: pointer;
        }
        
        .provider-badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
        
        .expire-warning {
            color: #ffc107;
        }
        
        .expire-danger {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>
    
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-white">
                <i class="fas fa-key"></i> จัดการ API Keys
            </h1>
            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addApiModal">
                <i class="fas fa-plus"></i> เพิ่ม API Key ใหม่
            </button>
        </div>
        
        <!-- Messages -->
        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- API Keys Grid -->
        <div class="row">
            <?php while ($api = $api_keys_result->fetch_assoc()): ?>
            <?php
            // Check if expired
            $is_expired = false;
            $days_until_expire = null;
            if ($api['expires_at']) {
                $expire_date = new DateTime($api['expires_at']);
                $today = new DateTime();
                $diff = $today->diff($expire_date);
                $days_until_expire = $diff->format('%r%a');
                $is_expired = $days_until_expire < 0;
            }
            ?>
            <div class="col-lg-6 mb-4">
                <div class="card api-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-key"></i> <?php echo htmlspecialchars($api['api_name']); ?>
                            </h5>
                            <span class="badge <?php echo $api['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $api['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="id" value="<?php echo $api['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-building"></i> ผู้ให้บริการ
                                </label>
                                <span class="badge provider-badge bg-primary">
                                    <?php echo htmlspecialchars($api['service_provider']); ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-key"></i> API Key
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="api_key" 
                                           id="api_key_<?php echo $api['id']; ?>"
                                           class="form-control api-key-input" 
                                           value="<?php echo htmlspecialchars($api['api_key']); ?>"
                                           placeholder="กรอก API Key">
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-toggle-visibility"
                                            onclick="toggleApiKeyVisibility(<?php echo $api['id']; ?>)">
                                        <i class="fas fa-eye" id="eye_icon_<?php echo $api['id']; ?>"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-copy" 
                                            onclick="copyApiKey(this, '<?php echo htmlspecialchars($api['api_key']); ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <?php if (!empty($api['api_key'])): ?>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt"></i> 
                                    แสดง: <?php echo substr($api['api_key'], 0, 8); ?>...<?php echo substr($api['api_key'], -4); ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-info-circle"></i> คำอธิบาย
                                </label>
                                <textarea name="api_description" 
                                          class="form-control" 
                                          rows="2"><?php echo htmlspecialchars($api['api_description']); ?></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="fas fa-calendar"></i> วันหมดอายุ
                                    </label>
                                    <input type="date" 
                                           name="expires_at" 
                                           class="form-control" 
                                           value="<?php echo $api['expires_at']; ?>">
                                    <?php if ($is_expired): ?>
                                    <small class="expire-danger">
                                        <i class="fas fa-exclamation-triangle"></i> หมดอายุแล้ว
                                    </small>
                                    <?php elseif ($days_until_expire !== null && $days_until_expire <= 30): ?>
                                    <small class="expire-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        หมดอายุใน <?php echo $days_until_expire; ?> วัน
                                    </small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="fas fa-chart-line"></i> การใช้งาน
                                    </label>
                                    <div>
                                        <?php if ($api['usage_limit']): ?>
                                        <span class="badge bg-info">
                                            <?php echo $api['usage_count']; ?> / <?php echo $api['usage_limit']; ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">ไม่จำกัด</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="active_<?php echo $api['id']; ?>" 
                                           name="is_active" 
                                           <?php echo $api['is_active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="active_<?php echo $api['id']; ?>">
                                        เปิดใช้งาน API Key นี้
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" name="update_api" class="btn btn-primary">
                                        <i class="fas fa-save"></i> บันทึก
                                    </button>
                                    <?php if ($api['api_name'] !== 'google_maps'): ?>
                                    <button type="submit" 
                                            name="delete_api" 
                                            class="btn btn-danger"
                                            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบ API Key นี้?')">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    อัพเดท: <?php echo date('d/m/Y H:i', strtotime($api['updated_at'])); ?>
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <!-- Add API Modal -->
    <div class="modal fade" id="addApiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus"></i> เพิ่ม API Key ใหม่
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อ API (ภาษาอังกฤษ ไม่มีช่องว่าง)</label>
                            <input type="text" 
                                   name="api_name" 
                                   class="form-control" 
                                   required 
                                   pattern="[a-z0-9_]+"
                                   placeholder="เช่น google_analytics">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="text" 
                                   name="api_key" 
                                   class="form-control api-key-input" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ผู้ให้บริการ</label>
                            <input type="text" 
                                   name="service_provider" 
                                   class="form-control" 
                                   required
                                   placeholder="เช่น Google, Facebook">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="api_description" 
                                      class="form-control" 
                                      rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">วันหมดอายุ (ถ้ามี)</label>
                            <input type="date" 
                                   name="expires_at" 
                                   class="form-control">
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="new_active" 
                                   name="is_active" 
                                   checked>
                            <label class="form-check-label" for="new_active">
                                เปิดใช้งานทันที
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="add_api" class="btn btn-primary">
                            <i class="fas fa-save"></i> บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function copyApiKey(button, apiKey) {
        if (!apiKey) {
            alert('ไม่มี API Key ให้คัดลอก');
            return;
        }
        
        // Copy to clipboard
        navigator.clipboard.writeText(apiKey).then(function() {
            // Change button icon temporarily
            const icon = button.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');
            
            // Reset after 2 seconds
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        });
    }
    
    function toggleApiKeyVisibility(id) {
        const input = document.getElementById('api_key_' + id);
        const icon = document.getElementById('eye_icon_' + id);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>