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

    public $productOrderItem = null;
    public $lineOrderItemTax = null;

    public function __construct($lineItemArray = array(), $order = null)
    {
        if (is_numeric($order)) {
            $this->order = new WC_Order($order);
            $this->orderid = $order;
        } elseif ($order instanceof WC_Order) {
            $this->order = $order;
            $this->orderid = $this->order->get_id();
        }

        if (!LS_Vend_Helper::isWooVersionLessThan_2_4_15()) {
            $this->productOrderItem = new WC_Order_Item_Product($lineItemArray);
            $this->lineOrderItemTax = new WC_Order_Item_Tax($lineItemArray);
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

            foreach ($lineItemArray as $item) {

                $item = (array)$item; //to array
                $key = ltrim($item['key'], '_');
                $this->lineItem[$key] = $item['value'];

            }
        }
    }

    public function get_product_amount()
    {
        $product_amount = 0;
        $lineSubTotal = $this->get_subtotal();
        $lineQuantity = $this->get_quantity();
        if (!empty($lineSubTotal)) {
            $product_amount = (float)($lineSubTotal / $lineQuantity);
        }

        return $product_amount;
    }

    /**
     * Get Discount Amount of the order perline item
     *
     * @return float|int
     */
    public function get_discount_amount()
    {
        $discount = 0;
        if (null == $this->productOrderItem) {
            $discount = (float)$this->line_subtotal - (float)$this->line_total;
        } else {
            $discount = (float)$this->productOrderItem->get_subtotal() - (float)$this->productOrderItem->get_total();
        }


        if (!empty($discount)) {
            $discount = (float)($discount / $this->get_quantity());
        }
        return $discount;
    }

    public function get_tax_class()
    {
        if (null == $this->productOrderItem) {
            return isset($this->wooLineItem['tax_class']) ? $this->wooLineItem['tax_class'] : '';
        }

        return $this->productOrderItem->get_tax_class();

    }

    public function get_tax_rate_id()
    {
        if (null == $this->productOrderItem) {
            return isset($this->wooLineItem['rate_id']) ? $this->wooLineItem['rate_id'] : '';
        }

        return $this->lineOrderItemTax->get_rate_id();
    }

    public function get_tax_label()
    {
        if (null == $this->productOrderItem) {
            return isset($this->lineItem['label']) ? $this->lineItem['label'] : '';
        }

        return $this->lineOrderItemTax->get_label();
    }

    public function get_line_tax()
    {
        if (null == $this->productOrderItem) {
            return isset($this->lineItem['line_tax']) ? $this->lineItem['line_tax'] : '';
        }

        return $this->productOrderItem->get_total_tax();
    }

    public function get_subtotal()
    {
        if (null == $this->productOrderItem) {
            return $this->lineItem['line_subtotal'];
        }

        return $this->productOrderItem->get_subtotal();

    }

    public function get_subtotal_tax()
    {

    }

    public function get_quantity()
    {
        if (null == $this->productOrderItem) {
            return $this->lineItem['qty'];
        }

        return $this->productOrderItem->get_quantity();
    }

    public function get_total()
    {

    }

    public function get_total_tax()
    {

    }

    public function get_taxes()
    {

    }

    public function get_order_id()
    {

    }

    public function get_product_id()
    {
        if (null == $this->productOrderItem) {
            return $this->wooLineItem['product_id'];
        }

        return $this->productOrderItem->get_product_id();
    }

    public function get_variation_id()
    {
        if (null == $this->productOrderItem) {
            return $this->wooLineItem['variation_id'];
        }

        return $this->productOrderItem->get_variation_id();
    }

    public function get_name()
    {
        if (null == $this->productOrderItem) {
            return $this->wooLineItem['name'];
        }

        return $this->productOrderItem->get_name();
    }

    public function get_shipping_tax_total()
    {
        if (null == $this->lineOrderItemTax) {
            return $this->lineItem['shipping_tax_amount'];
        }

        return $this->lineOrderItemTax->get_shipping_tax_total();

    }


    public function get_bundled_items()
    {
        if (null == $this->productOrderItem) {
            return isset($this->lineItem['bundled_items']) ? $this->lineItem['bundled_items'] : null;
        }


        $custom_meta_data = $this->wooLineItem->get_data();

        foreach ($custom_meta_data['meta_data'] as $meta_data) {
            if ('_bundled_items' == $meta_data->key) {
                return (array)$meta_data;
            }

        }

        return null;
    }

    public function get_bundled_item_id()
    {
        if (null == $this->productOrderItem) {
            return isset($this->lineItem['bundled_item_id']) ? $this->lineItem['bundled_item_id'] : null;
        }


        $custom_meta_data = $this->wooLineItem->get_data();

        foreach ($custom_meta_data['meta_data'] as $meta_data) {
            if ('_bundled_item_id' == $meta_data->key) {
                return (array)$meta_data;
            }

        }

        return null;

    }

    public function get_bundled_item_priced_individually()
    {
        if (null == $this->productOrderItem) {
            return isset($this->lineItem['bundled_item_needs_shipping']) ? $this->lineItem['bundled_item_needs_shipping'] : null;
        }


        $custom_meta_data = $this->wooLineItem->get_data();

        foreach ($custom_meta_data['meta_data'] as $meta_data) {
            if ('_bundled_item_priced_individually' == $meta_data->key) {
                return (array)$meta_data;
            }

        }

        return null;
    }

    public function is_bundled_item_priced_individually()
    {
        $priced_individually = $this->get_bundled_item_priced_individually();
        return isset($priced_individually['value']) ? $priced_individually['value'] : null;
    }

    public function get_bundle_cart_key()
    {
        if (null == $this->productOrderItem) {
            return isset($this->lineItem['bundle_cart_key']) ? $this->lineItem['bundle_cart_key'] : null;
        }

        $custom_meta_data = $this->wooLineItem->get_data();

        foreach ($custom_meta_data['meta_data'] as $meta_data) {
            if ('_bundle_cart_key' == $meta_data->key) {
                return (array)$meta_data;
            }

        }

        return null;
    }

    /**
     * Get line item base on its key
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        return isset($this->lineItem[$key]) ? $this->lineItem[$key] : null;
    }
}