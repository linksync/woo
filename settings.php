<?php
require_once(dirname(__FILE__) . '/classes/Class.linksync.php');
require_once(dirname(__FILE__) . '/plugin_tabs.php'); # Handle Tabs 
?>

<style>
    fieldset{
        border: 2px groove threedface;
        padding: 20px;
        margin-top: 20px;
    }
    p
    {
        margin-top: 10px;
    }
    .stock_control_div
    {
        padding-left: 25px;
    }
</style>
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery.tipTip.min.js"></script> 
<link rel="stylesheet" href="../wp-content/plugins/linksync/css/reveal.css">	
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery.reveal.js"></script>
<!--<link rel="stylesheet" id="woocommerce_admin_styles-css" href="../wp-content/plugins/linksync/css/admin.css" type="text/css" media="all">-->
<div class="wrap"> 
    <div id="response" style="padding: 15px; margin-top: 25px; display: none;"></div>
    <?php
    global $wpdb;
    $linksync = new linksync();
    //Send log feature
    $testMode = get_option('linksync_test');
    $LAIDKey = get_option('linksync_laid');
    $apicall = new linksync_class($LAIDKey, $testMode);

    if (isset($_GET['setting']) && $_GET['page']) {
        if ($_GET['setting'] == 'logs' && $_GET['page'] == 'linksync') {
            if (isset($_POST['clearlog'])) {
                $empty = mysql_query("TRUNCATE TABLE `" . $wpdb->prefix . "linksync_log`");
                if ($empty) {
                    $response = "Logs Clear successfully!";
                } else {
                    $response = "Error:Unable to Clear Logs Details";
                }
                ?><script>
                    jQuery('#response').removeClass('error').addClass('updated').html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
                </script><?php
        }

        if (isset($_GET['check']) && $_GET['check'] == 'all') {
            if (isset($_POST['send_log'])) {
                $fileName = dirname(__FILE__) . '/classes/raw-log.txt';
                $data = file_get_contents($fileName);
                $encoded_data = base64_encode($data);
                $result = array(
                    "attachment" => $encoded_data
                );
                $json = json_encode($result);
                $apicall_result = $apicall->linksync_sendLog($json);
                if (isset($apicall_result['result']) && $apicall_result['result'] == 'success') {
                    $response = 'Logs Sent Successfully !';
                } else {
                    $response = "Error:Unable to Send Logs Details";
                }
                    ?><script>
                        jQuery('#response').removeClass('error').addClass('updated').html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
                    </script><?php
            }

            echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="../wp-content/plugins/linksync/css/jquery-ui.css">
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-ui.js"></script>  
  <script>
  $(function() {
    $( document ).tooltip();
  });
  </script>
  <style>
  label {
    display: inline-block;
    width: 5em;
  }
  </style> ' . "<fieldset><legend>Linksync Log</legend><div style='float: left;
margin-bottom: 10px;'><form method='POST'><input type='submit' class='button' title=' Use this button to upload your log file to linksync. You should only need to do this if requested by linksync support staff.' style='color:blue'  name='send_log' value='Send log to linksync'></form></div><div style='float: right;
margin-bottom: 10px;'><form method='POST'><input type='submit' class='button' style='color:red' name='clearlog' value='Clear Logs'></form></div>" . linksync_class::printallLogs() . "</fieldset>";
        } else {
            if (isset($_POST['send_log'])) {
                $fileName = dirname(__FILE__) . '/classes/raw-log.txt';
                $data = file_get_contents($fileName);
                $encoded_data = base64_encode($data);
                $result = array(
                    "attachment" => $encoded_data
                );
                $json = json_encode($result);
                $apicall_result = $apicall->linksync_sendLog($json);
                if (isset($apicall_result['result']) && $apicall_result['result'] == 'success') {
                    $response = 'Logs Sent Successfully !';
                } else {
                    $response = "Error:Unable to Send Logs Details ";
                }
                    ?><script>
                        jQuery('#response').removeClass('error').addClass('updated').html("<?php echo $response; ?>").fadeIn().delay(3000).fadeOut(4000);
                    </script><?php
            }
            echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="../wp-content/plugins/linksync/css/jquery-ui.css">
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-ui.js"></script>  
  <script>
  $(function() {
    $( document ).tooltip();
  });
  </script>
  <style>
  label {
    display: inline-block;
    width: 5em;
  }
  </style> '. "<fieldset id=test><legend>Linksync Log</legend><div style='float: left;
margin-bottom: 10px;'><form method='POST'><input type='submit' class='button' title=' Use this button to upload your log file to linksync. You should only need to do this if requested by linksync support staff.'    style='color:blue'   name='send_log' value='Send log to linksync'></form></div><div style='float: right;
margin-bottom: 10px;'><form method='POST'><input type='submit' class='button' style='color:red' name='clearlog' value='Clear Logs'></form></div>" . linksync_class::getLogs() . "
<a href='?page=linksync&setting=logs&check=all'><br><center><input type='button' class='button' style='color:#0074a2' name='allLogs' value='Show all'></a></fieldset>";
        }
    } elseif ($_GET['page'] == 'linksync' && $_GET['setting'] == 'manage_api_key') {
        include_once(dirname(__FILE__) . '/manage_api_key.php');
    } elseif ($_GET['page'] == 'linksync' && $_GET['setting'] == 'product_config') {
        include_once(dirname(__FILE__) . '/product_config.php');
    } elseif ($_GET['page'] == 'linksync' && $_GET['setting'] == 'order_config') {
        require_once(dirname(__FILE__) . '/order_config.php');
    } elseif ($_GET['page'] == 'linksync' && $_GET['setting'] == 'logs' && $_GET['check'] == 'all') {
        echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
            <link rel="stylesheet" href="../wp-content/plugins/linksync/css/jquery-ui.css">
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-ui.js"></script>
' . "<fieldset><legend>Linksync Log</legend>" . linksync_class::printallLogs() . "</fieldset>";
    } elseif ($_GET['page'] == 'linksync' && $_GET['setting'] == 'other_setting') {
        include_once(dirname(__FILE__) . '/other_setting.php');
    }
} else {
    include_once(dirname(__FILE__) . '/configuration.php');
}
    ?></div> 