<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Order_Custom_Column
{

    public static function init()
    {
        add_filter('manage_edit-shop_order_columns', array('LS_Vend_Order_Custom_Column', 'is_sync_to_vend_column'));
        add_action('manage_shop_order_posts_custom_column', array('LS_Vend_Order_Custom_Column', 'is_sync_to_vend_row_content'));
    }


    public static function is_sync_to_vend_column($columns)
    {
        $new_columns = array();

        foreach ($columns as $column_name => $column_info) {

            $new_columns[$column_name] = $column_info;
            if ('billing_address' === $column_name) {
                $new_columns['sync_to_qbo'] = 'In Vend';
            }
        }

        return $new_columns;
    }

    public static function is_sync_to_vend_row_content($column)
    {
        global $post;

        if ('sync_to_qbo' === $column) {
            $order_id = $post->ID;

            $vendConfig = new LS_Vend_Config();
            $vendUrl = new LS_Vend_Url($vendConfig);
            $orderMeta = new LS_Order_Meta($order_id);
            $receipt_number = $orderMeta->get_vend_receipt_number();
            $view_order_link_in_vend = $vendUrl->get_order_view_url($receipt_number);



            if (!empty($receipt_number)) {

                echo '<a class="yes-in-vend dashicons dashicons-yes" href="' . $view_order_link_in_vend . '" target="_blank"></a>';

            } else {

                echo '<span class="no-in-vend dashicons dashicons-no-alt"></span>';
            }

        }

    }

}