<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LS_Order_Api{

	/**
	 * The api object
	 * @var null
	 */
	public $api = null;

	public $pagination = null;

	public $orders = null;

	public function __construct(LS_Api $api) {
		$this->api = $api;
	}

	/**
	 * @param null $params
	 * @return array|null
	 */
	public function get_orders( $params = null ){
		$results = null;

		if( !empty($params) ){

			$str_params = '';
			if( is_array($params) ){
				foreach($params as $key => $value){
					$str_params .= $key.'='.$value.'&';
				}
			}else{
				$str_params = $params;
			}

			//Remove last & in the params string
			$str_params = rtrim('&', $str_params);
			$results = $this->api->get('order'.$str_params);

		}else {
			$results = $this->api->get('order');
		}

		$this->orders		=	!empty($results['orders']) ? $results['orders']: null;
		$this->pagination	=	!empty($result['pagination']) ? $result['pagination'] : null;

		return $results;
	}


	public function get_order_per_page(){

		if( isset($this->pagination['per_page']) ){
			return $this->pagination['per_page'];
		}
		return null;
	}

	/**
	 * Get total pages for orders
	 * @return int
	 */
	public function get_total_pages(){

		$return = 0;
		if( isset($this->pagination['pages']) ){
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
	 * get next page if available otherwise null
	 * @return int|null
	 */
	public function get_next_page(){
		$return = null;
		if( $this->get_current_page() < $this->get_total_pages() ){
			$return = $this->get_current_page() + 1;
		}
		return $return;
	}

	/**
	 * Save orders
	 * @param $json_orders
	 * @return array|null
	 */
	public function save_orders( $json_orders ){
		if( !empty($json_orders) ){
			return $this->api->post('order', $json_orders );
		}
		return null;
	}

}