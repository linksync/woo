<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

    require_once LS_PLUGIN_DIR.'/classes/Class.linksync.php';
    require_once LS_INC_DIR. 'view/ls-plugins-tab-menu.php'; # Handle Tabs

?>

<div class="wrap" id="ls-main-wrapper">
    <div id="response"></div>

    <?php
        global $wpdb;
        //Send log feature
        $testMode = get_option('linksync_test');
        $LAIDKey = LS_ApiController::get_current_laid();
        $apicall = new linksync_class($LAIDKey, $testMode);

		if( !empty($LAIDKey) ){
			linksync::checkForConnection($LAIDKey);
		}

        LS_Vend()->view();

    ?>

</div> 