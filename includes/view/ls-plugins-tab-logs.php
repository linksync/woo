<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');


 if (isset($_POST['clearlog'])) {
    $empty = LSC_Log::instance()->truncate_table();
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
        $fileName = LS_PLUGIN_DIR. '/classes/raw-log.txt';
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

    echo  "<fieldset>
                <legend>Linksync Log</legend>
                <div style='float: left;margin-bottom: 10px;'>
                    <form method='POST'>
                            <input type='submit' class='button' title=' Use this button to upload your log file to linksync. You should only need to do this if requested by linksync support staff.' style='color:blue'  name='send_log' value='Send log to linksync'>
                    </form>
                </div>
                <div style='float: right;margin-bottom: 10px;'>
                    <form method='POST'>
                        <input type='submit' class='button' style='color:red' name='clearlog' value='Clear Logs'>
                        </form>
                    </div>" . LSC_Log::printallLogs() . "
            </fieldset>";
} else {
    if (isset($_POST['send_log'])) {
        $fileName = LS_PLUGIN_DIR . 'classes/raw-log.txt';
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

    echo "<fieldset id=test>
            <legend>Linksync Log</legend>
            <div style='float: left;margin-bottom: 10px;'>
                <form method='POST'>
                    <input type='submit' class='button' title=' Use this button to upload your log file to linksync. You should only need to do this if requested by linksync support staff.'    style='color:blue'   name='send_log' value='Send log to linksync'>
                </form>
            </div>

            <div style='float: right;margin-bottom: 10px;'>
                <form method='POST'>
                    <input type='submit' class='button' style='color:red' name='clearlog' value='Clear Logs'>
                </form>
            </div>" . LSC_Log::getLogs() . "

            <a href='?page=linksync&setting=logs&check=all'><br>
                <center>
                    <input type='button' class='button' style='color:#0074a2' name='allLogs' value='Show all'>
            </a>
          </fieldset>";
}

?>