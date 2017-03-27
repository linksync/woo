<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Tax_helper
{

    public static function getVendTaxDetailsBaseOnTaxClassMapping($taxRateName, $taxRateClass)
    {

        $wooTaxRate = LS_Woo_Tax::get_tax_rate_by_name_and_class($taxRateName, $taxRateClass);
        if (!empty($wooTaxRate)) {
            $taxClasses = self::getTaxClassMappingFromWooToVend($taxRateName, $taxRateClass);
            if ('success' == $taxClasses['result']) {
                $vend_taxes = explode('/', $taxClasses['tax_classes']);
                return array(
                    'taxId' => isset($vend_taxes[0]) ? $vend_taxes[0] : null,
                    'taxName' => isset($vend_taxes[1]) ? $vend_taxes[1] : null,
                    'taxRate' => isset($vend_taxes[2]) ? $vend_taxes[2] : null
                );
            }
        }
        return array(
            'taxId' => null,
            'taxName' => null,
            'taxRate' => null
        );
    }

    public static function getTaxClassMappingFromWooToVend($tax_name, $tax_class = NULL)
    {
        if (empty($tax_class))
            $tax_class = 'standard-tax';

        $wc_taxes = get_option('wc_to_vend_tax');
        if (isset($wc_taxes) && !empty($wc_taxes)) {
            $explode_tax = explode(',', $wc_taxes);
            if (isset($explode_tax) && !empty($explode_tax)) {
                foreach ($explode_tax as $taxes) {
                    $explode_taxes = explode('|', $taxes);
                    if (isset($explode_taxes) && !empty($explode_taxes)) {
                        if (in_array($tax_name . '-' . $tax_class, $explode_taxes)) {
                            return array('result' => 'success', 'tax_classes' => $explode_taxes[0]);
                        }
                    } else {
                        return array('result' => 'error', 'tax_classes' => NULL);
                    }
                }
            }
        }
    }

    public static function get_tax_details_for_product($taxname)
    {
        $result = array();
        $taxDb = get_option('tax_class');
        if (isset($taxDb) && !empty($taxDb)) {
            $tax_class = explode(",", $taxDb);
            foreach ($tax_class as $new) {
                $taxes = explode("|", $new);
                if (in_array($taxname, $taxes)) {
                    $tax = explode("-", @$taxes[0]);
                    $result['tax_rate'] = @$tax[1]; //tax_rate
                    $result['tax_name'] = @$tax[0]; //tax_name
                    return array('result' => 'success', 'data' => $result);
                }
            }
        }
        return array('result' => 'error', 'data' => 'no tax rule set');
    }

    /**
     * @return string either 'on' or 'off'
     */
    public static function is_excluding_tax()
    {
        $excluding_tax = 'on';

        $woocommerce_calc_taxes = LS_Vend()->option()->woocommerce_calc_taxes();
        $woocommerce_prices_include_tax = LS_Vend()->option()->woocommerce_prices_include_tax();
        $linksync_woocommerce_tax_option = LS_Vend()->option()->linksync_woocommerce_tax_option();

        $excluding_tax = get_option('excluding_tax');

        if ('yes' == $woocommerce_calc_taxes) {

            if ('on' == $linksync_woocommerce_tax_option) {
                $excluding_tax = 'on'; //Excluding tax is on

                if ('yes' == $woocommerce_prices_include_tax) {
                    $excluding_tax = 'off'; //Include tax is on
                }
            }
        }

        return $excluding_tax;
    }

    public static function is_taxable($tax_status, $tax_class)
    {
        if ('taxable' == $tax_status) {
            $taxname = empty($tax_class) ? 'standard-tax' : $tax_class;
            if (!empty($taxname)) {
                $response_taxes = LS_Vend_Tax_helper::get_tax_details_for_product($taxname);
                if ('success' == $response_taxes['result']) {
                    //$product['tax_name'] = !empty($product_meta->get_tax_name()) ? $product_meta->get_tax_name() : html_entity_decode($response_taxes['data']['tax_name']);
                    //$product['tax_rate'] = !empty($product_meta->get_tax_rate()) ? $product_meta->get_tax_rate() : $response_taxes['data']['tax_rate'];
                    //$taxsetup = true;
                    return true;
                }
            }

        }
        return false;

    }
}