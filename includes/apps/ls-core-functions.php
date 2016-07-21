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
 * Returns all the product variation ids base on main product id
 *
 * @param $product_id
 * @return array|null
 */
function ls_get_product_variant_ids( $product_id ){
	global $wpdb;
	$var_ids = $wpdb->get_results(
		$wpdb->prepare("SELECT ID
                        FROM " . $wpdb->posts . "
                        WHERE
                             post_type='product_variation' AND
                             post_parent= %d ", $product_id )
		, ARRAY_A);

	if( !empty($var_ids) ){
		return $var_ids;
	}
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

/**
 * Get term by name and there is no need to escape the name because it has been catered already unlike get_term_by
 *
 * @param $name                        String value of the Term
 * @param int|null $parent             Parent of the term, parent will not be added to the query if parent is null
 * @param string $taxonomy             Terms taxonomy name, product_cat is the default
 * @return false|object|void
 */
function ls_get_term_by_name( $name, $parent = null, $taxonomy = 'product_cat'  ){
	global $wpdb;

	//Escape the name value
	$name = ls_specialchars( $name, ENT_COMPAT );

	$tax_clause = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );

	$parent_clause = '';
	if( null != $parent){
		$parent_clause = $wpdb->prepare( "AND tt.parent = %d", $parent );
	}

	$term = $wpdb->get_row(
		$wpdb->prepare( "
				SELECT t.*, tt.* FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt
				ON t.term_id = tt.term_id
				WHERE t.name = %s "
			, $name
		) . " $parent_clause $tax_clause LIMIT 1" );

	if ( ! $term )
		return false;

	return $term;
}

/**
 * uses _wp_specialchars but we nee to use ENT_COMPAT for our use case
 * @param $string
 * @param int $quote_style
 * @return string
 */
function ls_specialchars( $string, $quote_style = ENT_NOQUOTES ){
	return _wp_specialchars( $string, $quote_style );
}

/**
 * Uses wp_specialchars_decode
 *
 * @param $string
 * @param int $quote_style
 * @return string
 */
function ls_speciachars_decode( $string, $quote_style = ENT_NOQUOTES ){
	return wp_specialchars_decode( $string, $quote_style );
}


/**
 * @param int $object_id      Product ID
 * @param string $taxonomy    Product taxonomy either product_tag, product_cat or product_brand
 * @param string|array $args {
 *     Array of argument.
 *     @type string $orderby                Field by which results should be sorted. Accepts 'name', 'count', 'slug',
 *                                          'term_group', 'term_order', 'taxonomy', 'parent', or 'term_taxonomy_id'.
 *                                          Default 'name'.
 *     @type string $order                  Sort order. Accepts 'ASC' or 'DESC'. Default 'ASC'.
 *     @type string $fields                 Fields to return for matched terms. Accepts 'tt_ids','term_id', 'name', 'slug' and 'term_group'.
 * }
 *   For string it will only accept 'all'
 * @return array|WP_Error The requested term data or empty array if no terms found.
 *                        WP_Error if any of the $taxonomies don't exist.
 */
function ls_get_object_terms( $object_id, $taxonomy = 'product_tag', $args = array() ) {
	if ( empty( $object_id ) || empty( $taxonomy ) )
		return array();

	global $wpdb;

	$object_id = (int) $object_id;

	$defaults = array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'fields'  => 'name'
	);

	$args = wp_parse_args( $args, $defaults );

	$orderby = $args['orderby'];
	$order = $args['order'];
	$fields = $args['fields'];

	if ( in_array( $orderby, array( 'term_id', 'name', 'slug', 'term_group' ) ) ) {
		$orderby = "t.$orderby";
	} elseif ( in_array( $orderby, array( 'count', 'parent', 'taxonomy', 'term_taxonomy_id' ) ) ) {
		$orderby = "tt.$orderby";
	} elseif ( 'term_order' === $orderby ) {
		$orderby = 'tr.term_order';
	} elseif ( 'none' === $orderby ) {
		$orderby = '';
		$order = '';
	} else {
		$orderby = 't.term_id';
	}

	// tt_ids queries can only be none or tr.term_taxonomy_id
	if ( ('tt_ids' == $fields) && !empty($orderby) )
		$orderby = 'tr.term_taxonomy_id';

	if ( !empty($orderby) )
		$orderby = "ORDER BY $orderby";

	$select_this = '';
	if( is_string($fields) ){

		if( 'all' == $fields ){
			$select_this = "tr.object_id,tt.term_taxonomy_id,tt.taxonomy,tt.description,tt.parent,tt.count,t.*";
		} elseif( 'tt_ids' == $fields ){
			$select_this = "tt.term_taxonomy_id";
		}else{
			$fields = esc_sql($fields);
			$select_this = "t.{$fields}";
		}

	}elseif( is_array($fields) && !empty($fields) ){
		$count_index = count($fields) - 1;
		$last_el = $fields[$count_index];
		unset($fields[$count_index]);
		if( !empty($fields) ){

			foreach( $fields as $field ){
				$field = esc_sql($field);
				$select_this .= "t.{$field},";
			}

		}
		$last_el = esc_sql($last_el);
		$select_this .="t.{$last_el}";

	}

	$tax_where = $wpdb->prepare( " tt.taxonomy = %s ", $taxonomy );
	$obj_where = $wpdb->prepare( " tr.object_id = %d ", $object_id );

	$sql = "SELECT $select_this FROM $wpdb->terms AS t  ";
	$sql .= " INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id ";
	$sql .= " INNER JOIN $wpdb->term_relationships as tr ON tr.term_taxonomy_id = tt.term_taxonomy_id  ";
	$sql .= " WHERE ".$tax_where." AND ".$obj_where;
	$sql .= " {$orderby} {$order} ";

	$terms = $wpdb->get_results( $sql, ARRAY_A );

	if( empty($terms) ){
		return array();
	}

	if( 'tt_ids' == $fields ){

		$tr = array();
		foreach( $terms as $term_id ){
			$tr[] = $term_id['term_taxonomy_id'];
		}
		// set the return value
		$terms = $tr;
	}


	return $terms;
}


/**
 * Get the selected taxonomy of the product Tag, Category or Brand
 * @param int|string $product_id      Product id
 * @param string $taxonomy            Taxonomy either tag, cat or brand
 * @return array|null
 */
function ls_get_product_terms( $product_id, $taxonomy = 'tag'){
	if( empty($taxonomy) || empty($taxonomy) ){
		return array();
	}

	$taxonomy = 'product_'.$taxonomy;
	$product_id = (int) $product_id;

	$to_vend = array();
	$woo_taxonomies =  ls_get_object_terms( $product_id, $taxonomy);
	if( !empty($woo_taxonomies) ){
		foreach( $woo_taxonomies as $taxonomy ){
			$name = ls_speciachars_decode($taxonomy['name'], ENT_COMPAT);
			$to_vend[] = array( 'name'=> $name );
		}
	}
	return !empty($to_vend) ? $to_vend : null;
}

/**
 * @return string either 'on' or 'off'
 */
function ls_is_excluding_tax(){
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

/**
 * Create Woocommerce product attribute if it does not exists
 *
 * @param $attribute_label
 * @return bool|string
 */
function ls_create_woo_attribute( $attribute_label ){
	global $wpdb;
	if( empty($attribute_label) ){
		return null;
	}

	$attribute_name = wc_attribute_taxonomy_name( stripslashes( $attribute_label ) );
	$attribute = ls_woo_product_attribute_exist($attribute_label);

	if( false == $attribute ){
		$attr_name = wc_sanitize_taxonomy_name( stripslashes( $attribute_label ) );
		$attr_label = wc_clean( stripslashes( $attribute_label ) );
		$attribute = array(
			'attribute_label'   =>  $attr_label,
			'attribute_name'    =>  $attr_name,
			'attribute_type'    =>  'select',
			'attribute_orderby' =>  'menu_order'
		);

		$attr = $wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
		//Todo make sure to make an eye on the next following line because it should run on init hook
		// Register the taxonomy now so that the import works!
		register_taxonomy(
			$attribute_name,
			apply_filters( 'woocommerce_taxonomy_objects_' . $attribute_name, array( 'product' ) ),
			apply_filters( 'woocommerce_taxonomy_args_' . $attribute_name, array(
				'hierarchical' => true,
				'show_ui'      => false,
				'query_var'    => true,
				'rewrite'      => false,
			) )
		);

		delete_transient( 'wc_attribute_taxonomies' );

	}else{
		$attribute_name = wc_attribute_taxonomy_name( stripslashes( $attribute['attribute_name'] ) );
	}

	return $attribute_name;
}

/**
 * Check if attribute exist via database direct call of the woocommerce_attribute_taxonomies table
 *
 * @param $attr_label
 * @return array|bool|null|object|void
 */
function ls_woo_product_attribute_exist( $attr_label ){
	global $wpdb;
	$tbl_name = $wpdb->prefix.'woocommerce_attribute_taxonomies';
	$attr_name = $attr_label;

	$where_clause = $wpdb->prepare( " attribute_label = %s ", $attr_name );
	$attribute = $wpdb->get_row( "SELECT * FROM $tbl_name WHERE ".$where_clause , ARRAY_A);
	if( null !== $attribute ){
		return $attribute;
	}else{
		return false;
	}

}


/**
 * Save last update_at value to the database plus one second
 *
 * @param $type string           Required field, either 'product' or 'order'
 * @param null $utc_date_time    update_at value comming from vend
 * @return bool|string           Returns false or the last update_at value
 */
function ls_last_update_at( $type, $utc_date_time = null ){
	$types = array( 'product', 'order' );

	if( in_array( $type, $types ) ){

		$option_name = 'ls_'.$type.'_last_updated_at';
		$last_updated_at = get_option($option_name);

		if( empty($utc_date_time) ){
			return $last_updated_at;
		}else{

			$last_time = strtotime($last_updated_at);
			$time_arg = strtotime($utc_date_time);
			if( $last_time < $time_arg ){
				$lt_plus_one_second = date( "Y-m-d H:i:s", $time_arg + 1 );
				update_option( $option_name, $lt_plus_one_second );
				return $lt_plus_one_second;
			}

		}

	}

	return false;
}

/**
 * Save and return last order update_at key from the order get response plus one second
 *
 * @param $utc_date_time string   Optional UTC time coming from vend
 * @return string|false           Returns utc time in string format or false if $utc_date_time is null
 */
function ls_last_product_updated_at( $utc_date_time = null ){
	return ls_last_update_at( 'product', $utc_date_time );
}

/**
 * Save and return last order update_at key from the order get response plus one second
 *
 * @param null $utc_date_time     Optional UTC time coming from vend
 * @return string|false           Returns utc time in string format or false if $utc_date_time is null
 */
function ls_last_order_update_at( $utc_date_time = null ){
	return ls_last_update_at( 'order', $utc_date_time );
}