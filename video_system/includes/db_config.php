<?php
// ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการกำหนดค่าแล้วหรือยัง
if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'localhost');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}
if (!defined('DB_NAME')) {
    //define('DB_NAME', 'school_satitup');
    define('DB_NAME', 'satitup');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', 3306);
}

// เชื่อมต่อกับฐานข้อมูล MySQL (ตรวจสอบว่ายังไม่มีการเชื่อมต่อ)
if (!isset($video_conn)) {
    $video_conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
    
    // ตรวจสอบการเชื่อมต่อ
    if ($video_conn === false) {
        // ไม่ die() เพื่อให้เว็บไซต์ยังทำงานต่อได้
        error_log("ERROR: ไม่สามารถเชื่อมต่อฐานข้อมูลวิดีโอได้. " . mysqli_connect_error());
        $video_conn = null;
    } else {
        // ตั้งค่า charset เป็น utf8
        mysqli_set_charset($video_conn, "utf8");
    }
}
