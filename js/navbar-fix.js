/**
 * Navbar Fix JavaScript
 * แก้ไขปัญหาการทำงานของ Navigation Bar
 */

$(document).ready(function() {
    // Initialize Bootstrap 5 components
    
    // Enable all tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Enable all popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Fix dropdown submenu for multi-level navigation
    $('.dropdown-submenu > a').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $submenu = $(this).next('.dropdown-menu');
        
        // Close other submenus at the same level
        $(this).closest('.dropdown-menu').find('.dropdown-menu.show').not($submenu).removeClass('show');
        
        // Toggle current submenu
        $submenu.toggleClass('show');
        
        return false;
    });
    
    // Close submenu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown-submenu').length) {
            $('.dropdown-submenu .dropdown-menu').removeClass('show');
        }
    });
    
    // Handle dropdown on hover for desktop only
    if (window.innerWidth > 991) {
        // Main navbar dropdowns
        $('.main-navbar .dropdown').hover(
            function() {
                var $dropdown = $(this);
                $dropdown.find('.dropdown-menu').first().stop(true, true).addClass('show');
                $dropdown.find('.dropdown-toggle').attr('aria-expanded', 'true');
            },
            function() {
                var $dropdown = $(this);
                $dropdown.find('.dropdown-menu').first().stop(true, true).removeClass('show');
                $dropdown.find('.dropdown-toggle').attr('aria-expanded', 'false');
            }
        );
        
        // Secondary navbar dropdowns
        $('.secondary-navbar .dropdown, .new-section-navbar .dropdown').hover(
            function() {
                var $dropdown = $(this);
                $dropdown.find('.dropdown-menu').first().stop(true, true).addClass('show');
                $dropdown.find('.dropdown-toggle').attr('aria-expanded', 'true');
            },
            function() {
                var $dropdown = $(this);
                $dropdown.find('.dropdown-menu').first().stop(true, true).removeClass('show');
                $dropdown.find('.dropdown-toggle').attr('aria-expanded', 'false');
            }
        );
    }
    
    // Ensure dropdowns work on click for mobile
    $('.dropdown-toggle').on('click', function(e) {
        if (window.innerWidth <= 991) {
            e.preventDefault();
            var $dropdown = $(this).parent();
            var $menu = $dropdown.find('.dropdown-menu').first();
            
            // Toggle dropdown
            $menu.toggleClass('show');
            $(this).attr('aria-expanded', $menu.hasClass('show'));
            
            // Close other dropdowns
            $('.dropdown-menu.show').not($menu).removeClass('show');
            $('.dropdown-toggle[aria-expanded="true"]').not(this).attr('aria-expanded', 'false');
        }
    });
    
    // Fix for sticky navbar
    var navbar = $('.main-navbar');
    if (navbar.length) {
        var navbarOffset = navbar.offset().top;
        
        $(window).scroll(function() {
            if ($(window).scrollTop() >= navbarOffset) {
                navbar.addClass('fixed-top');
                $('body').css('padding-top', navbar.outerHeight());
            } else {
                navbar.removeClass('fixed-top');
                $('body').css('padding-top', 0);
            }
        });
    }
});

// Fallback for browsers that don't support certain features
if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var el = this;
        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}
