<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Option
{
    protected static $_instance = null;
    public $option_prefix = 'linksync_vend_';

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function update_vend_outlets($vendOutletsData)
    {
        return self::instance()->update_option('outlets_container', $vendOutletsData);
    }

    public function get_vend_outlets()
    {
        return self::instance()->get_option('outlets_container');
    }

    public function save_woocommerce_version($wooVersion)
    {
        return self::instance()->update_option('wooversion', $wooVersion);
    }

    public function get_woocommerce_version()
    {
        return self::instance()->get_option('wooversion', '');
    }

    /**
     * Get or update the option value of the connected application
     * @param null $value
     * @return bool|mixed Returns String Application Name that is connected to. Return true or false if update of the option happens
     */
    public function connected_to($value = null)
    {
        if (null != $value) {
            return update_option('linksync_connectedto', $value);
        } else if (null == $value) {
            return get_option('linksync_connectedto', '');
        }

    }

    /**
     * Get or update the option value of the connected application
     *
     * @param null $value
     * @return bool|mixed Returns String Application Name that is connected with. Returns true or false if update of the option happens
     */
    public function connected_with($value = null)
    {
        if (null != $value) {
            return update_option('linksync_connectionwith', $value);
        } else if (null == $value) {
            return get_option('linksync_connectionwith', '');
        }
    }

    public function tax_class()
    {
        return get_option('tax_class');
    }

    public function tax_inclusive()
    {
        return get_option('linksync_tax_inclusive');
    }

    public function update_tax_inclusive($meta_value)
    {
        return update_option('linksync_tax_inclusive', $meta_value);
    }

    public function linksync_woocommerce_tax_option()
    {
        return get_option('linksync_woocommerce_tax_option');
    }

    public function update_linksync_woocommerce_tax_option($meta_value)
    {
        return update_option('linksync_woocommerce_tax_option', $meta_value);
    }

    public function woocommerce_calc_taxes()
    {
        return get_option('woocommerce_calc_taxes');
    }

    public function woocommerce_prices_include_tax()
    {
        return get_option('woocommerce_prices_include_tax');
    }

    public function wooToVendOutlet()
    {
        return get_option('ps_wc_to_vend_outlet');
    }

    public function updateWooToVendOutlet($meta_value)
    {
        return update_option('ps_wc_to_vend_outlet', $meta_value);
    }

    public function wooToVendOutletDetail()
    {
        return get_option('wc_to_vend_outlet_detail');
    }

    public function updateWooToVendOutletDetail($meta_values)
    {
        return update_option('wc_to_vend_outlet_detail', $meta_values);
    }

    public function vendToWooOutlet()
    {
        return get_option('ps_outlet', 'on');
    }

    public function updateVendToWooOutlet($meta_value)
    {
        return update_option('ps_outlet', $meta_value);
    }

    public function vendToWooOutletDetail()
    {
        return get_option('ps_outlet_details', '');
    }

    public function updateVendToWooOutletDetail($meta_value)
    {
        return update_option('ps_outlet_details', $meta_value);
    }

    public function get_last_time_tested()
    {
        return get_option('linksync_last_test_time');
    }

    public function update_last_time_test($value)
    {
        update_option('linksync_last_test_time', $value);
    }

    public function linksync_current_api_url()
    {
        return get_option('linksync_current_api_url');
    }

    public function connection_status()
    {
        return get_option('linksync_status');
    }

    /**
     * Save and return last product update_at key from the product get response plus one second
     *
     * @param null $utc_date_time
     * @return bool|mixed|string
     */
    public function lastProductUpdate($utc_date_time = null)
    {
        return self::instance()->lastUpdate('product_last_update', $utc_date_time);
    }

    /**
     * Save and return last order update_at key from the order get response plus one second
     *
     * @param null $utc_date_time
     * @return bool|mixed|string
     */
    public function lasOrderUpdate($utc_date_time = null)
    {
        return self::instance()->lastUpdate('order_last_update', $utc_date_time);
    }

    /**
     * Save last update_at value to the database plus one second
     * @param $type
     * @param null $utc_date_time
     * @return bool|mixed|string
     */
    public function lastUpdate($type, $utc_date_time = null)
    {
        $types = array('product_last_update', 'order_last_update');
        if (!in_array($type, $types)) {
            return false;
        }

        $last_updated_at = self::instance()->get_option($type);
        if (empty($utc_date_time)) {
            return $last_updated_at;
        }

        $last_time = strtotime($last_updated_at);
        $time_arg = strtotime($utc_date_time);
        if ($last_time <= $time_arg) {
            $lt_plus_one_second = date("Y-m-d H:i:s", $time_arg + 1);
            self::instance()->update_option($type, $lt_plus_one_second);
            return $lt_plus_one_second;
        }

        return false;
    }

    public function update_time_offset($timeArgs)
    {
        if (!empty($timeArgs)) {
            $server_response = strtotime($timeArgs);
            $server_time = time();
            $time = $server_response - $server_time;
            update_option('linksync_time_offset', $time);
        }
    }

    public function updateVendDuplicateProducts($values)
    {
        self::instance()->update_option('duplicate_products', $values);
    }

    public function getVendDuplicateProducts()
    {
        return self::instance()->get_option('duplicate_products', '');
    }

    /**
     * Uses Wordpress update_option
     * @param $key
     * @param $value
     * @return bool
     */
    public function update_option($key, $value)
    {
        $key = self::instance()->option_prefix . $key;
        return update_option($key, $value);
    }

    /**
     * Uses Wordpress get_option
     * @param $key
     * @param string $default
     * @return mixed
     */
    public function get_option($key, $default = '')
    {
        $key = self::instance()->option_prefix . $key;
        return get_option($key, $default);
    }

    public function reset_options()
    {
        update_option('product_sync_type', 'disabled_sync'); # Two-way ,Vend to WooCommerce,WooCommerce to Vend,Disabled
        update_option('ps_name_title', 'on');
        update_option('ps_description', 'on');
        update_option('ps_desc_copy', '');
        update_option('ps_price', 'on');
        update_option('excluding_tax', 'on');
        update_option('tax_class', '');
        update_option('price_book', 'off');
        update_option('price_book_identifier', '');
        update_option('ps_categories', 'off');
        update_option('ps_quantity', 'on');
        update_option('ps_outlet', 'on');
        update_option('ps_unpublish', 'on');
        update_option('ps_brand', 'on');
        update_option('ps_tags', 'off');
        update_option('cat_radio', 'ps_cat_tags');
        update_option('ps_imp_by_tag', 'off');
        update_option('import_by_tags_list', '');
        update_option('ps_images', 'off');
        update_option('ps_import_image_radio', 'Enable');
        update_option('ps_create_new', 'on');
        update_option('ps_delete', '');
        update_option('prod_update_req', '');
        update_option('prod_update_suc', NULL);
        update_option('ps_outlet_details', '');
        update_option('ps_wc_to_vend_outlet', 'on');
        update_option('wc_to_vend_outlet_detail', '');
        update_option('ps_pending', '');
        update_option('price_field', 'regular_price');
        update_option('ps_attribute', 'on');
        update_option('linksync_woocommerce_tax_option', 'on');

        //Order sync Add options
        update_option('order_sync_type', 'disabled');
        update_option('order_time_req', null);
        update_option('order_time_suc', null);
        update_option('order_status_wc_to_vend', 'wc-processing');
        update_option('wc_to_vend_outlet', '');
        update_option('wc_to_vend_register', '');
        update_option('wc_to_vend_user', '');
        update_option('wc_to_vend_tax', '');
        update_option('wc_to_vend_payment', '');
        update_option('wc_to_vend_export', '');

        // Vend To WC
        update_option('order_vend_to_wc', 'wc-completed');
        update_option('vend_to_wc_tax', '');
        update_option('vend_to_wc_payments', '');
        update_option('vend_to_wc_customer', '');
        update_option('laid_message', null);
        update_option('prod_last_page', '');

        update_option('product_import', 'no');
        update_option('order_import', 'no');

        //order id
        update_option('linksync_sent_order_id', '');
        update_option('Vend_orderIDs', '');

        //product details
        update_option('product_detail', '');

        //order details
        update_option('order_detail', '');

        //Woo Version Checker
        update_option('linksync_wooVersion', 'off');

        //user activity
        update_option('linksync_user_activity', time());
        update_option('linksync_user_activity_daily', time());

        //update notic
        update_option('linksync_update_notic', 'off');

        //Post product
        update_option('post_product', 0);

        // syncing Status
        update_option('linksync_sycning_status', NULL);

        //display_retail_price_tax_inclusive
        update_option('linksync_tax_inclusive', '');
    }

    public function initialize_options()
    {
        add_option('linksync_laid', "");
        add_option('linksync_status', "");
        add_option('linksync_frequency', "");
        add_option('linksync_connected_url', '');
        add_option('linksync_test', 'off');
        add_option('linksync_last_test_time', "");
        add_option('linksync_version', '');
        add_option('linksync_time_offset', '');
        add_option('linksync_connectedto', '');
        add_option('is_linksync_cron_running', "0");
        add_option('linksync_full_stock_import', 'yes');
        add_option('linksync_addedfile', '');
        add_option('linksync_connectionwith', '');
        add_option('linksync_current_stock_index', 0);
        add_option('linksync_current_stock_status', 0);
        add_option('linksync_updated_products_count', "0");
        add_option('linksync_stock_updated_time', "1900-01-01 00:00:00");
        add_option('linksync_option', '');
        add_option('hide_this_notice', 'on');

        // Product Sync Settings
        add_option('product_sync_type', 'disabled_sync'); # Two-way ,Vend to WooCommerce,WooCommerce to Vend,Disabled
        add_option('ps_name_title', 'on');
        add_option('ps_description', 'on');
        add_option('ps_desc_copy', '');
        add_option('ps_price', 'on');
        add_option('excluding_tax', 'on');
        add_option('tax_class', '');
        add_option('price_book', 'off');
        add_option('price_book_identifier', '');
        add_option('ps_categories', 'off');
        add_option('ps_quantity', 'on');
        add_option('ps_outlet', 'on');
        add_option('ps_unpublish', 'on');
        add_option('ps_brand', 'on');
        add_option('ps_tags', 'off');
        add_option('cat_radio', 'ps_cat_tags');
        add_option('ps_imp_by_tag', 'off');
        add_option('import_by_tags_list', '');
        add_option('ps_images', 'off');
        add_option('ps_import_image_radio', 'Enable');
        add_option('ps_create_new', 'on');
        add_option('ps_delete', '');
        add_option('prod_update_req', '');
        add_option('prod_update_suc', NULL);
        add_option('ps_outlet_details', '');
        add_option('ps_wc_to_vend_outlet', 'on');
        add_option('wc_to_vend_outlet_detail', '');
        add_option('ps_pending', '');
        add_option('price_field', 'regular_price');
        add_option('ps_attribute', 'on');
        add_option('linksync_visiable_attr', '1');
        add_option('linksync_woocommerce_tax_option', 'on');

        //Order sync Add options
        add_option('order_sync_type', 'disabled');
        add_option('order_time_req', null);
        add_option('order_time_suc', null);
        add_option('order_status_wc_to_vend', 'wc-processing');
        add_option('wc_to_vend_outlet', '');
        add_option('wc_to_vend_register', '');
        add_option('wc_to_vend_user', '');
        add_option('wc_to_vend_tax', '');
        add_option('wc_to_vend_payment', '');
        add_option('wc_to_vend_export', '');

        // Vend To WC
        add_option('order_vend_to_wc', 'wc-completed');
        add_option('vend_to_wc_tax', '');
        add_option('vend_to_wc_payments', '');
        add_option('vend_to_wc_customer', '');
        add_option('laid_message', null);
        add_option('prod_last_page', '');


        add_option('product_import', 'no');
        add_option('order_import', 'no');

        //order id
        add_option('linksync_sent_order_id', '');
        add_option('Vend_orderIDs', '');

        //product details
        add_option('product_detail', '');

        //order details
        add_option('order_detail', '');

        //Woo Version Checker
        add_option('linksync_wooVersion', 'off');

        //user activity
        add_option('linksync_user_activity', time());
        add_option('linksync_user_activity_daily', time());

        //update notic
        add_option('linksync_update_notic', 'off');

        //Post product
        add_option('post_product', 0);

        // syncing Status
        add_option('linksync_sycning_status', NULL);

        // display_retail_price_tax_inclusive
        add_option('linksync_tax_inclusive', '');
    }
}