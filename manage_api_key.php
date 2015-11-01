<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="../wp-content/plugins/linksync/css/jquery-ui.css">
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-ui.js"></script>
<script>
 var linksync_jQuery1=jQuery.noConflict( true );
</script>
<?php
if (!defined('ABSPATH')) {
    exit('Access is Denied'); // Exit if accessed directly
}
// Adding API Key by Pop UP into our wp database 
if (isset($_POST['add_apiKey'])) {
    global $wpdb;
    if (!empty($_POST['apikey'])) {
        $query_api_key = mysql_query("SELECT api_key  FROM " . $wpdb->prefix . "linksync_laidKey");
        if (0 == mysql_num_rows($query_api_key)) {
            //If Connection is established than save to database:
            if ($wpdb->insert($wpdb->prefix . 'linksync_laidKey', array('api_key' => trim($_POST['apikey']), 'status' => 'Under Process', 'date_add' => date('Y/m/d')))) {
                $result = linksync::checkForConnection($_POST['apikey']);
                if (get_option('linksync_laid') == '') {
                    update_option('linksync_laid', trim($_POST['apikey']));
                }
                $class1 = 'error';
                $class2 = 'updated';
                linksync_class::add('Manage API Keys', 'success', 'API Key Added Successfully', $_POST['apikey']);
                $response = 'API Key has been added successfully !';
            } else {
                linksync_class::add('Manage API Keys', 'fail', 'Unable to Insert', $_POST['apikey']);
            }
            // $query = mysql_query("SELECT api_key FROM " . $wpdb->prefix . "linksync_laidKey WHERE api_key='" . $_POST['apikey'] . "'");
            // if (0 == mysql_num_rows($query)) {

            if (isset($result['success'])) {
                $class1 = 'error';
                $class2 = 'updated';
                $response = $result['success'];
            } else {
                $wpdb->delete($wpdb->prefix . 'linksync_laidKey', array('api_key' => trim($_POST['apikey'])));
                $class1 = 'updated';
                $class2 = 'error';
                $response = $result['error'];
            }
        } else {
            $class1 = 'updated';
            $class2 = 'error';
            $response = "API Key is already exists!";
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
        linksync_jQuery1('#response').removeClass("<?php echo $class1; ?>").addClass("<?php echo $class2; ?>").html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
                    
    </script>
    <?php
// End - Adding API Key by Pop UP 
}

if (isset($_POST['apikey_update'])) {
    $table_name = $wpdb->prefix . 'linksync_laidKey';

    $where = array('id' => $_POST['id']);
    if (!empty($_POST['apikey'])) {
        $result = linksync::checkForConnection($_POST['apikey']);
        if (isset($result['success'])) {
            $status = 'Connected';
        } else {
            $status = 'InValid';
        }
        $data_array = array('api_key' => trim($_POST['apikey']), 'date_add' => date('Y/m/d'), 'status' => $status);
        if ($wpdb->update($table_name, $data_array, $where)) {
            linksync_class::add('Manage API Keys', 'success', 'API key Updated Successfully', $_POST['apikey']);
            $response = 'API key Updated Successfully!! ';
        } else {
            linksync_class::add('Manage API Keys', 'fail', 'Unable to Update!!', $_POST['apikey']);
        }
    } else {
        linksync_class::add('Manage API Keys', 'fail', 'API key is empty!!', '-');
        $response = "API key is empty!!";
    }
    ?>
    <script>
        linksync_jQuery1('#response').removeClass('error').addClass('updated').html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
                   
    </script>
    <?php
}
if (isset($_POST['apikey_delete'])) {
    $api_key = explode('|', $_POST['id']);
    $table_name = $wpdb->prefix . 'linksync_laidKey';
    $where = array('id' => $api_key[0]);
    if ($wpdb->delete($table_name, $where, $where_format = null)) {
        if (get_option('linksync_laid') == $api_key[1]) {
            update_option('linksync_laid', '');
            linksync_class::releaseOptions();
        }
    }
}
?><div id="tiptip_holder" style="margin:187px 1px 1px 643px  !important;display: none;" class="tip_top">
    <div id="tiptip_arrow" style="margin-left: 74.5px; margin-top: 47px;"><div id="tiptip_arrow_inner"></div></div>
    <div id="tiptip_content">The linksync API Key is a unique key that's created when you link two apps via the linksync dashboard. You need a valid API Key for this linkysnc extension to work.
    </div>
</div>
<div id="myModal" class="reveal-modal">
    <form method="POST" name="f1" action="">
        <center><span style="color: #0074a2;font-size: 18px;">Enter the API Key</span></center>
        <hr>
        <div style="float: left;font-size: 14px;color: #0074a2; text-align: right; margin-top: 4px;">API Key*:</div>
        <div style="float: left;margin-left: 10px;">
            <input type="text" size="40" name="apikey" value=""></div><a href="https://www.linksync.com/help/woocommerce"><img class="help_tip" src="../wp-content/plugins/linksync/img/help.png" height="16" width="16"></a>
        <br><br><br>
        <center><input type="submit" style="color: green;" value="Save" onclick="return checkEmptyLaidKey()" class="button" name="add_apiKey"></center>
    </form>
</div>
<style>

    strong {
        font-weight: bold; 
    }

    em {
        font-style: italic; 
    }table {
        background: #f5f5f5;
        border-collapse: separate;
        box-shadow: inset 0 1px 0 #fff;
        font-size: 12px;
        line-height: 24px;
        margin: 30px auto;
        text-align: left;
        width: 800px;
    }	

    th {
        background: url(http://jackrugile.com/images/misc/noise-diagonal.png), linear-gradient(#777, #444);
        border-left: 1px solid #555;
        border-right: 1px solid #777;
        border-top: 1px solid #555;
        border-bottom: 1px solid #333;
        box-shadow: inset 0 1px 0 #999;
        color: #fff;
        font-weight: bold;
        padding: 10px 15px;
        position: relative;
        text-shadow: 0 1px 0 #000;	
    }

    th:after {
        background: linear-gradient(rgba(255,255,255,0), rgba(255,255,255,.08));
        content: '';
        display: block;
        height: 25%;
        left: 0;
        margin: 1px 0 0 0;
        position: absolute;
        top: 25%;
        width: 100%;
    }

    th:first-child {
        border-left: 1px solid #777;	
        box-shadow: inset 1px 1px 0 #999;
    }

    th:last-child {
        box-shadow: inset -1px 1px 0 #999;
    }

    td {
        border-right: 1px solid #fff;
        border-left: 1px solid #e8e8e8;
        border-top: 1px solid #fff;
        border-bottom: 1px solid #e8e8e8;
        padding: 10px 15px;
        position: relative;
        transition: all 300ms;
    }

    td:first-child {
        box-shadow: inset 1px 0 0 #fff;
    }	

    td:last-child {
        border-right: 1px solid #e8e8e8;
        box-shadow: inset -1px 0 0 #fff;
    }	

    tr {
        background: url(http://jackrugile.com/images/misc/noise-diagonal.png);	
    }

    tr:nth-child(odd) td {
        background: #f1f1f1 url(http://jackrugile.com/images/misc/noise-diagonal.png);	
    }

    tr:last-of-type td {
        box-shadow: inset 0 -1px 0 #fff; 
    }

    tr:last-of-type td:first-child {
        box-shadow: inset 1px -1px 0 #fff;
    }	

    tr:last-of-type td:last-child {
        box-shadow: inset -1px -1px 0 #fff;
    }	


</style> 
<div class="wrap">
    <h2>Manage API Keys <?php if ($wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'linksync_laidKey') == 0) { ?><a href="#"  data-reveal-id="myModal" data-animation="fade" class="add-new-h2">Add New</a><?php } ?></h2>
    <table  class="wp-list-table widefat fixed pages">

        <thead >
            <tr >
                <td width="20px">
                    <strong style="color: #0074a2;">ID</strong> </td>
                <td align="center">
                    <span align="center"><strong style="color: #0074a2;">Date</strong></span>
                </td>
                <td align="center">
                    <strong style="color: #0074a2;">API Key</strong>
                </td>
                <td align="center"><strong style="color: #0074a2;">Status</strong></td>
                <td align="center"><strong style="color: #0074a2;">Action</strong></td>

            </tr>
        </thead>

        <?php
        if ($wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'linksync_laidKey') != 0) {
            $api_keys = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'linksync_laidKey`') or die(mysql_error());
            foreach ($api_keys as $api_key) {
                ?>
                <tr align="center" >
                    <td scope="row" class="check-column">
                        <?php echo $api_key->id; ?>
                    </td>  <td class="date column-date"><?php echo $api_key->date_add; ?></td>	
                    <td><?php echo $api_key->api_key; ?></td>
                    <td><?php echo $api_key->status; ?></td>
                    <td><div id="pop_up_<?php echo $api_key->id ?>" class="clientssummarybox" style="width: 36% !important; 
                             top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;
                             padding: 10px !important; line-height: 30px !important; left: 38%; position: absolute; 
                             top: 100%; float: left;  background-color: #ffffff;  border: 1px solid #ccc; order: 1px solid rgba(0, 0, 0, 0.2); 
                             -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                             -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; "/>

                        <form  method="POST" name="f1" action="">
                            <center><span style="color: #0074a2;font-size: 18px;">Update API Key</span></center>
                            <hr><br><div style="float: left;font-size: 16px;color: blueviolet; text-align: right; margin-left: 30px;">API Key*:</div>
                            <div style="float: left;margin-left: 20px;">
                                <?php
                                $apikey_detail = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'linksync_laidKey` WHERE id=' . $api_key->id) or die("problem in qury");
                                ?>
                                <input type="text" name="apikey" size="30" value="<?php echo $apikey_detail[0]->api_key; ?>">
                            </div>
                            <center><input type="hidden" value="<?php echo $api_key->id; ?>" name="id"> <input style="color: green;" class='button' type="submit" value="Update"  name="apikey_update"><br><br><input class='button' style="color: #0074a2;" type="button" onclick='closeAudit(<?php echo $api_key->id ?>)' name='close' value='Close'/></center>
                        </form>
                        </div>

                        <div id="pop_up_del_<?php echo $api_key->id ?>" class="clientssummarybox" style="width: 36% !important; 
                             top: 24% !important; display: none;  z-index: 999999999;  position: fixed !important;
                             padding: 10px !important; line-height: 30px !important; left: 38%; position: absolute; 
                             top: 100%; float: left;  background-color: #ffffff;  border: 1px solid #ccc; order: 1px solid rgba(0, 0, 0, 0.2); 
                             -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                             -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);         -webkit-background-clip: padding-box;         -moz-background-clip: padding;         background-clip: padding-box; "/>

                        <form method="POST" name="f1" action="">
                            Are you sure want to delete it?<br><br>
                            <input type="hidden" value="<?php echo $api_key->id . '|' . $api_key->api_key ?>" name="id"><input class='button' type="submit" style="color: red;" value="Delete"  name="apikey_delete">&nbsp;&nbsp;&nbsp; <input class='button' style="color: #0074a2;"  type="button" onclick='closeAuditdel(<?php echo $api_key->id ?>)' name="close" value='Cancel'/>
                        </form>   
                        </div>
                        <input type="button" class="button" style="color: green;" onclick='openAudit(<?php echo $api_key->id ?>)' value='Edit'/>
                        <input  type="button" class="button" style="color: red;"  onclick='openAuditdel(<?php echo $api_key->id ?>)' value='Delete'/></td>
                </tr>

                <?php
            }
        } else {
            ?><tr><td colspan="5">No Record Found!!</td></tr>
        <?php }
        ?> 
    </table>
</div>
<script>
     var linksync_jQuery=jQuery.noConflict( true );
    function openAudit(id) {
        linksync_jQuery('#pop_up_' + id).fadeIn(0500);
    }
    function closeAudit(id) {
        linksync_jQuery('#pop_up_' + id).fadeOut();
    }
    function openAuditdel(id) {
        linksync_jQuery('#pop_up_del_' + id).fadeIn(0500);
    }
    function closeAuditdel(id) {
        linksync_jQuery('#pop_up_del_' + id).fadeOut();
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