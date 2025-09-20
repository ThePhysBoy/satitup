# Admin News Management System - Template Guide

## 📋 ภาพรวม

ระบบจัดการข่าวสำหรับผู้ดูแลระบบที่ใช้การออกแบบ Glass Morphism สมัยใหม่ พร้อมฟีเจอร์ครบครัน

## 🎨 Template System

### ไฟล์หลัก

- **`template.php`** - Template หลักสำหรับการออกแบบ
- **`create.php`** - หน้าสร้างข่าวใหม่ (ใช้ template)
- **`edit_new.php`** - หน้าแก้ไขข่าว (ตัวอย่างการใช้ template)
- **`index_template_example.php`** - ตัวอย่างการใช้ template สำหรับหน้า index

## 🚀 การใช้ Template

### 1. รวมโค๊ด PHP Logic ก่อน

```php
<?php
// Include required files
require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';

// Your PHP logic here
$errors = [];
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
}

// Set template variables
$page_title = "ชื่อหน้า";
$page_header_icon = '<i class="fas fa-icon me-3"></i>';
$back_button = true;
$back_url = 'index.php';
$back_text = 'กลับ';
$include_summernote = true; // ถ้าต้องการ Summernote editor

// Build content
ob_start();
?>
```

### 2. สร้างเนื้อหา HTML

```php
<!-- Your HTML content here -->
<div class="card-modern">
    <div class="card-header-modern">
        <h6><i class="fas fa-icon"></i>หัวข้อ</h6>
    </div>
    <div class="card-body-modern">
        <!-- Form or content -->
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
```

## 🎨 CSS Classes ที่มีให้ใช้

### Layout Classes
- `.wrapper` - Container หลัก
- `.sidebar` - Sidebar ด้านซ้าย
- `main` - Main content area

### Card Components
- `.card-modern` - Card container
- `.card-header-modern` - Card header
- `.card-body-modern` - Card body

### Form Elements
- `.form-control-modern` - Input fields
- `.form-select-modern` - Select dropdowns
- `.form-label-modern` - Labels

### Buttons
- `.btn-glass` - Glass effect buttons
- `.btn-primary-gradient` - Primary gradient buttons

### Alerts
- `.alert-modern` - Error alerts
- `.alert-success-modern` - Success alerts

### Tables
- `.table-modern` - Modern styled tables

### Status Badges
- `.status-badge` - Base status badge
- `.status-draft` - Draft status
- `.status-published` - Published status
- `.status-pending` - Pending status

## 📱 Responsive Design

Template รองรับการแสดงผลบนอุปกรณ์ทุกขนาด:
- **Desktop** - แสดง sidebar และ layout เต็ม
- **Mobile** - ซ่อน sidebar และปรับ layout ให้เหมาะสม

## 🎯 Features

### Built-in Features
- ✅ Glass Morphism Design
- ✅ Smooth Animations
- ✅ Responsive Layout
- ✅ Modern Typography
- ✅ Interactive Elements
- ✅ Loading States
- ✅ Error Handling

### JavaScript Utilities
- Summernote Editor Integration
- Image Preview
- Form Validation
- Modal Confirmations
- Auto-save Functionality

## 📝 ตัวอย่างการใช้งาน

### 1. หน้าสร้างข่าว (create.php)
- ✅ ใช้ template หลัก
- ✅ รวม Summernote editor
- ✅ อัพโหลดรูปภาพ
- ✅ ตัวอย่างรูปภาพ

### 2. หน้าแก้ไขข่าว (edit_new.php)
- ✅ ใช้ template หลัก
- ✅ โหลดข้อมูลที่มีอยู่
- ✅ จัดการรูปภาพแกลเลอรี่
- ✅ ลบรูปภาพ

### 3. หน้าจัดการข่าว (index_template_example.php)
- ✅ แสดงตารางข้อมูล
- ✅ ตัวกรองและค้นหา
- ✅ Pagination
- ✅ Modal ยืนยันการลบ

## 🔧 การปรับแต่ง

### เพิ่ม CSS เพิ่มเติม

```css
<style>
    /* Your custom styles */
    .custom-element {
        /* Custom styling */
    }
</style>
```

### เพิ่ม JavaScript

```javascript
<script>
    $(document).ready(function() {
        // Your custom JavaScript
    });
</script>
```

## 📋 Template Variables

| Variable | Type | Description | Default |
|----------|------|-------------|---------|
| `$page_title` | string | ชื่อหน้า | - |
| `$page_header_icon` | string | ไอคอนหน้า | `<i class="fas fa-cog me-3"></i>` |
| `$back_button` | boolean | แสดงปุ่มกลับ | `false` |
| `$back_url` | string | URL ปุ่มกลับ | `'index.php'` |
| `$back_text` | string | ข้อความปุ่มกลับ | `'กลับ'` |
| `$include_summernote` | boolean | รวม Summernote | `false` |

## 🎨 Color Scheme

### CSS Variables
```css
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.18);
    --shadow-soft: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    --shadow-hover: 0 15px 35px 0 rgba(31, 38, 135, 0.4);
}
```

## 📱 Browser Support

- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+

## 🔄 การอัปเดต

เมื่อต้องการอัปเดต template:
1. แก้ไข `template.php`
2. ทดสอบกับหน้าต่างๆ
3. อัปเดต documentation

## 🐛 การแก้ไขปัญหา

### ปัญหาทั่วไป

1. **CSS ไม่แสดงผล**
   - ตรวจสอบการรวม CSS files
   - ตรวจสอบ CSS specificity

2. **JavaScript ไม่ทำงาน**
   - ตรวจสอบการรวม jQuery
   - ตรวจสอบ event listeners

3. **Responsive ไม่ทำงาน**
   - ตรวจสอบ media queries
   - ตรวจสอบ viewport meta tag

## 📞 การสนับสนุน

สำหรับการสนับสนุนเพิ่มเติม กรุณาติดต่อทีมพัฒนา

---

**สร้างโดย:** Admin Template System
**เวอร์ชัน:** 1.0.0
**อัปเดตล่าสุด:** <?php echo date('d/m/Y'); ?>
