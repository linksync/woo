<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Order_Option extends LS_Vend_Option
{
    /**
     * LS_QBO_Product_Option instance
     * @var null
     */
    protected static $_instance = null;

    public static function instance(){

        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function sync_type()
    {
        return get_option('order_sync_type');
    }

    public function get_all_sync_type()
    {
        return array('wc_to_vend', 'vend_to_wc-way','disabled');
    }
}

