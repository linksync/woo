(function ($) {

    var duplicateSkuList = {
        lsMainContainer: function () {
            return $('#ls-wrapper');
        },
        progressBar: function () {
            return $('#progressbar');
        },
        progressBarLabel: function () {
            return $(".progress-label");
        },
        modalBackDrop: function () {
            return $('.ls-modal-backdrop');
        },

        initializeModal: function () {

            duplicateSkuList.progressBar().show();
            duplicateSkuList.progressBarLabel().attr('style', '');
            duplicateSkuList.progressBar().progressbar({
                value: true,
                complete: function () {
                    // setTimeout(function () {
                    //
                    // }, 4000);


                }
            });
            duplicateSkuList.progressBar().progressbar("value", 0);
            duplicateSkuList.progressBarLabel().text('Checking duplicate products in Vend');
            duplicateSkuList.openModal();
        },
        makeVendSkuUnique: function (duplicateProductSkuList, product_number) {
            var $progressBar = duplicateSkuList.progressBar();
            var $progressBarLabel = duplicateSkuList.progressBarLabel();
            var $modalBackDrop = duplicateSkuList.modalBackDrop();

            var totalProductCount = duplicateProductSkuList.pagination.results;
            var currentResponsePage = duplicateProductSkuList.pagination.page;
            var currentResponsePages = duplicateProductSkuList.pagination.pages;


            if (typeof product_number == 'undefined') {
                product_number = 0;
            } else if (product_number <= 0) {
                //Make sure we always start to page first product
                product_number = 0;
            }

            product = duplicateProductSkuList.products[product_number];
            if (typeof product != 'undefined') {

                var product_count = product_number + 1;
                if (currentResponsePage > 1) {
                    product_count = product_count + (50 * (currentResponsePage - 1));
                }


                var p_data = {
                    action: 'vend_make_product_sku_unique',
                    page: currentResponsePage,
                    product_total_count: totalProductCount,
                    product: product,
                    product_count: product_count,
                    total_pages: currentResponsePages
                };

                lsAjax.post(p_data).done(function (p_res) {

                    console.log(p_res);
                    console.log("progress => " + p_res.percentage);

                    progressVal = $progressBar.progressbar("value");
                    if (progressVal < p_res.percentage) {
                        $progressBar.progressbar("value", p_res.percentage);
                        $progressBarLabel.html("Making " + p_res.product_number + " of " + p_res.product_total_count + " duplicate sku unique. (" + p_res.percentage + "%)");
                    }
                    if (p_res.product_number >= totalProductCount) {
                        //Making sku unique completed
                        duplicateSkuList.closeModal(function () {
                            $.post(ajaxurl, {action: 'vend_save_product_duplicates'}).done(function (response) {
                                window.location.reload();
                            });
                        });
                        console.log('Making sku unique completed');
                    } else {

                        var new_product_number = product_number + 1;
                        duplicateSkuList.makeVendSkuUnique(duplicateProductSkuList, new_product_number);
                    }

                });

            } else if (typeof product == 'undefined') {
                console.log('No product index page => ' + currentResponsePage + ' pages => ' + currentResponsePages);
                var page = currentResponsePage + 1;
                if (currentResponsePages >= page) {
                    duplicateSkuList.makeVendSkusUnique(page);
                }

            }

        },

        makeVendSkusUnique: function (page) {
            var $progressBar = duplicateSkuList.progressBar();
            var $progressBarLabel = duplicateSkuList.progressBarLabel();
            var $modalBackDrop = duplicateSkuList.modalBackDrop();


            if (typeof page == 'undefined') {
                page = 1;
            } else if (page <= 0) {
                //Make sure we always start to page 1
                page = 1;
            }
            var data = {
                action: 'vend_get_vend_duplicate_skus',
                page: page
            };
            lsAjax.post(data).done(function (duplicateProductSkuList) {
                console.log(duplicateProductSkuList.products);
                var product_count = duplicateProductSkuList.products.length;

                var currentResponsePage = duplicateProductSkuList.pagination.page;
                var currentResponsePages = duplicateProductSkuList.pagination.pages;

                if (product_count > 0) {

                    duplicateSkuList.makeVendSkuUnique(duplicateProductSkuList, 0);

                } else {
                    $progressBar.progressbar("value", 100);
                    $progressBarLabel.html("No duplicate product sku in Vend");
                }

                if (currentResponsePage <= currentResponsePages) {

                    page = parseInt(currentResponsePage) + 1;

                    if (page <= currentResponsePages) {
                        duplicateSkuList.makeVendSkusUnique(page);
                    }

                }

            });
        },

        openModal: function () {
            $('.ls-modal-message').show();
            $modalContent = duplicateSkuList.lsMainContainer().find('.ls-modal-content');
            $modalContent.show();
            duplicateSkuList.modalBackDrop().removeClass('close').addClass('open');
        },

        closeModal: function (callback) {
            setTimeout(function () {
                $('.ls-modal-message').hide();
                duplicateSkuList.progressBar().hide();
                duplicateSkuList.progressBarLabel().css({
                    'font-size': '15px',
                    'font-weight': 'bold',
                    'color': 'black',
                    'padding-bottom': '8px',
                });
                duplicateSkuList.progressBarLabel().html("Making Vend sku unique completed!");

                setTimeout(function () {

                    duplicateSkuList.modalBackDrop().removeClass('open').addClass('close');
                    $modalContent = duplicateSkuList.lsMainContainer().find('.ls-modal-content');
                    $modalContent.hide();

                    if (typeof callback === "function") {
                        callback();
                    }
                }, 2000);

            }, 2000);


        }
    };

    $(document).ready(function () {

        var all = $('*');
        var ls_wrapper = $('#ls-wrapper');

        ls_wrapper.on('submit', '#frm-duplicate-skus', function (e) {
            var activeElement = document.activeElement;
            var btnValue = $(activeElement).val();
            var btnName = $(activeElement).attr('name');


            if ('replaceallemptysku' == btnName) {
                //Empty sku ajax handler

                spinner = $('#ls-spinner');
                all.css({'cursor': 'wait'});
                spinner.show();
                $.post(ajaxurl, {action: 'vend_replace_all_empty_sku'}, function (data) {
                    window.location.href = window.location;
                });

            } else if ('makewooskuunique' == btnName) {
                //Duplicate sku ajax handler

                spinner = $('#ls-spinner2');
                all.css({'cursor': 'wait'});
                spinner.show();

                $.post(ajaxurl, {action: 'vend_make_woo_sku_unique'}, function (data) {
                    window.location.href = window.location;
                });


            } else if ('makevendskuunique' == btnName) {
                spinner = $('#ls-qbo-spinner');
                all.css({'cursor': 'wait'});
                spinner.show();
                duplicateSkuList.initializeModal();
                duplicateSkuList.makeVendSkusUnique();

            } else {


                if ('Apply' == btnValue) {

                    spinner = $('#ls-apply-spinner');


                    var searchIDs = $("input:checkbox:checked").map(function () {
                        return $(this).val();
                    }).get();

                    var action = $('#bulk-action-selector-top').val();

                    string_action = null;
                    if ('replace_empty_sku' == action) {
                        string_action = 'vend_replace_all_empty_sku';
                    } else if ('make_sku_unique' == action) {
                        string_action = 'vend_make_woo_sku_unique';
                    } else if ('delete_permanently' == action) {
                        string_action = 'vend_delete_products_permanently';
                    }

                    if(null != string_action && searchIDs.length > 0){

                        all.css({'cursor': 'wait'});
                        spinner.show();
                        data = {
                            action: string_action,
                            product_ids: searchIDs
                        };

                        console.log(searchIDs);

                        $.post(ajaxurl, data, function (data) {
                            console.log(data);
                            window.location.href = window.location;
                        });

                    }

                }

            }

            e.preventDefault();

        });
    });

}(jQuery));