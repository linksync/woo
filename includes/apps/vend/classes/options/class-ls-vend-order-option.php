<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Order_Option extends LS_Vend_Option
{
    /**
     * LS_QBO_Product_Option instance
     * @var null
     */
    protected static $_instance = null;

    public static function instance()
    {

        if (is_null(self::$_instance)) {
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
        return array('wc_to_vend', 'vend_to_wc-way', 'disabled');
    }

    public function isTheOrderWasSyncToVend($WoocommerceOrderId)
    {
        $vendOrderId = get_post_meta($WoocommerceOrderId, '_ls_vend_order_id', true);
        if (empty($vendOrderId)) {
            return false;
        }

        return true;
    }

    public function orderStatusWooToVend()
    {
        return get_option('order_status_wc_to_vend');
    }

    public function updateOrderStatusWooToVend($meta_value)
    {
        return update_option('order_status_wc_to_vend', $meta_value);
    }

    public function orderStatusVendToWoo()
    {
        return get_option('order_vend_to_wc', 'wc-processing');
    }

    public function updateOrderStatusVendToWoo($meta_value)
    {
        return update_option('order_vend_to_wc', $meta_value);
    }


    public function customerExport()
    {
        return get_option('wc_to_vend_export', 'customer');
    }

    public function updateCustomerExport($meta_value)
    {
        return update_option('wc_to_vend_export', $meta_value);
    }

    public function customerImport()
    {
        return get_option('vend_to_wc_customer', 'customer_data');
    }

    public function updateCustomerImport($meta_value)
    {
        return update_option('vend_to_wc_customer', $meta_value);
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
        if ('yes' == $optionValue) {
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
        if ('yes' == $optionValue) {
            $optValue = $optionValue;
        }

        return self::instance()->update_option('useshippingtobepostal', $optValue);
    }

    public function get_syncing_options()
    {
        return array(
            'sync_type' => $this->sync_type(),
            'order_status_vend_to_woo' => $this->orderStatusVendToWoo(),
            'customer_import' => $this->customerImport(),
            'order_status_woo_to_vend' => $this->orderStatusWooToVend(),
            'customer_export' => $this->customerExport()
        );
    }

    public static function save_order_syncing_settings()
    {
        $userOrderOptions = array();
        if (!empty($_POST['post_array'])) {
            if (!is_array($_POST['post_array'])) {
                parse_str($_POST['post_array'], $userOrderOptions);
            }

            if (!empty($userOrderOptions)) {

                //Woocommers To VEND
                if (!empty($userOrderOptions['order_sync_type'])) {
                    update_option('order_sync_type', $userOrderOptions['order_sync_type']);
                }
                if (isset($userOrderOptions['order_status_wc_to_vend'])) {
                    update_option('order_status_wc_to_vend', isset($userOrderOptions['order_status_wc_to_vend']) ? $userOrderOptions['order_status_wc_to_vend'] : 'off');
                } else {
                    update_option('order_status_wc_to_vend', 'off');
                }

                if (isset($userOrderOptions['wc_to_vend_outlet'])) {
                    $register = 'wc_to_vend_register|' . $userOrderOptions['wc_to_vend_outlet'];
                    if (isset($userOrderOptions[$register])) {
                        update_option('wc_to_vend_register', isset($userOrderOptions[$register]) ? $userOrderOptions[$register] : 'off');
                    } else {
                        update_option('wc_to_vend_register', 'off');
                    }
                    $user = 'wc_to_vend_user|' . $userOrderOptions['wc_to_vend_outlet'];
                    if (isset($user)) {
                        update_option('wc_to_vend_user', isset($userOrderOptions[$user]) ? $userOrderOptions[$user] : 'off');
                    } else {
                        update_option('wc_to_vend_user', 'off');
                    }
                    update_option('wc_to_vend_outlet', isset($userOrderOptions['wc_to_vend_outlet']) ? $userOrderOptions['wc_to_vend_outlet'] : 'off');
                } else {
                    update_option('wc_to_vend_outlet', 'off');
                }

                if (isset($userOrderOptions['wc_to_vend_tax']) && !empty($userOrderOptions['wc_to_vend_tax'])) {
                    $all_taxes = implode(',', $userOrderOptions['wc_to_vend_tax']);
                    update_option('wc_to_vend_tax', $all_taxes);
                } else {
                    update_option('wc_to_vend_tax', 'off');
                }
                if (isset($userOrderOptions['wc_to_vend_payment']) && !empty($userOrderOptions['wc_to_vend_payment'])) {
                    $all_payment = implode(',', $userOrderOptions['wc_to_vend_payment']);
                    update_option('wc_to_vend_payment', $all_payment);
                } else {
                    update_option('wc_to_vend_payment', 'off');
                }
                if ($userOrderOptions['wc_to_vend_export']) {
                    update_option('wc_to_vend_export', isset($userOrderOptions['wc_to_vend_export']) ? $userOrderOptions['wc_to_vend_export'] : 'off');
                    $orderOption = LS_Vend()->order_option();

                    $useBillingToBePhysicalOption = (isset($userOrderOptions['usebillingtobephysical']) && 'yes' == $userOrderOptions['usebillingtobephysical']) ? 'yes' : 'no';
                    $orderOption->setBillingAddressToBePhysicalAddress($useBillingToBePhysicalOption);

                    $useShippingToBePostalOption = (isset($userOrderOptions['useshippingtobepostal']) && 'yes' == $userOrderOptions['useshippingtobepostal']) ? 'yes' : 'no';
                    $orderOption->setShippingAddressToBePostalAddress($useShippingToBePostalOption);

                } else {
                    update_option('wc_to_vend_export', 'off');
                }
                //From VEND To Woocommers
                if (isset($userOrderOptions['order_vend_to_wc'])) {
                    update_option('order_vend_to_wc', isset($userOrderOptions['order_vend_to_wc']) ? $userOrderOptions['order_vend_to_wc'] : 'off');
                } else {
                    update_option('order_vend_to_wc', 'off');
                }


                if (isset($userOrderOptions['vend_to_wc_tax']) && !empty($userOrderOptions['vend_to_wc_tax'])) {
                    $all_taxes = implode(',', $userOrderOptions['vend_to_wc_tax']);
                    update_option('vend_to_wc_tax', $all_taxes);
                } else {
                    update_option('vend_to_wc_tax', 'off');
                }


                if (isset($userOrderOptions['vend_to_wc_payments']) && !empty($userOrderOptions['vend_to_wc_payments'])) {
                    $payment_vend = implode(',', $userOrderOptions['vend_to_wc_payments']);

                    update_option('vend_to_wc_payments', $payment_vend);
                } else {
                    update_option('vend_to_wc_payments', 'off');
                }

                if (isset($userOrderOptions['vend_to_wc_customer'])) {
                    update_option('vend_to_wc_customer', isset($userOrderOptions['vend_to_wc_customer']) ? $userOrderOptions['vend_to_wc_customer'] : 'off');
                } else {
                    update_option('vend_to_wc_customer', 'off');
                }

                if (isset($userOrderOptions['order_sync_type']) && $userOrderOptions['order_sync_type'] == 'vend_to_wc-way') {
                    // Update "since"
                    $current_date_time_string = strtotime(date("Y-m-d H:i:s"));
                    $time_offset = get_option('linksync_time_offset');
                    if (isset($time_offset) && !empty($time_offset)) {
                        $time = $current_date_time_string + $time_offset;
                    } else {
                        $time = $current_date_time_string; # UTC
                    }
                    $result_time = date("Y-m-d H:i:s", $time);
                    #order update  Request time
                    update_option('order_time_suc', $result_time);
                }

                if ($userOrderOptions['order_sync_type'] != 'disabled') {
                    if ($userOrderOptions['order_sync_type'] == 'vend_to_wc-way') {
                        $enable = 'Vend to Woo';
                    } else {
                        $enable = 'Woo to Vend';
                    }
                    $setting_message = $enable . ' is enable';
                } else {
                    $setting_message = 'Sync Setting Disabled';
                }

                $webhook = LS_Vend()->updateWebhookConnection();
                LS_Vend()->save_user_settings_to_linksync();

            }
        }

        //$vendView = new LS_Vend_View();
        //$vendView->display_order_configuration_tab();
        wp_send_json(array('msg' => 'success', 'post_data' => $userOrderOptions));
    }
}

