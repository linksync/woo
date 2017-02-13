<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Product_Option extends LS_Vend_Option
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
        return get_option('product_sync_type');
    }
}