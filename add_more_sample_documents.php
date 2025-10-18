<?php
// เพิ่มข้อมูลเอกสารเพิ่มเติมเพื่อให้มีครบ 5 รายการต่อหมวด

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

// เพิ่มข้อมูลเพิ่มเติม
$additional_docs = [
    // ข้อบังคับเพิ่ม (4 รายการ)
    [
        'doc_type' => 'regulation',
        'doc_number' => '7/2567',
        'category_id' => 2,
        'title' => 'ข้อบังคับว่าด้วยการเข้าเรียนและการขาดเรียน',
        'description' => 'กำหนดเวลาเข้าเรียนและหลักเกณฑ์การขาดเรียน',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-01-20',
        'effective_date' => '2024-02-01',
        'status' => 'active'
    ],
    [
        'doc_type' => 'regulation',
        'doc_number' => '8/2567',
        'category_id' => 3,
        'title' => 'ข้อบังคับว่าด้วยการประเมินผลการเรียน',
        'description' => 'หลักเกณฑ์การวัดและประเมินผลการเรียน',
        'publisher_name' => 'นายสมชาย ใจดี',
        'publisher_position' => 'รองผู้อำนวยการฝ่ายวิชาการ',
        'publish_date' => '2024-02-05',
        'effective_date' => '2024-03-01',
        'status' => 'active'
    ],
    [
        'doc_type' => 'regulation',
        'doc_number' => '9/2567',
        'category_id' => 1,
        'title' => 'ข้อบังคับว่าด้วยความประพฤตินักเรียน',
        'description' => 'กฎระเบียบความประพฤติและบทลงโทษ',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-03-10',
        'effective_date' => '2024-04-01',
        'status' => 'active'
    ],
    [
        'doc_type' => 'regulation',
        'doc_number' => '10/2567',
        'category_id' => 2,
        'title' => 'ข้อบังคับว่าด้วยการใช้ห้องปฏิบัติการ',
        'description' => 'ระเบียบการใช้ห้องปฏิบัติการต่างๆ',
        'publisher_name' => 'นายวิชัย สุขใจ',
        'publisher_position' => 'หัวหน้าฝ่ายอาคารสถานที่',
        'publish_date' => '2024-04-15',
        'effective_date' => '2024-05-01',
        'status' => 'active'
    ],
    // ระเบียบเพิ่ม (4 รายการ)
    [
        'doc_type' => 'rule',
        'doc_number' => '11/2567',
        'category_id' => 4,
        'title' => 'ระเบียบการใช้สนามกีฬา',
        'description' => 'กำหนดเวลาและวิธีการใช้สนามกีฬา',
        'publisher_name' => 'นายสุรชัย แข็งแรง',
        'publisher_position' => 'หัวหน้าฝ่ายพลศึกษา',
        'publish_date' => '2024-01-25',
        'effective_date' => '2024-02-01',
        'status' => 'active'
    ],
    [
        'doc_type' => 'rule',
        'doc_number' => '12/2567',
        'category_id' => 4,
        'title' => 'ระเบียบการเข้าใช้ห้องสมุด',
        'description' => 'ข้อปฏิบัติในการใช้ห้องสมุด',
        'publisher_name' => 'นางสาวมาลี รักการอ่าน',
        'publisher_position' => 'บรรณารักษ์',
        'publish_date' => '2024-02-20',
        'effective_date' => '2024-03-01',
        'status' => 'active'
    ],
    [
        'doc_type' => 'rule',
        'doc_number' => '13/2567',
        'category_id' => 6,
        'title' => 'ระเบียบการลางานของบุคลากร',
        'description' => 'หลักเกณฑ์การลาประเภทต่างๆ',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-03-15',
        'effective_date' => '2024-04-01',
        'status' => 'active'
    ],
    [
        'doc_type' => 'rule',
        'doc_number' => '14/2567',
        'category_id' => 5,
        'title' => 'ระเบียบการจอดรถในโรงเรียน',
        'description' => 'กำหนดพื้นที่และเวลาจอดรถ',
        'publisher_name' => 'นายวิชัย สุขใจ',
        'publisher_position' => 'รองผู้อำนวยการฝ่ายบริหารทั่วไป',
        'publish_date' => '2024-04-20',
        'effective_date' => '2024-05-01',
        'status' => 'active'
    ],
    // ประกาศเพิ่ม (3 รายการ)
    [
        'doc_type' => 'announcement',
        'doc_number' => '15/2567',
        'category_id' => 9,
        'title' => 'ประกาศผลสอบกลางภาคเรียนที่ 2/2567',
        'description' => 'แจ้งผลการสอบกลางภาค',
        'publisher_name' => 'นายสมชาย ใจดี',
        'publisher_position' => 'รองผู้อำนวยการฝ่ายวิชาการ',
        'publish_date' => '2024-10-15',
        'effective_date' => null,
        'status' => 'active'
    ],
    [
        'doc_type' => 'announcement',
        'doc_number' => '16/2567',
        'category_id' => 7,
        'title' => 'ประกาศปิดภาคเรียนฤดูร้อน',
        'description' => 'กำหนดวันปิด-เปิดภาคเรียน',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-02-28',
        'effective_date' => null,
        'status' => 'active'
    ],
    [
        'doc_type' => 'announcement',
        'doc_number' => '17/2567',
        'category_id' => 10,
        'title' => 'ประกาศการแข่งขันกีฬาสีประจำปี',
        'description' => 'กำหนดการแข่งขันกีฬาสี',
        'publisher_name' => 'นายสุรชัย แข็งแรง',
        'publisher_position' => 'หัวหน้าฝ่ายพลศึกษา',
        'publish_date' => '2024-11-10',
        'effective_date' => null,
        'status' => 'active'
    ],
    // คำสั่งเพิ่ม (3 รายการ)
    [
        'doc_type' => 'order',
        'doc_number' => '18/2567',
        'category_id' => 12,
        'title' => 'คำสั่งแต่งตั้งหัวหน้าระดับชั้น',
        'description' => 'แต่งตั้งหัวหน้าระดับชั้นประจำปีการศึกษา',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-05-01',
        'effective_date' => '2024-05-15',
        'status' => 'active'
    ],
    [
        'doc_type' => 'order',
        'doc_number' => '19/2567',
        'category_id' => 13,
        'title' => 'คำสั่งมอบหมายงานประจำภาคเรียน',
        'description' => 'มอบหมายภาระงานครูผู้สอน',
        'publisher_name' => 'นายสมชาย ใจดี',
        'publisher_position' => 'รองผู้อำนวยการฝ่ายวิชาการ',
        'publish_date' => '2024-05-10',
        'effective_date' => '2024-05-15',
        'status' => 'active'
    ],
    [
        'doc_type' => 'order',
        'doc_number' => '20/2567',
        'category_id' => 12,
        'title' => 'คำสั่งแต่งตั้งคณะกรรมการสอบ',
        'description' => 'แต่งตั้งกรรมการคุมสอบ',
        'publisher_name' => 'นางสาวพรรณี ศรีสุข',
        'publisher_position' => 'ผู้อำนวยการโรงเรียน',
        'publish_date' => '2024-12-01',
        'effective_date' => '2024-12-15',
        'status' => 'active'
    ]
];

// เพิ่มข้อมูลลงฐานข้อมูล
$success = 0;
$failed = 0;

foreach ($additional_docs as $doc) {
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

// แสดงสรุปจำนวนเอกสารแต่ละประเภท
$types = ['regulation', 'rule', 'announcement', 'order'];
echo "<h3>จำนวนเอกสารแต่ละประเภท:</h3>";
foreach ($types as $type) {
    $result = $conn->query("SELECT COUNT(*) as count FROM official_documents WHERE doc_type = '$type' AND status = 'active'");
    $row = $result->fetch_assoc();
    $type_th = [
        'regulation' => 'ข้อบังคับ',
        'rule' => 'ระเบียบ',
        'announcement' => 'ประกาศ',
        'order' => 'คำสั่ง'
    ];
    echo $type_th[$type] . ": " . $row['count'] . " เอกสาร<br>";
}

$conn->close();

echo "<br>";
echo "<a href='test_documents_display.php'>ดูหน้าทดสอบ</a><br>";
echo "<a href='news_announcements.php'>ไปที่หน้าแสดงประกาศ</a>";
?>
