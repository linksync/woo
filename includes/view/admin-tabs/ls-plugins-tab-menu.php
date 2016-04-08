<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

update_option('linksync_sycning_status', null);
/**
*   Check the current selected tab 
*/
if (isset($_REQUEST['page'], $_REQUEST['setting']) && $_REQUEST['page'] == 'linksync') {

    if ($_REQUEST['setting'] == 'config') {

        $configuration_tab = 'nav-tab-active';
    
    } else if ( $_REQUEST['setting'] == 'manage_api_key') {
        
        $manage_api_tab = 'nav-tab-active';

    } else if ($_REQUEST['setting'] == 'product_config') {
    
        $product_config_tab = 'nav-tab-active';

    } else if ($_REQUEST['setting'] == 'order_config') {
        
        $order_config_tab = 'nav-tab-active';

    } else if ($_REQUEST['setting'] == 'logs') {
        
        $logs_tab = 'nav-tab-active';

    } else{
        
        $configuration_tab = 'nav-tab-active';

    }
} else {
     $configuration_tab = 'nav-tab-active';
}
?> 
<h2>Linksync</h2>
<?php  

$file_perms = wp_is_writable(plugin_dir_path(__FILE__)); 

//Check if not writable
if(!$file_perms){ ?>
    <div class="error">
        <p><?php "Alert: File permission on <b>wp-content</b> will prevent linksync from syncing and/or functioning corectly.<a href='https://www.linksync.com/help/woocommerce-perms'>Please click here for more information</a>."; ?></p>
    </div> 
<?php } ?>

<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
    <a href="<?php echo admin_url('admin.php?page=linksync')?>" class="nav-tab <?php if (isset($configuration_tab)) echo $configuration_tab; ?>">Configuration</a>
    <a href="<?php echo admin_url('admin.php?page=linksync&setting=product_config')?>"  class="nav-tab <?php if (isset($product_config_tab)) echo $product_config_tab; ?> ">Product Syncing Setting</a>
    <a href="<?php echo admin_url('admin.php?page=linksync&setting=order_config')?>" class="nav-tab <?php if (isset($order_config_tab)) echo $order_config_tab; ?> ">Order Syncing Setting</a>
    <a href="<?php echo admin_url('admin.php?page=linksync&setting=logs')?>" class="nav-tab <?php if (isset($logs_tab)) echo $logs_tab; ?>">Logs</a>
</h2>
