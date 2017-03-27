<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

/**
 * Plugins Global functions file
 * Apps functions.php file will be included here
 */

/**
 * Vend functions.php
 */
include LS_INC_DIR.'apps/vend/functions/functions.php';

/**
 * QBO functions.php
 */
include LS_INC_DIR.'apps/qbo/functions/functions.php';

/**
 * A wrapper function that will wrap print_r into pre tag
 * Useful in debuging purposes.
 * @param array or object $data
 */
function ls_print_r($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

/**
 * Download an image from the specified URL uses ls_generate_woo_product_image function
 * @param $post_id 		The post id associated with the image
 * @param $image_url	The URl of the image to download
 * @param null $desc	Optional. Description of the image
 * @return int|mixed|object|WP_Error
 */
function ls_set_product_thumbnail( $post_id, $image_url, $desc = null ){

	$id = ls_generate_woo_product_image( $post_id, $image_url, $desc );
	if( !is_wp_error( $id ) ){
		set_post_thumbnail( $post_id, $id );
	}
	error_log( " ang id sa sa generation ".$id );
	return $id;

}

/**
 * Download an image from the specified URL
 * @param $post_id 		The post id associated with the image
 * @param $image_url	The URl of the image to download
 * @param null $desc	Optional. Description of the image
 * @return int|mixed|object|WP_Error
 */
function ls_generate_woo_product_image( $post_id, $image_url, $desc = null ){

	// Need to require these files
	if ( !function_exists('media_handle_upload') ) {
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	}

	// Set variables for storage
	// fix file filename for query strings
	preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $image_url, $matches);
	if ( ! $matches ) {
		return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
	}

	$file_array = array();
	$file_array['tmp_name'] = download_url( $image_url );

	// If error storing temporarily, unlink
	if( is_wp_error( $file_array['tmp_name'] ) ){
		// download failed, handle error
		@unlink( $file_array['tmp_name'] );
		return $file_array['tmp_name'];
	}

	$file_array['name'] = basename($matches[0]);

	if( empty( $desc )){
		$desc = sanitize_file_name( $file_array['name'] );
	}

	// do the validation and storage stuff
	$id = media_handle_sideload( $file_array, $post_id, $desc );

	// If error storing permanently, unlink
	if ( is_wp_error($id) ) {
		@unlink($file_array['tmp_name']);
		return $id;
	}

	return $id;
}

/**
 * Show Image help link
 */
function help_link($attribute){
    /**
     * Check if href key has been added if not set to default link
     */
    $href = isset($attribute['href'])? $attribute['href']: 'https://www.linksync.com/help/woocommerce';

    $src = '../wp-content/plugins/linksync/assets/images/linksync/help.png';
    echo '	<a style="color: transparent !important" target="_blank" href="', $href ,'">
				<img title="',$attribute['title'],'"
					 src="', $src ,'"
					 height="16" width="16">
			</a>';
}