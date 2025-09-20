<?php
/**
 * API Endpoint สำหรับเรียกใช้ Nvidia NIM API
 * ไฟล์นี้ทำหน้าที่เป็นตัวกลางระหว่าง Frontend กับ Nvidia NIM API
 * เพื่อป้องกันไม่ให้ API Key ถูกเปิดเผยในฝั่ง Client
 */

// อนุญาตให้เรียกใช้จากเว็บไซต์ของเราเท่านั้น
header('Content-Type: application/json');

// ตรวจสอบว่าเป็นการเรียกด้วยวิธี POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ตรวจสอบว่ามีการส่งข้อมูลมาหรือไม่
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['message'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// นำเข้าไฟล์ตั้งค่า
require_once dirname(__FILE__) . '/../chatbot-config.php';

// Debug: บันทึกข้อมูลที่ได้รับ
error_log("Received message: " . $input['message']);
error_log("API Key length: " . strlen($nvidia_api_key));

// ตรวจสอบว่ามี API key หรือไม่
if (empty($nvidia_api_key)) {
    error_log("Error: Nvidia API key is empty");
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'API key is not set']);
    exit;
}

// กำหนดให้ใช้ streaming หรือไม่
$use_streaming = false; // เปลี่ยนเป็น false ชั่วคราวเพื่อ debug

// สร้าง URL สำหรับเรียกใช้ API
$api_url = "https://integrate.api.nvidia.com/v1/chat/completions";

// เตรียมข้อมูลสำหรับส่งไปยัง API
$message = $input['message'];
$history = isset($input['history']) ? $input['history'] : [];

// แปลงรูปแบบประวัติการสนทนาให้เข้ากับ Nvidia NIM API
$messages = [];

// เพิ่ม system prompt
$messages[] = [
    'role' => 'system',
    'content' => 'You are a helpful AI assistant for Satit School, University of Phayao. Please respond in Thai language. Provide information about the school, curriculum, admissions, and activities. Keep responses friendly and helpful.'
];

// เพิ่มประวัติการสนทนา
foreach ($history as $entry) {
    if (isset($entry['role']) && isset($entry['parts'][0]['text'])) {
        $role = $entry['role'] === 'model' ? 'assistant' : $entry['role']; // แปลง 'model' เป็น 'assistant'
        $messages[] = [
            'role' => $role,
            'content' => $entry['parts'][0]['text']
        ];
    }
}

// เพิ่มข้อความปัจจุบัน
$messages[] = [
    'role' => 'user',
    'content' => $message
];

// สร้างข้อมูลสำหรับส่งไปยัง API ตามรูปแบบของ Python
$data = [
    'model' => 'meta/llama-4-maverick-17b-128e-instruct',
    'messages' => $messages,
    'max_tokens' => 200,
    'temperature' => 0.7,
    'top_p' => 0.9,
    'stream' => $use_streaming
];

// Debug: บันทึกข้อมูลที่จะส่งไปยัง API
error_log("Sending to API: " . json_encode($data));
error_log("API URL: " . $api_url);

// ตั้งค่า cURL
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 60); // เพิ่ม timeout เป็น 60 วินาที
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // timeout สำหรับการเชื่อมต่อ
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ปิด SSL verification (สำหรับ local testing)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // ปิด SSL host verification
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // ติดตาม redirect
curl_setopt($ch, CURLOPT_USERAGENT, 'SatitChatbot/1.0'); // กำหนด User Agent

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $nvidia_api_key
];

if ($use_streaming) {
    // ถ้าใช้ streaming ให้ตั้งค่า header Accept เป็น text/event-stream
    $headers[] = 'Accept: text/event-stream';
    
    // ไม่ใช้ RETURNTRANSFER เพื่อให้ส่งข้อมูลกลับทันที
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
        echo $data;
        flush();
        ob_flush();
        return strlen($data);
    });
    
    // ตั้งค่า header สำหรับ SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no'); // สำหรับ Nginx
} else {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers[] = 'Accept: application/json';
}

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// ถ้าไม่ใช้ streaming
if (!$use_streaming) {
    // ส่งคำขอไปยัง API
    $response = curl_exec($ch);

    // ตรวจสอบข้อผิดพลาดของ cURL
    if ($response === false) {
        $curl_error = curl_error($ch);
        error_log("cURL Error: $curl_error");
        http_response_code(500);
        echo json_encode(['error' => 'cURL error', 'details' => $curl_error]);
        curl_close($ch);
        exit;
    }

    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ตรวจสอบว่ามีข้อผิดพลาดหรือไม่
    if ($status_code !== 200) {
        // บันทึกข้อผิดพลาดลงในไฟล์ log
        error_log("Nvidia API Error: Status Code: $status_code, Response: $response");
        error_log("Request URL: $api_url");
        error_log("Request Data: " . json_encode($data));
        
        http_response_code($status_code);
        echo json_encode(['error' => 'API request failed', 'details' => $response]);
        exit;
    }

    // Debug: บันทึก response ที่ได้รับ
    error_log("Nvidia API Success Response: " . $response);
    
    // ตรวจสอบและแปลง response
    $response_data = json_decode($response, true);
    if ($response_data && isset($response_data['choices'][0]['message']['content'])) {
        $content = $response_data['choices'][0]['message']['content'];
        error_log("Extracted content: " . $content);
        
        // ถ้าเป็นข้อความว่าง ให้ใส่ข้อความเริ่มต้น
        if (trim($content) === '') {
            error_log("Content is empty, using default message");
            $response_data['choices'][0]['message']['content'] = 'สวัสดีครับ! มีอะไรให้ช่วยไหมครับ?';
            $response = json_encode($response_data, JSON_UNESCAPED_UNICODE);
        }
    }

    // ส่งคำตอบกลับไปยัง Client
    echo $response;
} else {
    // สำหรับ streaming จะส่งข้อมูลกลับทันทีผ่าน WRITEFUNCTION
    // เราเพียงแค่ execute curl และจัดการข้อผิดพลาด
    error_log("Starting streaming request to Nvidia API");
    
    $success = curl_exec($ch);
    
    if ($success === false) {
        $curl_error = curl_error($ch);
        error_log("cURL Error in streaming mode: $curl_error");
        echo "event: error\n";
        echo "data: " . json_encode(['error' => 'cURL error', 'details' => $curl_error]) . "\n\n";
        flush();
    }
    
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    error_log("Nvidia API Response Status Code: $status_code");
    curl_close($ch);
    
    if ($status_code !== 200) {
        error_log("Nvidia API Error in streaming mode: Status Code: $status_code");
        echo "event: error\n";
        echo "data: " . json_encode(['error' => 'API request failed', 'status' => $status_code]) . "\n\n";
        flush();
    } else {
        error_log("Streaming request completed successfully");
    }
}
?>
