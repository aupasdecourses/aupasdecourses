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
 * Apdc_Catalog_Block_Selection 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Catalog_Block_Product_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Selection extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * getAllSelections 
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getAllSelections()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection = $this->prepareProductCollection($collection);
        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        $collection->setPageSize(10);

        return $collection;

    }

    /**
     * Initialize product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function prepareProductCollection($collection)
    {
        $storeId = Mage::app()->getStore()->getId();
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter($storeId)
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('on_selection', 1)
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        return $collection;
    }

    /**
     * getSelectionById 
     * 
     * @param int $categoryId categoryId 
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getSelectionById($categoryId)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToFilter('category_id', $categoryId);
        $collection = $this->prepareProductCollection($collection);
        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        $collection->setPageSize(10);
        
        return $collection;
    }

}
