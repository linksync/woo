<?php

class LS_Product_Helper
{
    protected $product = null;

    public function __construct(WC_Product $product)
    {
        $this->product = $product;
    }

    public function getParendId()
    {
        return self::getProductParentId($this->product);
    }

    public function getStatus()
    {
        return self::getProductStatus($this->product);
    }

    public function getDescription()
    {
        return self::getProductDescription($this->product);
    }

    public function getName()
    {
        return self::getProductName($this->product);
    }

    public function getType()
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $this->product->post->post_type;
        }

        return $this->product->get_type();
    }

    public function isSimple()
    {
        return self::isSimpleProduct($this->product);
    }

    public function isVariable()
    {
        return self::isVariableProduct($this->product);
    }

    public function isVariation()
    {
        return self::isVariationProduct($this->product);
    }

    public function getSku()
    {
        return $this->product->get_sku();
    }

    public static function getProductParentId(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $product->post->post_parent;
        }

        return $product->get_parent_id();
    }

    public static function hasChildren(WC_Product $product)
    {
        return $product->has_child();
    }

    public static function isVariableAndDontHaveChildren(WC_Product $product)
    {
        if (true == self::isVariableProduct($product)) {
            $has_children = $product->has_child();
            if (true == $has_children) {
                return true;
            }
        }

        return false;
    }

    public static function getProductStatus(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $product->post->post_status;
        }

        return $product->get_status();
    }

    public static function getProductDescription(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return remove_escaping_str(html_entity_decode($product->post->post_content));
        }

        return remove_escaping_str(html_entity_decode($product->get_description()));
    }

    public static function getProductName(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return html_entity_decode(remove_escaping_str($product->get_title()));
        }

        return html_entity_decode(remove_escaping_str($product->get_name()));
    }

    public static function isSimpleProduct(WC_Product $product)
    {
        return $product->is_type('simple');
    }

    public static function isVariableProduct(WC_Product $product)
    {
        return $product->is_type('variable');
    }

    public static function isVariationProduct(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            if ($product->post->post_type == 'product_variation') {
                return true;
            }
            return false;
        }

        return $product->is_type('variation');
    }

    public static function getProductParendId(WC_Product $product)
    {
        if (LS_Helper::isWooVersionLessThan_2_4_15()) {
            return $product->post->post_parent;
        }

        return $product->get_parent_id();
    }


}