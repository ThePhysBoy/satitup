<?php
// Departments ID Guide - คู่มือ ID ของแต่ละหน่วยงาน
// อัปเดตข้อมูลนี้เมื่อมีการเพิ่ม ลบ หรือแก้ไข departments

$departments_guide = [
    // สายวิชาการ (Academic) - ID 1-8
    'academic' => [
        1 => 'วิทยาศาสตร์และเทคโนโลยี',
        2 => 'สังคมศึกษา',
        3 => 'ภาษาต่างประเทศ',
        4 => 'คณิตศาสตร์',
        5 => 'สุขศึกษาและพลศึกษา',
        6 => 'ภาษาไทย',
        7 => 'ศิลปะ',
        8 => 'การงานอาชีพ'
    ],

    // สายสนับสนุน (Support) - ID 9-14 (เดิมคือ 9-14 แต่ผู้ใช้ลบ 12-14)
    'support' => [
        9 => 'งานบริหารทั่วไป',
        10 => 'งานวิชาการ',
        11 => 'งานกิจการนักเรียน'
        // 12-14 ถูกลบออกไปแล้ว
    ],

    // ประถมศึกษา (Primary) - ID 15-21 (ถูกลบออกไปแล้ว)
    'primary' => [
        // 15-21 ถูกลบออกไปแล้ว
    ]
];

// แสดงข้อมูลสำหรับการตรวจสอบ
echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Departments ID Guide</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body class='p-4'>";

echo "<div class='container'>";
echo "<h1>📋 คู่มือ ID ของ Departments</h1>";
echo "<p class='text-muted'>อัปเดตล่าสุด: " . date('Y-m-d H:i:s') . "</p>";

// แสดงตารางข้อมูล
foreach ($departments_guide as $type => $departments) {
    if (empty($departments)) continue;

    $type_label = [
        'academic' => 'สายวิชาการ',
        'support' => 'สายสนับสนุน',
        'primary' => 'ประถมศึกษา'
    ][$type];

    echo "<h3>$type_label</h3>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>ID</th><th>ชื่อหน่วยงาน</th><th>การใช้งาน</th></tr></thead>";
    echo "<tbody>";

    foreach ($departments as $id => $name) {
        echo "<tr>";
        echo "<td><strong>$id</strong></td>";
        echo "<td>$name</td>";
        echo "<td><code>department_id = $id</code></td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
}

// คำแนะนำการใช้งาน
echo "<div class='alert alert-info mt-4'>";
echo "<h5>💡 คำแนะนำการใช้งาน:</h5>";
echo "<ul>";
echo "<li>ตรวจสอบไฟล์ <code>debug_departments.php</code> เพื่อดูข้อมูลจริงในฐานข้อมูล</li>";
echo "<li>อัปเดตคู่มือนี้เมื่อมีการเพิ่ม ลบ หรือแก้ไข departments</li>";
echo "<li>ใช้ ID ที่ถูกต้องในแต่ละหน้า staff pages</li>";
echo "<li>ตรวจสอบข้อมูลในฐานข้อมูลก่อนปรับปรุงโค้ด</li>";
echo "</ul>";
echo "</div>";

// ลิงก์ไปยังเครื่องมืออื่นๆ
echo "<div class='mt-4'>";
echo "<a href='debug_departments.php' class='btn btn-primary me-2'>ดูข้อมูล Departments จริง</a>";
echo "<a href='admin/department_manager.php' class='btn btn-success me-2'>จัดการ Departments</a>";
echo "<a href='admin/staff/index.php' class='btn btn-warning'>ดูรายการบุคลากร</a>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>

<?php
// แสดงข้อมูลในรูปแบบข้อความสำหรับการอ้างอิง
echo "<!-- 
Departments ID Reference:
สายวิชาการ:
- ID 1: วิทยาศาสตร์และเทคโนโลยี
- ID 2: สังคมศึกษา  
- ID 3: ภาษาต่างประเทศ
- ID 4: คณิตศาสตร์
- ID 5: สุขศึกษาและพลศึกษา
- ID 6: ภาษาไทย
- ID 7: ศิลปะ
- ID 8: การงานอาชีพ

สายสนับสนุน:
- ID 9: งานบริหารทั่วไป
- ID 10: งานวิชาการ
- ID 11: งานกิจการนักเรียน

ประถมศึกษา:
- ถูกลบออกไปแล้ว (เดิม ID 15-21)
-->";
?>
