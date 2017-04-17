<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Product_Option extends LS_Vend_Option
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

    public function sync_type($default = '')
    {
        return get_option('product_sync_type', $default);
    }

    public function update_sync_type($meta_value)
    {
        return update_option('product_sync_type', $meta_value);
    }

    public function nameTitle()
    {
        return get_option('ps_name_title');
    }

    public function updateNameTitle($meta_value)
    {
        return update_option('ps_name_title', $meta_value);
    }

    public function description()
    {
        return get_option('ps_description');
    }

    public function updateDescription($meta_value)
    {
        return update_option('ps_description', $meta_value);
    }

    public function price()
    {
        return get_option('ps_price');
    }

    public function updatePrice($meta_value)
    {
        return update_option('ps_price', $meta_value);
    }

    public function priceField()
    {
        return get_option('price_field');
    }

    public function updatePriceField($meta_value)
    {
        return update_option('price_field', $meta_value);
    }

    public function brand()
    {
        return get_option('ps_brand');
    }

    public function updateBrand($meta_value)
    {
        return update_option('ps_brand', $meta_value);
    }

    public function quantity()
    {
        return get_option('ps_quantity');
    }

    public function updateQuantity($meta_value)
    {
        return update_option('ps_quantity', $meta_value);
    }


    public function tag()
    {
        return get_option('ps_tags');
    }

    public function updatTag($meta_value)
    {
        return update_option('ps_tags', $meta_value);
    }


    public function linksyncStatus($default = '')
    {
        return get_option('linksync_status', $default);
    }

    public function updateLinksyncStatus($meta_value)
    {
        return update_option('linksync_status', $meta_value);
    }

    public function displayRetailPriceTaxInclusive()
    {
        return get_option('linksync_tax_inclusive');
    }

    public function updateDisplayRetailPriceTaxInclusive($meta_value)
    {
        return update_option('linksync_tax_inclusive', $meta_value);
    }

    public function delete()
    {
        return get_option('ps_delete');
    }

    public function update_delete($meta_value)
    {
        return update_option('ps_delete', $meta_value);
    }
}