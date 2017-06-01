<?php

class LS_Image_Helper
{

    public static function insertToProductUsingUrl($image_url, LS_Product_Meta $product_meta, $add_to_gallery = true)
    {
        $attachmentDataSet = array();

        $post_id = $product_meta->getWooProductId();
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $filename = basename($image_url);
        if (wp_mkdir_p($upload_dir['path']))
            $file = $upload_dir['path'] . '/' . $filename;
        else
            $file = $upload_dir['basedir'] . '/' . $filename;
        $attachmentDataSet['file_put_contents'] = file_put_contents($file, $image_data);

        $filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $file, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        $attachmentDataSet['attach_id'] = $attach_id;
        $attachmentDataSet['attachment_generated_metadata'] = $attach_data;
        $attachmentDataSet['attachment_metadata'] = wp_update_attachment_metadata($attach_id, $attach_data);

        if ($add_to_gallery) {
            $imageDataSet = $product_meta->get_image_gallery();
            if (!empty($imageDataSet)) {
                $product_meta->add_image_gallery_id($attach_id);
            } else {
                $product_meta->update_image_gallery($attach_id);
            }
        }


        return $attachmentDataSet;
    }


    public static function addProductThumbnail($image_url, LS_Product_Meta $product_meta)
    {
        $attachmentDataSet = self::insertToProductUsingUrl($image_url, $product_meta, false);
        $post_id = $product_meta->getWooProductId();
        $postThumbnail['post_thumbnail'] = self::setProductThumbnail($post_id, $attachmentDataSet['attach_id']);
        $postThumbnail['attachment_data'] = $attachmentDataSet;
        return $postThumbnail;
    }

    public static function setProductThumbnail($post_id, $attached_id)
    {
        return set_post_thumbnail($post_id, $attached_id);
    }

    public static function getAttachedFile($attachmentId)
    {
        /**
         * returns an array  @wp_get_attachment_image_src($image[0]);
         */
        return get_post_meta($attachmentId, '_wp_attached_file', true);
    }


    public static function getImagePath($attachedId)
    {
        return get_post_meta($attachedId, '_wp_attached_file', true);
    }

    /**
     * returns the paths of images of the product gallery
     * @param $product_gallery_ids array array of gallery ids of the product
     * @return array
     */
    public static function getProductImagesPaths($product_gallery_ids)
    {
        $product_gallery_ids = explode(',', $product_gallery_ids);
        $gallery_urls = array();
        foreach ($product_gallery_ids as $product_gallery_id) {
            $gallery_urls[] = self::getImagePath($product_gallery_id);
        }
        return $gallery_urls;
    }


}