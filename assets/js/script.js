jQuery(document).ready(function ($) {

    // Accordion Logic
    $('.nueva-accordion-header').on('click', function () {
        const item = $(this).parent('.nueva-accordion-item');
        const content = item.find('.nueva-accordion-content');

        item.toggleClass('active');
        content.slideToggle();
    });

    // Tabs Logic
    $('.nueva-tabs-nav li').on('click', function () {
        const tabId = $(this).data('tab');
        const wrapper = $(this).closest('.nueva-cfrd-tabs');

        // Nav
        wrapper.find('.nueva-tabs-nav li').removeClass('active');
        $(this).addClass('active');

        // Content
        wrapper.find('.nueva-tab-pane').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    // Swiper Initialization
    if (typeof Swiper !== 'undefined') {
        $('.nueva-cfrd-slider').each(function () {
            new Swiper(this, {
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                slidesPerView: 1,
                spaceBetween: 20,
                breakpoints: {
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                }
            });
        });
    }

    // Masonry Initialization
    if (typeof Masonry !== 'undefined' || $.fn.masonry) {
        $('.nueva-cfrd-masonry').masonry({
            itemSelector: '.nueva-masonry-item',
            columnWidth: '.nueva-masonry-item',
            percentPosition: true,
            gutter: 20
        });
    }

    // Master Detail Logic
    $('.nueva-master-item').on('click', function () {
        var index = $(this).data('index');
        var wrapper = $(this).closest('.nueva-cfrd-master-detail');

        wrapper.find('.nueva-master-item').removeClass('active');
        $(this).addClass('active');

        wrapper.find('.nueva-detail-pane').removeClass('active');
        wrapper.find('.nueva-detail-pane[data-index="' + index + '"]').addClass('active');
    });

    // Expandable Cards
    $('.nueva-expand-btn').on('click', function () {
        $(this).closest('.nueva-expandable-card').find('.nueva-card-full').slideToggle();
        $(this).text(function (i, text) {
            return text === "Expand" ? "Collapse" : "Expand";
        })
    });

    // Filterable Logic
    $('.nueva-filter-controls button').on('click', function () {
        var filterValue = $(this).data('filter');
        var container = $(this).closest('.nueva-cfrd-filterable').find('.nueva-filter-grid');

        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        if (filterValue === '*') {
            container.find('> div').show();
        } else {
            container.find('> div').hide();
            container.find(filterValue).show();
        }

        // Re-layout masonry if using masonry inside filter
        if ($.fn.masonry && container.hasClass('nueva-masonry')) {
            container.masonry('layout');
        }
    });

});
