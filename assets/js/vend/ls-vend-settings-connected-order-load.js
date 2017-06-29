(function ($) {

    $(document).ready(function () {

        var $mainContainer = $('#ls-main-wrapper');
        var orderby = getParameterByName('orderby');
        var order = getParameterByName('order');
        console.log(orderby);

        if (null == orderby) {
            orderby = 'id';
        }

        if (null == order) {
            order = 'desc';
        }

        var data = {
            action: 'vend_load_connected_order',
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
            $('#id > a').attr('href', connected_orders_url + '&orderby=' + data.orderby + '&order=' + order);
            $('#id > a > .sorting-indicator').hide();
        });

    });

}(jQuery));