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
        // 1. Data Source Controls
        $this->register_common_controls();

        // 2. Custom Loop Template (HTML)
        $this->start_controls_section(
            'section_custom_template',
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
    <h3>{{title}}</h3>
    <p>{{description}}</p>
</div>',
                'description' => 'Enter your HTML layout. Use {{field_key}} for sub-fields.',
            ]
        );

        $this->add_control(
            'wrapper_tag',
            [
                'label' => esc_html__('Wrapper Tag', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'div',
                'options' => [
                    'div' => 'div',
                    'ul' => 'ul',
                    'ol' => 'ol',
                    'table' => 'table',
                    'tbody' => 'tbody',
                ],
            ]
        );

        $this->add_control(
            'wrapper_custom_class',
            [
                'label' => esc_html__('Wrapper Class', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->end_controls_section();

        // 3. Custom Assets (CSS & JS)
        $this->start_controls_section(
            'section_custom_assets_final',
            [
                'label' => esc_html__('Custom Assets', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'custom_css_code',
            [
                'label' => esc_html__('Custom CSS', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'rows' => 10,
            ]
        );

        $this->add_control(
            'custom_js_code',
            [
                'label' => esc_html__('Custom JS', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'javascript',
                'rows' => 10,
                'description' => 'Run after widget load.',
            ]
        );

        $this->add_control(
            'custom_css_libs',
            [
                'label' => esc_html__('External CSS URLs', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'url',
                        'label' => 'URL',
                        'type' => \Elementor\Controls_Manager::TEXT,
                    ]
                ],
                'title_field' => '{{{ url }}}',
            ]
        );

        $this->add_control(
            'custom_js_libs',
            [
                'label' => esc_html__('External JS URLs', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'url',
                        'label' => 'URL',
                        'type' => \Elementor\Controls_Manager::TEXT,
                    ]
                ],
                'title_field' => '{{{ url }}}',
            ]
        );

        $this->end_controls_section();
    }

    // Override empty function to prevent confusion from base class calls if any
    protected function register_content_controls()
    {
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // 1. Output External CSS Libs
        if (!empty($settings['custom_css_libs'])) {
            foreach ($settings['custom_css_libs'] as $lib) {
                if (!empty($lib['url'])) {
                    echo '<link rel="stylesheet" href="' . esc_url($lib['url']) . '">';
                }
            }
        }

        // 2. Output Custom CSS (Code)
        if (!empty($settings['custom_css_code'])) {
            echo '<style>' . $settings['custom_css_code'] . '</style>';
        }

        // 3. Output External JS Libs
        if (!empty($settings['custom_js_libs'])) {
            foreach ($settings['custom_js_libs'] as $lib) {
                if (!empty($lib['url'])) {
                    echo '<script src="' . esc_url($lib['url']) . '"></script>';
                }
            }
        }

        // 4. Render Main Loop
        if (!class_exists('Nueva_CFRD_Renderer')) {
            require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-renderer.php';
        }

        // Determine Post ID
        $post_id = get_the_ID();
        if (isset($settings['post_id_source'])) {
            if ('custom' === $settings['post_id_source']) {
                $post_id = $settings['custom_post_id'];
            } elseif ('option' === $settings['post_id_source']) {
                $post_id = 'option';
            } elseif ('taxonomy' === $settings['post_id_source']) {
                $obj = get_queried_object();
                if ($obj instanceof \WP_Term) {
                    $post_id = $obj->taxonomy . '_' . $obj->term_id;
                }
            }
        }

        $renderer_args = [
            'id' => '',
            'post_id' => $post_id,
            'field' => $settings['repeater_field_name'],
            'layout' => 'custom',
            'class' => 'nueva-elementor-widget',
            'sub_fields' => [],
        ];

        $renderer = new \Nueva_CFRD_Renderer($renderer_args);

        $wrapper_args = [
            'tag' => $settings['wrapper_tag'] ?? 'div',
            'class' => $settings['wrapper_custom_class'] ?? '',
            'attrs' => $this->get_render_attribute_string('wrapper'),
        ];

        // Render with Template
        echo $renderer->render_custom_loop($settings['custom_template'] ?? '', $wrapper_args);

        // 5. Output Custom JS (Code)
        if (!empty($settings['custom_js_code'])) {
            echo '<script>' . $settings['custom_js_code'] . '</script>';
        }
    }
}
