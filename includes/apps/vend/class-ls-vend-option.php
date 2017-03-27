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