# ระบบจัดการเอกสารราชการ (Official Documents System)

## 📋 ภาพรวม
ระบบจัดการเอกสารราชการสำหรับโรงเรียนสาธิตมหาวิทยาลัยพะเยา รองรับการจัดการ 4 ประเภท:
- ข้อบังคับ (Regulations)
- ระเบียบ (Rules)
- ประกาศ (Announcements)
- คำสั่ง (Orders)

## 🚀 การติดตั้ง

### 1. สร้างฐานข้อมูล
เปิดเบราว์เซอร์และเข้าไปที่:
```
http://localhost/satitup/setup_official_documents.php
```
สคริปต์จะสร้างตารางและข้อมูลตัวอย่างให้อัตโนมัติ

### 2. โครงสร้างไฟล์ที่สร้าง
```
satitup/
├── admin/
│   └── official_documents/
│       ├── index.php          # หน้ารายการเอกสาร
│       ├── add.php            # หน้าเพิ่มเอกสาร
│       ├── edit.php           # หน้าแก้ไขเอกสาร
│       └── delete.php         # ลบเอกสาร
├── uploads/
│   └── official_documents/
│       ├── regulations/      # เก็บไฟล์ข้อบังคับ
│       ├── rules/            # เก็บไฟล์ระเบียบ
│       ├── announcements/    # เก็บไฟล์ประกาศ
│       └── orders/          # เก็บไฟล์คำสั่ง
├── news_announcements.php     # หน้าแสดงผลสำหรับผู้ใช้ทั่วไป
├── create_official_documents_table.sql  # ไฟล์ SQL
└── setup_official_documents.php         # สคริปต์ติดตั้ง
```

## 📊 โครงสร้างฐานข้อมูล

### ตาราง `official_documents`
| Column | Type | Description |
|--------|------|-------------|
| id | INT | รหัสเอกสาร (Primary Key) |
| doc_type | ENUM | ประเภทเอกสาร |
| doc_number | VARCHAR(50) | เลขที่เอกสาร |
| category_id | INT | รหัสหมวดหมู่ |
| title | VARCHAR(500) | ชื่อเรื่อง |
| description | TEXT | รายละเอียด |
| file_path | VARCHAR(255) | ที่อยู่ไฟล์ PDF |
| publisher_name | VARCHAR(255) | ชื่อผู้ประกาศ |
| publisher_position | VARCHAR(255) | ตำแหน่งผู้ประกาศ |
| publish_date | DATE | วันที่ประกาศ |
| effective_date | DATE | วันที่มีผลบังคับใช้ |
| status | ENUM | สถานะ (active/inactive/draft) |
| views | INT | จำนวนการดู |
| created_at | TIMESTAMP | วันที่สร้าง |
| updated_at | TIMESTAMP | วันที่แก้ไขล่าสุด |

### ตาราง `official_documents_categories`
| Column | Type | Description |
|--------|------|-------------|
| id | INT | รหัสหมวดหมู่ |
| doc_type | ENUM | ประเภทเอกสารหลัก |
| category_name | VARCHAR(255) | ชื่อหมวดหมู่ |
| description | TEXT | คำอธิบาย |
| sort_order | INT | ลำดับการแสดงผล |
| status | ENUM | สถานะ |

### ตาราง `official_documents_logs`
| Column | Type | Description |
|--------|------|-------------|
| id | INT | รหัส log |
| document_id | INT | รหัสเอกสาร |
| action | ENUM | การกระทำ (view/download) |
| ip_address | VARCHAR(45) | IP address |
| user_agent | TEXT | Browser info |
| user_id | INT | ผู้ใช้ที่ล็อกอิน |
| created_at | TIMESTAMP | เวลาที่เกิดเหตุการณ์ |

## 🎯 ฟีเจอร์หลัก

### สำหรับผู้ดูแลระบบ (Admin)
1. **เพิ่มเอกสาร**
   - เลือกประเภทเอกสาร
   - เลือกหมวดหมู่ย่อย
   - กรอกข้อมูลเอกสาร
   - อัพโหลดไฟล์ PDF
   - ระบุผู้ประกาศและตำแหน่ง

2. **แก้ไขเอกสาร**
   - แก้ไขข้อมูลทั้งหมด
   - เปลี่ยนไฟล์ PDF
   - เปลี่ยนสถานะการเผยแพร่

3. **ลบเอกสาร**
   - ลบข้อมูลและไฟล์ที่เกี่ยวข้อง

4. **ดูสถิติ**
   - จำนวนเอกสารแต่ละประเภท
   - จำนวนการดู/ดาวน์โหลด

### สำหรับผู้ใช้ทั่วไป
1. **ดูเอกสาร**
   - แยกตามประเภท 4 หมวด
   - คลิกดาวน์โหลด PDF
   - ดูข้อมูลผู้ประกาศ
   - ดูวันที่ประกาศ

## 🔐 การเข้าใช้งาน

### หน้าจัดการ (Admin)
```
http://localhost/satitup/admin/official_documents/
```
**หมายเหตุ:** ต้องล็อกอินเข้าระบบก่อน

### หน้าแสดงผล (Public)
```
http://localhost/satitup/news_announcements.php
```

## 📝 การใช้งาน

### 1. เพิ่มเอกสารใหม่
1. เข้าสู่หน้าจัดการเอกสาร
2. คลิก "เพิ่มเอกสารใหม่"
3. เลือกประเภทและกรอกข้อมูล
4. อัพโหลดไฟล์ PDF
5. คลิก "บันทึกเอกสาร"

### 2. แก้ไขเอกสาร
1. คลิกปุ่มแก้ไข (ไอคอนดินสอ)
2. แก้ไขข้อมูลที่ต้องการ
3. คลิก "บันทึกการแก้ไข"

### 3. ลบเอกสาร
1. คลิกปุ่มลบ (ไอคอนถังขยะ)
2. ยืนยันการลบ

## 🎨 การปรับแต่ง

### เพิ่มหมวดหมู่ใหม่
เพิ่มข้อมูลในตาราง `official_documents_categories`:
```sql
INSERT INTO official_documents_categories 
(doc_type, category_name, description, sort_order) 
VALUES ('announcement', 'ชื่อหมวดหมู่', 'คำอธิบาย', 5);
```

### เปลี่ยนขนาดไฟล์สูงสุด
แก้ไขใน php.ini:
```
upload_max_filesize = 10M
post_max_size = 10M
```

## 🔧 การแก้ปัญหา

### ไม่สามารถอัพโหลดไฟล์ได้
- ตรวจสอบ permission ของโฟลเดอร์ uploads/official_documents
- ตรวจสอบขนาดไฟล์ไม่เกิน 10MB

### ไม่เห็นเอกสารในหน้าแสดงผล
- ตรวจสอบสถานะเอกสารต้องเป็น "active"
- ตรวจสอบการเชื่อมต่อฐานข้อมูล

## 📞 ติดต่อ
หากพบปัญหาการใช้งาน กรุณาติดต่อทีมพัฒนา

---
พัฒนาโดย: ทีมพัฒนาโรงเรียนสาธิตมหาวิทยาลัยพะเยา
วันที่: <?php echo date('d/m/Y'); ?>
