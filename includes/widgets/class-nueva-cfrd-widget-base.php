<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Nueva CFRD Base Widget.
 *
 * Abstract base class for all Repeater Display widgets.
 *
 * @since 1.1.0
 */
abstract class Nueva_CFRD_Widget_Base extends \Elementor\Widget_Base
{

    public function get_categories()
    {
        return ['general'];
    }

    // Child classes must define these
    abstract public function get_layout_type();

    protected function register_controls()
    {
        $this->register_common_controls();
        $this->register_repeater_controls();
        $this->register_style_controls();
    }

    protected function register_common_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content source', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'repeater_field_name',
            [
                'label' => esc_html__('Repeater Field Name', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('e.g. home_faq', 'nueva-cfrd'),
                'description' => 'Enter the key of the ACF Repeater field.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'post_id_source',
            [
                'label' => esc_html__('Data Source', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'current',
                'options' => [
                    'current' => esc_html__('Current Post', 'nueva-cfrd'),
                    'custom' => esc_html__('Custom Post ID', 'nueva-cfrd'),
                    'option' => esc_html__('Option Page', 'nueva-cfrd'),
                    'taxonomy' => esc_html__('Current Taxonomy Term', 'nueva-cfrd'),
                ],
            ]
        );

        $this->add_control(
            'custom_post_id',
            [
                'label' => esc_html__('Post ID', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'condition' => [
                    'post_id_source' => 'custom',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_repeater_controls()
    {
        // --- SUB FIELDS REPEATER ---
        $this->start_controls_section(
            'repeater_config_section',
            [
                'label' => esc_html__('Structure & Fields', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'name',
            [
                'label' => esc_html__('Field Key', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'e.g. sub_field_key',
            ]
        );

        $repeater->add_control(
            'type',
            [
                'label' => esc_html__('Field Type', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'text',
                'options' => [
                    'text' => 'Span / Text',
                    'paragraph' => 'Paragraph (P)',
                    'heading' => 'Heading',
                    'image' => 'Image',
                    'video' => 'Video (URL)',
                    'link' => 'Link (Button/A)',
                    'html' => 'Raw HTML',
                ],
            ]
        );

        // Heading Tag
        $repeater->add_control(
            'heading_tag',
            [
                'label' => esc_html__('HTML Tag', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                ],
                'condition' => ['type' => ['heading', 'text', 'paragraph']],
            ]
        );

        // Image Controls
        $repeater->add_control(
            'image_size',
            [
                'label' => esc_html__('Width', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 1000],
                    '%' => ['min' => 0, 'max' => 100],
                ],
                'default' => ['unit' => '%', 'size' => 100],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .nueva-image' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['type' => 'image'],
            ]
        );

        // Video Controls
        $repeater->add_control(
            'video_ratio',
            [
                'label' => esc_html__('Aspect Ratio', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '169',
                'options' => [
                    '169' => '16:9',
                    '43' => '4:3',
                    '11' => '1:1',
                ],
                'condition' => ['type' => 'video'],
            ]
        );

        // Styling
        $repeater->add_control(
            'item_text_color',
            [
                'label' => esc_html__('Color', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .nueva-value' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}} h1, {{WRAPPER}} {{CURRENT_ITEM}} h2, {{WRAPPER}} {{CURRENT_ITEM}} h3' => 'color: {{VALUE}}',
                ],
                'condition' => ['type' => ['text', 'paragraph', 'heading', 'link', 'html']],
            ]
        );

        $repeater->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'item_typography',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .nueva-value',
                'condition' => ['type' => ['text', 'paragraph', 'heading', 'link']],
            ]
        );

        $this->add_control(
            'sub_fields_list',
            [
                'label' => esc_html__('Sub Fields Configuration', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'name' => 'title',
                        'type' => 'text'
                    ],
                ],
                'title_field' => '{{{ name }}}',
            ]
        );

        $this->end_controls_section();

        // Allow Child Classes to add Content Controls (e.g. Custom Loop Template)
        $this->register_content_controls();
    }

    protected function register_style_controls()
    {
        // --- STYLE TAB ---
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Items Style', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_bg_color',
            [
                'label' => esc_html__('Background Color', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nueva-card-builder' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .nueva-cfrd-item' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .nueva-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .nueva-card-builder, {{WRAPPER}} .nueva-cfrd-item, {{WRAPPER}} .nueva-card',
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .nueva-card-builder' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .nueva-cfrd-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .nueva-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .nueva-card-builder, {{WRAPPER}} .nueva-cfrd-item, {{WRAPPER}} .nueva-card',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => esc_html__('Padding', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .nueva-card-builder' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .nueva-cfrd-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .nueva-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Allow Child Classes to add Layout Specific Controls (e.g. Gap, Columns)
        $this->add_layout_controls();

        $this->end_controls_section();

        // Typography Section (Global Fallback)
        $this->start_controls_section(
            'typography_section',
            [
                'label' => esc_html__('Title/Text Typography', 'nueva-cfrd'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'label' => 'Text Typography',
                'selector' => '{{WRAPPER}} .nueva-value',
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => esc_html__('Text Color', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .nueva-value' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Virtual method for child classes to add controls in Style section
     */
    protected function add_layout_controls()
    {
    }

    /**
     * Virtual method for child classes to add extra sections/controls in Content Tab
     */
    protected function register_content_controls()
    {
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $repeater_name = $settings['repeater_field_name'];

        if (empty($repeater_name)) {
            return;
        }

        // Determine Post ID
        $post_id = get_the_ID();
        if ('custom' === $settings['post_id_source']) {
            $post_id = $settings['custom_post_id'];
        } elseif ('option' === $settings['post_id_source']) {
            $post_id = 'option';
        } elseif ('taxonomy' === $settings['post_id_source']) {
            $obj = get_queried_object();
            if ($obj instanceof \WP_Term) {
                // ACF format for terms is 'taxonomy_term_id' (e.g. category_123)
                $post_id = $obj->taxonomy . '_' . $obj->term_id;
            }
        }

        // Prepare Sub Fields array for Renderer
        $sub_fields = [];
        if (!empty($settings['sub_fields_list'])) {
            foreach ($settings['sub_fields_list'] as $field) {

                // Extract Config for Renderer
                $config = [
                    'name' => trim($field['name']),
                    'repeater_item_id' => $field['_id'], // Pass Elementor ID for styling
                    'type' => $field['type'],
                    'heading_tag' => $field['heading_tag'] ?? 'h3',
                    'video_ratio' => $field['video_ratio'] ?? '169',
                    // Image/Text Styling is handled via Elementor Selectors CSS, 
                    // so we don't strictly need to pass style strings manually anymore,
                    // BUT Renderer needs to know the TAG to render.
                ];

                $sub_fields[] = $config;
            }
        }

        // Init Renderer
        require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-renderer.php';

        $renderer_args = [
            'id' => '', // Manual mode
            'post_id' => $post_id,
            'field' => $repeater_name,
            'layout' => $this->get_layout_type(),
            'class' => 'nueva-elementor-widget',
            'sub_fields' => $sub_fields,
            // Pass settings for children usage (e.g. columns) if needed, 
            // though Renderer primarily uses 'atts'.
            // For now, grid columns are handled by CSS selectors in Elementor, 
            // but we can pass 'columns' if Renderer uses it logically.
            'columns' => $settings['grid_columns'] ?? 3,
        ];

        // Merge extra args from child?
        $renderer_args = array_merge($renderer_args, $this->get_renderer_args($settings));

        $renderer = new \Nueva_CFRD_Renderer($renderer_args);

        // Handle Custom Loop
        if ($this->get_layout_type() === 'custom') {
            echo $renderer->render_custom_loop($settings['custom_template'] ?? '');
        } else {
            echo $renderer->render();
        }
    }

    protected function get_renderer_args($settings)
    {
        return [];
    }
}
