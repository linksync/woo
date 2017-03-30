<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');


$product_type = get_option('product_sync_type');
if (get_option('linksync_status') == 'Active') {
    if ($product_type == 'two_way' || $product_type == 'wc_to_vend') {
        //check the post type (Product)
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'product') {
            global $wpdb;
            $taxsetup = false;
            $details = array();
            $varient_data = array();
            $testMode = get_option('linksync_test');
            $LAIDKey = get_option('linksync_laid');
            $apicall = new linksync_class($LAIDKey, $testMode);
            if ($apicall->lastresponse['result'] == 'success') {
                if ($_POST['post_status'] != 'trash') {
					/**
					 * Product Metas Instance to get custom metas and default metas
					 */
					$product_metas = new LS_Product_Meta( $_POST['post_ID'] );

                    if (@empty($_POST['_sku'])) {
                        $_POST['_sku'] = 'sku_' . $_POST['post_ID'];
                        update_post_meta($_POST['post_ID'], '_sku', 'sku_' . $_POST['post_ID']);
                    }
					if( isset($_POST['post_title']) ){
						$_POST['post_title'] = remove_escaping_str($_POST['post_title']);
					}
                    $product['sku'] = html_entity_decode($_POST['_sku']); //SKU(unique Key/Numbers) 

					$excluding_tax = ls_is_excluding_tax();

                    $display_retail_price_tax_inclusive = get_option('linksync_tax_inclusive');
                    //check for the product status ->publish or draft 
                    $product['active'] = (isset($_POST['post_status']) && $_POST['post_status'] == 'draft') ? 0 : 1;

#prices
//if the Product is update
                    if ($_POST['action'] == 'editpost' || $_POST['action'] == 'inline-save') { # only for POST ( Product type) 
                        if ($_POST['action'] == 'inline-save' || $_POST['original_post_status'] == 'publish' || $_POST['original_post_status'] == 'pending') { # Product being updated  
                            $product['name'] = (@get_option('ps_name_title') == 'on') ? html_entity_decode($_POST['post_title']) : null; ## Name/Title 

                            $product['description'] = (@get_option('ps_description') == 'on') ? remove_escaping_str(html_entity_decode($_POST['content'])) : null; ##Description
// Price with Tax
                            if (get_option('ps_price') == 'on') {
                                $tax_status = get_post_meta($_POST['post_ID'], '_tax_status', TRUE);
                                if (isset($tax_status) && $tax_status == 'taxable') {  # Product with TAX 
                                    $tax_class = get_post_meta($_POST['post_ID'], '_tax_class', TRUE);
                                    $taxname = empty($tax_class) ? 'standard-tax' : $tax_class;
                                    if (isset($taxname) && !empty($taxname)) {
                                        $response_taxes = linksyn_get_tax_details_for_product($taxname);
                                        if ($response_taxes['result'] == 'success') {
                                            //$product['tax_name'] = !empty($product_metas->get_tax_name()) ? $product_metas->get_tax_name() : html_entity_decode($response_taxes['data']['tax_name']);
                                            //$product['tax_rate'] = !empty($product_metas->get_tax_rate()) ? $product_metas->get_tax_rate() : $response_taxes['data']['tax_rate'];
                                            $taxsetup = true;
                                        }
                                    }
                                }

                                if ($excluding_tax == 'on') {  //ex ticked
                                    // No effect on price 
                                    # https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                                    if ($taxsetup) {
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
                                                //cost price:_regular_price
                                                $regular_price = (float) $_POST['_regular_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) @$product['tax_rate'];
                                                $tax_value = (float) ($regular_price * $tax_rate);

                                                //sell price:_regular_price
                                                /* For excluding tax (both Woo Tax Excluding and Vend Tax Excluding)
                                                 * display_retail_price_tax_inclusive 1, sell_price = Woo Final price + tax
                                                 * For display_retail_price_tax_inclusive 0, sell_price = Woo Final price
                                                 */

                                                if ($display_retail_price_tax_inclusive == '1') {
                                                    $price = $_POST['_regular_price'] + $tax_value;
                                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                                    $price = $_POST['_regular_price'];
                                                }
                                                $product['sell_price'] = str_replace(',', '.', $price);
                                                $product['list_price'] = str_replace(',', '.', $price);
                                                //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {
                                                $sale_price = (float) $_POST['_sale_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) @$product['tax_rate'];
                                                $tax_value = (float) ($sale_price * $tax_rate);
                                                if ($display_retail_price_tax_inclusive == '1') {
                                                    $price = $_POST['_sale_price'] + $tax_value;
                                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                                    $price = $_POST['_sale_price'];
                                                }
                                                $product['sell_price'] = str_replace(',', '.', $price);
                                                $product['list_price'] = str_replace(',', '.', $price);
                                                //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                            }
                                        }
                                    } else {  //not ticked exc
                                        //excluding tax off and tax not enabled in woocomerce 
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
                                                //sell price:_regular_price
                                                $product['sell_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                                $product['list_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {
                                                $product['sell_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                                $product['list_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                            }
                                        }
                                    }
                                } else {
                                    //Include Tax is checked
                                    if ($taxsetup) {
                                        /* For including tax (both Woo Tax Including and Vend Tax Including)
                                         * display_retail_price_tax_inclusive 1, sell_price = Woo Final price
                                         * For display_retail_price_tax_inclusive 0, sell_price = Woo Final price - tax
                                         */
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
                                                //cost price:_regular_price
                                                $regular_price = (float) $_POST['_regular_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) @$product['tax_rate'];
                                                $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                                if ($display_retail_price_tax_inclusive == '1') {
                                                    $price = $_POST['_regular_price'];
                                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                                    $price = $_POST['_regular_price'] - $tax_value;
                                                }
                                                //cost price:_regular_price 
                                                //sell price:_regular_price
                                                $product['sell_price'] = str_replace(',', '.', $price);
                                                $product['list_price'] = str_replace(',', '.', $price);
                                                //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {
                                                //cost price:_regular_price
                                                $regular_price = (float) $_POST['_sale_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) @$product['tax_rate'];
                                                $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                                if ($display_retail_price_tax_inclusive == '1') {
                                                    
                                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                                    $_POST['_sale_price'] = $_POST['_sale_price'] - $tax_value;
                                                }
                                                $product['sell_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                                $product['list_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                                //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                            }
                                        }
                                    } else {  //not ticked exc
                                        //excluding tax off and tax not enabled in woocomerce 
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
                                                //sell price:_regular_price
                                                $product['sell_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                                $product['list_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {
                                                $product['sell_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                                $product['list_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                            }
                                        }
                                    }
                                }
                            }
                            $product['includes_tax'] = (isset($_POST['_tax_status']) && $_POST['_tax_status'] == 'taxable') ? true : false;

                            unset($regular_price);
                            unset($tax_rate);
                            unset($tax_value);


#-------------Brand-------------#

                            if (get_option('ps_brand') == 'on') {

								if (isset($_POST['post_ID']) && !empty($_POST['post_ID'])) {
									$product['brands'] = ls_get_product_terms( $_POST['post_ID'] , 'brand' );
								} else {
									$product['brands'] = null;
								}

                            }

                            # outlets
                            if (isset($_POST['_manage_stock'])) {
                                if ($_POST['_manage_stock'] == 'yes' || $_POST['_manage_stock'] == '1') {
                                    if (get_option('ps_quantity') == 'on') {
                                        if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                            $getoutlets = get_option('wc_to_vend_outlet_detail');
                                            if (isset($getoutlets) && !empty($getoutlets)) {
                                                $outlet = explode('|', $getoutlets);
                                                if (isset($_POST['_stock']) && !empty($_POST['_stock'])) {
                                                    $product_outlets = array(array('name' => html_entity_decode($outlet[0]),
                                                            'quantity' => $_POST['_stock']));
                                                } else {
                                                    $product_outlets = array(array('name' => html_entity_decode($outlet[0]),
                                                            'quantity' => NULL));
                                                }
                                            }
                                        }
                                    } else {
                                        $product_outlets = array(array('quantity' => NULL));
                                    }
                                }
                            }
                            #-------------Outlets---------NULL----#
                            if (isset($_POST['product-type']) && $_POST['product-type'] == 'variable') {
                                
                            } else {
                                isset($product_outlets) ? $product['outlets'] = $product_outlets : $product['outlets'] = array();
                            }

#Tags
                            if (get_option('ps_tags') == 'on') {
                                if (isset($_POST['post_ID']) && !empty($_POST['post_ID'])) {
									$product['tags'] = ls_get_product_terms( $_POST['post_ID'], 'tag' );
                                } else {
                                    $product['tags'] = NULL; //If Tags NULL
                                }
                            }

                            ### ######### Start Varaible update  ########## 
                            $option = array(
                                1 => 'one',
                                2 => 'two',
                                3 => 'three',
                                4 => 'four',
                                5 => 'five',
                                6 => 'six',
                                7 => 'seven',
                                8 => 'eight',
                                9 => 'nine',
                                10 => 'ten'
                            );

                            if (isset($_POST['product-type']) && $_POST['product-type'] == 'variable') {
                                $varient_data = linksync_getVariantData($_POST['post_ID'], $excluding_tax, $display_retail_price_tax_inclusive);
                                $product['variants'] = $varient_data['variants'];
                                $total_product_outlets = $varient_data['total_product_outlets'];
                                if ($total_product_outlets > 0) {
                                    if (get_option('ps_quantity') == 'on') {
                                        if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                            $getoutlets = get_option('wc_to_vend_outlet_detail');
                                            if (isset($getoutlets) && !empty($getoutlets)) {
                                                $outlet = explode('|', $getoutlets);
                                                $product['outlets'] = array(array('name' => html_entity_decode($outlet[0]),
                                                        'quantity' => $total_product_outlets));
                                            }
                                        }
                                    } else {
                                        $product['outlets'] = array(array('quantity' => NULL));
                                    }
                                } else {
                                    $product['outlets'] = array();
                                }
                            } else if ($_POST['action'] == 'inline-save') {
                                $sql_query = "SELECT
                                                  ID,post_title
                                              FROM `" . $wpdb->posts . "`
                                              WHERE
                                                    post_type = 'product_variation' AND
                                                    post_parent = %d AND
                                                    post_status!='auto-draft'";

                                $variants_data = $wpdb->get_results($wpdb->prepare($sql_query,$_POST['post_ID']), ARRAY_A);
                                if (0 != $wpdb->num_rows) {
                                    $variable_product_outlets = 0;
                                    $post_detail = get_post_meta($_POST['post_ID']);
                                    $total_var_product = 0;
                                    foreach ($variants_data as $variant_data) {
                                        $variants_detail = get_post_meta($variant_data['ID']);
										$var_meta = new LS_Product_Meta($variant_data['ID']);

                                        if (@empty($variants_detail['_sku'][0])) {
                                            $variants_detail['_sku'][0] = 'sku_' . $variant_data['ID'];
                                            update_post_meta($variant_data['ID'], '_sku', 'sku_' . $_POST['post_ID']);
                                        }

                                        $variant['sku'] = html_entity_decode($variants_detail['_sku'][0]); //SKU(unique Key)
#Name/Title Check
                                        if (get_option('ps_name_title') == 'on') {
                                            $variant['name'] = html_entity_decode($variant_data['post_title']);
                                        }
#quantity
                                        if ($variants_detail['_manage_stock'][0] == 'yes') {
                                            if (@$variants_detail['_stock_status'][0] == 'instock') {
                                                $variant['quantity'] = @$variants_detail['_stock'][0];
                                            }
                                        }
// Price with Tax
                                        if (get_option('ps_price') == 'on') {
                                            $tax_status = get_post_meta($_POST['post_ID'], '_tax_status', true);
                                            if (isset($tax_status) && $tax_status == 'taxable') { # Product with TAX 
                                                $taxname = empty($variants_detail['_tax_class'][0]) ? 'standard-tax' : $variants_detail['_tax_class'][0];
                                                $response_taxes = linksyn_get_tax_details_for_product($taxname);
                                                if ($response_taxes['result'] == 'success') {
                                                    //$variant['tax_name'] = !empty($var_meta->get_tax_name())? $var_meta->get_tax_name(): html_entity_decode($response_taxes['data']['tax_name']);
                                                    //$variant['tax_rate'] = !empty($var_meta->get_tax_rate()) ? $var_meta->get_tax_rate() : $response_taxes['data']['tax_rate'];
                                                    $taxsetup = true;
                                                }
                                            }

                                            if ($excluding_tax == 'on') {
# https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                                                if ($taxsetup) {
                                                    if (get_option('price_field') == 'regular_price') {
                                                        if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
//cost price:_regular_price
                                                            $regular_price = (float) $variants_detail['_regular_price'][0];
// Get Tax_value
                                                            @$tax_rate = (float) @$variant['tax_rate'];
                                                            $tax_value = (float) ($regular_price * $tax_rate);
//sell price:_regular_price
                                                            //sell price:_regular_price
                                                            /* For excluding tax (both Woo Tax Excluding and Vend Tax Excluding)
                                                             * display_retail_price_tax_inclusive 1, sell_price = Woo Final price + tax
                                                             * For display_retail_price_tax_inclusive 0, sell_price = Woo Final price
                                                             */
                                                            if ($display_retail_price_tax_inclusive == '1') {
                                                                $variant_price = $variants_detail['_regular_price'][0] + $tax_value;
                                                            } elseif ($display_retail_price_tax_inclusive == '0') {
                                                                $variant_price = $variants_detail['_regular_price'][0];
                                                            }
                                                            $variant['sell_price'] = str_replace(',', '.', $variant_price);
                                                            $variant['list_price'] = str_replace(',', '.', $variant_price);
                                                            //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() : $tax_value;
                                                        }
                                                    } else {
                                                        if (isset($variants_detail['_sale_price'][0]) && !empty($variants_detail['_sale_price'][0])) {
                                                            $regular_price = (float) $variants_detail['_sale_price'][0];
// Get Tax_value
                                                            $tax_rate = (float) @$variant['tax_rate'];
                                                            $tax_value = (float) ($regular_price * $tax_rate);
                                                            if ($display_retail_price_tax_inclusive == '1') {
                                                                $variant_price = $variants_detail['_sale_price'][0] + $tax_value;
                                                            } elseif ($display_retail_price_tax_inclusive == '0') {
                                                                $variant_price = $variants_detail['_sale_price'][0];
                                                            }
                                                            $variant['sell_price'] = str_replace(',', '.', $variant_price);
                                                            $variant['list_price'] = str_replace(',', '.', $variant_price);
                                                            //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() : $tax_value;
                                                        }
                                                    }
                                                } else {
// excluding tax off and tax not enabled in woocomerce 
                                                    if (get_option('price_field') == 'regular_price') {
                                                        if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
//sell price:_regular_price
                                                            $variant['sell_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                                                            $variant['list_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                                                        }
                                                    } else {
                                                        if (isset($variants_detail['_sale_price'][0]) && !empty($variants_detail['_sale_price'][0])) {
                                                            $variant['sell_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                                                            $variant['list_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                                                        }
                                                    }
                                                }
                                            } else {
// No effect on price 
                                                if (get_option('price_field') == 'regular_price') {
                                                    if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
                                                        $regular_price = (float) $variants_detail['_regular_price'][0];
//                                                 Get Tax_value
                                                        if (isset($tax_rate)) {
                                                            $tax_rate = (float) $tax_rate;
                                                            $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                                        } else {
                                                            $tax_value = 0;
                                                        }

                                                        if ($display_retail_price_tax_inclusive == '1') {
                                                            
                                                        } elseif ($display_retail_price_tax_inclusive == '0') {
                                                            $variants_detail['_regular_price'][0] = $variants_detail['_regular_price'][0] - $tax_value;
                                                        }
//sell price:_regular_price
                                                        $variant['sell_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                                                        $variant['list_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                                                        //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() : $tax_value;
                                                    }
                                                } else {
                                                    if (isset($variants_detail['_sale_price'][0]) && !empty($variants_detail['_sale_price'][0])) {
                                                        $regular_price = (float) $variants_detail['_sale_price'][0];
//                                                 Get Tax_value
                                                        $tax_rate = (float) $tax_rate;
                                                        $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                                        if ($display_retail_price_tax_inclusive == '1') {
                                                            
                                                        } elseif ($display_retail_price_tax_inclusive == '0') {
                                                            $variants_detail['_sale_price'][0] = $variants_detail['_sale_price'][0] - $tax_value;
                                                        }
                                                        $variant['sell_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                                                        $variant['list_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                                                        //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() : $tax_value;
                                                    }
                                                }
                                            }
                                        }
// ATTRIBUTE && VARIANTS
										$variant_attributes = ls_get_variant_attributes( $variant_data['ID'] );
                                        if (!empty($variant_attributes)) {

                                            $option_key = 1;
                                            $vend_options = ls_vend_variant_option();
                                            $vend_options_count = count($vend_options);
                                            $option_name_str = 'name';
                                            $option_value_str = 'value';

                                            foreach ($variant_attributes as $variant_attribute) {
                                                if (isset($vend_options[$option_key])) {
                                                    $variant[ $vend_options[$option_key].$option_name_str ] = $variant_attribute[ $option_name_str ];
                                                    $variant[ $vend_options[$option_key].$option_value_str ] = $variant_attribute[ $option_value_str ];
                                                    $option_key++;

                                                }
                                            }

                                            for($i = $option_key; $i <= $vend_options_count; $i++){
                                                if(isset($vend_options[$i])){
                                                    $variant[ $vend_options[$i].$option_name_str ] = "NULL";
                                                    $variant[ $vend_options[$i].$option_value_str ] = "NULL";
                                                }
                                            }
                                        }
#qunantity-----UPDATE--variant---
                                        if (get_option('ps_quantity') == 'on') {
                                            if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                                $getoutlets = get_option('wc_to_vend_outlet_detail');
                                                if (isset($getoutlets) && !empty($getoutlets)) {
                                                    $outlets = explode('|', $getoutlets);
                                                    if ($variants_detail['_manage_stock'][0] == 'yes') {
                                                        if (isset($variants_detail['_stock'][0]) && !empty($variants_detail['_stock'][0])) {
                                                            $variant['outlets'] = array(array('name' => html_entity_decode($outlets[0]),
                                                                    'quantity' => $variants_detail['_stock'][0]));
                                                            $variable_product_outlets+=$variants_detail['_stock'][0];
                                                        } else {
                                                            $variant['outlets'] = array(array('name' => html_entity_decode($outlets[0]),
                                                                    'quantity' => NULL));
                                                        }
                                                    } else {
                                                        $variant['outlets'] = NULL;
                                                    }
                                                }
                                            } else {
                                                $variant['outlets'] = NULL;
                                            }
                                        } else {
                                            $variant['outlets'] = array(array('quantity' => NULL));
                                        }
                                        $product['variants'][] = $variant;
                                        $total_var_product++;
                                    }
                                    if ($variable_product_outlets > 0) {
                                        if (get_option('ps_quantity') == 'on') {
                                            if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                                $getoutlets = get_option('wc_to_vend_outlet_detail');
                                                if (isset($getoutlets) && !empty($getoutlets)) {
                                                    $outlet = explode('|', $getoutlets);
                                                    $product['outlets'] = array(array('name' => html_entity_decode($outlet[0]),
                                                            'quantity' => $variable_product_outlets));
                                                }
                                            }
                                        } else {
                                            $product['outlets'] = array(array('quantity' => NULL));
                                        }
                                    } else {
                                        $product['outlets'] = array();
                                    }
                                }
                            }
                            $data = json_encode($product);
                            $response = $apicall->linksync_postProduct($data);
                            $devLogMessage = 'Json data being sent: <br/><textarea>'.$data.'</textarea><br/>';
                            $devLogMessage .= '<br/> Response from LWS: <br/> <pre>'.json_encode($response).'</pre>';
                            LSC_Log::add_dev_success('Product Sync Woocommerce to Vend', $devLogMessage);
                            LSC_Log::add('Product Sync Woo to Vend', 'success', 'Product synced SKU:' . $product['sku'], $LAIDKey);
                        } else if ($_POST['original_post_status'] == 'auto-draft' || $_POST['original_post_status'] == 'draft') {

//if the product is publish new
// Title, Description, qty, price etc, even if these options are not enabled on the Admin UI.
                            if ((get_option('ps_name_title') == 'on'))
                                $product['name'] = (isset($_POST['post_title']) && !empty($_POST['post_title'])) ? html_entity_decode($_POST['post_title']) : null;
#Description 
                            if ((get_option('ps_description') == 'on'))
                                $product['description'] = (isset($_POST['content']) && !empty($_POST['content'])) ? remove_escaping_str(html_entity_decode($_POST['content'])) : null;

                            if (get_option('ps_price') == 'on') {
//                                 Tax && Price  
                                if (isset($_POST['_tax_status']) && $_POST['_tax_status'] == 'taxable') { # Product with TAX  
                                    $taxname = empty($_POST['_tax_class']) ? 'standard-tax' : $_POST['_tax_class'];
                                    $response_taxes = linksyn_get_tax_details_for_product($taxname);
                                    if ($response_taxes['result'] == 'success') {
                                        //$product['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                                        //$product['tax_rate'] = $response_taxes['data']['tax_rate'];
                                        $taxsetup = true;
                                    }
                                }

                                if ($excluding_tax == 'on') {
                                    # https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                                    if ($taxsetup) {
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
//cost price:_regular_price
                                                $regular_price = (float) $_POST['_regular_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) @$product['tax_rate'];
                                                $tax_value = (float) ($regular_price * $tax_rate);

                                                if ($display_retail_price_tax_inclusive == '1') {
                                                    $price = $_POST['_regular_price'] + $tax_value;
                                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                                    $price = $_POST['_regular_price'];
                                                }
//sell price:_regular_price
                                                $product['sell_price'] = str_replace(',', '.', $price);
                                                $product['list_price'] = str_replace(',', '.', $price);
                                                //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {

                                                $regular_price = (float) $_POST['_sale_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) @$product['tax_rate'];
                                                $tax_value = (float) ($regular_price * $tax_rate);
                                                if ($display_retail_price_tax_inclusive == '1') {
                                                    $price = $_POST['_sale_price'] + $tax_value;
                                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                                    $price = $_POST['_sale_price'];
                                                }
                                                $product['sell_price'] = str_replace(',', '.', $price);
                                                $product['list_price'] = str_replace(',', '.', $price);
                                                //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                            }
                                        }
                                    } else {
//                                         excluding tax on and tax not enabled in woocomerce 
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
//sell price:_regular_price
                                                $product['sell_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                                $product['list_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {
//sell price:_regular_price
                                                $product['sell_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                                $product['list_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                            }
                                        }
                                    }
                                } else {
                                    //      No effect on price 
                                    if (get_option('price_field') == 'regular_price') {
                                        if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
                                            $regular_price = (float) $_POST['_regular_price'];
//                                                 Get Tax_value
                                            if (isset($product['tax_rate'])) {
                                                $tax_rate = (float) $product['tax_rate'];
                                                $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                            } else {
                                                $tax_value = 0;
                                            }
                                            if ($display_retail_price_tax_inclusive == '1') {
                                                
                                            } elseif ($display_retail_price_tax_inclusive == '0') {
                                                $_POST['_regular_price'] = $_POST['_regular_price'] - $tax_value;
                                            }
//sell price:_regular_price
                                            $product['sell_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                            $product['list_price'] = str_replace(',', '.', $_POST['_regular_price']);
                                            //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                        }
                                    } else {
                                        if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {
                                            $regular_price = (float) $_POST['_sale_price'];
//                                                 Get Tax_value
                                            if (isset($product['tax_rate'])) {
                                                $tax_rate = (float) $product['tax_rate'];
                                                $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                            } else {
                                                $tax_value = 0;
                                            }

                                            if ($display_retail_price_tax_inclusive == '1') {
                                                
                                            } elseif ($display_retail_price_tax_inclusive == '0') {
                                                $_POST['_sale_price'] = $_POST['_sale_price'] - $tax_value;
                                            }
//sell price:_regular_price
                                            $product['sell_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                            $product['list_price'] = str_replace(',', '.', $_POST['_sale_price']);
                                            //$product['tax_value'] = !empty($product_metas->get_tax_value()) ? $product_metas->get_tax_value() : $tax_value;
                                        }
                                    }
                                }
                            }
                            if (get_option('ps_brand') == 'on') {
								if (isset($_POST['post_ID']) && !empty($_POST['post_ID'])) {
									$product['brands'] = ls_get_product_terms( $_POST['post_ID'] , 'brand' );
								} else {
									$product['brands'] = null;
								}
                            }

#qunantity
//Outlets
                            if (get_option('ps_quantity') == 'on') {
                                if (isset($_POST['_manage_stock']) && $_POST['_manage_stock'] == 'yes') {
                                    if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                        $getoutlets = get_option('wc_to_vend_outlet_detail');
                                        if (isset($getoutlets) && !empty($getoutlets)) {
                                            $outlet = explode('|', $getoutlets);
                                            if (isset($_POST['_stock']) && !empty($_POST['_stock'])) {
                                                $product_outlets = array(array('name' => isset($outlet[0]) ? html_entity_decode($outlet[0]) : '',
                                                        'quantity' => $_POST['_stock']));
                                            } else {
                                                $product_outlets = array(array('name' => isset($outlet[0]) ? html_entity_decode($outlet[0]) : '',
                                                        'quantity' => NULL));
                                            }
                                        }
                                    }
                                }
                            } else {
                                $product_outlets = array(array('quantity' => NULL));
                            }
#-------------Outlets---------NULL----#
                            isset($product_outlets) ? $product['outlets'] = $product_outlets : $product['outlets'] = array();
//Tags 
                            if (get_option('ps_tags') == 'on') {
								if (isset($_POST['post_ID']) && !empty($_POST['post_ID'])) {
									$product['tags'] = ls_get_product_terms( $_POST['post_ID'], 'tag' );
								} else {
									$product['tags'] = NULL; //If Tags NULL
								}
                            }
//include_tax
                            $product['includes_tax'] = (isset($_POST['_tax_status']) && $_POST['_tax_status'] == 'taxable') ? true : false;

                            ### ######### Start Varaible  ##########
                            if (isset($_POST['product-type']) && $_POST['product-type'] == 'variable') {
                                $varient_data = linksync_getVariantData($_POST['post_ID'], $excluding_tax, $display_retail_price_tax_inclusive);
                                $product['variants'] = $varient_data['variants'];
                                $total_product_outlets = $varient_data['total_product_outlets'];
                                if ($total_product_outlets > 0) {
                                    if (get_option('ps_quantity') == 'on') {
                                        if (get_option('ps_wc_to_vend_outlet') == 'on') {
                                            $getoutlets = get_option('wc_to_vend_outlet_detail');
                                            if (isset($getoutlets) && !empty($getoutlets)) {
                                                $outlet = explode('|', $getoutlets);
                                                $product['outlets'] = array(array('name' => html_entity_decode($outlet[0]),
                                                        'quantity' => $total_product_outlets));
                                            }
                                        }
                                    } else {
                                        $product['outlets'] = array(array('quantity' => NULL));
                                    }
                                } else {
                                    $product['outlets'] = array();
                                }
                            }
                            $data = json_encode($product);
                            $response = $apicall->linksync_postProduct($data);
                            $devLogMessage = 'Json data being sent: <br/><textarea>'.$data.'</textarea><br/>';
                            $devLogMessage .= '<br/> Response from LWS: <br/>'.json_encode($response);
                            LSC_Log::add_dev_success('Product Sync Woocommerce to Vend', $devLogMessage);
                            LSC_Log::add('Product Sync Woo to Vend', 'success', 'Product synced SKU:' . $product['sku'], $LAIDKey);
                        }
                    }
                }
            } else {
                die('Error in Configuration ' . $apicall['lastresponse']['message']);
            }
        }
    }
}

// Helper functions  
function linksyn_get_tax_details_for_product($taxname) {
    $result = array();
    $taxDb = get_option('tax_class');
    if (isset($taxDb) && !empty($taxDb)) {
        $tax_class = explode(",", $taxDb);
        foreach ($tax_class as $new) {
            $taxes = explode("|", $new);
            if (in_array($taxname, $taxes)) {
                $tax = explode("-", @$taxes[0]);
                $result['tax_rate'] = @$tax[1]; //tax_rate
                $result['tax_name'] = @$tax[0]; //tax_name  
                return array('result' => 'success', 'data' => $result);
            }
        }
    }
    return array('result' => 'error', 'data' => 'no tax rule set');
}

function linksync_getVariantData($product_ID, $excluding_tax, $display_retail_price_tax_inclusive) {
    global $wpdb;
    $taxsetup = false;
    $sql_query = "SELECT
                    ID,post_title
                  FROM `" . $wpdb->posts . "`
                  WHERE
                    post_type = 'product_variation' AND
                    post_parent = %d AND
                    post_status!='auto-draft'";

    $variants_data = $wpdb->get_results($wpdb->prepare($sql_query,$product_ID), ARRAY_A);
//  $variants_data = mysql_query("SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` WHERE post_type = 'product_variation' AND post_parent ='" . $product_wc['ID'] . "'");
    if (0 != $wpdb->num_rows) {
        $total_var_product = 0;
        $total_product_outlets = 0;
        foreach ($variants_data as $variant_data) {
            $option = array(
                1 => 'one',
                2 => 'two',
                3 => 'three',
                4 => 'four',
                5 => 'five',
                6 => 'six',
                7 => 'seven',
                8 => 'eight',
                9 => 'nine',
                10 => 'ten'
            );
            $variants_detail = get_post_meta($variant_data['ID']);
            if (@empty($variants_detail['_sku'][0])) {
                $variants_detail['_sku'][0] = 'sku_' . $variant_data['ID'];
            }
            $variants_detail['_sku'][0] = linksync_removespaces_sku_add_action($variants_detail['_sku'][0]);
            update_post_meta($variant_data['ID'], '_sku', $variants_detail['_sku'][0]);
            $variant['sku'] = html_entity_decode($variants_detail['_sku'][0]); //SKU(unique Key)

			$var_meta = new LS_Product_Meta( $variant_data['ID'] );
#Name/Title Check
            if (get_option('ps_name_title') == 'on') {
                $variant['name'] = html_entity_decode($variant_data['post_title']);
            }

#quantity 
            if ($variants_detail['_manage_stock'][0] == 'yes') {
                if (@$variants_detail['_stock_status'][0] == 'instock') {
                    $variant['quantity'] = @$variants_detail['_stock'][0];
                }
            }


// Price with Tax
            if (get_option('ps_price') == 'on') {
                if (isset($variants_detail['_tax_status'][0]) && $variants_detail['_tax_status'][0] == 'taxable') { # Product with TAX 
                    $taxname = empty($variants_detail['_tax_class'][0]) ? 'standard-tax' : $variants_detail['_tax_class'][0];
                    $response_taxes = linksyn_get_tax_details_for_product($taxname);
                    if ($response_taxes['result'] == 'success') {
                        //$variant['tax_name'] = !empty($var_meta->get_tax_name()) ? $var_meta->get_tax_name() : html_entity_decode($response_taxes['data']['tax_name']);
                        //$variant['tax_rate'] = !empty($var_meta->get_tax_rate()) ? $var_meta->get_tax_rate() : $response_taxes['data']['tax_rate'];
                        $taxsetup = true;
                    }
                }

                if ($excluding_tax == 'on') {
# https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                    if ($taxsetup) {
                        if (get_option('price_field') == 'regular_price') {
                            if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
//cost price:_regular_price
                                $regular_price = (float) $variants_detail['_regular_price'][0];
// Get Tax_value
                                @$tax_rate = (float) @$variant['tax_rate'];
                                $tax_value = (float) ($regular_price * $tax_rate);
                                if ($display_retail_price_tax_inclusive == '1') {
                                    $variant_price = $variants_detail['_regular_price'][0] + $tax_value;
                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                    $variant_price = $variants_detail['_regular_price'][0];
                                }
                                //sell price:_regular_price
                                $variant['sell_price'] = str_replace(',', '.', $variant_price);
                                $variant['list_price'] = str_replace(',', '.', $variant_price);
                                //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() : $tax_value;
                            }
                        } else {
                            if (isset($variants_detail['_sale_price'][0]) && !empty($variants_detail['_sale_price'][0])) {
                                $regular_price = (float) $variants_detail['_sale_price'][0];
// Get Tax_value
                                $tax_rate = (float) @$variant['tax_rate'];
                                $tax_value = (float) ($regular_price * $tax_rate);
                                if ($display_retail_price_tax_inclusive == '1') {
                                    $variant_price = $variants_detail['_sale_price'][0] + $tax_value;
                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                    $variant_price = $variants_detail['_sale_price'][0];
                                }
                                $variant['sell_price'] = str_replace(',', '.', $variant_price);
                                $variant['list_price'] = str_replace(',', '.', $variant_price);
                                //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() :$tax_value;
                            }
                        }
                    } else {
// excluding tax off and tax not enabled in woocomerce 
                        if (get_option('price_field') == 'regular_price') {
                            if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
//sell price:_regular_price
                                $variant['sell_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                                $variant['list_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                            }
                        } else {
                            if (isset($variants_detail['_sale_price'][0]) && !empty($variants_detail['_sale_price'][0])) {
                                $variant['sell_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                                $variant['list_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                            }
                        }
                    }
                } else {
// No effect on price  
                    if (get_option('price_field') == 'regular_price') {
                        if (isset($variants_detail['_regular_price'][0]) && !empty($variants_detail['_regular_price'][0])) {
                            $regular_price = (float) $variants_detail['_regular_price'][0];
// Get Tax_value
                            if (isset($variant['tax_rate'])) {
                                $tax_rate = (float) $variant['tax_rate'];
                                $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                            } else {
                                $tax_value = 0;
                            }
                            if ($display_retail_price_tax_inclusive == '1') {
                                //display_retail_price_tax_inclusive 1, sell_price = Woo Final price
                            } elseif ($display_retail_price_tax_inclusive == '0') {
                                $variants_detail['_regular_price'][0] = $variants_detail['_regular_price'][0] - $tax_value; //For display_retail_price_tax_inclusive 0, sell_price = Woo Final price - tax
                            }
//sell price:_regular_price
                            $variant['sell_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                            $variant['list_price'] = str_replace(',', '.', $variants_detail['_regular_price'][0]);
                            //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() : $tax_value;
                        }
                    } else {
                        if (isset($variants_detail['_sale_price'][0]) && !empty($variants_detail['_sale_price'][0])) {
                            $regular_price = (float) $variants_detail['_sale_price'][0];
// Get Tax_value
                            if (isset($variant['tax_rate'])) {
                                $tax_rate = (float) $variant['tax_rate'];
                                $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                            } else {
                                $tax_value = 0;
                            }
                            if ($display_retail_price_tax_inclusive == '1') {
                                //display_retail_price_tax_inclusive 1, sell_price = Woo Final price
                            } elseif ($display_retail_price_tax_inclusive == '0') {
                                $variants_detail['_sale_price'][0] = $variants_detail['_sale_price'][0] - $tax_value; //For display_retail_price_tax_inclusive 0, sell_price = Woo Final price - tax
                            }
                            $variant['sell_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                            $variant['list_price'] = str_replace(',', '.', $variants_detail['_sale_price'][0]);
                            //$variant['tax_value'] = !empty($var_meta->get_tax_value()) ? $var_meta->get_tax_value() : $tax_value;
                        }
                    }
                }
            }
// ATTRIBUTE && VARIANTS
			$variant_attributes = ls_get_variant_attributes( $variant_data['ID'] );
            if (!empty($variant_attributes)) {

                $option_key = 1;
                $vend_options = ls_vend_variant_option();
                $vend_options_count = count($vend_options);
                $option_name_str = 'name';
                $option_value_str = 'value';

                foreach ($variant_attributes as $variant_attribute) {
                    if (isset($vend_options[$option_key])) {
                        $variant[ $vend_options[$option_key].$option_name_str ] = $variant_attribute[ $option_name_str ];
                        $variant[ $vend_options[$option_key].$option_value_str ] = $variant_attribute[ $option_value_str ];
                        $option_key++;

                    }
                }

                for($i = $option_key; $i <= $vend_options_count; $i++){
                    if(isset($vend_options[$i])){
                        $variant[ $vend_options[$i].$option_name_str ] = "NULL";
                        $variant[ $vend_options[$i].$option_value_str ] = "NULL";
                    }
                }
            }

#qunantity-----UPDATE--variant---
            if (get_option('ps_quantity') == 'on') {
                if (get_option('ps_wc_to_vend_outlet') == 'on') {
                    $getoutlets = get_option('wc_to_vend_outlet_detail');
                    if (isset($getoutlets) && !empty($getoutlets)) {
                        $outlets = explode('|', $getoutlets);
                        if ($variants_detail['_manage_stock'][0] == 'yes') {
                            if (isset($variants_detail['_stock'][0]) && !empty($variants_detail['_stock'][0])) {
                                $variant['outlets'] = array(array('name' => html_entity_decode($outlets[0]),
                                        'quantity' => $variants_detail['_stock'][0]));
                            } else {
                                $variant['outlets'] = array(array('name' => html_entity_decode($outlets[0]),
                                        'quantity' => NULL));
                            }
                        } else {
                            $variant['outlets'] = NULL;
                        }
                    }
                }
            } else {
                $variant['outlets'] = array(array('quantity' => NULL));
            }
            $total_product_outlets+= isset($variant['quantity']) ? $variant['quantity'] : 0;
            $product_variant[] = $variant;
            unset($variant['quantity']);
            $total_var_product++;
        }
    }
    $variant_product_details['variants'] = $product_variant;
    $variant_product_details['total_product_outlets'] = $total_product_outlets;
    return $variant_product_details;
}

function linksync_removespaces_sku_add_action($sku) {
    if (isset($sku) && !empty($sku)) {
        if (strpos($sku, ' ')) {
            $sku_replaced = str_replace(' ', '', $sku);
            $sku = $sku_replaced;
        }
        $search = array('/', '\\', ':', ';', '!', '@', '#', '$', '%', '^', '*', '(', ')', '+', '=', '|', '{', '}', '[', ']', '"', "'", '<', '>', ',', '?', '~', '`', '&', '.');
        foreach ($search as $special) {
            if (strpos($sku, $special)) {
                $sku_replaced = str_replace($special, '-', $sku);
                $sku = $sku_replaced;
            }
        }
        return $sku;
    }
}