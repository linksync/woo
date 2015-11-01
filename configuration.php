<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="../wp-content/plugins/linksync/css/jquery-ui.css">
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-ui.js"></script>
<script>
    var linksync_jQuery1=jQuery.noConflict( true );
    linksync_jQuery1(function() {
        linksync_jQuery1(document).tooltip();
    });
</script>
<style>
    label {
        display: inline-block;
        width: 5em;
    }
</style><div id="tiptip_holder" style="margin:183px 185px 64px 608px !important;display: none;" class="tip_top">
    <div id="tiptip_arrow" style="margin-left: 74.5px; margin-top: 47px;"><div id="tiptip_arrow_inner"></div></div>
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
    linksync_class::add('Reset Option', 'success', "Reset Product and Order Syncing Setting", '-');
    $class1 = 'error';
    $class2 = 'updated';
    $response = 'Successfully! Reset Syncing Setting.';
   ?><script>
       linksync_jQuery1(document).ready(function() {
           linksync_jQuery1('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
       });
</script><?php
}
// Adding API Key by Pop UP into our wp database 
if (isset($_POST['add_apiKey']) || isset($_POST['laid_save'])) {
    global $wpdb;
    if (!empty($_POST['apikey'])) {
        //  $query = mysql_query("SELECT api_key FROM " . $wpdb->prefix . "linksync_laidKey WHERE api_key='" . $_POST['apikey'] . "'");
        //  if (0 == mysql_num_rows($query)) {
        $query_api_key = mysql_query("SELECT api_key  FROM " . $wpdb->prefix . "linksync_laidKey");
        if (0 == mysql_num_rows($query_api_key)) {
            if ($wpdb->insert($wpdb->prefix . 'linksync_laidKey', array('api_key' => trim($_POST['apikey']), 'status' => 'Under Process', 'date_add' => date('Y/m/d')))) {
                $result = linksync::checkForConnection($_POST['apikey']);
                if (get_option('linksync_laid') == '') {
                    update_option('linksync_laid', trim($_POST['apikey']));
                }
                $class1 = 'error';
                $class2 = 'updated';
                linksync_class::add('Manage API Keys', 'success', 'API Key Added Successfully', $_POST['apikey']);
                $response = 'API Key has been added successfully.!';
            } else {
                linksync_class::add('Manage API Keys', 'fail', 'Unable to Insert', $_POST['apikey']);
            }

            if (isset($result['success'])) {
                //If Connection is established than save to database: 
                $response = $result['success'];
                $class1 = 'error';
                $class2 = 'updated';
            } else {
                $wpdb->delete($wpdb->prefix . 'linksync_laidKey', array('api_key' => trim($_POST['apikey'])));
                $response = $result['error'];
                $class1 = 'updated';
                $class2 = 'error';
            }
        } else {
            $class1 = 'updated';
            $class2 = 'error';
            $response = 'API Key is already exists!';
        }
//        else {
//            $result = linksync::checkForConnection($_POST['apikey']);
//            if (isset($result['success'])) {
//                $response = $result['success'];
//                $class1 = 'error';
//                $class2 = 'updated';
//            } else {
//                $response = $result['error'];
//                $class1 = 'updated';
//                $class2 = 'error';
//            }
//            if (isset($_POST['add_apiKey'])) {
//                $class1 = 'updated';
//                $class2 = 'error';
//                $response = "Error : This <b><i>$_POST[apikey]</i></b> is already exists !!";
//                linksync_class::add('Manage API Keys', 'fail', "$_POST[apikey] is already exists", $_POST['apikey']);
//            }
//        }
    } else {
        linksync_class::add('Manage API Keys', 'fail', 'API Key is empty!!', '-');
        $response = "API Key is Empty!!";
    }
   ?>
<script>
    linksync_jQuery1(document).ready(function() {
        linksync_jQuery1('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
    });
</script>
    <?php
// End - Adding API Key by Pop UP 
}

$laid = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'linksync_laidKey`');
?>  
<div id="myModal" class="reveal-modal">
    <form method="POST" name="f1" action="">
        <center><span style="color: #0074a2;font-size: 18px;">Enter the API Key</span></center>
        <hr>
        <div style="float: left;font-size: 14px;color: #0074a2; text-align: right;margin-top: 4px;">API Key*:</div>
        <div style="float: left;margin-left: 10px;">
            <input type="text" name="apikey" size="40" value=""></div><a href="https://www.linksync.com/help/woocommerce"><img class="help_tip" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
        <br><br><br>
        <center><input type="submit" style="color: green;" value="Add" class="button" onclick="return checkEmptyLaidKey();" name="add_apiKey"></center>
    </form>
</div>
<div class="wrap">
    <div id="response" style="padding: 15px; margin-top: 25px; display: none;"></div>
    <?php
    if (get_option('linksync_test') == 'on') {
        $checked = 'checked';
    } else {
        $checked = '';
    }
    ?>    <fieldset>
        <legend>API Key configuration</legend><div style='float: right;padding: 9px;'><form method='POST'><input type='submit' class="button button-primary" title=' Use this button to reset Product and Order Syncing Setting.'   name='rest' value='Reset Syncing Setting'></form></div>
        <form method="post" onSubmit="return validate_laid();">
            <table cellpadding="8">
                <tr>
                    <td><b style='font-size: 14px;'>API Key*:</b></td>
                    <td>
                        <select name="apikey">
                            <option >Select API Key</option>
                            <?php
                            $laids = get_option('linksync_laid');
                            if (!empty($laids)) {
                                $groups = explode(",", $laids);
                            } else {
                                $groups = array();
                            }
                            foreach ($laid as $value) {
                                $checkSelected = 0;
                                foreach ($groups as $group) {
                                    if ($group == $value->api_key)
                                        $checkSelected = 1;
                                }
                                ?>
                                <option id="linksync_laid" size="25"  value="<?php echo $value->api_key ?>" <?php echo isset($value->api_key) && $checkSelected == 1 ? "selected" : ''; ?>><?php echo $value->api_key ?></option>
                            <?php } ?>
                        </select><a href="https://www.linksync.com/help/woocommerce"><img title="The linksync API Key is a unique key that's created when you link two apps via the linksync dashboard. You need a valid API Key for this linkysnc extension to work." style="margin-bottom: -4px;" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
                        &nbsp;&nbsp;&nbsp;&nbsp;<?php if ($wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'linksync_laidKey') == 0) { ?><a href="#"  data-reveal-id="myModal" data-animation="fade" class="add-new-h2">Add Api Key</a><?php } ?></td>
                </tr>
                <?php
                if ($wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'linksync_laidKey') >= 2) {
                    ?>
                    <tr>
                        <td colspan="2"> <input type="submit" class="button button-primary" id="laid_save" name="laid_save" value="Use This API Key" /> </td>
                    </tr>
                <?php } ?> 
            </table>
        </form>

    </fieldset>
    <?php $webhook = get_option('linksync_addedfile'); ?>
    <fieldset style="display: <?php
    if (isset($webhook) && !empty($webhook)) {
        echo "block";
    } else {
        echo "none";
    }
    ?>"><legend>Update</legend>
        <b>Update URL : </b><a onclick="show_confirm_box();" href="javascript:void(0)"><?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?></a>
        <br><br>Use the Trigger button to open the Update URL in a new window. linksync for WooCommerce is engineered to automatically have changes synced immediately for both products and orders, but you can use this option to manually trigger a sync.<p>
            <input type="button" onclick="show_confirm_box();"   class="button button-primary"   value="Trigger"> </p>

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
<div id="pop_up_syncll" class="clientssummarybox" style=" width:600px !important; top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;         padding: 10px !important;         line-height: 30px !important;          left: 25%;         position: absolute;         top: 100%;          float: left;           background-color: #ffffff;         border: 1px solid #ccc;         border: 1px solid rgba(0, 0, 0, 0.2);         -webkit-border-radius: 5px;         -moz-border-radius: 5px;         border-radius: 5px;         -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; ">
    <a name="lnkViews" href="javascript:;"><img id="syncing_close" style="display: none;height: 13px;float: right;"src="../wp-content/plugins/linksync/img/cross_icon.png"></a>
    <center><h4 style="display:none;" id="syncing_loader"><img src="../wp-content/plugins/linksync/img/ajax-loader.gif"></h4></center>
    <center><div id="total_product"></div></center>
    <center><h4 id="export_report">Do you want to sync of product data from   <?php echo (get_option('linksync_connectedto') == 'WooCommerce') ? get_option('linksync_connectionwith') : get_option('linksync_connectedto'); ?> ?</h4></center> 
    <center><h4 id="sync_start"></h4></center> 
    <div id="button">
        <?php
        if (get_option('linksync_connectionwith') == 'Vend' || get_option('linksync_connectedto') == 'Vend') {
            ?><input type="button" onclick="return sync_process_start();"  name="sync_all_product_to_vend" style="color: green;
                   margin-left: 168px;
                   width: 90px;
                   font-weight: 900;
                   float: left;
                   "   class="button hidesync"   value="Yes"> 
                   <?php
               } elseif (get_option('linksync_connectionwith') == 'QuickBooks Online' || get_option('linksync_connectedto') == 'QuickBooks Online') {
                   ?><input type="button" onclick="return sync_process_startQBO();"  name="sync_all_product_to_vend" style="color: green;
                   margin-left: 168px;
                   width: 90px;
                   font-weight: 900;
                   float: left;
                   "   class="button hidesync"   value="Yes"> 
                   <?php
               }
               ?> 
        <input  type="button" class="button hidesync" style="color: red;
                margin-left: 83px;
                width: 90px;font-weight: 900;"  name="close_syncall"  onclick="jQuery('#pop_up_syncll').fadeOut();"  value='No'/></div></div> 
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
                                            linksync_class::add('Product Deleted(In VEND Store):Clean Up', $method, $message, $laids);
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
                                        linksync_class::add('Product Deleted(In Woo Store):Clean Up', $method, $message, $laids);
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
                                    linksync_jQuery1('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $message_resp; ?>").fadeIn().delay(3000).fadeOut(4000);
        </script><?php
    }
                    ?>
    <link rel="stylesheet" href="../wp-content/plugins/linksync/css/style.css">
    <script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery.bpopup.min.js"></script>
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
            $prod_query = mysql_query("SELECT " . $wpdb->prefix . "postmeta.*,COUNT(*) as c  FROM `" . $wpdb->prefix . "postmeta`  WHERE meta_key='_sku' AND meta_value!='' GROUP BY " . $wpdb->prefix . "postmeta.meta_value HAVING c > 1");
            if (0 != mysql_num_rows($prod_query)) {
                ?>  <table width="100%" class="wp-list-table widefat plugins"> 
                    <thead>
                        <?php
                        echo $tr;
                        while ($product_data = mysql_fetch_assoc($prod_query)) {
                            $product_details = mysql_query("SELECT " . $wpdb->prefix . "postmeta.* ," . $wpdb->prefix . "posts.ID," . $wpdb->prefix . "posts.post_title FROM " . $wpdb->prefix . "postmeta JOIN `" . $wpdb->prefix . "posts` ON(" . $wpdb->prefix . "postmeta.post_id=" . $wpdb->prefix . "posts.ID)  WHERE " . $wpdb->prefix . "postmeta.meta_value ='" . $product_data['meta_value'] . "' AND " . $wpdb->prefix . "postmeta.meta_key='_sku'");
                            if (0 != mysql_num_rows($product_details)) {
                                while ($product_wc1 = mysql_fetch_assoc($product_details)) {
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
    }
                    ?>        <br>
            <div style="text-align:center;margin-top: 30px;  margin-right: 30px;"><input type="checkbox" name="in_woo" checked="checked"> In Woo-Commerce <input style="margin-left:150px"type="checkbox" name="in_vend" checked="checked"> In VEND
            </div>
            <div style="text-align:center;margin-top: 20px;">
                <input type="submit" name="confirm" value="Confirm" class="button button-primary" />
            </div>
        </form>
    </div> 
    <input style="margin-top: 10px;margin-bottom: 20px;"type='button' class="button button-primary" onClick="popup('duplicate')" name='duplicate' value='Duplicate Product SKU'>
<?php } ?>
<style> 
    .loader-please-wait {
        background-image: url(../wp-content/plugins/linksync/img/shader.png);
        position: fixed;
        display: none;
        z-index: 1000000000;
        height: 100%;
        width: 100%;
        left: 0;
        top: 0;
    }
    #h2_linksync{
        font-size: 17px !important;
        font-weight: 400;
        padding: 0px 0px 0px 0;  
        font-style: normal;
        color: #333;
        font-family: Helvetica,Arial,sans-serif;
        margin: 0 0 5px;
        text-align: center;
        line-height: 1.3em !important; 
    }
    .loader-please-wait .loader-content {

        border-radius: 10px; 
        box-shadow: 3px 6px 8px #555;
        position: relative;
        top: 200px;
        width: 300px;
        margin: auto;
        padding: 20px 0;
        text-align: center;
        background-color: #fff;
        border: 1px solid #666;
    </style>
</head>
<div id="please-wait" class="loader-please-wait" style="display: none;">
    <div class="loader-content">

        <h3 id="h2_linksync">Linksync is Updating data<br>Please wait...</h3>
        <p><img style="color: blue" src="../wp-content/plugins/linksync/img/loading_please_wait.gif"></p>
        </div>
    </div> 
    <script> 
        var linksync_jQuery=jQuery.noConflict( true );
        function popup(id){ 
            linksync_jQuery('#'+id).bPopup( {
                positionStyle: 'absolute' //'fixed' or 'absolute'
            });
        } 
        linksync_jQuery(document).ready(function() {
            linksync_jQuery('#selecctall').click(function(event) {  //on click 
                if(this.checked) { // check select status
                    linksync_jQuery('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = true;  //select all checkboxes with class "checkbox1"               
                    });
                }else{
                    linksync_jQuery('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "checkbox1"                       
                    });         
                }
            });
    
        }); 
        
        function  show_confirm_box() {  
            if(linksync_jQuery("#pop_up_syncll").is(":visible")==false){
                linksync_jQuery('#syncing_loader').hide();
                linksync_jQuery('#button').show();
                linksync_jQuery("#export_report").show(); 
                linksync_jQuery('#pop_up_syncll').fadeIn();  
            }  
        }
    
        function sync_process_start() {
<?php
update_option('product_detail', NULL);
update_option('image_process', 'complete');
update_option('prod_last_page', NULL);
update_option('product_image_ids', NULL);
?> linksync_jQuery("#export_report").hide(); 
        linksync_jQuery('#syncing_loader').show();
        linksync_jQuery('#sync_start').show();
        linksync_jQuery('#button').hide();
        linksync_jQuery('#sync_start').html("<h3>Starting....</h3>"); 
        importProduct();
         
    }
    var communication_key='<?php echo get_option('webhook_url_code'); ?>';
    var check_error=0;
    function importProduct(){ 
        var ajaxupdate=  linksync_jQuery.ajax({
            type: "POST",  
            dataType:'json', 
            url: "<?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?>", 
            success:function(dataupper){
                if(dataupper.message!=''){
                    linksync_jQuery("#please-wait").css("display", "none");
                    linksync_jQuery("#sync_start").show();    
                    linksync_jQuery("#sync_start").html("<p style='font-size:15px;'><b>"+dataupper.message+"</b>"); 
                    linksync_jQuery("#sync_start").hide(1500);
                    linksync_jQuery("#pop_up_syncll").hide(1500);
                    linksync_jQuery("#syncing_loader").hide(1500);
                }else if(dataupper.image_process=='running'){
                    linksync_jQuery("#please-wait").css("display", "none"); 
                    uploading_process_start_for_image(dataupper.product_count);
                }
            },
            error: function() {         
                console.log("Error Empty Response");  
                if(check_error==10){
                    check_error =0; 
                    ajaxupdate.abort();
                    linksync_jQuery("#sync_start").html("<p style='font-size:15px;color:red;'><b>Internal Connection Error : Please refresh and try again!</b>"); 
                    linksync_jQuery("#syncing_loader").hide(1500);
                    linksync_jQuery('#syncing_close').css('display','block');
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
    linksync_jQuery(document).on("click","a[name='lnkViews']", function (e) { 
        linksync_jQuery("#pop_up_syncll").fadeOut(500); 
        location.reload();  
    });
    /*
     * Quick Book Online Product Import
     */
    function sync_process_startQBO() {
<?php
update_option('product_detail', NULL);
update_option('prod_last_page', NULL);
?> linksync_jQuery("#export_report").hide(); 
        linksync_jQuery('#syncing_loader').show();
        linksync_jQuery('#sync_start').show();
        linksync_jQuery('#button').hide();
        linksync_jQuery('#sync_start').html("<h3>Starting....</h3>"); 
        importProductQBO();
         
    }
    function importProductQBO(){ 
        var check='on';
        linksync_jQuery.ajax({
            type: "POST",  
            dataType:'json', 
            url: "<?php echo content_url() . '/plugins/linksync/update.php?c=' . get_option('webhook_url_code'); ?>", 
            success:function(dataupper){  
                check='off'; 
                clearInterval(myVar);
                linksync_jQuery("#sync_start").show();    
                linksync_jQuery("#sync_start").html("<p style='font-size:15px;'><b>"+dataupper.message+"</b>"); 
                linksync_jQuery("#sync_start").hide(1500);
                linksync_jQuery("#pop_up_syncll").hide(1500);
                linksync_jQuery("#syncing_loader").hide(1500);
            },
            error: function() {         
                console.log("Error Empty Response"); 
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
                linksynProduct_jquery.ajax({
                    type: "POST",  
                    dataType:'json', 
                    data:{'communication_key':communication_key},
                    url: "../wp-content/plugins/linksync/report.php",
                    success:function(data){  
                        linksynProduct_jquery("#sync_start").html("linksync update is running.<br> Importing from product <b>"+data.total_product+"</b>");
                    }});    
            },2000);
        } 

    } 
    function ajaxRequestForproduct_image(i,totalreq,total_product,product_count,status){ 
        linksync_jQuery("#sync_start").html("linksync update is running.<br> Importing from product <b>"+ (product_count + 1) +" of "+total_product+"</b>");
        var ajaxobj= linksync_jQuery.ajax({
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
            error: function() { 
                status='resend';
                console.log('Resend Request for the same product');
            },
            complete:function(responsedata){ 
                if(responsedata){  
                    if(i>totalreq){ 
                        linksync_jQuery.ajax({
                            url:  '../wp-content/plugins/linksync/image_uploader.php',
                            type: 'POST',
                            data: { "get_total": "1",'communication_key':communication_key},
                            success: function(response) { 
                                linksync_jQuery("#please-wait").css("display", "block");
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
        linksync_jQuery.ajax({
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
        var laidField = linksync_jQuery("input[name='apikey']");
        if (laidField.val() == '') {
            laidField.css('border', '1px solid red');
            return false;
        } else {
            return true;
        }
    }
    </script> 