<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Ajax
{

    /**
     * Initialize needed ajax hooks for linksync vend plugin
     */
    public static function init_hook()
    {
        add_action('wp_ajax_vend_get_products', array('LS_Vend_Ajax', 'get_products'));
        add_action('wp_ajax_vend_import_to_woo', array('LS_Vend_Ajax', 'import_product_to_woo'));

        add_action('wp_ajax_vend_woo_get_products', array('LS_Vend_Ajax', 'woo_get_products'));
        add_action('wp_ajax_vend_woo_get_products_via_filter', array('LS_Vend_Ajax', 'woo_get_products_via_filter'));
        add_action('wp_ajax_vend_import_to_vend', array('LS_Vend_Ajax', 'import_woo_product_to_vend'));

        add_action('wp_ajax_vend_since_last_sync', array('LS_Vend_Ajax', 'get_products_since_last_update'));

        $wh_code = get_option('webhook_url_code');
        add_action('wp_ajax_ls_vend_' . $wh_code, array('LS_Vend_Ajax', 'sync_triggered_by_lws'));
        add_action('wp_ajax_nopriv_ls_vend_' . $wh_code, array('LS_Vend_Ajax', 'sync_triggered_by_lws'));

        add_action('wp_ajax_vend_load_configuration_tab', array('LS_Vend_Ajax', 'load_configuration_tab'));
        add_action('wp_ajax_vend_load_product_tab', array('LS_Vend_Ajax', 'load_product_tab'));
        add_action('wp_ajax_vend_load_order_tab', array('LS_Vend_Ajax', 'load_order_tab'));
        add_action('wp_ajax_vend_load_advance_tab', array('LS_Vend_Ajax', 'load_advance_tab'));
        add_action('wp_ajax_vend_load_support_tab', array('LS_Vend_Ajax', 'load_support_tab'));
        add_action('wp_ajax_vend_load_logs_tab', array('LS_Vend_Ajax', 'load_logs_tab'));

        add_action('wp_ajax_vend_load_connected_order', array('LS_Vend_Ajax', 'load_connected_order'));
        add_action('wp_ajax_vend_load_connected_product', array('LS_Vend_Ajax', 'load_connected_product'));
        add_action('wp_ajax_vend_load_duplicate_sku', array('LS_Vend_Ajax', 'load_duplicate_sku'));

        add_action('wp_ajax_vend_save_product_syncing_settings', array('LS_Vend_Product_Option', 'save_product_syncing_settings'));
        add_action('wp_ajax_vend_save_order_syncing_settings', array('LS_Vend_Order_Option', 'save_order_syncing_settings'));
        add_action('wp_ajax_vend_reset_syncing_settings', array('LS_Vend_Ajax', 'reset_syncing_options'));

        add_action('wp_ajax_vend_save_api_key', array('LS_Vend_Laid', 'save_api_key'));

        add_action('wp_ajax_vend_send_log_to_linksync', array('LS_Vend_Ajax', 'send_log_to_linksync'));
        add_action('wp_ajax_vend_clear_logs', array('LS_Vend_Ajax', 'clear_logs'));


        add_action('wp_ajax_vend_replace_all_empty_sku', array('LS_Vend_Ajax', 'replace_all_empty_sku_in_woocommerce'));
        add_action('wp_ajax_vend_make_woo_sku_unique', array('LS_Vend_Ajax', 'make_woo_sku_unique'));
        add_action('wp_ajax_vend_delete_products_permanently', array('LS_Vend_Ajax', 'delete_woo_products_permanently'));
        add_action('wp_ajax_vend_get_vend_duplicate_skus', array('LS_Vend_Ajax', 'get_vend_duplicate_skus'));

        add_action('wp_ajax_vend_make_product_sku_unique', array('LS_Vend_Ajax', 'make_vend_product_sku_unique'));

        add_action('wp_ajax_vend_connection_status_view', array('LS_Vend_Ajax', 'config_connection_status'));
        add_action('wp_ajax_vend_sync_now_view', array('LS_Vend_Ajax', 'config_sync_now_section'));

        add_action('wp_ajax_vend_save_product_duplicates', array('LS_Vend_Ajax', 'save_product_duplicates'));

        $laidClass = LS_Vend()->laid();
        add_action('wp_ajax_vend_check_api_key', array($laidClass, 'check_api_key'));

    }




    public static function save_product_duplicates()
    {
        global $linksync_vend_laid;

        $in_woo_duplicate_skus = LS_Woo_Product::get_woo_duplicate_sku();
        $in_woo_empty_product_skus = LS_Woo_Product::get_woo_empty_sku();

        if (!empty($linksync_vend_laid)) {
            $in_vend_duplicate_and_empty_skus = LS_Vend()->api()->product()->get_duplicate_products();
            LS_Vend()->option()->updateVendDuplicateProducts($in_vend_duplicate_and_empty_skus);
        }


        wp_send_json(array(
            'vend_product_duplicates' => isset($in_vend_duplicate_and_empty_skus['products']) ? $in_vend_duplicate_and_empty_skus['products'] : array(),
            'woo_empty_product_skus' => $in_woo_empty_product_skus,
            'woo_duplicate_skus' => $in_woo_duplicate_skus,
            'laid' => $linksync_vend_laid,
        ));
    }

    public static function config_connection_status()
    {
        LS_Vend_View_Config_Section::connection_status();
        die();
    }

    public static function config_sync_now_section()
    {
        LS_Vend_View_Config_Section::sync_now();
        die();
    }

    public static function replace_all_empty_sku_in_woocommerce()
    {
        if (isset($_POST['product_ids'])) {
            if (!empty($_POST['product_ids'])) {
                foreach ($_POST['product_ids'] as $product_id) {
                    if (is_numeric($product_id)) {
                        $product_meta = new LS_Product_Meta($product_id);
                        $sku = $product_meta->get_sku();
                        if (empty($sku)) {
                            $newSku = 'sku_' . $product_id;
                            $product_meta->update_sku($newSku);
                        }
                    }
                }
            }
        } else {

            $emptySkus = LS_Woo_Product::get_woo_empty_sku();
            foreach ($emptySkus as $product_ref) {
                $sku = $product_ref['meta_value'];
                if (empty($sku)) {
                    $product_meta = new LS_Product_Meta($product_ref['ID']);
                    $newSku = 'sku_' . $product_ref['ID'];
                    $product_meta->update_sku($newSku);
                }
            }

        }


        wp_send_json(array('message' => 'done'));
    }

    public static function make_woo_sku_unique()
    {
        if (isset($_POST['product_ids'])) {
            foreach ($_POST['product_ids'] as $product_id) {
                if (is_numeric($product_id)) {
                    $product_meta = new LS_Product_Meta($product_id);
                    $sku = $product_meta->get_sku();
                    $uniqueSku = 'sku_' . $product_id;
                    if ($sku != $uniqueSku && !empty($sku)) {
                        $newSku = $uniqueSku . $sku;
                        $product_meta->update_sku($newSku);
                    }
                }
            }
        } else {

            $duplicateSkus = LS_Woo_Product::get_woo_duplicate_sku();
            foreach ($duplicateSkus as $duplicateSku) {
                $sku = $duplicateSku['meta_value'];
                $productMeta = new LS_Product_Meta($duplicateSku['ID']);
                $uniqueSku = 'sku_' . $duplicateSku['ID'];
                if ($sku != $uniqueSku) {
                    $newSku = $uniqueSku . $sku;
                    $productMeta->update_sku($newSku);
                }

            }

        }


        wp_send_json(array('message' => 'done'));
    }

    public static function delete_woo_products_permanently()
    {

        if (!empty($_POST['product_ids'])) {

            foreach ($_POST['product_ids'] as $product_id) {
                if (is_numeric($product_id)) {
                    wp_delete_post($product_id, true);
                }
            }

        }
        wp_send_json(array('message' => 'done'));
    }

    public static function get_vend_duplicate_skus()
    {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $in_vend_duplicate_and_empty_skus = LS_Vend()->api()->product()->get_duplicate_products($page);
        LS_Vend()->option()->updateVendDuplicateProducts($in_vend_duplicate_and_empty_skus);
        wp_send_json($in_vend_duplicate_and_empty_skus);
    }

    public static function make_vend_product_sku_unique()
    {
        $product = new LS_Product($_POST['product']);
        $product->set_id(get_vend_id($product->get_id()));
        $sku = $product->get_sku();

        $product->set_sku($sku . '_' . time());
        $product->set_tax_id((empty($tax_id) ? null : $tax_id));
        $product->remove_deleted_at();
        $product->remove_update_at();
        $variants = $product->get_variants();
        if (empty($variants)) {
            $product->unset_data('variants');
        } else {
            foreach ($variants as $index => $variant) {

                $var_sku = $variants[$index]['sku'];
                $var_id = $variants[$index]['id'];
                $variants[$index]['sku'] = $var_sku . '_' . time();
                $variants[$index]['id'] = get_vend_id($var_id);
                error_log('index = >'. $index.' varsku => '.$var_sku);
            }

            $product->set_variants($variants);
        }
        $product->unset_data('category');
        $product->unset_data('subItem');
        $product->unset_data('parentId');
        $product->unset_data('level');

        error_log($product->get_product_json());
        $quickBooksProductUpdate = LS_Vend()->api()->product()->save_product($product->get_product_json());
        error_log(json_encode($quickBooksProductUpdate));

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $product_count = isset($_POST['product_count']) ? $_POST['product_count'] : 0;
        $product_total_count = isset($_POST['product_total_count']) ? $_POST['product_total_count'] : 0;
        $product_count = ($product_count > $product_total_count) ? $product_total_count : $product_count;
        $progressValue = round(($product_count / $product_total_count) * 100);
        $msg = $product_count . " of " . $product_total_count . " Product(s)";

        $response = array(
            'msg' => $msg,
            'percentage' => $progressValue,
            'product_number' => $product_count,
            'product_total_count' => $product_total_count,
            'product_update_response' => $quickBooksProductUpdate
        );
        wp_send_json($response);
    }

    public static function clear_logs()
    {
        $response = '';
        $error = '';
        if (isset($_POST['action']) && 'vend_clear_logs' == $_POST['action']) {
            $empty = LSC_Log::instance()->truncate_table();
            if ($empty) {
                $response = "Logs Clear successfully!";
            } else {
                $response = "Error:Unable to Clear Logs Details";
                $error = $response;
            }
        }
        wp_send_json(array(
            'message' => $response,
            'error' => $error,
            'action' => 'vend_clear_logs'
        ));
    }

    public static function send_log_to_linksync()
    {
        $response = '';
        $error = '';
        if (isset($_POST['action']) && 'vend_send_log_to_linksync' == $_POST['action']) {
            $fileName = LS_PLUGIN_DIR . '/classes/raw-log.txt';
            $data = file_get_contents($fileName);
            $encoded_data = base64_encode($data);
            $result = array(
                "attachment" => $encoded_data
            );
            $json = json_encode($result);
            $apicall_result = LS_Vend()->api()->sendLog($json);

            if (isset($apicall_result['result']) && $apicall_result['result'] == 'success') {
                $response = 'Logs Sent Successfully !';
            } else {
                $response = "Error:Unable to Send Logs Details";
                $error = $response;
            }
        }

        wp_send_json(array(
            'message' => $response,
            'error' => $error,
            'action' => 'send_log_to_linksync'
        ));
    }

    public static function reset_syncing_options()
    {
        $response = '';
        if (isset($_POST['reset'])) {
            LS_Vend()->option()->reset_options();
            LSC_Log::add('Reset Option', 'success', "Reset Product and Order Syncing Setting", '-');
            $response = 'Successfully! Reset Syncing Setting.';
        }
        wp_send_json(array(
            'message' => $response
        ));
    }

    public static function load_configuration_tab()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_configuration_tab();
        die();
    }

    public static function load_product_tab()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_product_configuration_tab();
        die();
    }

    public static function load_order_tab()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_order_configuration_tab();
        die();
    }

    public static function load_advance_tab()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_advance_tab();
        die();
    }

    public static function load_support_tab()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_support_tab();
        die();
    }

    public static function load_logs_tab()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_logs_tab();
        die();
    }

    public static function load_connected_order()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_connected_order_page();
        die();
    }

    public static function load_connected_product()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_connected_product_page();
        die();
    }

    public static function load_duplicate_sku()
    {
        LS_Vend()->initialize_data();
        LS_Vend()->view()->display_duplicate_sku_page();
        die();
    }

    public static function get_products()
    {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        if (!empty($_POST['since'])) {
            $productUrlParams = LS_Vend_Sync::prepare_url_params_for_get_product($page, $_POST['since']);
        } else {
            $productUrlParams = LS_Vend_Sync::prepare_url_params_for_get_product($page);
        }

        LSC_Log::add_dev_success('LS_Vend_Ajax::get_products', ' get_product_args => ' . $productUrlParams);
        $products = LS_Vend()->api()->product()->get_product($productUrlParams);
        wp_send_json($products);
    }

    public static function import_product_to_woo()
    {
        if (!empty($_POST['product'])) {

            $deleted_product = (isset($_POST['deleted_product']) && is_numeric($_POST['deleted_product'])) ? $_POST['deleted_product'] : 0;
            $total_product = (isset($_POST['product_total_count']) && is_numeric($_POST['product_total_count'])) ? $_POST['product_total_count'] : 0;

            $product_total_count = (int)$total_product;
            $product_results_count = (isset($_POST['product_result_count']) && is_numeric($_POST['product_result_count'])) ? $_POST['product_result_count'] : 0;

            $product = new LS_Product($_POST['product']);
            LS_Vend_Sync::importProductToWoo($product);

            $product_number = $_POST['product_number'];
            $product_number = ($product_number > $product_total_count) ? $product_total_count : $product_number;
            $msg = $product_number . " of " . $product_total_count . " Product(s)";

            $progressValue = round(($product_number / $product_total_count) * 100);

            $response = array(
                'msg' => $msg,
                'percentage' => $progressValue,
                'product_number' => $product_number,
                'product_results_count' => $product_results_count,
                'upgrade_button' => LS_User_Helper::update_button('upgrade now',''),
                'why_limit_link' => LS_User_Helper::why_limit_link(),
            );

            $trial_item_count = $_POST['trial_item_count'];
            $sync_limit_count = $_POST['sync_limit_count'];

            if('capping_did_not_exists' != $trial_item_count){

                if(
                    LS_Constants::TRIAL_PRODUCT_SYNC_LIMIT == $trial_item_count &&
                    LS_Constants::TRIAL_PRODUCT_SYNC_LIMIT == $product_number &&
                    LS_User_Helper::is_laid_on_free_trial()
                ){
                    $response['html_error_message'] = LS_User_Helper::save_syncing_error_limit();
                    //LS_Vend_Helper::send_capping_notice();
                }

                if($sync_limit_count == $trial_item_count){
                    $response['html_error_message'] = LS_User_Helper::save_syncing_error_limit();
                }

            }
            wp_send_json($response);
        }
        wp_send_json(array('import_product_to_woo'));
    }

    public static function woo_get_products()
    {
        wp_send_json(LS_Product_Helper::get_simple_product_ids());
    }

    public static function woo_get_products_via_filter()
    {
        wp_send_json(LS_Product_Helper::getProductViaFilter($_POST));
    }

    public static function import_woo_product_to_vend()
    {
        if (!empty($_POST['p_id'])) {
            $response_product_to_vend = LS_Vend_Sync::importProductToVend($_POST['p_id']);
            $product_number = isset($_POST['product_number']) ? $_POST['product_number'] : 0;
            $product_total_count = isset($_POST['total_count']) ? $_POST['total_count'] : 0;
            $product_number = ($product_number > $product_total_count) ? $product_total_count : $product_number;
            $progressValue = round(($product_number / $product_total_count) * 100);
            $msg = $product_number . " of " . $product_total_count . " Product(s)";

            $response = array(
                'msg' => $msg,
                'percentage' => $progressValue,
                'product_number' => $product_number,
                'response_product_to_vend' => $response_product_to_vend,
            );
            wp_send_json($response);
        }
    }

    public static function get_products_since_last_update()
    {
        set_time_limit(0);
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $last_sync = LS_Vend()->option()->lastProductUpdate();
        if (empty($last_sync)) {
            $last_sync = LS_Vend_Config::get_current_linksync_time();
            LS_Vend()->option()->lastProductUpdate($last_sync);
        }


        $get_product_args = LS_Vend_Sync::prepare_url_params_for_get_product($page, $last_sync);
        LSC_Log::add_dev_success('LS_Vend_Ajax::get_products_since_last_update', ' get_product_args => ' . $get_product_args);
        $products = LS_Vend()->api()->product()->get_product($get_product_args);

        wp_send_json($products);
    }

    /**
     * This method will be triggered by linksync server via update url
     */
    public static function sync_triggered_by_lws()
    {
        $last_product_sync = time();

        $response['success'] = 'Sync trigger success';
        if (!isset($_REQUEST['check'])) {

            $last_product_sync = LS_Vend()->option()->lastProductUpdate();
            $productSyncOption = LS_Vend()->product_option();
            if (
                'two_way' == $productSyncOption->sync_type() ||
                'vend_to_wc-way' == $productSyncOption->sync_type()
            ) {
                if (empty($last_product_sync)) {
                    $linksync_current_time = LS_Vend_Config::get_current_linksync_time();
                    if (!empty($linksync_current_time)) {
                        LS_Vend()->option()->lastProductUpdate($linksync_current_time);
                    }
                }

                set_time_limit(0);
                LS_Vend_Sync::all_product_to_woo_since_last_update();
            }

            require_once LS_PLUGIN_DIR . 'update.php';
            LS_Vend_Sync::from_vend_to_woo_quantity_update();
            LS_Vend_Sync::get_and_sync_product_to_woo_by_sku();

            $response['last_product_sync'] = $last_product_sync;
        }

        wp_send_json($response);
    }


}