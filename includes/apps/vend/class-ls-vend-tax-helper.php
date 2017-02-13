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
}