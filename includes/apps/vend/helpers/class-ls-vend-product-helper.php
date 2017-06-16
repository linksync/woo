<?php

class LS_Vend_Product_Helper
{

    public static function isTypeSyncAbleToVend($type)
    {
        $bool = false;
        $product_types = array('simple', 'variable', 'product');

        if (in_array($type, $product_types)) {
            $bool = true;
        }
        return $bool;
    }

    /**
     * Delete WooCommerce Product Base on product id
     * @param $product_id int           The woocommerce product id
     * @param bool $force_delete Defaults is true, to force deletion and avoid trash
     * @return array|bool|false|WP_Post
     */
    public static function deleteWooProducts($product_id, $force_delete = true)
    {
        $delete_option = LS_Vend()->product_option()->delete();
        $deleted = false;
        if ('on' == $delete_option) {
            $var_ids = LS_Product_Helper::getVariantIds($product_id);
            if (!empty($var_ids)) {
                foreach ($var_ids as $var_id) {
                    wp_delete_post($var_id['ID'], $force_delete);
                }
            }
            $deleted = wp_delete_post($product_id, $force_delete);
        }

        return $deleted;
    }

    public static function createWooProduct(LS_Vend_Product_Option $productSyncOption, LS_Product $product, $is_new = false)
    {
        $product_id = null;
        if ('on' == $productSyncOption->createNew()) {
            $product_description = $product->get_description();
            $product_description = empty($product_description) ? '' : html_entity_decode($product_description);
            //Create the product array
            $product_args['post_title'] = $product->get_name();
            $product_args['post_status'] = 'publish';

            if ('on' == $productSyncOption->description()) {
                $product_args['post_content'] = !empty($productDescription) ? $productDescription : '';
            }

            if ('on' == $productSyncOption->shortDescription()) {
                $product_args['post_excerpt'] = $product_description;
            }

            $productSyncQuantityOption = $productSyncOption->quantity();
            $productQuantity = self::getProductQuantity($product);
            if (
                'on' == $productSyncQuantityOption &&
                'on' == $productSyncOption->changeProductStatusBaseOnQuantity() &&
                false == $product->has_variant()
            ) {
                if ($productQuantity <= 0) {
                    $product_args['post_status'] = 'draft';
                } else {
                    $product_args['post_status'] = 'publish';
                }
            }


            if ('on' == $productSyncOption->productStatusToPending() && $is_new) {
                $product_args['post_status'] = 'pending';
            }

            if ('0' == $product->is_active() || 0 == $product->is_active()) {
                $product_args['post_status'] = 'draft';
            }

            return LS_Product_Helper::create($product_args, true);
        }

        return null;
    }

    public static function updateWooProductPostData($productId, LS_Product $product, $productQuantity)
    {
        $productSyncOption = LS_Vend()->product_option();
        $productSyncQuantityOption = $productSyncOption->quantity();
        $wooProduct = new LS_Woo_Product($productId);
        $postArg['ID'] = $productId;

        $productDescription = $product->get_description();
        $productName = $product->get_name();
        $postArg['post_status'] = 'publish';
        if (
            'on' == $productSyncQuantityOption &&
            'on' == $productSyncOption->changeProductStatusBaseOnQuantity() &&
            false == $product->has_variant()
        ) {
            if ($productQuantity <= 0) {
                $postArg['post_status'] = 'draft';
            } else {
                $postArg['post_status'] = 'publish';
            }
        }

        if ('pending' == $wooProduct->get_status()) {
            $postArg['post_status'] = 'pending';
        }

        if ('on' == $productSyncOption->description()) {
            $postArg['post_content'] = !empty($productDescription) ? $productDescription : '';
        }

        if ('on' == $productSyncOption->nameTitle()) {
            $postArg['post_title'] = $productName;
        }

        if ('on' == $productSyncOption->shortDescription()) {
            $postArg['post_excerpt'] = !empty($productDescription) ? $productDescription : '';
        }

        if (!empty($postArg['post_status']) && !empty($postArg['ID'])) {
            $productUpdate = wp_update_post($postArg, true);
            return $productUpdate;
        }

        return null;
    }

    public static function updateWooProduct(LS_Product_Meta $products_meta, LS_Product $product, $is_new = false)
    {
        $productSyncOption = LS_Vend()->product_option();
        $productSyncQuantityOption = $productSyncOption->quantity();
        $productId = $products_meta->getWooProductId();
        $products_meta->update_tax_value($product->get_tax_value());
        $products_meta->update_tax_name($product->get_tax_name());
        $products_meta->update_tax_rate($product->get_tax_rate());
        $products_meta->update_tax_id($product->get_tax_id());
        $products_meta->update_sku($product->get_sku());
        $excluding_tax = LS_Vend_Helper::isExcludingTax();

        $productQuantity = self::getProductQuantity($product);
        if (!$is_new) {
            self::updateWooProductPostData($productId, $product, $productQuantity);
        }

        if ('on' == $productSyncOption->attributes()) {
            self::deleteWooVariationAttribute($productId);
        }

        self::toWooTerms($productId, array(
            'tags' => $product->get_tags(),
            'brands' => $product->get_brands(),
            'product_type' => $product->get_product_type()
        ));

        if ($product->has_variant()) {
            $products_meta->update_regular_price('');
            $products_meta->update_sale_price('');


            $variants = $product->get_variants();
            /**
             * Set the parent product as variable product
             */
            wp_set_object_terms($productId, 'variable', 'product_type');

            self::deleteWooVariantThatDoesNotExistInVend($product, $products_meta);

            if (!empty($variants)) {
                $productAttributes = array();

                foreach ($variants as $variant) {
                    $variant = new LS_Product_Variant($products_meta, $variant);
                    $res = self::createUpdateVariantProduct($variant);
                    $productAttributes = array_merge((array)$productAttributes, isset($res['_product_attributes']) ? (array)$res['_product_attributes'] : array());
                }
                $products_meta->update_product_attributes($productAttributes);
            }

        } else {

            if ('on' == $productSyncOption->price()) {

                self::setWooProductTaxClassAndStatus($products_meta, $product);
                if ('on' == $excluding_tax) {
                    //If 'yes' then product price SELL Price(excluding any taxes.)
                    self::updateWooPrice($products_meta, $product->get_sell_price());

                } else {

                    //If 'no' then product price SELL Price(including any taxes.)
                    $tax_and_sell_price_product = $product->get_sell_price() + $product->get_tax_value();
                    self::updateWooPrice($products_meta, $tax_and_sell_price_product);
                }
            }
        }


        if ('on' == $productSyncOption->image()) {
            $importImageOption = $productSyncOption->importImage();
            if ('Enable' == $importImageOption || 'Ongoing' == $importImageOption) {
                LS_Vend_Image_Helper::importProductImageToWoo($product, $products_meta, $importImageOption);
            }
        }

        if ('on' == $productSyncQuantityOption) {
            if (true == $product->has_variant()) {
                if ('on' == $productSyncOption->changeProductStatusBaseOnQuantity()) {

                    $totalVariantQuantity = $product->getTotalVariantsQuantity();
                    if ($totalVariantQuantity <= 0) {
                        $products_meta->update_stock_status('outofstock');
                    } else {
                        $products_meta->update_stock_status('instock');
                    }

                }

            } else {

                if ($product->has_outlets()) {
                    $products_meta->update_manage_stock('yes');
                    $products_meta->update_stock($productQuantity);
                    $stockStatus = ($productQuantity > 0 ? 'instock' : 'outofstock');

                    if ($productQuantity < 1 && 'on' == $productSyncOption->changeProductStatusBaseOnQuantity()) {
                        $status = 'draft';
                    }
                    $products_meta->update_stock_status($stockStatus);
                } else {
                    $products_meta->update_manage_stock('no');
                    $products_meta->update_stock(NULL);
                    $products_meta->update_stock_status('instock');
                }
            }

        }

    }

    public static function setWooProductTaxClassAndStatus(LS_Product_Meta $product_meta, LS_Product $product)
    {
        $new_product_taxes = LS_Vend()->option()->tax_class();
        if (!empty($new_product_taxes)) {
            $taxes_all = explode(',', $new_product_taxes);
            if (!empty($taxes_all)) {
                foreach ($taxes_all as $taxes) {
                    $tax = explode('|', $taxes);
                    if (!empty($tax)) {
                        $explode_tax_name = explode('-', $tax[0]); //GST-1.0 to explode GST and 1.0
                        if (in_array($product->get_tax_name(), $explode_tax_name)) {
                            $explode = explode(' ', $tax[1]);
                            $implode = implode('-', $explode);
                            $tax_mapping_name = strtolower($implode);
                            $product_meta->update_tax_status('taxable');

                            if ($tax_mapping_name == 'standard-tax') {
                                $tax_mapping_name = '';
                            }
                            $product_meta->update_tax_class($tax_mapping_name);
                        }
                    }
                }
            }
        }
    }

    public static function toWooTerms($product_id, $terms)
    {
        $tags = $terms['tags'];
        $brands = $terms['brands'];
        $product_type = $terms['product_type'];

        #Tag of the Products
        if (get_option('ps_tags') == 'on') {
            if (!empty($tags)) {
                self::setWooProductTerms($product_id, $tags);
            }
        }

        # BRAND syncing ( update )
        if (in_array('woocommerce-brands/woocommerce-brands.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            if (get_option('ps_brand') == 'on') {

                if (!empty($brands)) {
                    self::setWooProductTerms($product_id, $brands, 'brand');
                }

            }
        }

        #Category
        if (get_option('ps_categories') == 'on') {
            if (get_option('cat_radio') == 'ps_cat_product_type') {
                self::setWooProductCategory($product_id, $product_type, 'product_type');
            }
            if (get_option('cat_radio') == 'ps_cat_tags') {
                self::setWooProductCategory($product_id, $tags);
            }
        }
    }

    public static function setWooProductTerms($product_id, $terms, $taxonomy = 'tag')
    {
        $taxonomy = 'product_' . $taxonomy;
        $product_id = (int)$product_id;
        //Remove the old terms
        wp_set_object_terms($product_id, null, $taxonomy);
        if (!empty($terms) && is_array($terms)) {
            foreach ($terms as $term) {
                //$vend_tag_names[] = ;
                wp_set_object_terms($product_id, $term['name'], $taxonomy, true);
            }
        }
    }

    /**
     * Setting Woocommerce Product Category by Vend Tags or by Vend Product Types
     * @param int $product_id The product Id
     * @param string|array $categories The Vend Tag that may contain " parent / child " format of category or Product Type that is only string
     * @param string $type Default is tag, it should be tag or product_type base on the categories option
     */
    public static function setWooProductCategory($product_id, $categories, $type = 'tag')
    {
        if (!empty($categories)) {
            //Category Tags
            if ('tag' == $type) {
                foreach ($categories as $tag) {
                    if (isset($tag['name']) && !empty($tag['name'])) {
                        $tags = explode(' / ', $tag['name']);
                        if (isset($tags) && !empty($tags)) {
                            $parent_id = 0;

                            foreach ($tags as $cat_key => $cat_name) {
                                $cat_name = trim($cat_name);
                                $ls_term = ls_get_term_by_name($cat_name, $parent_id);

                                if (false != $ls_term) {
                                    $term_id = (int)$ls_term->term_id;
                                    wp_set_object_terms($product_id, $term_id, 'product_cat', TRUE);
                                    $parent_id = $ls_term->term_id;
                                }
                            }
                        }
                    }
                }
            } //Category Product Types
            elseif ('product_type' == $type) {

                $cat_name = trim($categories);
                $ls_term = ls_get_term_by_name($cat_name);
                if (false != $ls_term) {
                    $term_id = (int)$ls_term->term_id;
                    wp_set_object_terms($product_id, $term_id, 'product_cat', TRUE);
                }

            }

        }
    }

    /**
     * Updates woocommerce prices base on the selected option(regular or sale price)
     * @param LS_Product_Meta $product_meta
     * @param $price int|double The price from vend
     */
    public static function updateWooPrice(LS_Product_Meta $product_meta, $price)
    {

        $price_option = LS_Vend()->product_option()->priceField();

        $price = ($price == 0) ? '' : $price;

        if ('regular_price' == $price_option) {

            $product_meta->update_regular_price($price);

            $sale_price_meta = $product_meta->get_sale_price();
            if ('' == $sale_price_meta) {
                $product_meta->update_price($price);
            } elseif ($price > $sale_price_meta) {
                //Make sure to update the price to be equal to sale price if regular price is greater than the sale price
                $product_meta->update_price($sale_price_meta);
            }

        } elseif ('sale_price' == $price_option) {

            $product_meta->update_sale_price($price);
            $product_meta->update_price($price);

        }

    }

    public static function getProductQuantity(LS_Product $product)
    {
        $quantity = 0;

        $productSyncOption = LS_Vend()->product_option();
        $product_type = $productSyncOption->sync_type();

        $productOutlets = $product->get_outlets();
        if ('two_way' == $product_type) {

            $wooToVendOutlet = $productSyncOption->wooToVendOutlet();
            $wooToVendOutletDetail = $productSyncOption->wooToVendOutletDetail();
            if (is_array($productOutlets) && !empty($productOutlets)) {
                foreach ($productOutlets as $outlet) {
                    if ($wooToVendOutlet == 'on' && !empty($wooToVendOutletDetail)) {

                        $outlet_id = explode('|', $wooToVendOutletDetail);
                        if (isset($outlet_id[1]) && $outlet_id[1] == $outlet['outlet_id']) {
                            $quantity += $outlet['quantity'];
                        }

                    }
                }
            }

        } elseif ('vend_to_wc-way' == $product_type) {

            $vendToWooOutlet = $productSyncOption->vendToWooOutlet();
            $vendToWooOutletDetail = $productSyncOption->vendToWooOutletDetail();
            if (is_array($productOutlets) && !empty($productOutlets)) {
                foreach ($productOutlets as $outlet) {

                    if ('on' == $vendToWooOutlet && !empty($vendToWooOutletDetail)) {

                        $outlet_id = explode('|', $vendToWooOutletDetail);
                        foreach ($outlet_id as $id) {
                            if ($id == $outlet['outlet_id']) {
                                $quantity += $outlet['quantity'];
                            }
                        }
                    }

                }
            }
        }

        return $quantity;

    }

    public static function deleteWooVariationAttribute($variableId)
    {
        global $wpdb;
        $product_attributes = get_post_meta($variableId, '_product_attributes', TRUE);
        if (isset($product_attributes) && !empty($product_attributes)) {
            foreach ($product_attributes as $taxonomy_name => $taxonomy_detail) {
                $taxonomy_query = $wpdb->get_results($wpdb->prepare(
                    "SELECT term_taxonomy_id
                     FROM `" . $wpdb->term_taxonomy . "`
                     WHERE `taxonomy`= %s", $taxonomy_name
                ), ARRAY_A);

                if (0 != $wpdb->num_rows) {
                    foreach ($taxonomy_query as $term_taxonmy_id_db) {
                        $wpdb->query($wpdb->prepare(
                            "DELETE FROM `" . $wpdb->term_relationships . "`
                             WHERE
                                    object_id= %d  AND
                                    term_taxonomy_id= %d "
                            , $variableId
                            , $term_taxonmy_id_db['term_taxonomy_id']
                        ));
                    }
                }
            }
        }
    }

    /**
     * Vend variant attributes to woocommerce
     * @param $args {
     *    Array argument.
     *    $product_id   The product id or the parent id of the variant
     *    $variant_id   The variant id
     *    $attr_name    The attribute name from vend or the custom taxonomy in wordpress
     *    $attr_value   The attribute value from vend or the term of a taxonomy
     * }
     * @return null
     */
    public static function toWooVariantAttributes($args)
    {
        if (empty($args['product_id']) || empty($args['variant_id']) || empty($args['attr_name']) || '' == trim($args['attr_value'])) {
            return null;
        }
        $productSyncOption = LS_Vend()->product_option();

        $product_ID = (int)$args['product_id'];
        $variation_product_id = (int)$args['variant_id'];
        $attribute_label = $args['attr_name'];
        $attribute_name = ls_create_woo_attribute($attribute_label);
        $attribute_value = trim($args['attr_value']);

        if (!empty($attribute_label) && !empty($attribute_value)) {
            $attribute_option = $productSyncOption->attributes();
            $visible = '0';
            if ('1' == $productSyncOption->attributeVisibleOnProductPage()) {
                $visible = '1';
            }

            $attr = array(
                'name' => $attribute_name,
                'value' => '',
                'is_visible' => $visible,
                'is_variation' => '1',
                'is_taxonomy' => '1',
                'position' => 0,
            );

            $terms = wp_set_object_terms($product_ID, $attribute_value, $attribute_name, true);

            $term = ls_get_term_by_name($attribute_value, null, $attribute_name);

            //Check if there are no terms found and will try to search via slug
            if (false == $term) {
                $attribute_value = sanitize_title($attribute_value);
                $term = ls_get_term_by_slug($attribute_value, null, $attribute_name);
            }

            $attr_key = "attribute_" . $attribute_name;

            //Attibute option enabled
            if ('on' == $attribute_option) {

                if ('' != trim($term->slug)) {
                    update_post_meta($variation_product_id, $attr_key, $term->slug);
                }

                //Attribute option disabled
            } elseif ('on' != $attribute_option) {

                $var_attribute = get_post_meta($variation_product_id, $attr_key, true);
                if ('' != trim($term->slug) && '' != trim($var_attribute)) {
                    update_post_meta($variation_product_id, $attr_key, $term->slug);
                }
            }

            return array('tax_name' => $attribute_name, 'attr' => $attr);
        }

        return array('tax_name' => $attribute_name);
    }

    public static function createUpdateVariantProduct(LS_Product_Variant $variant)
    {
        $returnDataSet = array();
        $productSyncOption = LS_Vend()->product_option();
        $excluding_tax = LS_Vend_Helper::isExcludingTax();

        $deletedAt = $variant->get_deleted_at();
        $variantSku = $variant->get_sku();
        if (empty($deletedAt)) {
            $variantParentId = $variant->getParentMeta()->getWooProductId();
            $varProductId = LS_Product_Helper::getProductIdBySku($variantSku);
            $product_name = $variant->get_name();
            $status = 'publish';

            if (!empty($varProductId)) {
                $post_var_args['ID'] = $varProductId;
            }
            $post_var_args['post_title'] = $product_name;
            $post_var_args['post_status'] = $status;
            $post_var_args['post_type'] = 'product_variation';
            $post_var_args['post_parent'] = $variantParentId;

            $returnDataSet['post_data'] = $post_var_args;
            $varProductId = LS_Product_Helper::create($post_var_args, true);
            if (!empty($varProductId)) {
                //set last sync to the current update_at key
                LS_Vend()->option()->lastProductUpdate($variant->get_update_at());
                $returnDataSet['var_id'] = $varProductId;
                $var_meta = new LS_Product_Meta($varProductId);
                $var_meta->update_tax_value($variant->get_tax_value());
                $var_meta->update_tax_name($variant->get_tax_name());
                $var_meta->update_tax_rate($variant->get_tax_rate());
                $var_meta->update_tax_id($variant->get_tax_id());
                $var_meta->update_sku($variant->get_sku());
                $var_meta->update_visibility($status == 'publish' ? 'visible' : '');
                $var_meta->updateFromLinkSyncJson($variant->getJsonProduct());
                $var_meta->update_vend_product_id(get_vend_id($variant->get_id()));
                $variantQuantity = self::getProductQuantity($variant);

                if ('on' == $productSyncOption->price()) {

                    self::setWooProductTaxClassAndStatus($var_meta, $variant);
                    if ('on' == $excluding_tax) {
                        //If 'yes' then product price SELL Price(excluding any taxes.)
                        self::updateWooPrice($var_meta, $variant->get_sell_price());

                    } else {

                        //If 'no' then product price SELL Price(including any taxes.)
                        $tax_and_sell_price_product = $variant->get_sell_price() + $variant->get_tax_value();
                        self::updateWooPrice($var_meta, $tax_and_sell_price_product);
                    }
                }

                if ('on' == $productSyncOption->quantity()) {
                    if ($variant->has_outlets()) {
                        $var_meta->update_manage_stock('yes');
                        $var_meta->update_stock($variantQuantity);
                        $stock_status = ($variantQuantity > 0 ? 'instock' : 'outofstock');
                        $var_meta->update_stock_status($stock_status);
                    } else {
                        $var_meta->update_manage_stock('no');
                        $var_meta->update_stock(NULL);
                        $var_meta->update_stock_status('instock');
                    }
                }


                $variantOptions = LS_Vend_Helper::variant_options();
                for ($i = 1; $i <= 3; $i++) {

                    $attr_name = $variant->getData($variantOptions[$i] . 'name');
                    $attr_value = $variant->getData($variantOptions[$i] . 'value');
                    $attr_args = array(
                        'product_id' => $variantParentId,
                        'variant_id' => $varProductId,
                        'attr_name' => $attr_name,
                        'attr_value' => $attr_value
                    );

                    if (!empty($attr_name) && !empty($attr_value) && 'NULL' != $attr_value) {

                        //use for setting _product_attributes meta attribute
                        $attr_data = self::toWooVariantAttributes($attr_args);
                        $returnDataSet['_product_attributes'][$attr_data['attr']['name']] = $attr_data['attr'];
                    }
                }
            }

        } else {
            if ('on' == $productSyncOption->delete()) {
                LS_Product_Helper::deleteWooProductBySku($variantSku);
            }
        }

        return $returnDataSet;
    }

    public static function deleteWooVariantThatDoesNotExistInVend(LS_Product $product, LS_Product_Meta $product_meta)
    {
        $wooVariantSkuSets = LS_Product_Helper::getVariantSkus($product_meta->getWooProductId());
        $vendVariantSkuSets = $product->getAllVariantSku();
        $variantsThatDoesNotExistInVend = array_diff($wooVariantSkuSets, $vendVariantSkuSets);
        if (is_array($variantsThatDoesNotExistInVend)) {
            foreach ($variantsThatDoesNotExistInVend as $var_sku) {
                $varId = LS_Product_Helper::getProductIdBySku(trim($var_sku));
                if (!empty($varId)) {
                    wp_delete_post($varId, true);
                }
            }
        }

    }


    public static function get_vend_connected_products($orderBy = '', $order = 'asc')
    {
        global $wpdb;

        $orderBySql = '';
        if (!empty($orderBy)) {
            $orderBySql = 'ORDER BY ' . $orderBy . ' ' . strtoupper($order);
        } else {
            $orderBySql = 'ORDER BY wpmeta.meta_value ASC';
        }

        $sql = "
					SELECT
							wposts.ID,
							wposts.post_title AS product_name,
                            wposts.post_status AS product_status,
                            wpmeta.meta_key,
                            wpmeta.meta_value,
                            wposts.post_type AS product_type,
                            wposts.post_parent AS product_parent
					FROM $wpdb->postmeta AS wpmeta
					INNER JOIN $wpdb->posts as wposts on ( wposts.ID = wpmeta.post_id )
					WHERE 
					      wpmeta.meta_key IN ('_ls_vend_pid') AND wpmeta.meta_value != '' AND 
					      wposts.post_type IN('product','product_variation')
					  $orderBySql
				";

        //get all products with empty sku
        $empty_skus = $wpdb->get_results($sql, ARRAY_A);

        return $empty_skus;
    }

    /**
     * Update Product Vend Ids
     * @param $linksync_product
     */
    public static function update_vend_ids($linksync_product)
    {
        if (!empty($linksync_product) && !empty($linksync_product['id'])) {
            $responseProduct = new LS_Product($linksync_product);
            $productId = LS_Product_Helper::getProductIdBySku($responseProduct->get_sku());
            if (!empty($productId)) {
                $product_meta = new LS_Product_Meta($productId);
                $product_meta->update_vend_product_id(get_vend_id($responseProduct->get_id()));
            }

            if ($responseProduct->has_variant()) {
                $variants = $responseProduct->get_variants();
                foreach ($variants as $variation) {
                    $variationProduct = new LS_Product($variation);
                    $variationId = LS_Product_Helper::getProductIdBySku($variationProduct->get_sku());
                    if (!empty($variationId)) {
                        $variationMeta = new LS_Product_Meta($variationId);
                        $variationMeta->update_vend_product_id(get_vend_id($variationProduct->get_id()));
                    }
                }
            }
        }
    }

}