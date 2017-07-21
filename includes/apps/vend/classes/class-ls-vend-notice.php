<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Notice
{

    public function vendNotice()
    {
        remove_all_actions('admin_notices');
        $current_screen = get_current_screen();

        if ('shop_order' == $current_screen->id) {
            if (isset($_GET['post'])) {

                if (isset($_GET['ls_dev_log'])) {
                    if ('woo_to_vend' == $_GET['ls_dev_log']) {
                        $this->showWooToVendDevOrderNotice($_GET['post']);
                    } else if ('vend_to_woo' == $_GET['ls_dev_log']) {
                        $this->showVendToWooDevOrderNotice($_GET['post']);
                    }

                }

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

            }
        }

        /**
         * Do not show getting started video notice
         */
        //$this->linksync_video_message();
        $this->linksync_update_plugin_notice();

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
        global $linksync_vend_laid;
        $running_version = Linksync_Vend::$version;

        if (!empty($linksync_vend_laid)) {

            $laid_info = LS_Vend()->laid()->get_laid_info($linksync_vend_laid);
            if (!empty($laid_info)) {
                LS_Vend()->laid()->update_current_laid_info($laid_info);
            }

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
        if (get_option('linksync_wooVersion') == 'off') {
            if (isset($_POST['hide'])) {
                update_option('hide_this_notice', 'off');
            }
            if (get_option('hide_this_notice') == 'on') {
                ?>
                <div class="updated">
                    <p>
                        <?php
                        echo '<form method="POST">
                                <input 
                                    style="float:right;cursor:pointer" 
                                    type="submit" 
                                    class="add-new-h2"   
                                    name="hide" value="Hide this notice">
                               </form>
                               Watch the 3-minute "getting started guide" for linksync for WooCommerce.<br><br>
                               <a href="//fast.wistia.net/embed/iframe/mfwv2hb8wx?popover=true" 
                               class="wistia-popover[height=576,playerColor=5aaddd,width=1024]">
                                    <img src="https://embed-ssl.wistia.com/deliveries/92d5bedfb2638333806b598616d315640b701a95.jpg?image_play_button=true&image_play_button_color=5aaddde0&image_crop_resized=200x113" alt="" />
                               </a>
                               
                                <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/popover-v1.js"></script>';
                        ?>
                    </p>
                    <style>
                        .add-new-h2:hover {
                            background: #2ea2cc;
                            color: #fff;
                        }

                        .add-new-h2 {
                            margin-left: 4px;
                            padding: 4px 8px;
                            position: relative;
                            top: -3px;
                            color: #0074a2;
                            text-decoration: none;
                            border: none;
                            -webkit-border-radius: 2px;
                            border-radius: 2px;
                            background: #e0e0e0;
                            text-shadow: none;
                            font-weight: 600;
                            font-size: 13px;
                        }
                    </style>

                </div>

                <?php
            }
        }
    }

}

