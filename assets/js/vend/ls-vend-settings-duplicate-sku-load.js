(function ($) {

    $(document).ready(function () {

        var $mainContainer = $('#ls-main-wrapper');
        var orderby = getParameterByName('orderby');
        var order = getParameterByName('order');
        if (null == orderby) {
            orderby = 'name';
        }

        if (null == order) {
            order = 'desc';
        }

        var data = {
            action: 'vend_load_duplicate_sku',
            page: 'configuration',
            orderby: orderby,
            order: order
        };
        var $loading = $mainContainer.find('.ls-loading');
        $loading.removeClass('close');
        lsAjax.post(data).done(function (html) {
            $loading.addClass('close');
            $mainContainer.html(html);
        });

    });

}(jQuery));