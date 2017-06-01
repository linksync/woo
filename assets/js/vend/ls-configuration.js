(function ($) {

    var lsConfigurationPage = {
        init: function () {
            this.cacheDom();
            this.bindEvents();

        },

        cacheDom: function () {
            this.$mainContainer = $('#ls-main-wrapper');
        },

        bindEvents: function () {

        }
    };

    $(document).ready(function () {

        lsVendSyncModal.init({
            first_message_label: "Getting products from Vend since last update.",
            no_products_to_import_woo: "No products were imported to WooCommerce since last update",
            action: 'vend_since_last_sync'
        });

        // lsConfigurationPage.init();
    });

})(jQuery);