# คู่มือการใช้งานระบบแผนที่พันธมิตร (Partners Map System)

## 📍 ภาพรวมระบบ
ระบบแผนที่พันธมิตร เป็นระบบที่แสดงหน่วยงานพันธมิตรที่ทำ MOU ร่วมกันบนแผนที่ Google Maps แบบ Interactive พร้อมฟีเจอร์:
- 🗺️ แสดงตำแหน่งพันธมิตรบนแผนที่
- 🎯 Hover เพื่อดูข้อมูลแบบ Popup
- 🔗 คลิกเพื่อดูรายละเอียด
- 📍 จัดการพิกัดผ่านหน้า Admin

## 🚀 การติดตั้งและเตรียมระบบ

### 1. รันคำสั่ง SQL เพิ่มฟิลด์ในฐานข้อมูล
```sql
-- รันไฟล์: add_partners_location.sql
ALTER TABLE partners 
ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 8) NULL,
ADD COLUMN IF NOT EXISTS longitude DECIMAL(11, 8) NULL,
ADD COLUMN IF NOT EXISTS address TEXT NULL,
ADD COLUMN IF NOT EXISTS map_zoom_level INT DEFAULT 15;
```

### 2. ตั้งค่า Google Maps API Key

#### วิธีที่ 1: ผ่านระบบจัดการ (แนะนำ)
1. เข้าสู่ระบบ Admin
2. ไปที่ "จัดการ API Keys"
3. แก้ไข google_maps
4. ใส่ API Key ที่ได้จาก Google Cloud Console
5. บันทึก

#### วิธีที่ 2: ขอ API Key ใหม่ (ถ้าจำเป็น)
1. ไปที่ [Google Cloud Console](https://console.cloud.google.com/)
2. สร้างโปรเจกต์ใหม่หรือเลือกโปรเจกต์ที่มีอยู่
3. เปิดใช้งาน APIs:
   - Maps JavaScript API
   - Places API
   - Geocoding API
4. สร้าง API Key และจำกัดการใช้งาน

**หมายเหตุ**: ระบบมี API Key เริ่มต้นให้แล้ว สามารถใช้ได้ทันที

## 📝 การใช้งานฝั่ง Admin

### เพิ่มพิกัดให้พันธมิตร
1. เข้าหน้าจัดการพันธมิตร (`/admin/partners/`)
2. คลิกปุ่ม "ตำแหน่ง" (สีเขียว) ที่พันธมิตรที่ต้องการ
3. ในหน้าจัดการตำแหน่ง สามารถ:
   - **คลิกบนแผนที่** เพื่อปักหมุด
   - **ลากหมุด** เพื่อย้ายตำแหน่ง
   - **ค้นหาสถานที่** ด้วยช่องค้นหา
   - **ใช้ตำแหน่งปัจจุบัน** (GPS)
   - **ปรับระดับ Zoom** (1-20)
4. กดบันทึกตำแหน่ง

### Badge แสดงสถานะพิกัด
- 🟢 **มีพิกัด** - พันธมิตรมีตำแหน่งบนแผนที่แล้ว
- 🟡 **ยังไม่มีพิกัด** - ยังไม่ได้กำหนดตำแหน่ง

## 🗺️ การแสดงผลบนหน้าเว็บ

### วิธีใช้งานไฟล์ partners_section_map.php
1. เรียกใช้แทนไฟล์ `partners_section.php` เดิม
2. ในไฟล์ `index.php` แก้ไข:

```php
// เดิม
include 'partners_section.php';

// เปลี่ยนเป็น
include 'partners_section_map.php';
```

### ฟีเจอร์การแสดงผล
1. **แผนที่ Interactive**
   - ซูมเข้า-ออกได้
   - เปลี่ยนมุมมอง (Map/Satellite)
   - Street View

2. **Markers บนแผนที่**
   - แสดงตามพิกัดที่กำหนด
   - สีม่วงไล่เฉด (Gradient)
   - Animation เมื่อโหลด

3. **Hover Effects**
   - เอาเมาส์ชี้ที่หมุด = แสดง Popup
   - แสดงข้อมูล: ชื่อ, โครงการ, คำอธิบาย, ที่อยู่
   - เอาเมาส์ออก = Popup หายไป

4. **คลิกที่หมุด**
   - เปิดหน้ารายละเอียดใน Tab ใหม่

5. **รายการพันธมิตรด้านข้าง**
   - คลิกเพื่อ Focus ไปที่หมุดบนแผนที่
   - Highlight เมื่อ Hover หมุด

## 🎨 การปรับแต่ง

### เปลี่ยนจุดศูนย์กลางเริ่มต้น
ในไฟล์ `partners_section_map.php` บรรทัด 319:
```javascript
const centerLocation = { lat: 6.6238, lng: 100.0676 }; // สตูล
```

### ปรับสไตล์แผนที่
แก้ไข styles array ในบรรทัด 335-361

### เปลี่ยนสี Marker
แก้ไข SVG ในบรรทัด 393-406

### ปรับขนาด Info Window
แก้ไข CSS class `.info-window-content` 

## 🔧 การแก้ปัญหา

### แผนที่ไม่แสดง
1. ตรวจสอบ API Key ถูกต้อง
2. ตรวจสอบ API ถูกเปิดใช้งาน
3. ดู Console Log สำหรับ Error

### หมุดไม่แสดง
1. ตรวจสอบข้อมูล latitude/longitude ในฐานข้อมูล
2. ตรวจสอบ status = 'active'

### Popup ไม่แสดง
1. ตรวจสอบข้อมูลในฐานข้อมูลครบถ้วน
2. ดู Console สำหรับ JavaScript Error

## 📊 ตัวอย่างพิกัดสถานที่ในสตูล

```sql
-- มหาวิทยาลัยราชภัฏสงขลา วิทยาเขตสตูล
UPDATE partners SET 
latitude = 6.6301, 
longitude = 100.0802,
address = 'มหาวิทยาลัยราชภัฏสงขลา วิทยาเขตสตูล'
WHERE id = 1;

-- โรงพยาบาลสตูล
UPDATE partners SET 
latitude = 6.6237, 
longitude = 100.0665,
address = 'โรงพยาบาลสตูล อ.เมือง จ.สตูล'
WHERE id = 2;

-- ศาลากลางจังหวัดสตูล
UPDATE partners SET 
latitude = 6.6234, 
longitude = 100.0618,
address = 'ศาลากลางจังหวัดสตูล'
WHERE id = 3;
```

## 🛡️ ความปลอดภัย

1. **จำกัดการใช้งาน API Key**
   - ระบุโดเมนที่อนุญาต
   - กำหนด Quota การใช้งาน

2. **ตรวจสอบสิทธิ์ Admin**
   - เฉพาะ Admin/PR เท่านั้นที่แก้ไขพิกัดได้

3. **Validate Input**
   - ตรวจสอบค่า latitude (-90 ถึง 90)
   - ตรวจสอบค่า longitude (-180 ถึง 180)

## 📱 Responsive Design

ระบบรองรับการแสดงผลบน:
- 💻 Desktop: แผนที่ + รายการด้านข้าง
- 📱 Tablet: แผนที่ + รายการด้านล่าง
- 📱 Mobile: แผนที่เต็มจอ + รายการด้านล่าง

## 📚 ลิงค์ที่เป็นประโยชน์

- [Google Maps JavaScript API](https://developers.google.com/maps/documentation/javascript)
- [Places Library](https://developers.google.com/maps/documentation/javascript/places)
- [Marker Clustering](https://developers.google.com/maps/documentation/javascript/marker-clustering)
- [Custom Markers](https://developers.google.com/maps/documentation/javascript/custom-markers)

---

*พัฒนาโดย: ทีมพัฒนาโรงเรียนสาธิตมหาวิทยาลัยราชภัฏสงขลา*
