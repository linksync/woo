<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LS_Product_Api{
	/**
	 * The api object
	 * @var null
	 */
	public $api = null;

	public $pagination = null;

	public $product = null;

	public function __construct(LS_Api $api) {
		$this->api = $api;
	}

	/**
	 * Get the products
	 * @param null|string $params
	 * @return array|null
	 */
	public function get_product( $params = null ){
		$result = null;

		if( !empty($params) ){

			$str_params = '';
			if(is_array($params)){
				foreach($params as $key => $value){
					$str_params .= $key.'='.urlencode($value).'&';
				}

			}else{
				$str_params = $params;
			}
			//Remove last & in the params string
			$str_params = rtrim($str_params, '&');

			$result = $this->api->get('product?'.$str_params);

		}else {

			$result = $this->api->get('product');
		}

		$this->product		=	!empty($result['products']) ? $result['products'] : null;
		$this->pagination	=	!empty($result['pagination']) ? $result['pagination'] : null;

		return  $result;

	}

	/**
	 * @param int $page
	 * @return null
	 */
	public function get_product_by_page( $page = 1 ){
		$result = null;

		if( is_numeric($page) && !empty($page) ){

			$result = $this->get_product('page='.$page);

		}

		return $result;
	}

	/**
	 * Get the total pages
	 * @return int
	 */
	public function get_total_pages(){
		$return = 0;
		if( isset( $this->pagination['pages'] ) ){
			$return = $this->pagination['pages'];
		}

		return $return;
	}

	/**
	 * Get the current page
	 * @return int
	 */
	public function get_current_page(){
		$return = 1;
		if( isset( $this->pagination['page'] ) ){
			$return = $this->pagination['page'];
		}

		return $return;
	}

	/**
	 * Check if the response has next page
	 * @return bool
	 */
	public function has_next_page(){
		$return = false;

		if( $this->get_current_page() < $this->get_total_pages() ){
			$return = true;
		}

		return $return;
	}

	/**
	 * @return null
	 */
	public function get_next_page(){
		$return = null;

		if( $this->get_current_page() < $this->get_total_pages() ){
			$return = $this->pagination['page'] + 1;
		}

		return $return;
	}

	/**
	 * Returns duplicate product
	 * @return array|null
	 */
	public function get_duplicate_products(){
		return $this->get_product('duplicate=true');
	}


	/**
	 * Delete a product base on sku
	 *
	 * @param $sku
	 * @return array|null
	 */
	public function delete_product( $sku ){
		if( !empty($sku) ){
			return $this->api->delete('product/'. urlencode($sku));
		}
		return null;
	}

	public function save_product( $json_product ){
		if( !empty($json_product) ){
			return $this->api->post('product', $json_product );
		}
		return null;
	}
}