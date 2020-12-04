(function ($) {
    'use strict';

    $(function () {

        $('.adv-create-ad #banner').on('click', open_custom_media_window);
        $('.adv-create-ad #in_story').on('click', open_custom_media_window);
        $('.adv-create-ad #footer').on('click', open_custom_media_window);
        $('.adv-create-ad #sidebar_one').on('click', open_custom_media_window);
        $('.adv-create-ad #sidebar_two').on('click', open_custom_media_window);
        $('#adv-dashboard').DataTable();

        function open_custom_media_window() {
            var container = $(this).attr('id');
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