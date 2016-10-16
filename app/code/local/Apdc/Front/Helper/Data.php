<?php
/**
 * @copyright  Pierre Mainguet
 */
class Apdc_Front_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve Thumbnail image URL.
     *
     * @return string
     */
    public function getThumbnailImageUrl($category)
    {
        $url = false;
        if ($image = $category->getThumbnail()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }

        return $url;
    }
}