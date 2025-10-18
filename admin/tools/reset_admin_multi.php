<?php
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireAdmin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernames = ['admin02','admin03','admin04','admin05'];
    $newpass = '1234';
    if (isset($_POST['newpass']) && $_POST['newpass'] !== '') {
        $newpass = $_POST['newpass'];
    }
    if (strlen($newpass) < 4) {
        $message = 'รหัสผ่านควรมีอย่างน้อย 4 ตัวอักษร';
    } else {
        $hash = password_hash($newpass, PASSWORD_DEFAULT);
        $updated = 0;
        foreach ($usernames as $u) {
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
            $stmt->bind_param('ss', $hash, $u);
            if ($stmt->execute()) { $updated++; }
        }
        $message = 'อัพเดทรหัสผ่านแล้ว ' . $updated . ' ผู้ใช้';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีเซ็ตรหัสผ่าน admin02-05</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body{background:#f7f7fb} </style>
    </head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3">รีเซ็ตรหัสผ่าน admin02 – admin05</h4>
                        <?php if ($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่านใหม่ (ค่าเริ่มต้น 1234)</label>
                                <input class="form-control" type="text" name="newpass" placeholder="1234">
                            </div>
                            <div class="d-grid"><button class="btn btn-primary">ยืนยัน</button></div>
                        </form>
                        <hr>
                        <p class="text-muted small mb-0">ผู้ใช้ที่ได้รับผล: admin02, admin03, admin04, admin05</p>
                        <a href="../index.php" class="btn btn-link">กลับผู้ดูแลระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


