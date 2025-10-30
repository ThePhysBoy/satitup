# 🔧 วิธีเปิดใช้งาน Places API สำหรับช่องค้นหาสถานที่

## 📌 ขั้นตอนการเปิดใช้งาน Places API:

### 1️⃣ เข้า Google Cloud Console
1. ไปที่: https://console.cloud.google.com/
2. เข้าสู่ระบบด้วยบัญชี Google ที่สร้าง API Key

### 2️⃣ เลือกโปรเจกต์
1. คลิกที่ชื่อโปรเจกต์ด้านบน
2. เลือกโปรเจกต์ที่มี API Key ของคุณ

### 3️⃣ เปิดหน้า APIs & Services
1. คลิกเมนู ☰ (ด้านบนซ้าย)
2. เลือก **APIs & Services**
3. เลือก **Library**

### 4️⃣ เปิดใช้งาน Places API
1. ในช่องค้นหา พิมพ์ **"Places API"**
2. คลิกเลือก **Places API**
3. คลิกปุ่ม **ENABLE** (สีน้ำเงิน)
4. รอสักครู่จนเปิดใช้งานเสร็จ

### 5️⃣ เปิด API เพิ่มเติม (แนะนำ)
ค้นหาและเปิดใช้งาน API เหล่านี้ด้วย:
- ✅ **Maps JavaScript API** (สำหรับแสดงแผนที่)
- ✅ **Places API** (สำหรับค้นหาสถานที่)
- ✅ **Geocoding API** (สำหรับแปลงพิกัดเป็นที่อยู่)
- ✅ **Geolocation API** (สำหรับหาตำแหน่งปัจจุบัน)

## 🔍 วิธีตรวจสอบว่าเปิดแล้ว:

### ไปที่หน้า Enabled APIs
1. APIs & Services > **Enabled APIs**
2. ต้องเห็นรายการ:
   - Maps JavaScript API ✓
   - Places API ✓
   - Geocoding API ✓

## 💳 การเปิดใช้ Billing (ถ้าจำเป็น):

Google Maps API ใหม่ๆ อาจต้องเปิด Billing Account:
1. ไปที่ **Billing** ในเมนู
2. สร้าง Billing Account (ได้เครดิตฟรี $200)
3. เชื่อมกับโปรเจกต์

**หมายเหตุ**: 
- ได้ฟรี $200 เครดิตแรกเริ่ม
- ใช้ฟรี $200/เดือน สำหรับ Maps
- ส่วนใหญ่ไม่เกินโควต้าฟรี

## 🔄 หลังเปิด Places API แล้ว:

1. **รอ 5 นาที** ให้ระบบอัพเดท
2. **Refresh หน้าเว็บ** (Ctrl+F5)
3. **ล้าง Cache Browser**
4. **ทดสอบค้นหาใหม่**

## ✅ เมื่อทำงานปกติจะเห็น:
- ช่องค้นหา**ไม่มี error แดง**
- พิมพ์แล้ว**มี dropdown** แสดงผล
- เลือกแล้ว**แผนที่เลื่อน**ไปที่สถานที่
- **ไม่มี error** ใน Console

## 🚨 หากยังมีปัญหา:

### ตรวจสอบ API Key Restrictions:
1. ไปที่ **Credentials**
2. คลิกที่ API Key ของคุณ
3. ดูส่วน **API restrictions**
4. เลือก **Don't restrict key** (ชั่วคราว)
   หรือ
5. เลือก **Restrict key** แล้วเลือก APIs:
   - Maps JavaScript API ☑
   - Places API ☑
   - Geocoding API ☑

### ตรวจสอบ Website Restrictions:
1. ในส่วน **Website restrictions**
2. เพิ่ม:
   ```
   localhost/*
   localhost:*
   127.0.0.1/*
   ```

## 📱 Quick Links:

### เปิด Places API โดยตรง:
https://console.cloud.google.com/apis/library/places-backend.googleapis.com

### ดู API Key ของคุณ:
https://console.cloud.google.com/apis/credentials

### ดูการใช้งาน API:
https://console.cloud.google.com/apis/dashboard

---

## 🎯 สรุปขั้นตอนด่วน:
1. เข้า Google Cloud Console
2. APIs & Services > Library
3. ค้นหา "Places API"
4. คลิก ENABLE
5. รอ 5 นาที
6. Refresh หน้าเว็บ

**API Key ปัจจุบัน**: `AIzaSyAvHiS_X2q82YL5pdInenuswpeJN7RpuiQ`

---
*หากทำตามแล้วยังไม่ได้ กรุณาแจ้ง error ที่พบ*
