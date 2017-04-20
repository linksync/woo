<?php if (!defined('ABSPATH')) exit('Access is Denied');

/**
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    /**
     * This file will serve as the entry point of vend app
     */
    final class LS_Vend
    {

        /**
         * @var LS_Vend instance
         */
        protected static $_instance = null;

        public static $api = null;

        public function __construct()
        {
            $this->includes();
            $this->load_hooks();

            do_action('ls_vend_loaded');
        }

        public function load_hooks()
        {

        }

        /**
         * LS_Vend get self instance
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function api()
        {
            if(is_null(self::$api)){
                self::$api = new LS_Vend_Api(LS_ApiController::get_api());
            }

            return self::$api;
        }


        /**
         * Vend Includes
         */
        public static function includes()
        {
            include_once LS_INC_DIR . 'apps/ls-core-functions.php';
            include_once LS_INC_DIR . 'apps/class-ls-woo-tax.php';

            include_once LS_INC_DIR . 'apps/vend/class-ls-vend-tax-helper.php';
            include_once LS_INC_DIR . 'apps/class-ls-woo-order-line-item.php';
            include_once LS_INC_DIR . 'apps/class-ls-product-meta.php';



            include_once LS_INC_DIR . 'api/ls-api.php';
            include_once LS_INC_DIR . 'api/ls-api-controller.php';
            include_once LS_INC_DIR . 'apps/class-ls-product-api.php';
            include_once LS_INC_DIR . 'apps/class-ls-order-api.php';
            require_once LS_INC_DIR . 'apps/vend/class-ls-vend-api.php';

            include_once LS_INC_DIR . 'apps/vend/class-ls-vend-option.php';
            include_once LS_INC_DIR . 'apps/vend/class-ls-vend-order-option.php';
            include_once LS_INC_DIR . 'apps/vend/class-ls-vend-product-option.php';

            include_once LS_INC_DIR . 'apps/class-ls-product-meta.php';
            include_once LS_INC_DIR . 'apps/class-ls-simple-product.php';
            include_once LS_INC_DIR . 'apps/class-ls-variant-product.php';

            include_once LS_INC_DIR . 'apps/class-ls-json-product-factory.php';
            include_once LS_INC_DIR . 'apps/class-ls-json-order-factory.php';

            require_once LS_INC_DIR . 'apps/vend/ls-vend-api-key.php';
            require_once LS_INC_DIR . 'apps/vend/ls-vend-log.php';
            require_once LS_INC_DIR . 'apps/vend/controllers/ls-log.php';

            include_once LS_INC_DIR . 'apps/helpers/class-ls-user-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-support-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-product-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-order-helper.php';
            include_once LS_INC_DIR . 'apps/vend/helpers/class-ls-vend-helper.php';
            include_once LS_INC_DIR . 'apps/vend/helpers/class-ls-vend-product-helper.php';
            if (is_vend()) {
                include_once LS_INC_DIR . 'apps/vend/class-ls-vend-sync.php';
            }

        }

        /**
         * Show Vend views
         */
        public function view()
        {
            if (isset($_GET['setting'], $_GET['page']) && $_GET['page'] == 'linksync') {

                if ($_GET['setting'] == 'logs') {

                    include_once LS_INC_DIR . 'view/ls-plugins-tab-logs.php';

                } elseif ($_GET['setting'] == 'product_config') {

                    include_once LS_INC_DIR . 'view/vend/ls-plugins-tab-product-config.php';

                } elseif ($_GET['setting'] == 'order_config') {

                    require_once LS_INC_DIR . 'view/vend/ls-plugins-tab-order-config.php';

                } elseif ($_GET['setting'] == 'support') {

                    LS_Support_Helper::renderFormForSupportTab();

                } else {
                    include_once LS_INC_DIR . 'view/ls-plugins-tab-configuration.php';
                }
            } else {
                include_once LS_INC_DIR . 'view/ls-plugins-tab-configuration.php';
            }

        }

        public function option()
        {
            return LS_Vend_Option::instance();
        }

        public function order_option()
        {
            return LS_Vend_Order_Option::instance();
        }

        public function product_option()
        {
            return LS_Vend_Product_Option::instance();
        }

        /**
         * Check if the plugin needs to import orders to woocommerce(yes).
         * @return string yes or no
         */
        public function should_import_order_to_woo()
        {
            $order_option = $this->order_option();
            $sync_types = $order_option->get_all_sync_type();

            if ($sync_types[1] == $order_option->sync_type()) {
                return 'yes';
            }
            return 'no';
        }

        public function updateWebhookConnection()
        {
            $laid = LS_ApiController::get_current_laid();

            $webHookData['url'] = linksync::getWebHookUrl();
            $webHookData['version'] = linksync::$version;

            $orderSyncType = LS_Vend()->order_option()->sync_type();
            $orderImport = 'no';
            if('vend_to_wc-way' == $orderSyncType){
                $orderImport = 'yes';
            }
            $webHookData['order_import'] = $orderImport;

            $prodyctSyncType = LS_Vend()->product_option()->sync_type();
            $productImport = 'no';
            if('two_way' == $prodyctSyncType || 'vend_to_wc-way' == $prodyctSyncType){
                $productImport = 'yes';
            }

            $webHookData['product_import'] = $productImport;


            $webHook = LS_ApiController::update_webhook_connection($webHookData);
            $pluginUrl = plugins_url();
            $webhookUrlCode = get_option('webhook_url_code');

            if(!empty($webHook['result']) && $webHook['result'] == 'success'){
                LSC_Log::add('WebHookConnection', 'success', 'Connected to a file ' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode, $laid);
                update_option('linksync_addedfile', '<a href="' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode . '">' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode . '</a>');

            } else {
                LSC_Log::add('WebHookConnection', 'fail', 'Order-Config File: Connected to a file ' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode, $laid);
            }
            return $webHook;
        }

        /**
         * @return null|string The hook name to use on triggering order syncing to vend
         */
        public function orderSyncToVendHookName()
        {
            $orderHookName = get_option('order_status_wc_to_vend');
            if(!empty($orderHookName)){
                $name = substr( $orderHookName, 3 );
                $orderHook = 'woocommerce_order_status_'.$name;

                return $orderHook;
            }
            return null;
        }

        public function getSelectedOrderStatusToTriggerWooToVendSync()
        {
            $orderHookName = get_option('order_status_wc_to_vend');
            if(!empty($orderHookName)){
                if (LS_Helper::isWooVersionLessThan_2_4_15()) {
                    return $orderHookName;
                }

                $wc_prefix = substr($orderHookName, 0, 3);
                if('wc-' == $wc_prefix){
                    return str_replace($wc_prefix, '', $orderHookName);
                }

                return $orderHookName;
            }
            return null;
        }

    }

    function LS_Vend()
    {
        return LS_Vend::instance();
    }

    // Global for backwards compatibility.
    $GLOBALS['ls_vend'] = LS_Vend();


}
