<?php
$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
requireAdmin();

$message = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$newpass = $_POST['newpass'] ?? '';
	if (strlen($newpass) < 4) {
		$message = 'รหัสผ่านควรมีอย่างน้อย 4 ตัวอักษร';
	} else {
		$hash = password_hash($newpass, PASSWORD_DEFAULT);
		$stmt = $conn->prepare("UPDATE users SET password=?, role='admin', user_type='pr_officer', full_name=COALESCE(full_name,'นักประชาสัมพันธ์'), position=COALESCE(position,'เจ้าหน้าที่ประชาสัมพันธ์') WHERE username='admin01'");
		$stmt->bind_param('s', $hash);
		if ($stmt->execute()) {
			$message = 'อัพเดทรหัสผ่านสำเร็จ';
		} else {
			$message = 'เกิดข้อผิดพลาด: '.$conn->error;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>รีเซ็ตรหัสผ่าน admin01</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="card shadow-sm">
					<div class="card-body">
						<h4 class="mb-3">รีเซ็ตรหัสผ่าน admin01</h4>
						<?php if ($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
						<form method="post">
							<div class="mb-3">
								<label class="form-label">รหัสผ่านใหม่</label>
								<input class="form-control" type="text" name="newpass" placeholder="เช่น 1234" required>
							</div>
							<div class="d-grid"><button class="btn btn-primary">ยืนยัน</button></div>
						</form>
						<hr>
						<a href="../index.php" class="btn btn-outline-secondary">กลับแผงควบคุม</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
