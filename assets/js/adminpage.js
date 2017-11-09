// Admin settings script
// version 1.0

(function($) {
    "use strict";

    $(function() {

        // init color picker
        $('.wp-colorpicker').wpColorPicker();
        // init dragging plugin
        dragula([document.querySelector('#anssp-active-items'), document.querySelector('#anssp-inactive-items')], {
        });

        // save active items as CSV
        $('#anssp-settings-form').on('submit', function(e) {
            var active = [];
            $('#anssp-active-items').find('[data-share-name]').each(function(index) {
                active.push($(this).attr('data-share-name'));
            });
            $('[name="anssp_settings[active_items]"]').val(active.join(','));
        });

	});

})(jQuery);