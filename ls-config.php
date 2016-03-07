<?php if ( ! defined( 'ABSPATH' ) ) exit;  


/**
 *	True on testmode
 */
$config['testmode'] = false;


/**
 * Set api version		
 */
$config['api']		= 'v1';

/**
 *  Plugin directories and Url
 */

$plugin_url         = plugins_url();
$plugin_dir_url     = plugin_dir_url( __FILE__ );
$plugin_dir_path    = plugin_dir_path(__FILE__ );
$plugin_basename    = plugin_basename( __FILE__ );

$plugin_includes_dir    = $plugin_dir_path.'includes/';

$plugin_assets_dir      = $plugin_dir_path.'assets/';
$plugin_assets_url      = $plugin_dir_url.'assets/';


require_once($plugin_dir_path.'ls-config.php');
if($config['testmode']){

  $config['api'] = 'test';

}

$apiConfig = include_once($plugin_includes_dir.'api/ls-apiconfig.php');

include_once($plugin_includes_dir.'api/ls-api.php');

$api = new LS_Api($apiConfig[$config['api']],get_option('linksync_laid'));

//Sample get request
$request = $api->get('product'); 


exit;
