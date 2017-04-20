<?php if ( ! defined( 'ABSPATH' ) ) exit;



class LS_ApiController{

    /**
     * Get the api Object
     * @param null
     * @return Returns the api Object
     */

    public static $api = null;

    public static function get_api(){
        /**
         * Get api config
         */
        if( is_null( self::$api) ){
            $apiConfig = self::get_config();
            $laid_key = self::get_current_laid();

            self::$api = new LS_Api( $apiConfig, $laid_key );
        }


        return self::$api;
    }

    /**
     * Save or Update Webhook information in LWS
     * @param $web_hook_data
     * @return array
     */
    public static function update_webhook_connection( $web_hook_data = null ){

        $url = linksync::getWebHookUrl();
        $webhookURL = isset($web_hook_data['url']) ? $web_hook_data['url'] : $url;

        $web_hook_data = array(
            "url" 				=>	$webhookURL,
            "version"			=>	linksync::$version,
            "order_import"		=>	isset($web_hook_data['order_import'])? $web_hook_data['order_import'] : 'yes',
            "product_import"	=>	isset($web_hook_data['product_import'])? $web_hook_data['product_import'] : 'yes'
        );

        return self::get_api()->post( 'laid', json_encode($web_hook_data) );
    }

    /**
     * Get Key information
     * @param string $laid_key Linksync API Key http://developer.linksync.com/linksync-api-key-laid
     *
     * @return array LAID key information
     */
    public static function get_key_info($laid_key = null){
        if( empty($laid_key)){
            $laid_key = self::get_current_laid();
        }
        /**
         * Require api Config
         */
        $apiConfig = self::get_config();
        $api = new LS_Api( $apiConfig, $laid_key );

        return $api->get('laid');

    }

    /**
     * Update the current Laid key connection if its connected or invalid
     *
     * @param null|string $laid LAID key or the Api key used in connecting to Linksync Server
     * @return array|null|string
     */
    public static function check_api_key($laid = null){
        //if laid is null then get the current laid key connection to check its validity
        $laid_key = (null == $laid) ? self::get_current_laid() : $laid;

        if (empty($laid_key)) {
            return 'Empty Api key';
        }

        $current_laid_key_info = self::get_key_info($laid_key);

        if (!empty($current_laid_key_info['errorCode'])) {

            $laid_key_options = array(
                'linksync_laid' => $laid_key,
                'linksync_status' => 'Inactive',
                'linksync_frequency' => $current_laid_key_info['userMessage']
            );

            linksync::update_laid_key_options($laid_key_options);

            $laid_key_options['errorCode'] = $current_laid_key_info['errorCode'];
            $laid_key_options['lws_laid_key_info'] = $current_laid_key_info;

            return $laid_key_options;

        } else {
            $connected_to = self::get_connected_app($current_laid_key_info['connected_app']);
            $connected_with = self::get_connected_app($current_laid_key_info['app']);
            $laid_connection = array();

            if ('QuickBooks Online' == $connected_to || 'QuickBooks Online' == $connected_with) {
                $laid_connection['connected_to'] = 'QuickBooks Online';
                linksync::update_laid_key_options(array(
                    'linksync_laid' => $laid_key,
                    'linksync_status' => 'Active',
                    'linksync_frequency' => !empty($current_laid_key_info['message']) ? $current_laid_key_info['message'] : '',
                    'linksync_connectedto' => $connected_to,
                    'linksync_connectionwith' => $connected_with
                ));

                LS_ApiController::update_webhook_connection();

            } else if ('Vend' == $connected_to || 'Vend' == $connected_with) {
                $laid_connection['connected_to'] = 'Vend';
                linksync::checkForConnection($laid_key);

            }

            return $laid_connection;
        }

        return null;

    }

    /**
     * Get connected Application
     * @param int get the connected application name based on its id
     * @return string
     */
    public static function get_connected_app($app){
        $apps = self::get_apps();

        if(is_numeric($app) && array_key_exists($app, $apps)) {
            return $apps[$app];
        }

        return false;

    }

    /**
     * @return array of possible app connection
     */
    public static function get_apps(){
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
     * Get Linksync config
     */
    public static function get_config(){
        /**
         * Get the configuration or the selection of api config.
         */
        $config = require(LS_PLUGIN_DIR.'ls-config.php');

        /**
         * Check if test mode is set to true
         */
        if($config['testmode']){
            $config['api'] = 'test';
            update_option('linksync_test','on');
        }else{
            update_option('linksync_test','off');
        }

        /**
         * Require api information
         */
        $apiConfig = require(LS_INC_DIR.'api/ls-api-info.php');

        return $apiConfig[$config['api']];
    }

    /**
     * Return the current laid key
     * @return mixed|void
     */
    public static function get_current_laid($default = '')
    {
        return get_option('linksync_laid', $default);
    }

    /**
     * Update current laid key
     * @param $laid
     * @return bool
     */
    public static function update_current_laid($laid)
    {
        return update_option('linksync_laid', trim($laid));
    }

    public static function get_current_laid_info($default = '')
    {
        return get_option('linksync_laid_info', $default);
    }

    public static function update_current_laid_info($laid_info)
    {
        return update_option('linksync_laid_info', $laid_info);
    }


    /**
     * Return the previous laid key being used
     * @param string $default
     * @return mixed|void
     */
    public static function get_previous_laid($default = '')
    {
        return get_option('linksync_previous_laid', $default);
    }


    /**
     * Update previouse laid key being used
     * @param $laid
     * @return bool
     */
    public static function update_previous_laid($laid)
    {
        return update_option('linksync_previous_laid', trim($laid));
    }


    public static function is_new($laid)
    {
        $current_laid = self::get_current_laid();

        if($laid != $current_laid){
            return true;
        }

        return false;
    }

    public static function update_laid($laid)
    {

        $return = array();

        $is_new = self::is_new($laid);

        $return['is_new'] = $is_new;
        if($is_new){
            $current_laid = self::get_current_laid();

            self::update_current_laid($laid);
            $return['current_laid'] = $laid;

            self::update_previous_laid($current_laid);
            $return['previous_laid'] = $current_laid;
        }

        return $return;
    }




}