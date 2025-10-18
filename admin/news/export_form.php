<?php
/**
 * News Export Form
 * Form to export news data
 */

// Include database connection and authentication functions
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

// Require user to be logged in and have news access permission
requireNewsAccess();

// Get categories for dropdown
$categories = getAllCategories($conn);

// Set page variables
$page_title = 'ส่งออกข้อมูลข่าว';
$page_header_icon = '<i class="fas fa-file-export me-2"></i>';
$back_button = true;
$back_button_url = 'index.php';
$back_button_text = 'กลับไปหน้ารายการข่าว';

// Build content
ob_start();
?>

<div class="card-modern mb-4">
    <div class="card-header-modern">
        <h5><i class="fas fa-file-export me-2"></i>ส่งออกข้อมูลข่าว</h5>
    </div>
    <div class="card-body-modern">
        <form method="get" action="export.php" class="row g-3">
            <div class="col-md-6">
                <label class="form-label-modern">ประเภทการส่งออก</label>
                <select class="form-select form-select-modern" name="type" id="export-type">
                    <option value="all">ทั้งหมด</option>
                    <option value="category">ตามหมวดหมู่</option>
                    <option value="status">ตามสถานะ</option>
                    <option value="date">ตามช่วงวันที่</option>
                </select>
            </div>
            
            <div class="col-md-6 export-option" id="category-option" style="display: none;">
                <label class="form-label-modern">หมวดหมู่</label>
                <select class="form-select form-select-modern" name="category_id">
                    <option value="">เลือกหมวดหมู่</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6 export-option" id="status-option" style="display: none;">
                <label class="form-label-modern">สถานะ</label>
                <select class="form-select form-select-modern" name="status">
                    <option value="">เลือกสถานะ</option>
                    <option value="published">เผยแพร่</option>
                    <option value="draft">แบบร่าง</option>
                    <option value="pending">รอตรวจสอบ</option>
                </select>
            </div>
            
            <div class="col-md-6 export-option" id="date-from-option" style="display: none;">
                <label class="form-label-modern">วันที่เริ่มต้น</label>
                <input type="date" class="form-control form-control-modern" name="date_from">
            </div>
            
            <div class="col-md-6 export-option" id="date-to-option" style="display: none;">
                <label class="form-label-modern">วันที่สิ้นสุด</label>
                <input type="date" class="form-control form-control-modern" name="date_to">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-modern">
                    <i class="fas fa-download me-2"></i>ส่งออกข้อมูล (CSV)
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card-modern mb-4">
    <div class="card-header-modern">
        <h5><i class="fas fa-info-circle me-2"></i>คำแนะนำการส่งออกข้อมูล</h5>
    </div>
    <div class="card-body-modern">
        <div class="alert alert-info">
            <h6><i class="fas fa-lightbulb me-2"></i>วิธีการส่งออกข้อมูล</h6>
            <ol>
                <li>เลือกประเภทการส่งออก (ทั้งหมด, ตามหมวดหมู่, ตามสถานะ, หรือตามช่วงวันที่)</li>
                <li>กรอกข้อมูลตามประเภทที่เลือก</li>
                <li>คลิกปุ่ม "ส่งออกข้อมูล (CSV)"</li>
                <li>ไฟล์ CSV จะถูกดาวน์โหลดโดยอัตโนมัติ</li>
            </ol>
            <p class="mb-0">ไฟล์ CSV สามารถเปิดได้ด้วยโปรแกรม Microsoft Excel, Google Sheets, หรือโปรแกรมตารางคำนวณอื่นๆ</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportType = document.getElementById('export-type');
    const categoryOption = document.getElementById('category-option');
    const statusOption = document.getElementById('status-option');
    const dateFromOption = document.getElementById('date-from-option');
    const dateToOption = document.getElementById('date-to-option');
    
    exportType.addEventListener('change', function() {
        // Hide all options first
        categoryOption.style.display = 'none';
        statusOption.style.display = 'none';
        dateFromOption.style.display = 'none';
        dateToOption.style.display = 'none';
        
        // Show relevant options based on selected type
        switch (this.value) {
            case 'category':
                categoryOption.style.display = 'block';
                break;
            case 'status':
                statusOption.style.display = 'block';
                break;
            case 'date':
                dateFromOption.style.display = 'block';
                dateToOption.style.display = 'block';
                break;
        }
    });
});
</script>

<?php
$content = ob_get_clean();

// Include the template
include 'template.php';
?>
