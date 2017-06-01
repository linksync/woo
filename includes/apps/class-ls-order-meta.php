<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Order_Meta
{

    private $orderId = null;

    public  function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderJsonFromWooToVend()
    {
        return $this->get_meta('_ls_json_from_woo_to_vend');
    }

    public function updateOrderJsonFromWooToVend($jsonBeingSentAndResponseFromLinkSync)
    {
        return $this->update_meta('_ls_json_from_woo_to_vend', $jsonBeingSentAndResponseFromLinkSync);
    }

    public function getOrderJsonFromVendToWoo()
    {
        return $this->get_meta('_ls_json_from_vend_to_woo');
    }

    public function updateOrderJsonFromVendToWoo($orderJsonFromVend)
    {
        return $this->update_meta('_ls_json_from_vend_to_woo', $orderJsonFromVend);
    }

    /**
     * @param $meta_key
     * @param $meta_value
     * @param bool $unique
     * @return false|int
     */
    public function add_meta($meta_key, $meta_value, $unique = false)
    {
        return add_post_meta($this->orderId, $meta_key, $meta_value, $unique);
    }

    /**
     * @param $meta_key
     * @param $meta_value
     * @return bool|int
     */
    public function update_meta($meta_key, $meta_value)
    {
        return update_post_meta($this->orderId, $meta_key, $meta_value);
    }

    /**
     * @param $meta_key
     * @return mixed
     */
    public function get_meta($meta_key)
    {
        return get_post_meta($this->orderId, $meta_key, true);
    }
}