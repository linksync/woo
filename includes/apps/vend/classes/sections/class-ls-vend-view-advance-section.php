<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_View_Advance_Section
{

    public static function custom_since_sync()
    {
        $last_product_sync = LS_Vend()->option()->lastProductUpdate();
        if(empty($last_product_sync)){
            $last_product_sync = time();
        }

        $last_product_sync = date('Y-m-d', strtotime($last_product_sync));

        $vend_option = LS_Vend()->option();
        $status = $vend_option->connection_status();
        $vend_product_option = LS_Vend()->product_option();
        $sync_type = $vend_product_option->sync_type();

        ?>
        <br/><br/>
        <table class="wp-list-table widefat fixed">
            <thead>
            <tr>
                <td><strong>Import Vend updates from a selected date</strong></td>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>
                    In case of integration halt and you need to get vend product changes you made yesterday or from the other day.<br/>
                    You can select a date and import vend updates to your WooCommerce products.
                    <p>Select Date: <input type="text" readonly id="sinceSpecifiedDate" value="<?php echo $last_product_sync;  ?>" style="background-color: white;"></p>
                    <p>
                        <input type="button"
                               class="button button-primary <?php echo (
                                   ('Active' == $status && 'two_way' == $sync_type) ||
                                   ('Active' == $status && 'vend_to_wc-way' == $sync_type)
                               )  ? 'vend_to_woo_since_specified' : ''; ?>"
                            <?php echo ('Inactive' == $status || 'wc_to_vend' == $sync_type) ? 'disabled' : ''; ?>
                               value="Sync Now">
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
        <script>
            (function ($) {

                $(document).ready(function () {
                    $( "#sinceSpecifiedDate" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'yy-mm-dd'
                    });
                });

            }(jQuery));
        </script>
        <?php
    }

    public static function custom_woo_sync_to_vend()
    {
        $vend_option = LS_Vend()->option();
        $status = $vend_option->connection_status();
        $productSyncOption = LS_Vend()->product_option();
        $product_sync_type = $productSyncOption->sync_type();

        $availabLeSyncingType = array(
            'wc_to_vend',
            'two_way'
        );
        ?>
        <style>
            label{
                width: 100%;
            }
        </style>
        <br/><br/>
        <table class=" wp-list-table widefat fixed">
            <thead>
            <tr>
                <td><strong>Export WooCommerce products to Vend on a selected product filter</strong></td>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>

                    <table>
                        <tr class="tr-syncable" valign="top" style="<?php echo ('two_way' == $product_sync_type || 'wc_to_vend' == $product_sync_type) ? '': ''; ?>">

                            <td class="forminp forminp-checkbox">

                                Product Status
                                <?php
                                help_link(array(
                                    'title' => 'When enabled, products are synced with a \'common identifier\' when syncing product information between the two systems by using either of the fields:'
                                ));
                                ?>

                                <br/>
                                <label>
                                    <input class="syncable-status-all" type="checkbox" <?php echo 'checked'; ?>>
                                    All
                                </label><br/>
                                <?php
                                $product_statuses = get_post_statuses();
                                foreach ($product_statuses as $key => $product_status){
                                    ?>
                                    <label>
                                        <input class="other_syncable_status other_syncable_status_<?php echo $key;?>" name="syncable_product_status[<?php echo $key; ?>]" value="<?php echo $key; ?>" type="checkbox"
                                            <?php echo 'checked'; ?>>
                                        <?php echo $product_status; ?>
                                    </label><br/>
                                    <?php
                                }
                                ?>

                            </td>
                            <?php
                                LS_Vend_Product_Helper::filter_select_product_types();
                                LS_Vend_Product_Helper::filter_product_categories();
                                LS_Vend_Product_Helper::filter_product_tags();
                            ?>

                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <input type="button"
                               class="button button-primary <?php echo (
                                   ('Active' == $status && 'two_way' == $product_sync_type) ||
                                   ('Active' == $status && 'wc_to_vend' == $product_sync_type)
                               )  ? 'btn-sync-woo-to-vend-via-filter' : ''; ?>"
                            <?php echo ('Inactive' == $status || !in_array($product_sync_type, $availabLeSyncingType)) ? 'disabled' : ''; ?>
                               value="Sync filtered products to Vend">
                    </p>
                </td>
            </tr>
            </tbody>
        </table>

        <?php

        ?>
        <script>
            (function ($) {
                $(document).ready(function () {

                    var $mainContainer = $('#ls-main-wrapper');

                    $mainContainer.on('click', '.syncable-status-all', function (e) {
                        var $currentElement = $(this);
                        var isChecked = $currentElement.prop("checked");

                        $('.other_syncable_status').each(function (e) {
                            $(this).prop('checked', isChecked);
                        });

                        if(!isChecked){
                            $('.other_syncable_status_publish').prop('checked', true);
                        }

                    });

                    $mainContainer.on('click', '.other_syncable_status', function (e) {

                        var checkedCount = 0;
                        var checkAll = true;
                        $('.other_syncable_status').each(function (e) {

                            var isChecked = $(this).prop("checked");
                            if(!isChecked){
                                checkAll = false;
                                checkedCount += 1;
                            }

                        });

                        $('.syncable-status-all').prop('checked', checkAll);

                        if(4 == checkedCount){
                            //If everything is unchecked then check published product to be checked as default
                            $('.other_syncable_status_publish').prop('checked', true);
                        }

                    });

                    function setDefaultSelectedType() {
                        $('#dropdown_product_type > option').each(function () {

                            if('Simple product' == $(this).text()){
                                $('#dropdown_product_type ').val($(this).val());
                            }
                        });
                    }
                    $mainContainer.on('change', '#dropdown_product_type', function () {
                        var $productTypeSelect = $(this);
                        var value = $productTypeSelect.val();
                        if(!value){
                            setDefaultSelectedType();
                        }
                    });

                    //Initial selected product type
                    setDefaultSelectedType();

                });
            }(jQuery));
        </script>
        <?php
    }

}