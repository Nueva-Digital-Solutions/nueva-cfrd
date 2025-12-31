=== Nueva Custom Field Repeater Display (nueva-cfrd) ===
Contributors: nuevadigitalsolutions
Tags: acf, repeater, custom fields, shortcode, slider, accordion, grid, masonry, timeline
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display ACF Repeater fields or generic serialized arrays on the frontend with various layouts (Grid, List, Slider, Accordion, Timeline, Master-Detail, etc.).

== Description ==

**Nueva Custom Field Repeater Display** allows developers and site admins to easily display repeating data (like ACF Repeaters) on the frontend using a simple shortcode. It comes packed with a powerful rendering engine supporting over 15+ layouts including Grids, Sliders, Accordions, Timelines, and more.

**Key Features:**
*   **Universal Support:** Works with ACF Repeater fields and generic serialized array meta.
*   **Diverse Layouts:**
    *   **Static:** Grid, List, Table, Masonry, Split View, Stacked, Compact.
    *   **Interactive:** Accordion, Tabs, Expandable Cards, Hover Reveal, Master-Detail.
    *   **Media:** Slider, Carousel, Zig-Zag.
    *   **Advanced:** Timeline, Comparison Table, Filterable Grid.
*   **Customizable:** Control columns, classes, and detailed layout behaviors via shortcode attributes.
*   **Responsive:** Built-in responsive styles for mobile-friendly displays.
*   **Developer Friendly:** Clean codebase, extendable architecture.

**Usage:**

Simply use the `[nueva_cfrd]` shortcode in your posts, pages, or widgets.

`[nueva_cfrd field="my_repeater_field" layout="grid"]`

== Installation ==

1. Upload the `nueva-cfrd` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the shortcode `[nueva_cfrd]` in your content.

== Frequently Asked Questions ==

= How do I use the Timeline layout? =
Use `[nueva_cfrd field="my_field" layout="timeline"]`. Ensure your repeater has enough content to look good!

= Can I use this with non-ACF fields? =
Yes! Set `type="generic"` in the shortcode: `[nueva_cfrd field="my_custom_meta" type="generic"]`. The field value must be a serialized array.

= How do I filter items? =
Use the `filterable` layout. It automatically detects sub-fields named `category`, `tag`, or `type` to generate filter buttons.

== Changelog ==

= 1.0.0 =
*   Initial release with 15+ layouts (Grid, List, Slider, Accordion, Timeline, and more).
