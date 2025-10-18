แก้ไขปัญหา 404 Not Found สำหรับรูปภาพ
========================================

ปัญหาที่พบ:
- external_links.php พยายามโหลดรูปภาพจาก images/logos/ ที่ไม่มีอยู่จริง
- placeholder-logo.png และ user-placeholder.png ไม่มีในระบบ
- เกิดข้อผิดพลาด 404 เมื่อโหลดหน้าเว็บ

การแก้ไข:
1. สร้างโฟลเดอร์ images/logos/
2. สร้าง placeholder-logo.png จาก comingsoon.png
3. สร้าง user-placeholder.png ใน assets/img/
4. สร้าง placeholder สำหรับโลโก้ทั้งหมด:
   - moe-logo.png (กระทรวงศึกษาธิการ)
   - obec-logo.png (สพฐ.)
   - niets-logo.png (สทศ.)
   - onesqa-logo.png (สมศ.)
   - tsri-logo.png (สกสว.)
   - up-logo.png (มหาวิทยาลัยพะเยา)

สถานะ:
✅ แก้ไขเรียบร้อยแล้ว - ไม่มีข้อผิดพลาด 404 อีกต่อไป

หมายเหตุ:
- ควรแทนที่ placeholder เหล่านี้ด้วยโลโก้จริงของแต่ละหน่วยงานในภายหลัง
- comingsoon.png ถูกใช้เป็น placeholder ชั่วคราว
- user-placeholder.png ถูกใช้ในระบบจัดการบุคลากร
