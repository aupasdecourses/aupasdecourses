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
 * Apdc_Catalog_Model_Observer_ProductAvailability 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Observer_ProductAvailability
{
    /**
     * joinProductAvailabilty 
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function joinProductAvailabilty(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if(Mage::getSingleton('core/session')->getDdate()) {
            $timestamp = strtotime(Mage::getSingleton('core/session')->getDdate());
            $date = date('Y-m-d', $timestamp);

            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

            $condition[] = 'availability.product_id = e.entity_id';
            $condition[] = $connection->quoteInto('availability.website_id = ?', (int) Mage::app()->getWebsite()->getId());
            $condition[] = $connection->quoteInto('availability.delivery_date = ?', $date);
            $collection->getSelect()->joinLeft(
                ['availability' => $collection->getTable('apdc_catalog/product_availability')],
                implode(' AND ', $condition),
                ['product_availability' => 'availability.status']
            );
        }
        $collection->joinAttribute(
            'can_order_days',
            'catalog_product/can_order_days',
            'entity_id',
            null,
            'left'
        );
        $collection->getSelect()->columns(
            [
                'can_order' => new Zend_Db_Expr('
                    IF(
                        at_can_order_days.value IS NULL,
                        IF(
                            at_can_order_days_default.value IS NULL,
                            1,
                            at_can_order_days_default.value REGEXP (WEEKDAY(NOW()) + 1)
                        ), 
                        at_can_order_days.value REGEXP (WEEKDAY(NOW()) + 1)
                    )
                ')
            ]
        );
    }
}
