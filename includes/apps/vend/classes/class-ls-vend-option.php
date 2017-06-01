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


}