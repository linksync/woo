<?php if ( ! defined( 'ABSPATH' ) ) exit;  

class LS_ApiController{

	/**
	 * Get the api Object
	 * @param null
	 * @return Returns the api Object
	 */

	public static function get_api(){
		/**
		 * Get the configuration or the selection of api config.
		 */
		require_once(LS_PLUGIN_DIR.'ls-config.php');

		/**
		 * Check if test mode is set to true
		 */
		if($config['testmode']){
		  $config['api'] = 'test';
		}

		/**
		 * Require api information
		 */
		$apiConfig = include_once(LS_INC_DIR.'api/ls-api-info.php');

		/**
		 * Require the api Object
		 */
		include_once(LS_INC_DIR.'api/ls-api.php');

		$api = new LS_Api($apiConfig[$config['api']],get_option('linksync_laid'));

		return $api;
	}
}