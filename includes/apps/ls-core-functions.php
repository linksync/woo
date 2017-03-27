<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');


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
 * @param $slug
 * @param null $parent
 * @param null $taxonomy
 * @return array|bool|null|object|void
 */
function ls_get_term_by_slug( $slug, $parent = null, $taxonomy = null ){
	global $wpdb;

	$tax_clause = '';
	if( !empty($taxonomy) ){
		$tax_clause = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );
	}

	$parent_clause = '';
	if( null != $parent){
		$parent_clause = $wpdb->prepare( "AND tt.parent = %d", $parent );
	}

	$term = $wpdb->get_row(
		$wpdb->prepare( "
				SELECT t.*, tt.* FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt
				ON t.term_id = tt.term_id
				WHERE t.slug = %s "
			, $slug
		) . " $parent_clause $tax_clause LIMIT 1" );

	if ( ! $term )
		return false;

	return $term;
}


function ls_get_term( $args ){
	//Todo to unify ls_get_term_by_slug and ls_get_term_by_name functions
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

/**
 * Get the attribute of a variant product
 *
 * @param $var_id int The varaint id
 * @return array
 */
function ls_get_variant_attributes($var_id)
{
    global $wpdb;
    $parent_id = ls_get_post_parent_id($var_id);
    $parent_attributes = get_post_meta($parent_id, '_product_attributes', true);

    $attr_search = 'attribute_';
    $pa_search = 'pa_';
    $attr_where = $wpdb->esc_like($attr_search) . '%';

    $sql = 'SELECT meta_key, meta_value
			FROM ' . $wpdb->postmeta . '
			WHERE
				 meta_key LIKE %s AND
				 post_id = %d ';

    $var_attrs = $wpdb->get_results($wpdb->prepare($sql, $attr_where, $var_id), ARRAY_A);

    $attributes = array();
    if (!empty($var_attrs)) {
        foreach ($var_attrs as $var_attr) {
            $attr_removed = preg_replace('/' . $attr_search . '/', '', $var_attr['meta_key']);
            $pa_removed = preg_replace('/' . $pa_search . '/', '', $attr_removed);

            $attr_label = '';
            $attr_value = '';

            //$attr_removed and $pa_removed should not be equal if pa_ has been remove
            if ($attr_removed != $pa_removed) {

                if (
                    isset($parent_attributes[$pa_search . $pa_removed]) &&
                    $parent_attributes[$pa_search . $pa_removed]['is_variation']
                ) {
                    //Woocommerce Attributes
                    $woo_attribute = ls_woo_product_attribute_exist($pa_removed);
                    if (false != $woo_attribute) {
                        $attr_label = $woo_attribute['attribute_label'];
                        $term = ls_get_term_by_slug($var_attr['meta_value']);
                        $attr_value = $term->name;
                    }
                }


            } elseif ($attr_removed == $pa_removed) {

                //Custom attributes from woocommerce
                if (false != $parent_id) {

                    if (!empty($parent_attributes[$pa_removed]) && $parent_attributes[$pa_removed]['is_variation']) {

                        $attr_label = $parent_attributes[$pa_removed]['name'];
                        $attr_values = explode('|', $parent_attributes[$pa_removed]['value']);
                        $value = preg_grep('/' . $var_attr['meta_value'] . '/i', $attr_values);
                        if(!empty($value)){
                            $attr_value = $var_attr['meta_value'];
                        }

                    }
                }

            }


            if (!empty($attr_label) && !empty($attr_value)) {
                $attributes[] = array('name' => $attr_label, 'value' => $attr_value);
            }

        }
    }

    return $attributes;
}

/**
 * @return array Possible option in vend, the limit is three
 */
function ls_vend_variant_option(){
	$options = array(
		1 => 'option_one_',
		2 => 'option_two_',
		3 => 'option_three_',
	);
	return $options;
}

/**
 * Get the parent ID of a variant uses wordpress wp_get_post_parent_id function
 * @param $post_id
 * @return false|int
 */
function ls_get_post_parent_id( $post_id ){
	return wp_get_post_parent_id( $post_id );
}

/**
 * Returns available payment methods in woocommerce
 * @return array
 */
function ls_get_woo_payment_methods(){
    return WC_Payment_Gateways::instance()->payment_gateways();
}

/**
 * Check the connection if it is Vend
 * @return bool
 */
function is_vend(){
    $bool = false;
    $connected_to = get_option('linksync_connectedto');
    $connected_with = get_option('linksync_connectionwith');

    if ( 'Vend' == $connected_to || 'Vend' == $connected_with) {
        $bool = true;
    }

    return $bool;
}

/**
 * Check the connection if it is QuickBooks Online
 * @return bool
 */
function is_qbo(){
    $bool = false;
    $connected_to = get_option('linksync_connectedto');
    $connected_with = get_option('linksync_connectionwith');

    if ( 'QuickBooks Online' == $connected_to || 'QuickBooks Online' == $connected_with) {
        $bool = true;
    }

    return $bool;
}

/**
 * returns string UTC time of the last syncing
 * @return string
 */
function get_last_sync(){
    $utc_str = gmdate("Y-m-d H:i:s", time());
    $utc = gmdate("Y-m-d H:i:s",strtotime($utc_str) );

    return get_option( 'ls_last_sync', $utc);
}

/**
 * returns string UTC time of the last syncing
 * @return string
 */
function update_last_sync(){
    $utc_str = gmdate("Y-m-d H:i:s", time());
    $utc = gmdate("Y-m-d H:i:s",strtotime($utc_str));
    update_option( 'ls_last_sync', $utc );

    return get_last_sync();
}

/**
 * Return true if post_type is a product or not
 * @param $type
 * @return bool
 */
function is_woo_product( $type ){
    $bool = false;
    $product_types = array('product', 'product_variation');

    if( in_array($type, $product_types) ){
        $bool = true;
    }
    return $bool;
}

/**
 * Remove unneeded string
 * @param $string
 * @return mixed|string
 */
function remove_escaping_str( $string ){
    $str_tobe_removed = array("\\");
    $str = '';

    foreach( $str_tobe_removed as $needle ){
        $str = str_replace($needle, '', $string );
    }

    return $str;
}


/**
 * Returns all the needed woocommerce order action hooks that was selected by the user
 * @return array
 */
function ls_woo_order_hook_names(){
    $selected_orders = LS_QBO()->order_option()->order_status();
    $order_hooks = array();

    if( !empty($selected_orders)){
        foreach( $selected_orders as $order_name ){
            $name = substr( $order_name, 3 );

            $order_hooks[] = 'woocommerce_order_status_'.$name;
        }
    }

    return $order_hooks;
}

function ls_selected_order_status_to_trigger_sync(){
    $selected_orders = LS_QBO()->order_option()->order_status();
    $order_hooks = array();
    if( !empty($selected_orders)){
        foreach( $selected_orders as $order_name ){
            $name = substr( $order_name, 3 );

            $order_hooks[] = $name;
        }
    }
    return $order_hooks;
}

function get_qbo_id( $id ){
    $laid = LS_ApiController::get_current_laid();
    if( !empty($laid) ){
        return str_replace( $laid, '', $id );
    }
    return $id;
}

if (!function_exists('array_udiff_custom_compare_product_id')) {
    function array_udiff_custom_compare_product_id($a, $b)
    {
        return $a['ID'] - $b['ID'];
    }
}

if (!function_exists('ls_is_odd')) {

    function ls_is_odd($number)
    {
        if (($number % 2) == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('get_vend_id')) {

    function get_vend_id($id)
    {
        $laid = LS_ApiController::get_current_laid();
        if( !empty($laid) ){
            return str_replace( $laid, '', $id );
        }
        return $id;
    }
}

