<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Log_Helper
{
    public static function clearLogsDetails()
    {
        $fileName = dirname(__FILE__) . '/classes/raw-log.txt';
        if (file_exists($fileName)) {
            /**
             * @reference http://stackoverflow.com/questions/5650958/erase-all-data-from-txt-file-php?answertab=active#tab-top
             * @manual http://php.net/manual/en/function.fopen.php
             */
            $handle = fopen($fileName, "w+");
            if ($handle) {
                fclose($handle);
                LSC_Log::add('Daily Cron', 'Success', 'Older 10000 Lines Removed!', '-');
            }
        }
    }

    public static function log_users_activity_time()
    {

        $orignal_time = time();
        $save_time = get_option('linksync_user_activity') + 3600;
        if (isset($save_time) && !empty($save_time) && isset($orignal_time) && !empty($orignal_time)) {
            if ($orignal_time >= $save_time) {
                update_option('linksync_user_activity', $orignal_time);
            }
        }
        $daily = get_option('linksync_user_activity_daily') + 86400;
        if (isset($daily) && !empty($daily) && isset($orignal_time) && !empty($orignal_time)) {
            if ($orignal_time >= $daily) {
                LS_Vend_Log_Helper::clearLogsDetails();
                update_option('linksync_user_activity_daily', $orignal_time);
            }
        }

    }
}