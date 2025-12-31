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

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'nueva_cfrd_widget';
    }

    /**
     * Get widget title.
     *
     * Retrieve widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return esc_html__('ACF Repeater Display', 'nueva-cfrd');
    }

    /**
     * Get widget icon.
     *
     * Retrieve widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-code';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
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
            ]
        );

        $this->add_control(
            'sub_field_name',
            [
                'label' => esc_html__('Sub Field(s)', 'nueva-cfrd'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('e.g. home_question', 'nueva-cfrd'),
                'description' => 'Enter the Sub Field key to display. For multiple, separate by comma.',
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

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
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

        // Prepare Sub Fields
        $sub_fields = [];
        if (!empty($settings['sub_field_name'])) {
            $parts = explode(',', $settings['sub_field_name']);
            foreach ($parts as $part) {
                $sub_fields[] = ['name' => trim($part)];
            }
        }

        // Init Renderer
        // We need to pass 'id' as null to force manual mode
        require_once NUEVA_CFRD_PATH . 'includes/class-nueva-cfrd-renderer.php';

        $renderer_args = [
            'id' => '', // Manual mode
            'post_id' => $post_id,
            'field' => $repeater_name,
            'layout' => $settings['layout_type'],
            'class' => 'nueva-elementor-widget',
            'sub_fields' => $sub_fields
        ];

        $renderer = new \Nueva_CFRD_Renderer($renderer_args);
        echo $renderer->render();
    }

}
