<?php

class Nueva_CFRD_Admin
{

    public function run()
    {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_nueva_fetch_fields', array($this, 'ajax_fetch_fields'));
    }

    public function enqueue_admin_assets($hook)
    {
        global $post_type;
        if ('nueva_layout' !== $post_type)
            return;

        wp_enqueue_style('nueva-admin-css', NUEVA_CFRD_URL . 'assets/css/admin.css', array(), NUEVA_CFRD_VERSION);
        wp_enqueue_script('nueva-admin-js', NUEVA_CFRD_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), NUEVA_CFRD_VERSION, true);
        wp_localize_script('nueva-admin-js', 'nueva_vars', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_style('wp-color-picker');
    }

	public function ajax_fetch_fields() {
		$source_val = $_POST['post_id']; // Can be '123' or 'option_slug'
		if ( ! $source_val ) wp_send_json_error( 'Invalid Source' );

		$repeaters = array();
		
		// Check if Option Page
		if ( strpos( $source_val, 'option_' ) === 0 ) {
			// It's an option page, logic differs slightly. ACF stores with no ID or specific ID.
			// Usually get_fields('option') works.
			// But for direct meta scan, we look at wp_options where option_name LIKE 'options_%' ?
			// Actually ACF uses 'options' post ID for option pages often.
			// Let's rely on standard logic:
			$all_meta = get_fields( 'option' ); // If ACF exists
			if ( $all_meta ) {
				foreach ( $all_meta as $key => $val ) {
					if ( is_array($val) && !empty($val) && isset($val[0]) && is_array($val[0]) ) {
						$repeaters[$key] = array_keys($val[0]);
					}
				}
			}
		} else {
			// Helper to recursively finding array of arrays (repeater structure) in meta
			$post_id = intval($source_val);
			$all_meta = get_post_meta( $post_id );
			
			foreach ( $all_meta as $key => $values ) {
				if ( strpos( $key, '_' ) === 0 ) continue;
				$val = $values[0];
				
				// 1. Try Unserialize
				$data = @unserialize( $val );
				if ( $data === false && is_serialized( $val ) ) $data = unserialize( $val );
				
				// 2. Try ACF get_field if it didn't look like serialized array or just to be sure
				if ( function_exists('get_field') && ( !is_array($data) || empty($data) ) ) {
					$acf_data = get_field( $key, $post_id );
					if ( is_array($acf_data) ) $data = $acf_data;
				}

				if ( is_array( $data ) && ! empty( $data ) && isset( $data[0] ) && is_array( $data[0] ) ) {
					$repeaters[ $key ] = array_keys( $data[0] );
				}
			}
		}
		
		wp_send_json_success( $repeaters );
	}

    public function add_meta_boxes()
    {
        add_meta_box('nueva_cfrd_main', 'Layout Builder', array($this, 'render_main_box'), 'nueva_layout', 'normal', 'high');
        add_meta_box('nueva_cfrd_shortcode', 'Generate Shortcode', array($this, 'render_shortcode_box'), 'nueva_layout', 'side', 'high');
        add_meta_box('nueva_cfrd_classes', 'CSS Class Reference', array($this, 'render_classes_box'), 'nueva_layout', 'side', 'default');
    }

    // --- Helper Views ---

    public function render_shortcode_box($post)
    {
        echo '<div class="nueva-shortcode-display">';
        echo '<p>Copy this shortcode to display the layout:</p>';
        echo '<code style="display:block; padding: 10px; background: #f0f0f1; border:1px solid #ddd;">[nueva_cfrd id="' . $post->ID . '"]</code>';
        echo '</div>';
    }

    public function render_classes_box($post)
    {
        echo '<ul>';
        echo '<li><code>.nueva-card-builder</code> - Card Container</li>';
        echo '<li><code>.nueva-field-{index}</code> - Field Item</li>';
        echo '<li><code>:hover</code> - Hover State</li>';
        echo '</ul>';
    }

    // --- MAIN RENDERER ---

    public function render_main_box($post)
    {
        wp_nonce_field('nueva_cfrd_save_data', 'nueva_cfrd_meta_nonce');

        // Load Data
        $field_name = get_post_meta($post->ID, 'nueva_field_name', true);
        $layout_type = get_post_meta($post->ID, 'nueva_layout_type', true) ?: 'grid';
        $columns = get_post_meta($post->ID, 'nueva_columns', true) ?: '3';
        $sub_fields = get_post_meta($post->ID, 'nueva_sub_fields', true) ?: array();
        $custom_css = get_post_meta($post->ID, 'nueva_custom_css', true);

        // Style Config (Array of normal/hover)
        $style_config = get_post_meta($post->ID, 'nueva_style_config', true);
        $style_normal = $style_config['normal'] ?? [];
        $style_hover = $style_config['hover'] ?? [];
        ?>

        <div class="nueva-admin-container">

            <!-- Tabs Nav -->
            <div class="nueva-tabs">
                <a href="#" class="nueva-tab-link active" data-tab="tab-config">1. Configuration</a>
                <a href="#" class="nueva-tab-link" data-tab="tab-styling">2. Styling (Global)</a>
                <a href="#" class="nueva-tab-link" data-tab="tab-css">3. Custom CSS</a>
            </div>

            <!-- TAB 1: CONFIGURATION -->
            <div id="tab-config" class="nueva-tab-content active">

                <!-- Data Source -->
                <div class="nueva-section">
                    <h3 class="nueva-section-title">Data Source</h3>
                    <div class="nueva-discovery-box">
                        <strong>Autodetect Fields:</strong>
                        <input type="number" id="nueva-demo-post-id" class="small-text" placeholder="Post ID">
                        <button type="button" class="button button-secondary" id="nueva-fetch-fields-btn">Detect</button>
                        <span id="nueva-fetch-status"></span>
                    </div>
                    <div class="nueva-form-row">
                        <label>Repeater Field Name</label>
                        <div style="display:flex; gap:10px;">
                            <input type="text" name="nueva_field_name" id="nueva_field_name"
                                value="<?php echo esc_attr($field_name); ?>" class="widefat">
                            <select id="nueva_detected_repeaters" style="display:none;"></select>
                        </div>
                    </div>
                </div>

                <!-- Layout -->
                <div class="nueva-section">
                    <h3 class="nueva-section-title">Layout</h3>
                    <div class="nueva-form-row two-col">
                        <div>
                            <label>Layout Type</label>
                            <select name="nueva_layout_type" class="widefat">
                                <?php foreach (['grid', 'list', 'slider', 'accordion', 'card-deck', 'masonry', 'timeline', 'master-detail'] as $l)
                                    echo '<option value="' . $l . '" ' . selected($layout_type, $l, false) . '>' . ucfirst($l) . '</option>'; ?>
                            </select>
                        </div>
                        <div>
                            <label>Columns (Grid)</label>
                            <input type="number" name="nueva_columns" value="<?php echo esc_attr($columns); ?>" max="12"
                                min="1" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- Sub Fields Repeater (Simplified) -->
                <div class="nueva-section">
                    <h3 class="nueva-section-title">Sub Fields (Content)</h3>
                    <div id="nueva-sub-fields-wrapper">
                        <?php if (!empty($sub_fields))
                            foreach ($sub_fields as $i => $f)
                                $this->render_sub_field_row($i, $f); ?>
                    </div>
                    <button type="button" class="button button-primary" id="nueva-add-field">Add Sub Field</button>
                </div>

            </div>

            <!-- TAB 2: STYLING -->
            <div id="tab-styling" class="nueva-tab-content">

                <div style="margin-bottom:20px; text-align:center;">
                    <button type="button" class="button button-secondary nueva-style-state-tab active"
                        data-state="normal">Normal State</button>
                    <button type="button" class="button button-secondary nueva-style-state-tab" data-state="hover">Hover
                        State</button>
                </div>

                <!-- Normal State -->
                <div class="nueva-style-state-content nueva-style-state-normal">
                    <?php $this->render_style_inputs('normal', $style_normal); ?>
                </div>

                <!-- Hover State -->
                <div class="nueva-style-state-content nueva-style-state-hover" style="display:none;">
                    <?php $this->render_style_inputs('hover', $style_hover); ?>
                </div>

            </div>

            <!-- TAB 3: CUSTOM CSS -->
            <div id="tab-css" class="nueva-tab-content">
                <textarea name="nueva_custom_css" class="widefat" rows="15"
                    style="font-family:monospace; background:#2d2d2d; color:#fff;"><?php echo esc_textarea($custom_css); ?></textarea>
            </div>

        </div>

        <script type="text/template" id="nueva-field-template">
                    <?php $this->render_sub_field_row('{{INDEX}}', array()); ?>
                </script>
        <?php
    }

    private function render_sub_field_row($index, $field)
    {
        $name = $field['name'] ?? '';
        $tag = $field['tag'] ?? 'div';
        ?>
        <div class="nueva-field-row">
            <div class="nueva-form-row three-col" style="margin-bottom:0;">
                <div><input type="text" name="nueva_sub_fields[<?php echo $index; ?>][name]"
                        value="<?php echo esc_attr($name); ?>" class="widefat" placeholder="Field Key"></div>
                <div>
                    <select name="nueva_sub_fields[<?php echo $index; ?>][tag]" class="widefat">
                        <?php foreach (['div', 'h1', 'h2', 'h3', 'h4', 'p', 'span', 'img', 'a'] as $t)
                            echo '<option value="' . $t . '" ' . selected($tag, $t, false) . '>' . strtoupper($t) . '</option>'; ?>
                    </select>
                </div>
                <div style="text-align:right;"><button type="button" class="button-link nueva-remove-field"
                        style="color:red;">Remove</button></div>
            </div>
        </div>
        <?php
    }

    private function render_style_inputs($state, $vals)
    {
        $prefix = "nueva_style_config[$state]";
        $fonts = ['Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Oswald', 'Source Sans Pro', 'Slabo 27px', 'Raleway', 'PT Sans', 'Merriweather', 'Noto Sans', 'Nunito', 'Concert One', 'Poppins', 'Playfair Display'];
        ?>

        <div class="nueva-section">
            <h4 class="nueva-section-title">Typography & Colors</h4>
            <div class="nueva-form-row three-col">
                <div>
                    <label>Font Family</label>
                    <select name="<?php echo $prefix; ?>[font_family]" class="widefat">
                        <option value="">Default</option>
                        <?php foreach ($fonts as $f)
                            echo '<option value="' . $f . '" ' . selected($vals['font_family'] ?? '', $f, false) . '>' . $f . '</option>'; ?>
                    </select>
                </div>
                <div>
                    <label>Text Color</label>
                    <input type="text" name="<?php echo $prefix; ?>[color]" value="<?php echo esc_attr($vals['color'] ?? ''); ?>"
                        class="nueva-color-picker">
                </div>
                <div>
                    <label>Background Color</label>
                    <input type="text" name="<?php echo $prefix; ?>[bg_color]"
                        value="<?php echo esc_attr($vals['bg_color'] ?? ''); ?>" class="nueva-color-picker">
                </div>
            </div>
        </div>

        <div class="nueva-section">
            <h4 class="nueva-section-title">Box Model (Top / Right / Bottom / Left)</h4>

            <div class="nueva-form-row">
                <label>Margin (px)</label>
                <div class="nueva-quad-input">
                    <?php foreach (['top', 'right', 'bottom', 'left'] as $s)
                        echo '<input type="number" placeholder="' . ucfirst($s) . '" name="' . $prefix . '[margin][' . $s . ']" value="' . esc_attr($vals['margin'][$s] ?? '') . '">'; ?>
                </div>
            </div>

            <div class="nueva-form-row">
                <label>Padding (px)</label>
                <div class="nueva-quad-input">
                    <?php foreach (['top', 'right', 'bottom', 'left'] as $s)
                        echo '<input type="number" placeholder="' . ucfirst($s) . '" name="' . $prefix . '[padding][' . $s . ']" value="' . esc_attr($vals['padding'][$s] ?? '') . '">'; ?>
                </div>
            </div>

            <div class="nueva-form-row">
                <label>Border Width (px)</label>
                <div class="nueva-quad-input">
                    <?php foreach (['top', 'right', 'bottom', 'left'] as $s)
                        echo '<input type="number" placeholder="' . ucfirst($s) . '" name="' . $prefix . '[border][' . $s . ']" value="' . esc_attr($vals['border'][$s] ?? '') . '">'; ?>
                </div>
            </div>

            <div class="nueva-form-row">
                <label>Border Color</label>
                <input type="text" name="<?php echo $prefix; ?>[border_color]"
                    value="<?php echo esc_attr($vals['border_color'] ?? ''); ?>" class="nueva-color-picker">
            </div>
        </div>
        <?php
    }

    public function save_meta($post_id)
    {
        if (!isset($_POST['nueva_cfrd_meta_nonce']) || !wp_verify_nonce($_POST['nueva_cfrd_meta_nonce'], 'nueva_cfrd_save_data'))
            return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!current_user_can('edit_post', $post_id))
            return;

        $fields = ['nueva_field_name', 'nueva_layout_type', 'nueva_columns', 'nueva_sub_fields', 'nueva_custom_css', 'nueva_style_config'];
        foreach ($fields as $field) {
            if (isset($_POST[$field]))
                update_post_meta($post_id, $field, $_POST[$field]);
            else
                delete_post_meta($post_id, $field);
        }
    }
}
