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

        if ($type == $product_type) {
            return true;
        }

        return false;
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

        return $product_name;
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


}