(function ($) {
    'use strict';

    $(function () {

        $('.adv-create-ad #banner').on('click', open_custom_media_window);
        $('.adv-create-ad #in_story').on('click', open_custom_media_window);
        $('.adv-create-ad #footer').on('click', open_custom_media_window);
        $('.adv-create-ad #sidebar_one').on('click', open_custom_media_window);
        $('.adv-create-ad #sidebar_two').on('click', open_custom_media_window);
        $('.adv-create-ad #company_logo').on('click', open_custom_media_window);
        $('#adv-dashboard').DataTable();
        $('.form-adver .add-more').on('click', function(){
            $('<div class="form-adver upcoming-events"><div class="form-fields"><input name="event_date[]" type="date" placeholder="Date of Event"></div><div class="form-fields"><input name="event_title[]" type="text" placeholder="Title of Event"></div><div class="form-fields"><textarea name="event_description[]" placeholder="Event Description" spellcheck="false"></textarea></div></div><div class="form-adver"><div class="form-fields"><a href="javascript:void(0);" class="remove-event"><span class="minus"></span>Remove the above Event</a></div></div>').insertAfter('.form-adver.remove-event:last');
            removeEventFunction();
        });

        // $('.form-fields.company_category input[type="radio"]').on('change', function(){
        //     var count = $('.form-fields.company_category input[type="radio"]:checked').length;
        //     $('.form-fields.company_category input[name="company_category_count"]').val(count);
        // })

        removeEventFunction();

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

        function removeEventFunction(){
            $('.form-adver .remove-event').on('click', function(){
                if( $('.form-adver.upcoming-events').length > 1 ){
                    $(this).parent().parent().prev().remove();
                    $(this).parent().parent().remove();
                } else {
                    $('.form-adver.upcoming-events input[type="text"]').val('');
                    $('.form-adver.upcoming-events input[type="date"]').val('');
                    $('.form-adver.upcoming-events textarea').val('');
                }
            });
        }
    });
})(jQuery);