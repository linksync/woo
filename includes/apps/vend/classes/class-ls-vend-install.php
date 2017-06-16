<?php

/*
 * All installation process
 */

class LS_Vend_Install
{

    public static function plugin_activate()
    {
        global $wpdb;


        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        LS_Vend()->option()->initialize_options();

        /**
         * WEBHOOK CONCEPT
         */
        $webhook_url_code = LS_Vend()->laid()->generate_code();
        add_option('webhook_url_code', $webhook_url_code);


        /**
         * Create table for logs
         */
        LSC_Log::instance()->create_table();

        /**
         * Create the table for api keys
         */
        LS_Vend_Api_Key::create_table();

        $wooCommerceCheck = self::check_woocommerce();
        if (!empty($wooCommerceCheck)) {
            deactivate_plugins(basename(__FILE__));
            wp_die($wooCommerceCheck, 'Plugin Activation Error', array('response' => 200, 'back_link' => TRUE));
            exit;
        }

        add_option('linksync_do_activation_redirect', LS_Vend_Wizard::$slug);
    }

    public static function install()
    {
        if (get_option('linksync_do_activation_redirect')) {
            delete_option('linksync_do_activation_redirect');
            wp_safe_redirect(LS_Vend_Menu::wizard_admin_url());
            exit();
        }
    }

    public static function check_woocommerce()
    {
        $error = '';
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

            $check = '2.2';
            $wooVercions = WC()->version;
            if (!empty($wooVercions)) {
                LS_Vend()->option()->save_woocommerce_version($wooVercions);
            }

            if (version_compare($wooVercions, $check, '<=')) {
                update_option('linksync_wooVersion', 'on');
                $error = 'WooCommerce ' . WC()->version . ' detected - linksync WooCommerce requires WooCommerce 2.2.x or higher. Please upgrade your version of WooCommerce to use this plugin.';
            } else {
                update_option('linksync_wooVersion', 'off');
            }

        } else {
            $error = 'linksync for WooCommerce requires WooCommerce Plugin. Please Activate Or <a target="_blank" href="http://wordpress.org/plugins/woocommerce/">Install it</a> first.';
        }

        return $error;

    }

}