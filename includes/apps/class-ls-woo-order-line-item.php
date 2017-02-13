<?php if (!defined('ABSPATH')) exit('Access is Denied');

/**
 * Class LS_Woo_Order_Line_Item
 * Intended to parse Woocommerce Order line item in a more accessible way
 *
 * @property string qty
 * @property string tax_class
 * @property string product_id
 * @property string variation_id
 * @property string line_subtotal
 * @property string line_total
 * @property string line_subtotal_tax
 * @property string line_tax
 * @property string line_data_tax
 */
class LS_Woo_Order_Line_Item
{

	public $orderid = null;
	public $order = null;

	public $lineItem = array();
	public $name = '';
	public $itemMeta = null;
	public $wooLineItem = null;

	public function __construct($lineItemArray = array(), $order = null)
	{
		if (is_numeric($order)) {
			$this->order = new WC_Order($order);
			$this->orderid = $order;
		} elseif ($order instanceof WC_Order) {
			$this->order = $order;
			$this->orderid = $this->order->id;
		}
		$this->wooLineItem = $lineItemArray;
		$this->name = isset($lineItemArray['name']) ? $lineItemArray['name'] : null;
		$this->itemMeta = isset($lineItemArray['item_meta']) ? $lineItemArray['item_meta'] : null;
		$this->itemMetaArray = isset($lineItemArray['item_meta_array']) ? $lineItemArray['item_meta_array'] : null;
		$this->parseWooOrderLineItem($lineItemArray['item_meta_array']);

	}

	/**
	 * Parse key and value of the line item to be as one whole array with keys = value
	 * @param $lineItemArray
	 */
	public function parseWooOrderLineItem($lineItemArray)
	{
		if (!empty($lineItemArray)) {
			$lineItemArray = (array)$lineItemArray;

			foreach($lineItemArray as $item){

				$item = (array) $item; //to array
				$key = ltrim($item['key'], '_');
				$this->lineItem[$key] = $item['value'];

			}
		}
	}

	/**
	 * Get Discount Amount of the order perline item
	 *
	 * @return float|int
	 */
	public function get_discount_amount(){
		$discount = 0;
		$discount = (float)$this->line_subtotal - (float)$this->line_total;
		return $discount;
	}

	public function get_tax_class(){
		return isset($this->lineItem['tax_class']) ? $this->lineItem['tax_class'] : '';
	}

	public function get_tax_name(){
		return isset($this->lineItem['label']) ? $this->lineItem['label'] : '';
	}

	/**
	 * Get line item base on its key
	 * @param $key
	 * @return null
	 */
	public function __get( $key ) {
		return isset( $this->lineItem[$key]) ? $this->lineItem[$key] : null ;
	}
}