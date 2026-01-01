jQuery(window).on('elementor:init', function () {
    console.log('Nueva CFRD: Editor Init detected');

    elementor.hooks.addAction('panel/open_editor/widget/nueva_cfrd_custom', function (panel, model, view) {
        console.log('Nueva CFRD: Panel Opened for widget', model.get('id'));

        // Function to inject preset code
        const injectPreset = function (presetType) {
            console.log('Nueva CFRD: Injecting Preset', presetType);

            if (!window.nuevaTemplates || !window.nuevaTemplates[presetType]) {
                console.warn('Nueva CFRD: Preset not found in window.nuevaTemplates', window.nuevaTemplates);
                return;
            }

            const config = window.nuevaTemplates[presetType];
            const settings = model.get('settings');

            // 1. Prepare HTML
            let titleKey = settings.get('key_title') || 'title';
            let descKey = settings.get('key_desc') || 'description';
            let imgKey = settings.get('key_image') || 'image';
            let linkKey = settings.get('key_link') || 'link';
            let btnText = settings.get('key_button') || 'Read More';

            let html = config.html
                .replace(/{{key_title}}/g, '{{' + titleKey + '}}')
                .replace(/{{key_desc}}/g, '{{' + descKey + '}}')
                .replace(/{{key_image}}/g, '{{' + imgKey + '}}')
                .replace(/{{key_link}}/g, '{{' + linkKey + '}}')
                .replace(/{{key_button}}/g, btnText);

            // 2. Inject HTML Control (custom_template)
            // Model Update (Source of Truth)
            model.setSetting('custom_template', html);

            // Force UI Update (CodeMirror often needs direct interaction)
            var $htmlControl = panel.$el.find('[data-setting="custom_template"]');
            if ($htmlControl.length) {
                $htmlControl.val(html).trigger('input');
                // If it's a code mirror instance? Elementor handles input event usually.
            }
            // Fallback trigger
            elementor.channels.editor.trigger('change:setting', 'custom_template', html);


            // 3. Inject CSS Control (n_css_code)
            if (config.css) {
                model.setSetting('n_css_code', config.css);

                var $cssControl = panel.$el.find('[data-setting="n_css_code"]');
                if ($cssControl.length) {
                    $cssControl.val(config.css).trigger('input');
                }
                // Fallback trigger
                elementor.channels.editor.trigger('change:setting', 'n_css_code', config.css);
            }

            // 4. Inject Wrapper Controls
            if (config.change_wrapper_tag) {
                model.setSetting('wrapper_tag', config.change_wrapper_tag);
                // Select boxes usually update fine with model setSetting but strictly:
                panel.$el.find('[data-setting="wrapper_tag"]').val(config.change_wrapper_tag).trigger('change');
            }
            if (config.wrapper_class) {
                model.setSetting('wrapper_custom_class', config.wrapper_class);
                panel.$el.find('[data-setting="wrapper_custom_class"]').val(config.wrapper_class).trigger('input');
            }

            console.log('Nueva CFRD: Injection Complete');
        };

        // Listen to Preset Change
        model.on('change:preset_type', function () {
            const newPreset = model.getSetting('preset_type');
            const source = model.getSetting('template_source');

            // Only auto-inject if Template Source is explicitly "Preset"
            if (source === 'preset') {
                if (confirm('Overwrite current HTML/CSS with ' + newPreset + ' preset?')) {
                    injectPreset(newPreset);
                }
            }
        });

        // Listen to Template Source Change
        model.on('change:template_source', function () {
            if (model.getSetting('template_source') === 'preset') {
                const currentPreset = model.getSetting('preset_type');
                // Confirm just to be polite and safe against accidental clicks
                if (confirm('Load code for ' + currentPreset + ' preset?')) {
                    injectPreset(currentPreset);
                }
            }
        });

    });
});
