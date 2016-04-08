<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

$product_type = get_option('product_sync_type_QBO');
if (get_option('linksync_status') == 'Active') {
    if ($product_type == 'two_way' || $product_type == 'wc_to_QB') {
        //check the post type (Product) 
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'product') {
            global $wpdb;
            $taxsetup = false;
            $testMode = get_option('linksync_test');
            $LAIDKey = get_option('linksync_laid');
            $apicall = new linksync_class($LAIDKey, $testMode);
            if ($apicall->lastresponse['result'] == 'success') {
                if ($_POST['post_status'] != 'trash') {
                    if (@empty($_POST['_sku'])) {
                        $_POST['_sku'] = 'sku_' . $_POST['post_ID'];
                        update_post_meta($_POST['post_ID'], '_sku', 'sku_' . $_POST['post_ID']);
                    }
                    $product['sku'] = html_entity_decode($_POST['_sku']); //SKU(unique Key/Numbers) 
//check for the product status ->publish or draft 

                    $product['active'] = (isset($_POST['post_status']) && $_POST['post_status'] == 'draft') ? 0 : 1;
#prices
//if the Product is update  

                    if ($_POST['action'] == 'editpost') {# only for POST ( Product type) 
                        if ($_POST['original_post_status'] == 'publish' || $_POST['original_post_status'] == 'pending') { # Product being updated  
                            $product['name'] = (@get_option('ps_name_title') == 'on') ? html_entity_decode($_POST['post_title']) : null; ## Name/Title 
                            // Price with Tax
                            if (get_option('ps_price') == 'on') {
                                $tax_status = get_post_meta($_POST['post_ID'], '_tax_status', TRUE);
                                if (isset($tax_status) && $tax_status == 'taxable') {  # Product with TAX  
                                    $tax_class = get_post_meta($_POST['post_ID'], '_tax_class', TRUE);
                                    $taxname = empty($tax_class) ? 'standard-tax' : $tax_class;
                                    if (isset($taxname) && !empty($taxname)) {
                                        $response_taxes = linksyn_tax_QBO($taxname);
                                        if ($response_taxes['result'] == 'success') {
                                            $product['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                                            $product['tax_rate'] = $response_taxes['data']['tax_rate'];
                                            $taxsetup = true;
                                        }
                                    }
                                }
                                if (get_option('excluding_tax') == 'on') {  //ex ticked
                                    // No effect on price 
                                    # https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                                    if ($taxsetup) {
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
                                                //cost price:_regular_price
                                                $regular_price = (float) $_POST['_regular_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) $product['tax_rate'];
                                                $tax_value = ($regular_price * $tax_rate);
                                                //sell price:_regular_price
                                                $price = $_POST['_regular_price'] + $tax_value;
                                                $product['sell_price'] = str_replace(',', '.', $price);
                                                $product['list_price'] = str_replace(',', '.', $price);
                                                $product['tax_value'] = $tax_value;
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {
                                                $sale_price = (float) $_POST['_sale_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) $product['tax_rate'];
                                                $tax_value = ($sale_price * $tax_rate);
                                                $price = $_POST['_sale_price'] + $tax_value;
                                                $product['sell_price'] = str_replace(',', '.', $price);
                                                $product['list_price'] = str_replace(',', '.', $price);
                                                $product['tax_value'] = $tax_value;
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
                                    if (get_option('price_field') == 'regular_price') {
                                        if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
                                            //cost price:_regular_price 
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
                            $product['includes_tax'] = (isset($_POST['_tax_status']) && $_POST['_tax_status'] == 'taxable') ? true : false;

                            unset($regular_price);
                            unset($tax_rate);
                            unset($tax_value);
                            /*
                             * Quantity 
                             */
                            if (get_option('ps_quantity') == 'on') {
                                if (isset($_POST['_manage_stock']) && $_POST['_manage_stock'] == 'yes') {
                                    if (isset($_POST['_stock']) && !empty($_POST['_stock'])) {
                                        $product['quantity'] = $_POST['_stock'];
                                    } else {
                                        $product['quantity'] = NULL;
                                    }
                                }
                            }
                            #-------------Brand-------------#

                            if (get_option('ps_brand') == 'on') {
                                if (isset($_POST['tax_input']['product_brand']) && !empty($_POST['tax_input']['product_brand'])) {
                                    if (isset($_POST['tax_input']['product_brand'][1]) && !empty($_POST['tax_input']['product_brand'][1])) {
                                        $brand_id = $_POST['tax_input']['product_brand'][1];
                                        global $wpdb;
                                        $sql = "SELECT * FROM " . $wpdb->base_prefix . "terms WHERE term_id=" . $brand_id;
                                        $query = $wpdb->get_results($sql, ARRAY_A );

                                        if (0 != $wpdb->num_rows) {
                
                                            foreach ($query  as $brand_details) {
                                                $product['brands'][] = array('name' => html_entity_decode($brand_details['name']));
                                            }
                    
                                        }
                                    }
                                }
                            }
                            /*
                             * Asset Account ID
                             * Income Account ID 
                             * Expense Account ID
                             */
                            $accounts_asset = get_option('ps_account_asset');
                            if (isset($accounts_asset) && !empty($accounts_asset)) {
                                $product['asset_account_id'] = $accounts_asset;
                            } else {
                                $product['asset_account_id'] = null;
                            }
                            $accounts_expense = get_option('ps_account_expense');
                            if (isset($accounts_expense) && !empty($accounts_expense)) {
                                $product['expense_account_id'] = $accounts_expense;
                            } else {
                                $product['expense_account_id'] = null;
                            }
                            $accounts_revenue = get_option('ps_account_revenue');
                            if (isset($accounts_revenue) && !empty($accounts_revenue)) {
                                $product['income_account_id'] = $accounts_revenue;
                            } else {
                                $product['income_account_id'] = null;
                            }
                            $data = json_encode($product);
                            $response = $apicall->linksync_postProduct($data);
                            LSC_Log::add('Product Sync Woo to QBO', 'success', 'Product synced SKU:' . $product['sku'], $LAIDKey);
                        } else if ($_POST['original_post_status'] == 'auto-draft' || $_POST['original_post_status'] == 'draft') {

//if the product is publish new
// Title, Description, qty, price etc, even if these options are not enabled on the Admin UI.

                            $product['name'] = (isset($_POST['post_title']) && !empty($_POST['post_title'])) ? html_entity_decode($_POST['post_title']) : null;
                            if (get_option('ps_price') == 'on') {
//                                 Tax && Price  
                                if (isset($_POST['_tax_status']) && $_POST['_tax_status'] == 'taxable') { # Product with TAX  
                                    $taxname = empty($_POST['_tax_class']) ? 'standard-tax' : $_POST['_tax_class'];
                                    $response_taxes = linksyn_tax_QBO($taxname);
                                    if ($response_taxes['result'] == 'success') {
                                        $product['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                                        $product['tax_rate'] = $response_taxes['data']['tax_rate'];
                                        $taxsetup = true;
                                    }
                                }
                                if (get_option('excluding_tax') == 'on') {
                                    # https://www.evernote.com/shard/s144/sh/e63f527b-903f-4002-8f00-313ff0652290/d9c1e0ce5a95800a
                                    if ($taxsetup) {
                                        if (get_option('price_field') == 'regular_price') {
                                            if (isset($_POST['_regular_price']) && !empty($_POST['_regular_price'])) {
//cost price:_regular_price
                                                $regular_price = (float) $_POST['_regular_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) $product['tax_rate'];
                                                $tax_value = ($regular_price * $tax_rate);


//sell price:_regular_price
                                                $price = $_POST['_regular_price'] + $tax_value;
                                                $product['sell_price'] = str_replace(',', '.', $price);

                                                $product['list_price'] = str_replace(',', '.', $price);
                                                $product['tax_value'] = $tax_value;
                                            }
                                        } else {
                                            if (isset($_POST['_sale_price']) && !empty($_POST['_sale_price'])) {

                                                $regular_price = (float) $_POST['_sale_price'];
//                                                 Get Tax_value
                                                $tax_rate = (float) $product['tax_rate'];
                                                $tax_value = ($regular_price * $tax_rate);
                                                $price = $_POST['_sale_price'] + $tax_value;
                                                $product['sell_price'] = str_replace(',', '.', $price);

                                                $product['list_price'] = str_replace(',', '.', $price);
                                                $product['tax_value'] = $tax_value;
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
                            }
#qunantity 
                            if (get_option('ps_quantity') == 'on') {
                                if (isset($_POST['_manage_stock']) && $_POST['_manage_stock'] == 'yes') {
                                    if (isset($_POST['_stock']) && !empty($_POST['_stock'])) {
                                        $product['quantity'] = $_POST['_stock'];
                                    } else {
                                        $product['quantity'] = NULL;
                                    }
                                }
                            }
                            if (get_option('ps_brand') == 'on') {
                                if (isset($_POST['tax_input']['product_brand']) && !empty($_POST['tax_input']['product_brand'])) {
                                    if (isset($_POST['tax_input']['product_brand'][1]) && !empty($_POST['tax_input']['product_brand'][1])) {
                                        $brand_id = $_POST['tax_input']['product_brand'][1];
                                        global $wpdb;
                                        $sql = "SELECT * FROM " . $wpdb->base_prefix . "terms WHERE term_id=" . $brand_id;
                                        $query = $wpdb->get_results($sql, ARRAY_A);

                                        if (0 != $wpdb->num_rows) {
                                            $brand_details = mysql_fetch_assoc($query);
                                            foreach ($query as $brand_details) {
                                                 $product['brands'][] = array('name' => html_entity_decode($brand_details['name']));
                                            }
        
                                        }
                                    }
                                }
                            }
                            /*
                             * Asset Account ID
                             * Income Account ID 
                             * Expense Account ID
                             */
                            $accounts_asset = get_option('ps_account_asset');
                            if (isset($accounts_asset) && !empty($accounts_asset)) {
                                $product['asset_account_id'] = $accounts_asset;
                            } else {
                                $product['asset_account_id'] = null;
                            }
                            $accounts_expense = get_option('ps_account_expense');
                            if (isset($accounts_expense) && !empty($accounts_expense)) {
                                $product['expense_account_id'] = $accounts_expense;
                            } else {
                                $product['expense_account_id'] = null;
                            }
                            $accounts_revenue = get_option('ps_account_revenue');
                            if (isset($accounts_revenue) && !empty($accounts_revenue)) {
                                $product['income_account_id'] = $accounts_revenue;
                            } else {
                                $product['income_account_id'] = null;
                            }

//include_tax
                            $product['includes_tax'] = (isset($_POST['_tax_status']) && $_POST['_tax_status'] == 'taxable') ? true : false;
                            $data = json_encode($product);
                            $response = $apicall->linksync_postProduct($data);
                            LSC_Log::add('Product Sync Woo to QBO', 'success', 'Product synced SKU:' . $product['sku'], $LAIDKey);
                        }
                    }
                }
            } else {
                die('Error in Configuration ' . $apicall['lastresponse']['message']);
            }
        }
    }
}// Helper functions  

function linksyn_tax_QBO($taxname) {
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

?>