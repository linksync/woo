<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Ajax
{

    public static function init_hook()
    {
        add_action('wp_ajax_vend_get_products', array('LS_Vend_Ajax', 'get_products'));
        add_action('wp_ajax_vend_import_to_woo', array('LS_Vend_Ajax', 'import_product_to_woo'));

        add_action('wp_ajax_vend_woo_get_products', array('LS_Vend_Ajax', 'woo_get_products'));
        add_action('wp_ajax_vend_import_to_vend', array('LS_Vend_Ajax', 'import_woo_product_to_vend'));

        add_action('wp_ajax_vend_since_last_sync', array('LS_Vend_Ajax', 'get_products_since_last_update'));

        $wh_code = get_option('webhook_url_code');
        add_action('wp_ajax_vend_' . $wh_code, array('LS_Vend_Ajax', 'sync_triggered_by_lws'));
        add_action('wp_ajax_nopriv_vend_' . $wh_code, array('LS_Vend_Ajax', 'sync_triggered_by_lws'));
    }

    public static function get_products()
    {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $productUrlParams = LS_Vend_Sync::prepare_url_params_for_get_product($page);
        $products = LS_Vend()->api()->product()->get_product($productUrlParams);
        wp_send_json($products);
    }

    public static function import_product_to_woo()
    {
        if (!empty($_POST['product'])) {
            $deleted_product = (isset($_POST['deleted_product']) && is_numeric($_POST['deleted_product'])) ? $_POST['deleted_product']: 0;
            $total_product = (isset($_POST['product_total_count']) && is_numeric($_POST['product_total_count'])) ? $_POST['product_total_count']: 0;

            $product_total_count = (int)$total_product - (int)$deleted_product;

            $product = new LS_Product($_POST['product']);
            LS_Vend_Sync::importProductToWoo($product);

            $product_number = $_POST['product_number'];
            $product_number = ($product_number > $product_total_count) ? $product_total_count : $product_number;
            $msg = $product_number . " of " . $product_total_count . " Product(s)";

            $progressValue = round(($product_number / $product_total_count) * 100);

            $response = array(
                'msg' => $msg,
                'percentage' => $progressValue
            );
            wp_send_json($response);
        }
        wp_send_json(array('import_product_to_woo'));
    }

    public static function woo_get_products()
    {
        wp_send_json(LS_Product_Helper::get_simple_product_ids());
    }

    public static function import_woo_product_to_vend()
    {
        if (!empty($_POST['p_id'])) {
            LS_Vend_Sync::importProductToVend($_POST['p_id']);
            $product_number = isset($_POST['product_number']) ? $_POST['product_number'] : 0;
            $product_total_count = isset($_POST['total_count']) ? $_POST['total_count'] : 0;
            $product_number = ($product_number > $product_total_count) ? $product_total_count : $product_number;
            $progressValue = round(($product_number / $product_total_count) * 100);
            $msg = $product_number . " of " . $product_total_count . " Product(s)";

            $response = array(
                'msg' => $msg,
                'percentage' => $progressValue
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
            $laid_info = LS_Vend()->laid()->get_laid_info();
            $last_sync = $laid_info['time'];
            LS_Vend()->option()->lastProductUpdate($last_sync);
        }


        $get_product_args = LS_Vend_Sync::prepare_url_params_for_get_product($page, $last_sync);
        $products = LS_Vend()->api()->product()->get_product($get_product_args);

        wp_send_json($products);
    }

    /**
     * This method will be triggered by linksync server via update url
     */
    public static function sync_triggered_by_lws()
    {
        $last_product_sync = LS_Vend()->option()->lastProductUpdate();
        if(empty($last_product_sync)){
            set_time_limit(0);
            LS_Vend_Sync::all_product_to_woo();
        } else {
            set_time_limit(0);
            LS_Vend_Sync::all_product_to_woo_since_last_update();
        }

    }




}