(function ($) {
    'use strict';

    $(function () {
        var data = {
            action: 'showAdverts',
            nonce: advAjax.nonce,
            dataType: 'json'
        };

        $.post(advAjax.url, data, function (response) {
            console.log(JSON.parse( response ));
            var data = JSON.parse(response);
            if( $('.header_logo_wrapper .container .logo_wrapper').length ){
                $('.header_logo_wrapper .container').prepend(data.banner);
            }
            $('footer').prepend(data.footer);
            $('#categories-4').append(data.sidebar_one);
            $('#recent-posts-2').append(data.sidebar_two);
            if( $('body').hasClass('single-post') ){
                $('.entry-content').append(data.in_story);
            }
        });
    });
})(jQuery);