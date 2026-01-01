<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Nueva_CFRD_Widget_Table extends Nueva_CFRD_Widget_Base
{
    public function get_name()
    {
        return 'nueva_cfrd_table';
    }

    public function get_title()
    {
        return esc_html__('ACF Repeater Table', 'nueva-cfrd');
    }

    public function get_icon()
    {
        return 'eicon-table';
    }

    public function get_layout_type()
    {
        return 'table';
    }

    protected function add_layout_controls()
    {
        // Table specific styles could go here (border collapse, etc)
        $this->add_control(
            'table_width',
            [
                'label' => esc_html__('Width', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} table' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'table_border_spacing',
            [
                'label' => esc_html__('Row/Column Gap', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .nueva-cfrd-table' => 'border-collapse: separate; border-spacing: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    }
}
