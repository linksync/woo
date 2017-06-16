<?php

class LS_Helper
{
    public static function isWooVersionLessThan_2_4_15()
    {
        $wooVersion = LS_Vend()->option()->get_woocommerce_version();
        if (version_compare($wooVersion, '2.6.15', '<')) {
            return true;
        }

        return false;
    }
}