(function ($) {

    $(document).ready(function () {

        var $mainContainer = $('#ls-main-wrapper');

        var data = {
            action: 'vend_load_order_tab',
            page: 'order'
        };
        var $loading = $mainContainer.find('.ls-loading');
        $loading.removeClass('close');
        lsAjax.post(data).done(function (html) {
            $loading.addClass('close');
            $mainContainer.html(html);
        });

    });

}(jQuery));