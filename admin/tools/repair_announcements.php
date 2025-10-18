<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

$conn = new mysqli('localhost', 'root', '', 'satitup');
$conn->set_charset('utf8mb4');
$conn->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['repair_single'])) {
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $content = $_POST['content'];
        $department = $_POST['department'];
        
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ?, department = ? WHERE id = ?");
        $stmt->bind_param('sssi', $title, $content, $department, $id);
        
        if ($stmt->execute()) {
            $message = "อัปเดตข้อมูล ID $id สำเร็จ";
        }
    }
    
    if (isset($_POST['delete_broken'])) {
        // ลบข้อมูลที่มีแต่ ?????
        $conn->query("DELETE FROM announcements WHERE title LIKE '%????%' AND (content IS NULL OR content = '' OR content LIKE '%????%')");
        $affected = $conn->affected_rows;
        $message = "ลบข้อมูลที่เสียหาย $affected รายการ";
    }

    // Add a small UTF-8 test announcement without PDF
    if (isset($_POST['add_test'])) {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        if ($title !== '') {
            $stmt = $conn->prepare("INSERT INTO announcements (title, content, category, status) VALUES (?, ?, 'announcement', 'open')");
            $stmt->bind_param('ss', $title, $content);
            if ($stmt->execute()) {
                $message = "เพิ่มประกาศทดสอบสำเร็จ (ID " . $stmt->insert_id . ")";
            }
        }
    }
}

// Get all announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ซ่อมแซมข้อมูลประกาศ</title>
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .broken {
            background: #ffe0e0;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            background: #d4edda;
            color: #155724;
        }
        .form-group {
            margin: 10px 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ซ่อมแซมข้อมูลประกาศ</h1>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="post" style="margin: 20px 0;">
            <button type="submit" name="delete_broken" class="btn btn-danger" 
                    onclick="return confirm('ลบข้อมูลที่เป็น ????? ทั้งหมด?')">
                ลบข้อมูลที่เสียหาย
            </button>
        </form>
        
        <h2>รายการประกาศทั้งหมด</h2>
        <table>
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        $isBroken = strpos($row['title'], '?') !== false;
                    ?>
                    <tr class="<?php echo $isBroken ? 'broken' : ''; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars(substr($row['content'] ?? '', 0, 100)); ?>...</td>
                        <td><?php echo htmlspecialchars($row['department'] ?? ''); ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <button class="btn btn-primary" onclick="editRow(<?php echo $row['id']; ?>)">แก้ไข</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- Edit Form Modal -->
        <div id="editModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000;">
            <div style="background:white; margin:50px auto; max-width:600px; padding:30px; border-radius:10px;">
                <h2>แก้ไขประกาศ</h2>
                <form method="post">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label>หัวข้อ:</label>
                        <input type="text" name="title" id="edit_title" required>
                    </div>
                    <div class="form-group">
                        <label>เนื้อหา:</label>
                        <textarea name="content" id="edit_content" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label>ฝ่าย/หน่วยงาน:</label>
                        <input type="text" name="department" id="edit_department">
                    </div>
                    <button type="submit" name="repair_single" class="btn btn-success">บันทึก</button>
                    <button type="button" onclick="closeModal()" class="btn">ยกเลิก</button>
                </form>
            </div>
        </div>
        
        <hr style="margin: 40px 0;">
        
        <h2>เพิ่มประกาศใหม่ (ทดสอบ UTF-8)</h2>
        <form method="post" action="">
            <div class="form-group">
                <label>หัวข้อ:</label>
                <input type="text" name="title" placeholder="ทดสอบภาษาไทย" required>
            </div>
            <div class="form-group">
                <label>เนื้อหา:</label>
                <textarea name="content" rows="3" placeholder="รายละเอียดภาษาไทย"></textarea>
            </div>
            <button type="submit" name="add_test" class="btn btn-primary">เพิ่มประกาศทดสอบ</button>
        </form>
    </div>
    
    <script>
        const data = <?php 
            $result->data_seek(0);
            $allData = [];
            while ($row = $result->fetch_assoc()) {
                $allData[$row['id']] = $row;
            }
            echo json_encode($allData, JSON_UNESCAPED_UNICODE);
        ?>;
        
        function editRow(id) {
            const row = data[id];
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_title').value = row.title || '';
            document.getElementById('edit_content').value = row.content || '';
            document.getElementById('edit_department').value = row.department || '';
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</body>
</html>