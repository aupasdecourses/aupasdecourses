<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Helper_Category 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Helper_Category extends Mage_Core_Helper_Abstract
{
    /**
     * getImageUrl 
     * 
     * @param Mage_Catalog_Model_Category $category category 
     * 
     * @return string
     */
    public function getImageUrl(Mage_Catalog_Model_Category $category)
    {
        return $this->getImage($category->getImage());
    }

    /**
     * getThumbnailImageUrl 
     * 
     * @param Mage_Catalog_Model_Category $category category 
     * 
     * @return string
     */
    public function getThumbnailImageUrl(Mage_Catalog_Model_Category $category)
    {
        return $this->getImage($category->getThumbnail());
    }

    /**
     * getImage 
     * 
     * @param string|null $image image 
     * 
     * @return string | false
     */
    public function getImage($image)
    {
        $url = false;
        if ($image) {
            Mage::log(Mage::getBaseDir('media') . DS . $image);
            if (file_exists(Mage::getBaseDir('media') . DS . $image)) {
                $url = Mage::getBaseUrl('media') . $image;
            } else if (file_exists(Mage::getBaseDir('media') . '/catalog/category/' . $image)) {
                $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
            }
        }
        return $url;
    }
}
