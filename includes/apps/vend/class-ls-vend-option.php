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
     *
     * @param $key
     * @return mixed|void
     */
    public function get_option($key)
    {
        $key = self::instance()->option_prefix . $key;
        return get_option($key);
    }


}