(function ($) {

    function advanceTabLoad() {
        var $mainContainer = $('#ls-main-wrapper');

        var data = {
            action: 'vend_load_advance_tab',
            page: 'support'
        };
        var $loading = $mainContainer.find('.ls-loading');
        $loading.removeClass('close');
        lsAjax.post(data).done(function (html) {
            $loading.addClass('close');
            $mainContainer.html(html);
        });
    }
    $(document).ready(function () {

        advanceTabLoad();

    });


}(jQuery));