<?php if (!defined('ABSPATH')) exit('Access is Denied'); ?>

<h2>Linksync (Version: <?php echo Linksync_Vend::$version; ?>)</h2>
<?php
$webHookUpdate = LS_Vend()->updateWebhookConnection();
if (
    isset($webHookUpdate['errorCode']) &&
    isset($webHookUpdate['userMessage']) &&
    'Connection to the update URL failed.' == $webHookUpdate['userMessage']
) {
    LS_Message_Builder::notice('Connection to the update URL failed. Please check our <a href="https://help.linksync.com/hc/en-us/articles/115000591510-Connection-to-the-update-URL-failed" target="_blank">FAQ</a> section to find possible solutions.', 'error ');
}


$file_perms = wp_is_writable(plugin_dir_path(__FILE__));

//Check if not writable
if (!$file_perms) {
    LS_Message_Builder::notice("Alert: File permission on <b>wp-content</b> will prevent linksync from syncing and/or functioning corectly.<a href='https://www.linksync.com/help/woocommerce-perms'>Please click here for more information</a>.");
}

$laid_info = LS_Vend()->laid()->get_laid_info();
if (!empty($laid_info)) {
    LS_Vend()->laid()->update_current_laid_info($laid_info);
}

do_action('before_linksync_tab_menu');
LS_Vend_Menu::output_menu_tabs();
?>

