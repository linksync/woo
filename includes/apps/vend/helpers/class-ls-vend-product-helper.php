<?php

class LS_Vend_Product_Helper
{

    public static function isTypeSyncAbleToVend($type)
    {
        $bool = false;
        $product_types = array('simple', 'variable');

        if (in_array($type, $product_types)) {
            $bool = true;
        }
        return $bool;
    }


}