<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Sync
{
    public static $orderSyncOption = null;
    public static $productSyncOption = null;

    public function __construct()
    {
        self::$orderSyncOption = LS_Vend()->order_option();
        self::$productSyncOption = LS_Vend()->product_option();

        if (self::$orderSyncOption->woocommerceToVend() == self::$orderSyncOption->sync_type()) {
            add_action(LS_Vend()->orderSyncToVendHookName(), array('LS_Vend_Sync', 'importOrderToVend'), 1);
            add_action('woocommerce_process_shop_order_meta', array('LS_Vend_Sync', 'importOrderToVend'));
        }


        self::add_action_save_post();

        $pro_sync_type = self::$productSyncOption->sync_type();
        if ($pro_sync_type == 'two_way' || $pro_sync_type == 'wc_to_vend') {
            if ('on' == self::$productSyncOption->delete()) {
                add_action('before_delete_post', array('LS_Vend_Sync', 'deleteVendProduct'));
            }
        }

    }

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

        LS_Vend_Sync::importProductToVend($product_id);

    }

    /**
     * Imports Woocommerce Products to Vend
     * @param $product_id
     */
    public static function importProductToVend($product_id)
    {
        set_time_limit(0);
        $product = wc_get_product($product_id);
        $product_meta = new LS_Product_Meta($product_id);
        $productOptionSyncType = LS_Vend()->product_option()->sync_type();

        if ('two_way' == $productOptionSyncType || 'wc_to_vend' == $productOptionSyncType) {
            $product_type = $product->get_type();

            if(LS_Product_Helper::isVariationProduct($product)){
                $parent_id = LS_Product_Helper::getProductParentId($product);
                $product = wc_get_product($parent_id);
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
                    if (!LS_Helper::isWooVersionLessThan_2_4_15()) {
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
                    'product_regular_price' => $product->get_regular_price(),
                    'product_sale_price' => $product->get_sale_price(),
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
                    if (!empty($result['id'])) {
                        LSC_Log::add_dev_success('LS_Vend_Sync::importProductToVend', 'Product was imported to QuickBooks <br/> Product json being sent <br/>' . $j_product . '<br/> Response: <br/>' . json_encode($result));
                    } else {
                        LSC_Log::add_dev_failed('LS_Vend_Sync::importProductToVend25', 'Product ID: ' . $product_id . '<br/><br/>Json product being sent: ' . $j_product . '<br/><br/> Response: ' . json_encode($result));
                    }
                }



            }
        }

    }


    /**
     * Imports woocommerce order to Vend
     * @param $order_id
     */
    public static function importOrderToVend($order_id)
    {
        set_time_limit(0);
        if (true == self::$orderSyncOption->isTheOrderWasSyncToVend($order_id)) {
            //order was already synced to vend
            return;
        }

        $totalQuantity = 0;
        $json_order = new LS_Order_Json_Factory();
        $wooOrder = wc_get_order($order_id);
        $wooOrderHelper = new LS_Order_Helper($wooOrder);

        $orderStatus = $wooOrderHelper->getStatus();
        $orderStatusToSyncWooToVend = LS_Vend()->getSelectedOrderStatusToTriggerWooToVendSync();
        if ($orderStatus != $orderStatusToSyncWooToVend) {
            //Do not continue importing to vend if it status was not selected
            return;
        }


        $orderTotal = $wooOrder->get_total();
        $orderCurrency = $wooOrder->get_currency();
        $taxesIncluded = false;

        $orderItems = $wooOrder->get_items();
        $orderTaxes = $wooOrder->get_taxes();

        foreach ($orderItems as $orderItem) {
            $orderLineItem = new LS_Woo_Order_Line_Item($orderItem);

            $vendTaxDetails = array(
                'taxId' => null,
                'taxName' => null,
                'taxRate' => null
            );


            foreach ($orderTaxes as $tax_label) {
                $vendTaxDetails = LS_Vend_Tax_helper::getVendTaxDetailsBaseOnTaxClassMapping(
                    $tax_label['label'],
                    $orderItem['item_meta']['_tax_class'][0]
                );
            }

            $variationId = $orderLineItem->get_variation_id();
            if (!empty($variationId)) {
                $product_id = $variationId;
            } else {
                $product_id = $orderLineItem->get_product_id();
            }

            $wooProduct = wc_get_product($product_id);
            $product_amount = $wooProduct->get_price();
            $lineSubTotal = $orderLineItem->get_subtotal();
            $lineQuantity = $orderLineItem->get_quantity();

            if (!empty($lineSubTotal)) {
                $product_amount = (float)($lineSubTotal / $lineQuantity);
            }


            $discount = $orderLineItem->get_discount_amount();
            if (!empty($discount)) {
                $discount = (float)($discount / $lineQuantity);
            }

            //Product Amount = product org amount - discount amount
            $product_total_amount = (float)$product_amount - (float)$discount;
            if (!empty($product_total_amount) && !empty($vendTaxDetails['taxRate'])) {
                $taxValue = ($product_total_amount * $vendTaxDetails['taxRate']);
                error_log($orderLineItem->get_name() . " taxvalue => " . $taxValue);
            }

            $products[] = array(
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
            $totalQuantity += $lineQuantity;
        }
        //exit();

        $shippingMethod = $wooOrder->get_shipping_method();

        if (!empty($shippingMethod)) {
            $shipping_cost = $wooOrder->get_shipping_total();
            $shipping_tax = $wooOrder->get_shipping_tax();

            $vendTaxDetails = array(
                'taxId' => null,
                'taxName' => null,
                'taxRate' => null
            );

            if (!empty($shipping_tax)) {
                foreach ($orderTaxes as $tax_label) {
                    $vendTaxDetails = LS_Vend_Tax_helper::getVendTaxDetailsBaseOnTaxClassMapping(
                        $tax_label['label'],
                        $orderItem['item_meta']['_tax_class'][0]
                    );
                }
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

        $orderTransactionId = $wooOrder->get_transaction_id();
        $orderPaymentMethod = $wooOrderHelper->getPaymentMethod();
        if (!empty($orderPaymentMethod)) {
            $vendPaymentDetails = self::$orderSyncOption->getMappedVendPaymentId($orderPaymentMethod);
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
        $orderTaxTotal = $wooOrder->get_total_tax();

        $selectedVendUser = self::$orderSyncOption->getSelectedVendUser();

        $json_order->set_uid($selectedVendUser['vend_uid']);
        $json_order->set_user_name($selectedVendUser['vend_username']);
        $json_order->set_orderId($order_id);
        $json_order->set_source('WooCommerce');
        $json_order->set_created($created);
        $json_order->set_register_id(!empty($registerDb) ? $registerDb : null);
        $json_order->set_primary_email($primaryEmail);
        $json_order->set_total($orderTotal);
        $json_order->set_total_tax(!empty($orderTaxTotal) ? $orderTaxTotal : 0);
        $json_order->set_comments($comments);
        $json_order->set_taxes_included($taxesIncluded);
        $json_order->set_currency($orderCurrency);

        $json_order->set_shipping_method(!empty($orderPaymentMethod) ? $orderPaymentMethod : null);


        $json_order->set_products($products);
        $json_order->set_billingAddress($billing_address);
        $json_order->set_deliveryAddress($delivery_address);


        $order_json_data = $json_order->get_json_orders();
        $post_order = LS_Vend()->api()->order()->save_orders($order_json_data);
        if (!empty($post_order['id'])) {
            self::$orderSyncOption->setFlagOrderWasSyncToVend($order_id, $post_order['orderId']);
            $note = sprintf(__('This Order was exported to Vend with the Receipt Number %s', 'woocommerce'), $post_order['orderId']);
            $wooOrder->add_order_note($note);
            LSC_Log::add('Order Sync Woo to Vend', 'success', 'Woo Order no:' . $order_id, LS_ApiController::get_current_laid());

            LSC_Log::add_dev_success('LS_Vend_Sync::importOrderToVend', 'Woo Order ID: ' . $order_id . '<br/><br/>Json order being sent: ' . $order_json_data . '<br/><br/> Response: ' . json_encode($post_order));
        } else {
            update_post_meta($order_id, '_ls_json_order_error', $post_order);
            LSC_Log::add_dev_failed('LS_Vend_Sync::importOrderToVend', 'Woo Order ID: ' . $order_id . '<br/><br/>Json order being sent: ' . $order_json_data . '<br/><br/> Response: ' . json_encode($post_order));
        }

    }

    public static function deleteVendProduct($product_id)
    {
        set_time_limit(0);
        $pro_object = wc_get_product($product_id);
        $productHelper = new LS_Product_Helper($pro_object);

        if (LS_Vend_Product_Helper::isTypeSyncAbleToVend($productHelper->getType())) {
            $product_sku = $productHelper->getSku();
            if (!empty($product_sku)) {
                LS_Vend()->api()->product()->delete_product($product_sku);
            }
        }
    }
}

new LS_Vend_Sync();