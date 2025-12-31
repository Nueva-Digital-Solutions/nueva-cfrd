<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Nueva CFRD Elementor Widget.
 *
 * Elementor widget that displays ACF Repeater data.
 *
 * @since 1.0.0
 */
class Nueva_CFRD_Elementor_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'nueva_cfrd_widget';
    }

    public function get_title()
    {
        return esc_html__('ACF Repeater Display', 'nueva-cfrd');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'nueva-cfrd'),
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

        // --- SUB FIELDS REPEATER ---
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
                    'text' => 'Text',
                    'image' => 'Image (URL/Array)',
                    'link' => 'Link (URL/Array)',
                    'html' => 'HTML / WYSIWYG',
                ],
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
        // --- END REPEATER ---

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__('Layout', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => 'Grid',
                    'list' => 'List',
                    'table' => 'Table',
                    'accordion' => 'Accordion',
                    'slider' => 'Slider',
                ],
            ]
        );

        $this->end_controls_section();

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
                    '{{WRAPPER}} .nueva-cfrd-item' => 'background-color: {{VALUE}}', // fallback pattern
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .nueva-card-builder, {{WRAPPER}} .nueva-cfrd-item',
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
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .nueva-card-builder, {{WRAPPER}} .nueva-cfrd-item',
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
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_gap',
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
                    '{{WRAPPER}} .nueva-cfrd-grid' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .nueva-cfrd-list' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Grid Columns Control (Responsive)
        $this->add_responsive_control(
            'grid_columns',
            [
                'label' => esc_html__('Grid Columns', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'selectors' => [
                    '{{WRAPPER}} .nueva-cfrd-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
                'condition' => [
                    'layout_type' => 'grid',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'typography_section',
            [
                'label' => esc_html__('Typography', 'nueva-cfrd'),
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
                // Ensure name is clean, critical for matching logic
                $sub_fields[] = [
                    'name' => trim($field['name']),
                    'type' => $field['type']
                ];
            }
        }

        // Init Renderer
        require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-renderer.php';

        $renderer_args = [
            'id' => '', // Manual mode
            'post_id' => $post_id,
            'field' => $repeater_name,
            'layout' => $settings['layout_type'],
            'class' => 'nueva-elementor-widget',
            'sub_fields' => $sub_fields,
            'columns' => 3 // Default, but overridden by CSS mostly
        ];

        $renderer = new \Nueva_CFRD_Renderer($renderer_args);
        echo $renderer->render();
    }
}
