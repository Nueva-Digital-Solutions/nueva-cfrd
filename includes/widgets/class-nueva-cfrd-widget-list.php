<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Nueva_CFRD_Widget_List extends Nueva_CFRD_Widget_Base
{
    public function get_name()
    {
        return 'nueva_cfrd_list';
    }

    public function get_title()
    {
        return esc_html__('ACF Repeater List', 'nueva-cfrd');
    }

    public function get_icon()
    {
        return 'eicon-editor-list-ul';
    }

    public function get_layout_type()
    {
        return 'list';
    }

    protected function add_layout_controls()
    {
        $this->add_responsive_control(
            'list_gap',
            [
                'label' => esc_html__('Item Gap', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .nueva-cfrd-list' => 'display: flex; flex-direction: column; gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .nueva-cfrd-list li' => 'margin-bottom: 0;', // Reset default li margin
                ],
            ]
        );
    }
}
