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

    public function getTags()
    {
        return $this->api->get('vend/tags');
    }

    public function getTaxes()
    {
        return $this->api->get('vend/taxes');
    }

    public function getOutlets()
    {
        return $this->api->get('vend/outlets');
    }

    public function getRegisters()
    {
        return $this->api->get('vend/registers');
    }

    public function getPaymentTypes()
    {
        return $this->api->get('vend/paymentTypes');
    }

    public function getUsers()
    {
        return $this->api->get('vend/users');
    }


    public function getVendConfig()
    {
        return $this->api->get('vend/config');
    }

    public function sendLog($data)
    {
        return $this->api->post('laid/sendLog', $data);
    }

    public function save_users_settings( $user_settings )
    {
        $savedUserSettings = $this->api->post('config', $user_settings);

        return $savedUserSettings;
    }


}