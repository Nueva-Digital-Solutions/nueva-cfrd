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
        $this->add_control(
            'slider_autoplay',
            [
                'label' => esc_html__('Autoplay', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'slider_speed',
            [
                'label' => esc_html__('Speed (ms)', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3000,
            ]
        );

        $this->add_control(
            'slider_loop',
            [
                'label' => esc_html__('Infinite Loop', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'slider_arrows',
            [
                'label' => esc_html__('Show Arrows', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'slider_dots',
            [
                'label' => esc_html__('Show Dots', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
    }
}
