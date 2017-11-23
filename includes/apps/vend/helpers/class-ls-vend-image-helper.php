<?php if (!defined('ABSPATH')) exit('Access is Denied');

class LS_Vend_Image_Helper
{
    /**
     * Create or update product image thumbnail. This method should only be used if the image option is set to Ongoing
     * @param $vend_images
     * @param $product_thumbnail_id
     * @param LS_Product_Meta $product_meta
     * @return array|string String for the image thumbnail url and if product image thumbnail is inserted on the process returns array of
     *                      metadata of attachment, attachment id vend url information
     */
    private static function process_image_thumbnail_for_ongoing_option(&$vend_images, $product_thumbnail_id, LS_Product_Meta $product_meta)
    {
        $product_thumbnail_path = LS_Image_Helper::getImagePath($product_thumbnail_id);
        $product_thumbnail_path_info = pathinfo($product_thumbnail_path);
        $vend_1st_img = pathinfo($vend_images[0]['url']);
        $thumbnail_image_url = '';

        if($product_thumbnail_path_info['basename'] != $vend_1st_img['basename']) {
            if (empty($product_thumbnail_path_info['basename']) && !empty($vend_images[0]['url'])) {

                $thumbnail_image_url = $vend_images[0]['url'];

            } else if (!empty($product_thumbnail_path_info['basename']) && !empty($vend_images[0]['url'])) {

                $thumbnail_image_url = $vend_images[0]['url'];

            }


            if (!empty($thumbnail_image_url)) {

                $attach_id = LS_Image_Helper::setProductThumbnailFromImageUrl($vend_images[0]['url'], $product_meta);
                $thumbnailArray = array(
                    'attach_id' => $attach_id['attachment_id'],
                    'vend_url' => trim($vend_images[0]['url']),
                    'pathinfo' => pathinfo($vend_images[0]['url'])
                );
                $product_meta->updateVendImageThumbnail($thumbnailArray);

                LS_Image_Helper::add_image_gallery_attachment_id($product_meta, $attach_id['attachment_id']);
                unset($vend_images[0]);

                return $thumbnailArray;
            }
        }

        return $thumbnail_image_url;

    }

    /**
     * Create or update product image thumbnail. This method should only be used if the image option is set to Once
     *
     * @param $product_thumbnail_id
     * @param $vend_images
     * @param LS_Product_Meta $product_meta
     * @param $previously_saved_vend_thumbnail_data
     * @return array|null               If product image thumbnail is inserted on the process, will returns an array of
     *                                  metadata of attachment, attachment id vend url information else null
     */
    private static function process_image_thumbnail_for_once_option($product_thumbnail_id, &$vend_images, LS_Product_Meta $product_meta, $previously_saved_vend_thumbnail_data)
    {

        /**
         * Checks if the previously saved vend thumbnail image is not the same then upload and set the api response vend image
         * to be the current product featured image
         */
        if (
            (
                isset($previously_saved_vend_thumbnail_data['vend_url']) &&
                !empty($vend_images[0]) &&
                $previously_saved_vend_thumbnail_data['vend_url'] != $vend_images[0]['url']
            ) ||
            empty($previously_saved_vend_thumbnail_data)
        ) {

            if (empty($product_thumbnail_id) && !empty($vend_images[0])) {
                $attach_id = LS_Image_Helper::setProductThumbnailFromImageUrl($vend_images[0]['url'], $product_meta);
                $thumbnailArray = array(
                    'attach_id' => $attach_id['attachment_id'],
                    'vend_url' => trim($vend_images[0]['url']),
                    'pathinfo' => pathinfo($vend_images[0]['url'])
                );
                $product_meta->updateVendImageThumbnail($thumbnailArray);

                LS_Image_Helper::add_image_gallery_attachment_id($product_meta, $attach_id['attachment_id']);

                return $thumbnailArray;
            }
        }

        return null;
    }

    /**
     * Check whether the new vend image url was previously saved
     *
     * @param $woo_image_gallery_url_paths
     * @param $new_image_url
     * @return array|bool
     */
    public static function existFromPreviouslySavedGalleryImage($woo_image_gallery_url_paths, $new_image_url)
    {
        $vend_image_path_info = pathinfo($new_image_url);
        $vend_image_exist_in_woo = false;

        if(!empty($woo_image_gallery_url_paths)){

            foreach ($woo_image_gallery_url_paths as $woo_gallery_index => $woo_image_gallery_url_path) {
                $woo_image_path_info = pathinfo($woo_image_gallery_url_path);

                if ($vend_image_path_info['basename'] == $woo_image_path_info['basename']) {
                    return array(
                        'attach_id' => $woo_gallery_index
                    );
                }
            }

        }
        return $vend_image_exist_in_woo;
    }

    public static function importProductImageToWoo(LS_Product $product, LS_Product_Meta $product_meta, $importImageOption)
    {
        $productImageDataSet = array();

        if ($product->hasImages()) {
            $post_id = $product_meta->getWooProductId();

            $vend_images = $product->get_images();
            $product_thumbnail_id = $product_meta->getThumbnailId();
            $product_gallery_ids = $product_meta->get_image_gallery();

            $previously_saved_vend_thumbnail_data = $product_meta->getVendImageThumbnail();
            $previously_saved_vend_images_data = $product_meta->getVendImageGallery();

            $woo_image_gallery_url_paths = LS_Image_Helper::getProductImagesPaths($product_gallery_ids);

            $thumbnailArray = array(
                'vend_url' => trim($vend_images[0]['url']),
                'pathinfo' => pathinfo($vend_images[0]['url'])
            );

            if ('Enable' == $importImageOption) {


                $previously_exist = self::existFromPreviouslySavedGalleryImage($woo_image_gallery_url_paths, $vend_images[0]['url']);
                if (false !== $previously_exist) {
                    $attach_id = $previously_exist['attach_id'];

                    if (wp_attachment_is_image($attach_id) && empty($product_thumbnail_id)) {
                        /**
                         * If the product image exist from the previously saved gallery image data before
                         * then use attachment id to set the current product featured image
                         */

                        LS_Image_Helper::setProductThumbnail($post_id, $attach_id);
                        $thumbnailArray['attach_id'] = $attach_id;
                        $product_meta->updateVendImageThumbnail($thumbnailArray);
                    } else {


                        $productImageDataSet['thumbnail_attachment'] = self::process_image_thumbnail_for_once_option(
                            $product_thumbnail_id,
                            $vend_images, $product_meta,
                            $previously_saved_vend_thumbnail_data
                        );

                        if (!empty($productImageDataSet['thumbnail_attachment'])) {
                            $thumbnailArray = array_merge($thumbnailArray, $productImageDataSet['thumbnail_attachment']);
                        }

                    }


                } else {

                    $productImageDataSet['thumbnail_attachment'] = self::process_image_thumbnail_for_once_option(
                        $product_thumbnail_id,
                        $vend_images, $product_meta,
                        $previously_saved_vend_thumbnail_data
                    );

                    if (!empty($productImageDataSet['thumbnail_attachment'])) {
                        $thumbnailArray = array_merge($thumbnailArray, $productImageDataSet['thumbnail_attachment']);
                    }

                }


                if (empty($product_gallery_ids)) {
                    if (!empty($vend_images[0])) {
                        /**
                         * Unset first index of linksync image api response because that is the featured images or
                         * the main/primary image in vend
                         */
                        unset($vend_images[0]);
                    }

                    $imageGalleryAttached = array();
                    foreach ($vend_images as $vend_image_index => $vend_image_value) {

                        $vend_image_exist_in_woo = self::existFromPreviouslySavedGalleryImage($woo_image_gallery_url_paths, $vend_image_value['url']);
                        if (false == $vend_image_exist_in_woo) {

                            $attach_id = LS_Image_Helper::media_side_loaded_image($vend_image_value['url'], $post_id);
                            if (!empty($attach_id)) {

                                LS_Image_Helper::add_image_gallery_attachment_id($product_meta, $attach_id);
                                $imageGalleryAttached[$attach_id] = array(
                                    'attach_id' => $attach_id,
                                    'vend_url' => $vend_image_value['url'],
                                    'pathinfo' => pathinfo($vend_image_value['url'])
                                );

                                $productImageDataSet['image_gallery_attached'] = $imageGalleryAttached;
                            }

                        }

                    }

                    if (!empty($thumbnailArray['attach_id'])) {
                        /**
                         * Add the thumbnail vend data to the gallery image storage
                         */
                        $imageGalleryAttached[$thumbnailArray['attach_id']] = $thumbnailArray;
                    }
                    $product_meta->updateVendImageGallery($imageGalleryAttached);

                }


            } elseif ('Ongoing' == $importImageOption) {

                $previously_exist = self::existFromPreviouslySavedGalleryImage($woo_image_gallery_url_paths, $vend_images[0]['url']);
//                ls_print_r($woo_image_gallery_url_paths);
//                ls_print_r($vend_images[0]['url']);
//                ls_var_dump($previously_exist);
//                exit();
                if (false !== $previously_exist) {

                    $attach_id = $previously_exist['attach_id'];

                    if (wp_attachment_is_image($attach_id)) {

                        /**
                         * If the product image exist from the previously saved gallery image data before
                         * then use attachment id to set the current product featured image
                         */
                        LS_Image_Helper::setProductThumbnail($post_id, $attach_id);
                        $thumbnailArray['attach_id'] = $attach_id;
                        $product_meta->updateVendImageThumbnail($thumbnailArray);
                    } else {

                        /**
                         * Main/primary image was not sync before so set the product image now
                         */
                        $productImageDataSet['thumbnail_attachment'] = self::process_image_thumbnail_for_ongoing_option(
                            $vend_images,
                            $product_thumbnail_id,
                            $product_meta
                        );

                        if (!empty($productImageDataSet['thumbnail_attachment'])) {
                            $thumbnailArray = array_merge($thumbnailArray, $productImageDataSet['thumbnail_attachment']);
                        }
                    }

                } else {

                    /**
                     * Main/primary image was not sync before so set the product image now
                     */
                    $productImageDataSet['thumbnail_attachment'] = self::process_image_thumbnail_for_ongoing_option(
                        $vend_images,
                        $product_thumbnail_id,
                        $product_meta
                    );

                    if (!empty($productImageDataSet['thumbnail_attachment'])) {
                        $thumbnailArray = array_merge($thumbnailArray, $productImageDataSet['thumbnail_attachment']);
                    }
                }



                if (!empty($vend_images)) {
                    $imageGalleryAttached = array();
                    foreach ($vend_images as $vend_image_index => $vend_image_url) {

                        $vend_image_exist_in_woo = self::existFromPreviouslySavedGalleryImage($woo_image_gallery_url_paths, $vend_image_url['url']);
                        if (false == $vend_image_exist_in_woo) {

                            $attach_id = LS_Image_Helper::media_side_loaded_image($vend_image_url['url'], $post_id);
                            if (!empty($attach_id)) {

                                LS_Image_Helper::add_image_gallery_attachment_id($product_meta, $attach_id);
                                $imageGalleryAttached[$attach_id] = array(
                                    'attach_id' => $attach_id,
                                    'vend_url' => $vend_image_url['url'],
                                    'pathinfo' => pathinfo($vend_image_url['url'])
                                );

                                $productImageDataSet['image_gallery_attached'] = $imageGalleryAttached;
                            }

                        }

                    }

                    if (!empty($thumbnailArray['attach_id'])) {
                        /**
                         * Add the thumbnail vend data to the gallery image storage
                         */
                        $imageGalleryAttached[$thumbnailArray['attach_id']] = $thumbnailArray;
                    }
                    $product_meta->updateVendImageGallery($imageGalleryAttached);
                }

            }


        }

        return $productImageDataSet;
    }
}