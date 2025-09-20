/**
 * Hover Dropdown Script for Bootstrap 5
 * Enables dropdown menus to show on hover instead of click
 */

document.addEventListener('DOMContentLoaded', function() {
    // Only apply hover behavior on desktop (screens wider than 992px)
    if (window.innerWidth >= 992) {
        initializeHoverDropdowns();
    }
    
    // Re-initialize on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth >= 992) {
                initializeHoverDropdowns();
            } else {
                removeHoverDropdowns();
            }
        }, 250);
    });
});

function initializeHoverDropdowns() {
    // Get all dropdown toggles
    const dropdownToggles = document.querySelectorAll('.navbar .dropdown-toggle');
    
    dropdownToggles.forEach(function(toggle) {
        const dropdown = toggle.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (!dropdown || !menu) return;
        
        let hoverTimer;
        let isHovering = false;
        
        // Show dropdown on mouse enter
        dropdown.addEventListener('mouseenter', function() {
            isHovering = true;
            clearTimeout(hoverTimer);
            
            // Close other dropdowns at the same level
            const siblings = dropdown.parentElement.querySelectorAll('.dropdown');
            siblings.forEach(function(sibling) {
                if (sibling !== dropdown) {
                    const siblingMenu = sibling.querySelector('.dropdown-menu');
                    if (siblingMenu) {
                        siblingMenu.classList.remove('show');
                        siblingMenu.style.display = 'none';
                    }
                }
            });
            
            // Show this dropdown
            menu.classList.add('show');
            menu.style.display = 'block';
            
            // Add aria attributes for accessibility
            toggle.setAttribute('aria-expanded', 'true');
        });
        
        // Hide dropdown on mouse leave (with delay)
        dropdown.addEventListener('mouseleave', function() {
            isHovering = false;
            hoverTimer = setTimeout(function() {
                if (!isHovering) {
                    menu.classList.remove('show');
                    menu.style.display = 'none';
                    toggle.setAttribute('aria-expanded', 'false');
                }
            }, 300); // 300ms delay before hiding
        });
        
        // Prevent click from toggling the dropdown
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth >= 992) {
                // If the dropdown is already open, allow navigation to link
                if (menu.classList.contains('show') && toggle.getAttribute('href') && toggle.getAttribute('href') !== '#') {
                    return true;
                }
                // Otherwise prevent default click behavior
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
    
    // Handle nested dropdowns (submenus)
    handleNestedDropdowns();
    
    // Handle new section navbar
    handleNewSectionNavbar();
}

function handleNestedDropdowns() {
    const submenus = document.querySelectorAll('.dropdown-submenu');
    
    submenus.forEach(function(submenu) {
        const toggle = submenu.querySelector('.dropdown-toggle');
        const menu = submenu.querySelector('.dropdown-menu');
        
        if (!toggle || !menu) return;
        
        let hoverTimer;
        
        submenu.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimer);
            menu.classList.add('show');
            menu.style.display = 'block';
            menu.style.position = 'absolute';
            menu.style.left = '100%';
            menu.style.top = '0';
        });
        
        submenu.addEventListener('mouseleave', function() {
            hoverTimer = setTimeout(function() {
                menu.classList.remove('show');
                menu.style.display = 'none';
            }, 300);
        });
        
        // Prevent click on submenu toggle
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth >= 992) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
}

function handleNewSectionNavbar() {
    // Special handling for new section navbar
    const newSectionDropdowns = document.querySelectorAll('.new-section-navbar .dropdown');
    
    newSectionDropdowns.forEach(function(dropdown) {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (!toggle || !menu) return;
        
        let hoverTimer;
        
        dropdown.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimer);
            
            // Close other dropdowns in new section navbar
            const siblings = dropdown.parentElement.querySelectorAll('.dropdown');
            siblings.forEach(function(sibling) {
                if (sibling !== dropdown) {
                    const siblingMenu = sibling.querySelector('.dropdown-menu');
                    if (siblingMenu) {
                        siblingMenu.classList.remove('show');
                        siblingMenu.style.display = 'none';
                    }
                }
            });
            
            menu.classList.add('show');
            menu.style.display = 'block';
            toggle.setAttribute('aria-expanded', 'true');
        });
        
        dropdown.addEventListener('mouseleave', function() {
            hoverTimer = setTimeout(function() {
                menu.classList.remove('show');
                menu.style.display = 'none';
                toggle.setAttribute('aria-expanded', 'false');
            }, 300);
        });
        
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth >= 992) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
}

function removeHoverDropdowns() {
    // Reset to default Bootstrap behavior for mobile
    const dropdownToggles = document.querySelectorAll('.navbar .dropdown-toggle');
    
    dropdownToggles.forEach(function(toggle) {
        const dropdown = toggle.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (menu) {
            menu.classList.remove('show');
            menu.style.display = '';
        }
    });
}

// Utility function to close all dropdowns
function closeAllDropdowns() {
    const openMenus = document.querySelectorAll('.navbar .dropdown-menu.show');
    openMenus.forEach(function(menu) {
        menu.classList.remove('show');
        menu.style.display = 'none';
        const toggle = menu.previousElementSibling;
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (window.innerWidth >= 992) {
        if (!e.target.closest('.navbar')) {
            closeAllDropdowns();
        }
    }
});

// Keyboard navigation support
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllDropdowns();
    }
});
