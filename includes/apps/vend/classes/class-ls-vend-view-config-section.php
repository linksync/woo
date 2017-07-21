<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_View_Config_Section
{
    public static function api_key_configuration()
    {
        global $linksync_vend_laid;
        ?>
        <table class="wp-list-table widefat fixed">
            <thead>
            <tr>
                <td><strong>API Key configuration</strong></td>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>
                    <div>
                        <form method='POST' id="frmResetSyncingSettings">
                            <input type='submit' style="float: right;" class="button button-primary"
                                   title=' Use this button to reset Product and Order Syncing Setting.' name='rest'
                                   value='Reset Syncing Setting'>
                            <span class="spinner"></span>
                        </form>
                    </div>


                    <table cellpadding="8">
                        <tr>
                            <td><b style='font-size: 14px;'>API Key*:</b></td>
                            <td>
                                <?php echo '<b class="apikeyholder">', empty($linksync_vend_laid) ? 'No Api Key' : $linksync_vend_laid, '</b>'; ?>
                                <a href="https://www.linksync.com/help/woocommerce"
                                   style="text-decoration: none !important;">
                                    <img title="The linksync API Key is a unique key that's created when you link two apps via the linksync dashboard. You need a valid API Key for this linkysnc extension to work."
                                         style="margin-bottom: -4px;"
                                         src="../wp-content/plugins/linksync/assets/images/linksync/help.png"
                                         height="16" width="16"/>
                                </a>
                            </td>
                            <td>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <?php
                                if (empty($linksync_vend_laid)) {
                                    echo '<a href="#"  data-reveal-id="myModal" data-animation="fade" class="button button-primary">Add Api Key</a>';
                                } else {
                                    echo '<a href="#" data-reveal-id="modal_update_api" class="button button-primary">Edit Api Key</a>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public static function sync_now()
    {
        $vend_option = LS_Vend()->option();
        $status = $vend_option->connection_status();
        $vend_product_option = LS_Vend()->product_option();
        $sync_type = $vend_product_option->sync_type();
        $woocommerce_product_ids = LS_Woo_Product::get_product_ids();

        if ('Active' == $status) {
            ?>

            <table class="wp-list-table widefat fixed">
                <thead>
                <tr>
                    <td <?php echo ('two_way' == $sync_type) ? 'colspan="2"' : ''; ?>>
                        <?php echo ('disabled_sync' != $sync_type) ? '<strong>Sync Now!</strong>' : ''; ?>
                        <?php echo ('disabled_sync' == $sync_type) ? 'Product syncing type disabled! <a href="'.LS_Vend_Menu::settings_page_menu_url('product_config').'">Configure it now!</a>': ''; ?>
                    </td>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <?php
                    if ('two_way' == $sync_type || 'vend_to_wc-way' == $sync_type) {
                        ?>
                        <td class="sync-message">
                            <p>
                                Selecting the Sync Reset button resets linksync to update all WooCommerce products with
                                data from Vend, based on your existing Product Sync Settings.
                            </p>
                        </td>
                        <?php
                    }

                    if (('two_way' == $sync_type || 'wc_to_vend' == $sync_type) && !empty($woocommerce_product_ids)) {
                        ?>
                        <td class="sync-message">
                            <p>
                                Selecting this option will sync your entire WooCommerce product catalogue to Vend, based
                                on your existing Product Sync Settings. It takes 3-5 seconds to sync each product,
                                depending on the performance of your server, and your geographic location.
                            </p>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    if ('two_way' == $sync_type || 'vend_to_wc-way' == $sync_type) {
                        ?>
                        <td class="sync-qbo-button-cont">
                            <p class="form-holder">
                                <input type="button" name="sync_reset_btn"
                                       title="Selecting the Sync Reset button resets linksync to update all WooCommerce products with data from Vend, based on your existing Product Sync Settings."
                                       value="Sync all products from Vend" id="sync_reset_btn_id"
                                       class="button button-primary btn-sync-vend-to-woo" style="display:<?php
                                if ($sync_type == 'wc_to_vend') {
                                    echo "none";
                                }
                                ?>" name="sync_reset"/>
                            </p>
                        </td>
                        <?php
                    }

                    if (('two_way' == $sync_type || 'wc_to_vend' == $sync_type) && !empty($woocommerce_product_ids)) {
                        ?>
                        <td class="sync-qbo-button-cont">
                            <p class="form-holder">
                                <input id="sync_reset_all_btn_id" type="button"
                                       title="Selecting this option will sync your entire WooCommerce product catalogue to Vend, based on your existing Product Sync Settings. It takes 3-5 seconds to sync each product, depending on the performance of your server, and your geographic location."
                                       value="Sync all products to Vend" style="display:<?php
                                if ($sync_type == 'vend_to_wc-way') {
                                    echo "none";
                                }
                                ?>" class="button button-primary btn-sync-woo-to-vend"/>
                            </p>
                        </td>
                        <?php
                    }
                    ?>

                </tr>

                </tbody>
            </table>
            <?php LS_Vend()->view()->display_syncing_modal(); ?>
            <?php
        }
    }

    public static function update_section()
    {
        $vend_option = LS_Vend()->option();
        $status = $vend_option->connection_status();
        $vend_product_option = LS_Vend()->product_option();
        $sync_type = $vend_product_option->sync_type();
        $order_option = LS_Vend()->order_option();
        $order_sync_type = $order_option->sync_type();
        ?>
        <table class="wp-list-table widefat fixed">
            <thead>
            <tr>
                <td><strong>Update</strong></td>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>
                    <b>Update URL : </b><a
                            class="<?php echo (
                            ('Active' == $status && 'two_way' == $sync_type) ||
                            ('Active' == $status && 'vend_to_wc-way' == $sync_type)
                            ) ? 'vend_to_woo_since_last_update' : ''; ?>"
                            href="javascript:void(0)"><?php echo Linksync_Vend::getWebHookUrl(); ?></a>
                    <br><br>In case of integration halt, use this button to manually update and resync data from Vend to
                    WooCommerce since last sync
                    <p>
                        <input type="button"
                               class="button button-primary <?php echo (
                                   ('Active' == $status && 'two_way' == $sync_type) ||
                                   ('Active' == $status && 'vend_to_wc-way' == $sync_type)
                               )  ? 'vend_to_woo_since_last_update' : ''; ?>"
                            <?php echo ('Inactive' == $status || 'wc_to_vend' == $sync_type) ? 'disabled' : ''; ?>
                               value="Resync from last update">
                    </p>

                </td>
            </tr>
            </tbody>
        </table>
        <?php LS_Vend()->view()->display_syncing_modal(); ?>
        <?php
    }

    public static function connection_status()
    {
        $vend_option = LS_Vend()->option();
        $status = $vend_option->connection_status();
        if (isset($status) && $status == 'Active' || $status == 'Inactive') {
            $last_time_tested = $vend_option->get_last_time_tested();
            $connected_to = $vend_option->connected_to();
            $vendConfig = new LS_Vend_Config();
            $vendDomainPrefix = $vendConfig->get_domain_prefix();
            $vendUrl = new LS_Vend_Url($vendConfig);
            $vendStoreUrl = $vendUrl->get_store_url();
            ?>
            <style>
                #ls-vend-status > table tr td label {
                    width: 100px !important;
                }
            </style>

            <table class="wp-list-table widefat fixed">
                <thead>
                <tr>
                    <td><strong <?php echo ('Inactive' == $status) ? 'style="color: red;"' : ''; ?>>Connection
                            Status</strong></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><label>Account Status</label> :
                        <strong><?php echo($last_time_tested != '' ? get_option('linksync_status') : 'Failed / Not tested'); ?></strong>
                    </td>
                </tr>

                <tr>
                    <td><label>linksync API Url</label> :
                        <strong><?php echo($last_time_tested != '' ? '<a target="_blank" href="http://developer.linksync.com/">' . get_option('linksync_connected_url') . '</a>' : 'Failed / Not tested') ?></strong>
                    </td>
                </tr>

                <?php
                if (!empty($connected_to)) {
                    ?>
                    <tr>
                        <td><label>Connected To</label> :
                            <strong><?php echo($last_time_tested != '' ? $connected_to : 'Failed / Not tested') ?></strong>
                        </td>
                    </tr>
                    <?php
                }
                ?>


                <?php
                if (!empty($connected_to) && !empty($vendDomainPrefix)) {
                    ?>
                    <tr>
                        <td><label><?php echo $connected_to . ' Url ' ?></label> :
                            <strong><?php echo($last_time_tested != '' ? '<a target="_blank" href="' . $vendStoreUrl . '">' . $vendStoreUrl . '</a>' : 'Failed / Not tested') ?></strong>
                        </td>
                    </tr>
                    <?php
                }
                ?>


                <tr>
                    <td><label>Last Message</label> :
                        <strong><?php echo($last_time_tested != '' ? get_option('linksync_frequency') : 'Failed / Not tested') ?></strong>
                    </td>
                </tr>

                <tr>
                    <td><label>Last time tested</label> :
                        <strong><?php echo($last_time_tested != '' ? $last_time_tested : 'Failed / Not tested') ?></strong>
                    </td>
                </tr>
                </tbody>

            </table>

        <?php }
    }
}