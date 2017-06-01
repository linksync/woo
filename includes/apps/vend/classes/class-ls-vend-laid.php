<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Laid
{
    public function get_api($apiKey = null)
    {
        $apiConfig = $this->get_config();
        if (null == $apiKey) {
            $apiKey = $this->get_current_laid();
        }
        return new LS_Api($apiConfig, $apiKey);
    }

    public function get_config()
    {
        /**
         * Get the configuration or the selection of api config.
         */
        $config = require(LS_PLUGIN_DIR . 'ls-api-config.php');

        /**
         * Check if test mode is set to true
         */
        if ($config['testmode']) {
            $config['api'] = 'test';
            update_option('linksync_test', 'on');
        } else {
            update_option('linksync_test', 'off');
        }

        /**
         * Require api information
         */
        $apiConfig = require(LS_INC_DIR . 'api/ls-api-info.php');

        return $apiConfig[$config['api']];
    }

    public function get_current_laid($default = '')
    {
        $current_laid = get_option('linksync_laid', $default);
        if(!empty($current_laid)){
            update_option('linksync_vend_laid', $current_laid);
        }
        return $current_laid;
    }

    public function update_current_laid($value)
    {
        update_option('linksync_laid', $value);
        return update_option('linksync_vend_laid', $value);
    }

    public function get_laid_info($laidKey = null)
    {
        if( empty($laid_key)){
            $laid_key = $this->get_current_laid();
        }
        /**
         * Require api Config
         */
        $apiConfig = $this->get_config();
        $api = new LS_Api( $apiConfig, $laid_key );

        return $api->get('laid');
    }

    /**
     * @return array of possible app connection
     */
    public function get_apps()
    {
        return array(
            '4' => 'Xero',
            '7' => 'MYOB RetailManager',
            '8' => 'Saasu',
            '13' => 'WooCommerce',
            '15' => 'QuickBooks Online',
            '18' => 'Vend'
        );
    }

    /**
     * Get connected Application
     * @param int       Get the connected application name based on its id
     * @return string
     */
    public function get_connected_app($app)
    {
        $apps = $this->get_apps();

        if(is_numeric($app) && array_key_exists($app, $apps)) {
            return $apps[$app];
        }

        return false;
    }

    public function update_webhook_connection($webHookData = null)
    {
        $url = linksync::getWebHookUrl();
        $webhookURL = isset($web_hook_data['url']) ? $web_hook_data['url'] : $url;

        $laid = null;
        if (!empty($web_hook_data['laid_key'])) {
            $laid = $web_hook_data['laid_key'];
        }
        $web_hook_data = array(
            "url" => $webhookURL,
            "version" => linksync::$version,
            "order_import" => isset($web_hook_data['order_import']) ? $web_hook_data['order_import'] : 'yes',
            "product_import" => isset($web_hook_data['product_import']) ? $web_hook_data['product_import'] : 'yes'
        );
        return $this->get_api($laid)->post('laid', json_encode($web_hook_data));
    }
}