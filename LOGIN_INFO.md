# 🔐 ข้อมูลการเข้าสู่ระบบ (Login Information)

## ✅ ปัญหาได้รับการแก้ไขแล้ว!

ระบบ login พร้อมใช้งาน มีบัญชีผู้ใช้ 3 บัญชีให้เลือกใช้:

---

## 📱 บัญชีผู้ใช้งาน

### 1️⃣ ผู้ดูแลระบบหลัก
- **Username:** `admin`  
- **Password:** `admin1234`
- **สิทธิ์:** เข้าถึงได้ทุกส่วนของระบบ
- **เหมาะสำหรับ:** ผู้บริหาร, IT Admin

### 2️⃣ เจ้าหน้าที่ประชาสัมพันธ์
- **Username:** `admin01`
- **Password:** `1234`
- **สิทธิ์:** จัดการข่าวสาร, วิดีโอ, ภาพสไลด์
- **เหมาะสำหรับ:** ฝ่ายประชาสัมพันธ์

### 3️⃣ ผู้ใช้ทดสอบ
- **Username:** `demo`
- **Password:** `demo1234`
- **สิทธิ์:** ทดลองใช้งานระบบ
- **เหมาะสำหรับ:** ทดสอบการทำงาน

---

## 🚀 วิธีเข้าใช้งาน

1. **เข้าหน้า Login:**
   ```
   http://localhost/satitup/admin/login.php
   ```

2. **กรอก Username และ Password** จากรายการด้านบน

3. **คลิก "เข้าสู่ระบบ"**

---

## 🔧 กรณีลืมรหัสผ่านหรือ Login ไม่ได้

### วิธีที่ 1: Reset Password (แนะนำ)
1. เปิด: `http://localhost/satitup/admin/reset_password.php`
2. ระบบจะ reset รหัสผ่านให้อัตโนมัติ
3. แสดงรายการบัญชีพร้อมรหัสผ่านใหม่

### วิธีที่ 2: ใช้ไฟล์ Reset เดิม
1. เปิด: `http://localhost/satitup/reset_admin.php`
2. จะ reset เฉพาะบัญชี admin01

---

## 📍 หน้าสำคัญของระบบ Admin

| หน้า | URL |
|------|-----|
| Login | http://localhost/satitup/admin/login.php |
| Dashboard | http://localhost/satitup/admin/news/dashboard.php |
| จัดการข่าว | http://localhost/satitup/admin/news/ |
| จัดการวิดีโอ | http://localhost/satitup/admin/video_system/ |
| จัดการสไลด์โชว์ | http://localhost/satitup/admin/slideshow/ |
| จัดการบุคลากร | http://localhost/satitup/admin/staff/ |

---

## ⚠️ ข้อควรระวัง

1. **เปลี่ยนรหัสผ่านทันที** หลังใช้งานจริง
2. **อย่าใช้รหัสผ่านเริ่มต้น** ในระบบ production
3. **ตรวจสอบ XAMPP** ว่า Apache และ MySQL ทำงานอยู่

---

## 💡 Tips

- หากไม่สามารถ login ได้ ให้รัน reset password ใหม่
- ตรวจสอบว่าใช้ฐานข้อมูล `satitup` 
- ดู error log ใน XAMPP หากมีปัญหา

---

*อัปเดตล่าสุด: <?php echo date('Y-m-d H:i:s'); ?>*
