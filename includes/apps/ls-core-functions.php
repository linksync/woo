<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

/**
 * @return array|null|object
 */
function ls_get_woo_product_ids(){
	global $wpdb;

	$product_ids =	$wpdb->get_results("
						SELECT post.ID
						FROM $wpdb->posts AS post
						WHERE
							post.post_type IN('product','product_variation')
						ORDER BY post.ID ASC
						", ARRAY_A);

	if ( $product_ids ) return  $product_ids;

	return null;
}

/**
 * Get Product using the sku
 * @param $sku
 * @return int
 */
function ls_get_product_id_by_sku( $sku ){
	global $wpdb;

	$product_id =	$wpdb->get_var(
		$wpdb->prepare("
						SELECT post.ID
						FROM $wpdb->posts AS post
						INNER JOIN $wpdb->postmeta AS pmeta ON (post.ID = pmeta.post_id)
						WHERE
							pmeta.meta_key='_sku' AND
							pmeta.meta_value=%s AND
							post.post_type IN('product','product_variation')
						LIMIT 1"
			, $sku )
	);

	if ( $product_id ) return  $product_id;

	return null;
}

/**
 * @param $ls_order_id
 * @return null|int null or the linksync order id
 */
function ls_order_exist( $ls_order_id ){
	global $wpdb;

	$order_id =	$wpdb->get_var(
		$wpdb->prepare("
						SELECT post.ID
						FROM $wpdb->posts AS post
						INNER JOIN $wpdb->postmeta AS pmeta ON (post.ID = pmeta.post_id)
						WHERE
							pmeta.meta_key='ls_oid' AND
							pmeta.meta_value=%s AND
							post.post_type IN('shop_order')
						LIMIT 1"
			, $ls_order_id )
	);

	if ( $order_id ) return  $order_id;

	return false;
}