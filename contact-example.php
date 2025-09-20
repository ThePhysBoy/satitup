<?php
/**
 * ตัวอย่างการแก้ไขปัญหาตำแหน่งของ QR code และปุ่ม CONTACT US ที่ซ้อนทับกัน
 * นำโค้ดนี้ไปประยุกต์ใช้กับหน้าที่มีปัญหา
 */

// นำเข้าไฟล์ส่วนหัวของเว็บไซต์
include_once 'header.php';
?>

<div class="container py-5">
    <h2 class="text-center mb-4">ติดต่อเรา</h2>
    
    <!-- ตัวอย่างการใช้คลาสที่กำหนดไว้ใน contact-fix.css -->
    <div class="contact-qrcode-container">
        <!-- ปุ่ม CONTACT US -->
        <div class="contact-button">
            <a href="#contact-form" class="btn btn-danger btn-lg">CONTACT US</a>
        </div>
        
        <!-- QR Codes -->
        <div class="qrcode-wrapper">
            <!-- QR Code ตัวที่ 1 (LINE) -->
            <div class="qrcode-item">
                <img src="images/qr-line.png" alt="LINE QR Code">
                <div class="qrcode-label">LINE Official</div>
            </div>
            
            <!-- QR Code ตัวที่ 2 (Facebook) -->
            <div class="qrcode-item">
                <img src="images/qr-facebook.png" alt="Facebook QR Code">
                <div class="qrcode-label">Facebook Page</div>
            </div>
        </div>
    </div>
    
    <!-- ส่วนอื่นๆ ของหน้าติดต่อเรา -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h3>ข้อมูลติดต่อ</h3>
            <p><i class="fas fa-map-marker-alt me-2"></i> 19 หมู่ 2 ตำบลแม่กา อำเภอเมืองพะเยา จังหวัดพะเยา 56000</p>
            <p><i class="fas fa-phone me-2"></i> 054-466666 ต่อ 1234</p>
            <p><i class="fas fa-envelope me-2"></i> satit@up.ac.th</p>
        </div>
        <div class="col-md-6">
            <h3 id="contact-form">แบบฟอร์มติดต่อ</h3>
            <form>
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">หัวข้อ</label>
                    <input type="text" class="form-control" id="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">ข้อความ</label>
                    <textarea class="form-control" id="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ส่งข้อความ</button>
            </form>
        </div>
    </div>
</div>

<?php
// นำเข้าส่วนท้ายของเว็บไซต์
include_once 'footer.php';
?>
