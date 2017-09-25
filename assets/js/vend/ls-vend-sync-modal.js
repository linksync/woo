(function ($) {

    function getFormattedTimeWithOutSeconds(date) {
        var date = new Date(date);

        var years = date.getFullYear();
        var months = date.getMonth() + 1;
        var days = date.getDate();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();

        if (years < 10) {
            years = "0" + years;
        }
        if (months < 10) {
            months = "0" + months;
        }
        if (days < 10) {
            days = "0" + days;
        }


        if (hours < 10) {
            hours = "0" + hours;
        }
        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            seconds = "0" + seconds;
        }

        return years + '-' + months + '-' + days + ' ' + hours + ':' + minutes;
    }

    LS_Syncing_Time = {
        start_date : new Date(),
        end_date: new Date(),
        set_start_time: function () {
            this.start_date = new Date();
        },

        get_start_time: function () {
            return this.start_date;
        },

        set_end_time: function () {
            this.end_date = new Date();
        },

        get_end_time: function () {
            return this.end_date;
        },

        get_execution_time_in_seconds: function () {
            return (this.get_end_time().getTime() - this.get_start_time().getTime()) / 1000;
        },

        get: function () {
            return {
                'start_time': this.get_start_time(),
                'end_time' : this.get_end_time(),
                'execution_time_in_seconds' : this.get_execution_time_in_seconds()
            }
        }
    };

    lsVendSyncModal = {
        options: '',
        SYNC_LIMIT: 0,
        syncing_times_in_seconds: [],
        get_average_syncing_time_in_seconds: function () {
            var average_syncing_time_in_seconds = 0;
            var recorded_syncing_time_count = lsVendSyncModal.syncing_times_in_seconds.length;

            console.log('lsVendSyncModal.syncing_times_in_seconds', lsVendSyncModal.syncing_times_in_seconds);
            console.log('recorded_syncing_time_count', recorded_syncing_time_count);
            if(recorded_syncing_time_count > 0){

                lsVendSyncModal.syncing_times_in_seconds.forEach(function (time_in_seconds, index) {
                    console.log(time_in_seconds);
                    average_syncing_time_in_seconds += time_in_seconds;
                })
            }

            return average_syncing_time_in_seconds / recorded_syncing_time_count;
        },

        calculated_time_of_completion: function (seconds) {

            var total_seconds_for_completion = seconds;
            var total_minutes_for_completion = total_seconds_for_completion / 60;
            var total_hours_for_completion = total_minutes_for_completion / 60;

            var calculated_time_of_completion = '';
            if (total_seconds_for_completion >= 1) {
                calculated_time_of_completion = ' ( ' + total_seconds_for_completion.toFixed(2) + ' seconds )';
            }

            if (total_minutes_for_completion >= 1) {
                calculated_time_of_completion = ' ( ' + total_minutes_for_completion.toFixed(2) + ' minutes )';
            }

            if (total_hours_for_completion >= 1) {
                calculated_time_of_completion = ' ( ' + total_hours_for_completion.toFixed(2) + ' hours )';
            }

            return calculated_time_of_completion;
        },

        init: function (options) {
            this.cacheDom();
            this.bindEvents();
            this.setOptions(options);
        },

        setOptions: function (options) {
            this.options = options;
        },

        since: null,
        getSinceSpecifiedDate: function () {
           var $mainContainer = $('#ls-main-wrapper');
           return $mainContainer.find('#sinceSpecifiedDate').val();
        },

        cacheDom: function () {
            this.$mainContainer = $('#ls-main-wrapper');

            this.$estimatedTime = this.$mainContainer.find('#estimated_time');
            this.$btnClassVendToWoo = '.product_sync_to_woo';
            this.$btnClassWooToVend = '.product_sync_to_vend';
            this.$btnClassWooToVendViaFilter = '.product_sync_to_woo_via_filter';
            this.$btnClassVendToWooSinceLastSync = '.product_sync_to_woo_since_last_sync';
            this.$btnClassVendToWooSinceSpecified = '.product_sync_to_woo_since_specified';
            this.btnClassShowPopUpForWooToVend = '.btn-sync-woo-to-vend';
            this.btnClassShowPopUpForWooToVendViaFilter = '.btn-sync-woo-to-vend-via-filter';
            this.btnClassShowPopUpForVendToWoo = '.btn-sync-vend-to-woo';
            this.btnClassShopPopUpForVendToWooSinceLastUpdate = '.vend_to_woo_since_last_update';
            this.btnClassShopPopUpForVendToWooSinceSpecified = '.vend_to_woo_since_specified';


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
            this.$modalCloseContainer = this.$mainContainer.find('.ls-modal-close-container');
            this.$syncButtonsContainer = this.$mainContainer.find('.sync-buttons');

            this.$syncToWooButtons = this.$mainContainer.find('.sync-to-woo-buttons');
            this.$syncToWooViaFilterButtons = this.$mainContainer.find('.sync-to-woo-via-filter-buttons');
            this.$syncToVendButtons = this.$mainContainer.find('.sync-to-vend-buttons');
            this.$syncToWooButtonsSinceLastSync = this.$mainContainer.find('.sync-to-woo-buttons-since-last-update');
            this.$syncToWooButtonsSinceSpecified = this.$mainContainer.find('.sync-to-woo-buttons-since-specified');
            this.$syncTwoWayButtons = this.$mainContainer.find('.two-way-sync-vend-buttons');

            this.$tabMenu = this.$mainContainer.find('.ls-tab-menu');
        },

        on: function (event, childOrCallback, callback) {
            if (typeof callback == 'undefined') {
                this.$mainContainer.on(event, childOrCallback);
            } else {
                this.$mainContainer.on(event, childOrCallback, callback);
            }
        },

        click: function (child, callback) {
            this.on('click', child, callback);
        },

        openTwoWaySyncModal: function () {
            lsVendSyncModal.cacheDom();
            lsVendSyncModal.open({
                buttonGroup: 'two_way',
                htmlMessage: 'Your changes will require a full re-sync of product data. <br/>Do you want to re-sync now?<br/>'
            });
        },

        openWooToVendSyncModal: function () {
            lsVendSyncModal.cacheDom();
            lsVendSyncModal.open({
                buttonGroup: 'woo_to_vend',
                htmlMessage: 'Your WooCommerce products will be exported to Vend.<br/>Do you wish to continue?'
            });
        },

        openWooToVendViaFilterSyncModal: function () {
            lsVendSyncModal.cacheDom();
            lsVendSyncModal.open({
                buttonGroup: 'woo_to_vend_via_filter',
                htmlMessage: 'Your WooCommerce products will be exported to Vend.<br/>Do you wish to continue?'
            });
        },

        openVendToWooSyncModal: function () {
            lsVendSyncModal.cacheDom();
            lsVendSyncModal.open({
                buttonGroup: 'vend_to_woo',
                htmlMessage: 'Your products from Vend will be imported to WooCommerce.<br/>Do you wish to continue?'
            });
        },

        openVendToWooSinceLastSyncModal: function () {
            lsVendSyncModal.cacheDom();
            lsVendSyncModal.open({
                buttonGroup: 'vend_to_woo_since_last_sync',
                htmlMessage: 'Your products from Vend will be imported to WooCommerce.<br/>Do you wish to continue?'
            });
        },

        openVendToWooSinceSpecified: function () {
            lsVendSyncModal.cacheDom();
            lsVendSyncModal.open({
                buttonGroup: 'vend_to_woo_since_specified',
                htmlMessage: 'Your products from Vend will be imported to WooCommerce.<br/>Do you wish to continue?'
            });
        },

        bindEvents: function () {

            this.click(this.$btnClassVendToWooSinceLastSync, function () {
                lsVendSyncModal.cacheDom();
                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.setOptions({
                        first_message_label: "Getting products from Vend since last update.",
                        no_products_to_import_woo: "No products were imported to WooCommerce since last sync",
                        action: 'vend_since_last_sync'
                    });
                    lsVendSyncModal.$progressBar.progressbar("value", 0);
                    lsVendSyncModal.$progressBarLabel.html("Sync is starting!");
                    lsVendSyncModal.syncProductsFromVend();
                });
            });


            this.click(this.$btnClassVendToWooSinceSpecified, function () {
                var specifiedDate = lsVendSyncModal.getSinceSpecifiedDate();
                lsVendSyncModal.since = specifiedDate;
                lsVendSyncModal.cacheDom();
                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.setOptions({
                        first_message_label: "Getting products from Vend since "+specifiedDate+".",
                        no_products_to_import_woo: "No products were imported to WooCommerce since last sync",
                        action: 'vend_get_products'
                    });
                    lsVendSyncModal.$progressBar.progressbar("value", 0);
                    lsVendSyncModal.$progressBarLabel.html("Sync is starting!");
                    lsVendSyncModal.syncProductsFromVend(1, specifiedDate);
                });
            });

            this.click(this.$btnClassVendToWoo, function () {
                lsVendSyncModal.cacheDom();
                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.setOptions({
                        first_message_label: "Sync is starting!",
                        no_products_to_import_woo: "No products were imported to WooCommerce",
                        action: 'vend_get_products'
                    });
                    lsVendSyncModal.$progressBar.progressbar("value", 0);
                    lsVendSyncModal.$progressBarLabel.html("Sync is starting!");
                    lsVendSyncModal.syncProductsFromVend();
                });
            });

            this.click(this.$btnClassWooToVend, function () {
                lsVendSyncModal.cacheDom();
                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.$progressBar.progressbar("value", 0);
                    lsVendSyncModal.$progressBarLabel.html("Sync is starting!");
                    lsVendSyncModal.syncProductsToVend();
                });
            });


            this.click(this.$btnClassWooToVendViaFilter, function () {
                lsVendSyncModal.cacheDom();
                lsVendSyncModal.hideSyncButtonsAndShowProgress(function () {
                    lsVendSyncModal.$progressBar.progressbar("value", 0);
                    lsVendSyncModal.$progressBarLabel.html("Sync is starting!");
                    lsVendSyncModal.syncProductsToVendViaFilter();
                });
            });



            this.click('.ls-modal-close', function () {
                lsVendSyncModal.cacheDom();
                lsVendSyncModal.close(1);
            });


            this.click(this.btnClassShowPopUpForVendToWoo, function () {
                lsVendSyncModal.openVendToWooSyncModal();
            });

            this.click(this.btnClassShowPopUpForWooToVend, function () {
                lsVendSyncModal.openWooToVendSyncModal();
            });

            this.click(this.btnClassShowPopUpForWooToVendViaFilter, function () {
                lsVendSyncModal.openWooToVendViaFilterSyncModal();
            });

            this.click(this.btnClassShopPopUpForVendToWooSinceLastUpdate, function () {
                lsVendSyncModal.openVendToWooSinceLastSyncModal();
            });

            this.click(this.btnClassShopPopUpForVendToWooSinceSpecified, function () {
                lsVendSyncModal.openVendToWooSinceSpecified();
            });

            this.initializeSyncProgress();

        },

        stopSyncing: function (product_sync_response) {
            var syncing_response = product_sync_response.response_product_to_vend.response;
            console.log(syncing_response);
            if (
                syncing_response.errorCode &&
                syncing_response.type &&
                'C400' == syncing_response.type
            ) {
                console.log('Stop syncing now!');
                console.log(syncing_response);

                return true;
            }

            return false;
        },
        showCappingHtmlError: function (htmlErrorMessage) {
            lsVendSyncModal.$modalMessage.html('<p style="color:red;">' + htmlErrorMessage + '</p>');
            lsVendSyncModal.$modalMessage.show();
            lsVendSyncModal.$modalCloseContainer.show();
            lsVendSyncModal.$syncProgressContainer.hide();
            lsVendSyncModal.$modalClose.show();
            $('.product-capping-error').remove();
            $('.ls-trial-message').remove();
            lsVendSyncModal.$mainContainer.before('<div class="notice notice-error product-capping-error"><p>' + htmlErrorMessage + '</p></div>');
        },

        syncProductToVend: function (woocommerce_products, product_index) {
            var syncingTime = Object.create(LS_Syncing_Time);
            syncingTime.set_start_time();

            if (typeof product_index == 'undefined') {
                product_index = 0;
            } else if (product_index <= 0) {
                //Make sure we always start to index 1
                product_index = 0;
            }
            var product_total_count = woocommerce_products.length;
            if (product_total_count > 0) {

                if (typeof woocommerce_products[product_index] != 'undefined') {

                    var product_number = product_index + 1;
                    var data = {
                        action: 'vend_import_to_vend',
                        p_id: woocommerce_products[product_index].ID,
                        product_number: product_number,
                        total_count: product_total_count,
                    };
                    lsAjax.post(data).done(function (product_sync_response) {
                        console.log('Successful AJAX Call! Return Data: =>');
                        console.log(product_sync_response);
                        if(product_sync_response){

                            var haltSync = lsVendSyncModal.stopSyncing(product_sync_response);
                            console.log('haltSync => '+haltSync);
                            if (false == haltSync) {

                                syncingTime.set_end_time();
                                lsVendSyncModal.syncing_times_in_seconds.push(syncingTime.get_execution_time_in_seconds());

                                var currentDate = new Date();
                                var average_syncing_time_in_seconds = lsVendSyncModal.get_average_syncing_time_in_seconds();
                                var remaining_item_to_sync = product_total_count - product_sync_response.product_number;
                                var total_seconds_for_completion = (average_syncing_time_in_seconds * remaining_item_to_sync);

                                var estimatedDateTime = currentDate.setSeconds(currentDate.getSeconds() + total_seconds_for_completion);
                                var calculated_time_of_completion = lsVendSyncModal.calculated_time_of_completion(total_seconds_for_completion);


                                lsVendSyncModal.$estimatedTime.html('Estimated syncing time of completion: <b>'+getFormattedTimeWithOutSeconds(estimatedDateTime)+calculated_time_of_completion+'</b>');
                                lsVendSyncModal.$estimatedTime.show();


                                lsVendSyncModal.$progressBarLabel.html("Exported " + product_sync_response.percentage + "% of WooCommerce products to Vend");
                                lsVendSyncModal.$modalClose.hide();
                                var progressVal = lsVendSyncModal.$progressBar.progressbar("value");

                                if (product_sync_response.product_number == product_total_count) {
                                    lsVendSyncModal.$progressBar.progressbar("value", 100);
                                    lsVendSyncModal.syncCompleted();
                                } else {

                                    if (progressVal < product_sync_response.percentage) {
                                        lsVendSyncModal.$progressBar.progressbar("value", product_sync_response.percentage);
                                    }

                                    var temp_product_index = product_index + 1;
                                    lsVendSyncModal.syncProductToVend(woocommerce_products, temp_product_index);
                                }



                            } else if (true == haltSync) {
                                var syncing_response = product_sync_response.response_product_to_vend.response;
                                var htmlErrorMessage = syncing_response.html_error_message;
                                console.log(htmlErrorMessage);
                                if(typeof htmlErrorMessage != 'undefined'){
                                    lsVendSyncModal.showCappingHtmlError(htmlErrorMessage);
                                }

                            }

                        } else {

                            var temp_product_index = product_index + 1;
                            lsVendSyncModal.syncProductToVend(woocommerce_products, temp_product_index);
                        }


                    }).fail(function (data) {

                        console.log('Failed AJAX Call of syncProductToVend :( Return Data: => ');
                        console.log(data);

                        //If failed, retry to sync with the same product index
                        lsVendSyncModal.syncProductToVend(woocommerce_products, product_index);

                    });

                } else if (typeof woocommerce_products[product_index] == 'undefined') {

                }


            } else {

                //No Woocommerce products to sync
                lsVendSyncModal.$progressBar.progressbar("value", 100);
                lsVendSyncModal.$progressBarLabel.html("No products from WooCommerce to export in Vend");

            }

        },

        syncProductsToVendViaFilter: function () {


            var $mainContainer = $('#ls-main-wrapper');


            var selectedStatuses = [];
            $('.other_syncable_status').each(function (e) {
                if($(this).prop('checked')){
                    var val = $(this).val();
                    selectedStatuses.push(val);
                }
            });

            var productTypes = $mainContainer.find('#dropdown_product_type');
            var productCategories = $mainContainer.find('#dropdown_product_category');
            var productTags = $mainContainer.find('#dropdown_product_tag');

            var postData = {
                statuses: selectedStatuses,
                product_types : productTypes.val(),
                categories : productCategories.val(),
                tags: productTags.val(),
                action: 'vend_woo_get_products_via_filter'
            };

            console.log(postData);

            lsAjax.post(postData).done(function (woo_products) {

                lsVendSyncModal.$progressBarLabel.html("Getting WooCommerce products to be exported in Vend.");
                console.log(woo_products);

                if (!$.isEmptyObject(woo_products)) {

                    lsVendSyncModal.syncProductToVend(woo_products, 0);

                } else {
                    lsVendSyncModal.$progressBar.progressbar("value", 100);
                    lsVendSyncModal.$progressBarLabel.html("No products from WooCommerce to export in Vend");
                }

            }).fail(function (data) {

                console.log('Failed AJAX Call of syncProductsToVend :( Return Data: => ');
                console.log(data);
                lsVendSyncModal.syncProductToVend();

            });
        },

        syncProductsToVend: function () {
            var syncingTime = Object.create(LS_Syncing_Time);
            syncingTime.set_start_time();

            lsAjax.post({action: 'vend_woo_get_products'}).done(function (woo_products) {

                lsVendSyncModal.$progressBarLabel.html("Getting WooCommerce products to be exported in Vend.");
                console.log(woo_products);

                if (!$.isEmptyObject(woo_products)) {

                    syncingTime.set_end_time();
                    lsVendSyncModal.syncing_times_in_seconds.push(syncingTime.get_execution_time_in_seconds());

                    lsVendSyncModal.syncProductToVend(woo_products, 0);

                } else {
                    lsVendSyncModal.$progressBar.progressbar("value", 100);
                    lsVendSyncModal.$progressBarLabel.html("No products from WooCommerce to export in Vend");
                }

            }).fail(function (data) {

                console.log('Failed AJAX Call of syncProductsToVend :( Return Data: => ');
                console.log(data);
                lsVendSyncModal.syncProductToVend();

            });
        },

        syncProductFromVend: function (linksync, product_number) {
            var syncingTime = Object.create(LS_Syncing_Time);
            syncingTime.set_start_time();

            if (typeof product_number == 'undefined') {
                product_number = 0;
            } else if (product_number <= 0) {
                //Make sure we always start to page 1
                product_number = 0;
            }

            json_linksync_products = linksync.products[product_number];
            if (typeof json_linksync_products != 'undefined') {
                console.log('json_linksync_products =>');
                console.log(json_linksync_products);

                var product_count = product_number + 1;
                if (linksync.pagination.page > 1) {
                    product_count = product_count + (50 * (linksync.pagination.page - 1));
                }

                if(json_linksync_products.variants.length > 0){
                    lsVendSyncModal.SYNC_LIMIT = lsVendSyncModal.SYNC_LIMIT + json_linksync_products.variants.length;
                } else {
                    lsVendSyncModal.SYNC_LIMIT = lsVendSyncModal.SYNC_LIMIT + 1;
                }

                var trialItemCount = linksync.pagination.trialItemCount;
                if(typeof linksync.pagination.trialItemCount == 'undefined'){
                    trialItemCount = 'capping_did_not_exists';
                }

                var p_data = {
                    action: 'vend_import_to_woo',
                    page: linksync.pagination.page,
                    product_total_count: linksync.pagination.results,
                    product: json_linksync_products,
                    product_number: product_count,
                    product_result_count: linksync.pagination.results,
                    deleted_product: linksync.pagination.deleted_product,
                    trial_item_count : trialItemCount,
                    sync_limit_count : lsVendSyncModal.SYNC_LIMIT
                };

                console.log('post data =>');
                console.log(p_data);
                lsVendSyncModal.$modalCloseContainer.hide();
                lsAjax.post(p_data).done(function (product_sync_response) {


                    console.log('Successful AJAX Call! Return Data: =>');
                    console.log('count = '+lsVendSyncModal.SYNC_LIMIT+' trialItemCount = '+trialItemCount);
                    if(
                        'capping_did_not_exists' != trialItemCount &&
                        lsVendSyncModal.SYNC_LIMIT == trialItemCount
                    ){
                        //Sync should stop
                        console.log('Sync should stop!');
                        if(typeof product_sync_response.html_error_message != 'undefined'){
                            lsVendSyncModal.showCappingHtmlError(product_sync_response.html_error_message);
                        }

                    } else {

                        var currentDate = new Date();
                        var average_syncing_time_in_seconds = lsVendSyncModal.get_average_syncing_time_in_seconds();
                        var remaining_item_to_sync = product_sync_response.product_results_count - product_sync_response.product_number;
                        var total_seconds_for_completion = (average_syncing_time_in_seconds * remaining_item_to_sync);

                        var estimatedDateTime = currentDate.setSeconds(currentDate.getSeconds() + total_seconds_for_completion);
                        var calculated_time_of_completion = lsVendSyncModal.calculated_time_of_completion(total_seconds_for_completion);

                        lsVendSyncModal.$estimatedTime.html('Estimated syncing time of completion: <b>'+getFormattedTimeWithOutSeconds(estimatedDateTime)+calculated_time_of_completion+'</b>');
                        lsVendSyncModal.$estimatedTime.show();

                        lsVendSyncModal.$progressBarLabel.html("Imported " + product_sync_response.percentage + "% of products in WooCommerce");
                        lsVendSyncModal.$modalClose.hide();
                        progressVal = lsVendSyncModal.$progressBar.progressbar("value");

                        if (product_sync_response.product_number == linksync.pagination.results) {
                            lsVendSyncModal.$progressBar.progressbar("value", 100);

                            lsVendSyncModal.syncCompleted();
                        } else {

                            if (progressVal < product_sync_response.percentage) {
                                lsVendSyncModal.$progressBar.progressbar("value", product_sync_response.percentage);
                            }

                            var product_index = product_number + 1;
                            lsVendSyncModal.syncProductFromVend(linksync, product_index);

                        }




                    }

                }).fail(function (data) {

                    console.log('Failed AJAX Call of syncProductFromVend :( Return Data: ');
                    console.log(data);
                    //If ajax failed retry with the same product_number
                    lsVendSyncModal.syncProductFromVend(linksync, product_number);
                });

            } else if (typeof json_linksync_products == 'undefined') {
                console.log('No product index page => ' + linksync.pagination.page + ' pages => ' + linksync.pagination.pages);
                var page = linksync.pagination.page + 1;
                if (linksync.pagination.pages >= page) {
                    lsVendSyncModal.syncProductsFromVend(page, lsVendSyncModal.since);
                }

            }


        },

        syncProductsFromVend: function (page, since) {

            var syncingTime = Object.create(LS_Syncing_Time);
            syncingTime.set_start_time();
            //check if page is undefined then we set it to one
            if (typeof page == 'undefined') {
                page = 1;
            } else if (page <= 0) {
                //Make sure we always start to page 1
                page = 1;
            }

            var action = 'vend_get_products';
            if (lsVendSyncModal.options.action != null) {
                action = lsVendSyncModal.options.action;
            }

            var data_to_request = {
                action: action,
                page: page
            };

            if (typeof since != 'undefined' || since != null) {
                data_to_request.since = since;
            }
            console.log('data_to_request => ');
            console.log(data_to_request);

            lsAjax.post(data_to_request).done(function (linksync_response) {

                lsVendSyncModal.$modalClose.hide();
                lsVendSyncModal.$progressBarLabel.html("Syncing products from Vend to WooCommerce.");
                console.log('Ajax Call Done of syncProductsFromVend :) Returned Data =>');
                console.log(linksync_response);
                syncingTime.set_end_time();
                lsVendSyncModal.syncing_times_in_seconds.push(syncingTime.get_execution_time_in_seconds());


                var product_count = linksync_response.products.length;
                if (product_count > 0) {

                    lsVendSyncModal.syncProductFromVend(linksync_response, 0);

                } else if (product_count <= 1) {
                    lsVendSyncModal.$progressBar.progressbar("value", 100);
                    lsVendSyncModal.syncCompleted();
                    if (lsVendSyncModal.options.no_products_to_import_woo == null) {
                        lsVendSyncModal.$progressBarLabel.html("No products were imported to WooCommerce");
                    } else {
                        lsVendSyncModal.$progressBarLabel.html(lsVendSyncModal.options.no_products_to_import_woo);
                    }
                }

            }).fail(function (data) {
                console.log('Failed AJAX Call of syncProductsFromVend :( Return Data: ' + data);
                //Failed then retry with the same page
                lsVendSyncModal.syncProductsFromVend(page, lsVendSyncModal.since);
            });
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

            lsVendSyncModal.cacheDom();
            lsVendSyncModal.$progressBar.progressbar({
                value: true,
                complete: function () {
                    lsVendSyncModal.$dasboardLink.removeClass('hide');
                    lsVendSyncModal.close();
                }
            });
            lsVendSyncModal.$progressBar.progressbar("value", 0);

        },

        syncCompleted: function (delay) {
            console.log(lsVendSyncModal.syncing_times_in_seconds);
            console.log(lsVendSyncModal.get_average_syncing_time_in_seconds());
            lsVendSyncModal.syncing_times_in_seconds = [];
            lsVendSyncModal.since = null;
            if (typeof delay == 'undefined') {
                delay = 4000;
            }
            setTimeout(function () {

                lsVendSyncModal.$tabMenu.before('<div class="notice notice-success  sync-completed" > <p>Sync Completed!</p> </div>');
                lsVendSyncModal.$mainContainer.find('.sync-completed').delay(delay).fadeOut('fast');

            }, delay);

        },

        close: function (delay) {
            if (typeof delay == 'undefined') {
                delay = 4000;
            }
            lsVendSyncModal.syncing_times_in_seconds = [];
            lsVendSyncModal.since = null;
            lsVendSyncModal.SYNC_LIMIT = 0;
            lsVendSyncModal.$syncModalContainer.delay(delay).fadeOut('fast', function () {
                lsVendSyncModal.$progressBarLabel.html("Sync Completed!");
                lsVendSyncModal.$modalBackDrop.removeClass('open').addClass('close');
                lsVendSyncModal.initializeSyncProgress();
                lsVendSyncModal.$modalMessage.show();
                lsVendSyncModal.$syncProgressContainer.hide();
                lsVendSyncModal.$popUpButtons.show();
                lsVendSyncModal.$modalCloseContainer.hide();
                lsVendSyncModal.$modalContent.fadeOut();
            });
        },

        open: function (option) {

            console.log(option);
            lsVendSyncModal.$estimatedTime.hide();
            lsVendSyncModal.syncing_times_in_seconds = [];
            lsVendSyncModal.$syncButtonsContainer.hide();
            lsVendSyncModal.$modalClose.show();
            lsVendSyncModal.$modalCloseContainer.show();
            var message = 'Your products from Vend will be imported to WooCommerce.<br/>Do you wish to continue?';
            if (null != option.htmlMessage) {
                message = option.htmlMessage;
            }
            lsVendSyncModal.SYNC_LIMIT = 0;
            lsVendSyncModal.$modalMessage.html(message);

            if ('woo_to_vend' == option.buttonGroup) {
                lsVendSyncModal.$syncToVendButtons.show();
            } else if ('woo_to_vend_via_filter' == option.buttonGroup) {
                lsVendSyncModal.$syncToWooViaFilterButtons.show();
            } else if ('vend_to_woo' == option.buttonGroup) {
                lsVendSyncModal.$syncToWooButtons.show();
            } else if ('vend_to_woo_since_last_sync' == option.buttonGroup) {
                lsVendSyncModal.$syncToWooButtonsSinceLastSync.show();
            } else if ('vend_to_woo_since_specified' == option.buttonGroup) {
                lsVendSyncModal.$syncToWooButtonsSinceSpecified.show();
            } else if ('two_way' == option.buttonGroup) {
                lsVendSyncModal.$syncTwoWayButtons.show();
            }

            lsVendSyncModal.$syncModalContainer.fadeIn();
            lsVendSyncModal.$modalContent.fadeIn();
        }
    };

})(jQuery);