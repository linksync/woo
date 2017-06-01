<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Order
{
    public $order;

    public function __construct($order)
    {
        if(!empty($order)){

            if (!is_array($order)) {
                $this->order = json_decode($order, true);
            } else {
                $this->order = $order;
            }
        }
    }

    /**
     * @param $key
     * @return null|mixed
     */
    public function __get($key)
    {
        // TODO: Implement __get() method.
        return isset($this->order[$key]) ? $this->order[$key] : null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getRegisterId()
    {
        return $this->register_id;
    }

    public function getPrimaryEmail()
    {
        return $this->primary_email;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getTaxIncluded()
    {
        return $this->taxes_included;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getBillingAddress()
    {
        if (empty($this->billingAddress)) {
            return null;
        }
        return new LS_Address($this->billingAddress);
    }

    public function getDeliveryAddress()
    {
        if (empty($this->deliveryAddress)) {
            return null;
        }

        return new LS_Address($this->deliveryAddress);
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function getProducts()
    {
        if (empty($this->products)) {
            return null;
        }

        $products = array();
        foreach ($this->products as $product) {
            $products[] = new LS_Product($product);
        }
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }


    public function getOrderArray()
    {
        return $this->order;
    }
}