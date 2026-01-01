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
            'section_nueva_code',
            [
                'label' => esc_html__('Custom Assets', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'n_css_code',
            [
                'label' => esc_html__('Custom CSS', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'rows' => 10,
                'description' => 'CSS applied to this widget.',
            ]
        );

        $this->add_control(
            'n_css_libs',
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
            'n_js_libs',
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
            'n_js_code',
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

        // Wrapper Controls (Exposed for Presets)
        $this->add_control(
            'wrapper_tag',
            [
                'label' => esc_html__('Wrapper HTML Tag', 'nueva-cfrd'),
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
                'description' => 'Classes added to the main container.',
            ]
        );

        $this->add_control(
            'custom_template',
            [
                'label' => esc_html__('HTML Template', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'html',
                'rows' => 20,
                // 'condition' => ['template_source' => 'custom'], // Always visible for editing
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
        if (!empty($settings['n_css_libs'])) {
            foreach ($settings['n_css_libs'] as $lib) {
                if (!empty($lib['url'])) {
                    echo '<link rel="stylesheet" href="' . esc_url($lib['url']) . '">';
                }
            }
        }

        // Note: Preset Logic removed in favor of JS injection in Editor.
        // We rely on what is saved in 'custom_template' and 'n_css_code'.

        // 2. Output Custom CSS
        if (!empty($settings['n_css_code'])) {
            echo '<style>' . $settings['n_css_code'] . '</style>';
        }

        // 3. Output External JS Libs
        if (!empty($settings['n_js_libs'])) {
            foreach ($settings['n_js_libs'] as $lib) {
                if (!empty($lib['url'])) {
                    echo '<script src="' . esc_url($lib['url']) . '"></script>';
                }
            }
        }

        // 4. Render Main Loop

        // Ensure Renderer Class is loaded
        if (!class_exists('Nueva_CFRD_Renderer')) {
            require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-renderer.php';
        }

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
            'pre_html' => $settings['pre_wrapper_html'] ?? '', // Only set by JS hidden field if added, currently standard controls don't have pre_html exposed but we can add later if needed. For now sticking to tag.
            'post_html' => $settings['post_wrapper_html'] ?? '',
            'class' => $settings['wrapper_custom_class'] ?? '',
            'attrs' => $this->get_render_attribute_string('wrapper'),
        ];

        // Inject preset data attr derived from settings if needed, or rely on JS?
        // We can just add data-preset if we really want to track it
        if (!empty($settings['preset_type'])) {
            // We can't easily add attributes to the renderer's wrapper blindly without using $renderer calls.
            // But $wrapper_args handles 'attrs'.
            $wrapper_args['attrs'] .= ' data-preset="' . esc_attr($settings['preset_type']) . '"';
        }

        // Ensure we pass the template to the renderer
        echo $renderer->render_custom_loop($settings['custom_template'] ?? '', $wrapper_args);

        // 5. Output Custom JS
        if (!empty($settings['n_js_code'])) {
            echo '<script>' . $settings['n_js_code'] . '</script>';
        }
    }
}
