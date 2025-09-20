<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Function to check if a menu item should be active
function isActive($page_name, $dir_name = null) {
    global $current_page, $current_dir;
    if ($dir_name !== null) {
        return ($current_page === $page_name && $current_dir === $dir_name) ? 'active' : '';
    }
    return ($current_page === $page_name) ? 'active' : '';
}

// Function to check if a menu item should be expanded
function isExpanded($dir_name) {
    global $current_dir;
    return ($current_dir === $dir_name) ? 'show' : '';
}

// Function to check if a parent menu item should be active
function isParentActive($dir_name) {
    global $current_dir;
    return ($current_dir === $dir_name) ? 'active' : '';
}
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-school"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SATITUP Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo isActive('index.php'); ?>">
        <a class="nav-link" href="<?php echo $current_dir !== 'admin' ? '../index.php' : 'index.php'; ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>แดชบอร์ด</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        จัดการเว็บไซต์
    </div>

    <?php if (isAdmin() || canManageSlideshow()): ?>
    <!-- Nav Item - Slideshow -->
    <li class="nav-item <?php echo isParentActive('slideshow'); ?>">
        <a class="nav-link <?php echo isParentActive('slideshow') ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#collapseSlideshows"
            aria-expanded="<?php echo isParentActive('slideshow') ? 'true' : 'false'; ?>" aria-controls="collapseSlideshows">
            <i class="fas fa-fw fa-images"></i>
            <span>สไลด์โชว์</span>
        </a>
        <div id="collapseSlideshows" class="collapse <?php echo isExpanded('slideshow'); ?>" aria-labelledby="headingSlideshows" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">จัดการสไลด์โชว์:</h6>
                <a class="collapse-item <?php echo isActive('index.php', 'slideshow'); ?>" href="<?php echo $current_dir !== 'slideshow' ? '../slideshow/index.php' : 'index.php'; ?>">รายการทั้งหมด</a>
                <a class="collapse-item <?php echo isActive('create.php', 'slideshow'); ?>" href="<?php echo $current_dir !== 'slideshow' ? '../slideshow/create.php' : 'create.php'; ?>">เพิ่มสไลด์ใหม่</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php if (isAdmin() || canManageRankings()): ?>
    <!-- Nav Item - Rankings -->
    <li class="nav-item <?php echo isParentActive('rankings'); ?>">
        <a class="nav-link <?php echo isParentActive('rankings') ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#collapseRankings"
            aria-expanded="<?php echo isParentActive('rankings') ? 'true' : 'false'; ?>" aria-controls="collapseRankings">
            <i class="fas fa-fw fa-trophy"></i>
            <span>อันดับมหาวิทยาลัย</span>
        </a>
        <div id="collapseRankings" class="collapse <?php echo isExpanded('rankings'); ?>" aria-labelledby="headingRankings" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">จัดการอันดับ:</h6>
                <a class="collapse-item <?php echo isActive('index.php', 'rankings'); ?>" href="<?php echo $current_dir !== 'rankings' ? '../rankings/index.php' : 'index.php'; ?>">รายการทั้งหมด</a>
                <a class="collapse-item <?php echo isActive('create.php', 'rankings'); ?>" href="<?php echo $current_dir !== 'rankings' ? '../rankings/create.php' : 'create.php'; ?>">เพิ่มอันดับใหม่</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php if (isAdmin() || isPrOfficer()): ?>
    <!-- Nav Item - News -->
    <li class="nav-item <?php echo isParentActive('news'); ?>">
        <a class="nav-link <?php echo isParentActive('news') ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#collapseNews"
            aria-expanded="<?php echo isParentActive('news') ? 'true' : 'false'; ?>" aria-controls="collapseNews">
            <i class="fas fa-fw fa-newspaper"></i>
            <span>ข่าวและกิจกรรม</span>
        </a>
        <div id="collapseNews" class="collapse <?php echo isExpanded('news'); ?>" aria-labelledby="headingNews" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">จัดการข่าว:</h6>
                <a class="collapse-item <?php echo isActive('index.php', 'news'); ?>" href="<?php echo $current_dir !== 'news' ? '../news/index.php' : 'index.php'; ?>">รายการข่าวทั้งหมด</a>
                <a class="collapse-item <?php echo isActive('create.php', 'news'); ?>" href="<?php echo $current_dir !== 'news' ? '../news/create.php' : 'create.php'; ?>">เพิ่มข่าวใหม่</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php if (isAdmin() || isPrOfficer()): ?>
    <!-- Nav Item - Staff -->
    <li class="nav-item <?php echo isParentActive('staff'); ?>">
        <a class="nav-link <?php echo isParentActive('staff') ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#collapseStaff"
            aria-expanded="<?php echo isParentActive('staff') ? 'true' : 'false'; ?>" aria-controls="collapseStaff">
            <i class="fas fa-fw fa-users"></i>
            <span>บุคลากร</span>
        </a>
        <div id="collapseStaff" class="collapse <?php echo isExpanded('staff'); ?>" aria-labelledby="headingStaff" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">จัดการข้อมูลบุคลากร:</h6>
                <a class="collapse-item <?php echo isActive('index.php', 'staff'); ?>" href="<?php echo $current_dir !== 'staff' ? '../staff/index.php' : 'index.php'; ?>">รายการบุคลากรทั้งหมด</a>
                <a class="collapse-item <?php echo isActive('create.php', 'staff'); ?>" href="<?php echo $current_dir !== 'staff' ? '../staff/create.php' : 'create.php'; ?>">เพิ่มบุคลากรใหม่</a>
                <a class="collapse-item <?php echo isActive('setup_database.php', 'staff'); ?>" href="<?php echo $current_dir !== 'staff' ? '../staff/setup_database.php' : 'setup_database.php'; ?>">ตั้งค่าฐานข้อมูล</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        ผู้ใช้งาน
    </div>

    <!-- Nav Item - Profile -->
    <li class="nav-item <?php echo isActive('profile.php'); ?>">
        <a class="nav-link" href="<?php echo $current_dir !== 'admin' ? '../profile.php' : 'profile.php'; ?>">
            <i class="fas fa-fw fa-user"></i>
            <span>โปรไฟล์</span>
        </a>
    </li>

    <?php if (isAdmin()): ?>
    <!-- Nav Item - User Management -->
    <li class="nav-item <?php echo isActive('users.php'); ?>">
        <a class="nav-link" href="<?php echo $current_dir !== 'admin' ? '../users.php' : 'users.php'; ?>">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>จัดการผู้ใช้</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Nav Item - Logout -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $current_dir !== 'admin' ? '../logout.php' : 'logout.php'; ?>">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>ออกจากระบบ</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->