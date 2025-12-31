<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Nueva_CFRD_Widget_Accordion extends Nueva_CFRD_Widget_Base
{
    public function get_name()
    {
        return 'nueva_cfrd_accordion';
    }

    public function get_title()
    {
        return esc_html__('ACF Repeater Accordion', 'nueva-cfrd');
    }

    public function get_icon()
    {
        return 'eicon-accordion';
    }

    public function get_layout_type()
    {
        return 'accordion';
    }

    protected function add_layout_controls()
    {
        // Add Accordion specific controls
    }
}
