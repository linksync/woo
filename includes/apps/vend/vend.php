<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');
/**
 * This file will serve as the entry point of vend app
 */

/**
 * Include the class for managing vend api key
 */
require_once LS_INC_DIR.'apps/vend/ls-vend-api-key.php';

/**
 * Include the class for managing logs
 */
require_once LS_INC_DIR.'apps/vend/ls-vend-log.php';

/**
 * Require the controllers below
 */
 require_once LS_INC_DIR.'apps/vend/controllers/ls-log.php';