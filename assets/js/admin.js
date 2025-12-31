jQuery(document).ready(function ($) {

    // Init Color Pickers
    function initColorPicker() {
        $('.nueva-color-picker').wpColorPicker();
    }
    initColorPicker();

    // Add Field
    var fieldTemplate = $('#nueva-field-template').html();
    var wrapper = $('#nueva-sub-fields-wrapper');

    $('#nueva-add-field').on('click', function (e) {
        e.preventDefault();

        var index = wrapper.find('.nueva-field-row').length;
        var newRow = fieldTemplate.replace(/{{INDEX}}/g, index);

        wrapper.append(newRow);
        initColorPicker(); // Re-init for new row
    });

    // Remove Field
    wrapper.on('click', '.nueva-remove-field', function (e) {
        e.preventDefault();
        if (confirm('Are you sure?')) {
            $(this).closest('.nueva-field-row').remove();
        }
    });

    // Sortable (requires jQuery UI Sortable enqueued by WP usually, but keeping simple for now)
    // If we want sortable, we need to enqueue jquery-ui-sortable and enable it on 'wrapper'.

});
