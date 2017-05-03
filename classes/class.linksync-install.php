<?php
/* 
 * All installation process
 */
 
class Linksync_installation {
	
	public static function init()
	{
		add_action( 'init', array( __CLASS__, 'install' ), 5 );
	}
	
	public static function install()
	{
		if(get_option('linksync_do_activation_redirect')) {
			delete_option( 'linksync_do_activation_redirect' );
			wp_safe_redirect( admin_url( 'admin.php?page=linksync-wizard' ) );
			exit();
		}
	}
	
	public static function wizard_handler($res)
	{
		?>
			<p id="logo"><img src="<?php echo LS_PLUGIN_URL ?>assets/images/linksync/logo.png" alt="" /></p>
			<div class="wizard-content">
				<div class="content-wrap">
					<?php
					
					$step = isset($_GET['step'])?$_GET['step']:1;
					
					switch($step)
					{
						case 1:
							// Set up API Key
                            $laid = LS_ApiController::get_current_laid();
							include_once(LS_PLUGIN_DIR.'includes/view/wizard/setup-api.php');
							break;
							
						case 2:
							// Set up Product syncing options
                            $selected_product_syncing_type = '';
							$view_pcontent = '';
							if(isset($res['connected_to']) && $res['connected_to'] == 'QuickBooks Online') {
								$view_pcontent = '-qbo';
								$product_option = LS_QBO()->product_option();
                                $product_syncing_type = $product_option->sync_type();
                                $match_product_with = $product_option->match_product_with();
                                $name_or_title = $product_option->title_or_name();
                                $description = $product_option->description();
                                $price = $product_option->price();
                                $quantity = $product_option->quantity();
                                $categories = $product_option->category();
                                $product_status = $product_option->product_status();
                                $create_new = $product_option->create_new();
                                $delete = $product_option->delete();
							} else if(isset($res['connected_to']) && $res['connected_to'] == 'Vend') {
                                $product_option = LS_Vend()->product_option();
                                $product_syncing_type = $product_option->sync_type();
                                $name_or_title = $product_option->nameTitle();
                                $description = $product_option->description();
                                $short_description = $product_option->shortDescription();
                                $price = $product_option->price();
                                $quantity = $product_option->quantity();
                                $tag = $product_option->tag();
                                $category = $product_option->category();
                                $product_status = $product_option->productStatus();
                                $image = $product_option->image();
                                $create_new = $product_option->createNew();
                                $delete = $product_option->delete();

                                $attribute = $product_option->attributes();
                                $attribute_visible_on_product_page = $product_option->attributeVisibleOnProductPage();

                            }
							include_once(LS_PLUGIN_DIR.'includes/view/wizard/product-syncing'. $view_pcontent .'.php');
							break;
							
						case 3:
							// Set up Order syncing options
							$view_ocontent = '';
                            if (isset($res['connected_to']) && $res['connected_to'] == 'QuickBooks Online') {
                                $view_ocontent = '-qbo';
                                $order_option = LS_QBO()->order_option();
                                $order_syncing_type = $order_option->sync_type();
                                $export_customer_data = $order_option->customer_export();
                                $post_to_quickbooks_as = $order_option->receipt_type();

                                $order_number_for_quickbooks = $order_option->order_number();
                            } else if (isset($res['connected_to']) && $res['connected_to'] == 'Vend') {
                                $order_option = LS_Vend()->order_option();
                                $order_syncing_type = $order_option->sync_type();
                                $customer_export = $order_option->customerExport();
                                $customer_import = $order_option->customerImport();
                                $order_status_wootovend = $order_option->orderStatusWooToVend();
                                $order_status_vendtowoo = $order_option->orderStatusVendToWoo();

                            }
							include_once(LS_PLUGIN_DIR.'includes/view/wizard/order-syncing'. $view_ocontent .'.php');
							break;
					}
					
					?>
				</div>
			</div>
		<?php
	}
}