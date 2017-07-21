<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Script
{

    public static function enqueue_scripts_and_styles()
    {
        //Check for linksync plugin page before adding the styles and scripts to wp-admin
        $linkSyncVendMenuId = LS_Vend_Menu::get_id();
        $currentScreen = get_current_screen();
        $currentScreenId = $currentScreen->id;

        $isSettingsPage = LS_Vend_Menu::is_settings_linksync_page();
        $activeTab = LS_Vend_Menu::get_active_tab();
        $pluginVersion = Linksync_Vend::$version;

        ?>
        <script>
            var connected_products_url = '<?php echo admin_url(LS_Vend_Menu::page_menu_url('connected_products')); ?>';
            var connected_orders_url = '<?php echo admin_url(LS_Vend_Menu::page_menu_url('connected_orders')); ?>';
        </script>
        <?php
        if ($linkSyncVendMenuId == $currentScreenId) {
            $activePage = LS_Vend_Menu::get_active_linksync_page();

            wp_enqueue_style('ls-styles', LS_ASSETS_URL . 'css/style.css');

            //settings tab styles and scripts
            wp_enqueue_style('ls-settings-tab', LS_ASSETS_URL . 'css/admin-tabs/ls-plugins-setting.css');
            wp_enqueue_style('ls-reveal-style', LS_ASSETS_URL . 'css/admin-tabs/ls-reveal.css');
            wp_enqueue_script('ls-tiptip-plugin', LS_ASSETS_URL . 'js/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), $pluginVersion, true);
            wp_enqueue_script('ls-reveal-script', LS_ASSETS_URL . 'js/jquery-tiptip/jquery.reveal.js', array('jquery'), $pluginVersion, true);
            wp_enqueue_script('ls-jquery-ui-plugin', LS_ASSETS_URL . 'js/jquery-tiptip/jquery-ui.js', array('jquery'), $pluginVersion, true);
            wp_enqueue_script('ls-custom-scripts', LS_ASSETS_URL . 'js/ls-custom.js', array('jquery'), $pluginVersion, true);

            //ls-plugins-tab-configuration styles and scripts
            wp_enqueue_style('ls-jquery-ui', LS_ASSETS_URL . 'css/jquery-ui/jquery-ui.css');
            wp_enqueue_style('ls-tab-configuration-style', LS_ASSETS_URL . 'css/admin-tabs/ls-plugins-tab-configuration.css');

            self::javascript_page_loader($isSettingsPage, $activeTab);

            if ($isSettingsPage && 'product_config' == $activeTab) {

                wp_enqueue_script('ls-ajax-handler', LS_ASSETS_URL . 'js/vend/ls-ajax.js', array('jquery'), $pluginVersion, true);
                wp_enqueue_script('ls-vend-sync-modal', LS_ASSETS_URL . 'js/vend/ls-vend-sync-modal.js', array('jquery'), $pluginVersion, true);
                wp_enqueue_script('ls-product-syncing-settings', LS_ASSETS_URL . 'js/vend/ls-product-syncing-settings.js', array('jquery'), $pluginVersion, true);

                wp_enqueue_style('ls-jquery-ui-css', LS_ASSETS_URL . 'jquery-ui.css');

            } else {
                wp_enqueue_script('ls-ajax-handler', LS_ASSETS_URL . 'js/vend/ls-ajax.js', array('jquery'), $pluginVersion, true);
                wp_enqueue_script('ls-vend-sync-modal', LS_ASSETS_URL . 'js/vend/ls-vend-sync-modal.js', array('jquery'), $pluginVersion, true);
                wp_enqueue_script('ls-configuration', LS_ASSETS_URL . 'js/vend/ls-configuration.js', array('jquery'), $pluginVersion, true);

                wp_enqueue_style('ls-jquery-ui-css', LS_ASSETS_URL . 'jquery-ui.css');
            }

            LS_Support_Helper::supportScripts();


            if('duplicate_sku' == $activePage){
                wp_enqueue_script('ls-duplicate-list', LS_ASSETS_URL . 'js/vend/ls-duplicate-list.js', array('jquery'), $pluginVersion, true);
            }
        }

        if (isset($_GET['page']) && $_GET['page'] == LS_Vend_Wizard::$slug) {
            add_action('admin_head', array('LS_Vend_Wizard', 'remove_all_admin_notices_during_wizard_process'));
            wp_enqueue_style('admin-linksync-style', LS_ASSETS_URL . 'css/wizard/wizard-styles.css');
        }


        if ('shop_order' == $currentScreenId) {
            wp_enqueue_script('ls-shop-order-scripts', LS_ASSETS_URL . 'js/ls-shop-order.js', array('jquery'), $pluginVersion, true);
        }
    }

    public static function javascript_page_loader($isSettingsPage, $activeTab)
    {
        $jsToLoad = 'configuration';

        if ($isSettingsPage) {

            if ('product_config' == $activeTab) {
                $jsToLoad = 'product';
            } else if ('order_config' == $activeTab) {
                $jsToLoad = 'order';
            } else if ('support' == $activeTab) {
                $jsToLoad = 'support';
            } else if ('logs' == $activeTab) {
                $jsToLoad = 'logs';
            } else if('advance' == $activeTab){
                $jsToLoad = 'advance';
            }

        }

        $activatePage = LS_Vend_Menu::get_active_linksync_page();
        if($isSettingsPage || empty($activatePage)){
            wp_enqueue_script('ls-vend-' . $jsToLoad . '-load', LS_ASSETS_URL . 'js/vend/ls-vend-settings-' . $jsToLoad . '-load.js', array('jquery'), Linksync_Vend::$version, true);
        }

    }

}