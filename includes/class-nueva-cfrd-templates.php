<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Nueva_CFRD_Templates
{
    /**
     * Get Template Config
     *
     * @param string $type grid, list, slider, accordion, table
     * @param array $keys Mapped keys (title, image, etc)
     * @return array [html, css, wrapper_class, wrapper_attrs]
     */
    public static function get_config($type, $keys)
    {
        $keys = wp_parse_args($keys, [
            'key_title' => 'title',
            'key_desc' => 'description',
            'key_image' => 'image',
            'key_link' => 'link',
            'key_button' => 'Read More',
        ]);

        switch ($type) {
            case 'grid':
                return self::get_grid_config($keys);
            case 'list':
                return self::get_list_config($keys);
            case 'slider':
                return self::get_slider_config($keys);
            case 'accordion':
                return self::get_accordion_config($keys);
            case 'table':
                return self::get_table_config($keys);
            default:
                return [];
        }
    }

    private static function get_grid_config($keys)
    {
        // Placeholders
        $title = '{{' . $keys['key_title'] . '}}';
        $desc = '{{' . $keys['key_desc'] . '}}';
        $img = '{{' . $keys['key_image'] . '}}';
        $link = '{{' . $keys['key_link'] . '}}';
        $btn = $keys['key_button'];

        $html = "
        <div class=\"nueva-preset-card\">
            <div class=\"nueva-preset-img-wrap\">
                <img src=\"$img\" alt=\"$title\" class=\"nueva-preset-img\">
            </div>
            <div class=\"nueva-preset-content\">
                <h3 class=\"nueva-preset-title\">$title</h3>
                <div class=\"nueva-preset-desc\">$desc</div>
                <a href=\"$link\" class=\"nueva-preset-btn\">$btn</a>
            </div>
        </div>";

        $css = "
        .nueva-cfrd-custom-loop {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .nueva-preset-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .nueva-preset-card:hover { transform: translateY(-5px); }
        .nueva-preset-img-wrap { height: 200px; overflow: hidden; }
        .nueva-preset-img { width: 100%; height: 100%; object-fit: cover; }
        .nueva-preset-content { padding: 20px; }
        .nueva-preset-title { margin: 0 0 10px; font-size: 1.25rem; }
        .nueva-preset-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 16px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .nueva-preset-btn:hover { background: #0056b3; }
        ";

        return [
            'html' => $html,
            'css' => $css,
            'wrapper_class' => '',
        ];
    }

    private static function get_list_config($keys)
    {
        // Similar placeholders
        $title = '{{' . $keys['key_title'] . '}}';
        $desc = '{{' . $keys['key_desc'] . '}}';
        $img = '{{' . $keys['key_image'] . '}}';
        $link = '{{' . $keys['key_link'] . '}}';

        $html = "
        <div class=\"nueva-preset-list-item\">
            <div class=\"nueva-preset-list-img\">
                <img src=\"$img\" alt=\"$title\">
            </div>
            <div class=\"nueva-preset-list-content\">
                <h3 class=\"nueva-preset-list-title\">$title</h3>
                <div class=\"nueva-preset-list-desc\">$desc</div>
                <a href=\"$link\" class=\"nueva-preset-list-link\">Read More &rarr;</a>
            </div>
        </div>";

        $css = "
        .nueva-cfrd-custom-loop { display: flex; flex-direction: column; gap: 15px; }
        .nueva-preset-list-item { 
            display: flex; 
            align-items: center; 
            background: #fff; 
            padding: 15px; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }
        .nueva-preset-list-img { width: 80px; height: 80px; flex-shrink: 0; margin-right: 20px; border-radius: 6px; overflow: hidden; }
        .nueva-preset-list-img img { width: 100%; height: 100%; object-fit: cover; }
        .nueva-preset-list-title { margin: 0 0 5px; font-size: 1.1rem; }
        ";

        return [
            'html' => $html,
            'css' => $css,
            'wrapper_class' => '',
        ];
    }

    private static function get_slider_config($keys)
    {
        $title = '{{' . $keys['key_title'] . '}}';
        $img = '{{' . $keys['key_image'] . '}}';

        // Swiper Slide Structure
        $html = "
        <div class=\"swiper-slide\">
            <div class=\"nueva-preset-slide\">
                <img src=\"$img\" class=\"nueva-slide-bg\" />
                <div class=\"nueva-slide-overlay\">
                    <h3>$title</h3>
                </div>
            </div>
        </div>";

        $css = "
        .nueva-cfrd-custom-loop { overflow: hidden; position: relative; } /* Swiper Container */
        .nueva-preset-slide { position: relative; height: 300px; border-radius: 10px; overflow: hidden; }
        .nueva-slide-bg { width: 100%; height: 100%; object-fit: cover; }
        .nueva-slide-overlay {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            padding: 20px; color: #fff;
        }
        ";

        return [
            'html' => $html,
            'css' => $css,
            // Important: Add swiper classes to wrapper
            'wrapper_class' => 'swiper nueva-cfrd-slider',
            // Wrapper needs inner wrapper for swiper? Actually render_custom_loop makes ONE wrapper.
            // Swiper needs: Container > Wrapper > Slide.
            // Our render_custom_loop makes: <div class="wrapper"> ...items... </div>
            // So we need to inject the <div class="swiper-wrapper"> INSIDE that? 
            // Or make the main wrapper the swiper-wrapper?
            // Elementor Swiper usually is: .swiper-container > .swiper-wrapper > .swiper-slide
            // We will handle this by returning a 'wrapper_start_html' / 'wrapper_end_html' or 
            // instructing the renderer to output the swiper-wrapper div.
            'is_slider' => true,
        ];
    }

    // ... Implement others similarly ...
    // For brevity, I'll stick to full implementation in the file write
    private static function get_accordion_config($keys)
    {
        $title = '{{' . $keys['key_title'] . '}}';
        $desc = '{{' . $keys['key_desc'] . '}}';

        $html = "
         <div class=\"nueva-accordion-item\">
            <div class=\"nueva-accordion-header\">$title</div>
            <div class=\"nueva-accordion-content\">
                <div class=\"inner-content\">$desc</div>
            </div>
         </div>";

        $css = "
         .nueva-accordion-item { border-bottom: 1px solid #eee; }
         .nueva-accordion-header { padding: 15px; cursor: pointer; font-weight: bold; background: #f9f9f9; }
         .nueva-accordion-content { display: none; padding: 15px; background: #fff; }
         .nueva-accordion-item.active .nueva-accordion-content { display: block; }
         ";

        return [
            'html' => $html,
            'css' => $css,
            'wrapper_class' => 'nueva-cfrd-accordion-wrapper', // Hook for JS
            'is_accordion' => true,
        ];
    }

    private static function get_table_config($keys)
    {
        // Table is tricky because of Header vs Body.
        // Custom Loop repeats the ITEM.
        // So we can only render ROWS here. The wrapper needs to be the TABLE.

        $title = '{{' . $keys['key_title'] . '}}';
        $desc = '{{' . $keys['key_desc'] . '}}';

        $html = "
        <tr>
            <td>$title</td>
            <td>$desc</td>
        </tr>";

        $css = "
        .nueva-cfrd-custom-loop table { width: 100%; border-collapse: collapse; }
        .nueva-cfrd-custom-loop td { border: 1px solid #ddd; padding: 10px; }
        ";

        return [
            'html' => $html,
            'css' => $css,
            'wrapper_tag' => 'table', // Special hint to renderer?
            // Or we just output <table><tbody>...inside wrapper?
            // No, the Wrapper IS the container.
            // If wrapper is <div>, we can't put <tr> inside directly.
            // We need the wrapper to be <table> or <tbody>.
            'change_wrapper_tag' => 'tbody',
            'pre_wrapper_html' => '<table class="nueva-preset-table">',
            'post_wrapper_html' => '</table>'
        ];
    }
}
