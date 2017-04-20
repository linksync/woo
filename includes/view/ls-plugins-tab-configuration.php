<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

// Adding API Key by Pop UP into our wp database
if (isset($_POST['add_apiKey'])) {
    global $wpdb;
    if (!empty($_POST['apikey'])) {

        $result = linksync::checkForConnection($_POST['apikey']);
        $currentLaid = LS_ApiController::get_current_laid('');
        if ('' == $currentLaid) {
            LS_ApiController::update_current_laid(trim($_POST['apikey']));
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
        $ls_api = LS_ApiController::get_key_info($_POST['apikey']);
        if (isset($ls_api['errorCode'])) {
            LSC_Log::add('checkAPI Key', 'fail', 'Invalid API Key', '-');
            $response = 'Update Rejected. ' . $ls_api['userMessage'];

        } else {
            $result = linksync::checkForConnection($_POST['apikey']);
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
    $linksync = new linksync;
    $linksync->linksync_restOptions();
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


$laid = LS_ApiController::get_current_laid();
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
    <?php
        $checked = get_option('linksync_test') == 'on' ? 'checked': '';
    ?>    
    <fieldset>
        <legend>API Key configuration</legend>
        <div>
            <form method='POST'>
                <input type='submit' class="button button-primary" title=' Use this button to reset Product and Order Syncing Setting.'   name='rest' value='Reset Syncing Setting'>
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
    <?php $webhook = get_option('linksync_addedfile'); ?>
    <fieldset style="display: <?php echo isset($webhook) && !empty($webhook) ? 'block': 'none';?>">
        <legend>Update</legend>
        <b>Update URL : </b><a onclick="show_confirm_box();" href="javascript:void(0)"><?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?></a>
        <br><br>Use the Trigger button to open the Update URL in a new window. linksync for WooCommerce is engineered to automatically have changes synced immediately for both products and orders, but you can use this option to manually trigger a sync.
        <p><input type="button" onclick="show_confirm_box();"   class="button button-primary"   value="Trigger"> </p>

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
<div id="pop_up_syncll" class="clientssummarybox" >
    <a name="lnkViews" href="javascript:;"><img id="syncing_close" src="../wp-content/plugins/linksync/assets/images/linksync/cross_icon.png "></a>
    <center><h4 style="display:none;" id="syncing_loader"><img src="../wp-content/plugins/linksync/assets/images/linksync/ajax-loader.gif"></h4></center>
    <center><div id="total_product"></div></center>
    <center><h4 id="export_report">Do you want to sync of product data from   <?php echo (get_option('linksync_connectedto') == 'WooCommerce') ? get_option('linksync_connectionwith') : get_option('linksync_connectedto'); ?> ?</h4></center> 
    <center><h4 id="sync_start"></h4></center> 
    <div id="button">
        <?php
        if (get_option('linksync_connectionwith') == 'Vend' || get_option('linksync_connectedto') == 'Vend') { ?>

            <input type="button" onclick="return sync_process_start();"  name="sync_all_product_to_vend"   class="button hidesync"   value="Yes"> 
        
        <?php } elseif (get_option('linksync_connectionwith') == 'QuickBooks Online' || get_option('linksync_connectedto') == 'QuickBooks Online') { ?>
        
            <input type="button" onclick="return sync_process_startQBO();"  name="sync_all_product_to_vend"  class="button hidesync"   value="Yes"> 
        
        <?php } ?> 

        <input  type="button" class="button hidesync"   name="close_syncall"  onclick="jQuery('#pop_up_syncll').fadeOut();"  value='No'/>
    </div>
</div> 
                <?php
                if (isset($check_duplicate_tool) && $check_duplicate_tool == 'enabled') {
                    if (isset($_POST['confirm'])) {
                        if (isset($_POST['product_sku']) && !empty($_POST['product_sku'])) {
                            if (isset($_POST['in_vend']) && $_POST['in_vend'] == 'on') {
                                $laids = get_option('linksync_laid');
                                foreach ($_POST['product_sku'] as $product_sku) {
                                    if (!empty($product_sku)) {
                                        $response = $apicall->linksync_deleteProduct($product_sku);
                                        if (isset($response) && !empty($response)) {
                                            if ($response['status'] == 'success') {
                                                $method = "Success";
                                                $message = 'Product Sku:' . $product_sku;
                                            } else {
                                                $method = "Error";
                                                $message = $response['details'];
                                            }
                                            LSC_Log::add('Product Deleted(In VEND Store):Clean Up', $method, $message, $laids);
                                        }
                                    }
                                }
                            }
                            if (isset($_POST['in_woo']) && !empty($_POST['in_woo'])) {
                                $laids = get_option('linksync_laid');
                                foreach ($_POST['product_sku'] as $product_id => $product_sku) {
                                    if (!empty($product_sku)) {
                                        $count = wp_delete_post($product_id); //use the product Id and delete the product
                                        if ($count) {
                                            $method = "Success";
                                            $message = 'Product Sku:' . $product_sku . ', Product Id in Woo:' . $product_id;
                                        } else {
                                            $method = "Error";
                                            $message = "Unable to Delete Product";
                                        }
                                        LSC_Log::add('Product Deleted(In Woo Store):Clean Up', $method, $message, $laids);
                                    }
                                }
                            }
                            $class1 = 'error';
                            $class2 = 'updated';
                            $message_resp = "Clean up Run Successfully!!";
                        } else {
                            $class1 = 'updated';
                            $class2 = 'error';
                            $message_resp = "No Product(s) Selected !!";
                        }
                        ?>  <script>
                                jQuery(document).ready(function($){
                                    $('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $message_resp; ?>").fadeIn().delay(3000).fadeOut(4000);
                                });
                            </script><?php
    }
                    ?>
    <!-- <script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery.bpopup.min.js"></script> -->
    <?php
    global $wpdb;
    $tr = '<tr>   <th width="10%"> <input id="selecctall" checked="checked" type="checkbox"  name="checkall"></th>
                        <th width="20%" > <strong style="color: #0074a2;">ID</strong> </th>
                        <th width="30%"> <strong style="color: #0074a2;">SKU</strong> </th> 
                        <th width="40%"> <strong style="color: #0074a2;">Product Name</strong> </th> 
                    </tr>';
    ?>
    <div style="left: 320.5px  !important;" id="duplicate" class="popup">
        <div class='popTitle'>
            <p class='popHeader'>Duplicate Product(s)</p>
        </div>
        <hr />
        <span class='closePopupBtn b-close'><span>X</span></span>     
        <form   action="" method="post"> 
            <?php
            $sql = "SELECT " . $wpdb->prefix . "postmeta.*,COUNT(*) as c  
                    FROM `" . $wpdb->prefix . "postmeta`  
                    WHERE meta_key='_sku' AND meta_value!='' 
                    GROUP BY " . $wpdb->prefix . "postmeta.meta_value HAVING c > 1";
                    
            $prod_query = $wpdb->get_results($sql, ARRAY_A);
            if (0 != $wpdb->num_rows) {
                ?>  <table width="100%" class="wp-list-table widefat plugins"> 
                    <thead>
                        <?php
                        echo $tr;
                        foreach ($prod_query as $product_data) {
                            $sql_query = "SELECT " . 
                                            $wpdb->prefix . "postmeta.* ," . 
                                            $wpdb->prefix . "posts.ID," . 
                                            $wpdb->prefix . "posts.post_title 
                                    FROM " . 
                                            $wpdb->prefix . "postmeta 
                                    JOIN `" . $wpdb->prefix . "posts` 
                                            ON(" . $wpdb->prefix . "postmeta.post_id=" . $wpdb->prefix . "posts.ID)  WHERE " . $wpdb->prefix . "postmeta.meta_value ='" . $product_data['meta_value'] . "' AND " . $wpdb->prefix . "postmeta.meta_key='_sku'";
                           
                            $product_details = $wpdb->get_results($sql_query, ARRAY_A);

                           if(0 != $wpdb->num_rows){
                                foreach ($product_details as $product_wc1) {
                                    ?>
                                    <tr>
                                        <td><input style="margin-left: 8px;" class="checkbox1" checked="checked"  type="checkbox" name="product_sku[<?php echo $product_wc1['ID']; ?>]" value="<?php echo $product_wc1['meta_value']; ?>" /></td>
                                        <td><a target="_blank" href="post.php?post=<?php echo $product_wc1['ID']; ?>&action=edit"><?php echo $product_wc1['ID']; ?></a></td>
                                        <td><?php echo $product_wc1['meta_value']; ?></td>
                                        <td><a target="_blank"  target="_blank" href="edit.php?s=<?php echo $product_wc1['post_title']; ?>&post_status=all&post_type=product"><?php echo $product_wc1['post_title']; ?></a></td>
                                    </tr> 
                                    <?php
                                }
                            }
                        }
                        ?></thead>
                </table> <?php
            } else {
                        ?><table width="100%"> 
                    <thead><?php echo $tr; ?>
                    </thead>
                </table><div style="text-align:center;margin-top: 30px;color: red;">No Product Found!</div><?php
            } ?>        <br>
            
            <div style="text-align:center;margin-top: 30px;  margin-right: 30px;"><input type="checkbox" name="in_woo" checked="checked"> In Woo-Commerce <input style="margin-left:150px"type="checkbox" name="in_vend" checked="checked"> In VEND</div>
            <div style="text-align:center;margin-top: 20px;">
                <input type="submit" name="confirm" value="Confirm" class="button button-primary" />
            </div>
        </form>
    </div> 
    <input style="margin-top: 10px;margin-bottom: 20px;"type='button' class="button button-primary" onClick="popup('duplicate')" name='duplicate' value='Duplicate Product SKU'>
<?php } ?>

<div id="please-wait" class="loader-please-wait" style="display: none;">
    <div class="loader-content">

        <h3 id="h2_linksync">Linksync is Updating data<br>Please wait...</h3>
        <p><img style="color: blue" src="../wp-content/plugins/linksync/assets/images/linksync/loading_please_wait.gif"></p>
        </div>
    </div> 
    <script>

       
        function popup(id){ 
            jQuery('#'+id).bPopup( {
                positionStyle: 'absolute' //'fixed' or 'absolute'
            });
        } 
        jQuery(document).ready(function($) {
            $('#selecctall').click(function(event) {  //on click 
                if(this.checked) { // check select status
                    $('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = true;  //select all checkboxes with class "checkbox1"               
                    });
                }else{
                    $('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "checkbox1"                       
                    });         
                }
            });
    
        }); 
        
        function  show_confirm_box() {  
            if(jQuery("#pop_up_syncll").is(":visible")==false){
                jQuery('#syncing_loader').hide();
                jQuery('#button').show();
                jQuery("#export_report").show(); 
                jQuery('#pop_up_syncll').fadeIn();  
            }  
        }
    
        function sync_process_start() {
            <?php
                update_option('product_detail', NULL);
                update_option('image_process', 'complete');
                update_option('prod_last_page', NULL);
                //update_option('product_image_ids', NULL);
            ?> 
            jQuery("#export_report").hide(); 
            jQuery('#syncing_loader').show();
            jQuery('#sync_start').show();
            jQuery('#button').hide();
            jQuery('#sync_start').html("<h3>Starting....</h3>"); 
            importProduct(); 
        }

    var communication_key='<?php echo get_option('webhook_url_code'); ?>';
    var check_error=0;
    function importProduct(){ 
        var ajaxupdate=  jQuery.ajax({
            type: "POST",  
            dataType:'json', 
            url: "<?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?>", 
            success:function(dataupper){
                console.log(dataupper);
                if(dataupper.message!=''){
                    jQuery("#please-wait").css("display", "none");
                    jQuery("#sync_start").show();    
                    jQuery("#sync_start").html("<p style='font-size:15px;'><b>"+dataupper.message+"</b>"); 
                    jQuery("#sync_start").hide(1500);
                    jQuery("#pop_up_syncll").hide(1500);
                    jQuery("#syncing_loader").hide(1500);
                }else if(dataupper.image_process=='running'){
                    jQuery("#please-wait").css("display", "none"); 
                    uploading_process_start_for_image(dataupper.product_count);
                }else if(dataupper.image_process=='complete'){
                    jQuery("#please-wait").css("display", "block");
                    importProduct();
                }
            },
            error: function(xhr, status, error){         
                console.log("Error Empty Response");
                console.log(xhr);
                console.log(status);
                console.log(error);
                if(check_error==10){
                    check_error =0; 
                    ajaxupdate.abort();
                    jQuery("#sync_start").html("<p style='font-size:15px;color:red;'><b>Internal Connection Error : Please refresh and try again!</b>"); 
                    jQuery("#syncing_loader").hide(1500);
                    jQuery('#syncing_close').css('display','block');
                }else{
                    importProduct(); 
                }
                check_error++;
            },
            statusCode: {
                404: function(){
                    console.log('Got 404 status File not found! ');  
                }, 
                200: function(){
                    // jQuery("#export_report").html(i++);
                }, 
                504:function(){
                    console.log('Got 504 Gateway Time-out! ');  
                }, 
                500:function(){
                    console.log('Got 500 Error ! ');  
                }
            }
                      
        }); 
    }
    
    jQuery(document).on("click","a[name='lnkViews']", function (e) { 
        jQuery("#pop_up_syncll").fadeOut(500); 
        location.reload();  
    });
    /*
     * Quick Book Online Product Import
     */
    function sync_process_startQBO() {
        <?php
            update_option('product_detail', NULL);
            update_option('prod_last_page', NULL);
        ?> 
        jQuery("#export_report").hide(); 
        jQuery('#syncing_loader').show();
        jQuery('#sync_start').show();
        jQuery('#button').hide();
        jQuery('#sync_start').html("<h3>Starting....</h3>"); 
        importProductQBO();
         
    }

    function importProductQBO(){ 
        var check='on';
        jQuery.ajax({
            type: "POST",  
            dataType:'json', 
            url: "<?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?>", 
            success:function(dataupper){  
                check='off'; 
                clearInterval(myVar);
                jQuery("#sync_start").show();    
                jQuery("#sync_start").html("<p style='font-size:15px;'><b>"+dataupper.message+"</b>"); 
                jQuery("#sync_start").hide(1500);
                jQuery("#pop_up_syncll").hide(1500);
                jQuery("#syncing_loader").hide(1500);
            },
            error: function(xhr, status, error) {         
                console.log("Error Empty Response");
                console.log(xhr);
                console.log(status);
                console.log(error);
                importProductQBO(); 
            },
            statusCode: {
                404: function(){
                    console.log('Got 404 status File not found! '); 
                }, 
                200: function(){ 
                    
                }, 
                504:function(){
                    console.log('Got 504 Gateway Time-out! ');  
                }, 
                500:function(){
                    console.log('Got 500 Error ! '); 
                }
            }
                      
        });
        if(check=='on'){
            var myVar=  setInterval(function(){ 
                jQuery.ajax({
                    type: "POST",  
                    dataType:'json', 
                    data:{'communication_key':communication_key},
                    url: "../wp-content/plugins/linksync/report.php",
                    success:function(data){  
                        jQuery("#sync_start").html("linksync update is running.<br> Importing from product <b>"+data.total_product+"</b>");
                    }});    
            },2000);
        } 

    } 
    function ajaxRequestForproduct_image(i,totalreq,total_product,product_count,status){ 
        jQuery("#sync_start").html("linksync update is running.<br> Importing from product <b>"+ (product_count + 1) +" of "+total_product+"</b>");
        var ajaxobj= jQuery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'product_id':i,'communication_key':communication_key,'check_status':status},
            url: '../wp-content/plugins/linksync/image_uploader.php',
            success:function(data){ 
                var result=data.response;
                if(result.image=='on'){
                    if(result.gallery == 'success' && result.thumbnail=='success'){
                        status='send';
                        i++;
                        product_count++;  
                    }else{
                        status='resend';
                        console.log('Resend Request for the same product: Process Not complete yet');
                    }  
                }else{
                    status='send';
                    i++;
                    product_count++; 
                }  
                 
            },
            error: function(xhr, status, error){ 
                status='resend';
                console.log(xhr);
                console.log(status);
                console.log(error);
                console.log('Resend Request for the same product');
            },
            complete:function(responsedata){ 
                if(responsedata){  
                    if(i>totalreq){ 
                        jQuery.ajax({
                            url:  '../wp-content/plugins/linksync/image_uploader.php',
                            type: 'POST',
                            data: { "get_total": "1",'communication_key':communication_key},
                            success: function(response) { 
                                jQuery("#please-wait").css("display", "block");
                                importProduct();
                            }
                        }); 
                        ajaxobj.abort();
                        return false;
                    }else {
                        console.log(i);
                        ajaxRequestForproduct_image(i,totalreq,total_product,product_count,status);
                    }
                                     
                }  
            },  
            statusCode: {
                404: function(){  
                    console.log('File not Found !'); 
                }, 
                200: function(){
                    // linksync_jQuery("#export_report").html(i++);
                }, 
                504:function(){
                    console.log('Got 504 status code in response then request again '); 
                }
            }
        }); 
           
    }
    function uploading_process_start_for_image(product_count) { 
        var dataupper;
        var communication_key='<?php echo get_option('webhook_url_code'); ?>';
        jQuery.ajax({
            type: "POST",  
            dataType:'json',
            data:{'communication_key':communication_key},
            url: '../wp-content/plugins/linksync/image_uploader.php',
            success:function(dataupper){ 
                if(dataupper.total_post_id!=0){ 
                    var totalreq=dataupper.total_post_id;  
                    ajaxRequestForproduct_image(1,totalreq,dataupper.total_product,product_count,'send'); 
                } 
            }
        }); 
    } 

    function checkEmptyLaidKey() {
        var laidField = jQuery("input[name='apikey']");
        if (laidField.val() == '') {
            laidField.css('border', '1px solid red');
            return false;
        } else {
            return true;
        }
    }
    </script> 