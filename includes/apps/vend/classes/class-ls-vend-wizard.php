<?php

class LS_Vend_Wizard
{
    public static $slug = 'linksync-wizard';

    public static function wizard_menu()
    {
        $wizard_page = add_submenu_page(null, 'linksync wizard', 'linksync wizard', 'manage_options', self::$slug, array(__CLASS__, 'wizard'));
    }

    public static function wizard_process()
    {
        if (isset($_POST['process']) && $_POST['process'] == 'wizard') {
            if (isset($_POST['action'])) {
                self::processall();
            }
        }
    }

    public static function processall()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'apikey') {
            self::apikey();
        }

        if (isset($_POST['action']) && $_POST['action'] == 'product-sync') {
            self::product_syncing();
        }

        if (isset($_POST['action']) && $_POST['action'] == 'order-sync') {
            self::order_syncing();
        }
    }


    public static function apikey()
    {
        $app_name_status = 'Inactive';
        $status = 'Inactive';

        // Save and check valid laid api Key
        $apikey = $_POST['linksync']['api_key'];
        if (!empty($apikey)) {
            $res = LS_Vend()->laid()->check_api_key($apikey);
            if (!empty($res['error_message'])) {

                if (!empty($res['error_message']) && 'Connection to the update URL failed.' == $res['error_message']) {
                    update_option('linksync_error_message', 'Connection to the update URL failed. Please check our <a href="https://help.linksync.com/hc/en-us/articles/115000591510-Connection-to-the-update-URL-failed" target="_blank">FAQ</a> section to find possible solutions.');

                } else {
                    $message = 'Invalid or Expired Api key.';
                    $errorMessage = explode(',', $res['error_message']);

                    if (!empty($errorMessage[1])) {

                        if (true == LS_User_Helper::isFreeTrial(trim($errorMessage[2]))) {
                            $message = 'Oops, it looks like your free trial has expired. Click here to upgrade to a paid plan. ' . LS_User_Helper::update_button();
                        }

                    }

                    update_option('linksync_error_message', $message);
                }

                wp_redirect(LS_Vend_Menu::wizard_admin_url());
                exit();

            } else {

                $nextpage = isset($_POST['nextpage']) ? $_POST['nextpage'] : 0;
                if (is_numeric($nextpage) && $nextpage > 0) {
                    wp_redirect(LS_Vend_Menu::wizard_admin_url('step=' . $nextpage));
                    exit();
                }

            }


        } else {
            update_option('linksync_error_message', 'Please provide the valid API Key before proceeding to next step.');
            wp_redirect(LS_Vend_Menu::wizard_admin_url());
            exit();
        }
    }

    public static function product_syncing()
    {
        $synctype = $_POST['synctype'];
        $data = $_POST['linksync'];

        update_option('product_sync_type', $data['product_sync_type']);
        switch ($data['product_sync_type']) {
            case 'two_way':
                update_option('ps_name_title', (isset($data['product_two_way_name_title']) ? 'on' : ''));
                update_option('ps_description', (isset($data['product_two_way_description']) ? 'on' : ''));
                update_option('ps_desc_copy', (isset($data['product_two_way_short_description']) ? 'on' : ''));
                update_option('ps_price', (isset($data['product_two_way_price']) ? 'on' : ''));
                update_option('ps_quantity', (isset($data['product_two_way_quantity']) ? 'on' : ''));
                update_option('ps_tags', (isset($data['product_two_way_tags']) ? 'on' : ''));
                update_option('ps_categories', (isset($data['product_two_way_categories']) ? 'on' : ''));
                update_option('ps_pending', (isset($data['product_two_way_product_status']) ? 'on' : ''));
                update_option('ps_imp_by_tag', $data['product_two_way_product_import_tags']);
                update_option('ps_images', (isset($data['product_two_way_images']) ? 'on' : ''));
                update_option('ps_create_new', (isset($data['product_two_way_create_new']) ? 'on' : ''));
                update_option('ps_delete', (isset($data['product_two_way_delete']) ? 'on' : ''));
                break;
            case 'vend_to_wc-way':
                update_option('ps_name_title', (isset($data['product_vend_to_woo_name_title']) ? 'on' : ''));
                update_option('ps_description', (isset($data['product_vend_to_woo_description']) ? 'on' : ''));
                update_option('ps_desc_copy', (isset($data['product_vend_to_woo_short_description']) ? 'on' : ''));
                update_option('ps_price', (isset($data['product_vend_to_woo_price']) ? 'on' : ''));
                update_option('ps_quantity', (isset($data['product_vend_to_woo_quantity']) ? 'on' : ''));
                update_option('ps_attribute', (isset($data['product_vend_to_woo_attributes_values']) ? 'on' : ''));
                update_option('linksync_visiable_attr', (isset($data['product_vend_to_woo_attributes_visible']) ? 1 : ''));
                update_option('ps_tags', (isset($data['product_vend_to_woo_tags']) ? 'on' : ''));
                update_option('ps_categories', (isset($data['product_vend_to_woo_categories']) ? 'on' : ''));
                update_option('ps_pending', (isset($data['product_vend_to_woo_product_status']) ? 'on' : ''));
                update_option('ps_imp_by_tag', $data['product_vend_to_woo_product_import_tags']);
                update_option('ps_images', (isset($data['product_vend_to_woo_images']) ? 'on' : ''));
                update_option('ps_create_new', (isset($data['product_vend_to_woo_create_new']) ? 'on' : ''));
                update_option('ps_delete', (isset($data['product_vend_to_woo_delete']) ? 'on' : ''));
                break;
            case 'wc_to_vend':
                update_option('ps_name_title', (isset($data['product_woo_to_vend_name_title']) ? 'on' : ''));
                update_option('ps_description', (isset($data['product_woo_to_vend_description']) ? 'on' : ''));
                update_option('ps_price', (isset($data['product_woo_to_vend_price']) ? 'on' : ''));
                update_option('ps_quantity', (isset($data['product_woo_to_vend_quantity']) ? 'on' : ''));
                update_option('ps_tags', (isset($data['product_woo_to_vend_tags']) ? 'on' : ''));
                update_option('ps_delete', (isset($data['product_woo_to_vend_delete']) ? 'on' : ''));
                break;
        }


        $nextpage = isset($_POST['nextpage']) ? $_POST['nextpage'] : 0;
        if (is_numeric($nextpage) && $nextpage > 0) {
            wp_redirect(LS_Vend_Menu::wizard_admin_url('step=' . $nextpage));
            exit();
        }
    }

    public static function order_syncing()
    {
        $synctype = $_POST['synctype'];
        $data = $_POST['linksync'];

        update_option('order_sync_type', $data['order_sync_type']);
        switch ($data['order_sync_type']) {
            case 'vend_to_wc-way':
                update_option('vend_to_wc_customer', $data['order_vend_to_woo_import_customer']);
                update_option('order_vend_to_wc', $data['order_vend_to_woo_order_status']);
                break;
            case 'wc_to_vend':
                update_option('wc_to_vend_export', $data['order_woo_to_vend_export_customer']);
                update_option('order_status_wc_to_vend', $data['order_woo_to_vend_order_status']);
                break;
        }


        $nextpage = isset($_POST['nextpage']) ? $_POST['nextpage'] : 0;
        if ($nextpage == 0) {
            wp_redirect(LS_Vend_Menu::menu_url());
            exit();
        }
    }


    public static function wizard()
    {
        $apikey = LS_Vend()->laid()->get_current_laid();
        $response = false;
        if (!empty($apikey)) {
//            $response = LS_Vend()->laid()->check_api_key($apikey);
//            if (!empty($response['error_message'])) {
//
//                if (!empty($response['error_message']) && 'Connection to the update URL failed.' == $response['error_message']) {
//                    update_option('linksync_error_message', 'Connection to the update URL failed. Please check our <a href="https://help.linksync.com/hc/en-us/articles/115000591510-Connection-to-the-update-URL-failed" target="_blank">FAQ</a> section to find possible solutions.');
//                } else {
//                    $message = 'Invalid or Expired Api key.';
//                    $errorMessage = explode(',', $response['error_message']);
//
//                    if (!empty($errorMessage[1])) {
//
//                        if (true == LS_User_Helper::isFreeTrial(trim($errorMessage[2]))) {
//                            $message = 'Oops, it looks like your free trial has expired. Click here to upgrade to a paid plan. ' . LS_User_Helper::update_button();
//                        }
//
//                    }
//
//                    update_option('linksync_error_message', $message);
//                }
//
//            }
        }

        if (empty($apikey) && isset($_GET['step']) && $_GET['step'] > 1) {
            update_option('linksync_error_message', 'Please provide the valid API Key before proceeding to next step.');
            wp_redirect(LS_Vend_Menu::wizard_admin_url());
            exit();
        }

        // Display UI
        self::wizard_handler($response);
    }

    public static function wizard_handler($res)
    {
        ?>
        <p id="logo"><img src="<?php echo LS_PLUGIN_URL ?>assets/images/linksync/logo.png" alt=""/></p>
        <div class="wizard-content">
            <div class="content-wrap">
                <?php

                $step = isset($_GET['step']) ? $_GET['step'] : 1;

                switch ($step) {
                    case 1:
                        // Set up API Key
                        $laid = LS_Vend()->laid()->get_current_laid();
                        include_once(LS_PLUGIN_DIR . 'includes/view/wizard/setup-api.php');
                        break;

                    case 2:
                        // Set up Product syncing options
                        $selected_product_syncing_type = '';
                        $view_pcontent = '';
                        $product_option = LS_Vend()->product_option();
                        $product_syncing_type = $product_option->sync_type();
                        $name_or_title = $product_option->nameTitle();
                        $description = $product_option->description();
                        $short_description = $product_option->shortDescription();
                        $price = $product_option->price();
                        $quantity = $product_option->quantity();
                        $tag = $product_option->tag();
                        $category = $product_option->category();
                        $product_status = $product_option->productStatusToPending();
                        $image = $product_option->image();
                        $create_new = $product_option->createNew();
                        $delete = $product_option->delete();

                        $attribute = $product_option->attributes();
                        $attribute_visible_on_product_page = $product_option->attributeVisibleOnProductPage();


                        include_once(LS_PLUGIN_DIR . 'includes/view/wizard/product-syncing' . $view_pcontent . '.php');
                        break;

                    case 3:
                        // Set up Order syncing options
                        $view_ocontent = '';
                        $order_option = LS_Vend()->order_option();
                        $order_syncing_type = $order_option->sync_type();
                        $customer_export = $order_option->customerExport();
                        $customer_import = $order_option->customerImport();
                        $order_status_wootovend = $order_option->orderStatusWooToVend();
                        $order_status_vendtowoo = $order_option->orderStatusVendToWoo();


                        include_once(LS_PLUGIN_DIR . 'includes/view/wizard/order-syncing' . $view_ocontent . '.php');
                        break;
                }

                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Removes all admin notice during linksync vend wizard process
     */
    public static function remove_all_admin_notices_during_wizard_process()
    {
        remove_all_actions('admin_notices');
    }

}