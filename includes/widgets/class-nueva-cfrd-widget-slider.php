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
            'slider_dots',
            [
                'label' => esc_html__('Show Dots', 'nueva-cfrd'),
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
            'arrow_type',
            [
                'label' => esc_html__('Arrow Type', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => 'Default',
                    'icon' => 'Icon Library',
                    'custom' => 'Custom Image/SVG',
                ],
                'condition' => ['slider_arrows' => 'yes'],
            ]
        );

        $this->add_control(
            'arrow_icon_prev',
            [
                'label' => esc_html__('Previous Icon', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-chevron-left',
                    'library' => 'fa-solid',
                ],
                'condition' => ['slider_arrows' => 'yes', 'arrow_type' => 'icon'],
            ]
        );

        $this->add_control(
            'arrow_icon_next',
            [
                'label' => esc_html__('Next Icon', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-chevron-right',
                    'library' => 'fa-solid',
                ],
                'condition' => ['slider_arrows' => 'yes', 'arrow_type' => 'icon'],
            ]
        );

        $this->add_control(
            'arrow_image_prev',
            [
                'label' => esc_html__('Previous Image', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => ['slider_arrows' => 'yes', 'arrow_type' => 'custom'],
            ]
        );

        $this->add_control(
            'arrow_image_next',
            [
                'label' => esc_html__('Next Image', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => ['slider_arrows' => 'yes', 'arrow_type' => 'custom'],
            ]
        );
    }

    protected function get_renderer_args($settings)
    {
        return [
            'slider_autoplay' => $settings['slider_autoplay'],
            'slider_speed' => $settings['slider_speed'],
            'slider_loop' => $settings['slider_loop'],
            'slider_dots' => $settings['slider_dots'],
            'slider_arrows' => $settings['slider_arrows'],
            'arrow_type' => $settings['arrow_type'] ?? 'default',
            'arrow_icon_prev' => $settings['arrow_icon_prev'] ?? [],
            'arrow_icon_next' => $settings['arrow_icon_next'] ?? [],
            'arrow_image_prev' => $settings['arrow_image_prev'] ?? [],
            'arrow_image_next' => $settings['arrow_image_next'] ?? [],
        ];
    }
}
