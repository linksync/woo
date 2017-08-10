<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Product_Custom_Column
{

    public static function init()
    {
        add_filter('manage_edit-product_columns', array('LS_Vend_Product_Custom_Column', 'is_sync_to_vend_column'));
        add_action('manage_product_posts_custom_column', array('LS_Vend_Product_Custom_Column', 'is_sync_to_vend_row_content'));
    }

    public static function is_sync_to_vend_column($columns)
    {
        $new_columns = array();
        foreach ($columns as $column_name => $column_info) {

            $new_columns[$column_name] = $column_info;
            if ('sku' === $column_name || 'is_in_stock' === $column_name) {
                $new_columns['sync_to_qbo'] = 'In Vend';
            }

        }
        return $new_columns;
    }

    public static function is_sync_to_vend_row_content($column)
    {
        global $post;

        if ('sync_to_qbo' === $column) {

            $product_id = $post->ID;
            $productMeta = new LS_Product_Meta($product_id);
            $vend_product_id = $productMeta->get_vend_product_id();
            if (!empty($vend_product_id)) {
                $vendConfig = new LS_Vend_Config();
                $vendUrl = new LS_Vend_Url($vendConfig);
                $edit_link_in_vend = $vendUrl->get_product_edit_url($vend_product_id);
                echo '<a class="yes-in-vend dashicons dashicons-yes" href="' . $edit_link_in_vend . '" target="_blank"></a>';
            } else {
                echo '<span class="no-in-vend dashicons dashicons-no-alt"></span>';
            }

        }

    }

}