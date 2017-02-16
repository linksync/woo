<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Sync
{
    public static $orderSyncOption = null;

    public function __construct()
    {
        self::$orderSyncOption = LS_Vend()->order_option();

        if (self::$orderSyncOption->woocommerceToVend() == self::$orderSyncOption->sync_type()) {
            add_action(LS_Vend()->orderSyncToVendHookName(), array('LS_Vend_Sync', 'importOrderToVend'), 1);
            add_action('woocommerce_process_shop_order_meta', array('LS_Vend_Sync', 'importOrderToVend'));
        }

    }

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

        $orderStatus = $wooOrder->post->post_status;
        $orderStatusToSyncWooToVend = LS_Vend()->getSelectedOrderStatusToTriggerWooToVendSync();
        if ($orderStatus != $orderStatusToSyncWooToVend) {
            //Do not continue importing to vend if it status was not selected
            return;
        }

        $orderTotal = $wooOrder->get_total();
        $orderCurrency = $wooOrder->get_order_currency();
        $taxesIncluded = false;

        $orderItems = $wooOrder->get_items();
        $orderTaxes = $wooOrder->get_taxes();

        foreach ($orderItems as $orderItem) {
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

            if (isset($orderItem['variation_id']) && !empty($orderItem['variation_id'])) {
                $product_id = $orderItem['variation_id'];
            } else {
                $product_id = $orderItem['product_id'];
            }

            $wooProduct = new WC_Product($product_id);
            $orderLineItem = new LS_Woo_Order_Line_Item($orderItem);

            $product_amount = $wooProduct->get_price();
            if (!empty($orderLineItem->lineItem['line_subtotal'])) {
                $product_amount = (float)($orderLineItem->lineItem['line_subtotal'] / $orderLineItem->lineItem['qty']);
            }

            $discount = $orderLineItem->get_discount_amount();
            if (!empty($discount)) {
                $discount = (float)($discount / $orderLineItem->lineItem['qty']);
            }

            //Product Amount = product org amount - discount amount
            $product_total_amount = (float)$product_amount - (float)$discount;
            if (!empty($product_total_amount) && !empty($vendTaxDetails['taxRate'])) {
                $taxValue = ($product_total_amount * $vendTaxDetails['taxRate']);
            }

            $products[] = array(
                'sku' => $wooProduct->get_sku(),
                'title' => $orderItem['name'],
                'price' => $product_total_amount,
                'quantity' => $orderItem['qty'],
                'discountAmount' => $discount,
                'taxName' => $vendTaxDetails['taxName'],
                'taxId' => $vendTaxDetails['taxId'],
                'taxRate' => $vendTaxDetails['taxRate'],
                'taxValue' => isset($taxValue) ? $taxValue : null,
                'discountTitle' => isset($discountTitle) ? $discountTitle : 'sale',
            );
            $totalQuantity += $orderItem['qty'];
        }

        $shippingMethod = $wooOrder->get_shipping_method();

        if (!empty($shippingMethod)) {
            $shipping_cost = $wooOrder->get_total_shipping();
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

            $shippingCost = $wooOrder->get_total_shipping();
            if (!empty($shippingCost) && !empty($vendTaxDetails['taxRate'])) {
                $shippingTaxValue = (float)$shippingCost * (float)$vendTaxDetails['taxRate'];
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
        $orderPaymentMethod = $wooOrder->payment_method;
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
            $phone = !empty($_POST['_billing_phone']) ? $_POST['_billing_phone'] : $wooOrder->billing_phone;
            // Formatted Addresses
            $filtered_billing_address = apply_filters('woocommerce_order_formatted_billing_address', array(
                'firstName' => !empty($_POST['_billing_first_name']) ? $_POST['_billing_first_name'] : $wooOrder->billing_first_name,
                'lastName' => !empty($_POST['_billing_last_name']) ? $_POST['_billing_last_name'] : $wooOrder->billing_last_name,
                'phone' => $phone,
                'street1' => !empty($_POST['_billing_address_1']) ? $_POST['_billing_address_1'] : $wooOrder->billing_address_1,
                'street2' => !empty($_POST['_billing_address_2']) ? $_POST['_billing_address_2'] : $wooOrder->billing_address_2,
                'city' => !empty($_POST['_billing_city']) ? $_POST['_billing_city'] : $wooOrder->billing_city,
                'state' => !empty($_POST['_billing_state']) ? $_POST['_billing_state'] : $wooOrder->billing_state,
                'postalCode' => !empty($_POST['_billing_postcode']) ? $_POST['_billing_postcode'] : $wooOrder->billing_postcode,
                'country' => !empty($_POST['_billing_country']) ? $_POST['_billing_country'] : $wooOrder->billing_country,
                'company' => !empty($_POST['_billing_company']) ? $_POST['_billing_company'] : $wooOrder->billing_company,
                'email_address' => !empty($_POST['_billing_email']) ? $_POST['_billing_email'] : $wooOrder->billing_email
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
                'firstName' => !empty($_POST['_shipping_first_name']) ? $_POST['_shipping_first_name'] : $wooOrder->shipping_first_name,
                'lastName' => !empty($_POST['_shipping_last_name']) ? $_POST['_shipping_last_name'] : $wooOrder->shipping_last_name,
                'phone' => $phone,
                'street1' => !empty($_POST['_shipping_address_1']) ? $_POST['_shipping_address_1'] : $wooOrder->shipping_address_1,
                'street2' => !empty($_POST['_shipping_address_2']) ? $_POST['_shipping_address_2'] : $wooOrder->shipping_address_2,
                'city' => !empty($_POST['_shipping_city']) ? $_POST['_shipping_city'] : $wooOrder->shipping_city,
                'state' => !empty($_POST['_shipping_state']) ? $_POST['_shipping_state'] : $wooOrder->shipping_state,
                'postalCode' => !empty($_POST['_shipping_postcode']) ? $_POST['_shipping_postcode'] : $wooOrder->shipping_postcode,
                'country' => !empty($_POST['_shipping_country']) ? $_POST['_shipping_country'] : $wooOrder->shipping_country,
                'company' => !empty($_POST['_shipping_company']) ? $_POST['_shipping_company'] : $wooOrder->shipping_company,
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

            if('yes' == $useBillingToBePhysicalOption){
                $delivery_address = $orderBillingAddress;
            }

            $useShippingToBePostalOption = $orderOption->useShippingAddressToBePostalAddress();
            if('yes' == $useShippingToBePostalOption){
                $billing_address = $orderShippingAddress;
            }

            $primaryEmail = !empty($primary_email_address) ? $primary_email_address : $billing_address['email_address'];
            unset($billing_address['email_address']);

        }

        $products = !empty($products) ? $products : null;
        $primaryEmail = !empty($primaryEmail) ? $primaryEmail : get_option('admin_email');
        $billing_address = !empty($billing_address) ? $billing_address : null;
        $delivery_address = !empty($delivery_address) ? $delivery_address : null;
        $comments = $wooOrder->post->post_excerpt;
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
}

new LS_Vend_Sync();