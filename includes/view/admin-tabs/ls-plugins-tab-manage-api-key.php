<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

// Adding API Key by Pop UP into our wp database
if (isset($_POST['add_apiKey'])) {
    global $wpdb;
    if (!empty($_POST['apikey'])) {
        $query_api_key = LS_Vend_Api_Key::get_count();

        if (0 == $query_api_key ) {

            $data_to_insert = array(
                    'api_key' => trim($_POST['apikey']),
                    'status' => 'Under Process',
                    'date_add' => date('Y/m/d')
            );
            //If Connection is established than save to database:
            if (LS_Vend_Api_Key::insert($data_to_insert)) {

                $result = linksync::checkForConnection($_POST['apikey']);
                if (get_option('linksync_laid') == '') {
                    update_option('linksync_laid', trim($_POST['apikey']));
                }
                $class1 = 'error';
                $class2 = 'updated';
                LSC_Log::add('Manage API Keys', 'success', 'API Key Added Successfully', $_POST['apikey']);
                $response = 'API Key has been added successfully !';
            } else {
                LSC_Log::add('Manage API Keys', 'fail', 'Unable to Insert', $_POST['apikey']);
            }

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
            $class1 = 'updated';
            $class2 = 'error';
            $response = "API Key is already exists!";
        }

    } else {
        LSC_Log::add('Manage API Keys', 'fail', 'API Key is empty!!', '-');
        $response = "API Key is Empty!!";
    }
    ?>
    <script>
        
        jQuery('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
                    
    </script>
    <?php
// End - Adding API Key by Pop UP 
}

if (isset($_POST['apikey_update'])) {


    $where = array('id' => $_POST['id']);
    if (!empty($_POST['apikey'])) {
        $result = linksync::checkForConnection($_POST['apikey']);
        if (isset($result['success'])) {
            $status = 'Connected';
        } else {
            $status = 'InValid';
        }
        $data_array = array('api_key' => trim($_POST['apikey']), 'date_add' => date('Y/m/d'), 'status' => $status);
        if (LS_Vend_Api_Key::update($data_array,$where)) {
            LSC_Log::add('Manage API Keys', 'success', 'API key Updated Successfully', $_POST['apikey']);
            $response = 'API key Updated Successfully!! ';
        } else {
            LSC_Log::add('Manage API Keys', 'fail', 'Unable to Update!!', $_POST['apikey']);
        }
    } else {
        LSC_Log::add('Manage API Keys', 'fail', 'API key is empty!!', '-');
        $response = "API key is empty!!";
    }
    ?>
    <script>
        jQuery('#response').removeClass('error').addClass('updated').html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
                   
    </script>
    <?php
}
if (isset($_POST['apikey_delete'])) {

    $api_key = explode('|', $_POST['id']);
    if (LS_Vend_Api_Key::delete_by_id($api_key[0])) {
        if (get_option('linksync_laid') == $api_key[1]) {
            update_option('linksync_laid', '');
            linksync_class::releaseOptions();
        }
    }
}
?>
<div id="tiptip_holder" style="margin:187px 1px 1px 643px  !important;display: none;" class="tip_top">
    
    <div id="tiptip_arrow" style="margin-left: 74.5px; margin-top: 47px;">
        <div id="tiptip_arrow_inner"></div>
    </div>

    <div id="tiptip_content">
        The linksync API Key is a unique key that's created when you link two apps via the linksync dashboard. You need a valid API Key for this linkysnc extension to work.
    </div>

</div>

<div id="myModal" class="reveal-modal">
    <form method="POST" name="f1" action="">
        <center><span>Enter the API Key</span></center>

        <hr>
        <div>API Key*:</div>
        <div>
            <input type="text" size="40" name="apikey" value="">
        </div>
        <a href="https://www.linksync.com/help/woocommerce" title=' Unsure about how to generate an API Key? Click the icon for a specific guidelines to get you up and running with linksync Vend & WooCommerce.'>
            <img class="help_tip" src="../wp-content/plugins/linksync/assets/images/linksync/help.png" height="16" width="16">
        </a>
        <br><br><br>
        <center><input type="submit" value="Save" onclick="return checkEmptyLaidKey()" class="button color-green" name="add_apiKey"></center>
    </form>
</div>

<div class="wrap">
    <h2>Manage API Keys 
        <?php 
            $ls_linksync_laid_key_count = LS_Vend_Api_Key::get_count(); 

            if ($ls_linksync_laid_key_count == 0) { ?>
                <a href="#"  data-reveal-id="myModal" data-animation="fade" class="add-new-h2">Add New</a><?php 
            } ?>
    </h2>

    <table  class="wp-list-table widefat fixed pages">

        <thead >
            <tr >
                <td width="20px">
                    <strong class="color-dark-blue">ID</strong> </td>
                <td align="center">
                    <span align="center"><strong class="color-dark-blue">Date</strong></span>
                </td>
                <td align="center">
                    <strong class="color-dark-blue">API Key</strong>
                </td>
                <td align="center"><strong class="color-dark-blue">Status</strong></td>
                <td align="center"><strong class="color-dark-blue">Action</strong></td>

            </tr>
        </thead>

        <?php
        if ($ls_linksync_laid_key_count != 0) {
            $api_keys = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'linksync_laidKey` LIMIT 1');
            foreach ($api_keys as $api_key) {
                ?>
                <tr align="center" >
                    <td scope="row" class="check-column"><?php echo $api_key->id; ?></td>  
                    <td class="date column-date"><?php echo $api_key->date_add; ?></td>  
                    <td><?php echo $api_key->api_key; ?></td>
                    <td><?php echo $api_key->status; ?></td>
                    <td>
                        <div id="pop_up_<?php echo $api_key->id ?>" class="clientssummarybox ls-hide">

                            <form  method="POST" name="f1" action="">
                                <center><span>Update API Key</span></center>
                                <hr><br>

                                <div>API Key*:</div>
                                <div>
                                    <?php
                                        $apikey_detail = LS_Vend_Api_Key::select_by_id($api_key->id);
                                    ?>
                                    <input type="text" name="apikey" size="30" value="<?php echo $apikey_detail['api_key']; ?>">
                                </div>

                                <center>
                                    <input type="hidden" value="<?php echo $api_key->id; ?>" name="id"> 
                                    <input class='button color-green' type="submit" value="Update"  name="apikey_update">
                                    <input class='button color-dark-blue' type="button" onclick='closeAudit(<?php echo $api_key->id ?>)' name='close' value='Close'/>
                                </center>

                            </form>

                        </div>

                        <div id="pop_up_del_<?php echo $api_key->id ?>" class="clientssummarybox ls-hide">

                            <form method="POST" name="f1" action="">
                                Are you sure want to delete it?<br><br>
                                <input type="hidden" value="<?php echo $api_key->id . '|' . $api_key->api_key ?>" name="id">
                                <input class='button color-red' type="submit"  value="Delete"  name="apikey_delete">&nbsp;&nbsp;&nbsp; 
                                <input class='button color-dark-blue' type="button" onclick='closeAuditdel(<?php echo $api_key->id ?>)' name="close" value='Cancel'/>
                            </form>   

                        </div>

                        <input type="button" class="button color-green" onclick='openAudit(<?php echo $api_key->id ?>)' value='Edit'/>
                        <input type="button" class="button color-red"   onclick='openAuditdel(<?php echo $api_key->id ?>)' value='Delete'/></td>
                </tr>

                <?php
            }
        } else {?>

                <tr><td colspan="5">No Record Found!!</td></tr><?php 

        }?> 
    </table>
</div>
