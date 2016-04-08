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

    /**
     * Get and display logs
     * @param int $last
     */
	public static function getLogs($last = 10) {

        $query = 'SELECT * FROM  `' . $GLOBALS['wpdb']->prefix . 'linksync_log` ORDER BY `id_linksync_log` DESC LIMIT 0 , ' . $last;
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

        $query = 'SELECT * FROM  `'. $GLOBALS['wpdb']->prefix . 'linksync_log` ORDER BY `id_linksync_log` DESC';
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