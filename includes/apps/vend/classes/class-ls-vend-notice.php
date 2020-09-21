<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Notice
{

    public function vendNotice()
    {
        global $current_screen;

        if ('shop_order' == $current_screen->id) {
            if (isset($_GET['post'])) {

                if (isset($_GET['ls_dev_log'])) {
                    if ('woo_to_vend' == $_GET['ls_dev_log']) {
                        $this->showWooToVendDevOrderNotice($_GET['post']);
                    } else if ('vend_to_woo' == $_GET['ls_dev_log']) {
                        $this->showVendToWooDevOrderNotice($_GET['post']);
                    }

                }

                $this->show_order_woo_to_vend_admin_error_notice($_GET['post']);

            }
        } else if ('product' == $current_screen->id) {
            if (isset($_GET['post'])) {

                if (isset($_GET['ls_dev_log'])) {
                    if ('woo_to_vend' == $_GET['ls_dev_log']) {
                        $this->showWooToVendDevProductNotice($_GET['post']);
                    } else if ('vend_to_woo' == $_GET['ls_dev_log']) {
                        $this->showVendToWooDevProductNotice($_GET['post']);
                    }

                }

                $this->show_product_woo_to_vend_admin_error_notice($_GET['post']);
            }
        }

        /**
         * Do not show getting started video notice
         */
        //$this->linksync_video_message();
        $this->linksync_update_plugin_notice();

    }

    public function show_order_woo_to_vend_admin_error_notice($order_id)
    {
      
    }


    public function show_product_woo_to_vend_admin_error_notice($product_id)
    {

    }

    public function showVendToWooDevProductNotice($product_id)
    {
        $productMeta = new LS_Product_Meta($product_id);
        echo '<div>';
        ls_print_r($productMeta->fromLinkSyncJson());
        echo '</div>';
    }

    public function showWooToVendDevProductNotice($product_id)
    {
        $productMeta = new LS_Product_Meta($product_id);
        echo '<div>';
        ls_print_r($productMeta->toLinkSyncJson());
        echo '</div>';
    }

    public function showVendToWooDevOrderNotice($orderId)
    {
        $orderMeta = new LS_Order_Meta($orderId);
        echo '<div>';
        ls_print_r($orderMeta->getOrderJsonFromVendToWoo());
        echo '</div>';
    }

    public function showWooToVendDevOrderNotice($orderId)
    {
        $orderMeta = new LS_Order_Meta($orderId);
        echo '<div>';
        ls_print_r($orderMeta->getOrderJsonFromWooToVend());
        echo '</div>';

    }


    /**
     * Add a notice to all admin that linksync and vend plugin has updates
     */
    public function linksync_update_plugin_notice()
    {
        global $linksync_vend_laid, $ls_vend_product_capping_error, $ls_vend_order_capping_error, $ls_vend_current_screen;
        $running_version = Linksync_Vend::$version;

        if (!empty($linksync_vend_laid)) {

            $laid_info = LS_Vend()->laid()->get_current_laid_info();

            if (!empty($laid_info) && !isset($laid_info['errorCode'])) {

                if ($laid_info['connected_app'] == '13') {
                    $linksync_version = $laid_info['connected_app_version'];
                } elseif ($laid_info['app'] == '13') {
                    $linksync_version = $laid_info['app_version'];
                } else {
                    $linksync_version = NULL;
                }

                update_option('linksync_version', $linksync_version);
                $linksync_version = get_option('linksync_version');
                if (version_compare($linksync_version, $running_version, '>')) {
                    LS_Message_Builder::info('linksync for WooCommerce <b>' . $linksync_version . '</b> is available! Please <a target="_blank" href="https://www.linksync.com/help/releases/vend-woocommerce">Update now.</a>', true);
                }
                update_option('laid_message', isset($laidinfo['message']) ? $laidinfo['message'] : null);
            }

        }

    }


    public function linksync_video_message()
    {

    }

}

