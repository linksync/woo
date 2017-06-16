<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

// Adding API Key by Pop UP into our wp database
if (isset($_POST['add_apiKey'])) {
    global $wpdb;
    if (!empty($_POST['apikey'])) {

        $result = LS_Vend()->laid()->check_api_key($_POST['apikey']);
        $currentLaid = LS_Vend()->laid()->get_current_laid('');
        if ('' == $currentLaid) {
            LS_Vend()->laid()->update_current_laid(trim($_POST['apikey']));
        }
        $class1 = 'error';
        $class2 = 'updated';
        LSC_Log::add('Manage API Keys', 'success', 'API Key Added Successfully', $_POST['apikey']);
        $response = 'API Key has been added successfully !';

        if (isset($result['success'])) {
            $class1 = 'error';
            $class2 = 'updated';
            $response = $result['success'];
        } else {

            LS_Vend_Api_Key::delete_by_api(trim($_POST['apikey']));

            $class1 = 'updated';
            $class2 = 'error';
            $response = $result['error'];
        }

    } else {
        LSC_Log::add('Manage API Keys', 'fail', 'API Key is empty!!', '-');
        $response = "API Key is Empty!!";
    }
    if (is_vend()) {
        LS_Vend()->updateWebhookConnection();
    }
    ?>
    <script>

        jQuery('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);

    </script>
    <?php
// End - Adding API Key by Pop UP
}

if (isset($_POST['apikey_update'])) {
    $class1 = 'updated';
    $class2 = 'error';

    if (!empty($_POST['apikey'])) {
        $ls_api = LS_Vend()->laid()->get_laid_info($_POST['apikey']);
        if (isset($ls_api['errorCode'])) {
            LSC_Log::add('checkAPI Key', 'fail', 'Invalid API Key', '-');
            $response = 'Update Rejected. ' . $ls_api['userMessage'];

        } else {
            $result = LS_Vend()->laid()->check_api_key($_POST['apikey']);
            if (isset($result['success'])) {
                $status = 'Connected';
                LSC_Log::add('Manage API Keys', 'success', 'API key Updated Successfully', $_POST['apikey']);
                $response = 'API key Updated Successfully!! ';
                $class1 = 'error';
                $class2 = 'updated';
            } else {
                $status = 'InValid';
                LSC_Log::add('Manage API Keys', 'fail', 'Unable to Update!!', $_POST['apikey']);
                $response = $result['error'];
            }
        }

    } else {
        LSC_Log::add('Manage API Keys', 'fail', 'API key is empty!!', '-');
        $response = "API key is empty!!";
    }
    if (is_vend()) {
        LS_Vend()->updateWebhookConnection();
    }
    ?>
    <script>
        jQuery('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);

    </script>
    <?php
}

?>

<div id="tiptip_holder"  class="tip_top">
    <div id="tiptip_arrow"><div id="tiptip_arrow_inner"></div></div>
    <div id="tiptip_content">The linksync API Key is a unique key that's created when you link two apps via the linksync dashboard. You need a valid API Key for this linkysnc extension to work.
    </div>
</div><?php
if (!defined('ABSPATH')) {
    exit('Access is Denied'); // Exit if accessed directly
}
//$test_mode = 'enabled';
if (isset($test_mode) && $test_mode == 'enabled') {
    update_option('linksync_test', 'on');
} else {
    update_option('linksync_test', 'off');
}
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
   ?><script>
        jQuery(document).ready(function($){
            $('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
        });
</script><?php
}


$laid = LS_Vend()->laid()->get_current_laid();

?>
<div id="myModal" class="reveal-modal">
    <form method="POST" name="f1" action="">
        <center><span>Enter the API Key</span></center>
        <hr><br/>

        <center>
            <div>
                <b style="color: #0074a2;">API Key*:</b>
                <a href="https://www.linksync.com/help/woocommerce"
                   style="text-decoration: none"
                   target="_blank"
                   title=' Unsure about how to generate an API Key? Click the icon for a specific guidelines to get you up and running with linksync Vend & WooCommerce.'>
                    <img class="help_tip" src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16">
                </a>
                <input type="text" size="30" name="apikey" value="">
                <input type="submit" value="Save" onclick="return checkEmptyLaidKey()" class="button color-green" name="add_apiKey">
            </div>
        </center>
        <span class="ui-icon ui-icon-close close-reveal-modal"></span>
    </form>
</div>

<div id="modal_update_api" class="reveal-modal">
    <form  method="POST" name="f1" action="">
        <center><span>Update API Key</span></center>
        <hr><br>
        <center>
            <div>
                <b style="color: #0074a2;">API Key*:</b>
                <a href="https://www.linksync.com/help/woocommerce"
                   style="text-decoration: none"
                   target="_blank"
                   title=' Unsure about how to generate an API Key? Click the icon for a specific guidelines to get you up and running with linksync Vend & WooCommerce.'>
                    <img class="help_tip" src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16">
                </a>
                <input type="text" size="30" name="apikey"  value="<?php echo !empty($laid) ? $laid: ''; ?>">
                <input type="submit" value="Update" class='button color-green'  name="apikey_update">
            </div>
        </center>
        <span class="ui-icon ui-icon-close close-reveal-modal"></span>
    </form>
</div>

<div class="wrap">
    <div id="response" ></div>

    <fieldset>
        <legend>API Key configuration</legend>
        <div>
            <form method='POST'>
                <input type='submit' style="float: right;" class="button button-primary" title=' Use this button to reset Product and Order Syncing Setting.'   name='rest' value='Reset Syncing Setting'>
            </form>
        </div>

        <form method="post" onSubmit="return validate_laid();">
            <table cellpadding="8">
                <tr>
                    <td><b style='font-size: 14px;'>API Key*:</b></td>
                    <td>
                        <?php

                            $laids = empty($laid)? 'No Api Key': $laid;
                            echo '<b>',$laids,'</b>';
                        ?>
                        <a href="https://www.linksync.com/help/woocommerce"
                           style="text-decoration: none !important;">
                            <img title="The linksync API Key is a unique key that's created when you link two apps via the linksync dashboard. You need a valid API Key for this linkysnc extension to work."
                                 style="margin-bottom: -4px;"
                                 src="../wp-content/plugins/linksync/assets/images/linksync/help.png"
                                 height="16" width="16"/>
                        </a>
                    </td>
                    <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php 
                            $count_ls_laidkeys = LS_Vend_Api_Key::get_count();

                            if (empty($laid)) { ?>
                                <a href="#"  data-reveal-id="myModal" data-animation="fade" class="button button-primary">Add Api Key</a><?php
                            }else{
                                ?>
                                <a href="#" data-reveal-id="modal_update_api" class="button button-primary">Edit Api Key</a>
                                <?php
                            } ?>
                    </td>
                </tr>
            </table>
        </form>

    </fieldset>

    <fieldset>
        <legend>Update</legend>
        <b>Update URL : </b><a class="vend_to_woo_since_last_update" href="javascript:void(0)"><?php echo Linksync_Vend::getWebHookUrl(); ?></a>
        <br><br>Use the Trigger button to open the Update URL in a new window. linksync for WooCommerce is engineered to automatically have changes synced immediately for both products and orders, but you can use this option to manually trigger a sync.
        <p><input type="button" class="button button-primary vend_to_woo_since_last_update"   value="Trigger"> </p>

    </fieldset>
    <?php
    $status = get_option('linksync_status');
    if (isset($status) && $status == 'Active' || $status == 'Inactive') {
        ?>
        <fieldset>
            <legend>Linksync Status</legend>
            <form method="post">
                <p>Account Status : <b><?php echo (get_option('linksync_last_test_time') != '' ? get_option('linksync_status') : 'Failed / Not tested'); ?></b></p>
                <p>Connected URL : <b><?php echo (get_option('linksync_last_test_time') != '' ? get_option('linksync_connected_url') : 'Failed / Not tested') ?></b></p>
                <p>Last Message: <b><?php echo (get_option('linksync_last_test_time') != '' ? get_option('linksync_frequency') : 'Failed / Not tested') ?></b></p>
                <p>Connected: <b><?php echo (get_option('linksync_last_test_time') != '' ? get_option('linksync_connectedto') : 'Failed / Not tested') ?></b></p>
                <p>Connected To: <b><?php echo (get_option('linksync_last_test_time') != '' ? get_option('linksync_connectionwith') : 'Failed / Not tested') ?></b></p>
                <p>Last time tested: <b><?php echo (get_option('linksync_last_test_time') != '' ? get_option('linksync_last_test_time') : 'Failed / Not tested') ?></b></p>

            </form>
        </fieldset>
    <?php } ?>
</div>

<div class="ls-vend-sync-modal">
    <div class="ls-vend-sync-modal-content"
         style="width: 500px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 34%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">

        <center>
            <h4 id="sync_start_export_all" class="sync-modal-message">1Do you want to sync all product to Vend?</h4>
        </center>

        <div id="sync_progress_container" style="display: none;">

            <center>
                <br/>
                <div id="syncing_loader">
                    <p style="font-weight: bold;">Please do not close or refresh the browser while syncing is in progress.</p>
                </div>
            </center>
            <center>
                <div>
                    <div id="progressbar"></div>
                    <div class="progress-label">Loading...</div>
                </div>
                <?php
                if(isset($_GET['page']) && LS_Vend::$slug != $_GET['page']){
                    ?>
                    <p class="form-holder hide ls-dashboard-link" >
                        <a href="<?php echo LS_Vend_Menu::menu_url(); ?>" class="a-href-like-button">Go To Dashboard</a>
                    </p>
                    <?php
                }
                ?>
            </center>
            <br/>

        </div>

        <div id="pop_button">

            <div class="sync-buttons sync-to-vend-buttons">
                <input type="button" name="sync_all_product_to_vend" class="button hidesync product_sync_to_vend btn-yes" value="Yes">
                <input type="button" class="button hidesync ls-modal-close btn-no ls-modal-close"  name="close_syncall" value='No'/>
            </div>

            <div class="sync-buttons sync-to-woo-buttons">
                <input type="button" class="button product_sync_to_woo btn-yes" value="Yes">
                <input type="button" class="button btn-no ls-modal-close"  name="no" value='No'/>
            </div>

            <div class="sync-buttons sync-to-woo-buttons-since-last-update">
                <input type="button" class="button product_sync_to_woo_since_last_sync btn-yes" value="Yes">
                <input type="button" class="button btn-no ls-modal-close"  name="no" value='No'/>
            </div>
        </div>


    </div>

    <div class="ls-modal-backdrop close" ></div>
</div>