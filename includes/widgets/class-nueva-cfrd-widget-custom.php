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
            'template_source',
            [
                'label' => esc_html__('Template Source', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'custom' => esc_html__('Custom Code', 'nueva-cfrd'),
                    'preset' => esc_html__('Preset Layout', 'nueva-cfrd'),
                ],
            ]
        );

        $this->add_control(
            'preset_type',
            [
                'label' => esc_html__('Preset Type', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => 'Card Grid',
                    'list' => 'List View',
                    'slider' => 'Slider / Carousel',
                    'accordion' => 'Accordion',
                    'table' => 'Simple Table',
                ],
                'condition' => ['template_source' => 'preset'],
            ]
        );

        // --- Field Mapping Controls ---
        $this->add_control(
            'key_title',
            [
                'label' => 'Title Field Key',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'title',
                'condition' => ['template_source' => 'preset'],
            ]
        );
        $this->add_control(
            'key_desc',
            [
                'label' => 'Description / Content Key',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'description',
                'condition' => ['template_source' => 'preset'],
            ]
        );
        $this->add_control(
            'key_image',
            [
                'label' => 'Image Key (URL)',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'image',
                'condition' => ['template_source' => 'preset'],
            ]
        );
        $this->add_control(
            'key_link',
            [
                'label' => 'Link Key (URL)',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'link',
                'condition' => ['template_source' => 'preset'],
            ]
        );
        $this->add_control(
            'key_button',
            [
                'label' => 'Button Text',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Read More',
                'condition' => ['template_source' => 'preset'],
            ]
        );

        $this->add_control(
            'custom_template',
            [
                'label' => esc_html__('HTML Template', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'html',
                'rows' => 20,
                'condition' => ['template_source' => 'custom'], // Hide if Preset
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

        // Pre-Process Presets
        if (isset($settings['template_source']) && $settings['template_source'] === 'preset') {
            if (class_exists('Nueva_CFRD_Templates')) {
                $keys = [
                    'key_title' => $settings['key_title'] ?? 'title',
                    'key_desc' => $settings['key_desc'] ?? 'description',
                    'key_image' => $settings['key_image'] ?? 'image',
                    'key_link' => $settings['key_link'] ?? 'link',
                    'key_button' => $settings['key_button'] ?? 'Read More',
                ];

                $config = Nueva_CFRD_Templates::get_config($settings['preset_type'], $keys);

                if (!empty($config['html'])) {
                    $settings['custom_template'] = $config['html'];
                }
                if (!empty($config['css'])) {
                    $settings['nueva_custom_css'] .= "\n" . $config['css'];
                }
                if (!empty($config['wrapper_class'])) {
                    $this->add_render_attribute('wrapper', 'class', $config['wrapper_class']);
                }
                if (!empty($config['change_wrapper_tag'])) {
                    $settings['wrapper_tag'] = $config['change_wrapper_tag'];
                    $settings['pre_wrapper_html'] = $config['pre_wrapper_html'] ?? '';
                    $settings['post_wrapper_html'] = $config['post_wrapper_html'] ?? '';
                }
                $this->add_render_attribute('wrapper', 'data-preset', $settings['preset_type']);
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

        // 4. Render Main Loop

        // Determine Post ID (Copied from Widget_Base)
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
            'sub_fields' => [], // Custom loop handles its own sub-fields via templating
        ];

        $renderer = new \Nueva_CFRD_Renderer($renderer_args);

        $wrapper_args = [
            'tag' => $settings['wrapper_tag'] ?? 'div',
            'pre_html' => $settings['pre_wrapper_html'] ?? '',
            'post_html' => $settings['post_wrapper_html'] ?? '',
            'class' => $this->get_render_attribute_string('wrapper'),
            'attrs' => $this->get_render_attribute_string('wrapper'),
        ];

        // Ensure we pass the template to the renderer
        echo $renderer->render_custom_loop($settings['custom_template'] ?? '', $wrapper_args);

        // 5. Output Custom JS
        if (!empty($settings['nueva_custom_js'])) {
            echo '<script>' . $settings['nueva_custom_js'] . '</script>';
        }
    }
}
