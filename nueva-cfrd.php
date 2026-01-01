<?php
/**
 * Plugin Name: Nueva Custom Field Repeater Display
 * Plugin URI: https://github.com/Nueva-Digital-Solutions/nueva-cfrd
 * Description: Display ACF Repeater fields or generic serialized arrays on the frontend with various layouts (Grid, List, Slider, Accordion, etc.).
 * Version: 1.0.0
 * Author: Nueva Digital Solutions
 * Author URI: https://nuevadigital.co.in
 * Text Domain: nueva-cfrd
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('NUEVA_CFRD_VERSION', '1.0.0');
define('NUEVA_CFRD_PATH', plugin_dir_path(__FILE__));
define('NUEVA_CFRD_URL', plugin_dir_url(__FILE__));

// Include Core Class
require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-core.php';
// require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-cpt.php';

// if (is_admin()) {
//     require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-admin.php';
// }

// Initialize Plugin
function nueva_cfrd_init()
{
    $plugin = new Nueva_CFRD_Core();
    $plugin->run();

    // $cpt = new Nueva_CFRD_CPT();
    // $cpt->run();

    // if (is_admin()) {
    //     $admin = new Nueva_CFRD_Admin();
    //     $admin->run();
    // }
}
add_action('plugins_loaded', 'nueva_cfrd_init');

// Register Elementor Widget
// Register Elementor Widgets
// Register Elementor Widgets
// Include Helper Class
require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-templates.php';

// Register Elementor Widgets
function nueva_register_elementor_widgets($widgets_manager)
{
    require_once NUEVA_CFRD_PATH . 'includes/widgets/class-nueva-cfrd-widget-base.php';
    require_once NUEVA_CFRD_PATH . 'includes/widgets/class-nueva-cfrd-widget-custom.php';

    $widgets_manager->register(new \Nueva_CFRD_Widget_Custom());
}
add_action('elementor/widgets/register', 'nueva_register_elementor_widgets');

// Enqueue Editor Scripts
function nueva_cfrd_enqueue_editor_scripts()
{
    wp_enqueue_script(
        'nueva-cfrd-editor',
        NUEVA_CFRD_URL . 'assets/js/admin-editor.js',
        ['jquery', 'elementor-editor'], // Dep: elementor-editor usually ensures jquery
        NUEVA_CFRD_VERSION,
        true
    );

    // Get Templates for JS
    if (class_exists('Nueva_CFRD_Templates')) {
        // We pass "placeholder" keys to get the raw pattern back
        $placeholders = [
            'key_title' => '{{key_title}}',
            'key_desc' => '{{key_desc}}',
            'key_image' => '{{key_image}}',
            'key_link' => '{{key_link}}',
            'key_button' => '{{key_button}}',
        ];

        $presets = [
            'grid' => Nueva_CFRD_Templates::get_config('grid', $placeholders),
            'list' => Nueva_CFRD_Templates::get_config('list', $placeholders),
            'slider' => Nueva_CFRD_Templates::get_config('slider', $placeholders),
            'accordion' => Nueva_CFRD_Templates::get_config('accordion', $placeholders),
            'table' => Nueva_CFRD_Templates::get_config('table', $placeholders),
        ];

        wp_localize_script('nueva-cfrd-editor', 'nuevaTemplates', $presets);
    }
}
add_action('elementor/editor/after_enqueue_scripts', 'nueva_cfrd_enqueue_editor_scripts');
