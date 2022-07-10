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
        $('.adv-create-ad #spotlight').on('click', open_custom_media_window);
        $('.adv-create-ad #provider_340b').on('click', open_custom_media_window);
        $('.adv-create-ad #marketing_graphic').on('click', open_custom_media_window);

        $('.newsletter_posts').on('change', function () {
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

            $('.wrap.340b-newsletter-posts .send-newsletter').on('click', function (e) {
                e.preventDefault();
                swal({
                        title: "Are you sure you want to send?",
                        text: "",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((sendNewsletter) => {
                        if (sendNewsletter) {
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
                                        'nonce': $('.advertise-form .form-fields.not-visible input[name="mailchimp_340b_send_nonce"]').val(),
                                        'newsletter_referer': $('input[name="newsletter_referer"]').val(),
                                        'target_subscribers': $('.subscribers-wrap .target_subscribers').val(),
                            
                                    },
                                    success: function (response) {
                                        setTimeout(function () {
                                            if ($('input[name="newsletter_referer"]').val() == 'breaking_news') {
                                                window.location.href = '/wp-admin/admin.php?page=340b_breaking_news';
                                            } else {
                                                window.location.href = '/wp-admin/admin.php?page=340b_mailchimp_newsletter';
                                            }
                                        }, 400);
                                    }
                                });
                            }
                        }
                    });
            });

            $('.wrap.340b-newsletter-posts .mailchimp-newsletter').on('submit', function (e) {
                e.preventDefault();
                $('.wrap.340b-newsletter-posts input[type="submit"]').css('opacity', '0.6');
                $('.wrap.340b-newsletter-posts input[type="submit"]').css('pointer-events', 'none');
                if (!$('.mailchimp-newsletter .advertise-add').hasClass('has-drafts')) {
                    var article_copy_1 = tinyMCE.get("article_description_1").getContent();
                    var article_copy_2 = tinyMCE.get("article_description_2").getContent();

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
                            'newsletter_top_ad': $('.wrap.340b-newsletter-posts .newsletter_top_ad').val(),
                            'newsletter_middle_ad': $('.wrap.340b-newsletter-posts .newsletter_middle_ad').val(),
                            'newsletter_bottom_ad': $('.wrap.340b-newsletter-posts .newsletter_bottom_ad').val(),
                            'newsletter_audience': $('.wrap.340b-newsletter-posts .newsletter_audience').val(),
                            'target_subscribers': $('.subscribers-wrap .target_subscribers').val(),
                            'newsletter_type': $('.wrap.340b-newsletter-posts .newsletter_type').val(),
                            'newsletter_referer': $('input[name="newsletter_referer"]').val(),
                            'closing_message_members': tinyMCE.get("closing_message_members").getContent(),
                            'closing_message_subscribers': tinyMCE.get("closing_message_subscribers").getContent(),
                            'api_key': $('.wrap.340b-newsletter-posts input[name="340_mailchimp_key"]').val(),
                            'subject': $('.wrap.340b-newsletter-posts input[name="340_mailchimp_subject"]').val(),
                            'preview_text': $('.wrap.340b-newsletter-posts textarea[name="340_mailchimp_preview_text"]').val(),
                            'article_copy_1': article_copy_1,
                            'article_copy_2': article_copy_2,
                            'spotlight_title': $('input[name="spotlight_title"]').val(),
                            'spotlight_image': $('input[name="upload_spotlight"]').val(),
                            'spotlight_post': $('.newsletter_spotlights').val(),
                            'provider_title': $('input[name="provider_title"]').val(),
                            'provider_image': $('input[name="upload_provider"]').val(),
                            'provider_post': $('.newsletter_providers').val(),
                            'marketing_graphic_text': $('input[name="marketing_graphic_text"]').val(),
                            'marketing_graphic_url': $('input[name="marketing_graphic_url"]').val(),
                            'marketing_graphic_image': $('input[name="marketing_graphic_image"]').val(),
                            'marketing_graphic_url_for_image': $('input[name="marketing_graphic_url_for_image"]').val()
                        },
                        success: function (response) {
                            setTimeout(function () {
                                if ($('input[name="newsletter_referer"]').val() == 'breaking_news') {
                                    window.location.href = '/wp-admin/admin.php?page=340b_breaking_news';
                                } else {
                                    window.location.href = '/wp-admin/admin.php?page=340b_mailchimp_newsletter';
                                }
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
                                    'nonce': $(this).data('nonce'),
                                    'newsletter_referer': $('input[name="newsletter_referer"]').val(),
                                    'target_subscribers': $('.subscribers-wrap .target_subscribers').val(),
                            
                                },
                                success: function (response) {
                                    // $('#setting-error-340b-newsletter').removeClass('notice-error').addClass('notice-success');
                                    // $('#setting-error-340b-newsletter strong span:eq(0)').text('NewsLetter Draft Removed Successfully.');
                                    swal("Draft was removed successfully! You are being redirected...", {
                                        icon: "success",
                                    });
                                    setTimeout(function () {
                                        if ($('input[name="newsletter_referer"]').val() == 'breaking_news') {
                                            window.location.href = '/wp-admin/admin.php?page=340b_breaking_news';
                                        } else {
                                            window.location.href = '/wp-admin/admin.php?page=340b_mailchimp_newsletter';
                                        }
                                    }, 400);
                                }
                            });
                        } else {
                            swal("Your Newsletter draft is safe!");
                        }
                    });
            });
        }

        var target_subscribers;

        if( $('.target_subscribers').length ){
            $.ajax({
                url: advAjax.url,
                dataType : "json",
                type: 'POST',
                data: {
                    'action': 'get_mailchimp_lists'
                },
                success: function (response) {
                    var lists = response.lists;
                    var html = '';
                    for( var i=0; i<lists.length; i++ ){
                        html += '<option value="'+lists[i].id+'">'+lists[i].name+'</option>';
                    }
                    $('.target_subscribers').append(html);
                    $('.subscribers-wrap .loading').fadeOut();
                }
            });

            // $('.target_subscribers').on('change', function(){
            //     if( target_subscribers != $(this).val() ){
            //         var subscribers = $(this).val();
            //         $.ajax({
            //             url: advAjax.url,
            //             dataType : "json",
            //             type: 'POST',
            //             data: {
            //                 'action': 'get_list_segments',
            //                 'list_id': subscribers
            //             },
            //             success: function (response) {
            //                 var segments = response.segments;
            //                 var html = '<h3 class="adv-headings">Segments: </h3><br><br><select name="target_segment" name="target_segment" class="target_segment" required><option value="">Choose Segment: </option>';

            //                 for( var i=0; i<segments.length; i++ ){
            //                     html += '<option value="'+segments[i].id+'">'+segments[i].name+'</option>';
            //                 }
            //                 html += $('.subscribers-wrap .loading').html();
            //                 $('.segments-wrap').html(html);
            //                 $('.segments-wrap .loading').fadeOut();
            //             }
            //         });
            //     }
            //     target_subscribers = $(this).val();
            // });
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

                    if ((up_height != im_height || up_width != im_width) && $(container).find('.error').length) {
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

        function removePostFunction() {
            $('.form-adver .remove-above-post').on('click', function () {
                $(this).parents('.additional-article').hide();
                $('.form-adver.add-wrap').show();

                // if ($('.additional-article').length == 1) {
                //     $('<div class="form-adver"><div class="form-fields"><a href="javascript:void(0);" class="add-more-post"><span class="plus"></span>Add another article</a></div></div>').insertAfter('.additional-article');
                //     addMoreArticleToNewsletter();
                // }
            });
        }

        function addMoreArticleToNewsletter() {
            $('.form-adver .add-more-post').on('click', function () {
                // $('<div class="form-adver additional-article"><div class="form-fields"><input name="article_title_2b" type="text" placeholder="Title of article"></div><div class="form-fields"><input name="article_url_2" type="text" placeholder="Article URL"></div><div class="form-fields"><textarea name="article_description_2" placeholder="Article description" spellcheck="false"></textarea></div><div class="form-fields"> <a href="javascript:void(0);" class="remove-above-post"><span class="minus"></span>Remove the above article</a></div></div>').insertAfter($('.advertise-add .additional-article:last'));
                // removePostFunction();
                // if ($('.additional-article').length >= 2) {
                //     $('.add-more-post').parent().parent().remove();
                // }
                $('.form-adver.add-wrap').hide();
                $('.form-adver.additional-article:eq(1)').show();
            });
        }

        function closingMessageSwitch() {
            if ($('.newsletter_audience').length) {
                if ($('.newsletter_audience').val() == '3374743') {
                    $('.closing_message_subscribers').hide();
                    $('.closing_message_members').show();
                } else if ($('.newsletter_audience').val() == '3366313') {
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