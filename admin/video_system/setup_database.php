<?php
require_once '../includes/auth_functions.php';

// Check if user is logged in and has admin privileges
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Include database configuration
require_once '../../video_system/includes/db_config.php';

$success_message = '';
$error_message = '';

// ตรวจสอบว่าตารางมีอยู่แล้วหรือไม่
function tableExists($conn, $table_name) {
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    return $result->num_rows > 0;
}

// ดำเนินการตั้งค่าฐานข้อมูลเมื่อกดปุ่ม
if (isset($_POST['setup_database'])) {
    // SQL สำหรับสร้างตาราง video_categories
    $sql_create_categories = "
    CREATE TABLE IF NOT EXISTS video_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // SQL สำหรับสร้างตาราง videos
    $sql_create_videos = "
    CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        youtube_url VARCHAR(255) NOT NULL UNIQUE,
        thumbnail_url VARCHAR(255),
        is_featured BOOLEAN DEFAULT FALSE,
        views INT DEFAULT 0,
        shares INT DEFAULT 0,
        upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES video_categories(id) ON DELETE SET NULL
    )";

    // SQL สำหรับเพิ่มข้อมูลหมวดหมู่เริ่มต้น
    $sql_insert_categories = "
    INSERT IGNORE INTO video_categories (id, name, description) VALUES
    (1, 'บรรยากาศการเรียนการสอน', 'วิดีโอที่แสดงถึงบรรยากาศและกิจกรรมการเรียนการสอนภายในโรงเรียน'),
    (2, 'กีฬา', 'วิดีโอการแข่งขันกีฬา กิจกรรมกีฬา และการฝึกซ้อมของนักเรียน'),
    (3, 'ดนตรี', 'วิดีโอการแสดงดนตรี กิจกรรมทางดนตรี และวงดนตรีของโรงเรียน'),
    (4, 'ห้องปฏิบัติการ', 'วิดีโอการทดลอง กิจกรรมในห้องปฏิบัติการวิทยาศาสตร์และคอมพิวเตอร์'),
    (5, 'แนะนำมหาวิทยาลัย', 'วิดีโอแนะนำมหาวิทยาลัยพะเยาและคณะต่างๆ'),
    (6, 'การเรียนภาษาอังกฤษ', 'วิดีโอเกี่ยวกับการเรียนการสอนภาษาอังกฤษและกิจกรรมส่งเสริมทักษะภาษา'),
    (7, 'การนำเสนอผลงานทางวิชาการในและต่างประเทศ', 'วิดีโอการนำเสนอผลงานวิชาการของนักเรียนทั้งในและต่างประเทศ'),
    (8, 'โครงการ วมว.', 'วิดีโอเกี่ยวกับโครงการห้องเรียนวิทยาศาสตร์ในโรงเรียน โดยการกำกับดูแลของมหาวิทยาลัย (วมว.)'),
    (9, 'ผลงานนักเรียน', 'วิดีโอแสดงผลงาน โครงงาน และความสำเร็จต่างๆ ของนักเรียน')
    ";

    // ดำเนินการสร้างตาราง
    try {
        global $video_conn;
        
        // สร้างตาราง video_categories
        if ($video_conn->query($sql_create_categories)) {
            $success_message .= "สร้างตาราง video_categories สำเร็จ<br>";
            
            // สร้างตาราง videos
            if ($video_conn->query($sql_create_videos)) {
                $success_message .= "สร้างตาราง videos สำเร็จ<br>";
                
                // เพิ่มข้อมูลหมวดหมู่เริ่มต้น
                if ($video_conn->query($sql_insert_categories)) {
                    $success_message .= "เพิ่มข้อมูลหมวดหมู่เริ่มต้นสำเร็จ<br>";
                } else {
                    $error_message .= "เกิดข้อผิดพลาดในการเพิ่มข้อมูลหมวดหมู่: " . $video_conn->error . "<br>";
                }
            } else {
                $error_message .= "เกิดข้อผิดพลาดในการสร้างตาราง videos: " . $video_conn->error . "<br>";
            }
        } else {
            $error_message .= "เกิดข้อผิดพลาดในการสร้างตาราง video_categories: " . $video_conn->error . "<br>";
        }
    } catch (Exception $e) {
        $error_message .= "เกิดข้อผิดพลาด: " . $e->getMessage() . "<br>";
    }
}

// ดำเนินการรีเซ็ตฐานข้อมูลเมื่อกดปุ่ม
if (isset($_POST['reset_database'])) {
    try {
        global $video_conn;
        
        // ลบตาราง videos ก่อนเพราะมี foreign key
        $sql_drop_videos = "DROP TABLE IF EXISTS videos";
        if ($video_conn->query($sql_drop_videos)) {
            $success_message .= "ลบตาราง videos สำเร็จ<br>";
        } else {
            $error_message .= "เกิดข้อผิดพลาดในการลบตาราง videos: " . $video_conn->error . "<br>";
        }
        
        // ลบตาราง video_categories
        $sql_drop_categories = "DROP TABLE IF EXISTS video_categories";
        if ($video_conn->query($sql_drop_categories)) {
            $success_message .= "ลบตาราง video_categories สำเร็จ<br>";
        } else {
            $error_message .= "เกิดข้อผิดพลาดในการลบตาราง video_categories: " . $video_conn->error . "<br>";
        }
    } catch (Exception $e) {
        $error_message .= "เกิดข้อผิดพลาด: " . $e->getMessage() . "<br>";
    }
}

// ตรวจสอบสถานะของตาราง
$categories_table_exists = tableExists($video_conn, 'video_categories');
$videos_table_exists = tableExists($video_conn, 'videos');

$page_title = "ตั้งค่าฐานข้อมูลวิดีโอ";

// Start output buffering
ob_start();
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">ตั้งค่าฐานข้อมูลระบบวิดีโอ</h1>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">สถานะฐานข้อมูล</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ตาราง</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>video_categories</td>
                                    <td>
                                        <?php if ($categories_table_exists): ?>
                                            <span class="badge badge-success">มีอยู่แล้ว</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">ยังไม่มี</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>videos</td>
                                    <td>
                                        <?php if ($videos_table_exists): ?>
                                            <span class="badge badge-success">มีอยู่แล้ว</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">ยังไม่มี</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <form method="POST" action="">
                            <button type="submit" name="setup_database" class="btn btn-primary">
                                <i class="fas fa-database"></i> ตั้งค่าฐานข้อมูล
                            </button>
                            <button type="submit" name="reset_database" class="btn btn-danger ml-2" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะรีเซ็ตฐานข้อมูล? ข้อมูลทั้งหมดจะถูกลบ');">
                                <i class="fas fa-trash"></i> รีเซ็ตฐานข้อมูล
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">คำแนะนำ</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5 class="alert-heading">การตั้งค่าฐานข้อมูล</h5>
                        <p>การตั้งค่าฐานข้อมูลจะสร้างตารางที่จำเป็นสำหรับระบบวิดีโอ ได้แก่:</p>
                        <ul>
                            <li><strong>video_categories</strong> - ตารางสำหรับเก็บข้อมูลหมวดหมู่วิดีโอ</li>
                            <li><strong>videos</strong> - ตารางสำหรับเก็บข้อมูลวิดีโอ</li>
                        </ul>
                        <p>หากตารางมีอยู่แล้ว ระบบจะไม่สร้างใหม่</p>
                        <hr>
                        <h5 class="alert-heading">การรีเซ็ตฐานข้อมูล</h5>
                        <p>การรีเซ็ตฐานข้อมูลจะลบตารางทั้งหมดที่เกี่ยวข้องกับระบบวิดีโอ และข้อมูลทั้งหมดจะหายไป</p>
                        <p class="mb-0 text-danger"><strong>คำเตือน:</strong> การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <h5 class="alert-heading">หมายเหตุ</h5>
                        <p>หากคุณพบปัญหาในการตั้งค่าฐานข้อมูล โปรดตรวจสอบ:</p>
                        <ul>
                            <li>การเชื่อมต่อฐานข้อมูล (ไฟล์ video_system/includes/db_config.php)</li>
                            <li>สิทธิ์ในการสร้างตารางในฐานข้อมูล</li>
                            <li>ความถูกต้องของโครงสร้างฐานข้อมูล</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
