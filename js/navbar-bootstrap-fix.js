/**
 * Navbar Bootstrap Fix
 * แก้ไขปัญหา dropdown ไม่ทำงานใน navbar
 */

// รอให้ทุกอย่างโหลดเสร็จก่อน
window.addEventListener('load', function() {
    console.log('Starting navbar fix...');
    
    // ตรวจสอบว่า Bootstrap โหลดหรือยัง
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap not loaded!');
        return;
    }
    
    console.log('Bootstrap version:', bootstrap.Tooltip.VERSION);
    
    // Initialize all dropdowns manually
    initializeDropdowns();
    
    // Fix for navbar dropdowns
    fixNavbarDropdowns();
    
    // Fix for new section navbar
    fixNewSectionNavbar();
});

function initializeDropdowns() {
    // ค้นหา dropdown ทั้งหมด
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    
    dropdownElementList.forEach(function(dropdownToggleEl) {
        // ลบ event listeners เก่าออกก่อน
        var newEl = dropdownToggleEl.cloneNode(true);
        dropdownToggleEl.parentNode.replaceChild(newEl, dropdownToggleEl);
        
        // สร้าง dropdown instance ใหม่
        new bootstrap.Dropdown(newEl);
    });
    
    console.log('Initialized', dropdownElementList.length, 'dropdowns');
}

function fixNavbarDropdowns() {
    // แก้ไข main navbar dropdowns
    document.querySelectorAll('.main-navbar .dropdown').forEach(function(dropdown) {
        var toggle = dropdown.querySelector('.dropdown-toggle');
        var menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            // Click event
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle menu
                if (menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    // Close other menus first
                    closeAllDropdowns();
                    menu.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });
    
    // แก้ไข secondary navbar dropdowns
    document.querySelectorAll('.secondary-navbar .dropdown').forEach(function(dropdown) {
        var toggle = dropdown.querySelector('.dropdown-toggle');
        var menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    // Close other menus in secondary navbar
                    document.querySelectorAll('.secondary-navbar .dropdown-menu.show').forEach(function(openMenu) {
                        openMenu.classList.remove('show');
                    });
                    menu.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });
}

function fixNewSectionNavbar() {
    // แก้ไข new section navbar dropdowns
    document.querySelectorAll('.new-section-navbar .dropdown').forEach(function(dropdown) {
        var toggle = dropdown.querySelector('.dropdown-toggle');
        var menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    // Close other menus in new section navbar
                    document.querySelectorAll('.new-section-navbar .dropdown-menu.show').forEach(function(openMenu) {
                        openMenu.classList.remove('show');
                    });
                    menu.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
        menu.classList.remove('show');
    });
    document.querySelectorAll('.dropdown-toggle[aria-expanded="true"]').forEach(function(toggle) {
        toggle.setAttribute('aria-expanded', 'false');
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        closeAllDropdowns();
    }
});

// Handle submenu (multi-level dropdown)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-submenu > a').forEach(function(submenuToggle) {
        submenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('dropdown-menu')) {
                // Close other submenus
                this.closest('.dropdown-menu').querySelectorAll('.dropdown-menu.show').forEach(function(openSubmenu) {
                    if (openSubmenu !== submenu) {
                        openSubmenu.classList.remove('show');
                    }
                });
                
                // Toggle this submenu
                submenu.classList.toggle('show');
            }
        });
    });
});

// Add hover effect for desktop
if (window.innerWidth > 991) {
    document.querySelectorAll('.navbar .dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('mouseenter', function() {
            var menu = this.querySelector('.dropdown-menu');
            if (menu && !menu.classList.contains('show')) {
                menu.classList.add('show');
                this.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'true');
            }
        });
        
        dropdown.addEventListener('mouseleave', function() {
            var menu = this.querySelector('.dropdown-menu');
            if (menu) {
                menu.classList.remove('show');
                this.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
            }
        });
    });
}

console.log('Navbar Bootstrap Fix loaded');
