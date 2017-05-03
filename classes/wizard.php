<?php

class Wizard_Model
{

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

    public static function remove_all_admin_notices_during_wizard_process()
    {
        remove_all_actions('admin_notices');
    }

    public static function apikey()
    {
        $app_name_status = 'Inactive';
        $status = 'Inactive';

        // Save and check valid laid api Key
        $apikey = $_POST['linksync']['api_key'];
        if (!empty($apikey)) {
            $res = LS_ApiController::check_api_key($apikey);

            if (isset($res['connected_to'])) {
                $nextpage = isset($_POST['nextpage']) ? $_POST['nextpage'] : 0;
                if (is_numeric($nextpage) && $nextpage > 0) {
                    wp_redirect(admin_url('admin.php?page=linksync-wizard&step=' . $nextpage));
                    exit();
                }
            } else {
                update_option('linksync_error_message', $res['lws_laid_key_info']['userMessage']);
                wp_redirect(admin_url('admin.php?page=linksync-wizard'));
                exit();
            }
        } else {
            update_option('linksync_error_message', 'Please provide the valid API Key before proceeding to next step.');
            wp_redirect(admin_url('admin.php?page=linksync-wizard'));
            exit();
        }
    }

    public static function product_syncing()
    {
        $synctype = $_POST['synctype'];
        $data = $_POST['linksync'];
        if ($synctype == 'qbo') {
            update_option('ls_psqbo_sync_type', $data['product_sync_type']);
            switch ($data['product_sync_type']) {
                case 'two_way':
                    LS_QBO()->product_option()->update_match_product_with($data['match_product_with']);
                    LS_QBO()->product_option()->update_title_or_name(isset($data['product_two_way_name_title']) ? 'on' : '');
                    LS_QBO()->product_option()->update_description(isset($data['product_two_way_description']) ? 'on' : '');
                    LS_QBO()->product_option()->update_price(isset($data['product_two_way_price']) ? 'on' : '');
                    LS_QBO()->product_option()->update_quantity(isset($data['product_two_way_quantity']) ? 'on' : '');
                    LS_QBO()->product_option()->update_category(isset($data['product_two_way_categories']) ? 'on' : '');
                    LS_QBO()->product_option()->update_product_status(isset($data['product_two_way_product_status']) ? 'on' : '');
                    LS_QBO()->product_option()->update_create_new(isset($data['product_two_way_create_new']) ? 'on' : '');
                    LS_QBO()->product_option()->update_delete(isset($data['product_two_way_delete']) ? 'on' : '');
                    break;
                case 'qbo_to_woo':
                    LS_QBO()->product_option()->update_match_product_with($data['product_qbo_to_woo_match_product_with']);
                    LS_QBO()->product_option()->update_title_or_name(isset($data['product_qbo_to_woo_name_title']) ? 'on' : '');
                    LS_QBO()->product_option()->update_description(isset($data['product_qbo_to_woo_description']) ? 'on' : '');
                    LS_QBO()->product_option()->update_price(isset($data['product_qbo_to_woo_price']) ? 'on' : '');
                    LS_QBO()->product_option()->update_quantity(isset($data['product_qbo_to_woo_quantity']) ? 'on' : '');
                    LS_QBO()->product_option()->update_category(isset($data['product_qbo_to_woo_categories']) ? 'on' : '');
                    LS_QBO()->product_option()->update_product_status(isset($data['product_qbo_to_woo_product_status']) ? 'on' : '');
                    LS_QBO()->product_option()->update_create_new(isset($data['product_qbo_to_woo_create_new']) ? 'on' : '');
                    LS_QBO()->product_option()->update_delete(isset($data['product_qbo_to_woo_delete']) ? 'on' : '');
                    break;
            }
        } else {
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
        }

        $nextpage = isset($_POST['nextpage']) ? $_POST['nextpage'] : 0;
        if (is_numeric($nextpage) && $nextpage > 0) {
            wp_redirect(admin_url('admin.php?page=linksync-wizard&step=' . $nextpage));
            exit();
        }
    }

    public static function order_syncing()
    {
        $synctype = $_POST['synctype'];
        $data = $_POST['linksync'];
        if ($synctype == 'qbo') {
            update_option('ls_osqbo_sync_type', $data['order_sync_type']);
            switch ($data['order_sync_type']) {
                case 'woo_to_qbo':
                    LS_QBO()->order_option()->update_customer_export($data['order_woo_to_qbo_export_customer']);
                    LS_QBO()->order_option()->update_receipt_type($data['order_woo_to_qbo_post_as']);
                    LS_QBO()->order_option()->update_order_status($data['order_woo_to_qbo_order_status']);
                    LS_QBO()->order_option()->update_order_number($data['order_woo_to_qbo_order_number']);
                    break;
            }
        } else {
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
        }

        $nextpage = isset($_POST['nextpage']) ? $_POST['nextpage'] : 0;
        if ($nextpage == 0) {
            wp_redirect(admin_url('admin.php?page=linksync'));
            exit();
        }
    }

    public static function checkAppConnection($apikey, $save = 0)
    {
        $apicall = new linksync_class($apikey, 'off');
        $result = $apicall->testConnection();
        $response = array();
        $app_connection = false;
        if (isset($result) && !empty($result)) {
            if (isset($result['errorCode']) && !empty($result['userMessage'])) {
                update_option('linksync_error_message', $result['userMessage']);
                $response = array(
                    'success' => false
                );
            } else {
                if (isset($result['app']) && !empty($result['app'])) {
                    $app_name = self::appid_app($result['app']);
                    if (isset($app_name) && !empty($app_name['success'])) {
                        $app_name_status = 'Active';
                    }
                }

                if (isset($result['connected_app']) && !empty($result['connected_app'])) {
                    $connected_app = self::appid_app($result['connected_app']);
                    if (isset($connected_app) && !empty($connected_app['success'])) {
                        $status = 'Active';
                        $app_connection = $connected_app['success'];
                    }
                }

                if ($status == 'Active' && $app_name_status == 'Active') {
                    if ($save == 1) {
                        update_option('linksync_laid', $apikey);
                    }
                    $response = array(
                        'success' => true,
                        'app_connected' => $app_connection
                    );
                } else {
                    update_option('linksync_error_message', "The supplied API Key is not valid for use with linksync for WooCommerce.");
                    $response = array(
                        'success' => false
                    );
                }
            }
        } else {
            update_option('linksync_error_message', "The supplied API Key is not valid for use with linksync for WooCommerce.");
            $response = array(
                'success' => false
            );
        }

        return $response;
    }

    public static function appid_app($app_id)
    {
        $connected_app = array(
            '4' => 'Xero',
            '7' => 'MYOB RetailManager',
            '8' => 'Saasu',
            '13' => 'WooCommerce',
            '15' => 'QuickBooks Online',
            '18' => 'Vend'
        );
        if (array_key_exists($app_id, $connected_app)) {
            $result['success'] = $connected_app[$app_id];
        } else {
            $result['error'] = 'The supplied API Key is not valid for use with linksync for WooCommerce.';
        }
        return $result;
    }
}