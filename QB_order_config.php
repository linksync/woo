 <script> 
    var linksync_OrderjQuery=jQuery.noConflict( true );
    linksync_OrderjQuery(document).ready(function() {
        linksync_OrderjQuery("input[name='order_sync_type_QBO']").click(function() {
            if (linksync_OrderjQuery("#disabled_sync_id").is(":checked")) {
                linksync_OrderjQuery('#order_sync_setting').slideUp(500);
            } else if (linksync_OrderjQuery("#wc_to_QBO_sync_id").is(":checked")) {
                linksync_OrderjQuery('#order_sync_wc_to_QBO').slideDown(500);
                linksync_OrderjQuery('#order_sync_QBO_to_wc').slideUp(500);
                linksync_OrderjQuery('#order_sync_setting').slideDown(500);
            } else if (linksync_OrderjQuery("#QBO_to_wc_sync_id").is(":checked")) {
                linksync_OrderjQuery('#order_sync_QBO_to_wc').slideDown(500);
                linksync_OrderjQuery('#order_sync_wc_to_QBO').slideUp(500);
                linksync_OrderjQuery('#order_sync_setting').slideDown(500);
            } else {
                linksync_OrderjQuery('#order_sync_setting').slideDown(500);
                linksync_OrderjQuery('#order_sync_wc_to_QBO').slideDown(500);
                linksync_OrderjQuery('#order_sync_QBO_to_wc').slideDown(500);
            }
        });
 });
</script><?php
$LAIDKey = get_option('linksync_laid');
$testMode = get_option('linksync_test');
$apicall = new linksync_class($LAIDKey, $testMode);
$gatway = new WC_Payment_Gateways;
$payment = $apicall->linksync_QuickBook_payment();
$taxes = $apicall->linksync_QuickBook_taxes();
$order_Status = $apicall->linksync_get_order_statuses();
if (isset($_POST['save_order_sync_setting'])) {
    //Woocommers To QBO
    if (isset($_POST['order_sync_type_QBO']) && !empty($_POST['order_sync_type_QBO'])) {
        update_option('order_sync_type_QBO', $_POST['order_sync_type_QBO']);
    }
    if (isset($_POST['order_status_wc_to_QBO'])) {
        $order_status_wc_to_QBO = implode('|', $_POST['order_status_wc_to_QBO']);
        update_option('order_status_wc_to_QBO', isset($order_status_wc_to_QBO) ? $order_status_wc_to_QBO : 'off' );
    } else {
        update_option('order_status_wc_to_QBO', 'off');
    }

    if (isset($_POST['wc_to_QBO_tax']) && !empty($_POST['wc_to_QBO_tax'])) {
        $all_taxes = implode(',', $_POST['wc_to_QBO_tax']);
        update_option('wc_to_QBO_tax', $all_taxes);
    } else {
        update_option('wc_to_QBO_tax', 'off');
    }
    if (isset($_POST['wc_to_QBO_payment']) && !empty($_POST['wc_to_QBO_payment'])) {
        $all_payment = implode(',', $_POST['wc_to_QBO_payment']);
        update_option('wc_to_QBO_payment', $all_payment);
    } else {
        update_option('wc_to_QBO_payment', 'off');
    }
    if ($_POST['wc_to_QBO_export']) {
        update_option('wc_to_QBO_export', isset($_POST['wc_to_QBO_export']) ? $_POST['wc_to_QBO_export'] : 'off');
    } else {
        update_option('wc_to_QBO_export', 'off');
    }
    //From QuickBooks To Woocommers
    if (isset($_POST['order_QBO_to_wc'])) {
        update_option('order_QBO_to_wc', isset($_POST['order_QBO_to_wc']) ? $_POST['order_QBO_to_wc'] : 'off');
    } else {
        update_option('order_QBO_to_wc', 'off');
    }


    if (isset($_POST['QBO_to_wc_tax']) && !empty($_POST['QBO_to_wc_tax'])) {
        $all_taxes = implode(',', $_POST['QBO_to_wc_tax']);
        update_option('QBO_to_wc_tax', $all_taxes);
    } else {
        update_option('QBO_to_wc_tax', 'off');
    }

    if (isset($_POST['QBO_to_wc_payments']) && !empty($_POST['QBO_to_wc_payments'])) {
        $payment_QBO = implode(',', $_POST['QBO_to_wc_payments']);

        update_option('QBO_to_wc_payments', $payment_QBO);
    } else {
        update_option('QBO_to_wc_payments', 'off');
    }

    if (isset($_POST['QBO_to_wc_customer'])) {
        update_option('QBO_to_wc_customer', isset($_POST['QBO_to_wc_customer']) ? $_POST['QBO_to_wc_customer'] : 'off');
    } else {
        update_option('QBO_to_wc_customer', 'off');
    }
    //Order Accounts
    if (isset($_POST['order_account']) && !empty($_POST['order_account'])) {
        update_option('order_account', $_POST['order_account']);
    } else {
        update_option('order_account', 'off');
    }//Order classes
    if (isset($_POST['QBO_class']) && !empty($_POST['QBO_class'])) {
        update_option('QBO_class', $_POST['QBO_class']);
    } else {
        update_option('QBO_class', 'off');
    }//Order location
    if (isset($_POST['QBO_locations']) && !empty($_POST['QBO_locations'])) {
        update_option('QBO_locations', $_POST['QBO_locations']);
    } else {
        update_option('QBO_locations', 'off');
    }
    if (isset($_POST['order_sync_type_QBO']) && $_POST['order_sync_type_QBO'] == 'QBO_to_wc-way') {
        // Update "since" 
        $current_date_time_string = strtotime(date("Y-m-d H:i:s"));
        $time_offset = get_option('linksync_time_offset');
        if (isset($time_offset) && !empty($time_offset)) {
            $time = $current_date_time_string + $time_offset;
        } else {
            $time = $current_date_time_string; # UTC 
        }
        $result_time = date("Y-m-d H:i:s", $time);
        #order update  Request time
        update_option('order_time_suc', $result_time);
        update_option('order_import', 'yes');
        // Set Import To Yes on the base of point 31 
        $result = $apicall->testConnection();
        $plugin_file = dirname(__FILE__) . '/linksync.php';
        $plugin_data = get_plugin_data($plugin_file, $markup = true, $translate = true);
        $linksync_version = $plugin_data['Version'];
        $webhook = $apicall->webhookConnection(plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $linksync_version, 'yes', get_option('product_import'));
        if (isset($webhook) && !empty($webhook)) {
            if (isset($webhook['result']) && $webhook['result'] == 'success') {
                $apicall->add('WebHookConnection', 'success', 'Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
                update_option('linksync_addedfile', '<a href="' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '">' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '</a>');
            }
        } else {
            $apicall->add('WebHookConnection', 'fail', 'Order-Config File: Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
        }
    } else {
        update_option('order_import', 'no');
        $result = $apicall->testConnection();
        $plugin_file = dirname(__FILE__) . '/linksync.php';
        $plugin_data = get_plugin_data($plugin_file, $markup = true, $translate = true);
        $linksync_version = $plugin_data['Version'];
        $webhook = $apicall->webhookConnection(plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $linksync_version, 'no', get_option('product_import'));
        if (isset($webhook) && !empty($webhook)) {
            if (isset($webhook['result']) && $webhook['result'] == 'success') {
                $apicall->add('WebHookConnection', 'success', 'Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
                update_option('linksync_addedfile', '<a href="' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '">' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code') . '</a>');
            }
        } else {
            $apicall->add('WebHookConnection', 'fail', 'Order-Config File: Connected to a file ' . plugins_url() . '/linksync/update.php?c=' . get_option('webhook_url_code'), $LAIDKey);
        }
    }
    if ($_POST['order_sync_type_QBO'] != 'disabled') {
        if ($_POST['order_sync_type_QBO'] == 'QBO_to_wc-way') {
            $enable = 'QBO to Woo';
        } else {
            $enable = 'Woo to QBO';
        }
        $setting_message = $enable . ' is enable';
    } else {
        $setting_message = 'Sync Setting Disabled';
    }
    linksync_class::add('Order Sync Setting', 'success', $setting_message, $LAIDKey);
}
?><h3>Order Syncing Configuration</h3>
<form name="save_order_sync_setting" method="post">
    <fieldset>
        <legend>Order Syncing Type</legend> 
        <div>
            <input type="radio" name="order_sync_type_QBO" id="wc_to_QBO_sync_id"  <?php echo (get_option('order_sync_type_QBO') == 'wc_to_QBO' ? 'checked' : ''); ?> value="wc_to_QBO"> WooCommerce to QBO <a href="https://www.linksync.com/help/woocommerce"><img title="If you're using the Vend to WooCommerce product syncing option, then you need to enable this option so that any sales in WooCommerce are synced to Vend - this ensures that the inventory levels in Vend are updated based on any orders entered in WooCommerce. " style="margin-bottom: -4px; " src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
            &nbsp;&nbsp;&nbsp;&nbsp; 
            <input type="radio" name="order_sync_type_QBO" id="QBO_to_wc_sync_id"  <?php echo (get_option('order_sync_type_QBO') == 'QBO_to_wc-way' ? 'checked' : ''); ?> value="QBO_to_wc-way"> QBO to WooCommerce <a href="https://www.linksync.com/help/woocommerce"><img title="If you're using the WooCommerce to Vend product syncing option, then you need to enable this option so that any sales in Vend are synced to WooCommerce - this ensures that the inventory levels in WooCommerce are updated based on any orders entered in Vend."  style="margin-bottom: -4px; " src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
            &nbsp;&nbsp;&nbsp;&nbsp; 
            <input type="radio" name="order_sync_type_QBO" id="disabled_sync_id"  <?php echo (get_option('order_sync_type_QBO') == 'disabled' ? 'checked' : ''); ?> value="disabled"> Disabled <a href="https://www.linksync.com/help/woocommerce"><img title="Prevent any orders syncing between Vend and WooCommerce stores." style="margin-bottom: -4px; " src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
        </div>
    </fieldset>
    <div id="order_sync_setting" style="display:<?php
if (get_option('order_sync_type_QBO') == 'disabled') {
    echo "none";
} else {
    echo "block";
}
?>"> 
        <div id="order_sync_wc_to_QBO" style="display:<?php
         if (get_option('order_sync_type_QBO') == 'wc_to_QBO' || get_option('order_sync_type_QBO') == 'enable') {
             echo "block";
         } else {
             echo "none";
         }
?>">
            <h3>Woo-Commerce to QBO:</h3>
            <!---------------------------------------------------------------------- INFO Check ------------------------------------------------------->
            <?php
            $info = $apicall->linksync_QuickBook_info();
            if (!isset($info['errorCode'])) {
                $response['allow'] = array();
                if (!@$info['allowDiscount']) {
                    $response['allow'][] = "Discount";
                }
                if (!@$info['allowShipping']) {
                    $response['allow'][] = "Shipping";
                }
                if (isset($response['allow']) && !empty($response['allow'])) {
                    $check = implode(" and ", $response['allow']);
                    ?>
                    <div id="message" class="error">
                        <p>Please enable <strong><?php echo $check; ?></strong> in your QuickBooks Online Settings. Select the 'gear' link at the top of the page in your QuickBooks Online Company, and select 'Settings' then 'Sales'. Make your changes and be sure to save, then revisit this page to proceed with enabling order syncing.</p>
                    </div>
                    <?php
                }
            } else {
                ?><span style='color:red; '><?php echo 'Error in getting Info: ' . $info['userMessage'] ?></span><?php
        }
            ?>
            <table class="form-table">
                <tbody>

                    <!------------------------------------------------------------------ Order Status Wc_to_QBO------------------------------------>
                    <tr valign="top">
                        <th  class="titledesc">Order Status<a href="https://www.linksync.com/help/woocommerce"><img title="Use this option to select what status the order must be before it is exported. Keep in mind that an order can only be exported to QBO once, and once exported, it can not be edited in QBO. " style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                        <td class="forminp forminp-checkbox">
                            <?php
                            if (isset($order_Status) && !empty($order_Status)) {
                                foreach ($order_Status as $order_name => $order_value) {
                                    $order_status_db = explode('|', get_option('order_status_wc_to_QBO'));
                                    if (is_array($order_status_db)) {
                                        if (in_array($order_name, $order_status_db)) {
                                            $status = 'checked';
                                        } else {
                                            $status = '';
                                        }
                                    }
                                    ?><div style="margin-top:5px;"><input type="checkbox" <?php
                            echo isset($status) ? $status : '';
                                    ?> value="<?php echo $order_name ?>" name="order_status_wc_to_QBO[]" /><?php echo $order_value ?></div>
                                        <?php
                                    }
                                    ?>
                                <br><?php
                            } else {
                                    ?><span style='color:red; '><?php echo 'Error in getting  Order-Status: No Getting Expecting Data!!<br>' ?></span><?php
                        }
                                ?></td>
                    </tr>
                    <?php
                    if (!isset($info['errorCode'])) {
                        if ($info['version'] == 'QuickBooks Online Plus') {
                            $class = $apicall->linksync_QuickBook_class();
                            if (!isset($class['errorCode'])) {
                                if (isset($class) && !empty($class)) {
                                    $class_db = get_option('QBO_class');
                                    ?> <tr valign="top">
                                        <th  class="titledesc">Class Tracking<a href="https://www.linksync.com/help/woocommerce"><img title="" style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                                        <td class="forminp forminp-checkbox">
                                            <select  style="width: 310px;" name="QBO_class">
                                                <option>Select Class</option>
                                                <?php
                                                foreach ($class['classes'] as $sub_classes) {
                                                    if (isset($class_db) && !empty($class_db)) {
                                                        if ($class_db == $sub_classes['id']) {
                                                            $check_class = 'selected';
                                                        } else {
                                                            $check_class = '';
                                                        }
                                                    }
                                                    echo '<option ' . $check_class . ' value=' . $sub_classes['id'] . '>' . $sub_classes['fullyQualifiedName'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr><?php
                            } else {
                                                ?><span style='color:red; '><?php echo 'Error in getting  Order-Status: No Getting Expecting Data!!<br>' ?></span><?php
                }
            } else {
                                            ?><span style='color:red; '><?php echo 'Error in getting Classes: ' . $info['userMessage'] ?></span><?php
            }

            $location = $apicall->linksync_QuickBook_location();
            if (!isset($location['errorCode'])) {
                if (isset($location) && !empty($location)) {
                    $location_db = get_option('QBO_locations');
                                                ?> <tr valign="top">
                                    <th  class="titledesc">Location Tracking<a href="https://www.linksync.com/help/woocommerce"><img title="" style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                                    <td class="forminp forminp-checkbox">
                                        <select style="width: 310px;" name="QBO_locations">
                                            <option>Select Location</option>
                                            <?php
                                            foreach ($location['locations'] as $sub_location) {
                                                if (isset($location_db) && !empty($location_db)) {
                                                    if ($location_db == $sub_location['id']) {
                                                        $check_location = 'selected';
                                                    } else {
                                                        $check_location = '';
                                                    }
                                                }
                                                echo '<option ' . $check_location . ' value=' . $sub_classes['id'] . '>' . $sub_location['fullyQualifiedName'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr><?php
                        } else {
                                            ?><span style='color:red; '><?php echo 'Error in getting  Order-Status: No Getting Expecting Data!!<br>' ?></span><?php
            }
        } else {
                                        ?><span style='color:red; '><?php echo 'Error in getting Classes: ' . $location['userMessage'] ?></span><?php
            }
        }
    }
                            ?>
                <!------------------------------------------- Order Acccouts --------------------------------------->
                <?php
                $accounts = $apicall->linksync_QuickBook_account();
                if (!isset($accounts['errorCode'])) {
                    if (isset($accounts) && !empty($accounts)) {
                        $accounts_db = get_option('order_account');
                        ?> <tr valign="top">
                            <th  class="titledesc">Account fields<a href="https://www.linksync.com/help/woocommerce"><img title="" style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                            <td class="forminp forminp-checkbox">
                                <select style="width: 310px;" name="order_account">
                                    <option>Select Account fields</option>
                                    <?php
                                    foreach ($accounts['accounts'] as $account) {
                                        if (isset($accounts_db) && !empty($accounts_db)) {
                                            if ($accounts_db == $account['id']) {
                                                $check = 'selected';
                                            } else {
                                                $check = '';
                                            }
                                        }
                                        echo '<option ' . $check . ' value=' . $account['id'] . '>' . $account['fullyQualifiedName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr><?php
                        } else {
                                    ?><span style='color:red; '><?php echo 'Error in getting  Order-Status: No Getting Expecting Data!!<br>' ?></span><?php
            }
        } else {
                                ?><span style='color:red; '><?php echo 'Error in getting Classes: ' . $accounts['userMessage'] ?></span><?php }
                            ?>

                <!------------------------------------------------------------------ Tax Mapping Wc_to_QBO---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Tax mapping<a href="https://www.linksync.com/help/woocommerce"><img title="When syncing orders, both QBO and WooCommerce have their own tax configurations - use these Tax Mapping settings to 'map' the QBO taxes with those in your WooCommerce store." style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                    <td class="forminp forminp-checkbox"><?php
                #Check for the Taxes 
                if (isset($taxes) && !empty($taxes)) {
                    if (!isset($taxes['errorCode'])) {
                        if (isset($taxes['taxes'])) {
                            $taxes_all = explode(',', get_option('wc_to_QBO_tax'));
                                        ?><ul><legend style="display: inline-block;width: 15em; float: left"> <b>Woo-Commerce Tax Classes</b></legend>   <legend style="display: inline-block;width: 3em; float: left">=></legend>  <legend style="display: inline-block;width: 25em; "><b>QBO Taxes</b></legend><br><?php
                        $implode_tax['tax_name'][] = 'standard-tax';
                        $tax_classes_list = array_map("rtrim", explode("\n", get_option('woocommerce_tax_classes')));
                        foreach ($tax_classes_list as $value) {
                            $taxexplode = explode(" ", strtolower($value));
                            $implode_tax['tax_name'][] = implode("-", $taxexplode);
                        }
                        foreach ($implode_tax['tax_name'] as $woo_taxes) {
                                            ?>  <li> <legend style="display: inline-block;width: 15em;float: left"><?php echo $woo_taxes; ?> </legend> 
                                            <legend style="display: inline-block;width: 3em; float: left">=></legend> 
                                            <legend style="display: inline-block;width: 30em; "><select style="margin-top: -5px"name="wc_to_QBO_tax[]">
                                                    <?php
                                                    foreach ($taxes['taxes'] as $select_tax) {
                                                        if (in_array($select_tax['id'] . "/" . $select_tax['name'] . "/" . $select_tax['rate'] . '|' . $woo_taxes, $taxes_all)) {
                                                            $selected = "selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                        echo '<option value="' . $select_tax['id'] . "/" . $select_tax['name'] . "/" . $select_tax['rate'] . '|' . $woo_taxes . '" ' . $selected . '>' . $select_tax['name'] . '</option>';
                                                    }
                                                    ?>
                                                </select></legend> </li>
                                        <?php }
                                        ?></ul><?php } else {
                                        ?><span style='color:red; '><?php echo 'Error in getting Taxes : No Getting Expecting Data!!<br>' ?></span><?php
                    }
                } else {
                                    ?><span style='color:red; '><?php echo 'Error in getting Taxes : ' . $taxes['userMessage'] . '<br>' ?></span><?php
                    }
                } else {
                                ?><span style='color:red; '><?php echo 'Error in getting Taxes : No Getting Expecting Data!!<br>' ?></span><?php
                    }
                            ?>
                    </td>
                </tr>

                <!------------------------------------------------------------------ Payment Mapping Wc_to_QBO---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Payment mapping<a href="https://www.linksync.com/help/woocommerce"><img title="When syncing orders, both QBO and WooCommerce have their own payment methods - use these Payment Mapping settings to 'map' the QBO payment methods with those in your WooCommerce store." style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a> </th>
                    <td class="forminp forminp-checkbox">
                        <?php
#Check for the Payment
                        if (isset($payment) && !empty($payment)) {
                            if (!isset($payment['errorCode'])) {
                                if (isset($payment['payments'])) {
                                    ?>
                                    <?php
                                    $payment_wc_to_QBO = get_option('wc_to_QBO_payment');
                                    $wc_to_QBO_payment = explode(',', $payment_wc_to_QBO);
                                    $payment_gatways = $gatway->payment_gateways();
                                    ?><ul><legend style="display: inline-block;width: 15em; float: left"> <b>QBO Payment</b></legend>   <legend style="display: inline-block;width: 5em; float: left">=></legend>  <legend style="display: inline-block;width: 25em; "><b>Woo-Commerce Payment Gateways</b></legend><br>
                                    <?php foreach ($payment['payments'] as $payment_mapping) {
                                        ?> <li> <legend style="display: inline-block;width:15em; float: left">
                                                <?php echo $payment_mapping['name']; ?></legend>    <legend style="display: inline-block;width: 5em; float: left">=></legend> 
                                            <legend style="display: inline-block;width: 25em; "><select style="margin-top: -5px" name="wc_to_QBO_payment[]">
                                                    <?php
                                                    foreach ($payment_gatways as $gatways) {

                                                        if (in_array($payment_mapping['name'] . "%%" . $payment_mapping['id'] . '|' . $gatways->title . '|' . $gatways->id, $wc_to_QBO_payment)) {
                                                            $selected = "selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                        echo '<option value="' . $payment_mapping['name'] . "%%" . $payment_mapping['id'] . '|' . $gatways->title . '|' . $gatways->id . '" ' . $selected . '>' . $gatways->title . '</option>';
                                                    }
                                                    ?>
                                                </select></legend>
                                        <?php } ?>
                                    </ul>

                                <?php } else {
                                    ?><span style='color:red; '><?php echo 'Error in getting Payment : No Getting Expecting Data!!<br>' ?></span><?php
                    }
                } else {
                                ?><span style='color:red; '><?php echo 'Error in getting Payment : ' . $payment['userMessage'] . '<br>' ?></span><?php
                    }
                } else {
                            ?><span style='color:red; '><?php echo 'Error in getting Payment : No Getting Expecting Data!!<br>' ?></span><?php
                    }
                        ?> 
                    </td>
                </tr>

                <!------------------------------------------------------------------ Customer Wc_to_QBO---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Customer Export</th>
                    <td class="forminp forminp-checkbox">

                <legend style="display: inline-block;width: 25em; "><input type="radio"  checked  value="customer" <?php echo (get_option('wc_to_QBO_export') == 'customer' ? 'checked' : ''); ?> name="wc_to_QBO_export" />Export Customer data<a href="https://www.linksync.com/help/woocommerce"><img title="Select this option if you'd like customer data, such as name, email address and shipping and billing address, to be included when exporting orders to QBO." style="margin-left: 4px;margin-bottom: -3px;"  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a> </legend><br>
                <legend style="display: inline-block;width: 25em; "><input type="radio" value="cash_sale" <?php echo (get_option('wc_to_QBO_export') == 'cash_sale' ? 'checked' : ''); ?>  name="wc_to_QBO_export" />Export as 'Cash Sale' <a href="https://www.linksync.com/help/woocommerce"><img title="Select this option if you're not interested in including the customer information when exporting orders to QBO. " style="margin-left: 4px;margin-bottom: -3px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></legend></td>
                </tr>
                </tbody>
            </table> 
        </div><br>
        <div id="order_sync_QBO_to_wc" style="display:<?php
                        if (get_option('order_sync_type_QBO') == 'QBO_to_wc-way' || get_option('order_sync_type_QBO') == 'enable') {
                            echo "block";
                        } else {
                            echo "none";
                        }
                        ?>">
            <h3>QBO to WooCommerce:</h3> 
            <!----------------------------------------------------------Order QBO To WC----------------------------------------->
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th  class="titledesc">Order Status<a href="https://www.linksync.com/help/woocommerce"><img title="Use this option to select the default status of the order when it's imported. In most cases you will set this to 'Completed'  " style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                        <td class="forminp forminp-checkbox">
                            <?php if (isset($order_Status) && !empty($order_Status)) { ?>

                                <?php
                                foreach ($order_Status as $order_name => $order_value) {
                                    ?> <input type="radio" <?php
                            echo (get_option('order_QBO_to_wc') == $order_name ? 'checked' : '');
                                    ?> value="<?php echo $order_name ?>" name="order_QBO_to_wc" /><?php echo $order_value ?><br>
                                           <?php
                                       }
                                       ?><br><?php
                        } else {
                                       ?><span style='color:red; '><?php echo 'Error in getting  Outlets: No Getting Expecting Data!!<br>' ?></span><?php
                        }
                                   ?></td>
                    </tr>
                    <!------------------------------------------------------------------ Tax Mapping QBO_to_wc---------------------------------------->
                    <tr valign="top">
                        <th  class="titledesc">Tax mapping<a href="https://www.linksync.com/help/woocommerce"><img title="When syncing orders, both QBO and WooCommerce have their own tax configurations - use these Tax Mapping settings to 'map' the QBO taxes with those in your WooCommerce store. " style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                        <td class="forminp forminp-checkbox">
                            <?php
#Check for the Taxes  
                            if (isset($taxes) && !empty($taxes)) {
                                if (!isset($taxes['errorCode'])) {
                                    if (isset($taxes['taxes'])) {
                                        $taxes_all = explode(',', get_option('QBO_to_wc_tax'));
                                        ?><ul><legend style="display: inline-block;width: 20em; float: left"> <b> QBO Taxes</b></legend>   <legend style="display: inline-block;width: 5em; float: left">=></legend>  <legend style="display: inline-block;width: 25em; "><b>Woo-Commerce Tax Classes</b></legend><br>
                                        <?php
                                        $QBO_to_wc_implode_tax['tax_name'][] = 'standard-tax';
                                        $tax_classes_list = array_map("rtrim", explode("\n", get_option('woocommerce_tax_classes')));
                                        foreach ($tax_classes_list as $value) {
                                            $taxexplode = explode(" ", strtolower($value));
                                            $QBO_to_wc_implode_tax['tax_name'][] = implode("-", $taxexplode);
                                        }
                                        foreach ($taxes['taxes'] as $select_tax) {
                                            ?>
                                                <li> <legend style="display: inline-block;width: 20em; float: left"><?php echo $select_tax['name']; ?> </legend> 
                                                <legend style="display: inline-block;width: 5em; float: left">=></legend> 
                                                <legend style="display: inline-block;width: 25em; "><select style="margin-top: -5px" name="QBO_to_wc_tax[]">
                                                        <?php
                                                        foreach ($QBO_to_wc_implode_tax['tax_name'] as $tax) {
                                                            if (in_array($select_tax['id'] . '|' . $tax, $taxes_all)) {
                                                                $selected = "selected";
                                                            } else {
                                                                $selected = "";
                                                            }
                                                            echo '<option value="' . $select_tax['id'] . '|' . $tax . '" ' . $selected . '>' . $tax . '</option>';
                                                        }
                                                        ?>
                                                    </select></legend></li><?php } ?> </ul></td>
                                    <?php
                                } else {
                                    ?><span style='color:red; '><?php echo 'Error in getting Taxes : No Getting Expecting Data!!<br>' ?></span><?php
                                }
                            } else {
                                ?><span style='color:red; '><?php echo 'Error in getting Taxes : ' . $taxes['userMessage'] . '<br>' ?></span><?php
            }
        }
                        ?>
                </td>
                </tr>
                <!------------------------------------------------------------------ Payment Mapping QBO_to_wc---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Payment mapping<a href="https://www.linksync.com/help/woocommerce"><img title="When syncing orders, both QBO and WooCommerce have their own payment methods - use these Payment Mapping settings to 'map' the QBO payment methods with those in your WooCommerce store." style="margin-bottom: -4px;margin-left: 4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></th>
                    <td class="forminp forminp-checkbox">
                        <?php
#Check for the Payment
                        if (isset($payment) && !empty($payment)) {
                            if (!isset($payment['errorCode'])) {
                                if (isset($payment['payments'])) {
                                    $all_payment = get_option('QBO_to_wc_payments');
                                    $payment_all = explode(',', $all_payment);
                                    $payment_gatways = $gatway->payment_gateways();
                                    ?><ul><legend style="display: inline-block;width: 15em; float: left"> <b>QBO Payment</b></legend>  
                                        <legend style="display: inline-block;width: 5em; float: left">=></legend> 
                                        <legend style="display: inline-block;width: 25em; "><b>Woo-Commerce Payment Gateways</b></legend><br>
                                        <?php foreach ($payment['payments'] as $payment_mapping) {
                                            ?><li> <legend style="display: inline-block;width: 15em; float: left"><?php echo $payment_mapping['name']; ?></legend> 
                                            <legend style="display: inline-block;width: 5em; float: left">=></legend> 
                                            <legend style="display: inline-block;width: 25em; "><select style="margin-top: -5px" name="QBO_to_wc_payments[]">
                                                    <?php
                                                    foreach ($payment_gatways as $gatways) {
                                                        if (in_array($payment_mapping['name'] . '|' . $gatways->title, $payment_all)) {
                                                            $selected = "selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                        echo '<option value="' . $payment_mapping['name'] . '|' . $gatways->title . '" ' . $selected . '>' . $gatways->title . '</option>';
                                                    }
                                                    ?>
                                                </select></legend>
                                            </li><?php } ?></ul>
                                <?php } else {
                                    ?><span style='color:red; '><?php echo 'Error in getting Payment : No Getting Expecting Data!!<br>' ?></span><?php
                        }
                    } else {
                                ?><span style='color:red; '><?php echo 'Error in getting Payment : ' . $payment['userMessage'] . '<br>' ?></span><?php
                    }
                }
                        ?>  </td>
                </tr>
                <tr valign="top">
                    <th  class="titledesc">Customer Import </th>
                    <td class="forminp forminp-checkbox">
                <legend style="display: inline-block;width: 25em; ">  <input type="radio"  checked  <?php echo (get_option('QBO_to_wc_customer') == 'customer_data' ? 'checked' : ''); ?> value="customer_data" name="QBO_to_wc_customer" />Import Customer data<a href="https://www.linksync.com/help/woocommerce"><img title="Select this option if you'd like customer data, such as name, email address and shipping and billing address, to be included when importing orders from QBO."   style="margin-left: 4px;margin-bottom: -3px;"src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a></legend><br>
                <legend style="display: inline-block;width: 25em; "><input type="radio" <?php echo (get_option('QBO_to_wc_customer') == 'guest' ? 'checked' : ''); ?> value="guest" name="QBO_to_wc_customer" />Import as 'Guest'<a href="https://www.linksync.com/help/woocommerce"><img title="Select this option if you're not interested in including the customer information when importing orders from QBO." style="margin-left: 4px;margin-bottom: -3px;"  src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a><br/><br/>
                </legend>
                </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
    <p class="wc_t" style="text-align: center;"><input class="button button-primary button-large" type="submit" name="save_order_sync_setting" value="Save Changes"></p>

</form> 