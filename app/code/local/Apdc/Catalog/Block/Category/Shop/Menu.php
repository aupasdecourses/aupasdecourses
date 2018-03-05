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
 * Apdc_Catalog_Block_Category_Shop_Menu 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Category_Shop_Menu extends Mage_Core_Block_Template
{

    /**
     * getMenuHtml 
     * 
     * @return string
     */
    public function getMenuHtml()
    {
        $category = $this->getCurrentRootCategory();
        $catlistHtml = '';
        if ($category && $category->getId() && $category->getLevel() == 3) {
            $catlistHtml = $this->getTreeCategories($category->getId(), false);
        }
        return $catlistHtml;
    }

    /**
     * getTreeCategories 
     * 
     * @param int     $parentId : parentId 
     * @param boolean $isChild  : isChild 
     * 
     * @return string
     */
    public function getTreeCategories($parentId, $isChild)
    {
        $html = '';
        $allCats = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active','1')
            ->addAttributeToFilter('include_in_menu','1')
            ->addAttributeToFilter('parent_id',array('eq' => $parentId));
        $allCats->getSelect()->order('position');

        $block = Mage::app()->getLayout()->createBlock(
            'apdc_catalog/category_shop_menu',
            'menu_renderer',
            [
                'template' => 'apdc_catalog/category/menu/renderer.phtml',
                'all_cats' => $allCats
            ]
        );
        return $block->toHtml();
    }

    /**
     * getCurrentRootCategory
     * 
     * @return Mage_Catalog_Model_Category
     */
    protected function getCurrentRootCategory()
    {
        $category = Mage::registry('current_category');
        while ($category->getLevel() > 3) {
            $category = $category->getParentCategory();
        }
        return $category;
    }

    /**
     * getCommercantName 
     * 
     * @return string
     */
    protected function getCommercantName()
    {
        $infoShop = Mage::helper('apdc_commercant')->getInfoShop();
        if (!empty($infoShop)) {
            return $infoShop['model']->getName();
        }
        return '';
    }

    /**
     * getCurrentCategory
     * 
     * @return int
     */
    protected function getCurrentCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * getClasses 
     * 
     * @param Mage_Catalog_Model_Category $category category 
     * 
     * @return string
     */
    public function getClasses(Mage_Catalog_Model_Category $category)
    {
        $classes = [];
        $classes[] = 'level-' . $category->getLevel();
        if ($category->getId() == $this->getCurrentCategory()->getId()) {
            $classes[] = 'current';
        }
        if (preg_match('#^' . preg_quote($category->getPath(), '/') . '#', $this->getCurrentCategory()->getPath())) {
            $classes[] = 'active';
            if ($category->hasChildren()) {
                $classes[] = 'open';
            }
        }
        if ($category->hasChildren()) {
            $classes[] = 'parent';
        }

        return implode(' ', $classes);
    }
}
