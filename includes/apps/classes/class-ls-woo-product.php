<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Woo_Product
{
    private $post_data = null;
    private $product_id = null;
    private $product_meta = null;

    public function __construct($product_id)
    {
        if (is_numeric($product_id)) {
            $this->product_id = $product_id;
            $this->post_data = $this->get_post($product_id);
            $this->product_meta = new LS_Product_Meta($product_id);
        }
    }

    public function get_meta()
    {
        return $this->product_meta;
    }

    public function get_sku()
    {
        return $this->product_meta->get_sku();
    }

    public function get_id()
    {
        return $this->product_id;
    }

    public function get_post($product_id)
    {
        return get_post($product_id);
    }

    public function get_post_type()
    {
        $post_type = 'product';
        $post_data = $this->get_post_data();

        if (!empty($post_data->post_type)) {
            $post_type = $post_data->post_type;
        }

        return $post_type;
    }

    public function get_post_data()
    {
        return $this->post_data;
    }

    public function get_type()
    {
        $product_type = 'simple';

        if ('product_variation' == $this->get_post_type()) {

            $product_type = 'variation';

        } else {

            $terms_array = ls_get_object_terms($this->get_id(), 'product_type', array(
                'fields' => 'all'
            ));
            if (!empty($terms_array[0]['name'])) {
                $product_type = $terms_array[0]['name'];
            }

        }


        return $product_type;
    }

    public function is_type($type)
    {
        $product_type = $this->get_type();
        $product_type = strtolower($product_type);

        if ($type == $product_type) {
            return true;
        }

        return false;
    }

    public function is_simple()
    {
        return $this->is_type('simple');
    }

    public function is_bundle()
    {
        return $this->is_type('bundle');
    }

    public function is_subscription()
    {
        return $this->is_type('subscription');
    }

    public function is_variable()
    {
        return $this->is_type('variable');
    }

    public function is_variation()
    {
        return $this->is_type('variation');
    }

    public function get_parent_id()
    {
        $parent_id = 0;
        $post_data = $this->get_post_data();
        if (!empty($post_data->post_parent)) {
            $parent_id = $post_data->post_parent;
        }

        return $parent_id;
    }

    public function get_status()
    {
        $product_status = 'publish';

        $post_data = $this->get_post_data();
        if (!empty($post_data->post_status)) {
            $product_status = $post_data->post_status;
        }
        return $product_status;
    }

    public function get_description()
    {
        $product_description = '';
        $post_data = $this->get_post_data();

        if (!empty($post_data->post_content)) {
            $product_description = $post_data->post_content;
        }

        return $product_description;
    }

    public function get_name()
    {
        $product_name = '';
        $post_data = $this->get_post_data();

        if (!empty($post_data->post_title)) {
            $product_name = $post_data->post_title;
        }

        return html_entity_decode(remove_escaping_str($product_name));
    }

    public function has_child()
    {
        return 0 < count($this->get_children());
    }

    public function get_children()
    {
        global $wpdb;
        $id = $this->get_id();

        $sql = "SELECT " . $wpdb->posts . ".ID as id
                        FROM " . $wpdb->posts . "
                        WHERE " . $wpdb->posts . ".post_parent= %d AND  ". $wpdb->posts .".post_type = 'product_variation' ";
        $children = $wpdb->get_results($wpdb->prepare($sql, $id), ARRAY_A);

        $childrenId = array();
        if (!empty($children)) {
            foreach ($children as $child){
                $childrenId[] = $child['id'];
            }
        }

        return $childrenId;
    }


    public static function direct_db_post_meta_delete($meta_id)
    {
        global $wpdb;
        $result = $wpdb->delete($wpdb->postmeta, array('meta_id' => $meta_id));

        return $result;
    }

    public static function get_post_meta($product_id, $meta_key, $meta_value = null)
    {
        global $wpdb;

        $preparedQuery = $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %s AND meta_key = %s AND meta_value = %s", $product_id, $meta_key, $meta_value);
        $result = $wpdb->get_results($preparedQuery, ARRAY_A);

        return $result;
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
						wpmeta.meta_value
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
                $product_metas = LS_Woo_Product::get_post_meta($duplicateSku['ID'], $duplicateSku['meta_key'], $duplicateSku['meta_value']);
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