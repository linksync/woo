<?php

require(dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once(dirname(__FILE__) . '/../../../classes/Class.linksync.php'); # Class file having API Call functions
global $wp;
// Initializing 
$wp->init();
$wp->parse_request();
$wp->query_posts();
$wp->register_globals();
$product_sync_type = get_option('product_sync_type');
if ($_POST['communication_key'] != get_option('webhook_url_code')) {
    die('Access is Denied');
}

function logs_detail() {
    $total_post = get_option('post_product');
    $LAIDKey = get_option('linksync_laid');
    update_option('linksync_sycning_status', 'completed');
    LSC_Log::add('Product Sync Woo to Vend', 'success', $total_post . ' Product synced', $LAIDKey);
}

if (isset($_POST['get_total'])) {
    logs_detail();
    exit;
}
if (isset($product_sync_type) && $product_sync_type == 'wc_to_vend' || $product_sync_type == 'two_way') {
    update_option('linksync_sycning_status', 'running');
    set_time_limit(0);
    $testMode = get_option('linksync_test');
    $LAIDKey = get_option('linksync_laid');
    $apicall = new linksync_class($LAIDKey, $testMode);
    $productimported = array();
    $productnotimported = array();
    global $wpdb;
    if (isset($_POST['offset']) && !empty($_POST['offset'])) {
        //sleep(10);
        $offset = ($_POST['offset'] - 1); // start from 0
        $query_with_limit = "SELECT ID,post_status,post_title,post_content,post_type FROM `" . $wpdb->prefix . "posts`  WHERE post_type = 'product' AND post_status!='auto-draft' ORDER BY ID ASC LIMIT " . $offset . ",1";
        $product_details = $wpdb->get_results($query_with_limit, ARRAY_A);
        $product_wc = $product_details[0];
        if ($product_wc['post_status'] != 'trash') {
			$excluding_tax = ls_is_excluding_tax();
            $display_retail_price_tax_inclusive = get_option('linksync_tax_inclusive');
            $taxsetup = false;
            $product = array();
            $post_detail = get_post_meta($product_wc['ID']);
            if (@empty($post_detail['_sku'][0])) {
                $post_detail['_sku'][0] = 'sku_' . $product_wc['ID'];
            }
            $post_detail['_sku'][0] = linksync_removespaces_sku($post_detail['_sku'][0]);
            update_post_meta($product_wc['ID'], '_sku', $post_detail['_sku'][0]);
            $product['sku'] = html_entity_decode($post_detail['_sku'][0]);
            $product['active'] = isset($product_wc['post_status']) && $product_wc['post_status'] == 'publish' ? 1 : 0;
            if (get_option('ps_price') == 'on') {
                if (isset($post_detail['_tax_status'][0]) && $post_detail['_tax_status'][0] == 'taxable') {
                    $taxname = empty($post_detail['_tax_class'][0]) ? 'standard-tax' : $post_detail['_tax_class'][0];
                    $response_taxes = linksyn_get_tax_details_syncall($taxname);
                    if ($response_taxes['result'] == 'success') {
                        //$product['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                        //$product['tax_rate'] = $response_taxes['data']['tax_rate'];
                        $taxsetup = true;
                    }
                }
                if ($excluding_tax == 'on') {
                    // For excluding tax (both Woo Tax Excluding and Vend Tax Excluding)
                    if ($taxsetup) {
                        if (get_option('price_field') == 'regular_price') {
                            if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
//cost price:_regular_price
                                $regular_price = (float) $post_detail['_regular_price'][0];
                                $tax_rate = (float) @$product['tax_rate'];
                                $tax_value = (float) ($regular_price * $tax_rate);
//sell price:_regular_price 

                                if ($display_retail_price_tax_inclusive == '1') {
                                    $price = $post_detail['_regular_price'][0] + $tax_value; //display_retail_price_tax_inclusive 1, sell_price = Woo Final price + tax
                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                    $price = $post_detail['_regular_price'][0]; //For display_retail_price_tax_inclusive 0, sell_price = Woo Final price
                                }

                                $product['sell_price'] = str_replace(',', '.', $price);

                                $product['list_price'] = str_replace(',', '.', $price);
                                //$product['tax_value'] = $tax_value;
                            }
                        } else {
                            if (isset($post_detail['_sale_price'][0]) && !empty($post_detail['_sale_price'][0])) {
                                $regular_price = (float) $post_detail['_sale_price'][0];
                                $tax_rate = (float) @$product['tax_rate'];
                                $tax_value = (float) ($regular_price * $tax_rate);
                                if ($display_retail_price_tax_inclusive == '1') {
                                    $price = $post_detail['_sale_price'][0] + $tax_value; //display_retail_price_tax_inclusive 1, sell_price = Woo Final price + tax
                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                    $price = $post_detail['_sale_price'][0]; //For display_retail_price_tax_inclusive 0, sell_price = Woo Final price
                                }
                                $product['sell_price'] = str_replace(',', '.', $price);
                                $product['list_price'] = str_replace(',', '.', $price);
                                //$product['tax_value'] = $tax_value;
                            }
                        }
                    } else {
// excluding tax off and tax not enabled in woocomerce 
                        if (get_option('price_field') == 'regular_price') {
                            if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
//cost price:_regular_price
//sell price:_regular_price
                                $product['sell_price'] = str_replace(',', '.', $post_detail['_regular_price'][0]);
                                $product['list_price'] = str_replace(',', '.', $post_detail['_regular_price'][0]);
                            }
                        } else {
                            if (isset($post_detail['_sale_price'][0]) && !empty($post_detail['_sale_price'][0])) {
                                $product['sell_price'] = str_replace(',', '.', $post_detail['_sale_price'][0]);
                                $product['list_price'] = str_replace(',', '.', $post_detail['_sale_price'][0]);
                            }
                        }
                    }
                } else {
                    if ($taxsetup) {
                        // For including tax (both Woo Tax Including and Vend Tax Including)
// No effect on price 
                        if (get_option('price_field') == 'regular_price') {
                            $regular_price = (float) $post_detail['_regular_price'][0];
                            $tax_rate = (float) @$product['tax_rate'];
                            $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                            if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
                                if ($display_retail_price_tax_inclusive == '1') {
                                    //display_retail_price_tax_inclusive 1, sell_price = Woo Final price
                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                    $post_detail['_regular_price'][0] = $post_detail['_regular_price'][0] - $tax_value; //For display_retail_price_tax_inclusive 0, sell_price = Woo Final price - tax
                                }
//sell price:_regular_price
                                $product['sell_price'] = str_replace(',', '.', $post_detail['_regular_price'][0]);
                                $product['list_price'] = str_replace(',', '.', $post_detail['_regular_price'][0]);
                                //$product['tax_value'] = $tax_value;
                            }
                        } else {
                            if (isset($post_detail['_sale_price'][0]) && !empty($post_detail['_sale_price'][0])) {
                                $regular_price = (float) $post_detail['_sale_price'][0];
                                $tax_rate = (float) @$product['tax_rate'];
                                $tax_value = ($regular_price - ($regular_price / (1 + $tax_rate)));
                                if ($display_retail_price_tax_inclusive == '1') {
                                    //display_retail_price_tax_inclusive 1, sell_price = Woo Final price
                                } elseif ($display_retail_price_tax_inclusive == '0') {
                                    $post_detail['_sale_price'][0] = $post_detail['_sale_price'][0] - $tax_value; //For display_retail_price_tax_inclusive 0, sell_price = Woo Final price - tax
                                }

                                $product['sell_price'] = str_replace(',', '.', $post_detail['_sale_price'][0]);
                                $product['list_price'] = str_replace(',', '.', $post_detail['_sale_price'][0]);
                                //$product['tax_value'] = $tax_value;
                            }
                        }
                    } else {
// excluding tax off and tax not enabled in woocomerce 
                        if (get_option('price_field') == 'regular_price') {
                            if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
//cost price:_regular_price
//sell price:_regular_price
                                $product['sell_price'] = str_replace(',', '.', $post_detail['_regular_price'][0]);
                                $product['list_price'] = str_replace(',', '.', $post_detail['_regular_price'][0]);
                            }
                        } else {
                            if (isset($post_detail['_sale_price'][0]) && !empty($post_detail['_sale_price'][0])) {
                                $product['sell_price'] = str_replace(',', '.', $post_detail['_sale_price'][0]);
                                $product['list_price'] = str_replace(',', '.', $post_detail['_sale_price'][0]);
                            }
                        }
                    }
                }
            }
            if (isset($post_detail['_stock_status'][0]) && $post_detail['_stock_status'][0] == 'instock') {
                $product['quantity'] = isset($post_detail['_stock'][0]) ? $post_detail['_stock'][0] : 0;
            }
#Name/Title Check
            if (get_option('ps_name_title') == 'on') {
                $product['name'] = html_entity_decode($product_wc['post_title']);
            }
#Description 
            if (get_option('ps_description') == 'on') {
                $product['description'] = remove_escaping_str( html_entity_decode($product_wc['post_content']) );
            }
            $product['includes_tax'] = (isset($post_detail['_tax_status'][0]) && $post_detail['_tax_status'][0] == 'taxable') ? true : false;
#---Outlet---Product----#
            if (get_option('ps_quantity') == 'on') {
                if (get_option('ps_wc_to_vend_outlet') == 'on') {
                    $getoutlets = get_option('wc_to_vend_outlet_detail');
                    if (isset($getoutlets) && !empty($getoutlets)) {
                        $outlet = explode('|', $getoutlets);
                        if (isset($post_detail['_stock'][0]) && !empty($post_detail['_stock'][0])) {
                            $product['outlets'] = array(array('name' => html_entity_decode($outlet[0]),
                                    'quantity' => $post_detail['_stock'][0]));
                        }
                    }
                }
            } else {
                $product['outlets'] = array(array('quantity' => NULL));
            }
#qunantity
//        

            if (get_option('ps_tags') == 'on') {
				$product['tags'] = ls_get_product_terms( $product_wc['ID'], 'tag' );
            }

            if (get_option('ps_brand') == 'on') {
				$product['brands'] = ls_get_product_terms( $product_wc['ID'], 'brand' );
            }

            $variants_data = $wpdb->get_results($wpdb->prepare("SELECT ID,post_title FROM `" . $wpdb->posts . "` WHERE post_type = 'product_variation' AND post_parent = %d AND post_status!='auto-draft'", $product_wc['ID'] ),ARRAY_A);
//  $variants_data = mysql_query("SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` WHERE post_type = 'product_variation' AND post_parent ='" . $product_wc['ID'] . "'");
            if (0 != $wpdb->num_rows) {
                $total_var_product = 0;
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
                    $variants_detail['_sku'][0] = linksync_removespaces_sku($variants_detail['_sku'][0]);
                    update_post_meta($variant_data['ID'], '_sku', $variants_detail['_sku'][0]);
                    $variant['sku'] = html_entity_decode($variants_detail['_sku'][0]); //SKU(unique Key)
#Name/Title Check
                    if (get_option('ps_name_title') == 'on') {
                        $variant['name'] = html_entity_decode($variant_data['post_title']);
                    }

#quantity
                    if (@$variants_detail['_stock_status'][0] == 'instock') {
                        $variant['quantity'] = @$variants_detail['_stock'][0];
                    }


// Price with Tax
                    if (get_option('ps_price') == 'on') {
                        if (isset($variants_detail['_tax_status'][0]) && $variants_detail['_tax_status'][0] == 'taxable') { # Product with TAX 
                            $taxname = empty($variants_detail['_tax_class'][0]) ? 'standard-tax' : $variants_detail['_tax_class'][0];
                            $response_taxes = linksyn_get_tax_details_syncall($taxname);
                            if ($response_taxes['result'] == 'success') {
                                //$variant['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                                //$variant['tax_rate'] = $response_taxes['data']['tax_rate'];
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
                                        //$variant['tax_value'] = $tax_value;
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
                                        //$variant['tax_value'] = $tax_value;
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
                                    //$variant['tax_value'] = $tax_value;
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
                                    //$variant['tax_value'] = $tax_value;
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
                    } else {
                        $variant['outlets'] = array(array('quantity' => NULL));
                    }
                    $product['variants'][] = $variant;
                    $total_var_product++;
                }
            }
            if (isset($product['variants']) && !empty($product['variants'])) {
                $count_variant = count($product['variants']);
            }
            $data = json_encode($product);
            $response = $apicall->linksync_postProduct($data);
        }
    } else {
        $product_details = $wpdb->get_results("SELECT MIN(ID) AS first_post_id,MAX(ID) AS last_post_id ,COUNT(*) AS total_post_id FROM `" . $wpdb->posts . "` WHERE post_type = 'product' AND post_status!='auto-draft'", ARRAY_A);
        if (0 != $wpdb->num_rows) {
            $product_wc = $product_details[0];
            update_option('post_product', $product_wc['total_post_id']);
            echo json_encode($product_wc);
            exit;
        }
    }
//    $delay=($count_variant)?$count_variant:'1';
//    $total_delay=$delay*3;
    sleep(3);
    echo isset($count_variant) ? $count_variant : '1';
    exit;
    // $response=array('request_time'=> isset($count_variant)?$count_variant:'1');
    //echo json_encode($response);
    //echo json_encode(array('product' => $product_wc, 'apiresponse' => isset($response) ? $response : '','request_time'=>  isset($count_variant)?$count_variant:'1'));
}

// Helper functions  
function linksyn_get_tax_details_syncall($taxname) {
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

function linksync_removespaces_sku($sku) {
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

?>