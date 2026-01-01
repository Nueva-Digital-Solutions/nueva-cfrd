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
                if (behavior === 'accordion') {
                    $wrapper.find('.nueva-accordion-item.active').each(function () {
                        $(this).removeClass('active');
                        $(this).find('.nueva-accordion-content').slideUp(300);
                    });
                }

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

            // Merge Data Settings
            var dataSettings = $el.data('swiper-settings');
            if (dataSettings) {
                // Handle Autoplay logic specifically if it's turned off
                if (dataSettings.autoplay === false || dataSettings.autoplay === 0) {
                    config.autoplay = false;
                } else if (typeof dataSettings.autoplay === 'number') {
                    config.autoplay = {
                        delay: dataSettings.autoplay,
                        disableOnInteraction: false
                    };
                }

                if (dataSettings.loop !== undefined) config.loop = dataSettings.loop;
                if (dataSettings.speed) config.speed = dataSettings.speed;
                if (dataSettings.pagination === false) config.pagination = false;
                if (dataSettings.navigation === false) config.navigation = false;
            }

            if (typeof Swiper !== 'undefined') {
                new Swiper($el[0], config);
            } else if (typeof elementorFrontend !== 'undefined' && elementorFrontend.utils && elementorFrontend.utils.swiper) {
                // Elementor 3.0+ way
                new elementorFrontend.utils.swiper($el[0], config);
            }

            // Hide Default Icons if Custom Arrow used
            // Swiper adds :after content for default icons. We suppress it via inline style or class if custom.
            $el.find('.nueva-arrow-icon, .nueva-arrow-custom').each(function () {
                $(this).addClass('bfs-hide-default-arrow');
                // Quick CSS injection if not in stylesheet
                $(this).css({
                    'content': 'none',
                    'background': 'none'
                });
                // To kill the :after pseudo element from CSS is hard via JS directly on element, 
                // but we can add a class to the container or button to let our CSS handle it.
                // Ideally this should be in CSS file: .bfs-hide-default-arrow::after { display: none !important; }
            });
            // Inject cleaner style for arrows if missing
            if ($('.nueva-arrow-icon, .nueva-arrow-custom').length > 0 && $('#nueva-arrow-fix').length === 0) {
                $('head').append('<style id="nueva-arrow-fix">.swiper-button-next.bfs-hide-default-arrow::after, .swiper-button-prev.bfs-hide-default-arrow::after { display: none !important; content: none !important; }</style>');
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
