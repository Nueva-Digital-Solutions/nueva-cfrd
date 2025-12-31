<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Nueva_CFRD_Widget_Slider extends Nueva_CFRD_Widget_Base
{
    public function get_name()
    {
        return 'nueva_cfrd_slider';
    }

    public function get_title()
    {
        return esc_html__('ACF Repeater Slider', 'nueva-cfrd');
    }

    public function get_icon()
    {
        return 'eicon-slides';
    }

    public function get_layout_type()
    {
        return 'slider';
    }

    protected function add_layout_controls()
    {
        // Add Slider specific controls (autoplay, speed, etc)
        // These keys would need to be handled by the JS script or Renderer
    }
}
