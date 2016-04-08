<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LS_Vend_Api_Key{

	/**
	 * @var Table name 
	 */
	public static $table_name 	= 'linksync_laidKey';

	/**
	 *@var column name id
	 */
	public static $col_id 		= 'id';

	/**
	 * @var column name api_key
	 */
	public static $col_api_key	= 'api_key';

	/**
	 *@var column name status
	 */
	public static $col_status 	= 'status';

	/**
	 * @var column name date_add
	 */
	public static $col_date_add	= 'date_add';


	/**
	 * Create of linksync_laidKey table
	 */
	public static function create_table(){
		global $wpdb;

		$sql_create_table = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix .self::$table_name. '`(
			`'.self::$col_id.'` int(10) unsigned NOT NULL auto_increment,
			`'.self::$col_api_key.'` varchar(200),
            `'.self::$col_status.'` varchar(50),
            `'.self::$col_date_add.'` date,
			PRIMARY KEY (`'.self::$col_id.'`)) DEFAULT CHARSET=utf8';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_create_table );
	}

	/**
	 * Select all linksync_laidKey rows and Change our output default type to array
	 * @param string $output_type
	 */
	public static function select_all($output_type = ''){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;

		$sql = 'SELECT * FROM '.$table_name;

		/**
		 * Change our output_type to default in ARRAY_A
		 * Reference for output type https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
		 */

		if('' == $output_type){
			return $wpdb->get_results($sql, ARRAY_A );
		}else{
			if('OBJECT' == $output_type){

				return $wpdb->get_results($sql, OBJECT );

			}else if('OBJECT_K ' == $output_type){

				return $wpdb->get_results($sql, OBJECT_K );

			}else if('ARRAY_A' == $output_type){

				return $wpdb->get_results($sql, ARRAY_A );

			}else if('ARRAY_N' == $output_type){

				return $wpdb->get_results($sql, ARRAY_N );

			}else {

				return $wpdb->get_results($sql, ARRAY_A );

			}

		}

	}

	/**
	 * Get rows base on apikey
	 * @param string $api_key
	 */
	public static function select_by_apikey($api_key){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;


		$sql = 'SELECT * FROM '. $table_name .' WHERE '.self::$col_api_key.' = %s ';

		return $wpdb->get_row($wpdb->prepare($sql,$api_key), ARRAY_A );

	}
	/**
	 * Get rows base on id
	 * @param string $id
	 */
	public static function select_by_id($id){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;


		$sql = 'SELECT * FROM '. $table_name .' WHERE '.self::$col_id.' = %d ';

		return $wpdb->get_row($wpdb->prepare($sql,$id), ARRAY_A );
	}

	/**
	 * Get count rows inside linksync_laidKey
	 * @return int as the number of rows
	 */
	public static function get_count(){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;

		$sql = 'SELECT COUNT('.self::$col_id.') FROM '.$table_name;


		return $wpdb->get_var($sql);
	}

	/**
	 * Insert row 
	 * @param array $data_to_insert
	 * format:
	 * 		array(
	 * 			'column_name' => 'value'
	 * 		)
	 */
	public static function insert($data_to_insert){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;

		return $wpdb->insert($table_name,$data_to_insert);
	}

	/**
	 *$wpdb update specific for linksync_laidKey table
	 *@param array $data key as the column of the array and value as exact value
	 *@param array $where
	 */
	public static function update($data, $where){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;

		return $wpdb->update($table_name,$data,$where);
	}

	/**
	 * Update Vend api status
	 * @param string $status 
	 * @param string $api_key vend api key
	 * @return null
	 */
	public static function update_status($status, $api_key){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;

		/**
		 * data to be update 
		 * format: 
		 * 		array(
		 *   		'column_name' => value
		 * 		)
		 */
		
		$data = array(
			self::$col_status => $status
		);

		//where 
		$where = array(
			self::$col_api_key=> $api_key
		);

		return $wpdb->update($table_name,$data,$where);
	}

	/**
	 * Delete using api_key value
	 * @param string $api_key
	 */

	public static function delete_by_api($api_key){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;

		return $wpdb->delete($table_name, array(self::$col_api_key => trim($api_key)));
	}

	/**
	 * Delete using table id
	 */
	public static function delete_by_id($id){
		global $wpdb;

		//set the table name
		$table_name = $wpdb->prefix.self::$table_name;
		return $wpdb->delete($table_name, array(self::$col_id => trim($id)));
	}


}