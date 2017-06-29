<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Order_Json_Factory
{

    private $json_orders = null;

    /**
     * Set each key for orders post request http://developer.linksync.com/order
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        if (!empty($key)) {
            $this->json_orders[$key] = $value;
        }
    }

    public function get($key)
    {
        return isset($this->json_orders[$key]) ? $this->json_orders[$key] : null;
    }

    public function set_uid($value)
    {
        $value = empty($value) ? null : $value;
        $this->set('uid', $value);
    }

    public function get_uid()
    {
        return $this->get('uid');
    }

    public function set_created($date)
    {
        $date = empty($date) ? null : $date;
        $this->set('created', $date);
    }

    public function get_created()
    {
        return $this->get('created');
    }

    public function set_orderId($order_id)
    {
        $this->set('orderId', $order_id);
    }

    public function get_orderId()
    {
        return $this->get('orderId');
    }

    public function set_idSource($source_id)
    {
        $this->set('idSource', $source_id);
    }

    public function get_idSource()
    {
        return $this->get('idSource');
    }

    public function set_orderType($ordertype)
    {
        $this->set('orderType', $ordertype);
    }

    public function get_orderType()
    {
        return $this->get('orderType');
    }

    public function set_source($source)
    {
        $this->set('source', $source);
    }

    public function get_source()
    {
        return $this->get('source');
    }

    public function set_register_id($register_id)
    {
        $register_id = empty($register_id) ? null : $register_id;
        $this->set('register_id', $register_id);
    }

    public function get_register_id()
    {
        return $this->get('register_id');
    }

    public function set_user_name($user_name)
    {
        $user_name = empty($user_name) ? null : $user_name;
        $this->set('user_name', $user_name);
    }

    public function get_user_name()
    {
        return $this->get('user_name');
    }

    public function set_primary_email($primary_email)
    {
        $primary_email = empty($primary_email) ? null : $primary_email;
        $this->set('primary_email', $primary_email);
    }

    public function get_primary_email()
    {
        return $this->get('primary_email');
    }

    public function set_total($total)
    {
        $this->set('total', $total);
    }

    public function get_total()
    {
        return $this->get('total');
    }

    public function set_taxes_included($included = 1)
    {
        $this->set('taxes_included', $included);
    }

    public function get_taxes_included()
    {
        return $this->get('taxes_included');
    }

    public function set_global_tax_calculation($taxsetup)
    {
        $this->set('globalTaxCalculation', $taxsetup);
    }

    public function get_globalTaxCalculation()
    {
        return $this->get('globalTaxCalculation');
    }

    public function set_total_tax($total_tax)
    {
        $this->set('total_tax', $total_tax);
    }

    public function get_total_tax()
    {
        return $this->get('total_tax');
    }

    public function set_comments($comments)
    {
        $this->set('comments', $comments);
    }

    public function get_comments()
    {
        return $this->get('comments');
    }

    public function set_currency($currency)
    {
        $this->set('currency', $currency);
    }

    public function get_currency()
    {
        return $this->get('currency');
    }

    public function set_class_id($class_id)
    {
        $this->set('class_id', $class_id);
    }

    public function get_class_id()
    {
        return $this->get('class_id');
    }

    public function set_location_id($location_id)
    {
        $this->set('location_id', $location_id);
    }

    public function get_location_id()
    {
        return $this->get('location_id');
    }

    public function set_shipping_method($shipping_method)
    {
        $this->set('shipping_method', $shipping_method);
    }

    public function get_shipping_method()
    {
        return $this->get('shipping_method');
    }

    public function set_tracking_number($tracking_number)
    {
        $this->set('tracking_number', $tracking_number);
    }

    public function get_tracking_number()
    {
        return $this->get('tracking_number');
    }

    public function set_payment_type_id($payment_type_id)
    {
        $this->set('payment_type_id', $payment_type_id);
    }

    public function get_payment_type_id()
    {
        return $this->get('payment_type_id');
    }

    public function set_billingAddress($billingAddress)
    {
        $billingAddress = empty($billingAddress) ? null : $billingAddress;
        $this->set('billingAddress', $billingAddress);
    }

    public function get_billingAddress()
    {
        return $this->get('billingAddress');
    }

    public function set_deliveryAddress($deliveryAddress)
    {
        $deliveryAddress = empty($deliveryAddress) ? null : $deliveryAddress;
        $this->set('deliveryAddress', $deliveryAddress);
    }

    public function get_deliveryAddress()
    {
        return $this->get('deliveryAddress');
    }

    public function set_payment($payment)
    {
        $this->set('payment', $payment);
    }

    public function get_payment()
    {
        return $this->get('payment');
    }

    public function set_products($products)
    {
        $products = empty($products) ? null : $products;
        $this->set('products', $products);
    }

    public function get_products()
    {
        return $this->get('products');
    }

    public function getOrderArray()
    {
        return $this->json_orders;
    }

    /**
     * Returns a json representation of a single product for LWS
     * @return string
     */
    public function get_json_orders()
    {
        return json_encode($this->json_orders);
    }
}