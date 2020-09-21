<?php if (!defined('ABSPATH')) exit('Access is Denied');

    require_once LS_PLUGIN_DIR . '/classes/Class.linksync.php';
?>

<div class="wrap" id="ls-main-wrapper">
    <div id="response"></div>

    <?php
        $LAIDKey = LS_Vend()->laid()->get_current_laid();

        if (!empty($LAIDKey)) {
            LS_Vend()->laid()->check_api_key($LAIDKey);
        }

        LS_Vend()->view()->display();

    ?>

</div> 