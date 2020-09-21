<?php if (!defined('ABSPATH')) exit('Access is Denied');
global $linksync_vend_laid;
?>

    <div id="tiptip_holder" class="tip_top">
        <div id="tiptip_arrow">
            <div id="tiptip_arrow_inner"></div>
        </div>
        <div id="tiptip_content">The linksync API Key is a unique key that's created when you link two apps via the
            linksync dashboard. You need a valid API Key for this linkysnc extension to work.
        </div>
    </div>
<?php

//Uncomment $check_duplicate_tool to Enable the tool
//$check_duplicate_tool = 'enabled';
/*
 * Reset Product and Order Syncing Setting
 */
if (isset($_POST['rest'])) {
    LS_Vend()->option()->reset_options();
    LSC_Log::add('Reset Option', 'success', "Reset Product and Order Syncing Setting", '-');
    $class1 = 'error';
    $class2 = 'updated';
    $response = 'Successfully! Reset Syncing Setting.';
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
        });
    </script><?php
}
?>
<?php
LS_Vend()->view()->display_add_api_key_modal();
LS_Vend()->view()->display_update_api_key_modal();
?>

    <div class="ls-wrap">
        <div id="response"></div>

        <div id="ls-vend-api-key-configuration" class="ls-vend-section">
            <br/>
            <?php LS_Vend_View_Config_Section::api_key_configuration(); ?>
        </div>

        <div id="ls-vend-sync-now"
             class="ls-vend-section">
            <?php LS_Vend_View_Config_Section::sync_now(); ?>
        </div>


        <div id="ls-vend-status" class="ls-vend-section">
            <?php LS_Vend_View_Config_Section::connection_status(); ?>

        </div>
    </div>

