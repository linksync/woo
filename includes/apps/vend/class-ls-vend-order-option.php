<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Order_Option extends LS_Vend_Option
{
    /**
     * LS_QBO_Product_Option instance
     * @var null
     */
    protected static $_instance = null;

    public static function instance(){

        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function sync_type()
    {
        return get_option('order_sync_type');
    }

    public function woocommerceToVend()
    {
        return 'wc_to_vend';
    }

    public function vendToWoocommmerce()
    {
        return 'vend_to_wc-way';
    }

    public function get_all_sync_type()
    {
        return array('wc_to_vend', 'vend_to_wc-way','disabled');
    }

    public function isTheOrderWasSyncToVend($WoocommerceOrderId)
    {
        $vendOrderId = get_post_meta($WoocommerceOrderId, '_ls_vend_order_id', true);
        if(empty($vendOrderId)){
            return false;
        }

        return true;
    }

    public function setFlagOrderWasSyncToVend($WoocommerceOrderId, $vendOrderId)
    {
        update_post_meta($WoocommerceOrderId, '_ls_vend_order_id', $vendOrderId);
    }

    public function getMappedVendPaymentId($woocommercePaymentMethod)
    {
        $wc_payment = get_option('wc_to_vend_payment');

        if (!empty($wc_payment)) {
            $total_payments = explode(",", $wc_payment);
            foreach ($total_payments as $mapped_payment) {
                $exploded_mapped_payment = explode("|", $mapped_payment);
                if (!empty($exploded_mapped_payment[2]) && !empty($exploded_mapped_payment[0])) {
                    if ($exploded_mapped_payment[2] == $woocommercePaymentMethod) {
                        $vend_payment_data = explode("%%", $exploded_mapped_payment['0']);
                        return $vend_payment_data;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Will be used on order register_id key
     * @return mixed|null|void
     */
    public function getRegisterId()
    {
        $registerId = get_option('wc_to_vend_register', null);
        return empty($registerId) ? null : $registerId;
    }

    /**
     * Return the selected vend user Id under Order Syncing Settings -> Outlets setting
     * @return array|null
     */
    public function getSelectedVendUser()
    {
        $vend_user_detail = get_option('wc_to_vend_user');
        if (isset($vend_user_detail) && !empty($vend_user_detail)) {
            $user = explode('|', $vend_user_detail);
            $vend_uid = isset($user[0]) ? $user[0] : null;
            $vend_username = isset($user[1]) ? $user[1] : null;

            return array(
                'vend_uid' => $vend_uid,
                'vend_username' => $vend_username
            );
        }

        return array(
            'vend_uid' => null,
            'vend_username' => null
        );
    }

    public function useBillingAddressToBePhysicalAddress()
    {
        return self::instance()->get_option('usebillingtobephysical', 'no');
    }

    public function setBillingAddressToBePhysicalAddress($optionValue)
    {
        $optValue = 'no';
        if('yes' == $optionValue){
            $optValue = $optionValue;
        }

        return self::instance()->update_option('usebillingtobephysical', $optValue);
    }

    public function useShippingAddressToBePostalAddress()
    {
        return self::instance()->get_option('useshippingtobepostal', 'no');
    }

    public function setShippingAddressToBePostalAddress($optionValue)
    {
        $optValue = 'no';
        if('yes' == $optionValue){
            $optValue = $optionValue;
        }

        return self::instance()->update_option('useshippingtobepostal', $optValue);
    }
}

