<?php

/**
 * Table list of duplicate or empty sku in QuikcBooks Online
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class LS_Vend_Duplicate_Sku_List extends WP_List_Table
{
    public $empty_skus = null;
    public $duplicate_skus = null;
    public $duplicate_and_empty_skus = null;

    public function __construct($args = null)
    {
        global $status, $page;

        if (!empty($args['duplicate_products'])) {
            $this->duplicate_skus = $args['duplicate_products'];
        }

        if (!empty($args['empty_product_skus'])) {
            $this->empty_skus = $args['empty_product_skus'];
        }

        if (!empty($args['section'])) {
            $this->section = $args['section'];
        }

        if (!empty($args['duplicate_and_empty_skus'])) {
            $this->duplicate_and_empty_skus = $args['duplicate_and_empty_skus'];
        }

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'sku',
            'plural' => 'skus',
            'ajax' => true
        ));
    }

    public function get_duplicate_and_empty_skus()
    {
        return $this->duplicate_and_empty_skus;
    }

    public function column_default($item, $column_name)
    {

        switch ($column_name) {
            case 'name':
            case 'sku':
            case 'active':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }

    }

    public function column_name($item)
    {
        //Return the title contents
        return sprintf('%1$s',
            /*$1%s*/
            $item['name']
        );
    }

    public function column_active($item)
    {
        $status = 'Inactive';
        if ('1' == $item['active']) {
            $status = 'Active';
        }
        return sprintf('%1$s',
            $status
        );
    }

    public function is_section($section)
    {
        $active_section = LS_Vend_Menu::get_active_section();
        if ($active_section == $section) {
            return true;
        }
        return false;
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            'ID',  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/
            $item['id']                //The value of the checkbox should be the record's id
        );
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
            'name' => 'Product Name',
            'sku' => 'Product SKU',
            'active' => 'Product Status'
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', false),     //true means it's already sorted
            'sku' => array('sku', false),
            'active' => array('active', false)
        );
        return $sortable_columns;
    }

    public function extra_tablenav($which)
    {
        if ('top' == $which && $this->has_items()) {
            ?>
            <div>
                <input id="make-qbo-skus-unique"
                       class="button button-primary button-large "
                       type="submit"
                       name="makevendskuunique"
                       value="Make Vend SKU Unique"
                       style="float: left; margin-top: 1px;">

                <span id="ls-qbo-spinner" class="spinner is-active"
                      style="float: left;display: none;"></span>
                <br/>
            </div>

            <?php
        }
    }

    public function get_bulk_actions()
    {
        $actions = array(
            'replace_empty_sku' => 'Replace Empty SKU',
            'make_sku_unique' => 'Make SKU Unique',
            'delete_permanently' => 'Delete Permanently',
        );
        return $actions;
    }

    public function process_bulk_action()
    {
        //Detect when a bulk action is being triggered...
        if ('delete_permanently' === $this->current_action()) {

        }

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


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->duplicate_and_empty_skus;

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'name'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
        }

        if (!empty($data)) {
            usort($data, 'usort_reorder');
        }


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