<?php if ( ! defined( 'ABSPATH' ) ) exit('Access is Denied');

/**
 * Plugins Global functions file
 * Apps functions.php file will be included here
 */

/**
 * Vend functions.php
 */
include LS_INC_DIR.'apps/vend/functions/functions.php';

/**
 * QBO functions.php
 */
include LS_INC_DIR.'apps/qbo/functions/functions.php';

/**
 * A wrapper function that will wrap print_r into pre tag
 * Useful in debuging purposes.
 * @param array or object $data
 */
function ls_print_r($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}


/**
 * Remove unneeded string
 * @param $string
 * @return mixed|string
 */
function remove_escaping_str( $string ){
	$str_tobe_removed = array("\\");
	$str = '';

	foreach( $str_tobe_removed as $needle ){
		$str = str_replace($needle, '', $string );
	}

	return $str;
}
