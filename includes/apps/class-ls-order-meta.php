<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Order_Meta
{

    private $orderId = null;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function set_vend_order_id()
    {

    }

    public function get_customer_id()
    {
        return $this->get_meta('_customer_user');
    }

    public function update_costumer_id($customer_id)
    {
        return $this->update_meta('_customer_user', $customer_id);
    }


    public function get_billing_first_name()
    {
        return $this->get_meta('_billing_first_name');
    }

    public function update_billing_first_name($first_name)
    {
        return $this->update_meta('_billing_first_name', $first_name);
    }


    public function get_billing_last_name()
    {
        return $this->get_meta('_billing_last_name');
    }

    public function update_billing_last_name($last_name)
    {
        return $this->update_meta('_billing_last_name', $last_name);
    }


    public function get_billing_email()
    {
        return $this->get_meta('_billing_email');
    }

    public function update_billing_email($email)
    {
        return $this->update_meta('_billing_email', $email);
    }

    public function get_vend_order_id()
    {
        return $this->get_meta('_ls_vend_oid');
    }

    public function update_vend_order_id($meta_value)
    {
        return $this->update_meta('_ls_vend_oid', $meta_value);
    }

    public function get_vend_receipt_number()
    {
        return $this->get_meta('_ls_vend_receipt_number');
    }

    public function update_vend_receipt_number($receipt_number)
    {
        return $this->update_meta('_ls_vend_receipt_number', $receipt_number);
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