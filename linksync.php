<?php
/*
  Plugin Name: linksync for WooCommerce
  Plugin URI: http://www.linksync.com/integrate/woocommerce
  Description:  WooCommerce extension for syncing inventory and order data with other apps, including Xero, QuickBooks Online, Vend, Saasu and other WooCommerce sites.
  Author: linksync
  Author URI: http://www.linksync.com
  Version: 2.5.2
 */

if (!class_exists('Linksync_Vend')) {

    final class Linksync_Vend
    {

        /**
         * @var string
         */
        public static $version = '2.5.2';
        protected static $_instance = null;

        /**
         * Cloning is forbidden.
         */
        public function __clone()
        {
            wp_die('Cheatin&#8217; huh?');
        }

        /**
         * Unserializing instances of this class is forbidden.
         */
        public function __wakeup()
        {
            wp_die('Cheatin&#8217; huh?');
        }

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct()
        {
            $this->define_constants();
            $this->includes();
            $this->init();
        }

        /**
         * linksync Vend initialization
         */
        public function init()
        {
            /**
             * To handle all languages
             */
            mb_internal_encoding('UTF-8');
            mb_http_output('UTF-8');
            mb_http_input('UTF-8');
            mb_language('uni');
            mb_regex_encoding('UTF-8');

            /**
             * Initialize hooks
             */
            $this->init_hooks();
        }

        /**
         * Run linksync vend plugin
         */
        public function run()
        {
            LS_Vend()->run();
        }

        public function init_hooks()
        {
            add_action('plugins_loaded', array(__CLASS__, 'pluginUpdateChecker'), 0);
            register_activation_hook(__FILE__, array('LS_Vend_Install', 'plugin_activate'));
        }

        /**
         * Include Required files for Linksync
         */
        public function includes()
        {
            include_once(LS_PLUGIN_DIR . 'ls-functions.php');
            include_once(LS_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php');
            include_once(LS_PLUGIN_DIR . 'classes/Class.linksync.php');
            require_once(LS_INC_DIR . 'apps/vend/vend.php');
        }

        /**
         * Plugin directories and Url
         * Set Globals Linksync Constant
         */
        public function define_constants()
        {
            $pluginBaseName = plugin_basename(__FILE__);
            $this->define('LS_PLUGIN_BASE_NAME', $pluginBaseName);
            $this->define('LS_PLUGIN_DIR', plugin_dir_path(__FILE__));
            $this->define('LS_INC_DIR', LS_PLUGIN_DIR . 'includes/');
            $this->define('LS_PLUGIN_URL', plugin_dir_url(__FILE__));
            $this->define('LS_ASSETS_URL', LS_PLUGIN_URL . 'assets/');

        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define($name, $value)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /**
         * @return string Returns the web hook url of the plugin
         */
        public static function getWebHookUrl()
        {
            $webHookUrlCode = get_option('webhook_url_code');
            if (is_vend()) {
                //Used for Vend update url
                return admin_url('admin-ajax.php?action=ls_vend_' . $webHookUrlCode);
            }

            //Used for QuickBooks update url
            $url = admin_url('admin-ajax.php?action=' . $webHookUrlCode);
            return $url;
        }


        /**
         * linksync plugin updater
         */
        public static function pluginUpdateChecker()
        {
            if (is_admin()) {
                include_once(LS_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php');
                $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                    'https://github.com/linksync/woo',
                    __FILE__,
                    LS_Vend::$slug
                );
            }
        }

    }

}


Linksync_Vend::instance()->run();

