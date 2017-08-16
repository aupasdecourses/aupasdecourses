<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Search
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Search_Block_Result_List 
 * 
 * @category Apdc
 * @package  Search
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Search_Block_Result_List extends Mage_Catalog_Block_Product_List
{

    /**
     * filterByCommercant 
     * 
     * @return array
     */
    protected function filterByCommercant()
    {
        $collection = $this->_getProductCollection();
        $productIds = $collection->getColumnValues('entity_id');
        $collection->addAttributeToSelect('commercant');

        $commercantByNbProducts = $this->getCommercantByNbProducts($productIds);

        $commercantIds = array_unique($collection->getColumnValues('commercant'));
        $commercantByNbItems = $this->getCommercantByNbItems($commercantIds);
        $orderedCommercants = $this->getOrderedCommercantIds($commercantByNbProducts, $commercantByNbItems);
        $collection->load();

        foreach ($collection as $product) {
            if (!isset($orderedCommercants[$product->getCommercant()])) {
                $orderedCommercants[$product->getCommercant()] = array();
            }
            $orderedCommercants[$product->getCommercant()][] = $product;
        }
        return $orderedCommercants;
    }

    public function getProductsByCommercant()
    {
        return $this->filterByCommercant();
    }

    public function getCommercantName($product)
    {
        $name = Mage::getResourceSingleton('catalog/product')
            ->getAttribute('commercant')
            ->getSource()
            ->getOptionText($product->getData('commercant'));
        if (!$name) {
            return $this->__('Paniers & Plateaux');
        }
        return $name;
    }

    public function getCommercantByNbProducts($productIds)
    {
        $col = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect('commercant')
            ->addAttributeToSort('commercant');
        $col->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $col->getSelect()->reset(Zend_Db_Select::ORDER);
        $col->getSelect()->columns('count(e.entity_id) as nb_product, commercant_option_value_t2.option_id as commercant');
        $col->getSelect()->group('commercant');
        $col->getSelect()->order('count(e.entity_id) DESC');

        return $col;
    }

    public function getCommercantByNbItems($commercantIds)
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $collection = Mage::getModel('sales/quote_item')->getCollection()
            ->addFieldToFilter('commercant', array('in' => $commercantIds))
            ->addFieldToFilter('quote_id', $quote->getId());
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns('count(item_id) as nb_item');
            $collection->getSelect()->columns('commercant');
        $collection->getSelect()->group('commercant');
        $collection->getSelect()->order('count(item_id) DESC');
        return $collection;
    }

    public function getOrderedCommercantIds($commercantByNbProducts, $commercantByNbItems)
    {
        $commercantIds = array();
        foreach ($commercantByNbItems->getData() as $commercantByNbItem) {
            $commercantIds[$commercantByNbItem['commercant']] = array();
        }
        foreach ($commercantByNbProducts->getData() as $commercantByNbProduct) {
            if (!in_array($commercantByNbProduct['commercant'], array_keys($commercantIds))) {
                $commercantIds[$commercantByNbProduct['commercant']] = array();
            }
        }
        return $commercantIds;
    }

    protected function _beforeToHtml()
    {
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        return $this;
    }
}
