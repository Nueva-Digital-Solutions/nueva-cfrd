jQuery(document).ready(function ($) {

    // Init Color Pickers
    function initColorPicker() {
        $('.nueva-color-picker').wpColorPicker();
    }
    initColorPicker();

    // Tabs Switcher
    $('.nueva-tab-link').on('click', function (e) {
        e.preventDefault();
        var tabId = $(this).data('tab');

        // Tab Nav
        $('.nueva-tab-link').removeClass('active');
        $(this).addClass('active');

        // Tab Content
        $('.nueva-tab-content').removeClass('active');
        $('#' + tabId).addClass('active');

        // State Tabs (Normal/Hover) - handled separately if needed, but styling engine might use nested logic
    });

    // Style State Tabs (Nested)
    $('.nueva-style-state-tab').on('click', function (e) {
        e.preventDefault();
        var state = $(this).data('state'); // 'normal' or 'hover'

        $('.nueva-style-state-tab').removeClass('active');
        $(this).addClass('active');

        $('.nueva-style-state-content').hide();
        $('.nueva-style-state-' + state).show();
    });

    // --- AUTO DISCOVERY & FIELD REPEATER (Previous Logic Kept) ---
    var detectedData = {};

    $('#nueva-fetch-fields-btn').on('click', function (e) {
        e.preventDefault();
        var postId = $('#nueva-demo-post-id').val();
        var btn = $(this);
        var status = $('#nueva-fetch-status');

        if (!postId) { alert('Please enter a Post ID'); return; }

        btn.prop('disabled', true).text('Scanning...');
        status.text('');

        $.ajax({
            url: nueva_vars.ajax_url,
            type: 'POST',
            data: { action: 'nueva_fetch_fields', post_id: postId },
            success: function (response) {
                btn.prop('disabled', false).text('Detect Fields from Post');
                if (response.success) {
                    detectedData = response.data;
                    var options = '<option value="">-- Select Detected Repeater --</option>';
                    var count = 0;
                    $.each(detectedData, function (key, subfields) {
                        options += '<option value="' + key + '">' + key + '</option>';
                        count++;
                    });
                    if (count > 0) {
                        $('#nueva_detected_repeaters').html(options).show();
                        $('#nueva_field_name').hide();
                        status.text('✅ Found ' + count + ' repeaters!');
                    } else { status.text('⚠️ No repeaters found.'); }
                } else { status.text('❌ Error: ' + response.data); }
            }
        });
    });

    $('#nueva_detected_repeaters').on('change', function () {
        var val = $(this).val();
        if (val) {
            $('#nueva_field_name').val(val).show();
            $(this).hide();
            if (confirm('Auto-populate sub-fields from this repeater?')) {
                var subfields = detectedData[val];
                $('#nueva-sub-fields-wrapper').empty();
                $.each(subfields, function (i, key) {
                    $('#nueva-add-field').click();
                    var lastRow = $('#nueva-sub-fields-wrapper .nueva-field-row').last();
                    lastRow.find('input[type="text"]').first().val(key);
                });
            }
        }
    });

    var fieldTemplate = $('#nueva-field-template').html();
    var wrapper = $('#nueva-sub-fields-wrapper');
    $('#nueva-add-field').on('click', function (e) {
        e.preventDefault();
        var index = wrapper.find('.nueva-field-row').length;
        var newRow = fieldTemplate.replace(/{{INDEX}}/g, index);
        wrapper.append(newRow); // No color picker re-init needed as we removed color from sub-fields
    });
    wrapper.on('click', '.nueva-remove-field', function (e) {
        e.preventDefault();
        if (confirm('Are you sure?')) $(this).closest('.nueva-field-row').remove();
    });

});
