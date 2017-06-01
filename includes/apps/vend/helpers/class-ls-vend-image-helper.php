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

        $thumbnail_image_url = '';

        if (empty($product_thumbnail_path_info['basename']) && !empty($vend_images[0]['url'])) {

            $thumbnail_image_url = $vend_images[0]['url'];

        } else if (!empty($product_thumbnail_path_info['basename']) && !empty($vend_images[0]['url'])) {

            $woo_thumbnail_image_exist = false;
            foreach ($vend_images as $vend_image_key => $vend_image_value) {
                $vend_image_path_info = pathinfo($vend_image_value['url']);

                if ($product_thumbnail_path_info['basename'] == $vend_image_path_info['basename']) {
                    $woo_thumbnail_image_exist = true;
                    unset($vend_images[$vend_image_key]);
                }

            }

            if (false == $woo_thumbnail_image_exist && isset($vend_images[0]['url'])) {
                $thumbnail_image_url = $vend_images[0]['url'];
            }

        }

        if (!empty($thumbnail_image_url)) {
            $attach_id = LS_Image_Helper::addProductThumbnail($vend_images[0]['url'], $product_meta);
            $thumbnailArray = array(
                'attach_id' => $attach_id['post_thumbnail']['attach_id'],
                'vend_url' => trim($vend_images[0]['url']),
                'pathinfo' => pathinfo($vend_images[0]['url']),
                'attachment_meta_data' => $attach_id,
            );
            $product_meta->updateVendImageThumbnail($thumbnailArray);
            unset($vend_images[0]);

            return $thumbnailArray;
        }

        return $thumbnail_image_url;

    }

    /**
     * Create or update product image thumbnail. This method should only be used if the image option is set to Once
     *
     * @param $product_thumbnail_id
     * @param $vend_images
     * @param LS_Product_Meta $product_meta
     * @return array|null               If product image thumbnail is inserted on the process, will returns an array of
     *                                  metadata of attachment, attachment id vend url information else null
     */
    private static function process_image_thumbnail_for_once_option($product_thumbnail_id, &$vend_images, LS_Product_Meta $product_meta)
    {
        if (empty($product_thumbnail_id) && !empty($vend_images[0])) {
            $attach_id = LS_Image_Helper::addProductThumbnail($vend_images[0]['url'], $product_meta);
            $thumbnailArray = array(
                'attach_id' => $attach_id['post_thumbnail']['attach_id'],
                'vend_url' => trim($vend_images[0]['url']),
                'pathinfo' => pathinfo($vend_images[0]['url']),
                'attachment_meta_data' => $attach_id,
            );
            $product_meta->updateVendImageThumbnail($thumbnailArray);

            return $thumbnailArray;
        }

        if (!empty($vend_images[0])) {
            unset($vend_images[0]);
        }

        return null;
    }

    public static function importProductImageToWoo(LS_Product $product, LS_Product_Meta $product_meta, $importImageOption)
    {
        $productImageDataSet = array();

        if ($product->hasImages()) {

            $vend_images = $product->get_images();
            $product_thumbnail_id = $product_meta->getThumbnailId();
            $product_gallery_ids = $product_meta->get_image_gallery();

            if ('Enable' == $importImageOption) {

                $productImageDataSet['thumbnail_attachment'] = self::process_image_thumbnail_for_once_option(
                    $product_thumbnail_id,
                    $vend_images, $product_meta
                );

                if (empty($product_gallery_ids)) {
                    $imageGalleryAttached = array();
                    foreach ($vend_images as $vend_image_index => $vend_image_value) {

                        $attach_id = LS_Image_Helper::insertToProductUsingUrl($vend_image_value['url'], $product_meta);
                        $imageGalleryAttached[$attach_id['attach_id']] = array(
                            'attach_id' => $attach_id['attach_id'],
                            'vend_url' => $vend_image_value['url'],
                            'pathinfo' => pathinfo($vend_image_value['url']),
                            'attachment_meta_data' => $attach_id,
                        );

                        $productImageDataSet['image_gallery_attached'] = $imageGalleryAttached;
                    }

                    $product_meta->updateVendImageGallery($imageGalleryAttached);

                }


            } elseif ('Ongoing' == $importImageOption) {

                $woo_image_gallery_url_paths = LS_Image_Helper::getProductImagesPaths($product_gallery_ids);
                $productImageDataSet['thumbnail_attachment'] = self::process_image_thumbnail_for_ongoing_option(
                    $vend_images,
                    $product_thumbnail_id,
                    $product_meta
                );

                if (!empty($vend_images)) {
                    $imageGalleryAttached = array();
                    foreach ($vend_images as $vend_image_index => $vend_image_url) {

                        $vend_image_path_info = pathinfo($vend_image_url['url']);
                        $vend_image_exist_in_woo = false;

                        foreach ($woo_image_gallery_url_paths as $woo_gallery_index => $woo_image_gallery_url_path) {
                            $woo_image_path_info = pathinfo($woo_image_gallery_url_path);

                            if ($vend_image_path_info['basename'] == $woo_image_path_info['basename']) {
                                $vend_image_exist_in_woo = true;
                                unset($woo_image_gallery_url_paths[$woo_gallery_index]);
                            }
                        }

                        if (false == $vend_image_exist_in_woo) {
                            $attach_id = LS_Image_Helper::insertToProductUsingUrl($vend_image_url['url'], $product_meta);
                            $imageGalleryAttached[$attach_id['attach_id']] = array(
                                'attach_id' => $attach_id['attach_id'],
                                'vend_url' => $vend_image_url['url'],
                                'pathinfo' => $vend_image_path_info,
                                'attachment_meta_data' => $attach_id,
                            );

                            $productImageDataSet['image_gallery_attached'] = $imageGalleryAttached;
                        }

                    }
                    $product_meta->updateVendImageGallery($imageGalleryAttached);
                }

            }


        }

        return $productImageDataSet;
    }
}