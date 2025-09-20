<!-- CSS สำหรับแสดงการ์ดเดี่ยว -->
<style>
/* ส่วนพื้นหลังของเซคชั่นการจัดอันดับ */
.rankings-section {
    /* พื้นหลังไล่สีจากม่วงอ่อนไปขาว */
    background: linear-gradient(135deg, #f8f7fb 0%, #ffffff 100%);
    /* ระยะห่างด้านบนและล่าง 80px */
    padding: 80px 0;
    /* กำหนดตำแหน่งเป็น relative เพื่อให้สามารถวาง pseudo-element ได้ */
    position: relative;
    /* ซ่อนเนื้อหาส่วนที่ล้นออกไป */
    overflow: hidden;
}

/* สร้างวงกลมพื้นหลังด้านขวาบน */
.rankings-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    /* สร้างวงกลมไล่สีจากตรงกลางออกไป */
    background: radial-gradient(circle, rgba(139, 122, 168, 0.05) 0%, transparent 70%);
    /* แอนิเมชันลอยไปมา */
    animation: float 20s ease-in-out infinite;
}

/* กำหนดแอนิเมชัน float */
@keyframes float {
    /* เริ่มต้นและสิ้นสุดที่ตำแหน่งเดิม */
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    /* ตรงกลางแอนิเมชันเคลื่อนที่ไปทางซ้ายบนและหมุน */
    50% { transform: translate(-30px, -30px) rotate(180deg); }
}

/* ส่วนหัวข้อของเซคชั่น */
.section-header {
    /* ระยะห่างด้านล่าง 3rem */
    margin-bottom: 3rem;
}

/* สไตล์หัวข้อหลัก */
.section-title {
    /* ขนาดตัวอักษร 2.5rem */
    font-size: 2.5rem;
    /* ตัวหนา */
    font-weight: 700;
    /* สีตัวอักษรตามตัวแปร primary-color (สีม่วง) */
    color: var(--primary-color);
    /* ระยะห่างด้านล่าง */
    margin-bottom: 1rem;
    /* กำหนดตำแหน่งเพื่อให้สามารถใส่เส้นใต้ได้ */
    position: relative;
    /* แสดงเป็น inline-block เพื่อให้ขนาดพอดีกับเนื้อหา */
    display: inline-block;
}

/* เส้นใต้หัวข้อ */
.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    /* จัดให้อยู่ตรงกลาง */
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    /* พื้นหลังไล่สีจากสีชมพูไปสีม่วง */
    background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
    /* มุมโค้ง */
    border-radius: 2px;
}

/* สไตล์หัวข้อรอง */
.section-subtitle {
    /* ขนาดตัวอักษร */
    font-size: 1.1rem;
    /* สีตัวอักษรตามตัวแปร text-secondary (สีเทา) */
    color: var(--text-secondary);
}

/* สไตล์การ์ดแสดงการจัดอันดับ */
.ranking-card-single {
    /* พื้นหลังสีขาว */
    background: white;
    /* มุมโค้ง 20px */
    border-radius: 20px;
    /* ซ่อนเนื้อหาที่ล้นออกไป */
    overflow: hidden;
    /* เงาของการ์ด */
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    /* การเปลี่ยนแปลงทุกคุณสมบัติใช้เวลา 0.5 วินาที ด้วยฟังก์ชัน cubic-bezier */
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    /* ความกว้าง 90% ของพื้นที่ที่มี */
    width: 90%;
    /* ความกว้างสูงสุด 1200px */
    max-width: 1200px;
    /* จัดให้อยู่ตรงกลาง */
    margin: 0 auto;
    /* จัดการแสดงผลแบบ flex */
    display: flex;
    /* จัดเรียงเนื้อหาแนวนอน */
    flex-direction: row;
    /* ความสูงที่เหมาะสม */
    height: 400px;
}

/* เอฟเฟกต์เมื่อ hover การ์ด */
.ranking-card-single:hover {
    /* ยกขึ้น 10px และขยายขนาดเล็กน้อย */
    transform: translateY(-10px) scale(1.02);
    /* เงาเข้มขึ้นและใหญ่ขึ้น */
    box-shadow: 0 20px 60px rgba(139, 122, 168, 0.3);
}

/* ลิงก์ในการ์ด */
.ranking-link {
    /* ลบเส้นใต้ */
    text-decoration: none;
    /* สีตัวอักษรตามปกติ */
    color: inherit;
    /* แสดงเป็นบล็อก */
    display: block;
}

/* ส่วนแสดงรูปภาพ */
.ranking-image {
    /* กำหนดตำแหน่งเป็น relative เพื่อให้สามารถวาง overlay ทับได้ */
    position: relative;
    /* ซ่อนส่วนที่ล้นออกไป */
    overflow: hidden;
    /* พื้นหลังไล่สีจากม่วงอ่อนไปม่วงเข้ม (กรณีที่ไม่มีรูปภาพ) */
    background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
    /* จัดให้เนื้อหาอยู่ตรงกลาง */
    display: flex;
    align-items: center;
    justify-content: center;
    /* กำหนดความกว้างเป็น 50% ของการ์ด */
    flex: 0 0 50%;
    /* ความสูงเต็ม */
    height: 100%;
    /* padding สำหรับระยะห่าง */
    padding: 20px;
}

/* รูปภาพในส่วนแสดงรูปภาพ */
.ranking-image img {
    /* ความกว้างสูงสุด 100% ของพื้นที่ */
    max-width: 100%;
    /* ความสูงสูงสุด 100% ของพื้นที่ */
    max-height: 100%;
    /* ความกว้างอัตโนมัติ */
    width: auto;
    /* ความสูงอัตโนมัติ */
    height: auto;
    /* ปรับขนาดภาพให้พอดีกับพื้นที่ แสดงเต็มภาพโดยไม่ตัดส่วนใด */
    object-fit: contain;
    /* จัดตำแหน่งภาพให้อยู่ตรงกลาง */
    object-position: center;
    /* การเปลี่ยนแปลงใช้เวลา 0.5 วินาที */
    transition: all 0.5s ease;
    /* ตำแหน่งสัมพัทธ์ */
    position: relative;
    /* ให้ภาพอยู่ด้านหน้า */
    z-index: 1;
    /* แสดงเป็นบล็อก */
    display: block;
    /* ปรับขอบให้สวยงาม */
    border-radius: 12px;
    /* เงาเบาๆ รอบรูป */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* เอฟเฟกต์รูปภาพเมื่อ hover การ์ด */
.ranking-card-single:hover .ranking-image img {
    /* ขยายรูปภาพขึ้น 10% */
    transform: scale(1.1);
}

/* โอเวอร์เลย์ทับรูปภาพ */
.ranking-overlay {
    /* กำหนดตำแหน่งเป็น absolute เพื่อวางทับรูปภาพ */
    position: absolute;
    /* วางทับเต็มพื้นที่รูปภาพ */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    /* พื้นหลังไล่สีโปร่งใสจากม่วงอ่อนไปม่วงเข้ม */
    background: linear-gradient(135deg, rgba(139, 122, 168, 0.3), rgba(139, 122, 168, 0.6));
    /* จัดให้ไอคอนอยู่ตรงกลาง */
    display: flex;
    align-items: center;
    justify-content: center;
    /* เริ่มต้นโปร่งใส (มองไม่เห็น) */
    opacity: 0;
    /* การเปลี่ยนแปลงใช้เวลา 0.5 วินาที */
    transition: all 0.5s ease;
}

/* แสดงโอเวอร์เลย์เมื่อ hover การ์ด */
.ranking-card-single:hover .ranking-overlay {
    /* แสดงโอเวอร์เลย์ */
    opacity: 1;
}

/* ไอคอนในโอเวอร์เลย์ */
.ranking-overlay i {
    /* ขนาดไอคอน */
    font-size: 4rem;
    /* สีขาว */
    color: white;
    /* เริ่มต้นขนาดเป็น 0 (ไม่แสดง) */
    transform: scale(0);
    /* การเปลี่ยนแปลงใช้เวลา 0.5 วินาที และหน่วงเวลา 0.2 วินาที */
    transition: all 0.5s ease 0.2s;
}

/* แสดงไอคอนเมื่อ hover การ์ด */
.ranking-card-single:hover .ranking-overlay i {
    /* ขยายไอคอนเป็นขนาดปกติและหมุน 360 องศา */
    transform: scale(1) rotate(360deg);
}

/* ส่วนแสดงเนื้อหาของการ์ด */
.ranking-content {
    /* ใช้พื้นที่ที่เหลือ 50% */
    flex: 1;
    /* ระยะห่างภายใน */
    padding: 30px;
    /* จัดข้อความตรงกลาง */
    text-align: center;
    /* จัดเนื้อหาให้อยู่ตรงกลางแนวตั้ง */
    display: flex;
    flex-direction: column;
    justify-content: center;
    /* ระยะห่างระหว่างองค์ประกอบ */
    gap: 15px;
}

/* หัวข้อของการจัดอันดับ */
.ranking-title {
    /* ขนาดตัวอักษร 2rem */
    font-size: 2rem;
    /* ตัวหนา */
    font-weight: 700;
    /* สีตัวอักษรตามตัวแปร primary-dark (สีม่วงเข้ม) */
    color: var(--primary-dark);
    /* ระยะห่างด้านล่าง */
    margin-bottom: 15px;
    /* การเปลี่ยนแปลงใช้เวลา 0.3 วินาที */
    transition: all 0.3s ease;
    /* ความสูงบรรทัด */
    line-height: 1.4;
}

/* เปลี่ยนสีหัวข้อเมื่อ hover การ์ด */
.ranking-card-single:hover .ranking-title {
    /* เปลี่ยนเป็นสีม่วงอ่อนกว่าเดิม */
    color: var(--primary-color);
}

/* คำอธิบายการจัดอันดับ */
.ranking-description {
    /* ขนาดตัวอักษร 1.2rem */
    font-size: 1.2rem;
    /* สีตัวอักษรตามตัวแปร text-secondary (สีเทา) */
    color: var(--text-secondary);
    /* ไม่มีระยะห่างด้านล่าง */
    margin-bottom: 0;
    /* ความสูงบรรทัด */
    line-height: 1.6;
}

/* ปุ่มควบคุมสไลด์ซ้าย-ขวา */
.rankings-control-prev,
.rankings-control-next {
    /* ขนาดปุ่ม 50x50 px */
    width: 50px;
    height: 50px;
    /* พื้นหลังสีขาว */
    background: white;
    /* ปุ่มทรงกลม */
    border-radius: 50%;
    /* ขอบสีม่วงอ่อน */
    border: 2px solid var(--primary-light);
    /* เงาของปุ่ม */
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    /* ความโปร่งใส 90% */
    opacity: 0.9;
    /* การเปลี่ยนแปลงใช้เวลา 0.3 วินาที */
    transition: all 0.3s ease;
    /* จัดตำแหน่งให้อยู่กึ่งกลางด้านข้าง */
    top: 50%;
    transform: translateY(-50%);
    /* เพิ่มเอฟเฟกต์เรืองแสง */
    cursor: pointer;
    z-index: 10;
}

/* เอฟเฟกต์เมื่อ hover ปุ่มควบคุม */
.rankings-control-prev:hover,
.rankings-control-next:hover {
    /* เปลี่ยนพื้นหลังเป็นไล่สีม่วง */
    background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
    /* เปลี่ยนสีขอบเป็นม่วงเข้ม */
    border-color: var(--primary-color);
    /* ขยายขนาดขึ้น 10% */
    transform: translateY(-50%) scale(1.1);
    /* ความโปร่งใส 100% */
    opacity: 1;
    /* เพิ่มเงาเรืองแสง */
    box-shadow: 0 0 20px rgba(123, 59, 149, 0.5);
}

/* เปลี่ยนสีไอคอนเมื่อ hover ปุ่มควบคุม */
.rankings-control-prev:hover .control-icon,
.rankings-control-next:hover .control-icon {
    /* เปลี่ยนสีไอคอนเป็นสีขาว */
    color: white;
}

/* ปุ่มควบคุมด้านซ้าย */
.rankings-control-prev {
    /* ห่างจากขอบซ้าย 20px */
    left: 20px;
}

/* ปุ่มควบคุมด้านขวา */
.rankings-control-next {
    /* ห่างจากขอบขวา 20px */
    right: 20px;
}

/* ไอคอนในปุ่มควบคุม */
.control-icon {
    /* สีม่วงตามตัวแปร primary-color */
    color: var(--primary-color);
    /* ขนาดไอคอน */
    font-size: 20px;
    /* การเปลี่ยนแปลงใช้เวลา 0.3 วินาที */
    transition: all 0.3s ease;
}

/* จุดบอกตำแหน่งสไลด์ด้านล่าง */
.rankings-indicators {
    /* ห่างจากด้านล่าง 20px */
    bottom: 20px;
    z-index: 5;
}

/* ปุ่มจุดบอกตำแหน่งสไลด์ */
.rankings-indicators button {
    /* ขนาด 12x12 px */
    width: 12px;
    height: 12px;
    /* ทรงกลม */
    border-radius: 50%;
    /* พื้นหลังสีม่วงโปร่งใส 30% */
    background: rgba(139, 122, 168, 0.3);
    /* ขอบสีม่วงอ่อน */
    border: 2px solid var(--primary-light);
    /* ระยะห่างซ้าย-ขวา 5px */
    margin: 0 5px;
    /* การเปลี่ยนแปลงใช้เวลา 0.3 วินาที */
    transition: all 0.3s ease;
    cursor: pointer;
}

/* เอฟเฟกต์เมื่อ hover ปุ่มจุดบอกตำแหน่งสไลด์ */
.rankings-indicators button:hover {
    /* เพิ่มขนาด */
    transform: scale(1.2);
    /* เพิ่มความทึบ */
    background: rgba(139, 122, 168, 0.6);
    /* เพิ่มเงา */
    box-shadow: 0 0 10px rgba(123, 59, 149, 0.3);
}

/* ปุ่มจุดบอกตำแหน่งสไลด์ที่กำลังแสดงอยู่ */
.rankings-indicators button.active {
    /* ขยายความกว้างเป็น 30px */
    width: 30px;
    /* เปลี่ยนเป็นรูปแคปซูล */
    border-radius: 20px;
    /* พื้นหลังไล่สีม่วง */
    background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
    /* ขอบสีม่วงเข้ม */
    border-color: var(--primary-color);
    /* เพิ่มเงา */
    box-shadow: 0 0 15px rgba(123, 59, 149, 0.5);
}

/* เอฟเฟกต์จางเข้า-ออกสำหรับสไลด์ */
.carousel-fade .carousel-item {
    /* เริ่มต้นโปร่งใส (มองไม่เห็น) */
    opacity: 0;
    /* การเปลี่ยนแปลงความโปร่งใสใช้เวลา 0.8 วินาที */
    transition: opacity 0.8s ease-in-out;
}

/* สไลด์ที่กำลังแสดงอยู่ */
.carousel-fade .carousel-item.active {
    /* แสดงเต็มความทึบ */
    opacity: 1;
}

/* แอนิเมชันสำหรับการ์ดเมื่อสไลด์เปลี่ยน */
.carousel-item.active .ranking-card-single {
    /* ใช้แอนิเมชัน slideInUp เป็นเวลา 0.8 วินาที */
    animation: slideInUp 0.8s ease forwards;
}

/* กำหนดแอนิเมชัน slideInUp */
@keyframes slideInUp {
    /* จุดเริ่มต้น: เลื่อนลงด้านล่าง 50px และโปร่งใส */
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    /* จุดสิ้นสุด: ตำแหน่งปกติและแสดงเต็มความทึบ */
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* การปรับขนาดตามหน้าจอ (Responsive Design) */

/* สำหรับหน้าจอขนาดกลาง (น้อยกว่า 992px) */
@media (max-width: 992px) {
    /* ปรับการ์ดให้แสดงแนวตั้ง */
    .ranking-card-single {
        flex-direction: column;
        height: auto;
        width: 95%;
    }
    
    /* ปรับขนาดส่วนรูปภาพ */
    .ranking-image {
        flex: none;
        width: 100%;
        height: 250px;
        padding: 15px;
    }
    
    /* ลดขนาดหัวข้อ */
    .ranking-title {
        font-size: 1.8rem;
    }
    
    /* ลดขนาดคำอธิบาย */
    .ranking-description {
        font-size: 1.1rem;
    }
    
    /* ปรับ padding ของเนื้อหา */
    .ranking-content {
        padding: 25px;
    }
}

/* สำหรับหน้าจอขนาดเล็ก (น้อยกว่า 768px) */
@media (max-width: 768px) {
    /* ลดขนาดหัวข้อเซคชั่น */
    .section-title {
        font-size: 2rem;
    }
    
    /* ลดขนาดหัวข้อรอง */
    .section-subtitle {
        font-size: 1rem;
    }
    
    /* ปรับการ์ดให้เต็มความกว้าง */
    .ranking-card-single {
        width: 100%;
        border-radius: 15px;
    }
    
    /* ปรับขนาดส่วนรูปภาพ */
    .ranking-image {
        height: 200px;
        padding: 12px;
    }
    
    /* ลดระยะห่างภายในส่วนเนื้อหา */
    .ranking-content {
        padding: 20px;
        gap: 10px;
    }
    
    /* ลดขนาดหัวข้อการจัดอันดับ */
    .ranking-title {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    
    /* ลดขนาดคำอธิบาย */
    .ranking-description {
        font-size: 1rem;
    }
    
    /* ลดขนาดไอคอนในโอเวอร์เลย์ */
    .ranking-overlay i {
        font-size: 3rem;
    }
    
    /* ลดขนาดปุ่มควบคุม */
    .rankings-control-prev,
    .rankings-control-next {
        width: 40px;
        height: 40px;
    }
    
    /* ลดขนาดไอคอนในปุ่มควบคุม */
    .control-icon {
        font-size: 16px;
    }
}

/* สำหรับหน้าจอขนาดเล็กมาก (น้อยกว่า 576px) */
@media (max-width: 576px) {
    /* ลดระยะห่างของเซคชั่น */
    .rankings-section {
        padding: 30px 0;
    }
    
    /* ปรับการ์ดให้มีขอบโค้งน้อยลง */
    .ranking-card-single {
        border-radius: 10px;
    }
    
    /* ปรับขนาดส่วนรูปภาพ */
    .ranking-image {
        height: 180px;
        padding: 10px;
    }
    
    /* ลดระยะห่างภายในส่วนเนื้อหา */
    .ranking-content {
        padding: 15px;
        gap: 8px;
    }
    
    /* ลดขนาดหัวข้อการจัดอันดับ */
    .ranking-title {
        font-size: 1.3rem;
        margin-bottom: 8px;
    }
    
    /* ลดขนาดคำอธิบาย */
    .ranking-description {
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    /* ปรับตำแหน่งปุ่มควบคุมให้ชิดขอบมากขึ้น */
    .rankings-control-prev {
        left: 5px;
    }
    
    .rankings-control-next {
        right: 5px;
    }
    
    /* ลดขนาดปุ่มควบคุม */
    .rankings-control-prev,
    .rankings-control-next {
        width: 35px;
        height: 35px;
    }
    
    /* ปรับขนาดไอคอน */
    .ranking-overlay i {
        font-size: 2.5rem;
    }
    
    /* ลดขนาดรูปภาพ */
    .ranking-image img {
        border-radius: 8px;
    }
}
</style>
