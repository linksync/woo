<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LS_Vend_Log{
	/**
	 * @var Table name 
	 */
	public  $table_name 	= 'linksync_log';

	/**
	 *@var column name id_linksync_log
	 */
	public  $col_id_linksync_log 		= 'id_linksync_log';

	/**
	 *@var column name method
	 */
	public  $col_method 		= 'method';

	/**
	 *@var column name result
	 */
	public  $col_result 		= 'result';

	/**
	 *@var column name laid
	 */
	public  $col_laid 		= 'laid';

	/**
	 *@var column name id_linksync_log
	 */
	public  $col_message 		= 'message';

	/**
	 *@var column name method
	 */
	public  $col_date_add 		= 'date_add';

	/**
	 * Get the exact whole name of the table
	 * @return string 
	 */
	public function get_table_name(){
		global $wpdb;

		return $wpdb->prefix.$this->table_name;
	}

	/**
	 * Create of linksync_log table
	 */
	public function create_table(){
		global $wpdb;

		$sql_create_table = 'CREATE TABLE IF NOT EXISTS `'.$this->get_table_name().'`(
			`'.$this->col_id_linksync_log.'` int(10) unsigned NOT NULL auto_increment,
			`'.$this->col_method.'` varchar(200),
			`'.$this->col_result.'` varchar(200),
            `'.$this->col_laid.'` varchar(50),
			`'.$this->col_message.'` text,
			`'.$this->col_date_add.'` datetime,
			PRIMARY KEY (`'.$this->col_id_linksync_log.'`)) DEFAULT CHARSET=utf8';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_create_table );
	}

	/**
	 * Empty linksync_log
	 */
	public function truncate_table(){
		global $wpdb;

		$truncate_query = 'TRUNCATE TABLE '.$this->get_table_name();

		return $wpdb->query($truncate_query);
	}

	/**
	 * Get rows from linksync_log table
	 * @param string $query The sql query
	 */
	public function select_by_query($query){
		global $wpdb;

		return $wpdb->get_results($query);
	}

	/**
	 * Insert Data to linksync_log
	 * @param array $data array of columns and value to insert
	 */
	public function insert($data){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.$this->table_name;

        return $wpdb->insert($table_name,$data);
	}

}