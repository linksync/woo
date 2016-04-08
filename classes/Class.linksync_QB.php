<?php

class linksync_class_QB extends linksync_class {
    /*
     * Quick Book Online Product Import 
     * The function used to Add / Update product in woocommerce 
     *
     * @param array product
     * 
     * @return true if goes well without any error 
     */

    public function importProductToWoocommerce_QBO($product) {
        global $wpdb;
        remove_all_actions('save_post');
        $ps_create_new = get_option('ps_create_new'); # Product Setting if Create New Checked box is ON
        $name = $product['name'];
        $reference = $product['sku'];
        $status = 'publish';
        $cost_price = $product['cost_price'];
        $tax_name = $product['tax_name'];
        $status = '';
        # @return product id if reference exists 
        $result_reference = self::isReferenceExists($reference); #  Check if already exist product into woocommerce 

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        if ($result_reference['result'] == 'success') { // it means it already exists  
            /*
             * Update exists product into WC
             */

            #Delete the product from the woocommerce with the value delected_at of the product
            if (get_option('ps_delete') == 'on') {
                if (!empty($product['deleted_at'])) {
                    wp_delete_post($result_reference['data']); //use the product Id and delete the product
                }
            }

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
                $db_sale_price = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . "postmeta` WHERE `post_id` = %d AND meta_key='_sale_price'",$result_reference['data']), ARRAY_A);
                if (get_option('excluding_tax') == 'on') {
                    //If 'yes' then product price SELL Price(excluding any taxes.)  
                    if (0 != $wpdb->num_rows) {
                        $result_sale_price = $db_sale_price[0];
                        if ($result_sale_price['meta_value'] == NULL) {
                            update_post_meta($result_reference['data'], '_price', $cost_price);
                        }
                    } else {
                        update_post_meta($result_reference['data'], '_price', $cost_price);
                    }
                    if (get_option('price_field') == 'regular_price') {
                        update_post_meta($result_reference['data'], '_regular_price',$cost_price);
                    } else {
                        update_post_meta($result_reference['data'], '_price', $cost_price);
                        update_post_meta($result_reference['data'], '_sale_price', $cost_price);
                    }
                } else {
                    //If 'no' then product price SELL Price(including any taxes.) 
                    $tax_and_cost_price_product = $cost_price + $product['tax_value'];
                    if (0 != $wpdb->num_rows) {
                        $result_sale_price = $db_sale_price[0];
                        if ($result_sale_price['meta_value'] == NULL) {
                            update_post_meta($result_reference['data'], '_price', $tax_and_cost_price_product);
                        }
                    } else {
                        update_post_meta($result_reference['data'], '_price', $tax_and_cost_price_product);
                    }
                    if (get_option('price_field') == 'regular_price') {
                        update_post_meta($result_reference['data'], '_regular_price', $tax_and_cost_price_product);
                    } else {
                        update_post_meta($result_reference['data'], '_price', $tax_and_cost_price_product);
                        update_post_meta($result_reference['data'], '_sale_price', $tax_and_cost_price_product);
                    }
                }
            }
# BRAND syncing ( update ) 
            if (in_array('woocommerce-brands/woocommerce-brands.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                if (get_option('ps_brand') == 'on') {
                    // Delete existing brand then create 
                    $term_taxonmy_id = array();
                    $data = $wpdb->get_results("SELECT term_taxonomy_id FROM  `" . $wpdb->prefix . "term_taxonomy` WHERE taxonomy='product_brand'", ARRAY_A);
                    foreach($data as $exists_brands) {
                        $sql = "DELETE FROM `" . $wpdb->term_relationships . "` WHERE object_id= %d AND term_taxonomy_id= %d ";
                        $wpdb->query($wpdb->prepare($sql,$result_reference['data'],$exists_brands['term_taxonomy_id']));
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
                                            $wpdb->insert(
                                                    $wpdb->term_relationships,
                                                    array(
                                                        'object_id'         =>  $result_reference['data'],
                                                        'term_taxonomy_id'  =>  $termid_taxonomy['term_taxonomy_id'],
                                                        'term_order'        =>  0
                                                    )
                                            );

                                            $wpdb->query(
                                                $wpdb->prepare(
                                                    "UPDATE `" . $wpdb->term_taxonomy .
                                                    "` SET count=count+1  WHERE term_id= %d ",
                                                    $termid_taxonomy['term_id']
                                                )
                                            );

                                        }
                                    }
                                }
                            }
                        }
                    }

                    unset($termid_taxonomy);
                }
            }
// if product in vend having status : inactive ( active==0  ) it should be not displayed (mark as draft in woo) 
            if ($product['active'] == '0')
                $status = 'draft';

            #---------GET product Status-------------#
            $product_status_db = $wpdb->get_results($wpdb->prepare("SELECT post_status FROM `" . $wpdb->posts . "` WHERE post_status ='pending' AND ID= %d ", $result_reference['data']), ARRAY_A);

            if (0 != $wpdb->num_rows) {
                if (get_option('ps_pending') == 'on')
                    $status = 'pending';
            }

            #Product Quantity 
            if (get_option('ps_quantity') == 'on') {
                //Check the Account is Plus Version(inventry account)  
                if ($product['quantity'] != NULL) {
                    update_post_meta($result_reference['data'], '_manage_stock', 'yes');
                    update_post_meta($result_reference['data'], '_stock', $product['quantity']);
                    update_post_meta($result_reference['data'], '_stock_status', ($product['quantity'] > 0 ? 'instock' : 'outofstock'));
                    if (get_option('ps_unpublish') == 'on' && $product['quantity'] < 1) {
                        $status = 'draft';
                    } else {
                        $status = 'publish';
                    }
                } else {
                    update_post_meta($result_reference['data'], '_manage_stock', 'no');
                    update_post_meta($result_reference['data'], '_stock', NULL);
                    update_post_meta($result_reference['data'], '_stock_status', 'instock');
                }
            } else {
                update_post_meta($result_reference['data'], '_manage_stock', 'no');
                update_post_meta($result_reference['data'], '_stock', NULL);
                update_post_meta($result_reference['data'], '_stock_status', 'instock');
            }
            $status = isset($status) && !empty($status) ? $status : 'publish';

            if (get_option('ps_unpublish') == 'on')
                update_post_meta($result_reference['data'], '_visibility', ($status == 'publish' ? 'visible' : ''));
            $my_product = array();
            $my_product['ID'] = $result_reference['data'];
            $my_product['post_status'] = $status;
            $my_product['post_modified'] = current_time('mysql');
            $my_product['post_modified_gmt'] = gmdate('Y-m-d h:i:s');
            if (get_option('ps_name_title') == 'on')
                $my_product['post_title'] = $name;
            //Update product Post
            wp_update_post($my_product);
            unset($status);
            /*
             * Ending Update product
             */
        } elseif ($result_reference['result'] == 'error') {
            /*
             * New Product Creation if "Create New" option enabled 
             */
            if ($ps_create_new == 'on' && empty($product['deleted_at'])) { # it's new product 
                // code for adding new product int WC
                // if product in vend having status : inactive ( active==0  ) it should be not displayed (mark as draft in woo) 
                if ($product['ps_name_title'] == 'on')
                    $product_name = isset($product['name']) && !empty($product['name']) ? $product['name'] : NULL;

                $my_post = array(
                    'post_title' => isset($product_name) && !empty($product_name) ? $product_name : NULL,
                    'post_author' => 1,
                    'post_type' => 'product'
                );
                $product_ID = wp_insert_post($my_post);
                if ($product_ID) {
                    add_post_meta($product_ID, '_sku', $product['sku']);
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


                            if (get_option('excluding_tax') == 'on') {
                                //If 'yes' then product price SELL Price(excluding any taxes.) 
                                add_post_meta($product_ID, '_price', $cost_price);
                                if (get_option('price_field') == 'regular_price') {
                                    add_post_meta($product_ID, '_regular_price', $cost_price);
                                } else {
                                    add_post_meta($product_ID, '_sale_price', $cost_price);
                                }
                            } else {
                                //If 'no' then product price SELL Price(including any taxes.) 
                                $tax_and_cost_price_product = $cost_price + $product['tax_value'];
                                add_post_meta($product_ID, '_price', $tax_and_cost_price_product);
                                if (get_option('price_field') == 'regular_price') {
                                    add_post_meta($product_ID, '_regular_price', $tax_and_cost_price_product);
                                } else {
                                    add_post_meta($product_ID, '_sale_price', $tax_and_cost_price_product);
                                }
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
                                                $wpdb->insert(
                                                        $wpdb->term_relationships,
                                                        array(
                                                            'object_id'         => $product_ID,
                                                            'term_taxonomy_id'  => $termid_taxonomy['term_taxonomy_id'],
                                                            'term_order'        => 0
                                                        )
                                                );

                                                $wpdb->query(
                                                        $wpdb->prepare(
                                                            "UPDATE `" . $wpdb->term_taxonomy .
                                                            "` SET count=count+1  WHERE term_id= %d ",
                                                            $termid_taxonomy['term_id']
                                                        )
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        unset($termid_taxonomy);
                    }
                }
                #Product Quantity 
                if (get_option('ps_quantity') == 'on') {
                    //Check the Account is Plus Version(inventry account)  
                    if ($product['quantity'] != NULL) {
                        add_post_meta($product_ID, '_manage_stock', 'yes');
                        add_post_meta($product_ID, '_stock', $product['quantity']);
                        add_post_meta($product_ID, '_stock_status', ($product['quantity'] > 0 ? 'instock' : 'outofstock'));
                        if (get_option('ps_unpublish') == 'on' && $product['quantity'] < 1) {
                            $status = 'draft';
                        } else {
                            $status = 'publish';
                        }
                    } else {
                        add_post_meta($product_ID, '_manage_stock', 'no');
                        add_post_meta($product_ID, '_stock', NULL);
                        add_post_meta($product_ID, '_stock_status', 'instock');
                        $status = 'publish';
                    }
                } else {
                    add_post_meta($product_ID, '_manage_stock', 'no');
                    add_post_meta($product_ID, '_stock', NULL);
                    add_post_meta($product_ID, '_stock_status', 'instock');
                    $status = 'publish';
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
                $product_status = array(
                    'ID' => $product_ID,
                    'post_status' => $status
                );
                wp_update_post($product_status);
                if (get_option('ps_unpublish') == 'on')
                    add_post_meta($product_ID, '_visibility', ($status == 'publish' ? 'visible' : ''));

                unset($status);
            }
        }

//        $prod_update_suc = get_option('prod_update_suc'); # it has NULL or DATETIME 
//        if (isset($prod_update_suc) && !empty($prod_update_suc)) {
//            LSC_Log::add('Product Sync Vend to Woo', 'success', 'Product synced SKU:' . $product['sku'], get_option('linksync_laid'));
//        }
    }

}

?>