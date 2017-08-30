<?php

/**
 * Table list of duplicate sku in WooCommerce or in QuikcBooks Online
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class LS_Vend_Connected_Order_List extends WP_List_Table
{

    public $connected_orders = array();

    public function __construct($connected_orders = array())
    {

        $this->connected_orders = $connected_orders;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'connected_product',
            'plural' => 'connected_products',
            'ajax' => true
        ));

    }

    /**
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'id' => 'Order',
            'email' => 'Email',
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'id' => array('id', false),
        );
        return $sortable_columns;
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            'ID',  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/
            $item['ID']                //The value of the checkbox should be the record's id
        );
    }

    public function column_id($item)
    {
        $vendConfig = new LS_Vend_Config();
        $vendUrl = new LS_Vend_Url($vendConfig);
        $orderMeta = new LS_Order_Meta($item['ID']);

        $edit_link_in_woo = get_edit_post_link($item['ID']);
        $edit_link_in_vend = $vendUrl->get_order_edit_url($item['vend_receipt_number']);
        $user_edit_link = get_edit_user_link($orderMeta->get_customer_id());

        //Build row actions
        $actions = array(
            'edit_in_woo' => sprintf('<a target="_blank" href="%s">Edit in WooCommerce</a>', $edit_link_in_woo),
            'edit_in_vend' => sprintf('<a target="_blank" href="%s">View in Vend</a>', $edit_link_in_vend),
        );


        $row_title = '<a target="_blank" href="' . $edit_link_in_woo . '" class="row-title"><strong>#' . $item['ID'] . '</strong></a>';
        $row_title .= ' by ';
        $row_title .= '<a target="_blank" href="' . $user_edit_link . '">' . $orderMeta->get_billing_first_name() . ' ' . $orderMeta->get_billing_last_name() . '</a>';

        //Return the title contents
        return sprintf('%1$s <br/> <span style="color:silver">(%2$s)</span>%3$s',
            $row_title,
            $item['product_name'],
            $this->row_actions($actions)
        );

    }

    public function column_email($item)
    {
        $orderMeta = new LS_Order_Meta($item['ID']);
        return $orderMeta->get_billing_email();
    }


    public function process_bulk_action()
    {

    }

    public function prepare_items()
    {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        $data = $this->connected_orders;
        if (empty($this->connected_orders)) {
            $orderBy = '';
            $order = 'DESC';
            if(isset($_REQUEST['orderby']) && 'id' == $_REQUEST['orderby']){
                $orderBy = 'wposts.ID';
            }

            if(isset($_REQUEST['order'])){
                $order = $_REQUEST['order'];
            }

            $data = LS_Vend_Order_Helper::get_vend_connected_orders($orderBy, $order);
        }


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        if (!empty($data)) {
            $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        }


        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page' => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }
}