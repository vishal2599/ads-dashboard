(function ($) {
    'use strict';

    $(function () {

        $('#upload_adv_banner').on('click', open_custom_media_window);
        $('#upload_adv_in_story').on('click', open_custom_media_window);
        $('#upload_adv_footer').on('click', open_custom_media_window);
        $('#upload_adv_sidebar_one').on('click', open_custom_media_window);
        $('#upload_adv_sidebar_two').on('click', open_custom_media_window);
        $('#adv-dashboard').DataTable();

        function open_custom_media_window() {
            var container = $(this).parent().parent().attr('class');
            console.log(container);
            if (this.window === undefined) {
                this.window = wp.media({
                    title: 'Insert Image',
                    library: {
                        type: 'image'
                    },
                    multiple: false,
                    button: {
                        text: 'Insert Image'
                    }
                });

                var self = this;
                this.window.on('select', function () {
                    var response = self.window.state().get('selection').first().toJSON();

                    $("." + container + ' .wp_attachment_id').val(response.id);
                    $("." + container + ' .image').attr('src', response.sizes.thumbnail.url);
                    $("." + container + ' .image').show();
                });
            }

            this.window.open();
            return false;
        }
    });
})(jQuery);