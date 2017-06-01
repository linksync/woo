<?php

class LS_Product_Helper
{
    protected $product = null;
    public $post_data = null;

    public function __construct(WC_Product $product)
    {
        $this->product = $product;
        $this->post_data = get_post($this->product->get_id());
    }

    public function getPostTitle()
    {
        if (isset($this->post_data->post_title)) {
            return $this->post_data->post_title;
        }
        return null;
    }

    public function getPostContent()
    {
        if (isset($this->post_data->post_content)) {
            return $this->post_data->post_content;
        }
        return null;
    }

    public function getPostParentId()
    {
        if (isset($this->post_data->post_parent)) {
            return $this->post_data->post_parent;
        }
        return null;
    }

    public function getPostStatus()
    {
        if (isset($this->post_data->post_status)) {
            return $this->post_data->post_status;
        }
        return null;
    }

    public function getPostType()
    {
        if (isset($this->post_data->post_type)) {
            return $this->post_data->post_type;
        }
        return null;
    }

    public function getParendId()
    {
        return self::getProductParentId($this->product);
    }

    public function getStatus()
    {
        return self::getProductStatus($this->product);
    }

    public function getDescription()
    {
        return self::getProductDescription($this->product);
    }

    public function getName()
    {
        return self::getProductName($this->product);
    }

    public function getType()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->product->post->post_type;
        }

        return $this->product->get_type();
    }

    public function isSimple()
    {
        return self::isSimpleProduct($this->product);
    }

    public function isVariable()
    {
        return self::isVariableProduct($this->product);
    }

    public function isVariation()
    {
        return self::isVariationProduct($this->product);
    }

    public function getSku()
    {
        return $this->product->get_sku();
    }

    public static function getProductParentId(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $product->post->post_parent;
        }

        return $product->get_parent_id();
    }

    public static function hasChildren(WC_Product $product)
    {
        return $product->has_child();
    }

    public static function isVariableAndDontHaveChildren(WC_Product $product)
    {
        if (true == self::isVariableProduct($product)) {
            $has_children = $product->has_child();
            if (true == $has_children) {
                return true;
            }
        }

        return false;
    }

    public static function getProductStatus(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $product->post->post_status;
        }

        return $product->get_status();
    }

    public static function getProductDescription(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return remove_escaping_str(html_entity_decode($product->post->post_content));
        }

        return remove_escaping_str(html_entity_decode($product->get_description()));
    }

    public static function getProductName(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return html_entity_decode(remove_escaping_str($product->get_title()));
        }

        return html_entity_decode(remove_escaping_str($product->get_name()));
    }

    public static function isSimpleProduct(WC_Product $product)
    {
        return $product->is_type('simple');
    }

    public static function isVariableProduct(WC_Product $product)
    {
        return $product->is_type('variable');
    }

    public static function isVariationProduct(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            if ($product->post->post_type == 'product_variation') {
                return true;
            }
            return false;
        }

        return $product->is_type('variation');
    }

    public static function getProductParendId(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $product->post->post_parent;
        }

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
            $wpdb->prepare("SELECT ".$wpdb->posts.".ID as id, ".$wpdb->postmeta.".meta_value AS sku
                        FROM " . $wpdb->posts . "
                        INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->postmeta.".post_id = ".$wpdb->posts.".ID
                        WHERE
                             ".$wpdb->posts.".post_type='product_variation' AND
                             ".$wpdb->posts.".post_parent= %d AND 
                             ".$wpdb->postmeta.".meta_key ='_sku' ", $parent_id)
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
        if(is_array($variantIdAndSkus)){
            foreach ($variantIdAndSkus as $variantIdAndSku){
                $skus[] = $variantIdAndSku['sku'];
            }
        }

        return $skus;
    }

    public static function getVariantIds($parent_id)
    {
        $ids = array();
        $variantIdAndSkus = self::getVariantIdAndSkus($parent_id);
        if(is_array($variantIdAndSkus)){
            foreach ($variantIdAndSkus as $variantIdAndSku){
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

}