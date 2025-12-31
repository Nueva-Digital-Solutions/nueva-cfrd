<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Nueva_CFRD_Widget_Grid extends Nueva_CFRD_Widget_Base
{
    public function get_name()
    {
        return 'nueva_cfrd_grid';
    }

    public function get_title()
    {
        return esc_html__('ACF Repeater Grid', 'nueva-cfrd');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_layout_type()
    {
        return 'grid';
    }

    protected function add_layout_controls()
    {
        $this->add_responsive_control(
            'grid_gap',
            [
                'label' => esc_html__('Gap', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .nueva-cfrd-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_columns',
            [
                'label' => esc_html__('Columns', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'selectors' => [
                    '{{WRAPPER}} .nueva-cfrd-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );
    }
}
