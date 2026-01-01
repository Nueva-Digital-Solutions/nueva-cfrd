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
        // Behavior Controls
        $this->add_control(
            'accordion_behavior',
            [
                'label' => esc_html__('Behavior', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'toggle',
                'options' => [
                    'toggle' => 'Toggle (Multiple Open)',
                    'accordion' => 'Accordion (One Open)',
                ],
            ]
        );

        $this->add_control(
            'default_state',
            [
                'label' => esc_html__('Default State', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all_closed',
                'options' => [
                    'all_closed' => 'All Closed',
                    'first_open' => 'First Open',
                ],
            ]
        );

        // Styling Controls (Title & Content)
        $this->add_control(
            'accordion_title_color',
            [
                'label' => esc_html__('Title Color', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nueva-accordion-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'accordion_title_bg',
            [
                'label' => esc_html__('Title Background', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nueva-accordion-title' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'accordion_content_bg',
            [
                'label' => esc_html__('Content Background', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nueva-accordion-content' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        // Grid / Layout Controls
        $this->add_responsive_control(
            'accordion_columns',
            [
                'label' => esc_html__('Columns', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => 1,
                'tablet_default' => 1,
                'mobile_default' => 1,
                'selectors' => [
                    '{{WRAPPER}} .nueva-cfrd-accordion' => 'display: grid; grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'accordion_gap',
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
                    '{{WRAPPER}} .nueva-cfrd-accordion' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    }
}
