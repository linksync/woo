<?php
/*
 * This File will be triggered by the Webhook Callback
 * 
 */
$fp = fopen(dirname(__FILE__) . '/update.php', "r+");
if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock  
    require(dirname(__FILE__) . '../../../../wp-load.php'); # WordPress Load File 
    $current_date_time_string = strtotime(date("Y-m-d H:i:s"));
// RE-CONNECT because it's wp set on mysqli
    @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    @mysql_select_db(DB_NAME);
    include_once(dirname(__FILE__) . '/classes/Class.linksync.php'); # Class file having API Call functions
    include_once(dirname(__FILE__) . '/classes/Class.linksync_QB.php'); # Class file having API Call functions
    global $wp;
// Initializing 
    $wp->init();
    $wp->parse_request();
    $wp->query_posts();
    $wp->register_globals();
    set_time_limit(0);
    if (!in_array('linksync/linksync.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        die('Access is denied');
    }
    if (!isset($_REQUEST['c']) || @get_option('webhook_url_code') != $_REQUEST['c']) {
        linksync_class::add('Webhook Triggered', 'error', 'Invalid Request', ''); # Error to be loggged 
        die('Access is denied');
    }
    $status = get_option('linksync_sycning_status');
    if (isset($status) && $status == 'running') {
        die('Access is denied');
    }

    $image_process = get_option('image_process');
    if (isset($image_process) && $image_process == 'running') {
        die('Access is denied');
    }

    if (isset($_REQUEST['sendlog'])) {
        $fileName = dirname(__FILE__) . '/classes/raw-log.txt';
        if (file_exists($fileName)) {
            $LAIDKey = get_option('linksync_laid'); # Activated LAID KEY
            $testMode = get_option('linksync_test'); # TEST MODE CHECKS 
// CREATING OBJECT OF MAIN CLASS
            $apicall = new linksync_class($LAIDKey, $testMode);
            $data = file_get_contents($fileName);
            $encoded_data = base64_encode($data);
            $result = array(
                "attachment" => $encoded_data
            );
            $json = json_encode($result);
            $apicall_result = $apicall->linksync_sendLog($json);
            if (isset($apicall_result['result']) && $apicall_result['result'] == 'success') {
                $response = 'Logs Sent Successfully !';
                linksync_class::add('Webhook Triggered', 'success', $response, ''); # success to be loggged 
            } else {
                $response = "Error:Unable to Send Logs Details";
                linksync_class::add('Webhook Triggered', 'error', $response, ''); # error to be loggged  
            }
        }
    }
    $message['message'] = '';
    // Check for the Enable app(QuickBook,VEND,etc)
    if (get_option('linksync_connectionwith') == 'Vend' || get_option('linksync_connectedto') == 'Vend') {

        $order_sync_type = get_option('order_sync_type');
        if (isset($order_sync_type) && $order_sync_type == 'vend_to_wc-way') {
            #-----End Checking valid Webhook Callback -----#
            $LAIDKey = get_option('linksync_laid'); # Activated LAID KEY
            $testMode = get_option('linksync_test'); # TEST MODE CHECKS
            update_option('order_detail', '');
// CREATING OBJECT OF MAIN CLASS
            $apicall = new linksync_class($LAIDKey, $testMode);
#-----First Checking For any order---------------#
            $time_offset = get_option('linksync_time_offset');
            if (isset($time_offset) && !empty($time_offset)) {
                $time = $current_date_time_string + $time_offset;
            } else {
                $time = $current_date_time_string; # UTC 
            }
            $result_time = date("Y-m-d H:i:s", $time);
            #order update  Request time
            update_option('order_time_req', $result_time);
            $order_time_suc = get_option('order_time_suc'); # it has NULL or DATETIME 
            if (isset($order_time_suc) && !empty($order_time_suc)) {
                $url = 'since=' . urlencode($order_time_suc);
            }
            $orderurl = "/api/v1/order/?";

            $all_orders = $apicall->linksync_getOrder($url);
            $orderurl.=isset($url) ? $url : '';
            if (isset($all_orders['pagination'])) {
                if (isset($all_orders['pagination']['results']) && $all_orders['pagination']['results'] != 0) {
                    $vc_to_woocommerce_orders = $apicall->importOrderToWoocommerce($all_orders);
                }
            }
            if (isset($vc_to_woocommerce_orders) && !empty($vc_to_woocommerce_orders)) {
                update_option('order_time_suc', get_option('order_time_req'));
            }
            $order_message = 'Order Sync: Complete Successfully!!<br>';
        }
#----------End of Order Import----------# 
        $product_sync_type = get_option('product_sync_type');
        if (isset($product_sync_type) && $product_sync_type == 'vend_to_wc-way' || $product_sync_type == 'two_way') { # IT WILL NOT PROCESS IF DISABLED 
            update_option('product_detail', NULL);
#-----End Checking valid Webhook Callback -----#
            $LAIDKey = get_option('linksync_laid'); # Activated LAID KEY
            $testMode = get_option('linksync_test'); # TEST MODE CHECKS
// CREATING OBJECT OF MAIN CLASS 

            $apicall = new linksync_class($LAIDKey, $testMode);
#-----First Checking For any order---------------#
            $time_offset = get_option('linksync_time_offset');
            if (isset($time_offset) && !empty($time_offset)) {
                $time = $current_date_time_string + $time_offset;
            } else {
                $time = $current_date_time_string; # UTC 
            }
            $result_time = date("Y-m-d H:i:s", $time);
#Product update  Request time
            update_option('prod_update_req', $result_time);

// Getting Product (GET) 
// GET PRODUCTS FROM REMOTE BUSINESS APP USING LWS 
#----------- Import by Selected Tags ----------- #
            $url = '';
            if (@get_option('ps_imp_by_tag') == 'on') {
                $import_tags = get_option('import_by_tags_list');
                $import_unserialize = unserialize($import_tags);
                $tags_import = explode('|', $import_unserialize);
                foreach ($tags_import as $value) {
                    $url.='tags=' . urlencode($value) . '&';
                }
            }

#----------- End Import by Selected Tags ----------- #
            if (@get_option('ps_outlet') == 'on') {
                if ($product_sync_type == 'vend_to_wc-way') {
                    $outletDb = get_option('ps_outlet_details');
                    if (!empty($outletDb)) {
                        $outletDb_arr = explode('|', $outletDb);
                        //    Outlets - use the 'outlet' parameter for the Product endpoint to request product 
                        foreach ($outletDb_arr as $outlet_name) {
                            $url.='outlet=' . urlencode($outlet_name) . '&';
                        }
                    }
                } elseif ($product_sync_type == 'two_way') {
                    if (get_option('ps_wc_to_vend_outlet') == 'on') {
                        $getoutlet = get_option('wc_to_vend_outlet_detail');
                        $outlet = explode('|', $getoutlet);
                        $url.='outlet=' . urlencode(isset($outlet[1]) ? $outlet[1] : '') . '&';
                    }
                }
            }
//Get Product using the 'since' parameter
            $prod_update_suc = get_option('prod_update_suc'); # it has NULL or DATETIME 
            // echo $prod_update_suc;echo "<br>";exit;
            if (isset($prod_update_suc) && !empty($prod_update_suc)) {
                $url.='since=' . urlencode($prod_update_suc);
            }
            if ($product_sync_type == 'vend_to_wc-way') { # if only select Vend to Woocomerce
                $price_book = get_option('price_book'); # it has NULL or DATETIME 
                if (isset($price_book) && $price_book == 'on') {
                    $url.='pricebook=' . urlencode(get_option('price_book_identifier'));
                }
            }
            $current_user_id = get_current_user_id();
            if ($current_user_id == 0) {
                // logged_one is 'System'  
                $urli = rtrim($url, '&');
                $urli.='&page=';
                $page = 1;
                $last_left_out_page = get_option('prod_last_page');
                if (isset($last_left_out_page) && !empty($last_left_out_page)) {
                    $page = $last_left_out_page;
                } else {
                    $page = 1;
                }
                do {
                    $requesturl = $urli . $page;
                    $products = $apicall->getProductWithParam($requesturl);
                    if (isset($products) && !empty($products)) {
                        if (!isset($products['errorCode'])) {
                            if (isset($products['products']) && !empty($products['products'])) {
                                $api_response = $apicall->importProductToWoocommerce($products);
                            }
                            $page++;
                            $products['pagination']['page']++;
                            update_option('prod_last_page', $page);
                        }
                    }
                } while (@$products['pagination']['page'] <= @$products['pagination']['pages']);
                $last_left_out_page = get_option('prod_last_page');
                if (isset($products['pagination']['pages'])) {
                    if (($products['pagination']['pages'] <= ($last_left_out_page - 1))) {

                        if (isset($products['pagination']['results']) && $products['pagination']['results'] != 0) {
                            update_option('prod_update_suc', get_option('prod_update_req'));
                        }
                        update_option('prod_last_page', NULL);
                        update_option('product_detail', NULL);
                    }
                }
                if (isset($products['errorCode']) || !isset($products) || empty($products)) {
                    update_option('prod_update_suc', get_option('prod_update_suc'));
                }
                if (isset($products['pagination']['results']) && $products['pagination']['results'] != 0)
                    linksync_class::add('Product Sync Vend to Woo', 'success', $products['pagination']['results'] . ' Product(s) synced.', $LAIDKey);

                $message['message'].= 'Product Sync:Complete Successfully!!';
            } else {
                $urli = rtrim($url, '&');
                $urli.='&page=';
                $page = 1;
                $last_left_out_page = get_option('prod_last_page');
                if (isset($last_left_out_page) && !empty($last_left_out_page)) {
                    $page = $last_left_out_page;
                } else {
                    $page = 1;
                }
                $requesturl = $urli . $page;
                $products = $apicall->getProductWithParam($requesturl);
                if (isset($products) && !empty($products)) {
                    if (!isset($products['errorCode'])) {
                        update_option('product_detail', $products['pagination']['results']);
                        $message['product_count'] = ($products['pagination']['page'] - 1) * 50;
                        if (isset($products['products']) && !empty($products['products'])) {
                            $api_response = $apicall->importProductToWoocommerce($products);
                            $current_user_id = get_current_user_id();
                            if ($current_user_id == 0) {
                                // logged_one is 'System'  
                            } else {
                                //User Manually
                                if (isset($api_response) && !empty($api_response)) {
                                    update_option('image_process', 'running');
                                    update_option('product_image_ids', $api_response);
                                }else{
                                    update_option('image_process', 'complete');
                                }
                            }
                            //    echo $product_report = json_encode(array('total_product' => $products['pagination']['results'] . '( ' . $page . 'x' . $products['pagination']['pages'] . ')', 'request_url' => urldecode($requesturl), 'test' => $api_response));
                        }
                        $page++;
                        $products['pagination']['page']++;
                        update_option('prod_last_page', $page);
                    }
                }
                if (get_option('image_process') == 'complete') {
                    $last_left_out_page = get_option('prod_last_page');
                    if (isset($products['pagination']['pages'])) {
                        if (($products['pagination']['pages'] <= ($last_left_out_page - 1))) {

                            if (isset($products['pagination']['results']) && $products['pagination']['results'] != 0) {
                                update_option('prod_update_suc', get_option('prod_update_req'));
                            }
                            update_option('prod_last_page', NULL);
                            if (isset($products['pagination']['results']) && $products['pagination']['results'] != 0)
                                linksync_class::add('Product Sync Vend to Woo', 'success', $products['pagination']['results'] . ' Product(s) synced.', $LAIDKey);
                            $message['message'] = isset($order_message) ? $order_message . 'Product Sync:Complete Successfully!!' : 'Product Sync:Complete Successfully!!';
                        }elseif (isset($api_response) && !empty($api_response)) { 
                            $message['image_process'] = 'running';
                            echo json_encode($message);
                        }else {
                            $message['image_process'] = 'complete';
                            echo json_encode($message);
                        }
                    }
                    if (isset($products['errorCode']) || !isset($products) || empty($products)) {
                        update_option('prod_update_suc', get_option('prod_update_suc'));
                    }
                } else {
                    $message['image_process'] = 'running';
                    echo json_encode($message);
                }
            }
        }
    } elseif (get_option('linksync_connectionwith') == 'QuickBooks Online' || get_option('linksync_connectedto') == 'QuickBooks Online') {
        $product_sync_type_QBO = get_option('product_sync_type_QBO');
        /*
         * QuickBooks Online 
         */ if (isset($product_sync_type_QBO) && $product_sync_type_QBO == 'QB_to_wc-way' || $product_sync_type_QBO == 'two_way') { # IT WILL NOT PROCESS IF DISABLED 
            #-----End Checking valid Webhook Callback -----#
            $LAIDKey = get_option('linksync_laid'); # Activated LAID KEY
            $testMode = get_option('linksync_test'); # TEST MODE CHECKS
// CREATING OBJECT OF MAIN CLASS 

            $apicall = new linksync_class_QB($LAIDKey, $testMode);
#-----First Checking For any order---------------#
            $time_offset = get_option('linksync_time_offset');
            if (isset($time_offset) && !empty($time_offset)) {
                $time = $current_date_time_string + $time_offset;
            } else {
                $time = $current_date_time_string; # UTC 
            }
            $result_time = date("Y-m-d H:i:s", $time);
#Product update  Request time
            update_option('prod_update_req', $result_time);

// Getting Product (GET) 
// GET PRODUCTS FROM REMOTE BUSINESS APP USING LWS  
            $url = '';
//Get Product using the 'since' parameter
            $prod_update_suc = get_option('prod_update_suc'); # it has NULL or DATETIME  
            if (isset($prod_update_suc) && !empty($prod_update_suc)) {
                $url.='since=' . urlencode($prod_update_suc);
            }
            $urli = rtrim($url, '&');
            $urli.='&page=';
            $page = 1;
            $last_left_out_page = get_option('prod_last_page');
            if (isset($last_left_out_page) && !empty($last_left_out_page)) {
                $page = $last_left_out_page;
            } else {
                $page = 1;
            }
            do {
                $requesturl = $urli . $page;
                $products = $apicall->getProductWithParam($requesturl);
                if (isset($products) && !empty($products)) {
                    if (!isset($products['errorCode'])) {
                        if (isset($products['products']) && !empty($products['products'])) {
                            $product_details = get_option('product_detail');
                            if (strpos($product_details, '|')) {
                                $result = explode('|', $product_details);
                            } else {
                                $result[1] = 1;
                            }
                            foreach ($products['products'] as $product) {
                                update_option('product_detail', $products['pagination']['results'] . '|' . $result[1]);
                                $api_response = $apicall->importProductToWoocommerce_QBO($product);
                                $result[1]++;
                            }
                        }
                        $page++;
                        $products['pagination']['page']++;
                        update_option('prod_last_page', $page);
                    }
                }
            } while (@$products['pagination']['page'] <= @$products['pagination']['pages']);
            $last_left_out_page = get_option('prod_last_page');
            if (isset($products['pagination']['pages'])) {
                if (($products['pagination']['pages'] <= ($last_left_out_page - 1))) {

                    if (isset($products['pagination']['results']) && $products['pagination']['results'] != 0) {
                        update_option('prod_update_suc', get_option('prod_update_req'));
                    }
                    update_option('prod_last_page', NULL);
                    update_option('product_detail', NULL);
                }
            }
            if (isset($products['errorCode']) || !isset($products) || empty($products)) {
                update_option('prod_update_suc', get_option('prod_update_suc'));
            }
            if (isset($products['pagination']['results']) && $products['pagination']['results'] != 0)
                linksync_class::add('Product Sync Vend to Woo', 'success', $products['pagination']['results'] . ' Product(s) synced.', $LAIDKey);

            $message['message'].= 'Product Sync:Complete Successfully!!';
        } else {
            $message['message'].= '<span style="color:#d54e21;">Product Sync has been Disabled Or Not Selected</span>';
        }
    } else {
        linksync_class::add('Webhook Triggered', 'error', 'Invalid Request', ''); # Error to be loggged 
    }
    fflush($fp);            // flush output before releasing the lock 
    flock($fp, LOCK_UN);    // release the lock
} else {
    echo "Couldn't get the lock!";
}
if (isset($message['message']) && !empty($message['message'])) {
    update_option('product_detail', NULL);
    echo json_encode($message);
    exit;
}
exit;
fclose($fp);
?> 