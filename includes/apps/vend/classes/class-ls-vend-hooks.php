<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Hook
{

    /**
     * Initialize hooks that needs to be executed
     */
    public static function init()
    {
        add_action('admin_init', array('LS_Vend_Wizard', 'wizard_process'), 1);
        add_action( 'init', array( 'LS_Vend_Install', 'install' ), 5 );

        add_filter('contextual_help', array('LS_Support_Helper', 'vend_screen_help_tab'));
        add_action('admin_menu', array('LS_Vend_Helper', 'save_woocommerce_version'));


        add_action('wp_ajax_linksync_new_ticket_support', array('LS_Support_Helper', 'send_new_ticket'));
        add_action('wp_ajax_vend_send_capping_notice',  array('LS_Vend_Helper', 'send_capping_notice'));

        /**
         * Added needed Admin menu action hooks
         */
        $vend_menu = new LS_Vend_Menu();
        add_action('admin_menu', array($vend_menu, 'initialize_admin_menu'));
        add_action('admin_head', array($vend_menu, 'remove_first_sub_menu'));



        add_action('admin_notices', array('LS_User_Helper', 'setUpLaidInfoMessage'), 15);

        /**
         * Added Admin notice action hooks
         */
        $vend_notice = new LS_Vend_Notice();
        add_action('admin_notices', array($vend_notice, 'vendNotice'), 16);

        /**
         * Add Styles and Javascript files to wp-admin area
         */
        add_action('admin_enqueue_scripts', array('LS_Vend_Script', 'enqueue_scripts_and_styles'));


        $vend_view = new LS_Vend_View();
        add_filter('plugin_action_links_' . LS_PLUGIN_BASE_NAME, array($vend_view, 'plugin_action_links'));


        /**
         * Add product list row action to view product in vend
         */
        //add_filter('post_row_actions', array('LS_Vend_Product_Helper', 'row_action_links'), 10, 2);
        $productSyncingOption = LS_Vend()->product_option();
        $orderSyncingOption = LS_Vend()->order_option();

        if('disabled' != $orderSyncingOption->sync_type()){
            LS_Vend_Order_Custom_Column::init();
        }

        if('disabled_sync' != $productSyncingOption->sync_type()){
            LS_Vend_Product_Custom_Column::init();
        }

    }


}