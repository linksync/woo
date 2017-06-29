(function ($) {

    var lsVendLog = {
        load: function () {

            var $mainContainer = $('#ls-main-wrapper');

            var data = {
                action: 'vend_load_logs_tab',
                page: 'logs',
                logtype: getParameterByName('logtype'),
                check: getParameterByName('check')
            };
            var $loading = $mainContainer.find('.ls-loading');
            $loading.removeClass('close');
            lsAjax.post(data).done(function (html) {
                $loading.addClass('close');
                $mainContainer.html(html);
            });
        }
    };
    $(document).ready(function () {

        var $mainContainer = $('#ls-main-wrapper');

        lsVendLog.load();


        var $frmSendLogToLinksyncId = '#frmSendLogToLinksync';
        var $frmClearLogsId = '#frmClearLogs';

        $mainContainer.on('submit', $frmSendLogToLinksyncId, function (e) {
            var $frm = $(this);
            var $spinner = $frm.find('.spinner');
            var $btnSubmit = $frm.find('input[type="submit"]');
            var $tabMenu = $mainContainer.find('.ls-tab-menu');
            var data = {
                action: 'vend_send_log_to_linksync'
            };
            $spinner.addClass('is-active');
            $btnSubmit.prop('disabled', true);
            lsAjax.post(data).done(function (response) {
                console.log(response);
                if ('none' != response.error) {
                    $tabMenu.before('<div class="notice notice-success send-log" > <p>' + response.message + '</p> </div>');
                    $mainContainer.find('.send-log').delay(4000).fadeOut('fast');
                } else {
                    $tabMenu.before('<div class="notice notice-error reset-syncing-settings" > <p>' + response.message + '</p> </div>');
                    $mainContainer.find('.send-log').delay(4000).fadeOut('fast');
                }
                $btnSubmit.prop('disabled', false);
                $spinner.removeClass('is-active');
            });
            e.preventDefault();
        });

        $mainContainer.on('submit', $frmClearLogsId, function (e) {
            var $frm = $(this);
            var $spinner = $frm.find('.spinner');
            var $btnSubmit = $frm.find('input[type="submit"]');
            var $tabMenu = $mainContainer.find('.ls-tab-menu');

            var data = {
                action: 'vend_clear_logs'
            };
            $spinner.addClass('is-active');
            $btnSubmit.prop('disabled', true);
            lsAjax.post(data).done(function (response) {
                if ('' == response.error) {
                    $tabMenu.before('<div class="notice notice-success send-log" > <p>' + response.message + '</p> </div>');
                    $mainContainer.find('.send-log').delay(4000).fadeOut('fast');
                } else {
                    $tabMenu.before('<div class="notice notice-error reset-syncing-settings" > <p>' + response.message + '</p> </div>');
                    $mainContainer.find('.send-log').delay(4000).fadeOut('fast');
                }
                $btnSubmit.prop('disabled', false);
                $spinner.removeClass('is-active');
                window.location.reload();
            });

            e.preventDefault();
        });

    });

}(jQuery));