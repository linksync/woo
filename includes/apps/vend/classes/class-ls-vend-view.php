<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_View
{

    public function display()
    {
        global $currentScreenId, $linkSyncVendMenuId;

        if ($linkSyncVendMenuId == $currentScreenId) {
            echo '<div class="wrap" id="ls-main-wrapper">';
            echo '<div id="response"></div>';

            if (LS_Vend_Menu::is_settings_linksync_page()) {

                $tab = LS_Vend_Menu::get_active_tab();

                if (empty($tab)) {

                    $this->display_configuration_tab();

                } else if ('logs' == $tab) {

                    $this->display_logs_tab();

                } else if ('product_config' == $tab) {

                    $this->display_product_configuration_tab();

                } else if ('order_config' == $tab) {

                    $this->display_order_configuration_tab();

                } else if ('support' == $tab) {

                    $this->display_support_tab();

                } else {
                    $this->display_configuration_tab();

                }

            } else {

                if (LS_Vend_Menu::is_linksync_page('connected_products')) {
                    $this->display_connected_product_page();

                } else if (LS_Vend_Menu::is_linksync_page('connected_orders')) {
                    
                    $this->display_connected_order_page();
                    
                } else if(LS_Vend_Menu::is_linksync_page('duplicate_sku')){
                    
                    $this->display_duplicate_sku_page();
                    
                }else {
                    $this->display_configuration_tab();
                }
            }

            echo '</div>';


        }
    }

    public function display_connected_product_page()
    {
        $this->settings_header();
        $orderByName = '';
        $order = 'asc';
        if(isset($_REQUEST['orderby']) && 'name' == $_REQUEST['orderby']){
            $orderByName = 'product_name';
        }

        if(isset($_REQUEST['order'])){
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
        $orderBy= '';
        $order = 'DESC';
        if(isset($_REQUEST['orderby']) && 'id' == $_REQUEST['orderby']){
            $orderBy = 'wposts.ID';
        }

        if(isset($_REQUEST['order'])){
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
        global  $duplicate_products, $empty_product_skus;

        $duplicateSkuList = new LS_Duplicate_Sku_List();
        $active_section = LS_Vend_Menu::get_active_section();

        if(empty($active_section) || 'in_woocommerce' == $active_section){

            $duplicateSkuList = new LS_Duplicate_Sku_List(array(
                'duplicate_products' => $duplicate_products,
                'empty_product_skus' => $empty_product_skus,
            ));
        }

        if('in_vend' == $active_section){

        }

        //Fetch, prepare, sort, and filter our data...
        $duplicateSkuList->prepare_items();
        $mainDuplicateSkuListUrl = LS_Vend_Menu::admin_url(LS_Vend_Menu::page_menu_url('duplicate_sku'));
        if('in_quickbooks_online' == $active_section){
            ?>
            <style>
                #frm-duplicate-skus .bulkactions{
                    display: none !important;
                }
            </style>
            <?php
        }
        if(!empty($duplicate_and_empty_skus) || !empty($duplicate_products) || !empty($empty_product_skus)){
            $linkToKnowledgeBase = '<a target="_blank" href="https://help.linksync.com/hc/en-us/articles/115000710830-What-if-I-have-duplicate-SKUs-in-either-or-both-systems-"> click here</a>.';
            LS_Message_Builder::notice("You have duplicate or empty skus. Please update your skus to make it unique. For more information ".$linkToKnowledgeBase);
        }

        ?>
        <div class="wrap" id="ls-wrapper">
            <div id="icon-users" class="icon32"><br/></div>
            <h2>Duplicate SKU List</h2>
            <ul class="subsubsub">
                <li><a href="<?php echo $mainDuplicateSkuListUrl . '&section=in_woocommerce'; ?>"
                       class="<?php echo (empty($active_section) || 'in_woocommerce' == $active_section) ?  'current': ''; ?>">In WooCommerce</a> |
                </li>
                <li>
                    <a href="<?php echo $mainDuplicateSkuListUrl . '&section=in_vend'; ?>"
                       class="<?php echo ('in_vend' == $active_section) ?  'current': ''; ?>">In Vend</a> |
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

    public function display_tab_menu()
    {
        require LS_INC_DIR . 'view/ls-plugins-tab-menu.php';
    }

    public function display_configuration_tab()
    {
        $this->display_tab_menu();
        include_once LS_INC_DIR . 'view/ls-plugins-tab-configuration.php';
    }

    public function display_logs_tab()
    {
        $this->display_tab_menu();
        include_once LS_INC_DIR . 'view/ls-plugins-tab-logs.php';
    }

    public function display_product_configuration_tab()
    {
        $this->display_tab_menu();
        $this->display_loading_div();

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

    public function display_missing_apikey_message()
    {
        ?>
        <div class="error notice">
            <h3><?php echo LS_Constants::NOT_CONNECTED_MISSING_API_KEY; ?> </h3>
        </div>
        <?php
    }

    public function display_order_configuration_tab()
    {
        $this->display_tab_menu();
        $this->display_loading_div();


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
        $this->display_tab_menu();
        LS_Support_Helper::renderFormForSupportTab();
    }

    public function display_loading_div()
    {
        echo '<div class="se-pre-con"></div>';
    }

    public function settings_header()
    {
        ?><h2>Linksync (Version: <?php echo Linksync_Vend::$version; ?>)</h2><?php
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

}