<?php

class linksync_class {

    /**
     * @var string testingURL for Testing Purpose
     */
    public $testingURL = 'https://stg-api.linksync.com/api/v1/';

    /**
     * @var string URL for Live
     */
    public $URL = 'https://api.linksync.com/api/v1/';
    public $lastresponse;

    /**
     * @var string LAID Key
     */
    private $LAID;

    /**
     * @var string testmode if On then it will use TestingURL
     */
    public $testmode;

    /**
     * Initiate Requiremd values
     *
     * @param string $LAID
     * @param string $testmode (on,off)
     *
     */
    public function __construct($LAID, $testmode) {
        $this->testmode = $testmode;
        if (empty($LAID)) {
            $this->lastresponse = array(
                'result' => 'error',
                'message' => 'Missing API Key !!'
            );
            return false;
        } else {
            $this->LAID = $LAID;
            $this->lastresponse = array(
                'result' => 'success',
                'message' => 'Parameters Received'
            );
        }
    }

    /**
     * testConnection
     * Request UrL : URL: /api/v1/laid
     * Returns connected app details for an API Key (LAID).
     * Operations allowed: GET,POST
     */
    public function testConnection() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'laid', 'GET');
        return $result;
    }

    public function upgrade_notification() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'laid', 'GET');
        return $result;
    }

    public function webhookConnection($webhookURL, $version, $order) {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $json = array(
            "url" => $webhookURL,
            "version" => $version,
            "order_import" => $order
//                "product_import" => $product
        );
        $data = json_encode($json);
        $result = $this->_CalltoAPIArray($url, 'laid', 'POST', $data);
        return $result;
    }

    public function linksync_getProducts() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'product', 'GET');
        return $result;
    }

    public function linksync_getTags() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'vend/tags', 'GET');
        return $result;
    }

    public function linksync_getTaxes() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'vend/taxes', 'GET');
        return $result;
    }

    public function linksync_deleteProduct($sku) {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'product/' . urlencode($sku), 'DELETE');
        return $result;
    }

    public function linksync_getOutlets() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'vend/outlets', 'GET');
        return $result;
    }

    public function linksync_getRegisters() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'vend/registers', 'GET');
        return $result;
    }

    public function linksync_getpaymentTypes() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'vend/paymentTypes', 'GET');
        return $result;
    }

    public function linksync_getUsers() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'vend/users', 'GET');
        return $result;
    }

    public function getProductWithSince($time) {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'product?since=' . urlencode($time), 'GET');
        return $result;
    }

    public function getProductWithTags($tags) {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'product?tags=' . urlencode($tags), 'GET');
        return $result;
    }

    public function getProductWithOutlets($outlets) {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'product?outlet=' . urlencode($outlets), 'GET');
        return $result;
    }

    public function getVendConfig() {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        $result = $this->_CalltoAPIArray($url, 'vend/config', 'GET');
        return $result;
    }

    public function getProductWithParam($param) {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        if (isset($param) && !empty($param)) {
            $result = $this->_CalltoAPIArray($url, 'product?' . $param, 'GET');
        } else {
            $result = $this->_CalltoAPIArray($url, 'product', 'GET');
        }
        return $result;
    }

    public function isReferenceExists($reference) {
        global $wpdb;
        $reference_result = null;
        $sql_query = "SELECT post_id
                      FROM `" . $wpdb->postmeta . "`
                      WHERE meta_key='_sku' AND BINARY meta_value= %s ";
        $query = $wpdb->get_results($wpdb->prepare($sql_query, $reference), ARRAY_A);
        if (0 != $wpdb->num_rows) {
            foreach($query as  $result){

                $get_product = "SELECT *
                              FROM `" . $wpdb->posts . "`
                              WHERE post_type='product' AND ID= %d AND post_status!='trash'";

                $product_detail = $wpdb->get_results($wpdb->prepare($get_product, $result['post_id']), ARRAY_A);
                if(0 != $wpdb->num_rows){
                    foreach($product_detail as  $detail){
                        $reference_result['result'] = 'success';
                        $reference_result['data'] = $detail['ID'];
                    }
                }
            }
        } else {
            $reference_result['result'] = 'error';
        }
        return $reference_result;
    }

    public function isReferenceExists_order($reference) {
        global $wpdb;
        $sql_query = "SELECT post_id
                      FROM `" . $wpdb->postmeta . "`
                      WHERE meta_key='_sku' AND BINARY meta_value= %s";

        $query = $wpdb->get_results($wpdb->prepare($sql_query, $reference), ARRAY_A);
        if (0 != $wpdb->num_rows) {
            $result = $query[0];
            $reference_result['result'] = 'success';
            $reference_result['data'] = $result['post_id'];
        } else {
            $reference_result['result'] = 'error';
        }
        return $reference_result;
    }

    public function variantSkuHandler($sku, $parent_id) {
        global $wpdb;
        $sql_query = "SELECT post_id
                      FROM `" . $wpdb->postmeta . "`
                      WHERE meta_key='_sku' AND BINARY meta_value= %s ";

        $query = $wpdb->get_results($wpdb->prepare($sql_query, $sku), ARRAY_A);
        if (0 != $wpdb->num_rows) {
            foreach ($query as $result) {
                $reference_result = $this->check_product_variant($result['post_id'], $parent_id);
            }
        } else {
            $reference_result['result'] = 'error';
        }
        return $reference_result;
    }

    public function check_product_variant($variant_id, $parent_id) {
        global $wpdb;
//Example: SELECT * FROM `pxxf_posts` WHERE post_type='product_variation' AND ID='4359' AND `post_parent`='4350' AND post_status!='trash'
        $sql_query = "SELECT ID,post_parent
                      FROM `" . $wpdb->posts . "`
                      WHERE
                            post_type='product_variation' AND
                            ID= %d  AND
                            `post_parent`= %d   AND
                            post_status!='trash'";

        $product_detail = $wpdb->get_results($wpdb->prepare($sql_query, $variant_id, $parent_id), ARRAY_A);
        if (0 != $wpdb->num_rows) {
            foreach ($product_detail as $detail) {
                $reference_result['result'] = 'success';
                $reference_result['data'] = $detail['ID'];
            }
        } else {
            $reference_result['result'] = 'error';
        }
        return $reference_result;
    }

// VEND Order Fuctions
    public function linksync_getOrder($param) {
        $url = $this->testmode == 'on' ? $this->testingURL : $this->URL;
        if (isset($param) && !empty($param)) {
            $result = $this->_CalltoAPIArray($url, 'order?' . $param, 'GET');
        } else {
            $result = $this->_CalltoAPIArray($url, 'order', 'GET');
        }

        return $result;
    }

    public function linksync_postProduct($data) {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'product', 'POST', $data);
        return $result;
    }

    public function linksync_postOrder($data) {

        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'order', 'POST', $data);
        return $result;
    }

    public function linksync_sendLog($data) {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'laid/sendLog', 'POST', $data);
        return $result;
    }

    /**
     * QuickBooks Online Fucntions
     */
    public function linksync_QuickBook_taxes() {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'qbo/tax', 'GET');
        return $result;
    }

    public function linksync_QuickBook_info() {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'qbo/info', 'GET');
        return $result;
    }

    public function linksync_QuickBook_taxCode() {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'qbo/taxcode', 'GET');
        return $result;
    }

    public function linksync_QuickBook_class() {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'qbo/class', 'GET');
        return $result;
    }

    public function linksync_QuickBook_payment() {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'qbo/payment', 'GET');
        return $result;
    }

    public function linksync_QuickBook_location() {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'qbo/location', 'GET');
        return $result;
    }

    public function linksync_QuickBook_account($account) {
        $url = $this->testmode == 'on' ? $url = $this->testingURL : $url = $this->URL;
        $result = $this->_CalltoAPIArray($url, 'qbo/account?classification=' . $account, 'GET');
        return $result;
    }



    /**
     * @param array the variants array
     * returns overall total quantity of a variant product
     */
    public function get_total_variants_quantity($variants){
        $quantity = 0;
        if(!empty($variants)){
            /**
             *Loop through the available variants to get the quantity
             */
            foreach($variants as $variant){
                if(isset($variant['outlets'])){
                    /**
                     * Loop through to get the quantity on each available outlet
                     */
                    foreach($variant['outlets'] as $outlet){
                        //add the outlets quantity
                        $quantity += $outlet['quantity'];
                    }
                }
            }

        }

        return $quantity;
    }

    /**
     *  Create term relationship if it doesn't exist
     *  @param int $object_id
     *  @param int $term_taxonomy_id
     */
    public function create_term_relationship($object_id, $term_taxonomy_id, $term_order = 0){
        global $wpdb;
        $tbl_term = $wpdb->term_relationships;

        $wpdb->get_results($wpdb->prepare(
            "SELECT object_id
              FROM `" . $tbl_term . "`
              WHERE object_id = %d AND term_taxonomy_id = %d "
            , $object_id
            , $term_taxonomy_id
        ),ARRAY_A);

        //check query result count
        if($wpdb->num_rows < 1){
            $wpdb->query($wpdb->prepare(
                "INSERT INTO `" . $tbl_term . "`
                (object_id,term_taxonomy_id,term_order)
                VALUES( %d ,%d , %d )"
                , $object_id
                , $term_taxonomy_id
                , $term_order
            ));
        }
    }

    /**
     * The function used to Add / Update product in woocommerce
     *
     * @param array product
     *
     * @return true if goes well without any error
     */
    public function importProductToWoocommerce($products) {
        global $wpdb;
        $option = array(
            1 => 'one',
            2 => 'two',
            3 => 'three'
        );
        $product_ids = array();
        foreach ($products['products'] as $product) {
            remove_all_actions('save_post');
            $ps_create_new = get_option('ps_create_new'); # Product Setting if Create New Checked box is ON
            $name = $product['name'];
            $description = $product['description'];
            $reference = $product['sku'];
// $list_price = $product['list_price'];
            $sell_price = $product['sell_price'];
            $quantity = 0;
            if (count($product['outlets']) != 0) {

                foreach ($product['outlets'] as $outlet) {
                    $product_type = get_option('product_sync_type');
                    if ($product_type == 'two_way') {
                        if (get_option('ps_wc_to_vend_outlet') == 'on') {
                            $selected_outlet = get_option('wc_to_vend_outlet_detail');
                            if (isset($selected_outlet) && !empty($selected_outlet)) {
                                $outlet_id = explode('|', $selected_outlet);
                                if (isset($outlet_id[1])) {
                                    if ($outlet_id[1] == $outlet['outlet_id']) {
                                        $quantity+=$outlet['quantity'];
                                    }
                                }
                            }
                        }
                    } elseif ($product_type == 'vend_to_wc-way') {
                        if (get_option('ps_outlet') == 'on') {
                            $selected_outlet = get_option('ps_outlet_details');
                            if (isset($selected_outlet) && !empty($selected_outlet)) {
                                $outlet_id = explode('|', $selected_outlet);
                                foreach ($outlet_id as $id) {
                                    if ($id == $outlet['outlet_id']) {
                                        $quantity+=$outlet['quantity'];
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $outlet_checker = 'noOutlet';
            }
            $tax_name = $product['tax_name'];
#Checking for Price Entered With Tax
            if (get_option('woocommerce_calc_taxes') == 'yes') {
                if (get_option('linksync_woocommerce_tax_option') == 'on') {
                    if (get_option('woocommerce_prices_include_tax') == 'yes') {
                        $excluding_tax = 'off';
                    } else {
                        $excluding_tax = 'on';
                    }
                } else {
                    $excluding_tax = get_option('excluding_tax');
                }
            } else {
                $excluding_tax = get_option('excluding_tax');
            }
# @return product id if reference exists
            $result_reference = self::isReferenceExists($reference); #  Check if already exist product into woocommerce

            include_once(ABSPATH . 'wp-admin/includes/image.php');

            if ($result_reference['result'] == 'success') { // it means it already exists
                $product_ids[] = $result_reference['data'] . '|update_id';
                $status = '';
                /*
                 * Update exists product into WC
                 */

#Delete the product from the woocommerce with the value delected_at of the product
                if (get_option('ps_delete') == 'on') {
                    if (!empty($product['deleted_at'])) {
                        global $wpdb;
                        $product_detail = $wpdb->get_results(
                                                $wpdb->prepare("SELECT ID,post_parent
                                                                FROM `" . $wpdb->posts . "`
                                                                WHERE
                                                                    post_type='product_variation' AND
                                                                    `post_parent`= %d ", $result_reference['data'])
                                            , ARRAY_A);
                        if (0 != $wpdb->num_rows) {
                            foreach ($product_detail as $detail) {
                                wp_delete_post($detail['ID']);
                            }
                        }
                        wp_delete_post($result_reference['data'], true);
                    }
                }
                if (empty($product['deleted_at'])) {
#-----Delete All Relationship--------#
                    if (get_option('ps_attribute') == 'on') {
                        $product_attributes = get_post_meta($result_reference['data'], '_product_attributes', TRUE);
                        if (isset($product_attributes) && !empty($product_attributes)) {
                            foreach ($product_attributes as $taxonomy_name => $taxonomy_detail) {
                                $taxonomy_query = $wpdb->get_results(
                                                    $wpdb->prepare("SELECT term_taxonomy_id
                                                                    FROM `" . $wpdb->term_taxonomy . "`
                                                                    WHERE `taxonomy`= %s", $taxonomy_name), ARRAY_A);
                                if (0 != $wpdb->num_rows) {
                                    foreach ($taxonomy_query as $term_taxonmy_id_db) {
                                        $wpdb->query($wpdb->prepare(
                                            "DELETE FROM `" . $wpdb->term_relationships . "`
                                                WHERE
                                                    object_id= %d  AND
                                                    term_taxonomy_id= %d "
                                            , $result_reference['data']
                                            , $term_taxonmy_id_db['term_taxonomy_id']
                                        ));
                                    }
                                }
                            }
                        }
                    }
#-------------------------VARIENT DATA--------------------------------#

                    if (isset($product['variants']) && !empty($product['variants'])) {
                        wp_set_object_terms($result_reference['data'], 'variable', 'product_type'); //this will create a variable product
                        $result_data = $this->variant_sku_check($product['variants'], $result_reference['data']);
                        if (isset($result_data) && !empty($result_data)) {
                            if (isset($result_data['thedata']) && !empty($result_data['thedata'])) {
                                $product_attributes = get_post_meta($result_reference['data'], '_product_attributes', TRUE);
                                if (isset($product_attributes) && !empty($product_attributes)) {
                                    foreach ($result_data['thedata'] as $exists_attribute_key => $exists_attribute_value) {
                                        $exists_attribute[] = $exists_attribute_key;
                                        $var[$exists_attribute_key] = $exists_attribute_value;
                                    }
                                    for ($i = 0; $i < COUNT($exists_attribute); $i++) {
                                        foreach ($product_attributes as $key => $p_attribute) {
                                            if ($exists_attribute[$i] == $key) {
                                                $thedata[$key] = $p_attribute;
                                                break;
                                            } else {
                                                $thedata[$exists_attribute[$i]] = $var[$exists_attribute[$i]];
                                            }
                                        }
                                    }
                                    if (get_option('ps_attribute') == 'off') {
                                        $thedata = array_merge($thedata, $product_attributes);
                                    }
                                    if (get_option('linksync_visiable_attr') == 0) {
                                        foreach ($thedata as $visible) {
                                            $thedata[$visible['name']]['is_visible'] = 0;
                                        }
                                    }
                                } else {
                                    $thedata = $result_data['thedata'];
                                }
                                update_post_meta($result_reference['data'], '_product_attributes', $thedata); //ADD Product Attribute
                                unset($thedata);
                                unset($result_data['thedata']);
                                unset($exists_attribute);
                                unset($var);
                            }
                            /*
                             * Max and Min variation prices
                             */
                            $variant_product_id_query = $wpdb->get_results(
                                                                $wpdb->prepare(
                                                                    "SELECT *
                                                                      FROM  `" . $wpdb->posts . "`
                                                                      WHERE
                                                                        `post_parent` = %d AND
                                                                        `post_type` =  'product_variation'"
                                                                    ,$result_reference['data']
                                                                ),ARRAY_A);

                            $variant = array();
                            foreach ($variant_product_id_query as $variant_product_ids) {
                                $variant[] = $variant_product_ids['ID'];
                            }
                            $price_list = array('_price', '_sale_price', '_regular_price');
                            $max_and_min = array('max', 'min');
                            foreach ($max_and_min as $check) {
                                foreach ($price_list as $price) {
                                    $db_prices_query = $wpdb->get_results("SELECT * FROM  `" . $wpdb->postmeta . "` WHERE  `meta_key` =  '$price' AND  `post_id` IN (" . implode(',', $variant) . ")", ARRAY_A);
                                    $max_min_price_handle = array();
                                    foreach ($db_prices_query as $db_prices) {
                                        if (isset($db_prices['meta_value']) && !empty($db_prices['meta_value'])) {
                                            $max_min_price_handle[$price][$db_prices['post_id']] = $db_prices['meta_value'];
                                        }
                                    }
                                    if (isset($max_min_price_handle[$price]) && !empty($max_min_price_handle[$price])) {
                                        $set_price_id = array_keys($max_min_price_handle[$price], $check($max_min_price_handle[$price]));
                                        $set_price = $check($max_min_price_handle[$price]);
                                        if ($price == '_price' || $check == 'min')
                                            update_post_meta($result_reference['data'], '_price', $set_price);

                                        update_post_meta($result_reference['data'], '_' . $check . '_variation' . $price, $set_price);
                                        update_post_meta($result_reference['data'], '_' . $check . $price . '_variation_id', $set_price_id[0]);
                                    }
                                }
                            }
// if all variants has qty is 0
                            if (isset($result_data['var_quantity'])) {
                                if ($result_data['var_quantity'] <= 0) {
                                    if (isset($outlet_checker) && $outlet_checker == 'noOutlet') {

                                    } else {
                                        update_post_meta($result_reference['data'], '_stock_status', 'outofstock');
                                        if (get_option('ps_unpublish') == 'on')
                                            $status = 'draft';
                                    }
                                }else {
                                    update_post_meta($result_reference['data'], '_stock_status', 'instock');
                                }
                            }
                        }
#----------------------------------------END VARIENT DATA----------------------------------------#
                    }

#Tag of the Products
                    if (get_option('ps_tags') == 'on') {
                        $term_exists['term_id'] = 0; # default parent id is 0
                        if (isset($product['tags'])) {
                            $data = $wpdb->get_results("SELECT term_taxonomy_id
                                                        FROM  `" . $wpdb->term_taxonomy . "`
                                                        WHERE taxonomy='product_tag'", ARRAY_A);

                            foreach ($data as $term_taxonmy_id) {

                                $sql_query ="DELETE FROM `" . $wpdb->term_relationships . "`
                                                WHERE object_id= %d  AND term_taxonomy_id= %d ";
                                $wpdb->query($wpdb->prepare(
                                                    $sql_query,
                                                    $result_reference['data'],
                                                    $term_taxonmy_id['term_taxonomy_id']
                                            ));
                            }
                            foreach ($product['tags'] as $tag) {
                                if (isset($tag['name']) && !empty($tag['name']) && isset($term_exists['term_id'])) {
                                    $check_term_exists = term_exists($tag['name'], 'product_tag', $term_exists['term_id']); # just check if tag with name already created
                                    if (!is_array($check_term_exists)) {
                                        $term_exists = (array) wp_insert_term($tag['name'], 'product_tag');
                                        if (isset($term_exists['term_taxonomy_id']) && $term_exists['term_id']) {

                                            $this->create_term_relationship( $result_reference['data'], $term_exists['term_taxonomy_id'] );
                                            $wpdb->query($wpdb->prepare(
                                                "UPDATE `" . $wpdb->term_taxonomy . "` SET count=count+1 WHERE term_id= %d "
                                                , $term_exists['term_id']
                                            ));
                                        }
                                    } else {

                                        $this->create_term_relationship( $result_reference['data'], $check_term_exists['term_taxonomy_id'] );
                                        $wpdb->query($wpdb->prepare(
                                            "UPDATE `" . $wpdb->prefix . "term_taxonomy` SET count=count+1 WHERE term_id= %d "
                                            , $check_term_exists['term_id']
                                        ));
                                    }
                                }
                            }
                        }
                    }
# BRAND syncing ( update )
                    if (in_array('woocommerce-brands/woocommerce-brands.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                        if (get_option('ps_brand') == 'on') {
// Delete existing brand then create
                            $term_taxonmy_id = array();
                            $data = $wpdb->get_results(
                                            "SELECT term_taxonomy_id
                                              FROM  `" . $wpdb->term_taxonomy . "`
                                              WHERE taxonomy='product_brand'",ARRAY_A);

                            foreach ($data as $exists_brands) {
                                $wpdb->query($wpdb->prepare(
                                    "  DELETE FROM `" . $wpdb->term_relationships . "`
                                        WHERE
                                            object_id= %d  AND
                                            term_taxonomy_id= %d "
                                    , $result_reference['data']
                                    , $exists_brands['term_taxonomy_id']
                                ));
                            }

                            if (isset($product['brands']) && !empty($product['brands'])) {
                                $brands = $product['brands'];

                                foreach ($brands as $brand) {
                                    if (isset($brand['name']) && !empty($brand['name'])) {
                                        if (!ctype_space($brand['name'])) { // if coming with white space
                                            $termid_taxonomy = term_exists($brand['name'], 'product_brand');
                                            if (!is_array($termid_taxonomy)) {
                                                $termid_taxonomy = @wp_insert_term($brand['name'], 'product_brand');
                                            }
                                            if (!isset($termid_taxonomy->errors)) {
//print_r($termid_taxonomy);
                                                if (isset($termid_taxonomy['term_taxonomy_id']) && isset($termid_taxonomy['term_id'])) {

                                                    $this->create_term_relationship( $result_reference['data'], $termid_taxonomy['term_taxonomy_id'] );
                                                    $wpdb->query($wpdb->prepare(
                                                        "UPDATE `" . $wpdb->term_taxonomy . "` SET count=count+1  WHERE term_id= %d"
                                                        , $termid_taxonomy['term_id']
                                                    ));

                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            unset($termid_taxonomy);
                        }
                    }

#Category
                    if (get_option('ps_categories') == 'on') {
                        include_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
                        wp_delete_object_term_relationships($result_reference['data'], 'product_cat');
                        if (get_option('cat_radio') == 'ps_cat_product_type') {
                            if (isset($product['product_type']) && !empty($product['product_type'])) {
                                $ls_product_type = esc_html($product['product_type']);

                                $term = get_term_by('name', $ls_product_type, 'product_cat');
                                if (isset($term) && !empty($term)) {
                                    wp_set_object_terms($result_reference['data'], $term->term_id, 'product_cat');
                                }
                            }
                        }
                        if (get_option('cat_radio') == 'ps_cat_tags') { //Product Tag is Selected
                            if (isset($product['tags']) && !empty($product['tags'])) {
                                foreach ($product['tags'] as $tag) {
                                    if (isset($tag['name']) && !empty($tag['name'])) {
                                        $tags = explode('/', $tag['name']);
                                        if (isset($tags) && !empty($tags)) {
                                            foreach($tags as $cat_key => $cat_name){
                                                $cat_name = esc_html(trim($cat_name));
                                                $ls_term = get_term_by('name', $cat_name, 'product_cat');
                                                if($ls_term){
                                                    wp_set_object_terms($result_reference['data'], $ls_term->term_id, 'product_cat', TRUE);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (isset($product['variants']) && !empty($product['variants'])) {
                        update_post_meta($result_reference['data'], '_regular_price', '');
                        update_post_meta($result_reference['data'], '_sale_price', '');
                    } else {
# defined fundtion to update existing product
                        if (get_option('ps_price') == 'on') {
                            $update_tax_classes = get_option('tax_class');
                            if (isset($update_tax_classes) && !empty($update_tax_classes)) {
                                $taxes_all = explode(',', $update_tax_classes);
                                if (isset($taxes_all) && !empty($taxes_all)) {
                                    foreach ($taxes_all as $taxes) {
                                        $tax = explode('|', $taxes);
                                        if (isset($tax) && !empty($tax)) {
                                            $explode_tax_name = explode('-', $tax[0]); //GST-1.0 to explode GST and 1.0
                                            if (in_array($tax_name, $explode_tax_name)) {
                                                $explode = explode(' ', $tax[1]);
                                                $implode = implode('-', $explode);
                                                $tax_mapping_name = strtolower($implode);
                                                update_post_meta($result_reference['data'], '_tax_status', 'taxable');
                                                if ($tax_mapping_name == 'standard-tax') {
                                                    $tax_mapping_name = '';
                                                }
                                                update_post_meta($result_reference['data'], '_tax_class', $tax_mapping_name);
                                            }
                                        }
                                    }
                                }
                            }

                            $db_sale_price = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM `" . $wpdb->postmeta . "`
                                WHERE `post_id` = %d  AND meta_key='_sale_price'"
                                , $result_reference['data']
                            ),ARRAY_A);
                            $db_sale_price_num_rows = $wpdb->num_rows;

                            if ($excluding_tax == 'on') {
//If 'yes' then product price SELL Price(excluding any taxes.)
                                /*
                                    Get the meta key from database
                                */
                                $price_meta = get_post_meta($result_reference['data'],'_price',true);
                                //Check if the product has been set to no price('')
                                $sell_price = $price_meta == '' && $sell_price == 0 ? '': $sell_price;

                                if (0 != $db_sale_price_num_rows) {
                                    $result_sale_price = $db_sale_price[0];
                                    if ($result_sale_price['meta_value'] == NULL) {
                                        update_post_meta($result_reference['data'], '_price', $sell_price);//$sell_price);
                                    }
                                } else {
                                    update_post_meta($result_reference['data'], '_price', $sell_price);//$sell_price);
                                }

                                if (get_option('price_field') == 'regular_price') {
                                    update_post_meta($result_reference['data'], '_regular_price', $sell_price);
                                } else {
                                    update_post_meta($result_reference['data'], '_price', $sell_price);
                                    update_post_meta($result_reference['data'], '_sale_price', $sell_price);
                                }
                            } else {
//If 'no' then product price SELL Price(including any taxes.)
                                $tax_and_sell_price_product = $sell_price + $product['tax_value'];
                                if (0 != $db_sale_price_num_rows) {
                                    $result_sale_price = $db_sale_price[0];
                                    if ($result_sale_price['meta_value'] == NULL) {
                                        update_post_meta($result_reference['data'], '_price', $tax_and_sell_price_product);
                                    }
                                } else {
                                    update_post_meta($result_reference['data'], '_price', $tax_and_sell_price_product);
                                }
                                if (get_option('price_field') == 'regular_price') {
                                    update_post_meta($result_reference['data'], '_regular_price', $tax_and_sell_price_product);
                                } else {
                                    update_post_meta($result_reference['data'], '_price', $tax_and_sell_price_product);
                                    update_post_meta($result_reference['data'], '_sale_price', $tax_and_sell_price_product);
                                }
                            }
                        }
                    }
#Product Quantity Update
                    /*
                        Reference: Product Quantity Updated
                        Product quantity updated if "Sync product Quantity between apps" is checked
                    */
                    if (get_option('ps_quantity') == 'on') {
                        if (isset($product['variants']) && !empty($product['variants'])) {

                        } else {
                            if (isset($outlet_checker) && $outlet_checker == 'noOutlet') {
                                update_post_meta($result_reference['data'], '_manage_stock', 'no');
                                update_post_meta($result_reference['data'], '_stock', NULL);
                                update_post_meta($result_reference['data'], '_stock_status', 'instock');
                            } else {
                                update_post_meta($result_reference['data'], '_manage_stock', 'yes');
                                update_post_meta($result_reference['data'], '_stock', $quantity);
                                update_post_meta($result_reference['data'], '_stock_status', ($quantity > 0 ? 'instock' : 'outofstock'));
                                if (get_option('ps_unpublish') == 'on' && $quantity < 1) {
                                    $status = 'draft';
                                }
                            }
                            unset($outlet_checker);
                        }
                    }
#End Product Quantity Update
#Product Image
                    if (get_option('ps_images') == 'on') {
//Product Gallery Image
                        $woo_filename_gallery = array();
                        $image_query = $wpdb->get_results($wpdb->prepare(
                                                    "SELECT meta_value
                                                      FROM  `" . $wpdb->postmeta . "`
                                                      WHERE
                                                            meta_key='_product_image_gallery' AND
                                                            `post_id` = %d "
                                                    , $result_reference['data']
                                                )
                                        , ARRAY_A);
                        $result_image = $wpdb->num_rows;
                        if ( 0 != $result_image) {
                            $image = $image_query[0];
                            if (isset($image['meta_value']) && !empty($image['meta_value'])) {
                                if (strpos($image['meta_value'], ','))
                                    $images_postId = explode(',', $image['meta_value']);
                                else
                                    $images_postId[] = $image['meta_value'];

                                if (isset($images_postId) && !empty($images_postId)) {
                                    foreach ($images_postId as $value) {
                                        $wp_attached_file = get_post_meta($value, '_wp_attached_file', true); // returns an array
                                        if (isset($wp_attached_file) && !empty($wp_attached_file)) {
                                            $woo_filename_gallery[$value] = basename($wp_attached_file);
                                        }
                                    }
                                }
                            }
                        }
                        $current_user_id = get_current_user_id();
                        foreach ($product['images'] as $key => $images) {
                            $vend_image_data[$key . '|' . $images['url']] = basename($images['url']);
                        }
                        if ($current_user_id == 0) {
// logged_one is 'System';
                            if (isset($product['images']) && !empty($product['images'])) {
//Thumbnail Image data
                                if (isset($product['images'][0]['url']) && !empty($product['images'][0]['url'])) {
                                    /*
                                     *  Ongoing Seleted->This option provides the same function as 'Once',
                                     *  but will update product images if the they are modified in Vend.
                                     *  For example, if you update an image for a product in Vend, then that update images will be synced to the corresponding
                                     *  product in WooCommerce.
                                     */
                                    if (get_option('ps_import_image_radio') == 'Ongoing') {
                                        $image_query = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT meta_value FROM  `" . $wpdb->postmeta . "`
                                                                                WHERE
                                                                                    meta_key='_thumbnail_id' AND
                                                                                    `post_id` = %d "
                                                                            , $result_reference['data']
                                                        ), ARRAY_A);
                                        if ($wpdb->num_rows > 1) {
                                            foreach ($image_query as $images) {
                                                if (isset($images['meta_value']) && !empty($images['meta_value'])) {
                                                    $image_attributes = get_post_meta($images['meta_value'], '_wp_attached_file', true); // returns an array  @wp_get_attachment_image_src($image[0]);
                                                    if (isset($image_attributes) && !empty($image_attributes)) {
                                                        $path_parts = pathinfo($image_attributes);
                                                        $wp_upload_dir = @wp_upload_dir();
                                                        $ext = substr($image_attributes, strrpos($image_attributes, "."));
                                                        $filename = $wp_upload_dir['basedir'] . '/' . $path_parts['dirname'] . '/' . basename($image_attributes, $ext);
                                                        foreach (glob("$filename*") as $filename) {
                                                            if (file_exists($filename)) {
                                                                unlink($filename);
                                                            }
                                                        }
                                                    }
                                                    delete_post_meta($result_reference['data'], '_thumbnail_id');
                                                }
                                            }
                                        }
                                        $image = $image_query[0];
                                        if (isset($image['meta_value']) && !empty($image['meta_value'])) {
                                            $image_attributes = get_post_meta($image['meta_value'], '_wp_attached_file', true); // returns an array  @wp_get_attachment_image_src($image[0]);
                                            if (isset($image_attributes) && !empty($image_attributes)) {
                                                checkAndDelete_attachement(basename($image_attributes));
                                                if (in_array(basename($image_attributes), $vend_image_data)) {
                                                    $product_image_search = array_search(basename($image_attributes), $vend_image_data);
                                                    $result_image_search = explode('|', $product_image_search);
                                                    unset($product['images'][$result_image_search[0]]);
                                                } else {
                                                    addImage_thumbnail($product['images'][0]['url'], $result_reference['data']);
                                                    unset($product['images'][0]);
                                                }
                                            }
                                        } else {
                                            if (!in_array(basename($product['images'][0]['url']), $woo_filename_gallery)) {
                                                addImage_thumbnail($product['images'][0]['url'], $result_reference['data']);
                                                unset($product['images'][0]);
                                            } else {
                                                $left_upload = array_diff($vend_image_data, $woo_filename_gallery);
                                                foreach ($left_upload as $upload => $value_upload) {
                                                    $image_left = explode('|', $upload);
                                                    addImage_thumbnail($image_left[1], $result_reference['data']);
                                                    unset($product['images'][$image_left[0]]);
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    /*
                                     * Enable (Once)-> This option will sync images from Vend to WooCommerce products on creation of a new product,
                                     *  or if an existing product in WooCommerce does not have an image.
                                     */ elseif (get_option('ps_import_image_radio') == 'Enable') {
                                        $thumb_query = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT meta_value
                                                                              FROM  `" . $wpdb->postmeta . "`
                                                                              WHERE
                                                                                    meta_key='_thumbnail_id' AND
                                                                                    `post_id` = %d "
                                                                            , $result_reference['data']
                                                        ), ARRAY_A);
                                        $image = $wpdb->num_rows;
                                        if ($image != 0) {
                                            $image_id = $thumb_query[0];
                                            $image_name = get_post_meta($image_id['meta_value'], '_wp_attached_file', TRUE);
                                            $unsetvalue = array_search(basename($image_name), $vend_image_data);
                                            if ($unsetvalue) {
                                                $image_left = explode('|', $unsetvalue);
                                                unset($product['images'][$image_left[0]]);
                                            }
                                        } else {
                                            if (!in_array(basename($product['images'][0]['url']), $woo_filename_gallery)) {
                                                addImage_thumbnail($product['images'][0]['url'], $result_reference['data']);
                                                unset($product['images'][0]);
                                            } else {
                                                $left_upload = array_diff($vend_image_data, $woo_filename_gallery);
                                                foreach ($left_upload as $upload => $value_upload) {
                                                    $image_left = explode('|', $upload);
                                                    addImage_thumbnail($image_left[1], $result_reference['data']);
                                                    unset($product['images'][$image_left[0]]);
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                if (isset($product['images']) && !empty($product['images'])) {
                                    foreach ($product['images'] as $images) {
                                        if (get_option('ps_import_image_radio') == 'Ongoing') {
                                            if (!in_array(basename($images['url']), $woo_filename_gallery)) {
                                                $attach_ids[] = linksync_insert_image($images['url'], $result_reference['data']);
                                            } else {
                                                $attach_ids[] = array_search(basename($images['url']), $woo_filename_gallery);
                                            }
                                        } elseif (get_option('ps_import_image_radio') == 'Enable') {
                                            $db_query = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT * FROM  `" . $wpdb->postmeta . "`
                                                                                WHERE
                                                                                    meta_key='_product_image_gallery' AND
                                                                                    `post_id` = %d "
                                                                            , $result_reference['data']
                                                        ), ARRAY_A);
                                            $image = $wpdb->num_rows;
                                            if ($image != 0) {

                                            } else {
                                                $attach_ids[] = linksync_insert_image($images['url'], $result_reference['data']);
                                            }
                                        }
                                    }
                                }
                                if (get_option('ps_import_image_radio') == 'Ongoing') {
                                    if (isset($attach_ids) && !empty($attach_ids)) {
                                        $product_image_gallery = implode(",", $attach_ids);
                                        update_post_meta($result_reference['data'], '_product_image_gallery', $product_image_gallery);
                                    } else {
                                        update_post_meta($result_reference['data'], '_product_image_gallery', '');
                                    }
                                } elseif (get_option('ps_import_image_radio') == 'Enable') {
                                    if (isset($attach_ids) && !empty($attach_ids)) {
                                        $product_image_gallery = implode(",", $attach_ids);
                                        add_post_meta($result_reference['data'], '_product_image_gallery', $product_image_gallery);
                                    }
                                }
                                unset($attach_ids);
                                unset($product_image_gallery);
                            } else {
                                if (get_option('product_sync_type') != 'two_way') {
                                    if (get_option('ps_import_image_radio') == 'Ongoing') {
                                        $db_query = $wpdb->get_results($wpdb->prepare(
                                                                        "SELECT * FROM  `" . $wpdb->postmeta . "`
                                                                            WHERE
                                                                                meta_key='_product_image_gallery' AND
                                                                                `post_id` = %d "
                                                                        , $result_reference['data'])
                                                    , ARRAY_A);
                                        $image = $wpdb->num_rows;
                                        if ($image != 0) {
                                            update_post_meta($result_reference['data'], '_product_image_gallery', '');
                                        }
                                        $db_query = $wpdb->get_results($wpdb->prepare(
                                                                        "SELECT * FROM  `" . $wpdb->postmeta . "`
                                                                            WHERE
                                                                                meta_key='_thumbnail_id' AND
                                                                                `post_id` = %d "
                                                                        , $result_reference['data']
                                                    ),ARRAY_A);
                                        $thumbnail_image = $wpdb->num_rows;
                                        if ($thumbnail_image != 0) {
                                            update_post_meta($result_reference['data'], '_thumbnail_id', '');
                                        }
                                    }
                                }
                            }
                        } else {
                            if (isset($product['images']) && !empty($product['images'])) {
//logged_one is current_user
                                if (isset($product['images'][0]['url']) && !empty($product['images'][0]['url'])) {
                                    if (get_option('ps_import_image_radio') == 'Ongoing') {

                                        $image_query = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT meta_value
                                                                              FROM  `" . $wpdb->postmeta . "`
                                                                              WHERE
                                                                                    meta_key='_thumbnail_id' AND
                                                                                    `post_id` = %d "
                                                                            , $result_reference['data']
                                                        ), ARRAY_A);
                                        $image = $image_query[0];
                                        if (isset($image['meta_value']) && !empty($image['meta_value'])) {
                                            $image_attributes = get_post_meta($image['meta_value'], '_wp_attached_file', true); // returns an array  @wp_get_attachment_image_src($image[0]);
                                            if (isset($image_attributes) && !empty($image_attributes)) {
                                                if (in_array(basename($image_attributes), $vend_image_data)) {
                                                    $product_image_search = array_search(basename($image_attributes), $vend_image_data);
                                                    $result_image_search = explode('|', $product_image_search);
                                                    unset($product['images'][$result_image_search[0]]);
                                                    unset($vend_image_data);
                                                    unset($product_image_search);
                                                } else {
                                                    update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $product['images'][0]['url']);
                                                    unset($product['images'][0]);
                                                }
                                            }
                                        } else {
                                            if (isset($woo_filename_gallery) && !empty($woo_filename_gallery)) {
                                                if (!in_array(basename($product['images'][0]['url']), $woo_filename_gallery)) {
                                                    update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $product['images'][0]['url']);
                                                    unset($product['images'][0]);
                                                } else {
                                                    $left_upload = array_diff($vend_image_data, $woo_filename_gallery);
                                                    foreach ($left_upload as $upload => $value_upload) {
                                                        $image_left = explode('|', $upload);
                                                        update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $image_left[1]);
                                                        unset($product['images'][$image_left[0]]);
                                                        break;
                                                    }
                                                }
                                            } else {
                                                update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $product['images'][0]['url']);
                                                unset($product['images'][0]);
                                            }
                                        }
                                    } elseif (get_option('ps_import_image_radio') == 'Enable') {
                                        $image_query = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT meta_value FROM  `" . $wpdb->postmeta . "`
                                                                                WHERE
                                                                                    meta_key='_thumbnail_id' AND
                                                                                    `post_id` = %d "
                                                                            , $result_reference['data']
                                                        ), ARRAY_A);
                                        $image = $image_query[0];
                                        if (isset($image['meta_value']) && !empty($image['meta_value'])) {
                                            $image_attributes = get_post_meta($image['meta_value'], '_wp_attached_file', true); // returns an array  @wp_get_attachment_image_src($image[0]);
                                            if (isset($image_attributes) && !empty($image_attributes)) {
                                                if (in_array(basename($image_attributes), $vend_image_data)) {
                                                    $product_image_search = array_search(basename($image_attributes), $vend_image_data);
                                                    $result_image_search = explode('|', $product_image_search);
                                                    unset($product['images'][$result_image_search[0]]);
                                                    unset($vend_image_data);
                                                    unset($product_image_search);
                                                } else {
                                                    if (isset($woo_filename_gallery) && !empty($woo_filename_gallery)) {
                                                        if (!in_array(basename($product['images'][0]['url']), $woo_filename_gallery)) {
                                                            update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $product['images'][0]['url']);
                                                            unset($product['images'][0]);
                                                        } else {
                                                            $left_upload = array_diff($vend_image_data, $woo_filename_gallery);
                                                            foreach ($left_upload as $upload => $value_upload) {
                                                                $image_left = explode('|', $upload);
                                                                update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $image_left[1]);
                                                                unset($product['images'][$image_left[0]]);
                                                                break;
                                                            }
                                                        }
                                                    } else {
                                                        update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $product['images'][0]['url']);
                                                        unset($product['images'][0]);
                                                    }
                                                }
                                            }
                                        } else {
                                            if (isset($woo_filename_gallery) && !empty($woo_filename_gallery)) {
                                                if (!in_array(basename($product['images'][0]['url']), $woo_filename_gallery)) {
                                                    update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $product['images'][0]['url']);
                                                    unset($product['images'][0]);
                                                } else {
                                                    $left_upload = array_diff($vend_image_data, $woo_filename_gallery);
                                                    foreach ($left_upload as $upload => $value_upload) {
                                                        $image_left = explode('|', $upload);
                                                        update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $image_left[1]);
                                                        unset($product['images'][$image_left[0]]);
                                                        break;
                                                    }
                                                }
                                            } else {
                                                update_post_meta($result_reference['data'], 'Vend_thumbnail_image', $product['images'][0]['url']);
                                                unset($product['images'][0]);
                                            }
                                        }
                                    }
                                } else {
                                    if (get_option('ps_import_image_radio') == 'Ongoing') {
                                        update_post_meta($result_reference['data'], '_thumbnail_id', '');
                                    }
                                }
                                if (isset($product['images']) && !empty($product['images'])) {
                                    delete_post_meta($result_reference['data'], 'Vend_product_image_gallery');
                                    foreach ($product['images'] as $images) {
                                        $db_query = $wpdb->get_results($wpdb->prepare(
                                                                        "SELECT * FROM  `" . $wpdb->postmeta . "`
                                                                        WHERE
                                                                            meta_key='Vend_product_image_gallery' AND
                                                                            `post_id` = %d "
                                                                        , $result_reference['data']
                                                    ), ARRAY_A);

                                        $vend_gallery_image = $wpdb->num_rows;
                                        if ($vend_gallery_image != 0) {
                                            $wpdb->query($wpdb->prepare(
                                                        "UPDATE `" . $wpdb->postmeta . "`
                                                        SET meta_value=CONCAT(meta_value,',$images[url]')
                                                        WHERE
                                                            post_id= %d  AND
                                                            meta_key='Vend_product_image_gallery'"
                                                        , $result_reference['data']
                                            ));
                                        }
                                        else
                                            add_post_meta($result_reference['data'], 'Vend_product_image_gallery', $images['url']);
                                    }
                                }else {
                                    update_post_meta($result_reference['data'], '_product_image_gallery', '');
                                }

                                unset($product['images']);
                            } else {
                                if (get_option('product_sync_type') != 'two_way') {
                                    if (get_option('ps_import_image_radio') == 'Ongoing') {
                                        update_post_meta($result_reference['data'], '_thumbnail_id', '');
                                        update_post_meta($result_reference['data'], '_product_image_gallery', '');
                                    }
                                }
                            }
                        }
                    }
// if product in vend having status : inactive ( active==0  ) it should be not displayed (mark as draft in woo)
                    if ($product['active'] == '0') {
                        $status = 'draft';
                    }
#---------GET product Status-------------#
                    /*
                        Reference: Update Product status
                    */
                    $product_status_db = $wpdb->get_results($wpdb->prepare(
                                                            "SELECT post_status
                                                              FROM `" . $wpdb->posts . "`
                                                              WHERE
                                                                    post_status ='pending' AND
                                                                    ID= %d "
                                                            , $result_reference['data']
                                        ), ARRAY_A);
                    if (0 != $wpdb->num_rows ) {
                        $status = 'pending';
                    }
                    /*
                        If 'Change product status in WooCommerce based on stock quantity' is checked or on
                    */
                    if (get_option('ps_unpublish') == 'on') {
                        $status = isset($status) && !empty($status) ? $status : 'publish';



                        if (isset($product['variants']) && !empty($product['variants'])){
                            $quantity = $this->get_total_variants_quantity($product['variants']);
                            if($quantity <= 0){
                                $status = 'draft';
                                //Product variation should be 'Out of Stock'
                                $product__stock_status = 'outofstock';

                            }else if($quantity > 0){
                                $status = ($product['active'] == 1) ? 'publish': 'draft';
                                //Make user that variation should be 'instock'
                                $product__stock_status = 'instock';
                            }


                            //Check quantity less or equal to zero
                            //Note $quantity came from `Reference: Product Quantity Updated` line number 667
                        }else if($quantity <= 0){
                            $status = 'draft';
                            //Product variation should be 'Out of Stock'
                            $product__stock_status = 'outofstock';

                        }else if($quantity > 0){
                            $status = ($product['active'] == 1) ? 'publish': 'draft';
                            //Make user that variation should be 'instock'
                            $product__stock_status = 'instock';
                        }
                        //Update products _stock_status
                        update_post_meta($result_reference['data'],'_stock_status',$product__stock_status);
                    } else {

                        $status = get_post_status($result_reference['data']);//Just use the woocommerce status data

                    }

                    $my_product = array();
                    $my_product['ID'] = $result_reference['data'];
                    $my_product['post_status'] = $status;
                    $my_product['post_modified'] = current_time('mysql');
                    $my_product['post_modified_gmt'] = gmdate('Y-m-d h:i:s');
                    if (get_option('ps_name_title') == 'on')
                        $my_product['post_title'] = $name;
//Import Description
                    if (get_option('ps_description') == 'on') {
                        $my_product['post_content'] = isset($description) && !empty($description) ? $description : '';
                    }
// Import Copy Short Description
                    if (get_option('ps_desc_copy') == 'on') {
                        $my_product['post_excerpt'] = isset($description) && !empty($description) ? $description : '';
                    }

                    update_post_meta($result_reference['data'], '_visibility', ($status == 'publish' ? 'visible' : ''));

//Update product Post
                    wp_update_post($my_product);
                    unset($status);
                    wc_delete_product_transients($result_reference['data']);
                    /*
                     * Ending Update product
                     */
                }
            } elseif ($result_reference['result'] == 'error') {
                /*
                 * New Product Creation if "Create New" option enabled
                 */
                if ($ps_create_new == 'on' && empty($product['deleted_at'])) { # it's new product
                    $status = '';
// code for adding new product int WC
                    $my_post = array(
                        'post_author' => 1,
//                        'post_date' => current_time('mysql'),
//                        'post_date_gmt' => gmdate('Y-m-d h:i:s'),
//                        'post_modified' => current_time('mysql'),
//                        'post_modified_gmt' => gmdate('Y-m-d h:i:s'),
                        'post_type' => 'product'
                    );
//Import Name
//  if (get_option('ps_name_title') == 'on') #we have used wp_insert_post() function that required at least one parameters
                    $my_post['post_title'] = $product['name'];
//Import Description
                    if (get_option('ps_description') == 'on')
                        $my_post['post_content'] = isset($description) && !empty($description) ? $description : '';
// Import Copy Short Description
                    if (get_option('ps_desc_copy') == 'on')
                        $my_post['post_excerpt'] = isset($description) && !empty($description) ? $description : '';
                    $product_ID = wp_insert_post($my_post);
                    $product_ids[] = $product_ID . '|new_id';
                    if ($product_ID) {
                        add_post_meta($product_ID, '_sku', $product['sku']);
#Tag of the Products
                        if (get_option('ps_tags') == 'on') {
                            $term_exists['term_id'] = 0;
                            foreach ($product['tags'] as $tag) {
                                if (isset($tag['name']) && !empty($tag['name'])) {
                                    $check_term_exists = term_exists($tag['name'], 'product_tag', $term_exists['term_id']);
                                    if (!is_array($check_term_exists))
                                        $term_exists = wp_insert_term($tag['name'], 'product_tag');
                                    $term_exists = term_exists($tag['name'], 'product_tag');
                                    if (is_array($term_exists)) {

                                        $this->create_term_relationship( $product_ID, $term_exists['term_taxonomy_id'] );
                                        $wpdb->query($wpdb->prepare(
                                                        "UPDATE `" . $wpdb->term_taxonomy . "` SET count=count+1
                                                        WHERE term_id= %d "
                                                        , $term_exists['term_id']
                                        ));

                                    }
                                }
                            }
                        }
# BRAND syncing
                        if (in_array('woocommerce-brands/woocommerce-brands.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                            if (get_option('ps_brand') == 'on') {
                                if (isset($product['brands']) && !empty($product['brands'])) {
                                    $brands = $product['brands'];
                                    foreach ($brands as $brand) {
                                        if (isset($brand['name']) && !empty($brand['name'])) {
                                            if (!ctype_space($brand['name'])) {
                                                $termid_taxonomy = term_exists($brand['name'], 'product_brand');
                                                if (!is_array($termid_taxonomy)) {
                                                    $termid_taxonomy = @wp_insert_term($brand['name'], 'product_brand');
                                                }
                                                if (!isset($termid_taxonomy->errors)) {
                                                    if (isset($termid_taxonomy['term_taxonomy_id']) && isset($termid_taxonomy['term_id'])) {

                                                        $this->create_term_relationship( $product_ID, $termid_taxonomy['term_taxonomy_id'] );
                                                       $wpdb->query($wpdb->prepare(
                                                                    "UPDATE `" . $wpdb->term_taxonomy . "`
                                                                    SET count=count+1
                                                                    WHERE term_id= %d "
                                                                    , $termid_taxonomy['term_id']
                                                       ));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                unset($termid_taxonomy);
                            }
                        }

#Category
                        if (get_option('ps_categories') == 'on') {
                            include_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
                            if (get_option('cat_radio') == 'ps_cat_product_type') {
                                if (isset($product['product_type']) && !empty($product['product_type'])) {
                                    $term = get_term_by('name', $product['product_type'], 'product_cat');
                                    if (isset($term) && !empty($term)) {
                                        wp_set_object_terms($result_reference['data'], $term->term_id, 'product_cat');
                                    }
                                }
                            }
                            if (get_option('cat_radio') == 'ps_cat_tags') { //Product Tag is Selected
                                if (isset($product['tags']) && !empty($product['tags'])) {
                                    foreach ($product['tags'] as $tag) {
                                        if (isset($tag['name']) && !empty($tag['name'])) {
                                            $tags = explode('/', $tag['name']);
                                            if (isset($tags) && !empty($tags)) {

                                                foreach($tags as $cat_key => $cat_name){
                                                    $cat_name = esc_html(trim($cat_name));
                                                    $ls_term = get_term_by('name', $cat_name, 'product_cat');
                                                    if($ls_term){
                                                        wp_set_object_terms($product_ID, $ls_term->term_id, 'product_cat', TRUE);
                                                    }
                                                }

                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (isset($product['variants']) && !empty($product['variants'])) {
                            add_post_meta($product_ID, '_regular_price', '');
                            add_post_meta($product_ID, '_sale_price', '');
                        } else {
                            if (get_option('ps_price') == 'on') {
                                $new_product_taxes = get_option('tax_class');
                                if (isset($new_product_taxes) && !empty($new_product_taxes)) {
                                    $taxes_all = explode(',', $new_product_taxes);
                                    if (isset($taxes_all) && !empty($taxes_all)) {
                                        foreach ($taxes_all as $taxes) {
                                            $tax = explode('|', $taxes);
                                            if (isset($tax) && !empty($tax)) {
                                                $explode_tax_name = explode('-', $tax[0]); //GST-1.0 to explode GST and 1.0
                                                if (in_array($tax_name, $explode_tax_name)) {
                                                    $explode = explode(' ', $tax[1]);
                                                    $implode = implode('-', $explode);
                                                    $tax_mapping_name = strtolower($implode);
                                                    add_post_meta($product_ID, '_tax_status', 'taxable');
                                                    if ($tax_mapping_name == 'standard-tax') {
                                                        $tax_mapping_name = '';
                                                    }
                                                    add_post_meta($product_ID, '_tax_class', $tax_mapping_name);
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($excluding_tax == 'on') {
//If 'yes' then product price SELL Price(excluding any taxes.)
                                    add_post_meta($product_ID, '_price', $sell_price);
                                    if (get_option('price_field') == 'regular_price') {
                                        add_post_meta($product_ID, '_regular_price', $sell_price);
                                    } else {
                                        add_post_meta($product_ID, '_sale_price', $sell_price);
                                    }
                                } else {
//If 'no' then product price SELL Price(including any taxes.)
                                    $tax_and_sell_price_product = $sell_price + $product['tax_value'];
                                    add_post_meta($product_ID, '_price', $tax_and_sell_price_product);
                                    if (get_option('price_field') == 'regular_price') {
                                        add_post_meta($product_ID, '_regular_price', $tax_and_sell_price_product);
                                    } else {
                                        add_post_meta($product_ID, '_sale_price', $tax_and_sell_price_product);
                                    }
                                }
                            }
                        }

#Product Image
                        if (get_option('ps_images') == 'on') {
                            $current_user_id = get_current_user_id();
                            if (get_option('ps_import_image_radio') == 'Enable' || get_option('ps_import_image_radio') == 'Ongoing') {
                                if (isset($product['images']) && !empty($product['images'])) {
                                    if (isset($product['images'][0]['url']) && !empty($product['images'][0]['url'])) {
                                        if ($current_user_id == 0) {
                                            addImage_thumbnail($product['images'][0]['url'], $product_ID);
                                        } else {
                                            add_post_meta($product_ID, 'Vend_thumbnail_image', $product['images'][0]['url']);
                                        }
                                    }
                                    unset($product['images'][0]);
                                    foreach ($product['images'] as $images) {
                                        if ($current_user_id == 0) {
                                            $attach_ids = linksync_insert_image($images['url'], $product_ID);
                                            $imageDb = get_post_meta($product_ID, '_product_image_gallery');
                                            if (isset($imageDb) && !empty($imageDb)){
                                                $wpdb->query($wpdb->prepare(
                                                    "UPDATE `" . $wpdb->postmeta . "`
                                                     SET meta_value=CONCAT(meta_value,',$attach_ids')
                                                     WHERE post_id= %d  AND meta_key='_product_image_gallery'"
                                                    , $product_ID
                                                ));
                                            }else
                                                add_post_meta($product_ID, '_product_image_gallery', $attach_ids);

                                            unset($attach_ids);
                                        } else {
                                            $vend_gallery_image = get_post_meta($product_ID, 'Vend_product_image_gallery');
                                            if (isset($vend_gallery_image) && !empty($vend_gallery_image)){
                                                $wpdb->query($wpdb->prepare(
                                                            "UPDATE `" . $wpdb->postmeta . "`
                                                            SET meta_value=CONCAT(meta_value,',$images[url]')
                                                            WHERE post_id= %d AND meta_key='Vend_product_image_gallery'"
                                                            , $product_ID
                                                ));

                                            }else
                                                add_post_meta($product_ID, 'Vend_product_image_gallery', $images['url']);
                                        }
                                    }
                                }
                            }
                        }

#-------------------------VARIENT DATA--------------------------------#
                        if (isset($product['variants']) && !empty($product['variants'])) {
                            $thedata = array();
                            $var_qty = 0;
                            $price_max = array(
                                'max' => 0,
                                'max_variable_id' => 0
                            );
                            $price_min = array(
                                'min' => 0,
                                'min_variable_id' => 0
                            );
                            wp_set_object_terms($product_ID, 'variable', 'product_type'); //this will create a variable product
                            foreach ($product['variants'] as $product_variants) {
                                if ($product_variants['deleted_at'] == null) {
                                    $variant_status = 'publish';
//  $list_price = $product_variants['list_price'];
                                    $sell_price = $product_variants['sell_price'];
                                    if (count($product_variants['outlets']) != 0) {
                                        $variant_quantity = 0;
                                        foreach ($product_variants['outlets'] as $outlet) {
                                            $product_type = get_option('product_sync_type');
                                            if ($product_type == 'two_way') {
                                                $selected_outlet = get_option('wc_to_vend_outlet_detail');
                                                $outlet_id = explode('|', $selected_outlet);
                                                if ($outlet_id[1] == $outlet['outlet_id']) {
                                                    $variant_quantity+=(float) ($outlet['quantity']);
                                                }
                                            } elseif ($product_type == 'vend_to_wc-way') {
                                                $selected_outlet = get_option('ps_outlet_details');
                                                $outlet_id = explode('|', $selected_outlet);
                                                foreach ($outlet_id as $id) {
                                                    if ($id == $outlet['outlet_id']) {
                                                        $variant_quantity+=(float) ($outlet['quantity']);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $outlet_checker_variant = 'noOutlet';
                                    }
                                    if (isset($variant_quantity)) {
                                        $var_qty+=$variant_quantity;
                                    }
                                    $tax_name = $product_variants['tax_name'];
                                    $my_post = array(
                                        'post_title' => $product_variants['name'],
                                        'post_status' => $variant_status,
                                        'post_author' => 1,
//                                            'post_date' => current_time('mysql'),
//                                            'post_date_gmt' => gmdate('Y-m-d h:i:s'),
//                                            'post_modified' => current_time('mysql'),
//                                            'post_modified_gmt' => gmdate('Y-m-d h:i:s'),
                                        'post_type' => 'product_variation',
                                        'post_parent' => $product_ID
                                    );
                                    $variation_product_id = wp_insert_post($my_post);
                                    if ($variation_product_id) {
                                        add_post_meta($variation_product_id, '_sku', $product_variants['sku']);
                                        add_post_meta($variation_product_id, '_visibility', ($variant_status == 'publish' ? 'visible' : ''));

                                        if (get_option('ps_price') == 'on') {
                                            $new_variant_taxes = get_option('tax_class');
                                            if (isset($new_variant_taxes) && !empty($new_variant_taxes)) {
                                                $taxes_all = explode(',', $new_variant_taxes);
                                                if (isset($taxes_all) && !empty($taxes_all)) {
                                                    foreach ($taxes_all as $taxes) {
                                                        $tax = explode('|', $taxes);
                                                        if (isset($tax) && !empty($tax)) {
                                                            $explode_tax_name = explode('-', $tax[0]); //GST-1.0 to explode GST and 1.0
                                                            if (in_array($tax_name, $explode_tax_name)) {
                                                                $explode = explode(' ', $tax[1]);
                                                                $implode = implode('-', $explode);
                                                                $tax_mapping_name = strtolower($implode);
                                                                add_post_meta($variation_product_id, '_tax_status', 'taxable');
                                                                if ($tax_mapping_name == 'standard-tax') {
                                                                    $tax_mapping_name = '';
                                                                }
                                                                add_post_meta($variation_product_id, '_tax_class', $tax_mapping_name);
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            if ($excluding_tax == 'on') {
                                                if ($price_max['max'] == 0) {
                                                    $price_max['max'] = $sell_price;
                                                    $price_max['max_variable_id'] = $variation_product_id;
                                                }
                                                if ($price_max['max'] <= $sell_price) {
                                                    $price_max['max'] = $sell_price;
                                                    $price_max['max_variable_id'] = $variation_product_id;
                                                }
                                                if ($price_min['min'] == 0) {
                                                    $price_max['min'] = $sell_price;
                                                    $price_max['min_variable_id'] = $variation_product_id;
                                                }
                                                if ($price_min['min'] >= $sell_price) {
                                                    $price_max['min'] = $sell_price;
                                                    $price_max['min_variable_id'] = $variation_product_id;
                                                }
//If 'yes' then product price SELL Price(excluding any taxes.)
                                                add_post_meta($variation_product_id, '_price', $sell_price);
                                                if (get_option('price_field') == 'regular_price') {
                                                    add_post_meta($variation_product_id, '_regular_price', $sell_price);
                                                } else {
                                                    add_post_meta($variation_product_id, '_sale_price', $sell_price);
                                                }
                                            } else {
//If 'no' then product price SELL Price(including any taxes.)
                                                $tax_and_sell_price_variant = $sell_price + $product_variants['tax_value'];
                                                if ($price_max['max'] == 0) {
                                                    $price_max['max'] = $tax_and_sell_price_variant;
                                                    $price_max['max_variable_id'] = $variation_product_id;
                                                }
                                                if ($price_max['max'] <= $tax_and_sell_price_variant) {
                                                    $price_max['max'] = $tax_and_sell_price_variant;
                                                    $price_max['max_variable_id'] = $variation_product_id;
                                                }
                                                if ($price_min['min'] == 0) {
                                                    $price_max['min'] = $tax_and_sell_price_variant;
                                                    $price_max['min_variable_id'] = $variation_product_id;
                                                }
                                                if ($price_min['min'] >= $tax_and_sell_price_variant) {
                                                    $price_max['min'] = $tax_and_sell_price_variant;
                                                    $price_max['min_variable_id'] = $variation_product_id;
                                                }
                                                add_post_meta($variation_product_id, '_price', $tax_and_sell_price_variant);
                                                if (get_option('price_field') == 'regular_price') {
                                                    add_post_meta($variation_product_id, '_regular_price', $tax_and_sell_price_variant);
                                                } else {
                                                    add_post_meta($variation_product_id, '_sale_price', $tax_and_sell_price_variant);
                                                }
                                            }
                                        }
#Product Quantity
                                        if (get_option('ps_quantity') == 'on') {
                                            if (isset($outlet_checker_variant) && $outlet_checker_variant == 'noOutlet') {
                                                add_post_meta($variation_product_id, '_manage_stock', 'no');
                                                add_post_meta($variation_product_id, '_stock', NULL);
                                                add_post_meta($variation_product_id, '_stock_status', 'instock');
                                            } else {
                                                add_post_meta($variation_product_id, '_manage_stock', 'yes');
                                                add_post_meta($variation_product_id, '_stock', $variant_quantity);
                                                add_post_meta($variation_product_id, '_stock_status', ($variant_quantity > 0 ? 'instock' : 'outofstock'));
                                            }
                                            unset($outlet_checker_variant);
                                        } else {
                                            add_post_meta($variation_product_id, '_manage_stock', 'no');
                                        }
                                    }
                                    for ($i = 1; $i <= 3; $i++) {
                                        if (!empty($product_variants['option_' . $option[$i] . '_name'])) {
                                            /*
                                             * check attribute lable Exists or Not tbl->woocommerce_attribute_taxonomies
                                             */
                                            $attribute_name = $this->linksync_check_attribute_label($product_variants['option_' . $option[$i] . '_name']);
                                            $visible = get_option('linksync_visiable_attr');
                                            $thedata['pa_' . $attribute_name] = Array(
                                                'name' => 'pa_' . $attribute_name,
                                                'value' => '',
                                                'is_visible' => $visible,
                                                'is_variation' => '1',
                                                'is_taxonomy' => '1'
                                            );
                                            /*
                                             * check Term value  Exists or Not tbl->term
                                             */
                                            if (!empty($product_variants['option_' . $option[$i] . '_value'])) {
                                                $term_slug = $this->linksync_check_term_value($product_variants['option_' . $option[$i] . '_value']);
                                                if (isset($term_slug) && !empty($term_slug)) {
                                                    add_post_meta($variation_product_id, "attribute_pa_" . strtolower($attribute_name), strtolower($term_slug['slug']));
                                                    $taxonomy_query = $wpdb->get_results($wpdb->prepare(
                                                                                            "SELECT * FROM `" . $wpdb->term_taxonomy . "`
                                                                                                WHERE
                                                                                                    term_id = %d  AND
                                                                                                    taxonomy = %s "
                                                                                            , $term_slug['term_id']
                                                                                            , 'pa_'.strtolower($attribute_name)
                                                                        ), ARRAY_A);
                                                    if (0 == $wpdb->num_rows) {
                                                        $insert_term = $wpdb->query($wpdb->prepare(
                                                                                    "INSERT INTO `" . $wpdb->term_taxonomy . "`
                                                                                    (term_id,taxonomy,parent,description,count)
                                                                                    VALUES(%d, %s, %d, %s , %d )"
                                                                                    , $term_slug['term_id']
                                                                                    , 'pa_'.strtolower($attribute_name), 0, ' ', 0
                                                                        ));

                                                        if ($insert_term) {
                                                            $taxonomy_id = $wpdb->insert_id;
                                                            $this->create_term_relationship( $product_ID, $taxonomy_id );

                                                        }

                                                        $query_select = $wpdb->get_results($wpdb->prepare(
                                                                                            "SELECT * FROM `" . $wpdb->prefix . "woocommerce_termmeta`
                                                                                                WHERE woocommerce_term_id= %d "
                                                                                            , $term_slug['term_id']
                                                                        ), ARRAY_A);
                                                        if (0 == $wpdb->num_rows) {
                                                            $wpdb->query($wpdb->prepare(
                                                                        "INSERT INTO `" . $wpdb->prefix . "woocommerce_termmeta`
                                                                        (woocommerce_term_id,meta_key,meta_value)
                                                                        VALUES(%d ,%s, %d)"
                                                                        , $term_slug['term_id']
                                                                        , 'order_pa_'.strtolower($attribute_name), 0
                                                            ));
                                                        }
                                                    } else {
                                                        $taxonomy_data = $taxonomy_query[0];
                                                        $this->create_term_relationship( $product_ID, $taxonomy_data['term_taxonomy_id'] );
                                                        $wpdb->query($wpdb->prepare(
                                                            "UPDATE `" . $wpdb->term_taxonomy . "`
                                                            SET count=count+1 WHERE term_id= %d "
                                                            , $term_slug['term_id']
                                                        ));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            add_post_meta($product_ID, '_product_attributes', $thedata); //ADD Product Attribute
                            if (isset($price_min)) {
                                add_post_meta($product_ID, '_min_variation_price', $price_min['min']);
                                add_post_meta($product_ID, '_price', $price_min['min']);
                                add_post_meta($product_ID, '_min_price_variation_id', $price_min['min_variable_id']);
                                add_post_meta($product_ID, '_min_variation_regular_price', $price_min['min']);
                                add_post_meta($product_ID, '_min_regular_price_variation_id', $price_min['min_variable_id']);
                                add_post_meta($product_ID, '_min_variation_sale_price', '');
                                add_post_meta($product_ID, '_min_sale_price_variation_id', '');
                            }
                            if (isset($price_max)) {
                                add_post_meta($product_ID, '_max_variation_price', $price_max['max']);
                                add_post_meta($product_ID, '_max_price_variation_id', $price_max['max_variable_id']);
                                add_post_meta($product_ID, '_max_variation_regular_price', $price_max['max']);
                                add_post_meta($product_ID, '_max_regular_price_variation_id', $price_max['max_variable_id']);
                                add_post_meta($product_ID, '_max_variation_sale_price', '');
                                add_post_meta($product_ID, '_max_sale_price_variation_id', '');
                            }

// $thedata = array();

                            if ($var_qty <= 0) {
                                if (isset($outlet_checker) && $outlet_checker == 'noOutlet') {

                                } else {
                                    add_post_meta($product_ID, '_stock_status', 'outofstock');
                                    if (get_option('ps_unpublish') == 'on') {
                                        $status = 'draft';
                                    }
                                }
                            } else {
                                add_post_meta($product_ID, '_stock_status', 'instock');
                            }
                        }
#----------------------------------------END VARIENT DATA----------------------------------------#
#Product Quantity
                        if (get_option('ps_quantity') == 'on') {
                            if (isset($product['variants']) && !empty($product['variants'])) { # if it's variable  product then ignore qty for parent product
                            } else {
                                if (isset($outlet_checker) && $outlet_checker == 'noOutlet') {
                                    add_post_meta($product_ID, '_manage_stock', 'no');
                                    add_post_meta($product_ID, '_stock', NULL);
                                    add_post_meta($product_ID, '_stock_status', 'instock');
                                } else {
                                    add_post_meta($product_ID, '_manage_stock', 'yes');
                                    add_post_meta($product_ID, '_stock', $quantity);
                                    add_post_meta($product_ID, '_stock_status', ($quantity > 0 ? 'instock' : 'outofstock'));
                                    if (get_option('ps_unpublish') == 'on' && $quantity < 1) {
                                        $status = 'draft';
                                    }
                                }
                            }
                            unset($outlet_checker);
                        } else {
                            add_post_meta($product_ID, '_manage_stock', 'no');
                        }
                        /*
                         * Product Status Dealing
                         */
//If the Pending is checked
                        if (get_option('ps_pending') == 'on')
                            $status = 'pending';
// if product in vend having status : inactive ( active==0  ) it should be not displayed (mark as draft in woo)
                        if ($product['active'] == '0')
                            $status = 'draft';


                        $status = isset($status) && !empty($status) ? $status : 'publish';
                        $my_post = array(
                            'ID' => $product_ID,
                            'post_status' => $status
                        );
                        wp_update_post($my_post);

                        add_post_meta($product_ID, '_visibility', ($status == 'publish' ? 'visible' : ''));

                        unset($status);
                    }
                }
                delete_transient('wc_attribute_taxonomies');
            }
        }

        delete_transient('wc_attribute_taxonomies'); #Flush attribute
        $prod_update_suc = get_option('prod_update_suc'); # it has NULL or DATETIME
        if (isset($prod_update_suc) && !empty($prod_update_suc)) {
            LSC_Log::add('Product Sync Vend to Woo', 'success', 'Product synced SKU:' . $product['sku'], get_option('linksync_laid'));
        }
        return $product_ids;
    }

// Helper functions
    public function linksync_check_attribute_label($attribute_label) {
        global $wpdb;
//Return Slug of attribute
        $check_attribute_label = $wpdb->get_results($wpdb->prepare(
                                                "SELECT attribute_name FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies`
                                                WHERE BINARY attribute_label= %s "
                                                , $attribute_label
                                 ),ARRAY_A);
        if ($wpdb->num_rows != 0) {
//Exists an attribute label
            $attribute_name = $check_attribute_label[0];
            $attribute_label_slug = $attribute_name['attribute_name'];
        } else {
//Create new attribute label
            $attribute_label_slug = iconv('UTF-8', 'ASCII//TRANSLIT', $attribute_label);
            if (strpos($attribute_label_slug, ' ')) {
                $attribute_label_slug = str_replace(' ', '', $attribute_label_slug);
            } elseif (strpos($attribute_label_slug, '-')) {
                $attribute_label_slug = str_replace('-', '', $attribute_label_slug);
            }
            $attribute_label_slug = preg_replace('/[^A-Za-z0-9\-]/', '', $attribute_label_slug);
            /*
             * Check for the slug exists or not
             */
            $check_attribute_slug = $wpdb->get_results($wpdb->prepare(
                                                        "SELECT * FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies`
                                                        WHERE BINARY attribute_name= %s "
                                                        , strtolower($attribute_label_slug)
                                    ), ARRAY_A);
            if ( $wpdb->num_rows != 0 ) {
                $check = pow(strlen($attribute_label_slug), 2);
                for ($i = 1; $i <= $check; $i++) {
                    $attribute_label_slug = $attribute_label_slug . '-' . $i;
                    $check_for_all = $wpdb->get_results($wpdb->prepare(
                                                "SELECT * FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies`
                                                WHERE BINARY attribute_name= %s "
                                                , strtolower($attribute_label_slug)
                                     ), ARRAY_A);
                    if ($wpdb->num_rows == 0) {
                        break;
                    }
                }
            }

            $wpdb->query($wpdb->prepare(
                        "INSERT INTO `" . $wpdb->prefix . "woocommerce_attribute_taxonomies`
                        (attribute_name,attribute_label,attribute_type,attribute_orderby)
                        VALUES (%s , %s, %s, %s)"
                        , strtolower($attribute_label_slug)
                        , $attribute_label
                        , 'select'
                        , 'menu_order'
            ));
        }
        return strtolower($attribute_label_slug);
    }

    public function linksync_check_term_value($term_value_check) {
        global $wpdb;
        $query_select = $wpdb->get_results($wpdb->prepare(
                                            "SELECT * FROM `" . $wpdb->terms . "` WHERE
                                            BINARY name= %s "
                                            , $term_value_check
                        ),ARRAY_A);
        if (0 == $wpdb->num_rows) {
            /*
             * Term Name not exists
             */
            $term_value = iconv('UTF-8', 'ASCII//TRANSLIT', $term_value_check);
            if (strpos($term_value, ' ')) {
                $slug = str_replace(' ', '', $term_value);
            } else {
                $slug = $term_value;
            }
            if (strpos($term_value, '.')) {
                $slug = str_replace('.', '-', $slug);
            }
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
            /*
             * Check for the slug exists or not
             */


            $check_term_slug = $wpdb->get_results($wpdb->prepare(
                                                "SELECT * FROM `" . $wpdb->terms . "`
                                                 WHERE BINARY slug= %s "
                                                , strtolower($slug)
                                ), ARRAY_A);
            if ($wpdb->num_rows != 0) {
                $check_term = pow(strlen($slug), 2);
                for ($j = 1; $j <= $check_term; $j++) {
                    $slug = $slug . '-' . $j;

                    $check_for_all = $wpdb->get_results($wpdb->prepare(
                                                        "SELECT * FROM `" . $wpdb->terms . "`
                                                        WHERE BINARY slug= %s "
                                                        , strtolower($slug)
                                     ),ARRAY_A);
                    if ($wpdb->num_rows == 0) {
                        break;
                    }
                }
            }
            $db_insert = $wpdb->query($wpdb->prepare(
                                        "INSERT INTO `" . $wpdb->terms . "`
                                          (name,slug,term_group)
                                          VALUES(%s ,%s ,%d)"
                                        , $term_value_check
                                        , strtolower($slug), 0
                         ));
            if ($db_insert) {
                $term_id = $wpdb->insert_id;
                $result['term_id'] = $term_id;
                $result['slug'] = strtolower($slug);
            }
        } else {
            $term_id = $query_select[0];
            $result['term_id'] = $term_id['term_id'];
            $result['slug'] = $term_id['slug'];
        }

        return $result;
    }

    public function _json_encode($val) {
        if (is_string($val))
            return '"' . $val . '"';
        if (is_numeric($val))
            return $val;
        if ($val === null)
            return 'null';
        if ($val === true)
            return 'true';
        if ($val === false)
            return 'false';

        $assoc = false;
        $i = 0;
        foreach ($val as $k => $v) {
            if ($k !== $i++) {
                $assoc = true;
                break;
            }
        }
        $res = array();
        foreach ($val as $k => $v) {
            if (is_string($v)) {
                if (strstr($v, PHP_EOL)) {
                    $v = str_replace(array("\r", "\n"), array('\r', '\n'), $v);
                }
                if (false !== strpos($v, '"')) {
                    $v = str_replace('"', "\"", $v);
                }
                if (false !== strpos($v, "'")) {
                    $v = str_replace("'", "\\'", $v);
                }
            }
            $v = $this->_json_encode($v);
            if ($assoc) {
                $k = '"' . $k . '"';
                $v = $k . ':' . $v;
            }
            $res[] = $v;
        }
        $res = implode(',', $res);
        return ($assoc) ? '{' . $res . '}' : '[' . $res . ']';
    }

    public function importOrderToWoocommerce($orders) {
        if (isset($orders) && !empty($orders)) {
            $order_status = get_option('order_vend_to_wc'); //Order Status from order config setting
            foreach ($orders['orders'] as $order) {
                if (isset($order['id']) && !empty($order['id'])) {
                    $OrderIds = get_option("Vend_orderIDs");
                    if (isset($OrderIds) && !empty($OrderIds)) {
                        $Ids = unserialize($OrderIds);
                    } else {
                        $Ids = array();
                    }
                    if (!in_array($order['id'], $Ids)) {
                        update_option('Vend_orderIDs', serialize(array_merge($Ids, array($order['id']))));
                        $order_data = array(
                            'post_name' => 'order-' . date('M-d-Y-hi-a'), //'order-nov-29-2014-0503-am'
                            'post_type' => 'shop_order',
                            'post_title' => date('M d, Y @ h:i A'), //'June 19, 2014 @ 07:19 PM'
                            'post_excerpt' => 'Source: ' . ucfirst($order['source']) . ' Order #' . $order['orderId'], //Source: Vend Order #17
                            'post_status' => $order_status,
                            'ping_status' => 'closed',
                            'comment_status' => 'open'
                        );
                        $order_id = wp_insert_post($order_data, true);
// create order

                        if (is_wp_error($order_id)) {
                            $order->errors = $order_id;
                        } else {
                            if (isset($order['payment']['transactionNumber']) && !empty($order['payment']['transactionNumber'])) {
                                add_post_meta($order_id, 'transaction_id', $order['payment']['transactionNumber'], true);
                            }
                            /* ---------------------------------------Payment Mapping --------------------------------- */
                            if (isset($order['payment']['retailer_payment_type_id']) && !empty($order['payment']['retailer_payment_type_id'])) {
                                $all_payment = get_option('vend_to_wc_payments');
                                if (isset($all_payment) && !empty($all_payment)) {
                                    $explode_payment = explode(',', $all_payment);
                                    foreach ($explode_payment as $payments_method) {
                                        $payment_method = explode('|', $payments_method);
                                        if (in_array($order['payment']['retailer_payment_type_id'], $payment_method)) {
                                            $gatways = new WC_Payment_Gateways;
                                            $payment = $gatways->get_available_payment_gateways();
                                            $wocoomercepayment = $payment_method[1];
                                            foreach ($payment as $payment_method_id => $payment_method_title) {
                                                if ($payment_method_title->title == $wocoomercepayment) {
                                                    add_post_meta($order_id, '_payment_method_title', $wocoomercepayment, true);
                                                    add_post_meta($order_id, '_payment_method', $payment_method_id, true);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $customer_import = get_option('vend_to_wc_customer');
                            if (isset($customer_import) && $customer_import == 'customer_data') {
                                if (isset($order['billingAddress'])) {
                                    add_post_meta($order_id, '_billing_first_name', isset($order['billingAddress']['firstName']) ? $order['billingAddress']['firstName'] : NULL, true);
                                    add_post_meta($order_id, '_billing_last_name', isset($order['billingAddress']['lastName']) ? $order['billingAddress']['lastName'] : NULL, true);
                                    add_post_meta($order_id, '_billing_company', isset($order['billingAddress']['company']) ? $order['billingAddress']['company'] : NULL, true);
                                    add_post_meta($order_id, '_billing_address_1', isset($order['billingAddress']['street1']) ? $order['billingAddress']['street1'] : NULL, true);
                                    add_post_meta($order_id, '_billing_address_2', isset($order['billingAddress']['street2']) ? $order['billingAddress']['street2'] : NULL, true);
                                    add_post_meta($order_id, '_billing_city', isset($order['billingAddress']['city']) ? $order['billingAddress']['city'] : NULL, true);
                                    add_post_meta($order_id, '_billing_postcode', isset($order['billingAddress']['postalCode']) ? $order['billingAddress']['postalCode'] : NULL, true);
                                    add_post_meta($order_id, '_billing_country', isset($order['billingAddress']['country']) ? $order['billingAddress']['country'] : NULL, true);
                                    add_post_meta($order_id, '_billing_state', isset($order['billingAddress']['state']) ? $order['billingAddress']['state'] : NULL, true);
                                    add_post_meta($order_id, '_billing_phone', isset($order['billingAddress']['phone']) ? $order['billingAddress']['phone'] : NULL, true);
                                }
                                if (isset($order['deliveryAddress'])) {
                                    add_post_meta($order_id, '_shipping_first_name', isset($order['deliveryAddress']['firstName']) ? $order['deliveryAddress']['firstName'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_last_name', isset($order['deliveryAddress']['lastName']) ? $order['deliveryAddress']['lastName'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_company', isset($order['deliveryAddress']['company']) ? $order['deliveryAddress']['company'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_address_1', isset($order['deliveryAddress']['street1']) ? $order['deliveryAddress']['street1'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_address_2', isset($order['deliveryAddress']['street2']) ? $order['deliveryAddress']['street2'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_city', isset($order['deliveryAddress']['city']) ? $order['deliveryAddress']['city'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_postcode', isset($order['deliveryAddress']['postalCode']) ? $order['deliveryAddress']['postalCode'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_country', isset($order['deliveryAddress']['country']) ? $order['deliveryAddress']['country'] : NULL, true);
                                    add_post_meta($order_id, '_shipping_state', isset($order['deliveryAddress']['state']) ? $order['deliveryAddress']['state'] : NULL, true);
                                }
                                if (isset($order['primary_email'])) {
                                    require_once(ABSPATH . 'wp-includes/user.php');
                                    require_once(ABSPATH . 'wp-includes/pluggable.php');
                                    $user_email = $order['primary_email'];
                                    $user_name = $order['billingAddress']['firstName'] . ' ' . $order['billingAddress']['lastName'];
                                    $user_id = email_exists($user_email);
                                    $email_password = false;
                                    if (!$user_id) {
                                        $user_password = wp_generate_password(12, false);
                                        $user_id = wp_create_user($user_name, $user_password, $user_email);
                                        update_user_option($user_id, 'default_password_nag', true, true);
                                        $email_password = true;
                                        $message = " Username: $user_name\n Password: $user_password\n " . wp_login_url();
                                        if (isset($order['billingAddress'])) {
                                            add_user_meta($user_id, 'billing_first_name', isset($order['billingAddress']['firstName']) ? $order['billingAddress']['firstName'] : NULL, true);
                                            add_user_meta($user_id, 'billing_last_name', isset($order['billingAddress']['lastName']) ? $order['billingAddress']['lastName'] : NULL, true);
                                            add_user_meta($user_id, 'billing_company', isset($order['billingAddress']['company']) ? $order['billingAddress']['company'] : NULL, true);
                                            add_user_meta($user_id, 'billing_address_1', isset($order['billingAddress']['street1']) ? $order['billingAddress']['street1'] : NULL, true);
                                            add_user_meta($user_id, 'billing_address_2', isset($order['billingAddress']['street2']) ? $order['billingAddress']['street2'] : NULL, true);
                                            add_user_meta($user_id, 'billing_city', isset($order['billingAddress']['city']) ? $order['billingAddress']['city'] : NULL, true);
                                            add_user_meta($user_id, 'billing_postcode', isset($order['billingAddress']['postalCode']) ? $order['billingAddress']['postalCode'] : NULL, true);
                                            add_user_meta($user_id, 'billing_country', isset($order['billingAddress']['country']) ? $order['billingAddress']['country'] : NULL, true);
                                            add_user_meta($user_id, 'billing_state', isset($order['billingAddress']['state']) ? $order['billingAddress']['state'] : NULL, true);
                                            add_user_meta($user_id, 'billing_phone', isset($order['billingAddress']['phone']) ? $order['billingAddress']['phone'] : NULL, true);
                                        }
                                        if (isset($order['deliveryAddress'])) {
                                            add_user_meta($user_id, 'shipping_first_name', isset($order['deliveryAddress']['firstName']) ? $order['deliveryAddress']['firstName'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_last_name', isset($order['deliveryAddress']['lastName']) ? $order['deliveryAddress']['lastName'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_company', isset($order['deliveryAddress']['company']) ? $order['deliveryAddress']['company'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_address_1', isset($order['deliveryAddress']['street1']) ? $order['deliveryAddress']['street1'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_address_2', isset($order['deliveryAddress']['street2']) ? $order['deliveryAddress']['street2'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_city', isset($order['deliveryAddress']['city']) ? $order['deliveryAddress']['city'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_postcode', isset($order['deliveryAddress']['postalCode']) ? $order['deliveryAddress']['postalCode'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_country', isset($order['deliveryAddress']['country']) ? $order['deliveryAddress']['country'] : NULL, true);
                                            add_user_meta($user_id, 'shipping_state', isset($order['deliveryAddress']['state']) ? $order['deliveryAddress']['state'] : NULL, true);
                                        }
                                        wp_mail($user_email, 'Your username and password', $message);
                                        $user = new WP_User($user_id);
                                        $user->set_role('customer');
                                    }
                                    add_post_meta($order_id, '_customer_user', $user_id);
                                }
                            }
                            if (isset($order['total']) && !empty($order['total'])) {
                                if (isset($order['total_tax'])) {
                                    $order['total'] = $order['total_tax'] + $order['total'];
                                }
                                add_post_meta($order_id, '_order_total', $order['total'], true);
                            }

                            if (isset($order['taxes_included']) && $order['taxes_included'] == true) {
                                add_post_meta($order_id, '_order_tax', $order['total_tax'], true);
                            }

                            if (isset($order['updated_at']) && !empty($order['updated_at'])) {
                                add_post_meta($order_id, '_completed_date', $order['updated_at'], true);
                            }

                            if (isset($order['id']) && !empty($order['id'])) {
                                add_post_meta($order_id, '_vend_orderid', $order['id'], true);
                            }
                            if (isset($order['currency']) && !empty($order['currency'])) {
                                add_post_meta($order_id, '_order_currency', $order['currency'], true);
                            }

// billing info

                            if (isset($order['user_name']) && !empty($order['user_name'])) {
                                add_post_meta($order_id, '_billing_email', $order['user_name'], true);
                            }
                            $i = 0;
                            foreach ($order['products'] as $products) {
                                $product_id = $this->isReferenceExists_order($products['sku']);
                                if ($product_id['result'] == 'success' && !empty($product_id['data'])) {
                                    $product = new WC_Product($product_id['data']);
                                    if ($product->post->post_type == 'product_variation') {
                                        $variant_id = $product->id;
                                        $product->id = $product->post->post_parent;
                                    }
                                    $wcproduct = $product->post;
                                    if ($product) {
// add item
                                        $item_id = wc_add_order_item($order_id, array(
                                            'order_item_name' => $wcproduct->post_title,
                                            'order_item_type' => 'line_item',
                                        ));
                                        if ($item_id) {
                                            $line_tax = array();
                                            $line_subtax = array();
// add item meta data
                                            if (isset($products['price']) && !empty($products['price'])) {
                                                $products['price'] = (float) ($products['price'] * $products['quantity']);
                                            }
                                            $line_total = (float) $products['price'];
                                            wc_add_order_item_meta($item_id, '_qty', $products['quantity']); //Product Order Quantity From Vend
                                            wc_add_order_item_meta($item_id, '_product_id', $product->id);
                                            wc_add_order_item_meta($item_id, '_line_total', $line_total);
                                            wc_add_order_item_meta($item_id, '_variation_id', isset($variant_id) ? $variant_id : '');
                                            $result_tax_class = $this->linksync_tax_classes_vend_to_wc($products['taxId']);
                                            if ($result_tax_class['result'] == 'success') {
                                                $tax_class = $result_tax_class['tax_class'];
                                            }
                                            wc_add_order_item_meta($item_id, '_tax_class', isset($tax_class) ? $tax_class : '');
                                            wc_add_order_item_meta($item_id, '_line_tax', $products['taxValue']);
                                            wc_add_order_item_meta($item_id, '_line_subtotal', $products['price']);
                                            wc_add_order_item_meta($item_id, '_line_subtotal_tax', $products['taxValue']);
                                            $line_tax['total'][1] = $products['taxValue'];
                                            $line_subtax['subtotal'][1] = $products['taxValue'];
                                            $line_tax_data = array_merge($line_tax, $line_subtax);
                                            wc_add_order_item_meta($item_id, '_line_tax_data', $line_tax_data);
                                            if (isset($variant_id) && !empty($variant_id)) {
                                                global $wpdb;
                                                $query = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT meta_key,meta_value FROM `" . $wpdb->postmeta . "`
                                                                             WHERE post_id= %d AND meta_key LIKE 'attribute_pa_%'"
                                                                            , $variant_id
                                                         ), ARRAY_A);
                                                foreach ($query as $result) {
                                                    $meta_key = str_replace('attribute_', '', $result['meta_key']);
                                                    wc_add_order_item_meta($item_id, $meta_key, $result['meta_value']);
                                                }
                                            }
                                        }
                                    } else {
                                        $order->errors = 'Product SKU (' . $order->item_id . ') not found.';
                                    }
                                } elseif ($products['sku'] == 'shipping') {
                                    $taxes = array();
// add item
                                    $shipping_id = wc_add_order_item($order_id, array(
                                        'order_item_name' => $products['title'],
                                        'order_item_type' => 'shipping',
                                    ));
                                    if ($shipping_id) {
                                        wc_add_order_item_meta($shipping_id, 'cost', $products['price']);
                                        wc_add_order_item_meta($shipping_id, 'method_id', '');
                                        wc_add_order_item_meta($shipping_id, 'taxes', '');
                                        add_post_meta($order_id, '_order_shipping', $products['price']);
                                        add_post_meta($order_id, '_order_shipping_tax', $products['taxValue']);
                                        $shippping_tax_amount = $products['taxValue'];
                                        $taxes[1] = $products['taxValue'];
                                        wc_add_order_item_meta($shipping_id, 'taxes', $taxes);
                                    }
                                } elseif ($products['sku'] == 'vend-discount') {
                                    add_post_meta($order_id, '_cart_discount', $products['price']);
                                }
                                /* ---------------------------------------Tax Mapping --------------------------------- */
                                if ($products['sku'] != 'shipping' || $products['sku'] != 'vend-discount') {
                                    if ($i == 0) {
                                        $tax_class_name = $this->linksync_tax_classes_vend_to_wc($products['taxId']);
                                        if ($tax_class_name['result'] == 'success') {
// add item
                                            $tax_id = wc_add_order_item($order_id, array(
                                                'order_item_name' => $tax_class_name['tax_class_name'] . '-' . $tax_class_name['tax_rate_id'],
                                                'order_item_type' => 'tax',
                                            ));
                                            if ($tax_id) {
                                                wc_add_order_item_meta($tax_id, 'rate_id', $tax_class_name['tax_rate_id']);
                                                wc_add_order_item_meta($tax_id, 'label', $tax_class_name['tax_class_name']);
                                                wc_add_order_item_meta($tax_id, 'compound', 0);
                                                $tax_amount = $order['total_tax'];
                                                wc_add_order_item_meta($tax_id, 'tax_amount', isset($tax_amount) ? $tax_amount : 0);
                                                wc_add_order_item_meta($tax_id, 'shipping_tax_amount', isset($shippping_tax_amount) ? $shippping_tax_amount : 0);
                                            }
                                        }
                                        $i++;
                                    }
                                }
                            }
                        }
                        LSC_Log::add('Order Sync Vend to Woo', 'success', 'Vend Order no:' . $order['orderId'] . ', Woo Order no:' . $order_id, get_option('linksync_laid'));
                    }
                }
            }
        }
        return true;
    }

// WooCommerce Functions
    function linksync_tax_classes_vend_to_wc($tax_id) {
        global $wpdb;
        $wc_taxes = get_option('vend_to_wc_tax');
        if (isset($wc_taxes) && !empty($wc_taxes)) {
            $explode_tax = explode(',', $wc_taxes);
            if (isset($explode_tax) && !empty($explode_tax)) {
                foreach ($explode_tax as $taxes) {
                    $explode_taxes = explode('|', $taxes);
                    if (isset($explode_taxes) && !empty($explode_taxes)) {
                        if (in_array($tax_id, $explode_taxes)) {
                            if ($explode_taxes[1] == 'standard-tax') {
                                $explode_taxes[1] = '';
                            }
                            $result_query = $wpdb->get_results($wpdb->prepare(
                                                                "SELECT tax_rate_name,tax_rate_id FROM `" . $wpdb->prefix . "woocommerce_tax_rates`
                                                                WHERE tax_rate_class= %s "
                                                                , $explode_taxes[1]
                                            ),ARRAY_A);
                            if ($wpdb->num_rows != 0) {
                                $tax_class_name = $result_query[0];
                                return array('result' => 'success', 'tax_class_name' => $tax_class_name['tax_rate_name'], 'tax_rate_id' => $tax_class_name['tax_rate_id'], 'tax_class' => $explode_taxes[1]);
                            } else {
                                return array('result' => 'error', 'tax_classes' => NULL);
                            }
                        }
                    } else {
                        return array('result' => 'error', 'tax_classes' => NULL);
                    }
                }
            }
        }
    }

    public function wooCommerce_getProduct() {
        $product_ids = wc_get_product_ids_on_sale();
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            $productDetails[] = $product;
        }
        return $productDetails;
    }

    public function linksync_get_order_statuses() {
        $linksync_order_statuses = array();
        if (function_exists('wc_get_order_statuses')) {
            $order_statuses = wc_get_order_statuses();
            if ($order_statuses) {
                foreach ($order_statuses as $key => $status) {
                    $linksync_order_statuses[$key] = $status;
                }
            }
        } else {
            $order_statuses = get_terms('shop_order_status', array(
                'hide_empty' => 0
            ));
            if ($order_statuses) {
                foreach ($order_statuses as $status) {
                    $linksync_order_statuses[$status->slug] = $status->name;
                }
            }
        }
        return $linksync_order_statuses;
    }

    private function __checkStatus($url) {
        $requesturl = $url . 'laid';
        $curl = curl_init($requesturl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type:application/json",
            "LAID: " . $this->LAID
        ));
        $jsondata = curl_exec($curl);
        $http_response = curl_getinfo($curl);
        if ((int) $http_response['http_code'] == 200) {
//$this->addrawlogs(date('Y-m-d H:i:s'), 'GET', $requesturl,'HTTP Code : '.@$http_response['http_code'],'HTTP Response result :  '.  @serialize($http_response), $this->LAID, 'Technicaly Debuging');
            return true;
        } else {
            $this->addrawlogs(date('Y-m-d H:i:s'), 'NULL', $requesturl, 'HTTP Code : ' . @$http_response['http_code'], 'HTTP Response result :  ' . @serialize($http_response), @$this->LAID);
//$this->addrawlogs(date('Y-m-d H:i:s'), $method, $requesturl, isset($user_data) ? $user_data : 'No POST Data', isset($jsondata) ? $jsondata : 'No Response', $this->LAID, 'Technicaly Debuging');
            return false;
        }
    }

// Calling Function
    private function _CalltoAPIArray($url, $appendurl, $method, $data = NULL) {
//        $http_code = $this->__checkStatus($url);
//        if (!$http_code) {
//            return false;
//        }
        $requesturl = $url . $appendurl;
        $curl = curl_init($requesturl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POST, true);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type:application/json",
            "LAID: " . $this->LAID
        ));
//sleep(10);
        $jsondata = curl_exec($curl);
        if (isset($appendurl) && $appendurl != 'laid/sendLog') {
            if (isset($data) && !empty($data)) {
                $json_decode = json_decode($data, true);
                $user_data = $this->_json_encode($json_decode);
            }
            if (isset($jsondata) && !empty($jsondata)) {
                $decode = json_decode($jsondata, true);
                if (isset($decode['errorCode'])) {
                    if (strpos($decode['userMessage'], 'limit reached')) {
                        $this->add('Product Sync Woo to Vend', 'Error', $decode['userMessage'], $this->LAID);
                    }
                }
            }
            $this->addrawlogs(date('Y-m-d H:i:s'), $method, $requesturl, isset($user_data) ? $user_data : 'No POST Data', isset($jsondata) ? $jsondata : 'No Response', $this->LAID);
        }
        if (curl_error($curl)) {
            $error = "Connection Error: " . curl_errno($curl) . ' - ' . curl_error($curl);
            return array(
                'errorCode' => 007,
                'userMessage' => $error
            );
        }
        curl_close($curl);
        update_option('linksync_connected_url', $url);
        $arr = json_decode($jsondata, true); # Decode JSON String
        return $arr; # Output XML Response as Array
    }

//Function
    public static function releaseOptions() {
        update_option('linksync_status', "");
        update_option('linksync_last_test_time', '');
        update_option('linksync_connected_url', "");
        update_option('linksync_connectedto', '');
        update_option('linksync_connectionwith', '');
        update_option('linksync_addedfile', '');
        update_option('linksync_frequency', '');
    }

    public function variant_sku_check($product_variant, $product_ID) {
        global $wpdb;
        $variant_product_sku_woo['woo_data'] = array();
        $variant_product_sku_vend['vend_data'] = array();
        $add_var_result = array();
        $select_post_id = $wpdb->get_results($wpdb->prepare(
                                            "SELECT ID FROM `" . $wpdb->prefix . "posts` WHERE post_parent= %d "
                                            , $product_ID
                          ),ARRAY_A);
        if (0 != $wpdb->num_rows) {
            foreach ($select_post_id as $variant_id) {
                $variant_product_sku_woo['woo_data'][] = get_post_meta($variant_id['ID'], '_sku', true);
            }
        }

        foreach ($product_variant as $product_variants) {
            $variant_product_sku_vend['vend_data'][] = $product_variants['sku'];
        }

        $vend_sku = array_diff($variant_product_sku_vend['vend_data'], $variant_product_sku_woo['woo_data']);
        if (isset($vend_sku) && !empty($vend_sku)) {
            foreach ($vend_sku as $sku) {
                foreach ($product_variant as $variant_data) {
                    if ($sku == $variant_data['sku']) {
                        $add_var_result[] = $this->add_variant_product($product_ID, $variant_data);
                    }
                }
            }
        }
        $update = array_intersect($variant_product_sku_vend['vend_data'], $variant_product_sku_woo['woo_data']);
        if (isset($update) && !empty($update)) {
            foreach ($update as $sku) {
                foreach ($product_variant as $variant_data) {
                    if ($sku == $variant_data['sku']) {
                        $product_variant_data[] = $variant_data;
                    }
                }
            }
            $result = $this->update_variant_product($product_ID, $product_variant_data);
        }
        $woo_sku = array_diff($variant_product_sku_woo['woo_data'], $variant_product_sku_vend['vend_data']);
        if (isset($woo_sku) && !empty($woo_sku)) {
            foreach ($woo_sku as $sku) {
                $sku = trim($sku);
                if (isset($sku) && !empty($sku)) {
                    $woo_product_id = $wpdb->get_results($wpdb->prepare(
                                                         "SELECT post_id FROM `" . $wpdb->postmeta . "`
                                                         WHERE meta_key='_sku' AND BINARY meta_value= %s "
                                                         , $sku
                                      ),ARRAY_A);
                    $variant_product_id = $woo_product_id[0];
                    wp_delete_post($variant_product_id['post_id']);
                }
            }
        }
        unset($woo_sku);

        return $result;
    }

    public function add_variant_product($product_ID, $product_variants) {
        global $wpdb;
        if (get_option('woocommerce_calc_taxes') == 'yes') {
            if (get_option('linksync_woocommerce_tax_option') == 'on') {
                if (get_option('woocommerce_prices_include_tax') == 'yes') {
                    $excluding_tax = 'off';
                } else {
                    $excluding_tax = 'on';
                }
            } else {
                $excluding_tax = get_option('excluding_tax');
            }
        } else {
            $excluding_tax = get_option('excluding_tax');
        }
        if ($product_variants['deleted_at'] == null) {
            $array_name = array();
            $thedata = array();
            $var_qty = 0;
            $option = array(
                1 => 'one',
                2 => 'two',
                3 => 'three'
            );

// Creating new variants if it's new added
            $status = 'publish';
//  $list_price = $product_variants['list_price'];
            $sell_price = $product_variants['sell_price'];
            if (count($product_variants['outlets']) != 0) {
                $variant_quantity = 0;
                foreach ($product_variants['outlets'] as $outlet) {
                    $product_type = get_option('product_sync_type');
                    if ($product_type == 'two_way') {
                        $selected_outlet = get_option('wc_to_vend_outlet_detail');
                        if (isset($selected_outlet) && !empty($selected_outlet)) {
                            $outlet_id = explode('|', $selected_outlet);
                            if ($outlet_id[1] == $outlet['outlet_id']) {
                                $variant_quantity+=(float) ($outlet['quantity']);
                            }
                        }
                    } elseif ($product_type == 'vend_to_wc-way') {
                        $selected_outlet = get_option('ps_outlet_details');
                        $outlet_id = explode('|', $selected_outlet);
                        foreach ($outlet_id as $id) {
                            if ($id == $outlet['outlet_id']) {
                                $variant_quantity+=(float) ($outlet['quantity']);
                            }
                        }
                    }
                }
            } else {
                $outlet_checker = 'noOutlet';
            }
            if (isset($variant_quantity)) {
                $var_qty+= $variant_quantity;
            }
            $tax_name = $product_variants['tax_name'];
            $my_post = array(
                'post_title' => $product_variants['name'],
                'post_status' => $status,
                'post_author' => 1,
                'post_type' => 'product_variation',
                'post_parent' => $product_ID
            );
            $variation_product_id = wp_insert_post($my_post);
            if ($variation_product_id) {
                add_post_meta($variation_product_id, '_sku', $product_variants['sku']);
                add_post_meta($variation_product_id, '_visibility', ($status == 'publish' ? 'visible' : ''));

                if (get_option('ps_price') == 'on') {
                    $tax_classes = get_option('tax_class');
                    if (isset($tax_classes) && !empty($tax_classes)) {
                        $taxes_all = explode(',', $tax_classes);
                        if (isset($taxes_all) && !empty($taxes_all)) {
                            foreach ($taxes_all as $taxes) {
                                $tax = explode('|', $taxes);
                                if (isset($tax) && !empty($tax)) {
                                    $explode_tax_name = explode('-', $tax[0]); //GST-1.0 to explode GST and 1.0
                                    if (in_array($tax_name, $explode_tax_name)) {
                                        $explode = explode(' ', $tax[1]);
                                        $implode = implode('-', $explode);
                                        $tax_mapping_name = strtolower($implode);
                                        add_post_meta($variation_product_id, '_tax_status', 'taxable');
                                        if ($tax_mapping_name == 'standard-tax') {
                                            $tax_mapping_name = '';
                                        }
                                        add_post_meta($variation_product_id, '_tax_class', $tax_mapping_name);
                                    }
                                }
                            }
                        }
                    }

                    if ($excluding_tax == 'on') {
//If 'yes' then product price SELL Price(excluding any taxes.)
                        add_post_meta($variation_product_id, '_price', $sell_price);
                        if (get_option('price_field') == 'regular_price') {
                            add_post_meta($variation_product_id, '_regular_price', $sell_price);
                        } else {
                            add_post_meta($variation_product_id, '_sale_price', $sell_price);
                        }
                    } else {
                        $tax_and_sell_price_variant = $sell_price + $product_variants['tax_value'];
//If 'no' then product price SELL Price(including any taxes.)
                        add_post_meta($variation_product_id, '_price', $tax_and_sell_price_variant);
                        if (get_option('price_field') == 'regular_price') {
                            add_post_meta($variation_product_id, '_regular_price', $tax_and_sell_price_variant);
                        } else {
                            add_post_meta($variation_product_id, '_sale_price', $tax_and_sell_price_variant);
                        }
                    }
                }
#Product Quantity
                if (get_option('ps_quantity') == 'on') {
                    if (isset($outlet_checker) && $outlet_checker == 'noOutlet') {
                        add_post_meta($variation_product_id, '_manage_stock', 'no');
                        add_post_meta($variation_product_id, '_stock', NULL);
                        add_post_meta($variation_product_id, '_stock_status', 'instock');
                    } else {
                        add_post_meta($variation_product_id, '_manage_stock', 'yes');
                        add_post_meta($variation_product_id, '_stock', $variant_quantity);
                        add_post_meta($variation_product_id, '_stock_status', ($variant_quantity > 0 ? 'instock' : 'outofstock'));
                    }
                    unset($outlet_checker);
                } else {
                    add_post_meta($variation_product_id, '_manage_stock', 'no');
                }
            }
            for ($i = 1; $i <= 3; $i++) {
                if (!empty($product_variants['option_' . $option[$i] . '_name'])) {
                    /*
                     * check attribute lable Exists or Not tbl->woocommerce_attribute_taxonomies
                     */
                    $attribute_name = $this->linksync_check_attribute_label($product_variants['option_' . $option[$i] . '_name']);
                    $visible = get_option('linksync_visiable_attr');
                    $thedata['pa_' . $attribute_name] = Array(
                        'name' => 'pa_' . $attribute_name,
                        'value' => '',
                        'is_visible' => $visible,
                        'is_variation' => '1',
                        'is_taxonomy' => '1'
                    );
                    /*
                     * check Term value  Exists or Not tbl->term
                     */
                    if (!empty($product_variants['option_' . $option[$i] . '_value'])) {
                        $term_slug = $this->linksync_check_term_value($product_variants['option_' . $option[$i] . '_value']);
                        if (isset($term_slug) && !empty($term_slug)) {
                            add_post_meta($variation_product_id, "attribute_pa_" . strtolower($attribute_name), strtolower($term_slug['slug']));
                            $taxonomy_query = $wpdb->get_results($wpdb->prepare(
                                                            "SELECT * FROM `" . $wpdb->term_taxonomy . "`
                                                            WHERE
                                                                term_id= %d  AND
                                                                taxonomy= %s"
                                                            , $term_slug['term_id']
                                                            , 'pa_'.strtolower($attribute_name)
                                              ),ARRAY_A);
                            if ($wpdb->num_rows == 0) {
                                $db_insert = $wpdb->query($wpdb->prepare(
                                    "INSERT INTO `" . $wpdb->term_taxonomy . "`
                                     (term_id,taxonomy,parent,description,count)
                                     VALUES(%d ,%s ,%d , %s , %d)"
                                    , $term_slug['term_id']
                                    , 'pa_'.strtolower($attribute_name),0 ,' ',0
                                ));
                                if ($db_insert) {
                                    $taxonomy_id = $wpdb->insert_id;
                                    $this->create_term_relationship( $product_ID, $taxonomy_id );
                                }
                                $query_select = $wpdb->get_results($wpdb->prepare(
                                                                    "SELECT * FROM `" . $wpdb->prefix . "woocommerce_termmeta`
                                                                    WHERE woocommerce_term_id= %d "
                                                                    , $term_slug['term_id']
                                                ),ARRAY_A);
                                if (0 == $wpdb->num_rows) {
                                    $wpdb->query($wpdb->prepare(
                                        "INSERT INTO `" . $wpdb->prefix . "woocommerce_termmeta`
                                        (woocommerce_term_id,meta_key,meta_value)
                                        VALUES(%d, %s, %d)"
                                        , $term_slug['term_id']
                                        , 'order_pa_'.strtolower($attribute_name), 0
                                    ));
                                }
                            } else {
                                $taxonomy_data = $taxonomy_query[0];
                                $this->create_term_relationship( $product_ID, $taxonomy_data['term_taxonomy_id'] );
                                $wpdb->query($wpdb->prepare(
                                    "UPDATE `" . $wpdb->term_taxonomy . "`
                                    SET count=count+1
                                    WHERE term_id= %d "
                                    , $term_slug['term_id']
                                ));
                            }
                        }
                    }
                }
            }// end creating new variatns

            $return['thedata'] = isset($thedata) ? $thedata : ' ';
//$return['array_name'] = isset($array_name) ? $array_name : ' ';
            $return['var_quantity'] = $var_qty;
            return $return;
        }
    }

    public function update_variant_product($product_ID, $product_variant) {
        if (get_option('woocommerce_calc_taxes') == 'yes') {
            if (get_option('linksync_woocommerce_tax_option') == 'on') {
                if (get_option('woocommerce_prices_include_tax') == 'yes') {
                    $excluding_tax = 'off';
                } else {
                    $excluding_tax = 'on';
                }
            } else {
                $excluding_tax = get_option('excluding_tax');
            }
        } else {
            $excluding_tax = get_option('excluding_tax');
        }
        $var_qty = 0;
        $array_name = array();
        $thedata = array();
        $option = array(
            1 => 'one',
            2 => 'two',
            3 => 'three'
        );
        global $wpdb;
        $status = 'publish';

        foreach ($product_variant as $product_variants) {
            if (empty($product_variants['deleted_at'])) {
                $variant_reference = $this->variantSkuHandler($product_variants['sku'], $product_ID);
//                variantSku($product_variants['sku']);
                if ($variant_reference['result'] == 'success') {
                    $result_reference['data'] = $product_ID;
                    $sell_price = $product_variants['sell_price'];
                    $quantity = 0;
                    if (count($product_variants['outlets']) != 0) {
                        foreach ($product_variants['outlets'] as $outlet) {
                            $product_type = get_option('product_sync_type');
                            if ($product_type == 'two_way') {
                                $selected_outlet = get_option('wc_to_vend_outlet_detail');
                                $outlet_id = explode('|', $selected_outlet);
                                if ($outlet_id[1] == $outlet['outlet_id']) {
                                    $quantity+=(float) ($outlet['quantity']);
                                }
                            } elseif ($product_type == 'vend_to_wc-way') {
                                $selected_outlet = get_option('ps_outlet_details');
                                $outlet_id = explode('|', $selected_outlet);
                                foreach ($outlet_id as $id) {
                                    if ($id == $outlet['outlet_id']) {
                                        $quantity+=(float) ($outlet['quantity']);
                                    }
                                }
                            }
                        }
                    } else {
                        $outlet_checker = 'noOutlet';
                    }

//$quantity = (int) ($product_variants['quantity']);
                    $tax_name = $product_variants['tax_name'];
                    if (isset($quantity)) {
                        $var_qty+=$quantity;
                    }
                    $my_post = array(
                        'ID' => $variant_reference['data'],
                        'post_title' => $product_variants['name'],
                        'post_status' => 'publish',
                        'post_author' => 1,
                        'post_type' => 'product_variation',
                        'post_parent' => $result_reference['data']
                    );
                    $variation_product_id = wp_update_post($my_post);

                    if ($variation_product_id) {
                        update_post_meta($variation_product_id, '_visibility', ($status == 'publish' ? 'visible' : ''));

                        if (get_option('ps_price') == 'on') {
                            $variant_tax_classes = get_option('tax_class');
                            if (isset($variant_tax_classes) && !empty($variant_tax_classes)) {
                                $taxes_all = explode(',', $variant_tax_classes);
                                if (isset($taxes_all) && !empty($taxes_all)) {
                                    foreach ($taxes_all as $taxes) {
                                        $tax = explode('|', $taxes);
                                        if (isset($tax) && !empty($tax)) {
                                            $explode_tax_name = explode('-', $tax[0]); //GST-1.0 to explode GST and 1.0
                                            if (in_array($tax_name, $explode_tax_name)) {
                                                $explode = explode(' ', $tax[1]);
                                                $implode = implode('-', $explode);
                                                $tax_mapping_name = strtolower($implode);
                                                update_post_meta($variation_product_id, '_tax_status', 'taxable');
                                                if ($tax_mapping_name == 'standard-tax') {
                                                    $tax_mapping_name = '';
                                                }
                                                update_post_meta($variation_product_id, '_tax_class', $tax_mapping_name);
                                            }
                                        }
                                    }
                                }
                            }
                            $db_sale_price = $wpdb->get_results($wpdb->prepare(
                                                    "SELECT * FROM `" . $wpdb->postmeta . "`
                                                    WHERE `post_id` = %d AND meta_key='_sale_price'"
                                                    , $variation_product_id
                                             ),ARRAY_A);
                            if ($excluding_tax == 'on') {
//If 'yes' then product price SELL Price(excluding any taxes.)
                                if (0 != $wpdb->num_rows) {
                                    $result_sale_price = $db_sale_price[0];
                                    if ($result_sale_price['meta_value'] == NULL) {
                                        update_post_meta($variation_product_id, '_price', $sell_price);
                                    }
                                } else {
                                    update_post_meta($variation_product_id, '_price', $sell_price);
                                }
                                if (get_option('price_field') == 'regular_price') {
                                    update_post_meta($variation_product_id, '_regular_price', $sell_price);
                                } else {
                                    update_post_meta($variation_product_id, '_price', $sell_price);
                                    update_post_meta($variation_product_id, '_sale_price', $sell_price);
                                }
                            } else {
//If 'no' then product price SELL Price(including any taxes.)
                                $tax_and_sell_price = $sell_price + $product_variants['tax_value'];
                                if (0 != $wpdb->num_rows) {
                                    $result_sale_price = $db_sale_price[0];
                                    if ($result_sale_price['meta_value'] == NULL) {
                                        update_post_meta($variation_product_id, '_price', $tax_and_sell_price);
                                    }
                                } else {
                                    update_post_meta($variation_product_id, '_price', $tax_and_sell_price);
                                }
                                if (get_option('price_field') == 'regular_price') {
                                    update_post_meta($variation_product_id, '_regular_price', $tax_and_sell_price);
                                } else {
                                    update_post_meta($variation_product_id, '_price', $tax_and_sell_price);
                                    update_post_meta($variation_product_id, '_sale_price', $tax_and_sell_price);
                                }
                            }
                        }
#Product Quantity

                        if (get_option('ps_quantity') == 'on') {
                            if (isset($outlet_checker) && $outlet_checker == 'noOutlet') {
                                update_post_meta($variation_product_id, '_manage_stock', 'no');
                                update_post_meta($variation_product_id, '_stock', NULL);
                                update_post_meta($variation_product_id, '_stock_status', 'instock');
                            } else {
                                update_post_meta($variation_product_id, '_manage_stock', 'yes');
                                update_post_meta($variation_product_id, '_stock', $quantity);
                                update_post_meta($variation_product_id, '_stock_status', ($quantity > 0 ? 'instock' : 'outofstock'));
                            }
                            unset($outlet_checker);
                        }
#----------Remove Post Meta----Attribute----#
                        if (get_option('ps_attribute') == 'on') {
                            $wpdb->query($wpdb->prepare(
                                "DELETE FROM `" . $wpdb->postmeta . "`
                                WHERE post_id= %d AND meta_key LIKE 'attribute_pa_%%'"
                                , $variation_product_id
                            ));
                        }
//                       if (taxonomy_exists('pa_' . strtolower($product_variants['option_' . $option[$i] . '_name']))) {
//                            wp_delete_object_term_relationships($result_reference['data'], 'pa_' . strtolower($product_variants['option_' . $option[$i] . '_name']));
//                        }
                        for ($i = 1; $i <= 3; $i++) {
                            if (!empty($product_variants['option_' . $option[$i] . '_name'])) {
                                /*
                                 * check attribute lable Exists or Not tbl->woocommerce_attribute_taxonomies
                                 */
                                $attribute_name = $this->linksync_check_attribute_label($product_variants['option_' . $option[$i] . '_name']);
                                if (get_option('ps_attribute') == 'off') {
                                    $wpdb->query($wpdb->prepare(
                                        "DELETE FROM `" . $wpdb->postmeta . "`
                                        WHERE
                                            post_id= %d  AND
                                            meta_key = %s "
                                        , $variation_product_id
                                        , 'attribute_pa_'.strtolower($attribute_name)
                                    ));
                                }
                                $visible = get_option('linksync_visiable_attr');

                                $thedata['pa_' . $attribute_name] = Array(
                                    'name' => 'pa_' . $attribute_name,
                                    'value' => '',
                                    'is_visible' => $visible,
                                    'is_variation' => '1',
                                    'is_taxonomy' => '1'
                                );
                                /*
                                 * check Term value  Exists or Not tbl->term
                                 */
                                if (!empty($product_variants['option_' . $option[$i] . '_value'])) {
                                    $term_slug = $this->linksync_check_term_value($product_variants['option_' . $option[$i] . '_value']);
                                    if (isset($term_slug) && !empty($term_slug)) {
                                        add_post_meta($variation_product_id, "attribute_pa_" . strtolower($attribute_name), strtolower($term_slug['slug']));
                                        $taxonomy_query = $wpdb->get_results($wpdb->prepare(
                                                                             "SELECT * FROM `" . $wpdb->term_taxonomy . "`
                                                                             WHERE term_id= %d AND taxonomy= %s "
                                                                            , $term_slug['term_id']
                                                                            , 'pa_'. strtolower($attribute_name)
                                                            ), ARRAY_A);
                                        if ($wpdb->num_rows == 0) {
                                            $db_insert = $wpdb->query($wpdb->prepare(
                                                "INSERT INTO `" . $wpdb->term_taxonomy . "`
                                                (term_id,taxonomy,parent,description,count)
                                                VALUES(%d, %s ,%d ,%s , %d)"
                                                , $term_slug['term_id']
                                                , 'pa_'.strtolower($attribute_name), 0, ' ', 0
                                            ));
                                            if ($db_insert) {
                                                $taxonomy_id = $wpdb->insert_id;
                                                $this->create_term_relationship( $product_ID, $taxonomy_id );
                                            }
                                            $query_select = $wpdb->get_results($wpdb->prepare(
                                                                                "SELECT * FROM `" . $wpdb->prefix . "woocommerce_termmeta`
                                                                                WHERE woocommerce_term_id= %d "
                                                                                , $term_slug['term_id']
                                                            ),ARRAY_A);
                                            if (0 == $wpdb->num_rows) {

                                                $wpdb->query($wpdb->prepare(
                                                    "INSERT INTO `" . $wpdb->prefix . "woocommerce_termmeta`
                                                    (woocommerce_term_id,meta_key,meta_value)
                                                    VALUES(%d ,%s , %d )"
                                                    , $term_slug['term_id']
                                                    , 'order_pa_'.strtolower($attribute_name), 0
                                                ));
                                            }
                                        } else {
                                            $taxonomy_data = $taxonomy_query[0];
                                            $this->create_term_relationship( $product_ID, $taxonomy_data['term_taxonomy_id'] );
                                            $wpdb->query($wpdb->prepare(
                                                "UPDATE `" . $wpdb->term_taxonomy . "` SET count=count+1
                                                WHERE term_id= %d "
                                                , $term_slug['term_id']
                                            ));
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (get_option('ps_delete') == 'on') {
                        if (!empty($product_variants['sku'])) {
                            $vend_product_id = $wpdb->get_results($wpdb->prepare(
                                                        "SELECT post_id FROM `" . $wpdb->postmeta . "`
                                                        WHERE meta_key='_sku' AND BINARY meta_value= %s "
                                                        , $product_variants['sku']
                                                ),ARRAY_A);
                            $variant_product = $vend_product_id[0];
                            wp_delete_post($variant_product['post_id']); //use the product Id and delete the product
                        }
                    }
                }
            }
        }

        $return['thedata'] = isset($thedata) ? $thedata : ' ';
        $return['var_quantity'] = $var_qty;
        return $return;
    }


    public function addrawlogs($datetime, $method, $requesturl, $postdata, $response, $LAID) {
        $remote_ip = $_SERVER['REMOTE_ADDR'];
        if ($remote_ip == '::1')
            $remote_ip = 'Localhost';
        static $username = null;

        $current_user_id = get_current_user_id();
        if ($current_user_id == 0) {
            $logged_one = 'System';
        } else {
            $logged_one = $current_user_id;
        }
        if (!file_exists(dirname(__FILE__) . "/raw-log.txt")) {
            $file = @fopen(dirname(__FILE__) . "/raw-log.txt", "wb");
            @fwrite($file, "Date/Time | Method | POST Data | Response data | Api Key | | User | IP Address | Notes \r\n\r\n");
            $str = $datetime . " | " . $method . " | " . $requesturl . " | " . $postdata . " | " . $response . " | " . $LAID . " | " . $current_user_id . "|" . $remote_ip;
            @fwrite($file, $str);
            @chmod(dirname(__FILE__) . "/raw-log.txt", 0777);
        } else {
            $file = dirname(__FILE__) . "/raw-log.txt";

// Append a new person to the file

            $log_text = "\r\n\r\n" . $datetime . ' | ' . $method . ' | ' . urldecode($requesturl) . ' | ' . $postdata . ' | ' . $response . ' | ' . $LAID . " | " . $current_user_id . "|" . $remote_ip;
// Write the contents back to the file
            @file_put_contents($file, $log_text, FILE_APPEND | LOCK_EX);
        }
    }

}

/*
 * Insert Image to database and upload to Upload folder
 */

function linksync_insert_image($image_url, $post_id) {
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if (wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    unset($image_url);
    unset($post_id);
    return $attach_id;
}

//Image Update Function
function addImage_thumbnail($image_url, $post_id) {
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if (wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    set_post_thumbnail($post_id, $attach_id);
}

function checkAndDelete_attachement($image_name) {
    global $wpdb;
    $image_query = $wpdb->get_results(
                            "SELECT * FROM  `" . $wpdb->postmeta . "`
                            WHERE  meta_key='_wp_attached_file' AND `meta_value` LIKE '%" . $image_name . "'",ARRAY_A
                    );
	if(!empty($image_query)){
		foreach ($image_query as $images) {

			$check_attachment = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM  `" . $wpdb->postmeta . "`
                                        WHERE  meta_key = '_thumbnail_id'  AND meta_value= %s "
				, $images['post_id']
			),ARRAY_A);
			if ($wpdb->num_rows == 0) {
				delete_post_meta($images['post_id'], '_wp_attached_file');
				$wpdb->query($wpdb->prepare(
					"DELETE FROM `" . $wpdb->posts . "` WHERE ID = %d "
					, $images['post_id']
				));
			}
		}
	}
}

?>