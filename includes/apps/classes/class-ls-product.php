<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Product
{

    /**
     * JSON representation of the product
     * @var null
     */
    public $product;

    public $product_variants = null;

    public function __construct($product = null)
    {
        if (!empty($product)) {

            if (!is_array($product)) {
                $product = trim($product);
                $this->product = json_decode($product, true);
            } else {
                $this->product = $product;
            }

        }
    }


    public function getData($key)
    {
        return isset($this->product[$key]) ? $this->product[$key] : null;
    }

    public function set_data($key, $value)
    {
        $this->product[$key] = $value;
    }

    public function unset_data($key)
    {
        if (isset($this->product[$key])) {
            unset($this->product[$key]);
        }
    }

    public function get_purchasing_information()
    {
        return $this->getData('purchase_description');
    }

    public function set_purchase_description($value)
    {
        $this->set_data('purchase_description', $value);
    }

    public function get_id()
    {
        return $this->getData('id');
    }

    public function set_id($value)
    {
        $this->set_data('id', $value);
    }

    public function remove_id()
    {
        $this->unset_data('id');
    }

    public function get_name()
    {
        return $this->getData('name');
    }

    public function set_name($value)
    {
        $this->set_data('name', $value);
    }
    public function remove_name()
    {
        $this->unset_data('name');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function set_tittle($value)
    {
        $this->set_data('title', $value);
    }

    public function get_description()
    {
        return $this->getData('description');
    }

    public function set_description($value)
    {
        $this->set_data('description', $value);
    }

    public function remove_description()
    {
        $this->unset_data('description');
    }

    public function get_sku()
    {
        return $this->getData('sku');
    }

    public function set_sku($value)
    {
        $this->set_data('sku', $value);
    }

    public function is_active()
    {
        return $this->getData('active');
    }

    public function set_active($value)
    {
        $this->set_data('active', $value);
    }

    public function getPrice()
    {
        return $this->getData('price');
    }

    public function set_price($value)
    {
        $this->set_data('price', $value);
    }

    public function get_cost_price()
    {
        return $this->getData('cost_price');
    }

    public function remove_cost_price()
    {
        $this->unset_data('cost_price');
    }

    public function set_cost_price($value)
    {
        $this->set_data('cost_price', $value);
    }

    public function get_list_price()
    {
        return $this->getData('list_price');
    }

    public function set_list_price($value)
    {
        $this->set_data('list_price', $value);
    }

    public function remove_list_price()
    {
        $this->unset_data('list_price');
    }

    public function get_sell_price()
    {
        return $this->getData('sell_price');
    }

    public function set_sell_price($value)
    {
        $this->set_data('sell_price', $value);
    }

    public function remove_sell_price()
    {
        $this->unset_data('sell_price');
    }

    public function get_taxable()
    {
        return $this->getData('taxable');
    }

    public function set_taxable($value)
    {
        $this->set_data('taxable', $value);
    }

    public function get_tax_value()
    {
        return $this->getData('tax_value');
    }

    public function set_tax_value($value)
    {
        $this->set_data('tax_value', $value);
    }

    public function getTaxValue()
    {
        return $this->getData('taxValue');
    }

    public function set_taxValue($value)
    {
        $this->set_data('taxValue', $value);
    }

    public function get_tax_name()
    {
        return $this->getData('tax_name');
    }

    public function set_tax_name($value)
    {
        $this->set_data('tax_name', $value);
    }

    public function get_tax_rate()
    {
        return $this->getData('tax_rate');
    }

    public function set_tax_rate($value)
    {
        $this->set_data('tax_rate', $value);
    }

    public function get_tax_id()
    {
        return $this->getData('tax_id');
    }

    public function set_tax_id($value)
    {
        $this->set_data('tax_id', $value);
    }

    public function getTaxId()
    {
        return $this->getData('taxId');
    }

    public function set_taxId($value)
    {
        $this->set_data('taxId', $value);
    }


    public function does_includes_tax()
    {
        return $this->getData('includes_tax');
    }

    public function set_includes_tax($value)
    {
        $this->set_data('includes_tax', $value);
    }

    public function get_quantity()
    {
        return $this->getData('quantity');
    }

    public function remove_quantity()
    {
        $this->unset_data('quantity');
    }

    public function set_quantity($value)
    {
        $this->set_data('quantity', $value);
    }

    public function get_product_type()
    {
        return $this->getData('product_type');
    }

    public function set_product_type($value)
    {
        $this->set_data('product_type', $value);
    }

    public function get_income_account_id()
    {
        return $this->getData('income_account_id');
    }

    public function set_income_account_id($value)
    {
        $this->set_data('income_account_id', $value);
    }

    public function get_expense_account_id()
    {
        return $this->getData('expense_account_id');
    }

    public function set_expense_account_id($value)
    {
        $this->set_data('expense_account_id', $value);
    }

    public function get_asset_account_id()
    {
        return $this->getData('asset_account_id');
    }

    public function set_asset_account_id($value)
    {
        $this->set_data('asset_account_id', $value);
    }

    public function get_update_at()
    {
        return $this->getData('update_at');
    }

    public function get_deleted_at()
    {
        return $this->getData('deleted_at');
    }

    public function hasImages()
    {
        $images = $this->get_images();
        return empty($images) ? false : true;
    }

    public function get_images()
    {
        return $this->getData('images');
    }

    public function getFirstImages()
    {
        $images = $this->get_images();
        if (!empty($images[0]['url'])) {
            return $images[0];
        }

        return $images;
    }

    public function get_last_image()
    {
        $images = $this->get_images();
        $image_count = count($images);
        $last_image_index = $image_count - 1;

        if (!empty($images[$last_image_index]['url'])) {
            return $images[0];
        }

        return $images;
    }

    public function getSecondToTheLastImages()
    {
        $images = $this->get_images();
        if (!empty($images[0]['url'])) {
            unset($images[0]);
        }

        return $images;
    }

    public function get_first_to_second_last_image()
    {
        $images = $this->get_images();
        $image_count = count($images);
        $last_image_index = $image_count - 1;

        if (!empty($images[$last_image_index]['url'])) {
            unset($images[$last_image_index]);
        }

        return $images;
    }

    public function has_variant()
    {
        return count($this->getData('variants')) > 0 ? true : false;
    }

    public function get_variants()
    {
        return $this->getData('variants');
    }

    public function set_variants($value)
    {
        $this->set_data('variants', $value);
    }


    public function getAllVariantSku()
    {
        $allVariantSku = array();
        $variants = $this->get_variants();
        if (!empty($variants)) {
            foreach ($variants as $variant) {
                $allVariantSku[] = $variant['sku'];
            }
        }

        return $allVariantSku;
    }

    public function get_tags()
    {
        return $this->getData('tags');
    }

    public function set_tags($value)
    {
        $this->set_data('tags', $value);
    }

    public function has_outlets()
    {
        return count($this->get_outlets()) > 0 ? true : false;
    }

    public function get_outlets()
    {
        return $this->getData('outlets');
    }

    public function set_outlets($value)
    {
        $this->set_data('outlets', $value);
    }

    public function get_price_books()
    {
        return $this->getData('price_books');
    }

    public function set_price_books($value)
    {
        $this->set_data('price_books', $value);
    }

    public function get_brands()
    {
        return $this->getData('brands');
    }

    public function set_brands($value)
    {
        $this->set_data('brands', $value);
    }

    public function get_categories()
    {
        return $this->getData('category');
    }

    public function set_category($value)
    {
        $this->set_data('category', $value);
    }

    public function getTotalVariantsQuantity()
    {
        $quantity = 0;

        $variants = $this->get_variants();
        if (!empty($variants)) {
            /**
             *Loop through the available variants to get the quantity
             */
            foreach ($variants as $variant) {
                if (isset($variant['outlets'])) {
                    /**
                     * Loop through to get the quantity on each available outlet
                     */
                    foreach ($variant['outlets'] as $outlet) {
                        //add the outlets quantity
                        $quantity += $outlet['quantity'];
                    }
                }
            }

        }
        return $quantity;
    }

    public function get_product_array()
    {
        return $this->product;
    }

    public function get_product_json()
    {
        if(empty($this->product)){
            return null;
        }

        return json_encode($this->get_product_array());
    }

    public function getJsonProduct()
    {
        return json_encode($this->product);
    }

}