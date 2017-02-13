<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LS_Order_Json_Factory{

	private $json_orders = null;

	/**
	 * Set each key for orders post request http://developer.linksync.com/order
	 * @param $key
	 * @param $value
	 */
	public function set( $key, $value ){
		if( !empty($key) ){
			$this->json_orders[$key] = $value;
		}
	}

	public function set_uid( $value ){
		$value = empty($value) ? null : $value;
		$this->set( 'uid', $value );
	}

	public function set_created( $date ){
		$date = empty($date) ? null : $date;
		$this->set( 'created', $date );
	}

	public function set_orderId( $order_id ){
		$this->set( 'orderId', $order_id );
	}

	public function set_idSource( $source_id ){
		$this->set( 'idSource', $source_id );
	}

	public function set_orderType( $ordertype ){
		$this->set( 'orderType', $ordertype );
	}

	public function  set_source( $source ){
		$this->set( 'source', $source );
	}

	public function set_register_id( $register_id ){
		$register_id = empty($register_id) ? null : $register_id;
		$this->set( 'register_id', $register_id );
	}

	public function set_user_name( $user_name ){
		$user_name = empty( $user_name ) ? null : $user_name;
		$this->set( 'user_name', $user_name );
	}

	public function set_primary_email( $primary_email ){
		$primary_email = empty($primary_email) ? null : $primary_email;
		$this->set( 'primary_email', $primary_email );
	}

	public function set_total( $total ){
		$this->set( 'total', $total );
	}

	public function set_taxes_included( $included = 1 ){
		$this->set( 'taxes_included', $included );
	}

	public function set_global_tax_calculation( $taxsetup ){
		$this->set( 'globalTaxCalculation', $taxsetup );
	}

	public function set_total_tax( $total_tax ){
		$this->set( 'total_tax', $total_tax );
	}

	public function set_comments( $comments ){
		$this->set( 'comments', $comments );
	}

	public function set_currency( $currency ){
		$this->set( 'currency', $currency );
	}

	public function set_class_id( $class_id ){
		$this->set( 'class_id', $class_id );
	}

	public function set_location_id( $location_id ){
		$this->set( 'location_id', $location_id );
	}

	public function set_shipping_method( $shipping_method ){
		$this->set( 'shipping_method', $shipping_method );
	}

	public function set_tracking_number( $tracking_number ){
		$this->set( 'tracking_number', $tracking_number );
	}

	public function set_payment_type_id( $payment_type_id ){
		$this->set( 'payment_type_id', $payment_type_id );
	}

	public function set_billingAddress( $billingAddress ){
		$billingAddress = empty($billingAddress) ? null : $billingAddress;
		$this->set( 'billingAddress', $billingAddress );
	}

	public function set_deliveryAddress( $deliveryAddress ){
		$deliveryAddress = empty($deliveryAddress) ? null : $deliveryAddress;
		$this->set( 'deliveryAddress', $deliveryAddress );
	}

	public function set_payment( $payment ){
		$this->set( 'payment', $payment );
	}

	public function set_products( $products ){
		$products = empty($products) ? null : $products;
		$this->set( 'products', $products );
	}

	/**
	 * Returns a json representation of a single product for LWS
	 * @return string
	 */
	public function get_json_orders(){
		return json_encode($this->json_orders);
	}
}