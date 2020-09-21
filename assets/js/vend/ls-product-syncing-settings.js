(function ($) {

    $(document).ready(function () {
        lsVendSyncModal.init({
            first_message_label: "Getting products from Vend since last update.",
            no_products_to_import_woo: "No products were imported to WooCommerce since last update"
        });
    });

})(jQuery);