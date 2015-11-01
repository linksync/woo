<?php

require(dirname(__FILE__) . '../../../../wp-load.php');
@mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
@mysql_select_db(DB_NAME);
@mysql_set_charset('utf8');
include_once(dirname(__FILE__) . '/classes/Class.linksync.php'); # Class file having API Call functions 
global $wp;
// Initializing 
$wp->init();
$wp->parse_request();
$wp->query_posts();
$wp->register_globals();
$product_sync_type = get_option('product_sync_type_QBO');
if ($_POST['communication_key'] != get_option('webhook_url_code')) {
    die('Access is Denied');
}

function logs_detail() {
    $total_post = get_option('post_product');
    $LAIDKey = get_option('linksync_laid');
    update_option('linksync_sycning_status', 'completed');
    linksync_class::add('Product Sync Woo to Vend', 'success', $total_post . ' Product synced', $LAIDKey);
}

if (isset($_POST['get_total'])) {
    echo logs_detail();
    exit;
}
if (isset($product_sync_type) && $product_sync_type == 'wc_to_QB' || $product_sync_type == 'two_way') {
    update_option('linksync_sycning_status', 'running');
    set_time_limit(0);
    $testMode = get_option('linksync_test');
    $LAIDKey = get_option('linksync_laid');
    $apicall = new linksync_class($LAIDKey, $testMode);
    global $wpdb;
    if (isset($_POST['offset']) && !empty($_POST['offset'])) {
        //sleep(10);
        $offset = ($_POST['offset'] - 1); // start from 0
        $query_with_limit = "SELECT ID,post_status,post_title,post_content,post_type FROM `" . $wpdb->prefix . "posts`  WHERE post_type = 'product' AND post_status!='auto-draft' ORDER BY ID ASC LIMIT " . $offset . ",1";
        $product_details = mysql_query($query_with_limit) or die(mysql_error());
        $product_wc = mysql_fetch_assoc($product_details);
        if ($product_wc['post_status'] != 'trash') {
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
                    $response_taxes = linksyn_get_tax_details_syncall_QBO($taxname);
                    if ($response_taxes['result'] == 'success') {
                        $product['tax_name'] = html_entity_decode($response_taxes['data']['tax_name']);
                        $product['tax_rate'] = $response_taxes['data']['tax_rate'];
                        $taxsetup = true;
                    }
                }
                if (get_option('excluding_tax') == 'on') {
                    if ($taxsetup) {
                        if (get_option('price_field') == 'regular_price') {
                            if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
//cost price:_regular_price
                                $regular_price = (float) $post_detail['_regular_price'][0];
                                $tax_rate = (float) $product['tax_rate'];
                                $tax_value = ($regular_price * $tax_rate);
//sell price:_regular_price
                                $price = $post_detail['_regular_price'][0] + $tax_value;
                                $product['sell_price'] = str_replace(',', '.', $price);

                                $product['list_price'] = str_replace(',', '.', $price);
                                $product['tax_value'] = $tax_value;
                            }
                        } else {
                            if (isset($post_detail['_sale_price'][0]) && !empty($post_detail['_sale_price'][0])) {
                                $regular_price = (float) $post_detail['_sale_price'][0];
                                $tax_rate = (float) $product['tax_rate'];
                                $tax_value = ($regular_price * $tax_rate);
                                $price = $post_detail['_sale_price'][0] + $tax_value;
                                $product['sell_price'] = str_replace(',', '.', $price);
                                $product['list_price'] = str_replace(',', '.', $price);
                                $product['tax_value'] = $tax_value;
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
// No effect on price 
                    if (get_option('price_field') == 'regular_price') {
                        if (isset($post_detail['_regular_price'][0]) && !empty($post_detail['_regular_price'][0])) {
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

#Name/Title Check
            if (get_option('ps_name_title') == 'on')
                $product['name'] = html_entity_decode($product_wc['post_title']);

            $product['includes_tax'] = (isset($post_detail['_tax_status'][0]) && $post_detail['_tax_status'][0] == 'taxable') ? true : false;
            /*
             * Quantity 
             */
            if (get_option('ps_quantity') == 'on') {
                if (isset($post_detail['_stock_status'][0]) && $post_detail['_stock_status'][0] == 'instock') {
                    $product['quantity'] = isset($post_detail['_stock'][0]) ? $post_detail['_stock'][0] : 0;
                }
            } else {
                $product['quantity'] = array(array('quantity' => NULL));
            }
            #brands
            if (get_option('ps_brand') == 'on') {
//To get the Detail of the Tags and Category of the product using product id(Post ID) 
                $brands_query = "SELECT " . $wpdb->prefix . "terms.name FROM `" . $wpdb->prefix . "term_taxonomy` JOIN " . $wpdb->prefix . "terms ON(" . $wpdb->prefix . "terms.term_id=" . $wpdb->prefix . "term_taxonomy.term_id)  JOIN " . $wpdb->prefix . "term_relationships ON(" . $wpdb->prefix . "term_relationships.term_taxonomy_id=" . $wpdb->prefix . "term_taxonomy.term_taxonomy_id) WHERE " . $wpdb->prefix . "term_taxonomy.`taxonomy`='product_brand' AND " . $wpdb->prefix . "term_relationships.object_id='" . $product_wc['ID'] . "'";
                $result_brands = mysql_query($brands_query) or die(mysql_error());

                if (mysql_num_rows($result_brands) != 0) {
                    while ($row_brands = mysql_fetch_assoc($result_brands)) {
                        $brands[] = array(
                            'name' => html_entity_decode($row_brands['name']));
                    }
                }
                if (!empty($brands)) {
                    $product['brands'] = $brands;
                }
//To free an array to use futher
                unset($brands);
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
        }
    } else {
        $product_details = mysql_query("SELECT MIN(ID) AS first_post_id,MAX(ID) AS last_post_id ,COUNT(*) AS total_post_id FROM `" . $wpdb->prefix . "posts` WHERE post_type = 'product' AND post_status!='auto-draft'") or die("Error in second query" . mysql_error());
        if (mysql_num_rows($product_details) != 0) {
            $product_wc = mysql_fetch_assoc($product_details);
            update_option('post_product', $product_wc['total_post_id']);
            echo json_encode($product_wc);
            exit;
        }
    }
    exit;
}

// Helper functions  
function linksyn_get_tax_details_syncall_QBO($taxname) {
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