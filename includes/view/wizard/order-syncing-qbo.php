<h1>Order Syncing Configuration</h1>
<hr>
<form class="wizard-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="process" value="wizard" />
	<input type="hidden" name="action" value="order-sync" />
	<input type="hidden" name="synctype" value="qbo" />
	<input type="hidden" name="nextpage" value="0" />
	<p class="form-holder">
		<strong>Order syncing type</strong>
		<select name="linksync[order_sync_type]" id="order_syncing_type" class="form-field">
			<option value="woo_to_qbo">WooCommerce to QuickBooks</option>
			<option value="disabled">Disabled</option>
		<select>
            <span id="order_sync_type_helper_message">WooCommerce to QuickBooks - orders created in WooCommerce will be synced to QuickBooks online.</span>
	</p>

	<!-- Woo to QuickBooks options -->
	<div id="linksync_order_woo_to_qbo" class="linksync_order_syncing_options" style="display:none;">
		<h3>WooCommerce to QuickBooks Options</h3>
		<p class="form-holder">
			<strong>Would you like to export your Customer Data to QuickBooks?</strong>
			<select name="linksync[order_woo_to_qbo_export_customer]" id="order_woo_to_qbo_export_customer" class="form-field">
				<option <?php echo ('export_as_customer_data' == $export_customer_data) ? 'selected': ''; ?> value="export_as_customer_data">Yes</option>
				<option <?php echo ('export_as_woo_sale' == $export_customer_data) ? 'selected': ''; ?> value="export_as_woo_sale">No</option>
			<select>
			<br>
			<span>If No, customer will be exported as WooCommerce Sale</span>
		</p>

        <br/>
		<p class="form-holder">
			<strong>Post to QuickBooks as</strong>
			<select name="linksync[order_woo_to_qbo_post_as]" id="order_woo_to_qbo_post_as" class="form-field">
				<option <?php echo ('sales_receipt' == $post_to_quickbooks_as) ? 'selected': ''; ?> value="sales_receipt">Sales Receipt</option>
				<option <?php echo ('sales_invoice' == $post_to_quickbooks_as) ? 'selected': ''; ?> value="sales_invoice">Invoice</option>
			<select>
                <span id="sales_type_helper_message">Sales Receipt (recommended) - orders from WooCommerce are created in QuickBooks Online as Sales Receipts.</span>
		</p>

        <br/>
		<p class="form-holder">
			<strong style="margin-bottom: -2px;">Order Status</strong>
            <span>Choose what order statuses should be used to trigger the syncing of WooCommerce orders to QuickBooks Online.</span><br/><br/>

			<label><input name="linksync[order_woo_to_qbo_order_status][]" checked value="wc-pending" type="checkbox">Pending Payment</label><br/>
			<label><input name="linksync[order_woo_to_qbo_order_status][]" checked value="wc-processing" type="checkbox">Processing</label><br/>
			<label><input name="linksync[order_woo_to_qbo_order_status][]" checked value="wc-on-hold" type="checkbox">On Hold</label><br/>
			<label><input name="linksync[order_woo_to_qbo_order_status][]" checked value="wc-completed" type="checkbox">Completed</label><br/>
			<label><input name="linksync[order_woo_to_qbo_order_status][]" value="wc-cancelled" type="checkbox">Cancelled</label><br/>
			<label><input name="linksync[order_woo_to_qbo_order_status][]" value="wc-refunded" type="checkbox">Refunded</label>
		</p>

        <br/>
		<p class="form-holder">
			<strong>Order number for QuickBooks</strong>
			<select name="linksync[order_woo_to_qbo_order_number]" id="order_woo_to_qbo_order_number" class="form-field">
				<option <?php echo ('use_qbo' == $order_number_for_quickbooks)? 'selected': ''; ?> value="use_qbo">Use QuickBooks to set order number</option>
				<option <?php echo ('use_woo' == $order_number_for_quickbooks)? 'selected': ''; ?> value="use_woo">Use Woocommerce to set order number</option>
			<select>
		</p>
	</div>
	<p class="form-holder">
		<input type="submit" name="submit" value="Done" />
	</p>
	<div class="clearfix"></div>
</form>

<script type="text/javascript">
	jQuery(function() {
		// First Load
        var orderSyncType = jQuery('#order_syncing_type');
        var orderSyncTypeHelperMessage = jQuery('#order_sync_type_helper_message');
        var salesTypeTobeUsed = jQuery('#order_woo_to_qbo_post_as');
        var saleTypeHelperMessage = jQuery('#sales_type_helper_message');

        orderSyncType.val('<?php echo $order_syncing_type; ?>');
        order_syncing_form_load();
        salesTypeMesageToUser();

        orderSyncType.change(function() {
            order_syncing_form_load();
		});

        salesTypeTobeUsed.change(function () {
            salesTypeMesageToUser(salesTypeTobeUsed.val());
        });

		function order_syncing_form_load() {
            var val = jQuery('#order_syncing_type').val();
            if(val != 'disabled') {
                switch(val) {
                    case 'woo_to_qbo':
                        jQuery('#linksync_order_two_way').hide('slow');
                        jQuery('#linksync_order_woo_to_qbo').show('slow');
                        break;
                }
            } else {

                jQuery('.linksync_order_syncing_options').hide('slow');
            }
            var message = '';
            message =   '<b style="font-weight: bolder;">WooCommerce to QuickBooks</b> - orders created in WooCommerce will be synced to QuickBooks online.<br/>' +
                        '<b style="font-weight: bolder;">Disabled</b> - orders will not be synced between WooCommerce and QuickBooks Online. See <a href=\'https://help.linksync.com/hc/en-us/articles/206438604-Order-Syncing-Settings\' target=\'_blank\'>Order Syncing Settings</a> for more info. ';
            orderSyncTypeHelperMessage.html(message);
        }
        
        function salesTypeMesageToUser(val) {
		    var message = '';
            message =   '<b style="font-weight: bolder;">Sales Receipt (recommended)</b> - orders from WooCommerce are created in QuickBooks Online as Sales Receipts.<br/>' +
                        '<b style="font-weight: bolder;">Invoice</b> - orders from WooCommerce are created in QuickBooks Online as invoices. You\'ll need to manually process payment in QuickBooks Online for this option. ';

            saleTypeHelperMessage.html(message);
        }
	});
</script>