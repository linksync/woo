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
            $url = '';
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

        LS_Vend_Ajax::sync_triggered_by_lws();
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