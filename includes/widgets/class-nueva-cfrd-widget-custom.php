<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Nueva_CFRD_Widget_Custom extends Nueva_CFRD_Widget_Base
{
    public function get_name()
    {
        return 'nueva_cfrd_custom';
    }

    public function get_title()
    {
        return esc_html__('ACF Repeater Custom Loop', 'nueva-cfrd');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_layout_type()
    {
        return 'custom';
    }

    protected function add_layout_controls()
    {
        $this->start_controls_section(
            'custom_loop_section',
            [
                'label' => esc_html__('Custom Loop Template', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'custom_template',
            [
                'label' => esc_html__('HTML Template', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'html',
                'rows' => 20,
                'placeholder' => '<div class="my-item">
    <h3>{{field_key_1}}</h3>
    <p>{{field_key_2}}</p>
</div>',
                'description' => 'Use {{field_key}} to output sub-field values.',
            ]
        );

        $this->end_controls_section();
    }
}
