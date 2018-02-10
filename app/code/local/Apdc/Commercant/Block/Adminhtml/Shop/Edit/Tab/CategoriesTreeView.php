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
 * Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_CategoriesTreeView 
 * 
 * @category Apdc
 * @package  Commercant
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Catalog_Category_Tree
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_CategoriesTreeView extends Apdc_Core_Block_Adminhtml_Tab_CategoriesTreeView
{

    public function getShop()
    {
        return Mage::registry('shop');
    }

    public function getCategoryIds()
    {
        if (is_null($this->_categoryIds)){
            $this->_categoryIds = [];
            if ($this->getShop()->getCategoryIds()) {
                $this->_categoryIds = $this->getShop()->getCategoryIds();
            }
        }
        return $this->_categoryIds;
    }
}
