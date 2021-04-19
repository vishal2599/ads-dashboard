(function ($) {
    'use strict';

    $(function () {
        removePostFunction();
        closingMessageSwitch();
        
        $('select.newsletter_audience').on('change', closingMessageSwitch);

        $('.adv-create-ad #banner').on('click', open_custom_media_window);
        $('.adv-create-ad #in_story').on('click', open_custom_media_window);
        $('.adv-create-ad #footer').on('click', open_custom_media_window);
        $('.adv-create-ad #sidebar_one').on('click', open_custom_media_window);
        $('.adv-create-ad #sidebar_two').on('click', open_custom_media_window);
        $('.adv-create-ad #company_logo').on('click', open_custom_media_window);
        $('.adv-create-ad #newsletter').on('click', open_custom_media_window);

        $('.newsletter_posts').on('change', function(){
            $('.advertise-add  input[type="submit"]').removeAttr('style');
        });

        $('#adv-dashboard').DataTable();
        // $('.form-adver .add-more').on('click', function () {
        //     $('<div class="form-adver upcoming-events"><div class="form-fields"><input name="event_date[]" type="date" placeholder="Date of Event"></div><div class="form-fields"><input name="event_title[]" type="text" placeholder="Title of Event"></div><div class="form-fields"><input name="event_url[]" type="text" placeholder="Event URL"></div><div class="form-fields"><textarea name="event_description[]" placeholder="Event Description" spellcheck="false"></textarea></div></div><div class="form-adver"><div class="form-fields"><a href="javascript:void(0);" class="remove-event"><span class="minus"></span>Remove the above Event</a></div></div>').insertAfter('.form-adver.remove-event:last');
        //     removeEventFunction();
        // });

        $('.adv-form .url input').on('focusout', function () {
            var old_url = $(this).val();
            var new_url = validateSponsorUrl(old_url);
            $(this).val(new_url);
        });

        $('.form-adver .add-more').on('click', function () {
            $('<div class="form-adver upcoming-events"><h4 class="label">Start Date: </h4><div class="form-fields"><input name="event_start_date[]" type="datetime-local" value="<?php echo  $eve->event_start_date; ?>"></div><h4 class="label">End Date: </h4><div class="form-fields"><input name="event_end_date[]" type="datetime-local" value="<?php echo $eve->event_end_date; ?>"></div><div class="form-fields"><input name="event_title[]" type="text" placeholder="Title of Event"></div><div class="form-fields"><input name="event_url[]" type="text" placeholder="Event URL"></div><div class="form-fields"><textarea name="event_description[]" placeholder="Event Description" spellcheck="false"></textarea></div></div><div class="form-adver"><div class="form-fields"><a href="javascript:void(0);" class="remove-event"><span class="minus"></span>Remove the above Event</a></div></div>').insertAfter('.form-adver.remove-event:last');
            removeEventFunction();
        });

        addMoreArticleToNewsletter();

        if ($('.wrap.340b-newsletter-posts').length) {

            $('.wrap.340b-newsletter-posts .adv_admin_edit').on('submit', function (e) {
                e.preventDefault();
                $('.wrap.340b-newsletter-posts input[type="submit"]').css('opacity', '0.6');
                $('.wrap.340b-newsletter-posts input[type="submit"]').css('pointer-events', 'none');
                if ($('.mailchimp-newsletter .advertise-add').hasClass('has-drafts')) {
                    $.ajax({
                        url: advAjax.url,
                        type: 'POST',
                        beforeSend: function () {
                            swal("Sending Campaign", "Please wait while the campaign is being sent. You will be redirected upon completion.", "success");
                        },
                        data: {
                            'action': $('.advertise-form .form-fields.not-visible input[name="action"]').val(),
                            'nonce': $('.advertise-form .form-fields.not-visible input[name="mailchimp_340b_send_nonce"]').val()
                        },
                        success: function (response) {
                            setTimeout(function () {
                                window.location.href = '/wp-admin/admin.php?page=340b_mailchimp_newsletter';
                            }, 400);
                        }
                    });
                } else {
                    var article_title = [], article_url = [], article_copy = [];
                    var addArticle = $('.form-adver.additional-article').length;
                    for( var i=0; i<addArticle; i++ ){
                        article_title.push($('input[name="article_title[]"]:eq('+i+')').val());
                        article_url.push($('input[name="article_url[]"]:eq('+i+')').val());
                        article_copy.push($('textarea[name="article_description[]"]:eq('+i+')').val());
                    }
                    
                    $.ajax({
                        url: advAjax.url,
                        type: 'POST',
                        beforeSend: function () {
                            swal("Creating Campaign", "Please wait while the campaign is being created. You will be redirected upon completion.", "success");
                        },
                        data: {
                            'action': '340b_mailchimp_newsletter',
                            'nonce': $('.wrap.340b-newsletter-posts input[name="mailchimp_340b_nonce"]').val(),
                            'posts': $('.wrap.340b-newsletter-posts .newsletter_posts').val(),
                            'newsletter_middle_ad': $('.wrap.340b-newsletter-posts .newsletter_middle_ad').val(),
                            'newsletter_audience': $('.wrap.340b-newsletter-posts .newsletter_audience').val(),
                            'closing_message_members': $('.wrap.340b-newsletter-posts #closing_message_members').val(),
                            'closing_message_subscribers': $('.wrap.340b-newsletter-posts #closing_message_subscribers').val(),
                            'api_key': $('.wrap.340b-newsletter-posts input[name="340_mailchimp_key"]').val(),
                            'subject': $('.wrap.340b-newsletter-posts input[name="340_mailchimp_subject"]').val(),
                            'preview_text': $('.wrap.340b-newsletter-posts textarea[name="340_mailchimp_preview_text"]').val(),
                            'article_title' : article_title,
                            'article_url' : article_url,
                            'article_copy' : article_copy
                        },
                        success: function (response) {
                            setTimeout(function () {
                                window.location.href = '/wp-admin/admin.php?page=340b_mailchimp_newsletter';
                            }, 400);
                        }
                    });
                }
            });

            $('.wrap.340b-newsletter-posts .button-secondary.remove-draft').on('click', function () {
                swal({
                        title: "Are you sure?",
                        text: "This Newsletter Draft will be deleted from Mailchimp Dashboard as well as Wordpress Admin.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: advAjax.url,
                                type: 'POST',
                                beforeSend: function () {
                                    //     $('#setting-error-340b-newsletter strong span:eq(0)').text('Removing Drafts from Mailchimp Dashboard and Wordpress Admin. PLEASE WAIT!');
                                    // $('#setting-error-340b-newsletter').removeClass('notice-success').addClass('notice-error');
                                    // $('#setting-error-340b-newsletter').show();
                                },
                                data: {
                                    'action': $(this).data('action'),
                                    'nonce': $(this).data('nonce')
                                },
                                success: function (response) {
                                    // $('#setting-error-340b-newsletter').removeClass('notice-error').addClass('notice-success');
                                    // $('#setting-error-340b-newsletter strong span:eq(0)').text('NewsLetter Draft Removed Successfully.');
                                    swal("Draft was removed successfully! You are being redirected...", {
                                        icon: "success",
                                    });
                                    setTimeout(function () {
                                        window.location.href = '/wp-admin/admin.php?page=340b_mailchimp_newsletter';
                                    }, 400);
                                }
                            });
                        } else {
                            swal("Your Newsletter draft is safe!");
                        }
                    });
            });
        }

        removeEventFunction();

        function open_custom_media_window() {
            var container = '.' + $(this).attr('id');
            var im_width = $(container).data('width');
            var im_height = $(container).data('height');

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
                    var up_width = response.sizes.full.width;
                    var up_height = response.sizes.full.height;

                    if (up_height != im_height || up_width != im_width) {
                        $(container).find('.error').text('IMAGE UPLOAD ERROR! Image dimensions should be ' + im_width + ' x ' + im_height);
                    } else {
                        $(container + ' .wp_attachment_id').val(response.id);
                        $(container + ' .image').attr('src', response.sizes.thumbnail.url);
                        $(container + ' .image').show();
                        $(container).find('.error').text('');
                    }

                });
            }

            this.window.open();
            return false;
        }

        function removeEventFunction() {
            $('.form-adver .remove-event').on('click', function () {
                if ($('.form-adver.upcoming-events').length > 1) {
                    $(this).parent().parent().prev().remove();
                    $(this).parent().parent().remove();
                } else {
                    $('.form-adver.upcoming-events input[type="text"]').val('');
                    $('.form-adver.upcoming-events input[type="date"]').val('');
                    $('.form-adver.upcoming-events textarea').val('');
                    $('.form-adver.upcoming-events input[type="datetime-local"]').val('');
                }
            });
        }

        function validateSponsorUrl(url) {
            let newUrl = window.decodeURIComponent(url);
            newUrl = newUrl.trim().replace(/\s/g, "");

            if (/^(:\/\/)/.test(newUrl)) {
                return `http${newUrl}`;
            }
            if (!/^(f|ht)tps?:\/\//i.test(newUrl)) {
                return `http://${newUrl}`;
            }

            return newUrl;
        }

        function removePostFunction(){
            $('.form-adver .remove-above-post').on('click', function () {
               $(this).parents('.additional-article').remove();
               if( $('.additional-article').length == 1 ){
                   $('<div class="form-adver"><div class="form-fields"><a href="javascript:void(0);" class="add-more-post"><span class="plus"></span>Add another article</a></div></div>').insertAfter('.additional-article');
                   addMoreArticleToNewsletter();
               }
            });
        }

        function addMoreArticleToNewsletter(){
            $('.form-adver .add-more-post').on('click', function(){
                $('<div class="form-adver additional-article"><div class="form-fields"><input name="article_title[]" type="text" placeholder="Title of article"></div><div class="form-fields"><input name="article_url[]" type="text" placeholder="Article URL"></div><div class="form-fields"><textarea name="article_description[]" placeholder="Article description" spellcheck="false"></textarea></div><div class="form-fields"> <a href="javascript:void(0);" class="remove-above-post"><span class="minus"></span>Remove the above article</a></div></div>').insertAfter( $('.advertise-add .additional-article:last'));
                removePostFunction();
                if( $('.additional-article').length >= 2 ){
                    $('.add-more-post').parent().parent().remove();
                }
            });
        }

        function closingMessageSwitch(){
            if( $('.newsletter_audience').length ){
                if( $('.newsletter_audience').val() == '3355165' ){
                    $('.closing_message_subscribers').hide();
                    $('.closing_message_members').show();
                } else if( $('.newsletter_audience').val() == '3355169' ) {
                    $('.closing_message_members').hide();
                    $('.closing_message_subscribers').show();
                } else {
                    $('.closing_message_members').hide();
                    $('.closing_message_subscribers').hide();
                }
            }
        }
    });
})(jQuery);