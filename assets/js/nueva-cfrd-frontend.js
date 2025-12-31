jQuery(document).ready(function ($) {

    // --- Accordion Logic ---
    $('.nueva-cfrd-wrapper[data-layout="accordion"]').each(function () {
        var $wrapper = $(this);
        var behavior = $wrapper.data('behavior') || 'toggle'; // 'toggle' or 'accordion' (one open)
        // Note: We need to pass behavior via data attribute from PHP if we want it dynamic.
        // For now, simple toggle.

        $wrapper.find('.nueva-accordion-header').on('click', function () {
            var $header = $(this);
            var $content = $header.next('.nueva-accordion-content');
            var $item = $header.parent();

            if ($item.hasClass('active')) {
                // Close
                $content.slideUp(300);
                $item.removeClass('active');
            } else {
                // Open
                // If 'accordion' mode, close others
                // (We'd need to check config, but let's assume standard accordion for now if requested)

                $content.slideDown(300);
                $item.addClass('active');
            }
        });
    });

    // --- Slider Logic (Swiper) ---
    // We rely on Elementor's Swiper if available, or a packaged one.
    // For this context, standard Elementor widgets usually init their own swiper.
    // Since we are a custom widget, we might need to init it manually.

    var initSwiper = function () {
        $('.nueva-cfrd-slider').each(function () {
            var $el = $(this);
            if ($el.hasClass('swiper-initialized')) return;

            // Get settings (could be passed via data-attributes)
            var config = {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            };

            if (typeof Swiper !== 'undefined') {
                new Swiper($el[0], config);
            } else if (typeof elementorFrontend !== 'undefined' && elementorFrontend.utils && elementorFrontend.utils.swiper) {
                // Elementor 3.0+ way
                new elementorFrontend.utils.swiper($el[0], config);
            }
        });
    };

    // Run on init
    initSwiper();

    // Run on Elementor frontend init (editor preview)
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/nueva_cfrd_slider.default', initSwiper);
    });

});
