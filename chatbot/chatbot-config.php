<?php
/**
 * ไฟล์สำหรับตั้งค่า API key และการตั้งค่าอื่นๆ ของแชทบอท
 * โรงเรียนสาธิตมหาวิทยาลัยพะเยา
 */

// ตั้งค่า API key ของ Nvidia NIM
// วิธีที่ 1: กำหนดค่าตรงๆ (ไม่แนะนำสำหรับการใช้งานจริง)
// $nvidia_api_key = "YOUR_NVIDIA_API_KEY";

// วิธีที่ 2: ใช้ Environment Variable (แนะนำ)
$nvidia_api_key = getenv('NVIDIA_API_KEY') ?: '';

// วิธีที่ 3: เก็บในไฟล์แยกที่ไม่ได้อยู่ใน Git repository
$api_key_file = dirname(__FILE__) . '/.api_key';
if (file_exists($api_key_file)) {
    $nvidia_api_key = trim(file_get_contents($api_key_file));
}

// ตั้งค่า Nvidia NIM model
$nvidia_model = "meta/llama-4-maverick-17b-128e-instruct"; // โมเดลที่ใช้

// ตั้งค่าอื่นๆ ของแชทบอท
$chatbot_settings = [
    'bot_name' => 'แชทบอทโรงเรียนสาธิต',
    'welcome_message' => 'สวัสดีครับ ผมเป็นแชทบอทอัจฉริยะของโรงเรียนสาธิตมหาวิทยาลัยพะเยา มีอะไรให้ช่วยไหมครับ?',
    'placeholder_text' => 'พิมพ์ข้อความที่นี่...',
    'auto_open' => false, // ตั้งค่าเป็น true ถ้าต้องการให้แชทบอทเปิดอัตโนมัติเมื่อโหลดหน้าเว็บ
    'theme_color' => '#7b3b95', // สีหลักของแชทบอท (สีม่วงของโรงเรียนสาธิต)
    'use_backend_api' => true, // ตั้งค่าเป็น true เพื่อใช้ API ผ่าน backend แทนที่จะเรียกตรงจาก frontend
    'api_endpoint' => 'chatbot/api/chat.php', // endpoint สำหรับเรียกใช้ API ผ่าน backend
];

/**
 * ฟังก์ชันสำหรับแสดงแชทบอทในหน้าเว็บ
 * เรียกใช้ฟังก์ชันนี้ในหน้าเว็บที่ต้องการให้แสดงแชทบอท
 */
function display_chatbot() {
    global $nvidia_api_key, $nvidia_model, $chatbot_settings;
    
    // ตรวจสอบว่ามีการตั้งค่า API key หรือไม่
    if (empty($nvidia_api_key)) {
        error_log("Warning: Nvidia API key is not set. Chatbot will not function properly.");
    }
    
    // สร้าง HTML สำหรับตั้งค่าแชทบอท
    $html = '<script>
        window.CHATBOT_CONFIG = {
            model: "' . htmlspecialchars($nvidia_model) . '",
            useBackendApi: ' . ($chatbot_settings['use_backend_api'] ? 'true' : 'false') . ',
            apiEndpoint: "' . htmlspecialchars($chatbot_settings['api_endpoint']) . '",
            botName: "' . htmlspecialchars($chatbot_settings['bot_name']) . '",
            welcomeMessage: "' . htmlspecialchars($chatbot_settings['welcome_message']) . '",
            placeholderText: "' . htmlspecialchars($chatbot_settings['placeholder_text']) . '",
            themeColor: "' . htmlspecialchars($chatbot_settings['theme_color']) . '",
            autoOpen: ' . ($chatbot_settings['auto_open'] ? 'true' : 'false') . '
        };
    </script>';
    
    // ถ้าไม่ใช้ backend API ให้ส่ง API key ไปด้วย (ไม่แนะนำสำหรับการใช้งานจริง)
    if (!$chatbot_settings['use_backend_api']) {
        $html .= '<input type="hidden" id="nvidia-api-key" value="' . htmlspecialchars($nvidia_api_key) . '">';
    }
    
    // เพิ่ม CSS และ JavaScript ของแชทบอท
    $html .= '<link rel="stylesheet" href="chatbot/css/chatbot.css">';
    $html .= '<script src="chatbot/js/chatbot.js"></script>';
    
    // ส่งคืน HTML
    echo $html;
}
?>
