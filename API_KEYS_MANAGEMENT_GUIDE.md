# 🔑 คู่มือระบบจัดการ API Keys

## 📋 ภาพรวมระบบ
ระบบจัดการ API Keys ช่วยให้ Admin สามารถจัดการ API Keys ต่างๆ ของเว็บไซต์ได้จากที่เดียว โดยไม่ต้องแก้ไขโค้ดโดยตรง

## ✅ สิ่งที่ติดตั้งแล้ว

### 1. ฐานข้อมูล
- ตาราง `api_keys` สำหรับเก็บ API Keys ทั้งหมด
- Google Maps API Key ถูกเพิ่มเป็นค่าเริ่มต้น: `AIzaSyAvHiS_X2q82YL5pdInenuswpeJN7RpuiQ`

### 2. หน้าจัดการ API Keys
- **ที่อยู่**: `/admin/api_keys/index.php`
- **การเข้าถึง**: เฉพาะ Admin เท่านั้น

### 3. การเชื่อมต่อกับระบบแผนที่
- `partners_section.php` ดึง API Key อัตโนมัติจากฐานข้อมูล
- แสดงแผนที่พันธมิตรบนหน้า index.php

## 🚀 การใช้งาน

### เข้าสู่หน้าจัดการ API Keys
1. เข้าสู่ระบบ Admin
2. ไปที่ Dashboard
3. คลิกปุ่ม "จัดการ API Keys" (สีแดง)

หรือเข้าโดยตรงที่: `/admin/api_keys/`

### ฟีเจอร์หลัก
1. **ดู/แก้ไข API Keys** - แก้ไข API Key ที่มีอยู่
2. **เพิ่ม API Key ใหม่** - เพิ่ม API สำหรับบริการอื่นๆ
3. **เปิด/ปิดการใช้งาน** - ควบคุมการใช้งาน API
4. **คัดลอก API Key** - คลิกปุ่ม Copy เพื่อคัดลอก
5. **กำหนดวันหมดอายุ** - ตั้งวันหมดอายุ API (ถ้ามี)

## 📊 API Keys ที่มีในระบบ

| ชื่อ API | ผู้ให้บริการ | สถานะ | การใช้งาน |
|----------|-------------|---------|-----------|
| google_maps | Google Cloud Platform | ✅ ใช้งาน | แสดงแผนที่พันธมิตร |
| google_analytics | Google | ⏸️ ไม่ใช้งาน | ติดตามสถิติเว็บ |
| facebook_app | Facebook | ⏸️ ไม่ใช้งาน | เชื่อมต่อ Social |
| line_notify | Line | ⏸️ ไม่ใช้งาน | ส่งการแจ้งเตือน |
| youtube_data | Google | ⏸️ ไม่ใช้งาน | ดึงข้อมูลวิดีโอ |

## 🔧 การเพิ่ม API Key ใหม่

### ขั้นตอน:
1. คลิกปุ่ม "เพิ่ม API Key ใหม่"
2. กรอกข้อมูล:
   - **ชื่อ API**: ภาษาอังกฤษ ตัวเล็ก ใช้ _ คั่น (เช่น facebook_pixel)
   - **API Key**: ค่า API Key ที่ได้จากผู้ให้บริการ
   - **ผู้ให้บริการ**: ชื่อบริษัท/บริการ
   - **คำอธิบาย**: อธิบายการใช้งาน
   - **วันหมดอายุ**: (ถ้ามี)
3. เลือก "เปิดใช้งานทันที" ถ้าต้องการใช้ทันที
4. คลิก "บันทึก"

## 🔐 ความปลอดภัย

### แนวปฏิบัติที่ดี:
1. **จำกัดการเข้าถึง** - เฉพาะ Admin เท่านั้น
2. **ไม่แชร์ API Key** - อย่าส่งต่อหรือเปิดเผย
3. **ตั้งวันหมดอายุ** - สำหรับ API ชั่วคราว
4. **ตรวจสอบการใช้งาน** - ดู usage count เป็นระยะ
5. **Rotate Keys** - เปลี่ยน API Key เป็นระยะ

## 💡 การใช้ API Key ในโค้ด

### ตัวอย่างการดึง API Key จาก PHP:
```php
// ดึง API Key จากฐานข้อมูล
$api_query = "SELECT api_key FROM api_keys 
              WHERE api_name = 'google_maps' 
              AND is_active = 1 
              LIMIT 1";
$result = $conn->query($api_query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $api_key = $row['api_key'];
}
```

### ตัวอย่างการใช้ใน JavaScript:
```javascript
const apiKey = '<?php echo $api_key; ?>';
const script = document.createElement('script');
script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}`;
```

## 🔄 การอัพเดท Google Maps API Key

### ถ้าต้องการเปลี่ยน API Key:
1. เข้าหน้า Admin > จัดการ API Keys
2. หา "google_maps"
3. แก้ไขช่อง API Key
4. คลิก "บันทึก"
5. แผนที่จะใช้ Key ใหม่ทันที

## ⚠️ การแก้ปัญหา

### แผนที่ไม่แสดง:
1. ตรวจสอบว่า API Key ถูกต้อง
2. ตรวจสอบว่า API "เปิดใช้งาน" อยู่
3. ดู Console Log สำหรับ error
4. ตรวจสอบว่า API ไม่หมดอายุ

### Error: API Key ไม่ทำงาน:
- ตรวจสอบการจำกัดการใช้งานใน Google Cloud Console
- ตรวจสอบว่าเปิดใช้ APIs ที่จำเป็น:
  - Maps JavaScript API
  - Places API (สำหรับค้นหาสถานที่)
  - Geocoding API (สำหรับแปลงที่อยู่)

## 📞 ติดต่อสอบถาม
หากพบปัญหาหรือต้องการความช่วยเหลือ กรุณาติดต่อทีม IT

---

*อัพเดทล่าสุด: <?php echo date('Y-m-d H:i:s'); ?>*
*พัฒนาโดย: ทีมพัฒนาโรงเรียนสาธิตมหาวิทยาลัยราชภัฏสงขลา*
