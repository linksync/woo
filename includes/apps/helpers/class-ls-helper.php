<?php

class LS_Helper
{
    public static function isWooVersionLessThan_2_4_15()
    {
        if (version_compare(WC()->version, '2.6.15', '<')) {
            return true;
        }

        return false;
    }
}