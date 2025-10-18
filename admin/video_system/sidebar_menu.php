<?php
// Sidebar menu for video system
?>
<ul class="nav nav-pills flex-column">
    <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
            <i class="fas fa-list me-2"></i>รายการวิดีโอทั้งหมด
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_video.php' ? 'active' : ''; ?>" href="add_video.php">
            <i class="fas fa-plus me-2"></i>เพิ่มวิดีโอใหม่
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'setup_database.php' ? 'active' : ''; ?>" href="setup_database.php">
            <i class="fas fa-database me-2"></i>ตั้งค่าฐานข้อมูล
        </a>
    </li>
    <li class="nav-item mt-3">
        <a class="nav-link" href="../index.php">
            <i class="fas fa-arrow-left me-2"></i>กลับหน้าแดชบอร์ด
        </a>
    </li>
</ul>
