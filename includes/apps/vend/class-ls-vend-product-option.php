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
        return get_option('ps_name_title', 'on');
    }

    public function updateNameTitle($meta_value)
    {
        return update_option('ps_name_title', $meta_value);
    }

    public function description()
    {
        return get_option('ps_description', 'on');
    }

    public function updateDescription($meta_value)
    {
        return update_option('ps_description', $meta_value);
    }

    public function shortDescription()
    {
        return get_option('ps_desc_copy', 'off');
    }

    public function updateShortDescription($meta_value)
    {
        return get_option('ps_desc_copy', $meta_value);
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
        return get_option('ps_quantity', 'on');
    }

    public function updateQuantity($meta_value)
    {
        return update_option('ps_quantity', $meta_value);
    }

    public function tag()
    {
        return get_option('ps_tags', 'off');
    }

    public function updatTag($meta_value)
    {
        return update_option('ps_tags', $meta_value);
    }

    public function category()
    {
        return get_option('ps_categories', 'off');
    }

    public function updateCategory($meta_value)
    {
        return $this->update_option('ps_categories', $meta_value);
    }

    public function productStatus()
    {
        return get_option('ps_pending', 'off');
    }

    public function updateProductStatus($meta_value)
    {
        return update_option('ps_pending', $meta_value);
    }

    public function image()
    {
        return get_option('ps_images', 'off');
    }

    public function updateImage($meta_value)
    {
        return update_option('ps_images', $meta_value);
    }

    public function createNew()
    {
        return get_option('ps_create_new', 'on');
    }

    public function updateCreateNew($meta_value)
    {
        return update_option('ps_create_new', $meta_value);
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
        return get_option('ps_delete', 'off');
    }

    public function update_delete($meta_value)
    {
        return update_option('ps_delete', $meta_value);
    }

    public function attributes()
    {
        return get_option('ps_attribute', 'on');
    }

    public function updateAttributes($meta_value)
    {
        return update_option('ps_attribute', $meta_value);
    }

    public function attributeVisibleOnProductPage()
    {
        return get_option('linksync_visiable_attr', '1');
    }

    public function updateAttributeVisibleOnProductPage($meta_value)
    {
        return update_option('linksync_visiable_attr', $meta_value);
    }

}