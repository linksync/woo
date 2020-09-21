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
        console.log(orderby);
        var data = {
            action: 'vend_load_connected_product',
            page: 'configuration',
            orderby: orderby,
            order: order
        };
        var $loading = $mainContainer.find('.ls-loading');
        $loading.removeClass('close');
        lsAjax.post(data).done(function (html) {
            $loading.addClass('close');
            $mainContainer.html(html);
            if ('desc' == order) {
                order = 'asc'
            } else if ('asc' == order) {
                order = 'desc'
            }
            $('#name > a').attr('href', connected_products_url + '&orderby=' + orderby + '&order=' + order);
            $('#name > a > .sorting-indicator').hide();
        });

    });

}(jQuery));