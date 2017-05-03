<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied'); ?>

<div class="se-pre-con"></div>

<?php

    $LAIDKey = get_option('linksync_laid');
    $testMode = get_option('linksync_test');
    if (!empty($LAIDKey)) {
        //Each API Key will have its own 'group' of settings per the following requirements listed below.
        $ls_connected_with  = get_option('linksync_connectionwith');
        $ls_connected_to    = get_option('linksync_connectedto');

        if ($ls_connected_with == 'Vend' ||  $ls_connected_to == 'Vend') {
            include_once LS_INC_DIR . 'apps/vend/vend_product_config.php';
        }  else {
            LS_User_Helper::setUpLaidInfoMessage();
        }
    } else {
        ?>
        <div class="error notice">
            <h3><?php echo LS_Constants::NOT_CONNECTED_MISSING_API_KEY; ?> </h3>
        </div>
        <?php
    }
?> 