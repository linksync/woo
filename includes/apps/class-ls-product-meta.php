<?php if (!defined('ABSPATH')) exit('Access is Denied');

/**
 * Class LS_Product_Meta
 *
 *  show off @property, @property-read, @property-write
 *
 * @property string _visibility                 visible
 * @property mixed _stock_status
 * @property int total_sales
 * @property string _downloadable               yes or no
 * @property string _virtual                    yes or no
 * @property string _purchase_note
 * @property string _featured
 * @property mixed _weight
 * @property mixed _length
 * @property mixed _width
 * @property mixed _height
 * @property string _sku
 * @property mixed _product_attributes
 * @property mixed _regular_price
 * @property mixed _sale_price
 * @property mixed _sale_price_dates_from
 * @property mixed _sale_price_dates_to
 * @property mixed _price
 * @property mixed _sold_individually
 * @property mixed _manage_stock
 * @property mixed _backorders
 * @property mixed _stock
 * @property mixed _upsell_ids
 * @property mixed _crosssell_ids
 * @property mixed _downloadable_files
 * @property mixed _download_limit
 * @property mixed _download_expiry
 * @property mixed _tax_status
 * @property mixed _tax_class
 *
 * Variants property
 * @property mixed _variation_description
 */
class LS_Product_Meta
{

    public $product_id = null;

    protected $metas = array();

    /**
     * LS_Product_Meta constructor.
     *
     * @param $product
     */
    public function __construct($product)
    {

        if ($product instanceof WC_Product) {
            $this->product_id = $product->id;
        } else if (is_numeric($product)) {
            $this->product_id = $product;
        }

    }

    /**
     * Returns the defaul meta of a woocomerce product
     * @return array
     */
    public function default_metas()
    {

        return array(
            '_visibility' => 'visible',
            '_stock_status' => 'instock',
            '_downloadable' => 'no',
            '_virtual' => 'no',
            '_purchase_note' => '',
            '_featured' => 'no',
            '_weight' => '',
            '_length' => '',
            '_width' => '',
            '_height' => '',
            '_regular_price' => '',
            '_sale_price' => '',
            '_sale_price_dates_from' => '',
            '_sale_price_dates_to' => '',
            '_price' => '',
            '_sold_individually' => '',
            '_manage_stock' => 'no',
            '_backorders' => 'no',
            '_stock' => '',
            '_product_attributes' => array()
        );
    }


    /**
     * Set up the default metas of a product
     */
    public function setup_defaults()
    {
        if (isset($this->product_id) && is_int($this->product_id)) {

            $default_metas = $this->default_metas();

            $this->add_meta('total_sales', '0', true);
            foreach ($default_metas as $key => $value) {
                $this->update_meta($key, $value);
            }

        }
    }

    public function get_cost_price()
    {
        return $this->get_meta('_ls_cost_price');
    }

    public function set_cost_price($meta_value)
    {
        $this->metas['_ls_cost_price'] = $meta_value;
    }

    public function update_cost_price($meta_value)
    {
        return $this->update_meta('_ls_cost_price', $meta_value);
    }

    public function get_sell_price()
    {
        return $this->get_meta('_ls_sell_price');
    }

    public function set_sell_price($meta_value)
    {
        $this->metas['_ls_sell_price'] = $meta_value;
    }

    public function update_sell_price($meta_value)
    {
        return $this->update_meta('_ls_sell_price', $meta_value);
    }

    public function get_list_price()
    {
        return $this->get_meta('_ls_list_price');
    }

    public function set_list_price($meta_value)
    {
        $this->metas['_ls_list_price'] = $meta_value;
    }

    public function update_list_price($meta_value)
    {
        return $this->update_meta('_ls_list_price', $meta_value);
    }


    public function set_product_id($meta_value)
    {
        $this->metas['_ls_pid'] = $meta_value;
    }

    public function get_product_id()
    {
        return $this->get_meta('_ls_pid');
    }

    public function update_product_id($meta_value)
    {
        return $this->update_meta('_ls_pid', $meta_value);
    }

    public function set_tax_value($meta_value)
    {
        $this->metas['_ls_tax_value'] = $meta_value;
    }

    public function get_tax_value()
    {
        return $this->get_meta('_ls_tax_value');
    }

    public function update_tax_value($meta_value)
    {
        return $this->update_meta('_ls_tax_value', $meta_value);
    }

    public function set_tax_name($meta_value)
    {
        $this->metas['_ls_tax_name'] = $meta_value;
    }

    public function get_tax_name()
    {
        return $this->get_meta('_ls_tax_name');
    }

    public function update_tax_name($meta_value)
    {
        return $this->update_meta('_ls_tax_name', $meta_value);
    }

    public function set_tax_rate($meta_value)
    {
        $this->metas['_ls_tax_rate'] = $meta_value;
    }

    public function get_tax_rate()
    {
        return $this->get_meta('_ls_tax_rate');
    }

    public function update_tax_rate($meta_value)
    {
        return $this->update_meta('_ls_tax_rate', $meta_value);
    }

    public function set_tax_id($meta_value)
    {
        $this->metas['_ls_tax_id'] = $meta_value;
    }

    public function get_tax_id()
    {
        return $this->get_meta('_ls_tax_id');
    }

    public function update_tax_id($meta_value)
    {
        return $this->update_meta('_ls_tax_id', $meta_value);
    }


    public function set_product_type($meta_value)
    {
        $this->metas['_ls_ptype'] = $meta_value;
    }

    public function get_product_type()
    {
        return $this->get_meta('_ls_ptype');
    }

    public function update_product_type($meta_value)
    {
        return $this->update_meta('_ls_ptype', $meta_value);
    }

    public function set_income_account_id($meta_value)
    {
        $this->metas['_ls_qbo_inc_act_id'] = $meta_value;
    }

    public function get_income_account_id()
    {
        return $this->get_meta('_ls_qbo_inc_act_id');
    }

    public function update_income_account_id($meta_value)
    {
        return $this->update_meta('_ls_qbo_inc_act_id', $meta_value);
    }

    public function set_expense_account_id($meta_value)
    {
        $this->metas['_ls_qbo_exp_act_id'] = $meta_value;
    }

    public function get_expense_account_id()
    {
        return $this->get_meta('_ls_qbo_exp_act_id');
    }

    public function update_expense_account_id($meta_value)
    {
        return $this->update_meta('_ls_qbo_exp_act_id', $meta_value);
    }

    public function set_asset_account_id($meta_value)
    {
        $this->metas['_ls_qbo_ass_act_id'] = $meta_value;
    }

    public function get_asset_account_id()
    {
        return $this->get_meta('_ls_qbo_ass_act_id');
    }

    public function update_asset_account_id($meta_value)
    {
        return $this->update_meta('_ls_qbo_ass_act_id', $meta_value);
    }

    public function set_qbo_includes_tax($meta_value)
    {
        $this->metas['_ls_qbo_includes_tax'] = $meta_value;
    }

    public function get_qbo_includes_tax()
    {
        return $this->get_meta('_ls_qbo_includes_tax');
    }

    public function update_qbo_includes_tax($meta_value)
    {
        return $this->update_meta('_ls_qbo_includes_tax', $meta_value);
    }


    public function set_visibility($meta_value)
    {
        $this->metas['_visibility'] = $meta_value;
    }

    public function get_visibility()
    {
        return $this->get_meta('_visibility');
    }

    public function update_visibility($meta_value)
    {
        return $this->update_meta('_visibility', $meta_value);
    }

    public function set_stock_status($meta_value)
    {
        $this->metas['_stock_status'] = $meta_value;
    }

    public function get_stock_status()
    {
        return $this->get_meta('_stock_status');
    }

    public function update_stock_status($meta_value)
    {
        return $this->update_meta('_stock_status', $meta_value);
    }

    public function set_total_sales($meta_value)
    {
        $this->metas['total_sales'] = $meta_value;
    }

    public function get_total_sales()
    {
        return $this->get_meta('total_sales');
    }

    public function update_total_sales($meta_value)
    {
        return $this->update_meta('total_sales', $meta_value);
    }

    public function set_downloadable($meta_value)
    {
        $this->metas['_downloadable'] = $meta_value;
    }

    public function get_downloadable()
    {
        return $this->get_meta('_downloadable');
    }

    public function update_downloadable($meta_value)
    {
        return $this->update_meta('_downloadable', $meta_value);
    }

    public function set_virtual($meta_value)
    {
        $this->metas['_virtual'] = $meta_value;
    }

    public function get_virtual()
    {
        return $this->get_meta('_virtual');
    }

    public function update_virtual($meta_value)
    {
        return $this->update_meta('_virtual', $meta_value);
    }

    public function set_purchase_note($meta_value)
    {
        $this->metas['_purchase_note'] = $meta_value;
    }

    public function get_purchase_note()
    {
        return $this->get_meta('_purchase_note');
    }

    public function update_purchase_note($meta_value)
    {
        return $this->update_meta('_purchase_note', $meta_value);
    }

    public function set_featured($meta_value)
    {
        $this->metas['_featured'] = $meta_value;
    }

    public function get_featured()
    {
        return $this->get_meta('_featured');
    }

    public function update_featured($meta_value)
    {
        return $this->update_meta('_featured', $meta_value);
    }

    public function set_weight($meta_value)
    {
        $this->metas['_weight'] = $meta_value;
    }

    public function get_weight()
    {
        return $this->get_meta('_weight');
    }

    public function update_weight($meta_value)
    {
        return $this->update_meta('_weight', $meta_value);
    }

    public function set_length($meta_value)
    {
        $this->metas['_length'] = $meta_value;
    }

    public function get_length()
    {
        return $this->get_meta('_length');
    }

    public function update_length($meta_value)
    {
        return $this->update_meta('_length', $meta_value);
    }

    public function set_width($meta_value)
    {
        $this->metas['_width'] = $meta_value;
    }

    public function get_width()
    {
        return $this->get_meta('_width');
    }

    public function update_width($meta_value)
    {
        return $this->update_meta('_width', $meta_value);
    }

    public function set_height($meta_value)
    {
        $this->metas['_height'] = $meta_value;
    }

    public function get_height()
    {
        return $this->get_meta('_height');
    }

    public function update_height($meta_value)
    {
        return $this->update_meta('_height', $meta_value);
    }

    public function set_sku($meta_value)
    {
        $this->metas['_sku'] = $meta_value;
    }

    public function get_sku()
    {
        return $this->get_meta('_sku');
    }

    public function update_sku($meta_value)
    {
        return $this->update_meta('_sku', $meta_value);
    }


    public function set_product_attributes($meta_value)
    {
        $this->metas['_product_attributes'] = $meta_value;
    }

    public function get_product_attributes()
    {
        return $this->get_meta('_product_attributes');
    }

    public function update_product_attributes($meta_value)
    {
        return $this->update_meta('_product_attributes', $meta_value);
    }

    public function set_regular_price($meta_value)
    {
        $this->metas['_regular_price'] = $meta_value;
    }

    public function get_regular_price()
    {
        return $this->get_meta('_regular_price');
    }

    public function update_regular_price($meta_value)
    {
        return $this->update_meta('_regular_price', $meta_value);
    }

    public function set_sale_price($meta_value)
    {
        $this->metas['_sale_price'] = $meta_value;
    }

    public function get_sale_price()
    {
        return $this->get_meta('_sale_price');
    }

    public function update_sale_price($meta_value)
    {
        return $this->update_meta('_sale_price', $meta_value);
    }

    public function set_sale_price_dates_from($meta_value)
    {
        $this->metas['_sale_price_dates_from'] = $meta_value;
    }

    public function get_sale_price_dates_from()
    {
        return $this->get_meta('_sale_price_dates_from');
    }

    public function update_sale_price_dates_from($meta_value)
    {
        return $this->update_meta('_sale_price_dates_from', $meta_value);
    }

    public function set_sale_price_dates_to($meta_value)
    {
        $this->metas['_sale_price_dates_to'] = $meta_value;
    }

    public function get_sale_price_dates_to()
    {
        return $this->get_meta('_sale_price_dates_to');
    }

    public function update_sale_price_dates_to($meta_value)
    {
        return $this->update_meta('_sale_price_dates_to', $meta_value);
    }

    public function set_price($meta_value)
    {
        $this->metas['_price'] = $meta_value;
    }

    public function get_price()
    {
        return $this->get_meta('_price');
    }

    public function update_price($meta_value)
    {
        return $this->update_meta('_price', $meta_value);
    }

    public function set_sold_individually($meta_value)
    {
        $this->metas['_sold_individually'] = $meta_value;
    }

    public function get_sold_individually()
    {
        return $this->get_meta('_sold_individually');
    }

    public function update_sold_individually($meta_value)
    {
        return $this->update_meta('_sold_individually', $meta_value);
    }

    public function set_manage_stock($meta_value)
    {
        $this->metas['_manage_stock'] = $meta_value;
    }

    public function get_manage_stock()
    {
        return $this->get_meta('_manage_stock');
    }

    public function update_manage_stock($meta_value)
    {
        return $this->update_meta('_manage_stock', $meta_value);
    }

    public function set_backorders($meta_value)
    {
        $this->metas['_backorders'] = $meta_value;
    }

    public function get_backorders()
    {
        return $this->get_meta('_backorders');
    }

    public function update_backorders($meta_value)
    {
        return $this->update_meta('_backorders', $meta_value);
    }

    public function set_stock($meta_value)
    {
        $this->metas['_stock'] = $meta_value;
    }

    public function get_stock()
    {
        return $this->get_meta('_stock');
    }

    public function update_stock($meta_value)
    {
        return $this->update_meta('_stock', $meta_value);
    }

    public function set_upsell_ids($meta_value)
    {
        $this->metas['_upsell_ids'] = $meta_value;
    }

    public function get_upsell_ids()
    {
        return $this->get_meta('_upsell_ids');
    }

    public function update_upsell_ids($meta_value)
    {
        return $this->update_meta('_upsell_ids', $meta_value);
    }

    public function set_crosssell_ids($meta_value)
    {
        $this->metas['_crosssell_ids'] = $meta_value;
    }

    public function get_crosssell_ids()
    {
        return $this->get_meta('_crosssell_ids');
    }

    public function update_crosssell_ids($meta_value)
    {
        return $this->update_meta('_crosssell_ids', $meta_value);
    }

    public function set_downloadable_files($meta_value)
    {
        $this->metas['_downloadable_files'] = $meta_value;
    }

    public function get_downloadable_files()
    {
        return $this->get_meta('_downloadable_files');
    }

    public function update_downloadable_files($meta_value)
    {
        return $this->update_meta('_downloadable_files', $meta_value);
    }

    public function set_download_limit($meta_value)
    {
        $this->metas['_download_limit'] = $meta_value;
    }

    public function get_download_limit()
    {
        return $this->get_meta('_download_limit');
    }

    public function update_download_limit($meta_value)
    {
        return $this->update_meta('_download_limit', $meta_value);
    }

    public function set_download_expiry($meta_value)
    {
        $this->metas['_download_expiry'] = $meta_value;
    }

    public function get_download_expiry()
    {
        return $this->get_meta('_download_expiry');
    }

    public function update_download_expiry($meta_value)
    {
        return $this->update_meta('_download_expiry', $meta_value);
    }

    public function set_variation_description($meta_value)
    {
        $this->metas['_variation_description'] = $meta_value;
    }

    public function get_variation_description()
    {
        return $this->get_meta('_variation_description');
    }

    public function update_variation_description($meta_value)
    {
        return $this->update_meta('_variation_description', $meta_value);
    }

    public function set_taxable($meta_value)
    {
        $this->metas['_ls_qbo_taxable'] = $meta_value;
    }

    public function get_taxable()
    {
        return $this->get_meta('_ls_qbo_taxable');
    }

    public function update_taxable($meta_value)
    {
        return $this->update_meta('_ls_qbo_taxable', $meta_value);
    }

    public function set_tax_status($meta_value)
    {
        $this->metas['_tax_status'] = $meta_value;
    }

    public function get_tax_status()
    {
        return $this->get_meta('_tax_status');
    }

    public function update_tax_status($meta_value)
    {
        return $this->update_meta('_tax_status', $meta_value);
    }

    public function set_tax_class($meta_value)
    {
        $this->metas['_tax_class'] = $meta_value;
    }

    public function get_tax_class()
    {
        return $this->get_meta('_tax_class');
    }

    public function update_tax_class($meta_value)
    {
        return $this->update_meta('_tax_class', $meta_value);
    }

    public function update_woo_product_description($product_description)
    {
        return $this->update_meta('_ls_product_description', $product_description);
    }

    public function get_woo_product_description()
    {
        return $this->get_meta('_ls_product_description');
    }


    public function get_metas()
    {
        return $this->metas;
    }

    public function set($meta_key, $meta_value)
    {
        $this->metas[$meta_key] = $meta_value;
    }

    public function update_metas()
    {
        if (!empty($this->metas) && !empty($this->product_id)) {
            $metas = $this->metas;

            foreach ($metas as $meta_key => $meta_value) {
                $this->update_meta($meta_key, $meta_value);
            }
        }
    }

    /**
     * Get the meta value in a single product
     *
     * @param $name
     * @return mixed
     */
    public function __get($meta_key)
    {
        return $this->get_meta($meta_key);
    }

    /**
     * Update product meta
     *
     * @param $name
     * @param $value
     */
    public function __set($meta_key, $meta_value)
    {
        $this->update_meta($meta_key, $meta_value);
    }

    /**
     * @param $meta_key
     * @param $meta_value
     * @param bool $unique
     * @return false|int
     */
    public function add_meta($meta_key, $meta_value, $unique = false)
    {
        return add_post_meta($this->product_id, $meta_key, $meta_value, $unique);
    }

    /**
     * @param $meta_key
     * @param $meta_value
     * @return bool|int
     */
    public function update_meta($meta_key, $meta_value)
    {
        return update_post_meta($this->product_id, $meta_key, $meta_value);
    }

    /**
     * @param $meta_key
     * @return mixed
     */
    public function get_meta($meta_key)
    {
        return get_post_meta($this->product_id, $meta_key, true);
    }
}