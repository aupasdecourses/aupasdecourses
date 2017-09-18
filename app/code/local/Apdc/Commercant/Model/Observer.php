<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Commercant_Model_Observer 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Model_Observer
{
    /**
     * cleanShopCategory 
     * Check if the category is used by a shop (table apdc_shop => id_category)
     * If used => clean the shop id_category field
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function cleanShopCategory(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();

        $shops = Mage::getModel('apdc_commercant/shop')->getCollection();
        $id = (int)$category->getId();
        $shops->getSelect()->where(sprintf('id_category REGEXP "^%1$d$|^%1$d\,|\,%1$d$|\,%1$d\,"', $id));
        $shops->load();

        if ($shops->count() > 0) {
            foreach ($shops as $shop) {
                $idCategory = $shop->getIdCategory();
                foreach ($idCategory as $key => $idCat) {
                    if ($idCat == $id) {
                        unset($idCategory[$key]);
                    }
                }
                $shop->setIdCategory($idCategory);
                $shop->save();
            }
        }
    }
}
