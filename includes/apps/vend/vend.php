<?php if (!defined('ABSPATH')) exit('Access is Denied');

if (!class_exists('LS_Vend')) {

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
        private static $laid = null;

        public static $slug = 'linksync-vend';

        /**
         * Cloning is forbidden.
         */
        public function __clone()
        {
            wp_die('Cheatin&#8217; huh?', 'woocommerce');
        }

        /**
         * Unserializing instances of this class is forbidden.
         */
        public function __wakeup()
        {
            wp_die('Cheatin&#8217; huh?', 'woocommerce');
        }

        public function __construct()
        {
            $this->includes();
            do_action('ls_vend_loaded');
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
            if (is_null(self::$api)) {
                self::$api = new LS_Vend_Api(LS_Vend()->laid()->get_api());
            }

            return self::$api;
        }

        public function laid()
        {
            if (is_null(self::$laid)) {
                self::$laid = new LS_Vend_Laid();
            }

            return self::$laid;
        }

        public function initialize_data()
        {
            global $linksync_vend_laid;

            $laidData = null;
            if (!empty($linksync_vend_laid)) {
                LS_Vend_Config::maybe_save_vend_config();
                $laidData = LS_Vend()->laid()->check_api_key($linksync_vend_laid);
            }
            $GLOBALS['laidData'] = $laidData;
        }

        /**
         * This method is intended to run linksync vend plugin after including files
         * to avoid code execution on including php files
         */
        public function run()
        {

            $linksync_vend_laid = LS_Vend()->laid()->get_current_laid();
            $GLOBALS['linksync_vend_laid'] = $linksync_vend_laid;

            LS_Vend_Hook::init();

            do_action('ls_vend_init');
        }


        /**
         * Vend Includes
         */
        public function includes()
        {
            include_once LS_INC_DIR . 'apps/ls-core-functions.php';
            include_once LS_INC_DIR . 'apps/class-ls-woo-tax.php';

            include_once LS_INC_DIR . 'apps/vend/helpers/class-ls-vend-tax-helper.php';
            include_once LS_INC_DIR . 'apps/class-ls-woo-order-line-item.php';
            include_once LS_INC_DIR . 'apps/class-ls-product-meta.php';
            include_once LS_INC_DIR . 'apps/class-ls-order-meta.php';


            include_once LS_INC_DIR . 'api/ls-api.php';
            include_once LS_INC_DIR . 'apps/class-ls-product-api.php';
            include_once LS_INC_DIR . 'apps/class-ls-order-api.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-api.php';


            include_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-option.php';
            include_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-order-option.php';
            include_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-product-option.php';

            include_once LS_INC_DIR . 'apps/classes/class-ls-address.php';
            include_once LS_INC_DIR . 'apps/classes/class-ls-order.php';
            include_once LS_INC_DIR . 'apps/classes/class-ls-product.php';
            include_once LS_INC_DIR . 'apps/classes/class-ls-product-variant.php';
            include_once LS_INC_DIR . 'apps/classes/class-ls-woo-product.php';
            include_once LS_INC_DIR . 'apps/classes/class-ls-modal.php';
            include_once LS_INC_DIR . 'apps/class-ls-simple-product.php';
            include_once LS_INC_DIR . 'apps/class-ls-simple-product.php';
            include_once LS_INC_DIR . 'apps/class-ls-variant-product.php';

            include_once LS_INC_DIR . 'apps/class-ls-json-product-factory.php';
            include_once LS_INC_DIR . 'apps/class-ls-json-order-factory.php';

            require_once LS_INC_DIR . 'apps/vend/ls-vend-api-key.php';
            require_once LS_INC_DIR . 'apps/vend/ls-vend-log.php';
            require_once LS_INC_DIR . 'apps/vend/controllers/ls-log.php';

            include_once LS_INC_DIR . 'apps/helpers/class-ls-constant.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-image-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-user-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-support-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-message-builder.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-product-helper.php';
            include_once LS_INC_DIR . 'apps/helpers/class-ls-order-helper.php';
            include_once LS_INC_DIR . 'apps/vend/helpers/class-ls-vend-helper.php';
            include_once LS_INC_DIR . 'apps/vend/helpers/class-ls-vend-product-helper.php';
            include_once LS_INC_DIR . 'apps/vend/helpers/class-ls-vend-order-helper.php';
            include_once LS_INC_DIR . 'apps/vend/helpers/class-ls-vend-image-helper.php';

            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-menu.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-view.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-view-config-section.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-view-product-section.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-laid.php';

            include_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-notice.php';
            include_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-ajax.php';

            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                /**
                 * Initialize syncing class that is hooking to
                 *  save_post - to create product in vend
                 *  woocommerce_process_shop_order_meta -> to sync woocommerce order to vend
                 *  woocommerce_order_status_{woo_order_status} -> to sync woocommerce order to vend
                 *  before_delete_post - to delete vend product if delete option is enable
                 */
                include_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-sync.php';
            }


            include_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-install.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-wizard.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-scripts.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-hooks.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-config.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-url.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-order-custom-column.php';
            require_once LS_INC_DIR . 'apps/vend/classes/class-ls-vend-product-custom-column.php';

            require_once LS_INC_DIR . 'apps/vend/classes/list/class-ls-connected-order-list.php';
            require_once LS_INC_DIR . 'apps/vend/classes/list/class-ls-connected-product-list.php';
            require_once LS_INC_DIR . 'apps/vend/classes/list/class-ls-duplicate-sku-list.php';
            require_once LS_INC_DIR . 'apps/vend/classes/list/class-ls-vend-duplicate-sku-list.php';


        }

        /**
         * Show Vend views
         */
        public function view()
        {
            return new LS_Vend_View();
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
            $laid = LS_Vend()->laid()->get_current_laid();
            if (!empty($laid)) {
                $webHookData['laid_key'] = $laid;
                $webHookData['url'] = Linksync_Vend::getWebHookUrl();
                $webHookData['version'] = Linksync_Vend::$version;

                $orderSyncType = LS_Vend()->order_option()->sync_type();
                $orderImport = 'no';
                if ('vend_to_wc-way' == $orderSyncType) {
                    $orderImport = 'yes';
                }
                $webHookData['order_import'] = $orderImport;

                $prodyctSyncType = LS_Vend()->product_option()->sync_type();
                $productImport = 'no';
                if ('two_way' == $prodyctSyncType || 'vend_to_wc-way' == $prodyctSyncType || 'wc_to_vend' == $prodyctSyncType) {
                    $productImport = 'yes';
                }

                $webHookData['product_import'] = $productImport;
                $webHook = LS_Vend()->laid()->update_webhook_connection($webHookData);
                $pluginUrl = plugins_url();
                $webhookUrlCode = get_option('webhook_url_code');

                if (!empty($webHook['result']) && $webHook['result'] == 'success') {
                    LSC_Log::add('WebHookConnection', 'success', 'Connected to a file ' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode, $laid);
                    update_option('linksync_addedfile', '<a href="' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode . '">' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode . '</a>');

                } else {
                    LSC_Log::add('WebHookConnection', 'fail', 'Order-Config File: Connected to a file ' . $pluginUrl . '/linksync/update.php?c=' . $webhookUrlCode, $laid);
                }

                return $webHook;
            }

            return null;

        }


        /**
         * @return null|string The hook name to use on triggering order syncing to vend
         */
        public function orderSyncToVendHookName()
        {
            $orderHookName = get_option('order_status_wc_to_vend');
            if (!empty($orderHookName)) {
                $name = substr($orderHookName, 3);
                $orderHook = 'woocommerce_order_status_' . $name;

                return $orderHook;
            }
            return null;
        }

        public function getSelectedOrderStatusToTriggerWooToVendSync()
        {
            $orderHookName = get_option('order_status_wc_to_vend');
            if (!empty($orderHookName)) {
                if (LS_Helper::isWooVersionLessThan_2_4_15()) {
                    return $orderHookName;
                }

                $wc_prefix = substr($orderHookName, 0, 3);
                if ('wc-' == $wc_prefix) {
                    return str_replace($wc_prefix, '', $orderHookName);
                }

                return $orderHookName;
            }
            return null;
        }

        /**
         * Save users settins to linksync database
         * @return array
         */
        public function save_user_settings_to_linksync()
        {
            $product_options = LS_Vend()->product_option();
            $order_options = LS_Vend()->order_option();

            $vendApi = LS_Vend()->api();
            $productOptions = $product_options->get_syncing_options();
            $orderOptions = $order_options->get_syncing_options();


            $userSettings['product_settings'] = $productOptions;
            $userSettings['order_settings'] = $orderOptions;
            $userSettings = json_encode($userSettings);
            return $vendApi->save_users_settings($userSettings);
        }


    }

}

if (!function_exists('LS_Vend')) {

    function LS_Vend()
    {
        return LS_Vend::instance();
    }

}

