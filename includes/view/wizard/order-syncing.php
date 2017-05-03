<h1>Order Syncing Configuration</h1>
<hr>
<form class="wizard-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="process" value="wizard" />
	<input type="hidden" name="action" value="order-sync" />
	<input type="hidden" name="synctype" value="vend" />
	<input type="hidden" name="nextpage" value="0" />
	<p class="form-holder">
		<strong>Order syncing type</strong>
		<select name="linksync[order_sync_type]" id="order_syncing_type" class="form-field">
			<option value="vend_to_wc-way">Vend to WooCommerce</option>
			<option value="wc_to_vend">WooCommerce to Vend</option>
			<option value="disabled">Disabled</option>
		<select>
            <span>
                <b style="font-weight: bolder;">Vend to WooCommerce</b> - orders created in Vend will be synced to WooCommerce.<br/>
                <b style="font-weight: bolder;">WooCommerce to Vend</b> - orders created in WooCommerce will be synced to Vend.<br/>
                <b style="font-weight: bolder;">Disabled</b> - orders will not be synced between WooCommerce and Vend. See <a href="https://help.linksync.com/hc/en-us/articles/205715939-Order-Syncing-Settings" target="_blank">Order Syncing Settings</a> for more info.
            </span>
	</p>
	
	<!-- Vend to Woo options -->
	<div id="linksync_order_vend_to_woo" class="linksync_order_syncing_options" style="display:none;">
		<h3>Vend to WooCommerce Options</h3>
		<p class="form-holder">
			<strong>Would you like to import your Customer Data?</strong>
			<select name="linksync[order_vend_to_woo_import_customer]" id="order_vend_to_woo_import_customer" class="form-field">
				<option value="customer_data">Yes</option>
				<option value="guest">No</option>
			<select>
			<br>
			<span>If No, customer will be imported as guest</span>
		</p>
		<p class="form-holder">
            <br/>
			<strong style="margin-bottom: 0px;">Order Status</strong>
            <span>Choose what order statuses should be used to trigger the syncing of WooCommerce orders to Vend.</span><br/><br/>
			<select name="linksync[order_vend_to_woo_order_status]" id="order_vend_to_woo_order_status" class="form-field">
				<option value="wc-pending">Pending Payment</option>
				<option value="wc-processing">Processing</option>
				<option value="wc-on-hold">On Hold</option>
				<option value="wc-completed">Completed</option>
				<option value="wc-cancelled">Cancelled</option>
				<option value="wc-refunded">Refunded</option>
			<select>

		</p>
	</div>
	
	<!-- Woo to Vend options -->
	<div id="linksync_order_woo_to_vend" class="linksync_order_syncing_options" style="display:none;">
		<h3>WooCommerce to Vend Options</h3>
		<p class="form-holder">
			<strong>Would you like to export your Customer Data to vend order?</strong>
			<select name="linksync[order_woo_to_vend_export_customer]" id="order_woo_to_vend_export_customer" class="form-field">
				<option value="customer">Yes</option>
				<option value="cash_sale">No</option>
			<select>
			<br>
			<span>If No, customer will be exported as Cash Sale</span>
		</p>
		<p class="form-holder">
            <br/>
			<strong style="margin-bottom: 0px;">Order Status</strong>
            <span>Choose what order statuses should be used to trigger the syncing of WooCommerce orders to QuickBooks Online.</span><br/><br/>
			<select name="linksync[order_woo_to_vend_order_status]" id="order_woo_to_vend_order_status" class="form-field">
				<option value="wc-pending">Pending Payment</option>
				<option value="wc-processing">Processing</option>
				<option value="wc-on-hold">On Hold</option>
				<option value="wc-completed">Completed</option>
				<option value="wc-cancelled">Cancelled</option>
				<option value="wc-refunded">Refunded</option>
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
		jQuery('#order_syncing_type').val('disabled');
		
		jQuery('#order_syncing_type').change(function() {
			var val = jQuery(this).val();
			if(val != 'disabled') {
				switch(val) {
					case 'vend_to_wc-way':
						jQuery('#linksync_order_two_way').hide('slow');
						jQuery('#linksync_order_vend_to_woo').show('slow');
						jQuery('#linksync_order_woo_to_vend').hide('slow');
						break;
						
					case 'wc_to_vend':
						jQuery('#linksync_order_two_way').hide('slow');
						jQuery('#linksync_order_vend_to_woo').hide('slow');
						jQuery('#linksync_order_woo_to_vend').show('slow');
						break;
				}
			} else {
				jQuery('.linksync_order_syncing_options').hide('slow');
			}
		});
	});
</script>