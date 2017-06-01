(function ($) {

    lsAjax = {
        /**
         * ajax post request
         * @param data
         * @param callback
         */
        post: function (data, callback) {
            /**
             * since wordpress 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
             * @reference https://codex.wordpress.org/AJAX_in_Plugins#Ajax_on_the_Administration_Side
             */
            return $.post(ajaxurl, data, callback);
        },

        /**
         * Get products by page, each page contains a maximum of 50 products
         * @param page
         * @param callback
         */
        get_product_by_page: function (page, callback) {
            var data = {
                action: 'vend_get_products',
                page: page
            };

            lsAjax.post(data, function (response) {

                if (typeof callback === "function") {
                    callback(response);
                }

            });

        },

        /**
         * Get products by page, each page contains a maximum of 50 products
         * @param page
         * @param callback
         */
        get_product_since_last_update: function (page, callback) {
            var data = {
                action: 'vend_since_last_sync',
                page: page
            };

            lsAjax.post(data, function (response) {

                if (typeof callback === "function") {
                    callback(response);
                }

            });

        },

        done_required_sync: function () {
            var data = {
                action: 'vend_done_syncing_required'
            };
            lsAjax.post(data, function (data) {
                $('.require-resync').hide();
            });
        }
    }
}(jQuery));