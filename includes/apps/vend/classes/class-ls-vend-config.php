<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Config
{
    private $config_array = null;

    public function __construct()
    {
        $this->config_array = self::get_vend_config();
    }

    /**
     * Get all the vend config arrays
     *
     * @return mixed|null
     */
    public function get_vend_config_array()
    {
        return $this->config_array;
    }

    /**
     * Get the value from previously saved vend config arrays
     * @param $key
     * @return null
     */
    public function get_data($key)
    {
        if (isset($this->config_array[$key])) {
            return $this->config_array[$key];
        }

        return null;
    }

    /**
     * Get account state or vend plan
     * @return null
     */
    public function get_account_state()
    {
        return $this->get_data('account_state');
    }

    /**
     * Get Vend domain prefix
     * @return null
     */
    public function get_domain_prefix()
    {
        return $this->get_data('domain_prefix');
    }

    /**
     * Get vend setup if price displya inclusive or not
     * @return null
     */
    public function get_retail_price_tax_inclusive_display()
    {
        return $this->get_data('display_retail_price_tax_inclusive');
    }

    /**
     * Save Vend config into wordpress option
     *
     * @param $vend_config_array
     * @return bool
     */
    public static function update_vend_config($vend_config_array)
    {
        return update_option('_ls_vend_config', $vend_config_array);
    }

    /**
     * Get previously save vend config from wordpress option table.
     * Saving happens when vend plugin executed the application run method.
     *
     * @see LS_Vend run method
     * @return mixed
     */
    public static function get_vend_config()
    {
        return get_option('_ls_vend_config', '');
    }

}