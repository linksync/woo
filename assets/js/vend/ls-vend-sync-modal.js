(function ($) {


    lsVendSyncModal = {
        options: '',
        init: function (options) {
            this.cacheDom();
            this.bindEvents();
            this.setOptions(options);
        },

        setOptions: function (options) {
            this.options = options;
        },

        cacheDom: function () {
            this.$mainContainer = $('#ls-main-wrapper');
            this.$btnVendToWoo = this.$mainContainer.find('.product_sync_to_woo');
            this.$btnWooToVend = this.$mainContainer.find('.product_sync_to_vend');
            this.btnVendToWooSinceLastSync = this.$mainContainer.find('.product_sync_to_woo_since_last_sync');

            this.$modalMessage = this.$mainContainer.find('.sync-modal-message');
            this.$popUpButtons = this.$mainContainer.find('#pop_button');
            this.$closeIcon = this.$mainContainer.find('.close-icon');
            this.$syncProgressContainer = this.$mainContainer.find('#sync_progress_container');
            this.$progressBar = this.$mainContainer.find("#progressbar");
            this.$progressBarLabel = this.$mainContainer.find(".progress-label");
            this.$dasboardLink = this.$mainContainer.find('.ls-dashboard-link');
            this.$syncModalContainer = this.$mainContainer.find('.ls-vend-sync-modal');
            this.$modalContent = this.$mainContainer.find('.ls-vend-sync-modal-content');
            this.$modalBackDrop = this.$mainContainer.find('.ls-modal-backdrop');
            this.$modalClose = this.$mainContainer.find('.ls-modal-close');
            this.$btnShowPopUpForWooToVend = this.$mainContainer.find('.btn-sync-woo-to-vend');
            this.$btnShowPopUpForVendToWoo = this.$mainContainer.find('.btn-sync-vend-to-woo');
            this.$btnShopPopUpForVendToWooSinceLastUpdate = this.$mainContainer.find('.vend_to_woo_since_last_update');
            this.$syncButtonsContainer = this.$mainContainer.find('.sync-buttons');

            this.$syncToWooButtons = this.$mainContainer.find('.sync-to-woo-buttons');
            this.$syncToVendButtons = this.$mainContainer.find('.sync-to-vend-buttons');
            this.$syncToWooButtonsSinceLastSync = this.$mainContainer.find('.sync-to-woo-buttons-since-last-update');
        },

        bindEvents: function () {
            this.btnVendToWooSinceLastSync.on('click', function () {
                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.setOptions({
                        first_message_label: "Getting products from Vend since last update.",
                        no_products_to_import_woo: "No products were imported to WooCommerce since last sync",
                        action: 'vend_since_last_sync'
                    });
                    lsVendSyncModal.syncProductsFromVend();
                });
            });

            this.$btnVendToWoo.on('click', function () {
                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.setOptions({
                        first_message_label: "Getting products from Vend.",
                        no_products_to_import_woo: "No products were imported to WooCommerce",
                        action: 'vend_get_products'
                    });
                    lsVendSyncModal.syncProductsFromVend();
                });
            });
            
            this.$btnWooToVend.on('click', function () {

                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.syncProductsToVend();
                })
            });

            this.$btnShowPopUpForVendToWoo.on('click', function () {

                lsVendSyncModal.open({
                    buttonGroup : 'vend_to_woo',
                    htmlMessage : 'Your products from Vend will be imported to WooCommerce.<br/>Do you wish to continue?'
                });

            });

            this.$btnShowPopUpForWooToVend.on('click', function () {

                lsVendSyncModal.open({
                    buttonGroup : 'woo_to_vend',
                    htmlMessage : 'Your WooCommerce products will be exported to Vend.<br/>Do you wish to continue?'
                });
            });

            this.$btnShopPopUpForVendToWooSinceLastUpdate.on('click', function () {

                lsVendSyncModal.open({
                    buttonGroup : 'vend_to_woo_since_last_sync',
                    htmlMessage : 'Your products from Vend will be imported to WooCommerce.<br/>Do you wish to continue?'
                });

            });

            this.$modalClose.on('click', function () {
                lsVendSyncModal.close(1);
            });

            this.initializeSyncProgress();

        },

        syncProductsToVend: function () {
            var product_number = 0;
            lsVendSyncModal.$progressBar.progressbar("value", 1);
            lsAjax.post({action: 'vend_woo_get_products'}, function (woo_products) {
                lsVendSyncModal.$progressBarLabel.html("Getting WooCommerce products to be exported in Vend.");
                lsVendSyncModal.$progressBar.progressbar("value", 2);
                console.log(woo_products);

                if (!$.isEmptyObject(woo_products)) {
                    var product_total_count = woo_products.length;

                    if (product_total_count > 0) {
                        for (var i = 0; i < product_total_count; i++) {

                            product_number = i + 1;
                            var data = {
                                action: 'vend_import_to_vend',
                                p_id: woo_products[i].ID,
                                product_number: product_number,
                                total_count: product_total_count,
                            };
                            lsAjax.post(data, function (p_res) {
                                progressVal = lsVendSyncModal.$progressBar.progressbar("value");
                                if (progressVal < p_res.percentage) {
                                    lsVendSyncModal.$progressBar.progressbar("value", p_res.percentage);
                                    lsVendSyncModal.$progressBarLabel.html("Exported " + p_res.msg + " to Vend (" + p_res.percentage + "%)");
                                }
                                console.log(p_res);

                            });
                        }
                    } else {
                        lsVendSyncModal.$progressBar.progressbar("value", 100);
                        lsVendSyncModal.$progressBarLabel.html("No products from WooCommerce to export in Vend");
                    }

                } else {
                    lsVendSyncModal.$progressBar.progressbar("value", 100);
                    lsVendSyncModal.$progressBarLabel.html("No products from WooCommerce to export in Vend");
                }
            })
        },

        syncProductsFromVend: function (page) {

            //check if page is undefined then we set it to one
            if (typeof page == 'undefined') {
                page = 1;
            } else if (page <= 0) {
                //Make sure we always start to page 1
                page = 1;
            }
            var product_number = 0;

            if(lsVendSyncModal.options.first_message_label == null){
                lsVendSyncModal.$progressBarLabel.html("Getting products from Vend.");
            } else {
                lsVendSyncModal.$progressBarLabel.html(lsVendSyncModal.options.first_message_label);
            }

            lsVendSyncModal.$progressBar.progressbar("value", 1);

            var action = 'vend_get_products';
            if(lsVendSyncModal.options.action != null){
                action = lsVendSyncModal.options.action;
            }

            var data_to_request = {
                action: action,
                page: page
            };
            console.log(data_to_request);
            lsAjax.post(data_to_request, function (res) {
                lsVendSyncModal.$progressBar.progressbar("value", 2);

                var product_count = res.products.length;
                var totalProductCount = res.pagination.results;
                console.log("total product result " + totalProductCount);
                console.log(res);

                if (product_count > 0) {

                    for (var i = 0; i < product_count; i++) {
                        product = res.products[i];
                        if (product.deleted_at == null) {
                            product_number = i + 1;
                        }

                        if (res.pagination.page > 1) {
                            product_number += 50;
                        }


                        var p_data = {
                            action: 'vend_import_to_woo',
                            page: res.pagination.page,
                            product_total_count: res.pagination.results,
                            product: product,
                            product_number: product_number,
                            deleted_product: res.pagination.deleted_product
                        };

                        lsAjax.post(p_data, function (p_res) {

                            progressVal = lsVendSyncModal.$progressBar.progressbar("value");
                            if (progressVal < p_res.percentage) {
                                lsVendSyncModal.$progressBar.progressbar("value", p_res.percentage);
                                lsVendSyncModal.$progressBarLabel.html("Imported " + p_res.msg + " in WooCommerce (" + p_res.percentage + "%)");
                            }
                            console.log(p_res);
                            console.log("progress => " + p_res.percentage);
                        });
                    }
                } else if (product_count <= 1) {
                    lsVendSyncModal.$progressBar.progressbar("value", 100);

                    if(lsVendSyncModal.options.no_products_to_import_woo == null){
                        lsVendSyncModal.$progressBarLabel.html("No products were imported to WooCommerce");
                    } else {
                        lsVendSyncModal.$progressBarLabel.html(lsVendSyncModal.options.no_products_to_import_woo);
                    }
                }

            })
        },

        hideSyncButtonsAndShowProgress: function (callback) {

            lsVendSyncModal.$popUpButtons.hide();
            lsVendSyncModal.$modalMessage.hide();
            lsVendSyncModal.$closeIcon.hide();
            lsVendSyncModal.$syncProgressContainer.show();

            lsVendSyncModal.initializeSyncProgress();
            lsVendSyncModal.$progressBarLabel.html("");
            lsVendSyncModal.$modalBackDrop.removeClass('close').addClass('open');
            lsVendSyncModal.$modalContent.css({
                'z-index': '99999'
            });

            if (typeof callback === "function") {
                callback();
            }
        },

        initializeSyncProgress: function () {

            lsVendSyncModal.$progressBar.progressbar({
                value: true,
                complete: function () {
                    lsVendSyncModal.$dasboardLink.removeClass('hide');
                    lsVendSyncModal.close();
                }
            });
            lsVendSyncModal.$progressBar.progressbar("value", 0);

        },

        close: function (delay) {
            if (typeof delay == 'undefined') {
                delay = 4000;
            }
            lsVendSyncModal.$syncModalContainer.delay(delay).fadeOut('fast', function () {
                lsVendSyncModal.$progressBarLabel.html("Sync Completed!");
                lsVendSyncModal.$modalBackDrop.removeClass('open').addClass('close');
                lsVendSyncModal.initializeSyncProgress();
                lsVendSyncModal.$modalMessage.show();
                lsVendSyncModal.$syncProgressContainer.hide();
                lsVendSyncModal.$popUpButtons.show();

                lsVendSyncModal.$modalContent.fadeOut();

            });
        },

        open: function (option) {

            console.log(option);

            lsVendSyncModal.$syncButtonsContainer.hide();
            var message = 'Your products from Vend will be imported to WooCommerce.<br/>Do you wish to continue?';
            if (null != option.htmlMessage) {
                message = option.htmlMessage;
            }

            lsVendSyncModal.$modalMessage.html(message);

            if ('woo_to_vend' == option.buttonGroup) {
                lsVendSyncModal.$syncToVendButtons.show();
            } else if ('vend_to_woo' == option.buttonGroup) {
                lsVendSyncModal.$syncToWooButtons.show();
            } else if('vend_to_woo_since_last_sync' == option.buttonGroup){
                lsVendSyncModal.$syncToWooButtonsSinceLastSync.show();
            }

            lsVendSyncModal.$syncModalContainer.fadeIn();
            lsVendSyncModal.$modalContent.fadeIn();
        }
    };

})(jQuery);