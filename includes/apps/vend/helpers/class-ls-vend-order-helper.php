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

}