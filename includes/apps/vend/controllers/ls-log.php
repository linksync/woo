<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LSC_Log{

	/**
	 * Get the instace of LS_Vend_log class
	 */
	public static function instance(){
		return new LS_Vend_Log();
	}

	/**
	 * Insert data to linksync_log table
	 * @param string $method
	 * @param string $status
	 * @param string $message
	 * @param string $laid
	 * @return true or false whether the insertion is sucessful
	 */
	public static function add($method, $status, $message, $laid) {

        $data_to_insert = array(
            'method' => $method,
            'result' => $status,
            'message' => $message,
            'laid' => $laid,
            'date_add' => current_time('mysql')
        );
        return self::instance()->insert($data_to_insert);
    }

	public static function add_success($method, $message, $laid){
		return self::add($method, 'Success', $message, $laid);
	}

	public static function add_dev_success($method, $message){
		return self::add($method, '<p style="color:green;">Success</p>', $message, 'developer');
	}

	public static function add_failed($method, $message, $laid){
		return self::add($method, 'Failed', $message, $laid);
	}

	public static function add_dev_failed($method, $message){
		return self::add($method, '<p style="color:red;">Failed</p>', $message, 'developer');
	}

	public static function count_dev_logs(){
		global $wpdb;
		$tableName = $wpdb->prefix . 'linksync_log';
		$log_count = $wpdb->get_var(" SELECT COUNT(result) FROM ".$tableName." WHERE laid = 'developer' ");

		return $log_count;
	}

	public static function clear_some_dev_logs(){
		global $wpdb;
		$devLogCount = self::count_dev_logs();
		$tableName = $wpdb->prefix . 'linksync_log';
		if($devLogCount > 150){
			$query = "DELETE FROM ".$tableName." WHERE laid='developer' LIMIT 100";
			$wpdb->query($query);
		}
	}

    /**
     * Get and display logs
     * @param int $last
     */
	public static function getLogs($last = 10) {

		$log_type = ' WHERE '.$GLOBALS['wpdb']->prefix.'linksync_log.laid !=\'developer\' ';
		if(!empty($_GET['logtype']) && 'developer' == $_GET['logtype']){
			$log_type = ' WHERE '.$GLOBALS['wpdb']->prefix.'linksync_log.laid =\''.$_GET['logtype'].'\' ';
		}
        $query = 'SELECT * FROM  `' . $GLOBALS['wpdb']->prefix . 'linksync_log` '.$log_type.' ORDER BY `id_linksync_log` DESC LIMIT 0 , ' . $last;
        $logs = self::instance()->select_by_query($query);

        $html =' <table class="wp-list-table widefat plugins">
                <thead>
                    <tr>
                       <th scope="col" id="name" class="manage-column column-name" style="">Date</th>
                        <th scope="col" id="name" class="manage-column column-name" style="">Method</th>
                        <th scope="col" id="description" class="manage-column column-description" style="">Status</th>	
                        <th scope="col" id="description" class="manage-column column-description" style="">Message</th>	
                        <th scope="col" id="description" class="manage-column column-name" style="">API Key</th>	
                    </tr>
                </thead>';
        if (!empty($logs)) {
            foreach ($logs as $logsDetails) {
                $html.='<tr>
	                        <td>' . $logsDetails->date_add . '</td>
	                        <td>' . $logsDetails->method . '</td>
	                        <td>' . $logsDetails->result . '</td>
	                        <td>' . $logsDetails->message . '</td>
	                        <td>' . $logsDetails->laid . '</td>
	                    </tr>';
            }
        } else {
            $html.="<tr><td colspan=5>No Record Found!!</td></tr>";
        }
        $html.='</table> ';
        return $html;
    }

    /**
     * Get and display all logs
     * @return string $html
     */
    public static function printallLogs() {
        global $wpdb;
		$log_type = ' WHERE '.$GLOBALS['wpdb']->prefix.'linksync_log.laid !=\'developer\' ';
		if(!empty($_GET['logtype']) && 'developer' == $_GET['logtype']){
			$log_type = ' WHERE '.$GLOBALS['wpdb']->prefix.'linksync_log.laid =\''.$_GET['logtype'].'\' ';
		}

        $query = 'SELECT * FROM  `'. $GLOBALS['wpdb']->prefix . 'linksync_log` '.$log_type.' ORDER BY `id_linksync_log` DESC';
        $log_result = self::instance()->select_by_query($query);
        $html = '';

        $html.=' <table class="wp-list-table widefat plugins">
                <thead>
                    <tr>
                       <th scope="col" id="name" class="manage-column column-name" style="">Date</th>
                        <th scope="col" id="name" class="manage-column column-name" style="">Method</th>
                        <th scope="col" id="description" class="manage-column column-description" style="">Status</th>	
                        <th scope="col" id="description" class="manage-column column-description" style="">Message</th>	
                        <th scope="col" id="description" class="manage-column column-name" style="">API Key</th>
                    </tr>
                </thead>';
        if(!empty($log_result)){
        	foreach ($log_result as $logsDetails) {
        		 $html.='<tr>
	                        <td>' . $logsDetails->date_add . '</td>
	                        <td>' . $logsDetails->method . '</td>
	                        <td>' . $logsDetails->result . '</td>
	                        <td>' . $logsDetails->message . '</td>
	                        <td>' . $logsDetails->laid . '</td>
	                    </tr>';
        	}
        }else{
        	 $html.="<tr><td colspan=5>No Record Found!!</td></tr>";
        }

        $html.='</table> ';
        return $html;
    }
}