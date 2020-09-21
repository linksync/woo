(function ($) {

    $(document).ready(function () {

        var $mainContainer = $('#ls-main-wrapper');

        var data = {
            action: 'vend_load_product_tab',
            page: 'product'
        };
        var $loading = $mainContainer.find('.ls-loading');
        $loading.removeClass('close');
        lsAjax.post(data).done(function (html) {
            $loading.addClass('close');
            $mainContainer.html(html);
        });

    });

}(jQuery));