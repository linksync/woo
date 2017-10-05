<?php if (!defined('ABSPATH')) exit('Access is Denied');

/**
 * Plugins Global functions file
 * Apps functions.php file will be included here
 */

/**
 * A wrapper function that will wrap print_r into pre tag
 * Useful in debuging purposes.
 * @param array or object $data
 */
function ls_print_r($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * A wrapper function that will wrap var_dump into pre tag
 * Useful in debuging purposes.
 * @param array or object $data
 */
function ls_var_dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * Show Image help link
 */
function help_link($attribute)
{
    /**
     * Check if href key has been added if not set to default link
     */
    $href = isset($attribute['href']) ? $attribute['href'] : 'https://www.linksync.com/help/woocommerce';

    $src = LS_ASSETS_URL.'images/linksync/help.png';
    if (!empty($attribute['label'])) {
        echo $attribute['label'];
    }

    echo '	<a style="color: transparent !important;text-decoration: none;" target="_blank" href="', $href, '">
				<img style="position: relative;top: 5px;" title="', $attribute['title'], '"
					 src="', $src, '"
					 height="16" width="16">
			</a>';
}

