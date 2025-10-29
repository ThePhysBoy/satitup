<?php
/**
 * Edit News - Modern Template
 */

$conn = require_once '../includes/db_config.php';
require_once '../includes/auth_functions.php';
require_once 'news_functions.php';

requireLogin();
if (!isAdmin() && !isPrOfficer()) { header("Location: ../index.php"); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: index.php"); exit; }

$news = getNewsById($id, $conn);
if (!$news) { header("Location: index.php"); exit; }

// ไม่ต้องดึงข้อมูลหมวดหมู่แล้ว เพราะเราไม่ใช้หมวดหมู่

// Handle updates
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$title = trim($_POST['title'] ?? '');
	$excerpt = trim($_POST['excerpt'] ?? '');
	$content = trim($_POST['content'] ?? '');
	$status = $_POST['status'] ?? 'draft';
	$sdg_goals = isset($_POST['sdg_goals']) ? implode(',', $_POST['sdg_goals']) : '';

	if ($title === '') $errors['title'] = 'กรุณากรอกหัวข้อข่าว';
	if ($content === '') $errors['content'] = 'กรุณากรอกเนื้อหาข่าว';

	// Featured image upload (optional)
	$featured_image = $news['featured_image'];
	if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
		$upload = uploadImage($_FILES['featured_image'], 'news/featured');
		if ($upload['success']) {
			// delete old
			if (!empty($featured_image) && file_exists('../../' . $featured_image)) { @unlink('../../' . $featured_image); }
			$featured_image = $upload['path'];
		} else {
			$errors['featured_image'] = $upload['error'];
		}
	}

	if (empty($errors)) {
		$published_at = $news['published_at'];
		if ($status === 'published' && empty($published_at)) {
			$published_at = date('Y-m-d H:i:s');
		}
		$stmt = $conn->prepare("UPDATE news SET title=?, excerpt=?, content=?, status=?, featured_image=?, published_at=?, sdg_goals=?, updated_at=NOW() WHERE id=?");
		$stmt->bind_param('sssssssi', $title, $excerpt, $content, $status, $featured_image, $published_at, $sdg_goals, $id);
		if ($stmt->execute()) {
			// Handle gallery uploads
			if (isset($_FILES['images'])) {
				$images = reArrayFiles($_FILES['images']);
				foreach ($images as $index => $image) {
					if ($image['error'] === 0) {
						$up = uploadImage($image, 'news/gallery');
						if ($up['success']) {
							$path = $up['path'];
							$caption = $_POST['captions'][$index] ?? '';
							$order = (int)($_POST['orders'][$index] ?? ($index+1));
							$s2 = $conn->prepare("INSERT INTO news_images (news_id, image_path, caption, display_order) VALUES (?, ?, ?, ?)");
							$s2->bind_param('issi', $id, $path, $caption, $order);
							$s2->execute();
						}
					}
				}
			}
			header("Location: edit_new.php?id=$id&success=1"); exit;
		} else {
			$errors['database'] = 'บันทึกไม่สำเร็จ: ' . $conn->error;
		}
	}
	// refresh news
	$news = getNewsById($id, $conn);
}

$images = getNewsImages($id, $conn);
$page_title = 'แก้ไขข่าว';
$page_header_icon = '<i class="fas fa-edit me-2"></i>';
$back_button = true;
$back_button_url = 'index.php';
$back_button_text = 'กลับไปหน้ารายการข่าว';

// Build content
ob_start();
?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i> บันทึกข้อมูลเรียบร้อยแล้ว
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i> พบข้อผิดพลาด:
    <ul class="mb-0 mt-2">
        <?php foreach($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-end">
            <a href="../../news/detail.php?slug=<?php echo urlencode($news['slug']); ?>" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-external-link-alt me-1"></i> ดูหน้าเว็บ
            </a>
        </div>
    </div>
</div>

<form method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-8">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <h5><i class="fas fa-newspaper me-2"></i>รายละเอียดข่าว</h5>
                </div>
                <div class="card-body-modern">
                    <div class="mb-3">
                        <label class="form-label-modern">หัวข้อข่าว</label>
                        <input class="form-control form-control-modern" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-modern">สรุปย่อ</label>
                        <textarea class="form-control form-control-modern" name="excerpt" rows="3"><?php echo htmlspecialchars($news['excerpt']); ?></textarea>
                        <div class="form-text">สรุปย่อจะแสดงในหน้ารายการข่าวและด้านบนของเนื้อหาข่าว</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-modern">เนื้อหา</label>
                        <textarea id="content" class="form-control form-control-modern" name="content" rows="12"><?php echo htmlspecialchars($news['content']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <h5><i class="fas fa-images me-2"></i>แกลเลอรี่รูปภาพ</h5>
                </div>
                <div class="card-body-modern">
                    <?php if (count($images) > 0): ?>
                    <div class="row g-3 mb-4">
                        <?php foreach ($images as $img): ?>
                        <div class="col-md-3">
                            <div class="position-relative">
                                <img src="../../<?php echo htmlspecialchars($img['image_path']); ?>" class="img-fluid rounded" style="width: 100%; height: 120px; object-fit: contain; background-color: #f8f9fa;" alt="">
                                <div class="position-absolute top-0 end-0 p-2">
                                    <form method="post" action="delete_image.php" onsubmit="return confirm('ยืนยันการลบรูปภาพนี้?');">
                                        <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger rounded-circle">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="mt-2 text-center small">
                                    <span class="badge bg-secondary">ลำดับ: <?php echo (int)$img['display_order']; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> ยังไม่มีรูปภาพในแกลเลอรี่
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label-modern">เพิ่มรูปภาพใหม่</label>
                        <input type="file" class="form-control form-control-modern" name="images[]" multiple accept="image/*">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i> รองรับหลายรูปภาพ (JPG, PNG, GIF, WEBP)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <h5><i class="fas fa-paper-plane me-2"></i>การเผยแพร่</h5>
                </div>
                <div class="card-body-modern">
                    <div class="mb-3">
                        <label class="form-label-modern">สถานะ</label>
                        <select class="form-select form-select-modern" name="status">
                            <option value="draft" <?php echo $news['status']==='draft'?'selected':''; ?>>ฉบับร่าง</option>
                            <option value="published" <?php echo $news['status']==='published'?'selected':''; ?>>เผยแพร่</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-modern">วันที่สร้าง</label>
                        <input type="text" class="form-control form-control-modern" value="<?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-modern">วันที่แก้ไขล่าสุด</label>
                        <input type="text" class="form-control form-control-modern" value="<?php echo date('d/m/Y H:i', strtotime($news['updated_at'])); ?>" readonly>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-save me-2"></i>บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- SDG Goals Card -->
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <h5><i class="fas fa-globe me-2"></i>เป้าหมายการพัฒนาที่ยั่งยืน (SDGs)</h5>
                </div>
                <div class="card-body-modern">
                    <small class="text-muted d-block mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        สามารถเลือกได้หลายเป้าหมาย
                    </small>
                    <div class="sdg-grid">
                        <?php
                        $sdg_list = [
                            1 => ['name' => 'ขจัดความยากจน', 'color' => '#E5243B'],
                            2 => ['name' => 'ขจัดความหิวโหย', 'color' => '#DDA63A'],
                            3 => ['name' => 'สุขภาพและความเป็นอยู่ที่ดี', 'color' => '#4C9F38'],
                            4 => ['name' => 'การศึกษาที่มีคุณภาพ', 'color' => '#C5192D'],
                            5 => ['name' => 'ความเท่าเทียมทางเพศ', 'color' => '#FF3A21'],
                            6 => ['name' => 'น้ำสะอาดและสุขาภิบาล', 'color' => '#26BDE2'],
                            7 => ['name' => 'พลังงานสะอาดที่เข้าถึงได้', 'color' => '#FCC30B'],
                            8 => ['name' => 'งานที่มีคุณค่าและการเติบโตทางเศรษฐกิจ', 'color' => '#A21942'],
                            9 => ['name' => 'อุตสาหกรรม นวัตกรรม และโครงสร้างพื้นฐาน', 'color' => '#FD6925'],
                            10 => ['name' => 'ลดความเหลื่อมล้ำ', 'color' => '#DD1367'],
                            11 => ['name' => 'เมืองและชุมชนที่ยั่งยืน', 'color' => '#FD9D24'],
                            12 => ['name' => 'การบริโภคและการผลิตที่ยั่งยืน', 'color' => '#BF8B2E'],
                            13 => ['name' => 'การดำเนินการด้านสภาพภูมิอากาศ', 'color' => '#3F7E44'],
                            14 => ['name' => 'ชีวิตใต้น้ำ', 'color' => '#0A97D9'],
                            15 => ['name' => 'ชีวิตบนบก', 'color' => '#56C02B'],
                            16 => ['name' => 'สันติภาพ ความยุติธรรม และสถาบันที่เข้มแข็ง', 'color' => '#00689D'],
                            17 => ['name' => 'ความร่วมมือเพื่อบรรลุเป้าหมาย', 'color' => '#19486A']
                        ];
                        
                        $selected_sdgs = !empty($news['sdg_goals']) ? explode(',', $news['sdg_goals']) : [];
                        foreach ($sdg_list as $num => $sdg):
                        ?>
                        <div class="sdg-item">
                            <input type="checkbox" class="sdg-checkbox" id="sdg_<?php echo $num; ?>" 
                                   name="sdg_goals[]" value="<?php echo $num; ?>"
                                   <?php echo in_array($num, $selected_sdgs) ? 'checked' : ''; ?>>
                            <label for="sdg_<?php echo $num; ?>" class="sdg-label" style="background-color: <?php echo $sdg['color']; ?>">
                                <span class="sdg-number"><?php echo $num; ?></span>
                                <span class="sdg-name"><?php echo $sdg['name']; ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <h5><i class="fas fa-image me-2"></i>รูปภาพหลัก</h5>
                </div>
                <div class="card-body-modern">
                    <?php if (!empty($news['featured_image'])): ?>
                    <div class="text-center mb-3">
                        <img src="../../<?php echo htmlspecialchars($news['featured_image']); ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;" alt="">
                    </div>
                    <div class="d-grid mb-3">
                        <form method="post" action="delete_featured_image.php" onsubmit="return confirm('ยืนยันการลบรูปภาพหลัก?');">
                            <input type="hidden" name="news_id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-trash me-1"></i> ลบรูปภาพหลัก
                            </button>
                        </form>
                    </div>
                    <hr>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label-modern">อัพโหลดรูปภาพหลักใหม่</label>
                        <input type="file" class="form-control form-control-modern" name="featured_image" accept="image/*">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i> ขนาดแนะนำ 1200x630 พิกเซล
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
// Set summernote flag for template
$include_summernote = true;

// Add custom styles for SDG
$custom_styles = <<<EOT
/* SDG Grid Styles */
.sdg-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 8px;
    margin-top: 10px;
}

.sdg-item {
    position: relative;
}

.sdg-checkbox {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.sdg-label {
    display: flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 8px;
    cursor: pointer;
    color: white;
    transition: all 0.3s ease;
    opacity: 0.7;
    border: 2px solid transparent;
    font-size: 12px;
}

.sdg-checkbox:checked + .sdg-label {
    opacity: 1;
    border-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.sdg-label:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.sdg-number {
    font-weight: bold;
    font-size: 16px;
    margin-right: 6px;
    min-width: 20px;
    text-align: center;
}

.sdg-name {
    font-size: 10px;
    line-height: 1.2;
    flex: 1;
}
EOT;

// Add custom scripts for this page
$custom_scripts = <<<EOT
<script>
    $(document).ready(function() {
        console.log('jQuery version:', $.fn.jquery);
        console.log('Summernote element exists:', $('#content').length);

        // Initialize Summernote with delay to ensure DOM is ready
        setTimeout(function() {
            if (typeof $.fn.summernote === 'undefined') {
                console.error('Summernote is not loaded');
                return;
            }

            $('#content').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        // You could implement image upload here if needed
                        for (let i = 0; i < files.length; i++) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                $('#content').summernote('insertNode', img);
                            };
                            reader.readAsDataURL(files[i]);
                        }
                    }
                }
            });
            console.log('Summernote initialized successfully');
        }, 100);

        // Add any additional custom functionality here if needed
    });
</script>
EOT;

$content = ob_get_clean();

// Include the template
include 'template.php';
?>