/**
 * News and Announcements JavaScript
 * จาวาสคริปต์สำหรับส่วนข่าวสารและประกาศ
 */

document.addEventListener('DOMContentLoaded', function() {
    // เริ่มต้น Bootstrap tabs
    initializeTabs();
    
    // เพิ่มแอนิเมชันการโหลด
    initializeAnimations();
    
    // เพิ่มการทำงานของ lazy loading สำหรับรูปภาพ
    initializeLazyLoading();
    
    // เพิ่มการค้นหาข่าว
    initializeSearch();
});

/**
 * เริ่มต้นการทำงานของ Bootstrap tabs
 */
function initializeTabs() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#newsTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
            
            // เพิ่มแอนิเมชันเมื่อเปลี่ยนแท็บ
            var targetPane = document.querySelector(triggerEl.getAttribute('data-bs-target'));
            if (targetPane) {
                targetPane.style.opacity = '0';
                setTimeout(function() {
                    targetPane.style.opacity = '1';
                }, 150);
            }
        });
    });
}

/**
 * เริ่มต้นแอนิเมชันการโหลด
 */
function initializeAnimations() {
    // แอนิเมชันสำหรับการ์ดข่าว
    var newsCards = document.querySelectorAll('.news-card');
    newsCards.forEach(function(card, index) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(function() {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // แอนิเมชันสำหรับรายการประกาศ
    var announcementItems = document.querySelectorAll('.list-group-item');
    announcementItems.forEach(function(item, index) {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(function() {
            item.style.transition = 'all 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, index * 50);
    });
}

/**
 * เริ่มต้น lazy loading สำหรับรูปภาพ
 */
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(function(img) {
            imageObserver.observe(img);
        });
    }
}

/**
 * เริ่มต้นการค้นหาข่าว
 */
function initializeSearch() {
    // สร้างช่องค้นหา (ถ้าต้องการ)
    var searchInput = document.querySelector('#newsSearch');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            var searchTerm = e.target.value.toLowerCase();
            filterNews(searchTerm);
        }, 300));
    }
}

/**
 * กรองข่าวตามคำค้นหา
 */
function filterNews(searchTerm) {
    var newsCards = document.querySelectorAll('.news-card');
    
    newsCards.forEach(function(card) {
        var title = card.querySelector('.news-title a').textContent.toLowerCase();
        var excerpt = card.querySelector('.news-excerpt').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || excerpt.includes(searchTerm) || searchTerm === '') {
            card.style.display = 'block';
            card.style.opacity = '1';
        } else {
            card.style.display = 'none';
            card.style.opacity = '0';
        }
    });
}

/**
 * Debounce function สำหรับการค้นหา
 */
function debounce(func, wait) {
    var timeout;
    return function executedFunction() {
        var later = function() {
            clearTimeout(timeout);
            func.apply(this, arguments);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * เพิ่มการทำงานของปุ่ม "ดูทั้งหมด"
 */
function setupViewAllButtons() {
    var viewAllButtons = document.querySelectorAll('.btn-view-all');
    
    viewAllButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            // เพิ่มเอฟเฟกต์ loading
            var originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลด...';
            this.disabled = true;
            
            // จำลองการโหลด
            setTimeout(function() {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 1000);
        });
    });
}

/**
 * เพิ่มการแสดงผลข้อมูลเพิ่มเติมเมื่อ hover
 */
function setupHoverEffects() {
    var newsCards = document.querySelectorAll('.news-card');
    
    newsCards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            // เพิ่มข้อมูลเพิ่มเติม
            var metaInfo = this.querySelector('.news-meta');
            if (metaInfo && !metaInfo.querySelector('.read-time')) {
                var readTime = document.createElement('span');
                readTime.className = 'read-time';
                readTime.innerHTML = '<i class="fas fa-clock"></i> 3 นาที';
                metaInfo.appendChild(readTime);
            }
        });
    });
}

/**
 * จัดการการแสดงผลประกาศตามความสำคัญ
 */
function prioritizeAnnouncements() {
    var highPriorityItems = document.querySelectorAll('.list-group-item.high-priority');
    
    highPriorityItems.forEach(function(item) {
        // เพิ่มไอคอนแจ้งเตือน
        var title = item.querySelector('.announcement-title');
        if (title && !title.querySelector('.priority-icon')) {
            var icon = document.createElement('i');
            icon.className = 'fas fa-exclamation-triangle priority-icon text-danger me-2';
            title.insertBefore(icon, title.firstChild);
        }
        
        // เพิ่มเอฟเฟกต์กระพริบ
        item.style.animation = 'pulse 2s infinite';
    });
}

/**
 * เพิ่ม CSS สำหรับแอนิเมชัน
 */
function addCustomStyles() {
    var style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
        
        .news-card {
            transition: all 0.3s ease;
        }
        
        .news-card:hover {
            transform: translateY(-8px);
        }
        
        .lazy {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .lazy.loaded {
            opacity: 1;
        }
        
        .read-time {
            color: #999;
            font-size: 0.8rem;
        }
        
        .priority-icon {
            animation: blink 1s linear infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }
    `;
    document.head.appendChild(style);
}

// เรียกใช้ฟังก์ชันเพิ่มเติม
document.addEventListener('DOMContentLoaded', function() {
    setupViewAllButtons();
    setupHoverEffects();
    prioritizeAnnouncements();
    addCustomStyles();
});

/**
 * ฟังก์ชันสำหรับโหลดข้อมูลข่าวแบบ AJAX
 */
function loadMoreNews(page = 1, category = 'all') {
    return fetch(`api/news.php?page=${page}&category=${category}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.news;
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error loading news:', error);
            return [];
        });
}

/**
 * ฟังก์ชันสำหรับโหลดข้อมูลประกาศแบบ AJAX
 */
function loadMoreAnnouncements(page = 1, type = 'all') {
    return fetch(`api/announcements.php?page=${page}&type=${type}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.announcements;
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error loading announcements:', error);
            return [];
        });
}

// Export functions สำหรับใช้ในไฟล์อื่น
window.NewsAnnouncements = {
    loadMoreNews: loadMoreNews,
    loadMoreAnnouncements: loadMoreAnnouncements,
    filterNews: filterNews
};
