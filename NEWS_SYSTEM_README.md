# ระบบจัดการข่าวประชาสัมพันธ์
## โรงเรียนสาธิตมหาวิทยาลัยพะเยา

### 📋 ภาพรวมระบบ
ระบบจัดการข่าวประชาสัมพันธ์ที่พัฒนาขึ้นสำหรับโรงเรียนสาธิตมหาวิทยาลัยพะเยา เป็นระบบที่ช่วยในการจัดการข่าวสาร ประกาศ และเอกสารต่างๆ โดยมีระบบการจัดการผู้ใช้งานแบบ 2 ระดับ

### 🚀 คุณสมบัติหลัก
- **ระบบผู้ใช้งาน 2 ระดับ**: superadmin และ admin
- **จัดการประกาศ 3 หมวดหมู่**:
  - คำสั่งและประกาศ
  - การจัดซื้อจัดจ้าง
  - การรับสมัครงาน
- **อัพโหลดไฟล์ PDF** พร้อมประกาศ
- **ระบบค้นหาและกรองข้อมูล**
- **แดชบอร์ดแสดงสถิติ**
- **การแก้ไขและลบประกาศ** (สำหรับ superadmin)

### 📁 โครงสร้างไฟล์
```
satitup/
├── db_connect.php           # การเชื่อมต่อฐานข้อมูล
├── create_database.php      # สคริปต์สร้างฐานข้อมูลและตาราง
├── setup_database.php       # สคริปต์ตั้งค่าฐานข้อมูลเริ่มต้น
├── login.php                    # หน้าเข้าสู่ระบบ
├── logout.php                   # ระบบออกจากระบบ
├── news_announcements.php       # ส่วนแสดงผลข่าวสำหรับเว็บไซต์หลัก
├── news_management_system.php   # ระบบจัดการข่าวประชาสัมพันธ์ (Admin)
├── test_site.php                # หน้าทดสอบระบบ
└── uploads/
    └── announcements/      # โฟลเดอร์เก็บไฟล์ PDF ที่อัพโหลด
```

### 🔧 การติดตั้ง

#### ข้อกำหนดระบบ
- XAMPP หรือ Web Server ที่รองรับ PHP 7.4+
- MySQL 5.7+ หรือ MariaDB 10.3+
- PHP Extensions: PDO, PDO_MySQL

#### ขั้นตอนการติดตั้ง

1. **คัดลอกไฟล์ไปยัง Web Root**
   ```bash
   C:\xampp\htdocs\satitup\
   ```

2. **สร้างฐานข้อมูลและตาราง**
   ```bash
   php create_database.php
   ```
   หรือเข้าผ่าน browser:
   ```
   http://localhost/satitup/create_database.php
   ```

3. **เข้าสู่ระบบ**
   ```
   http://localhost/satitup/login.php
   ```

### 👥 ข้อมูลผู้ใช้งานเริ่มต้น

#### Super Administrator
- **Username**: superadmin
- **Password**: Admin@2024
- **สิทธิ์**: จัดการระบบทั้งหมด, แก้ไข/ลบประกาศ

#### Admin Users
| Username | ชื่อ-นามสกุล | ตำแหน่ง | Email |
|----------|-------------|---------|--------|
| admin02 | อินทนิล จินดากาศ | หัวหน้างานบริหารทั่วไป | inthanin.ji@up.ac.th |
| admin03 | อรอนงค์ เรียงจิตต์ | หัวหน้างานวิชาการ | onanong.ri@up.ac.th |
| admin04 | พนิดา ดุมดก | หัวหน้างานกิจการนักเรียน | phanida.du@up.ac.th |
| admin05 | กานต์ชนก บุญแข็ง | หัวหน้างานแผนงาน | kanchanok.bu@up.ac.th |
| admin06 | จินตนา ดูใจ | หัวหน้างานแผนงาน | jintana.do@up.ac.th |

**รหัสผ่านเริ่มต้นทั้งหมด**: Admin@2024

### 📊 โครงสร้างฐานข้อมูล

#### ตาราง users
```sql
- id (INT, Primary Key, AUTO_INCREMENT)
- username (VARCHAR(50), UNIQUE, NOT NULL)
- password (VARCHAR(255), NOT NULL)
- fullname (VARCHAR(100))
- email (VARCHAR(100), UNIQUE)
- role (ENUM('superadmin', 'admin'), NOT NULL)
- position (VARCHAR(100))
- created_at (TIMESTAMP)
```

#### ตาราง announcements
```sql
- id (INT, Primary Key, AUTO_INCREMENT)
- title (VARCHAR(255), NOT NULL)
- content (TEXT, NOT NULL)
- category (ENUM('announcement', 'procurement', 'recruitment'))
- file_path (VARCHAR(255))
- file_name (VARCHAR(255))
- user_id (INT, Foreign Key)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### 💡 การใช้งาน

#### สำหรับ Admin ทั่วไป
1. เข้าสู่ระบบด้วย username และ password
2. เพิ่มประกาศใหม่โดยกรอกข้อมูล:
   - หัวข้อประกาศ
   - เลือกหมวดหมู่
   - เนื้อหาประกาศ
   - แนบไฟล์ PDF (ถ้ามี)
3. ดูรายการประกาศทั้งหมด
4. ค้นหาและกรองประกาศตามหมวดหมู่

#### สำหรับ Super Admin
นอกจากสิทธิ์ของ Admin ทั่วไปแล้ว ยังสามารถ:
- แก้ไขประกาศที่มีอยู่
- ลบประกาศ
- จัดการประกาศของผู้ใช้ทุกคน

### 🔒 ความปลอดภัย
- รหัสผ่านเข้ารหัสด้วย `password_hash()` (bcrypt)
- ป้องกัน SQL Injection ด้วย PDO Prepared Statements
- Session-based Authentication
- ตรวจสอบสิทธิ์การเข้าถึงทุกหน้า
- จำกัดการอัพโหลดเฉพาะไฟล์ PDF
- ขนาดไฟล์สูงสุด 10MB

### 🎨 UI/UX Features
- Responsive Design ด้วย Bootstrap 5
- แอนิเมชั่น Fade In/Out
- ไอคอนด้วย Font Awesome
- การแจ้งเตือนแบบ Auto-dismiss
- ฟอร์มที่ใช้งานง่าย
- Dashboard แสดงสถิติแบบ Real-time

### 📝 หมายเหตุ
- แนะนำให้เปลี่ยนรหัสผ่านเริ่มต้นหลังจากเข้าสู่ระบบครั้งแรก
- ควรสำรองข้อมูลฐานข้อมูลเป็นประจำ
- ตรวจสอบการอัพเดทระบบเป็นระยะ

### 🆘 การแก้ไขปัญหา

#### ปัญหา: ไม่สามารถเชื่อมต่อฐานข้อมูลได้
- ตรวจสอบว่า MySQL/MariaDB service ทำงานอยู่
- ตรวจสอบ username/password ในไฟล์ `db_connect.php`

#### ปัญหา: ไม่สามารถอัพโหลดไฟล์ได้
- ตรวจสอบ permission ของโฟลเดอร์ `uploads/announcements/`
- ตรวจสอบ `upload_max_filesize` ใน php.ini

#### ปัญหา: Session ไม่ทำงาน
- ตรวจสอบ `session.save_path` ใน php.ini
- ตรวจสอบ permission ของโฟลเดอร์ session

### 📞 ติดต่อผู้พัฒนา
หากพบปัญหาหรือต้องการความช่วยเหลือ กรุณาติดต่อทีมพัฒนา

---
**Version**: 1.0.0  
**Last Updated**: 2024  
**Developed for**: โรงเรียนสาธิตมหาวิทยาลัยพะเยา
