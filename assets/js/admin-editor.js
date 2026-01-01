jQuery(window).on('elementor:init', function () {
    elementor.hooks.addAction('panel/open_editor/widget/nueva_cfrd_custom', function (panel, model, view) {

        // Function to inject preset code
        const injectPreset = function (presetType) {
            if (!window.nuevaTemplates || !window.nuevaTemplates[presetType]) {
                return;
            }

            const config = window.nuevaTemplates[presetType];
            const settings = model.get('settings');

            // We need to trigger change to save settings? 
            // Elementor controls are tricky. We should try to update the MODEL first.
            // But updating model doesn't always reflect in UI inputs immediately if they are already rendered.
            // So we target the input fields.

            // HTML Control
            let titleKey = settings.get('key_title') || 'title';
            let descKey = settings.get('key_desc') || 'description';
            let imgKey = settings.get('key_image') || 'image';
            let linkKey = settings.get('key_link') || 'link';
            let btnText = settings.get('key_button') || 'Read More';

            // Simple replace placeholders in HTML
            let html = config.html
                .replace(/{{key_title}}/g, '{{' + titleKey + '}}')
                .replace(/{{key_desc}}/g, '{{' + descKey + '}}')
                .replace(/{{key_image}}/g, '{{' + imgKey + '}}')
                .replace(/{{key_link}}/g, '{{' + linkKey + '}}')
                .replace(/{{key_button}}/g, btnText);

            // Update Control Values
            // Note: 'custom_template' is the control name

            // Helper to set value
            elementor.channels.editor.trigger('change:setting', 'custom_template', html);
            elementor.channels.editor.trigger('input:setting', 'custom_template', html); // Trigger updates

            // CSS Control
            if (config.css) {
                // Prepend or replace? Let's append if empty, or replace?
                // User wants presets, so we probably replace to show it working.
                elementor.channels.editor.trigger('input:setting', 'n_css_code', config.css);
                model.setSetting('n_css_code', config.css);
            }

            // Wrapper Controls
            if (config.change_wrapper_tag) {
                elementor.channels.editor.trigger('input:setting', 'wrapper_tag', config.change_wrapper_tag);
                model.setSetting('wrapper_tag', config.change_wrapper_tag);
            }
            if (config.wrapper_class) {
                elementor.channels.editor.trigger('input:setting', 'wrapper_custom_class', config.wrapper_class);
                model.setSetting('wrapper_custom_class', config.wrapper_class);
            }

            // Force refresh can be helped by setting a timer to 'custom' source back?
            // No, keeping it on 'preset' is fine as long as user knows it initiated the code.
        };

        // Listen to Preset Change
        model.on('change:preset_type', function () {
            const newPreset = model.getSetting('preset_type');
            const source = model.getSetting('template_source');

            // Should we confirm overwrite?
            if (source === 'preset') {
                if (confirm('This will overwrite your current HTML/CSS code with the ' + newPreset + ' preset. Continue?')) {
                    injectPreset(newPreset);
                }
            }
        });

        // Also listen to 'template_source' change to 'preset' to trigger initial load?
        model.on('change:template_source', function () {
            if (model.getSetting('template_source') === 'preset') {
                // Maybe trigger current preset?
                const currentPreset = model.getSetting('preset_type');
                if (confirm('Apply ' + currentPreset + ' preset code now?')) {
                    injectPreset(currentPreset);
                }
            }
        });

    });
});
