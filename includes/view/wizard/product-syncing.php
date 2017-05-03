<h1>Product Syncing Configuration</h1>
<hr>
<form class="wizard-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="process" value="wizard" />
	<input type="hidden" name="action" value="product-sync" />
	<input type="hidden" name="synctype" value="vend" />
	<input type="hidden" name="nextpage" value="3" />
	<p class="form-holder">
		<strong>Product syncing type</strong>
		<select name="linksync[product_sync_type]" id="product_syncing_type" class="form-field">
			<option value="two_way">Two Way</option>
			<option value="vend_to_wc-way">Vend to WooCommerce</option>
			<option value="wc_to_vend">WooCommerce to Vend</option>
			<option value="disabled_sync">Disabled</option>
		<select>
            <span>
                <b style="font-weight: bolder;">Two way (recommended)</b> - product changes made in either WooCommerce or Vend will be kept in sync.<br/>
                <b style="font-weight: bolder;">Vend to WooCommerce</b> - changes made in Vend will be synced to WooCommerce, but changes made in WooCommerce won't be synced to Vend.<br/>
                <b style="font-weight: bolder;">WooCommerce to Vend</b> - changes made in WooCommerce will be synced to Vend, but changes made in Vend won't be synced to WooCommerce.<br/>
                <b style="font-weight: bolder;">Disabled</b> - products information will not be synced between WooCommerce and Vend. See <a href="https://help.linksync.com/hc/en-us/articles/205715889-Product-Syncing-Settings" target="_blank"> Product Syncing Settings</a> for more info. <br/>
            </span>
	</p>
	
	<!-- Two way options -->
	<div id="linksync_product_two_way" class="linksync_product_syncing_options" style="display:none;">
		<h3>Two Way Options</h3>
		<p class="form-holder">
			<strong>Name/Title</strong>
			<label for="product_two_way_name_title">
				<input type="checkbox" name="linksync[product_two_way_name_title]" id="product_two_way_name_title" value="1" /> Sync the product titles between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Description</strong>
			<label for="product_two_way_description">
				<input type="checkbox" name="linksync[product_two_way_description]" id="product_two_way_description" value="1" /> Sync the product description between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Short Description</strong>
			<label for="product_two_way_short_description">
				<input type="checkbox" name="linksync[product_two_way_short_description]" id="product_two_way_short_description" value="1" /> Copy full description from Vend to short description in WooCommerce
			</label>
		</p>
		<p class="form-holder">
			<strong>Price</strong>
			<label for="product_two_way_price">
				<input type="checkbox" name="linksync[product_two_way_price]" id="product_two_way_price" value="1" /> Sync prices between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Quantity</strong>
			<label for="product_two_way_quantity">
				<input type="checkbox" name="linksync[product_two_way_quantity]" id="product_two_way_quantity" value="1" /> Sync product Quantity between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Tags</strong>
			<label for="product_two_way_tags">
				<input type="checkbox" name="linksync[product_two_way_tags]" id="product_two_way_tags" value="1" /> Sync tags between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Categories</strong>
			<label for="product_two_way_categories">
				<input type="checkbox" name="linksync[product_two_way_categories]" id="product_two_way_categories" value="1" /> Sync WooCommerce product categories with Vend
			</label>
			<br>
			<span>Use with caution as any existing product categories in WooCommerce not matching those in Vend will be deleted <a href="http://docs.linksync.com/x/6gBG" target="_blank">Click here for more information</a></span>
		</p>
		<p class="form-holder">
			<strong>Product Status</strong>
			<label for="product_two_way_product_status">
				<input type="checkbox" name="linksync[product_two_way_product_status]" id="product_two_way_product_status" value="1" /> Tick this option to Set new product to Pending
			</label>
		</p>
		<p class="form-holder">
			<strong>Were about to import your products from vend to woocommerce, choose the best option applicable to you.</strong>
			<select name="linksync[product_two_way_product_import_tags]" id="product_two_way_product_import_tags" class="form-field">
				<option value="off">Import all product</option>
				<option value="on">Import selected products</option>
			<select>
		</p>
		<p class="form-holder">
			<strong>Images</strong>
			<label for="product_two_way_images">
				<input type="checkbox" name="linksync[product_two_way_images]" id="product_two_way_images" value="1" /> Sync images from Vend to WooCommerce
			</label>
		</p>
		<p class="form-holder">
			<strong>Create New</strong>
			<label for="product_two_way_create_new">
				<input type="checkbox" name="linksync[product_two_way_create_new]" id="product_two_way_create_new" value="1" /> Create new products from Vend 
			</label>
		</p>
		<p class="form-holder">
			<strong>Delete</strong>
			<label for="product_two_way_delete">
				<input type="checkbox" name="linksync[product_two_way_delete]" id="product_two_way_delete" value="1" /> Sync product deletions between apps
			</label>
		</p>
	</div>
	
	<!-- Vend to Woo options -->
	<div id="linksync_product_vend_to_woo" class="linksync_product_syncing_options" style="display:none;">
		<h3>Vend to WooCommerce Options</h3>
		<p class="form-holder">
			<strong>Name/Title</strong>
			<label for="product_vend_to_woo_name_title">
				<input type="checkbox" name="linksync[product_vend_to_woo_name_title]" id="product_vend_to_woo_name_title" value="1" /> Sync the product titles between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Description</strong>
			<label for="product_vend_to_woo_description">
				<input type="checkbox" name="linksync[product_vend_to_woo_description]" id="product_vend_to_woo_description" value="1" /> Sync the product description between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Short Description</strong>
			<label for="product_vend_to_woo_short_description">
				<input type="checkbox" name="linksync[product_vend_to_woo_short_description]" id="product_vend_to_woo_short_description" value="1" /> Copy full description from Vend to short description in WooCommerce
			</label>
		</p>
		<p class="form-holder">
			<strong>Price</strong>
			<label for="product_vend_to_woo_price">
				<input type="checkbox" name="linksync[product_vend_to_woo_price]" id="product_vend_to_woo_price" value="1" /> Sync prices between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Quantity</strong>
			<label for="product_vend_to_woo_quantity">
				<input type="checkbox" name="linksync[product_vend_to_woo_quantity]" id="product_vend_to_woo_quantity" value="1" /> Sync product Quantity between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Attributes</strong>
			<label for="product_vend_to_woo_attributes_values">
				<input type="checkbox" name="linksync[product_vend_to_woo_attributes_values]" id="product_vend_to_woo_attributes_values" value="1" /> Sync attributes and values with Vend
			</label><br>
			<label for="product_vend_to_woo_attributes_visible">
				<input type="checkbox" name="linksync[product_vend_to_woo_attributes_visible]" id="product_vend_to_woo_attributes_visible" value="1" /> Sync attributes Visible on Product Page
			</label>
		</p>
		<p class="form-holder">
			<strong>Tags</strong>
			<label for="product_vend_to_woo_tags">
				<input type="checkbox" name="linksync[product_vend_to_woo_tags]" id="product_vend_to_woo_tags" value="1" /> Sync tags between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Categories</strong>
			<label for="product_vend_to_woo_categories">
				<input type="checkbox" name="linksync[product_vend_to_woo_categories]" id="product_vend_to_woo_categories" value="1" /> Sync WooCommerce product categories with Vend
			</label>
			<br>
			<span>Use with caution as any existing product categories in WooCommerce not matching those in Vend will be deleted <a href="http://docs.linksync.com/x/6gBG" target="_blank">Click here for more information</a></span>
		</p>
		<p class="form-holder">
			<strong>Product Status</strong>
			<label for="product_vend_to_woo_product_status">
				<input type="checkbox" name="linksync[product_vend_to_woo_product_status]" id="product_vend_to_woo_product_status" value="1" /> Tick this option to Set new product to Pending
			</label>
		</p>
		<p class="form-holder">
			<strong>Were about to import your products from vend to woocommerce, choose the best option applicable to you.</strong>
			<select name="linksync[product_vend_to_woo_product_import_tags]" id="product_vend_to_woo_product_import_tags" class="form-field">
				<option value="off">Import all product</option>
				<option value="on">Import selected products</option>
			<select>
		</p>
		<p class="form-holder">
			<strong>Images</strong>
			<label for="product_vend_to_woo_images">
				<input type="checkbox" name="linksync[product_vend_to_woo_images]" id="product_vend_to_woo_images" value="1" /> Sync images from Vend to WooCommerce
			</label>
		</p>
		<p class="form-holder">
			<strong>Create New</strong>
			<label for="product_vend_to_woo_create_new">
				<input type="checkbox" name="linksync[product_vend_to_woo_create_new]" id="product_vend_to_woo_create_new" value="1" /> Create new products from Vend 
			</label>
		</p>
		<p class="form-holder">
			<strong>Delete</strong>
			<label for="product_vend_to_woo_delete">
				<input type="checkbox" name="linksync[product_vend_to_woo_delete]" id="product_vend_to_woo_delete" value="1" /> Sync product deletions between apps
			</label>
		</p>
	</div>
	
	<!-- Woo to Vend options -->
	<div id="linksync_product_woo_to_vend" class="linksync_product_syncing_options" style="display:none;">
		<h3>WooCommerce to Vend Options</h3>
		<p class="form-holder">
			<strong>Name/Title</strong>
			<label for="product_woo_to_vend_name_title">
				<input type="checkbox" name="linksync[product_woo_to_vend_name_title]" id="product_woo_to_vend_name_title" value="1" /> Sync the product titles between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Description</strong>
			<label for="product_woo_to_vend_description">
				<input type="checkbox" name="linksync[product_woo_to_vend_description]" id="product_woo_to_vend_description" value="1" /> Sync the product description between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Price</strong>
			<label for="product_woo_to_vend_price">
				<input type="checkbox" name="linksync[product_woo_to_vend_price]" id="product_woo_to_vend_price" value="1" /> Sync prices between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Quantity</strong>
			<label for="product_woo_to_vend_quantity">
				<input type="checkbox" name="linksync[product_woo_to_vend_quantity]" id="product_woo_to_vend_quantity" value="1" /> Sync product Quantity between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Tags</strong>
			<label for="product_woo_to_vend_tags">
				<input type="checkbox" name="linksync[product_woo_to_vend_tags]" id="product_woo_to_vend_tags" value="1" /> Sync tags between apps
			</label>
		</p>
		<p class="form-holder">
			<strong>Delete</strong>
			<label for="product_woo_to_vend_delete">
				<input type="checkbox" name="linksync[product_woo_to_vend_delete]" id="product_woo_to_vend_delete" value="1" /> Sync product deletions between apps
			</label>
		</p>
	</div>
	<p class="form-holder">
		<input type="submit" name="submit" value="Next Step" />
	</p>
	<div class="clearfix"></div>
</form>

<script type="text/javascript">
	jQuery(function() {
		// First Load
		jQuery('#product_syncing_type').val('disabled_sync');
		
		jQuery('#product_syncing_type').change(function() {
			var val = jQuery(this).val();
			if(val != 'disabled_sync') {
				switch(val) {
					case 'two_way':
						jQuery('#linksync_product_two_way').show('slow');
						jQuery('#linksync_product_vend_to_woo').hide('slow');
						jQuery('#linksync_product_woo_to_vend').hide('slow');
						break;
						
					case 'vend_to_wc-way':
						jQuery('#linksync_product_two_way').hide('slow');
						jQuery('#linksync_product_vend_to_woo').show('slow');
						jQuery('#linksync_product_woo_to_vend').hide('slow');
						break;
						
					case 'wc_to_vend':
						jQuery('#linksync_product_two_way').hide('slow');
						jQuery('#linksync_product_vend_to_woo').hide('slow');
						jQuery('#linksync_product_woo_to_vend').show('slow');
						break;
				}
			} else {
				jQuery('.linksync_product_syncing_options').hide('slow');
			}
		});
	});
</script>