<?php

class Nueva_CFRD_Core
{

    public function run()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('nueva_cfrd', array($this, 'render_shortcode'));
    }

    public function enqueue_assets()
    {
        // Enqueue generic styles
        wp_enqueue_style('nueva-cfrd-style', NUEVA_CFRD_URL . 'assets/css/nueva-cfrd-frontend.css', array(), NUEVA_CFRD_VERSION);

        // Enqueue Swiper for sliders/carousels (using a CDN for now or local if available)
        // For production, we should bundle this, but for now we'll use a CDN for quick setup
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.5', true);

        // Enqueue Masonry (built-in to WP)
        wp_enqueue_script('masonry');

        // Enqueue custom script
        wp_enqueue_script('nueva-cfrd-script', NUEVA_CFRD_URL . 'assets/js/nueva-cfrd-frontend.js', array('jquery', 'swiper-js', 'masonry'), NUEVA_CFRD_VERSION, true);
    }

    public function render_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'field' => '',       // The meta key or ACF field name
                'type' => 'acf',    // 'acf' or 'generic'
                'layout' => 'grid',   // grid, list, table, accordion, tabs, slider, etc.
                'post_id' => get_the_ID(),
                'class' => '',       // Custom wrapper class
                'columns' => '3',      // For grid layouts (1-12)
            ),
            $atts,
            'nueva_cfrd'
        );

        if (empty($atts['field'])) {
            return '';
        }

        require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-renderer.php';
        $renderer = new Nueva_CFRD_Renderer($atts);
        return $renderer->render();
    }
}
