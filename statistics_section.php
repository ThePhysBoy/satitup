<!-- Statistics Section -->
<section class="statistics-section py-4">
    <div class="container">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12 text-center">
                <h2 class="section-title mb-1">ข้อมูลสถิติโรงเรียนสาธิตมหาวิทยาลัยพะเยา</h2>
                <p class="section-subtitle mb-2">ปีการศึกษา 2568</p>
            </div>
        </div>

        <!-- Main Statistics Grid -->
        <div class="row g-3">
            <!-- Student Statistics -->
            <div class="col-lg-8 col-md-7">
                <div class="stats-card h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper purple">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="stat-number mb-0" data-count="761">0</h3>
                            <p class="stat-label mb-0">นักเรียนทั้งหมด</p>
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <div class="sub-stat">
                                <span class="sub-stat-number" data-count="133">0</span>
                                <span class="sub-stat-label">ประถมศึกษา</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="sub-stat">
                                <span class="sub-stat-number" data-count="203">0</span>
                                <span class="sub-stat-label">มัธยมต้น</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="sub-stat">
                                <span class="sub-stat-number" data-count="248">0</span>
                                <span class="sub-stat-label">ห้องปกติ</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="sub-stat">
                                <span class="sub-stat-number" data-count="177">0</span>
                                <span class="sub-stat-label">ห้องโครงการ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Statistics -->
            <div class="col-lg-4 col-md-5">
                <div class="stats-card h-100">
                    <div class="d-flex align-items-center h-100">
                        <div class="stat-icon-wrapper lavender">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="stat-number mb-0" data-count="130">0</h3>
                            <p class="stat-label mb-0">บุคลากรทั้งหมด</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Year Info -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="academic-info text-center">
                    <small><i class="fas fa-calendar-alt me-1"></i> ข้อมูล ณ วันที่ 2 ปีการศึกษา 2568</small>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function animateCounter(element, target, duration = 1500) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start);
            }
        }, 16);
    }

    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('[data-count]');
                statNumbers.forEach(number => {
                    const target = parseInt(number.getAttribute('data-count'));
                    animateCounter(number, target);
                });
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const statCards = document.querySelectorAll('.stats-card');
    statCards.forEach(card => observer.observe(card));
});
</script>

<style>
/* Statistics Section - Purple Soft Theme */
.statistics-section {
    background: linear-gradient(135deg, #e0c3fc 0%, #d5b9f5 50%, #c5a8f0 100%);
    position: relative;
    overflow: hidden;
}

.statistics-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px);
    background-size: 30px 30px;
    opacity: 0.5;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #5a3d7a;
    text-shadow: 0 2px 4px rgba(255,255,255,0.5);
}

.section-subtitle {
    font-size: 0.9rem;
    color: #6b4d8a;
    font-weight: 500;
}

.stats-card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 4px 15px rgba(138, 99, 186, 0.15);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(138, 99, 186, 0.25);
}

.stat-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.stat-icon-wrapper.purple {
    background: linear-gradient(135deg, #b794f6, #9f7aea);
    color: white;
}

.stat-icon-wrapper.lavender {
    background: linear-gradient(135deg, #d5b9f5, #b794f6);
    color: white;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #7c3aed, #9f7aea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.85rem;
    color: #6b4d8a;
    font-weight: 600;
}

.sub-stat {
    background: linear-gradient(135deg, rgba(183, 148, 246, 0.15), rgba(213, 185, 245, 0.15));
    padding: 0.6rem 0.5rem;
    border-radius: 8px;
    text-align: center;
    border: 1px solid rgba(183, 148, 246, 0.2);
    transition: all 0.3s ease;
}

.sub-stat:hover {
    background: linear-gradient(135deg, rgba(183, 148, 246, 0.25), rgba(213, 185, 245, 0.25));
    transform: scale(1.02);
}

.sub-stat-number {
    font-size: 1.3rem;
    font-weight: 700;
    display: block;
    color: #7c3aed;
    line-height: 1.2;
}

.sub-stat-label {
    font-size: 0.7rem;
    color: #6b4d8a;
    font-weight: 500;
    display: block;
    margin-top: 0.2rem;
}

.academic-info {
    padding: 0.6rem 1rem;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 8px;
    border: 1px solid rgba(183, 148, 246, 0.3);
    position: relative;
    z-index: 1;
}

.academic-info small {
    color: #6b4d8a;
    font-weight: 500;
    font-size: 0.8rem;
}

.academic-info i {
    color: #9f7aea;
}

/* Responsive Design */
@media (max-width: 768px) {
    .section-title {
        font-size: 1.2rem;
    }
    
    .section-subtitle {
        font-size: 0.8rem;
    }

    .stat-number {
        font-size: 1.6rem;
    }

    .stat-icon-wrapper {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }

    .stats-card {
        padding: 1rem;
    }
    
    .sub-stat-number {
        font-size: 1.1rem;
    }
    
    .sub-stat-label {
        font-size: 0.65rem;
    }
}

@media (max-width: 576px) {
    .stat-label {
        font-size: 0.75rem;
    }
    
    .sub-stat {
        padding: 0.5rem 0.3rem;
    }
}
</style>