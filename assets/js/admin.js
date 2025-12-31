jQuery(document).ready(function ($) {

    // Init Color Pickers
    function initColorPicker() {
        $('.nueva-color-picker').wpColorPicker();
    }
    initColorPicker();

    // Tabs Switcher
    $(document).on('click', '.nueva-tab-link', function (e) {
        e.preventDefault();
        var tabId = $(this).data('tab');

        // Tab Nav
        $('.nueva-tab-link').removeClass('active');
        $(this).addClass('active');

        // Tab Content
        $('.nueva-tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    // Style State Tabs (Nested)
    $(document).on('click', '.nueva-style-state-tab', function (e) {
        e.preventDefault();
        var state = $(this).data('state'); // 'normal' or 'hover'

        $('.nueva-style-state-tab').removeClass('active');
        $(this).addClass('active');

        $('.nueva-style-state-content').hide();
        $('.nueva-style-state-' + state).show();
    });

    // --- AUTO DISCOVERY ---
    var detectedData = {};

    $('#nueva-fetch-fields-btn').on('click', function (e) {
        e.preventDefault();
        var sourceVal = $('#nueva-demo-post-id').val();
        var btn = $(this);
        var status = $('#nueva-fetch-status');

        if (!sourceVal) { alert('Please select a Content Source first.'); return; }

        btn.prop('disabled', true).text('Scanning...');
        status.html('<span style="color:#666;">Scanning...</span>');

        $.ajax({
            url: nueva_vars.ajax_url,
            type: 'POST',
            data: { action: 'nueva_fetch_fields', post_id: sourceVal },
            success: function (response) {
                btn.prop('disabled', false).text('Detect Fields');
                if (response.success) {
                    detectedData = response.data;
                    var count = 0;
                    var options = '<option value="">-- Select Detected Repeater --</option>';
                    $.each(detectedData, function (key, subfields) {
                        options += '<option value="' + key + '">' + key + ' (' + subfields.length + ' subfields)</option>';
                        count++;
                    });

                    if (count > 0) {
                        $('#nueva_detected_repeaters').html(options).show();
                        $('#nueva_field_name').hide();
                        status.html('<span style="color:green;">✅ Found ' + count + ' repeaters! Select one from the list.</span>');
                    } else { status.html('<span style="color:orange;">⚠️ No repeater fields found.</span>'); }
                } else { status.html('<span style="color:red;">❌ Error: ' + response.data + '</span>'); }
            },
            error: function () {
                btn.prop('disabled', false).text('Detect Fields');
                status.html('<span style="color:red;">❌ Request failed</span>');
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
