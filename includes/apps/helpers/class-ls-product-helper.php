<?php

class LS_Product_Helper
{
    protected $product = null;
    public $post_data = null;

    public function __construct(LS_Woo_Product $product)
    {
        $this->product = $product;
        $this->post_data = get_post($this->product->get_id());
    }

    public function getParendId()
    {
        return $this->product->get_parent_id();
    }

    public function getStatus()
    {
        return $this->product->get_status();
    }

    public function getDescription()
    {
        return $this->product->get_description();
    }

    public function getName()
    {
        return $this->product->get_name();
    }

    public function getType()
    {
        return $this->product->get_type();
    }

    public function isSimple()
    {
        return $this->product->is_type('simple');
    }

    public function isVariable()
    {
        return $this->product->is_type('variable');
    }

    public function isVariation()
    {
        return $this->product->is_type('variation');
    }

    public function getSku()
    {
        return $this->product->get_sku();
    }

    public static function getProductParentId(LS_Woo_Product $product)
    {
        return $product->get_parent_id();
    }

    public static function hasChildren(LS_Woo_Product $product)
    {
        return $product->has_child();
    }

    public static function isVariableAndDontHaveChildren(LS_Woo_Product $product)
    {
        if (true == self::isVariableProduct($product)) {
            $has_children = $product->has_child();
            if (true == $has_children) {
                return true;
            }
        }

        return false;
    }

    public static function getProductStatus(LS_Woo_Product $product)
    {
        return $product->get_status();
    }

    public static function getProductDescription(LS_Woo_Product $product)
    {
        return remove_escaping_str(html_entity_decode($product->get_description()));
    }

    public static function getProductName(LS_Woo_Product $product)
    {
        return html_entity_decode(remove_escaping_str($product->get_name()));
    }

    public static function isSimpleProduct(LS_Woo_Product $product)
    {
        return $product->is_type('simple');
    }

    public static function isVariableProduct(LS_Woo_Product $product)
    {
        return $product->is_type('variable');
    }

    public static function isVariationProduct(LS_Woo_Product $product)
    {
        return $product->is_type('variation');
    }

    public static function getProductParendId(LS_Woo_Product $product)
    {
        return $product->get_parent_id();
    }

    /**
     * Get Product using the sku
     * @param $sku
     * @return int
     */
    public static function getProductIdBySku($sku)
    {
        global $wpdb;

        $product_id = $wpdb->get_var(
            $wpdb->prepare("
						SELECT post.ID
						FROM $wpdb->posts AS post
						INNER JOIN $wpdb->postmeta AS pmeta ON (post.ID = pmeta.post_id)
						WHERE
							pmeta.meta_key='_sku' AND
							pmeta.meta_value=%s AND
							post.post_type IN('product','product_variation')
						LIMIT 1"
                , $sku)
        );

        if ($product_id) return $product_id;

        return null;
    }

    /**
     * Returns all the product variation ids base on main product id
     *
     * @param $parent_id
     * @return array|null
     */
    public static function getVariantIdAndSkus($parent_id)
    {
        global $wpdb;
        $var_ids = $wpdb->get_results(
            $wpdb->prepare("SELECT " . $wpdb->posts . ".ID as id, " . $wpdb->postmeta . ".meta_value AS sku
                        FROM " . $wpdb->posts . "
                        INNER JOIN " . $wpdb->postmeta . " ON " . $wpdb->postmeta . ".post_id = " . $wpdb->posts . ".ID
                        WHERE
                             " . $wpdb->posts . ".post_type='product_variation' AND
                             " . $wpdb->posts . ".post_parent= %d AND 
                             " . $wpdb->postmeta . ".meta_key ='_sku' ", $parent_id)
            , ARRAY_A);

        if (!empty($var_ids)) {
            return $var_ids;
        }
        return null;
    }

    public static function getVariantSkus($parent_id)
    {
        $skus = array();
        $variantIdAndSkus = self::getVariantIdAndSkus($parent_id);
        if (is_array($variantIdAndSkus)) {
            foreach ($variantIdAndSkus as $variantIdAndSku) {
                $skus[] = $variantIdAndSku['sku'];
            }
        }

        return $skus;
    }

    public static function getVariantIds($parent_id)
    {
        $ids = array();
        $variantIdAndSkus = self::getVariantIdAndSkus($parent_id);
        if (is_array($variantIdAndSkus)) {
            foreach ($variantIdAndSkus as $variantIdAndSku) {
                $ids[] = $variantIdAndSku['id'];
            }
        }

        return $ids;
    }

    public static function create($postarr, $wp_error = false)
    {
        $product_type = array('product', 'product_variation');
        $post_type = 'product';
        if (!empty($postarr['post_type'])) {
            if (in_array($postarr['post_type'], $product_type)) {
                $post_type = $postarr['post_type'];
            } else {
                $post_type = 'product';
            }
        }

        $postarr['post_type'] = $post_type;
        $postarr['post_title'] = empty($postarr['post_title']) ? 'This product name is empty' : $postarr['post_title'];
        return wp_insert_post($postarr, $wp_error);
    }

    public static function deleteWooProductBySku($variant_wku, $force_delete = true)
    {

        $deleted = false;
        $varId = self::getProductIdBySku($variant_wku);
        if (!empty($varId)) {
            $deleted = wp_delete_post($varId, $force_delete);
        }

        return $deleted;

    }

    public static function get_simple_product_ids()
    {
        global $wpdb;

        $product_ids = $wpdb->get_results("
						SELECT post.ID AS ID
						FROM $wpdb->posts AS post
						WHERE
							post.post_type IN('product') AND 
							post.post_status != 'auto-draft'
						ORDER BY post.ID ASC
						", ARRAY_A);

        if ($product_ids) return $product_ids;

        return null;
    }

    public static function get_product_ids()
    {
        global $wpdb;

        $product_ids = $wpdb->get_results("
						SELECT post.ID AS ID
						FROM $wpdb->posts AS post
						WHERE
							post.post_type IN('product','product_variation') AND 
							post.post_status != 'auto-draft'
						ORDER BY post.ID ASC
						", ARRAY_A);

        if ($product_ids) return $product_ids;

        return null;
    }

    /**
     * Returns all empty sku field for woocommerce product
     * @return mixed
     */
    public static function get_woo_empty_sku()
    {
        global $wpdb;

        //get all products with empty sku
        $empty_skus = $wpdb->get_results("
					SELECT
							wposts.ID,
							wposts.post_title AS product_name,
                            wposts.post_status AS product_status,
                            wpmeta.meta_key,
                            wpmeta.meta_value
					FROM $wpdb->postmeta AS wpmeta
					INNER JOIN $wpdb->posts as wposts on ( wposts.ID = wpmeta.post_id )
					WHERE wpmeta.meta_key = '_sku' AND wpmeta.meta_value = '' AND wposts.post_type IN('product','product_variation')
					ORDER BY wpmeta.meta_value ASC
				", ARRAY_A);

        return $empty_skus;
    }

    public static function get_woo_duplicate_sku()
    {
        global $wpdb;

        //get all duplicate product sku
        $result = $wpdb->get_results("
				SELECT
						wposts.ID,
						wposts.post_title AS product_name,
						wposts.post_status AS product_status,
						wpmeta.meta_key,
						wpmeta.meta_value, 
						wposts.post_type
				FROM $wpdb->postmeta AS wpmeta
				JOIN (
						SELECT
							pmeta.meta_key,
							pmeta.meta_value
						FROM  $wpdb->postmeta AS pmeta
						INNER JOIN $wpdb->posts as w_post ON (w_post.ID = pmeta.post_id)
						WHERE pmeta.meta_key = '_sku' AND w_post.post_type IN('product','product_variation')
						GROUP BY pmeta.meta_value
						HAVING COUNT(pmeta.meta_value) > 1
					 ) AS s_wpmeta
						ON wpmeta.meta_value = s_wpmeta.meta_value
				INNER JOIN $wpdb->posts as wposts on ( wposts.ID = wpmeta.post_id )
				WHERE wpmeta.meta_key = '_sku' AND wpmeta.meta_value != '' AND wposts.post_type IN('product','product_variation') 
				ORDER BY wpmeta.meta_value ASC
			", ARRAY_A);

        $real_sku_duplicate = array();
        if (!empty($result)) {
            foreach ($result as $duplicateSku) {
                $product_metas = self::get_post_meta($duplicateSku['ID'], $duplicateSku['meta_key'], $duplicateSku['meta_value']);
                $count = count($product_metas);

                if ($count >= 2) {
                    if (isset($product_metas[0]['meta_id'])) {
                        unset($product_metas[0]);
                    }
                    foreach ($product_metas as $product_meta) {
                        self::direct_db_post_meta_delete($product_meta['meta_id']);
                    }
                } else {
                    $real_sku_duplicate[] = $duplicateSku;
                }
            }
        }

        return $real_sku_duplicate;
    }

    public static function get_post_meta($product_id, $meta_key, $meta_value = null)
    {
        global $wpdb;

        $preparedQuery = $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %s AND meta_key = %s ", $product_id, $meta_key);
        if(null === $preparedQuery){
            $preparedQuery = $wpdb->prepare(" AND meta_value = %s", $meta_value);
        }
        $result = $wpdb->get_results($preparedQuery, ARRAY_A);

        return $result;
    }

    public static function direct_db_post_meta_delete($meta_id)
    {
        global $wpdb;
        $result = $wpdb->delete($wpdb->postmeta, array('meta_id' => $meta_id));

        return $result;
    }

}