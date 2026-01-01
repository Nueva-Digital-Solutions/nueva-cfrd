<?php

class Nueva_CFRD_Renderer
{

    private $config_id;
    private $styles_css = '';
    private $atts = array();
    private $data = array();

    public function __construct($atts)
    {
        // 1. Check if ID exists
        if (!empty($atts['id'])) {
            $this->config_id = $atts['id'];
            // Load config from Post Meta
            $field = get_post_meta($this->config_id, 'nueva_field_name', true);
            $layout = get_post_meta($this->config_id, 'nueva_layout_type', true);
            $columns = get_post_meta($this->config_id, 'nueva_columns', true);
            $sub_fields = get_post_meta($this->config_id, 'nueva_sub_fields', true);

            // Defaults if saved config is missing
            $defaults = array(
                'field' => $field,
                'layout' => $layout ?: 'grid',
                'columns' => $columns ?: '3',
                'sub_fields' => $sub_fields ?: array(),
            );

            $this->atts = shortcode_atts(
                array_merge(array(
                    'post_id' => get_the_ID(),
                    'type' => 'acf',
                    'class' => '',
                    'id' => '',
                ), $defaults),
                $atts
            );

        } else {
            // Legacy/Manual Mode (No ID)
            $this->atts = $atts;
        }
    }

    public function render()
    {
        $this->fetch_data();

        if (empty($this->data)) {
            if (current_user_can('edit_posts')) {
                $msg = 'Nueva CFRD: No data found for field <strong>' . esc_html($this->atts['field']) . '</strong> on Post ID ' . $this->atts['post_id'] . '.';

                // Smart Suggestion
                $suggestion = '';
                $all_keys = get_post_meta($this->atts['post_id']);
                if ($all_keys) {
                    foreach ($all_keys as $k => $v) {
                        // Look for pattern: parent_0_child (ACF style)
                        if (preg_match('/^(.+)_\d+_' . preg_quote($this->atts['field'], '/') . '$/', $k, $matches)) {
                            $suggestion = $matches[1];
                            break;
                        }
                    }
                }

                if ($suggestion) {
                    $msg .= '<br><br>💡 <strong>Did you mean "' . esc_html($suggestion) . '"?</strong><br>We found "' . esc_html($this->atts['field']) . '" as a sub-field of "' . esc_html($suggestion) . '". Try changing the Repeater Field Name to <strong>' . esc_html($suggestion) . '</strong>.';
                } else {
                    $msg .= ' Check if the field name is correct.';
                }

                return '<div style="background:#fff3cd; color:#856404; padding:10px; border:1px solid #ffeeba;">' . $msg . '</div>';
            }
            return '';
        }

        // Generate Dynamic Styles if using Builder
        if (!empty($this->config_id)) {
            $this->generate_styles();
        }

        ob_start();

        if (!empty($this->styles_css)) {
            echo '<style>' . $this->styles_css . '</style>';
        }

        $wrapper_class = 'nueva-cfrd-wrapper ' . esc_attr($this->atts['class']) . ' nueva-layout-' . esc_attr($this->atts['layout']);
        if (!empty($this->config_id))
            $wrapper_class .= ' nueva-id-' . $this->config_id;

        echo '<div class="' . $wrapper_class . '" data-layout="' . esc_attr($this->atts['layout']) . '">';

        switch ($this->atts['layout']) {
            case 'list':
                $this->render_list();
                break;
            case 'table':
                $this->render_table();
                break;
            case 'accordion':
                $this->render_accordion();
                break;
            case 'tabs':
                $this->render_tabs();
                break;
            case 'slider':
            case 'carousel':
            case 'card-deck':
                $this->render_slider();
                break;
            case 'split':
            case 'two-column':
                $this->render_split();
                break;
            case 'stacked':
                $this->render_stacked();
                break;
            case 'timeline':
                $this->render_timeline();
                break;
            case 'comparison':
                $this->render_comparison();
                break;
            case 'zig-zag':
                $this->render_zigzag();
                break;
            case 'filterable':
                $this->render_filterable();
                break;
            case 'expandable':
                $this->render_expandable();
                break;
            case 'hover-reveal':
                $this->render_hover_reveal();
                break;
            case 'master-detail':
                $this->render_master_detail();
                break;
            case 'compact':
                $this->render_compact();
                break;
            case 'masonry':
                $this->render_masonry();
                break;
            case 'grid':
            default:
                $this->render_grid();
                break;
        }

        echo '</div>';
        return ob_get_clean();
    }

    public function render_custom_loop($template)
    {
        ob_start();
        $this->fetch_data();
        $this->generate_styles(); // For generic styles if any

        // Wrapper
        echo '<div class="nueva-cfrd-custom-loop nueva-id-' . esc_attr($this->config_id) . '">';

        foreach ($this->data as $item) {
            if (!is_array($item))
                continue;

            $item_html = $template;

            // Basic mustache replacement logic: {{ key }}
            foreach ($item as $key => $value) {
                // Determine format
                $formatted_value = $this->format_value($value, 'text'); // default to text for now
                // We could enhance this to sniff type or allow {{key:image}} syntax later

                // Case-insensitive replacement
                $item_html = str_ireplace('{{' . $key . '}}', $formatted_value, $item_html);
            }

            // Cleanup unused tags? Optional.

            echo $item_html;
        }

        echo '</div>';
        return ob_get_clean();
    }

    private function fetch_data()
    {
        $post_id = $this->atts['post_id'];
        $field = $this->atts['field'];

        // 1. Try ACF (get_field)
        if (function_exists('get_field')) {
            $this->data = get_field($field, $post_id);

            if (empty($this->data)) {
                $option_data = get_field($field, 'option');
                if (!empty($option_data) && is_array($option_data)) {
                    $this->data = $option_data;
                }
            }
        }

        // 2. Fallback: get_post_meta (Serialized Arrays)
        if (empty($this->data)) {
            $meta = get_post_meta($post_id, $field, true);
            if (is_serialized($meta)) {
                $this->data = unserialize($meta);
            } elseif (is_array($meta)) {
                $this->data = $meta;
            }
        }

        // Ensure data is array and iterable
        if (!is_array($this->data)) {
            $this->data = array();
        }
    }

    private function generate_styles()
    {
        $css = '';
        $id = $this->config_id;
        $config = get_post_meta($id, 'nueva_style_config', true);

        if (!$config)
            return;

        // Font Loading
        $fonts = [];
        if (!empty($config['normal']['font_family']))
            $fonts[] = $config['normal']['font_family'];
        if (!empty($config['hover']['font_family']))
            $fonts[] = $config['hover']['font_family'];

        if (!empty($fonts)) {
            $fonts = array_unique($fonts);
            foreach ($fonts as $f) {
                $css .= "@import url('https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $f) . ":wght@400;700&display=swap');";
            }
        }

        // Styles
        $states = [
            'normal' => ".nueva-id-{$id} .nueva-card-builder",
            'hover' => ".nueva-id-{$id} .nueva-card-builder:hover"
        ];

        foreach ($states as $type => $selector) {
            if (empty($config[$type]))
                continue;
            $vals = $config[$type];
            $props = '';

            if (!empty($vals['font_family']))
                $props .= "font-family: '" . $vals['font_family'] . "', sans-serif; ";
            if (!empty($vals['color']))
                $props .= "color: " . $vals['color'] . "; ";
            if (!empty($vals['bg_color']))
                $props .= "background-color: " . $vals['bg_color'] . "; ";

            foreach (['margin', 'padding'] as $box) {
                if (!empty($vals[$box])) {
                    foreach (['top', 'right', 'bottom', 'left'] as $side) {
                        if (isset($vals[$box][$side]) && $vals[$box][$side] !== '') {
                            $props .= "{$box}-{$side}: " . $vals[$box][$side] . "px; ";
                        }
                    }
                }
            }

            if (!empty($vals['border'])) {
                $has = false;
                foreach (['top', 'right', 'bottom', 'left'] as $side) {
                    if (isset($vals['border'][$side]) && $vals['border'][$side] !== '') {
                        $props .= "border-{$side}-width: " . $vals['border'][$side] . "px; ";
                        $props .= "border-{$side}-style: solid; ";
                        $has = true;
                    }
                }
                if ($has && !empty($vals['border_color']))
                    $props .= "border-color: " . $vals['border_color'] . "; ";
            }

            if ($props)
                $css .= "{$selector} { {$props} } ";
        }

        // Custom CSS
        $custom = get_post_meta($id, 'nueva_custom_css', true);
        if ($custom)
            $css .= strip_tags($custom);

        $this->styles_css = $css;
    }

    // --- Layout Renderers ---

    private function render_grid()
    {
        $style = 'display: grid; gap: 20px;';
        // Only apply inline columns if NOT simple manual mode (Elementor handles its own columns via CSS)
        // Actually, if it's Elementor, it passes 'columns' too, but we want CSS to win.
        // Let's assume if 'columns' is set, we use it, BUT Elementor's CSS should invoke !important or we avoid inline.

        // Better: If we have a config_id (Shortcode Builder), use inline.
        // If we don't (Elementor), skip inline columns so responsive controls work.
        if (!empty($this->config_id) || empty($this->atts['class']) || strpos($this->atts['class'], 'elementor') === false) {
            $style .= ' grid-template-columns: repeat(' . esc_attr($this->atts['columns']) . ', 1fr);';
        }

        echo '<div class="nueva-cfrd-grid" style="' . $style . '">';
        foreach ($this->data as $item) {
            $this->render_item_card($item);
        }
        echo '</div>';
    }

    private function render_list()
    {
        echo '<ul class="nueva-cfrd-list">';
        foreach ($this->data as $item) {
            echo '<li class="nueva-cfrd-item">';
            $this->render_item_content($item);
            echo '</li>';
        }
        echo '</ul>';
    }

    private function render_table()
    {
        if (empty($this->data))
            return;

        $headers = array_keys($this->data[0]);

        echo '<div class="nueva-table-responsive">';
        echo '<table class="nueva-cfrd-table">';
        echo '<thead><tr>';
        foreach ($headers as $header) {
            echo '<th>' . esc_html(ucfirst(str_replace('_', ' ', $header))) . '</th>';
        }
        echo '</tr></thead>';
        echo '<tbody>';
        foreach ($this->data as $item) {
            echo '<tr>';
            foreach ($item as $key => $value) {
                echo '<td>' . $this->format_value($value) . '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    private function render_accordion()
    {
        echo '<div class="nueva-cfrd-accordion">';
        foreach ($this->data as $index => $item) {
            $keys = array_keys($item);
            $title_key = isset($keys[0]) ? $keys[0] : '';
            $title = isset($item[$title_key]) ? $item[$title_key] : 'Item ' . ($index + 1);

            echo '<div class="nueva-accordion-item">';
            echo '<button class="nueva-accordion-header">' . esc_html($this->format_value($title)) . '</button>';
            echo '<div class="nueva-accordion-content">';
            $this->render_item_content($item, array($title_key));
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_tabs()
    {
        echo '<div class="nueva-cfrd-tabs">';
        echo '<ul class="nueva-tabs-nav">';
        foreach ($this->data as $index => $item) {
            $class = $index === 0 ? 'active' : '';
            echo '<li class="' . $class . '" data-tab="tab-' . $index . '">Tab ' . ($index + 1) . '</li>';
        }
        echo '</ul>';
        echo '<div class="nueva-tabs-content">';
        foreach ($this->data as $index => $item) {
            $class = $index === 0 ? 'active' : '';
            echo '<div class="nueva-tab-pane ' . $class . '" id="tab-' . $index . '">';
            $this->render_item_content($item);
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }

    private function render_slider()
    {
        echo '<div class="swiper nueva-cfrd-slider"><div class="swiper-wrapper">';
        foreach ($this->data as $item) {
            echo '<div class="swiper-slide">';
            $this->render_item_card($item);
            echo '</div>';
        }
        echo '</div><div class="swiper-pagination"></div><div class="swiper-button-next"></div><div class="swiper-button-prev"></div></div>';
    }

    private function render_masonry()
    {
        echo '<div class="nueva-cfrd-masonry">';
        foreach ($this->data as $item) {
            echo '<div class="nueva-masonry-item">';
            $this->render_item_card($item);
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_compact()
    {
        echo '<div class="nueva-cfrd-compact">';
        foreach ($this->data as $item) {
            echo '<div class="nueva-compact-item">';
            $this->render_item_inline($item);
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_split()
    {
        echo '<div class="nueva-cfrd-split">';
        foreach ($this->data as $item) {
            echo '<div class="nueva-split-row">';
            $keys = array_keys($item);
            $half = ceil(count($keys) / 2);
            $left = array_slice($item, 0, $half, true);
            $right = array_slice($item, $half, null, true);

            echo '<div class="nueva-split-left">';
            $this->render_item_content($left);
            echo '</div>';
            echo '<div class="nueva-split-right">';
            $this->render_item_content($right);
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_stacked()
    {
        echo '<div class="nueva-cfrd-stacked">';
        foreach ($this->data as $item) {
            echo '<div class="nueva-stacked-section">';
            $this->render_item_content($item);
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_timeline()
    {
        echo '<div class="nueva-cfrd-timeline">';
        foreach ($this->data as $index => $item) {
            $side = ($index % 2 === 0) ? 'left' : 'right';
            echo '<div class="nueva-timeline-item ' . $side . '">';
            echo '<div class="nueva-timeline-content">';
            $this->render_item_content($item);
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_comparison()
    {
        echo '<div class="nueva-cfrd-comparison" style="display: flex; overflow-x: auto;">';
        echo '<div class="nueva-comparison-labels">';
        $keys = array_keys($this->data[0] ?? []);
        foreach ($keys as $key) {
            echo '<div class="nueva-comp-label">' . esc_html(ucfirst($key)) . '</div>';
        }
        echo '</div>';

        foreach ($this->data as $item) {
            echo '<div class="nueva-comparison-col">';
            foreach ($keys as $key) {
                $val = $item[$key] ?? '';
                echo '<div class="nueva-comp-value">' . $this->format_value($val) . '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_zigzag()
    {
        echo '<div class="nueva-cfrd-zigzag">';
        foreach ($this->data as $index => $item) {
            $reverse = ($index % 2 !== 0) ? 'nueva-row-reverse' : '';
            echo '<div class="nueva-zigzag-row ' . $reverse . '">';

            $image_key = null;
            foreach ($item as $k => $v) {
                if (is_array($v) && isset($v['url'])) {
                    $image_key = $k;
                    break;
                }
            }

            echo '<div class="nueva-zigzag-media">';
            if ($image_key && isset($item[$image_key])) {
                echo $this->format_value($item[$image_key]);
            } else {
                echo '<div class="nueva-placeholder-img">Image</div>';
            }
            echo '</div>';

            echo '<div class="nueva-zigzag-content">';
            $this->render_item_content($item, $image_key ? array($image_key) : array());
            echo '</div>';

            echo '</div>';
        }
        echo '</div>';
    }

    private function render_filterable()
    {
        $categories = array();
        $filter_key = '';
        foreach ($this->data as $item) {
            foreach (['category', 'tag', 'type'] as $k) {
                if (isset($item[$k])) {
                    $val = is_array($item[$k]) ? ($item[$k]['name'] ?? $item[$k][0] ?? '') : $item[$k];
                    if ($val) {
                        $categories[$val] = sanitize_title($val);
                        if (!$filter_key)
                            $filter_key = $k;
                    }
                }
            }
        }

        echo '<div class="nueva-cfrd-filterable">';
        if (!empty($categories)) {
            echo '<div class="nueva-filter-controls">';
            echo '<button class="active" data-filter="*">All</button>';
            foreach ($categories as $name => $slug) {
                echo '<button data-filter=".' . esc_attr($slug) . '">' . esc_html($name) . '</button>';
            }
            echo '</div>';
        }

        echo '<div class="nueva-filter-grid">';
        foreach ($this->data as $item) {
            $classes = ['nueva-filter-item'];
            if ($filter_key && isset($item[$filter_key])) {
                $val = is_array($item[$filter_key]) ? ($item[$filter_key]['name'] ?? $item[$filter_key][0] ?? '') : $item[$filter_key];
                $classes[] = sanitize_title($val);
            }
            echo '<div class="' . implode(' ', $classes) . '">';
            $this->render_item_card($item);
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }

    private function render_expandable()
    {
        echo '<div class="nueva-cfrd-grid nueva-expandable-grid">';
        foreach ($this->data as $index => $item) {
            echo '<div class="nueva-card nueva-expandable-card">';
            echo '<div class="nueva-card-preview">';
            $this->render_item_content(array_slice($item, 0, 2, true));
            echo '<button class="nueva-expand-btn">Expand</button>';
            echo '</div>';
            echo '<div class="nueva-card-full" style="display:none;">';
            $this->render_item_content(array_slice($item, 2, null, true));
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_hover_reveal()
    {
        echo '<div class="nueva-cfrd-grid">';
        foreach ($this->data as $item) {
            echo '<div class="nueva-card nueva-hover-reveal">';
            echo '<div class="nueva-reveal-visible">';
            $keys = array_keys($item);
            $first = $item[$keys[0]] ?? '';
            echo $this->format_value($first);
            echo '</div>';
            echo '<div class="nueva-reveal-hidden">';
            $this->render_item_content($item, array($keys[0]));
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_master_detail()
    {
        echo '<div class="nueva-cfrd-master-detail">';
        echo '<div class="nueva-master-list">';
        foreach ($this->data as $index => $item) {
            $keys = array_keys($item);
            $title = $item[$keys[0]] ?? 'Item ' . ($index + 1);
            $active = $index === 0 ? 'active' : '';
            echo '<div class="nueva-master-item ' . $active . '" data-index="' . $index . '">';
            echo '<strong>' . esc_html(strip_tags($this->format_value($title))) . '</strong>';
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="nueva-detail-view">';
        foreach ($this->data as $index => $item) {
            $active = $index === 0 ? 'active' : '';
            echo '<div class="nueva-detail-pane ' . $active . '" data-index="' . $index . '">';
            $this->render_item_content($item);
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }

    // --- Helpers ---

    private function render_item_card($item)
    {
        // If config, render with wrapper
        if (!empty($this->config_id)) {
            echo '<div class="nueva-card-builder">';
            $this->render_item_content($item);
            echo '</div>';
        } else {
            echo '<div class="nueva-card">';
            $this->render_item_content($item);
            echo '</div>';
        }
    }

    private function render_item_inline($item)
    {
        echo '<div class="nueva-inline-row">';
        foreach ($item as $key => $value) {
            echo '<span class="nueva-inline-field"><strong>' . esc_html($key) . ':</strong> ' . $this->format_value($value) . '</span> ';
        }
        echo '</div>';
    }

    private function render_item_content($item, $exclude_keys = array())
    {
        if (!is_array($item)) {
            echo $this->format_value($item);
            return;
        }

        $rendered_count = 0;

        // If sub_fields are configured (from Builder), use them to filter and order
        if (!empty($this->atts['sub_fields']) && is_array($this->atts['sub_fields'])) {

            // Normalize Item Keys for searching (Trim + Lowercase)
            $normalized_item = [];
            foreach ($item as $k => $v) {
                $normalized_item[strtolower(trim($k))] = $v;
            }

            foreach ($this->atts['sub_fields'] as $field_config) {
                // Determine Key, Type
                if (is_array($field_config)) {
                    $target_key = strtolower(trim($field_config['name'] ?? ''));
                    $type = $field_config['type'] ?? 'text';
                    $custom_style = $field_config['style'] ?? '';
                } else {
                    $target_key = strtolower(trim($field_config));
                    $type = 'text';
                    $custom_style = '';
                }

                if (!$target_key || in_array($target_key, $exclude_keys))
                    continue;

                // Loose Match Search
                if (array_key_exists($target_key, $normalized_item)) {
                    $value = $normalized_item[$target_key];

                    echo '<div class="nueva-field nueva-field-' . esc_attr($target_key) . '">';

                    $style_attr = $custom_style ? ' style="' . esc_attr($custom_style) . '"' : '';

                    // Determine Wrapper Tag based on Type
                    $tag = 'span';
                    if ($type === 'paragraph')
                        $tag = 'p';
                    if ($type === 'heading')
                        $tag = $field_config['heading_tag'] ?? 'h3';
                    if ($type === 'div')
                        $tag = 'div';

                    // HTML & Video might not need a wrapper or need specific handling
                    if ($type === 'html') {
                        echo '<div class="nueva-html-content">' . $value . '</div>';
                    } elseif ($type === 'video') {
                        $ratio = $field_config['video_ratio'] ?? '169';
                        echo '<div class="nueva-video-wrapper nueva-ratio-' . esc_attr($ratio) . '">';
                        // Simple oEmbed or Iframe logic
                        if (strpos($value, 'iframe') !== false) {
                            echo $value;
                        } else {
                            // Assistive oEmbed
                            echo wp_oembed_get($value, array('width' => 800));
                        }
                        echo '</div>';
                    } else {
                        // Text, Link, Image (wrapper is span/div/h3 etc)
                        echo '<' . $tag . ' class="nueva-value"' . $style_attr . '>' . $this->format_value($value, $type) . '</' . $tag . '>';
                    }

                    echo '</div>';
                    $rendered_count++;
                }
            }
        }

        // Fallback: If no sub-fields matched (or none configured), show all fields
        if ($rendered_count === 0) {
            foreach ($item as $key => $value) {
                if (in_array($key, $exclude_keys))
                    continue;

                echo '<div class="nueva-field nueva-field-' . esc_attr($key) . '">';
                // For fallback, we default to showing labels, but hide them for cleaner layouts like List/Slider
                // unless the user specifically wants them (which they can't config here yet, so we choose defaults).
                if (!in_array($this->atts['layout'], ['list', 'slider', 'carousel', 'card-deck'])) {
                    echo '<strong class="nueva-label">' . esc_html(ucfirst(str_replace('_', ' ', $key))) . ': </strong>';
                }
                echo '<span class="nueva-value">' . $this->format_value($value) . '</span>';
                echo '</div>';
            }
        }
    }

    private function format_value($value, $type = 'text')
    {
        if (is_array($value)) {
            // ACF Image array
            if (isset($value['url'])) {
                return '<img src="' . esc_url($value['url']) . '" alt="' . esc_attr($value['alt'] ?? '') . '" />';
            }
            // ACF Link array
            if (isset($value['url']) && isset($value['title'])) {
                return '<a href="' . esc_url($value['url']) . '" target="' . esc_attr($value['target'] ?? '_self') . '">' . esc_html($value['title']) . '</a>';
            }
            return implode(', ', $value);
        }

        switch ($type) {
            case 'image':
                // Expecting ID or URL
                if (is_numeric($value)) {
                    return wp_get_attachment_image($value, 'full');
                }
                return '<img src="' . esc_url($value) . '" />';
            case 'link':
                return '<a href="' . esc_url($value) . '">' . esc_html($value) . '</a>';
            case 'html':
                return $value; // Raw HTML (trusting the admin/input)
            case 'text':
            default:
                return wp_kses_post($value);
        }
    }
}
