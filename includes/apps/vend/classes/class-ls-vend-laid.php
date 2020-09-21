<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Laid
{
    public function get_api($apiKey = null)
    {
        $apiConfig = $this->get_config();
        if (empty($apiKey)) {
            $apiKey = $this->get_current_laid();
        }
        return new LS_Api($apiConfig, $apiKey);
    }

    public function get_config()
    {
        /**
         * Get the configuration or the selection of api config.
         */
        $config = require(LS_PLUGIN_DIR . 'environment.php');

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
        if (!empty($current_laid)) {
            update_option('linksync_vend_laid', $current_laid);
        }
        return $current_laid;
    }

    public function update_current_laid($value)
    {
        update_option('linksync_laid', $value);
        return update_option('linksync_vend_laid', $value);
    }

    public function get_current_laid_info($default = '')
    {
        return get_option('linksync_laid_info', $default);
    }

    public function update_current_laid_info($laid_info)
    {
        return update_option('linksync_laid_info', $laid_info);
    }

    public function get_laid_info($laidKey = null)
    {
        $api = $this->get_api($laidKey);
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

        if (is_numeric($app) && array_key_exists($app, $apps)) {
            return $apps[$app];
        }

        return false;
    }

    public function update_webhook_connection($web_hook_data = null)
    {
        set_time_limit(0);
        $url = Linksync_Vend::getWebHookUrl();
        $webhookURL = isset($web_hook_data['url']) ? $web_hook_data['url'] : $url;

        $laid = null;
        if (!empty($web_hook_data['laid_key'])) {
            $laid = $web_hook_data['laid_key'];
        }

        $checkConnection = '?check=connection';
        if (!empty($web_hook_data['check'])) {
            $checkConnection = '?check=' . $web_hook_data['check'];
        }

        $web_hook_data = array(
            "url" => $webhookURL,
            "version" => Linksync_Vend::$version,
            "order_import" => isset($web_hook_data['order_import']) ? $web_hook_data['order_import'] : 'yes',
            "product_import" => isset($web_hook_data['product_import']) ? $web_hook_data['product_import'] : 'yes'
        );
        $json_data = json_encode($web_hook_data);
        return $this->get_api($laid)->post('laid' . $checkConnection, $json_data);
    }

    public function get_app_id_app($app_id)
    {
        $connected_app = array(
            '4' => 'Xero',
            '7' => 'MYOB RetailManager',
            '8' => 'Saasu',
            '13' => 'WooCommerce',
            '15' => 'QuickBooks Online',
            '18' => 'Vend'
        );
        if (array_key_exists($app_id, $connected_app)) {
            $result['success'] = $connected_app[$app_id];
        } else {
            $result['error'] = 'The supplied API Key is not valid for use with linksync for WooCommerce.';
        }
        return $result;
    }

    public function update_laid_key_options(array $options)
    {
        $apiConfig = $this->get_config();

        if (!empty($options['linksync_laid'])) {
            $this->update_current_laid($options['linksync_laid']);
        }

        update_option('linksync_status', $options['linksync_status']);
        update_option('linksync_last_test_time', isset($options['linksync_last_test_time']) ?: current_time('mysql'));
        update_option('linksync_connected_url', $apiConfig['url']);
        update_option('linksync_connectedto', isset($options['linksync_connectedto']) ? $options['linksync_connectedto'] : '');
        update_option('linksync_connectionwith', isset($options['linksync_connectionwith']) ? $options['linksync_connectionwith'] : '');
        update_option('linksync_addedfile', isset($options['linksync_addedfile']) ? $options['linksync_addedfile'] : '');
        update_option('linksync_frequency', isset($options['linksync_frequency']) ? $options['linksync_frequency'] : '');
    }

    /**
     * Check if the apikey/laid is valid or not
     * @param null $laid linksync laid
     * @param bool $force_get_laid If true it forces to get the current laid information from the api
     * @return null|string
     */
    public function check_api_key($laid = null, $force_get_laid = false)
    {
        set_time_limit(0);
        //if laid is null then get the current laid key connection to check its validity
        $laid_key = (null == $laid) ? $this->get_current_laid() : $laid;

        if (empty($laid_key)) {
            return 'Empty Api key';
        }

        $current_laid_key_info = $this->get_current_laid_info();
        if(empty($current_laid_key_info) || true == $force_get_laid){
            $current_laid_key_info = LS_Vend()->laid()->get_laid_info($laid_key);
        }

        if (!empty($current_laid_key_info)) {
            LS_Vend()->laid()->update_current_laid_info($current_laid_key_info);

            if (!empty($current_laid_key_info['errorCode'])) {

                $laid_key_options = array(
                    'linksync_laid' => $laid_key,
                    'linksync_status' => 'Inactive',
                    'linksync_frequency' => $current_laid_key_info['userMessage']
                );

                $this->update_laid_key_options($laid_key_options);

                $laid_key_options['laid'] = $laid_key;
                $laid_key_options['errorCode'] = $current_laid_key_info['errorCode'];
                $laid_key_options['error_message'] = $current_laid_key_info['userMessage'];
                $laid_key_options['lws_laid_key_info'] = $current_laid_key_info;

                return $laid_key_options;

            } else {
                $connected_to = $this->get_connected_app($current_laid_key_info['connected_app']);
                $connected_with = $this->get_connected_app($current_laid_key_info['app']);
                $laid_connection['laid'] = $laid_key;
                if (
                    ('Vend' == $connected_to || 'Vend' == $connected_with) &&
                    ('WooCommerce' == $connected_to || 'WooCommerce' == $connected_with)
                ) {

                    $updateWebHook = $this->update_webhook_connection(array(
                        'laid_key' => $laid_key,
                        'product_import' => 'yes'
                    ));

                    $laid_connection['webhook_response'] = $updateWebHook;
                    if (isset($updateWebHook['errorCode'])) {
                        $laid_connection['errorCode'] = $updateWebHook['errorCode'];
                    }

                    if (isset($updateWebHook['errorCode'])) {
                        $laid_connection['userMessage'] = $updateWebHook['userMessage'];
                        $laid_connection['error_message'] = $updateWebHook['userMessage'];
                    }

                    $laid_connection['connected_to'] = 'Vend';

                    if (isset($result['time']) && !empty($result['time'])) {
                        LS_Vend()->option()->update_time_offset($result['time']);
                    }

                    // Add Default setting into DB
                    $syncType = LS_Vend()->product_option()->sync_type();
                    $response_outlets = LS_Vend()->api()->getOutlets();
                    LS_Vend()->option()->update_vend_outlets($response_outlets);
                    /**
                     * Vend to WooCommerce
                     */
                    if (get_option('ps_outlet') == 'on' && 'vend_to_wc-way' == $syncType) {

                        if (isset($response_outlets) && !empty($response_outlets)) {
                            if (isset($response_outlets['errorCode']) && !empty($response_outlets['userMessage'])) {
                                // update_option('ps_outlet_details', 'off');
                                $response_ = $response_outlets['userMessage'];
                                LSC_Log::add('linksync_getOutlets', 'fail', $response_, $laid_key);
                            } else {

                                $selected_outlets = get_option('ps_outlet_details');
                                /**
                                 * Check if current settings for outlets is empty then select all outlets
                                 * else do nothing and do not override users selected outlet(s)
                                 */
                                if (empty($selected_outlets)) {
                                    $oulets = array();
                                    foreach ($response_outlets['outlets'] as $key => $value) {
                                        $oulets["{$key}"] = $value['id'];
                                    }
                                    $ouletsdb = implode('|', $oulets);
                                    update_option('ps_outlet_details', $ouletsdb);
                                    update_option('ps_outlet', 'on');
                                }
                            }
                        }
                    }

                    if (get_option('ps_wc_to_vend_outlet') == 'on' && 'two_way' == $syncType) {
                        if (isset($response_outlets['errorCode']) && !empty($response_outlets['userMessage'])) {
                            update_option('wc_to_vend_outlet_detail', 'off');
                            LSC_Log::add('linksync_getOutlets', 'fail', $response_outlets['userMessage'], $laid_key);
                        }
                    }
                    /*
                     * display_retail_price_tax_inclusive(0 or 1)
                     */
                    $vend_config = LS_Vend_Config::get_vend_config();
                    if (isset($vend_config) && !empty($vend_config)) {
                        if (!isset($vend_config['errorCode'])) {
                            update_option('linksync_tax_inclusive', $vend_config['display_retail_price_tax_inclusive']);
                        } else {
                            update_option('linksync_tax_inclusive', '');
                        }
                    }

                    LSC_Log::add('isConnected', 'success', 'Connected URL is ' . get_option('linksync_connected_url'), $laid_key);

                    $this->update_laid_key_options(array(
                        'linksync_laid' => $laid_key,
                        'linksync_status' => 'Active',
                        'linksync_connected_url' => get_option('linksync_connected_url'),
                        'linksync_frequency' => 'Valid API Key',
                        'linksync_connectionwith' => $connected_with,
                        'linksync_connectedto' => $connected_to,
                    ));
                    $laid_connection['success'] = 'Connection is established Successfully!!';


                } else {

                    $this->update_laid_key_options(array(
                        'linksync_status' => 'Inactive',
                        'linksync_connected_url' => get_option('linksync_connected_url'),
                        'linksync_connectedto' => 'The supplied API Key is not valid for use with linksync for WooCommerce.',
                        'linksync_connectionwith' => 'Supplied API Key not valid',
                        'linksync_frequency' => 'Invalid API Key'
                    ));

                    LSC_Log::add('checkAPI Key', 'fail', 'Invalid API Key', '-');
                    $laid_connection['error'] = "The supplied API Key is not valid for use with linksync for WooCommerce.";
                    $laid_connection['error_message'] = "The supplied API Key is not valid for use with linksync for WooCommerce.";

                }

                return $laid_connection;
            }

        } else {
            $laid_key_options = array(
                'linksync_status' => 'Inactive',
                'linksync_laid' => $laid_key,
                'linksync_connectedto' => 'The supplied API Key is not valid for use with linksync for WooCommerce.',
                'linksync_connectionwith' => 'Supplied API Key not valid',
                'linksync_frequency' => 'Invalid API Key'
            );

            $this->update_laid_key_options($laid_key_options);
            $laid_connection['laid'] = $laid_key;
            $laid_connection['error'] = "The supplied API Key is not valid for use with linksync for WooCommerce.";
            $laid_connection['error_message'] = "The supplied API Key is not valid for use with linksync for WooCommerce.";
            return $laid_connection;
        }


        return null;

    }

    public function is_new($laid)
    {
        $current_laid = self::get_current_laid();

        if ($laid != $current_laid) {
            return true;
        }

        return false;
    }

    public function generate_code($length = 6)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function save_api_key()
    {
        $posted_data = array();
        $message = 'invalid';
        $response = null;
        if (!empty($_POST['post_data'])) {
            if (!is_array($_POST['post_data'])) {
                parse_str($_POST['post_data'], $posted_data);
            }

            if (!empty($posted_data['apikey'])) {
                $laid = trim($posted_data['apikey']);
                $ls_api = LS_Vend()->laid()->get_laid_info($laid);
                if (!empty($ls_api)) {
                    LS_Vend()->laid()->update_current_laid_info($ls_api);
                }

                if (isset($ls_api['errorCode'])) {
                    $message = 'invalid_apikey';
                    $response = $ls_api;
                } else {
                    $response = LS_Vend()->laid()->check_api_key($laid);

                    if (isset($response['success'])) {
                        LSC_Log::add('Manage API Keys', 'success', 'API key Updated Successfully', $laid);
                        $message = 'api_key_updated';
                        if (isset($_POST['add_api_key'])) {
                            $message = 'api_key_added';
                        }

                        LS_User_Helper::reset_capping_error_message();
                    } else {
                        $message = 'invalid';
                    }
                }

            } else {
                $message = 'empty_api_key';
            }
        }

        wp_send_json(array(
            'message' => $message,
            'response' => $response
        ));
    }

}