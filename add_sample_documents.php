<?php
// เพิ่มข้อมูลตัวอย่างเอกสารราชการ
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'satitup';
$db_port = 3306;

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// เพิ่มข้อมูลตัวอย่าง
$sample_docs = [
    // ข้อบังคับ
    [
        'doc_type' => 'regulation',
        'doc_number' => '1/2567',
        'category_id' => 1,
        'title' => 'ข้อบังคับว่าด้วยการแต่งกายนักเรียน',
        'description' => 'กำหนดระเบียบการแต่งกายของนักเรียนทุกระดับชั้น',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-01-15',
        'effective_date' => '2024-02-01',
        'status' => 'active'
    ],
    // ระเบียบ
    [
        'doc_type' => 'rule',
        'doc_number' => '2/2567',
        'category_id' => 5,
        'title' => 'ระเบียบการลาหยุดเรียน',
        'description' => 'กำหนดหลักเกณฑ์และวิธีการลาหยุดเรียนของนักเรียน',
        'publisher_name' => 'นายสมชาย ใจดี',
        'publisher_position' => 'รองผู้อำนวยการฝ่ายวิชาการ',
        'publish_date' => '2024-02-10',
        'effective_date' => '2024-02-15',
        'status' => 'active'
    ],
    // ประกาศ  
    [
        'doc_type' => 'announcement',
        'doc_number' => '3/2567',
        'category_id' => 8,
        'title' => 'ประกาศรับสมัครนักเรียนใหม่ ปีการศึกษา 2568',
        'description' => 'เปิดรับสมัครนักเรียนชั้นมัธยมศึกษาปีที่ 1 และ 4',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-03-01',
        'effective_date' => null,
        'status' => 'active'
    ],
    [
        'doc_type' => 'announcement',
        'doc_number' => '4/2567',
        'category_id' => 10,
        'title' => 'ประกาศกิจกรรมวันเด็กแห่งชาติ 2568',
        'description' => 'กำหนดการจัดกิจกรรมวันเด็กแห่งชาติ',
        'publisher_name' => 'นายวิชัย สุขใจ',
        'publisher_position' => 'รองผู้อำนวยการฝ่ายกิจการนักเรียน',
        'publish_date' => '2024-12-20',
        'effective_date' => null,
        'status' => 'active'
    ],
    // คำสั่ง
    [
        'doc_type' => 'order',
        'doc_number' => '5/2567',
        'category_id' => 12,
        'title' => 'คำสั่งแต่งตั้งคณะกรรมการจัดงานกีฬาสี',
        'description' => 'แต่งตั้งคณะกรรมการและมอบหมายหน้าที่',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-11-01',
        'effective_date' => '2024-11-01',
        'status' => 'active'
    ],
    [
        'doc_type' => 'order',
        'doc_number' => '6/2567',
        'category_id' => 13,
        'title' => 'คำสั่งมอบหมายหน้าที่ครูเวรประจำวัน',
        'description' => 'มอบหมายหน้าที่ครูเวรดูแลนักเรียน',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-05-15',
        'effective_date' => '2024-06-01',
        'status' => 'active'
    ]
];

// เพิ่มข้อมูลลงฐานข้อมูล
$success = 0;
$failed = 0;

foreach ($sample_docs as $doc) {
    $sql = "INSERT INTO official_documents 
            (doc_type, doc_number, category_id, title, description, 
             publisher_name, publisher_position, publish_date, effective_date, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssssss", 
        $doc['doc_type'], $doc['doc_number'], $doc['category_id'], 
        $doc['title'], $doc['description'], $doc['publisher_name'],
        $doc['publisher_position'], $doc['publish_date'], 
        $doc['effective_date'], $doc['status']
    );
    
    if ($stmt->execute()) {
        $success++;
        echo "✓ เพิ่มสำเร็จ: " . $doc['title'] . "<br>";
    } else {
        $failed++;
        echo "✗ ล้มเหลว: " . $doc['title'] . " - " . $stmt->error . "<br>";
    }
    
    $stmt->close();
}

echo "<hr>";
echo "<h3>สรุป</h3>";
echo "สำเร็จ: $success เอกสาร<br>";
echo "ล้มเหลว: $failed เอกสาร<br>";

$conn->close();

echo "<br>";
echo "<a href='admin/official_documents/index.php'>ไปที่หน้าจัดการเอกสาร</a><br>";
echo "<a href='news_announcements.php'>ไปที่หน้าแสดงประกาศ</a>";
?>
