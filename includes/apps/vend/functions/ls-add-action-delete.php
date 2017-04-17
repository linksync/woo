<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

/**
 *Delete product based on post id
 * @param post id
 * @return null 
 */
function linksync_DeleteProduct($post_id) {
    $pro_object = new WC_Product($post_id);
    if ($pro_object->post->post_type == 'product') {
        $testMode = get_option('linksync_test');
        $LAIDKey = get_option('linksync_laid');
        $apicall = new linksync_class($LAIDKey, $testMode);
        if (!defined('ABSPATH'))
            define('ABSPATH', dirname(__FILE__) . '/');
        include_once (ABSPATH . 'wp-includes/post.php');
        $product_sku = get_post_meta($post_id, '_sku', true);
        if (!empty($product_sku)) {
            $apicall->linksync_deleteProduct($product_sku);
        }
    }
}

