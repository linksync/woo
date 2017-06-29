<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_View
{

    public function display()
    {
        global $currentScreenId, $linkSyncVendMenuId, $linksync_vend_laid;

        if ($linkSyncVendMenuId == $currentScreenId) {
            echo '<div class="wrap" id="ls-main-wrapper">';
            $this->display_loading_div();
            echo '</div>';
        }

    }

    public function display_connected_product_page()
    {
        $this->settings_header();
        $orderByName = '';
        $order = 'asc';
        if (isset($_REQUEST['orderby']) && 'name' == $_REQUEST['orderby']) {
            $orderByName = 'product_name';
        }

        if (isset($_REQUEST['order'])) {
            $order = $_REQUEST['order'];
        }

        $connectedProductsArray = LS_Vend_Product_Helper::get_vend_connected_products($orderByName, $order);
        $connectedProducts = new LS_Vend_Connected_Product_List($connectedProductsArray);
        $connectedProducts->prepare_items();

        ?>
        <div class="wrap" id="ls-wrapper">
            <div id="icon-users" class="icon32"><br/></div>
            <div class="ls-duplicate-sku-container">
                <form id="frm-duplicate-skus" method="get">
                    <?php $connectedProducts->display() ?>
                </form>
            </div>

        </div>
        <?php

    }

    public function display_connected_order_page()
    {
        $this->settings_header();
        $orderBy = '';
        $order = 'DESC';
        if (isset($_REQUEST['orderby']) && 'id' == $_REQUEST['orderby']) {
            $orderBy = 'wposts.ID';
        }

        if (isset($_REQUEST['order'])) {
            $order = $_REQUEST['order'];
        }

        $connectedOrdersArray = LS_Vend_Order_Helper::get_vend_connected_orders($orderBy, $order);
        $connectedOrders = new LS_Vend_Connected_Order_List($connectedOrdersArray);
        $connectedOrders->prepare_items();

        ?>
        <div class="wrap" id="ls-wrapper">
            <div id="icon-users" class="icon32"><br/></div>
            <div class="ls-duplicate-sku-container">
                <form id="frm-duplicate-skus" method="get">
                    <?php $connectedOrders->display() ?>
                </form>
            </div>

        </div>
        <?php
    }

    public function display_duplicate_sku_page()
    {
        global $duplicate_products, $empty_product_skus;

        $duplicateSkuList = new LS_Duplicate_Sku_List();
        $active_section = LS_Vend_Menu::get_active_section();

        if (empty($active_section) || 'in_woocommerce' == $active_section) {

            $duplicateSkuList = new LS_Duplicate_Sku_List(array(
                'duplicate_products' => $duplicate_products,
                'empty_product_skus' => $empty_product_skus,
            ));
        }

        if ('in_vend' == $active_section) {

        }

        //Fetch, prepare, sort, and filter our data...
        $duplicateSkuList->prepare_items();
        $mainDuplicateSkuListUrl = LS_Vend_Menu::admin_url(LS_Vend_Menu::page_menu_url('duplicate_sku'));
        if ('in_quickbooks_online' == $active_section) {
            ?>
            <style>
                #frm-duplicate-skus .bulkactions {
                    display: none !important;
                }
            </style>
            <?php
        }
        if (!empty($duplicate_and_empty_skus) || !empty($duplicate_products) || !empty($empty_product_skus)) {
            $linkToKnowledgeBase = '<a target="_blank" href="https://help.linksync.com/hc/en-us/articles/115000710830-What-if-I-have-duplicate-SKUs-in-either-or-both-systems-"> click here</a>.';
            LS_Message_Builder::notice("You have duplicate or empty skus. Please update your skus to make it unique. For more information " . $linkToKnowledgeBase);
        }

        ?>
        <div class="wrap" id="ls-wrapper">
            <div id="icon-users" class="icon32"><br/></div>
            <h2>Duplicate SKU List</h2>
            <ul class="subsubsub">
                <li><a href="<?php echo $mainDuplicateSkuListUrl . '&section=in_woocommerce'; ?>"
                       class="<?php echo (empty($active_section) || 'in_woocommerce' == $active_section) ? 'current' : ''; ?>">In
                        WooCommerce</a> |
                </li>
                <li>
                    <a href="<?php echo $mainDuplicateSkuListUrl . '&section=in_vend'; ?>"
                       class="<?php echo ('in_vend' == $active_section) ? 'current' : ''; ?>">In Vend</a> |
                </li>
            </ul>
            <br/><br/>
            <div class="ls-duplicate-sku-container">
                <form id="frm-duplicate-skus" method="get">
                    <?php $duplicateSkuList->display() ?>
                </form>
            </div>

        </div>
        <?php

    }

    public function display_tab_menu($active_tab = 'config')
    {
        $this->display_loading_div();
        $this->settings_header();
        $this->response_container();

        global $laidData;
        if (
            isset($laidData['errorCode']) &&
            isset($laidData['error_message']) &&
            'Connection to the update URL failed.' == $laidData['error_message']
        ) {
            LS_Message_Builder::notice('Connection to the update URL failed. Please check our <a href="https://help.linksync.com/hc/en-us/articles/115000591510-Connection-to-the-update-URL-failed" target="_blank">FAQ</a> section to find possible solutions.', 'error ');
        }


        $file_perms = wp_is_writable(plugin_dir_path(__FILE__));

        //Check if not writable
        if (!$file_perms) {
            LS_Message_Builder::notice("Alert: File permission on <b>wp-content</b> will prevent linksync from syncing and/or functioning corectly.<a href='https://www.linksync.com/help/woocommerce-perms'>Please click here for more information</a>.");
        }

        do_action('before_linksync_tab_menu');
        LS_Vend_Menu::output_menu_tabs($active_tab);
    }

    public function display_configuration_tab()
    {
        $this->display_tab_menu('config');
        include_once LS_INC_DIR . 'view/ls-plugins-tab-configuration.php';
    }

    public function display_logs_tab()
    {
        $this->display_tab_menu('logs');

        echo "<fieldset>
                <legend>Linksync Log</legend>
                <div style='float: left;margin-bottom: 10px;'>
                    <form method='POST' id='frmSendLogToLinksync'>
                        <input type='submit' class='button' title=' Use this button to upload your log file to linksync. You should only need to do this if requested by linksync support staff.' style='color:blue'  name='send_log' value='Send log to linksync'>
                        <span class='spinner'></span>
                    </form>
                </div>
                <div style='float: right;margin-bottom: 10px;'>
                    <form method='POST' id='frmClearLogs'>
                        <input type='submit' class='button' style='color:red' name='clearlog' value='Clear Logs'>
                        <span class='spinner'></span>
                    </form>
                </div>";

        if (!empty($_REQUEST['check']) && !empty($_REQUEST['logtype']) && 'all' == $_REQUEST['check']) {
            echo LSC_Log::printallLogs($_REQUEST['logtype']);
        } else {
            echo LSC_Log::getLogs();
            if (empty($_REQUEST['check'])) {
                echo "<a href='" . LS_Vend_Menu::settings_page_menu_url('logs') . "&check=all'><br>
                        <input type='button' class='button' style='color:#0074a2' name='allLogs' value='Show all'>
                      </a>";
            }

        }
        echo "</fieldset>";


    }

    public function display_product_configuration_tab()
    {
        $this->display_tab_menu('product_config');
        $laid_key = LS_Vend()->laid()->get_current_laid();

        if (!empty($laid_key)) {

            $ls_connected_to = LS_Vend()->option()->connected_to();
            $ls_connected_with = LS_Vend()->option()->connected_with();

            if ($ls_connected_with == 'Vend' || $ls_connected_to == 'Vend') {
                include_once LS_INC_DIR . 'view/vend/ls-plugins-tab-product-config.php';
            } else {
                LS_User_Helper::setUpLaidInfoMessage();
            }
        } else {

            $this->display_missing_apikey_message();

        }

    }

    public function display_order_configuration_tab()
    {
        $this->display_tab_menu('order_config');

        $laid_key = LS_Vend()->laid()->get_current_laid();

        if (!empty($laid_key)) {

            $ls_connected_to = LS_Vend()->option()->connected_to();
            $ls_connected_with = LS_Vend()->option()->connected_with();

            if ($ls_connected_with == 'Vend' || $ls_connected_to == 'Vend') {

                require_once LS_INC_DIR . 'view/vend/ls-plugins-tab-order-config.php';

            } else {
                LS_User_Helper::setUpLaidInfoMessage();
            }
        } else {

            $this->display_missing_apikey_message();

        }


    }

    public function display_support_tab()
    {
        $this->display_tab_menu('support');
        LS_Support_Helper::renderFormForSupportTab();
    }

    public function display_loading_div()
    {
        echo '<div class="ls-loading close"></div>';
    }

    public function settings_header()
    {
        ?><h2>Linksync (Version: <?php echo Linksync_Vend::$version; ?>)</h2><?php
    }

    public function response_container()
    {
        echo '<div id="response"></div>';
    }

    public function display_missing_apikey_message()
    {
        ?>
        <div class="error notice">
            <h3><?php echo LS_Constants::NOT_CONNECTED_MISSING_API_KEY; ?> </h3>
        </div>
        <?php
    }

    /**
     * Add Settings link in the plugin list view for this vend plugin
     *
     * @param $links
     * @return array
     */
    public function plugin_action_links($links)
    {
        $action_links = array(
            'settings' => '<a href="' . admin_url('admin.php?page=' . LS_Vend::$slug) . '" title="' . esc_attr(__('View Linksync Settings', LS_Vend::$slug)) . '">' . __('Settings', LS_Vend::$slug) . '</a>',
        );

        return array_merge($action_links, $links);
    }

    public function display_syncing_modal()
    {
        ?>
        <div class="ls-vend-sync-modal">
            <div class="ls-vend-sync-modal-content"
                 style="width: 500px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 34%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">

                <center>
                    <h4 id="sync_start_export_all" class="sync-modal-message">Do you want to sync all product to
                        Vend?</h4>
                </center>

                <div id="sync_progress_container" style="display: none;">

                    <center>
                        <br/>
                        <div id="syncing_loader">
                            <p style="font-weight: bold;">Please do not close or refresh the browser while syncing is in
                                progress.</p>
                        </div>
                    </center>
                    <center>
                        <div>
                            <div id="progressbar"></div>
                            <div class="progress-label">Loading...</div>
                        </div>
                        <?php
                        if (isset($_GET['page']) && LS_Vend::$slug != $_GET['page']) {
                            ?>
                            <p class="form-holder hide ls-dashboard-link">
                                <a href="<?php echo LS_Vend_Menu::menu_url(); ?>" class="a-href-like-button">Go To
                                    Dashboard</a>
                            </p>
                            <?php
                        }
                        ?>
                    </center>
                    <br/>

                </div>

                <div id="pop_button">

                    <div class="sync-buttons sync-to-vend-buttons">
                        <input type="button" name="sync_all_product_to_vend"
                               class="button hidesync product_sync_to_vend btn-yes" value="Yes">
                        <input type="button" class="button hidesync ls-modal-close btn-no ls-modal-close"
                               name="close_syncall" value='No'/>
                    </div>

                    <div class="sync-buttons sync-to-woo-buttons">
                        <input type="button" class="button product_sync_to_woo btn-yes" value="Yes">
                        <input type="button" class="button btn-no ls-modal-close" name="no" value='No'/>
                    </div>

                    <div class="sync-buttons sync-to-woo-buttons-since-last-update">
                        <input type="button" class="button product_sync_to_woo_since_last_sync btn-yes" value="Yes">
                        <input type="button" class="button btn-no ls-modal-close" name="no" value='No'/>
                    </div>
                </div>


            </div>

            <div class="ls-modal-backdrop close"></div>
        </div>
        <?php
    }

    public function display_add_api_key_modal()
    {
        ?>
        <div id="myModal" class="reveal-modal">
            <form method="POST" id="frmAddApiKey" name="f1" action="">
                <center><span>Enter the API Key</span></center>
                <hr>
                <br/>

                <center style="width: 411px;margin: 0 auto;">
                    <div>
                        <b style="color: #0074a2;">API Key*:</b>
                        <a href="https://www.linksync.com/help/woocommerce"
                           style="text-decoration: none"
                           target="_blank"
                           title=' Unsure about how to generate an API Key? Click the icon for a specific guidelines to get you up and running with linksync Vend & WooCommerce.'>
                            <img class="help_tip" src="../wp-content/plugins/linksync/assets/images/linksync/help.png"
                                 height="16" width="16">
                        </a>
                        <input type="text" size="30" name="apikey" value="">
                        <input type="submit" value="Save" class="button color-green" name="add_apiKey">
                        <span class="spinner"></span>
                    </div>
                </center>
                <span class="ui-icon ui-icon-close close-reveal-modal"></span>
            </form>
        </div>
        <?php
    }

    public function display_update_api_key_modal()
    {
        global $linksync_vend_laid;
        ?>
        <div id="modal_update_api" class="reveal-modal">
            <form method="POST" id="frmUpdateApiKey" name="f1" action="">
                <center><span>Update API Key</span></center>
                <hr>
                <br>
                <center style="width: 411px;margin: 0 auto;">
                    <div>
                        <b style="color: #0074a2;">API Key*:</b>
                        <a href="https://www.linksync.com/help/woocommerce"
                           style="text-decoration: none"
                           target="_blank"
                           title=' Unsure about how to generate an API Key? Click the icon for a specific guidelines to get you up and running with linksync Vend & WooCommerce.'>
                            <img class="help_tip" src="../wp-content/plugins/linksync/assets/images/linksync/help.png"
                                 height="16" width="16">
                        </a>
                        <input type="text" size="30" name="apikey"
                               value="<?php echo !empty($linksync_vend_laid) ? $linksync_vend_laid : ''; ?>">
                        <input type="submit" value="Update" class='button color-green' name="apikey_update">
                        <span class="spinner"></span>
                    </div>
                </center>
                <span class="ui-icon ui-icon-close close-reveal-modal"></span>
            </form>
        </div>
        <?php
    }
}