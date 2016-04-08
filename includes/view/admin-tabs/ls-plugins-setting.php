<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

    require_once LS_PLUGIN_DIR.'/classes/Class.linksync.php';
    require_once LS_INC_DIR. 'view/admin-tabs/ls-plugins-tab-menu.php'; # Handle Tabs 

?>

<div class="wrap"> 
    <div id="response"></div>

    <?php
        global $wpdb;
        $linksync = new linksync();
        //Send log feature
        $testMode = get_option('linksync_test');
        $LAIDKey = get_option('linksync_laid');
        $apicall = new linksync_class($LAIDKey, $testMode);

        if (isset($_GET['setting'],$_GET['page']) && $_GET['page'] == 'linksync') {
            
            if ($_GET['setting'] == 'logs') {
    
                include_once LS_INC_DIR. 'view/admin-tabs/ls-plugins-tab-logs.php'; 
     
            }  elseif ($_GET['setting'] == 'product_config') {

                include_once LS_INC_DIR . 'view/admin-tabs/ls-plugins-tab-product-config.php';
                
            } elseif ($_GET['setting'] == 'order_config') {

                require_once LS_INC_DIR. 'view/admin-tabs/ls-plugins-tab-order-config.php';

            } else {
                include_once LS_INC_DIR . 'view/admin-tabs/ls-plugins-tab-configuration.php';    
            }
        } else {
            include_once LS_INC_DIR . 'view/admin-tabs/ls-plugins-tab-configuration.php';
        }
    ?>

</div> 