<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LS_Variant_Product extends LS_Simple_Product{

	/**
	 * Array of information of a variant Product
	 * @var array
	 */
	public $variant_product = null;

	public $parent_sku = null;

	public function __construct( $parent_sku, $variant ) {
		$this->product_type = 'product_variation';
		if( !empty( $variant ) ){
			$this->variant_product = $variant;
			$this->parent_sku	= $parent_sku;
		}
	}
	/**
	 * @param $name
	 * @return null|mixed
	 */
	public function __get( $key ) {
		// TODO: Implement __get() method.
		return isset( $this->variant_product[$key]) ? $this->variant_product[$key] : null ;
	}

	public function get_option_one_name(){
		return $this->option_one_name;
	}

	public function get_option_one_value(){
		return $this->option_one_value;
	}

	public function get_option_two_name(){
		return $this->option_two_name;
	}

	public function get_option_two_value(){
		return $this->option_two_value;
	}

	public function get_option_three_name(){
		return $this->option_three_name;
	}

	public function  get_option_three_value(){
		return $this->option_three_value;
	}
}