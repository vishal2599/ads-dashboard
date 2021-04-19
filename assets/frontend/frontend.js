(function ($) {
    'use strict';

    $(function () {
        var bodyContainer, category;
        if ($('body').hasClass('home')) {
            bodyContainer = 'home';
            category = '';
        } else if ($('body').hasClass('single-post')) {
            bodyContainer = 'post';
            category = $('main .blog_posts_wrapper').data('id');
        }
        var data = {
            action: 'showAdverts',
            nonce: advAjax.nonce,
            'container': bodyContainer,
            'cat_id': category,
            dataType: 'json'
        };

        $.post(advAjax.url, data, function (response) {
            var data = JSON.parse(response);
            // console.log(data);
            advDashboardAdGen(data);
        });
    });
})(jQuery);

function advDashboardAdGen(data) {
    var $ = jQuery;

    if ($('body').hasClass('home')) {
        if ($('.header_logo_wrapper .container .logo_wrapper').length) {
            $('.header_logo_wrapper .container').prepend(data.banner);
        }
        $('footer').prepend(data.footer);
        $('#categories-4').append(data.sidebar_one);
        $('#recent-posts-2').append(data.sidebar_two);
        // $(data.in_story).insertAfter('.entry-content p:eq(1)');
    }

    if ($('body').hasClass('single-post') && !$('main').hasClass('industry-insights')) {
        if ($('.header_logo_wrapper .container .logo_wrapper').length) {
            $('.header_logo_wrapper .container').prepend(data.banner);
        }
        $('footer').prepend(data.footer);
        $('#categories-4').append(data.sidebar_one);
        $('#recent-posts-2').append(data.sidebar_two);
        $(data.in_story).insertAfter('.entry-content p:eq(1)');
    }
}