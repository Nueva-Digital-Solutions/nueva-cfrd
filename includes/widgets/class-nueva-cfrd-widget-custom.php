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
            'section_custom_assets_config',
            [
                'label' => esc_html__('Custom Assets', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'debug_test',
            [
                'label' => esc_html__('Debug Display', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'If you see this, the section works. Then we enable others.',
            ]
        );

        /*
        $this->add_control(
            'nueva_custom_css',
            [
                'label' => esc_html__('Custom CSS', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'rows' => 10,
                'description' => 'CSS applied to this widget.',
            ]
        );

        $this->add_control(
            'css_libs',
            [
                'label' => esc_html__('External CSS Libraries (URLs)', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'url',
                        'label' => 'URL',
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'placeholder' => 'https://example.com/style.css',
                    ]
                ],
                'title_field' => '{{{ url }}}',
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
            'nueva_custom_js',
            [
                'label' => esc_html__('Custom JS', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'javascript',
                'description' => 'Executes after widget load.',
            ]
        );
        */

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
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // 1. Output External CSS Libs
        if (!empty($settings['css_libs'])) {
            foreach ($settings['css_libs'] as $lib) {
                if (!empty($lib['url'])) {
                    echo '<link rel="stylesheet" href="' . esc_url($lib['url']) . '">';
                }
            }
        }

        // 2. Output Custom CSS
        if (!empty($settings['nueva_custom_css'])) {
            echo '<style>' . $settings['nueva_custom_css'] . '</style>';
        }

        // 3. Output External JS Libs
        if (!empty($settings['external_libs'])) {
            foreach ($settings['external_libs'] as $lib) {
                if (!empty($lib['url'])) {
                    echo '<script src="' . esc_url($lib['url']) . '"></script>';
                }
            }
        }

        // 4. Call Parent Render (Main Loop)
        parent::render();

        // 5. Output Custom JS
        if (!empty($settings['nueva_custom_js'])) {
            echo '<script>' . $settings['nueva_custom_js'] . '</script>';
        }
    }
}
