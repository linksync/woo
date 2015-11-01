<?php

require(dirname(__FILE__) . '../../../../wp-config.php'); # WordPress Configuration File   
@mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
@mysql_select_db(DB_NAME);
include_once(dirname(__FILE__) . '/classes/Class.linksync.php'); # Class file having API Call functions 
global $wp;
// Initializing 
$wp->init();
$wp->parse_request();
$wp->query_posts();
$wp->register_globals();
if ($_POST['communication_key'] != get_option('webhook_url_code')) {
    die('Access is Denied');
}
$order_details=get_option('order_detail');
if (isset($order_details) && !empty($order_details)) {
    if (strpos($order_details, '|')) {
        $order_counts = explode('|', $order_details);
        echo $order_report = json_encode(array('total_product' => $order_counts[2] . ' of ' . $order_counts[0]));
    }else{
       echo $order_report = json_encode(array('total_product' => 'Starting....'));  
    }
}

$product_details = get_option('product_detail'); 
if (isset($product_details) && !empty($product_details)) {
    if (strpos($product_details, '|')) {
        $product_counts = explode('|', $product_details);
        echo $product_report = json_encode(array('total_product' => $product_counts[1] . ' of ' . $product_counts[0]));
    }else{
       echo $product_report = json_encode(array('total_product' => 'Starting....'));  
    }
}

//echo $product_report = json_encode(array('total_product' => get_option('product_detail') ));
exit;
?>