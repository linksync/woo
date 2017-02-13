<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Api
{
    public $api = null;
    public $product = null;
    public $order = null;

    public function __construct(LS_Api $api)
    {
        $this->api = $api;
        $this->product = new LS_Product_Api($api);
        $this->order = new LS_Order_Api($api);
    }

    public function product()
    {
        return $this->product;
    }

    public function order()
    {
        return $this->order;
    }


}