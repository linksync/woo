<?php

class LS_Order_Helper
{
    protected $order = null;

    public function __construct(WC_Order $order)
    {
        $this->order = $order;
    }

    public function getStatus()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->post->post_status;
        }

        return $this->order->get_status();
    }

    public function getPaymentMethod()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->payment_method;
        }
        return $this->order->get_payment_method();
    }

    public function getBillingPhone()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_phone;
        }
        return $this->order->get_billing_phone();
    }

    public function getBillingFirsName()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_first_name;
        }
        return $this->order->get_billing_first_name();
    }

    public function getBillingLastName()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_last_name;
        }
        return $this->order->get_billing_last_name();
    }

    public function getBillingAddressOne()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_address_1;
        }
        return $this->order->get_billing_address_1();
    }

    public function getBillingAddressTwo()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->get_billing_address_2;
        }
        return $this->order->get_billing_address_2();
    }

    public function getBillingCity()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_city;
        }
        return $this->order->get_billing_city();
    }

    public function getBillingState()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_state;
        }
        return $this->order->get_billing_state();
    }

    public function getBillingPostcode()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_postcode;
        }
        return $this->order->get_billing_postcode();

    }

    public function getBillingCountry()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_country;
        }
        return $this->order->get_billing_country();
    }

    public function getBillingCompany()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_company;
        }
        return $this->order->get_billing_company();
    }

    public function getBillingEmail()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->billing_email;
        }
        return $this->order->get_billing_email();
    }

    public function getShippingFirstName()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_first_name;
        }
        return $this->order->get_shipping_first_name();
    }

    public function getShippingLastName()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_last_name;
        }
        return $this->order->get_shipping_last_name();
    }

    public function getShippingAddressOne()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_address_1;
        }
        return $this->order->get_shipping_address_1();
    }

    public function getShippingAddressTwo()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_address_2;
        }
        return $this->order->get_shipping_address_2();
    }

    public function getShippingCity()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_city;
        }
        return $this->order->get_shipping_city();
    }

    public function getShippingState()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_state;
        }
        return $this->order->get_shipping_state();
    }

    public function getShippingPostCode()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_postcode;
        }
        return $this->order->get_shipping_postcode();
    }

    public function getShippingCountry()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_country;
        }
        return $this->order->get_shipping_country();
    }

    public function getShippingCompany()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->order->shipping_company;
        }
        return $this->order->get_shipping_company();

    }


}