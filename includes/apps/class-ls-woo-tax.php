<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

class LS_Woo_Tax{

    public static function get_mapped_quickbooks_tax_for_product($taxMappingInfo, $orderTax, $taxClass){

        $qbo_tax =   '';
        if (!empty($orderTax)) {
            foreach ($orderTax as $tax_label) {
                $tax_line_item = new LS_Woo_Order_Line_Item($tax_label);
                $tax_info = LS_Woo_Tax::get_tax_rate_by_name_and_class(
                    $tax_line_item->get_tax_name(),
                    $taxClass
                );

                if (isset($tax_info['tax_rate_id']) && isset($tax_info['tax_rate_class'])) {
                    $wc_tax_class = ('' == $tax_info['tax_rate_class']) ? 'standard' : $tax_info['tax_rate_class'];
                    $wc_tax_id = $tax_info['tax_rate_id'];
                    $qbo_tax = explode('|', $taxMappingInfo[$wc_tax_id][$wc_tax_class]);
                    break;
                }

            }
        }

        return $qbo_tax;
    }

    public static function get_mapped_quickbooks_tax_for_shipping($taxMappingInfo, $orderTax){
        $qbo_tax =   '';
        if (!empty($orderTax)) {
            foreach ($orderTax as $tax_label) {
                $tax_line_item = new LS_Woo_Order_Line_Item($tax_label);
                if(empty($tax_line_item->lineItem['shipping_tax_amount'])){
                    return '';//No tax was set up for shipping
                }
                $tax_info = LS_Woo_Tax::get_tax_rate_by_rate_id($tax_line_item->lineItem['rate_id']);

                if (isset($tax_info['tax_rate_id']) && isset($tax_info['tax_rate_class'])) {
                    $wc_tax_class = ('' == $tax_info['tax_rate_class']) ? 'standard' : $tax_info['tax_rate_class'];
                    $wc_tax_id = $tax_info['tax_rate_id'];
                    $qbo_tax = explode('|', $taxMappingInfo[$wc_tax_id][$wc_tax_class]);
                    break;
                }

            }
        }
        return $qbo_tax;
    }

    /**
     * Get woocmmerce tax price setup, whether "Yes, I will enter prices inclusive of tax" or "No, I will enter prices exclusive of tax"
     *
     * @return bool|null
     */
    public static function is_included(){
        $tax_type = null;
        $woo_calc_taxes = LS_QBO()->options()->woocommerce_calc_taxes();
        $woo_prices_include_tax = LS_QBO()->options()->woocommerce_prices_include_tax();

        if ('yes' == $woo_calc_taxes ) {

            if ('yes' == $woo_prices_include_tax) {
                $tax_type = true; //Include tax is on
            } else {
                $tax_type = false; //Excluding tax is on
            }

        }

        return $tax_type;
    }

    /**
     * Get woocommerce tax classes
     * @return null|array
     */
    public static function get_tax_classes(){
        $tax_classes = null;

        $classes  = WC_Tax::get_tax_classes();

        //Add standard class
        $tax_classes['standard'] = 'Standard';
        foreach($classes as $class_name){
            $key = str_replace(' ', '-', strtolower($class_name));
            $tax_classes[$key] = $class_name;
        }

        return $tax_classes;
    }

    /**
     * Get taxt rate by name and by class
     *
     * @param string $tax_name
     * @param string $tax_class
     * @return array|null|object|void
     */
    public static function get_tax_rate_by_name_and_class($tax_name = '', $tax_class = ''){
        global $wpdb;
        $table_name = '`' . $wpdb->prefix . 'woocommerce_tax_rates`';
        $sql_prepare = 'SELECT * FROM ' . $table_name.' WHERE  tax_rate_name= %s AND tax_rate_class= %s ';
        $tax_rate_info = $wpdb->get_row($wpdb->prepare($sql_prepare, $tax_name, $tax_class), ARRAY_A);
        if ($tax_rate_info) return $tax_rate_info;
        return null;
    }

    /**
     * Get woocommerce Tax Details using Tax Woocommerce tax_rate_id
     * @param $tax_rate_id
     * @return array|null|object|void
     */
    public static function get_tax_rate_by_rate_id($tax_rate_id)
    {
        if (empty($tax_rate_id)) {
            return null;
        }
        global $wpdb;
        $table_name = '`' . $wpdb->prefix . 'woocommerce_tax_rates`';
        $sql_prepare = 'SELECT * FROM ' . $table_name.' WHERE  tax_rate_id= %s ';
        $tax_rate_info = $wpdb->get_row($wpdb->prepare($sql_prepare, $tax_rate_id), ARRAY_A);
        if ($tax_rate_info) return $tax_rate_info;
        return null;

    }

    /**
     * Get woocommerce tax rates
     * @param bool|string $tax_rate_class could be 'reduced-rate', 'zero-rate' or empty(standard) ''
     * @return array|null|object
     */
    public static function get_tax_rates($tax_rate_class = false){
        global $wpdb;
        $table_name = '`' . $wpdb->prefix . 'woocommerce_tax_rates`';
        $where = '';
        $sql_prepare = 'SELECT * FROM ' . $table_name;

        if (false !== $tax_rate_class) {

            $where = " WHERE `tax_rate_class` = %s ";
            if( '' == $tax_rate_class || 'standard' == $tax_rate_class){
                $tax_rate_class = '';
            }
            $sql_prepare = $wpdb->prepare($sql_prepare. $where, $tax_rate_class);

        }

        $woo_taxrates = $wpdb->get_results($sql_prepare, ARRAY_A);

        if ($woo_taxrates) return $woo_taxrates;

        return null;
    }

    /**
     * Get Woocommerce Standard Rates
     *
     * @return array|null|object
     */
    public static function get_standard_tax_rates(){
        //Standard rate has and emtpy tax_rate_class
        return self::get_tax_rates('');
    }

    /**
     * Get Woocommerce Reduce Rates
     * @return array|null|object
     */
    public static function get_reduce_tax_rates(){
        return self::get_tax_rates('reduced-rate');
    }

    /**
     * Get Woocommerce Zero Rates
     * @return array|null|object
     */
    public static function get_zero_tax_rates(){
        return self::get_tax_rates('zero-rate');
    }

    public static function getQuickBooksTaxInfoByWooTaxKey($wooTaxId)
    {
        if(empty($wooTaxId)){
            return 'empty_woo_tax_id';
        }

        $productOptions = '';//LS_QBO()->product_option();
        $taxClasses = $productOptions->tax_class();

        if(!empty($taxClasses[$wooTaxId])){
            $qboTaxId = $taxClasses[$wooTaxId];
        }

        if(!empty($qboTaxId)){
            if('no_tax' == $qboTaxId){
                $qboTaxInfo['id'] = null;
                $qboTaxInfo['name'] = null;
                $qboTaxInfo['rateValue'] = null;
                return $qboTaxInfo;
            }

            $qboTaxClasses = LS_QBO()->options()->getQuickBooksTaxClasses();
            foreach ($qboTaxClasses as $qboTaxInfo){
                if($qboTaxId == $qboTaxInfo['id']){
                    return $qboTaxInfo;
                }
            }
        }

        return null;
    }

    /**
     *
     * @return mixed|void
     */
    public static function getQuickBooksTaxClasses()
    {
        return get_option('ls_qbo_tax_classes');
    }

}