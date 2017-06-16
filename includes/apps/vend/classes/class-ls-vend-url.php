<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Url
{
    private $vend_config = null;
    private $vend_https = 'https://';
    private $vend_domain = 'vendhq.com';

    public function __construct(LS_Vend_Config $vend_config)
    {
        $this->vend_config = $vend_config;
    }

    /**
     * Get Vend store url
     * @return string
     */
    public function get_store_url()
    {
        $url = $this->vend_https;
        $url .= $this->vend_config->get_domain_prefix() . '.' . $this->vend_domain . '/';
        return $url;
    }

    /**
     * Get products url
     * @return string
     */
    public function get_products_url()
    {
        $url = $this->get_store_url();
        $url .= 'product';

        return $url;
    }

    /**
     * Get product View Url
     * @param $vend_product_id
     * @return string
     */
    public function get_product_view_url($vend_product_id)
    {
        $url = $this->get_products_url();
        $url .= '/'.$vend_product_id;

        return $url;
    }

    /**
     * Get product Edit url
     * @param $vend_product_id
     * @return string
     */
    public function get_product_edit_url($vend_product_id)
    {
        $url = $this->get_products_url();
        $url .= '/' . $vend_product_id . '/edit';

        return $url;
    }


    /**
     * Get order list url
     * @return string
     */
    public function get_order_url()
    {
        /**
         * Sample order url
         * https://s11tshirts.vendhq.com/history?receipt_number=
         */
        $url = $this->get_store_url();
        $url .= 'history';
        return $url;
    }

    /**
     * Return vend order url search by receipt number
     * @param $receipt_number
     * @return string
     */
    public function get_order_edit_url($receipt_number)
    {
        $url = $this->get_order_url();
        $url .= '?receipt_number='.$receipt_number;
        return $url;
    }


}