<?php

class LS_Image_Helper
{
    public static function media_side_loaded_image($file, $post_id, $desc = null)
    {
        if (!empty($file)) {

            // Set variables for storage, fix file filename for query strings.
            preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
            if (!$matches) {
                return new WP_Error('image_sideload_failed', __('Invalid image URL'));
            }

            $file_array = array();
            $file_array['name'] = basename($matches[0]);

            // Download file to temp location.
            $file_array['tmp_name'] = download_url($file);

            // If error storing temporarily, return the error.
            if (is_wp_error($file_array['tmp_name'])) {
                return $file_array['tmp_name'];
            }

            // Do the validation and storage stuff.
            $id = media_handle_sideload($file_array, $post_id, $desc);

            // If error storing permanently, unlink.
            if (is_wp_error($id)) {
                @unlink($file_array['tmp_name']);
                return $id;
            }

            return $id;
        }

        return null;

    }


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

    public static function add_image_gallery_attachment_id(LS_Product_Meta $product_meta, $attach_id)
    {

        $imageDataSet = $product_meta->get_image_gallery();
        if (!empty($imageDataSet)) {
            $product_meta->add_image_gallery_id($attach_id);
        } else {
            $product_meta->update_image_gallery($attach_id);
        }

    }


    public static function setProductThumbnailFromImageUrl($product_image_url, LS_Product_Meta $product_meta)
    {
        $postThumbnail = [];
        $post_id = $product_meta->getWooProductId();

        if (!empty($post_id)) {

            $attachment_id = self::media_side_loaded_image($product_image_url, $post_id);
            if (!empty($attachment_id)) {
                $postThumbnail['attachment_id'] = $attachment_id;
                $postThumbnail['post_thumbnail'] = self::setProductThumbnail($post_id, $attachment_id);
            }

        }

        return $postThumbnail;
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
        if(!empty($product_gallery_ids)){
            $product_gallery_ids = explode(',', $product_gallery_ids);
            $gallery_urls = array();
            foreach ($product_gallery_ids as $product_gallery_id) {
                $gallery_urls[$product_gallery_id] = self::getImagePath($product_gallery_id);
            }
            return $gallery_urls;
        }

        return null;
    }


}