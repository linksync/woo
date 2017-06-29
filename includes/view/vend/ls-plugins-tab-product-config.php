<?php
$saving_sync_type = null;
?>
<h3>Product Syncing Configuration</h3>
<form method="post" id="frmProductSyncingSettings" name="options">
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

        <p>
            <input type="button" name="sync_reset_btn" title="Selecting the Sync Reset button resets linksync to update all WooCommerce products with data from Vend, based on your existing Product Sync Settings."  value="Sync all products from Vend" id="sync_reset_btn_id" class="button button-primary btn-sync-vend-to-woo" style="display:<?php
            if ($product_sync_type == 'wc_to_vend') {
                echo "none";
            }
            ?>" name="sync_reset"/>
            <input id="sync_reset_all_btn_id" type="button" title="Selecting this option will sync your entire WooCommerce product catalogue to Vend, based on your existing Product Sync Settings. It takes 3-5 seconds to sync each product, depending on the performance of your server, and your geographic location." value="Sync all products to Vend" style="display:<?php
            if ($product_sync_type == 'vend_to_wc-way') {
                echo "none";
            }
            ?>" class="button button-primary btn-sync-woo-to-vend" />
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
                                $taxes = LS_Vend()->api()->getTaxes();
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
                                /**
                                 * Get the previously saved vend outlets information on LS_Vend_Laid class in the method check_api_key
                                 */
                                $outlets = LS_Vend()->option()->get_vend_outlets();
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
                                $product_tags = LS_Vend()->api()->getTags();
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

<div class="ls-vend-sync-modal">
    <div class="ls-vend-sync-modal-content"
         style="display: none; width: 500px !important; top: 24% !important;   z-index: 9999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 34%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">

        <div class="ls-modal-close-container" style="<?php echo ('two_way' == $saving_sync_type) ? '': 'display: none;'; ?>">
            <div class="ui-icon ui-icon-close ls-modal-close btn-no" style="width: 16px;height: 17px;float: right;"></div>
        </div>

        <center>
            <h4 id="sync_start_export_all" class="sync-modal-message">Your changes will require a full re-sync of product data. <br/>Do you want to re-sync now?<br/> </h4>
        </center>

        <div id="sync_progress_container" style="display: none;">

            <center>
                <br/>
                <div id="syncing_loader">
                    <p style="font-weight: bold;">Please do not close or refresh the browser while syncing is in progress.</p>
                </div>
            </center>
            <center>
                <div>
                    <div id="progressbar"></div>
                    <div class="progress-label">Loading...</div>
                </div>
                <?php
                if(isset($_GET['page']) && LS_Vend::$slug != $_GET['page']){
                    ?>
                    <p class="form-holder hide ls-dashboard-link" >
                        <a href="<?php echo LS_Vend_Menu::menu_url(); ?>" class="a-href-like-button">Go To Dashboard</a>
                    </p>
                    <?php
                }
                ?>
            </center>
            <br/>

        </div>

        <div id="pop_button">

            <div class="sync-buttons two-way-sync-vend-buttons"  style="width: 330px;"  >

                <input type="button"
                       title="This option will update product in your WooCommerce store with Product data from Vend. "
                       class="button product_sync_to_woo btn-yes"
                       style="width: 145px;"
                       value="Product from Vend">

                <input type="button"
                       title="This option will update product in your Vend store with the Product data from WooCommerce."
                       class="button product_sync_to_vend btn-yes "
                       style="width: 145px;"
                       value='Product to Vend'/>
            </div>

            <div class="sync-buttons sync-to-vend-buttons" style="display: none;">
                <input type="button" name="sync_all_product_to_vend" class="button hidesync product_sync_to_vend btn-yes" value="Yes">
                <input type="button" class="button hidesync ls-modal-close btn-no ls-modal-close"  name="close_syncall" value='No'/>
            </div>

            <div class="sync-buttons sync-to-woo-buttons" <?php echo ('vend_to_wc-way' == $saving_sync_type) ? '': 'style="display: none;"'; ?>">
            <input type="button" class="button product_sync_to_woo btn-yes" value="Yes">
            <input type="button" class="button btn-no ls-modal-close"  name="no" value='No'/>
        </div>

        <div class="sync-buttons sync-to-woo-buttons-since-last-update" style="display: none;">
            <input type="button" class="button product_sync_to_woo_since_last_sync btn-yes" value="Yes">
            <input type="button" class="button btn-no ls-modal-close"  name="no" value='No'/>
        </div>
    </div>


</div>

<div class="ls-modal-backdrop close" ></div>
</div>

<script type="text/javascript">
    jQuery(".outlets_check").change(function () {
        if (jQuery('.outlets_check:checked').length == 0) {
            jQuery("#check_outlets").show();
            jQuery("#check_outlets").html('You didn\'t select any outlet !');
            return false;
        } else {
            jQuery("#check_outlets").hide();
            return true;
        }
    });

    function show_confirm_box() {
        if (jQuery("#pop_up_syncll").is(":visible") == false && jQuery("#pop_up_two-way").is(":visible") == false && jQuery("#pop_up").is(":visible") == false) {
            jQuery(document).ready(function () {
                jQuery('.hidesync').show();
                jQuery('#sync_start_export').show();
                jQuery("#sync_start_export").html('Do you want to sync all product to Vend?');
                jQuery('#pop_up_syncll').fadeIn();
            });
            <?php update_option('post_product', 0); ?>

        }
    }

    jQuery(document).ready(function () {
        jQuery("input[name='product_sync_type']").click(function () {
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
            if (jQuery("#ps_quantity").is(":checked")) {
                if (jQuery("input[value='two_way']").is(":checked") || jQuery("input[value='wc_to_vend']").is(":checked")) {
                    if (jQuery("input[value='two_way']").is(":checked")) {
                        jQuery("#unpublish_stock_id").fadeIn(500);
                    } else {
                        jQuery("#unpublish_stock_id").fadeOut(500);
                    }
                    jQuery("#wctovend_outlet").fadeIn(500);
                } else if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
                    jQuery("#unpublish_stock_id").fadeIn(500);
                    jQuery("#outlet").fadeIn(500);
                    jQuery("#wctovend_outlet").fadeOut(500);
                }
            } else {
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
                if (jQuery('.outlets_check:checked').length == 0) {
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

        jQuery("input[name='ps_description']").click(function () {
            if (jQuery("input[name='ps_description']").is(":checked")) {
                jQuery("#ps_desc_span").fadeIn(500);
            } else {
                jQuery("#ps_desc_span").fadeOut(500);
            }
        });

        jQuery("input[name='ps_price']").click(function () {
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
        jQuery("input[name='ps_imp_by_tag']").click(function () {
            if (jQuery(this).is(":checked")) {
                jQuery("#import_by_tags_list").fadeIn(500);
            } else {
                jQuery("#import_by_tags_list").fadeOut(500);
            }
        });

        jQuery("input[name='ps_quantity']").click(function () {
            if (jQuery(this).is(":checked")) {
                if (jQuery("input[value='two_way']").is(":checked") || jQuery("input[value='wc_to_vend']").is(":checked")) {
                    if (jQuery("input[value='two_way']").is(":checked")) {
                        jQuery("#unpublish_stock_id").fadeIn(500);
                    } else {
                        jQuery("#unpublish_stock_id").fadeOut(500);
                    }
                    jQuery("#wctovend_outlet").fadeIn(500);
                } else if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
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
            if (jQuery("input[value='two_way']").is(":checked") || jQuery("input[value='wc_to_vend']").is(":checked")) {
                if (jQuery("input[value='two_way']").is(":checked")) {
                    jQuery("#unpublish_stock_id").fadeIn(500);
                } else {
                    jQuery("#unpublish_stock_id").fadeOut(500);
                }
                jQuery("#wctovend_outlet").fadeIn(500);
            } else if (jQuery("input[value='vend_to_wc-way']").is(":checked")) {
                jQuery("#unpublish_stock_id").fadeIn(500);
                jQuery("#outlet").fadeIn(500);
                jQuery("#wctovend_outlet").fadeOut(500);
            }
        } else {
            jQuery("#outlet").fadeOut(500);
            jQuery("#unpublish_stock_id").fadeOut(500);
            jQuery("#wctovend_outlet").fadeOut(500);
        }
        jQuery("input[name='ps_wc_to_vend_outlet']").click(function () {
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
        jQuery("input[name='ps_categories']").click(function () {
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
        jQuery("input[name='linksync_woocommerce_tax_option']").click(function () {
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
        jQuery("input[name='ps_images']").click(function () {
            if (jQuery("input[name='ps_images']").is(":checked")) {
                jQuery(".ps_images").fadeIn(500);
            } else {
                jQuery(".ps_images").fadeOut(500);
            }
        });
    });

    jQuery(document).on("click", "a[name='lnkViews']", function (e) {
        jQuery('#sync_reset_all_btn_id').attr('disabled', true);
        jQuery('#sync_reset_btn_id').attr('disabled', true);
        jQuery("#pop_up_two-way").fadeOut(500);
        jQuery('#response').removeClass('error').addClass('updated').html("Changes has been saved successfully!!").fadeIn(500).delay(3000).fadeOut(4000);
        location.reload();
    });
    jQuery("input[name='close']").click(function () {
        jQuery("#pop_up").fadeOut(200);
        jQuery('#response').removeClass('error').addClass('updated').html("Changes has been saved successfully!!").fadeIn(500).delay(3000).fadeOut(4000);

    });

    (function ($) {

        $(document).ready(function () {

            var $mainContainer = $('#ls-main-wrapper');

            $mainContainer.on('submit', '#frmProductSyncingSettings', function (e) {
                var $tabMenu = $('.ls-tab-menu');
                var $frm = $('#frmProductSyncingSettings');


                $tabMenu.before('<div class="ls-loading open"></div>');

                var data = {
                    action: 'vend_save_product_syncing_settings',
                    post_array: $frm.serialize()
                };

                lsVendSyncModal.close(1);
                lsAjax.post(data).done(function (response) {
                    $mainContainer.find('.ls-loading').fadeOut('fast');
                    console.log(response);
                    if (response.sync_type == 'two_way') {
                        lsVendSyncModal.openTwoWaySyncModal();
                    } else if (response.sync_type == 'vend_to_wc-way') {
                        lsVendSyncModal.openVendToWooSyncModal();
                    } else if (response.sync_type == 'wc_to_vend') {
                        lsVendSyncModal.openWooToVendSyncModal();
                    }

                });
                e.preventDefault();
            });
        });

    })(jQuery);
</script>