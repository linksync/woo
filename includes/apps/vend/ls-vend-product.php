<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

/**
 * Class for vend's product 
 */
class LS_vend_product{
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
	 * Get all the products from vend
	 * @return array of all products from vend
	 */
	public function get_vend_products(){
		
		$product = $this->api->get('product');
		$this->vend_response = $product;

		return $product['products'];
	}

	/**
	 * Get a certain product base on products sku
	 * @return array of product properties
	 */
	public function get_vend_product_by_sku($sku){
		/**
		 * Check vend_response has data ang products key exists to get a certain product
		 */
		if(!empty($this->vend_response) || isset($this->vend_response['products'])){
			foreach ($this->vend_response['products'] as $product) {
				if($product['sku'] == $sku){
					return $product;
				}
			}
		}else{
		/**
		 * Since vend_response is empty execute getting all the products 
		 * before getting the product by its sku
		 */
			$this->get_vend_products();
			if(!empty($this->vend_response) && isset($this->vend_response['products'])){
				foreach ($this->vend_response['products'] as $product) {
					if($product['sku'] == $sku){
						return $product;
					}
				}
			}
		}
	}



	/**
	 * This method will DELETE the product in VEND store by sku
	 * @param $sku products Stock Keeping unit
	 * @return array of response
	 */
	public function delete_vend_product($sku){
		return $this->api->delete('product/'.$sku);
	}
}