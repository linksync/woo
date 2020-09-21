<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_View_Product_Section
{
    public static function syncing_type($args)
    {
        $product_sync_type = $args['product_sync_type'];
        ?>
        <table class="wp-list-table widefat fixed">
            <thead>
            <tr>
                <td><strong>Product Syncing Type</strong></td>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>
                    <p>
                        <input id="ls-product-twoway" <?php echo($product_sync_type == 'two_way' ? 'checked' : ''); ?>
                               type="radio" name="product_sync_type" value="two_way">
                        <label for="ls-product-twoway">Two-way</label>
                        <?php
                        help_link(array(
                            'title' => 'Data is kept in sync between both systems, so changes to products and inventory can be made in either your WooCommerce or Vend store and those changes will be synced to the other store within a few moments.'
                        ));
                        ?>&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio"
                               id="ls-product-vendtowoo" <?php echo($product_sync_type == 'vend_to_wc-way' ? 'checked' : ''); ?>
                               name="product_sync_type" value="vend_to_wc-way">
                        <label for="ls-product-vendtowoo">Vend to WooCommerce </label>
                        <?php
                        help_link(array(
                            'title' => 'Vend is the \'master\' when it comes to managing product and inventory, and product updates are one-way, from Vend to WooCommerce - product and inventory data does not update back to Vend from WooCommerce. You must enable Order Syncing from WooCommerce to Vend for this option to work correctly.'
                        ));
                        ?>&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio"
                               id="ls-product-wootovend" <?php echo($product_sync_type == 'wc_to_vend' ? 'checked' : ''); ?>
                               name="product_sync_type" value="wc_to_vend">
                        <label for="ls-product-wootovend">WooCommerce to Vend </label>
                        <?php
                        help_link(array(
                            'title' => 'WooCommerce is the \'master\' when it comes to managing product and inventory, and product updates are one-way, from WooCommerce to Vend - product and inventory data does not update back to WooCommerce to Vend. You must enable Order Syncing from Vend to WooCommerce for this option to work correctly. '
                        ));
                        ?>&nbsp;&nbsp; &nbsp;&nbsp;

                        <input type="radio"
                               id="disabled_sync_id" <?php echo($product_sync_type == 'disabled_sync' ? 'checked' : ''); ?>
                               name="product_sync_type" value="disabled_sync">
                        <label for="disabled_sync_id">Disabled</label>
                        <?php
                        help_link(array(
                            'title' => 'Prevent any product syncing from taking place between your Vend and WooCommerce stores.'
                        ));
                        ?>
                    </p>
                </td>
            </tr>

            <tr>
                <td>
                    <div
                        <?php
                            if ($product_sync_type == 'disabled_sync') {
                                echo 'style="display:none";';
                            }
                        ?>
                    >
                        <p>

                        <input type="button" name="sync_reset_btn"
                               title="Selecting the Sync Reset button resets linksync to update all WooCommerce products with data from Vend, based on your existing Product Sync Settings."
                               value="Sync all products from Vend" id="sync_reset_btn_id"
                               class="button button-primary btn-sync-vend-to-woo" style="display:<?php
                        if ($product_sync_type == 'wc_to_vend') {
                            echo "none";
                        }
                        ?>" name="sync_reset"/>
                        <input id="sync_reset_all_btn_id" type="button"
                               title="Selecting this option will sync your entire WooCommerce product catalogue to Vend, based on your existing Product Sync Settings. It takes 3-5 seconds to sync each product, depending on the performance of your server, and your geographic location."
                               value="Sync all products to Vend" style="display:<?php
                        if ($product_sync_type == 'vend_to_wc-way') {
                            echo "none";
                        }
                        ?>" class="button button-primary btn-sync-woo-to-vend"/>
                        </p>
                    </div>

                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }
}