<?php
$LAIDKey = LS_Vend()->laid()->get_current_laid();

$gatway = new WC_Payment_Gateways;
# Get Payment Types
for ($count_payment = 1; $count_payment <= 3; $count_payment++) {
    $payment = LS_Vend()->api()->getPaymentTypes();
    if (isset($payment) && !empty($payment)) {
        break;
    }
}
# Get Taxes
for ($count_taxes = 1; $count_taxes <= 3; $count_taxes++) {
    $taxes = LS_Vend()->api()->getTaxes();
    if (isset($taxes) && !empty($taxes)) {
        break;
    }
}
#Get Order Status
for ($count_order_Status = 1; $count_order_Status <= 3; $count_order_Status++) {
    $order_Status = LS_Vend_Order_Helper::get_woo_order_statuses();
    if (isset($order_Status) && !empty($order_Status)) {
        break;
    }
}

?>

<form id="frmOrderSyncingSettings" name="save_order_sync_setting" method="post" class="ls-wrap">
    <br/>
    <table class="wp-list-table widefat fixed">
        <thead>
        <tr>
            <td><strong>Order Syncing Type</strong></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <p>
                    <label>
                        <input type="radio" name="order_sync_type" id="wc_to_vend_sync_id"  <?php echo (get_option('order_sync_type') == 'wc_to_vend' ? 'checked' : ''); ?> value="wc_to_vend"> WooCommerce to Vend
                        <?php
                        help_link(array(
                            'title' => "If you're using the Vend to WooCommerce product syncing option, then you need to enable this option so that any sales in WooCommerce are synced to Vend - this ensures that the inventory levels in Vend are updated based on any orders entered in WooCommerce. "
                        ));
                        ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </label>

                    <label>
                        <input type="radio" name="order_sync_type" id="vend_to_wc_sync_id"  <?php echo (get_option('order_sync_type') == 'vend_to_wc-way' ? 'checked' : ''); ?> value="vend_to_wc-way"> Vend to WooCommerce
                        <?php
                        help_link(array(
                            'title' => "If you're using the WooCommerce to Vend product syncing option, then you need to enable this option so that any sales in Vend are synced to WooCommerce - this ensures that the inventory levels in WooCommerce are updated based on any orders entered in Vend."
                        ));
                        ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </label>

                    <label>
                        <input type="radio" name="order_sync_type" id="disabled_sync_id"  <?php echo (get_option('order_sync_type') == 'disabled' ? 'checked' : ''); ?> value="disabled"> Disabled
                        <?php
                        help_link(array(
                            'title' => "Prevent any orders syncing between Vend and WooCommerce stores."
                        ));
                        ?>
                    </label>

                </p>
            </td>
        </tr>
        </tbody>
    </table>

    <div id="order_sync_setting" style="display:<?php
    if (get_option('order_sync_type') == 'disabled') {
        echo "none";
    } else {
        echo "block";
    }
    ?>">
        <div id="order_sync_wc_to_vend" style="display:<?php
        if (get_option('order_sync_type') == 'wc_to_vend' || get_option('order_sync_type') == 'enable') {
            echo "block";
        } else {
            echo "none";
        }
        ?>">
            <h3>WooCommerce to Vend:</h3>

            <!------------------------------------------------------------------ Order Status Wc_to_Vend------------------------------------>
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th  class="titledesc">Order Date
                        <?php
                        help_link(array(
                            'title' => "Use this option to select what date the order must be sync before it is exported. Keep in mind that an order can only be exported to Vend once, and once exported, it can not be edited in Vend. "
                        ));
                        ?>
                    </th>
                    <td class="forminp forminp-checkbox">
                        <select name="order_date_wc_to_vend">
                            <option value="current_date" <?php echo ((get_option('order_date_wc_to_vend')=='current_date')?'selected="selected"':''); ?>> Current Date </option>
                            <option value="order_date" <?php echo ((get_option('order_date_wc_to_vend')=='order_date')?'selected="selected"':''); ?>> Order Date </option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th  class="titledesc">Order Status
                        <?php
                        help_link(array(
                            'title' => "Use this option to select what status the order must be before it is exported. Keep in mind that an order can only be exported to Vend once, and once exported, it can not be edited in Vend. "
                        ));
                        ?>
                    </th>
                    <td class="forminp forminp-checkbox">
                        <?php
                        if (isset($order_Status) && !empty($order_Status)) {
                            if (isset($order_Status['wc-failed'])) {
                                unset($order_Status['wc-failed']);
                            }
                            foreach ($order_Status as $order_name => $order_value) {
                                ?><input type="radio"
                                <?php echo(get_option('order_status_wc_to_vend') == $order_name ? 'checked' : ''); ?>
                                         value="<?php echo $order_name ?>"
                                         name="order_status_wc_to_vend" /><?php echo $order_value ?>  <br>
                                <?php
                            }
                            ?>
                            <br><?php
                        } else {
                            ?>
                            <span style='color:red; '><?php echo 'Error in getting  Order-Status: No Getting Expecting Data!!<br>' ?></span><?php
                        }
                        ?>
                    </td>
                </tr>
                <!------------------------------------------------------------------ Outlet Wc_to_Vend------------------------------------>
                <tr valign="top">
                    <?php
                    # Get Outlets
                    for ($count_outlets = 1; $count_outlets <= 3; $count_outlets++) {
                        /**
                         * Get the previously saved vend outlets information on LS_Vend_Laid class in the method check_api_key
                         */
                        $linksync_outlets = LS_Vend()->option()->get_vend_outlets();
                        if (isset($linksync_outlets) && !empty($linksync_outlets)) {
                            break;
                        }
                    }
                    # Get Registers
                    for ($count_registers = 1; $count_registers <= 3; $count_registers++) {
                        $registers = LS_Vend()->api()->getRegisters();
                        if (isset($registers) && !empty($registers)) {
                            break;
                        }
                    }
                    # Get Users
                    for ($count_users = 1; $count_users <= 3; $count_users++) {
                        $users = LS_Vend()->api()->getUsers();
                        if (isset($users) && !empty($users)) {
                            break;
                        }
                    }
                    $registerDb = get_option('wc_to_vend_register');
                    $outletDb = get_option('wc_to_vend_outlet');
                    $userDb = get_option('wc_to_vend_user');
                    // echo $registerDb;echo "<br>";echo $outletDb;echo "<br>";echo $userDb;
                    #Check for the Outlets
                    if (isset($linksync_outlets) && !empty($linksync_outlets)) {
                    if (!isset($linksync_outlets['errorCode'])) {
                    if (isset($linksync_outlets['outlets'])) {
                    if (!empty($outletDb)) {
                        ?>
                        <th  class="titledesc">Outlets
                            <?php
                            help_link(array(
                                'title' => "If you have multiple Outlets in Vend, use this option to select which Outlet you want orders from WooCommerce to be associated with. If you have multiple registers and users for an outlet, you can also choose which register and/or user you want the orders to be imported against. "
                            ));
                            ?>
                        </th>
                        <td class="forminp forminp-checkbox"><?php
                        foreach ($linksync_outlets['outlets'] as $outlet) {
                            ?>
                            <input type="radio" class="outlet_class" <?php
                            echo($outletDb == $outlet['id'] ? 'checked' : '');
                            ?> value="<?php echo $outlet['id'] ?>" name="wc_to_vend_outlet" /><?php echo $outlet['name'] ?>
                            <span class="check" id="<?php echo $outlet['id']; ?>">  <?php
                                if (!empty($registerDb)) {
                                    ?>
                                    <?php if (count($registers['registers']) > 1) {
                                        ?><br><b style="margin-left:40px;float: left;">Register :-</b><?php
                                        $display_register = 'display:block;';
                                    } else {
                                        $display_register = 'display:none;';
                                    }
                                    $checkRegister = 0;
                                    foreach ($registers['registers'] as $register) {
                                        if ($outlet['id'] == $register['outlet_id']) {
                                            if ($outlet['id'] != $outletDb) {
                                                if ($checkRegister == 0) {
                                                    $checked = "checked";
                                                    $checkRegister++;
                                                } else {
                                                    $checked = '';
                                                }
                                            } else {
                                                $checked = '';
                                            }
                                            ?>  <div style="margin-left:20px;<?php echo $display_register; ?>"><input style="margin-top: 2px;" type="radio"  <?php
                                                if (isset($checked))
                                                    echo $checked . ' ';
                                                echo ( $registerDb == $register['id'] ? 'checked' : '');
                                                ?> value="<?php echo $register['id'] ?>" name="wc_to_vend_register|<?php echo $outlet['id'] ?>" /><?php echo $register['name'] ?>  </div>
                                            <?php
                                        }
                                    }
                                }
                                if (!empty($userDb)) {
                                    if (count($users['users']) > 1) {
                                        ?><br><b style="margin-left:40px;">User :-</b><?php
                                        $display_user = 'display:block;';
                                    } else {
                                        $display_user = 'display:none;';
                                    }
                                    $checkUser = 0;
                                    foreach ($users['users'] as $user) {
                                        if ($outlet['id'] == @$user['outlet_id'] OR @$user['outlet_id'] == null) {
                                            if ($outlet['id'] != $outletDb) {
                                                if ($checkUser == 0) {
                                                    $checked = "checked";
                                                    $checkUser++;
                                                } else {
                                                    $checked = '';
                                                }
                                            } else {
                                                $checked = '';
                                            }
                                            ?>
                                            <div style="margin-left:20px;<?php echo $display_user; ?>">
                                                <input type="radio" style="margin-top: 2px;"  <?php
                                                if (isset($checked))
                                                    echo $checked . ' ';
                                                echo ( $userDb == $user['id'] . '|' . $user['username'] ? 'checked' : '');
                                                ?> value="<?php echo $user['id'] . '|' . $user['username']; ?>" name="wc_to_vend_user|<?php echo $outlet['id'] ?>"  /><?php echo $user['name'] . "(<i>" . $user['username'] . "</i>)"; ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?></span><br> <?php
                        }
                        ?></td><?php
                    } else {
                    ?>
                    <th  class="titledesc">Outlets
                        <?php
                        help_link(array(
                            'title' => "If you have multiple Outlets in Vend, use this option to select which Outlet you want orders from WooCommerce to be associated with. If you have multiple registers and users for an outlet, you can also choose which register and/or user you want the orders to be imported against. "
                        ));
                        ?>
                    </th>
                    <td  class="forminp forminp-checkbox"><?php
                        $checkOutlet = 0;
                        foreach ($linksync_outlets['outlets'] as $outlet) {
                            if ($checkOutlet == 0) {
                                $checked = "checked";
                            } else {
                                $checked = '';
                            }
                            ?>
                            <br> <input type="radio"  class="outlet_class" <?php echo $checked; ?> value="<?php echo $outlet['id'] ?>" name="wc_to_vend_outlet" /><?php echo $outlet['name'] ?>

                            <span class="check" id="<?php echo $outlet['id']; ?>">
                                                    <?php
                                                    #Check for the Register
                                                    if (isset($registers) && !empty($registers)) {
                                                        if (!isset($registers['errorCode'])) {
                                                            if (isset($registers['registers'])) {
                                                                if (count($registers['registers']) > 1) {
                                                                    ?><br><b style="margin-left:40px;">Register :-</b><?php
                                                                    $display_register = 'display:block;';
                                                                } else {
                                                                    $display_register = 'display:none;';
                                                                }

                                                                $checkRegister = 0;
                                                                foreach ($registers['registers'] as $register) {
                                                                    if ($outlet['id'] == $register['outlet_id']) {
                                                                        if ($checkRegister == 0) {
                                                                            $checked = "checked";
                                                                            $checkRegister++;
                                                                        } else {
                                                                            $checked = '';
                                                                        }
                                                                        ?><div style="margin-left:20px;<?php echo $display_register; ?>"><input style="margin-top: 2px;" type="radio" <?php echo $checked;
                                                                        ?> value="<?php echo $register['id'] ?>"  name="wc_to_vend_register|<?php echo $outlet['id'] ?>" /><?php echo $register['name'] ?>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                            } else {
                                                                ?><span style='color:red; '><?php echo 'Error in getting  Register: No Getting Expecting Data!!' ?></span><?php
                                                            }
                                                        } else {
                                                            ?><span style='color:red; '><?php echo 'Error in getting Register : ' . $registers['userMessage'] ?></span><?php
                                                        }
                                                    } else {
                                                        ?><span style='color:red; '><?php echo 'Error in getting  Register: No Getting Expecting Data!!' ?></span><?php
                                                    }
                                                    ?>

                                <?php
                                #Check For the User
                                if (isset($users) && !empty($users)) {
                                    if (!isset($users['errorCode'])) {
                                        if (isset($users['users'])) {
                                            #-------------Changes---------------#

                                            if (count($users['users']) > 1) {
                                                ?><br><b style="margin-left:40px;">User :-</b><?php
                                                $display = 'display:block;';
                                            } else {
                                                $display = 'display:none;';
                                            }
                                            $i = 0;
                                            foreach ($users['users'] as $user) {

                                                if ($outlet['id'] == $user['outlet_id'] OR $user['outlet_id'] == null) {
                                                    if ($i == 0) {
                                                        $checked = "checked";
                                                        $i++;
                                                    } else {
                                                        $checked = '';
                                                    }
                                                    ?> <div style="margin-left:20px;<?php echo $display; ?>"><input style="margin-top: 2px;" type="radio"  <?php echo $checked; ?> value="<?php echo $user['id'] . '|' . $user['username']; ?>" name="wc_to_vend_user|<?php echo $outlet['id'] ?>"  /><?php echo $user['name'] . "(<i>" . $user['username'] . "</i>)" ?></div>
                                                    <?php
                                                }
                                            }
                                            #-------------Changes----------------#
                                        } else {
                                            ?><span style='color:red; '><?php echo 'Error in getting Users : No Getting Expecting Data!!' ?></span><?php
                                        }
                                    } else {
                                        ?><span style='color:red; '><?php echo 'Error in getting Users : ' . $registers['userMessage'] ?></span><?php
                                    }
                                }
                                ?>
                                <br>
                                                </span>
                            <?php
                            $checkOutlet++;
                        }
                        }
                        } else {
                            ?><span style='color:red; '><?php echo 'Error in getting  Outlets: No Getting Expecting Data!!<br>' ?></span><?php
                        }
                        } else {
                            ?><span style='color:red; '><?php echo 'Error in getting Outlets : ' . $linksync_outlets['userMessage'] . '<br>' ?></span><?php
                        }
                        }
                        ?>

                    </td>
                </tr>


                <!------------------------------------------------------------------ Tax Mapping Wc_to_Vend---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Tax mapping
                        <?php
                        help_link(array(
                            'title' => "When syncing orders, both Vend and WooCommerce have their own tax configurations - use these Tax Mapping settings to 'map' the Vend taxes with those in your WooCommerce store."
                        ));
                        ?>
                    </th>
                    <td class="forminp forminp-checkbox"><?php
                        #Check for the Taxes

                        if (isset($taxes) && !empty($taxes)) {
                            if (!isset($taxes['errorCode'])) {
                                if (isset($taxes['taxes'])) {
                                    $taxes_all = explode(',', get_option('wc_to_vend_tax'));
                                    ?><ul><legend style="display: inline-block;width:200px;float: left;"> <b>WooCommerce Tax Classes</b></legend>   <legend style="display: inline-block;width: 80px; float: left">=></legend>  <legend style="display: inline-block; "><b>Vend Taxes</b></legend><br><?php
                                    $implode_tax['tax_name']['standard-tax'] = '';
                                    $tax_classes_list = array_map("rtrim", explode("\n", get_option('woocommerce_tax_classes')));
                                    
                                    $tax_classes_list = WC_Tax::get_tax_classes();
                                       
                                    foreach ($tax_classes_list as $value) {
                                        $taxexplode = explode(" ", strtolower($value));
                                        $implode_tax['tax_name'][implode("-", $taxexplode)] = implode("-", $taxexplode);
                                    }
                                    

                                    foreach ($implode_tax['tax_name'] as $tax_classes_name => $woo_taxes) {
                                        global $wpdb;
                                        $sql = "SELECT * FROM `" . $wpdb->prefix . "woocommerce_tax_rates` WHERE `tax_rate_class` ='" . $woo_taxes . "'";
                                        $tax_rates = $wpdb->get_results($sql,ARRAY_A);

                                        if (0 != $wpdb->num_rows) {
                                            ?> <li><b><?php echo ucfirst($tax_classes_name); ?></b> </li> <?php

                                            foreach ($tax_rates as $tax_rate) {
                                                ?>
                                                <li> <legend style="display: inline-block;width: 180px;float: left;margin-left: 20px;"><?php echo $tax_rate['tax_rate_name']; ?> </legend>
                                                    <legend style="display: inline-block;width: 80px; float: left">=></legend>
                                                    <legend style="display: inline-block;"><select style="margin-top: -5px"name="wc_to_vend_tax[]">
                                                            <?php
                                                            foreach ($taxes['taxes'] as $select_tax) {
                                                                if (in_array($select_tax['id'] . "/" . $select_tax['name'] . "/" . $select_tax['rate'] . '|' . $tax_rate['tax_rate_name'].'-'.$tax_classes_name, $taxes_all)) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = "";
                                                                }
                                                                echo '<option value="' . $select_tax['id'] . "/" . $select_tax['name'] . "/" . $select_tax['rate'] . '|' . $tax_rate['tax_rate_name'].'-'.$tax_classes_name . '" ' . $selected . '>' . $select_tax['name'] . '</option>';
                                                            }
                                                            ?>
                                                        </select></legend> </li>
                                                <?php
                                            }

                                        }
                                    }
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

                <!------------------------------------------------------------------ Payment Mapping Wc_to_Vend---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Payment mapping
                        <?php
                        help_link(array(
                            'title' => "When syncing orders, both Vend and WooCommerce have their own payment methods - use these Payment Mapping settings to 'map' the Vend payment methods with those in your WooCommerce store."
                        ));
                        ?>
                    </th>
                    <td class="forminp forminp-checkbox">
                        <?php
                        #Check for the Payment
                        if (isset($payment) && !empty($payment)) {
                            if (!isset($payment['errorCode'])) {
                                if (isset($payment['paymentTypes'])) {
                                    ?>
                                    <?php
                                    $payment_wc_to_vend = get_option('wc_to_vend_payment');
                                    $wc_to_vend_payment = explode(',', $payment_wc_to_vend);
                                    $payment_gatways = $gatway->payment_gateways();
                                    ?><ul><legend style="display: inline-block;width: 20em; float: left"> <b>WooCommerce Payment Gateways</b></legend>   <legend style="display: inline-block;width: 3em; float: left">=></legend>  <legend style="display: inline-block;width: 25em; "><b>Vend Payment</b></legend><br>
                                    <?php
                                    foreach ($payment_gatways as $gatways) {
                                        if ($gatways->enabled == 'yes') {
                                            ?> <li> <legend style="display: inline-block;width: 20em; float: left">
                                                    <?php echo $gatways->title; ?></legend>    <legend style="display: inline-block;width: 3em; float: left">=></legend>
                                                <legend style="display: inline-block;width: 25em; "><select style="margin-top: -5px" name="wc_to_vend_payment[]">
                                                        <?php
                                                        foreach ($payment['paymentTypes'] as $payment_mapping) {
                                                            if (in_array($payment_mapping['name'] . "%%" . $payment_mapping['id'] . '|' . $gatways->title . '|' . $gatways->id, $wc_to_vend_payment)) {
                                                                $selected = "selected";
                                                            } else {
                                                                $selected = "";
                                                            }
                                                            echo '<option value="' . $payment_mapping['name'] . "%%" . $payment_mapping['id'] . '|' . $gatways->title . '|' . $gatways->id . '" ' . $selected . '>' . $payment_mapping['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select></legend></li>
                                        <?php }
                                    }
                                    ?>
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

                <!------------------------------------------------------------------ Customer Wc_to_Vend---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Customer Export</th>
                    <td class="forminp forminp-checkbox">

                        <legend style="display: inline-block;width: 25em; ">
                            <input  checked  type="radio"
                                    value="customer" <?php echo (get_option('wc_to_vend_export') == 'customer' ? 'checked' : ''); ?>
                                    name="wc_to_vend_export" />Export Customer data
                            <?php
                            help_link(array(
                                'title' => "Select this option if you'd like customer data, such as name, email address and shipping and billing address, to be included when exporting orders to Vend."
                            ));
                            ?>
                        </legend><br>
                        <?php
                        $orderOption = LS_Vend()->order_option();
                        $useBillingToBePhysicalOption = $orderOption->useBillingAddressToBePhysicalAddress();
                        $checkedBillingPhysicalOption = ('yes' == $useBillingToBePhysicalOption) ? 'checked':'';

                        $useShippingToBePostalOption = $orderOption->useShippingAddressToBePostalAddress();
                        $checkedShippingPostalOption = ('yes' == $useShippingToBePostalOption) ? 'checked':'';
                        ?>
                        <label style="width: 100%;margin-left: 50px;margin-bottom: 20px;">
                            <input name="usebillingtobephysical" value="yes" type="checkbox" <?php echo $checkedBillingPhysicalOption; ?>>Use Woocommerce Billing Address as Vend Physical Address
                        </label>

                        <label style="width: 100%;margin-left: 50px;margin-bottom: 20px;">
                            <input name="useshippingtobepostal" value="yes" type="checkbox" <?php echo $checkedShippingPostalOption; ?>>Use Woocommerce Shipping Address as Vend Postal Address
                        </label>

                        <legend style="display: inline-block;width: 25em; "><input type="radio" value="cash_sale" <?php echo (get_option('wc_to_vend_export') == 'cash_sale' ? 'checked' : ''); ?>  name="wc_to_vend_export" />Export as 'Cash Sale'
                            <?php
                            help_link(array(
                                'title' => "Select this option if you're not interested in including the customer information when exporting orders to Vend. "
                            ));
                            ?>
                        </legend>
                    </td>
                </tr>
                </tbody>
            </table>
        </div><br>
        <div id="order_sync_vend_to_wc" style="display:<?php
        if (get_option('order_sync_type') == 'vend_to_wc-way' || get_option('order_sync_type') == 'enable') {
            echo "block";
        } else {
            echo "none";
        }
        ?>">
            <h3>Vend to WooCommerce:</h3>
            <!----------------------------------------------------------Order Vend To WC----------------------------------------->
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th  class="titledesc">Order Status
                        <?php
                        help_link(array(
                            'title' => "Use this option to select the default status of the order when it's imported. In most cases you will set this to 'Completed'  "
                        ));
                        ?>
                    </th>
                    <td class="forminp forminp-checkbox">
                        <?php if (isset($order_Status) && !empty($order_Status)) { ?>

                            <?php
                            foreach ($order_Status as $order_name => $order_value) {
                                ?> <input type="radio"
                                <?php echo(get_option('order_vend_to_wc') == $order_name ? 'checked' : '');  ?>
                                          value="<?php echo $order_name ?>"
                                          name="order_vend_to_wc" /><?php echo $order_value ?><br>
                                <?php
                            }
                            ?><br><?php
                        } else {
                            ?>
                            <span style='color:red; '><?php echo 'Error in getting  Outlets: No Getting Expecting Data!!<br>' ?></span><?php
                        }
                        ?>
                    </td>
                </tr>
                <!------------------------------------------------------------------ Tax Mapping vend_to_wc---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Tax mapping
                        <?php
                        help_link(array(
                            'title' => "When syncing orders, both Vend and WooCommerce have their own tax configurations - use these Tax Mapping settings to 'map' the Vend taxes with those in your WooCommerce store. "
                        ));
                        ?>
                    </th>
                    <td class="forminp forminp-checkbox">
                        <?php
                        #Check for the Taxes
                        if (isset($taxes) && !empty($taxes)) {
                        if (!isset($taxes['errorCode'])) {
                        if (isset($taxes['taxes'])) {
                        $taxes_all = explode(',', get_option('vend_to_wc_tax'));
                        ?><ul><legend style="display: inline-block;width: 8em; float: left"> <b> Vend Taxes</b></legend>   <legend style="display: inline-block;width: 3em; float: left">=></legend>  <legend style="display: inline-block;width: 25em; "><b>WooCommerce Tax Classes</b></legend><br>
                            <?php
                            $vend_to_wc_implode_tax['tax_name'][] = 'standard-tax';
                            $tax_classes_list = array_map("rtrim", explode("\n", get_option('woocommerce_tax_classes')));
                            foreach ($tax_classes_list as $value) {
                                $taxexplode = explode(" ", strtolower($value));
                                $vend_to_wc_implode_tax['tax_name'][] = implode("-", $taxexplode);
                            }
                            foreach ($taxes['taxes'] as $select_tax) {
                                ?>
                                <li> <legend style="display: inline-block;width: 8em; float: left"><?php echo $select_tax['name']; ?> </legend>
                                <legend style="display: inline-block;width: 3em; float: left">=></legend>
                                <legend style="display: inline-block;width: 25em; "><select style="margin-top: -5px" name="vend_to_wc_tax[]">
                                        <?php
                                        foreach ($vend_to_wc_implode_tax['tax_name'] as $tax) {
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
                <!------------------------------------------------------------------ Payment Mapping vend_to_wc---------------------------------------->
                <tr valign="top">
                    <th  class="titledesc">Payment mapping
                        <?php
                        help_link(array(
                            'title' => "When syncing orders, both Vend and WooCommerce have their own payment methods - use these Payment Mapping settings to 'map' the Vend payment methods with those in your WooCommerce store."
                        ));
                        ?>
                    </th>
                    <td class="forminp forminp-checkbox">
                        <?php
                        #Check for the Payment
                        if (isset($payment) && !empty($payment)) {
                            if (!isset($payment['errorCode'])) {
                                if (isset($payment['paymentTypes'])) {
                                    $all_payment = get_option('vend_to_wc_payments');
                                    $payment_all = explode(',', $all_payment);
                                    $payment_gatways = $gatway->payment_gateways();
                                    ?><ul><legend style="display: inline-block;width: 8em; float: left"> <b>Vend Payment</b></legend>   <legend style="display: inline-block;width: 3em; float: left">=></legend>  <legend style="display: inline-block;width: 25em; "><b>WooCommerce Payment Gateways</b></legend><br>
                                    <?php foreach ($payment['paymentTypes'] as $payment_mapping) {
                                        ?><li> <legend style="display: inline-block;width: 8em; float: left"><?php echo $payment_mapping['name']; ?></legend>
                                        <legend style="display: inline-block;width: 3em; float: left">=></legend>
                                        <legend style="display: inline-block;width: 25em; "><select style="margin-top: -5px" name="vend_to_wc_payments[]">
                                                <?php
                                                foreach ($payment_gatways as $gatways) {
                                                    if ($gatways->enabled == 'yes') {
                                                        if (in_array($payment_mapping['id'] . '|' . $gatways->title, $payment_all)) {
                                                            $selected = "selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                        echo '<option value="' . $payment_mapping['id'] . '|' . $gatways->title . '" ' . $selected . '>' . $gatways->title . '</option>';
                                                    }
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
                        <legend style="display: inline-block;width: 25em; ">  <input type="radio"  checked  <?php echo (get_option('vend_to_wc_customer') == 'customer_data' ? 'checked' : ''); ?> value="customer_data" name="vend_to_wc_customer" />Import Customer data

                            <?php
                            help_link(array(
                                'title' => "Select this option if you'd like customer data, such as name, email address and shipping and billing address, to be included when importing orders from Vend."
                            ));
                            ?>
                        </legend><br>
                        <legend style="display: inline-block;width: 25em; ">
                            <input type="radio" <?php echo (get_option('vend_to_wc_customer') == 'guest' ? 'checked' : ''); ?> value="guest" name="vend_to_wc_customer" />Import as 'Guest'
                            <?php
                            help_link(array(
                                'title' => "Select this option if you're not interested in including the customer information when importing orders from Vend."
                            ));
                            ?><br/><br/>
                        </legend>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
    <p class="wc_t" style="text-align: center;"><input class="button button-primary button-large" type="submit" name="save_order_sync_setting" value="Save Changes"></p>

</form>
<script>

    jQuery(document).ready(function() {
        jQuery("input[name='order_sync_type']").click(function() {
            if (jQuery("#disabled_sync_id").is(":checked")) {
                jQuery('#order_sync_setting').slideUp(500);
            } else if (jQuery("#wc_to_vend_sync_id").is(":checked")) {
                jQuery('#order_sync_wc_to_vend').slideDown(500);
                jQuery('#order_sync_vend_to_wc').slideUp(500);
                jQuery('#order_sync_setting').slideDown(500);
            } else if (jQuery("#vend_to_wc_sync_id").is(":checked")) {
                jQuery('#order_sync_vend_to_wc').slideDown(500);
                jQuery('#order_sync_wc_to_vend').slideUp(500);
                jQuery('#order_sync_setting').slideDown(500);
            } else {
                jQuery('#order_sync_setting').slideDown(500);
                jQuery('#order_sync_wc_to_vend').slideDown(500);
                jQuery('#order_sync_vend_to_wc').slideDown(500);
            }
        });
        if (jQuery(".outlet_class").is(":checked")) {

            <?php if (!empty($outletDb)) { ?>
            jQuery(".check").hide();
            jQuery("#" + "<?php echo $outletDb; ?>").fadeIn(500);
            <?php } else { ?>
            var outlet = jQuery(".outlet_class").val();
            jQuery(".check").fadeOut(500);
            jQuery("#" + outlet).fadeIn(500);
            <?php } ?>
        }
        jQuery('.outlet_class').click(function() {
            var outlet = this.value;
            jQuery(".check").hide();
            jQuery("#" + outlet).fadeIn(500);
        });

    });

    (function ($) {

        $(document).ready(function () {

            var $mainContainer = $('#ls-main-wrapper');

            $mainContainer.on('submit', '#frmOrderSyncingSettings', function (e) {
                var $tabMenu = $('.ls-tab-menu');
                var $frm = $('#frmOrderSyncingSettings');


                $tabMenu.before('<div class="ls-loading open"></div>');

                var data = {
                    action: 'vend_save_order_syncing_settings',
                    post_array: $frm.serialize()
                };

                lsAjax.post(data).done(function (response) {
                    $mainContainer.find('.ls-loading').fadeOut('fast');
                    console.log(response);
                });
                e.preventDefault();
            });
        });

    })(jQuery);
</script>
