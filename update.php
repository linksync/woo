<?php
/*
 * This File will be triggered by the Webhook Callback
 * 
 */

    require(dirname(__FILE__) . '../../../../wp-load.php'); # WordPress Load File
    $current_date_time_string = strtotime(date("Y-m-d H:i:s"));

    include_once(dirname(__FILE__) . '/classes/Class.linksync.php'); # Class file having API Call functions


    set_time_limit(0);
    if (!in_array('linksync/linksync.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        die('Access is denied');
    }

	$order_last_update_at = ls_last_order_update_at();
	$p_last_updated_at = ls_last_product_updated_at();

	if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		$lwsmessage = ' Last Product Sync To Woocommerce: '.$p_last_updated_at.'(from linksync)<br/> Last Order Sync to Woocommerce: '.$order_last_update_at.'(from linksync)';
		LSC_Log::add('Linksync Triggered a sync', 'success', $lwsmessage, '');
	}

    if (!isset($_REQUEST['c']) || @get_option('webhook_url_code') != $_REQUEST['c']) {
        LSC_Log::add('Webhook Triggered', 'error', 'Invalid Request', ''); # Error to be loggged
        die('Access is denied, Webhook is not the same');
    }
    $status = get_option('linksync_sycning_status');
    if (isset($status) && $status == 'running') {
        die('Access is denied, status running');
    }


    if (isset($_REQUEST['sendlog'])) {
        $fileName = LS_PLUGIN_DIR . '/classes/raw-log.txt';
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
                LSC_Log::add('Webhook Triggered', 'success', $response, ''); # success to be loggged
            } else {
                $response = "Error:Unable to Send Logs Details";
                LSC_Log::add('Webhook Triggered', 'error', $response, ''); # error to be loggged
            }
        }
    }
    $message['message'] = '';
    // Check for the Enable app(QuickBook,VEND,etc)
    if (get_option('linksync_connectionwith') == 'Vend' || get_option('linksync_connectedto') == 'Vend') {

		$LAIDKey = get_option('linksync_laid'); # Activated LAID KEY
		$testMode = get_option('linksync_test'); # TEST MODE CHECKS

		$apicall = new linksync_class($LAIDKey, $testMode);

		#-----First Checking For any order---------------#
		$time_offset = get_option('linksync_time_offset');
		if (isset($time_offset) && !empty($time_offset)) {
			$time = $current_date_time_string + $time_offset;
		} else {
			$time = $current_date_time_string; # UTC
		}
		//for since parameter in getting order and product
		$result_time = date("Y-m-d H:i:s", $time);

        $order_sync_type = get_option('order_sync_type');
        if (isset($order_sync_type) && $order_sync_type == 'vend_to_wc-way') {
            update_option('order_detail', '');
            #order update  Request time
            update_option('order_time_req', $result_time);
            $order_time_suc = get_option('order_time_suc'); # it has NULL or DATETIME
            if (!empty($order_time_suc)) {
                $url = 'since=' . urlencode($order_time_suc);
            }


			if( !empty($order_last_update_at) ){
				$url = 'since=' . urlencode($order_last_update_at);
			}

            $all_orders = $apicall->linksync_getOrder($url);
			$devLogMessage = 'Get order using url endpoint : '.$url.' <br/> Query Response: '.json_encode($all_orders);
			LSC_Log::add_dev_success('Vend to woocommerce order syncing',$devLogMessage);

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
				if( false != $p_last_updated_at ){
					$url.='since=' . urlencode($prod_update_suc);
				}else{
					$url.='since=' . urlencode($p_last_updated_at);
				}
            }
            if ($product_sync_type == 'vend_to_wc-way') { # if only select Vend to Woocomerce
                $price_book = get_option('price_book'); # it has NULL or DATETIME
                if (isset($price_book) && $price_book == 'on') {
                    $url.='pricebook=' . urlencode(get_option('price_book_identifier'));
                }
            }
            $current_user_id = get_current_user_id();

			$urli = rtrim($url, '&');
			$urli.='&page=';
			$page = 1;
			$last_left_out_page = get_option('prod_last_page');
			if (isset($last_left_out_page) && !empty($last_left_out_page)) {
				$page = $last_left_out_page;
			} else {
				$page = 1;
			}
            if ($current_user_id == 0) {
                // logged_one is 'System'
                do {
                    $requesturl = $urli . $page;
                    $products = $apicall->getProductWithParam($requesturl);
                    $devLogMessage = 'logged_one <br/>Get Product from vend using url endpoint : '.$requesturl.' <br/> Query Response: '.json_encode($products);
                    LSC_Log::add_dev_success('Product Syncing Vend to Woocommerce or Two way', $devLogMessage);
                    if (isset($products) && !empty($products)) {
                        if (!isset($products['errorCode'])) {
                            if (isset($products['products']) && !empty($products['products'])) {
                                $api_response = $apicall->importProductToWoocommerce($products);

								if (isset($api_response) && !empty($api_response)) {
									update_option('image_process', 'running');
									update_option('product_image_ids', $api_response);
								}else{
									update_option('image_process', 'complete');
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
                    LSC_Log::add('Product Sync Vend to Woo', 'success', $products['pagination']['results'] . ' Product(s) synced.', $LAIDKey);

                $message['message'].= 'Product Sync:Complete Successfully!!';
            } else {

                $requesturl = $urli . $page;
                $products = $apicall->getProductWithParam($requesturl);
                $devLogMessage = 'Get Product from vend using url endpoint : '.$requesturl.' <br/> Query Response: '.json_encode($products);
                LSC_Log::add_dev_success('Product Syncing Vend to Woocommerce or Two way', $devLogMessage);

                if (isset($products) && !empty($products)) {
                    if (!isset($products['errorCode'])) {
                        update_option('product_detail', $products['pagination']['results']);
                        $message['product_count'] = ($products['pagination']['page'] - 1) * 50;
                        if (isset($products['products']) && !empty($products['products'])) {
                            $api_response = $apicall->importProductToWoocommerce($products);
							//User Manually
							if (isset($api_response) && !empty($api_response)) {
								update_option('image_process', 'running');
								update_option('product_image_ids', $api_response);
							}else{
								update_option('image_process', 'complete');
							}
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
                                LSC_Log::add('Product Sync Vend to Woo', 'success', $products['pagination']['results'] . ' Product(s) synced.', $LAIDKey);
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
        } elseif ( !empty($product_sync_type) && 'wc_to_vend' == $product_sync_type){
			if (get_option('ps_quantity') == 'on') {

				$url = '';
				if (isset($result_time) && !empty($result_time)) {
					$url = 'since=' . urlencode($result_time);
				}
				$product_last_update = ls_last_product_updated_at();
				if( false != $product_last_update ){
					$url = 'since=' . urlencode($product_last_update);
				}

				$current_page = 1;
				$total_pages = 0;
				do{
					$url .='&page='.$current_page;
					$products = $apicall->getProductWithParam( $url );
					if( !empty($products) ){
						$current_page = $products['pagination']['page'];
						$total_pages = $products['pagination']['pages'];

						foreach( $products['products'] as $product ){

							if( isset($product['variants']) && !empty( $product['variants'] )){

								foreach( $product['variants'] as $pro_variant ){

									if( !empty($pro_variant['sku']) ){
										$product_id = ls_get_product_id_by_sku( $pro_variant['sku'] );
										$product_meta = new LS_Product_Meta($product_id);
										ls_last_product_updated_at($pro_variant['update_at']);
										$quantity = 0;
										if( !empty($pro_variant['outlets']) ){
											foreach( $pro_variant['outlets'] as $outlet ){
												if( !empty($outlet['quantity']) ){
													$quantity += (int)$outlet['quantity'];
												}
											}
										}

										$product_meta->update_stock( $quantity );
									}
								}
							}else{
								if( !empty($product['sku']) ){

									$product_id = ls_get_product_id_by_sku( $product['sku'] );
									$product_meta = new LS_Product_Meta($product_id);
									ls_last_product_updated_at($product['update_at']);
									$quantity = 0;

									if( !empty($product['outlets']) ){
										foreach( $product['outlets'] as $outlet ){
											if( !empty($outlet['quantity']) ){
												$quantity += (int)$outlet['quantity'];
											}
										}
									}

									$product_meta->update_stock( $quantity );
								}
							}
						}
						$current_page++;
					}

				}while( $current_page <= $total_pages );


			}
        }
    } else {
        LSC_Log::add('Webhook Triggered', 'error', 'Invalid Request', ''); # Error to be loggged
    }

if (isset($message['message']) && !empty($message['message'])) {
    update_option('product_detail', NULL);
    echo json_encode($message);
    exit;
}
exit;
?> 