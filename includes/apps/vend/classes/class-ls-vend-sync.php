<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Sync
{
    public static $orderSyncOption = null;
    public static $productSyncOption = null;

    public function __construct()
    {
        $orderSyncOption = LS_Vend()->order_option();
        $productSyncOption = LS_Vend()->product_option();

        LS_Vend_Ajax::init_hook();

        if ($orderSyncOption->woocommerceToVend() == $orderSyncOption->sync_type()) {
            /**
             * Add the hook for syncing WooCommerce order if order syncing tye is WooCommerce to Vend
             */
            add_action(LS_Vend()->orderSyncToVendHookName(), array('LS_Vend_Sync', 'importOrderToVend'), 1);
            add_action('woocommerce_process_shop_order_meta', array('LS_Vend_Sync', 'importOrderToVend'));
        }


        self::add_action_save_post();

        $pro_sync_type = $productSyncOption->sync_type();
        if ($pro_sync_type == 'two_way' || $pro_sync_type == 'wc_to_vend') {
            if ('on' == $productSyncOption->delete()) {

                /**
                 * Enable vend product deletion if linksync product syncing delete option is enable
                 */
                add_action('before_delete_post', array('LS_Vend_Sync', 'deleteVendProduct'));
            }

            /**
             * Update Vend product on every Woocommmerce Order creation
             */
            add_action('woocommerce_process_shop_order_meta', array('LS_Vend_Sync', 'updateVendProductOnWooCommerceOrderUpdate'));
            add_action('woocommerce_thankyou', array('LS_Vend_Sync', 'updateVendProductOnWooCommerceOrderUpdate'));
        }
    }


    public static function updateVendProductOnWooCommerceOrderUpdate($order_id)
    {
        $wooOrder = wc_get_order($order_id);
        $orderItems = $wooOrder->get_items();
        foreach ($orderItems as $orderItem) {
            $orderLineItem = new LS_Woo_Order_Line_Item($orderItem);

            $variationId = $orderLineItem->get_variation_id();
            if (!empty($variationId)) {
                $product_id = $variationId;
            } else {
                $product_id = $orderLineItem->get_product_id();
            }

            LS_Vend_Sync::importProductToVend($product_id);
        }

    }

    /**
     * Will add hook on saving WooCommerce product
     */
    public static function add_action_save_post()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            add_action('save_post', array('LS_Vend_Sync', 'save_product'), 999, 3);
        } else {

            if (did_action('woocommerce_new_product') === 1 || did_action('woocommerce_update_product') === 1) {
                add_action('woocommerce_new_product', array('LS_Vend_Sync', 'importProductToVend'), 999);
                add_action('woocommerce_update_product', array('LS_Vend_Sync', 'importProductToVend'), 999);
            } elseif (did_action('woocommerce_new_product_variation') === 1 || did_action('woocommerce_update_product_variation') === 1) {
                add_action('woocommerce_new_product_variation', array('LS_Vend_Sync', 'importProductToVend'), 999);
                add_action('woocommerce_update_product_variation', array('LS_Vend_Sync', 'importProductToVend'), 999);
            } else {
                add_action('save_post', array('LS_Vend_Sync', 'save_product'), 999, 3);
            }

        }
    }

    public static function remove_action_save_post()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            remove_action('save_post', array('LS_Vend_Sync', 'save_product'), 999);
        } else {
            remove_action('woocommerce_new_product', array('LS_Vend_Sync', 'importProductToVend'), 999);
            remove_action('woocommerce_update_product', array('LS_Vend_Sync', 'importProductToVend'), 999);
            remove_action('save_post', array('LS_Vend_Sync', 'save_product'), 999);
        }
    }

    public static function save_product($product_id, $post, $update)
    {

        // Dont' send product for revisions or autosaves and auto-draft post_status
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $product_id;
        }


        // Don't save revisions and autosaves
        if (false !== wp_is_post_revision($product_id) || wp_is_post_autosave($product_id)) {
            return;
        }

        // Check post type is product
        if ('product' != $post->post_type || 'auto-draft' == $post->post_status || 'revision' == $post->post_type) {
            return;
        }

        //Do not send http post to linksync server if user is trashing product
        if ('trash' == $post->post_status) {
            return $product_id;
        }

        self::importProductToVend($product_id);

    }

    /**
     * Imports Woocommerce Products to Vend
     * @param $product_id
     */
    public static function importProductToVend($product_id)
    {
        set_time_limit(0);
        $product = new LS_Woo_Product($product_id);
        $product_meta = new LS_Product_Meta($product_id);
        $productHelper = new LS_Product_Helper($product);
        $productOptionSyncType = LS_Vend()->product_option()->sync_type();

        if ('two_way' == $productOptionSyncType || 'wc_to_vend' == $productOptionSyncType) {
            $product_type = $productHelper->getType();

            if (LS_Product_Helper::isVariationProduct($product)) {
                $parent_id = LS_Product_Helper::getProductParentId($product);
                $product = new LS_Woo_Product($parent_id);
            }

            $product_post_status = LS_Product_Helper::getProductStatus($product);

            //Check if the post type is product or product_variation
            if (LS_Vend_Product_Helper::isTypeSyncAbleToVend($product_type)) {

                $json_product = new LS_Json_Product_Factory();
                $productOptionNameTitle = LS_Vend()->product_option()->nameTitle();
                $productOptionDescription = LS_Vend()->product_option()->description();
                $productOptionPrice = LS_Vend()->product_option()->price();
                $productOptionPriceField = LS_Vend()->product_option()->priceField();
                $productOptionBrand = LS_Vend()->product_option()->brand();
                $productOptionTag = LS_Vend()->product_option()->tag();
                $productOptionQuantity = LS_Vend()->product_option()->quantity();

                $vendOptionWooToVendOutlet = LS_Vend()->option()->wooToVendOutlet();
                $vendOptionWooToVendOutletDetail = LS_Vend()->option()->wooToVendOutletDetail();

                $sku = LS_Vend_Helper::removeSpacesOnsku($product->get_sku());
                if (empty($sku)) {
                    $sku = 'sku_' . $product_id;
                    $product_meta->update_sku($sku);
                    $wooVersion = LS_Vend()->option()->get_woocommerce_version();
                    if (
                        !LS_Helper::isWooVersionLessThan_2_4_15($wooVersion) &&
                        version_compare($wooVersion, '3.0.8', '<')
                    ) {
                        return; //sku is empty
                    }
                }
                $json_product->set_sku(html_entity_decode($sku));


                $excluding_tax = LS_Vend_Tax_helper::is_excluding_tax();
                $display_retail_price_tax_inclusive = LS_Vend()->option()->tax_inclusive();
                $active = ($product_post_status == 'draft') ? 0 : 1;
                $json_product->set_active($active);

                $pTitle = null;
                if ('on' == $productOptionNameTitle) {
                    $pTitle = LS_Product_Helper::getProductName($product);
                }
                $json_product->set_name($pTitle);

                $pDescription = null;
                if ('on' == $productOptionDescription) {
                    $pDescription = LS_Product_Helper::getProductDescription($product);
                }
                $json_product->set_description($pDescription);
                $tax_status = $product_meta->get_tax_status();

                $paramsToGetSellAndListPriceToSyncInVend = array(
                    'product_option_price' => $productOptionPrice,
                    'product_option_price_field' => $productOptionPriceField,
                    'tax_status' => $tax_status,
                    'tax_class' => $product_meta->get_tax_class(),
                    'product_regular_price' => $product_meta->get_regular_price(),
                    'product_sale_price' => $product_meta->get_sale_price(),
                    'excluding_tax' => $excluding_tax,
                    'display_retail_price_tax_inclusive' => $display_retail_price_tax_inclusive,
                    'vend_option_woo_to_vend_outlet' => $vendOptionWooToVendOutlet,
                    'vend_option_woo_to_vend_outlet_detail' => $vendOptionWooToVendOutletDetail,
                );

                $priceBaseOnSettings = LS_Vend_Helper::getSellAndListPriceToSyncInVend($paramsToGetSellAndListPriceToSyncInVend);
                if (!empty($priceBaseOnSettings)) {
                    $json_product->set_sell_price($priceBaseOnSettings);
                    $json_product->set_list_price($priceBaseOnSettings);
                }
                //Set includes_tax key for the json product
                $json_product->set_includes_tax(('taxable' == $tax_status) ? true : false);


                if ('on' == $productOptionBrand) {
                    $pBrands = ls_get_product_terms($product_id, 'brand');
                    $json_product->set_brands($pBrands);
                }

                if ('on' == $productOptionTag) {
                    $pTags = ls_get_product_terms($product_id, 'tag');
                    $json_product->set_tags($pTags);
                }

                $manage_stock = $product_meta->get_manage_stock();
                $paramsToBuildJsonOutlets = array(
                    'manage_stock' => $manage_stock,
                    'product_option_quantity' => $productOptionQuantity,
                    'vend_option_woo_to_vend_outlet' => $vendOptionWooToVendOutlet,
                    'vend_option_woo_to_vend_outlet_detail' => $vendOptionWooToVendOutletDetail,
                    'product_stock' => $product_meta->get_stock()
                );

                if (LS_Product_Helper::isSimpleProduct($product)) {
                    $pOutlets = LS_Vend_Helper::buildOutletJsonBaseOnParams($paramsToBuildJsonOutlets);
                    $json_product->set_outlets($pOutlets);
                }


                if (true == LS_Product_Helper::isVariableProduct($product)) {
                    $has_children = $product->has_child();
                    if (true == $has_children) {
                        $variation_ids = $product->get_children();
                        if (!empty($variation_ids)) {

                            $paramsToBuildJsonVariant = array(
                                'product_option_name_title' => $productOptionNameTitle,
                                'product_option_description' => $productOptionDescription,
                                'product_option_price' => $productOptionPrice,
                                'product_option_price_field' => $productOptionPriceField,
                                'product_option_brand' => $productOptionBrand,
                                'product_option_tag' => $productOptionTag,
                                'product_option_quantity' => $productOptionQuantity,
                                'vend_option_woo_to_vend_outlet' => $vendOptionWooToVendOutlet,
                                'vend_option_woo_to_vend_outlet_detail' => $vendOptionWooToVendOutletDetail,
                                'parent_id' => $product_id
                            );
                            $variationTotalQuantity = 0;
                            $variation = array();
                            foreach ($variation_ids as $variation_id) {

                                $varArray = LS_Vend_Helper::buildSingleVariantJsonBaseOnParams($variation_id, $paramsToBuildJsonVariant);
                                $variation[] = $varArray;
                                $variationTotalQuantity += (int)$varArray['quantity'];
                            }


                            if (!empty($variation)) {
                                $paramsToBuildJsonOutlets['product_stock'] = $variationTotalQuantity;
                                $pOutlets = LS_Vend_Helper::buildOutletJsonBaseOnParams($paramsToBuildJsonOutlets);
                                $json_product->set_outlets($pOutlets);
                            }
                            $json_product->set_variants($variation);

                        }
                    }

                }


                $j_product = $json_product->get_json_product();

                if (true == LS_Product_Helper::isVariableProduct($product) && false == LS_Product_Helper::hasChildren($product)) {
                    $j_product = '';//Do not sync variable if no variation
                }

                if (!empty($j_product)) {
                    $result = LS_Vend()->api()->product()->save_product($j_product);
                    $product_meta->updateToLinkSyncJson(array(
                        'json_being_sent' => $j_product,
                        'response' => $result
                    ));

                    if (!empty($result['id'])) {
                        LS_Vend_Product_Helper::update_vend_ids($result);
                        LSC_Log::add_dev_success('LS_Vend_Sync::importProductToVend', 'Product was imported to QuickBooks <br/> Product json being sent <br/>' . $j_product . '<br/> Response: <br/>' . json_encode($result));
                    } else {
                        LSC_Log::add_dev_failed('LS_Vend_Sync::importProductToVend25', 'Product ID: ' . $product_id . '<br/><br/>Json product being sent: ' . $j_product . '<br/><br/> Response: ' . json_encode($result));
                    }

                    /**
                     * Fires once a product in WooCommerce has been sync to Vend.
                     *
                     * @since 2.4.21
                     *
                     * @param int $product_id Product ID.
                     */
                    do_action('ls_after_woo_to_vend_product_sync', $product_id, $json_product, $result);
                }
            }
        }

    }

    public static function importProductToWoo($product)
    {

        //Make sure its the instance of LS_Simple_Product class
        if (!$product instanceof LS_Product) {
            $product = new LS_Product($product);
        }

        $productSyncOption = LS_Vend()->product_option();

        if (
            'disabled_sync' == $productSyncOption->sync_type() ||
            'shipping' == $product->get_sku() ||
            'refund' == $product->get_sku()
        ) {
            //Do not create this product in woocommerce if the sku is shipping
            //Or return if sync type is disabled
            return;
        }

        remove_all_actions('save_post');
        $wooProductId = LS_Product_Helper::getProductIdBySku($product->get_sku());
        $productDeletedAt = $product->get_deleted_at();

        if (!empty($productDeletedAt)) {
            if(!empty($wooProductId)){
                LS_Vend_Product_Helper::deleteWooProducts($wooProductId);
            }
            //set last sync to the current update_at key
            LS_Vend()->option()->lastProductUpdate($product->get_update_at());
            //Do not continue the rest of the code since product is already deleted
            return;
        }


        $is_new = false;
        if (empty($wooProductId)) {
            $is_new = true;
            $wooProductId = LS_Vend_Product_Helper::createWooProduct($productSyncOption, $product, $is_new);
        }

        if (!empty($wooProductId)) {

            //Get the product meta object for product
            $product_meta = new LS_Product_Meta($wooProductId);
            $product_meta->updateFromLinkSyncJson($product->getJsonProduct());
            $product_meta->update_vend_product_id(get_vend_id($product->get_id()));

            //set last sync to the current update_at key
            LS_Vend()->option()->lastProductUpdate($product->get_update_at());
            LS_Vend_Product_Helper::updateWooProduct(
                $product_meta,
                $product,
                $is_new
            );
        }

        /**
         * Fires once a product has been created or updated in WooCommerce.
         *
         * @since 2.4.21
         *
         * @param int $wooProductId Product ID.
         * @param LS_Product $product linksync Json product getters.
         * @param bool $is_new Whether this is an existing product being updated or not.
         */
        do_action('ls_after_vend_to_woo_product_sync', $wooProductId, $product, $is_new);

    }

    /**
     * Imports woocommerce order to Vend
     * @param $order_id
     */
    public static function importOrderToVend($order_id)
    {
        set_time_limit(0);
        $orderSyncOption = LS_Vend()->order_option();
        if (true == $orderSyncOption->isTheOrderWasSyncToVend($order_id)) {
            //order was already synced to vend
            return;
        }

        $totalQuantity = 0;
        $json_order = new LS_Order_Json_Factory();
        $wooOrder = wc_get_order($order_id);
        $wooOrderHelper = new LS_Order_Helper($wooOrder);
        $wooOrderMeta = new LS_Order_Meta($order_id);

        $orderStatus = $wooOrderHelper->getStatus();
        $orderStatusToSyncWooToVend = LS_Vend()->getSelectedOrderStatusToTriggerWooToVendSync();
        if ($orderStatus != $orderStatusToSyncWooToVend) {
            //Do not continue importing to vend if it status was not selected
            return;
        }

        $orderTotal = $wooOrderHelper->getTotal();
        $orderCurrency = $wooOrderHelper->getCurrency();

        $orderItems = $wooOrder->get_items();
        $orderTaxes = $wooOrder->get_taxes();
        $orderTaxData = null;
        $orderTaxLabel = null;
        $orderTaxRateId = null;
        $orderShippingTaxTotal = 0;
        $orderShippingTaxRateId = null;

        foreach ($orderTaxes as $tax_label) {
            $orderTaxData = new LS_Woo_Order_Line_Item($tax_label);
            $orderShippingTaxTotal = $orderTaxData->get_shipping_tax_total();

            if (empty($orderShippingTaxTotal)) {
                $orderTaxLabel = $orderTaxData->get_tax_label();
                $orderTaxRateId = $orderTaxData->get_tax_rate_id();
            } else {
                $orderShippingTaxRateId = $orderTaxData->get_tax_rate_id();
            }
        }

        foreach ($orderItems as $orderItem) {
            $orderLineItem = new LS_Woo_Order_Line_Item($orderItem);
            $taxClass = $orderLineItem->get_tax_class();

            $variationId = $orderLineItem->get_variation_id();
            if (!empty($variationId)) {
                $product_id = $variationId;
            } else {
                $product_id = $orderLineItem->get_product_id();
            }

            $wooProduct = new LS_Woo_Product($product_id);
            $product_meta = new LS_Product_Meta($product_id);
            $wooProductTaxStatus = $product_meta->get_tax_status();
            $vendTaxDetails = array(
                'taxName' => null,
                'taxId' => null,
                'taxRate' => null
            );
            if ('taxable' == $wooProductTaxStatus) {

                $vendTaxDetails = LS_Vend_Tax_helper::getVendTaxDetailsBaseOnTaxClassMapping(
                    $orderTaxLabel,
                    $taxClass
                );

            }


            $product_amount = $product_meta->get_price();
            $lineQuantity = $orderLineItem->get_quantity();
            $discount = $orderLineItem->get_discount_amount();

            $taxValue = 0;
            //Product Amount = product org amount - discount amount
            $product_total_amount = (float)$product_amount - (float)$discount;
            if (!empty($product_total_amount) && !empty($vendTaxDetails['taxRate'])) {
                $taxValue = ($product_total_amount * $vendTaxDetails['taxRate']);
            }

            $product_total_amount = LS_Vend_Order_Helper::prepareProductPriceForSyncingOrderToVend($product_total_amount, $taxValue);

            $productArgs = array(
                'woo_id' => $product_id,
                'sku' => $wooProduct->get_sku(),
                'title' => $orderLineItem->get_name(),
                'price' => $product_total_amount,
                'quantity' => $lineQuantity,
                'discountAmount' => $discount,
                'taxName' => $vendTaxDetails['taxName'],
                'taxId' => $vendTaxDetails['taxId'],
                'taxRate' => $vendTaxDetails['taxRate'],
                'taxValue' => isset($taxValue) ? $taxValue : null,
                //'discountTitle' => isset($discountTitle) ? $discountTitle : 'sale',
            );

            $products[] = $productArgs;
            $totalQuantity += $lineQuantity;
        }

        $shippingMethod = $wooOrderHelper->getShippingMethod();

        if (!empty($shippingMethod)) {
            $shipping_cost = $wooOrderHelper->getShippingTotal();
            $shipping_tax = $wooOrderHelper->getShippingTax();

            $vendTaxDetails = array(
                'taxId' => null,
                'taxName' => null,
                'taxRate' => null
            );

            if (!empty($shipping_tax)) {
                $vendTaxDetails = LS_Vend_Tax_helper::getVendTaxDetailsBaseOnTaxClassMapping(
                    $orderShippingTaxRateId
                );
            }

            if (!empty($shipping_cost) && !empty($vendTaxDetails['taxRate'])) {
                $shippingTaxValue = (float)$shipping_cost * (float)$vendTaxDetails['taxRate'];
            }
            $products[] = array(
                "price" => isset($shipping_cost) ? $shipping_cost : null,
                "quantity" => 1,
                "sku" => "shipping",
                'taxName' => $vendTaxDetails['taxName'],
                'taxId' => $vendTaxDetails['taxId'],
                'taxRate' => $vendTaxDetails['taxRate'],
                'taxValue' => isset($shippingTaxValue) ? $shippingTaxValue : null
            );
        }

        $orderTotalRefund = $wooOrder->get_total_refunded();
        /**
         * Refund handling
         */
        if (!empty($orderTotalRefund)) {
            //Multiply negative one to make total refund negative value
            $orderTotalRefund = -1 * $orderTotalRefund;
            $products[] = array(
                "price" => $orderTotalRefund,
                "sku" => "refund"
            );
        }

        $orderTransactionId = $wooOrder->get_transaction_id();
        $orderPaymentMethod = $wooOrderHelper->getPaymentMethod();
        if (!empty($orderPaymentMethod)) {
            $vendPaymentDetails = $orderSyncOption->getMappedVendPaymentId($orderPaymentMethod);
            $payment = array(
                "retailer_payment_type_id" => !empty($vendPaymentDetails[1]) ? $vendPaymentDetails[1] : null,
                "method" => !empty($vendPaymentDetails[0]) ? $vendPaymentDetails[0] : null,
                "amount" => isset($orderTotal) ? $orderTotal : 0,
                "transactionNumber" => $orderTransactionId
            );

            $json_order->set_payment_type_id(!empty($vendPaymentDetails[1]) ? $vendPaymentDetails[1] : null);
            $json_order->set_payment($payment);

        }

        //UTC Time
        date_default_timezone_set("UTC");
        $created = date("Y-m-d H:i:s", time());
        $registerDb = get_option('wc_to_vend_register');
        $export_user_details = get_option('wc_to_vend_export');
        $primaryEmail = null;
        if (!empty($export_user_details) && 'customer' == $export_user_details) {
            $phone = !empty($_POST['_billing_phone']) ? $_POST['_billing_phone'] : $wooOrderHelper->getBillingPhone();
            // Formatted Addresses
            $filtered_billing_address = apply_filters('woocommerce_order_formatted_billing_address', array(
                'firstName' => !empty($_POST['_billing_first_name']) ? $_POST['_billing_first_name'] : $wooOrderHelper->getBillingFirsName(),
                'lastName' => !empty($_POST['_billing_last_name']) ? $_POST['_billing_last_name'] : $wooOrderHelper->getBillingLastName(),
                'phone' => $phone,
                'street1' => !empty($_POST['_billing_address_1']) ? $_POST['_billing_address_1'] : $wooOrderHelper->getBillingAddressOne(),
                'street2' => !empty($_POST['_billing_address_2']) ? $_POST['_billing_address_2'] : $wooOrderHelper->getBillingAddressTwo(),
                'city' => !empty($_POST['_billing_city']) ? $_POST['_billing_city'] : $wooOrderHelper->getBillingCity(),
                'state' => !empty($_POST['_billing_state']) ? $_POST['_billing_state'] : $wooOrderHelper->getBillingState(),
                'postalCode' => !empty($_POST['_billing_postcode']) ? $_POST['_billing_postcode'] : $wooOrderHelper->getBillingPostcode(),
                'country' => !empty($_POST['_billing_country']) ? $_POST['_billing_country'] : $wooOrderHelper->getBillingCountry(),
                'company' => !empty($_POST['_billing_company']) ? $_POST['_billing_company'] : $wooOrderHelper->getBillingCompany(),
                'email_address' => !empty($_POST['_billing_email']) ? $_POST['_billing_email'] : $wooOrderHelper->getBillingEmail()
            ), $wooOrder);

            $billing_address = array(
                'firstName' => $filtered_billing_address['firstName'],
                'lastName' => $filtered_billing_address['lastName'],
                'phone' => $filtered_billing_address['phone'],
                'street1' => $filtered_billing_address['street1'],
                'street2' => $filtered_billing_address['street2'],
                'city' => $filtered_billing_address['city'],
                'state' => $filtered_billing_address['state'],
                'postalCode' => $filtered_billing_address['postalCode'],
                'country' => $filtered_billing_address['country'],
                'company' => $filtered_billing_address['company'],
                'email_address' => $filtered_billing_address['email_address']
            );

            $filtered_shipping_address = apply_filters('woocommerce_order_formatted_shipping_address', array(
                'firstName' => !empty($_POST['_shipping_first_name']) ? $_POST['_shipping_first_name'] : $wooOrderHelper->getShippingFirstName(),
                'lastName' => !empty($_POST['_shipping_last_name']) ? $_POST['_shipping_last_name'] : $wooOrderHelper->getShippingLastName(),
                'phone' => $phone,
                'street1' => !empty($_POST['_shipping_address_1']) ? $_POST['_shipping_address_1'] : $wooOrderHelper->getShippingAddressOne(),
                'street2' => !empty($_POST['_shipping_address_2']) ? $_POST['_shipping_address_2'] : $wooOrderHelper->getShippingAddressTwo(),
                'city' => !empty($_POST['_shipping_city']) ? $_POST['_shipping_city'] : $wooOrderHelper->getShippingCity(),
                'state' => !empty($_POST['_shipping_state']) ? $_POST['_shipping_state'] : $wooOrderHelper->getShippingState(),
                'postalCode' => !empty($_POST['_shipping_postcode']) ? $_POST['_shipping_postcode'] : $wooOrderHelper->getShippingPostCode(),
                'country' => !empty($_POST['_shipping_country']) ? $_POST['_shipping_country'] : $wooOrderHelper->getShippingCountry(),
                'company' => !empty($_POST['_shipping_company']) ? $_POST['_shipping_company'] : $wooOrderHelper->getShippingCompany(),
            ), $wooOrder);

            $delivery_address = array(
                'firstName' => $filtered_shipping_address['firstName'],
                'lastName' => $filtered_shipping_address['lastName'],
                'phone' => $filtered_shipping_address['phone'],
                'street1' => $filtered_shipping_address['street1'],
                'street2' => $filtered_shipping_address['street2'],
                'city' => $filtered_shipping_address['city'],
                'state' => $filtered_shipping_address['state'],
                'postalCode' => $filtered_shipping_address['postalCode'],
                'country' => $filtered_shipping_address['country'],
                'company' => $filtered_shipping_address['company']
            );

            $orderOption = LS_Vend()->order_option();
            $useBillingToBePhysicalOption = $orderOption->useBillingAddressToBePhysicalAddress();
            $orderBillingAddress = $billing_address;
            $orderShippingAddress = $delivery_address;

            if ('yes' == $useBillingToBePhysicalOption) {
                $delivery_address = $orderBillingAddress;
            }

            $useShippingToBePostalOption = $orderOption->useShippingAddressToBePostalAddress();
            if ('yes' == $useShippingToBePostalOption) {
                $billing_address = $orderShippingAddress;
            }

            $primaryEmail = !empty($orderBillingAddress['email_address']) ? $orderBillingAddress['email_address'] : '';

        }

        $products = !empty($products) ? $products : null;
        $primaryEmail = !empty($primaryEmail) ? $primaryEmail : get_option('admin_email');
        $billing_address = !empty($billing_address) ? $billing_address : null;
        $delivery_address = !empty($delivery_address) ? $delivery_address : null;
        $comments = "WooCommerce " . $order_id;

        $selectedVendUser = $orderSyncOption->getSelectedVendUser();

        $json_order->set_uid($selectedVendUser['vend_uid']);
        $json_order->set_user_name($selectedVendUser['vend_username']);
        $json_order->set_orderId($order_id);
        $json_order->set_source('WooCommerce');
        $json_order->set_created($created);
        $json_order->set_register_id(!empty($registerDb) ? $registerDb : null);
        $json_order->set_primary_email($primaryEmail);
        $json_order->set_total($orderTotal);
        $json_order->set_comments($comments);
        $json_order->set_currency($orderCurrency);

        $json_order->set_shipping_method(!empty($orderPaymentMethod) ? $orderPaymentMethod : null);


        $json_order->set_products($products);
        $json_order->set_billingAddress($billing_address);
        $json_order->set_deliveryAddress($delivery_address);


        $order_json_data = $json_order->get_json_orders();
        $post_order = LS_Vend()->api()->order()->save_orders($order_json_data);

        $order_meta = new LS_Order_Meta($order_id);
        $order_meta->updateOrderJsonFromWooToVend(array(
            'order_being_sent' => $json_order->getOrderArray(),
            'response' => $post_order
        ));

        if (!empty($post_order['id'])) {
            $orderSyncOption->setFlagOrderWasSyncToVend($order_id, $post_order['orderId']);
            $wooOrderMeta->update_vend_order_id(get_vend_id($post_order['id']));
            $wooOrderMeta->update_vend_receipt_number($post_order['orderId']);

            $note = sprintf(__('This Order was exported to Vend with the Receipt Number %s', 'woocommerce'), $post_order['orderId']);
            $wooOrder->add_order_note($note);
            LSC_Log::add('Order Sync Woo to Vend', 'success', 'Woo Order no:' . $order_id, LS_Vend()->laid()->get_current_laid());

            LSC_Log::add_dev_success('LS_Vend_Sync::importOrderToVend', 'Woo Order ID: ' . $order_id . '<br/><br/>Json order being sent: ' . $order_json_data . '<br/><br/> Response: ' . json_encode($post_order));

            /**
             * Update vend product if sync type is two way or woocommerce to vend and quantity option is enabled
             */
            $productOption = LS_Vend()->product_option();
            $productOptionSyncType = $productOption->sync_type();
            if ('two_way' == $productOptionSyncType || 'wc_to_vend' == $productOptionSyncType) {
                if ('on' == $productOption->quantity()) {
                    $productsBeingOrdered = $json_order->get_products();
                    foreach ($productsBeingOrdered as $productBeingOrdered) {
                        if (!empty($productBeingOrdered['woo_id']) && is_numeric($productBeingOrdered['woo_id'])) {
                            self::importProductToVend($productBeingOrdered['woo_id']);
                        }
                    }
                }

            }

        } else {
            update_post_meta($order_id, '_ls_json_order_error', $post_order);
            LSC_Log::add_dev_failed('LS_Vend_Sync::importOrderToVend', 'Woo Order ID: ' . $order_id . '<br/><br/>Json order being sent: ' . $order_json_data . '<br/><br/> Response: ' . json_encode($post_order));
        }

        /**
         * Fires once an order has been sync to vend.
         *
         * @since 2.4.21
         *
         * @param int $order_id Order ID.
         */
        do_action('ls_after_woo_to_vend_order_sync', $order_id);

    }

    public static function deleteVendProduct($product_id)
    {
        set_time_limit(0);
        if (is_numeric($product_id)) {
            $product = new LS_Woo_Product($product_id);
            $productHelper = new LS_Product_Helper($product);

            if (LS_Vend_Product_Helper::isTypeSyncAbleToVend($productHelper->getType())) {
                $product_meta = new LS_Product_Meta($product_id);
                $product_sku = $product_meta->get_sku();
                if (!empty($product_sku)) {
                    $delete_response = LS_Vend()->api()->product()->delete_product($product_sku);

                    /**
                     * Fires once the product in vend was deleted.
                     *
                     * @since 2.4.21
                     *
                     * @param String $delete_response Json response from linksync after sending delete request for product.
                     */
                    do_action('ls_after_vend_product_deletion', $delete_response);
                }
            }
        }
    }

    /**
     * Importing all products to Woocommerce from page one to the last page.
     * @param int $page
     * @return null
     */
    public static function all_product_to_woo($page = 1)
    {
        $products = LS_Vend()->api()->product()->get_product_by_page($page);
        if (!empty($products['products'])) {
            foreach ($products['products'] as $product) {

                $product = new LS_Product($product);
                LS_Vend_Sync::importProductToWoo($product);

            }
        }

        if ($products['pagination']['page'] <= $products['pagination']['pages']) {

            $page = $products['pagination']['page'] + 1;
            if ($page <= $products['pagination']['pages']) {
                self::all_product_to_woo($page);
            }
        }
        return $products;
    }

    /**
     * Importing products from Vend to WooCommerce since last update from page one to the last page
     * @param int $page
     * @return array|null
     */
    public static function all_product_to_woo_since_last_update($page = 1)
    {
        $last_product_sync = LS_Vend()->option()->lastProductUpdate();
        $params = self::prepare_url_params_for_get_product($page, $last_product_sync);
        $products = LS_Vend()->api()->product()->get_product($params);

        if (!empty($products['products'])) {

            foreach ($products['products'] as $product) {

                $product = new LS_Product($product);
                self::importProductToWoo($product);

            }
        }

        if ($products['pagination']['page'] <= $products['pagination']['pages']) {

            $page = $products['pagination']['page'] + 1;
            if ($page <= $products['pagination']['pages']) {
                self::all_product_to_woo_since_last_update($page);
            }
        }
        return $products;
    }

    /**
     * Returns url params in getting products from linksync server base product syncing settings
     * @param int $page
     * @param null $last_product_sync
     * @return string
     */
    public static function prepare_url_params_for_get_product($page = 1, $last_product_sync = null)
    {
        $productSyncOption = LS_Vend()->product_option();
        $product_sync_type = $productSyncOption->sync_type();

        $urlParams = '';

        $importByTag = $productSyncOption->import_by_tag();
        if ('on' == $importByTag) {
            $import_tags = $productSyncOption->import_by_tags_list();
            $import_unserialize = unserialize($import_tags);
            $tags_import = explode('|', $import_unserialize);
            foreach ($tags_import as $value) {
                $urlParams .= 'tags=' . urlencode($value) . '&';
            }
        }

        $outletOption = $productSyncOption->vendToWooOutlet();
        if ('on' == $outletOption) {
            if ($product_sync_type == 'vend_to_wc-way') {
                $outletDb = $productSyncOption->vendToWooOutletDetail();
                if (!empty($outletDb)) {
                    $outletDb_arr = explode('|', $outletDb);
                    //    Outlets - use the 'outlet' parameter for the Product endpoint to request product
                    foreach ($outletDb_arr as $outlet_name) {
                        $urlParams .= 'outlet=' . urlencode($outlet_name) . '&';
                    }
                }
            } elseif ($product_sync_type == 'two_way') {
                $wooToVendOutlet = $productSyncOption->wooToVendOutlet();
                if ('on' == $wooToVendOutlet) {
                    $getoutlet = get_option('wc_to_vend_outlet_detail');
                    $outlet = explode('|', $getoutlet);
                    $urlParams .= 'outlet=' . urlencode(isset($outlet[1]) ? $outlet[1] : '') . '&';
                }
            }
        }

        if (null != $last_product_sync) {
            $urlParams .= 'since=' . urlencode($last_product_sync) . '&';
        }

        $urlParams .= 'page=' . urlencode($page);

        return $urlParams;
    }

    public static function from_vend_to_woo_quantity_update($page = 1)
    {
        $productSyncOption = LS_Vend()->product_option();
        $product_sync_type = $productSyncOption->sync_type();
        $product_quantity_option = $productSyncOption->quantity();

        if (
            !empty($product_sync_type) &&
            'wc_to_vend' == $product_sync_type &&
            'on' == $product_quantity_option
        ) {
            $laid_info = LS_Vend()->laid()->get_current_laid();

            $result_time = date("Y-m-d H:i:s", $laid_info['time']);
            $url = '';
            if (!empty($result_time)) {
                $url = 'since=' . urlencode($result_time);
            }

            $product_last_update = ls_last_product_updated_at();
            if (false != $product_last_update) {
                $url = 'since=' . urlencode($product_last_update);
            }

            $total_pages = 0;
            $url .= '&page=' . $page;

            $products = LS_Vend()->api()->product()->get_product($url);
            if (!empty($products['products']) && !empty($products['pagination'])) {

                $page = $products['pagination']['page'];
                $total_pages = $products['pagination']['pages'];

                foreach ($products['products'] as $product) {
                    if (!empty($product['variants'])) {

                        foreach ($product['variants'] as $pro_variant) {

                            if (!empty($pro_variant['sku'])) {
                                $product_id = LS_Product_Helper::getProductIdBySku($pro_variant['sku']);
                                if (!empty($product_id)) {
                                    $product_meta = new LS_Product_Meta($product_id);
                                    ls_last_product_updated_at($pro_variant['update_at']);
                                    $quantity = 0;
                                    if (!empty($pro_variant['outlets'])) {
                                        foreach ($pro_variant['outlets'] as $outlet) {
                                            if (!empty($outlet['quantity'])) {
                                                $quantity += (int)$outlet['quantity'];
                                            }
                                        }
                                    }

                                    $product_meta->update_stock($quantity);
                                    if ($quantity <= 0) {
                                        $product_meta->update_stock_status('outofstock');
                                    } else {
                                        $product_meta->update_stock_status('instock');
                                    }
                                }
                            }
                        }
                    } else {
                        if (!empty($product['sku'])) {
                            $product_id = LS_Product_Helper::getProductIdBySku($product['sku']);
                            if (!empty($product_id)) {
                                $product_meta = new LS_Product_Meta($product_id);
                                ls_last_product_updated_at($product['update_at']);
                                $quantity = 0;

                                if (!empty($product['outlets'])) {
                                    foreach ($product['outlets'] as $outlet) {
                                        if (!empty($outlet['quantity'])) {
                                            $quantity += (int)$outlet['quantity'];
                                        }
                                    }
                                }

                                $product_meta->update_stock($quantity);
                                if ($quantity <= 0) {
                                    $product_meta->update_stock_status('outofstock');
                                } else {
                                    $product_meta->update_stock_status('instock');
                                }
                            }
                        }
                    }
                }

                $next_page = $page + 1;

                if ($next_page <= $total_pages) {
                    self::from_vend_to_woo_quantity_update($next_page);
                }

            }

        }
    }
}

new LS_Vend_Sync();