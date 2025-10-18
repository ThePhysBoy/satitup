<?php
/**
 * News Sidebar Menu
 * Navigation menu for news management
 */

// Get current page name
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<div class="list-group mb-4">
    <div class="list-group-item list-group-item-dark">
        <i class="fas fa-newspaper me-2"></i>จัดการข่าวประชาสัมพันธ์
    </div>
    <a href="dashboard.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="fas fa-tachometer-alt me-2"></i>แดชบอร์ด
    </a>
    <a href="index.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
        <i class="fas fa-list me-2"></i>รายการข่าวทั้งหมด
    </a>
    <a href="create.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'create.php') ? 'active' : ''; ?>">
        <i class="fas fa-plus-circle me-2"></i>เพิ่มข่าวใหม่
    </a>
    <a href="search.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'search.php') ? 'active' : ''; ?>">
        <i class="fas fa-search me-2"></i>ค้นหาข่าว
    </a>
    <a href="export_form.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'export_form.php') ? 'active' : ''; ?>">
        <i class="fas fa-file-export me-2"></i>ส่งออกข้อมูล
    </a>
</div>

<div class="list-group mb-4">
    <div class="list-group-item list-group-item-dark">
        <i class="fas fa-link me-2"></i>ลิงก์ที่เกี่ยวข้อง
    </div>
    <a href="../index.php" class="list-group-item list-group-item-action">
        <i class="fas fa-home me-2"></i>หน้าหลักผู้ดูแลระบบ
    </a>
    <a href="../../news/index.php" class="list-group-item list-group-item-action" target="_blank">
        <i class="fas fa-external-link-alt me-2"></i>หน้าข่าวประชาสัมพันธ์
    </a>
</div>
