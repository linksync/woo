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

    <div class="wrap">
        <div id="response"></div>

        <fieldset>
            <legend>API Key configuration</legend>
            <div>
                <form method='POST' id="frmResetSyncingSettings">
                    <input type='submit' style="float: right;" class="button button-primary"
                           title=' Use this button to reset Product and Order Syncing Setting.' name='rest'
                           value='Reset Syncing Setting'>
                    <span class="spinner"></span>
                </form>
            </div>


            <table cellpadding="8">
                <tr>
                    <td><b style='font-size: 14px;'>API Key*:</b></td>
                    <td>
                        <?php echo '<b class="apikeyholder">', empty($linksync_vend_laid) ? 'No Api Key' : $linksync_vend_laid, '</b>'; ?>
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
                        if (empty($linksync_vend_laid)) {
                            echo '<a href="#"  data-reveal-id="myModal" data-animation="fade" class="button button-primary">Add Api Key</a>';
                        } else {
                            echo '<a href="#" data-reveal-id="modal_update_api" class="button button-primary">Edit Api Key</a>';
                        }
                        ?>
                    </td>
                </tr>
            </table>


        </fieldset>

        <fieldset>
            <legend>Update</legend>
            <b>Update URL : </b><a class="vend_to_woo_since_last_update"
                                   href="javascript:void(0)"><?php echo Linksync_Vend::getWebHookUrl(); ?></a>
            <br><br>Use the Trigger button to open the Update URL in a new window. linksync for WooCommerce is
            engineered to automatically have changes synced immediately for both products and orders, but you can use
            this option to manually trigger a sync.
            <p><input type="button" class="button button-primary vend_to_woo_since_last_update" value="Trigger"></p>

        </fieldset>
        <?php
        $status = get_option('linksync_status');
        if (isset($status) && $status == 'Active' || $status == 'Inactive') {
            ?>
            <fieldset>
                <legend>Linksync Status</legend>
                <form method="post">
                    <p>Account Status :
                        <b><?php echo(get_option('linksync_last_test_time') != '' ? get_option('linksync_status') : 'Failed / Not tested'); ?></b>
                    </p>
                    <p>Connected URL :
                        <b><?php echo(get_option('linksync_last_test_time') != '' ? get_option('linksync_connected_url') : 'Failed / Not tested') ?></b>
                    </p>
                    <p>Last Message:
                        <b><?php echo(get_option('linksync_last_test_time') != '' ? get_option('linksync_frequency') : 'Failed / Not tested') ?></b>
                    </p>
                    <p>Connected:
                        <b><?php echo(get_option('linksync_last_test_time') != '' ? get_option('linksync_connectedto') : 'Failed / Not tested') ?></b>
                    </p>
                    <p>Connected To:
                        <b><?php echo(get_option('linksync_last_test_time') != '' ? get_option('linksync_connectionwith') : 'Failed / Not tested') ?></b>
                    </p>
                    <p>Last time tested:
                        <b><?php echo(get_option('linksync_last_test_time') != '' ? get_option('linksync_last_test_time') : 'Failed / Not tested') ?></b>
                    </p>

                </form>
            </fieldset>
        <?php } ?>
    </div>

<?php LS_Vend()->view()->display_syncing_modal(); ?>