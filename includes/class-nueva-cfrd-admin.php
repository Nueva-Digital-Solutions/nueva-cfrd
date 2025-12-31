<?php

class Nueva_CFRD_Admin
{

    public function run()
    {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook)
    {
        global $post_type;
        if ('nueva_layout' !== $post_type)
            return;

        wp_enqueue_style('nueva-admin-css', NUEVA_CFRD_URL . 'assets/css/admin.css', array(), NUEVA_CFRD_VERSION);
        wp_enqueue_script('nueva-admin-js', NUEVA_CFRD_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), NUEVA_CFRD_VERSION, true);
        wp_enqueue_style('wp-color-picker');
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'nueva_cfrd_config',
            'Layout Configuration',
            array($this, 'render_meta_box'),
            'nueva_layout',
            'normal',
            'high'
        );

        add_meta_box(
            'nueva_cfrd_shortcode',
            'Generate Shortcode',
            array($this, 'render_shortcode_box'),
            'nueva_layout',
            'side',
            'high'
        );
    }

    public function render_shortcode_box($post)
    {
        echo '<div class="nueva-shortcode-display">';
        echo '<p>Copy this shortcode to display the layout:</p>';
        echo '<code style="display:block; padding: 10px; background: #f0f0f1; border:1px solid #ddd;">[nueva_cfrd id="' . $post->ID . '"]</code>';
        echo '</div>';
    }

    public function render_meta_box($post)
    {
        wp_nonce_field('nueva_cfrd_save_data', 'nueva_cfrd_meta_nonce');

        $field_name = get_post_meta($post->ID, 'nueva_field_name', true);
        $layout_type = get_post_meta($post->ID, 'nueva_layout_type', true) ?: 'grid';
        $columns = get_post_meta($post->ID, 'nueva_columns', true) ?: '3';
        $container = get_post_meta($post->ID, 'nueva_container_styles', true) ?: array();
        $sub_fields = get_post_meta($post->ID, 'nueva_sub_fields', true) ?: array();
        ?>

        <div class="nueva-admin-container">

            <!-- General Settings -->
            <div class="nueva-section">
                <h3 class="nueva-section-title">Data Source</h3>
                <div class="nueva-form-row">
                    <label>Repeater Field Name (ACF Name)</label>
                    <input type="text" name="nueva_field_name" value="<?php echo esc_attr($field_name); ?>" class="widefat"
                        placeholder="e.g. team_members">
                </div>
            </div>

            <!-- Layout Settings -->
            <div class="nueva-section">
                <h3 class="nueva-section-title">Display Settings</h3>
                <div class="nueva-form-row two-col">
                    <div>
                        <label>Layout Type</label>
                        <select name="nueva_layout_type" class="widefat">
                            <?php
                            $layouts = ['grid', 'list', 'slider', 'accordion', 'card-deck', 'masonry', 'timeline', 'master-detail'];
                            foreach ($layouts as $l) {
                                echo '<option value="' . $l . '" ' . selected($layout_type, $l, false) . '>' . ucfirst($l) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label>Columns (Grid/Masonry)</label>
                        <input type="number" name="nueva_columns" value="<?php echo esc_attr($columns); ?>" max="12" min="1"
                            class="widefat">
                    </div>
                </div>
            </div>

            <!-- Container Styles -->
            <div class="nueva-section">
                <h3 class="nueva-section-title">Container Styling</h3>
                <div class="nueva-form-row three-col">
                    <div>
                        <label>Padding (e.g. 20px)</label>
                        <input type="text" name="nueva_container_styles[padding]"
                            value="<?php echo esc_attr($container['padding'] ?? ''); ?>" class="widefat">
                    </div>
                    <div>
                        <label>Background Color</label>
                        <input type="text" name="nueva_container_styles[bg_color]"
                            value="<?php echo esc_attr($container['bg_color'] ?? ''); ?>" class="nueva-color-picker">
                    </div>
                    <div>
                        <label>Border Radius (px)</label>
                        <input type="number" name="nueva_container_styles[radius]"
                            value="<?php echo esc_attr($container['radius'] ?? ''); ?>" class="widefat">
                    </div>
                </div>
            </div>

            <!-- Field Mapping & Styling -->
            <div class="nueva-section">
                <h3 class="nueva-section-title">Sub Fields & Styling</h3>
                <p class="description">Add the sub-fields you want to display and style them.</p>

                <div id="nueva-sub-fields-wrapper">
                    <?php
                    if (!empty($sub_fields)) {
                        foreach ($sub_fields as $index => $field) {
                            $this->render_sub_field_row($index, $field);
                        }
                    }
                    ?>
                </div>

                <button type="button" class="button button-primary" id="nueva-add-field">Add Sub Field</button>
            </div>

        </div>

        <!-- Template for JS -->
        <script type="text/template" id="nueva-field-template">
                    <?php $this->render_sub_field_row('{{INDEX}}', array()); ?>
                </script>

        <?php
    }

    private function render_sub_field_row($index, $field)
    {
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? ''; // e.g. Title, Subtitle, Meta
        $color = $field['color'] ?? '';
        $size = $field['size'] ?? '';
        $weight = $field['weight'] ?? '';
        $tag = $field['tag'] ?? 'div';
        ?>
        <div class="nueva-field-row" data-index="<?php echo esc_attr($index); ?>">
            <div class="nueva-field-header">
                <span class="dashicons dashicons-sort"></span>
                <strong>Field Item</strong>
                <button type="button" class="button-link nueva-remove-field" style="color: #b32d2e;">Remove</button>
            </div>
            <div class="nueva-field-body">
                <div class="nueva-form-row three-col">
                    <div>
                        <label>Sub Field Name (Key)</label>
                        <input type="text" name="nueva_sub_fields[<?php echo $index; ?>][name]"
                            value="<?php echo esc_attr($name); ?>" class="widefat" placeholder="e.g. item_title">
                    </div>
                    <div>
                        <label>HTML Tag</label>
                        <select name="nueva_sub_fields[<?php echo $index; ?>][tag]" class="widefat">
                            <?php foreach (['h1', 'h2', 'h3', 'h4', 'div', 'p', 'span', 'img'] as $t)
                                echo '<option value="' . $t . '" ' . selected($tag, $t, false) . '>' . strtoupper($t) . '</option>'; ?>
                        </select>
                    </div>
                    <div>
                        <label>Font Size (px)</label>
                        <input type="number" name="nueva_sub_fields[<?php echo $index; ?>][size]"
                            value="<?php echo esc_attr($size); ?>" class="widefat">
                    </div>
                </div>
                <div class="nueva-form-row three-col">
                    <div>
                        <label>Text Color</label>
                        <input type="text" name="nueva_sub_fields[<?php echo $index; ?>][color]"
                            value="<?php echo esc_attr($color); ?>" class="nueva-color-picker">
                    </div>
                    <div>
                        <label>Font Weight</label>
                        <select name="nueva_sub_fields[<?php echo $index; ?>][weight]" class="widefat">
                            <option value="normal" <?php selected($weight, 'normal'); ?>>Normal</option>
                            <option value="bold" <?php selected($weight, 'bold'); ?>>Bold</option>
                            <option value="600" <?php selected($weight, '600'); ?>>Semibold</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function save_meta($post_id)
    {
        if (!isset($_POST['nueva_cfrd_meta_nonce']) || !wp_verify_nonce($_POST['nueva_cfrd_meta_nonce'], 'nueva_cfrd_save_data')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!current_user_can('edit_post', $post_id))
            return;

        // Save fields
        $fields_to_save = [
            'nueva_field_name',
            'nueva_layout_type',
            'nueva_columns',
            'nueva_container_styles',
            'nueva_sub_fields'
        ];

        foreach ($fields_to_save as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, $_POST[$field]);
            } else {
                delete_post_meta($post_id, $field);
            }
        }
    }
}
