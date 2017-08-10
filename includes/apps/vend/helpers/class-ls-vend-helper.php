<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Helper
{


    public static function getSellAndListPriceToSyncInVend($arrayOfParams)
    {
        $productOptionPrice = $arrayOfParams['product_option_price'];
        $productOptionPriceField = $arrayOfParams['product_option_price_field'];
        $tax_status = $arrayOfParams['tax_status'];
        $tax_class = $arrayOfParams['tax_class'];
        $product_regular_price = $arrayOfParams['product_regular_price'];
        $product_sale_price = $arrayOfParams['product_sale_price'];
        $excluding_tax = $arrayOfParams['excluding_tax'];
        $display_retail_price_tax_inclusive = $arrayOfParams['display_retail_price_tax_inclusive'];

        if ('on' == $productOptionPrice) {
            //Product with TAX
            $taxsetup = LS_Vend_Tax_helper::is_taxable($tax_status, $tax_class);
            $priceBaseOnSettings = ('regular_price' == $productOptionPriceField) ? $product_regular_price : $product_sale_price;

            /**
             * INCLUDE TAX IS CHECKED
             *
             * excluding tax off and tax not enabled in woocomerce
             * Default if tax is off sell_price and list_price will be modified in the next following code and if condition
             */
            if (!empty($priceBaseOnSettings)) {
                $price = $priceBaseOnSettings;
            }

            $priceBaseOnSettings = (float)$priceBaseOnSettings;
            $tax_rate = 0;
            if ('on' == $excluding_tax) {
                /**
                 *  No effect on price
                 *  https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                 */
                if (true == $taxsetup && !empty($priceBaseOnSettings)) {

                    $tax_value = (float)($priceBaseOnSettings * $tax_rate);

                    /**
                     * For excluding tax (both Woo Tax Excluding and Vend Tax Excluding)
                     * display_retail_price_tax_inclusive 1, sell_price = Woo Final price + tax
                     * For display_retail_price_tax_inclusive 0, sell_price = Woo Final price
                     */
                    if ('1' == $display_retail_price_tax_inclusive) {
                        $price = $priceBaseOnSettings + $tax_value;
                    } else if ('0' == $display_retail_price_tax_inclusive) {
                        $price = $priceBaseOnSettings;
                    }

                }
            } else {

                if (true == $taxsetup && !empty($priceBaseOnSettings)) {

                    $tax_value = ($priceBaseOnSettings - ($priceBaseOnSettings / (1 + $tax_rate)));
                    /**
                     * For including tax (both Woo Tax Including and Vend Tax Including)
                     * display_retail_price_tax_inclusive 1, sell_price = Woo Final price
                     * For display_retail_price_tax_inclusive 0, sell_price = Woo Final price - tax
                     */
                    if ('1' == $display_retail_price_tax_inclusive) {
                        $price = $priceBaseOnSettings;
                    } elseif ('0' == $display_retail_price_tax_inclusive) {
                        $price = $priceBaseOnSettings - $tax_value;
                    }
                }
            }

            if (isset($price)) {
                return $price;
            }

        }

        return 0;
    }

    /***
     * Helper method to build outlets array before sending product array to LWS
     * @param $arrayOfParams
     * @return array
     */
    public static function buildOutletJsonBaseOnParams($arrayOfParams)
    {

        $manage_stock = $arrayOfParams['manage_stock'];
        $productOptionQuantity = $arrayOfParams['product_option_quantity'];
        $vendOptionWooToVendOutlet = $arrayOfParams['vend_option_woo_to_vend_outlet'];
        $vendOptionWooToVendOutletDetail = $arrayOfParams['vend_option_woo_to_vend_outlet_detail'];
        $product_stock = $arrayOfParams['product_stock'];


        if (!empty($manage_stock) && ('yes' == $manage_stock || '1' == $manage_stock)) {

            if ('on' == $productOptionQuantity && 'on' == $vendOptionWooToVendOutlet && !empty($vendOptionWooToVendOutletDetail)) {

                $outlet = explode('|', $vendOptionWooToVendOutletDetail);
                $pBaseOutlet['name'] = html_entity_decode($outlet[0]);
                $pBaseOutlet['quantity'] = NULL;
                if (!empty($product_stock) || 0 == $product_stock) {
                    $pBaseOutlet['quantity'] = $product_stock;
                }
                return array($pBaseOutlet);


            } else {
                return array(array('quantity' => NULL));
            }
        }

        return null;
    }

    public static function buildSingleVariantJsonBaseOnParams($varId, $arrayOfParams)
    {
        $productOptionNameTitle = $arrayOfParams['product_option_name_title'];
        $productOptionDescription = $arrayOfParams['product_option_description'];
        $productOptionPrice = $arrayOfParams['product_option_price'];
        $productOptionPriceField = $arrayOfParams['product_option_price_field'];
        $productOptionBrand = $arrayOfParams['product_option_brand'];
        $productOptionTag = $arrayOfParams['product_option_tag'];
        $productOptionQuantity = $arrayOfParams['product_option_quantity'];
        $vendOptionWooToVendOutlet = $arrayOfParams['vend_option_woo_to_vend_outlet'];
        $vendOptionWooToVendOutletDetail = $arrayOfParams['vend_option_woo_to_vend_outlet_detail'];
        $parent_id = $arrayOfParams['parent_id'];

        $var_product = new LS_Woo_Product($varId);
        $var_product_meta = new LS_Product_Meta($varId);
        $var_json_product = new LS_Json_Product_Factory();

        $sku = LS_Vend_Helper::removeSpacesOnsku($var_product_meta->get_sku());
        if (empty($sku)) {
            $sku = 'sku_' . $varId;
        }
        $var_product_meta->update_sku($sku);
        $var_json_product->set_sku(html_entity_decode($sku));


        $varProductTitle = null;
        if ('on' == $productOptionNameTitle) {
            $varProductTitle = LS_Product_Helper::getProductName($var_product);
        }
        $var_json_product->set_name($varProductTitle);


        $excluding_tax = LS_Vend_Tax_helper::is_excluding_tax();
        $display_retail_price_tax_inclusive = LS_Vend()->option()->tax_inclusive();
        $tax_status = $var_product_meta->get_tax_status();
        $tax_class = $var_product_meta->get_tax_class();


        $paramsToGetSellAndListPriceToSyncInVend = array(
            'product_option_price' => $productOptionPrice,
            'product_option_price_field' => $productOptionPriceField,
            'tax_status' => $tax_status,
            'tax_class' => $tax_class,
            'product_regular_price' => $var_product_meta->get_regular_price(),
            'product_sale_price' => $var_product_meta->get_sale_price(),
            'excluding_tax' => $excluding_tax,
            'display_retail_price_tax_inclusive' => $display_retail_price_tax_inclusive,
            'vend_option_woo_to_vend_outlet' => $vendOptionWooToVendOutlet,
            'vend_option_woo_to_vend_outlet_detail' => $vendOptionWooToVendOutletDetail,
        );
        $priceBaseOnSettings = LS_Vend_Helper::getSellAndListPriceToSyncInVend($paramsToGetSellAndListPriceToSyncInVend);
        if (!empty($priceBaseOnSettings)) {
            $var_json_product->set_sell_price($priceBaseOnSettings);
            $var_json_product->set_list_price($priceBaseOnSettings);
        }
        $manage_stock = $var_product_meta->get_manage_stock();
        $var_stock = $var_product_meta->get_stock();
        $varQuantity = !empty($var_stock) ? $var_stock : null;
        $paramsToBuildJsonOutlets = array(
            'manage_stock' => $manage_stock,
            'product_option_quantity' => $productOptionQuantity,
            'vend_option_woo_to_vend_outlet' => $vendOptionWooToVendOutlet,
            'vend_option_woo_to_vend_outlet_detail' => $vendOptionWooToVendOutletDetail,
            'product_stock' => $varQuantity
        );
        $pOutlets = LS_Vend_Helper::buildOutletJsonBaseOnParams($paramsToBuildJsonOutlets);
        $var_json_product->set_outlets($pOutlets);
        $var_json_product->set_quantity($varQuantity);

        $variant_attributes = ls_get_variant_attributes($varId);
        if (!empty($variant_attributes)) {

            $option_key = 1;
            $vend_options = ls_vend_variant_option();
            $vend_options_count = count($vend_options);
            $option_name_str = 'name';
            $option_value_str = 'value';

            foreach ($variant_attributes as $variant_attribute) {
                if (isset($vend_options[$option_key])) {
                    $var_json_product->set($vend_options[$option_key] . $option_name_str, $variant_attribute[$option_name_str]);
                    $var_json_product->set($vend_options[$option_key] . $option_value_str, $variant_attribute[$option_value_str]);
                    $option_key++;

                }
            }

            for ($i = $option_key; $i <= $vend_options_count; $i++) {
                if (isset($vend_options[$i])) {
                    $var_json_product->set($vend_options[$i] . $option_name_str, "NULL");
                    $var_json_product->set($vend_options[$i] . $option_value_str, "NULL");
                }
            }
        }

        return $var_json_product->get_product_array();

    }

    public static function removeSpacesOnsku($sku)
    {
        if (!empty($sku)) {
            if (strpos($sku, ' ')) {
                $sku_replaced = str_replace(' ', '', $sku);
                $sku = $sku_replaced;
            }
            $search = array('/', '\\', ':', ';', '!', '@', '#', '$', '%', '^', '*', '(', ')', '+', '=', '|', '{', '}', '[', ']', '"', "'", '<', '>', ',', '?', '~', '`', '&', '.');
            foreach ($search as $special) {
                if (strpos($sku, $special)) {
                    $sku_replaced = str_replace($special, '-', $sku);
                    $sku = $sku_replaced;
                }
            }
            return $sku;
        }

    }

    public static function variant_options()
    {
        $options = array(
            1 => 'option_one_',
            2 => 'option_two_',
            3 => 'option_three_',
        );
        return $options;
    }

    public static function isWooVersionLessThan_2_4_15()
    {
        if (version_compare(WC()->version, '2.6.15', '<')) {
            return true;
        }

        return false;
    }

    public static function isExcludingTax()
    {
        $excluding_tax = 'on';

        if (get_option('woocommerce_calc_taxes') == 'yes') {
            if (get_option('linksync_woocommerce_tax_option') == 'on') {
                if (get_option('woocommerce_prices_include_tax') == 'yes') {
                    $excluding_tax = 'off'; //Include tax is on
                } else {
                    $excluding_tax = 'on'; //Excluding tax is on
                }
            } else {
                $excluding_tax = get_option('excluding_tax');
            }
        } else {
            $excluding_tax = get_option('excluding_tax');
        }

        return $excluding_tax;
    }

    public static function save_woocommerce_version()
    {
        $plugin_file = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
        $data = get_plugin_data($plugin_file, $markup = true, $translate = true);

        if (!empty($data['Version'])) {
            LS_Vend()->option()->save_woocommerce_version($data['Version']);
            $check = '2.2';
            if ($data['Version'] <= $check) {
                update_option('linksync_wooVersion', 'on');
            } else {
                update_option('linksync_wooVersion', 'off');
            }
        }

    }

    public static function send_capping_notice($total_connected_products = 0, $total_connected_order = 0, $linked_app = 'WooCommerce and Vend POS')
    {
        ob_start();

        if(empty($total_connected_products)){
            $connectedProductsArray = LS_Vend_Product_Helper::get_vend_connected_products();
            $total_connected_products = count($connectedProductsArray);
        }

        if(empty($total_connected_order)){
            $connectedOrdersArray = LS_Vend_Order_Helper::get_vend_connected_orders();
            $total_connected_order = count($connectedOrdersArray);
        }
        $upgrade_url = LS_User_Helper::upgrade_url();
        include_once LS_PLUGIN_DIR . 'templates/emails/email-capping-notice.php';
        // Get email contents
        $capping_html_notice = ob_get_clean();

        $to = get_option('admin_email');
        $subject = 'Your Syncing Progress Report';
        $body = $capping_html_notice;
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: linksync <support@linksync.com>',
        );
        wp_mail($to, $subject, $body, $headers);

    }
}