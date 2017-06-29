<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Product_Variant extends LS_Product
{

    private $parent_product_meta = null;

    public function __construct(LS_Product_Meta $parent_product_meta, $product = null)
    {
        parent::__construct($product);
        $this->parent_product_meta = $parent_product_meta;
    }

    public function getParentMeta()
    {
        return $this->parent_product_meta;
    }

    public function get_option_one_name(){
        return $this->getData('option_one_name');
    }

    public function get_option_one_value(){
        return $this->getData('option_one_value');
    }

    public function get_option_two_name(){
        return $this->getData('option_two_name');
    }

    public function get_option_two_value(){
        return $this->getData('option_two_value');
    }

    public function get_option_three_name(){
        return $this->getData('option_three_name');
    }

    public function  get_option_three_value(){
        return $this->getData('option_three_value');
    }

    public function get_button_order()
    {
        return $this->getData('button_order');
    }
}