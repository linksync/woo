<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Product_Option extends LS_Vend_Option
{

    /**
     * LS_QBO_Product_Option instance
     * @var null
     */
    protected static $_instance = null;

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * used on vend to woo sync type option for selecting multiple checkbox outlets for quantity syncing
     * @return array
     */
    public function vend_to_woo_selected_outlet()
    {
        $getoutlets = get_option('ps_outlet_details');

        return  explode("|", $getoutlets);
    }

    /**
     * used on two way and woo to vend sync type option for selecting radio button outlets for quantity syncing
     * @return array
     */
    public function two_way_selected_outlet()
    {
        $selected_outlets = get_option('wc_to_vend_outlet_detail');
        $selected_outlets = explode("|", $selected_outlets);
        if(!empty($selected_outlets[1])){
            return array($selected_outlets[1]);
        }
        return array();
    }

    public function allow_back_order()
    {
        return $this->get_option('allow_backorder', 'off');
    }

    public function update_allow_back_order($value)
    {
        return $this->update_option('allow_backorder', $value);
    }

    public function import_by_tag()
    {
        return get_option('ps_imp_by_tag', 'off');
    }

    public function update_import_by_tag($option_value)
    {
        return update_option('ps_imp_by_tag', $option_value);
    }

    public function import_by_tags_list()
    {
        return get_option('import_by_tags_list', '');
    }

    public function update_import_by_tags_list($option_value)
    {
        return update_option('import_by_tags_list', $option_value);
    }

    public function sync_type($default = '')
    {
        return get_option('product_sync_type', $default);
    }

    public function update_sync_type($meta_value)
    {
        return update_option('product_sync_type', $meta_value);
    }

    public function nameTitle()
    {
        return get_option('ps_name_title', 'on');
    }

    public function updateNameTitle($meta_value)
    {
        return update_option('ps_name_title', $meta_value);
    }

    public function description()
    {
        return get_option('ps_description', 'on');
    }

    public function updateDescription($meta_value)
    {
        return update_option('ps_description', $meta_value);
    }

    public function shortDescription()
    {
        return get_option('ps_desc_copy', 'off');
    }

    public function updateShortDescription($meta_value)
    {
        return get_option('ps_desc_copy', $meta_value);
    }

    public function price()
    {
        return get_option('ps_price');
    }

    public function updatePrice($meta_value)
    {
        return update_option('ps_price', $meta_value);
    }

    public function priceField()
    {
        return get_option('price_field');
    }

    public function updatePriceField($meta_value)
    {
        return update_option('price_field', $meta_value);
    }

    public function brand()
    {
        return get_option('ps_brand');
    }

    public function updateBrand($meta_value)
    {
        return update_option('ps_brand', $meta_value);
    }

    public function quantity()
    {
        return get_option('ps_quantity', 'on');
    }

    public function updateQuantity($meta_value)
    {
        return update_option('ps_quantity', $meta_value);
    }

    public function tag()
    {
        return get_option('ps_tags', 'off');
    }

    public function updatTag($meta_value)
    {
        return update_option('ps_tags', $meta_value);
    }

    public function category()
    {
        return get_option('ps_categories', 'off');
    }

    public function updateCategory($meta_value)
    {
        return $this->update_option('ps_categories', $meta_value);
    }

    public function productStatusToPending()
    {
        return get_option('ps_pending', 'off');
    }

    public function updateProductStatus($meta_value)
    {
        return update_option('ps_pending', $meta_value);
    }

    public function image()
    {
        return get_option('ps_images', 'off');
    }

    public function updateImage($meta_value)
    {
        return update_option('ps_images', $meta_value);
    }

    public function importImage()
    {
        return get_option('ps_import_image_radio');
    }

    public function updateImportImage($meta_value)
    {
        return update_option('ps_import_image_radio', $meta_value);
    }


    public function createNew()
    {
        return get_option('ps_create_new', 'on');
    }

    public function updateCreateNew($meta_value)
    {
        return update_option('ps_create_new', $meta_value);
    }


    public function excluding_tax()
    {
        return get_option('excluding_tax');
    }

    public function udpate_excluding_tax($value)
    {
        return update_option('excluding_tax', $value);
    }

    public function linksyncStatus($default = '')
    {
        return get_option('linksync_status', $default);
    }

    public function updateLinksyncStatus($meta_value)
    {
        return update_option('linksync_status', $meta_value);
    }

    public function displayRetailPriceTaxInclusive()
    {
        return get_option('linksync_tax_inclusive');
    }

    public function updateDisplayRetailPriceTaxInclusive($meta_value)
    {
        return update_option('linksync_tax_inclusive', $meta_value);
    }

    public function delete()
    {
        return get_option('ps_delete', 'off');
    }

    public function update_delete($meta_value)
    {
        return update_option('ps_delete', $meta_value);
    }

    public function attributes()
    {
        return get_option('ps_attribute', 'on');
    }

    public function updateAttributes($meta_value)
    {
        return update_option('ps_attribute', $meta_value);
    }

    public function attributeVisibleOnProductPage()
    {
        return get_option('linksync_visiable_attr', '1');
    }

    public function updateAttributeVisibleOnProductPage($meta_value)
    {
        return update_option('linksync_visiable_attr', $meta_value);
    }

    public function changeProductStatusBaseOnQuantity()
    {
        return get_option('ps_unpublish');
    }

    public function updateChangeProductStatusBaseOnQuantity($value)
    {
        return update_option('ps_unpublish', $value);
    }

    public function selected_radio_for_category()
    {
        return get_option('cat_radio');
    }

    public function syncable_product_status()
    {
        $optionValue = $this->get_option('syncable_product_status', '');
        return $optionValue;
    }

    public function update_syncable_product_status($value)
    {
        $this->update_option('syncable_product_status', $value);
    }

    public function get_syncing_options()
    {
        return array(
            'syncable_product_status' => $this->syncable_product_status(),
            'sync_type' => $this->sync_type(),
            'name_or_title' => $this->nameTitle(),
            'description' => $this->description(),
            'short_description' => $this->shortDescription(),
            'price_option_group' => array(
                'price' => $this->price(),
                'price_field' => $this->priceField(),
                'treat_price' => $this->excluding_tax(),
                'use_woocommerce_tax_opton' => $this->linksync_woocommerce_tax_option()
            ),
            'quantity_option_group' => array(
                'quantity' => $this->quantity(),
                'change_product_status' => $this->changeProductStatusBaseOnQuantity()
            ),
            'tags' => $this->tag(),
            'category_option_group' => array(
                'category' => $this->category(),
                'category_selected_radio' => $this->selected_radio_for_category()
            ),
            'product_status' => $this->productStatusToPending(),
            'import_by_tag' => $this->import_by_tag(),
            'image_option_group' => array(
                'image' => $this->image(),
                'image_sync_type' => $this->importImage()
            ),
            'create_new' => $this->createNew(),
            'delete' => $this->delete()
        );
    }

    public static function save_product_syncing_settings()
    {
        $productSyncOption = LS_Vend()->product_option();
        $product_sync_type = 'disabled';
        $userProductOptions = array();
        if (!empty($_POST['post_array'])) {
            if (!is_array($_POST['post_array'])) {
                parse_str($_POST['post_array'], $userProductOptions);
            }

            if (!empty($userProductOptions)) {

                if (isset($userProductOptions['product_sync_type']) && !empty($userProductOptions['product_sync_type'])) {
                    $product_sync_type = $userProductOptions['product_sync_type'];
                    update_option('product_sync_type', $userProductOptions['product_sync_type']);
                    if ($userProductOptions['product_sync_type'] == 'vend_to_wc-way') {
                        if (isset($userProductOptions['ps_quantity'])) {
                            if ($userProductOptions['ps_quantity'] == 'on') {
                                if (empty($userProductOptions['outlet'])) {
                                    $message['result'] = 'error';
                                    $message['message'] = 'You didn\'t select any outlet !';
                                } else {
                                    $message['result'] = 'success';
                                }
                            } else {
                                $message['result'] = 'success';
                            }
                        } else {
                            $message['result'] = 'success';
                        }
                    } elseif ($userProductOptions['product_sync_type'] == 'wc_to_vend' || $userProductOptions['product_sync_type'] == 'two_way') {
                        $message['result'] = 'success';
                    } else {
                        $message['result'] = 'success';
                    }
                }

                if (isset($message['result']) && $message['result'] == 'success') {
                    if (isset($userProductOptions['syncable_product_status'])) {
                        LS_Vend()->product_option()->update_syncable_product_status($userProductOptions['syncable_product_status']);
                    } else {
                        LS_Vend()->product_option()->update_syncable_product_status('');
                    }


                    update_option('prod_update_suc', NULL);
                    update_option('prod_last_page', NULL);
                    update_option('product_detail', NULL);
                    if (isset($userProductOptions['ps_name_title'])) {
                        if ($userProductOptions['ps_name_title'] == 'on') {
                            update_option('ps_name_title', $userProductOptions['ps_name_title']);
                        } else {
                            update_option('ps_name_title', 'off');
                        }
                    } else {
                        update_option('ps_name_title', 'off');
                    }
                    if (isset($userProductOptions['ps_description'])) {
                        if ($userProductOptions['ps_description'] == 'on') {
                            update_option('ps_description', $userProductOptions['ps_description']);
                        } else {
                            update_option('ps_description', 'off');
                        }
                    } else {
                        update_option('ps_description', 'off');
                    }
                    if (isset($userProductOptions['ps_desc_copy'])) {
                        if ($userProductOptions['ps_desc_copy'] == 'on') {
                            update_option('ps_desc_copy', 'on');
                        } else {
                            update_option('ps_desc_copy', 'off');
                        }
                    } else {
                        update_option('ps_desc_copy', 'off');
                    }
                    //Pending
                    if (isset($userProductOptions['ps_pending'])) {
                        if ($userProductOptions['ps_pending'] == 'on') {
                            update_option('ps_pending', $userProductOptions['ps_pending']);
                        } else {
                            update_option('ps_pending', 'off');
                        }
                    } else {
                        update_option('ps_pending', 'off');
                    }
                    if ($userProductOptions['product_sync_type'] == 'vend_to_wc-way') {
                        if (isset($userProductOptions['ps_attribute'])) {
                            if ($userProductOptions['ps_attribute'] == 'on') {
                                update_option('ps_attribute', $userProductOptions['ps_attribute']);
                            } else {
                                update_option('ps_attribute', 'off');
                            }
                        } else {
                            update_option('ps_attribute', 'off');
                        }
                        if (isset($userProductOptions['linksync_visiable_attr'])) {
                            if ($userProductOptions['linksync_visiable_attr'] == '1') {
                                update_option('linksync_visiable_attr', $userProductOptions['linksync_visiable_attr']);
                            } else {
                                update_option('linksync_visiable_attr', '0');
                            }
                        } else {
                            update_option('linksync_visiable_attr', '0');
                        }
                    } else {
                        update_option('linksync_visiable_attr', '0');
                        update_option('ps_attribute', 'on');
                    }
                    // Price
                    if (isset($userProductOptions['ps_price'])) {
                        if ($userProductOptions['ps_price'] == 'on') {
                            update_option('ps_price', $userProductOptions['ps_price']);
                            if (isset($userProductOptions['linksync_woocommerce_tax_option'])) {
                                if ($userProductOptions['linksync_woocommerce_tax_option'] == 'on') {
                                    update_option('linksync_woocommerce_tax_option', $userProductOptions['linksync_woocommerce_tax_option']);
                                } else {
                                    update_option('linksync_woocommerce_tax_option', 'off');
                                }
                            } else {
                                update_option('linksync_woocommerce_tax_option', 'off');
                            }
                            if (isset($userProductOptions['excluding_tax'])) {
                                if ($userProductOptions['excluding_tax'] == 'on') {
                                    update_option('excluding_tax', 'on');
                                } else {
                                    update_option('excluding_tax', 'off');
                                }
                            } else {
                                update_option('excluding_tax', 'off');
                            }
                            if (isset($userProductOptions['price_field'])) {
                                update_option('price_field', $userProductOptions['price_field']);
                            }
                            if (isset($userProductOptions['tax_class']) && !empty($userProductOptions['tax_class'])) {
                                if (array_filter($userProductOptions['tax_class'])) {
                                    $all_taxes = implode(',', $userProductOptions['tax_class']);
                                    update_option('tax_class', $all_taxes);
                                } else {
                                    update_option('tax_class', '');
                                }
                            } else {
                                update_option('tax_class', '');
                            }

                        } else {
                            update_option('ps_price', 'off');
                            update_option('excluding_tax', 'off');
                            update_option('tax_mapping', 'off');
                            //  update_option('price_book', 'off');
                        }
                    } else {
                        update_option('ps_price', 'off');
                        update_option('excluding_tax', 'off');
                        update_option('tax_mapping', 'off');
                        // update_option('price_book', 'off');
                        update_option('tax_class', 'off');
                    }
                    // Quantity
                    if (isset($userProductOptions['ps_quantity'])) {
                        if ($userProductOptions['ps_quantity'] == 'on') {
                            update_option('ps_quantity', 'on');
                            if (!empty($userProductOptions['outlet'])) {
                                $oulets = implode('|', $userProductOptions['outlet']);
                                update_option('ps_outlet', 'on');
                                update_option('ps_outlet_details', $oulets);
                            } else {
                                update_option('ps_outlet', 'off');
                                update_option('ps_outlet_details', 'off');
                            }

                            if (isset($userProductOptions['ps_unpublish'])) {
                                if ($userProductOptions['ps_unpublish'] == 'on') {
                                    update_option('ps_unpublish', 'on');
                                } else {
                                    update_option('ps_unpublish', 'off');
                                }
                            } else {
                                update_option('ps_unpublish', 'off');
                            }

                            if (isset($userProductOptions['allow_backorder'])) {

                                if ('on' == $userProductOptions['allow_backorder']) {
                                    $productSyncOption->update_allow_back_order('on');
                                } else {
                                    $productSyncOption->update_allow_back_order('off');
                                }

                            } else {
                                $productSyncOption->update_allow_back_order('off');
                            }
                        } else {
                            update_option('ps_quantity', 'off');
                            update_option('ps_unpublish', 'off');
                        }
                    } else {
                        update_option('ps_quantity', 'off');
                        update_option('ps_unpublish', 'off');
                    }

                    if (isset($userProductOptions['ps_brand']) && $userProductOptions['ps_brand'] == 'on') {
                        update_option('ps_brand', 'on');
                    } else {
                        update_option('ps_brand', 'off');
                    }

                    if (isset($userProductOptions['ps_tags'])) {
                        if ($userProductOptions['ps_tags'] == 'on') {
                            update_option('ps_tags', 'on');
                        } else {
                            update_option('ps_tags', 'off');
                        }
                    } else {
                        update_option('ps_tags', 'off');
                    }
                    if (isset($userProductOptions['ps_categories']) && !empty($userProductOptions['ps_categories'])) {
                        if (isset($userProductOptions['cat_radio'])) {
                            update_option('cat_radio', $userProductOptions['cat_radio']);
                        }
                        update_option('ps_categories', 'on');
                    } else {
                        update_option('ps_categories', 'off');
                    }

                    if (isset($userProductOptions['wc_to_vend_outlet_detail'])) {
                        update_option('wc_to_vend_outlet_detail', $userProductOptions['wc_to_vend_outlet_detail']);
                        update_option('ps_wc_to_vend_outlet', 'on');
                    }


                    if (isset($userProductOptions['ps_imp_by_tag'])) {
                        if ($userProductOptions['ps_imp_by_tag'] == 'on') {
                            if (isset($userProductOptions['import_by_tags_list']) && !empty($userProductOptions['import_by_tags_list'])) {
                                $selected_tags = array();
                                foreach ($userProductOptions['import_by_tags_list'] as $key => $selected_tab) {
                                    $selected_tags[] = remove_escaping_str($selected_tab);
                                }
                                $tags = implode('|', $selected_tags);

                                $import_by_tags_list_serialize = serialize($tags);
                                update_option('import_by_tags_list', $import_by_tags_list_serialize);
                                update_option('ps_imp_by_tag', 'on');
                            } else {
                                update_option('import_by_tags_list', '');
                                update_option('ps_imp_by_tag', 'off');
                            }
                        } else {
                            update_option('ps_imp_by_tag', 'off');

                            update_option('import_by_tags_list', '');
                        }
                    } else {
                        update_option('ps_imp_by_tag', 'off');

                        update_option('import_by_tags_list', '');
                    }
                    if (isset($userProductOptions['ps_images']) && !empty($userProductOptions['ps_images'])) {
                        if (isset($userProductOptions['ps_import_image_radio'])) {
                            update_option('ps_import_image_radio', $userProductOptions['ps_import_image_radio']);
                        }
                        update_option('ps_images', 'on');
                    } else {
                        update_option('ps_images', 'off');
                    }
                    if (isset($userProductOptions['ps_create_new'])) {
                        if ($userProductOptions['ps_create_new'] == 'on') {
                            update_option('ps_create_new', 'on');
                        } else {
                            update_option('ps_create_new', 'off');
                        }
                    } else {
                        update_option('ps_create_new', 'off');
                    }
                    if (isset($userProductOptions['ps_delete'])) {
                        if ($userProductOptions['ps_delete'] == 'on') {
                            update_option('ps_delete', 'on');
                        } else {
                            update_option('ps_delete', 'off');
                        }
                    } else {
                        update_option('ps_delete', 'off');
                    }

                    /**
                     * Set $saving_sync_type to have the currently selected syncing type
                     */
                    $saving_sync_type = $userProductOptions['product_sync_type'];
                    if ($userProductOptions['product_sync_type'] != 'disabled_sync') {
                        if ($userProductOptions['product_sync_type'] == 'vend_to_wc-way') {
                            $enable = 'Vend to Woo';
                        } elseif ($userProductOptions['product_sync_type'] == 'two_way') {
                            $enable = 'Two way';
                        } else {
                            $enable = 'Woo to Vend';
                        }
                        $setting_message = $enable . '  enable';
                    } else {
                        $setting_message = 'Sync Setting Disabled';
                    }
                }
                update_option('image_process', 'complete');

                LS_Vend()->updateWebhookConnection();
                LS_Vend()->save_user_settings_to_linksync();


            }
        }
        wp_send_json(array(
            'message' => 'success',
            'sync_type' => $product_sync_type
        ));
    }

}