<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Menu
{

    public function remove_first_sub_menu()
    {
        global $submenu;

        if (isset($submenu[LS_Vend::$slug]) && !empty($submenu[LS_Vend::$slug])) {

            if (isset($submenu[LS_Vend::$slug][0])) {
                // Remove 'linksync Vend' sub menu item
                unset($submenu[LS_Vend::$slug][0]);
            }
        }

    }

    public static function get_active_tab_page()
    {
        $settings_tabs = array(
            'config',
            'product_config',
            'order_config',
            'logs',
            'support',
        );
        $active_tab = 'config';
        if (isset($_REQUEST['page'], $_REQUEST['subpage'])) {

            if (in_array($_REQUEST['subpage'], $settings_tabs)) {
                $active_tab = $_REQUEST['subpage'];
            }

        }

        return $active_tab;
    }

    public static function is_settings_linksync_page()
    {
        return self::is_linksync_page('settings');
    }

    public static function is_linksync_page($page_slug)
    {
        if (!empty($page_slug) && self::get_active_linksync_page() == $page_slug) {
            return true;
        }

        return false;
    }

    public static function is_page($page)
    {
        $activate_page = self::get_active_page();
        if ($activate_page == $page) {
            return true;
        }

        return false;
    }

    public static function get_active_linksync_page()
    {
        $active_page = '';
        if (isset($_REQUEST['linksync_page'])) {
            $active_page = $_REQUEST['linksync_page'];
        }

        return $active_page;
    }

    public static function get_active_page()
    {
        $active_page = LS_Vend::$slug;
        if (isset($_REQUEST['page'])) {
            $active_page = $_REQUEST['page'];
        }

        return $active_page;
    }

    public static function get_active_section()
    {
        if (isset($_REQUEST['section'])) {
            return $_REQUEST['section'];
        }

        return null;
    }

    public static function get_active_tab()
    {
        if (isset($_REQUEST['tab'])) {
            return $_REQUEST['tab'];
        }

        return null;
    }

    public static function get_id()
    {
        return 'toplevel_page_' . LS_Vend::$slug;
    }

    public static function menu_url()
    {
        return 'admin.php?page=' . LS_Vend::$slug;
    }

    public static function page_menu_url($page, $tab = null, $section = null)
    {
        $url = self::menu_url() . '&linksync_page=' . $page;
        if (null != $tab) {
            $url .= '&tab=' . $tab;

            if (null != $section) {
                $url .= '&section=' . $tab;
            }

        }

        return $url;
    }

    public static function settings_page_menu_url($tab = null, $section = null)
    {
        return self::page_menu_url('settings', $tab, $section);
    }

    public static function get_current_menu_url()
    {
        $page = self::get_active_linksync_page();
        $tab = self::get_active_tab();
        $section = self::get_active_section();

        return self::page_menu_url($page, $tab, $section);
    }

    public static function wizard_admin_url($additional_endpoint = null)
    {
        $url = 'admin.php?page=' . LS_Vend_Wizard::$slug;

        if (null != $additional_endpoint) {
            $url .= '&' . $additional_endpoint;
        }
        return self::admin_url($url);
    }

    public static function admin_url($url = null)
    {
        return admin_url($url);
    }

    public static function output_menu_tabs($active_tab = 'config')
    {
        ?>
        <h2 class="ls-tab-menu">

            <a href="<?php echo LS_Vend_Menu::settings_page_menu_url('config'); ?>"
               class="ls-nav-tab <?php echo ('config' == $active_tab) ? 'ls-nav-tab-active' : ''; ?>">
                Configuration
            </a>

            <a href="<?php echo LS_Vend_Menu::settings_page_menu_url('product_config'); ?>"
               class="ls-nav-tab <?php echo ('product_config' == $active_tab) ? 'ls-nav-tab-active ' : ''; ?> ">
                Product Syncing Setting
            </a>

            <a href="<?php echo LS_Vend_Menu::settings_page_menu_url('order_config') ?>"
               class="ls-nav-tab <?php echo ('order_config' == $active_tab) ? 'ls-nav-tab-active' : ''; ?> ">
                Order Syncing Setting
            </a>

            <a href="<?php echo LS_Vend_Menu::settings_page_menu_url('advance') ?>"
               class="ls-nav-tab <?php echo ('advance' == $active_tab) ? 'ls-nav-tab-active' : ''; ?> ">
                Advance
            </a>

            <a href="<?php echo LS_Vend_Menu::settings_page_menu_url('support') ?>"
               class="ls-nav-tab  <?php echo ('support' == $active_tab) ? 'ls-nav-tab-active' : ''; ?>">
                Support
            </a>

            <a href="<?php echo LS_Vend_Menu::settings_page_menu_url('logs') ?>"
               class="ls-nav-tab <?php echo ('logs' == $active_tab) ? 'ls-nav-tab-active' : ''; ?>">
                Logs
            </a>

        </h2>
        <?php
    }

    public function initialize_admin_menu()
    {

        $menu_slug = LS_Vend::$slug;
        $vendView = new LS_Vend_View();

        add_menu_page(
            __('linksync Vend', $menu_slug),
            __('linksync Vend', $menu_slug),
            'manage_options',
            $menu_slug,
            array($vendView, 'display'),
            LS_ASSETS_URL . 'images/linksync/logo-icon.png',
            '55.6'
        );

        add_submenu_page(
            $menu_slug,
            __('linksync Configuration', $menu_slug),
            __('Configuration', 'manage_options'),
            'manage_options',
            self::settings_page_menu_url('config'),
            null
        );

        add_submenu_page(
            $menu_slug,
            __('linksync Product Settings', $menu_slug),
            __('Product Settings', 'manage_options'),
            'manage_options',
            self::settings_page_menu_url('product_config'),
            null
        );

        add_submenu_page(
            $menu_slug,
            __('linksync Order Settings', $menu_slug),
            __('Order Settings', 'manage_options'),
            'manage_options',
            self::settings_page_menu_url('order_config'),
            null
        );

        add_submenu_page(
            $menu_slug,
            __('linksync Connected Products', $menu_slug),
            __('Synced Products', $menu_slug),
            'manage_options',
            self::page_menu_url('synced_products'),
            null
        );

        add_submenu_page(
            $menu_slug,
            __('linksync Connected Orders', $menu_slug),
            __('Synced Orders', $menu_slug),
            'manage_options',
            self::page_menu_url('synced_orders'),
            null
        );

//        if (!empty($in_woo_duplicate_skus) || !empty($in_woo_empty_product_skus) || !empty($in_vend_duplicate_and_empty_skus)) {
//            add_submenu_page(
//                $menu_slug,
//                __('linksync Duplicate SKU', $menu_slug),
//                __('Duplicate SKU', $menu_slug),
//                'manage_options',
//                self::page_menu_url('duplicate_sku'),
//                null
//            );
//        }

        add_submenu_page(
            $menu_slug,
            __('linksync Support', $menu_slug),
            __('Support', $menu_slug),
            'manage_options',
            self::settings_page_menu_url('support'),
            null
        );

        add_submenu_page(
            $menu_slug,
            __('linksync Logs', $menu_slug),
            __('Logs', $menu_slug),
            'manage_options',
            self::settings_page_menu_url('logs'),
            null
        );

        LS_Vend_Wizard::wizard_menu();

    }

    public function footer_scripts()
    {
        $linkSyncVendMenuId = LS_Vend_Menu::get_id();
        $currentPage = self::get_current_menu_url();
        $mainMenuSelector = '#' . $linkSyncVendMenuId . ' > a';
        $mainMenuHrefUrl = self::menu_url();
        $subMenuSelector = '#' . $linkSyncVendMenuId . ' > ul > li';
        $duplicateMenuUrl = self::page_menu_url('duplicate_sku');


        ?>
        <script>
            (function ($) {

                var currentPage = '<?php echo $currentPage; ?>';
                $(document).ready(function () {

                    $('<?php echo $mainMenuSelector; ?>').attr("href", "<?php echo $mainMenuHrefUrl; ?>")
                    $('<?php echo $subMenuSelector; ?>').removeClass('current');
                    $('<?php echo $subMenuSelector; ?> a').each(function () {
                        if ($(this).attr('href') == currentPage) {
                            $(this).parent().addClass('current');
                        }

                        var currentMenuName = $(this).text();
                        if('Synced Orders' == currentMenuName){
                            var $connectedOrderElement = $(this).parent();

                            $.post(ajaxurl, {action: 'vend_save_product_duplicates'}).done(function (response) {
                                var vend_product_duplicates = response.vend_product_duplicates;
                                var woo_duplicate_product_skus = response.woo_duplicate_skus;
                                var woo_empty_product_skus = response.woo_empty_product_skus;
                                if(
                                    vend_product_duplicates.length > 0 ||
                                    woo_duplicate_product_skus.length > 0 ||
                                    woo_empty_product_skus.length > 0
                                ){
                                    $connectedOrderElement.after('<li><a href="<?php echo $duplicateMenuUrl; ?>">Duplicate SKU</a></li>');
                                }

                            });
                        }


                    });

                });

            }(jQuery));
        </script>
        <?php
    }

}

