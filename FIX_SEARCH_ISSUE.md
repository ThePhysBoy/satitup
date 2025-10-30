# 🔧 แก้ไขปัญหาช่องค้นหาสถานที่ไม่ทำงาน

## ✅ การแก้ไขที่ทำแล้ว:
1. **ป้องกันการ Submit Form** เมื่อกด Enter
2. **เพิ่มการตรวจสอบ** Places API Library
3. **เพิ่ม Error Handling** และ Console logs

## ⚠️ สิ่งที่ต้องตรวจสอบ:

### 1. เปิดใช้งาน Places API ใน Google Cloud Console
1. ไปที่ [Google Cloud Console](https://console.cloud.google.com/)
2. เลือกโปรเจกต์ของคุณ
3. ไปที่ **APIs & Services** > **Library**
4. ค้นหาและเปิดใช้งาน:
   - ✅ **Maps JavaScript API**
   - ✅ **Places API** (สำคัญมาก!)
   - ✅ **Geocoding API**

### 2. ตรวจสอบสิทธิ์ของ API Key
1. ไปที่ **APIs & Services** > **Credentials**
2. เลือก API Key ของคุณ
3. ตรวจสอบว่า:
   - ไม่มีการจำกัด IP (หรือรวม localhost)
   - มีสิทธิ์ใช้ Places API

### 3. ทดสอบการค้นหา
1. เปิด Developer Console (F12)
2. ดู Tab Console
3. พิมพ์ชื่อสถานที่ในช่องค้นหา
4. เลือกจาก Dropdown ที่แสดง (ไม่ต้องกด Enter)

## 🔍 วิธีใช้งานช่องค้นหาที่ถูกต้อง:

### ✅ วิธีที่ถูก:
1. **พิมพ์ชื่อสถานที่** (เช่น "โรงพยาบาลสตูล")
2. **รอให้แสดงรายการ** dropdown
3. **คลิกเลือกจากรายการ** หรือใช้ลูกศรเลือกแล้วกด Enter
4. แผนที่จะเลื่อนไปที่สถานที่นั้น

### ❌ วิธีที่ผิด:
- พิมพ์แล้วกด Enter ทันที (ไม่รอ dropdown)
- พิมพ์ที่อยู่แบบเต็มๆ

## 🛠️ การแก้ปัญหาเพิ่มเติม:

### ถ้ายังไม่ทำงาน:
1. **ดู Console Error**
   ```
   F12 > Console Tab
   ```
   ดูว่ามี error อะไร

2. **ตรวจสอบ API Key**
   ```sql
   -- ตรวจสอบใน Database
   SELECT * FROM api_keys WHERE api_name = 'google_maps';
   ```

3. **ทดสอบ API Key**
   - ไปที่ [Google Maps Platform Credentials](https://console.cloud.google.com/google/maps-apis/credentials)
   - ดู Request metrics ว่ามีการใช้งานหรือไม่

## 📝 หมายเหตุ:
- การค้นหาจะทำงานแบบ **Autocomplete** 
- ต้อง**เลือกจาก dropdown** ไม่ใช่กด Enter
- รองรับการค้นหาภาษาไทยและอังกฤษ
- ค้นหาได้ทั้งชื่อสถานที่และที่อยู่

## 🚨 ข้อความ Error ที่อาจพบ:

| Error | สาเหมตุ | วิธีแก้ |
|-------|---------|---------|
| "This API project is not authorized to use this API" | ไม่ได้เปิด Places API | เปิดใช้งาน Places API |
| "RefererNotAllowedMapError" | API Key ถูกจำกัด domain | แก้ไขการจำกัดใน API Key |
| "InvalidKeyMapError" | API Key ไม่ถูกต้อง | ตรวจสอบ API Key |
| "การค้นหาไม่พร้อมใช้งาน" | Places library ไม่โหลด | ตรวจสอบ API permissions |

## ✅ สิ่งที่ควรเห็นเมื่อทำงานปกติ:
1. พิมพ์ 2-3 ตัวอักษร จะเห็น dropdown แสดงผลการค้นหา
2. มีไอคอนและที่อยู่แสดงในแต่ละรายการ
3. เมื่อเลือก แผนที่จะ zoom ไปที่สถานที่นั้น
4. ที่อยู่จะถูกใส่ในช่อง "ที่อยู่" อัตโนมัติ

---
*อัพเดทล่าสุด: <?php echo date('Y-m-d H:i:s'); ?>*
