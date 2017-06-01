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

    public function get_purchasing_information()
    {
        return $this->getData('purchase_description');
    }

    public function has_variant()
    {
        return count($this->getData('variants')) > 0 ? true : false;
    }

    public function has_outlets()
    {
        return count($this->get_outlets()) > 0 ? true : false;
    }

    public function get_id()
    {
        return $this->getData('id');
    }

    public function get_name()
    {
        return $this->getData('name');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function get_description()
    {
        return $this->getData('description');
    }

    public function get_sku()
    {
        return $this->getData('sku');
    }

    public function is_active()
    {
        return $this->getData('active');
    }

    public function getPrice()
    {
        return $this->getData('price');
    }

    public function get_cost_price()
    {
        return $this->getData('cost_price');
    }

    public function get_list_price()
    {
        return $this->getData('list_price');
    }

    public function get_sell_price()
    {
        return $this->getData('sell_price');
    }

    public function get_taxable()
    {
        return $this->getData('taxable');
    }

    public function get_tax_value()
    {
        return $this->getData('tax_value');
    }

    public function getTaxValue()
    {
        return $this->getData('taxValue');
    }

    public function get_tax_name()
    {
        return $this->getData('tax_name');
    }

    public function get_tax_rate()
    {
        return $this->getData('tax_rate');
    }

    public function get_tax_id()
    {
        return $this->getData('tax_id');
    }

    public function getTaxId()
    {
        return $this->getData('taxId');
    }


    public function does_includes_tax()
    {
        return $this->getData('includes_tax');
    }

    public function get_quantity()
    {
        return $this->getData('quantity');
    }

    public function get_product_type()
    {
        return $this->getData('product_type');
    }

    public function get_income_account_id()
    {
        return $this->getData('income_account_id');
    }

    public function get_expense_account_id()
    {
        return $this->getData('expense_account_id');
    }

    public function get_asset_account_id()
    {
        return $this->getData('asset_account_id');
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

    public function get_variants()
    {
        return $this->getData('variants');
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

    public function get_outlets()
    {
        return $this->getData('outlets');
    }

    public function get_price_books()
    {
        return $this->getData('price_books');
    }

    public function get_brands()
    {
        return $this->getData('brands');
    }

    public function get_categories()
    {
        return $this->getData('category');
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

    public function getJsonProduct()
    {
        return json_encode($this->product);
    }

}