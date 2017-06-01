<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Notice
{
    public function __construct()
    {
        add_action('admin_notices', array($this, 'vendNotice'), 16);
    }


    public function vendNotice()
    {
        $current_screen = get_current_screen();

        if ('shop_order' == $current_screen->id) {
            if (isset($_GET['post'])) {

                if (isset($_GET['ls_dev_log']) && 'woo_to_vend' == $_GET['ls_dev_log']) {
                    $this->showWooToVendDevOrderNotice($_GET['post']);
                }

            }
        }

    }

    public function showWooToVendDevOrderNotice($orderId)
    {
        $orderMeta = new LS_Order_Meta($orderId);
        echo '<div>';
        ls_print_r($orderMeta->getOrderJsonFromWooToVend());
        echo '</div>';

    }

}

new LS_Vend_Notice();