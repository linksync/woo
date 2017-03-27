<?php
$apicall = new linksync_class($LAIDKey, $testMode);
if (isset($_POST['save_product_sync_setting'])) {
    if (isset($_POST['product_sync_type']) && !empty($_POST['product_sync_type'])) {
        update_option('product_sync_type', $_POST['product_sync_type']);
        if ($_POST['product_sync_type'] == 'vend_to_wc-way') {
            if (isset($_POST['ps_quantity'])) {
                if ($_POST['ps_quantity'] == 'on') {
                    if (empty($_POST['outlet'])) {
                        $message['result'] = 'error';
                        $message['message'] = 'You didn\'t select any outlet !';
                    } else {
                        $message['result'] = 'success';
                    }
                } else {
                    $message['result'] = 'success';
                }
            } else {
                $message['result'] = 'success';
            }
        } elseif ($_POST['product_sync_type'] == 'wc_to_vend' || $_POST['product_sync_type'] == 'two_way') {
            $message['result'] = 'success';
        } else {
            $message['result'] = 'success';
        }
    }

    if (isset($message['result']) && $message['result'] == 'success') {
        update_option('prod_update_suc', NULL);
        update_option('prod_last_page', NULL);
        update_option('product_detail', NULL);
        if (isset($_POST['ps_name_title'])) {
            if ($_POST['ps_name_title'] == 'on') {
                update_option('ps_name_title', $_POST['ps_name_title']);
            } else {
                update_option('ps_name_title', 'off');
            }
        } else {
            update_option('ps_name_title', 'off');
        }
        if (isset($_POST['ps_description'])) {
            if ($_POST['ps_description'] == 'on') {
                update_option('ps_description', $_POST['ps_description']);
            } else {
                update_option('ps_description', 'off');
            }
        } else {
            update_option('ps_description', 'off');
        }
        if (isset($_POST['ps_desc_copy'])) {
            if ($_POST['ps_desc_copy'] == 'on') {
                update_option('ps_desc_copy', 'on');
            } else {
                update_option('ps_desc_copy', 'off');
            }
        } else {
            update_option('ps_desc_copy', 'off');
        }
        //Pending
        if (isset($_POST['ps_pending'])) {
            if ($_POST['ps_pending'] == 'on') {
                update_option('ps_pending', $_POST['ps_pending']);
            } else {
                update_option('ps_pending', 'off');
            }
        } else {
            update_option('ps_pending', 'off');
        }
        if ($_POST['product_sync_type'] == 'vend_to_wc-way') {
            if (isset($_POST['ps_attribute'])) {
                if ($_POST['ps_attribute'] == 'on') {
                    update_option('ps_attribute', $_POST['ps_attribute']);
                } else {
                    update_option('ps_attribute', 'off');
                }
            } else {
                update_option('ps_attribute', 'off');
            }
            if (isset($_POST['linksync_visiable_attr'])) {
                if ($_POST['linksync_visiable_attr'] == '1') {
                    update_option('linksync_visiable_attr', $_POST['linksync_visiable_attr']);
                } else {
                    update_option('linksync_visiable_attr', '0');
                }
            } else {
                update_option('linksync_visiable_attr', '0');
            }
        } else {
            update_option('linksync_visiable_attr', '0');
            update_option('ps_attribute', 'on');
        }
        // Price 
        if (isset($_POST['ps_price'])) {
            if ($_POST['ps_price'] == 'on') {
                update_option('ps_price', $_POST['ps_price']);
                if (isset($_POST['linksync_woocommerce_tax_option'])) {
                    if ($_POST['linksync_woocommerce_tax_option'] == 'on') {
                        update_option('linksync_woocommerce_tax_option', $_POST['linksync_woocommerce_tax_option']);
                    } else {
                        update_option('linksync_woocommerce_tax_option', 'off');
                    }
                } else {
                    update_option('linksync_woocommerce_tax_option', 'off');
                }
                if (isset($_POST['excluding_tax'])) {
                    if ($_POST['excluding_tax'] == 'on') {
                        update_option('excluding_tax', 'on');
                    } else {
                        update_option('excluding_tax', 'off');
                    }
                } else {
                    update_option('excluding_tax', 'off');
                }
                if (isset($_POST['price_field'])) {
                    update_option('price_field', $_POST['price_field']);
                }
                if (isset($_POST['tax_class']) && !empty($_POST['tax_class'])) {
                    if (array_filter($_POST['tax_class'])) {
                        $all_taxes = implode(',', $_POST['tax_class']);
                        update_option('tax_class', $all_taxes);
                    } else {
                        update_option('tax_class', '');
                    }
                } else {
                    update_option('tax_class', '');
                }
# Hiddne on clint demand
//            if (isset($_POST['price_book'])) {
//                if ($_POST['price_book'] == 'on') {
//                    update_option('price_book', 'on');
//                    if ($_POST['product_sync_type'] == 'vend_to_wc-way') {
//                        if (isset($_POST['price_book_identifier']) && !empty($_POST['price_book_identifier'])) {
//                            update_option('price_book_identifier', trim($_POST['price_book_identifier']));
//                        } else {
//                            $result = array('result' => 'error', 'message' => 'Price Book Identfier is required');
//                        }
//                    }
//                } else {
//                    update_option('price_book', 'off');
//                }
//            } else {
//                update_option('price_book', 'off');
//            }
            } else {
                update_option('ps_price', 'off');
                update_option('excluding_tax', 'off');
                update_option('tax_mapping', 'off');
                //  update_option('price_book', 'off');
            }
        } else {
            update_option('ps_price', 'off');
            update_option('excluding_tax', 'off');
            update_option('tax_mapping', 'off');
            // update_option('price_book', 'off');
            update_option('tax_class', 'off');
        }
        // Quantity
        if (isset($_POST['ps_quantity'])) {
            if ($_POST['ps_quantity'] == 'on') {
                update_option('ps_quantity', 'on');
                if (!empty($_POST['outlet'])) {
                    $oulets = implode('|', $_POST['outlet']);
                    update_option('ps_outlet', 'on');
                    update_option('ps_outlet_details', $oulets);
                } else {
                    update_option('ps_outlet', 'off');
                    update_option('ps_outlet_details', 'off');
                }

                if (isset($_POST['ps_unpublish'])) {
                    if ($_POST['ps_unpublish'] == 'on') {
                        update_option('ps_unpublish', 'on');
                    } else {
                        update_option('ps_unpublish', 'off');
                    }
                } else {
                    update_option('ps_unpublish', 'off');
                }
            } else {
                update_option('ps_quantity', 'off');
                update_option('ps_unpublish', 'off');
            }
        } else {
            update_option('ps_quantity', 'off');
            update_option('ps_unpublish', 'off');
        }

        if (@$_POST['ps_brand'] == 'on') {
            update_option('ps_brand', 'on');
        } else {
            update_option('ps_brand', 'off');
        }

        if (isset($_POST['ps_tags'])) {
            if ($_POST['ps_tags'] == 'on') {
                update_option('ps_tags', 'on');
            } else {
                update_option('ps_tags', 'off');
            }
        } else {
            update_option('ps_tags', 'off');
        }
        if (isset($_POST['ps_categories']) && !empty($_POST['ps_categories'])) {
            if (isset($_POST['cat_radio'])) {
                update_option('cat_radio', $_POST['cat_radio']);
            }
            update_option('ps_categories', 'on');
        } else {
            update_option('ps_categories', 'off');
        }

        if (isset($_POST['wc_to_vend_outlet_detail'])) {
            update_option('wc_to_vend_outlet_detail', $_POST['wc_to_vend_outlet_detail']);
            update_option('ps_wc_to_vend_outlet', 'on');
        }



        if (isset($_POST['ps_imp_by_tag'])) {
            if ($_POST['ps_imp_by_tag'] == 'on') {
                if (isset($_POST['import_by_tags_list']) && !empty($_POST['import_by_tags_list'])) {
					$selected_tags = array();
					foreach( $_POST['import_by_tags_list'] as $key => $selected_tab ){
						$selected_tags[] = remove_escaping_str($selected_tab);
					}
					$tags = implode( '|', $selected_tags );

                    $import_by_tags_list_serialize = serialize($tags);
                    update_option('import_by_tags_list', $import_by_tags_list_serialize);
                    update_option('ps_imp_by_tag', 'on');
                } else {
                    update_option('import_by_tags_list', '');
                    update_option('ps_imp_by_tag', 'off');
                }
            } else {
                update_option('ps_imp_by_tag', 'off');

                update_option('import_by_tags_list', '');
            }
        } else {
            update_option('ps_imp_by_tag', 'off');

            update_option('import_by_tags_list', '');
        }
        if (isset($_POST['ps_images']) && !empty($_POST['ps_images'])) {
            if (isset($_POST['ps_import_image_radio'])) {
                update_option('ps_import_image_radio', $_POST['ps_import_image_radio']);
            }
            update_option('ps_images', 'on');
        } else {
            update_option('ps_images', 'off');
        }
        if (isset($_POST['ps_create_new'])) {
            if ($_POST['ps_create_new'] == 'on') {
                update_option('ps_create_new', 'on');
            } else {
                update_option('ps_create_new', 'off');
            }
        } else {
            update_option('ps_create_new', 'off');
        }
        if (isset($_POST['ps_delete'])) {
            if ($_POST['ps_delete'] == 'on') {
                update_option('ps_delete', 'on');
            } else {
                update_option('ps_delete', 'off');
            }
        } else {
            update_option('ps_delete', 'off');
        }
        if ($_POST['product_sync_type'] == 'two_way') {
            ?>     <div id="pop_up_two-way" class="clientssummarybox" style=" width:600px !important; top: 24% !important; display: none;  z-index: 9999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 25%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
                <a name="lnkViews" href="javascript:;"><img style="height: 13px;float: right;"src="../wp-content/plugins/linksync/assets/images/linksync/cross_icon.png"></a><center><div id="showMessage"><center><h4>Your changes will require a full re-sync of product data.</h4></center>
                        <center><h4>Do you want to re-sync now?</h4></center></div></center> 
                <center><h4 style="display:none;" id="syncing_loader_all"><img src="../wp-content/plugins/linksync/assets/images/linksync/ajax-loader.gif"></h4></center> 
                <center><h4 id="sync_start"></h4><h4 id="sync_start_export_product"></h4></center>
                <center><p id="show-result_all"></p></center>
                <div id="pop_button"><input type="button" title="This option will update product in your WooCommerce store with Product data from Vend. " style="color: green; margin-left: 97px; width: 138px; font-weight: 900;float: left;margin-bottom: 12px;"  class="button"   onclick="return re_sync_process_start2();"  value="Product from Vend"> 
                    <input type="button" title="This option will update product in your Vend store with the Product data from WooCommerce." style="color: green; margin-left: 130px; width: 130px; font-weight: 900;float: left;"  class="button"   onclick="return sync_process_start();"  value="Product to Vend">
                </div> </div> 
            <script>
                jQuery(window).load(function() {
                    jQuery('#pop_up_two-way').fadeIn(500);
                });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
            </script><?php
        }
        if ($_POST['product_sync_type'] == 'vend_to_wc-way') {
            ?>     <div id="pop_up" class="clientssummarybox" style=" width:600px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 25%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
                <a name="lnkViews" href="javascript:;"><img id="syncing_close" style="display: none;height: 13px;float: right;"src="../wp-content/plugins/linksync/assets/images/linksync/cross_icon.png"></a>
                <center><div id="showMessage"><center><h4>Your changes will require a full re-sync of product data from Vend.</h4></center>
                        <center><h4>Do you want to re-sync now?</h4></center></div></center> 
                <center><h4 style="display:none;" id="syncing_loader"><img src="../wp-content/plugins/linksync/assets/images/linksync/ajax-loader.gif"></h4></center> 
                <center><h4 id="sync_start"></h4></center> 
                <div id="pop_button"><input type="button" style="color: green; margin-left: 168px; width: 90px; font-weight: 900;float: left;"  class="button"   onclick="return re_sync_process_start2();"  value="Yes"> 
                    <input  type="button" class="button" style="color: red;
                            margin-left: 83px;
                            width: 90px;font-weight: 900;"  name="close"   value='No'/></div> </div> 
            <script>
                jQuery(window).load(function() {
                    jQuery('#pop_up').fadeIn(500);
                });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
            </script><?php
        }
        if ($_POST['product_sync_type'] != 'disabled_sync') {
            if ($_POST['product_sync_type'] == 'vend_to_wc-way') {
                $enable = 'Vend to Woo';
            } elseif ($_POST['product_sync_type'] == 'two_way') {
                $enable = 'Two way';
            } else {
                $enable = 'Woo to Vend';
            }
            $setting_message = $enable . '  enable';
        } else {
            $setting_message = 'Sync Setting Disabled';
        }
        LSC_Log::add('Product Sync Setting', 'success', $setting_message, $LAIDKey);
    }
    update_option('image_process', 'complete');
    if (is_vend()) {
        LS_Vend()->updateWebhookConnection();
    }
} elseif (isset($_POST['sync_reset_btn'])) {
    update_option('prod_update_suc', NULL);
    update_option('prod_last_page', NULL);
    update_option('product_detail', NULL);
    update_option('image_process', 'complete');
    ?> 
    <div id="pop_up" class="clientssummarybox" style=" width:600px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 25%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
        <center><div id="showMessage"><center><h4>Your changes will require a full re-sync of product data from Vend.</h4></center>
                <center><h4>Do you want to re-sync now?</h4></center></div>  </center>  
        <center><h4 style="display:none;" id="syncing_loader1"><img src="../wp-content/plugins/linksync/assets/images/linksync/ajax-loader.gif"></h4></center> 
        <center><h4 id="sync_start"></h4></center>
        <center><div id="total_product"></div></center>
        <center><p id="show-result"></p></center>
        <center><h4 id="sync_start_export1"></h4></center>
        <div id="pop_button"><input type="button" style="color: green; margin-left: 168px; width: 90px; font-weight: 900;float: left;"  class="button"   onclick="return re_sync_process_start2();"  value="Yes"> 
            <input  type="button" class="button" style="color: red;  margin-left: 83px;  width: 90px;font-weight: 900;"  name="no"   value='No'/></div></div>  <script>
                jQuery(window).load(function() { 
                    jQuery('#pop_up').fadeIn(500);
                });
    </script><?php
}
?>    <h3>Product Syncing Configuration</h3>
<?php
if (isset($message) && !empty($message)) {
    if ($message['result'] == 'error') {
        ?><script>
            jQuery('#response').removeClass('updated').addClass('error').html("<?php echo $message['message']; ?>").fadeIn(500).delay(3000).fadeOut(4000);

        </script><?php
    }
}
?>
<form method="post" name="options">
    <fieldset>
        <legend>Product Syncing Type</legend> 
        <p>
            <?php 
                $product_sync_type = get_option('product_sync_type');
            ?>
            <input id="ls-product-twoway" <?php echo ($product_sync_type == 'two_way' ? 'checked' : ''); ?> type="radio" name="product_sync_type"  value="two_way"> 
                <label for="ls-product-twoway">Two-way</label>
                <a href="https://www.linksync.com/help/woocommerce">
                    <img style="margin-bottom:-4px;" title="Data is kept in sync between both systems, so changes to products and inventory can be made in either your WooCommerce or Vend store and those changes will be synced to the other store within a few moments."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16">
                </a>&nbsp;&nbsp;&nbsp;&nbsp;

            <input type="radio" id="ls-product-vendtowoo" <?php echo ($product_sync_type == 'vend_to_wc-way' ? 'checked' : ''); ?> name="product_sync_type" value="vend_to_wc-way"> 
                <label for="ls-product-vendtowoo">Vend to WooCommerce </label>
                <a  href="https://www.linksync.com/help/woocommerce">
                    <img style="margin-bottom:-4px;" title="Vend is the 'master' when it comes to managing product and inventory, and product updates are one-way, from Vend to WooCommerce - product and inventory data does not update back to Vend from WooCommerce. You must enable Order Syncing from WooCommerce to Vend for this option to work correctly. " src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16">
                </a>&nbsp;&nbsp;&nbsp;&nbsp; 

            <input type="radio" id="ls-product-wootovend" <?php echo ($product_sync_type == 'wc_to_vend' ? 'checked' : ''); ?> name="product_sync_type" value="wc_to_vend"> 
                <label for="ls-product-wootovend">WooCommerce to Vend </label>
                <a  href="https://www.linksync.com/help/woocommerce">
                    <img style="margin-bottom:-4px;" title="WooCommerce is the 'master' when it comes to managing product and inventory, and product updates are one-way, from WooCommerce to Vend - product and inventory data does not update back to WooCommerce to Vend. You must enable Order Syncing from Vend to WooCommerce for this option to work correctly. " src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16">
                </a>&nbsp;&nbsp; &nbsp;&nbsp;  

            <input type="radio" id="disabled_sync_id" <?php echo ($product_sync_type == 'disabled_sync' ? 'checked' : ''); ?> name="product_sync_type" value="disabled_sync" >
                <label for="disabled_sync_id">Disabled</label>
                <a  href="https://www.linksync.com/help/woocommerce">
                    <img style="margin-bottom:-4px;" title="Prevent any product syncing from taking place between your Vend and WooCommerce stores. " src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16">
                </a>
        </p>

    </fieldset>
    <div style="display:<?php
if ($product_sync_type == 'disabled_sync') {
    echo "none";
}
?>"  id="product_sync_settig">

        <p>    <input type="submit" name="sync_reset_btn" title="Selecting the Sync Reset button resets linksync to update all WooCommerce products with data from Vend, based on your existing Product Sync Settings."  value="Sync Reset" id="sync_reset_btn_id" class="button button-primary" style="display:<?php
         if ($product_sync_type == 'wc_to_vend') {
             echo "none";
         }
?>" name="sync_reset"/> 
            <input id="sync_reset_all_btn_id" type="button" title="Selecting this option will sync your entire WooCommerce product catalogue to Vend, based on your existing Product Sync Settings. It takes 3-5 seconds to sync each product, depending on the performance of your server, and your geographic location."  onclick="show_confirm_box();" value="Sync all product to Vend" style="display:<?php
                      if ($product_sync_type == 'vend_to_wc-way') {
                          echo "none";
                      }
?>" class="button button-primary" />
        </p> 
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th  class="titledesc">Name/Title  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, Product titles will be kept in sync. In WooCommerce this is the product Name and in Vend it's the Product name. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </th>
                    <td class="forminp forminp-checkbox">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_name_title') == 'on' ? 'checked' : ''); ?> value="on" name="ps_name_title" /> Sync the product titles between apps</label>
                    </td>
                </tr>
                <tr  valign="top">
                    <th  class="titledesc">Description<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, product descriptions will be kept in sync. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>
                    <td class="forminp">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_description') == 'on' ? 'checked' : ''); ?> value="on" name="ps_description" /> Sync the product description between apps</label>
                    </td>
                </tr> 
                <tr  valign="top" id="short_description" style="display:<?php
                   if ($product_sync_type == 'wc_to_vend') {
                       echo "none";
                   }
?>">
                    <th  class="titledesc">Short Description  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, the product description from Vend will be applied to the Product Short Description in WooCommerce."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>
                    <td class="forminp">

                        <input type="checkbox" <?php echo (get_option('ps_desc_copy') == 'on' ? 'checked' : ''); ?> value="on"  name="ps_desc_copy" />Copy full description from Vend to short description in WooCommerce
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" class="titledesc">Price<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, prices will be kept in sync. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </th>
                    <td class="forminp forminp-checkbox">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_price') == 'on' ? 'checked' : ''); ?> value="on" name="ps_price" />Sync prices between apps</label>
                        <br><br> <div style="margin-left: 40px;" ><span  class="ps_price_sub_options"> 
                                <b>WooCommerce price field to sync</b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select which WooCommerce price field you want to sync with Vend sell price."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a>
                                <br>
                                <div  style="margin-left: 23px;margin-top: 10px;">
                                    <input type="radio" name="price_field" <?php echo (get_option('price_field') == 'regular_price' ? 'checked' : ''); ?> value="regular_price">Regular Price
                                    <input style="margin-left:20px;" type="radio" <?php echo (get_option('price_field') == 'sale_price' ? 'checked' : ''); ?> name="price_field" value="sale_price">Sale Price<br><br> 
                                </div> 
                                <br>
                                <?php
                                if (get_option('woocommerce_calc_taxes') == 'yes') {
                                    ?>
                                    <label style="display: inline-block;width: 20px;">   <input type="checkbox"  value="on" <?php echo (get_option('linksync_woocommerce_tax_option') == 'on' ? 'checked' : ''); ?> name="linksync_woocommerce_tax_option" /></label>Use WooCommerce Tax Options <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Recommended - this option uses the WooCommerce Tax Options settings to determine if your prices are inclusive or exclusive of tax when syncing with Vend. You should only need to disable this option if you have altered the standard tax settings in Vend."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a>
                                    <div id="linksync_taxes" style="margin-left: 25px;"><br><b>Treat prices in Vend as</b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When syncing prices with Vend, should linksync treat the Vend price as inclusive or exclusive of tax. Which option you select will depend on whether your prices in WooCommerce include tax or not."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a>
                                        <br> 
                                        <div  style="margin-left: 23px;">
                                            <ul>
                                                <li>  
                                                    <input name="excluding_tax" value="on" type="radio"   <?php echo (get_option('excluding_tax') == 'on' ? 'checked' : ''); ?>> Exclusive of Tax
                                                </li>
                                                <li>
                                                    <input name="excluding_tax" value="off" type="radio"   <?php echo (get_option('excluding_tax') == 'off' ? 'checked' : ''); ?>> Inclusive of Tax
                                                </li>
                                            </ul></div>   </div>  <br>                          
                                <?php } else {
                                    ?> <b>Treat prices in Vend as</b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When syncing prices with Vend, should linksync treat the Vend price as inclusive or exclusive of tax. Which option you select will depend on whether your prices in WooCommerce include tax or not."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a>
                                    <br> 
                                    <div  style="margin-left: 23px;">
                                        <ul>
                                            <li>  
                                                <input name="excluding_tax" value="on" type="radio" <?php echo (get_option('excluding_tax') == 'on' ? 'checked' : '') ?> > Exclusive of Tax
                                            </li>
                                            <li>
                                                <input name="excluding_tax" value="off" type="radio" <?php echo (get_option('excluding_tax') == 'off' ? 'checked' : ''); ?>> Inclusive of Tax
                                            </li>
                                        </ul></div><?php }
                                ?>
                                <br><label class="ps_price_sub_options" style="display: inline-block;width: 25em;"><b>Tax Mapping<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When syncing products, both Vend and WooCommerce have their own tax configurations - use these Tax Mapping settings to 'map' the Vend taxes with those in your WooCommerce store. Note that the mapping is used to specify the Tax Class for a product in WooCommerce, and the Sales tax for a product in Vend, depending on which Product Syncing Type you select. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></b></label>
                                <p style="margin-left: 23px;" class="description ps_price_sub_options">To set the relevant tax rate for a product in WooCommerce  
                                </p> <?php
                                for ($count_taxes = 1; $count_taxes <= 3; $count_taxes++) {
                                    $taxes = $apicall->linksync_getTaxes();
                                    if (isset($taxes) && !empty($taxes)) {
                                        break;
                                    }
                                }
                                $taxes_all = explode(',', get_option('tax_class'));
                                if (isset($taxes) && !empty($taxes)) {
                                    if (!isset($taxes['errorCode'])) {
                                        if (isset($taxes['taxes'])) {
                                            ?><div style="margin-left: 23px;"><ul><legend class="ps_price_sub_options" style="display: inline-block;width: 8em; float: left"> <b>Vend Taxes</b></legend>   <legend class="ps_price_sub_options" style="display: inline-block;width: 3em; float: left">=></legend>  <legend class="ps_price_sub_options" style="display: inline-block;width: 25em; "><b>Woo-Commerce Tax Classes</b></legend><br><?php
                                $tax_classes_list = array_map("rtrim", explode("\n", get_option('woocommerce_tax_classes')));
                                $implode_tax['tax_name'][] = 'standard-tax';
                                foreach ($tax_classes_list as $value) {
                                    $taxexplode = explode(" ", strtolower($value));
                                    $implode_tax['tax_name'][] = implode("-", $taxexplode);
                                }
                                foreach ($taxes['taxes'] as $select_tax) {
                                                ?>
                                                        <li> <legend class="ps_price_sub_options" style="display: inline-block;width: 8em; float: left"><?php echo $select_tax['name']; ?> </legend> 
                                                        <legend class="ps_price_sub_options" style="display: inline-block;width: 3em; float: left">=></legend> 
                                                        <legend class="ps_price_sub_options" style="display: inline-block;width: 25em; "><select style="margin-top: -5px"name="tax_class[]">
                                                                <?php
                                                                foreach ($implode_tax['tax_name'] as $tax) {
                                                                    $taxes_all = array_filter($taxes_all);
                                                                    if (!empty($taxes_all)) {
                                                                        if (in_array($select_tax['name'] . '-' . $select_tax['rate'] . '|' . $tax, $taxes_all)) {
                                                                            $selected = "selected";
                                                                        } else {
                                                                            $selected = '';
                                                                        }
                                                                    } else {
                                                                        $selected = ($select_tax['name'] == 'No Tax' && $tax == 'zero-rate') ? 'selected' : '';
                                                                    }

                                                                    echo '<option value="' . $select_tax['name'] . '-' . $select_tax['rate'] . '|' . $tax . '" ' . $selected . '>' . $tax . '</option>';
                                                                }
                                                                ?>
                                                            </select></legend></li>
                                                    <?php }
                                                    ?></ul></div><?php
                                        } else {
                                            echo "<span style='color:red;font-weight:bold;'>Error in getting Taxes : Not Getting Expecting Data !!</span><br>";
                                        }
                                    } else {
                                        echo "<span style='color:red;font-weight:bold;'>Error in getting Taxes : $taxes[userMessage]</span><br>";
                                    }
                                }
                                        ?>

                                </legend> 

                                <!--                                <legend  id="ps_pricebook_id" style="display:<?php
                                    //    if ($product_sync_type == 'two_way' || $product_sync_type == 'wc_to_vend') {
                                    //   echo "none";
                                    // }
                                        ?>;width: 30em;"><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Product data is synced to and from WooCommerce and Vend (default)"  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a><b>Price Book</b>
                                                                    <br><span style="margin-left: 40px;"><input type="checkbox"  <?php // echo (get_option('price_book') == 'on' ? 'checked' : '');                                                                                                                                                                                                                                                             ?> value="on"  name="price_book" />User a Vend Price book to set product prices<br>
                                                                        <span id="price_book_identifier_id"style="margin-top:15px;display:<?php
                                // if (get_option('price_book') != 'on') {
                                //  echo "none";
                                //   }
                                        ?>;margin-left: 61px;" >
                                                                            Price Book identifier: <input type="text" maxlength="64"   name="price_book_identifier" value="<?php // echo get_option('price_book_identifier');                                                                                                                                                                                                                                                                ?>" /></span> </span>
                                                                </legend> -->
                            </span></div>
                    </td>
                </tr>
                <tr valign="top">
                    <th  class="titledesc">Quantity  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, product quantities will be kept in sync. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>
                    <td class="forminp forminp-checkbox">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_quantity') == 'on' ? 'checked' : ''); ?> value="on" name="ps_quantity" id="ps_quantity"/>Sync product Quantity between apps</label>
                        <span class="ps_quanity_suboptions" >
                            <?php
                            $getoutlets = get_option('ps_outlet_details');
                            $wc_to_vend_dboutlet = get_option('wc_to_vend_outlet_detail');
                            for ($count_outlets = 1; $count_outlets <= 3; $count_outlets++) {
                                $outlets = $apicall->linksync_getOutlets();
                                if (isset($outlets) && !empty($outlets)) {
                                    break;
                                }
                            }
                            if (isset($outlets) && !empty($outlets)) {
                                if (!isset($outlets['errorCode'])) {
                                    if (isset($outlets['outlets'])) {
                                        if (count($outlets['outlets']) == 1) {
                                            $dispaynone = 'display:none;';
                                            $checked = "checked";
                                            $margin = "";
                                        } else {
                                            $dispaynone = 'display:block;';
                                            $margin = 'margin-left:40px;margin-top:10px;';
                                            $checked = '';
                                        }
                                        ?>
                                        <div  id = "wctovend_outlet" style = "margin-left: 45px;display:<?php
                            if ($product_sync_type == 'vend_to_wc-way') {
                                echo "none";
                            }
                                        ?>;"><div id="wc_to_vend_outlet"><label  style = "<?php echo $dispaynone; ?>width: 440px;margin-top:10px">
            <!--                                                <input type = "checkbox" <?php //echo (get_option('ps_wc_to_vend_outlet') == 'on' ? 'checked' : '');
                                        ?> value="on" name="ps_wc_to_vend_outlet" />-->
                                                    <b> Vend Outlet to sync WooCommerce with </b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="If you have more than one outlet in your Vend store you have the option of choosing which outlet/s you want to keep product synced with. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </label>
                                                <?php
                                                echo'<span style=' . $margin . $dispaynone . '>';
                                                $i = 0;
                                                foreach ($outlets['outlets'] as $outlet):
                                                    if (!empty($wc_to_vend_dboutlet)) {
                                                        $outlet_name = explode('|', $wc_to_vend_dboutlet);
                                                        if (isset($outlet_name[1]) && !empty($outlet_name[1])) {
                                                            if ($outlet['id'] == $outlet_name[1]) {
                                                                $check = 1;
                                                                $checked = "checked";
                                                                $margin = 'margin-left:40px;';
                                                            } elseif (count($outlets['outlets']) == 1) {
                                                                $checked = "checked";
                                                                $margin = "";
                                                            } else {
                                                                if (isset($check) == 1) {
                                                                    $checked = "";
                                                                    $margin = 'margin-left:40px;';
                                                                } else {
                                                                    $check = 2;
                                                                    $checked = "checked";
                                                                    $margin = 'margin-left:40px;';
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        if ($i == 0) {
                                                            $checked = 'checked';
                                                        } else {
                                                            $checked = '';
                                                        }
                                                    }
                                                    ?><input type="radio" style="'<?php
                                            if (isset($margin)) {
                                                echo $margin;
                                            }
                                                    ?>'"<?php
                                        if (isset($checked)) {
                                            echo $checked;
                                        }
                                                    ?> name="wc_to_vend_outlet_detail" <?php
                                        echo 'value="' . htmlentities($outlet['name'], ENT_QUOTES) . '|' . $outlet['id'] . '">' . $outlet['name'] . ' &nbsp;&nbsp;&nbsp;&nbsp;';
                                        $i++;
                                    endforeach;
                                    echo"   </span> ";
                                } else {
                                    echo "<span style='color:red;font-weight:bold;'>Error in getting outlets : Not Getting Expecting Data !!</span><br>";
                                }
                            } else {
                                echo "<span style='color:red;font-weight:bold;'>Error in getting outlets : $outlets[userMessage]</span><br>";
                            }
                        } else {
                            echo "<br>";
                            echo "<span style='color:red';>Error in Getting Outlets</span>";
                            echo "<br>";
                        }
                                    ?> 
                                </div></div></div><?php
                                        if (isset($outlets) && !empty($outlets)) {
                                            if (!isset($outlets['errorCode'])) {
                                                if (isset($outlets['outlets'])) {
                                                    if (count($outlets['outlets']) == 1) {
                                                        $dispaynone = 'display:none;';
                                                        $checked = "checked";
                                                        $margin = "";
                                                    } else {
                                                        $dispaynone = 'display:block;';
                                                        $margin = 'margin-left:40px;margin-top:10px;';
                                                        $checked = 'checked';
                                                    }
                                                    $dboutlet = explode("|", $getoutlets);

                                                    #--------------------------------VEND-----TO----WC------OUTLET---------------------------------#
                                                ?> 
                                        <div  id="vend-to-wc_outlet" style="margin-left:45px;display:<?php
                            if ($product_sync_type == 'wc_to_vend' || $product_sync_type == 'two_way') {
                                echo "none;";
                            }
                                                ?>;"><div  id="outlet"> <label  style="<?php echo $dispaynone; ?>width: 300px;margin-top:10px">
                                                <!--<input type="checkbox" <?php // echo (get_option('ps_outlet') == 'on' ? 'checked' : '');                                                                                                                                                               ?> value="on" name="ps_outlet" />--> 
                                                    <b>Vend Outlet to sync WooCommerce with</b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="If you have more than one outlet in your Vend store you have the option of choosing which outlet/s you want to keep product synced with. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a>
                                                </label>
                                                <?php
                                                echo'<span style=' . $margin . $dispaynone . '>';
                                                foreach ($outlets['outlets'] as $outlet):
                                                    if (!empty($dboutlet)) {
                                                        if (in_array($outlet['id'], $dboutlet)) {
                                                            $checked = "checked";
                                                            $margin = 'margin-left:40px;';
                                                        } elseif (count($outlets['outlets']) == 1) {
                                                            $checked = "checked";
                                                            $margin = "";
                                                        } else {
                                                            $checked = "";
                                                            $margin = 'margin-left:40px;';
                                                        }
                                                    }
                                                    ?><input class="outlets_check" type="checkbox" style="'<?php
                                    if (isset($margin)) {
                                        echo $margin;
                                    }
                                                    ?>'"<?php
                                           if (isset($checked)) {
                                               echo $checked;
                                           }
                                                    ?> name="outlet[]" <?php
                                           echo 'value="' . $outlet['id'] . '">' . $outlet['name'] . ' &nbsp;&nbsp;&nbsp;&nbsp;';
                                       endforeach;
                                                ?>  <div id="check_outlets" style="color:red;margin-top: 10px;"></div> <?php
                                           echo"   </span> ";
                                       } else {
                                           echo "<span style='color:red;font-weight:bold;'>Error in getting outlets : Not Getting Expecting Data !!</span><br>";
                                       }
                                   } else {
                                       echo "<span style='color:red;font-weight:bold;'>Error in getting outlets : $outlets[userMessage]</span><br>";
                                   }
                               }
                                    ?> 

                                </div></div><div  id="unpublish_stock_id" style="margin-top:5px;display:<?php
                                           if ($product_sync_type == 'wc_to_vend') {
                                               echo "none";
                                           }
                                    ?>;margin-left: 45px;margin-top:10px;"><input type="checkbox"  <?php echo (get_option('ps_unpublish') == 'on' ? 'checked' : ''); ?> value="on" name="ps_unpublish"  />Change product status in WooCommerce based on stock quantity<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select this option if you want product with inventory quantities of 0 (zero) or less to be made unavailable for purchase in your WooCommerce store. In the case of simple product this option will set them them to 'draft', and in the case of Variable products, the variation would be set to 'Out of stock'."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a>
                            </div></span> 
                    </td>
                </tr>
                <?php if (in_array('woocommerce-brands/woocommerce-brands.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    ?>
                    <tr valign="top" class="woocommerce_frontend_css_colors">
                        <th scope="row" class="titledesc">Brand<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Enable this option to keep the Brand fields in sync between WooCommerce and Vend."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>
                        <td class="forminp">
                            <label style="display: inline-block;width: 25em;">
                                <input type="checkbox" <?php echo (get_option('ps_brand') == 'on' ? 'checked' : ' '); ?> value="on" name="ps_brand" />Brand</label>
                        </td>
                    </tr>
                    <?php
                } if ($product_sync_type == 'wc_to_vend' || $product_sync_type == 'two_way') {
                    $display = 'none';
                } else {
                    $display = '';
                }
                ?>
                <tr id="product_attribute" style="display:<?php echo $display; ?>"  valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Attributes<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select this option if you want your product attributes for Variable Products to mirror those in Vend - checking this option will result in removal of attributes and values that don't exist for synced products from your Vend store "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </th>
                    <td class="forminp">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_attribute') == 'on' ? 'checked' : ''); ?> value="on" name="ps_attribute" />Sync attributes and values with Vend</label>
                        <br><br>
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('linksync_visiable_attr') == '1' ? 'checked' : ''); ?> value="1" name="linksync_visiable_attr" />Sync attributes <b>Visible on Product Page</b></label>
                    </td>
                </tr>
                <tr valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Tags  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, product tags will be kept in sync. If you use tags for the Categories , or for the Import by tag options below, then you may opt not to enable this option as you might have tags you don't want displayed to visitors of your store. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </th>
                    <td class="forminp">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_tags') == 'on' ? 'checked' : ''); ?> value="on" name="ps_tags" />Sync tags between apps</label>
                    </td>
                </tr>
                <tr style="display:<?php echo ($product_sync_type == 'wc_to_vend' ? 'none' : ''); ?>" id="ps_cat_id_p" valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Categories   <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-right: 10px" title="Enabling this option will allow you to sync products from Vend to categories in WooCommerce by Vend Tags or by Product Types"  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>
                    <td class="forminp">
                        <!--aman changes-->
                        <input name="ps_categories" type="checkbox" value="on"  <?php echo (get_option('ps_categories') == 'on' ? 'checked' : ''); ?> />Sync WooCommerce product categories with Vend<br>
                        <p style="margin-left: 23px;" class="description">Use with caution as any existing product categories in WooCommerce not matching those in Vend will be deleted<br> <a href="https://help.linksync.com/hc/en-us/articles/205715889">Click here for more information</a>
                        </p><br>
                        <div class="ps_categories" style="margin-left:45px"><label class="ps_categories" style="display: inline-block;width: 10em; " ><input type="radio" name="cat_radio"  <?php echo (get_option('cat_radio') == 'ps_cat_tags' ? 'checked' : ''); ?>  value="ps_cat_tags">Tags<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Vend tags are mapped to existing categories in WooCommerce &#45; if no category has been set up in WooCommerce, tags will not be mapped. Subcategories are recognised when the different paths are separated by &quot; / &quot; (space/space).  "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></label>
                            <br><br> <label class="ps_categories" style="display: inline-block;width: 10em; " ><input type="radio" name="cat_radio" <?php echo (get_option('cat_radio') == 'ps_cat_product_type' ? 'checked' : ''); ?>  value="ps_cat_product_type">Product Types<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Vend product types are mapped to existing categories in WooCommerce &#45; if no category has been set up in WooCommerce, product types will not be mapped."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </label></div>
                    </td> 
                </tr>
                <tr style="display:<?php echo ($product_sync_type == 'wc_to_vend' ? 'none' : ''); ?>" id="ps_pending" valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Product Status <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-right: 10px" title="Enable this option if you want newly created product in Vend to be set to 'Pending Review' so that you can review and update new product before they are published in your WooCommerce store."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>

                    <td class="forminp">
                        <!--aman changes-->
                        <input name="ps_pending" type="checkbox" value="on"  <?php echo (get_option('ps_pending') == 'on' ? 'checked' : ''); ?> />Tick this option to Set new product to <b>Pending</b><br><br>
                    </td>

                </tr>
                <tr id="import_by_tags_tr" valign="top" style="margin-top:5px;display:<?php
                if ($product_sync_type == 'wc_to_vend') {
                    echo "none";
                }
                ?>;" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Import by Tag <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Enable this option if you only want product in Vend with specific tags to be synced to your WooCommerce store. This might be of benefit if you don't want all product in your Vend store syncing to your WooCommerce store. Select one or more tags from the list to only sync product with matching tags."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>
                    <td class="forminp">
                        <label style="display: inline-block;width:22px;">
                            <input type="checkbox" value="on" <?php echo (get_option('ps_imp_by_tag') == 'on' ? 'checked' : ''); ?>  name="ps_imp_by_tag" /></label>Import by Tag
                        <br><br>  <span  style="display:<?php echo (get_option('ps_imp_by_tag') == 'on' ? 'block' : 'none'); ?>"id="import_by_tags_list" style="margin-left: 20px;">
                            <?php
                            for ($count_tags = 1; $count_tags <= 3; $count_tags++) {
                                $product_tags = $apicall->linksync_getTags();
                                if (isset($product_tags) && !empty($product_tags)) {
                                    break;
                                }
                            }
                            if (!empty($product_tags['tags'])) {
                                sort($product_tags['tags']);
                            }
                            $import = get_option('import_by_tags_list');
                            if (!empty($import)) {
                                $import_tags = unserialize($import);
                                $tags_import = explode('|', $import_tags);
                            }
                            ?>
                            <select style="margin-left:45px;" multiple="multiple" name="import_by_tags_list[]">
                                <?php

                                if( !empty( $product_tags['tags'] ) ){
									$i = 0;
									foreach( $product_tags['tags'] as $tag ){
										if (empty($import_tags)) {
											if ($i == 0) {
												$selected = "selected=seleted";
											} else {
												$selected = "";
											}
										} else {
											if (in_array($tag['name'], $tags_import)) {
												$selected = "selected=seleted";
											} else {
												$selected = "";
											}
										}
										?>
										<option value="<?php echo $tag['name']; ?>" <?php echo $selected ?>><?php echo $tag['name']; ?></option>
										<?php
										$i++;
									}

								}

                                ?>
                            </select> </span> </td>
                </tr> 

                <tr style="display:<?php echo ($product_sync_type == 'wc_to_vend' ? 'none' : ''); ?>" id="ps_import_image_id" valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Images<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select this option to have product images in Vend synced to products in WooCommerce."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></th>
                    <td class="forminp">
                        <!--aman changes-->
                        <input name="ps_images" type="checkbox" value="on"  <?php echo (get_option('ps_images') == 'on' ? 'checked' : ''); ?> />Sync images from Vend to WooCommerce<br><br>
                        <div class="ps_images" style="margin-left:45px"><label class="ps_images" style="display: inline-block;width: 10em; " > 
                                <input type="radio" <?php echo (get_option('ps_import_image_radio') == 'Enable' ? 'checked' : ''); ?> name="ps_import_image_radio" value="Enable">Once <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="This option will sync images from Vend to WooCommerce products on creation of a new product, or if an existing product in WooCommerce does not have an image."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a></label>
                            <br><br> <label class="ps_images" style="display: inline-block;width: 10em; " >   <input type="radio" <?php echo (get_option('ps_import_image_radio') == 'Ongoing' ? 'checked' : ''); ?> name="ps_import_image_radio" value="Ongoing">Ongoing <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="This option provides the same function as 'Once', but will update product images if the they are modified in Vend. For example, if you update an image for a product in Vend, then that update images will be synced to the corresponding product in WooCommerce."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </label></div>
                    </td> 
                </tr>
                <tr style="margin-top:5px;display:<?php
                                if ($product_sync_type == 'wc_to_vend') {
                                    echo "none";
                                }
                                ?>;" id="ps_create_tr" valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Create New<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select this option if you want 'new' products from Vend created in WooCommerce automatically. If this option is not enabled, then new products will not be created in WooCommerce - you will need to manually create them, after which, they will be kept in sync. "  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </th>
                    <td class="forminp">  <input type="checkbox" <?php echo (get_option('ps_create_new') == 'on' ? 'checked' : ''); ?> value="on" name="ps_create_new" />Create new products from Vend <br>
                    </td>
                </tr>
                <tr valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Delete<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Caution - use with care - deleted products can not be recovered. Select this option if you want product permanently deleted. Depending on which Product Syncing Type you select, if products are deleted in one store, they will immediately be deleted from the other."  src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16"></a> </th>
                    <td class="forminp">   <input type="checkbox" <?php echo (get_option('ps_delete') == 'on' ? 'checked' : ''); ?> value="on" name="ps_delete" />Sync product deletions between apps<br>
                    </td>
                </tr>

                </div>
            </tbody>
        </table></div>
    <p style="text-align: center;"><input  class="button button-primary button-large save_changes" type="submit"  name="save_product_sync_setting" value="Save Changes" /></p>
</form>
<div id="pop_up_syncll" class="clientssummarybox" style="width: 500px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 34%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
    <center><h4 style="display:none;" id="syncing_loader1"><img src="../wp-content/plugins/linksync/assets/images/linksync/ajax-loader.gif"></h4></center>
    <center><div id="total_product"></div></center>
    <center><p id="show-result"></p></center>
    <center><h4 id="sync_start_export1"></h4></center>
    <center><h4 id="sync_start_export_all">Do you want to sync all product to Vend?</h4></center>

    <form target="" method="post" action=""><input type="button" onclick="return sync_process_start();"  name="sync_all_product_to_vend" style="color: green;
                                                   margin-left: 118px;
                                                   width: 90px;
                                                   font-weight: 900;float: left;" target="_blank" class="button hidesync"   value="Yes"></form>

    <input  type="button" class="button hidesync" style="color: red;
            margin-left: 83px;
            width: 90px;font-weight: 900;"  name="close_syncall"  onclick="jQuery('#pop_up_syncll').fadeOut();"  value='No'/></div>  
<style> 
    .loader-please-wait {
        background-image: url(../wp-content/plugins/linksync/assets/images/linksync/shader.png);
        position: fixed;
        display: none;
        z-index: 1000000000;
        height: 100%;
        width: 100%;
        left: 0;
        top: 0;
    }
    #h2_linksync{
        font-size: 17px !important;
        font-weight: 400;
        padding: 0px 0px 0px 0;  
        font-style: normal;
        color: #333;
        font-family: Helvetica,Arial,sans-serif;
        margin: 0 0 5px;
        text-align: center;
        line-height: 1.3em !important; 
    }
    .loader-please-wait .loader-content {

        border-radius: 10px; 
        box-shadow: 3px 6px 8px #555;
        position: relative;
        top: 200px;
        width: 300px;
        margin: auto;
        padding: 20px 0;
        text-align: center;
        background-color: #fff;
        border: 1px solid #666;
    }
    </style>

    <div id="please-wait" class="loader-please-wait" style="display: none;">
        <div class="loader-content"> 
            <h3 id="h2_linksync">Linksync is Updating data<br>Please wait...</h3>
            <p><img style="color: blue" src="../wp-content/plugins/linksync/assets/images/linksync/loading_please_wait.gif"></p>
            </div>
        </div> 
        <script type="text/javascript"> 
            jQuery(".outlets_check").change(function(){
                if (jQuery('.outlets_check:checked').length ==0) {
                    jQuery("#check_outlets").show(); 
                    jQuery("#check_outlets").html('You didn\'t select any outlet !'); 
                    return false;
                }else{ 
                    jQuery("#check_outlets").hide();
                    return true;
                } 
            });
            function  show_confirm_box() { 
                if(jQuery("#pop_up_syncll").is(":visible")==false && jQuery("#pop_up_two-way").is(":visible")==false && jQuery("#pop_up").is(":visible")==false){
                    jQuery(document).ready(function() {
                        jQuery('.hidesync').show(); 
                        jQuery('#sync_start_export').show();
                        jQuery("#sync_start_export").html('Do you want to sync all product to Vend?'); 
                        jQuery('#pop_up_syncll').fadeIn();   });
<?php update_option('post_product', 0); ?>
      
        }
    }  
    var request_time=3000;
    var communication_key='<?php echo get_option('webhook_url_code'); ?>';
    var mycounter=1;  
    function ajaxRequestForproduct(i,totalreq){ 
        var ajaxobj= jQuery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'offset':i,'communication_key':communication_key},
            url: '../wp-content/plugins/linksync/includes/apps/vend/sync_all_product_to_vend.php',
            success:function(data){ 
                console.log(data);
            },
            complete:function(responsedata){ 
                if(responsedata){ 
                    var incounter=mycounter++;
                    jQuery('#sync_start_export_product').show();
                    jQuery('#sync_start_export_product').html("<p style='font-size:15px;'><b>"+incounter+"</b> of <b>"+totalreq+"</b> product(s) exporting..</p>");
                    jQuery("#sync_start_export1").show();    
                    jQuery("#sync_start_export1").html("<p style='font-size:15px;'><b>"+incounter+"</b> of <b>"+totalreq+"</b> product(s) exporting..</p>");
                    if(incounter>=totalreq){ 
                        jQuery.ajax({
                            url:  '../wp-content/plugins/linksync/includes/apps/vend/sync_all_product_to_vend.php',
                            type: 'POST',
                            data: { "get_total": "1",'communication_key':communication_key},
                            success: function(response) { 
                            }
                        });
                        jQuery("#show-result").show();   
                        jQuery("#show-result").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                        jQuery("#show-result_all").show();   
                        jQuery("#show-result_all").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                        jQuery('#syncing_loader1').hide(); 
                        jQuery("#pop_up_syncll").hide(1500); 
                        jQuery("#pop_up_two-way").hide(1500); 
                        jQuery('#sync_start_export1').hide();
                        jQuery('#sync_start_export_product').hide();
                        jQuery("#show-result").hide(1500);  
                        jQuery("#show-result_all").hide(1500); 
                        jQuery("#sync_start_export_all").show(1500);
                        ajaxobj.abort();
                        return false;
                    }else {
                        ajaxRequestForproduct(i,totalreq);
                    }
                                     
                }  
            },  
            statusCode: {
                404: function(){
                    console.log('sync_all_product_to_vend.php File not Found !');
                }, 
                200: function(){
                    // jQuery("#export_report").html(i++);
                }, 
                504:function(){
                    console.log('Got 504 status code in response then request again '); 
                }
            }
        }); 
        i++;
    }
    function sync_process_start() {
        jQuery('#showMessage').hide();
        jQuery('#pop_button').hide(); 
        jQuery('#sync_start_export_all').hide();  
        jQuery('#syncing_loader_all').css("display", "block");
        jQuery('#sync_start_export_product').show();
        jQuery('#sync_start_export_product').html("<h3>Starting....</h3>");
        jQuery('#syncing_loader1').css("display", "block");
        jQuery('#sync_start_export1').show();
        jQuery('#sync_start_export1').html("<h3>Starting....</h3>");
        var dataupper;
        var communication_key='<?php echo get_option('webhook_url_code'); ?>';
        jQuery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'communication_key':communication_key},
            url: '../wp-content/plugins/linksync/includes/apps/vend/sync_all_product_to_vend.php',
            success:function(dataupper){ 
                if(dataupper.total_post_id!=0){ 
                    var totalreq=dataupper.total_post_id;  
                    ajaxRequestForproduct(1,totalreq);
                   
                }else{
                    jQuery("#show-result").show();   
                    jQuery("#show-result").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                    jQuery("#show-result_all").show();   
                    jQuery("#show-result_all").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                    jQuery('#syncing_loader1').css("display", "none");
                    jQuery('#syncing_loader_all').css("display", "none");
                    jQuery("#pop_up_syncll").hide(1500);  
                    jQuery("#pop_up_two-way").hide(1500);
                    jQuery('#sync_start_export1').hide();
                    jQuery('#sync_start_export_product').hide();
                    jQuery("#show-result").hide(1500);
                    jQuery("#show-result_all").hide(1500); 
                    jQuery("#sync_start_export_all").show(1500);
                }
            }
        }); 
        jQuery('.hidesync').hide();
         
    } 
    jQuery(document).ready(function() {
        jQuery("input[name='product_sync_type']").click(function() {
            if (jQuery("#disabled_sync_id").is(":checked")) {
                jQuery('#product_sync_settig').slideUp(500);
            } else {
                jQuery('#product_sync_settig').slideDown(500);
            }
            if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
                jQuery('#sync_reset_all_btn_id').hide(500); 
                jQuery('#import_by_tags_tr').fadeIn(500);
            } else { 
                jQuery('#sync_reset_all_btn_id').show(500);
            } 
            if (jQuery("input[value='two_way']").is(":checked")) { 
                jQuery('#import_by_tags_tr').fadeIn(500);
            }
            if (jQuery("input[value='wc_to_vend']").is(":checked")) {
                jQuery('#sync_reset_btn_id').hide(500);
                jQuery('#short_description').hide(500);
                jQuery('#ps_cat_id_p').fadeOut(500);
                jQuery('#ps_import_image_id').fadeOut(500);
                jQuery("input[name='ps_create_new_p']").fadeOut(500);
                jQuery('#ps_create_tr').fadeOut(500);
                jQuery('#ps_pending').fadeOut(500); 
                jQuery('#import_by_tags_tr').fadeOut(500);
            } else {
                jQuery('#sync_reset_btn_id').show(500);
                jQuery('#short_description').show(500);
                jQuery("input[name='ps_create_new_p']").fadeIn(500);
                jQuery('#ps_cat_id_p').fadeIn(500);
                jQuery('#ps_import_image_id').fadeIn(500);
                jQuery('#ps_create_tr').fadeIn(500);
                jQuery('#ps_pending').fadeIn(500);
            }
            if(jQuery("#ps_quantity").is(":checked")){ 
                if (jQuery("input[value='two_way']").is(":checked")||jQuery("input[value='wc_to_vend']").is(":checked")){
                    if(jQuery("input[value='two_way']").is(":checked")) {
                        jQuery("#unpublish_stock_id").fadeIn(500);   
                    }else{
                        jQuery("#unpublish_stock_id").fadeOut(500); 
                    }
                    jQuery("#wctovend_outlet").fadeIn(500);
                }else if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
                    jQuery("#unpublish_stock_id").fadeIn(500); 
                    jQuery("#outlet").fadeIn(500);
                    jQuery("#wctovend_outlet").fadeOut(500);
                }
            }else{ 
                jQuery("#unpublish_stock_id").fadeOut(500); 
                jQuery("#outlet").fadeOut(500);
                jQuery("#wctovend_outlet").fadeOut(500); 
            }
            if (jQuery("input[value='two_way']").is(":checked") || jQuery("input[value='wc_to_vend']").is(":checked")) {
                jQuery('#ps_pricebook_id').fadeOut(500); 
                jQuery('#product_attribute').fadeOut(500);  
            } else {
                jQuery('#ps_pricebook_id').fadeIn(500);
                jQuery('#product_attribute').fadeIn(500);  
            }
            if (jQuery("input[value='wc_to_vend']").is(":checked")) {
                jQuery("#wc_to_vend_outlet").fadeIn(500);
                jQuery("#vend-to-wc_outlet").fadeOut(500); 
            } else if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
                if (jQuery('.outlets_check:checked').length ==0) { 
                    jQuery("#check_outlets").show(); 
                    jQuery("#check_outlets").html('You didn\'t select any outlet !');
                }
                jQuery("#wc_to_vend_outlet").fadeOut(500);
                jQuery("#vend-to-wc_outlet").fadeIn(500); 
                
            } else {
                jQuery("#wc_to_vend_outlet").fadeIn(500);
                jQuery("#vend-to-wc_outlet").fadeOut(500); 
            }

        });
        
        if (jQuery("input[name='ps_description']").is(":checked")) {
            jQuery("#ps_desc_span").fadeIn(500);
        }

        jQuery("input[name='ps_description']").click(function() {
            if (jQuery("input[name='ps_description']").is(":checked")) {
                jQuery("#ps_desc_span").fadeIn(500);
            } else {
                jQuery("#ps_desc_span").fadeOut(500);
            }
        });

        jQuery("input[name='ps_price']").click(function() {
            if (jQuery(this).is(":checked")) {
                jQuery(".ps_price_sub_options").fadeIn(500);
            } else {
                jQuery(".ps_price_sub_options").fadeOut(500);
            }
        });
        if (jQuery("input[name='ps_price']").is(":checked")) {
            jQuery(".ps_price_sub_options").fadeIn(500);
        } else {
            jQuery(".ps_price_sub_options").fadeOut(500);
        }
        jQuery("input[name='ps_imp_by_tag']").click(function() {
            if (jQuery(this).is(":checked")) {
                jQuery("#import_by_tags_list").fadeIn(500);
            } else {
                jQuery("#import_by_tags_list").fadeOut(500);
            }
        });

        jQuery("input[name='ps_quantity']").click(function() {
            if (jQuery(this).is(":checked")) { 
                if (jQuery("input[value='two_way']").is(":checked")||jQuery("input[value='wc_to_vend']").is(":checked")){
                    if(jQuery("input[value='two_way']").is(":checked")) {
                        jQuery("#unpublish_stock_id").fadeIn(500);   
                    }else{
                        jQuery("#unpublish_stock_id").fadeOut(500); 
                    }
                    jQuery("#wctovend_outlet").fadeIn(500);
                }else if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
                    jQuery("#unpublish_stock_id").fadeIn(500); 
                    jQuery("#outlet").fadeIn(500);
                    jQuery("#wctovend_outlet").fadeOut(500);
                }
                
            } else {
                jQuery("#outlet").fadeOut(500);
                jQuery("#wctovend_outlet").fadeOut(500);
                jQuery("#unpublish_stock_id").fadeOut(500);  
            } 
             
        });
        if (jQuery("#ps_quantity").is(":checked")) { 
            if (jQuery("input[value='two_way']").is(":checked")||jQuery("input[value='wc_to_vend']").is(":checked")){
                if(jQuery("input[value='two_way']").is(":checked")) {
                    jQuery("#unpublish_stock_id").fadeIn(500);   
                }else{
                    jQuery("#unpublish_stock_id").fadeOut(500); 
                }
                jQuery("#wctovend_outlet").fadeIn(500);
            }else if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
                jQuery("#unpublish_stock_id").fadeIn(500); 
                jQuery("#outlet").fadeIn(500);
                jQuery("#wctovend_outlet").fadeOut(500);
            }
        } else { 
            jQuery("#outlet").fadeOut(500);
            jQuery("#unpublish_stock_id").fadeOut(500);  
            jQuery("#wctovend_outlet").fadeOut(500);
        } 
        jQuery("input[name='ps_wc_to_vend_outlet']").click(function() {
            if (jQuery("input[name='ps_wc_to_vend_outlet']").is(":checked")) {
                jQuery("#wc_to_vend_outlet").fadeIn(500);
            } else {
                jQuery("#wc_to_vend_outlet").fadeOut(500);
            }
        });
        if (jQuery("input[name='ps_categories']").is(":checked")) {
            jQuery(".ps_categories").fadeIn(500);
        } else {
            jQuery(".ps_categories").fadeOut(500);
        }
        jQuery("input[name='ps_categories']").click(function() {
            if (jQuery("input[name='ps_categories']").is(":checked")) {
                jQuery(".ps_categories").fadeIn(500);
            } else {
                jQuery(".ps_categories").fadeOut(500);
            }
        });
        if (jQuery("input[name='linksync_woocommerce_tax_option']").is(":checked")) {  
            jQuery('#linksync_taxes').fadeOut(500);
        } else { 
            jQuery('#linksync_taxes').fadeIn(500); 
        }
        jQuery("input[name='linksync_woocommerce_tax_option']").click(function() {
            if (jQuery("input[name='linksync_woocommerce_tax_option']").is(":checked")) {
                jQuery("#linksync_taxes").fadeOut(500);
            } else {  
                jQuery("#linksync_taxes").fadeIn(500);
            }
        });
        if (jQuery("input[name='ps_images']").is(":checked")) {
            jQuery(".ps_images").fadeIn(500);
        } else {
            jQuery(".ps_images").fadeOut(500);
        }
        jQuery("input[name='ps_images']").click(function() {
            if (jQuery("input[name='ps_images']").is(":checked")) {
                jQuery(".ps_images").fadeIn(500);
            } else {
                jQuery(".ps_images").fadeOut(500);
            }
        });
    });
    jQuery(document).on("click","a[name='lnkViews']", function (e) {
        jQuery('#sync_reset_all_btn_id').attr('disabled',true);
        jQuery('#sync_reset_btn_id').attr('disabled',true); 
        jQuery("#pop_up_two-way").fadeOut(500); 
        jQuery('#response').removeClass('error').addClass('updated').html("Changes has been saved successfully!!").fadeIn(500).delay(3000).fadeOut(4000);
        location.reload();  
    });
    jQuery("input[name='close']").click(function() {  
        jQuery("#pop_up").fadeOut(200); 
        jQuery('#response').removeClass('error').addClass('updated').html("Changes has been saved successfully!!").fadeIn(500).delay(3000).fadeOut(4000);

    });
    jQuery("input[name='no']").click(function() {
        jQuery("#pop_up").fadeOut(500); 
        jQuery('#response').removeClass('error').addClass('updated').html("Synchronic Reset successfully!!").fadeIn(500).delay(3000).fadeOut(4000);

    }); 
    function  re_sync_process_start2() {
<?php
update_option('image_process', 'complete');
update_option('prod_last_page', NULL);
//update_option('product_image_ids', NULL);
?>
        jQuery('#showMessage').hide();
        jQuery('#pop_button').hide();
        jQuery('#syncing_loader1').css('display','block');
        jQuery('#syncing_loader').css('display','block');
        jQuery('#syncing_loader_all').css('display','block'); 
        jQuery('#sync_start').show();
        jQuery('#sync_start').html("<h3>Starting....</h3>"); 
        importProduct(); 
    } 
    var check_error =0;
    function importProduct(){ 
        var ajaxupdate=jQuery.ajax({
            type: "POST",  
            dataType:'json', 
            url: "<?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?>", 
            success:function(dataupper){
                if(dataupper.message!=''){
                    jQuery("#please-wait").css("display", "none");
                    jQuery("#sync_start").show();    
                    jQuery("#sync_start").html("<p style='font-size:15px;'><b>"+dataupper.message+"</b>");  
                    jQuery('#syncing_loader1').css('display','none');
                    jQuery('#syncing_loader').css('display','none');
                    jQuery('#syncing_loader_all').css('display','none'); 
                    jQuery("#pop_up").fadeOut(2000); 
                    jQuery("#pop_up_two-way").hide(1500);
                }else if(dataupper.image_process=='running'){
                    jQuery("#please-wait").css("display", "none"); 
                    uploading_process_start_for_image(dataupper.product_count); 
                }else if(dataupper.image_process=='complete'){
                    jQuery("#please-wait").css("display", "block");
                    importProduct();
                }
            },
            error: function(xhr, status, error) {  
                console.log("Error Empty Response");
                console.log(xhr);
                console.log(status);
                console.log(error);
                if(check_error==10){
                    check_error =0; 
                    ajaxupdate.abort();
                    jQuery("#sync_start").html("<p style='font-size:15px;color:red'><b>Internal Connection Error : Please refresh and try again</b>");  
                    jQuery('#syncing_loader1').css('display','none');
                    jQuery('#syncing_loader').css('display','none');
                    jQuery('#syncing_loader_all').css('display','none'); 
                    jQuery('#syncing_close').css('display','block');
                }else{
                    importProduct(); 
                }
                check_error++;
            },
            statusCode: {
                404: function(){
                    console.log('Got 404 status File not found! '); 
                }, 
                200: function(){
                    // jQuery("#export_report").html(i++);
                }, 
                504:function(){
                    console.log('Got 504 Gateway Time-out! ');  
                }, 
                500:function(){
                    console.log('Got 500 Error ! '); 
                }
            }
               
        }); 
    }
                  
    function ajaxRequestForproduct_image(i,totalreq,total_product,product_count,status){ 
        jQuery("#sync_start").html("linksync update is running.<br> Importing from product <b>"+ (product_count + 1) +" of "+total_product+"</b>");
        var ajaxobj= jQuery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'product_id':i,'communication_key':communication_key,'check_status':status},
            url: '../wp-content/plugins/linksync/image_uploader.php',
            success:function(data){

				if( data ){
					var result=data.response;
					if( result && result.image ){
						if(result.image=='on'){
							if(result.gallery == 'success' && result.thumbnail=='success'){
								status='send';
								i++;
								product_count++;
							}else{
								status='resend';
								console.log('Resend Request for the same product: Process Not complete yet');
							}
						}else{
							status='send';
							i++;
							product_count++;
						}
					}else{
						status='send';
						i++;
						product_count++;
					}
				}
                 
            },
            error: function(xhr, status, error) { 
                status='resend';
                console.log(xhr);
                console.log(status);
                console.log(error);
                console.log('Resend Request for the same product');
            },
            complete:function(responsedata){ 
                if(responsedata){  
                    if(i>totalreq){ 
                        jQuery.ajax({
                            url:  '../wp-content/plugins/linksync/image_uploader.php',
                            type: 'POST',
                            data: { get_total: "1",communication_key:communication_key},
                            success: function(response) { 
                                jQuery("#please-wait").css("display", "block");
                                importProduct();
                            }
                        }); 
                        ajaxobj.abort();
                        return false;
                    }else {
                        console.log(i);
                        ajaxRequestForproduct_image(i,totalreq,total_product,product_count,status);
                    }
                                     
                }  
            },  
            statusCode: {
                404: function(){  
                    console.log('File not Found !'); 
                }, 
                200: function(){
                    // jQuery("#export_report").html(i++);
                }, 
                504:function(){
                    console.log('Got 504 status code in response then request again '); 
                }
            }
        }); 
           
    }
    function uploading_process_start_for_image(product_count) { 
        var dataupper;
        var communication_key='<?php echo get_option('webhook_url_code'); ?>';
        jQuery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'communication_key':communication_key},
            url: '../wp-content/plugins/linksync/image_uploader.php',
            success:function(dataupper){ 
                if(dataupper.total_post_id!=0){ 
                    var totalreq=dataupper.total_post_id;  
                    ajaxRequestForproduct_image(1,totalreq,dataupper.total_product,product_count,'send'); 
                } 
            },error: function(xhr, status, error){  
                console.log('Error');
                console.log(xhr);
                console.log(status);
                console.log(error);
                uploading_process_start_for_image(product_count);
            }
        }); 
    } 
          
        </script> 