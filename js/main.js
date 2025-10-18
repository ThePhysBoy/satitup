(function ($) {
  "use strict";

  /** Define Mobile Enviroment */
  var isMobile = {
    Android: function () {
      return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function () {
      return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function () {
      return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function () {
      return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function () {
      return navigator.userAgent.match(/IEMobile/i);
    },
    any: function () {
      return (
        isMobile.Android() ||
        isMobile.BlackBerry() ||
        isMobile.iOS() ||
        isMobile.Opera() ||
        isMobile.Windows()
      );
    },
  };

  /** Navigation Dropdown */
  if (typeof $.fn.dropdown_menu !== 'undefined') {
    $("#header .menu").dropdown_menu();
  }

  /** Mobile Menu */
  if (isMobile.any() && typeof $.fn.doubleTapToGo !== 'undefined') {
    $(".menu li:has(ul)").doubleTapToGo();
  }

  /** Main Navigation Sticky */
  if (!isMobile.any() && typeof $.fn.waypoint !== 'undefined') {
    $("#main-nav").waypoint("sticky", {
      handler: function (direction) {
        if (direction == "down") {
          var offset = $("#site").offset();
          $("#main-nav").css("left", offset.left);
        } else {
          $("#main-nav").css("left", 0);
        }
      },
    });
  }

  /** Sticky Booking Rom */
  if (!isMobile.any() && typeof $.fn.waypoint !== 'undefined') {
    $("#header").waypoint({
      handler: function (direction) {
        if (direction == "down") {
          $(".booking-row").css("position", "absolute");
        } else {
          $(".booking-row").css("position", "fixed");
        }
      },
      offset: "bottom-in-view",
    });
  } else {
    $(".booking-row").css("position", "absolute");
  }

  /** Services Carousel */
  if ($(".services-carousel").length > 0) {
    $(".services-carousel").owlCarousel({
      items: 4,
      margin: 20,
      loop: true,
      autoplay: true,
      autoplayTimeout: 2000,
      autoplayHoverPause: true,
      responsive: {
        0: {
          items: 1,
          dots: true,
        },
        767: {
          items: 2,
          dots: false,
        },
        992: {
          items: 3,
          dots: false,
        },
        1200: {
          items: 4,
          dots: false,
        },
      },
    });
  }

  /** Testimonials Carousel */
  if ($(".testimonials-carousel").length > 0) {
    $(".testimonials-carousel").owlCarousel({
      items: 3,
      margin: 20,
      nav: true,
      loop: true,
      autoplay: true,
      autoplayTimeout: 2000,
      autoplayHoverPause: true,
      responsive: {
        0: {
          items: 1,
          dots: true,
          nav: false,
        },
        767: {
          items: 2,
          dots: true,
        },
        992: {
          items: 3,
          dots: true,
        },
      },
    });
  }

  /** Testimonials Carousel */
  if ($(".content-carousel").length > 0) {
    $(".content-carousel").owlCarousel({
      items: 1,
      nav: false,
      loop: false,
      dots: false,
      URLhashListener: true,
      autoplay: false,
      autoplayTimeout: 2000,
      autoplayHoverPause: true,
    });
  }

  /** Gallery Carousel */
  if ($(".gallery-carousel").length > 0) {
  }

  /** Stellar */
  if (
    !isMobile.any() &&
    $(".parallax-container, .parallax, .parallax-bg").length > 0 &&
    typeof $.stellar === 'function'
  ) {
    $.stellar({
      horizontalScrolling: false,
      parallaxBackgrounds: true,
      hideDistantElements: false,
    });
  }

  /** Contact Map */
  if ($("#map-holder").length > 0 && typeof $.fn.gmap3 !== 'undefined' && typeof google !== 'undefined') {
    $("#map-container")
      .width("100%")
      .height("100%")
      .gmap3({
        map: {
          options: {
            center: [51.50853, -0.12574],
            zoom: 15,
            disableDefaultUI: true,
            draggable: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            mapTypeControlOptions: {},
            navigationControl: false,
            scrollwheel: false,
            streetViewControl: false,
            styles: [
              {
                featureType: "administrative",
                elementType: "all",
                stylers: [
                  { visibility: "on" },
                  { saturation: -100 },
                  { lightness: 20 },
                ],
              },
              {
                featureType: "road",
                elementType: "all",
                stylers: [
                  { visibility: "on" },
                  { saturation: -100 },
                  { lightness: 40 },
                ],
              },
              {
                featureType: "water",
                elementType: "all",
                stylers: [
                  { visibility: "on" },
                  { saturation: -10 },
                  { lightness: 30 },
                ],
              },
              {
                featureType: "landscape.man_made",
                elementType: "all",
                stylers: [
                  { visibility: "simplified" },
                  { saturation: -60 },
                  { lightness: 10 },
                ],
              },
              {
                featureType: "landscape.natural",
                elementType: "all",
                stylers: [
                  { visibility: "simplified" },
                  { saturation: -60 },
                  { lightness: 60 },
                ],
              },
              {
                featureType: "poi",
                elementType: "all",
                stylers: [
                  { visibility: "off" },
                  { saturation: -100 },
                  { lightness: 60 },
                ],
              },
              {
                featureType: "transit",
                elementType: "all",
                stylers: [
                  { visibility: "off" },
                  { saturation: -100 },
                  { lightness: 60 },
                ],
              },
            ],
          },
        },
        marker: {
          latLng: [51.50853, -0.12574],
        },
      });
  }

  /** Coming Soon */
  if ($("#coming-soon").length > 0) {
    $("#coming-soon").css("margin-top", -($("#coming-soon").outerHeight() / 2));
  }

  /** Nice Self Scroll */
  $('a[target="_self"]').on("click", function (e) {
    e.preventDefault();
    var target = $(this).attr("href");
    if (typeof $.fn.velocity !== 'undefined') {
      $(target).velocity("scroll", {
        duration: 1000,
        easing: "easeOutCubic",
        offset: -($("#main-nav").outerHeight() + 30),
      });
    } else {
      // Fallback to standard jQuery animation
      $('html, body').animate({
        scrollTop: $(target).offset().top - ($("#main-nav").outerHeight() + 30)
      }, 1000);
    }
  });

  /** Lightbox */
  if ($('a[rel="lightbox"]').length > 0 && typeof $.fn.boxer !== 'undefined') {
    $(function () {
      $('a[rel="lightbox"]').boxer({
        fixed: true,
      });
    });
  }

  /** Booking Select */
  if (!isMobile.any() && $(".select-or-die").length > 0 && typeof $.fn.selectOrDie !== 'undefined') {
    $(".select-or-die").selectOrDie();
  }

  /** Video Bg */
  if ($("#video-bg").length > 0 && typeof $.fn.wallpaper !== 'undefined') {
    $("#video-bg").wallpaper({
      source: {
        poster: "images/sunny.jpg",
        mp4: "video/sunny.mp4",
        ogg: "video/sunny.ogv",
        webm: "video/sunny.webm",
      },
    });
  }

  /** 404 Video Bg */
  if ($("#videobg-2").length > 0 && typeof $.fn.wallpaper !== 'undefined') {
    $("#videobg-2").wallpaper({
      source: {
        poster: "images/birds.jpg",
        mp4: "video/birds.mp4",
        ogg: "video/birds.ogv",
        webm: "video/birds.webm",
      },
    });
  }

  /** Recreation Video Bg */
  if ($("#videobg-3").length > 0 && typeof $.fn.wallpaper !== 'undefined') {
    $("#videobg-3").wallpaper({
      source: {
        poster: "images/snorkelling.jpg",
        mp4: "video/snorkelling.mp4",
        ogg: "video/snorkelling.ogv",
        webm: "video/snorkelling.webm",
      },
    });
  }

  /** Search Form */
  $(".search-button").on("click", function (e) {
    e.preventDefault();

    if (typeof $.fn.velocity !== 'undefined') {
      $("#search-form").velocity("fadeIn");
    } else {
      $("#search-form").fadeIn();
    }
    $(".search-field").focus();

    $("#main-nav").mouseleave(function () {
      if (typeof $.fn.velocity !== 'undefined') {
        $("#search-form").velocity("fadeOut");
      } else {
        $("#search-form").fadeOut();
      }
    });
  });

  $(".close-search").on("click", function (e) {
    e.preventDefault();

    if (typeof $.fn.velocity !== 'undefined') {
      $("#search-form").velocity("fadeOut");
    } else {
      $("#search-form").fadeOut();
    }
  });

  /** Tooltips */
  if (typeof $.fn.tooltip !== 'undefined') {
    $("[data-toggle='tooltip']").tooltip();
  }

  /** Date Picker */
  if ($('.form-control[data-provide="datepicker"]').length > 0 && typeof $.fn.datepicker !== 'undefined') {
    $('.form-control[data-provide="datepicker"]')
      .datepicker()
      .on("show", function (e) {
        $(".datepicker").css("min-width", $(this).outerWidth());
      });
  }

  /** Animations */
  if ($(".animated").length > 0 && !isMobile.any() && typeof $.fn.waypoint !== 'undefined') {
    $(".animated").waypoint(
      function () {
        var target = $(this);
        if (!target.hasClass("animated_off")) {
          if (typeof $.fn.velocity !== 'undefined') {
            $(target).delay(150).velocity("transition.slideUpIn");
          } else {
            $(target).delay(150).fadeIn();
          }
          target.addClass("animated_off");
        }
      },
      {
        offset: typeof $.waypoints === 'function' ? $.waypoints("viewportHeight") : '100%',
      }
    );
  } else {
    $(".animated").css("opacity", 1);
  }
  if ($(".animated-children").length > 0 && !isMobile.any() && typeof $.fn.waypoint !== 'undefined') {
    $(".animated-children").waypoint(
      function () {
        var target = $(this);
        if (!target.hasClass("animated_off")) {
          if (typeof $.fn.velocity !== 'undefined') {
            $('[class*="col-"]', target)
              .children()
              .velocity("transition.slideUpIn", { stagger: 100 });
          } else {
            $('[class*="col-"]', target)
              .children()
              .each(function(index) {
                $(this).delay(index * 100).fadeIn();
              });
          }
          target.addClass("animated_off");
        }
      },
      {
        offset: typeof $.waypoints === 'function' ? $.waypoints("viewportHeight") : '100%',
      }
    );
  } else {
    $('[class*="col-"]', ".animated-children").css("opacity", 1);
  }
  if (!isMobile.any() && typeof $.fn.waypoint !== 'undefined') {
    $("#footer").waypoint(
      function () {
        if (!$("#footer").hasClass("animated_off")) {
          if (typeof $.fn.velocity !== 'undefined') {
            $("aside", "#footer")
              .delay(50)
              .velocity("transition.slideUpIn", { drag: true, stagger: 50 });
          } else {
            $("aside", "#footer").each(function(index) {
              $(this).delay(50 + (index * 50)).fadeIn();
            });
          }
          $("#footer").addClass("animated_off");
        }
      },
      {
        offset: typeof $.waypoints === 'function' ? $.waypoints("viewportHeight") : '100%',
      }
    );
  } else {
    $("aside", "#footer").css("opacity", 1);
  }

  /** Mobile Navigation */
  if (isMobile.any()) {
    $("#toggle-secondary-nav").change(function () {
      if ($(this).is(":checked")) {
        $("#toggle-main-nav").prop("checked", false);
      }
    });
    $("#toggle-main-nav").change(function () {
      if ($(this).is(":checked")) {
        $("#toggle-secondary-nav").prop("checked", false);
      }
    });
  }

  /** Attractions Isotope */
  if ($("#isotope").length > 0 && typeof $.fn.isotope !== 'undefined') {
    $("#isotope").isotope();
    $("#isotope-filter a").on("click", function (e) {
      e.preventDefault();
      var filterValue = $(this).attr("data-filter");
      $("#isotope").isotope({ filter: filterValue });
      $(this).parent().siblings().removeClass("selected");
      $(this).parent().addClass("selected");
      if (!$("#footer").hasClass("animated_off")) {
        if (typeof $.fn.velocity !== 'undefined') {
          $("aside", "#footer")
            .delay(150)
            .velocity("transition.slideUpIn", { drag: true, stagger: 50 });
        } else {
          $("aside", "#footer").each(function(index) {
            $(this).delay(150 + (index * 50)).fadeIn();
          });
        }
        $("#footer").addClass("animated_off");
      }
    });
  }
})(jQuery);
