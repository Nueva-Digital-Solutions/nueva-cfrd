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

        $repeater->add_control(
            'show_label',
            [
                'label' => esc_html__('Show Label?', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
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
                        'type' => 'text',
                        'show_label' => 'yes'
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
        }

        // Prepare Sub Fields array for Renderer
        $sub_fields = [];
        if (!empty($settings['sub_fields_list'])) {
            foreach ($settings['sub_fields_list'] as $field) {
                // Ensure name is clean, critical for matching logic
                $sub_fields[] = [
                    'name' => trim($field['name']), 
                    'type' => $field['type'],
                    'show_label' => $field['show_label'] === 'yes'
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
}
