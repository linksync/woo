<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Simple_Product
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
                $this->product = json_decode($product, true);
            } else {
                $this->product = $product;
            }

            if ($this->has_variant()) {

                foreach ($this->variants as $variant) {
                    array_push($this->product_variants, new LS_Variant_Product($this->get_sku(), $variant));
                }

            }
        }
    }

    /**
     * @param $name
     * @return null|mixed
     */
    public function __get($key)
    {
        // TODO: Implement __get() method.
        return isset($this->product[$key]) ? $this->product[$key] : null;
    }

    public function get_purchasing_information()
    {
        return $this->purchase_description;
    }

    public function has_variant()
    {
        return count($this->variants) > 0 ? true : false;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_sku()
    {
        return $this->sku;
    }

    public function is_active()
    {
        return $this->active;
    }

    public function get_cost_price()
    {
        return $this->cost_price;
    }

    public function get_list_price()
    {
        return $this->list_price;
    }

    public function get_sell_price()
    {
        return $this->sell_price;
    }

    public function get_taxable()
    {
        return $this->taxable;
    }

    public function get_tax_value()
    {
        return $this->tax_value;
    }

    public function get_tax_name()
    {
        return $this->tax_name;
    }

    public function get_tax_rate()
    {
        return $this->tax_rate;
    }

    public function get_tax_id()
    {
        return $this->tax_id;
    }

    public function does_includes_tax()
    {
        return $this->includes_tax;
    }

    public function get_quantity()
    {
        return $this->quantity;
    }

    public function get_product_type()
    {
        return $this->product_type;
    }

    public function get_income_account_id()
    {
        return $this->income_account_id;
    }

    public function get_expense_account_id()
    {
        return $this->expense_account_id;
    }

    public function get_asset_account_id()
    {
        return $this->asset_account_id;
    }

    public function get_update_at()
    {
        return $this->update_at;
    }

    public function get_deleted_at()
    {
        return $this->deleted_at;
    }

    public function get_images()
    {
        return $this->images;
    }

    public function get_variants()
    {
        return $this->product_variants;
    }

    public function get_tags()
    {
        return $this->tags;
    }

    public function get_outlets()
    {
        return $this->outlets;
    }

    public function get_price_books()
    {
        return $this->price_books;
    }

    public function get_brands()
    {
        return $this->brands;
    }

    public function get_categories()
    {
        return $this->category;
    }
}