<?php

class LS_Vend_Order_Helper
{


    /**
     * Prepare and process product price for syncing order To vend base on Vend, WooCommerce and linksync product syncing tax price settings
     *
     * @param $product_total_amount
     * @param $taxValue
     * @return mixed
     */
    public static function prepareProductPriceForSyncingOrderToVend($product_total_amount, $taxValue)
    {

        if ('on' != LS_Vend()->option()->linksync_woocommerce_tax_option()) {

            $wooIncludeTax = LS_Vend()->option()->woocommerce_prices_include_tax();
            $linkSyncExcludeTax = LS_Vend()->product_option()->excluding_tax();

            if ('no' == $wooIncludeTax && 'off' == $linkSyncExcludeTax) {
                // Done check and passed (no need to add or subtract to $product_total_amount)
            } else if ('no' == $wooIncludeTax && 'on' == $linkSyncExcludeTax) {
                // Done check and passed (no need to add or subtract to $product_total_amount)
            } else if ('yes' == $wooIncludeTax && 'off' == $linkSyncExcludeTax) {
                // Done check and passed
                $product_total_amount = $product_total_amount - $taxValue;
            } else if ('yes' == $wooIncludeTax && 'on' == $linkSyncExcludeTax) {
                // Done check and passed
                $product_total_amount = $product_total_amount - $taxValue;
            }

        } else if ('on' == LS_Vend()->option()->linksync_woocommerce_tax_option()) {

            $wooIncludeTax = LS_Vend()->option()->woocommerce_prices_include_tax();

            if ('no' == $wooIncludeTax) {
                // Done check and passed (no need to add or subtract to $product_total_amount)
            } else if ('yes' == $wooIncludeTax) {
                // Done check and passed
                $product_total_amount = $product_total_amount - $taxValue;
            }

        }

        return $product_total_amount;
    }

    public static function get_woo_order_statuses()
    {
        $linksync_order_statuses = array();
        if (function_exists('wc_get_order_statuses')) {
            $order_statuses = wc_get_order_statuses();
            if ($order_statuses) {
                foreach ($order_statuses as $key => $status) {
                    $linksync_order_statuses[$key] = $status;
                }
            }
        } else {
            $order_statuses = get_terms('shop_order_status', array(
                'hide_empty' => 0
            ));
            if ($order_statuses) {
                foreach ($order_statuses as $status) {
                    $linksync_order_statuses[$status->slug] = $status->name;
                }
            }
        }
        return $linksync_order_statuses;
    }

    public static function get_vend_connected_orders( $orderBy = '', $order = 'DESC')
    {
        global $wpdb;

        $orderBySql = '';
        if(!empty($orderBy)){
            $orderBySql = 'ORDER BY '.$orderBy.' '.strtoupper($order);
        } else {
            $orderBySql = 'ORDER BY ID DESC';
        }

        $sql = "
					SELECT
							wposts.ID AS ID,
							wposts.post_title AS product_name,
                            wposts.post_status AS product_status,
                            wpmeta.meta_key,
                            wpmeta.meta_value,
                            wposts.post_type AS product_type
					FROM $wpdb->postmeta AS wpmeta
					INNER JOIN $wpdb->posts as wposts on ( wposts.ID = wpmeta.post_id )
					WHERE
					      wpmeta.meta_key IN ('_ls_vend_receipt_number') AND wpmeta.meta_value != '' AND
					      wposts.post_type IN('shop_order')
					  $orderBySql
				";

        //get all products with empty sku
        $empty_skus = $wpdb->get_results($sql, ARRAY_A);

        return $empty_skus;
    }

}