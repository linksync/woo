(function ($) {

    var lsConfigurationPage = {
        init: function () {
            this.cacheDom();
            this.bindEvents();

        },

        cacheDom: function () {
            this.$mainContainer = $('#ls-main-wrapper');
            this.frmAddApikey = '#frmAddApiKey';
            this.frmUpdateApiKey = '#frmUpdateApiKey';
            this.frmResetSyncingSettings = '#frmResetSyncingSettings';
        },

        bindEvents: function () {

            this.on('submit', this.frmResetSyncingSettings, function (e) {
                var $frm = $(this);
                var $spinner = $frm.find('.spinner');
                var $btnSubmit = $frm.find('input[type="submit"]');
                var $tabMenu = lsConfigurationPage.$mainContainer.find('.ls-tab-menu');

                var data = {
                    action: 'vend_reset_syncing_settings',
                    reset: true
                };

                $btnSubmit.prop('disabled', true);
                $spinner.addClass('is-active');
                lsAjax.post(data).done(function (response) {
                    if ('' != response.message) {
                        $tabMenu.before('<div class="notice notice-success reset-syncing-settings" > <p>' + response.message + '</p> </div>');
                        lsConfigurationPage.$mainContainer.find('.reset-syncing-settings').delay(5000).fadeOut('fast');
                    }
                    $btnSubmit.prop('disabled', false);
                    $spinner.removeClass('is-active');
                });
                e.preventDefault();
            });

            this.on('submit', this.frmUpdateApiKey, function (e) {
                var $frm = $(this);
                var $spinner = $frm.find('.spinner');
                var $txtApiKey = $frm.find('input[name="apikey"]');
                var $btnSubmit = $frm.find('input[type="submit"]');
                var $tabMenu = lsConfigurationPage.$mainContainer.find('.ls-tab-menu');

                var data = {
                    action: 'vend_save_api_key',
                    post_data: $frm.serialize()
                };


                if ('' == $txtApiKey.val()) {
                    console.log("api key entered is empty");
                    alert('Empty API Key');
                } else {
                    $btnSubmit.prop('disabled', true);
                    $spinner.addClass('is-active');
                    lsAjax.post(data).done(function (response) {
                        console.log(response.message);
                        var msg = '', elementClass = 'notice-error';

                        if ('api_key_updated' == response.message) {
                            msg = 'API Key successfully updated.';
                            elementClass = 'notice-success';
                            $('.apikeyholder').html($txtApiKey.val());
                            console.log('api_key_updated');
                        } else if ('invalid' == response.message) {
                            msg = 'Update rejected. Invalid or expired API Key.';
                            console.log('invalid');
                        } else if ('empty_api_key' == response.message) {
                            msg = 'Empty API Key';
                            console.log('empty_api_key');
                        } else if ('invalid_apikey' == response.message) {
                            msg = 'Update rejected. Invalid or expired API Key.';
                            console.log('invalid_apikey');
                        }

                        $btnSubmit.prop('disabled', false);
                        $spinner.removeClass('is-active');
                        $tabMenu.before('<div class="notice ' + elementClass + '  is-dismissible api-key-update" > <p>' + msg + '</p> </div>');
                        lsConfigurationPage.$mainContainer.find('.api-key-update').delay(5000).fadeOut('fast');
                        $('.reveal-modal').trigger('reveal:close');
                    }).fail(function (e) {
                        console.log('failed saving api key')
                    });
                }


                e.preventDefault();
            });

            this.on('submit', this.frmAddApikey, function (e) {

                var $frm = $(this);
                var $spinner = $frm.find('.spinner');
                var $txtApiKey = $frm.find('input[name="apikey"]');
                var $btnSubmit = $frm.find('input[type="submit"]');
                var $tabMenu = lsConfigurationPage.$mainContainer.find('.ls-tab-menu');

                var data = {
                    action: 'vend_save_api_key',
                    post_data: $frm.serialize(),
                    add_api_key: true
                };

                if ('' == $txtApiKey.val()) {
                    console.log("api key entered is empty");
                    alert('Empty API Key');
                } else {
                    $btnSubmit.prop('disabled', true);
                    $spinner.addClass('is-active');
                    lsAjax.post(data).done(function (response) {
                        console.log(response.message);
                        var msg = '', elementClass = 'notice-error';
                        if ('api_key_added' == response.message) {
                            msg = 'API Key successfully added.';
                            elementClass = 'notice-success';
                            $('.apikeyholder').html($txtApiKey.val());
                        } else if ('invalid' == response.message) {
                            msg = 'Invalid or expired API Key.';
                        } else if ('empty_api_key' == response.message) {
                            msg = 'Empty API Key';
                        } else if ('invalid_apikey' == response.message) {
                            msg = 'Invalid or expired API Key.';
                        }

                        $btnSubmit.prop('disabled', false);
                        $spinner.removeClass('is-active');
                        $tabMenu.before('<div class="notice ' + elementClass + '  is-dismissible api-key-addition" > <p>' + msg + '</p> </div>');
                        lsConfigurationPage.$mainContainer.find('.api-key-addition').delay(5000).fadeOut('fast');
                        $('.reveal-modal').trigger('reveal:close');
                    }).fail(function (e) {
                        console.log('failed adding api key')
                    });
                }

                e.preventDefault();
            });
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
        }
    };

    $(document).ready(function () {

        lsVendSyncModal.init({
            first_message_label: "Getting products from Vend since last update.",
            no_products_to_import_woo: "No products were imported to WooCommerce since last update",
            action: 'vend_since_last_sync'
        });

        lsConfigurationPage.init();
    });

})(jQuery);