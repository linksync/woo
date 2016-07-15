<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied'); ?>

<div class="se-pre-con"></div>

<?php

    $LAIDKey = get_option('linksync_laid');
    if (!empty($LAIDKey)) {
        $ls_connected_to    = get_option('linksync_connectedto');
        $ls_connected_with  = get_option('linksync_connectionwith');

        if ( $ls_connected_with == 'Vend' || $ls_connected_to == 'Vend') {
            
            include_once LS_INC_DIR.'apps/vend/vend_order_config.php';

        }  else {
            echo "<p align=center style='color:red;font-size:17px;margin-top:150px;'><b>" . $LAIDKey . "</b> does not appear to be a valid API Key</p>";
        }
    } else {
        echo "<p align=center style='color:red;font-size:17px;margin-top:150px;'>Not Connected Or Missing API Key</p>";
    }
?> 