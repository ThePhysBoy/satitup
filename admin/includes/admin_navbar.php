<?php
// Simple admin navbar
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../news/index.php">จัดการข่าว</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../official_documents/index.php">เอกสารราชการ</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">ออกจากระบบ</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
