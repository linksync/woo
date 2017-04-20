<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

update_option('linksync_sycning_status', null);
/**
*   Check the current selected tab 
*/
$settings_tabs = array(
    'config',
    'product_config',
    'order_config',
    'logs',
    'support',
);
$active_tab = 'config';
if (isset($_REQUEST['page'], $_REQUEST['setting']) && $_REQUEST['page'] == 'linksync') {

    if (in_array($_REQUEST['setting'], $settings_tabs)) {
        $active_tab = $_REQUEST['setting'];
    }

}



?>
<h2>Linksync (Version: <?php echo linksync::$version; ?>)</h2>
<?php






if (is_vend()) {
    $webhook = LS_Vend()->updateWebhookConnection();
}

$file_perms = wp_is_writable(plugin_dir_path(__FILE__)); 

//Check if not writable
if(!$file_perms){ ?>
    <div class="error">
        <p><?php "Alert: File permission on <b>wp-content</b> will prevent linksync from syncing and/or functioning corectly.<a href='https://www.linksync.com/help/woocommerce-perms'>Please click here for more information</a>."; ?></p>
    </div> 
<?php } ?>

<?php
    $laid_info = LS_ApiController::get_key_info();
    if (!empty($laid_info)) {
        LS_ApiController::update_current_laid_info($laid_info);
    }

    do_action('before_linksync_tab_menu');
?>
<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
    <a href="<?php echo admin_url('admin.php?page=linksync') ?>" class="nav-tab <?php echo ('config' == $active_tab) ? 'nav-tab-active' : ''; ?>">Configuration</a>
    <a href="<?php echo admin_url('admin.php?page=linksync&setting=product_config') ?>" class="nav-tab <?php echo ('product_config' == $active_tab) ? 'nav-tab-active' : ''; ?> ">Product Syncing Setting</a>
    <a href="<?php echo admin_url('admin.php?page=linksync&setting=order_config') ?>" class="nav-tab <?php echo ('order_config' == $active_tab) ? 'nav-tab-active' : ''; ?> ">Order Syncing Setting</a>
    <a href="<?php echo admin_url('admin.php?page=linksync&setting=support') ?>" class="nav-tab <?php echo ('support' == $active_tab) ? 'nav-tab-active' : ''; ?>">Support</a>
    <a href="<?php echo admin_url('admin.php?page=linksync&setting=logs') ?>" class="nav-tab <?php echo ('logs' == $active_tab) ? 'nav-tab-active' : ''; ?>">Logs</a>
</h2>
