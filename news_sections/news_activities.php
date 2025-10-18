        <!-- สไตล์สำหรับภาพข่าวกิจกรรม -->
        <style>
            /* สไตล์สำหรับการ์ดข่าวกิจกรรม */
            .news-activity-card {
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                margin-bottom: 30px;
                transition: all 0.3s ease;
                background-color: #fff;
            }
            
            .news-activity-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(123, 59, 149, 0.2);
            }
            
            .news-activity-image {
                position: relative;
                overflow: hidden;
                height: 240px;
            }
            
            .news-activity-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }
            
            .news-activity-card:hover .news-activity-image img {
                transform: scale(1.05);
            }
            
            .news-activity-date {
                position: absolute;
                top: 15px;
                right: 15px;
                background: rgba(123, 59, 149, 0.85);
                color: white;
                padding: 8px 15px;
                border-radius: 20px;
                font-weight: 500;
                font-size: 0.9rem;
                backdrop-filter: blur(5px);
            }
            
            .news-activity-content {
                padding: 20px;
            }
            
            .news-activity-title {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 10px;
                color: #333;
                line-height: 1.4;
            }
            
            .news-activity-title a {
                color: #333;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .news-activity-title a:hover {
                color: #7b3b95;
            }
            
            .news-activity-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #666;
                font-size: 0.9rem;
                margin-top: 15px;
            }
            
            .news-activity-author {
                display: flex;
                align-items: center;
            }
            
            .news-activity-author i {
                margin-right: 5px;
                color: #7b3b95;
            }
            
            /* แอนิเมชันสำหรับการ์ดข่าวกิจกรรม */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-card {
                animation: fadeInUp 0.6s ease forwards;
            }
            
            /* ปรับการแสดงผลบนอุปกรณ์มือถือ */
            @media (max-width: 767px) {
                .news-activity-image {
                    height: 180px;
                }
                
                .news-activity-content {
                    padding: 15px;
                }
                
                .news-activity-title {
                    font-size: 1.1rem;
                }
            }
        </style>
        
        <!-- เนื้อหาของแต่ละแท็บ -->
        <div class="tab-content" id="newsTabContent">
            <!-- เนื้อหาแท็บ: ภาพข่าวกิจกรรม (แสดงเริ่มต้น) -->
            <div class="tab-pane fade show active" id="news-activities" role="tabpanel">
                <div class="row g-4">
<?php if (!empty($latest)): ?>
<?php foreach ($latest as $index => $item): ?>
<?php $colClass = ($index < 2) ? 'col-lg-6' : 'col-lg-4'; ?>
                    <div class="<?php echo $colClass; ?>">
                        <div class="news-activity-card">
                            <div class="news-activity-image">
                                <?php
                                // ตรวจสอบและแก้ไข path รูปภาพ
                                $image_path = '';
                                if (!empty($item['featured_image'])) {
                                    $image_path = $item['featured_image'];
                                    // ตัด admin/ ออกจาก path ถ้ามี
                                    if (strpos($image_path, 'admin/') === 0) {
                                        $image_path = substr($image_path, 6);
                                    }
                                } else {
                                    $image_path = 'images/comingsoon.png';
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <div class="news-activity-date"><?php echo date('d/m/Y', strtotime($item['published_at'] ?: $item['created_at'])); ?></div>
                                <?php if (!empty($item['category_name'])): ?>
                                <div class="news-category"><?php echo htmlspecialchars($item['category_name']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="news-activity-content">
                                <h3 class="news-activity-title">
                                    <?php if (!empty($item['slug'])): ?>
                                    <a href="news/detail.php?slug=<?php echo urlencode($item['slug']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                                    <?php else: ?>
                                    <a href="news/detail.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                                    <?php endif; ?>
                                </h3>
                                <?php if (!empty($item['excerpt'])): ?>
                                <p class="news-excerpt"><?php echo htmlspecialchars(mb_substr($item['excerpt'], 0, 150)) . '...'; ?></p>
                                <?php endif; ?>
                                <div class="news-activity-meta">
                                    <span class="news-activity-author">
                                        <i class="fas fa-user"></i> โดย <?php echo htmlspecialchars($item['full_name'] ?? $item['username'] ?? 'ระบบ'); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-eye"></i> <?php echo number_format((int)($item['view_count'] ?? 0)); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
<?php endforeach; ?>
<?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            ยังไม่มีข่าวเผยแพร่ หรือกรุณาตรวจสอบการเชื่อมต่อฐานข้อมูล
                        </div>
                    </div>
<?php endif; ?>
                </div>
                
                <!-- ปุ่มดูทั้งหมด -->
                <div class="text-center mt-4">
                    <a href="news/index.php" class="btn btn-view-all">
                        ข่าวทั้งหมด <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
