<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');


/**
 * Class for getting the Connected Vend account
 */
class LS_Vend_Data{

	/**
	 * @var Vend api request handler
	 */
	public $api;

	/**
	 *  @var Vend data in executing a request 
	 */
	public $vend_response = array();

	/**
	 * Set $api variable for handling request
	 */
	public function __construct(){
		/**
		 * if LS_ApiController exist then set api variable
		 * else require the file for getting the api
		 */

		if(class_exists('LS_ApiController')){
			$this->api = LS_ApiController::get_api();
		}else{
			require_once LS_INC_DIR.'api/ls-api-controller.php';
			$this->api = LS_ApiController::get_api();
		}
		
	}

	/**
	 * Get all outlets from vend
	 * @return array of outlets
	 */
	public function get_vend_outlets(){
		$outlets =  $this->api->get('vend/outlets');
		if(isset($outlets['outlets'])){
			return $outlets['outlets'];
		}
	}

	/**
	 * Get vend config
	 * @return array of vend configs
	 */
	public function get_vend_config(){
		return $this->api->get('vend/config');
	}

	/**
	 * Get vend user
	 * @return array of information of the user
	 */
	public function get_vend_user(){
		return $this->api->get('vend/users');
	}



}