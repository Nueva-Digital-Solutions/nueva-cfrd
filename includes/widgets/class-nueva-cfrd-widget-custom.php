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

    protected function register_controls()
    {
        // Only Common Controls (Data Source), NO Repeater
        $this->register_common_controls();

        // Custom Assets Section
        $this->start_controls_section(
            'assets_section',
            [
                'label' => esc_html__('Custom Assets', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'custom_css',
            [
                'label' => esc_html__('Custom CSS', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'rows' => 10,
                'description' => 'CSS applied to this widget.',
            ]
        );

        $this->add_control(
            'external_libs',
            [
                'label' => esc_html__('External JS Libraries (URLs)', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'url',
                        'label' => 'URL',
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'placeholder' => 'https://example.com/script.js',
                    ]
                ],
                'title_field' => '{{{ url }}}',
            ]
        );

        $this->add_control(
            'custom_js',
            [
                'label' => esc_html__('Custom JS', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'javascript',
                'description' => 'Executes after widget load.',
            ]
        );

        $this->end_controls_section();

        // Custom Loop Template (Hook)
        $this->register_content_controls();
    }

    protected function register_content_controls()
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
