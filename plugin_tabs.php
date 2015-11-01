 
<?php
update_option('linksync_sycning_status', null);
if (isset($_REQUEST['page']) && isset($_REQUEST['setting'])) {
    if ($_REQUEST['page'] == 'linksync' && $_REQUEST['setting'] == 'config') {
        $tab1 = 'nav-tab-active';
    } else if ($_REQUEST['page'] == 'linksync' && $_REQUEST['setting'] == 'manage_api_key') {
        $tab2 = 'nav-tab-active';
    } else if ($_REQUEST['page'] == 'linksync' && $_REQUEST['setting'] == 'product_config') {
        $tab3 = 'nav-tab-active';
    } else if ($_REQUEST['page'] == 'linksync' && $_REQUEST['setting'] == 'logs') {
        $tab4 = 'nav-tab-active';
    } else if ($_REQUEST['page'] == 'linksync' && $_REQUEST['setting'] == 'order_config') {
        $tab5 = 'nav-tab-active';
    }
} else {
    $tab1 = 'nav-tab-active';
}
?> 
<h2>Linksync</h2>
<?php  
$file_permission = substr(sprintf('%o', fileperms(plugin_dir_path(__FILE__))), -4); 
if ($file_permission != "0755" && $file_permission != "0775") {
   ?> <div class="error">
    <p><?php echo "Alert: File permission on <b>wp-content</b> will prevent linksync from syncing and/or functioning corectly.<a href='https://www.linksync.com/help/woocommerce-perms'>Please click here for more information</a>."; ?></p>
</div> 
    <?php
}?>
<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
    <a href="?page=linksync" class="nav-tab <?php if (isset($tab1)) echo $tab1; ?>">Configuration</a>
    <a href="?page=linksync&setting=manage_api_key" class="nav-tab <?php if (isset($tab2)) echo $tab2; ?>">Manage API Key</a>
    <a href="?page=linksync&setting=product_config"  class="nav-tab <?php if (isset($tab3)) echo $tab3; ?> ">Product Syncing Setting</a>
    <a href="?page=linksync&setting=order_config" class="nav-tab <?php if (isset($tab5)) echo $tab5; ?> ">Order Syncing Setting</a>
    <a href="?page=linksync&setting=logs" class="nav-tab <?php if (isset($tab4)) echo $tab4; ?>">Logs</a>
</h2>
