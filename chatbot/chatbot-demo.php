<?php
/**
 * ไฟล์สำหรับทดสอบแชทบอทโรงเรียนสาธิตมหาวิทยาลัยพะเยา
 */

// นำเข้าไฟล์ตั้งค่าแชทบอท
require_once 'chatbot-config.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบแชทบอทโรงเรียนสาธิตมหาวิทยาลัยพะเยา</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #7b3b95;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .instructions {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .instructions h2 {
            color: #7b3b95;
            margin-top: 0;
        }
        
        .instructions ol {
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 10px;
        }
        
        .api-key-form {
            background-color: #f8f5ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #7b3b95;
            margin-bottom: 30px;
        }
        
        .api-key-form h2 {
            color: #7b3b95;
            margin-top: 0;
        }
        
        .api-key-form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .api-key-form button {
            background-color: #7b3b95;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .api-key-form button:hover {
            background-color: #9b59b6;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ทดสอบแชทบอทโรงเรียนสาธิตมหาวิทยาลัยพะเยา</h1>
        
        <div class="instructions">
            <h2>วิธีใช้งานแชทบอท</h2>
            <ol>
                <li>คลิกที่ไอคอนแชทบอทที่มุมขวาล่างของหน้าเว็บเพื่อเปิดหน้าต่างแชท</li>
                <li>พิมพ์คำถามหรือข้อความที่ต้องการถามแชทบอท</li>
                <li>กดปุ่มส่งหรือกด Enter เพื่อส่งข้อความ</li>
                <li>รอรับคำตอบจากแชทบอท</li>
            </ol>
            <p>แชทบอทนี้ใช้ Gemini API ในการตอบคำถาม จึงต้องมีการตั้งค่า API key ก่อนใช้งาน</p>
        </div>
        
        <div class="api-key-form">
            <h2>ตั้งค่า Gemini API Key</h2>
            <p>คุณสามารถรับ API key ได้จาก <a href="https://ai.google.dev/" target="_blank">Google AI Studio</a></p>
            <form id="apiKeyForm">
                <input type="text" id="apiKeyInput" placeholder="ใส่ Gemini API key ของคุณที่นี่" required>
                <button type="submit">บันทึก API Key</button>
            </form>
        </div>
        
        <div class="footer">
            <p>แชทบอทโรงเรียนสาธิตมหาวิทยาลัยพะเยา &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
    
    <?php display_chatbot(); ?>
    
    <script>
        // JavaScript สำหรับจัดการฟอร์ม API key
        document.getElementById('apiKeyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const apiKey = document.getElementById('apiKeyInput').value.trim();
            if (apiKey) {
                // อัปเดต API key ในหน้าเว็บ
                document.getElementById('gemini-api-key').value = apiKey;
                
                // รีสตาร์ทแชทบอท (ถ้ามีอินสแตนซ์อยู่แล้ว)
                if (window.satitChatbot) {
                    // ลบอินสแตนซ์เดิม
                    document.querySelector('.chatbot-container')?.remove();
                    document.querySelector('.chatbot-toggle')?.remove();
                    
                    // สร้างอินสแตนซ์ใหม่
                    window.satitChatbot = new SatitChatbot(apiKey);
                }
                
                alert('บันทึก API key เรียบร้อยแล้ว คุณสามารถใช้งานแชทบอทได้ทันที');
            }
        });
    </script>
</body>
</html>
