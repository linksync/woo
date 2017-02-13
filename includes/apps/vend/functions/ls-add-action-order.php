<?php if (!defined('ABSPATH')) exit('Access is Denied');

if (!class_exists('linksync_class'))
    include_once LS_PLUGIN_DIR . '/classes/Class.linksync.php';# Handle Module Functions

function wc_get_orderid_by_order_key($order_key)
{
    global $wpdb;
    $order_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_order_key' AND meta_value = %s", $order_key));
    return $order_id;
}

function linksync_OrderFromFrontEnd()
{
    if (isset($_REQUEST['key']) && !empty($_REQUEST['key'])) {
        $orderId = wc_get_orderid_by_order_key($_REQUEST['key']);
        orderpost($orderId);
    }
}

function linksync_OrderFromBackEnd()
{//Ordered product(s)
    global $wpdb;
    $testMode = get_option('linksync_test');
    $LAIDKey = get_option('linksync_laid');
    $apicall = new linksync_class($LAIDKey, $testMode);
    if ($_POST['order_status'] == get_option('order_status_wc_to_vend')) {
        //Checking for already sent Order
        $orderId = $_POST['ID'];
        $vendOrderId = get_post_meta($orderId, '_ls_vend_order_id', true);
        //Check if it has been sent to lws and was already created in vend
        if (empty($vendOrderId)) {
            $total_quantity = 0;
            $order = new WC_Order($orderId);
            $order_no = $order->get_order_number();
            if (strpos($order_no, '#') !== false) {
                $order_no = str_replace('#', '', $order_no);
            }
            $get_total = $order->get_total();
            $currency = $order->get_order_currency();
            $order_total = $_POST['_order_total'];
            $taxes_included = false;
            $registerDb = get_option('wc_to_vend_register');
            $vend_user_detail = get_option('wc_to_vend_user');
            if (isset($vend_user_detail) && !empty($vend_user_detail)) {
                $user = explode('|', $vend_user_detail);
                $vend_uid = isset($user[0]) ? $user[0] : null;
                $vend_username = isset($user[1]) ? $user[1] : null;
            }
//Ordered product(s)
            $items = $order->get_items();
            $taxes = $order->get_taxes();
            foreach ($items as $item) {
                foreach ($taxes as $tax_label) {
                    $sql_query = "SELECT  tax_rate_id FROM  `" . $wpdb->prefix . "woocommerce_tax_rates` WHERE  tax_rate_name= %s AND tax_rate_class= %s ";
                    $sql = $wpdb->get_results($wpdb->prepare($sql_query, $tax_label['label'], $item['item_meta']['_tax_class'][0]), ARRAY_A);
                    if (0 != $wpdb->num_rows) {
                        $tax_classes = linksync_tax_classes($tax_label['label'], $item['item_meta']['_tax_class'][0]);
                        if ($tax_classes['result'] == 'success') {
                            $vend_taxes = explode('/', $tax_classes['tax_classes']);
                        }
                    }
                }
                $taxId = isset($vend_taxes[0]) ? $vend_taxes[0] : null;
                $taxName = isset($vend_taxes[1]) ? $vend_taxes[1] : null;
                $taxRate = isset($vend_taxes[2]) ? $vend_taxes[2] : null;
                if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                    $product_id = $item['variation_id'];
                } else {
                    $product_id = $item['product_id'];
                }
                $pro_object = new WC_Product($product_id);
                $itemtotal = (float)$item['item_meta']['_line_subtotal'][0];
                if (isset($item['line_subtotal']) && !empty($item['line_subtotal'])) {
                    $product_amount = (float)($item['line_subtotal'] / $item['qty']);
                }
                $discount = (float)$item['item_meta']['_line_subtotal'][0] - (float)$item['item_meta']['_line_total'][0];
                if (isset($discount) && !empty($discount)) {
                    $discount = (float)($discount / $item['qty']);
                }
                #---------Changes--------#
                //Product Amount = product org amount - discount amount
                $product_total_amount = (float)$product_amount - (float)$discount;
                $product_sku = $pro_object->get_sku();
                if (isset($product_total_amount) && isset($taxRate) && !empty($product_total_amount) && !empty($taxRate)) {
                    $taxValue = ($product_total_amount * $taxRate);
                }

                $products[] = array(
                    'sku' => $product_sku,
                    'title' => $item['name'],
                    'price' => $product_total_amount,
                    'quantity' => $item['qty'],
                    'discountAmount' => isset($discount) ? $discount : 0, 'taxName' => isset($taxName) ? $taxName : null,
                    'taxId' => isset($taxId) ? $taxId : null,
                    'taxRate' => isset($taxRate) ? $taxRate : null,
                    'taxValue' => isset($taxValue) ? $taxValue : null,
                    'discountTitle' => isset($discountTitle) ? $discountTitle : 'sale',
                );
                $total_quantity += $item['qty'];
                unset($taxId);
                unset($taxName);
                unset($taxRate);
                unset($taxValue);
            }

            #----------Shipping------------#
            foreach ($taxes as $tax_label) {
                if (isset($tax_label['shipping_tax_amount']) && !empty($tax_label['shipping_tax_amount'])) {
                    $tax_classes = linksync_tax_classes($tax_label['label'], $item['item_meta']['_tax_class'][0]);
                    if ($tax_classes['result'] == 'success') {
                        $vend_taxes = explode('/', $tax_classes['tax_classes']);
                        $taxId_shipping = isset($vend_taxes[0]) ? $vend_taxes[0] : null;
                        $taxName_shipping = isset($vend_taxes[1]) ? $vend_taxes[1] : null;
                        $taxRate_shipping = isset($vend_taxes[2]) ? $vend_taxes[2] : null;
                    }
                }
            }


            if (isset($_POST['shipping_method_id']) && !empty($_POST['shipping_method_id'][0])) {
                $shipping_method = $_POST['shipping_method'][$_POST['shipping_method_id'][0]];
                $shipping_cost = $_POST['shipping_cost'][$_POST['shipping_method_id'][0]];
                if (isset($shipping_cost) && isset($taxRate_shipping) && !empty($shipping_cost) && !empty($taxRate_shipping)) {
                    $taxValue_shipping = ($shipping_cost * $taxRate_shipping);
                }
                $products[] = array(
                    "price" => isset($shipping_cost) ? $shipping_cost : null,
                    "quantity" => 1,
                    "sku" => "shipping",
                    'taxName' => isset($taxName_shipping) ? $taxName_shipping : null,
                    'taxId' => isset($taxId_shipping) ? $taxId_shipping : null,
                    'taxRate' => isset($taxRate_shipping) ? $taxRate_shipping : null,
                    'taxValue' => isset($taxValue_shipping) ? $taxValue_shipping : null
                );
            }
            // Getting Payment id from mapping varabile
            if (isset($_POST['_payment_method']) && !empty($_POST['_payment_method'])) {
                $wc_payment = get_option('wc_to_vend_payment');
                $total_payments = explode(",", $wc_payment);
                foreach ($total_payments as $mapped_payment) {
                    $exploded_mapped_payment = explode("|", $mapped_payment);
                    if (isset($exploded_mapped_payment[2]) && !empty($exploded_mapped_payment[2]) && isset($exploded_mapped_payment[0]) && !empty($exploded_mapped_payment[0])) {
                        if ($exploded_mapped_payment[2] == $_POST['_payment_method']) {
                            $vend_payment_data = explode("%%", $exploded_mapped_payment['0']);
                            if (isset($vend_payment_data[0])) {
                                $payment_method = $vend_payment_data[0];
                            }
                            if (isset($vend_payment_data[1])) {
                                $payment_method_id = $vend_payment_data[1];
                            }
                            break;
                        }
                    }
                }
                $payment = array(
                    "retailer_payment_type_id" => isset($payment_method_id) ? $payment_method_id : null,
                    "amount" => isset($get_total) ? $get_total : 0,
                    "method" => isset($payment_method) ? $payment_method : null,
                    "transactionNumber" => isset($_POST['_transaction_id']) ? $_POST['_transaction_id'] : null,
                );
            }

            //UTC Time
            date_default_timezone_set("UTC");
            $created = date("Y-m-d H:i:s", time());
            $export_user_details = get_option('wc_to_vend_export');
            if (isset($export_user_details) && !empty($export_user_details)) {
                if ($export_user_details == 'customer') {
                    if (isset($_POST['customer_user']) && !empty($_POST['customer_user'])) {
                        $select_user = $wpdb->get_results($wpdb->prepare('SELECT user_email FROM `' . $wpdb->prefix . 'users` WHERE `ID` = %d ', $_POST['customer_user']), ARRAY_A);
                        if (0 != $wpdb->num_rows) {
                            $customer_detail = $select_user[0];
                            $primary_email_address = $customer_detail['user_email'];
                        }
                    }

                    $primary_email = isset($primary_email_address) ? $primary_email_address : $_POST['_billing_email'];
                }
            }
            $OrderArray = array(
                'uid' => isset($vend_uid) ? $vend_uid : null,
                "orderId" => isset($order_no) ? $order_no : null,
                "source" => "WooCommerce",
                'user_name' => isset($vend_username) ? $vend_username : null,
                'created' => (isset($created)) ? $created : null,
                'register_id' => isset($registerDb) ? $registerDb : null,
                'primary_email' => isset($primary_email) ? $primary_email : null,
                'total' => isset($order_total) ? $order_total : 0,
                'total_tax' => isset($total_tax) ? $total_tax : 0,
                'taxes_included' => $taxes_included,
                'currency' => isset($currency) ? $currency : 'USD',
                'shipping_method' => isset($shipping_method) ? $shipping_method : null,
                'payment' => (isset($payment) && !empty($payment)) ? $payment : null,
                'products' => (isset($products) && !empty($products)) ? $products : null,
                'payment_type_id' => isset($payment_method_id) ? $payment_method_id : null,
            );

            if (isset($export_user_details) && !empty($export_user_details)) {
                if ($export_user_details == 'customer') {
                    if (isset($_POST['_billing_first_name']))
                        $OrderArray['billingAddress']['firstName'] = $_POST['_billing_first_name'];

                    if (isset($_POST['_billing_last_name']))
                        $OrderArray['billingAddress']['lastName'] = $_POST['_billing_last_name'];

                    if (isset($_POST['_billing_company'])) {
                        $OrderArray['billingAddress']['company'] = $_POST['_billing_company'];
                    };
                    if (isset($_POST['_billing_address_1']))
                        $OrderArray['billingAddress']['street1'] = $_POST['_billing_address_1'];

                    if (isset($_POST['_billing_address_2']))
                        $OrderArray['billingAddress']['street2'] = $_POST['_billing_address_2'];

                    if (isset($_POST['_billing_city']))
                        $OrderArray['billingAddress']['city'] = $_POST['_billing_city'];

                    if (isset($_POST['_billing_postcode']))
                        $OrderArray['billingAddress']['postalCode'] = $_POST['_billing_postcode'];

                    if (isset($_POST['_billing_country']))
                        $OrderArray['billingAddress']['country'] = $_POST['_billing_country'];

                    if (isset($_POST['_billing_state']))
                        $OrderArray['billingAddress']['state'] = $_POST['_billing_state'];

                    if (isset($_POST['_billing_phone']))
                        $OrderArray['billingAddress']['phone'] = $_POST['_billing_phone'];

                    if (isset($_POST['_shipping_first_name']))
                        $OrderArray['deliveryAddress']['firstName'] = $_POST['_shipping_first_name'];

                    if (isset($_POST['_shipping_last_name']))
                        $OrderArray['deliveryAddress']['lastName'] = $_POST['_shipping_last_name'];

                    if (isset($_POST['_shipping_company']))
                        $OrderArray['deliveryAddress']['company'] = $_POST['_shipping_company'];

                    if (isset($_POST['_shipping_address_1']))
                        $OrderArray['deliveryAddress']['street1'] = $_POST['_shipping_address_1'];

                    if (isset($_POST['_shipping_address_2']))
                        $OrderArray['deliveryAddress']['street2'] = $_POST['_shipping_address_2'];

                    if (isset($_POST['_shipping_city']))
                        $OrderArray['deliveryAddress']['city'] = $_POST['_shipping_city'];

                    if (isset($_POST['_shipping_postcode']))
                        $OrderArray['deliveryAddress']['postalCode'] = $_POST['_shipping_postcode'];

                    if (isset($_POST['_shipping_country']))
                        $OrderArray['deliveryAddress']['country'] = $_POST['_shipping_country'];

                    if (isset($_POST['_shipping_state']))
                        $OrderArray['deliveryAddress']['state'] = $_POST['_shipping_state'];

                    if (isset($_POST['_shipping_phone']))
                        $OrderArray['deliveryAddress']['phone'] = $_POST['_shipping_phone'];
                    if (isset($_POST['excerpt']))
                        $OrderArray['comments'] = $_POST['excerpt'];
                }
            }

            $json = json_encode($OrderArray);
            $order_sent = $apicall->linksync_postOrder($json);
            $devLogMessage = '<b>Woocommerce order to vend '.$orderId.'<b><br/>';
            $devLogMessage .= 'Json being sent: <br/><textarea>'.$json.'</textarea><br/>';
            $devLogMessage .= 'Response from LWS: <pre>'.json_encode($order_sent).'</pre>';
            LSC_Log::add_dev_success('linksync_OrderFromBackEnd', $devLogMessage);
            if (!empty($order_sent['orderId'])) {
                update_post_meta($orderId, '_ls_vend_order_id', $order_sent['orderId']);
                $note = sprintf( __( 'This Order was exported to Vend with the Receipt Number %s', 'woocommerce' ), $order_sent['orderId'] );
                $order->add_order_note($note);
                LSC_Log::add('Order Sync Woo to Vend', 'success', 'Woo Order no:' . $order_no, $LAIDKey);
            }

        } else {
            LSC_Log::add('Order Sync Woo to Vend', 'Error', 'Already Sent Order '.$orderId, $LAIDKey);
        }
    }
}

function post_unpublished($new_status, $old_status, $post)
{
    if ($post->post_type == 'shop_order') {
        if ($new_status == get_option('order_status_wc_to_vend')) {
            orderpost($post->ID);
        }
    }
}

function orderpost($orderId)
{
    global $wpdb;
    $testMode = get_option('linksync_test');
    $LAIDKey = get_option('linksync_laid');
    $apicall = new linksync_class($LAIDKey, $testMode);
    //Checking for already sent Order
    $vendOrderId = get_post_meta($orderId, '_ls_vend_order_id', true);
    //Check if it has been sent to lws and was already created in vend
    if (empty($vendOrderId)) {
        $order = new WC_Order($orderId);
        if ($order->post_status == get_option('order_status_wc_to_vend')) {
            $order_no = $order->get_order_number();
            if (strpos($order_no, '#') !== false) {
                $order_no = str_replace('#', '', $order_no);
            }
            $get_total = $order->get_total();
            $get_user = $order->get_user();
            $comments = $order->post->post_excerpt;
            $primary_email_address = isset($get_user->data->user_email) ? $get_user->data->user_email : '';
            $currency = $order->get_order_currency();
            $shipping_method = $order->get_shipping_method();
            $order_total = $order->get_order_item_totals();
            $transaction_id = $order->get_transaction_id();
            $taxes_included = false;
            $total_discount = $order->get_total_discount();
            $total_quantity = 0;
            $registerDb = get_option('wc_to_vend_register');
            $vend_uid = get_option('wc_to_vend_user');
            $total_tax = $order->get_total_tax();
            // Geting Payment object details
            if (isset($order_total['payment_method']['value']) && !empty($order_total['payment_method']['value'])) {
                $wc_payment = get_option('wc_to_vend_payment');
                if (isset($wc_payment) && !empty($wc_payment)) {
                    $total_payments = explode(",", $wc_payment);
                    foreach ($total_payments as $mapped_payment) {
                        $exploded_mapped_payment = explode("|", $mapped_payment);
                        if (isset($exploded_mapped_payment[1]) && !empty($exploded_mapped_payment[1]) && isset($exploded_mapped_payment[0]) && !empty($exploded_mapped_payment[0])) {
                            if ($exploded_mapped_payment[1] == $order_total['payment_method']['value']) {
                                $vend_payment_data = explode("%%", $exploded_mapped_payment[0]);
                                if (isset($vend_payment_data[0])) {
                                    $payment_method = $vend_payment_data[0];
                                }
                                if (isset($vend_payment_data[1])) {
                                    $payment_method_id = $vend_payment_data[1];
                                }
                                break;
                            }
                        }
                    }
                }

                $payment = array(
                    "retailer_payment_type_id" => isset($payment_method_id) ? $payment_method_id : null,
                    "amount" => isset($get_total) ? $get_total : 0,
                    "method" => isset($payment_method) ? $payment_method : null,
                    "transactionNumber" => isset($transaction_id) ? $transaction_id : null,
                );
            }
            $export_user_details = get_option('wc_to_vend_export');
            if (isset($export_user_details) && !empty($export_user_details)) {
                if ($export_user_details == 'customer') {
                    //woocommerce filter
                    $billingAddress_filter = apply_filters('woocommerce_order_formatted_billing_address', array(
                        'firstName' => $order->billing_first_name,
                        'lastName' => $order->billing_last_name,
                        'phone' => $order->billing_phone,
                        'street1' => $order->billing_address_1,
                        'street2' => $order->billing_address_2,
                        'city' => $order->billing_city,
                        'state' => $order->billing_state,
                        'postalCode' => $order->billing_postcode,
                        'country' => $order->billing_country,
                        'company' => $order->billing_company,
                        'email_address' => $order->billing_email
                    ), $order);

                    $billingAddress = array(
                        'firstName' => $billingAddress_filter['firstName'],
                        'lastName' => $billingAddress_filter['lastName'],
                        'phone' => $billingAddress_filter['phone'],
                        'street1' => $billingAddress_filter['street1'],
                        'street2' => $billingAddress_filter['street2'],
                        'city' => $billingAddress_filter['city'],
                        'state' => $billingAddress_filter['state'],
                        'postalCode' => $billingAddress_filter['postalCode'],
                        'country' => $billingAddress_filter['country'],
                        'company' => $billingAddress_filter['company'],
                        'email_address' => $billingAddress_filter['email_address']
                    );

                    $deliveryAddress_filter = apply_filters('woocommerce_order_formatted_shipping_address', array(
                        'firstName' => $order->shipping_first_name,
                        'lastName' => $order->shipping_last_name,
                        'phone' => $order->shipping_phone,
                        'street1' => $order->shipping_address_1,
                        'street2' => $order->shipping_address_2,
                        'city' => $order->shipping_city,
                        'state' => $order->shipping_state,
                        'postalCode' => $order->shipping_postcode,
                        'country' => $order->shipping_country,
                        'company' => $order->shipping_company,
                    ), $order);
                    $deliveryAddress = array(
                        'firstName' => $deliveryAddress_filter['firstName'],
                        'lastName' => $deliveryAddress_filter['lastName'],
                        'phone' => $deliveryAddress_filter['phone'],
                        'street1' => $deliveryAddress_filter['street1'],
                        'street2' => $deliveryAddress_filter['street2'],
                        'city' => $deliveryAddress_filter['city'],
                        'state' => $deliveryAddress_filter['state'],
                        'postalCode' => $deliveryAddress_filter['postalCode'],
                        'country' => $deliveryAddress_filter['country'],
                        'company' => $deliveryAddress_filter['company']
                    );
                    $primary_email = !empty($primary_email_address) ? $primary_email_address : $billingAddress['email_address'];
                    unset($billingAddress['email_address']);
                }
            }

            $vend_user_detail = get_option('wc_to_vend_user');
            if (isset($vend_user_detail) && !empty($vend_user_detail)) {
                $user = explode('|', $vend_user_detail);
                $vend_uid = isset($user[0]) ? $user[0] : null;
                $vend_username = isset($user[1]) ? $user[1] : null;
            }
            //Ordered product(s)
            $items = $order->get_items();
            $taxes = $order->get_taxes();
            foreach ($items as $item) {
                foreach ($taxes as $tax_label) {
                    $sql_query = "SELECT  tax_rate_id FROM  `" . $wpdb->prefix . "woocommerce_tax_rates` WHERE  tax_rate_name= %s  AND tax_rate_class= %s ";
                    $sql = $wpdb->get_results($wpdb->prepare($sql_query, $tax_label['label'], $item['item_meta']['_tax_class'][0]), ARRAY_A);
                    if (0 != $wpdb->num_rows) {
                        $tax_classes = linksync_tax_classes($tax_label['label'], $item['item_meta']['_tax_class'][0]);
                        if ($tax_classes['result'] == 'success') {
                            $vend_taxes = explode('/', $tax_classes['tax_classes']);
                        }
                    }
                }
                $taxId = isset($vend_taxes[0]) ? $vend_taxes[0] : null;
                $taxName = isset($vend_taxes[1]) ? $vend_taxes[1] : null;
                $taxRate = isset($vend_taxes[2]) ? $vend_taxes[2] : null;
                if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                    $product_id = $item['variation_id'];
                } else {
                    $product_id = $item['product_id'];
                }
                $pro_object = new WC_Product($product_id);
                $itemtotal = (float)$item['item_meta']['_line_subtotal'][0];
                if (isset($item['line_subtotal']) && !empty($item['line_subtotal'])) {
                    $product_amount = (float)($item['line_subtotal'] / $item['qty']);
                }
                $discount = (float)$item['item_meta']['_line_subtotal'][0] - (float)$item['item_meta']['_line_total'][0];
                if (isset($discount) && !empty($discount)) {
                    $discount = (float)($discount / $item['qty']);
                }
                #---------Changes--------#
                //Product Amount = product org amount - discount amount
                $product_total_amount = (float)$product_amount - (float)$discount;
                $product_sku = $pro_object->get_sku();
                if (isset($product_total_amount) && isset($taxRate) && !empty($product_total_amount) && !empty($taxRate)) {
                    $taxValue = ($product_total_amount * $taxRate);
                }

                $products[] = array(
                    'sku' => $product_sku,
                    'title' => $item['name'],
                    'price' => $product_total_amount,
                    'quantity' => $item['qty'],
                    'discountAmount' => isset($discount) ? $discount : 0, 'taxName' => isset($taxName) ? $taxName : null,
                    'taxId' => isset($taxId) ? $taxId : null,
                    'taxRate' => isset($taxRate) ? $taxRate : null,
                    'taxValue' => isset($taxValue) ? $taxValue : null,
                    'discountTitle' => isset($discountTitle) ? $discountTitle : 'sale',
                );
                $total_quantity += $item['qty'];
                unset($taxId);
                unset($taxName);
                unset($taxRate);
                unset($taxValue);
            }
            #----------Shipping------------#
            foreach ($taxes as $tax_label) {
                if (isset($tax_label['shipping_tax_amount']) && !empty($tax_label['shipping_tax_amount'])) {
                    $tax_classes = linksync_tax_classes($tax_label['label'], $item['item_meta']['_tax_class'][0]);
                    if ($tax_classes['result'] == 'success') {
                        $vend_taxes = explode('/', $tax_classes['tax_classes']);
                        $taxId_shipping = isset($vend_taxes[0]) ? $vend_taxes[0] : null;
                        $taxName_shipping = isset($vend_taxes[1]) ? $vend_taxes[1] : null;
                        $taxRate_shipping = isset($vend_taxes[2]) ? $vend_taxes[2] : null;
                    }
                }
            }

            if (isset($shipping_method) && !empty($shipping_method)) {
                $shipping_cost = $order->get_total_shipping();
                $shipping_with_tax = $order->get_shipping_tax();
                if ($shipping_with_tax > 0) {
                    if (isset($shipping_cost) && isset($taxRate_shipping) && !empty($shipping_cost) && !empty($taxRate_shipping)) {
                        $taxValue_shipping = ($shipping_cost * $taxRate_shipping);
                    }
                }
                $products[] = array(
                    "price" => isset($shipping_cost) ? $shipping_cost : null,
                    "quantity" => 1,
                    "sku" => "shipping",
                    'taxName' => isset($taxName_shipping) ? $taxName_shipping : null,
                    'taxId' => isset($taxId_shipping) ? $taxId_shipping : null,
                    'taxRate' => isset($taxRate_shipping) ? $taxRate_shipping : null,
                    'taxValue' => isset($taxValue_shipping) ? $taxValue_shipping : null
                );
            }
            //UTC Time
            date_default_timezone_set("UTC");
            $order_created = date("Y-m-d H:i:s", time());
            $OrderArray = array(
                'uid' => isset($vend_uid) ? $vend_uid : null,
                'created' => (isset($order_created)) ? $order_created : null,
                "orderId" => isset($order_no) ? $order_no : null,
                "source" => "WooCommerce",
                'register_id' => isset($registerDb) ? $registerDb : null,
                'user_name' => isset($vend_username) ? $vend_username : null,
                'primary_email' => (isset($primary_email) && !empty($primary_email)) ? $primary_email : null,
                'total' => isset($get_total) ? $get_total : 0,
                'total_tax' => isset($total_tax) ? $total_tax : 0,
                'comments' => isset($comments) ? $comments : null,
                'taxes_included' => $taxes_included,
                'currency' => isset($currency) ? $currency : 'USD',
                'shipping_method' => isset($shipping_method) ? $shipping_method : null,
                'payment' => (isset($payment) && !empty($payment)) ? $payment : null,
                'products' => (isset($products) && !empty($products)) ? $products : null,
                'payment_type_id' => isset($payment_method_id) ? $payment_method_id : null,
                'billingAddress' => (isset($billingAddress) && !empty($billingAddress)) ? $billingAddress : null,
                'deliveryAddress' => (isset($deliveryAddress) && !empty($deliveryAddress)) ? $deliveryAddress : null,
            );
            $json = json_encode($OrderArray);
            $order_sent = $apicall->linksync_postOrder($json);
            $devLogMessage = '<b>Woocommerce order to Vend '.$orderId.'<b><br/>';
            $devLogMessage .= 'Json being sent: <br/><textarea>'.$json.'</textarea><br/>';
            $devLogMessage .= 'Response from LWS: <pre>'.json_encode($order_sent).'</pre>';
            LSC_Log::add_dev_success('linksync_OrderFromFrontEnd called orderpost', $devLogMessage);
            if (!empty($order_sent['orderId'])) {
                update_post_meta($orderId, '_ls_vend_order_id', $order_sent['orderId']);
                $note = sprintf( __( 'This Order was exported to Vend with the Receipt Number %s', 'woocommerce' ), $order_sent['orderId'] );
                $order->add_order_note($note);
                LSC_Log::add('Order Sync Woo to Vend', 'success', 'Woo Order no:' . $order_no, $LAIDKey);
            }
        }
    } else {
        LSC_Log::add('Order Sync Woo to Vend', 'Error', 'Already Sent Order '.$orderId, $LAIDKey);
    }

}

function order_product_post()
{
    global $wpdb;
    $testMode = get_option('linksync_test');
    $LAIDKey = get_option('linksync_laid');
    $apicall = new linksync_class($LAIDKey, $testMode);
    if (isset($_REQUEST['key']) && !empty($_REQUEST['key'])) {
        $orderId = wc_get_orderid_by_order_key($_REQUEST['key']);
    } else {
        $orderId = $_POST['ID'];
    }
    //Checking for already sent Order
    $vendOrderId = get_post_meta($orderId, '_ls_vend_order_id', true);

    if (empty($vendOrderId)) {

        $taxsetup = false;
        $product = array();
        $order = new WC_Order($orderId);
//Ordered product(s)
        $items = $order->get_items();
        foreach ($items as $item) {
            $product = array();
            $product_id = $item['product_id'];
            $sql_query = "SELECT post_status,post_content FROM `" . $wpdb->posts . "` WHERE ID= %d ";
            $query = $wpdb->get_results($wpdb->prepare($sql_query, $product_id), ARRAY_A);
            if (0 != $wpdb->num_rows) {
                $result = $query[0];
                if (isset($result) && !empty($result)) {
                    if ($result['post_status'] != 'trash') {
                        $post_detail = get_post_meta($product_id);
                        if (@empty($post_detail['_sku'][0])) {
                            $post_detail['_sku'][0] = 'sku_' . $product_id;
                        }
                        $post_detail['_sku'][0] = linksync_removespaces_sku_orderProduct($post_detail['_sku'][0]);
                        update_post_meta($product_id, '_sku', $post_detail['_sku'][0]);
                        $product['sku'] = html_entity_decode($post_detail['_sku'][0]); //SKU(unique Key)
                        //product status ->publish
                        $product['active'] = isset($result['post_status']) && $result['post_status'] == 'publish' ? 1 : 0;
                        // Price with Tax
                        $excluding_tax = ls_is_excluding_tax();

                        $display_retail_price_tax_inclusive = get_option('linksync_tax_inclusive');
                        if (get_option('ps_price') == 'on') {
                            if (isset($post_detail['_tax_status'][0]) && $post_detail['_tax_status'][0] == 'taxable') { # Product with TAX
                                $taxname = empty($post_detail['_tax_class'][0]) ? 'standard-tax' : $post_detail['_tax_class'][0];
                                $response_taxes = linksyn_get_tax_details($taxname);
                                // echo"<pre>"; print_r($response_taxes);
                                if ($response_taxes['result'] == 'success') {
                                    $product['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                                    $product['tax_rate'] = $response_taxes['data']['tax_rate'];
                                    $taxsetup = true;
                                }
                            }

                            if ($excluding_tax == 'on') {

                                # https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                                if ($taxsetup) {
                                    if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
//cost price:_regular_price
                                        $regular_price = (float)$post_detail['_regular_price'][0];
                                        // Get Tax_value
                                        $tax_rate = (float)$product['tax_rate'];
                                        $tax_value = (float)($regular_price * $tax_rate);
                                        /* For excluding tax (both Woo Tax Excluding and Vend Tax Excluding)
                                         * display_retail_price_tax_inclusive 1, sell_price = Woo Final price + tax
                                         * For display_retail_price_tax_inclusive 0, sell_price = Woo Final price
                                         */
                                        if ($display_retail_price_tax_inclusive == '1') {
                                            $price = $post_detail['_regular_price'][0] + $tax_value;
                                        } elseif ($display_retail_price_tax_inclusive == '0') {
                                            $price = $post_detail['_regular_price'][0];
                                        }
//sell price:_regular_price
                                        $product['sell_price'] = str_replace(',', '', $price);
                                        $product['list_price'] = str_replace(',', '', $price);
                                        $product['tax_value'] = $tax_value;
                                    }
                                } else {
                                    // excluding tax off and tax not enabled in woocomerce
                                    if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
//cost price:_regular_price
//sell price:_regular_price
                                        $product['sell_price'] = str_replace(',', '', $post_detail['_regular_price'][0]);
                                        $product['list_price'] = str_replace(',', '', $post_detail['_regular_price'][0]);
                                    }
                                }
                            } else {
                                // No effect on price
                                if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
                                    $regular_price = (float)$post_detail['_regular_price'][0];
//                                                 Get Tax_value
                                    $tax_rate = (float)$product['tax_rate'];
                                    $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                    if ($display_retail_price_tax_inclusive == '1') {

                                    } elseif ($display_retail_price_tax_inclusive == '0') {
                                        $post_detail['_regular_price'][0] = $post_detail['_regular_price'][0] - $tax_value;
                                    }
//sell price:_regular_price
                                    $product['sell_price'] = str_replace(',', '', $post_detail['_regular_price'][0]);
                                    $product['list_price'] = str_replace(',', '', $post_detail['_regular_price'][0]);
                                }
                            }
                        }


                        if (isset($post_detail['_stock_status'][0]) && $post_detail['_stock_status'][0] == 'instock') {
                            $product['quantity'] = isset($post_detail['_stock'][0]) ? $post_detail['_stock'][0] : 0;
                        }
                        #Name/Title Check
                        if (get_option('ps_name_title') == 'on') {
                            $product['name'] = html_entity_decode($item['name']);
                        }
                        #Description
                        if (get_option('ps_description') == 'on') {
                            $product['description'] = html_entity_decode($result['post_content']);
                        }
                        $product['includes_tax'] = (isset($post_detail['_tax_status'][0]) && $post_detail['_tax_status'][0] == 'taxable') ? true : false;

                        #---Outlet---Product----#
                        if (get_option('ps_quantity') == 'on') {
                            if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                $getoutlets = get_option('wc_to_vend_outlet_detail');
                                if (isset($getoutlets) && !empty($getoutlets)) {
                                    $outlet = explode('|', $getoutlets);
                                    if (isset($post_detail['_stock'][0]) && !empty($post_detail['_stock'][0])) {
                                        $product['outlets'] = array(array(
                                            'name' => html_entity_decode($outlet[0]),
                                            'quantity' => $post_detail['_stock'][0]
                                        ));
                                    } elseif (isset($post_detail['_stock'][0]) && 0 == $post_detail['_stock'][0]) {
                                        $product['outlets'] = array(array(
                                            'name' => html_entity_decode($outlet[0]),
                                            'quantity' => 0
                                        ));
                                    }
                                }
                            }
                        } else {
                            $product['outlets'] = array(array('quantity' => NULL));
                        }
                        #qunantity
//
                        #Tags
                        if (get_option('ps_tags') == 'on') {
                            //To get the Detail of the Tags and Category of the product using product id(Post ID)
                            $tags_query = "SELECT " . $wpdb->terms . ".name FROM `" . $wpdb->term_taxonomy . "` JOIN " . $wpdb->terms . " ON(" . $wpdb->terms . ".term_id=" . $wpdb->term_taxonomy . ".term_id)  JOIN " . $wpdb->term_relationships . " ON(" . $wpdb->term_relationships . ".term_taxonomy_id=" . $wpdb->term_taxonomy . ".term_taxonomy_id) WHERE " . $wpdb->term_taxonomy . ".taxonomy='product_tag' AND " . $wpdb->term_relationships . ".object_id= %d ";

                            $result_tags = $wpdb->get_results($wpdb->prepare($tags_query, $product_id), ARRAY_A);
//                    if (!$result_tags)
//                        die("Error In  Connection : " . mysql_error() . " Line No. " . __LINE__);
                            if (0 != $wpdb->num_rows) {
                                $tags_product_type = array();
                                foreach ($result_tags as $row_tags) {
                                    $tags_product_type[] = array(
                                        'name' => html_entity_decode($row_tags['name']));
                                }
                            }
                            if (isset($tags_product_type) && !empty($tags_product_type)) {
                                $product['tags'] = $tags_product_type;
                            }
                            //To free an array to use futher
                            unset($tags_product_type);
                        }

                        #brands
                        if (get_option('ps_brand') == 'on') {
                            //To get the Detail of the Tags and Category of the product using product id(Post ID)
                            $brands_query = "SELECT " . $wpdb->terms . ".name FROM `" . $wpdb->term_taxonomy . "` JOIN " . $wpdb->terms . " ON(" . $wpdb->terms . ".term_id=" . $wpdb->term_taxonomy . ".term_id)  JOIN " . $wpdb->term_relationships . " ON(" . $wpdb->term_relationships . ".term_taxonomy_id=" . $wpdb->term_taxonomy . ".term_taxonomy_id) WHERE " . $wpdb->term_taxonomy . ".`taxonomy`='product_brand' AND " . $wpdb->term_relationships . ".object_id= %d ";
                            $result_brands = $wpdb->get_results($wpdb->prepare($brands_query, $product_id), ARRAY_A);

                            if (0 != $wpdb->num_rows) {
                                foreach ($result_brands as $row_brands) {
                                    $brands[] = array(
                                        'name' => html_entity_decode($row_brands['name']));
                                }
                            }
                            if (!empty($brands)) {
                                $product['brands'] = $brands;
                            }
                            //To free an array to use futher
                            unset($brands);
                        }       #Variants product
                        $variants_data = get_posts(array(
                            'post_type' => 'product_variation',
                            'post_parent' => $product_id
                        ));

                        if (isset($variants_data) && !empty($variants_data)) {
                            $total_var_product = 0;
                            foreach ($variants_data as $variant_data) {
                                $option = array(
                                    1 => 'one',
                                    2 => 'two',
                                    3 => 'three',
                                    4 => 'four',
                                    5 => 'five',
                                    6 => 'six',
                                    7 => 'seven',
                                    8 => 'eight',
                                    9 => 'nine',
                                    10 => 'ten'
                                );
                                $variants_detail = get_post_meta($variant_data->ID);
                                if (@empty($variants_detail['_sku'][0])) {
                                    $variants_detail['_sku'][0] = 'sku_' . $variant_data->ID;
                                }
                                $variants_detail['_sku'][0] = linksync_removespaces_sku_orderProduct($variants_detail['_sku'][0]);
                                update_post_meta($variant_data->ID, '_sku', $variants_detail['_sku'][0]);
                                $variant['sku'] = html_entity_decode($variants_detail['_sku'][0]); //SKU(unique Key)
                                #Name/Title Check
                                if (get_option('ps_name_title') == 'on') {
                                    $variant['name'] = html_entity_decode($variant_data->post_title);
                                }

                                #quantity
                                if (@$variants_detail['_stock_status'][0] == 'instock') {
                                    $variant['quantity'] = @$variants_detail['_stock'][0];
                                }


                                // Price with Tax
                                if (get_option('ps_price') == 'on') {
                                    if (isset($variants_detail['_tax_status'][0]) && $variants_detail['_tax_status'][0] == 'taxable') { # Product with TAX
                                        $taxname = empty($variants_detail['_tax_class'][0]) ? 'standard-tax' : $variants_detail['_tax_class'][0];
                                        $response_taxes = linksyn_get_tax_details($taxname);
                                        if ($response_taxes['result'] == 'success') {
                                            $variant['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                                            $variant['tax_rate'] = $response_taxes['data']['tax_rate'];
                                            $taxsetup = true;
                                        }
                                    }

                                    if ($excluding_tax == 'on') {
                                        # https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                                        if ($taxsetup) {
                                            if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
//cost price:_regular_price
                                                $regular_price = (float)$variants_detail['_regular_price'][0];
                                                // Get Tax_value
                                                $tax_rate = (float)$variant['tax_rate'];
                                                $tax_value = (float)($regular_price * $tax_rate);
//sell price:_regular_price
                                                if ($display_retail_price_tax_inclusive == '1') {
                                                    $price_variant = $variants_detail['_regular_price'][0] + $tax_value;
                                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                                    $price_variant = $variants_detail['_regular_price'][0];
                                                }

                                                $variant['sell_price'] = str_replace(',', '', $price_variant);

                                                $variant['list_price'] = str_replace(',', '', $price_variant);
                                                $variant['tax_value'] = $tax_value;
                                            }
                                        } else {
                                            // excluding tax off and tax not enabled in woocomerce
                                            if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
//sell price:_regular_price
                                                $variant['sell_price'] = str_replace(',', '', $variants_detail['_regular_price'][0]);
                                                $variant['list_price'] = str_replace(',', '', $variants_detail['_regular_price'][0]);
                                            }
                                        }
                                    } else {
                                        // No effect on price
                                        if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
                                            $regular_price = (float)$variants_detail['_regular_price'][0];
//                                                 Get Tax_value
                                            $tax_rate = (float)$variant['tax_rate'];
                                            $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                            if ($display_retail_price_tax_inclusive == '1') {

                                            } elseif ($display_retail_price_tax_inclusive == '0') {
                                                $variants_detail['_regular_price'][0] = $variants_detail['_regular_price'][0] - $tax_value;
                                            }
//sell price:_regular_price
                                            $variant['sell_price'] = str_replace(',', '', $variants_detail['_regular_price'][0]);
                                            $variant['list_price'] = str_replace(',', '', $variants_detail['_regular_price'][0]);
                                            $variant['tax_value'] = $tax_value;
                                        }
                                    }
                                }
                                // ATTRIBUTE && VARIANTS
                                $sql_query = "SELECT * FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies`";
                                $attributes_select = $wpdb->get_results($sql_query, ARRAY_A);
                                $check = 1;
                                // $variants_detail['attribute_pa_' . strtolower($attributes['attribute_name'])][0]
                                if (0 != $wpdb->num_rows) {
                                    $keys = array_keys($variants_detail);
                                    if (false !== stripos(implode("\n", $keys), "attribute_pa_")) {
                                        foreach ($attributes_select as $attributes) {
                                            if (isset($variants_detail['attribute_pa_' . strtolower($attributes['attribute_name'])]) && !empty($variants_detail['attribute_pa_' . strtolower($attributes['attribute_name'])])) {
                                                $attribute_name = str_replace('pa_', '', $attributes['attribute_name']);
                                                $attribute_query = $wpdb->get_results($wpdb->prepare("SELECT attribute_label FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies` WHERE `attribute_name` = %s ", $attribute_name), ARRAY_A);
                                                if (0 != $wpdb->num_rows) {
                                                    $attribute_name_result = $attribute_query[0];
                                                    $name = $attribute_name_result['attribute_label'];
                                                }
                                                $variant['option_' . $option[$check] . '_name'] = isset($name) ? $name : '';
                                                $query = $wpdb->get_results($wpdb->prepare("SELECT name FROM `" . $wpdb->terms . "` WHERE `slug` = %s ", $variants_detail['attribute_pa_' . strtolower($attributes['attribute_name'])][0]), ARRAY_A);
                                                if (0 != $wpdb->num_rows) {
                                                    $attribute_value = $query[0];
                                                    $value = $attribute_value['name'];
                                                }
                                                $variant['option_' . $option[$check] . '_value'] = isset($value) ? $value : '';
                                                $check++;
                                            }
                                        }
                                    } else {
                                        if (isset($post_detail['_product_attributes'][0]) && !empty($post_detail['_product_attributes'][0])) {
                                            $_product_attributes = unserialize($post_detail['_product_attributes'][0]);
                                            foreach ($_product_attributes as $attribute_value) {
                                                $attributeName = $attribute_value['name'];
                                                $_attribute = explode('|', $attribute_value['value']);
                                                $value = trim($_attribute[$total_var_product]);
                                                $variant['option_' . $option[$check] . '_name'] = isset($attributeName) ? $attributeName : '';
                                                $variant['option_' . $option[$check] . '_value'] = isset($value) ? $value : '';
                                                $check++;
                                            }
                                        }
                                    }
                                } else {
                                    if (isset($post_detail['_product_attributes'][0]) && !empty($post_detail['_product_attributes'][0])) {
                                        $_product_attributes = unserialize($post_detail['_product_attributes'][0]);
                                        foreach ($_product_attributes as $attribute_value) {
                                            $attributeName = $attribute_value['name'];
                                            $_attribute = explode('|', $attribute_value['value']);
                                            $value = trim($_attribute[$total_var_product]);
                                            $variant['option_' . $option[$check] . '_name'] = isset($attributeName) ? $attributeName : '';
                                            $variant['option_' . $option[$check] . '_value'] = isset($value) ? $value : '';
                                            $check++;
                                        }
                                    }
                                }
                                #qunantity-----UPDATE--variant---
                                if (get_option('ps_quantity') == 'on') {
                                    if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                        $getoutlets = get_option('wc_to_vend_outlet_detail');
                                        if (isset($getoutlets) && !empty($getoutlets)) {
                                            $outlets = explode('|', $getoutlets);
                                            if (isset($variants_detail['_stock'][0]) && !empty($variants_detail['_stock'][0])) {
                                                $variant['outlets'] = array(array('name' => ($outlets[0]),
                                                    'quantity' => $variants_detail['_stock'][0]));
                                            } else {
                                                $variant['outlets'] = array(array('name' => html_entity_decode($outlets[0]),
                                                    'quantity' => NULL));
                                            }
                                        } else {
                                            $variant['outlets'] = NULL;
                                        }
                                    }
                                } else {
                                    $variant['outlets'] = array(array('quantity' => NULL));
                                }
                                $product['variants'][] = $variant;
                                $total_var_product++;
                            }
                        }
                        $data = json_encode($product);
                        $response = $apicall->linksync_postProduct($data);
                        LSC_Log::add('Product Sync Woo to Vend', 'success', 'Product synced SKU:' . $product['sku'], $LAIDKey);
                    }
                }
            }
        }
    }

}

function linksync_tax_classes($tax_name, $tax_class = NULL)
{
    if (empty($tax_class))
        $tax_class = 'standard-tax';

    $wc_taxes = get_option('wc_to_vend_tax');
    if (isset($wc_taxes) && !empty($wc_taxes)) {
        $explode_tax = explode(',', $wc_taxes);
        if (isset($explode_tax) && !empty($explode_tax)) {
            foreach ($explode_tax as $taxes) {
                $explode_taxes = explode('|', $taxes);
                if (isset($explode_taxes) && !empty($explode_taxes)) {
                    if (in_array($tax_name . '-' . $tax_class, $explode_taxes)) {
                        return array('result' => 'success', 'tax_classes' => $explode_taxes[0]);
                    }
                } else {
                    return array('result' => 'error', 'tax_classes' => NULL);
                }
            }
        }
    }
}

// Helper functions
function linksyn_get_tax_details($taxname)
{
    $result = array();
    $taxDb = get_option('tax_class');
    if (isset($taxDb) && !empty($taxDb)) {
        $tax_class = explode(",", $taxDb);
        foreach ($tax_class as $new) {
            $taxes = explode("|", $new);
            if (in_array($taxname, $taxes)) {
                $tax = explode("-", @$taxes[0]);
                $result['tax_rate'] = @$tax[1]; //tax_rate
                $result['tax_name'] = @$tax[0]; //tax_name
                return array('result' => 'success', 'data' => $result);
            }
        }
    }
    return array('result' => 'error', 'data' => 'no tax rule set');
}

function linksync_removespaces_sku_orderProduct($sku)
{
    if (isset($sku) && !empty($sku)) {
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

?>