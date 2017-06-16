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

    public function import_by_tag()
    {
        return get_option('ps_imp_by_tag', 'off');
    }

    public function update_import_by_tag($option_value)
    {
        return update_option('ps_imp_by_tag', $option_value);
    }

    public function import_by_tags_list()
    {
        return get_option('import_by_tags_list', '');
    }

    public function update_import_by_tags_list($option_value)
    {
        return update_option('import_by_tags_list', $option_value);
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

    public function productStatusToPending()
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

    public function importImage()
    {
        return get_option('ps_import_image_radio');
    }

    public function updateImportImage($meta_value)
    {
        return update_option('ps_import_image_radio', $meta_value);
    }


    public function createNew()
    {
        return get_option('ps_create_new', 'on');
    }

    public function updateCreateNew($meta_value)
    {
        return update_option('ps_create_new', $meta_value);
    }


    public function excluding_tax()
    {
        return get_option('excluding_tax');
    }

    public function udpate_excluding_tax($value)
    {
        return update_option('excluding_tax', $value);
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

    public function changeProductStatusBaseOnQuantity()
    {
        return get_option('ps_unpublish');
    }

    public function updateChangeProductStatusBaseOnQuantity($value)
    {
        return update_option('ps_unpublish', $value);
    }

    public function selected_radio_for_category()
    {
        return get_option('cat_radio');
    }

    public function get_syncing_options()
    {
        return array(
            'sync_type' => $this->sync_type(),
            'name_or_title' => $this->nameTitle(),
            'description' => $this->description(),
            'short_description' => $this->shortDescription(),
            'price_option_group' => array(
                'price' => $this->price(),
                'price_field' => $this->priceField(),
                'treat_price' => $this->excluding_tax(),
                'use_woocommerce_tax_opton' => $this->linksync_woocommerce_tax_option()
            ),
            'quantity_option_group' => array(
                'quantity' => $this->quantity(),
                'change_product_status' => $this->changeProductStatusBaseOnQuantity()
            ),
            'tags' => $this->tag(),
            'category_option_group' => array(
                'category' => $this->category(),
                'category_selected_radio' => $this->selected_radio_for_category()
            ),
            'product_status' => $this->productStatusToPending(),
            'import_by_tag' => $this->import_by_tag(),
            'image_option_group' => array(
                'image' => $this->image(),
                'image_sync_type' => $this->importImage()
            ),
            'create_new' => $this->createNew(),
            'delete' => $this->delete()
        );
    }

}