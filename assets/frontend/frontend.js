(function ($) {
    'use strict';

    $(function () {
        var bodyContainer, category;
        if($('body.home').length){
            bodyContainer = 'home';
            category = '';
        } else if($('body.single-post').length){
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
        console.log(data);

        $.post(advAjax.url, data, function (response) {
            var data = JSON.parse(response);
            advDashboardAdGen(data);
        });
    });
})(jQuery);

function advDashboardAdGen(data) {
    var $ = jQuery;
    if ($('.header_logo_wrapper .container .logo_wrapper').length) {
        $('.header_logo_wrapper .container').prepend(data.banner);
    }
    $('footer').prepend(data.footer);
    $('#categories-4').append(data.sidebar_one);
    $('#recent-posts-2').append(data.sidebar_two);
    if ($('body').hasClass('single-post')) {
        // $('.entry-content').append(data.in_story);
        $(data.in_story).insertAfter('.entry-content p:eq(1)');
    }
}