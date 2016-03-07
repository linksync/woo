<?php
$apicall = new linksync_class($LAIDKey, $testMode);
if (isset($_POST['save_product_sync_setting'])) {
    if (isset($_POST['product_sync_type_QBO']) && !empty($_POST['product_sync_type_QBO'])) {
        update_option('product_sync_type_QBO', $_POST['product_sync_type_QBO']);
    }

    if (isset($_POST['ps_name_title'])) {
        if ($_POST['ps_name_title'] == 'on') {
            update_option('ps_name_title', $_POST['ps_name_title']);
        } else {
            update_option('ps_name_title', 'off');
        }
    } else {
        update_option('ps_name_title', 'off');
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
    // Price 
    if (isset($_POST['ps_price'])) {
        if ($_POST['ps_price'] == 'on') {
            update_option('ps_price', $_POST['ps_price']);
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
        } else {
            update_option('ps_price', 'off');
            update_option('excluding_tax', 'off');
            update_option('tax_mapping', 'off');
        }
    } else {
        update_option('ps_price', 'off');
        update_option('excluding_tax', 'off');
        update_option('tax_mapping', 'off');
        update_option('tax_class', 'off');
    }
    // Quantity
    if (isset($_POST['ps_quantity'])) {
        if ($_POST['ps_quantity'] == 'on') {
            update_option('ps_quantity', 'on');
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
// Account
    if (isset($_POST['ps_account_asset']) && !empty($_POST['ps_account_asset'])) {
        update_option('ps_account_asset', $_POST['ps_account_asset']);
    } else {
        update_option('ps_account_asset', 'off');
    }
    if (isset($_POST['ps_account_expense']) && !empty($_POST['ps_account_expense'])) {
        update_option('ps_account_expense', $_POST['ps_account_expense']);
    } else {
        update_option('ps_account_expense', 'off');
    }
    if (isset($_POST['ps_account_revenue']) && !empty($_POST['ps_account_revenue'])) {
        update_option('ps_account_revenue', $_POST['ps_account_revenue']);
    } else {
        update_option('ps_account_revenue', 'off');
    }
    //Brands
    if (@$_POST['ps_brand'] == 'on') {
        update_option('ps_brand', 'on');
    } else {
        update_option('ps_brand', 'off');
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
    update_option('prod_update_suc', NULL);
    update_option('prod_last_page', NULL);
    update_option('product_detail', NULL);
    if ($_POST['product_sync_type_QBO'] == 'two_way' || $_POST['product_sync_type_QBO'] == 'QB_to_wc-way') {
        // Set Import To Yes on the base of point 31 
        update_option('product_import', 'yes');
        $result = $apicall->testConnection();
        $plugin_file = dirname(__FILE__) . '/linksync.php';
        $plugin_data = get_plugin_data($plugin_file, $markup = true, $translate = true);
        $linksync_version = $plugin_data['Version'];
        $webhook = $apicall->webhookConnection(plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $linksync_version, get_option('order_import'), 'yes');
        if (isset($webhook) && !empty($webhook)) {
            if (isset($webhook['result']) && $webhook['result'] == 'success') {
                $apicall->add('WebHookConnection', 'success', 'Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
                update_option('linksync_addedfile', '<a href="' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '">' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '</a>');
            }
        } else {
            $apicall->add('WebHookConnection', 'fail', 'Product-Config File: Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
        }
    } else {
        update_option('product_import', 'no');
        $result = $apicall->testConnection();
        $plugin_file = dirname(__FILE__) . '/linksync.php';
        $plugin_data = get_plugin_data($plugin_file, $markup = true, $translate = true);
        $linksync_version = $plugin_data['Version'];
        $webhook = $apicall->webhookConnection(plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $linksync_version, get_option('order_import'), 'no');
        if (isset($webhook) && !empty($webhook)) {
            if (isset($webhook['result']) && $webhook['result'] == 'success') {
                $apicall->add('WebHookConnection', 'success', 'Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
                update_option('linksync_addedfile', '<a href="' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '">' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '</a>');
            }
        } else {
            $apicall->add('WebHookConnection', 'fail', 'Product File: Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
        }
    }
    if ($_POST['product_sync_type_QBO'] == 'two_way') {
        ?>     <div id="pop_up_two-way" class="clientssummarybox" style=" width:600px !important; top: 24% !important; display: none;  z-index: 9999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 25%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
            <a name="lnkViews" href=""><img style="height: 13px;float: right;"src="../wp-content/plugins/linksync/img/cross_icon.png"></a><center><div id="showMessage"><center><h4>Your changes will require a full re-sync of product data.</h4></center>
                    <center><h4>Do you want to re-sync now?</h4></center></div></center> 
            <center><h4 style="display:none;" id="syncing_loader_all"><img src="../wp-content/plugins/linksync/img/ajax-loader.gif"></h4></center> 
            <center><h4 id="sync_start"></h4><h4 id="sync_start_export_product"></h4></center>
            <div id="pop_button"><input type="button" title="This option will update product in your WooCommerce store with Product data from QBO. " style="color: green; margin-left: 97px; width: 138px; font-weight: 900;float: left;margin-bottom: 12px;"  class="button"   onclick="return re_sync_process_start2();"  value="Product from QBO"> 
                <input type="button" title="This option will update product in your QBO store with the Product data from WooCommerce." style="color: green; margin-left: 130px; width: 130px; font-weight: 900;float: left;"  class="button"   onclick="return sync_process_start();"  value="Product to QBO">
            </div> </div> 
        <script>
            linksynProduct_jquery(window).load(function() {
                linksynProduct_jquery('#pop_up_two-way').fadeIn(500);   
            });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
        </script><?php
    }
    if ($_POST['product_sync_type_QBO'] == 'QB_to_wc-way') {
        ?>     <div id="pop_up" class="clientssummarybox" style=" width:600px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 25%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
            <center><div id="showMessage"><center><h4>Your changes will require a full re-sync of product data from QBO.</h4></center>
                    <center><h4>Do you want to re-sync now?</h4></center></div></center> 
            <center><h4 style="display:none;" id="syncing_loader"><img src="../wp-content/plugins/linksync/img/ajax-loader.gif"></h4></center> 
            <center><h4 id="sync_start"></h4></center>
            <div id="pop_button"><input type="button" style="color: green; margin-left: 168px; width: 90px; font-weight: 900;float: left;"  class="button"   onclick="return re_sync_process_start2();"  value="Yes"> 
                <input  type="button" class="button" style="color: red;
                        margin-left: 83px;
                        width: 90px;font-weight: 900;"  name="close"   value='No'/></div> </div> 
        <script>
            linksynProduct_jquery(window).load(function() {
                linksynProduct_jquery('#pop_up').fadeIn(500);  
            });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
        </script><?php
    }
    if ($_POST['product_sync_type_QBO'] != 'disabled_sync') {
        if ($_POST['product_sync_type_QBO'] == 'QB_to_wc-way') {
            $enable = 'QBO to Woo';
        } elseif ($_POST['product_sync_type_QBO'] == 'two_way') {
            $enable = 'Two way';
        } else {
            $enable = 'Woo to QBO';
        }
        $setting_message = $enable . '  enable';
    } else {
        $setting_message = 'Sync Setting Disabled';
    }
    linksync_class::add('Product Sync Setting', 'success', $setting_message, $LAIDKey);
} elseif (isset($_POST['sync_reset_btn'])) {
    update_option('prod_update_suc', NULL);
    update_option('prod_last_page', NULL);
    update_option('product_detail', NULL);
    ?> 
    <div id="pop_up" class="clientssummarybox" style=" width:600px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 25%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
        <center><div id="showMessage"><center><h4>Your changes will require a full re-sync of product data from QBO.</h4></center>
                <center><h4>Do you want to re-sync now?</h4></center></div>  </center>  
        <center><h4 style="display:none;" id="syncing_loader"><img src="../wp-content/plugins/linksync/img/ajax-loader.gif"></h4></center> 
        <center><h4 id="sync_start"></h4></center>
        <div id="pop_button"><input type="button" style="color: green; margin-left: 168px; width: 90px; font-weight: 900;float: left;"  class="button"   onclick="return re_sync_process_start2();"  value="Yes"> 
            <input  type="button" class="button" style="color: red;  margin-left: 83px;  width: 90px;font-weight: 900;"  name="no"   value='No'/></div></div>  <script>
                linksynProduct_jquery(window).load(function() {
                    linksynProduct_jquery('#pop_up').fadeIn(500); 
                }); 
    </script><?php
}
?>    <h3>Product Syncing Configuration</h3>
<?php
if (isset($message) && !empty($message)) {
    if ($message['result'] == 'error') {
        ?><script>
            linksynProduct_jquery(window).load(function() {
                linksynProduct_jquery('#response').removeClass('updated').addClass('error').html("<?php echo $message['message']; ?>").fadeIn(500).delay(3000).fadeOut(4000);
            });
        </script><?php
    }
}
?>
<form method="post" name="options">
    <fieldset>
        <legend>Product Syncing Type</legend> 
        <p>
            <input <?php echo (get_option('product_sync_type_QBO') == 'two_way' ? 'checked' : ''); ?> type="radio" name="product_sync_type_QBO"  value="two_way"> Two-way <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;" title="Data is kept in sync between both systems, so changes to products and inventory can be made in either your WooCommerce or QB online store and those changes will be synced to the other store within a few moments."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>

            &nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" <?php echo (get_option('product_sync_type_QBO') == 'QB_to_wc-way' ? 'checked' : ''); ?> name="product_sync_type_QBO" value="QB_to_wc-way"> QBO to WooCommerce <a  href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;" title="QBO is the 'master' when it comes to managing product and inventory, and product updates are one-way, from QBO to WooCommerce - product and inventory data does not update back to QBO from WooCommerce. You must enable Order Syncing from WooCommerce to QBO for this option to work correctly. " src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>

            &nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" <?php echo (get_option('product_sync_type_QBO') == 'wc_to_QB' ? 'checked' : ''); ?> name="product_sync_type_QBO" value="wc_to_QB"> WooCommerce to QBO <a  href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;" title="WooCommerce is the 'master' when it comes to managing product and inventory, and product updates are one-way, from WooCommerce to QBO - product and inventory data does not update back to WooCommerce to QBO. You must enable Order Syncing from QBO to WooCommerce for this option to work correctly. " src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>

            &nbsp;&nbsp; &nbsp;&nbsp;  <input type="radio" <?php echo (get_option('product_sync_type_QBO') == 'disabled_sync' ? 'checked' : ''); ?> name="product_sync_type_QBO" value="disabled_sync" id="disabled_sync_id"> Disabled <a  href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;" title="Prevent any product syncing from taking place between your QBO and WooCommerce stores. " src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
        </p>

    </fieldset>
    <div style="display:<?php
if (get_option('product_sync_type_QBO') == 'disabled_sync') {
    echo "none";
}
?>"  id="product_sync_settig">

        <p>    <input type="submit" name="sync_reset_btn" title="Selecting the Sync Reset button resets linksync to update all WooCommerce products with data from QBO, based on your existing Product Sync Settings."  value="Sync Reset" id="sync_reset_btn_id" class="button button-primary" style="display:<?php
         if (get_option('product_sync_type_QBO') == 'wc_to_QB') {
             echo "none";
         }
?>" name="sync_reset"/> 
            <input id="sync_reset_all_btn_id" type="button" title="Selecting this option will sync your entire WooCommerce product catalogue to QBO, based on your existing Product Sync Settings. It takes 3-5 seconds to sync each product, depending on the performance of your server, and your geographic location."  onclick="show_confirm_box();" value="Sync all product to QBO" style="display:<?php
                      if (get_option('product_sync_type_QBO') == 'QB_to_wc-way') {
                          echo "none";
                      }
?>" class="button button-primary" />
        </p> 
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th  class="titledesc">Name/Title  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, Product titles will be kept in sync. In WooCommerce this is the product Name and in QBO it's the Product name. "  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a> </th>
                    <td class="forminp forminp-checkbox">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_name_title') == 'on' ? 'checked' : ''); ?> value="on" name="ps_name_title" />  Sync the product titles between apps </label>
                    </td>
                </tr> 

                <tr valign="top">
                    <th scope="row" class="titledesc">Price<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, prices will be kept in sync. "  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a> </th>
                    <td class="forminp forminp-checkbox">
                        <label style="display: inline-block;width: 25em;">
                            <input type="checkbox" <?php echo (get_option('ps_price') == 'on' ? 'checked' : ''); ?> value="on" name="ps_price" />Sync prices between apps </label>
                        <br> <br> <div style="margin-left: 40px;" ><span  class="ps_price_sub_options"> 
                                <b>WooCommerce price field to sync</b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select which WooCommerce price field you want to sync with QBO sell price."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
                                <br><br>
                                <div  style="margin-left: 23px;">
                                    <input type="radio" name="price_field" <?php echo (get_option('price_field') == 'regular_price' ? 'checked' : ''); ?> value="regular_price">Regular Price
                                    <input style="margin-left:20px;" type="radio" <?php echo (get_option('price_field') == 'sale_price' ? 'checked' : ''); ?> name="price_field" value="sale_price">Sale Price<br><br> 
                                </div>
                                <br>
                                <?php
                                if (get_option('woocommerce_calc_taxes') == 'yes') {
                                    ?>
                                    <label style="display: inline-block;width: 20px;">   <input type="checkbox"  value="on" <?php echo (get_option('linksync_woocommerce_tax_option') == 'on' ? 'checked' : ''); ?> name="linksync_woocommerce_tax_option" /></label>Use WooCommerce Tax Options <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Recommended - this option uses the WooCommerce Tax Options settings to determine if your prices are inclusive or exclusive of tax when syncing with Vend. You should only need to disable this option if you have altered the standard tax settings in Vend."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
                                    <div id="linksync_taxes" style="margin-left: 25px;"><br><b>Treat prices in QBO as</b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When syncing prices with Vend, should linksync treat the Vend price as inclusive or exclusive of tax. Which option you select will depend on whether your prices in WooCommerce include tax or not."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
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
                                    ?> <b>Treat prices in QBO as</b><a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When syncing prices with Vend, should linksync treat the Vend price as inclusive or exclusive of tax. Which option you select will depend on whether your prices in WooCommerce include tax or not."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
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
                                ?><br>  <legend class="ps_price_sub_options" style="display: inline-block;width: 25em;"><b>Tax Mapping<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When syncing products, both QBO and WooCommerce have their own tax configurations - use these Tax Mapping settings to 'map' the QBO taxes with those in your WooCommerce store. Note that the mapping is used to specify the Tax Class for a product in WooCommerce, and the Sales tax for a product in QBO, depending on which Product Syncing Type you select. "  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></b></legend>
                                <p style="margin-left: 23px;" class="description ps_price_sub_options">To set the relevant tax rate for a product in WooCommerce.  
                                </p> <?php
                                $taxes = $apicall->linksync_QuickBook_taxes();
                                $taxes_all = explode(',', get_option('tax_class'));
                                if (isset($taxes) && !empty($taxes)) {
                                    if (!isset($taxes['errorCode'])) {
                                        if (isset($taxes['taxes'])) {
                                            ?>
                                            <div style="margin-left: 23px;">
                                                <ul>
                                                    <legend class="ps_price_sub_options" style="display: inline-block;width: 20em; float: left"> <b>QBO Taxes</b></legend> 
                                                    <legend class="ps_price_sub_options" style="display: inline-block;width: 5em; float: left">=></legend>  
                                                    <legend class="ps_price_sub_options" style="display: inline-block;width: 25em; "><b>Woo-Commerce Tax Classes</b></legend>
                                                    <br><?php
                                $tax_classes_list = array_map("rtrim", explode("\n", get_option('woocommerce_tax_classes')));
                                $implode_tax['tax_name'][] = 'standard-tax';
                                foreach ($tax_classes_list as $value) {
                                    $taxexplode = explode(" ", strtolower($value));
                                    $implode_tax['tax_name'][] = implode("-", $taxexplode);
                                }
                                foreach ($taxes['taxes'] as $select_tax) {
                                                ?>
                                                        <li> <legend class="ps_price_sub_options" style="display: inline-block;width: 20em;  float: left"><?php echo $select_tax['name']; ?> </legend> 
                                                        <legend class="ps_price_sub_options" style="display: inline-block;width: 5em; float: left">=></legend> 
                                                        <legend class="ps_price_sub_options" style="display: inline-block;width: 25em; "><select style="margin-top: -5px"name="tax_class[]">
                                                                <?php
                                                                foreach ($implode_tax['tax_name'] as $tax) {

                                                                    if (in_array($select_tax['name'] . '-' . $select_tax['rateValue'] . '|' . $tax, $taxes_all)) {
                                                                        $selected = "selected";
                                                                    } else {
                                                                        $selected = "";
                                                                    }
                                                                    echo '<option value="' . $select_tax['name'] . '-' . $select_tax['rateValue'] . '|' . $tax . '" ' . $selected . '>' . $tax . '</option>';
                                                                }
                                                                ?>
                                                            </select></legend></li>
                                                    <?php }
                                                    ?></ul></div><?php
                                        } else {
                                            echo "<span style='margin-left:50px;color:red;font-weight:bold;'>Error in getting Taxes : Not Getting Expecting Data !!</span><br>";
                                        }
                                    } else {
                                        echo "<span style='margin-left:50px;color:red;font-weight:bold;'>Error in getting Taxes : $taxes[userMessage]</span><br>";
                                    }
                                }
                                        ?>

                                </legend>  
                            </span></div>
                    </td>
                </tr>
                <?php
                $info = $apicall->linksync_QuickBook_info();
                if (!isset($info['errorCode'])) {
                    if ($info['version'] == 'QuickBooks Online Plus') {
                        ?> <tr valign="top">
                            <th  class="titledesc">Quantity  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="When enabled, product quantities will be kept in sync. "  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                            <td class="forminp forminp-checkbox">
                                <label style="display: inline-block;width: 25em;">
                                    <input type="checkbox" <?php echo (get_option('ps_quantity') == 'on' ? 'checked' : ''); ?> value="on" name="ps_quantity" />Sync product Quantity between apps</label>
                                <div  id="unpublish_stock_id" style="margin-top:5px;display:<?php
                if (get_option('product_sync_type_QBO') == 'wc_to_QB') {
                    echo "none";
                }
                        ?>;margin-left: 45px;margin-top:10px;"><input type="checkbox"  <?php echo (get_option('ps_unpublish') == 'on' ? 'checked' : ''); ?> value="on" name="ps_unpublish"  />Change product status in WooCommerce based on stock quantity<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select this option if you want product with inventory quantities of 0 (zero) or less to be made unavailable for purchase in your WooCommerce store. In the case of simple product this option will set them them to 'draft', and in the case of Variable products, the variation would be set to 'Out of stock'."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
                                </div> 
                            </td>
                        </tr><?php
                          }
                      } else {
                          echo "<span style='margin-left:50px;color:red;font-weight:bold;'>Error in getting Info : $info[userMessage]</span><br>";
                      }

                      $accounts = $apicall->linksync_QuickBook_account('Asset');
                      if (!isset($accounts['errorCode'])) {
                          $accounts_db = get_option('ps_account_asset');
                    ?> 
                    <tr valign="top" class="account" style="display:<?php echo (get_option('product_sync_type_QBO') == 'QB_to_wc-way' ? 'none' : ''); ?>">
                        <th  class="titledesc">Asset Accounts fields  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Accounts fields"  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                        <td class="forminp forminp-checkbox">
                            <label style="display: inline-block;width: 25em;">
                                <select name="ps_account_asset">
                                    <?php
                                    foreach ($accounts['accounts'] as $account) {
                                        if ($accounts_db == $account['id']) {
                                            $check = 'selected';
                                        } else {
                                            $check = '';
                                        }
                                        ?>

                                        <option <?php echo $check; ?> value='<?php echo $account['id']; ?>'><?php echo $account['fullyQualifiedName']; ?></option>
                                    <?php } ?>

                                </select>
                            </label>
                        </td> 
                    </tr>
                    <?php
                } else {
                    echo "<span style='margin-left:50px;color:red;font-weight:bold;'>Error in getting Info : $accounts[userMessage]</span><br>";
                }
                $accounts_expense = $apicall->linksync_QuickBook_account('Expense');
                if (!isset($accounts_expense['errorCode'])) {
                    $accounts_db_expense = get_option('ps_account_expense');
                    ?> <tr valign="top" class="account" style="display:<?php echo (get_option('product_sync_type_QBO') == 'QB_to_wc-way' ? 'none' : ''); ?>">
                        <th  class="titledesc">Expense Accounts fields  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Accounts fields"  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                        <td class="forminp forminp-checkbox">
                            <label style="display: inline-block;width: 25em;">
                                <select name="ps_account_expense">
                                    <?php
                                    foreach ($accounts_expense['accounts'] as $account_expense) {
                                        if ($accounts_db_expense == $account_expense['id']) {
                                            $check = 'selected';
                                        } else {
                                            $check = '';
                                        }
                                        ?>
                                        <option <?php echo $check; ?> value='<?php echo $account_expense['id']; ?>'><?php echo $account_expense['fullyQualifiedName']; ?></option>
                                    <?php } ?>

                                </select>
                            </label>
                        </td>
                    </tr><?php
                            } else {
                                echo "<span style='margin-left:50px;color:red;font-weight:bold;'>Error in getting Info : $accounts_expense[userMessage]</span><br>";
                            }
                            $accounts_revenue = $apicall->linksync_QuickBook_account("Revenue");
                            if (!isset($accounts_revenue['errorCode'])) {
                                $accounts_db_revenue = get_option('ps_account_revenue');
                                    ?> <tr valign="top" class="account" style="display:<?php echo (get_option('product_sync_type_QBO') == 'QB_to_wc-way' ? 'none' : ''); ?>">
                        <th  class="titledesc">Revenue Accounts fields  <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Accounts fields"  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                        <td class="forminp forminp-checkbox">
                            <label style="display: inline-block;width: 25em;">
                                <select name="ps_account_revenue">
                                    <?php
                                    foreach ($accounts_revenue['accounts'] as $account_revenue) {
                                        if ($accounts_db_revenue == $account_revenue['id']) {
                                            $check = 'selected';
                                        } else {
                                            $check = '';
                                        }
                                        ?>
                                        <option <?php echo $check; ?> value='<?php echo $account_revenue['id']; ?>'><?php echo $account_revenue['fullyQualifiedName']; ?></option>
                                    <?php } ?>

                                </select>
                            </label>
                        </td>
                    </tr><?php
                            } else {
                                echo "<span style='margin-left:50px;color:red;font-weight:bold;'>Error in getting Info : $accounts_revenue[userMessage]</span><br>";
                            }
                                ?>


                <?php if (in_array('woocommerce-brands/woocommerce-brands.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    ?>
                    <tr valign="top" class="woocommerce_frontend_css_colors">
                        <th scope="row" class="titledesc">Brand<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Enable this option to keep the Brand fields in sync between WooCommerce and QBO."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                        <td class="forminp">
                            <label style="display: inline-block;width: 25em;">
                                <input type="checkbox" <?php echo (get_option('ps_brand') == 'on' ? 'checked' : ' '); ?> value="on" name="ps_brand" />Brand</label>
                        </td>
                    </tr>
                <?php } ?>

                <tr style="display:<?php echo (get_option('product_sync_type_QBO') == 'wc_to_QB' ? 'none' : ''); ?>" id="ps_pending" valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Product Status <a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-right: 10px" title="Enable this option if you want newly created product in QBO to be set to 'Pending Review' so that you can review and update new product before they are published in your WooCommerce store."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>

                    <td class="forminp">
                        <!--aman changes-->
                        <input name="ps_pending" type="checkbox" value="on"  <?php echo (get_option('ps_pending') == 'on' ? 'checked' : ''); ?> />Tick this option to Set new product to <b>Pending</b>.<br><br>
                    </td>

                </tr>  
                <tr style="margin-top:5px;display:<?php
                if (get_option('product_sync_type_QBO') == 'wc_to_QB') {
                    echo "none";
                }
                ?>;" id="ps_create_tr" valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Create New<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Select this option if you want 'new' products from QBO created in WooCommerce automatically. If this option is not enabled, then new products will not be created in WooCommerce - you will need to manually create them, after which, they will be kept in sync. "  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a> </th>
                    <td class="forminp">  <input type="checkbox" <?php echo (get_option('ps_create_new') == 'on' ? 'checked' : ''); ?> value="on" name="ps_create_new" />Create new products from QBO <br>
                    </td>
                </tr>
                <tr valign="top" class="woocommerce_frontend_css_colors">
                    <th scope="row" class="titledesc">Delete<a href="https://www.linksync.com/help/woocommerce"><img style="margin-bottom:-4px;margin-left:4px" title="Caution - use with care - deleted products can not be recovered. Select this option if you want product permanently deleted. Depending on which Product Syncing Type you select, if products are deleted in one store, they will immediately be deleted from the other."  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a> </th>
                    <td class="forminp">   <input type="checkbox" <?php echo (get_option('ps_delete') == 'on' ? 'checked' : ''); ?> value="on" name="ps_delete" />Delete<br>
                    </td>
                </tr>

                </div>
            </tbody>
        </table></div>
    <p style="text-align: center;"><input  class="button button-primary button-large save_changes" type="submit"  name="save_product_sync_setting" value="Save Changes" /></p>
</form>

<div id="pop_up_syncll" class="clientssummarybox" style="width: 500px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 34%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
    <center><h4 style="display:none;" id="syncing_loader"><img src="../wp-content/plugins/linksync/img/ajax-loader.gif"></h4></center>
    <center><div id="total_product"></div></center>
    <center><p id="export_report"></p></center>
    <center><h4 id="sync_start_export1"></h4></center>
    <center><h4 id="sync_start_export"></h4></center>

    <form target="" method="post" action=""><input type="button" onclick="return sync_process_start();"  name="sync_all_product_to_vend" style="color: green;
                                                   margin-left: 118px;
                                                   width: 90px;
                                                   font-weight: 900;float: left;" target="_blank" class="button hidesync"  onclick="return linksynProduct_jquery('#pop_up_').fadeOut();"  value="Yes"></form>

    <input  type="button" class="button hidesync" style="color: red;
            margin-left: 83px;
            width: 90px;font-weight: 900;"  name="close_syncall"  onclick="linksynProduct_jquery('#pop_up_syncll').fadeOut();"  value='No'/></div>   
<script type="text/javascript"> 
    function  show_confirm_box() { 
        if(linksynProduct_jquery("#pop_up_syncll").is(":visible")==false && linksynProduct_jquery("#pop_up_two-way").is(":visible")==false && linksynProduct_jquery("#pop_up").is(":visible")==false){
            linksynProduct_jquery(document).ready(function() {
                linksynProduct_jquery('.hidesync').show(); 
                linksynProduct_jquery('#sync_start_export1').show();
                linksynProduct_jquery("#sync_start_export1").html('Do you want to sync all product to QuickBooks Online?'); 
                linksynProduct_jquery('#pop_up_syncll').fadeIn();   });
<?php update_option('post_product', 0); ?>
      
        }
    } 
    var request_time=3000;
    var communication_key='<?php echo get_option('webhook_url_code'); ?>';
    var mycounter=1;  
    function ajaxRequestForproduct(i,totalreq){ 
        var ajaxobj= linksynProduct_jquery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'offset':i,'communication_key':communication_key},
            url: '../wp-content/plugins/linksync/sync_all_product_to_QBO.php',
            success:function(data){ 
                console.log(data);
            },
            complete:function(responsedata){ 
                if(responsedata){ 
                    var incounter=mycounter++;
                    linksynProduct_jquery('#sync_start_export_product').show();
                    linksynProduct_jquery('#sync_start_export_product').html("<p style='font-size:15px;'><b>"+incounter+"</b> of <b>"+totalreq+"</b> product(s) exporting..</p>");
                    linksynProduct_jquery("#sync_start_export1").show();    
                    linksynProduct_jquery("#sync_start_export1").html("<p style='font-size:15px;'><b>"+incounter+"</b> of <b>"+totalreq+"</b> product(s) exporting..</p>");
                    if(incounter>=totalreq){ 
                        linksynProduct_jquery.ajax({
                            url:  '../wp-content/plugins/linksync/sync_all_product_to_QBO.php',
                            type: 'POST',
                            data: { "get_total": "1",'communication_key':communication_key},
                            success: function(response) { 
                            }
                        });
                        linksynProduct_jquery("#show-result").show();   
                        linksynProduct_jquery("#show-result").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                        linksynProduct_jquery("#show-result_all").show();   
                        linksynProduct_jquery("#show-result_all").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                        linksynProduct_jquery('#syncing_loader1').hide(); 
                        linksynProduct_jquery("#pop_up_syncll").hide(1500); 
                        linksynProduct_jquery("#pop_up_two-way").hide(1500); 
                        linksynProduct_jquery('#sync_start_export1').hide();
                        linksynProduct_jquery('#sync_start_export_product').hide();
                        linksynProduct_jquery("#show-result").hide(1500);  
                        linksynProduct_jquery("#show-result_all").hide(1500); 
                        linksynProduct_jquery("#sync_start_export_all").show(1500);
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
                    // linksynProduct_jquery("#export_report").html(i++);
                }, 
                504:function(){
                    console.log('Got 504 status code in response then request again '); 
                }
            }
        }); 
        i++;
    }
    function sync_process_start() {
    
        linksynProduct_jquery('#showMessage').hide();
        linksynProduct_jquery('#pop_button').hide(); 
        linksynProduct_jquery('#sync_start_export_all').hide();  
        linksynProduct_jquery('#syncing_loader_all').css("display", "block");
        linksynProduct_jquery('#sync_start_export_product').show();
        linksynProduct_jquery('#sync_start_export_product').html("<h3>Starting....</h3>");
        linksynProduct_jquery('#syncing_loader').css("display", "block");
        linksynProduct_jquery('#sync_start_export1').show();
        linksynProduct_jquery('#sync_start_export1').html("<h3>Starting....</h3>");
        var dataupper;
        var communication_key='<?php echo get_option('webhook_url_code'); ?>';
        linksynProduct_jquery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'communication_key':communication_key},
            url: '../wp-content/plugins/linksync/sync_all_product_to_QBO.php',
            success:function(dataupper){ 
                if(dataupper.total_post_id!=0){ 
                    var totalreq=dataupper.total_post_id;  
                    ajaxRequestForproduct(1,totalreq);
                   
                }else{
                    linksynProduct_jquery("#show-result").show();   
                    linksynProduct_jquery("#show-result").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                    linksynProduct_jquery("#show-result_all").show();   
                    linksynProduct_jquery("#show-result_all").html("<p style='font-size:15px;'><b>Completed!</b></p>");
                    linksynProduct_jquery('#syncing_loader1').css("display", "none");
                    linksynProduct_jquery('#syncing_loader_all').css("display", "none");
                    linksynProduct_jquery("#pop_up_syncll").hide(1500);  
                    linksynProduct_jquery("#pop_up_two-way").hide(1500);
                    linksynProduct_jquery('#sync_start_export1').hide();
                    linksynProduct_jquery('#sync_start_export_product').hide();
                    linksynProduct_jquery("#show-result").hide(1500);
                    linksynProduct_jquery("#show-result_all").hide(1500); 
                    linksynProduct_jquery("#sync_start_export_all").show(1500);
                }
            }
        }); 
        linksynProduct_jquery('.hidesync').hide();
         
    } 
 
    linksynProduct_jquery(document).ready(function() {
        linksynProduct_jquery("input[name='product_sync_type_QBO']").click(function() {
            if (linksynProduct_jquery("#disabled_sync_id").is(":checked")) {
                linksynProduct_jquery('#product_sync_settig').slideUp(500);
            } else {
                linksynProduct_jquery('#product_sync_settig').slideDown(500);
            }
            if (linksynProduct_jquery("input[value='QB_to_wc-way']").is(":checked")) {
                linksynProduct_jquery('#sync_reset_all_btn_id').hide(500);
                if (linksynProduct_jquery("input[name='ps_quantity']").is(":checked")) {
                    linksynProduct_jquery("#unpublish_stock_id").fadeIn(500);
                } else {
                    linksynProduct_jquery("#unpublish_stock_id").fadeOut(500);
                }
                linksynProduct_jquery('#import_by_tags_tr').fadeIn(500);
            } else {
                linksynProduct_jquery('#sync_reset_all_btn_id').show(500);
            }
            if (linksynProduct_jquery("input[value='two_way']").is(":checked")) {
                if (linksynProduct_jquery("input[name='ps_quantity']").is(":checked")) {
                    linksynProduct_jquery("#unpublish_stock_id").fadeIn(500);
                } else {
                    linksynProduct_jquery("#unpublish_stock_id").fadeOut(500);
                }
                linksynProduct_jquery('#import_by_tags_tr').fadeIn(500);
            }
            if (linksynProduct_jquery("input[value='wc_to_QB']").is(":checked")) {
                linksynProduct_jquery('#sync_reset_btn_id').hide(500);
                linksynProduct_jquery('#ps_cat_id_p').fadeOut(500);
                linksynProduct_jquery('#ps_import_image_id').fadeOut(500);
                linksynProduct_jquery("input[name='ps_create_new_p']").fadeOut(500);
                linksynProduct_jquery('#ps_create_tr').fadeOut(500);
                linksynProduct_jquery('#ps_pending').fadeOut(500);
              
                linksynProduct_jquery("#unpublish_stock_id").fadeOut(500); 
                linksynProduct_jquery('#import_by_tags_tr').fadeOut(500);
            } else {
                linksynProduct_jquery('#sync_reset_btn_id').show(500);
                linksynProduct_jquery("input[name='ps_create_new_p']").fadeIn(500);
                linksynProduct_jquery('#ps_cat_id_p').fadeIn(500);
                linksynProduct_jquery('#ps_import_image_id').fadeIn(500);
                linksynProduct_jquery('#ps_create_tr').fadeIn(500);
                linksynProduct_jquery('#ps_pending').fadeIn(500);
               
            }

            if (linksynProduct_jquery("input[value='two_way']").is(":checked") || linksynProduct_jquery("input[value='wc_to_QB']").is(":checked")) {
                linksynProduct_jquery('.account').fadeIn(500);
            } else {   
                linksynProduct_jquery('.account').fadeOut(500); 
            } 

        });
       
        linksynProduct_jquery("input[name='ps_price']").click(function() {
            if (linksynProduct_jquery(this).is(":checked")) {
                linksynProduct_jquery(".ps_price_sub_options").fadeIn(500);
            } else {
                linksynProduct_jquery(".ps_price_sub_options").fadeOut(500);
            }
        });
        if (linksynProduct_jquery("input[name='ps_price']").is(":checked")) {
            linksynProduct_jquery(".ps_price_sub_options").fadeIn(500);
        } else {
            linksynProduct_jquery(".ps_price_sub_options").fadeOut(500);
        }
        linksynProduct_jquery("input[name='ps_imp_by_tag']").click(function() {
            if (linksynProduct_jquery(this).is(":checked")) {
                linksynProduct_jquery("#import_by_tags_list").fadeIn(500);
            } else {
                linksynProduct_jquery("#import_by_tags_list").fadeOut(500);
            }
        }); 
    });
    if (linksynProduct_jquery("input[name='linksync_woocommerce_tax_option']").is(":checked")) {  
        linksynProduct_jquery('#linksync_taxes').fadeOut(500);
    } else { 
        linksynProduct_jquery('#linksync_taxes').fadeIn(500); 
    }
    linksynProduct_jquery("input[name='linksync_woocommerce_tax_option']").click(function() {
        if (linksynProduct_jquery("input[name='linksync_woocommerce_tax_option']").is(":checked")) {
            linksynProduct_jquery("#linksync_taxes").fadeOut(500);
        } else {  
            linksynProduct_jquery("#linksync_taxes").fadeIn(500);
        }
    });
    if (linksynProduct_jquery("#ps_quantity").is(":checked")) { 
        if (linksynProduct_jquery("input[value='two_way']").is(":checked")||linksynProduct_jquery("input[value='wc_to_vend']").is(":checked")){
            if(linksynProduct_jquery("input[value='two_way']").is(":checked")) {
                linksynProduct_jquery("#unpublish_stock_id").fadeIn(500);   
            }else{
                linksynProduct_jquery("#unpublish_stock_id").fadeOut(500); 
            } 
        }else if (linksynProduct_jquery("input[value='vend_to_wc-way']").is(":checked")) {
            linksynProduct_jquery("#unpublish_stock_id").fadeIn(500);  
        }
    } else {  
        linksynProduct_jquery("#unpublish_stock_id").fadeOut(500);   
    } 
    linksynProduct_jquery("input[name='ps_quantity']").click(function() {
        if (linksynProduct_jquery(this).is(":checked")) { 
            if (linksynProduct_jquery("input[value='two_way']").is(":checked")||linksynProduct_jquery("input[value='wc_to_vend']").is(":checked")){
                if(linksynProduct_jquery("input[value='two_way']").is(":checked")) {
                    linksynProduct_jquery("#unpublish_stock_id").fadeIn(500);   
                }else{
                    linksynProduct_jquery("#unpublish_stock_id").fadeOut(500); 
                } 
            }else if (linksynProduct_jquery("input[value='vend_to_wc-way']").is(":checked")) {
                linksynProduct_jquery("#unpublish_stock_id").fadeIn(500);  
            } 
        } else { 
            linksynProduct_jquery("#unpublish_stock_id").fadeOut(500);  
        } 
             
    });
    $(document).on("click","a[name='lnkViews']", function (e) {  
        $('#sync_reset_all_btn_id').attr('disabled',true);
        $('#sync_reset_btn_id').attr('disabled',true); 
        linksynProduct_jquery("#pop_up_two-way").fadeOut(500); 
        linksynProduct_jquery('#response').removeClass('error').addClass('updated').html("Changes has been saved successfully!!").fadeIn(500).delay(3000).fadeOut(4000);
        location.reload();      
    });
    linksynProduct_jquery("input[name='close']").click(function() {  
        linksynProduct_jquery("#pop_up").fadeOut(200);
        linksynProduct_jquery('#response').removeClass('error').addClass('updated').html("Changes has been saved successfully!!").fadeIn(500).delay(3000).fadeOut(4000);

    });
    linksynProduct_jquery("input[name='no']").click(function() {
        linksynProduct_jquery("#pop_up").fadeOut(500);
        linksynProduct_jquery('#response').removeClass('error').addClass('updated').html("Synchronic Reset successfully!!").fadeIn(500).delay(3000).fadeOut(4000);

    }); 
    function  re_sync_process_start2() {
        linksynProduct_jquery('#showMessage').hide();
        linksynProduct_jquery('#pop_button').hide();
        linksynProduct_jquery('#syncing_loader_all').css("display", "block");
        linksynProduct_jquery('#syncing_loader').css("display", "block"); 
        linksynProduct_jquery('#sync_start').show();
        linksynProduct_jquery('#sync_start').html("<h3>Starting....</h3>"); 
        importProduct();
      
    }
    function importProduct(){
        var communication_key='<?php echo get_option('webhook_url_code'); ?>'; 
        var test='on';
        linksynProduct_jquery.ajax({
            type: "POST",  
            dataType:'json', 
            url: "<?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?>", 
            success:function(dataupper){ 
                linksynProduct_jquery("#sync_start").show();    
                linksynProduct_jquery("#sync_start").html("<p style='font-size:15px;'><b>"+dataupper.message+"</b>"); 
                test='off'; 
                clearInterval(myVar);
                linksynProduct_jquery('#syncing_loader').css("display", "none"); 
                linksynProduct_jquery('#syncing_loader_all').css("display", "none");
                linksynProduct_jquery("#pop_up").fadeOut(2000); 
                linksynProduct_jquery("#pop_up_two-way").hide(1500);
            },
            error: function(xhr, status, error) {  
                console.log("Error Empty Response");
                importProduct(); 
            },
            statusCode: {
                404: function(){
                    console.log('Got 404 status File not found! '); 
                }, 
                200: function(){ 
                    
                }, 
                504:function(){
                    console.log('Got 504 Gateway Time-out! ');  
                }, 
                500:function(){
                    console.log('Got 500 Error ! '); 
                }
            }
                      
        });
        if(test=='on'){
            var myVar=  setInterval(function(){ 
                linksynProduct_jquery.ajax({
                    type: "POST",  
                    dataType:'json', 
                    data:{'communication_key':communication_key},
                    url: "../wp-content/plugins/linksync/report.php",
                    success:function(data){  
                        linksynProduct_jquery("#sync_start").html("linksync update is running.<br> Importing from product <b>"+data.total_product+"</b>");
                    }});    
            },2000);
        } 

    } 
          
</script> 