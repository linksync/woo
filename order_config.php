<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="../wp-content/plugins/linksync/css/jquery-ui.css">
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../wp-content/plugins/linksync/jquery-tiptip/jquery-ui.js"></script>
<script>
    var linksync_jQuery1=jQuery.noConflict( true );
    linksync_jQuery1(function() {
        linksync_jQuery1(document).tooltip();
    });

    linksync_jQuery1(window).load(function() {
        // Animate loader off screen
        linksync_jQuery1(".se-pre-con").fadeOut("slow");;
    });
</script>
<style>
    .no-js #loader { display: none;  }
    .js #loader { display: block; position: absolute; left: 100px; top: 0; }
    .se-pre-con {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        opacity: 0.7;
        background: url(../wp-content/plugins/linksync/img/loading.gif) center no-repeat rgb(0, 0, 0);
    }
    label {
        display: inline-block;
        width: 5em;
    }
</style>
<div class="se-pre-con"></div>
<?php
$LAIDKey = get_option('linksync_laid'); 
if (!empty($LAIDKey)) {
    if (get_option('linksync_connectionwith') == 'Vend' || get_option('linksync_connectedto') == 'Vend') {
        include_once(dirname(__FILE__) . '/vend_order_config.php');
    } elseif (get_option('linksync_connectionwith') == 'QuickBooks Online' || get_option('linksync_connectedto') == 'QuickBooks Online') {
        include_once(dirname(__FILE__) . '/QB_order_config.php');
    } else {
        echo "<p align=center style='color:red;font-size:17px;margin-top:150px;'><b>" . $LAIDKey . "</b> does not appear to be a valid API Key</p>";
    }
} else {
    echo "<p align=center style='color:red;font-size:17px;margin-top:150px;'>Not Connected Or Missing API Key</p>";
}
?> 